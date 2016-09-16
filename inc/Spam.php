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
* @file       inc/Spam.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Spam {
	
	public
		$comment = '',
		$email = '',
		$www = '',
		$name = '',
		$subject = '',
		$bb_allowed = true,
		$captcha = '',
		$check_code = '',
		$text = ''
	;
	
	private $spam = array(), $spam_db = '', $env = array(), $is = '';
	
	public function __construct($spam_db = false) {
		$this->spam_db = $spam_db;
		if (!$this->spam_db || !is_file($this->spam_db)) $this->spam_db = FTP_DIR_ROOT.'config/system/spam_db.php';
		if (!defined('SECRET_SALT')) define('SECRET_SALT','#$^W4eg^@sdhh%%');
	}
	
	public function getCode() {
		$this->rand = $this->random_key();
		$this->check_code = md5(SECRET_SALT.$this->rand.'|'.session_id()).'|'.$this->rand;
		$_SESSION['SPAM_CHECK_CODE'] = $this->check_code;
		return $this->check_code;
	}
	
	public function checkKey() {
		if (!$this->check_code && isset($_SESSION['SPAM_CHECK_CODE'])) {
			$this->check_code = $_SESSION['SPAM_CHECK_CODE'];
		}
		if (!$this->check_code) {
			$this->is = 'key_not_initialized';
			$this->text = 'Attempt to re-submit same comment';
			return true;	
		}
		$ex = explode('|',$this->check_code);
		if (count($ex)!=2) {
			$this->text = 'Bad error with key validation';
			$this->is = 'key_wrong_initialized';
			return true;
		}
		if (md5(SECRET_SALT.$ex[1].'|'.session_id())!==$ex[0]) {
			$this->text = 'Key cannot be validated';
			$this->is = 'key';
			return true;
		}
	}
	
	public function is() {
		require $this->spam_db;
		$this->spam =& $spam;
		$this->checkValid();
		if (!$this->is) $this->checkKey();
	//	if (!$this->is) $this->checkCaptcha();
		if (!$this->is) $this->checkEnv();
		if (!$this->is) $this->checkSubject();
		if (!$this->is) $this->checkName();
		if (!$this->is) $this->checkEmail();
		if (!$this->is) $this->checkWww();
		if (!$this->is) $this->checkComment();
		if (!$this->is) $this->checkSpamIp();
		return $this->is;
	}
	
	private function checkSpamIp() {
		return;
		$url = 'http://ajaxel.com/spamip.php';
		$postdata = array(
			'comment'	=> $this->comment,
			'subject'	=> $this->subject,
			'email'		=> $this->email,
			'ip'		=> self::getIP()
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_URL, $url);
		if (is_array($postdata)) $postdata = http_build_query($postdata);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, '&'.ltrim($postdata,'&'));
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data && strstr($data,'SPAM')) {
			$this->text = 'Your IP address is blacklisted';
			$this->is = 'spam_ip';	
		}
	}
	
	public static function getHost($ip) {
		$u = 'unknown';
		if (!$ip) return $u;
		$ret = trim(strtolower(@gethostbyaddr($ip)));
		return $ret;
	}
	private static function getFirstIpFromList($ip) {
		$p = strpos($ip, ',');
		if($p!==false) return substr($ip, 0, $p); else return $ip;
	}

	public static function getIP() {
		if (isset($_SESSION['IP'])) return $_SESSION['IP'];
		if(isset($_SERVER['HTTP_CLIENT_IP']) && ($ip = self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP'])) && strpos($ip, 'unknown')===false && self::getHost($ip) != $ip) $ret = $ip;
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($ip) && !empty($ip) && strpos($ip, 'unknown')===false && getHost($ip) != $ip) $ret = $ip;
		elseif(isset($_SERVER['HTTP_CLIENT_IP']) && strlen(self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']))!=0) $ret = self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']);
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen(self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']))!=0) $ret = self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']);
		else $ret = self::getFirstIpFromList($_SERVER['REMOTE_ADDR']);
		$ret = $ret?$ret:'127.0.0.1';
		$_SESSION['IP'] = $ret;
		return $ret;
	}
	
	private function checkValid() {
		
	}
	
	private function checkCaptcha() {
		if (!$_SESSION['Captcha_sessioncode']) return;
		if (!$this->capctha) {
			$this->text = 'Captcha code is empty';
			$this->is = 'captcha_empty';	
		}
		elseif (strtolower(trim($this->captcha))!=strtolower(trim($_SESSION['Captcha_sessioncode']))) {
			$this->text = 'Captcha entered is not valid';
			$this->is = 'wrong_captcha';
		}
	}
	
	
	const LETTERS_ODD = 'AEIJOUYaeijouy';
	const LETTERS_EVEN = 'BSDFGHKLMNPQRSTVWXZbsdfghklmnpqrstvwxz';
	
	private function checkSubject() {
		if (!$this->subject) return;
		$ex = str_split($this->subject,1);
		$l = count($ex);
		$up = $lo = $odds = $evens = 0;
		$odd = str_split(self::LETTERS_ODD);
		$even = str_split(self::LETTERS_EVEN);
		
		foreach ($ex as $e) {
			if (!preg_match('/[A-Za-z]/',$e)) continue;
			/*
			if (in_array($e,$odd)) $odds++;
			if (in_array($e,$even)) $evens++;
			*/
			if (strtoupper($e)===$e) $up++;
			elseif (strtolower($e)===$e) $lo++;
		}
		/*
		d($evens);
		d($odds);
		if ($evens <= $odds / 2) {
			$this->text = 'Subject line is unusual phrase';
			$this->is = 'subject';
			return true;
		}
		*/
		$words = str_word_count($this->subject);

		if ($lo && $up > $words) {
			$this->text = 'Subject is a not a normal phrase';
			$this->is = 'subject';
			return true;
		}

		foreach ($this->spam['comment'] as $v) {
			if (substr_count($this->subject, $v)) {
				$this->is = 'subject';
				$this->text = 'Spam subject detected: '.$v.'';
				return true;	
			}
		}
	}
	
	private static function isURL($url, $absolute = true) {
		if (!strpos($url,'://')) $url = 'http://'.$url;
		$chars = '[a-z0-9\/:_\-_\.\?\$,;~=#&%\+]';
		if ($absolute) {
			return preg_match("/^(http|https|ftp):\/\/". $chars ."+$/i", $url)?$url:false;
		} else {
			return preg_match("/^". $chars ."+$/i", $url)?$url:false;
		}
	}
	private static function isEmail($email,$type = 'simple') {
		if (!is_bool($email) && !strstr($email,'@') && !strstr($email,'.')) return false;
		$email = trim($email);
		return preg_match('/^[\'_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.(([a-z]{2,3})|(aero|coop|info|museum|name))$/i',$email)?$email:false;
	}
	
	private function checkWww() {
		if (!$this->www) return;
		if (self::isEmail($this->www)) {
			$this->is = 'www';
			$this->text = 'URL looks like an email address';
			return true;
		}
		if (!self::isURL($this->www)) {
			$this->is = 'www';
			$this->text = 'Please check the URL you have entered';
			return true;
		}
	//	$this->www = preg_replace('[^A-Za-z0-9\.\/\?&=-#]/','',$this->www);
		foreach ($this->spam['comment'] as $v) {
			if (substr_count($this->www, $v)) {
				$this->is = 'www';
				$this->text = 'Spam URL detected:'.htmlspecialchars($v);
				return true;	
			}
		}
	}
	
	private function checkName() {
		if (!$this->name) return;
		foreach ($this->spam['comment'] as $v) {
			if (substr_count($this->name, $v)) {
				$this->is = 'name';
				$this->text = 'Spam name detected: '.$v.'';
				return true;	
			}
		}
	}
	
	private function checkEmail() {
		if (!$this->email) return;
		foreach ($this->spam['email'] as $v) {
			if ($this->email==$v) {
				$this->text = 'Unacceptable email address';
				$this->is = 'email';
				return true;	
			}
		}
		foreach ($this->spam['email_regex'] as $v) {
			if (strstr($this->email,$v)) {
				$this->text = 'Unacceptable email address';
				$this->is = 'email_regex';
				return true;	
			}
		}
	}
	
	private function checkComment() {
		if (!$this->comment) return;
		$c = strtolower($this->comment);
		foreach ($this->spam['comment'] as $v) {
			if (substr_count($c, $v)) {
				$this->text = 'Spam word detected: '.htmlspecialchars($v);
				$this->is = 'comment';
				return true;
			}
		}
		if (substr_count($c,'[url=') && substr_count($c,'[link=')) {
			$this->text = 'Spam link detected';
			$this->is = 'comment';
			return true;
		}

	}
	
	private function checkEnv() {
		$this->env['ua'] = strtolower($_SERVER['HTTP_USER_AGENT']);
		$this->env['ref'] = strtolower($_SERVER['HTTP_REFERER']);
		$this->env['host'] = strtolower(@$_SERVER['REMOTE_HOST']);
		$this->env['ip'] = self::getIP();
		
		if ($_SESSION['HTTP_REFERER']) {
			$r = addslashes(urldecode($_SESSION['HTTP_REFERER']));
			$r = str_replace( '%3A', ':', $r);
			if (preg_match('/\.google\.co(m|\.[a-z]{2})/', $r) && strstr($r,'leave a comment')) {
				$this->is = 'referer';
				return true;
			}
		}
		if (strstr($this->env['ua'],'libwww-perl') 
			|| strstr($this->env['ua'],'iopus-')
			|| preg_match('/^(nutch|larbin|jakarta|java)/',$this->env['ua'])) {
			$this->is = 'user_agent';
			return true;	
		}
		
		if ($this->env['host']) {
			foreach ($this->spam['remote_host'] as $v) {
				if (preg_match('/'.$v.'/', $this->env['host'])) {
					$this->is = 'remote_host';
					return true;	
				}
			}
		}
		
		foreach ($this->spam['ip'] as $v) {
			if (preg_match('/'.$v.'/', $this->env['ip'])) {
				$this->is = 'ip';
				return true;	
			}
		}	
	}
	
	
	private function random_key() {
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		srand((float)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$keyCode = $keyCode . $tmp;
			$i++;
		}
		if (!$keyCode) {
			srand((float)74839201183*1000000);
			$i = 0;
			$pass = '' ;
			while ($i <= 7) {
				$num = rand() % 33;
				$tmp = substr($chars, $num, 1);
				$keyCode = $keyCode . $tmp;
				$i++;
			}
		}
		return $keyCode;
	}
}


