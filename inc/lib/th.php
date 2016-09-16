<?

if (!defined('DB_NAME')) die();
define('OPEN_CACHE_DIR','./files/temp/');


function clearCache($cache_dir, $days) {
	if($dp = @opendir($cache_dir)) {
		while($file = readdir($dp)) {
			if(preg_match('/^img_/', $file)) {
				$mtime = @filemtime($cache_dir.$file);
				if($mtime < time() - 3600 * 24 * $days) @unlink($cache_dir.$file);
			}
		}
		closedir($dp);
	}
}


function gdInfo() {
	$gd = array(
		'GD Version' => '',
		'FreeType Support' => 0,
		'FreeType Linkage' => '',
		'T1Lib Support' => 0,
		'GIF Read Support' => 0,
		'GIF Create Support' => 0,
		'GIF Support' => 0,
		'JPG Support' => 0,
		'PNG Support' => 0,
		'WBMP Support' => 0,
		'XBM Support' => 0
	);
	
	if(function_exists('ob_start')) {
		ob_start();
		phpinfo();
		$info = explode("\n", ob_get_contents());
		ob_end_clean();
		
		for($i = 0; $i < count($info); $i++) {
			while(list($str, $v) = each($gd)) {
				if( strstr($info[$i], $str)) {
					$val = trim(str_replace($str, '', strip_tags($info[$i])));
					$gd[$str] = ($val == 'enabled') ? 1 : $val;
				}
			}
			reset($gd);
		}
		if($gd['GIF Support']) $gd['GIF Read Support'] = $gd['GIF Create Support'] = 1;
		else if($gd['GIF Read Support'] && $gd['GIF Create Support']) $gd['GIF Support'] = 1;
	}
	return $gd;
}


function getImgType($data) {
	$header = substr($data, 0, 20);	
	if(strstr($header, 'GIF')) $type = 'gif';
	else if(strstr($header, 'JFIF') || strstr($header, 'Exif')) $type = 'jpeg';
	else if(strstr($header, 'PNG')) $type = 'png';
	else if(strstr($header, 'FWS') || strstr($header, 'CWS')) $type = 'swf';
	else $type = '';	
	return $type;
}

function getNewSize($src_w, $src_h) {
	global $width, $height;	
	$perc_w = $width * 100 / $src_w;
	$perc_h = $height * 100 / $src_h;
	$div = ($perc_w < $perc_h) ? 100 / $perc_w : 100 / $perc_h;	
	$dst_w = round($src_w / $div);
	$dst_h = round($src_h / $div);	
	return array($dst_w, $dst_h);
}


function viewImage($file, $type) {
	$last_modified = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';
	header("Content-Type: image/$type");
	header("Last-Modified: $last_modified");
	header('Cache-control: private, no-cache, must-revalidate');
	header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
	header('Date: Sat, 01 Jan 2000 00:00:00 GMT');
	header('Pragma: no-cache');
	if (function_exists('readfile')) {
		readfile($file);
	} else {
		readfile_chunked($file);
	}
	exit;
}


function readfile_chunked($filename) { 
	$chunksize = 100*1024;
	$buffer = ''; 
	$handle = fopen($filename, 'rb'); 
	if ($handle===false) { 
		return false; 
	} 
	while (!feof($handle)) { 
		$buffer = fread($handle, $chunksize); 
		print $buffer; 
	} 
	return fclose($handle);
}

function readCachedThumb($original, $thumbname, $view = true) {
	if (file_exists($original)) {
		if(file_exists("$thumbname.gif")) $type = 'gif';
		elseif (file_exists("$thumbname.jpeg")) $type = 'jpeg';
		elseif (file_exists("$thumbname.png")) $type = 'png';
		else $type = '';
		
		if ($type) {
			$thumbnail = "$thumbname.$type";
			clearstatcache();
			$last_modified = gmdate('D, d M Y H:i:s', filemtime($thumbnail)) . ' GMT';
		
			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
				$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
		
				if ($if_modified_since==$last_modified) {
					if ($view) {
						header('HTTP/1.0 304 Not Modified');
						exit;
					}
					else return true;
				}
			}
			if ($view) viewImage($thumbnail, $type);
			else return true;
		}
	}
	return false;
}


