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
* @file       inc/Image.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Image {
	
	protected $image;
	protected $type = '';
	public $img_x = 0;
	public $img_y = 0;
	public $img_info = 0;
	
	public $new_x = 0;
	public $new_y = 0;
	public $lib_path = '';
	public $resized = false;
	public $use_largest = false;
	public $error = false;
	private static $_instances = array();
	
	public function driver($driver = 'GD') {
		if (!$driver) $driver = 'GD';
		$className = 'Image'.$driver;
		if (!isset(self::$_instances[$driver])) self::$_instances[$driver] = new $className;
		return self::$_instances[$driver];
	}

	public function resize($x, $y) {
		if (!$x && !$y) return false;
		elseif (!$y) {
			$y = round($this->img_y * $x / $this->img_x);
		}
		elseif (!$x) {
			$x = round($this->img_x * $y / $this->img_y);
		}
		return $this->_resize($x, $y);
	}
	
	public function is_ani() {
		$filename = $this->image;
		$ex = explode('/',$filename);
		$f = $ex[count($ex)-1];
		if (Conf()->exists('is_ani_arr',$f)) {
			return Conf()->g2('is_ani_arr',$f);
		}
		$ext = ext($f);
		if ($ext!='gif') $ret = false;
		if (!($fh = @fopen($filename, 'rb'))) $ret = false; 
		else {
			$count = 0; 
			//an animated gif contains multiple "frames", with each frame having a 
			//header made up of: 
			// * a static 4-byte sequence (\x00\x21\xF9\x04) 
			// * 4 variable bytes 
			// * a static 2-byte sequence (\x00\x2C) 
		 
			// We read through the file til we reach the end of the file, or we've found 
			// at least 2 frame headers 
			while(!feof($fh) && $count < 2) { 
				$chunk = fread($fh, 1024 * 50); //read 50kb at a time 
				$c = preg_match_all('#\x00\x21\xF9\x04.{4}\x00\x2C#s', $chunk, $m);
				$count += $c;
			}
			fclose($fh);
			$ret = $count > 1; 
		}
		Conf()->s2('is_ani_arr',$f,$ret);
		return $ret;
	} 

	
	public function massResize($sizes, $arr_save, $file) {
		if ($file) {
			$path = $arr_save;
			if (!File::isPicture($file) || !$sizes) {
				@rename($path.$file,$path.'th1/'.$file);
				return true;
			}
			if (!is_dir($path)) mkdir($path,0777);
			foreach ($sizes as $i => $size) {
				$dir = $path.'th'.$i.'/';
				if (!is_dir($dir)) mkdir($dir,0777);
				if ($size && !$this->is_ani()) {
					$this->resize($size[0], $size[1])->save($dir.$file);
				} elseif ($i==1) {
					copy($path.$file,$dir.$file);
				}
			}
			@unlink($path.$file);
		} else {
			foreach ($sizes as $i => $size) {
				if ($size && !$this->is_ani()) {
					$this->resize($size[0], $size[1])->save($arr_save[$i]);	
				} else {
					copy($path.$file,$dir.$file);
				}
			}
		}
		$this->free();
	}

	
	public function _get_image_details($image, $getOnly = false) {
		$data = @getimagesize($image);
		#1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(intel byte order), 8 = TIFF(motorola byte order,
		# 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC
		if (!$data || !is_array($data)) return false;
		switch($data[2]){
			case 1:
				$type = 'gif';
			break;
			case 2:
				$type = 'jpeg';
			break;
			case 3:
				$type = 'png';
			break;
			case 4:
				$type = 'swf';
			break;
			case 5:
				$type = 'psd';
			case 6:
				$type = 'bmp';
			case 7:
			case 8:
				$type = 'tiff';
			default:
				return false;
		}
		if ($getOnly) {
			return array(
				'width'	=> $data[0],
				'height'=> $data[1],
				'type'	=> $type,
				'info'	=> $data[2]
			);
		}
		$this->img_x = $data[0];
		$this->img_y = $data[1];
		$this->img_info = $data[2];
		$this->type = $type;
		return $this;

	}
	public function load($image) {
		exit;	
	}
}






