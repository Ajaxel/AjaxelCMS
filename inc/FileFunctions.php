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
* @file       inc/FileFunctions.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

function fileGetUnique($dir,$nameonly,$ext,$ex=false,$i=2) {
	if ($i>1000) return $nameonly.'_'.rand(0,99999).'_'.$i.'.'.$ext;
	if (!$ex) {
		if (!$nameonly) $nameonly = md5(microtime());
		$dir = File::fixPath($dir);
		if (CONV_FILES_LATIN) {
			$nameonly = File::fixFileName($nameonly);
			$nameonly = Parser::toLat($nameonly);
			$nameonly = preg_replace('/[^A-Za-z0-9\.\_\-\(\)\[\]]/U','_',$nameonly);
			$nameonly = str_replace('__','_',$nameonly);
			$nameonly = str_replace('__','_',$nameonly);
		} else {
			$nameonly = File::fixFileName($nameonly);
		}
		$nameonly = rtrim(trim(preg_replace('/^(.*)\(([0-9]+)\)$/U','\\1',$nameonly)),'_');
		if (is_file($dir.$nameonly.'.'.$ext)) {
			return fileGetUnique($dir,$nameonly,$ext,true,$i);
		} else {
			return $nameonly.'.'.$ext;
		}
	}
	elseif (is_file($dir.$nameonly.'('.$i.').'.$ext)) {
		return fileGetUnique($dir,$nameonly,$ext,true,$i+1);
	} else {
		return $nameonly.'('.$i.').'.$ext;
	}
}

function fileMoveFiles($dir, $to, $match = array(), $level = 0) {
	$i = 0;
	$dir = rtrim($dir,'/').'/';
	$to = rtrim($to,'/').'/';
	if (!is_dir($dir)) return 0;
	if (!is_dir($to)) File::makeDir($to,0777);
	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if ($file=='.' || $file=='..') continue;
		if (is_dir($dir.$file)) {
			$i += fileMoveFiles($dir.$file.'/', $to.$file.'/', $match, $level+1);
		} else {
			if ($match && !in_array($file, $match)) continue;
			if (is_file($to.$file)) unlink($to.$file);
			if (rename($dir.$file,$to.$file)) {
				$i++;
			}
		}
	}
	closedir($dh);
	if (!$level && !$match) {
		fileDelFolder($dir, true, false, true);
	}
	return $i;
}

function fileDelFolder($dir, $recursive=true, $files = true, $del_last = true, $i = 0) {	
	if (!is_dir($dir)) return false;
	if (!$i) Conf()->s('delFolder',0);
	$dir = File::fixPath($dir);
	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if ($file=='.' || $file=='..') continue;
		if ($files && is_file($dir.$file)) {
			@chmod($dir.$file,0777);
			chown2($dir.$file,File::CHMOD_READ | File::CHMOD_WRITE);
			if (unlink($dir.$file)) {
				Conf()->plus('delFolder',1);
			}
		}
		elseif (is_dir($dir.$file)) {
			fileDelFolder($dir.$file.'/', $recursive, $files, $del_last, $i + 1);
		}
	}
	closedir($dh);
	if (is_dir($dir) && !$files) {
		if ((!$i && $del_last) || $i) {
			if (@rmdir($dir)) {
				Conf()->plus('delFolder',1);	
			}
		}
	}
	if (!$i) {
		$ret = Conf()->g('delFolder');
		Conf()->s('delFolder',0);
		return $ret;
	}
}

function file_download($file_path, $admin = false) {
	$dir = false;
	if (!is_file($file_path)) {
		if ($admin && is_dir($file_path)) {
			$file_path = rtrim($file_path,'/').'/';
			$dir = true;
		} else {
			die('Such file '.$file_path.' doesn\'t exist');
		}
	}
	if ($dir) {
		$type = 'application/zip';
		$file_path = File::dirToZip($file_path);
		$ex = explode('/',$file_path);
		$name = end($ex);
		$size = filesize($file_path);
	} else {
		$arrMime = arrMime();
		$size = filesize($file_path);
		$name = fileOnly($file_path);
		$ext = ext($name);
		$type = @$arrMime[$ext];
		if (!$type) {
			$type = '';
			if (function_exists('mime_content_type')) $type = mime_content_type($file_path);
			else if (function_exists('finfo_file')) {
				$info = finfo_open(FILEINFO_MIME);
				$type = finfo_file($info, $file_path);
				finfo_close($info);  
			}
			if (!$type) $type = 'application/force-download';
		}
	}
	
	@ob_end_clean();
	
	if(ini_get('zlib.output_compression'))
	ini_set('zlib.output_compression', 'Off');
	
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private',false); 
	header('Content-Type: '.$type);
	header('Content-Disposition: attachment; filename="'.$name.'"');
	header('Content-Transfer-Encoding: binary');
	
	if (isset($_SERVER['HTTP_RANGE'])) { 
		list ($a, $range) = explode('=',$_SERVER['HTTP_RANGE']); 
		str_replace($range, "-", $range); 
		$size2 = $size-1; 
		$new_length = $size-$range;
		$bytes = $range*$size2/$size;
		header("HTTP/1.1 206 Partial Content"); 
		header("Content-Length: $new_length"); 
		header("Content-Range: bytes $bytes"); 
	} else {
		header('Content-Length: '.$size); 
	}
	
	$file = fopen($file_path,'rb');
	if (isset($_SERVER['HTTP_RANGE'])) fseek($file, $range); 
	while(!feof($file)) {
		print(fread($file, 1024*1024));
		flush();
		if (connection_status()) {
			fclose($file);
		}
	}
	fclose($file);
	if ($dir) @unlink($file_path);
	exit;
}


function getMediaType($filename,$ext = false, $getArr = NULL) {
	if (!$ext) $ext = ext($filename);
	
	$arr = array(
		// wrapper
		'image'	=> array('jpg', 'jpe', 'jpeg', 'png', 'gif', 'bmp','tif','tiff'),
		'audio' => array('aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'),
		'video' => array('asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv', 'm4v'),
		'flash'	=> array('swf','flv'),
		
		// doc - idea => screenshot me
		'doc'	=> array('doc','docx','rtf','pdf','ppt','pptx','txt','xls','xlsx'),
		
		// download
		'doc2' 	=> array('pages','odt','key','odp','xls','xlsx','numbers','ods'),
		'work'	=> array('fla','psd'),
		'zip' 	=> array('tar','bz2','gz','cab','dmg','rar','sea','sit','sqx','zip'),
		
		'exe'		=> array('exe', 'scr', 'dll', 'msi', 'vbs', 'bat', 'com', 'pif', 'cmd', 'vxd', 'cpl'),
		'cgi'		=> array('shtml', 'jhtml', 'pl', 'py', 'cgi'),
		'html'		=> array('html', 'htm', 'js', 'jsb', 'mhtml', 'mht'),
		'php'		=> array('php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phpt')
	);
	$ret = 'unknown';
	if ($getArr) return $arr[$getArr];
	foreach ($arr as $type => $extensions) {
		if (in_array($ext,$extensions)) {
			$ret = $type;
			break;
		}
	}
	return $ret;
}

