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
* @file       inc/Install.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class InstallAjaxel {
	
	private static $_instance = false;
	private $db = false;
	
	private function __construct() {
		session_start();
		require FTP_DIR_ROOT.'config/defines.php';
		require FTP_DIR_ROOT.'inc/DB.php';
		error_reporting(E_ALL ^ E_NOTICE);
		$request_uri = str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']));
		if ($request_uri=='/index.php' || substr($request_uri,0,2)=='/?' || substr($request_uri,0,1)=='?') $request_uri = '';
		define ('HTTP_URL', 'http://'.trim($_SERVER['HTTP_HOST'].'/'.trim($request_uri,'/').'/','/').'/');
		define ('HTTP_EXT', '/'.join('/',array_slice(explode('/',HTTP_URL),3)));
		if (!defined('FTP_EXT')) define ('FTP_EXT', HTTP_EXT);
		define ('LEVEL', count(explode('/',HTTP_EXT))-2);
		define ('FTP_DIR_TPLS',FTP_DIR_ROOT.'tpls/');
		define ('DIR_FILES','files/');
		Session()->UserID = 1;
		Site::$exit = true;
	}
	
	public function __destruct() {
		Cache::storeSmall();	
	}
	
	private function presets() {
		if (!defined('DIR_FILES')) define ('DIR_FILES','files/');
		$this->checkWritable(FTP_DIR_ROOT.'config/');
		$this->checkWritable(FTP_DIR_ROOT.'config/lang/');
		$this->checkWritable(FTP_DIR_ROOT.DIR_FILES);
		$this->checkWritable(FTP_DIR_ROOT.DIR_FILES.'editor/');
		$this->checkWritable(FTP_DIR_ROOT.DIR_FILES.'temp/');
		$this->checkWritable(FTP_DIR_ROOT.DIR_FILES.'users/');
		$this->checkWritable(FTP_DIR_ROOT.'tpls/');
		
		$this->preset = array();
		$this->preset['languages'] = array(
			'en'	=> 'English',
			'ru'	=> 'Russian'
		);
		$this->preset['templates'] = array();

		if (!is_dir($f = FTP_DIR_ROOT.'files/temp/')) mkdir($f);
		if (!is_dir($f = FTP_DIR_ROOT.'files/user/')) mkdir($f);
		if (!is_dir($f = FTP_DIR_ROOT.'files/users/')) mkdir($f);
		if (!is_dir($f = FTP_DIR_ROOT.'files/editor/')) mkdir($f);
		
		$dir = FTP_DIR_ROOT.'tpls/';
		$skip = array('css','js','admin','email','img');
		$backup_ext = array('gz','sql','zip');
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if ($file=='.' || $file=='..' || $file=='default' || !is_dir($dir.$file) || substr($file,0,1)=='_') continue;
			if (preg_match('/[^a-zA-Z0-9]/',$file)) continue;
			if (in_array($file,$skip)) continue;
			if (!is_dir($dir.$file.'/backup/')) continue;
			$_dh = opendir($dir.$file.'/backup/');
			$backups = array();
			while ($_file = readdir($_dh)) {
				if ($_file=='.' || $_file=='..' || !is_file($dir.$file.'/backup/'.$_file)) continue;
				$ext = ext($_file);
				if (in_array($ext, $backup_ext)) {
					$backups[filemtime($dir.$file.'/backup/'.$_file)] = array($dir.$file.'/backup/',$_file);
				}
			}
			closedir($_dh);
			krsort($backups);
			if ($backups) {
				foreach ($backups as $backup) break;
				$this->preset['templates'][$file] = array($file.' ('.$backup[1].')', $backup);
			}
		}
		closedir($dh);
		
		$this->preset['templates']['default'] = array('Default (clean)',false);
	}
	
	
	private static function checkWritable($file) {
		if (!file_exists($file)) return false;
		if (!is_writable($file)) {
			if (is_file($file)) {
				@chmod($file,0777);
				if (!is_writable(FTP_DIR_ROOT.'config/')) {	
					Message::Halt('File write permissions error','Please set the permissions for <em>'.$file.'</em> to 0777');
				}
			} else {
				@chmod($file,0755);
				if (!is_writable($file)) {	
					Message::Halt('Folder write permissions error','Please set the permissions for <em>'.$file.'</em> to 0755 or 0777');
				}	
			}
		}	
	}
	
	public static function getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self;	
		}
		return self::$_instance;
	}
	
	private function tpl($name) {
		require FTP_DIR_ROOT.'tpls/admin/'.$name.'.php';	
	}
	
	private function printTpl() {
		$_POST['data']['http_base'] = HTTP_URL;
		$_POST['data']['prefix'] = 'default_';
		$_POST['data']['login'] = 'admin';
		$_POST['data']['email'] = 'admin@'.$_SERVER['HTTP_HOST'];
		$_POST['data']['db_host'] = ($_SERVER['REMOTE_ADDR']=='127.0.0.1' ? '127.0.0.1' : 'localhost');
		$_POST['data']['db_user'] = 'root';
		$_POST['data']['db_name'] = str_replace('.','_',$_SERVER['HTTP_HOST']);
		$this->tpl('default/_install');
	}
	
	public function init() {
		
		if (get('templates')=='true') {
			header('Content-Type: text/json; charset=utf-8');
			$preview = FTP_DIR_ROOT.'tpls/'.$_POST['template'].'/preview_th.jpg';
			$description = false;
			if (is_file(FTP_DIR_ROOT.'tpls/'.$_POST['template'].'/description.txt')) {
				$description = file_get_contents(FTP_DIR_ROOT.'tpls/'.$_POST['template'].'/description.txt');
			}
			if (is_file($preview)) {
				$ret = array('ok'=>true,'preview'=>'tpls/'.$_POST['template'].'/preview.jpg','preview_th'=>'tpls/'.$_POST['template'].'/preview_th.jpg','template'=>$_POST['template'],'description'=>$description);
			}
			elseif (is_dir(FTP_DIR_ROOT.'tpls/'.$_POST['template'])) {
				$ret = array('ok'=>true,'template'=>$_POST['template'],'preview'=>false,'description'=>$description);
			} else {
				$ret = array('ok'=>false,'fail'=>FTP_DIR_ROOT.'tpls/'.$_POST['template'].'/');	
			}
			echo json_encode($ret);
			exit;	
		}
		
		
		header('Content-Type: text/html; charset=utf-8');
		
		if (get('db_restore')) {
			$this->writeFromSqlContinue();
		}
		elseif (get('install_end')) {
			$this->installEnd(true);
		}		
		$this->presets();
		
		if (post('install_submitted')) {
			$this->ret['errors'] = array();
			$this->data = post('data');

			if (!$this->data['http_base']) {
				$this->ret['errors']['http_base'] = 'Site URL is empty';
			}
			define ('HTTP_BASE',trim($this->data['http_base'],'/').'/');
			if (!$this->data['language'] || !array_key_exists($this->data['language'],$this->preset['languages'])) {
				$this->ret['errors']['language'] = 'Language is empty';	
			}
			if (!$this->data['template'] || !array_key_exists($this->data['template'],$this->preset['templates'])) {
				$this->ret['errors']['template'] = 'Please select the template';	
			} else {
				$this->data['prefix'] = $this->data['template'];
				$this->data['backup'] = $this->preset['templates'][$this->data['template']][1];
			}
			if ($this->data['prefix']) {
				define ('PREFIX',trim($this->data['prefix'],'_').'_');
			}
			if ($this->data['template']=='default' || $this->data['template']=='sample') {
				if (!$this->data['login']) {
					$this->ret['errors']['login'] = 'Administrator\'s login is empty';	
				}
				elseif (!Parser::isAlphaNumeric($this->data['login'], false, true)) {
					$this->ret['errors']['login'] = 'Incorrect login name';
				}
				if (!$this->data['password']) {
					$this->ret['errors']['password'] = 'Administrator\'s password is empty';
				}
			}
			
			if (!$this->data['db_host']) {
				$this->ret['errors']['db_host'] = 'Database host is empty';	
			}
			if ($this->data['db_prefix']) {
				if (!Parser::isAlphaNumeric($this->data['db_prefix'], false, true)) {
					$this->ret['errors']['db_prefix'] = 'Incorrect database prefix';
				}
			}
			define ('DB_PREFIX',($this->data['db_prefix']?trim($this->data['db_prefix'],'_').'_':''));
			if (!$this->data['db_user']) {
				$this->ret['errors']['db_user'] = 'Database username is empty';	
			}
			if (!$this->data['db_name']) {
				$this->ret['errors']['db_name'] = 'Database name is empty';	
			}
			elseif (!Parser::isAlphaNumeric($this->data['db_name'], false, true)) {
				$this->ret['errors']['db_name'] = 'Database name must contain alpha-numeric characters';
			} else {
				define ('DB_NAME',$this->data['db_name']);
			}
			if ($this->data['db_host'] && $this->data['db_user']) {
				$this->db = @new mysqli($this->data['db_host'],$this->data['db_user'],$this->data['db_pass']);
				if ($this->db->connect_error) {
					$this->ret['errors']['db_pass'] = 'Cannot connect to mysqli: '.$this->db->connect_error.'';	
				} else {
					$connected = true;
					if (!$this->ret['errors']) {
						if (!$this->db->select_db($this->data['db_name'])) {
							$this->db->query('CREATE DATABASE `'.$this->data['db_name'].'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
							if (!$this->db->select_db($this->data['db_name'])) {
								$this->ret['errors']['db_name'] = 'Cannot create database: <em>'.$this->data['db_name'].'</em> ('.mysql_error().')';
							}
						} else {
							$this->db->query('SET NAMES \'utf8\'');	
						}
					}
				}
			}
			
			$_SESSION['DB::columns'] = array();
			
			if ($this->ret['errors']) {
				echo json_encode($this->ret);	
				exit;
			} else {
				$this->ret['errors'] = false;
				if (PREFIX=='default_') {
					$this->installTables();
					$this->writeDefault();
				} else {
					Session()->UserID = 1;
					$this->writeFromSql();
				}
				$this->installEnd();	
			}
			exit;
		} else {
			$this->printTpl();
		}
	}
	
	
	private function initLoop() {
		$this->data = Cache::getSmall('install');
		define ('SITE_TYPE','json');
		define ('HTTP_BASE',trim($this->data['http_base'],'/').'/');
		define ('DB_PREFIX',($this->data['db_prefix']?trim($this->data['db_prefix'],'_').'_':''));
		define ('PREFIX',$this->data['prefix']);
		$this->db = new mysqli($this->data['db_host'],$this->data['db_user'],$this->data['db_pass'],$this->data['db_name']);
		$this->db->query('SET NAMES \'utf8\'');
		DB::link($this->db);
	}
	
	private function writeFromSqlContinue() {
		$this->initLoop();
		$ret = Factory::call('dbfunc')->restore();
		if ($ret['done']) $this->writeUser();
		echo json_encode($ret);
		exit;
	}
	
	private function writeFromSql() {
		define ('SITE_TYPE','json');
		Cache::saveSmall('install',$this->data);
		@mkdir(FTP_DIR_ROOT.'files/'.$this->data['template'],0777);
		DB::link($this->db);
		$db = Factory::call('dbfunc');
		$db->prefix = PREFIX;
		$db->db_name = $this->data['db_name'];
		$db->filename = $this->data['backup'][1];
		$db->ext = ext($this->data['backup'][1]);
		$db->template = $this->data['template'];
		echo json_encode($db->restore());
		exit;
	}
	
	
	private function installEnd($restore = false) {
		if ($restore) $this->initLoop();
		$this->writeUser();
		$this->writeConfig();
		$this->writeHtaccess();
		$this->ret['message'] = '<b style="font-family:\'Times New Roman\', Times, serif;font-size:20px;color:#009933">Ajaxel CMS is successfuly installed on your server!</b>';
		$this->ret['message'] .= '<br /><br /><span style="font-size:15px;color:#000">Please <a href="'.HTTP_EXT.'" style="color:#000;font:15px Arial">refresh</a> the page now or press <a style="color:#000;font:15px Arial" href="'.HTTP_EXT.'?'.URL_KEY_ADMIN.'">CTRL+F12</a> to login to admin panel.</span>';
		$this->ret['message'] .= '<br /><br /><span style="color:#000;font-size:12px">To re-install system in future just rename or remove '.FTP_DIR_ROOT.'config/config.php file from server.<br />For more support contact to <a href="mailto:ajaxel.com@gmail.com" style="color:#000">ajaxel.com@gmail.com</a></span>';
		$this->ret['success'] = true;
		echo json_encode($this->ret);
		exit;
	}
	

	private function writeConfig() {
		$rn = "\n";
		$rnt = "\n\t";
		$rntt = "\n\t\t";
		$file = '<?php'.$rn.$rn.'/**'.$rn.'* Database configuration'.$rn.'* for Ajaxel CMS v'.Site::VERSION.''.$rn.'* Author: Alexander Shatalov <ajaxel.com@gmail.com>'.$rn.'* http://ajaxel.com'.$rn.''.$rn.'* Installed on '.date('H:i d.m.Y').''.$rn.'*/'.$rn.''.$rn.''.$rn.'switch ($_SERVER[\'HTTP_HOST\']) {'.$rnt.'case \''.$_SERVER['HTTP_HOST'].'\':'.$rnt.'default:'.$rntt.'define (\'DB_HOST\', \''.$this->data['db_host'].'\');'.$rntt.'define (\'DB_USERNAME\', \''.$this->data['db_user'].'\');'.$rntt.'define (\'DB_PASSWORD\', \''.$this->data['db_pass'].'\');'.$rntt.'define (\'DB_NAME\', \''.$this->data['db_name'].'\');'.$rntt.'define (\'DB_SOCKET\', \'\');'.$rntt.'define (\'DB_PORT\', false);'.$rntt.'define (\'DB_PREFIX\', \''.DB_PREFIX.'\');'.$rntt.'define (\'HTTP_BASE\', \''.HTTP_BASE.'\');'.$rntt.'define (\'DEFAULT_TEMPLATE\', \''.$this->data['template'].'\');'.$rnt.'break;'.$rn.'}'.$rn;
		return file_put_contents(FTP_DIR_ROOT.'config/config.php', $file);	
	}
	
	
	private function writeHtaccess() {
		if (is_file(FTP_DIR_ROOT.'.htaccess')) return false;
		$file = 'AddDefaultCharset utf-8

<FilesMatch "\.(ttf|otf|woff)$">
 <IfModule mod_headers.c>
  Header set Access-Control-Allow-Origin "*"
 </IfModule>
</FilesMatch>

# Uncomment to allow cross-domain ajax requests
#<IfModule mod_headers.c>
#  Header set Access-Control-Allow-Origin "*"
#</IfModule>

# Set the default handler.
DirectoryIndex index.php

<ifModule mod_gzip.c>
 mod_gzip_on Yes
 mod_gzip_dechunk Yes
 mod_gzip_item_include file .(html?|txt|css|js|php)$
 mod_gzip_item_include handler ^cgi-script$
 mod_gzip_item_include mime ^text/.*
 mod_gzip_item_include mime ^application/x-javascript.*
 mod_gzip_item_exclude mime ^image/.*
 mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*
</ifModule>

# Various rewrite rules.
<IfModule mod_rewrite.c>
 RewriteEngine on

 RewriteCond %{REQUEST_FILENAME} !-f
 RewriteCond %{REQUEST_FILENAME} !-d
 RewriteCond %{REQUEST_URI} !=/favicon.ico
 RewriteCond %{REQUEST_URI} !=/robots.txt
 RewriteBase /
 RewriteRule .* index.php [L,QSA]
</IfModule>';	
		@file_put_contents(FTP_DIR_ROOT.'.htaccess',$file);
	}

	private function writeDefault() {
		$sql = array();
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'PREFIX\', \''.($this->data['template']=='default'?PREFIX:'default_').'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'HTTP_BASE\', \''.HTTP_BASE.'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'MAIL_NAME\', \''.$_SERVER['HTTP_HOST'].'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'MAIL_EMAIL\', \''.$this->db->real_escape_string($this->data['email']).'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'DEFAULT_LANGUAGE\', \''.$this->data['language'].'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'DEFAULT_CURRENCY\', \'EUR\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'KEEP_LANG_URI\', \'1\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'KEEP_TPL_URI\', \'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'HTACCESS_WRITE\', \'1\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'NO_USER_REGISTER\', \'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'USER_EMAIL_CONFIRM\', \'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'USE_IM\', \'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'MAIL_WEBMASTER\', \''.$this->db->real_escape_string($this->data['email']).'\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'UI_ADMIN\', \'selene\')';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'site_notes\', \'System was installed on '.date('H:i d M Y').', from '.$_SERVER['HTTP_HOST'].' with IP: '.Session::getIP().'\')';
	
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'currencies\', \'[[:ARRAY:]]'.$this->db->real_escape_string(serialize(array(
			'EUR' => array(1, 'Euro','€ %1', 1)
		))).'\')';
		
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\'default\', \'languages\', \'[[:ARRAY:]]'.$this->db->real_escape_string(serialize(array(
			'en' => array('English','English','ENG')
		))).'\');';
		
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]templates` VALUES (\'default\', \'\', \'\', \'\', \'My Site\', 1, \'\', '.time().', 0);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]vars` VALUES (\'default\', \'site_title_separator\', \' | \');';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]vars` VALUES (\'default\', \'site_title_preparator\', \' :: \');';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]vars` VALUES (\'default\', \'site_title\', \'My new website!\');';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]vars` VALUES (\'default\', \'site_title_short\', \'New website\');';
		if (is_file(FTP_DIR_ROOT.'config/system/countries.php')) {
			$countries = include(FTP_DIR_ROOT.'config/system/countries.php');
			foreach ($countries as $code => $name) {
				$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]countries` VALUES (\''.$this->db->real_escape_string($code).'\', \''.$this->db->real_escape_string($name).'\')';
			}
			unset($countries);
		}
		$time = time();
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'article\', \'content\', \'Article\', \'\', \'apps/accessories-text-editor\', 1, \'\', 1, '.$time.', 1);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'gallery\', \'content\', \'Gallery\', \'\', \'actions/fileview-preview\', 1, \'\', 1, '.$time.', 2);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'banner\', \'content\', \'Banner\', \'\', \'actions/get-hot-new-stuff\', 1, \'\', 1, '.$time.', 4);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'product\', \'content\', \'Product\', \'\', \'apps/basket\', 1, \'\', 1, '.$time.', 4);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'html\', \'content\', \'HTML code\', \'\', \'apps/preferences-plugin-script\', 1, \'\', 1, '.$time.', 5);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'links\', \'grid\', \'Links\', \'\', \'places/network-wired\', 1, \'\', 1, '.$time.', 6);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'articles\', \'grid\', \'Articles\', \'\', \'actions/kdeprint-testprinter\', 1, \'\', 1, '.$time.', 7);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'entries\', \'category\', \'Entries categories\', \'\', \'places/folder-red\', 1, \'\', 1, '.$time.', 8);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'product\', \'category\', \'Product categories\', \'\', \'places/folder-blue\', 1, \'\', 1, '.$time.', 9);';
		$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'gallery\', \'category\', \'Gallery categories\', \'\', \'places/folder-violet\', 1, \'\', 1, '.$time.', 10);';
		
		
		
		foreach ($sql as $s) {
			$s = $this->prefix($s);
			if (!$this->db->query($s)) {
				d($s."\r\n>>> ".$this->db->error);
			}
		}		
	}
	
	
	private function writeUser() {
		if (!$this->data['login'] || !$this->data['password']) return false;
		$qry = $this->db->query('SELECT 1 FROM '.DB_PREFIX.'users WHERE login LIKE \''.$this->db->real_escape_string($this->data['login']).'\'');
		Factory::call('user');
		if ($qry && $qry->num_rows) {
			$this->db->query('UPDATE '.DB_PREFIX.'users SET password=\''.User::password($this->data['password'],$this->data['login']).'\', registered='.time().', logged='.time().', ip=\''.ip2long($_SERVER['REMOTE_ADDR']).'\', groupid='.ADMIN_GROUP.', email=\''.$this->data['email'].'\' WHERE login LIKE \''.$this->db->real_escape_string($this->data['login']).'\''); 
		} else {
			$this->db->query('INSERT INTO `'.DB_PREFIX.'users` (login, groupid, classid, email, password, registered, code, active) VALUES (\''.$this->data['login'].'\', '.ADMIN_GROUP.', 6, \''.$this->data['email'].'\', \''.User::password($this->data['password'],$this->data['login']).'\', '.time().', \''.md5(time().rand(0,555)).'\', 1)');
			$this->db->query('INSERT INTO '.DB_PREFIX.'users_profile (setid, firstname) VALUES ('.$this->db->insert_id.', \''.$this->db->real_escape_string($this->data['login']).'\')');
		}
	}
	
	private function installTables($prefix = false) {
		$sql = require FTP_DIR_ROOT.'config/system/tables.php';
		
		$db_prefix = '[[:DB_PREFIX:]]';
		$_prefix = '[[:PREFIX:]]';
		
		foreach ($sql as $s) {			
			if ($prefix) {
				if (!strpos($s,$_prefix)) continue;
				$s = str_replace($_prefix,$prefix,str_replace($db_prefix,DB_PREFIX,$s));
				
			} else {
				$s = $this->prefix($s);
			}
			if (!$this->db->query($s)) {
				d($s."\r\n>>> ".$this->db->error);	
			}
		}
	}
	private function prefix($sql) {
		$db_prefix = '[[:DB_PREFIX:]]';
		$_prefix = '[[:PREFIX:]]';
		return str_replace($_prefix,PREFIX,str_replace($db_prefix,DB_PREFIX,$sql));	
	}
}