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
* @file       inc/File.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

define('FILE_ERROR_NO_SOURCE', 100);
define('FILE_ERROR_COPY_FAILED', 101);
define('FILE_ERROR_DST_DIR_FAILED', 102);
define('FILE_COPY_OK', 103);

/**
* Files static functions
*/

abstract class File {
	
	const CHMOD_ALL  = 7;
	const CHMOD_READ  = 4;
	const CHMOD_WRITE  = 2;
	const CHMOD_EXECUTE  = 1;
	
	private static $loaded = false;
	
	public static function load() {
		if (self::$loaded) return;
		self::$loaded = true;
		require FTP_DIR_ROOT.'inc/FileFunctions.php';
		//Site::mem('FileFunctions.php load');
	}
	
	public static function chown2($filename) {
		self::load();
		return chown2($filename);
	}
	
	public static function isPicture($file, $hard = false, $ext = false) {
		if (!$ext) $ext = ext($file);
		$pic_ext = array('gif','jpg','jpeg','bmp','png','tif','tiff');
		$ret = in_array($ext,$pic_ext);
		if (!$ret) return false;
		if ($hard) {
			return ((is_file($file) && @getimagesize($file)) ? true : false);
		} else return $ret;
	}
	
	public static function isVideo($file) {
		if (!$ext) $ext = ext($file);
		$vid_ext = array('mpeg','avi','flv','mp4','mpg');
		$ret = in_array($ext,$vid_ext);
		return $ret;
	}
	
	public static function fitImage($f,$th,$w,$h) {
		if (is_array($f)) {
			if ($th==0) $th = $f['th'];
			else $th = '/th'.$th.'/';
			$file = str_replace('/th1/',$th,$f['path']).$f['file'];
		}
		else {
			$file = $f;	
		}
		if (!is_file($file)) return '';
		list ($_w, $_h) = @getimagesize($file);
		if (!$_w) return '';
		$top = -round($_h/2 - $h/2);
		$left = -round($_w/2 - $w/2);
		$ret = 'margin-top:'.$top.'px;margin-left:'.$left.'px;';
		return $ret;	
	}
	/*
	public static function ext($file, $pathinfo = PATHINFO_EXTENSION) {
		if (!$file) return '';
		if (preg_match('/\.(php|php3|php4|php5|phtml|inc|phpcgi)\.([^\.]+)$/i', $file)) return 'php';
		return strtolower(pathinfo($file, $pathinfo));
		/*
		$arr = explode('.',trim($file,'. '));
		$ret = (!isset($arr[1]) ? $file : strtolower($arr[count($arr)-1]));
		$return = ($ret && strlen($ret)<strlen($file)?$ret:NULL);
		return $return;
		
	}
	*/
	
	public static function moveFiles($dir, $to, $match = array(), $level = 0) {
		self::load();
		return fileMoveFiles($dir, $to, $match, $level);
	}
	
	private static $zip_dir = '';
	public static function dirToZip($dir,$root = '',$level = 0) {
		if (!$level) self::$zip_dir = $dir;
		$d = str_replace(self::$zip_dir,'',$dir);
		$dh = opendir($dir);
		$z = Factory::call('zip_compress');
		while ($file = readdir($dh)) {
			if ($file=='.' || $file=='..') continue;
			if (is_dir($dir.$file)) {
				self::dirToZip($dir.$file.'/',$root.'/'.$file,$level+1);
			} else {
				$z->addFile(file_get_contents($dir.$file),$d.$file);
			}
		}
		if (!$level) {
			$ex = explode('/',rtrim($dir,'/'));
			$name = end($ex).'.'.DOMAIN.'.zip';
			array_pop($ex);
			$file = join('/',$ex).'/'.$name;
			if (is_file($file)) unlink($file);
			file_put_contents($file, $z->file());
			return $file;
		}
	}
	
	
	public static function download($file_path, $admin = false) {
		self::load();
		return file_download($file_path, $admin);
	}


