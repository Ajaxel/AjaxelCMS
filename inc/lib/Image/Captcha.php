<?php

class CaptchaImageHandler {
	//var $mode = "gd"; // GD or bmp
	var $code;
	var $invalid = false;
	
	var $font;
	var $spacing;
	var $width;
	var $height;
	var $dir;
	var $mode;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->dir = FTP_DIR_ROOT.'config/';
		if (empty($_SESSION['Captcha_name'])) {
			$this->invalid = true;
		}
		
		if(!extension_loaded('gd')) {
			$this->mode = 'bmp';
		}else{
			$required_functions = array('imagecreatetruecolor', 'imagecolorallocate', 'imagefilledrectangle', 'imagejpeg', 'imagedestroy', 'imageftbbox'); 
			foreach($required_functions as $func) {
				if(!function_exists($func)) {
					$this->mode = 'bmp';
					break;
				}
			}
		}
	}



	/**
	 * Loads the captcha image
	 */
	function loadImage() {
		$this->createCode();
		$this->setCode();
		$this->createImage();
	}



	/**
	* Creates the Captcha Code
	*/
	function createCode() {
		global $ConfigCaptcha;
		if ($this->invalid) {
			return;
		}

		if($this->mode == 'bmp') {
			$ConfigCaptcha['captcha_num_chars'] = 4;
			$this->code = rand( pow(10, $ConfigCaptcha['captcha_num_chars'] - 1), intval( str_pad('9', $ConfigCaptcha['captcha_num_chars'], '9') ) );
		}else{
			if ($ConfigCaptcha['captcha_word']) {
				$this->wordlist_file = $this->dir.'system/words.txt';
				$a = array();
				for ($i=0;$i<$ConfigCaptcha['captcha_word'];$i++) {
					$a[] = $this->readCodeFromFile();
				}
				$this->code = join(' ',$a);
				$ConfigCaptcha['captcha_num_chars'] = strlen($this->code);
			} else {
				$raw_code = md5(uniqid(mt_rand(), 1));
				if (isset($ConfigCaptcha['captcha_skip_characters'])) {
					$valid_code = str_replace($ConfigCaptcha['captcha_skip_characters'], '', $raw_code);
					$this->code = substr( $valid_code, 0, $ConfigCaptcha['captcha_num_chars'] );
				} else {
					$this->code = substr( $raw_code, 0, $ConfigCaptcha['captcha_num_chars'] );
				}
				if (!$ConfigCaptcha['captcha_casesensitive']) {
					$this->code = strtoupper( $this->code );
				}
			}
		}
	}
	
	function readCodeFromFile() {
		$fp = @fopen($this->wordlist_file, 'rb');
		if (!$fp) return false;
		
		$fsize = filesize($this->wordlist_file);
		if ($fsize < 32) return false; // too small of a list to be effective
		
		if ($fsize < 128) {
		  $max = $fsize; // still pretty small but changes the range of seeking
		} else {
		  $max = 128;
		}
		
		fseek($fp, rand(0, $fsize - $max), SEEK_SET);
		$data = fread($fp, 128); // read a random 128 bytes from file
		fclose($fp);
		$data = preg_replace("/\r?\n/", "\n", $data);
		
		$start = strpos($data, "\n", rand(0, 100)) + 1; // random start position
		$end   = strpos($data, "\n", $start);           // find end of word
		
		return strtolower(substr($data, $start, $end - $start)); // return substring in 128 bytes
	}


  /**
   * Sets the Captcha code
   */
	function setCode() {
		if($this->invalid) {
			return;
		}
		
		$_SESSION['Captcha_sessioncode'] = strval( $this->code );
		$maxAttempts = intval( @$_SESSION['Captcha_maxattempts'] );
		
		// Increase the attempt records on refresh
		if(!empty($maxAttempts)) {
			$_SESSION['Captcha_attempt_'.$_SESSION['Captcha_name']]++;
			if($_SESSION['Captcha_attempt_'.$_SESSION['Captcha_name']] > $maxAttempts) {
				$this->invalid = true;
			}
		}
	}




	/**
	 * Creates the Captcha Image File
   * @param   string $file filename of the Captcha image
   * @return  object  The created image @todo is this an object?
	 */
	function createImage($file = '') {
		/*
		if($this->invalid) {
			header('Content-type: image/gif');
			readfile(_ROOT_PATH.'/images/subject/icon2.gif');
			return;
		}
		*/
		
		if($this->mode == 'bmp') {
			return $this->createImageBmp();
		}else{
			return $this->createImageGd();
		}
	}

	function createImageGd($file = '') {
		$this->loadFont();
		$this->setImageSize();
		
		$this->oImage = imagecreatetruecolor($this->width, $this->height);
		$background = imagecolorallocate($this->oImage, 255, 255, 255);
		imagefilledrectangle($this->oImage, 0, 0, $this->width, $this->height, $background);

		global $ConfigCaptcha;
		switch ($ConfigCaptcha['captcha_background_type']) {
			default:
			case 0:
			$this->drawBars();
			break;

			case 1:
			$this->drawCircles();
			break;

			case 2:
			$this->drawLines();
			break;

			case 3:
			$this->drawRectangles();
			break;

			case 4:
			$this->drawEllipses();
			break;

			case 5:
			$this->drawPolygons();
			break;

			case 100:
			$this->createFromFile();
			break;
		}
		$this->drawBorder();
		$this->drawCode();

		if (empty($file)) {
			header('Content-type: image/jpeg');
			imagejpeg($this->oImage);
		} else {
			imagejpeg($this->oImage, FTP_DIR_ROOT. '/files/temp/'. $file . '.jpg');
		}
		imagedestroy($this->oImage);
	}


	function _getList($name, $extension = '') {
		$items = array();

		$file_path = $this->dir.$name;
		$dh = opendir($file_path);
		while ($item = readdir($dh)) {
			if ($item=='..' || $item=='.' || $item=='.htaccess') continue;
			if (empty($extension) || preg_match("/(\.{$extension})$/i",$item) ) {
				$items[] = $item;
			}
		}
		return $items;
	}





	/**
	 * Loads the Captcha font
	 */
	function loadFont() 
	{
		$this->font = $this->dir.'fonts/MyriadPro-Semibold.ttf';

		/*
		return;
		$fonts = $this->_getList('fonts', 'ttf');
		$this->font = $this->dir.'fonts/'.$fonts[array_rand($fonts)];
		*/
	}



	/**
	 * Sets the Captcha image size
	 */
	function setImageSize() {
		$MaxCharWidth = 0;
		$MaxCharHeight = 0;
		$oImage = imagecreatetruecolor(100, 100);
		$text_color = imagecolorallocate($oImage, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
		global $ConfigCaptcha;
		$FontSize = $ConfigCaptcha['captcha_fontsize_max'];

		for ($Angle = -30; $Angle <= 30; $Angle++) {
			for ($i = 65; $i <= 90; $i++) {
				$CharDetails = imageftbbox($FontSize, $Angle, $this->font, chr($i), array());
				$_MaxCharWidth  = abs($CharDetails[0] + $CharDetails[2]);
				if ($_MaxCharWidth > $MaxCharWidth ) {
					$MaxCharWidth = $_MaxCharWidth;
				}
				$_MaxCharHeight  = abs($CharDetails[1] + $CharDetails[5]);
				if ($_MaxCharHeight > $MaxCharHeight ) {
					$MaxCharHeight = $_MaxCharHeight;
				}
			}
		}
		imagedestroy($oImage);
		
		$this->height = $MaxCharHeight + 2;
		$this->spacing = (int)( ($ConfigCaptcha['captcha_num_chars'] * $MaxCharWidth) / $ConfigCaptcha['captcha_num_chars']);
		$this->width = ($ConfigCaptcha['captcha_num_chars'] * $MaxCharWidth) + ($this->spacing/2);
	}



	/**
	* Returns random background
	*
	* @return array Random Background
	*/
	function loadBackground() {
		$RandBackground = null;
		if ($backgrounds = $this->_getList('backgrounds', '(gif|jpg|png)') ) {
			$RandBackground = $this->dir.'backgrounds/'.$backgrounds[array_rand($backgrounds)];
		}
		return $RandBackground;
	}




	/**
	* Draws Image background
	*/
	function createFromFile() 
	{
		if ( $RandImage = $this->loadBackground() ) {
			$ImageType = @getimagesize($RandImage);
			switch ( @$ImageType[2] ) {
				case 1:
				$BackgroundImage = imagecreatefromgif($RandImage);
				break;

				case 2:
				$BackgroundImage = imagecreatefromjpeg($RandImage);
				break;

				case 3:
				$BackgroundImage = imagecreatefrompng($RandImage);
				break;
			}
		}
		if(!empty($BackgroundImage)){
			imagecopyresized($this->oImage, $BackgroundImage, 0, 0, 0, 0, imagesx($this->oImage), imagesy($this->oImage), imagesx($BackgroundImage), imagesy($BackgroundImage));
			imagedestroy($BackgroundImage);
		} else {
			$this->drawBars();
		}
	}




	/**
	* Draw Captcha Code
	*/
	function drawCode() 
	{
		global $ConfigCaptcha;

		for ($i = 0; $i < $ConfigCaptcha['captcha_num_chars'] ; $i++) {
			// select random greyscale colour
			$text_color = imagecolorallocate($this->oImage, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));

			// write text to image
			$Angle = mt_rand(10, 30);
			if (($i % 2)) {
				$Angle = mt_rand(-30, -10);
			}

			// select random font size
			$FontSize = mt_rand($ConfigCaptcha['captcha_fontsize_min'], $ConfigCaptcha['captcha_fontsize_max']);

			$CharDetails = imageftbbox($FontSize, $Angle, $this->font, $this->code[$i], array());
			$CharHeight = abs( $CharDetails[1] + $CharDetails[5] );

			// calculate character starting coordinates
			$posX = ($this->spacing/2) + ($i * $this->spacing);
			$posY = 2 + ($this->height / 2) + ($CharHeight / 4);

			imagefttext($this->oImage, $FontSize, $Angle, $posX, $posY, $text_color, $this->font, $this->code[$i], array());
		}
	}




	/**
	* Draw Captcha Border
	*/
	function drawBorder() 
	{
		$rgb = rand(50, 150);
		$border_color = imagecolorallocate ($this->oImage, $rgb, $rgb, $rgb);
		imagerectangle($this->oImage, 0, 0, $this->width-1, $this->height-1, $border_color);
	}




	/**
	* Draw Captcha Circles background
	*/
	function drawCircles() 
	{
		global $ConfigCaptcha;
		for($i = 1; $i <= $ConfigCaptcha['captcha_background_num']; $i++){
			$randomcolor = imagecolorallocate ($this->oImage , mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			imagefilledellipse($this->oImage, mt_rand(0,$this->width-10), mt_rand(0,$this->height-3), mt_rand(10,20), mt_rand(20,30),$randomcolor);
		}
	}



	/**
	* Draw Captcha Lines background
	*/
	function drawLines() 
	{
		global $ConfigCaptcha;
		for ($i = 0; $i < $ConfigCaptcha['captcha_background_num']; $i++) {
			$randomcolor = imagecolorallocate($this->oImage, mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			imageline($this->oImage, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $randomcolor);
		}
	}



	/**
	* Draw Captcha Rectangles background
	*/
	function drawRectangles() 
	{
		global $ConfigCaptcha;
		for ($i = 1; $i <= $ConfigCaptcha['captcha_background_num']; $i++) {
			$randomcolor = imagecolorallocate ($this->oImage , mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			imagefilledrectangle($this->oImage, mt_rand(0,$this->width), mt_rand(0,$this->height), mt_rand(0, $this->width), mt_rand(0,$this->height),  $randomcolor);
		}
	}




	/**
	* Draw Captcha Bars background
	*/
	function drawBars() 
	{
		for ($i= 0 ; $i <= $this->height;) {
			$randomcolor = imagecolorallocate ($this->oImage , mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			imageline( $this->oImage, 0, $i, $this->width, $i, $randomcolor );
			$i = $i + 2.5;
		}
		for ($i = 0;$i <= $this->width;) {
			$randomcolor = imagecolorallocate ($this->oImage , mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			imageline( $this->oImage, $i, 0, $i, $this->height, $randomcolor );
			$i = $i + 2.5;
		}
	}




	/**
	* Draw Captcha Ellipses background
	*/
	function drawEllipses() 
	{
		global $ConfigCaptcha;
		for($i = 1; $i <= $ConfigCaptcha['captcha_background_num']; $i++){
			$randomcolor = imagecolorallocate ($this->oImage , mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			imageellipse($this->oImage, mt_rand(0,$this->width), mt_rand(0,$this->height), mt_rand(0,$this->width), mt_rand(0,$this->height), $randomcolor);
		}
	}





	/**
	* Draw Captcha polygons background
	*/
	function drawPolygons() 
	{
		global $ConfigCaptcha;
		for($i = 1; $i <= $ConfigCaptcha['captcha_background_num']; $i++){
			$randomcolor = imagecolorallocate ($this->oImage , mt_rand(190,255), mt_rand(190,255), mt_rand(190,255));
			$coords = array();
			for ($j=1; $j <= $ConfigCaptcha['captcha_polygon_point']; $j++) {
				$coords[] = mt_rand(0,$this->width);
				$coords[] = mt_rand(0,$this->height);
			}
			imagefilledpolygon($this->oImage, $coords, $ConfigCaptcha['captcha_polygon_point'], $randomcolor);
		}
	}

	function createImageBmp($file = '') 
	{
		$image = '';
			
		if(empty($file)) {
			header('Content-type: image/bmp');
			echo $image;
		}else{
			return $image;
		}
	}
}