class ImageIM extends Image {
	
	private $command = array();
	
	public function load($image) {
		$this->error = false;
		$this->uid = md5($_SERVER['REMOTE_ADDR']);
		$this->image = $image;
		$this->_get_image_details($image);
		return $this;
	}
	
	public function _resize($new_x, $new_y) {
	   $this->command['resize'] = "-geometry ${new_x}x${new_y}!";

	   $this->new_x = $new_x;
	   $this->new_y = $new_y;
	   return $this;
	}

	public function crop($crop_x, $crop_y, $crop_width, $crop_height) {
	   $this->command['crop'] = "-crop {$crop_width}x{$crop_height}+{$crop_x}+{$crop_y}";
	   return $this;
	}

   
	public function flip($horizontal) {
		if ($horizontal)	$this->command['flop'] = "-flop";
		else $this->command['flip'] = "-flip";
		return $this;
	}
   
	public function rotate($angle, $options=null) {
		if ('-' == $angle{0}) {
			$angle = 360 - substr($angle, 1);
		}
		$this->command['rotate'] = "-rotate $angle";
		return $this;
	}

	public function addText($params) {
	   $default_params = array(
							'text' => 'This is Text',
							'x' => 10,
							'y' => 20,
							'color' => 'red',
							'font' => 'Arial.ttf',
							'resize_first' => false
						);
		$params = array_merge($default_params, $params);
		extract($params);
		if (true === $resize_first) {
		  $key = 'ztext';
		} else {
		  $key = 'text';
		}
		$this->command[$key] = "-font $font -fill $color -draw 'text $x,$y \"$text\"'";
		return $this;
	}
	
	public function gamma($outputgamma=1.0) {
	   $this->command['gamma'] = "-gamma $outputgamma";
	   return $this;
	}

	public function save($filename, $type='', $quality = 85) {
		$type == '' ? $this->type : $type;
		$cmd = '' . IMAGE_TRANSFORM_LIB_PATH . 'convert ';
		$cmd .= implode(' ', $this->command) . " -quality $quality ";
		$cmd .= '"'.($this->image) . '" "' . ($filename) . '" 2>&1';
		exec($cmd,$retval);
		return $this;
	}

	public function display($type = '', $quality = 75) {
	   if ($type == '') {
		  header('Content-type: image/' . $this->type);
		  passthru(IMAGE_TRANSFORM_LIB_PATH . 'convert ' . implode(' ', $this->command) . " -quality $quality "  . escapeshellarg($this->image) . ' ' . strtoupper($this->type) . ":-");
	   } else {
		  header('Content-type: image/' . $type);
		  passthru(IMAGE_TRANSFORM_LIB_PATH . 'convert ' . implode(' ', $this->command) . " -quality $quality "  . escapeshellarg($this->image) . ' ' . strtoupper($type) . ":-");
	   }
	}

	public function free() {
	  return $this;
	}
}








class ImageGD extends Image {
	
	protected $imageHandle;
	protected $imageHandle_save = false;
	protected $old_image;
	protected $uid;

	public function load($image) {
		$this->error = false;
		$this->uid = md5($_SERVER['REMOTE_ADDR']);
		$this->image = $image;
		$this->_get_image_details($image);
		$functionName = 'imagecreatefrom'.$this->type;
		ini_set ('gd.jpeg_ignore_warning', 1);
		if (function_exists($functionName)) {
			$this->imageHandle = @$functionName($this->image);
		}
		return $this;
	}
	