	public static function getPost() {
		$input = file_get_contents('php://input', 1000000);
		return Factory::call('json')->decode($input);	
	}
	
	public static function valid($file) {
		if (!$file || $file=='.htaccess' || $file=='.' || $file=='..' || strstr($file,'/')) return false;
		return !in_array(self::getMedia($file),array('exe','cgi','php'));
		/*
		ade, adp, chm, cmd, com, cpl, exe, hta, ins, isp, jse, lib, mde, msk, msp, mst, pif, scr, sct, shb, sys, vb, vbe, vbs, vxd, wsc, wsf, wsh
		*/
	}
	
	public static function ok($file) {
		if (!$file || $file=='.htaccess' || $file=='.' || $file=='..' || strstr($file,'/')) return false;
		return true;
	}
	
	public static function fixFileName($f) {
		$f = preg_replace('/\s+/','_',trim($f));
		$f = preg_replace("/[^ !$\(\)\[\],-\.0-9:;=@A-Z^_`a-z~\\x80-\\xFF+]|:/u", '',$f);
		$ext = ext($f);
		$name = self::nameOnly($f);
		return ($name ? $name.($ext?'.'.$ext:'') : date('His_dmY').($ext?'.'.$ext:''));
	}
	public static function fixFolderName($name='') {
		if ($name) $name = preg_replace('/[\/\?*\\\|><"]/', '',$name);
		return $name ? $name : date('d.m.Y');
	}
	
	public static function fixPath($path) {
		if (!$path) return '';
		$first_slash = substr($path,0,1)=='/';
		$path = str_replace('\\','/',$path);
		$e = explode('/', trim($path, '/.'));
		if (substr($path,0,1)=='/') $e[0] = '/'.$e[0];
		$c = count($e);
		$path = '';
		for($i = 0; $i <= $c; $i++) {
			if (isset($e[$i]) && $e[$i]) $path .= '/'.self::fixFolderName($e[$i]);
		}
		if ($first_slash) {
			$path = rtrim($path,'/');
		} else {
			$path = trim($path,'/');
		}
		return $path.'/';
	}

	public static function makePath($a, $b) {
		return self::fixPath($a).self::fixPath($a);
	}
	
	/*
	public static function e($filename) {
		return preg_replace('/[^\w\._]/', '_', $filename);
	}
	*/
	public static function name($file) {
		$ex = explode('.',$file);
		array_pop($ex);
		$ret = join('.',$ex);
		return $ret ? $ret : $file;
	}
	public static function nameOnly($file) {
		$file = self::fileOnly($file);
		return self::name($file);
	}
	
	public static function fileOnly($file) {
		$ex = explode('/',trim($file,'/'));
		return end($ex);
	}
	
	
	public static function getUnique($dir,$nameonly,$ext,$ex=false,$i=2) {
		self::load();
		return fileGetUnique($dir,$nameonly,$ext,$ex,$i);
	}
	
	
	public static function exists($filename) {
		if (is_file($filename)) return $filename;
		return false;
	}
	

	public static function copyDirectory($src,$dst,$options=array()) {
		self::load();
		return copyDirectory($src,$dst,$options);
	}

	
	public static function arrMime($ext = NULL) {
		self::load();
		return arrMime($ext);
	}

	public static function getMedia($filename='', $ext = false) {
		return self::getMediaType($filename, $ext);
	}
	public static function uploadifyExt($arr) {
		return '*.'.join(';*.',$arr);
	}
	public static function getMediaType($filename,$ext = false, $getArr = NULL) {
		self::load();
		return getMediaType($filename,$ext, $getArr);
	}
	

	
	
	public static function getUploadError($error) {
		self::load();
		return getUploadError($error);
	}
	
	
	public static function rmdir($dir, $del_last = true) {
		if (!is_dir($dir)) return false;
		$i = self::delFolder($dir,true,true,$del_last);
		self::delFolder($dir,true,false,$del_last);
		if ($del_last) @rmdir($dir);
		return $i;
	}
	