function createThumbIM($original, $thumbname, $type, $view = true) {
	global $IM_path;	
	if (!$IM_path) return false;
	$imgsize = @getImageSize($original);
	list($dst_w, $dst_h) = getNewSize($imgsize[0], $imgsize[1]);
	$format = $dst_w . 'x' . $dst_h;
	$type2 = ($type == 'gif') ? 'png' : $type;
	$thumbnail = $thumbname . '.' . $type2;
	
	@exec("$IM_path/convert -sample $format $type:$original $type2:$thumbnail");
	
	if(file_exists("$thumbnail.0")) {
		@rename("$thumbnail.0", $thumbnail);
		for ($cnt = 1; file_exists("$thumbnail.$cnt"); $cnt++) @unlink("$thumbnail.$cnt");
	}

	if(file_exists($thumbnail)) {
		if(filesize($thumbnail) == 0) @unlink($thumbnail);
		else if($view) viewImage($thumbnail, $type2);
		else return true;
	}

}


function createThumbGD($original, $thumbname, $type, $view = true) {
	$error = '';
	$gd = function_exists('gd_info') ? gd_info() : gdInfo();
	$gd_ver = ereg_replace('[^0-9.]+', '', $gd['GD Version']);
	
	switch($type) {
		case 'gif': if(!$gd['GIF Read Support'] && !$gd['GIF Support']) $error = "GD $gd_ver: No GIF support"; break;
		case 'png': if(!$gd['PNG Support']) $error = "GD $gd_ver: No PNG support"; break;
		case 'jpeg': if(!$gd['JPG Support']) $error = "GD $gd_ver: No JPG support"; break;
		default: $error = 'No ' . strtoupper($type) . ' support'; break;
	}
	
	if(!$error) {
		if($fp = @fopen($original, 'rb')) {
			$data = fread($fp, filesize($original));
			fclose($fp);
			
			if($data) {
				if(function_exists('ImageCreateFromString')) {
					$src_img = @ImageCreateFromString($data);
				}
				if(!$src_img) {
					$php_ver = phpversion();			
					if($type == 'jpeg') {
						if(function_exists('ImageCreateFromJPEG')) {
							$src_img = @ImageCreateFromJPEG($original);
						}
						if(!$src_img) $error = "PHP $php_ver: No JPG support";
					}
					else if($type == 'gif') {
						if (function_exists('ImageCreateFromGIF')) {
							$src_img = @ImageCreateFromGIF($original);
						}
						if (!$src_img) $error = "PHP $php_ver: No GIF support";
					}
					else if($type == 'png') {
						if(function_exists('ImageCreateFromPNG')) {
							$src_img = @ImageCreateFromPNG($original);
						}
						if(!$src_img) $error = "PHP $php_ver: No PNG support";
					}
					else $error = 'Unknown type';
				}	
				if (!$error) {
					list($dst_w, $dst_h) = getNewSize(ImageSX($src_img), ImageSY($src_img));
			
					if($gd_ver >= 2.0) {
						if($type != 'gif' && function_exists('ImageCreateTrueColor')) $dst_img = ImageCreateTrueColor($dst_w, $dst_h);
						else $dst_img = ImageCreate($dst_w, $dst_h);
					
						if(function_exists('ImageCopyResampled')) {
							ImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, ImageSX($src_img), ImageSY($src_img));
						}
						else {
							ImageCopyResized($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, ImageSX($src_img), ImageSY($src_img));
						}
					} else {
						$dst_img = ImageCreate($dst_w, $dst_h);
						ImageCopyResized($dst_img, $src_img, 0, 0, 0, 0, $dst_w, $dst_h, ImageSX($src_img), ImageSY($src_img));
					}
			
					if($type == 'jpeg') {
						if(function_exists('ImageJPEG')) @ImageJPEG($dst_img, "$thumbname.$type");
						else $error = "PHP $php_ver: No JPG preview";
					}
					else if($type == 'gif') {
						if(function_exists('ImageGIF')) @ImageGIF($dst_img, "$thumbname.$type");
						else if(function_exists('ImagePNG')) @ImagePNG($dst_img, "$thumbname.png");
						else $error = "PHP $php_ver: No GIF preview";
					}
					else if($type == 'png') {
						if(function_exists('ImagePNG')) @ImagePNG($dst_img, "$thumbname.$type");
						else $error = "PHP $php_ver: No PNG preview";
					}
					ImageDestroy($src_img);
					ImageDestroy($dst_img);			
					if($view) readCachedThumb($original, $thumbname);
				}
			} else $error = 'No data';
		} else $error = 'Could not open';
	}
	return $error;
}