	protected function _resize($new_x, $new_y) {

		if ($this->resized===true) {
			$this->error = true;
			return $this;
		}
		if (!$this->img_x && !$this->img_y) {
			$w = $new_x;
			$h = $new_y;
		}
		elseif ($this->img_x <= $new_x && $this->img_y <= $new_y) {
			$h = $this->img_y;
			$w = $this->img_x;
		} else {
			$w = $new_x;
			$h = $new_y;

			if ($this->img_x >= $this->img_y) {
				if ($w / $this->img_x > $new_y / $this->img_y) {
					$w = $new_x;
					$h = $this->img_y * ($new_x / $this->img_x);
				} else {
					$w = $this->img_x * ($new_y / $this->img_y);
					$h = $new_y;
				}
			} else {
				if ($h / $this->img_y > $new_x / $this->img_x) {
					$h = $new_y;
					$w = $this->img_x * ($new_y / $this->img_y);
				} else {
					$h = $this->img_y * ($new_x / $this->img_x);
					$w = $new_x;
				}
			}
		}
			
		$new_img = imagecreatetruecolor($w, $h);
		if ($this->img_info == 1 || $this->img_info==3) {
			imagealphablending($new_img, false);
			imagesavealpha($new_img,true);
			$transparent = imagecolorallocatealpha($new_img, 255, 255, 255, 127);
			imagefilledrectangle($new_img, 0, 0, $w, $h, $transparent);
		}		
		if (function_exists('imagecopyresampled')) {
			imagecopyresampled($new_img, $this->imageHandle, 0, 0, 0, 0, $w, $h, $this->img_x, $this->img_y);
		} else {
			imagecopyresized($new_img, $this->imageHandle, 0, 0, 0, 0, $w, $h, $this->img_x, $this->img_y);
		}
		$this->resized = true;
		if ($this->use_largest) {
			$this->imageHandle_save = $new_img;
		} else {
			$this->img_x = $w;
			$this->img_y = $h;
			$this->imageHandle = $new_img;	
		}
		$this->new_x = $w;
		$this->new_y = $h;
		return $this;
		/*
		$this->old_image = $this->imageHandle;
		$this->imageHandle = $new_img;
		*/
	}
	
	
	public function addText($params) {
		$default_params = array(
			'text' => 'This is Text',
			'x' => 10,
			'y' => 20,
			'color' => array(255,0,0),
			'font' => 'Arial.ttf',
			'size' => '12',
			'angle' => 0,
			'resize_first' => false
		);
		$params = array_merge($default_params, $params);
		extract($params);
		if (!is_array($color)) {
			if ($color[0]=='#') {
				$color = $this->colorhex2colorarray($color);
			} else {
				$color = array(255,255,255);
			}
		}
		$c = imagecolorresolve($this->imageHandle, $color[0], $color[1], $color[2]);
		if ('ttf'==substr($font,-3)) {
			imagettftext($this->imageHandle, $size, $angle, $x, $y, $c, $font, $text);
		} else {
			imagepstext($this->imageHandle, $size, $angle, $x, $y, $c, $font, $text);
		}
		return $this;
	}
	
	
	public function putText($text, $font, $x_pos, $y_pos, $color) {
		$w = $this->img_x;
		$h = $this->img_y;
	
		if ($x_pos=='center' || $x_pos=='CENTER' || $x_pos==Center) {
			$px = (int) ($w / 2);
		}
		elseif ($x_pos=='left' || $x_pos=='Left' || $x_pos=='Left' || intval($x_pos) >= 0) {
			$x_pos = (int) $x_pos;
			if ($x_pos >= $w) $x_pos = 0;
			$px = $x_pos;
		 } else {
			$x_pos = (int) $x_pos;
			if ($x_pos < 0) {
				if ($x_pos < -$w) $px = $w ;
				else $px = $w + $x_pos;
			} else {
				$px = $w;
			}
		}
		if ($y_pos=='center' || $y_pos=='CENTER' || $y_pos==Center) {
			$py = (int) ($h / 2);
		} elseif ($y_pos=='top' || $y_pos=='Top' || $y_pos=='TOP' || intval($y_pos) >= 0) {
			$y_pos = (int) $y_pos;
			if ($y_pos >= $h) $y_pos = 0;
			$py = $y_pos;
		} else {
			$y_pos = (int) $y_pos;
			if ($y_pos < 0) {
				if ($y_pos < -$h) $py = $h;
				else $py = $h + $y_pos;
			} else {
				$py = $h;
			}
		}
		$color = $this->get_html_color($color);
		imagestring($this->imageHandle, $font, $px, $py, $text, $color);
		return $this;
	}
	public function hex2rgb($color) {
		$color = str_replace('#', '', $color);
		return (array(1 => hexdec('0x'.$color{0}.$color{1}), 2 => hexdec('0x'.$color{2}.$color{3}), 3 => hexdec('0x'.$color{4}.$color{5})));
	}
	
