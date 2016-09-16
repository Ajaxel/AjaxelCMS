<?php
session_name('PHPSESSID');
session_start();
error_reporting(E_ALL & ~E_NOTICE);
if (!isset($_SESSION['RIGHTS']) || !$_SESSION['RIGHTS']['filebrowser']) {
	die('No Access');
}
$getdata['src'] = './../../../../../';
if ($_SESSION['RIGHTS']['filebrowser_user_path']) {
	$ajaxel_folder = 'user/' . $_SESSION['RIGHTS']['filebrowser_user_path'];
} else {
	$ajaxel_folder = 'editor';
}
$http['image']   = $_SESSION['FTP_EXT'] . 'files/' . $ajaxel_folder;
$http['flash']   = $_SESSION['FTP_EXT'] . 'files/' . $ajaxel_folder . '';
$http['file']    = $_SESSION['FTP_EXT'] . 'files/' . $ajaxel_folder . '';
$folder['image'] = $getdata['src'] . 'files/' . $ajaxel_folder . '/';
$folder['flash'] = $getdata['src'] . 'files/' . $ajaxel_folder . '/';
$folder['file']  = $getdata['src'] . 'files/' . $ajaxel_folder . '/';
if ($_SESSION['type'] == "image") {
	$work_folder   = $folder['image'];
	$http_folder   = $http['image'];
	$enabled_files = "jpeg jpg gif bmp png tiff";
} elseif ($_SESSION['type'] == "flash") {
	$work_folder   = $folder['flash'];
	$enabled_files = "swf";
	$http_folder   = $http['flash'];
} else {
	$work_folder   = $folder['file'];
	$enabled_files = "psd ai jpg gif bmp png swf mov mp4 avi mpeg mp3 wav wmv mid pdf doc txt rtf xml xls js htm html css zip rar gz tgz bz2 7zip";
	$http_folder   = $http['file'];
}
function check_dir_addres($string) {
	$data = explode('/', str_replace('\\', '/', $string));
	foreach ($data as $val) {
		$val = trim($val);
		if (!in_array($val, array(
			'.',
			'..'
		)) && $val) {
			if ($new_string) {
				$new_string .= "/";
			}
			$new_string .= $val;
		}
	}
	if ($new_string)
		$new_string .= "/";
	return $new_string;
}
function arrayColumnSort() {
	$n  = func_num_args();
	$ar = func_get_arg($n - 1);
	if (!is_array($ar))
		return false;
	for ($i = 0; $i < $n - 1; $i++)
		$col[$i] = func_get_arg($i);
	foreach ($ar as $key => $val)
		foreach ($col as $kkey => $vval)
			if (is_string($vval))
				${"subar$kkey"}[$key] = $val[$vval];
	$arv = array();
	foreach ($col as $key => $val)
		$arv[] = (is_string($val) ? ${"subar$key"} : $val);
	$arv[] = $ar;
	call_user_func_array("array_multisort", $arv);
	return $ar;
}
function folderSize($path) {
	$dir  = new RecursiveDirectoryIterator($path);
	$size = 0;
	foreach (new RecursiveIteratorIterator($dir) as $file) {
		$size += filesize($file);
	}
	return $size;
}
function recursiveRemoveDirectory($path) {
	$dir = new RecursiveDirectoryIterator($path);
	foreach (new RecursiveIteratorIterator($dir) as $file) {
		@unlink($file);
	}
	foreach ($dir as $subDir) {
		if (!@rmdir($subDir)) {
			recursiveRemoveDirectory($subDir);
		}
	}
	rmdir($path);
}
function size_selector($filesize) {
	if ($filesize >= 1024 * 1024 * 1024) {
		$size = round($filesize / 1024 / 1024 / 1024, 2) . " Gb";
	} elseif ($filesize >= 1024 * 1024) {
		$size = round($filesize / 1024 / 1024, 2) . " Mb";
	} elseif ($filesize >= 1024) {
		$size = round($filesize / 1024, 2) . " Kb";
	} elseif ($filesize >= 1) {
		$size = $filesize . " b";
	} else {
		$size = "0 b";
	}
	return $size;
}
function get_all_dirs($path) {
	global $enabled_dirs_array;
	$enabled_dirs_array = array();
	function inside_get_all_dirs($path) {
		global $enabled_dirs_array;
		$dir = opendir($path);
		while (false !== ($file = readdir($dir))) {
			if (is_dir($path . $file) && $file != "." && $file != "..") {
				$enabled_dirs_array[] = $path . $file;
				inside_get_all_dirs($path . $file . "/");
			}
		}
		return $enabled_dirs_array;
	}
	inside_get_all_dirs($path);
	return array_unique($enabled_dirs_array);
}
function file_name_check($file_name) {
	$file_name = trim($file_name);
	$w['à']   = "a";
	$w['á']   = "b";
	$w['â']   = "v";
	$w['ã']   = "g";
	$w['ä']   = "d";
	$w['å']   = "e";
	$w['¸']   = "e";
	$w['æ']   = "z";
	$w['ç']   = "z";
	$w['è']   = "i";
	$w['é']   = "j";
	$w['ê']   = "k";
	$w['ë']   = "l";
	$w['ì']   = "m";
	$w['í']   = "n";
	$w['î']   = "o";
	$w['ï']   = "p";
	$w['ð']   = "r";
	$w['ñ']   = "c";
	$w['ò']   = "t";
	$w['ó']   = "u";
	$w['ô']   = "f";
	$w['õ']   = "h";
	$w['ö']   = "ts";
	$w['÷']   = "ts";
	$w['ø']   = "sh";
	$w['ù']   = "sh";
	$w['ü']   = "";
	$w['ú']   = "";
	$w['û']   = "q";
	$w['ý']   = "e";
	$w['þ']   = "ju";
	$w['ÿ']   = "ja";
	foreach ($w as $rus => $eng) {
		$file_name = str_replace($rus, $eng, $file_name);
		$file_name = str_replace(strtoupper($rus), strtoupper($eng), $file_name);
	}
	$file_name = str_replace("-", "_", $file_name);
	$file_name = str_replace(":", "_", $file_name);
	$file_name = str_replace("?", "_", $file_name);
	$file_name = str_replace("|", "_", $file_name);
	$file_name = str_replace("*", "_", $file_name);
	$file_name = str_replace(" ", "_", $file_name);
	$file_name = str_replace("__", "_", $file_name);
	$file_name = str_replace("<", "", $file_name);
	$file_name = str_replace(">", "", $file_name);
	$file_name = str_replace("'", "", $file_name);
	$file_name = str_replace('"', "", $file_name);
	$file_name = str_replace("`", "", $file_name);
	$file_name = str_replace(",", "_", $file_name);
	$file_name = str_replace("/", "", $file_name);
	$file_name = str_replace(trim("\ "), "", $file_name);
	return trim($file_name);
}
