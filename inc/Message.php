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
* @file       inc/Message.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Message {
	
	public static function halt($title, $descr, $lines = false, $die = true, $descr_plain = false, $log = true) {
		if (error_reporting()==0 || !$title || !$descr) return false;
		if (!defined('SITE_TYPE')) define('SITE_TYPE','index');
		if ($log) self::log($title, ($descr_plain ? $descr_plain : $descr));
		if (SITE_TYPE=='upload' || SITE_TYPE=='js') {
			echo $title."\n".strip_tags($descr);
			Site::$exit = true;
			exit;	
		} else {
			require_once FTP_DIR_ROOT.'config/system/halt_message.php';
			halt_message($title, $descr, $lines, $die, $descr_plain);
			if ($die) exit;
		}
	}
	
	public static function dbError($err_no,$err_msg,$sql,$die=true,$db_name) {
		if (error_reporting()==0) return false;
		self::log('MySQLi Error: #'.$err_no, $err_msg."\r\n".$sql);
		$sql = self::sql($sql,0);
		if (defined('MAIL_WEBMASTER') && MAIL_WEBMASTER) {
			File::load();
			informWebmaster('MySQLi Error #'.$err_no.' on '.$_SERVER['HTTP_HOST'], $err_msg.'<br /><br />'.$sql, true);
		}
		return self::halt('MySQLi Error: #'.$err_no.'<div style="float:right;margin-bottom:-35px;">'.$db_name.'</div>',$err_msg.'<fieldset style="margin-top:5px;padding:5px"><legend style="font:bold 9px Verdana;color:#666">Invalid SQL query:</legend><code>'.$sql.'</code></fieldset>',Debug::backTrace(),true,false,false);
	}
	public static function sql($sql) {
		return '<div class="a-sqls" style="color:#000">'.Parser::geshiHighlight($sql,'mysql').'</div>';
	}
	
	public static function minify_image($path, &$src, $file) {
		preg_match_all('/url\(("|\')?([^\'"\)]+)\\1?\)/',$src, $matches);
		foreach ($matches[2] as $i => $link) {
			if ($link && File::isPicture($link) && !strpos($link,'://') && substr($link,0,5)!='data:') {
				$path_file = $path.$link;
				if (substr($link,0,1)=='/') $path_file = FTP_DIR_ROOT.$link;
				if (is_file($path_file)) {
					if (substr_count($src, $link)==1 && filesize($path_file) <= MINIFY_IMAGE) {
						$base64 = 'data:image/'.pathinfo($link, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path_file));
						$src = str_replace($link,$base64,$src);	
					}
				} else {
					//$src = str_replace('url('.$matches[1][$i].$link.$matches[1][$i].')','transparent',$src);
					$src = str_replace($link,'data:image/gif;base64,R0lGODlhAQABAJH/AP///wAAAMDAwAAAACH5BAEAAAIALAAAAAABAAEAAAICVAEAOw==',$src);
					//Message::halt('CSS image file is missing','Tryng to minify image: <em>'.$link.'</em> from css file: <em>'.$file.'</em> in this code: <em>'.$matches[0][$i].'</em><br>Cannot convert this missing image to <em>data:image/png;base64,...</em><br>Missing file path: '.$path_file.'');
				}
			}
		}
		
		if (preg_match_all('/@font-face\s?\{([^\}]+)\}/i',$src,$matches)) {
			if (!is_dir(FTP_DIR_TPL.'css/fonts/')) {
				mkdir(FTP_DIR_TPL.'css/fonts/',0777);	
			}
			foreach ($matches[1] as $i => $ff) {
				if (preg_match_all('/url\(("|\')?([^\'"\)]+)\\1?\)/',$ff, $m)) {
					if (!strpos($m[2][0],'://')) continue;
					if (preg_match('/font-family:\'?([^;]+)\'?;/',$ff,$_m)) {
						$name = trim($_m[1],' \'');
						$weight = $style = '';
						if (preg_match('/font-weight:\'?([^;]+)\'?;/',$ff,$_m)) {
							$weight = '-'.trim($_m[1],' \'');	
						}
						if (preg_match('/font-style:\'?([^;]+)\'?;/',$ff,$_m)) {
							$style = '-'.trim($_m[1],' \'');	
						}
						foreach ($m[2] as $from) {
							$ext = ext($from);
							$to = fixFileName($name.$weight.$style.'.'.$ext);
							if (!is_file(FTP_DIR_TPL.'css/fonts/'.$to)) copy($from,FTP_DIR_TPL.'css/fonts/'.$to);
							$src = str_replace($from,'fonts/'.$to, $src);
						}
					}
				}
			}
		}
	}
	
	
	public static function combineJS() {
		Index()->js_arr = array_unique(Index()->js_arr);
		$n = md5(join('+',Index()->js_arr));
		$ftp_file = FTP_DIR_TPL.'temp/'.$n.'.js';
		$http_file = HTTP_DIR_TPL.'temp/'.$n.'.js';
		$t = time();
		if (!is_file($ftp_file)) {
			$time = $t;
			Index()->js_modify = true;
		} else {
			$time = filemtime($ftp_file);
		}
		
		if (Index()->js_modify && $time<=$t) {
			$fp = fopen($ftp_file,'w');
			$d = date('H:i d.m.Y',$t);
			fwrite($fp, "/**\n* Minified and combined javascript files\n* Generated on $d [http://".DOMAIN."]\n* Engine by Ajaxel CMS v".Site::VERSION."\n* \n* Files (".count(Index()->js_arr)."):\n* - ".join("\n* - ",Index()->js_arr)."\n*/");
			foreach (Index()->js_arr as $f) {
				if (!is_file($f)) continue;
				fwrite($fp,"\n\n/* $f */\n".file_get_contents($f).';');
			}
			fclose($fp);
		}
		return '<script type="text/javascript" src="'.FTP_EXT.$http_file.'?n='.$time.'"></script>'.Index::N;
	}
	
	public static function htaccess($ext, $ex) {
		switch ($ext) {
			case 'png':
				header('Content-Type: image/png');
				echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
			break;
			case 'gif':
				header('Content-Type: image/gif');
				echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
			break;
			case 'jpg':
			case 'jpeg':
				header('Content-Type: image/png');
				echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
			break;
			case 'bmp':
				header('Content-Type: image/vnd.wap.wbmp');
			break;
			case 'css':
				echo '/* ERROR! File '.trim($ex[0],'/').' is missing */';
			break;
			case 'js':
				echo '/* ERROR! File '.trim($ex[0],'/').' is missing */';
				echo 'alert(\'File '.trim($ex[0],'/').' is missing\');';
			break;
			default:
				echo 'ALERT! File '.trim($ex[0],'/').' is missing';
			break;
		}
		if (LOG_MISSING) {
			self::log(strtoupper($ext).' file is missing', 'Missing file: "'.trim($ex[0],'/').'" on "'.@$_SERVER['HTTP_REFERER'].'"');	
		}
	}
	
	public static function myErrorHandler($debug_backtrace, $errno, $errstr, $errfile, $errline, $six) {
		if (func_num_args()==6) {
			$exception = NULL;
			$backtrace = array_reverse($debug_backtrace);
		} else {
			$exc = func_get_arg(1);
			$errno = $exc->getCode();
			$errstr = $exc->getMessage();
			$errfile = $exc->getFile();
			$errline = $exc->getLine();
			$backtrace = $exc->getTrace();
		}

		$errorType = array (
			E_ERROR          => 'ERROR',
			E_WARNING        => 'WARNING',
			E_PARSE          => 'PARSING ERROR',
			E_NOTICE         => 'NOTICE',
			E_DEPRECATED	 => 'DEPRECATED',
			E_CORE_ERROR     => 'CORE ERROR',
			E_CORE_WARNING   => 'CORE WARNING',
			E_COMPILE_ERROR  => 'COMPILE ERROR',
			E_COMPILE_WARNING=> 'COMPILE WARNING',
			E_USER_ERROR     => 'USER ERROR',
			E_USER_WARNING   => 'USER WARNING',
			E_USER_NOTICE    => 'USER NOTICE',
			E_STRICT         => 'STRICT NOTICE',
			E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'
		);
		if (Site::$mini) {
			die('<br /><b>'.$errstr.'</b> in '.$errfile.' on line '.$errline.'<br />');
		}
		if (array_key_exists($errno, $errorType)) {
			$err = $errorType[$errno];
		} else {
			$err = 'CAUGHT EXCEPTION';
		}
		$errMsg = "$errstr in $errfile on line $errline";
		$msg = $errMsg;
		File::load();
		$cn = printErrorFile($errfile,$errline,6);
		if ($cn) {
			$lines = '<fieldset style="margin-top:10px;padding:5px;"><legend style="font:bold 9px Verdana;color:#666">Error on line:</legend><div style="padding:5px 10px;border:1px solid #dcdcdc;background:#fff"><code style="font-size:11px;">'.$cn.'</code></div></fieldset>';	
		} else {
			$lines = '';
		}
		$msg .= $lines;
		$max_time = 1800;
		$file = FTP_DIR_ROOT.'files/temp/error_handler.log';
		$inform = $write = true;
		if (file_exists($file)) {
			$ftime = filemtime($file);
			if (time() - $max_time > $ftime) {
				@unlink($file);
			} else {
				$f = file($file);
				foreach ($f as $i) {
					list ($time, $m) = explode(']: ',$i);
					if (trim($m)==$errMsg) {
						$inform = false;
						$write = false;
						break;
					}
				}
				unset($f);
			}
		}
		$fp = @fopen($file,'ab');
		@fwrite($fp,date('[H:i:s]:').' '.$errMsg."\n");
		@fclose($fp);
		
		if ($inform && !in_array($errno,array(E_USER_ERROR,E_USER_WARNING,E_NOTICE,E_DEPRECATED,E_USER_NOTICE))) {
			File::load();
			informWebmaster($err.' on '.$_SERVER['HTTP_HOST'], $msg, true);
		}
		self::halt('PHP Error: '.$err,$msg,Debug::backTrace(), true, $errMsg);
		return true;	
	}
	
	public static function display_size($f, $colorize = false) {
		if ($f<0) $f = abs($f);
		if (!$f) {
			$f = '0 bytes';
			$color = 'style="color:#999999"';
		}
		elseif ($f >= 1073741824) {
			$f = number_format(round($f / 1073741824 * 100) / 100,2,'.',',') .' GB';
			$color = 'style="color:#696934"';
		} elseif ($f >= 1048576) {
			$f = number_format(round($f / 1048576 * 100) / 100,2,'.',',') .' MB';
			$color = 'style="color:#3399FF"';
		} elseif ($f >= 1024) {
			$f = round($f / 1024 * 100) / 100 .' KB';
			$color = 'style="color:#CC3300"';
		} else {
			$f = $f.' byte'.($f==1?'':'s');
			$color = 'style="color:#009900"';
		}
		if ($colorize) $f = '<span '.$color.'>'.$f.'</span>';
		return $f;
	}
	
	public static function mem($mem_str = false, $kb = 0) {
		if (!$mem_str) {
			$memory = memory_get_usage(true) - MEMORY;
			$full_memory = memory_get_usage(true);
			$mtime = explode(' ',microtime());
			$pageload = $mtime[1] + $mtime[0] - Conf()->g('start_time');
			if (SITE_TYPE=='index') {
				$files = count(get_included_files());
				$size = 0;
				$j = array();
				$r = str_replace('inc'.DIRECTORY_SEPARATOR.'Message.php','',__FILE__);
				foreach (get_included_files() as $i => $f) {
					$s = filesize($f);
					$j[] = ($i+1).'. '.str_replace('\\','/',str_replace($r,'',$f)).' <span style="color:#666;font-size:11px">('.self::display_size($s).')</span>';
					$size += $s;
				}
				$t = 'Site memory usage: '.self::display_size($memory).'<br>Total memory usage: '.self::display_size($full_memory).'<br>Database time: '.sprintf('%01.4fs',Conf()->g('dbEndTime')-Conf()->g('dbBeginTime')).'<br><a href="javascript:;" onclick="document.getElementById(\'a-sqls_list\').style.display=\'inline-block\';window.scrollTo(0,9999);" style="color:#000;font:12px \'Lucida Console\'">Database accesses: '.Conf()->g('dbAccesses').'</a><br> Page load: '.sprintf('%01.4fs',$pageload).'<br><a href="javascript:;" onclick="document.getElementById(\'a-files_includes_list\').style.display=\'inline-block\';window.scrollTo(0,9999);" style="color:#000;font:12px \'Lucida Console\'">Files included: '.$files.'</a><br>Files size: '.self::display_size($size).'';
			} else {
				$t = '<div style="color:#000;font:12px \'Lucida Console\';text-align:right">Memory: '.self::display_size($memory).'/'.self::display_size($full_memory).'<br>DB: '.sprintf('%01.4fs',Conf()->g('dbEndTime')-Conf()->g('dbBeginTime')).'['.Conf()->g('dbAccesses').'], Page: '.sprintf('%01.4fs',$pageload).'</div>';
			}
			$ret = '<div ondblclick="try{$(this).remove()}catch(e){this.style.display=\'none\';}" style="color:#000;border:2px solid #ccc;clear:both;padding:5px 15px;background:#fff;margin:10px;'.(SITE_TYPE=='ajax'?'margin-left:0;':'').'display:inline-block;'.(SITE_TYPE!='index'?'height:88px;overflow:auto':'').'">'.Site::$mem_str.(SITE_TYPE=='index'?'<div style="font:12px \'Lucida Console\';text-align:right;">'.$t.'</div></div><div id="a-files_includes_list" style="display:none;text-align:left;background:#fff;font:12px \'Lucida Console\';padding:4px 10px;border:2px solid #ccc;margin:10px;margin-top:0;color:#000">'.join('<br>',$j).'</div><div id="a-sqls_list" style="display:none;text-align:left;font:12px \'Lucida Console\';">'.self::sqls().'</div>':$t.'</div>');
			return $ret;
		}
		if (!Site::$mem_str) Site::$mem_str = '<div style="font:12px Arial"><span style="display:inline-block;width:180px;padding-right:4px;text-align:right;font:12px \'Lucida Console\'">index.php (blank):</span> <b style="width:30px;display:inline-block;text-align:right;margin-right:10px;">512</b> <span style="color:#777">(512)</span></div>';
		$bytes = $kb - Site::$mem;
		if ($bytes>0) {
			$arr = array('end'=>'red','start'=>'green','load'=>'purple','factory'=>'steelblue');
			foreach ($arr as $l => $c) $mem_str = str_replace(' '.$l,' <span style="color:'.$c.'">'.$l.'</span>',$mem_str);
			Site::$mem_str .= '<div style="font:12px Arial"><span style="display:inline-block;width:180px;padding-right:4px;text-align:right;font:12px \'Lucida Console\'">'.$mem_str.':</span> <b style="width:30px;display:inline-block;text-align:right;margin-right:10px;">'.round($bytes / 1024).'</b> <span style="color:#777">('.round($kb/1024).')</span></div>';
		}
		Site::$mem = $kb;	
	}


	public static function sqls() {
		if (!Conf()->g('DB_SQL')) return false;
		$arr = $diffs = array();
		$oldTime = Conf()->g('start_time');
		foreach (Conf()->g('DB_SQL') as $i => $a) {
			$diff = sprintf('%01.4f s.',($a['time'] - $oldTime));
			$oldTime = $a['time'];
			$arr[] = array(
				'i'		=> $i+1,
				'diff'	=> $diff,
				'sql'	=> $a['sql'],
				'time'	=> $a['time'],
			);
			$diffs[] = $diff;
		}
		$min = min($diffs);
		$max = max($diffs);
		
		$f .= '<table id="a-sqls" style="margin:10px;border:1px solid #ccc;margin-top:0">';
		foreach ($arr as $i => $a) {
			if (DB_SQL_HL) {
				$s = self::sql(wrap($a['sql']));
			} else {
				$s = nl2br(html($a['sql']));
			}
			$f .= '<tr><td style="background:#fff;color:#000">'.$a['i'].'</td><td style="background:#fff;color:#000;font:11px monospace;'.($i?'border-top:2px solid #ccc':'').'">'.$s.'</td><td nowrap'.($max==$a['diff']?' style="font-weight:bold;color:red;background:#fff;"':' style="background:#fff;color:#000"').'>'.$a['diff'].'</td></tr>';
		}
		$f .= '</table>';
		return $f;	
	}
	
	/*
	public static function bug($title, $msg = '', $in = array()) {
		self::log($title, $msg);
	}
	*/
	public static function log($title, $msg) {
		
		//if (isset($_SESSION['LOGGED']) && $_SESSION['LOGGED']==$title) return false;
		if (!is_file(FTP_DIR_ROOT.'config/error.log')) @touch(FTP_DIR_ROOT.'config/error.log');
		if (!is_file(FTP_DIR_ROOT.'config/error.log')) return false;
		
		if ($fp = @fopen($f,'ab')) {
			$ip = Session::getIP();
			$url = URL::getFull();
			$time = date('d-M-Y H:i:s');
			$start = "[$title] $time [$ip] URL: $url\r\n";
			$end = "\r\n\r\n";
			$_SESSION['LOGGED'] = $title;
			$msg = str_replace('&nbsp;',' ',strip_tags(str_replace(array('<br />','<br>'),"\r\n",$msg)));
			$msg = $start.$msg.$end;
			$f = FTP_DIR_ROOT.'config/error.log';
			if (filesize($f) > 1024 * 3000) {
				$f = FTP_DIR_ROOT.'config/error_'.date('M_Y').'.log';
			}
			fwrite($fp,$msg);
			fclose($fp);
		}
	}
}