	public function get_html_color($color) {
		$color = $this->hex2rgb($color);
		$color = imagecolorallocate($this->image, $color[1], $color[2], $color[3]);
		return $color;
	}
	public function createBlank($width, $height, $bg_color) {
		$width = (int) $width;
		$height = (int) $height;
		if ($width < 1)	$width = 1;
		if ($height < 1)	$height = 1;
		if ($height > $this->max_side_size || $width > $this->max_side_size) {
			$this->error = true;
			return $this;
		}
		if ($this->imageHandle = imagecreatetruecolor($width, $height)) {
			if ($color = $this->get_html_color($bg_color))	{
				imagefill($this->imageHandle, 0, 0, $color);
			}
			$this->width = $img_x;
			$this->height = $img_y;
			return $this;
		}
		else {
			$this->error = true;
			return $this;
		}
	}
	public function putImage($p_image, $x_pos=0, $y_pos=0) {
		if (!$p_image) return false;
		$pw = imagesx($p_image);
		$ph = imagesy($p_image);
		$w = $this->img_x;
		$h = $this->img_y;
		if (!$ph || !$pw || $ph < 1 || $pw < 1 || $ph > $this->max_side_size || $pw > $this->max_side_size) return false;
		if (imagecopyresized($this->imageHandle, $p_image, $x_pos, $y_pos, 0, 0, $pw, $ph, $pw, $ph)) return true;
		else return false;
	}
	public function makeTransparent($newfile, $from = array()) {
		if (!$from) $from = array(0xFF, 0xFF, 0xFF);
		$img = imagecreatetruecolor($this->img_x,$this->img_y);
		$trans = imagecolorallocate($img, $from[0], $from[1], $from[2]);
		imagecolortransparent($img,$trans);
		imagecopy($img,$this->imageHandle,0,0,0,0,$this->img_x,$this->img_y);
		imagetruecolortopalette($img, true, 256);
		imageinterlace($img);
		$this->imageHandle = $img;
	}

