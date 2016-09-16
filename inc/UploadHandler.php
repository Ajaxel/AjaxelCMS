<?php

/**
* Ajaxel CMS v8.0
* http://ajaxel.com
* =================
* 
* Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* 
* The software, this file and its contents are subject to the Ajaxel CMS
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the source code of Ajaxel CMS.
* 
* @file       inc/UploadHandler.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class UploadHandler {
	public
		$field = 'Filedata',
		$filename = NULL,
		$content_range = NULL,
		$size = 0,
		$type = 0,
		$error = false,
		$append_file = false,
		$uploaded = false
	;
	
	public function __construct() {
		
	}
	
	private function get_server_var($id) {
        return isset($_SERVER[$id]) ? $_SERVER[$id] : '';
    }
    private function fix_integer_overflow($size) {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }
    private function get_file_size($file_path, $clear_stat_cache = false) {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }
        return $this->fix_integer_overflow(filesize($file_path));
    }
	
	public function prepare($tmp_name,$filename,$file,$size = 0,$type = '') {
		header('Vary: Accept');
        if (strpos($this->get_server_var('HTTP_ACCEPT'), 'application/json') !== false) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
		$this->file_name = $this->get_server_var('HTTP_CONTENT_DISPOSITION') ?
            rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $this->get_server_var('HTTP_CONTENT_DISPOSITION')
            )) : $filename;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $this->content_range = $this->get_server_var('HTTP_CONTENT_RANGE') ?
            preg_split('/[^0-9]+/', $this->get_server_var('HTTP_CONTENT_RANGE')) : NULL;
			
        $this->size =  $this->content_range ? $this->content_range[3] : $size;
		$this->type = $type;
		
		$this->size = $this->size ? $this->size : $this->get_server_var('CONTENT_LENGTH');
		$this->append_file = $this->content_range && is_file($file) && $this->size > $this->get_file_size($file);
	}

	public function move($tmp_name,$file) {
		
		if ($tmp_name && is_uploaded_file($tmp_name)) {
			if ($this->append_file) {
				$ret = file_put_contents($file, fopen($tmp_name,'r'), FILE_APPEND);
			} else {
				$ret = move_uploaded_file($tmp_name, $file);
			}
		} else {
			$ret = file_put_contents($file,	fopen('php://input', 'r'), $this->append_file ? FILE_APPEND : 0);
		}
		$size = $this->get_file_size($file, true);
		
		if ($size==$this->size) {
			$this->error = false;
			$this->uploaded = true;
		} else {
			$this->size = $size;
		}
		
		return $ret;
	}
	
	public function response() {
		return array(
			$this->field => $this->filename,
			'size'	=> $this->size,
			'type'	=> $this->type,
			'error'	=> $this->error,
		);
	}
	
	
	public function video_image($file, $frame=70) {
		if (!extension_loaded('ffmpeg') || !extension_loaded('gd')) {
			return false;	
		}		
		$img = $file.'.png';
		$mov = new ffmpeg_movie($file);
		if (file_exists($img)) {
			if (isset($_GET['reset'])) unlink($img);
			else return $img;
		}		
		if (!file_exists($img) && ($ff_frame = $mov->getFrame($frame))) {
			if ($gd_image = $ff_frame->toGDImage()) {
				imagepng($gd_image, $img);
				imagedestroy($gd_image);
			}
		}
		return $img;
			/*
			'file'		=> $file,
			'duration'	=> $mov->getDuration(),
			'has_audio'	=> $mov->hasAudio(),
			'width'		=> $mov->getFrameWidth(),
			'height'	=> $mov->getFrameHeight(),
			'paratio'	=> $mov->getPixelAspectRatio()
			*/
		//);
	}
	
	public function upload(&$obj) {
		
		list ($id, $ftp_path, $http_path) = $obj->listPath();
		
		if ($obj->sizes[$obj->name]) {
			$path = substr($ftp_path,0,-4);
			if ($obj->limit[$obj->name]==1 && !$obj->in_folder) {
				foreach ($obj->sizes[$obj->name] as $i => $a) {
					File::rmdir($path.'th'.$i.'/');
				}
			}
		} else {
			$path = $ftp_path;	
		}
	
		if (!is_dir($ftp_path)) File::makeDir($ftp_path,0777);
		if ($obj->in_folder[$obj->name]) {
			$file = File::getUnique($ftp_path,nameOnly($file),$ext);
		}
		
		
		if ($obj->field) {
			$filename = $_FILES[$obj->field]['name'];
			$tmp_name = $_FILES[$obj->field]['tmp_name'];
			
			$ex = explode('.',$filename);
			array_pop($ex);
			$ext = ext($filename);
			if ($obj->one[$obj->name]) {
				$name = $obj->one[$obj->name];
			} else {
				$name = join('.',$ex);
			}
			$file = File::fixFileName($name.'.'.$ext);
			
		} else {
			$upload = Site::$upload;
			$obj->name = $upload[1];
			$obj->id[$obj->name] = $upload[0];
			if (!array_key_exists($obj->name, $obj->names)) {
				return $obj->error('Cannot upload due to security issues');
			}
			$filename = $_FILES[$this->field]['name'];
			$tmp_name = $_FILES[$this->field]['tmp_name'];

			$ex = explode('.',$filename);
			array_pop($ex);
			$ext = ext($filename);
			if ($obj->one[$obj->name]) {
				$name = $obj->one[$obj->name];
			} else {
				$name = join('.',$ex);
			}
			$file = File::fixFileName($name.'.'.$ext);
			
			$this->prepare($tmp_name,$filename,$path.$file);
			$filename = $this->file_name;
		}
		
		if (!$this->append_file) {
			if (!$filename) {
				return $obj->error('No file was uploaded');
			}
			if (!$tmp_name) {
				return $obj->error('File has not been uploaded');
			}
			if (!File::valid($filename)) {
				return $obj->error('Uploading file format cannot be uploaded to server');
			}
			
			$total = $obj->getTotal($ftp_path);
	
			if ($obj->limit[$obj->name] && !$obj->one[$obj->name] && !$obj->in_folder[$obj->name]) {
				if ($total > $obj->limit[$obj->name]) {
					return $obj->error('Too many files uploaded, maximum is '.$obj->limit[$obj->name].'');
				}
			}
			
			if (!in_array($ext, $obj->ext[$obj->name])) {
				return $obj->error('Wrong file format');
			}
			$pic_ext = array('gif','jpg','jpeg','bmp','png','tif','tiff');
			if ($obj->min[$obj->name][0] && $obj->min[$obj->name][1] && in_array($ext, $pic_ext)) {
				list ($w, $h) = getimagesize($tmp_name);
				if (($obj->min[$obj->name][0] && $w < $obj->min[$obj->name][0]) || ($obj->min[$obj->name][1] && $h < $obj->min[$obj->name][1])) {
					return $obj->error('This image cannot be uploaded, too small. Minimum dimensions are: '.$obj->min[$obj->name][0].' x '.$obj->min[$obj->name][1].' pixels.');
				}
			}
			/*
			$mac = self::checkMacBinary($tmp_name);
			if (!$mac['valid']) {
				return $obj->error($mac['error']);
			}
			*/
			//elseif (is_file($ftp_path.$file)) unlink($ftp_path.$file);
		}
		
		if ($obj->in_folder[$obj->name]) {
			$obj->file($file);
		}

		if ($this->move($tmp_name,$path.$file)) {
			if (!$this->append_file && $obj->sizes[$obj->name]) $this->cropImage($path, $file, $obj->sizes[$obj->name]);
			$f = File::getFileInfo($ftp_path, $file);
			$obj->fixFile($f, $ftp_path, $http_path, File::isVideo($file));
			$echo = $obj->Index->My->uploaded($obj->name, $f, $id);

			if (!$this->append_file && !$obj->main_photo[$obj->name]) {
				$obj->Index->My->mained($obj->name, $f['file'], $id);
				$obj->main_photo[$obj->name] = $f['file'];
				$f['main'] = true;
			}
			$img = false;
			if ($this->uploaded && File::isVideo($file)) {
				$img = $this->video_image($path.$file);
			}
			
			if (!$this->append_file && $obj->table[$obj->name]) {
				foreach ($obj->table[$obj->name]['data'] as $col => $val) {
					$obj->table[$obj->name]['data'][$col] = str_replace(array('{$id}','{$file}'), array($id, $file), $val);	
				}
				DB::insert($obj->table[$obj->name]['table'],$obj->table[$obj->name]['data']);
				$f['itemid'] = DB::id();
			}
			
			if ($obj->field) return $file;
			elseif ($obj->tpl[$obj->name]) {
				if (!$obj->Index->Smarty) $obj->Index->loadSmarty();
				$obj->Index->Smarty->assign('f',$f);
				ob_start(array('OB','handler'));
				echo '1/';
				$obj->Index->Smarty->display($obj->tpl[$obj->name]);
				ob_end_flush();
			} else {
				echo '1/';
				if ($echo) echo $echo;
				else {
					if ($obj->th[$obj->name]>1) {
						$http_path = str_replace('/th1/','/th'.$obj->th[$obj->name].'/',$http_path);
					}
					echo $http_path.$file;
				}
			}
			if ($img) echo '?png';
		} else {
			return $obj->error('Cannot upload this file: ('.$file.'). Please try again');
		}
		return true;	
	}
	
	
	public function cropImage($path, $file, $image_sizes = array()) {
		if (!is_dir($path)) mkdir($path,0777);
		$im = Factory::call('image')->driver('GD')->load($path.$file);
		$im->massResize($image_sizes, $path, $file);
		@unlink($path.$file);
	}
	
	public static function checkMacBinary($file) {
		require_once FTP_DIR_ROOT.'inc/lib/Macbinary.php';
		$valid = false;
		$macbin = new MacBinary($file);
		if ($macbin->isValid()) {
			/*
			$dataFile = tempnam(FTP_DIR_ROOT.'files/temp/','MacBinary');
			$dataHandle = fopen($dataFile,'wb');
			$macbin->extractData($dataHandle);
			$file = $dataFile;
			$size = $macbin->dataForkLength();
			*/
			$valid = true;
		}
		$macbin->close();
		return array('file'=>$file,'valid'=>$valid,'size'=>$size,'error'=>$macbin->debug());
	}
	
	public function delete(&$obj, $file){
		list ($id, $ftp_path, $http_path) = $obj->listPath();
		if ($obj->table[$obj->name]) {
			$sql = 'DELETE FROM `'.$obj->table[$obj->name]['table'].'` WHERE `'.$obj->table[$obj->name]['column'].'`='.e($file).$obj->table[$obj->name]['check'].$obj->table[$obj->name]['filter'];
			DB::run($sql);
			if (!DB::affected()) {
				return array('error'=>'Not your file');
			}
		}
		if (is_file($ftp_path.$file)) {
			if (!isset($obj->sizes[$obj->name])) {
				return array('error'=>'Sizes array is not set');
			}
			if ($obj->sizes[$obj->name]) {
				$path = substr($ftp_path,0,-4);
				foreach ($obj->sizes[$obj->name] as $i => $a) {
					@unlink($path.'th'.$i.'/'.$file);
				}
				foreach ($obj->sizes[$obj->name] as $i => $a) {
					@rmdir($path.'th'.$i.'/');
				}
			} else {
				@unlink($ftp_path.$file);
				if (File::isVideo($file)) {
					@unlink($ftp_path.$file.'.png');
				}
				@rmdir($ftp_path);
			}
			
			$obj->Index->My->deleted($obj->name, $file, $id, $obj->getOneFile($ftp_path));
			$ret = array('ok'=>true);
		}
		else {
			$ret = array('error'=>'File '.$ftp_path.$file.' not found');
		}
		return $ret;
	}
}