function informWebmaster($s,$m,$backtrace=true) {
	if (!defined('MAIL_WEBMASTER') || !MAIL_WEBMASTER) return false;
	$n = "\r\n";
	$file = FTP_DIR_ROOT.'files/temp/inform_admin.txt';
	if (is_file($file)) $c = file_get_contents($file); else $c = -1;
	if ($c===$s) return false;
	
	$ssl = false;
	if (isset($_SERVER['HTTPS'])) {
		if ('on'== strtolower($_SERVER['HTTPS'])) $ssl = true;
		if ('1'==$_SERVER['HTTPS']) $ssl = true;
	} 
	elseif (isset($_SERVER['SERVER_PORT']) && ('443'==$_SERVER['SERVER_PORT'])) {
		$ssl = true;
	}
	$url = 'http'.($ssl?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$m = '<div style="font:bold 12px Verdana;padding-bottom:10px">'.$s.'</div><div style="font:12px Verdana;padding-bottom:20px;border-bottom:1px dashed #ccc">'.$m.'</div>'.$n;
	$m .= '<div style="padding-bottom:5px;margin-top:5px;border-bottom:1px dashed #ccc;font:12px Verdana;color:blue">URL: <a href="'.$url.'" style="font:12px Verdana">'.$url.'</a> <small style="color:#666">('.SITE_TYPE.')</small> <small style="color:#f1f1f1">['.AJAXEL_LICENSE_TYPE.']</small></div>'.$n;
	$m .= '<div style="padding-bottom:5px;margin-top:5px;font:12px Verdana;color:red;border-bottom:1px dashed #ccc">Server Load: '.getServerLoad().'</div>'.$n;
	$m .= '<div style="padding-bottom:5px;margin-top:5px;font:12px Verdana;color:#666666;border-bottom:1px dashed #ccc">User IP: '.Session::getIP().'</div>'.$n;
	$m .= '<div style="padding-bottom:5px;margin-top:5px;font:12px Verdana;color:#506011;border-bottom:1px dashed #ccc">Date: '.date('d M Y, H:i').'</div>'.$n;
	if ($_POST) {
		$m .= '<div style="padding-bottom:5px;border-bottom:1px dashed #ccc;margin-top:5px;"><div style="font:12px Verdana;color:#3333FF;margin-bottom:6px">$_POST:</div>'.$n._p($_POST,0).'</div>'.$n;
	}
	if ($_GET) {
		$m .= '<div style="padding-bottom:5px;border-bottom:1px dashed #ccc;margin-top:5px;"><div style="font:12px Verdana;color:#3333FF;margin-bottom:6px">$_GET:</div>'.$n._p($_GET,0).'</div>'.$n;
	}
	if ($_SERVER) {
		$m .= '<div style="padding-bottom:5px;border-bottom:1px dashed #ccc;margin-top:5px;"><div style="font:12px Verdana;color:#3333FF;margin-bottom:6px">$_SERVER:</div>'.$n._p($_SERVER,0).'</div>'.$n;
	}
	if (class_exists('Session') && function_exists('Session')) {
		$arr = Session()->get(false);
		if ($arr) {
			unset($arr['profile']);
			$m .= '<div style="padding-bottom:5px;border-bottom:1px dashed #ccc;margin-top:5px;"><div style="font:12px Verdana;color:#3333FF;margin-bottom:6px">Session():</div>'.$n._p($arr,0).'</div>'.$n;
		}
	}
	if ($backtrace) {
		$m .= '<div style="padding-bottom:5px;border-bottom:1px dashed #ccc"><div style="margin-top:5px;padding-bottom:5px;font:12px Verdana;color:#CC0066">Debug Backtrace:</div>'.printDebugBacktrace(debug_backtrace(), true, false).'</div>';
	}
	$m .= '<div style="border-left:15px solid #CEDBE3;margin-top:5px;color:#333;font:12px \'Trebuchet MS\', Verdana;padding:2px 6px;"><a href="http://ajaxel.com" style="font:bold 13px \'Trebuchet MS\', Verdana;text-decoration:none">Ajaxel CMS</a><br>Ajaxified desktop alike super simple content management system<br>+372 56759366<br><a href="mailto:ajaxel.com@gmail.com" style="font:12px \'Trebuchet MS\', Verdana;none;color:#333">ajaxel.com@gmail.com</a><br><a href="http://ajaxel.com">www.ajaxel.com</a></div>';
	file_put_contents($file,$s);
	if (!defined('MAIL_EMAIL') || !MAIL_EMAIL) $from = 'debug@'.$_SERVER['HTTP_HOST']; else $from = MAIL_EMAIL;
	$h = "From: ".$from."\r\nContent-Type: text/html; charset=utf-8\r\nMIME-Version: 1.0\r\n";
	$m = wordwrap($m,76,"\n");
	return mail(MAIL_WEBMASTER,$s,$m,$h);
}



function _p($v = 'Debug Backtrace', $echo = true, $trace = true, $depth = 0, $objects = array(), $array_key = '') {	
	$ret = '';
	$max = 1024000 * 2;
	$s = $e = '';
	
	if (!$depth) {
		$td_font = 'normal 12px Tahoma';
		$th_font = 'bold 11px Arial';
		
		if ($v && (is_array($v) || is_object($v))) {
			if (!Conf()->g('debuggg_seen')) {
				$top = '</select></textarea><a name="top" id="top"></a><style type="text/css">
html>body{overflow:auto!important}
.d-t_object{text-transform:none!important;text-align:left;margin:1px 0 1px -1px!important;padding:0px;outline:1px solid #FF4444;font:'.$td_font.'!important;color:#000!important;width:auto!important;background:#fff!important}
.d-t_array{text-transform:none!important;text-align:left;margin:1px 0 1px -1px!important;padding:0px;outline:1px solid #4682B4;font:'.$td_font.'!important;color:#242424;background:#fff!important;position:relative;z-index:1000;width:auto!important;background:#fff!important}
.d-t_array>tbody>tr>th{text-transform:none!important;height:auto!important;background:#fff!important;vertical-align:top;border:none;padding:0!important;width:20px!important;white-space:nowrap!important;font:'.$th_font.'!important;color:#000!important;border-bottom:1px solid #dedede!important;border-top:none!important;border-right:1px solid #dedede!important;border-left:none!important;vertical-align:top!important;width:auto}
.d-t_array>tbody>tr>td{text-transform:none!important;height:auto!important;font:'.$td_font.'!important;color:#000!important;text-align:left!important;background:#fff!important;padding:2px 4px 2px 3px!important;border-bottom:1px solid #dedede!important;border-top:none!important;vertical-align:top}
.d-t_object>thead>tr>td{text-transform:none!important;border-bottom:1px solid #dedede!important;border-top:none!important;background:#FFECE8;color:#991102;padding:2px 4px!important;'.$td_font.'}
.d-t_object>tbody>tr>th{text-transform:none!important;vertical-align:top;padding:1px 4px!important;width:20px!important;white-space:nowrap!important;font:'.$th_font.'!important;color:#000!important;border-bottom:1px solid #dedede!important;border-top:none!important;border-right:1px solid #dedede!important;border-left:none!important;}
.d-t_object>tbody>tr>th>a{font:'.$th_font.'!important;color:#000!important;text-decoration:none;text-align:left}
.d-t_object>tbody>tr>th>a:hover{text-decoration:underline}
.d-t_object>tbody>tr>td{text-transform:none!important;padding:0!important;border-bottom:1px solid #dedede!important;border-top:none!important;vertical-align:top}
.d-t_object>tbody>tr>td>div{padding:2px 4px 2px 3px}
.d-t_object>tbody>tr>th>div, .d-t_array>tbody>tr>th>div{text-transform:none!important;background:#f1f1f1!important;color:#000!important;padding:2px 4px 2px 3px!important;text-align:left!important}
.d-t_object>tbody>tr>td font, .d-t_array>tbody>tr>td font {text-transform:none!important;font-size:12px!important;font-family:Geneva;-font-style:normal;line-height:14px;}
.d-t_object a, .d-t_array a{color:blue;text-decoration:underline}
.d-t_object a:hover, .d-t_array a:hover{text-decoration:none}
.d-t_object>tbody>tr.d-tr_method th{text-align:left;font-weight:normal!important;}
.d-t_object>tbody>tr.d-tr_method a{color:#555;text-decoration:none;font-size:12px!important}
.d-t_object>tbody>tr.d-tr_method a:hover{text-decoration:underline}
.d-t_object pre, .d-t_array pre {font:'.$td_font.';padding:0!important;margin:0!important}
</style>';
				$ret .= $top;
				Conf()->s('debuggg_seen',true);
			}
		} else {
			$s = '<table cellpadding="0" cellspacing="0" style="text-align:left;margin:1px 0 1px -1px!important;outline:1px solid #CC9900;color:#242424!important;background:#FFF!important;position:relative;z-index:1000"><tr><td style="background:#fff!important;height:auto!important;text-align:left!important;padding:2px 4px!important;font:'.$td_font.'!important">';	
			$e = '</tr></table>';
		}
	}
	

	$si = $s.'<i>';
	$ei = '</i>'.$e;
	$span_a = '<div>';
	$span_b = '</div>';
	
	if ($v!=='Debug Backtrace' || $depth<4) {
		if (!isset($v)) $ret .= "$si<font color=#9A9A9A>(null)</font>$ei";
		elseif ($v === false) $ret .= $s.'<font color=red><b><i>false</i></b></font>'.$e;
		elseif ($v === true) $ret .= $s.'<font color=green><b><i>true</i></b></font>'.$e;
		elseif (is_array($v) && !$v) $ret .= "$si<font color=#3D8C2F>(empty array)</font>$ei";
		elseif (is_string($v) && !strlen($v)) $ret .= "$si<font color=#AA3524>(empty string)</font>$ei";
		elseif (is_numeric($v) && !$v) $ret .= "$si<font color=#415283>(number null)</font>$ei";
		elseif (is_resource($v)) $ret .= "$si<font color=#41A5BA>".sprintf('<b>Resource: %s</b>', get_resource_type($v))."</font>$ei";
		elseif (is_array($v)) {
			if ($depth>9) return '<div style="background:#000099;color:#FFF"><b><i>*** ARRAY RECURSION ***</i></b></div>';
			$ret .= '<table cellspacing=0 class="d-t_array"><tbody>';
			foreach ($v as $k => $j) {
				if ($depth==1 && ($k==='GLOBALS' || $k==='HTTP_SESSION_VARS')) continue;
				if (strlen($ret)>$max) $arr = 'Exit';
				else $arr = _p($j,false,$trace,$depth+1,$objects,$k);
				$ret .= '<tr><th>'.$span_a.(strlen($k)?htmlspecialchars($k):'&nbsp;').$span_b.'</th><td>'.$arr.'</td></tr>';
				if ($arr==='Exit') break;
			}
			$ret .= '</tbody></table>';
		}
		elseif (is_object($v)) {
			
			$class = get_class($v);
			if ($class=='MySmarty') {
				$ret .= '<table cellspacing=0 class="d-t_object"><thead><tr valign="top"><td colspan=2>object '.$class.'</td></tr></thead>';
				$ret .= '<tbody><tr><td><div><i>assign(), display(), fetch()</i> (read <a href="http://www.smarty.net/docs/'.LANG.'/" target="_blank">Smarty documentation</a>)</i></div></td></tr>';
				$ret .= '</table>';
			}
			elseif ($class=='Closure') {
				$reflection = new ReflectionFunction($v);
				$arguments  = $reflection->getParameters();
				$ret .= '<table cellpadding=0 class="d-t_object">';
				$ret .= '<thead><tr valign="top"><td colspan=2>class '.$class.'</td></tr></thead><tbody>';
				foreach ($arguments as $a) {
					$ret .= '<tr><th>'.$span_a.'$'.$a->getName().$span_b.'</th><td>'.$si.($a->isOptional() ? '<font color=#A5B015>optional ('.getArgument($a->getDefaultValue(), $a->getName()).')</font>':'<font color=#C11E6B>required</font>').($a->isPassedByReference() ? ', <font color=#181173>passed by reference</font>':'').$ei.'</td></tr>';	
				}
				$ret .= '</tbody></table>';
				
			} else {
				if ($depth) $objects[] = $class;
				$ret .= '<table cellpadding=0 class="d-t_object">';
				$ret .= '<thead><tr valign="top"><td colspan=2>object '.$class.'</td></tr></thead><tbody>';
				foreach ($v as $key => $value) {
					if ($depth && is_object($value) && get_class($value)!=$class && in_array(get_class($value),$objects)) {
						return '<div style="background:#C01401;color:#FFF;"><b><i>*** OBJECT RECURSION ***</i></b></div>';
					} else {
						$use_a = $class!='stdClass';
						$ret .= '<tr><th>'.$span_a.htmlspecialchars($key).$span_b.'</th><td>'._p($value,false,$trace,$depth+1,$objects).'</td></tr>';
					}
				}		
				foreach (get_class_methods($v) as $value) {
					$ret .= '<tr class="d-tr_method"><th><i>'.$class.'::'.$value.'()</i></th><td>{...}</td></tr>';
				}		
				$ret .= '</tbody></table>';
			}
		}
		else {
			$ret .= $s.parseDumpString($v,$array_key).$e;
		}
	}
	elseif ($v==='Debug Backtrace') {
		$db = debug_backtrace();
		unset($db[0]);
		$ret = '<a name="top"></a></select></textarea><table style="text-align:left;margin:20px;border-left:1px solid #ccc;border-top:1px solid #ccc;border-right:2px solid #ccc;border-bottom:2px solid #ccc;font:12px Tahoma;background:#f5f5f5;width:550px"><tr><td style="font:bold 13px Verdana;padding:5px;background:#CC0000;color:#fff">Debug backtrace</td></tr><tr><td>';
		$ret .= printDebugBacktrace($db);
		$ret .= '</td></tr></table>';
	}
	
	if ($depth==0 && !Conf()->g('debug_seen_i')) {
		$rnd = rand(111,999);
		$db = printDebugBacktrace(debug_backtrace(),true,false,true);
		$ret .= '<a href="javascript:;" onclick="this.style.display=\'none\';document.getElementById(\'debug_backtrace_'.$rnd.'\').style.display=\'inline-block\';" style="color:#444!important;font:11px Tahoma;outline:1px solid green;font:11px Tahoma;display:inline-block;padding:2px 4px!important;margin:3px 0 3px -1px">'.$db[0].'</a><div id="debug_backtrace_'.$rnd.'" style="outline:1px solid green;font:11px Tahoma;display:none;padding:2px 4px;margin:3px 0 3px -1px">';
		$c = count($db);
		foreach ($db as $i => $d) $ret .= $c-$i.'. '.$d.'<br />';
		$ret .= '</div><div style="clear:both;height:15px"></div>';
		Conf()->s('debug_seen_i',true);
	}
	if ($echo) {
		echo $ret;
	} else {
		return $ret;
	}
}

function printArguments($args) {
	$s = $ret = '';
	foreach((array)$args as $arg ) {
		$ret .= $s.getArgument($arg);
		$s = ', ';
	}
	return $ret;
}

function getArgument($arg) {
	switch (strtolower(gettype($arg))) {
		case 'string':
			$arg = htmlspecialchars($arg);
			if (strlen($arg)>60) $arg = substr($arg,0,20).'...'.substr($arg,-20);
			return '<font color=#CC0000>\''.$arg.'\'</font>';
		case 'boolean':
			return $arg ? '<b style="color:green">true</b>' : '<b style="color:red">false</b>';
		case 'object':
			return '<font color=#CC0033>object('.get_class($arg).')</font>';
		case 'array':
			return '<font color=#006600>array('.count($arg).')</font>';
		case 'resource':
			return 'resource('.get_resource_type($arg).')';
		case 'integer':
			return '<font color=#3366FF>'.(int)$arg.'</font>';
		case 'float':
		case 'double':
			return '<font color=#0066CC>'.(float)$arg.'</font>';
		case 'null':
			return '<font color=#888888>NULL</font>';
		default:
			return '\'unknown type: '.gettype($arg).'\'';
	}
}
function parseDumpString($s,$array_key = '') {
	if (!is_scalar($s)) return $s;
	if (substr($s,0,27)=='<a name="top"></a></select>') return $s;
	if (SUPER_ADMIN!=USER_ID && is_string($array_key) && (in_array(strtolower($array_key), array('password','code')) || substr(strtolower($array_key),0,8)=='password') && ($l = strlen($s))>4) $s = str_repeat('*',$l);
	if ($array_key && is_string($s)) $s = wordwrap($s,70);
	if (!strpos($s,"\n")) {
		$s = str_replace("\t",'&nbsp; &nbsp; ',nl2br(@htmlspecialchars($s)));
	} else {
		$s = '<pre'.($array_key?'':' style="font:normal 12px Tahoma"').'>'.html(str_replace('    ',"\t",$s)).'</pre>';
	}
	if (strstr($s,'://') && class_exists('Parser') && Parser::isURL($s)) {
		$s = '<a href="'.$s.'" style="color:#000" target="_blank">'.$s.'</a>';	
	}
	if (is_numeric($s)) {
		if (strlen($s)==10) {
			$s = '<span style=color:#9900CC onclick="this.innerHTML=\''.$s.'\';this.style.color=\'#000099\'">'.date('H:i:s d-M-Y',$s).'</span>';
		} else {
			$s = '<span style=color:#000099>'.$s.'</span>';
		}
	}
	elseif (is_string($s)) {
		$s = '<span style=color:#222>'.wordwrap($s, 300).'</span>';
	}
	
	return $s;
}

function pjs($array, $depth = 1) {
	$r = '';
	if ($depth>6) return '*RECURSIVE*';
	if (!is_array($array)) return htmlspecialchars($array);
	foreach ($array as $key => $val) {
		if (is_object($val)) continue;
		if (is_array($val)) $r .= '
'.$key.': {'.djs($array[$key], $depth++).'}, ';
		else $r .= '
'.$key.': '.htmlspecialchars($val);
	}
	return substr($r,0,-2);
}

function _sql($sql,$fieldset = true,$return = false) {
	if (!$sql) {
		return lang('$SQL query was empty');
	}
	$r = '';
	if (is_array($sql)) {
		$r .= '<table style="margin:10px;" align="center" width="100%" border="0" cellpadding="4" cellspacing="0" class="a-sql">';
		$oldTime = Conf()->g('START_TIME');
		foreach ($sql as $s) {
			if (!$s) continue;
			$r .= '<tr><td>'.wordwrap(sql($s,true,true),100,"\n",1).'</td></tr>';
		}
		$r .= '</table>';
		Conf()->s('code_no_wrap', false);
		if ($return) return $r; else echo $r;
	} else {
		Conf()->s('code_no_wrap', true);

		$r = '<div class="a-sql">'.Parser::geshiHighlight($sql,'mysql').'</div>';
		$r = str_replace('[php]','&lt;?php',$r);
		$r = str_replace('[phpe]','&lt;?=',$r);
		$r = str_replace('[phps]','&lt;?',$r);
		$r = str_replace('[/php]','?&gt;',$r);
		Conf()->s('code_no_wrap', false);
		if ($return) return $r;
		else echo $r;
	}
}

function printDebugBacktrace($backtrace, $p = true, $file = true, $as_array = false) {
	if (!DEBUG_BACKTRACE) return false;
	if (!is_array($backtrace)) return '';
	$skip = array('myerrorhandler','myExceptionHandler','trigger_error','dberror','backtrace','_p');
	$skipDB = array('error');
	$i = 0;
	$arr_trace = array();
	foreach ($backtrace as $v) {
		if (!isset($v['file']) || strstr($v['file'],'Smarty.class.php') || in_array(strtolower($v['function']),$skip)) continue;
		if (isset($v['class']) && $v['class']=='DB' && in_array(strtolower($v['function']),$skipDB)) continue;
		$i++;
		if ($as_array) {
			$trace = $v['file'].' on line '.$v['line'].' ';
			if (isset($v['class'])) {
				$trace .= '<font color=#6633CC>'.$v['class'].'::'.$v['function'].'(';
				if (isset($v['args'])) {
					$trace .= printArguments($v['args']);
				}
				$trace .= ');</font>';
			}
			elseif (isset($v['function'])) {
				$trace .= '<font color=#0033CC>'.$v['function'].'(';
				if (!empty($v['args'])) {
					$trace .= printArguments($v['args']);
				}
				$trace .= ');</font>';
			}
		} else {
			$allow_print = true;
			
			if ($file && $i<5) $cn = printErrorFile($v['file'],$v['line']);
			else $cn = false;
			$inc = in_array($v['function'],array('include','require','include_once','require_once'));
			$trace = '<div style="font:11px \'Lucida Console\', monospace;color:#424242!important;line-height:120%!important;padding:5px;border:1px solid #dcdcdc;'.($i!=1?'border-top:none;':'').'background:#f7f7f7" onMouseOver="this.style.background=\'#fff\'" onMouseOut="this.style.background=\'#f7f7f7\'">'.($cn?'<a href="javascript://" onClick="if(this.parentNode.childNodes[2].style.display==\'\'){this.parentNode.childNodes[2].style.display=\'none\'}else{this.parentNode.childNodes[2].style.display=\'\'}" style="font:11px \'Lucida Console\', monospace;color:#424242!important">':'').'[[::i::--]) '.$v['file'].' on line '.$v['line'].($cn?'</a>':'');
			$trace .= '<div style="padding-left:5px;font:11px monospace!important;line-height:120%!important">';
			if (isset($v['class'])) {
				$trace .= '<font color=#009900>'.$v['class'].'::'.$v['function'].'(';
				if (isset($v['args'])) {
					$trace .= printArguments($v['args']);
				}
				$trace .= ');</font>';
			}
			elseif (isset($v['function'])) {
				$trace .= '<font color='.($inc?'green':'steelblue').'>'.$v['function'].'(';
				if (!empty($v['args'])) {
					$trace .= printArguments($v['args']);
				}
				$trace .= ');</font>';
			}
			if ($cn) {
				$trace .= '</div><div style="display:none;padding:5px;font:11px monospace!important;line-height:120%!important">'.$cn.'</div>';			
			} else {
				$trace .= '</div>';	
			}
			$trace .= '</div>';
		}
		$arr_trace[] = $trace;
	}
	if ($as_array) return $arr_trace;
	$trace = '';
	$total = count($arr_trace);	
	foreach ($arr_trace as $i => $t) {
		$trace .= str_replace('[[::i::--]',(!$p?"\n":'').$total - $i,$t);
	}
	if (!$p) $trace = nl2br(strip_tags($p));
	return $trace;
}

function getServerLoad() {
	if (strstr(strtolower(PHP_OS),'win')) {
		$serverstats = @shell_exec("typeperf \"Processor(_Total)\% Processor Time\" -sc 1");
		if ($serverstats) {
			$server_reply = explode("\n",str_replace("\r","",$serverstats));
			$serverstats = array_slice($server_reply,2,1);
			$statline = explode(",",str_replace('"','',$serverstats[0]));
			$server_load = round( $statline[1],2);
		}
	} elseif ($serverstats = @exec("uptime")) {
		preg_match( "/(?:averages)?\: ([0-9\.]+)[^0-9\.]+([0-9\.]+)[^0-9\.]+([0-9\.]+)\s*/", $serverstats, $load );
		$server_load = $load[1];
	}
	$r = $server_load?$server_load:'Couldn\'t get';
	return $r;
}


function printErrorFile($file, $line, $range = 5) {
	$cn = '';
	if (!$line) {
		return 'This file is encoded.';
	}
	if (!is_file($file)) return false;
	$arr_file = file($file);
	$lines = count($arr_file);
	$cn = '';
	$nums = array();
	for ($i=1;$i<=$lines;$i++) {
		if ($i-$range<=$line && $i+$range>=$line) {
			if (!isset($arr_file[$i])) continue;
			$nums[] = $i;
			$cn .= str_replace("\t",'  ',rtrim($arr_file[$i]))."\r\n";
			if ($i==$line-1) $sel = $i;
		}
	}
	$cn = Parser::geshiHighlight($cn);
	$ex = explode("<br />",$cn);
	$cn = '<table cellspacing="0" cellpadding="0" width="100%" style="font:11px monospace;text-wrap:ellipsis;color:#000">';
	$total = $i;
	foreach ($nums as $i => $n) {
		$cn .= '<tr>';
		/*
		if (!$i) {
			$cn .= '<td width="40" align="right" rowspan="'.$total.'" style="color:#666">';
			foreach ($nums as $j => $_n) $cn .= $_n.')<br>';
			$cn .= '</td>';	
		}
		*/
		$cn .= '<td width="40" align="right" style="color:#666">'.($n+1).')</td>';
		$cn .= '<td width="100%"'.($i?' style="'.($sel==$n?'background:#F8FEA7;':'').'border-top:1px dotted #dfdfdf"':'').'>'.(strlen(trim($ex[$i]))?$ex[$i]:'&nbsp;').'</td>';
		$cn .= '</tr>';
	}
	return $cn.'</table>';
}


function chown2($filename) {
	if (!function_exists('fileowner') || !function_exists('filegroup'))	{
		return false;
	}
	$chf = FTP_DIR_ROOT.'index.php';
	if (!is_file($chf)) {
		$chf = __FILE__;
	}
	$file_uid = @fileowner($filename);
	$file_gid = @filegroup($filename);
	
	$common_php_owner = @fileowner($chf);
	$common_php_group = @filegroup($chf);
	
	if ($common_php_owner !== $file_uid && $common_php_owner !== false && $file_uid !== false) {
		if (@chown($filename, $common_php_owner)) {
			@clearstatcache();
			$file_uid = @fileowner($filename);
		}
	}
	if ($common_php_group !== $file_gid && $common_php_group !== false && $file_gid !== false) {
		if (@chgrp($filename, $common_php_group)) {
			@clearstatcache();
			$file_gid = @filegroup($filename);
		}
	}
	return array($file_uid, $file_gid);
}
/*
function importFile($file, $separator = '-- ', $func = false) {
	if (!$file || !is_file($file) || !$func) return false;
	set_time_limit(3600);		
	$dir = FTP_DIR_ROOT.DIR_FILES.'temp/splits/';
	File::rmdir($dir,false);
	if (!is_dir($dir)) mkdir($dir,0777);
	$i = 1000000;
	$handle = fopen($file,'r');
	if($handle) {
		if (is_array($separator)) {
			$c = 0;
			$separator_length = strlen($separator[0]);
			while (($buffer = fgets($handle))!==false) {
				$newfile = fopen($dir.'/'.$i.'.sql','a+');
				fwrite($newfile,$buffer);
				if (substr($buffer,0,$separator_length)===$separator[0]) {
					if ($c==$separator[1]) {
						$i++;
						$c = 0;
						fclose($newfile);
					}
					$c++;
				}
			}
			fclose($handle);
		} else {
			$separator_length = strlen($separator);
			while (($buffer = fgets($handle))!==false) {
				$newfile = fopen($dir.'/'.$i.'.sql','a+');
				fwrite($newfile,$buffer);
				if (substr($buffer,0,$separator_length)===$separator) {
					$i++;
					fclose($newfile);
				}
			}
			fclose($handle);
		}
	}
	set_time_limit(3600000);
	$files = scandir($dir);
	ob_start();
	foreach($files as $j => $file) {
		if ($func=='db') {
			echo $dir.$file.'\n';
			system('mysql -u '.DB_USERNAME.' -p'.DB_PASSWORD.' '.DB_NAME.' < '.$dir.'/'.$file);
			ob_flush();
		} else {
			call_user_func_array($func, array($dir.'/'.$file));
		}
	}
	return array($i, $j);
}

function W3validator($uri = SITE_URL) {
	return json_decode(@file_get_contents('http://validator.w3.org/check?output=json&uri='.rawurlencode($uri)));
}
*/

function filepos($filename, $needle, $offset = 0){
	$fp = fopen($filename, 'rb');
	for($i = $offset, $next = strlen($needle), $length = 1 + filesize($filename) - $next, $found = false; $i < $length;){
		if($found = strpos(fread($fp, $next), $needle) === 0)
		break;
		fseek($fp, ++$i, SEEK_SET);
	}
	fclose($fp);
	return  $found ? $i : -1;
}

function renameReplace($from, $to, $str, $strto) {
	$c = file_get_contents($from);
	$c = str_replace($str,$strto,$c);
	$fp = @fopen($to,'w');
	if ($fp) {
		fwrite($fp,$c);
		fclose($fp);
		@unlink($from);
		return true;
	}
	return false;
}

function copyReplace($from, $to, $str, $strto) {
	$c = file_get_contents($from);
	$c = str_replace($str,$strto,$c);
	$fp = @fopen($to,'w');
	if ($fp) {
		fwrite($fp,$c);
		fclose($fp);
		return true;
	}
	return false;
}

function detectScript($file) {
	$chunk = file_get_contents($file);
	$chunk = strtolower($chunk);
	if (!$chunk) return false;
	if (substr($chunk,0,2)=="\xfe\xff") $enc = "UTF-16BE";
	elseif (substr($chunk,0,2)=="\xff\xfe") $enc = "UTF-16LE";
	else $enc = NULL;
	if ($enc) $chunk= iconv($enc, "ASCII//IGNORE", $chunk);
	$chunk= trim($chunk);
	if (eregi("<!DOCTYPE *X?HTML",$chunk)) return true;
	$tags = array(
		'<body',
		'<head',
		'<html',   #also in safari
		'<img',
		'<pre',
		'<script', #also in safari
		'<table'
	);
	foreach ($tags as $tag) {
		if (false !== strpos($chunk,$tag)) {
			return true;
		}
	}
	include_once('lib_sanitizer.php');
	$chunk = Sanitizer::decodeCharReferences( $chunk );
	if (preg_match('!type\s*=\s*[\'"]?\s*(?:\w*/)?(?:ecma|java)!sim',$chunk)) return true;
	if (preg_match('!(?:href|src|data)\s*=\s*[\'"]?\s*(?:ecma|java)script:!sim',$chunk)) return true;
	if (preg_match('!url\s*\(\s*[\'"]?\s*(?:ecma|java)script:!sim',$chunk)) return true;
	return false;
}


function isWindows() {
	return (substr(php_uname(),0,7)=='Windows')?true:false;
}

/*
function shellexec($cmd, &$retval=null ) {
	$max_shell_memory = 102400;
	$max_shell_file_size = 102400;
	$IP = dirname(dirname(__FILE__));	
	if (php_uname('s')=='Linux') {
		$time = intval(ini_get('max_execution_time'));
		$mem = intval($max_shell_memory);
		$filesize = intval($max_shell_file_size);
		if ($time > 0 && $mem > 0) {
			$script = "$IP/bin/ulimit4.sh";
			if (is_executable($script)) {
				$cmd = escapeshellarg($script)." $time $mem $filesize ".escapeshellarg($cmd);
			}
		}
	} elseif (php_uname('s')=='Windows NT') {
		$cmd = '"'.$cmd.'"';
	}
	ob_start();
	$retval = 1;
	passthru ($cmd,$retval);
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function escape_shell_arg( ) {
	$args = func_get_args();
	$first = true;
	$retVal = '';
	foreach ($args as $arg) {
		if (!$first) {
			$retVal .= ' ';
		} else {
			$first = false;
		}
		if (isWindows()) {
			$tokens = preg_split( '/(\\\\*")/', $arg, -1, PREG_SPLIT_DELIM_CAPTURE );
			$arg = '';
			$delim = false;
			foreach ($tokens as $token) {
				if ($delim) {
					$arg .= str_replace( '\\', '\\\\', substr($token,0,-1)).'\\"';
				} else {
					$arg .= $token;
				}
				$delim = !$delim;
			}
			$m = array();
			if (preg_match('/^(.*?)(\\\\+)$/',$arg,$m)) {
				$arg = $m[1].str_replace('\\','\\\\',$m[2]);
			}
			$retVal .= '"'.$arg.'"';
		} else {
			$retVal .= escapeshellarg( $arg );
		}
	}
	return $retVal;
}
*/

function zipFiles2Array($o_dir, $file) {
	$allowed_ext = arrMime();
	$files = array();
	require_once FTP_DIR_ROOT.'inc/lib/PclZip.php';
	$f = getcwd();
	chdir($o_dir);
	$Zip = new PclZip($file);
	if (!is_file($file)) Message::halt('No file','File '.$file.' is missing');
	$name = nameOnly($file);
	if ($Zip->extract(PCLZIP_OPT_ADD_PATH,$name)==0) {
		chdir($f);
	} else {
		chdir($f);
		$ret = FuncDirFileRecursive($o_dir.$name, false, 'array', 'array', array(),0, 0);
		$i = 0;
		foreach ($ret as $dir => $arrF) {
			foreach ($arrF as $f) {
				$ext = ext($f);
				if (!$allowed_ext[$ext]) {
					$mtype = '';
					if (function_exists('mime_content_type')) $mtype = mime_content_type($dir.$f);
					elseif (function_exists('finfo_file')) {
						$finfo = finfo_open(FILEINFO_MIME);
						$mtype = finfo_file($finfo, $dir.$f);
						finfo_close($finfo);  
					}
					if (!$mtype) $mtype = 'application/force-download';
				} else $mtype = $allowed_ext[$ext];
				$files['name'][$i] = $f;
				$files['tmp_name'][$i] = $dir.$f;
				$files['type'][$i] = $mtype;
				$files['size'][$i] = filesize($dir.$f);
				$i++;
			}
		}
	}
	@unlink($o_dir.$file);
	return $files;
}

function checkFileType($ext,$type) {
	if (!$ext || !$type) return true;
	if (($ext=='jpg' || $ext=='jpeg' || $ext=='jpe') && $type!='image/jpeg' && $type!='image/jpg' && $type!='image/jpe' && $type!='image/pjpeg' && $type!='image/pjpg' && $type!='image/x-jpeg' && $type!='image/x-jpg') return false;
	elseif ($ext=='gif' && ($type!='image/gif' && $type!='image/x-gif')) return false;
	elseif ($ext=='png' && ($type!='image/png' && $type!='image/x-png')) return false;
	elseif ($ext=='tiff' && ($type!='image/tif' && $type!='image/tiff')) return false;	
	elseif ($ext=='bmp' && ($type!='image/bmp' && $type!='image/x-bmp')) return false;
	elseif (($ext=='html' || $ext=='htm') && $type!='text/html') return false;
	elseif ($ext=='txt' && $type!='text/plain') return false;
	elseif ($ext=='css' && $type!='text/css' && !strstr($type,'text')) return false;
	elseif ($ext=='xml' && $type!='text/xml') return false;
	elseif (($ext=='mpeg' || $ext=='mpg' || $ext=='mpe') && $type!='video/mpeg' && $type!='video/x-mpeg') return false;
	elseif (($ext=='mp3' || $ext=='mp2' || $ext=='mpga') && $type!='audio/mpeg' && $type!='audio/x-mpeg') return false;	
	elseif (($ext=='kar' || $ext=='mid' || $ext=='midi') && $type!='audio/midi' && $type!='audio/mid') return false;	
	elseif ($ext=='aif' || $ext=='aifc' || $ext=='aiff' && $type!='audio/x-aiff') return false;	
	elseif ($ext=='wav' && ($type!='audio/x-wav' && $type!='audio/wav')) return false;	
	elseif ($ext=='ram' || $ext=='ra' && $type!='audio/x-pn-realaudio') return false;	
	elseif ($ext=='zip' && ($type!='application/zip' && $type!='application/x-zip' && $type!='application/x-zip-compressed')) return false;	
	elseif ($ext=='pdf' && $type!='application/pdf') return false;
	elseif ($ext=='doc' && !strpos($type,'word')) return false;
	elseif ($ext=='rtf' && !strpos($type,'rtf' && !strstr($type,'text')) && !strpos($type,'word')) return false;
	elseif ($ext=='rtx' && !strpos($type,'richtext')) return false;
	elseif ($ext=='xls' && !strpos($type,'excel')) return false;
	elseif (substr($ext,0,3)=='php' && !strpos($type,'php') && !strstr($type,'text') && $type!='application/octet-stream') return false;
	elseif ($ext=='js' && !strpos($type,'js') && !strstr($type,'text') && $type!='application/octet-stream') return false;
	elseif ($ext=='css' && !strpos($type,'css') && !strstr($type,'text')) return false;
	elseif ($ext=='tpl' && !strpos($type,'stream') && !strstr($type,'text')) return false;
	elseif ($ext=='gz' && !strpos($type,'gzip')) return false;
	elseif ($ext=='swf' && !strpos($type,'flash')) return false;
	elseif ($ext=='sit' && $type!='application/x-stuffit') return false;	
	elseif ($ext=='tar' && $type!='application/x-tar') return false;
	elseif (arrMime($ext)!=$mime) return false;
	return true;
}

function arrMime($ext = NULL) {
	static $arrMIME = array(
		'acx'	=> 'application/internet-property-stream'
		,'ai'	=> 'application/postscript'
		,'aif'	=> 'audio/x-aiff'
		,'aifc'	=> 'audio/x-aiff'
		,'aiff'	=> 'audio/x-aiff'
		,'asf'	=> 'video/x-ms-asf'
		,'asr'	=> 'video/x-ms-asf'
		,'asx'	=> 'video/x-ms-asf'
		,'au'	=> 'audio/basic'
		,'avi'	=> 'video/x-msvideo'
		,'axs'	=> 'application/olescript'
		,'bas'	=> 'text/plain'
		,'bcpio'=> 'application/x-bcpio'
		,'bin'	=> 'application/octet-stream'
		,'bmp'	=> 'image/bmp'
		,'c'	=> 'text/plain'
		,'cat'	=> 'application/vnd.ms-pkiseccat'
		,'cdf'	=> 'application/x-netcdf'
		,'cer'	=> 'application/x-x509-ca-cert'
		,'class'=> 'application/octet-stream'
		,'clp'	=> 'application/x-msclip'
		,'cmx'	=> 'image/x-cmx'
		,'cod'	=> 'image/cis-cod'
		,'cpio'	=> 'application/x-cpio'
		,'crd'	=> 'application/x-mscardfile'
		,'crl'	=> 'application/pkix-crl'
		,'crt'	=> 'application/x-x509-ca-cert'
		,'csh'	=> 'application/x-csh'
		,'csv'	=> 'text/csv'
		,'css'	=> 'text/css'
		,'dcr'	=> 'application/x-director'
		,'der'	=> 'application/x-x509-ca-cert'
		,'dir'	=> 'application/x-director'
		,'dll'	=> 'application/x-msdownload'
		,'dms'	=> 'application/octet-stream'
		,'doc'	=> 'application/msword'
		,'docx'	=> 'application/msword'
		,'dot'	=> 'application/msword'
		,'dvi'	=> 'application/x-dvi'
		,'dxr'	=> 'application/x-director'
		,'eps'	=> 'application/postscript'
		,'etx'	=> 'text/x-setext'
		,'evy'	=> 'application/envoy'
		,'exe'	=> 'application/octet-stream'
		,'fif'	=> 'application/fractals'
		,'flr'	=> 'x-world/x-vrml'
		,'gif'	=> 'image/gif'
		,'gtar'	=> 'application/x-gtar'
		,'gz'	=> 'application/x-gzip'
		,'h'	=> 'text/plain'
		,'hdf'	=> 'application/x-hdf'
		,'hlp'	=> 'application/winhlp'
		,'hqx'	=> 'application/mac-binhex40'
		,'hta'	=> 'application/hta'
		,'htc'	=> 'text/x-component'
		,'htm'	=> 'text/html'
		,'html'	=> 'text/html'
		,'htt'	=> 'text/webviewhtml'
		,'ico'	=> 'image/x-icon'
		,'ief'	=> 'image/ief'
		,'iii'	=> 'application/x-iphone'
		,'ins'	=> 'application/x-internet-signup'
		,'isp'	=> 'application/x-internet-signup'
		,'jfif'	=> 'image/pipeg'
		,'jpe'	=> 'image/jpeg'
		,'jpeg'	=> 'image/jpeg'
		,'jpg'	=> 'image/jpeg'
		,'js'	=> 'application/x-javascript'
		,'latex'=> 'application/x-latex'
		,'lha'	=> 'application/octet-stream'
		,'lsf'	=> 'video/x-la-asf'
		,'lsx'	=> 'video/x-la-asf'
		,'lzh'	=> 'application/octet-stream'
		,'m13'	=> 'application/x-msmediaview'
		,'m14'	=> 'application/x-msmediaview'
		,'m3u'	=> 'audio/x-mpegurl'
		,'man'	=> 'application/x-troff-man'
		,'mdb'	=> 'application/x-msaccess'
		,'me'	=> 'application/x-troff-me'
		,'mht'	=> 'message/rfc822'
		,'mhtml'=> 'message/rfc822'
		,'mid'	=> 'audio/mid'
		,'mny'	=> 'application/x-msmoney'
		,'mov'	=> 'video/quicktime'
		,'movie'=> 'video/x-sgi-movie'
		,'mp2'	=> 'video/mpeg'
		,'mp3'	=> 'audio/mpeg'
		,'mpa'	=> 'video/mpeg'
		,'mpe'	=> 'video/mpeg'
		,'mpeg'	=> 'video/mpeg'
		,'mpg'	=> 'video/mpeg'
		,'mpp'	=> 'application/vnd.ms-project'
		,'mpv2'	=> 'video/mpeg'
		,'ms'	=> 'application/x-troff-ms'
		,'msg'	=> 'application/vnd.ms-outlook'
		,'mvb'	=> 'application/x-msmediaview'
		,'nc'	=> 'application/x-netcdf'
		,'nws'	=> 'message/rfc822'
		,'oda'	=> 'application/oda'
		,'p10'	=> 'application/pkcs10'
		,'p12'	=> 'application/x-pkcs12'
		,'p7b'	=> 'application/x-pkcs7-certificates'
		,'p7c'	=> 'application/x-pkcs7-mime'
		,'p7m'	=> 'application/x-pkcs7-mime'
		,'p7r'	=> 'application/x-pkcs7-certreqresp'
		,'p7s'	=> 'application/x-pkcs7-signature'
		,'pbm'	=> 'image/x-portable-bitmap'
		,'pdf'	=> 'application/pdf'
		,'pfx'	=> 'application/x-pkcs12'
		,'pgm'	=> 'image/x-portable-graymap'
		,'pko'	=> 'application/ynd.ms-pkipko'
		,'pma'	=> 'application/x-perfmon'
		,'png'	=> 'image/png'
		,'pmc'	=> 'application/x-perfmon'
		,'pml'	=> 'application/x-perfmon'
		,'pmr'	=> 'application/x-perfmon'
		,'pmw'	=> 'application/x-perfmon'
		,'pnm'	=> 'image/x-portable-anymap'
		,'pot'	=> 'application/vnd.ms-powerpoint'
		,'ppm'	=> 'image/x-portable-pixmap'
		,'pps'	=> 'application/vnd.ms-powerpoint'
		,'ppt'	=> 'application/vnd.ms-powerpoint'
		,'prf'	=> 'application/pics-rules'
		,'ps'	=> 'application/postscript'
		,'pub'	=> 'application/x-mspublisher'
		,'qt'	=> 'video/quicktime'
		,'ra'	=> 'audio/x-pn-realaudio'
		,'ram'	=> 'audio/x-pn-realaudio'
		,'ras'	=> 'image/x-cmu-raster'
		,'rgb'	=> 'image/x-rgb'
		,'rmi'	=> 'audio/mid'
		,'rar'	=> 'application-x-rar'
		,'roff'	=> 'application/x-troff'
		,'rtf'	=> 'application/rtf'
		,'rtx'	=> 'text/richtext'
		,'scd'	=> 'application/x-msschedule'
		,'sct'	=> 'text/scriptlet'
		,'setpay'=> 'application/set-payment-initiation'
		,'setreg'=> 'application/set-registration-initiation'
		,'sh'	=> 'application/x-sh'
		,'shar'	=> 'application/x-shar'
		,'sit'	=> 'application/x-stuffit'
		,'snd'	=> 'audio/basic'
		,'spc'	=> 'application/x-pkcs7-certificates'
		,'spl'	=> 'application/futuresplash'
		,'src'	=> 'application/x-wais-source'
		,'sst'	=> 'application/vnd.ms-pkicertstore'
		,'stl'	=> 'application/vnd.ms-pkistl'
		,'stm'	=> 'text/html'
		,'sv4cpio'=> 'application/x-sv4cpio'
		,'sv4crc'=> 'application/x-sv4crc'
		,'svg'	=> 'image/svg+xml'
		,'swf'	=> 'application/x-shockwave-flash'
		,'t'	=> 'application/x-troff'
		,'tar'	=> 'application/x-tar'
		,'tcl'	=> 'application/x-tcl'
		,'tex'	=> 'application/x-tex'
		,'texi'	=> 'application/x-texinfo'
		,'texinfo'=> 'application/x-texinfo'
		,'tgz'	=> 'application/x-compressed'
		,'tif'	=> 'image/tiff'
		,'tiff'	=> 'image/tiff'
		,'tr'	=> 'application/x-troff'
		,'trm'	=> 'application/x-msterminal'
		,'tsv'	=> 'text/tab-separated-values'
		,'txt'	=> 'text/plain'
		,'uls'	=> 'text/iuls'
		,'ustar'=> 'application/x-ustar'
		,'vcf'	=> 'text/x-vcard'
		,'vrml'	=> 'x-world/x-vrml'
		,'wav'	=> 'audio/x-wav'
		,'wcm'	=> 'application/vnd.ms-works'
		,'wdb'	=> 'application/vnd.ms-works'
		,'wks'	=> 'application/vnd.ms-works'
		,'wmf'	=> 'application/x-msmetafile'
		,'wps'	=> 'application/vnd.ms-works'
		,'wri'	=> 'application/x-mswrite'
		,'wrl'	=> 'x-world/x-vrml'
		,'wrz'	=> 'x-world/x-vrml'
		,'xaf'	=> 'x-world/x-vrml'
		,'xbm'	=> 'image/x-xbitmap'
		,'xla'	=> 'application/vnd.ms-excel'
		,'xlc'	=> 'application/vnd.ms-excel'
		,'xlm'	=> 'application/vnd.ms-excel'
		,'xls'	=> 'application/vnd.ms-excel'
		,'xlt'	=> 'application/vnd.ms-excel'
		,'xlw'	=> 'application/vnd.ms-excel'
		,'xof'	=> 'x-world/x-vrml'
		,'xpm'	=> 'image/x-xpixmap'
		,'xwd'	=> 'image/x-xwindowdump'
		,'z'	=> 'application/x-compress'
		,'zip'	=> 'application/zip'
		,'323'	=> 'text/h323'
	);
	if ($ext) {
		return isset($arrMIME[$ext]) ? $arrMIME[$ext] : false;
	}
	return $arrMIME;
}

function getUploadError($error) {
	if ($error===NULL) return false;	
	switch($error) {
		case '1':
			return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
		break;
		case '2':
			return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
		break;
		case '3':
			return 'The uploaded file was only partially uploaded';
		break;
		case '4':
			return 'No file was uploaded.';
		break;
		case '6':
			return 'Missing a temporary folder';
		break;
		case '7':
			return 'Failed to write file to disk';
		break;
		case '8':
			return 'File upload stopped by extension';
		break;
		default:
			return 'No error code avaiable';
	}	
}
function makeIndexLocation($dir,$location=NULL) {
	if (!$location) $location = HTTP_BASE;
	$dir = File::fixPath($dir);
	touch($dir.'index.php');
	$fp = fopen($dir.'index.php','w');
	fwrite($fp,"<?php\nheader('Location: $location');\n?>");
	fclose($fp);
	chmod($dir.'index.php',0644);
}

function fileStructure($dir,$drop = false,$selected='',$repl='',$to='',&$array,&$html) {
	if (!$array) {
		$dir = File::fixPath($dir);
		$selected = File::fixPath($selected);
		$repl = File::fixPath($repl);
	}
	if ($handle = @opendir($dir)) {
	   while (false !== ($file = readdir($handle))) {
		   if ($file != '.' && $file != '..') {
			   if(is_dir($dir.$file)){
				   $array[] = array(
						'dir_name'	=> $file
					//	,'dir_size'	=>($dir_size = (int)exec('du -ab '.$dir.$file))
						,'dir_time'	=> filemtime($dir.$file)
						,'subdirs'	=> array()
					);
					if ($drop) {
						$val = ($repl?str_replace($repl,$to,$dir.$file):$dir.$file).'/';
						$html .= '<option value="'.$val.'"'.($selected==$val?' selected="selected"':'').'>'.$val.' ['.count(File::openDirectory($dir.$file.'/')).']</option>';
					}
					fileStructure($dir.$file,$drop,$selected,$repl,$to,$array[count($array)-1]['subdirs'],$arrDrop);
			   }
		   }
	   }
	   closedir($handle);
	}
	if ($drop) return $html;
	else return $array;
}



function copyDirectory($src,$dst,$options=array()) {
	$fileTypes=array();
	$exclude=array();
	$level=-1;
	extract($options);
	copyDirectoryRecursive($src,$dst,'',$fileTypes,$exclude,$level);
}
function findFiles($dir,$options=array()) {
	$fileTypes=array();
	$exclude=array();
	$level=-1;
	extract($options);
	$list=findFilesRecursive($dir,'',$fileTypes,$exclude,$level);
	sort($list);
	return $list;
}

function copyDirectoryRecursive($src,$dst,$base,$fileTypes,$exclude,$level) {
	mkdir($dst);
	@chmod($dst, 0777);
	if (!is_dir($src)) return false;
	$folder = opendir($src);
	while ($file = readdir($folder)) {
		if($file==='.' || $file==='..' || $file=='Spiders') continue;
		$path=$src.DIRECTORY_SEPARATOR.$file;
		$isFile=is_file($path);
		if (validatePath($base,$file,$isFile,$fileTypes,$exclude)) {
			if ($isFile)
				copy($path,$dst.DIRECTORY_SEPARATOR.$file);
			elseif ($level)
				copyDirectoryRecursive($path,$dst.DIRECTORY_SEPARATOR.$file,$base.'/'.$file,$fileTypes,$exclude,$level-1);
		}
	}
	closedir($folder);
}
function findFilesRecursive($dir,$base,$fileTypes,$exclude,$level) {
	$list=array();
	$handle=opendir($dir);
	while(($file=readdir($handle))!==false)	{
		if($file==='.' || $file==='..')
			continue;
		$path=$dir.DIRECTORY_SEPARATOR.$file;
		$isFile=is_file($path);
		if(validatePath($base,$file,$isFile,$fileTypes,$exclude)) {
			if($isFile)
				$list[]=$path;
			else if($level)
				$list=array_merge($list,findFilesRecursive($path,$base.'/'.$file,$fileTypes,$exclude,$level-1));
		}
	}
	closedir($handle);
	return $list;
}
function validatePath($base,$file,$isFile,$fileTypes,$exclude) {
	foreach($exclude as $e) {
		if($file===$e || strpos($base.'/'.$file,$e)===0) return false;
	}
	if(!$isFile || empty($fileTypes)) return true;
	if(($pos=strrpos($file,'.'))!==false) {
		$type=substr($file,$pos+1);
		return in_array($type,$fileTypes);
	} else return false;
}



function is_dir_writable($path) {
	$path = File::fixPath($path);
	$result = true;
	if ($handle = @opendir($path)) {
		while (false!==($file = readdir($handle))) {
			if ($file=='.' || $file=='..') continue;
			$p = $path.$file;
			if (!@is_writable($p)) return false;
			if (@is_dir($p)) {
				$result = is_dir_writable($p);
				if (!$result) return false;
			}
		}
		@closedir($handle);
	} else return false;
	return true;
}


 

function get_url($url, $postdata = false, $use_cookie = false, $loop = 0, $timeout = 5) {	
	$url = str_replace('&amp;', '&',trim($url));
	if (!function_exists('curl_init')) return 'CURL library is not installed.';
	$ch = curl_init();
	$a = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
	curl_setopt($ch, CURLOPT_USERAGENT, $a);
	curl_setopt($ch, CURLOPT_URL, $url);
	if ($use_cookie) {
		if ($use_cookie===1) {
			$cookie = tempnam('/tmp', 'CURL');
			//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
		}
		elseif ($use_cookie===2) {
			curl_setopt($ch, CURLOPT_COOKIESESSION, true);
			curl_setopt($ch, CURLOPT_COOKIEJAR, FTP_DIR_ROOT.'cookie.txt');
		}
		else {
			if (is_array($use_cookie)) {
				curl_setopt($ch, CURLOPT_COOKIESESSION, true);
				curl_setopt($ch, CURLOPT_COOKIE, is_array($use_cookie[0]) ? join('&',$use_cookie[0]) : $use_cookie[0]);
			} else {
				curl_setopt($ch, CURLOPT_COOKIE, $use_cookie);
			}
		}
	}
	
	if ($postdata) {
		if (is_array($postdata)) $postdata = http_build_query($postdata);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, '&'.ltrim($postdata,'&'));
	}

	curl_setopt($ch, CURLOPT_REFERER, $url);
//	curl_setopt($ch, CURLOPT_REFERER, 'https://google.com/?q='.urlencode($url));
//	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_ENCODING, 'utf-8');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
//	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
	$content = curl_exec($ch);
	$response = curl_getinfo($ch);

	curl_close($ch);
	
	if ($response['url'] && $response['http_code']==400 && !$loop) {
		ini_set('user_agent', $a);
		return get_url($response['url'], $postdata, $use_cookie, $loop+1, $timeout);
	}
	elseif ($response['url'] && $response['http_code']==301 || $response['http_code']==302) {
		ini_set('user_agent', $a);
		if ($headers = @get_headers($response['url'])) {
			foreach($headers as $value ) {
				if (substr(strtolower($value), 0, 9)=='location:') return get_url(trim(substr($value, 9, strlen($value))), $postdata, $use_cookie, $loop+1, $timeout);
			}
		}
	}
	return $content;
	
	/*
	if ((!$content || preg_match('/Object moved to /', $content)) && $loop < $timeou) {
		return get_url($url, $postdata, $use_cookie, $loop+1, $timeout);
	}
	elseif ((preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) && $loop < $timeou) {
		return get_url($value[1], $postdata, $use_cookie, $loop+1, $timeout);
	}
	else {
		return $content;
	//	return array($content, $response);
	}
	*/
}

function xml2array($contents, $get_attributes=1, $priority = 'tag') { 
	if(!$contents) return array(); 

	if(!function_exists('xml_parser_create')) { 
		//print "'xml_parser_create()' function not found!"; 
		return array(); 
	} 

	//Get the XML parser of PHP - PHP must have this module for the parser to work 
	$parser = xml_parser_create(''); 
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
	xml_parse_into_struct($parser, trim($contents), $xml_values); 
	xml_parser_free($parser); 

	if(!$xml_values) return;//Hmm... 

	//Initializations 
	$xml_array = array(); 
	$parents = array(); 
	$opened_tags = array(); 
	$arr = array(); 

	$current = &$xml_array; //Refference 

	//Go through the tags. 
	$repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
	foreach($xml_values as $data) { 
		unset($attributes,$value);//Remove existing values, or there will be trouble 

		//This command will extract these variables into the foreach scope 
		// tag(string), type(string), level(int), attributes(array). 
		extract($data);//We could use the array by itself, but this cooler. 

		$result = array(); 
		$attributes_data = array(); 
		 
		if(isset($value)) { 
			if($priority == 'tag') $result = $value; 
			else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
		} 

		//Set the attributes too. 
		if(isset($attributes) and $get_attributes) { 
			foreach($attributes as $attr => $val) { 
				if($priority == 'tag') $attributes_data[$attr] = $val; 
				else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
			} 
		} 

		//See tag status and do the needed. 
		if($type == "open") {//The starting of the tag '<tag>' 
			$parent[$level-1] = &$current; 
			if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
				$current[$tag] = $result; 
				if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
				$repeated_tag_index[$tag.'_'.$level] = 1; 

				$current = &$current[$tag]; 

			} else { //There was another element with the same tag name 

				if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
					$repeated_tag_index[$tag.'_'.$level]++; 
				} else {//This section will make the value an array if multiple tags with the same name appear together 
					$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array 
					$repeated_tag_index[$tag.'_'.$level] = 2; 
					 
					if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
						$current[$tag]['0_attr'] = $current[$tag.'_attr']; 
						unset($current[$tag.'_attr']); 
					} 

				} 
				$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
				$current = &$current[$tag][$last_item_index]; 
			} 

		} elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
			//See if the key is already taken. 
			if(!isset($current[$tag])) { //New Key 
				$current[$tag] = $result; 
				$repeated_tag_index[$tag.'_'.$level] = 1; 
				if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

			} else { //If taken, put all things inside a list(array) 
				if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 

					// ...push the new element into that array. 
					$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
					 
					if($priority == 'tag' and $get_attributes and $attributes_data) { 
						$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
					} 
					$repeated_tag_index[$tag.'_'.$level]++; 

				} else { //If it is not an array... 
					$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value 
					$repeated_tag_index[$tag.'_'.$level] = 1; 
					if($priority == 'tag' and $get_attributes) { 
						if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
							 
							$current[$tag]['0_attr'] = $current[$tag.'_attr']; 
							unset($current[$tag.'_attr']); 
						} 
						 
						if($attributes_data) { 
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
						} 
					} 
					$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
				} 
			} 

		} elseif($type == 'close') { //End of tag '</tag>' 
			$current = &$parent[$level-1]; 
		} 
	} 
	 
	return($xml_array); 
}  


function _xml2array($contents, $get_attributes=1, $priority = true) {
	if(!$contents) return array();
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, trim($contents), $xml_values);
	xml_parser_free($parser);
	if(!$xml_values) return array();
	$arr = $parents = $opened_tags = array();
	$cur = &$arr;
	$i = array();
	foreach($xml_values as $v) {
		unset($attributes,$value);
		$l = 0;
		extract($v);
		$rs = array();
		$data = array();		
		if(isset($value)) {
			if ($priority) $rs = $value;
			else $rs['value'] = $value;
		}
		if(isset($attributes) && $get_attributes) {
			foreach($attributes as $attr => $val) {
				if ($priority) $data[$attr] = $val;
				else $rs[$attr] = $val;
			}
		}
		if($type=='open') {
			$parent[$l-1] = &$cur;
			if(!is_array($cur) or (!in_array($tag, array_keys($cur)))) {
				$cur[$tag] = $rs;
				if($data) $cur[$tag. '_attr'] = $data;
				$i[$tag.'_'.$l] = 1;
				$cur = &$cur[$tag];
			} else {
				if(isset($cur[$tag][0])) {
					$cur[$tag][$i[$tag.'_'.$l]] = $rs;
					$i[$tag.'_'.$l]++;
				} else {
					$cur[$tag] = array($cur[$tag],$rs);
					$i[$tag.'_'.$l] = 2;
					
					if(isset($cur[$tag.'_attr'])) {
						$cur[$tag]['0_attr'] = $cur[$tag.'_attr'];
						unset($cur[$tag.'_attr']);
					}
				}
				$li = $i[$tag.'_'.$l]-1;
				$cur = &$cur[$tag][$li];
			}
		}
		elseif($type=='complete') {
			if(!isset($cur[$tag])) {
				$cur[$tag] = $rs;
				$i[$tag.'_'.$l] = 1;
				if($priority && $data) $cur[$tag. '_attr'] = $data;
			} else {
				if(isset($cur[$tag][0]) && is_array($cur[$tag])) {
					$cur[$tag][$i[$tag.'_'.$l]] = $rs;					
					if ($priority && $get_attributes && $data) {
						$cur[$tag][$i[$tag.'_'.$l] . '_attr'] = $data;
					}
					$i[$tag.'_'.$l]++;
				} else {
					$cur[$tag] = array($cur[$tag],$rs);
					$i[$tag.'_'.$l] = 1;
					if($priority && $get_attributes) {
						if(isset($cur[$tag.'_attr'])) {
							$cur[$tag]['0_attr'] = $cur[$tag.'_attr'];
							unset($cur[$tag.'_attr']);
						}
						if($data) {
							$cur[$tag][$i[$tag.'_'.$l] . '_attr'] = $data;
						}
					}
					$i[$tag.'_'.$l]++;
				}
			}
		} elseif($type == 'close') {
			$cur = &$parent[$l-1];
		}
	}
	return $arr;
}


/*
$prot = new ProtectDir();
$prot->genHash('username','secret password');
$prot->write(dirname(__FILE__).'/../prot/');
*/


function findTag($content, $tag) {
	$content0 = $content;
	$found = array();
	$re =
		 '/^' //
		.'(.*)' // preceding text
		."(<$tag(?: [^>]*)?>)" // starting tag
		.'(.*?)' // following text, switched to greedy
		.'$/iUms'; // case-insensitive, ungreedy, multiline, dotall (dot includes newline)
	if (!@preg_match($re, $content, $p)) return array();
	$found[1] = $p[1];
	$found[2] = $p[2];
	$found[3] = '';
	$content = $p[3];
	$i = 1;
	$re = 
		 '/^'
		.'(.*)' // preceding text
		."(?:"
			."(<$tag(?: [^>]*)?>)" // find start tag
			."|(<\/$tag>)" // or end tag
		.')'
		.'(.*?)' // following text, switched to greedy
		.'$/iUms'; // case-insensitive, ungreedy, multiline, dotall (dot includes newline)
	do {
		if (!@preg_match($re, $content, $p)) return array();		
		if ($p[2]) $i++; else $i--;		
		$content = $p[4];
	} while ($i);
	$found[4] = $p[3];
	$found[5] = $p[4];
	$pos1 = strlen($found[1])+strlen($found[2]);
	$pos2 = strlen($content0)-strlen($found[4])-strlen($found[5]);
	$found[3] = substr($content0, $pos1, $pos2-$pos1);	
	return $found;
}

function table2array($html) {
	$table = FindTag($html, 'table');
	if (!$table) return array();
	$table = $table[3];	
	$rows = array();
	while ($row = findTag($table, 'tr')) {
		$row2 = $row[3];		
		$cells = array();
		while ($cell = findTag($row2, 'td')) {
			$cells[] = $cell[3];
			$row2 = $cell[5];
		}
		$rows[] = $cells;		
		$table = $row[5];
	}	
	return $rows;
}


function FuncDirFileRecursive($dir, $to, $func_file = '', $func_dir = '', $total = 0, $time = 0, $chmod = 0777, $str = 0, $strto = 0) {
	if (!$dir) return 0;
	$dir = File::fixPath($dir);
	if ($to) $to = File::fixPath($to);
	if ($to && strlen($to)<=2) return 0;
	if (!is_dir($dir)) {
		return 0;
	}
	$dh = @opendir($dir);
	if (!$dh) {
		return 0;
	}
	$func_file = trim(strtolower($func_file));
	if ($func_file==='array' && !$total) $total = array();
	
	if (($func_dir==='mkdir' || $func_dir==='rename') && $to && !is_dir($to)) File::checkDir($to);
	$t = time();
	while ($f = readdir($dh)) {
		if ($f=='..' || $f=='.') continue;
		if ($time && filemtime($dir.$file) > $t-$time) continue;
		if (is_dir($dir.$f) && $func_dir!==NULL) {
			
			if ($func_dir) {
				if ($func_dir==='rmdir') {
					if (!is_dir($dir.$f) && @rmdir($dir.$f)) {
						$total++;
					}
				}
				elseif (function_exists($func_dir) && !is_dir($to.$f)) {
					if ($func_dir($to.$f,0777)) {
						File::chown2($to.$f,File::CHMOD_READ | File::CHMOD_WRITE);
						$total++;
					}
				}
			}
			if ($func_file==='array') {
				$total = FuncDirFileRecursive($dir.$f, $to.$f, $func_file, $func_dir, $total, $time, $chmod, $str, $strto);
			} else {
				$total += FuncDirFileRecursive($dir.$f, $to.$f, $func_file, $func_dir, 0, $time, $chmod, $str, $strto);
			}
		} 
		elseif (is_file($dir.$f) && $func_file) {
			if ($func_file==='array') {
				$total[$dir][] = $f;
			}
			elseif ($func_file==='filesize') {
				$total += filesize($dir.$f);
			}
			elseif ($func_file==='rename') {
				if (is_file($to.$f)) unlink($to.$f);
				$total += rename($dir.$f,$to.$f);
			}
			elseif ($func_file==='copy') {
				if (is_file($to.$f)) unlink($to.$f);
				$total += copy($dir.$f,$to.$f);
			}
			elseif ($func_file=='copy_replace' && $str && $strto) {
				if (copyReplace($dir.$f,$to.$f,$str,$strto)) {
					$total++;
				}
			}
			elseif ($func_file==='unlink') {
				File::chown2($dir.$f,File::CHMOD_READ | File::CHMOD_WRITE);
				if (@unlink($dir.$f)) {
					$total++;
				}
			}
			else {
				if (function_exists($func_file) && $func_file($dir.$f,$to.$f)) {
					$total++;
				}
				if ($chmod) @chmod($to.$f,$chmod);
			}
		}
	}
	closedir($dh);
	if (!is_array($total)) $total = (int)$total;
	return $total;
}