	public function rotate($angle, $options=NULL) {
		if (function_exists('imagerotate') && 0) {
			$white = imagecolorallocate ($this->imageHandle, 255, 255, 255);
			$this->imageHandle = imagerotate($this->imageHandle, $angle, $white);
			return $this;
		}
		if ($options==NULL){
			$autoresize = true;
			$color_mask = array(255,255,0);
		} else {
			extract($options);
		}
		while ($angle <= -45) $angle += 360;
		while ($angle > 270) $angle -= 360;
		$t = deg2rad($angle);
		if (!is_array($color_mask)) {
			if ($color[0]=='#'){
				$color_mask = $this->colorhex2colorarray($color_mask);
			} else {
				$color_mask = array(255,255,255);
			//	include_once('Image/Transform/Driver/ColorDefs.php');
			//	$color = isset($colornames[$color_mask])?$colornames[$color_mask]:false;
			}
		}
		$cosT   = cos($t);
		$sinT   = sin($t);
		$img	=& $this->imageHandle;
		$width  = $max_x  = $this->img_x;
		$height = $max_y  = $this->img_y;
		$min_y  = 0;
		$min_x  = 0;
		$x1	= round($max_x/2,0);
		$y1	= round($max_y/2,0);
		
		if ($autoresize){
			$t = abs($t);
			$a = round($angle,0);
			switch((int)($angle)){
				case 0:
					$width2	= $width;
					$height2	= $height;
				break;
				case 90:
					$width2	= $height;
					$height2	= $width;
				break;
				case 180:
					$width2	= $width;
					$height2	= $height;
				break;
				case 270:
					$width2	= $height;
					$height2	= $width;
				break;
				default:
					$width2	= (int)(abs(sin($t) * $height + cos($t) * $width));
					$height2	= (int)(abs(cos($t) * $height+sin($t) * $width));
				break;
			}
			$width2	-= $width2%2;
			$height2	-= $height2%2;
			$d_width	= abs($width - $width2);
			$d_height   = abs($height - $height2);
			$x_offset   = $d_width/2;
			$y_offset   = $d_height/2;
			$min_x2	= -abs($x_offset);
			$min_y2	= -abs($y_offset);
			$max_x2	= $width2;
			$max_y2	= $height2;
		}
		if(function_exists('imagecreatetruecolor')){
			$img2 = imagecreatetruecolor($width2,$height2);
		} else {
			$img2 = imagecreate($width2,$height2);
		}
		if (!is_resource($img2)) {
			$this->error = true;
			return $this;
		}
		$this->img_x = $width2;
		$this->img_y = $height2;
		imagepalettecopy($img2,$img);
		$mask = imagecolorresolve($img2,$color_mask[0],$color_mask[1],$color_mask[2]);
		switch((int)($angle)){
			case 0:
				imagefill ($img2, 0, 0,$mask);
				for ($y=0; $y < $max_y; $y++) {
					for ($x = $min_x; $x < $max_x; $x++){
						$c  = @imagecolorat ( $img, $x, $y);
						imagesetpixel($img2,$x+$x_offset,$y+$y_offset,$c);
					}
				}
			break;
			case 90:
				imagefill ($img2, 0, 0,$mask);
				for ($x = $min_x; $x < $max_x; $x++){
					for ($y=$min_y; $y < $max_y; $y++) {
						$c  = imagecolorat ( $img, $x, $y);
						imagesetpixel($img2,$max_y-$y-1,$x,$c);
					}
				}
			break;
			case 180:
				imagefill ($img2, 0, 0,$mask);
				for ($y=0; $y < $max_y; $y++) {
					for ($x = $min_x; $x < $max_x; $x++){
						$c  = @imagecolorat ( $img, $x, $y);
						imagesetpixel($img2, $max_x2-$x-1, $max_y2-$y-1, $c);
					}
				}
			break;
			case 270:
				imagefill ($img2, 0, 0,$mask);
				for ($y=0; $y < $max_y; $y++) {
					for ($x = $max_x; $x >= $min_x; $x--){
						$c  = @imagecolorat ( $img, $x, $y);
						imagesetpixel($img2,$y,$max_x-$x-1,$c);
					}
				}
			break;
			default:
				$i=0;
				for ($y = $min_y2; $y < $max_y2; $y++) {
					$x2 = round((($min_x2-$x1) * $cosT) + (($y-$y1) * $sinT + $x1),0);
					$y2 = round((($y-$y1) * $cosT - ($min_x2-$x1) * $sinT + $y1),0);
					for ($x = $min_x2; $x < $max_x2; $x++) {
						if ($x2>=0 && $x2<$max_x && $y2>=0 && $y2<$max_y) {
							$c  = imagecolorat ( $img, $x2, $y2);
						} else {
							$c  = $mask;
						}
						imagesetpixel($img2,$x+$x_offset,$y+$y_offset,$c);
						$x2  += $cosT;
						$y2  -= $sinT;
					}
				}
			break;
		}
		$this->old_image = $this->imageHandle;
		$this->imageHandle = $img2;
		return $this;
	}
	
	
	public function addPic($img_name,$newname,$type,$w_file=0,$transparency=false,$position=0) {
		if (!$w_file) $w_file = Conf()->g('WM_file');
		if ($transparency===false) $transparency = Conf()->g('WM_transparency');
		if (!$position) $transparency = Conf()->g('WM_position');

		if (!$w_file || !file_exists($w_file)) {
			$this->error = true;
			return $this;
		}
		$png = $gif = false;
		if (!$type) {
			$size = getimagesize($img_name);
			if ($size[2]==2) $im = @imagecreatefromjpeg($img_name);
			elseif ($size[2]==3) {
				$im = @imagecreatefrompng($img_name);
				$png = true;
			} else {
				$im = @imagecreatefromgif($img_name);
				$gif = true;
			}	
		} else {
			if (!strcmp('image/jpg',$type) || !strcmp('image/jpeg',$type) || !strcmp('image/pjpeg',$type)) {
				$im = imagecreatefromjpeg($img_name);
			}	
			elseif (!strcmp('image/png',$type)) {
				$im = imagecreatefrompng($img_name);
				$png = true;
			}
			elseif (!strcmp('image/gif',$type)) {
				$im = imagecreatefromgif($img_name);
				$gif = true;
			}
			elseif (!strcmp('image/x-png',$type)) $im = imagecreatefrompng($img_name);
			elseif (!strcmp('image/x-gif',$type)) $im = imagecreatefromgif($img_name);
			elseif (!strcmp('image/x-jpg',$type)) $im = imagecreatefromjpeg($img_name);
		}
		if (!$im || !is_resource($im)) {
			$this->error = true;
			return $this;	
		}
		$old_x = imagesx($im);
		$old_y = imagesy($im);
		$wext = getFileExtension($w_file);
		if(!strcmp('jpg',$wext) || !strcmp('jpeg',$wext)) $w_im = imagecreatefromjpeg($w_file);
		if(!strcmp('png',$wext)) $w_im = imagecreatefrompng($w_file);
		if(!strcmp('gif',$wext)) $w_im = imagecreatefromgif($w_file);	
		$ww = imagesx($w_im);
		$wh = imagesy($w_im);
		
		$to = 10;
		$quality = 100;
		
		switch ($position) {
			case 'EC': // middle
				$dest_x = ($old_x/2 ) - ($ww/2); 
				$dest_y = ($old_y/2) - ($wh/2); 
			break;
			case 'TL': // top left 
				$dest_x = $to;
				$dest_y = $to;
			break;
			case 'TR': // top right 
				$dest_x = $old_x - $ww - $to; 
				$dest_y = $to; 
			break;
			case 'BR': // bottom right 
				$dest_x = $old_x - $ww - $to; 
				$dest_y = $old_y - $wh - $to;
			break;
			case 'BL': // bottom left
				$dest_x = $to; 
				$dest_y = $old_y - $wh - $to;
			break;
			case 'TM': // top middle
				$dest_x = ($old_x - $ww)/2 - $to; 
				$dest_y = $to;
			break;
			case 'BM': // bottom middle
				$dest_x = ($old_x-$ww)/2 - $to; 
				$dest_y = $old_y-$wh - $to; 
			break;
			default: // middle right
				$dest_x = $old_x - $ww - $to;
				$dest_y = $old_y - $wh - $to;
			break;
		}
		imagealphablending($w_im, true);
		imagealphablending($im, true);
		$this->imagecopymerge_alpha($im, $w_im, $dest_x, $dest_y, 0, 0, $ww, $wh, $transparency);
		if ($png) {
			if (function_exists('imagesavealpha')) imagesavealpha($im,true);
			if ($quality>10) {
				$quality = round($quality/10);
			}
			imagepng($im,$newname,$quality);
		}
		elseif ($gif) imagegif($im,$newname,$quality);
		else imagejpeg($im,$newname,$quality);
		imagedestroy($im);
		imagedestroy($w_im);
		return $this;
	}
	
