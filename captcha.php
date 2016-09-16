<?php

session_start();
session_name('PHPSESSID');
error_reporting(0);

define ('FTP_DIR_ROOT', './');
$ConfigCaptcha = array(
	'captcha_num_chars'	=> 7,
	'captcha_skip_characters'=> array(),
	'captcha_casesensitive'	=> false,
	'captcha_background_type'=> rand(1,5),
	'captcha_background_num'=> rand(10,25),
	'captcha_polygon_point'	=> 4,
	'captcha_fontsize_min'	=> 15,
	'captcha_fontsize_max'	=> 16,
	'captcha_word'			=> 1
	
);

$_SESSION['Captcha_name'] = ($_GET['name']?$_GET['name']:'captcha');

require FTP_DIR_ROOT.'inc/lib/Image/Captcha.php';

$image_handler = new CaptchaImageHandler();
$image_handler->loadImage();
