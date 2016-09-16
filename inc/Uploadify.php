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
* @file       inc/Uploadify.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Uploadify {
	
	public
		$Index,
	
		$name = '',
		$names = array(),
		$id = array(),
		$sizes = array(),
		$tpl = array(),
		$files = array(),
		$th = array(),
		$ext = array(),
		$desc = array(),
		$global = array(),
		$folder = array(),
		$limit = array(),
		$one = array(),
		$main_photo = array(),
		$main = array(),
		$min = array(),
		$dir = array(),
		$not = array(),
		$set = array(),
		$no_id = array(),
		$in_folder = array(),
		$table = array(),
		$qry = array(),
	// TODO!
		$th_folder = array(),
	
		$field = '',
		$error = '',
		
		$videos = array()
	;

	public function __construct(&$Index) {
		$this->Index =& $Index;
	}
	
	public function upload($field = '') {
		if (!$this->id[$this->name] && Site::$upload && Site::$upload[0] && Site::$upload[1]) {
			$this->name = Site::$upload[1];
			$this->id[$this->name] = Site::$upload[0];
		}
		$handler = new UploadHandler;
		$this->field = $field;
		return $handler->upload($this);
	}
	
	public function error($msg = false) {
		if ($msg===false) return $this->error;
		$this->error = $msg;
		if ($this->field) return false;
		echo $this->error;
		exit;
	}
	
	public function fixFile(&$f, $ftp_path, $http_path, $video = false) {
		$f['video'] = $video;
		$f['dir'] = $http_path;
		$f['name'] = $this->name;
		if ($f['id']) $f['itemid'] = $f['id'];
		$f['id'] = $this->id[$this->name];
		$f['th'] = '/th'.$this->th[$this->name].'/';
		$f['main'] = $this->main_photo[$this->name]==$f['file'] ? true : false;
	}
	
	public function json($action) {
		$ret = array();
		switch ($action) {
			case 'delete':
				$this->name = (string)$_POST['name'];
				$this->id((int)$_POST['id']);
				$ret = $this->delete((string)$_POST['file']);
			break;	
			case 'main':
				$_SESSION[(string)$_POST['name'].'_photo'] = $_POST['file'];
				$this->name = (string)$_POST['name'];
				$this->id((int)$_POST['id']);
				$this->Index->My->mained($this->name, $_POST['file'], $this->id[$this->name]);
			break;
		}
		return $ret;
	}
	
	public function delete($file){
		$handler = new UploadHandler;
		return $handler->delete($this, $file);
	}
	
	public function getMainPhoto() {
		return $this->getOneFile();
	}
	
	public function listPath($tempOnly = false) {
		if (!$this->id[$this->name]) $this->id[$this->name] = 0;
		$id = ((!$tempOnly && $this->id[$this->name]) ? $this->id[$this->name] : ($this->Index->Session->UserID ? $this->Index->Session->UserID : $this->Index->Session->SID));
		$suffix = 'th1/';
		if (!$this->sizes[$this->name]) $suffix = '';

		if (!$tempOnly && ($this->id[$this->name] || ($this->no_id[$this->name] && $this->in_folder[$this->name])) && $this->folder[$this->name]) {
			if ($this->in_folder[$this->name] && $this->id[$this->name]) {
				$i = ceil($id / $this->in_folder[$this->name]);
				if ($this->global[$this->name]) {
					$ftp_path = FTP_DIR_ROOT.DIR_FILES.$this->folder[$this->name].$i.'/'.$suffix;
					$http_path = DIR_FILES.$this->folder[$this->name].$i.'/'.$suffix;
				} else {
					$ftp_path = FTP_DIR_FILES.$this->folder[$this->name].$i.'/'.$suffix;
					$http_path = HTTP_DIR_FILES.$this->folder[$this->name].$i.'/'.$suffix;
				}
			}
			elseif ($this->global[$this->name]) {
				$ftp_path = FTP_DIR_ROOT.DIR_FILES.$this->folder[$this->name].$id.'/'.$suffix;
				$http_path = DIR_FILES.$this->folder[$this->name].$id.'/'.$suffix;
			} else {
				$ftp_path = FTP_DIR_FILES.$this->folder[$this->name].$id.'/'.$suffix;
				$http_path = HTTP_DIR_FILES.$this->folder[$this->name].$id.'/'.$suffix;
			}
		} else {
			if ($this->global[$this->name]) {
				$ftp_path = FTP_DIR_ROOT.DIR_FILES.'temp/'.$id.'/'.$suffix;
				$http_path = DIR_FILES.'temp/'.$id.'/'.$suffix;
			} else {
				$ftp_path = FTP_DIR_FILES.'temp/'.$id.'/'.$suffix;	
				$http_path = HTTP_DIR_FILES.'temp/'.$id.'/'.$suffix;	
			}
		}
		return array($id, $ftp_path, $http_path);
	}
	
	public function getTotal($ftp_path) {
		if ($this->table[$this->name]) {
			if (!$this->id[$this->name]) return 0;
			return DB::one('SELECT COUNT(1) FROM `'.$this->table[$this->name]['table'].'` WHERE `'.$this->table[$this->name]['relation'].'`='.$this->id[$this->name]);
		}
		elseif (is_dir($ftp_path)) {
			$i = 0;
			$dh = opendir($ftp_path);
			while (($file = readdir($dh))!==false) {
				if ($file=='.' || $file=='..' || is_dir($ftp_path.$file)) continue;
				$i++;
			}
			closedir($dh);
		}
		return $i;
	}
	
	public function file($file = NULL) {
		if ($file===NULL) return $_SESSION['uploadify'];
		$_SESSION['uploadify'] = $file;
	}
	
	public function getOneFile($ftp_path = false) {
		
		if ($this->table[$this->name]) {
			if (!$this->id[$this->name]) return 0;
			return DB::one('SELECT `'.$this->table[$this->name]['column'].'` FROM `'.$this->table[$this->name]['table'].'` WHERE `'.$this->table[$this->name]['relation'].'`='.(int)$this->id[$this->name].' ORDER BY '.$this->table[$this->name]['order'].'');
		}
		elseif ($this->in_folder[$this->name]) {
			return $_SESSION['uploadify'];	
		}
		else {
			if (!$ftp_path) list ($id, $ftp_path, $http_path) = $this->listPath();
			if (is_dir($ftp_path)) {
				$dh = opendir($ftp_path);
				while (($file = readdir($dh))!==false) {
					if ($file=='.' || $file=='..' || is_dir($ftp_path.$file)) continue;
					break;
				}
				closedir($dh);
				return $file;
			}
		}
	}
	
	public function skipFile($name) {
		if ($this->not[$this->name]) {
			if (is_array($this->not[$this->name])) {
				if (in_array($name, $this->not[$this->name])) return true;	
			}
			elseif (strstr($name,$this->not[$this->name])) return true;
		}
		return false;
	}
	
	public function getFiles($ftp_path, $http_path) {
		if ($this->no_id[$this->name]) return array();
		$files = array();
		$i = 0;
		if (is_dir($ftp_path)) {
			if ($this->qry[$this->name]) {
				$qry = DB::qry($this->qry[$this->name]['sql'].' ORDER BY '.$this->table[$this->name]['order'],$this->qry[$this->name]['offset'],$this->qry[$this->name]['limit']);
				while ($rs = DB::fetch($qry)) {
					if (is_file($ftp_path.$rs['file'])) {
						$files[$i] = array_merge($rs,File::getFileInfo($ftp_path, $rs['file']));
						$this->fixFile($files[$i], $ftp_path, $http_path);
						$i++;
					} else {
						
					}
				}
			} else {
				if ($this->in_folder[$this->name] && $this->main_photo[$this->name]) {
					$files = array(0=>File::getFileInfo($ftp_path, $this->main_photo[$this->name]));
					$this->fixFile($files[0], $ftp_path, $http_path, $video);
					return $files;
				}
				$dh = opendir($ftp_path);
				$break = false;
				while (($file = readdir($dh))!==false) {
					if ($file=='.' || $file=='..' || is_dir($ftp_path.$file)) continue;
					$take = false;
					$name = filename($file);
					if ($this->skipFile($name)) continue;
					if ($this->skipFile($file)) continue;
					
					if (File::isVideo($f = File::nameonly($file))) {
						$this->videos[$f] = true;
						continue;
					}
					$video = isset($this->videos[$file]);
					
					if ($this->one[$this->name]) {
						if (strstr($name,$this->one[$this->name])) {
							$take = true;
							$break = true;
						}
					}
					elseif ($this->limit[$this->name]==1 && $this->main_photo[$this->name]==$file) {
						$take = true;
						$break = true;
					} else {
						$take = true;
					}
					if ($take) {
						$files[$i] = File::getFileInfo($ftp_path, $file);
						$this->fixFile($files[$i], $ftp_path, $http_path, $video);
						$i++;
						if ($break) break;
					}
				}
				closedir($dh);
			}
		}
		return $files;
	}
	
	public function get() {
			
	}
	

	
	public function form($set = 'upload', $extend = array(), $get_files = true){
		$this->set[$this->name] = $set;
		list ($id, $ftp_path, $http_path) = $this->listPath();

		$ret = array(
			'name'	=> $this->name,
			'id'	=> $this->id[$this->name],
			'hash'	=> Site()->genHash($this->id[$this->name], $this->name),
			'files'	=> ($get_files ? $this->getFiles($ftp_path, $http_path) : NULL),
			'ext'	=> File::uploadifyExt($this->ext[$this->name]),
			'desc'	=> $this->desc[$this->name],
			'main_photo' => $this->main_photo[$this->name]
		);
		$ret = array_merge($extend, $ret);
		if ($set) {
			if ($set===true) $set = $this->name.'_upload';
			$this->Index->My->set($set,$ret);
		}
		return $ret;
	}
	
	public function move() {
		list ($id_from, $ftp_path_from, $http_path_from) = $this->listPath(true);
		list ($id_to, $ftp_path_to, $http_path_to) = $this->listPath();
		if ($this->sizes[$this->name]) {
			$from = substr($ftp_path_from,0,-4);
			$to = substr($ftp_path_to,0,-4);
		} else {
			$from = $ftp_path_from;
			$to = $ftp_path_to;
		}
		$match = array();
		if ($from==$to) return $this;
		File::moveFiles($from, $to, $match);
		return $this;
	}


	public function crop($which,$sizes,$file) {
		if (!$sizes || !$sizes['x2'] || !$sizes['y2']) return;
		$w = $sizes['x2'] - $sizes['x1'];
		$h = $sizes['y2'] - $sizes['y1'];
		$x = $sizes['x1'];
		$y = $sizes['y1'];
		list ($id, $ftp_path, $http_path) = $this->listPath();
		$path = str_replace('/th1/','/'.$which.'/',$ftp_path);
		
		if (!is_file($path.$file)) return false;		
		return Factory::call('image')->driver('GD')->load($path.$file)->crop($x,$y,$w,$h)->save($path.$file);
	}

	public function id($id) {
		$this->id[$this->name] = $id;
		return $this;
	}	
	public function main_photo($main_photo) {
		$this->main_photo[$this->name] = $main_photo;
		return $this;
	}
	/*
	public function files($files) {
		if ($files===true) {
			
			
		} else {
			$this->files[$this->name] = $files;	
		}
		return $this;
	}
	*/
	public function head() {
		$this->Index->addJSA('swfobject.min.js');
		$this->Index->addJSA('uploadify/'.JQUERY_UPLOADIFY);
		return $this;	
	}
	
	public function th($th) {
		$this->th[$this->name] = $th;	
		return $this;
	}

	public function name($name) {
		$this->name = $name;
		
		if (!isset($this->names[$name])) {
			$this->limit[$name] = 20;
			$this->main_photo[$name] = '';
			$this->global[$name] = false;
			$this->tpl[$name] = 'includes/pic.tpl';
			$this->ext[$name] = array('jpeg','jpg','gif','png');
			$this->desc[$name] = 'Image files';
			$this->min[$name] = array(160,120);
			$this->folder[$name] = 'undefined';
			$this->th[$name] = 2;
			$this->one[$name] = false;
			$this->sizes[$name] = array(
				1	=> array(800, 600)
				,2	=> array(140, 140)
			);
			$this->in_folder[$name] = false;
			$this->no_id[$name] = false;
			$this->table[$name] = false;
			$this->sql[$name] = false;
		}
		$this->names[$name] = 1;
		return $this;	
	}
	public function not($not) {
		$this->not[$this->name] = $not;
		return $this;		
	}
	public function folder($folder, $global = false) {
		$this->folder[$this->name] = trim($folder,'/').'/';
		$this->global[$this->name] = $global;
		return $this;
	}
	public function one($one) {
		$this->one[$this->name] = $one;
		return $this;
	}
	public function sizes($arr) {
		$this->sizes[$this->name] = $arr;
		return $this;
	}
	public function tpl($tpl){
		$this->tpl[$this->name] = $tpl;
		return $this;
	}
	public function ext($ext){
		$this->ext[$this->name] = $ext;
		return $this;
	}
	public function desc($desc){
		$this->desc[$this->name] = $desc;
		return $this;
	}
	public function limit($limit){
		$this->limit[$this->name] = $limit;
		return $this;
	}
	public function in_folder($in_folder){
		$this->in_folder[$this->name] = $in_folder;
		return $this;
	}
	public function no_id($no_id){
		$this->no_id[$this->name] = $no_id;
		return $this;
	}
	public function table($table){
		$this->table[$this->name] = $table;
		return $this;
	}
	public function qry($qry){
		$this->qry[$this->name] = $qry;
		if (!$this->qry[$this->name]['offset']) $this->qry[$this->name]['offset'] = 0;
		if (!$this->qry[$this->name]['limit']) $this->qry[$this->name]['limit'] = 0;
		if (!$this->table[$this->name]['order']) $this->table[$this->name]['order'] = 'id';
		return $this;
	}
	public function th_folder($th_folder) {
		$this->th_folder = $th_folder;
		return $this;	
	}
	public function end() {
			
	}
	public function __toString() {
		return '';	
	}
}