	public function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) { 
		if (!isset($pct)){ 
			$this->error = true;
			return $this;
		} 
		$pct /= 100; 
		$w = imagesx($src_im);
		$h = imagesy($src_im);
		imagealphablending($src_im, false); 
		$minalpha = 127; 
		for ($x=0;$x<$w;$x++) 
		for ($y=0;$y<$h;$y++) { 
			$alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF; 
			if ($alpha < $minalpha) {
				$minalpha = $alpha; 
			} 
		} 
		for ($x=0;$x<$w;$x++) { 
			for ($y=0;$y<$h;$y++) { 
				$colorxy = imagecolorat($src_im, $x, $y); 
				$alpha = ($colorxy >> 24) & 0xFF; 
				if ($minalpha!==127) { 
					$alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha); 
				} else { 
					$alpha += 127 * $pct; 
				} 
				$alphacolorxy = imagecolorallocatealpha($src_im,($colorxy >> 16) & 0xFF,($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha); 
				if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) { 
					$this->error = true;
					return $this;
				} 
			} 
		} 
		imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h); 
		return $this;
	}
	

	public function save($filename, $type = '', $quality = 90)	{
		$type = $type=='' ? $this->type : $type;
		$functionName = 'image' . $type;
		if (function_exists($functionName)) {
			if ($this->type=='png') {
				if (function_exists('imagesavealpha')) imagesavealpha($this->imageHandle_save ? $this->imageHandle_save : $this->imageHandle,true);
				if ($quality > 10) {
					$quality = round($quality/10);
				}
			}
			if ($this->imageHandle_save) {
				if($type=='jpeg') $functionName($this->imageHandle_save, $filename, $quality);
				else $functionName($this->imageHandle_save, $filename);
			} else {
				if($type=='jpeg') $functionName($this->imageHandle, $filename, $quality);
				else $functionName($this->imageHandle, $filename);	
			}
			$this->resized = false;
		}
		return $this;
	}
	
	public function crop($new_x, $new_y, $new_width, $new_height) {
		if (function_exists('imagecreatetruecolor')) {
			$new_img = imagecreatetruecolor($new_width,$new_height);
		} else {
			$new_img = imagecreate($new_width,$new_height);
		}
		if (function_exists('imagecopyresampled')){
			imagecopyresampled($new_img, $this->imageHandle, 0, 0, $new_x, $new_y,$new_width,$new_height,$new_width,$new_height);
		} else {
			imagecopyresized($new_img, $this->imageHandle, 0, 0, $new_x, $new_y, $new_width,$new_height,$new_width,$new_height);
		}
		$this->old_image = $this->imageHandle;
		$this->imageHandle = $new_img;
		$this->resized = true;
		$this->new_x = $new_x;
		$this->new_y = $new_y;
		return $this;
	}

	public function flip($horizontal) {
		if(!$horizontal) {
			$this->rotate(180);
		}
		$width = imagesx($this->imageHandle); 
		$height = imagesy($this->imageHandle); 
		for ($j = 0; $j < $height; $j++) { 
			$left = 0; 
			$right = $width-1;
			while ($left < $right) { 
				$t = imagecolorat($this->imageHandle, $left, $j); 
				imagesetpixel($this->imageHandle, $left, $j, imagecolorat($this->imageHandle, $right, $j)); 
				imagesetpixel($this->imageHandle, $right, $j, $t); 
				$left++; $right--; 
			}
		}
		return $this;
	}

	public function gamma($outputgamma=1.0) {
		imagegammacorrect($this->imageHandle, 1.0, $outputgamma);
		return $this;
	}

	public function display($type = '', $quality = 90)	{
		if ($type != '') {
			$this->type = $type;
		}
		$functionName = 'Image' . $this->type;
		if(function_exists($functionName)) {
			header('Content-type: image/' . strtolower($this->type));
			$functionName($this->imageHandle, '', $quality);
			$this->imageHandle = $this->old_image;
			$this->resized = false;
			imagedestroy($this->old_image);
			$this->free();
		}
		return $this;
	}

	public function free() {
		if ($this->imageHandle){
			imagedestroy($this->imageHandle);
		}
		return $this;
	}
	
}