function showMessage($text, $width, $height, $font = 2) {
	if($img = @ImageCreate($width, $height)) {
		$red = ImageColorAllocate($img, 255, 0, 0);
		$white = ImageColorAllocate($img, 255, 255, 255);
		ImageFill($img, 0, 0, $white);
		ImageColorTransparent($img, $white);
		$hcenter = round($width / 2);
		$vcenter = round($height / 2);
		$x = round($hcenter - ImageFontWidth($font) * strlen($text) / 2);
		$y = round($vcenter - ImageFontWidth($font) / 2);
		ImageString($img, $font, $x, $y, $text, $red);
		
		if(function_exists('ImagePNG')) {
			header('Content-Type: image/png');
			@ImagePNG($img);
		}
		else if(function_exists('ImageGIF')) {
			header('Content-Type: image/gif');
			@ImageGIF($img);
		}
		else if(function_exists('ImageJPEG')) {
			header('Content-Type: image/jpeg');
			@ImageJPEG($img);
		}
		ImageDestroy($img);
	}
}




function ThumbnailImage($params) {
	global $width, $height,$IM_path,$CD_CONFIG;
	
	$settings = array(
		'blank' 		=> 'R0lGODlhCgAKAIAAAMDAwAAAACH5BAEAAAAALAAAAAAKAAoAAAIIhI+py+0PYysAOw=='
		,'max_width'	=> $CD_CONFIG['DefaultThumbWidth']
		,'max_height'	=> $CD_CONFIG['DefaultThumbHeight']
		,'cache_dir'	=> OPEN_CACHE_DIR
		,'autodelete'	=> AUTODELETE
		,'IM_path'		=> IMAGEMAGICK_PATH
		,'db_host'		=> DB_SERVER
		,'db_user'		=> DB_USERNAME
		,'db_pass'		=> DB_PASSWORD
		,'database'		=> DB_NAME
		,'key'			=> DEFAULT_DB_KEY
	);
	
	if (!$params['width']) $params['width'] = $settings['max_width'];
	if (!$params['height']) $params['height'] = $settings['max_height'];
	if (!$params['cache_dir']) $params['cache_dir'] = $settings['cache_dir'];
	if (!$params['database']) $params['database'] = $settings['database'];
	if (!$params['key']) $params['key'] = $settings['key'];
	
	extract($settings);
	extract($params);

	$error = '';
	
	if ($autodelete) clearCache($cache_dir, $autodelete);
	if ($image) {
		if(strstr($table, '.')) {
			$arr = explode('.', $table);
			$database = $arr[0];
			$table = $arr[1];
		}
		if ($image) {
			if($fp = @fopen($image, 'rb')) {
				$size = filesize($image);
				$data = fread($fp, $size);
				fclose($fp);
				$original = $cache_dir.'img_'.md5($image.$size);
			}
			else $error = 'Could not open';
		}
		elseif ($id) {
			if (mysql_connect($db_host, $db_user, $db_pass)) {
				$sql = "SELECT $field FROM $database.$table WHERE $key='$id'";
				if($result = mysql_query($sql)) {
					if (mysql_num_rows($result)) $data = mysql_result($result, $field);
					else $error = 'Not found';
				}
				else $error = 'Bad SQL query';
				mysql_close();
				$original = $cache_dir.'img_'.md5("$database$table$field$id" . strlen($data));
			} else $error = 'No connection';
		} else $error = 'Database?';
		
		if (!$error) {
			if (!$data) {
				header("Content-Type: image/gif");
				echo base64_decode($blank);
				exit;
			} else {
				$thumbname = $original . '_' . $width . 'x' . $height;
				$cached = readCachedThumb($original, $thumbname);
				if (!$cached) {
					@unlink("$thumbname.gif");
					@unlink("$thumbname.jpeg");
					@unlink("$thumbname.png");
				
					if(!file_exists($original)) {
						if($fp = @fopen($original, 'wb')) {
							fwrite($fp, $data, strlen($data));
							fclose($fp);
						} else $error = 'Could not save';
					}
			
					if (!$error) {
						$imgsize = @getImageSize($original);
						
						switch ($imgsize[2]) {
							case 1: $type = 'gif'; break;
							case 2: $type = 'jpeg'; break;
							case 3: $type = 'png'; break;
							case 4: $type = 'swf'; break;
							default: $type = getImgType($data); break;
						}
			
						if($type == 'swf') $error = 'No SWF preview';
						elseif ($type) {	
							if ($imgsize[0] <= $width && $imgsize[1] <= $height) {
								viewImage($original, $type);
							} else {
								if($imgsize[0] < $width) $width = $imgsize[0];
								if($imgsize[1] < $height) $height = $imgsize[1];
								
								if (!createThumbIM($original, $thumbname, $type)) {
									$error = createThumbGD($original, $thumbname, $type);
								}
							}
						}
						else $error = 'Unknown type';
					}
				}
			}
		}
	} else $error = 'Arguments?';
	if ($error) showMessage($error, ($width < 50) ? 50 : $width, $height);
}