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
* @file       index.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

define ('DEBUG', false);
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

//error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
//error_reporting(E_ALL);
/*
ini_set('magic_quotes_runtime', 0);
ini_set('log_errors', 1);
ini_set('iconv.internal_encoding', 'utf-8');
*/

/**
* Allow access control for another domain
*
*/
// header('Access-Control-Allow-Origin: *');


/**
* If you want to place this index.php file to be connected to domain.com folder on same hosting:
*
define ('FTP_DIR_ROOT','/home/domains/domain.com/');
define ('FTP_EXT','http://domain.com/');
define ('HTTP_EXT','/');
*/

define ('FTP_DIR_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__)).'/'));
//if (!extension_loaded('IonCube Loader')) require FTP_DIR_ROOT.'config/system/ioncube_error.php';
define ('MEMORY',memory_get_usage(true));
require FTP_DIR_ROOT.'inc/Site.php';
Site::getInstance()->Init();