	public static function delFolder($dir, $recursive=true, $files = true, $del_last = true, $i = 0) {
		self::load();
		return fileDelFolder($dir, $recursive, $files, $del_last, $i);
	}

	public static function getRandom($folder) {
		$files = glob("$folder/*.*");
		shuffle($files);
		return $files[0];
	}
	
	public static function isWrongFile($filename,$ext=array()) {
		$arrFiles = array('index.php','Thumbs.db','desktop.ini','.htaccess');
		if (in_array($filename,$arrFiles)) return true;
		$arrExt = array('db');
		if (in_array($ext,$arrExt)) return true;
		return false;
	}
	
	public static function isThumb($file) {
		if (substr($file,0,3)=='th_') return true;
		return false;
	}
	
	public static function getFileNum($dir,$filename) {
		$arrFiles = self::openDirectory($dir);
		$lenFile = strlen($filename);
		$next = 0;
		foreach ($arrFiles as $file) {
			if ($filename.'_'==substr($file,0,$lenFile).'_') $next++;
		}
		return $next - 1;
	}
	
	public static function display_size($f, $colorize = false) {
		return Message::display_size($f, $colorize);
	}
	
	
	public static function getPhpTempFiles() {
		$ret = array();
		$dir = sys_get_temp_dir();
		$dir = '/tmp/';
		$files = self::openDirectory($dir,'/^php([^\.]+)\.TMP$/i');
		$i = 0;
		foreach ($files as $i => $file) {
			$time = filectime($dir.$file);
			$date = date('H:i:s d.m.Y',$time);
			$ret[$time.'_'.++$i] = array('tmp_name' => $file, 'filesize' => filesize($dir.$file), 'created' => $date, 'file' => $dir.$file);
		}
		asort($ret);
		return $ret;
	}

	
	public static function getFileInfo($dir, $file) {
		if (!is_file($dir.$file)) return array();
		$media = self::getMedia($file);
		$width = $height = 0;
		$ext = ext($file);
		$size = filesize($dir.$file);
		if ($media=='image' || $media=='flash') {
			list ($width, $height) = @getimagesize($dir.$file);
		}
		$ext_file = HTTP_DIR_TPLS.'img/ext/16/'.$ext.'.png';
		return array(
			'file'	=> $file,
			'ext'	=> $ext,
			'ext_file'	=> $ext_file,
			'path'	=> $dir,
			'width'	=> $width,
			'height'=> $height,
			'media'	=> $media,
			'size'	=> $size
		);	
	}
	
	public static function openDirectory($dir, $pattern=false, $func=false, $retDirs=false, $dirsOnly = false) {
		if (!$dir) return array();
		$dir = rtrim($dir,'/').'/';
		$er = error_reporting();
		if ($dir==FTP_DIR_PHPTEMP) error_reporting(0);
		elseif (!$pattern && !$func && !isset($_GET[URL_KEY_RESET]) && ($arr = Cache::get('dir_'.$dir,true))) return $arr;
		if (@is_dir($dir)) {
			if (!$dh = @opendir($dir)) return false;
			$file_arr = array();
			while (($file = @readdir($dh))!==false) {
				if (substr($file,0,1)=='.') continue;
				if ($dirsOnly) {
					if (!is_dir($dir.$file)) continue;
				} else {
					if (!$retDirs && is_dir($dir.$file)) continue;
					if (self::isWrongFile($file,ext($file))) continue;
				}
				if ($pattern && !preg_match($pattern,$file)) continue;
				if ($func && function_exists($func) && !$func($file)) continue;
				$file_arr[] = $file;
			}
			@closedir($dh);
			error_reporting($er);
			natsort($file_arr);
			if (!$pattern && !$func && !$retDirs) {
				if ($file_arr) Cache::save('dir_'.$dir,$file_arr,true);
				elseif (isset($_GET[URL_KEY_RESET])) Cache::delete('dir_'.$dir,true);
			}
			return $file_arr;
		} else {
			error_reporting($er);
			return array();
		}
	}
	
	public static function dir($dir, $pattern = false, $sort = false) {
		if (!is_dir($dir)) return false;
		if (!$dh = @opendir($dir)) return false;
		$ret = array();
		$i = 0;
		$sortTime = $sort=='time' || $sort=='time_DESC';
		while (($file = @readdir($dh))!==false) {
			if ($file=='..' || $file=='.') continue;	
			if ($pattern && !preg_match($pattern,$file)) continue;
			if ($sortTime) {
				$index = filemtime($dir.$file).'_'.$i;	
			} 
			else {
				$index = $i;	
			}
			$i++;
			$ret[$index] = self::char($file);
		}
		
		closedir($dh);
		if ($sortTime) {
			if ($sort=='time_DESC') {
				krsort($ret);
			} else {
				ksort($ret);
			}
			$ret = array_values($ret);
		}
		elseif ($sort=='name') {
			natsort($ret);
		}
		
		return $ret;
	}
	
	public static function formatSizeBy($int, $divider=1024) {
		return round($int/$divider,2);
	}
	
	public static function getFormatedSize($integer=0) {	
		$size_formats = array( 0=>'b',1=>'Kb',2=>'Mb',3=>'Gb',4=>'Tb');
		$i=0;
		while ($integer>1000 ){
			$integer = self::formatSizeBy($integer);
			$i++;
		}
		return isset($size_formats[$i]) ? $integer.' '.$size_formats[$i] : round($integer/1024,2).' Kb' ;
	}
	
	public static function checkDir($dir, $mode = 0755) {
		$dir = self::fixPath(dirname($dir));
		if (!is_dir($dir)) return self::makeDir($dir, $mode);
		elseif (!is_writable($dir)) {
			if (@chmod($dir,$mode)) {
				if (self::chown2($dir)) {
					return $dir;
				}
			}
			return false;
		}
		return $dir;
	}
	
	public static function mkdir($path, $mode) {
		return self::makeDir($path, $mode);
	}
	
	public static function makeDir($path, $mode = 0755) {
		$path = self::fixPath($path);
		$e = explode('/', trim($path, '/.'));
		if (substr($path,0,1) == '/') $e[0] = '/'.$e[0];
		$c = count($e);
		$cp = $e[0].'/'.$e[1].'/'.$e[2].'/'.$e[3];
		for ($i = 4; $i <= $c; $i++) {
			if (!is_dir($cp) && !mkdir($cp, $mode)) {
				return $cp.'/';
			}
			if (isset($e[$i]) && $e[$i]) $cp .= '/'.$e[$i];
		}
		return $cp.'/';
	}
	

	public static function contents($url) {
		return @file_get_contents( 
			$url, 
			NULL, 
			stream_context_create( 
				array( 
					'http'	=> array( 
						'method'=> 'GET', 
						'header'=> "Referer: http://".$_SERVER['HTTP_HOST']."/\r\n" 
					) 
				)
			) 
		); 	
	}
	
	
	public static function url($url, $postdata = '', $use_cookie = false, $javascript_loop = 0, $timeout = 5) {
		self::load();
		return get_url($url, $postdata, $use_cookie);
	}
	
	public static function xml2array($contents, $get_attributes=1, $priority = true) {
		self::load();
		return xml2array($contents, $get_attributes, $priority);
	}
	
	public static function _xml2array($contents, $get_attributes=1, $priority = true) {
		self::load();
		return _xml2array($contents, $get_attributes, $priority);
	}	
	
	public static function char($file) {
		return $file;
		return iconv('windows-1251','utf-8',$file);	
	}
	public static function unchar($file) {
		return $file;
		return iconv('utf-8','windows-1251',$file);	
	}
}