<?php

class ProtectDir {
	
	var $htpasswd_binary = '';
	var $auto_conf = true;
	var $alphabet_64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	var $htpasswd_line = '';
	var $username = '';
	var $debug = array();
	
	function ProtectDir($mode = false, $auto_conf = true) {
		$this->mode = $mode;
		$this->auto_conf = $auto_conf;
		if (!$this->mode) $this->mode = 'md5';
	}
	
	// access private
	function _locate_htpasswd() {
		$htpasswd = '';
		if (is_executable('/usr/sbin/htpasswd2')) {
			$htpasswd = '/usr/sbin/htpasswd2';
		} else if (is_executable('/usr/bin/htpasswd')) {
			$htpasswd = '/usr/bin/htpasswd';
		} else {
			$output2 = array();
			$return2 = null;
			exec('which htpasswd2', $output2, $return2);
			$output = array();
			$return = null;
			exec('which htpasswd', $output, $return);
			if (!$return2) $htpasswd = $output2[0];
			else if (!$return) $htpasswd = $output[0];
		}
		if (strpos($htpasswd, 'htpasswd')) {
			$output = array();
			$return = null;
			exec($htpasswd . ' -nbp user pass', $output, $return);
			if ($return || $output[0] != 'user:pass') $htpasswd = '';
		} else {
			$htpasswd = '';
		}
		return $htpasswd;
	}
	
	function _salt($length) {
		$length = intval($length);
		$salt = '';
		for ($i = 0; $i < $length; $i++) {
			$salt .= substr($this->alphabet_64, mt_rand(0, 63), 1);
		}
		return $salt;
	}
	
	function _convert_64($value, $count) {
		$result = '';
		while(--$count) {
			$result .= $this->alphabet_64[$value & 0x3f];
			$value >>= 6;
		}
		return $result;
	}
	
	function _crypt_apr_md5($password) {
		$salt = $this->_salt(8);
		$length  = strlen($password);
		$context = $password.'$apr1$'.$salt;
		$binary = md5($password . $salt . $password, true);
		for ($i = $length; $i > 0; $i -= 16) {
			$context .= substr($binary, 0, min(16, $i));
		}
		for ($i = $length; $i > 0; $i >>= 1) {
			$context .= ($i & 1) ? chr(0) : $password[0];
		}
		$binary = md5($context, true);
		for($i = 0; $i < 1000; $i++) {
			$new = ($i & 1) ? $password : $binary;
			if ($i % 3) {
				$new .= $salt;
			}
			if ($i % 7) {
				$new .= $password;
			}
			$new .= ($i & 1) ? $binary : $password;
			$binary = md5($new, true);
		}
		$p = array();
		for ($i = 0; $i < 5; $i++) {
			$k = $i + 6;
			$j = $i + 12;
			if ($j == 16) {
				$j = 5;
			}
			$p[] = $this->_convert_64((ord($binary[$i]) << 16) | (ord($binary[$k]) << 8) | (ord($binary[$j])), 5);
		}
		return '$apr1$'.$salt.'$'.implode($p).$this->_convert_64(ord($binary[11]), 3);
	}

	// access public
	function genHash($username,$password) {
		$valid = true;
		$valid = $valid && $username;
		$valid = $valid && !strpos($username, ':');
		$valid = $valid && $password;
	
		if ($valid) {
			if ($this->auto_conf) {
				$this->htpasswd_binary = $this->_locate_htpasswd();
			}
			if ($this->htpasswd_binary) {
				if (!ini_get('safe_mode')) { 
					$username_esc = escapeshellarg($username);
					$password_esc = escapeshellarg($password);
				} else {
					$username_esc = $username;
					$password_esc = $password;
				}
				$flags = '-bn';
				if ($this->mode == 'crypt') $flags .= 'd';
				else if ($this->mode == 'md5') $flags .= 'm'; 
				else if ($this->mode == 'sha') $flags .= 's'; 
				else if ($this->mode == 'plain') $flags .= 'p';
				$output = array();
				$return = NULL;
				exec("$htpasswd_binary $flags $username_esc $password_esc", $output, $return);
				if ($return) {
					$this->htpasswd_binary = '';
				} else {
					$this->htpasswd_line = $output[0];
				}
			}
			$this->username = $username;
			if (!$this->htpasswd_binary) {
				if ($this->mode == 'crypt' && CRYPT_STD_DES==1) {
					$this->htpasswd_line = $username.':'. crypt($password, $this->_salt(2));
				}
				else if ($this->mode == 'md5') {
					$this->htpasswd_line = $username.':'. $this->_crypt_apr_md5($password);
				}
				else if ($this->mode == 'sha') {
					$this->htpasswd_line = $username.':'.'{SHA}'.base64_encode(sha1($password, true));
				}
				else {
					$this->htpasswd_line = $username.':'.$password;
				}
			}
		} else {
			$this->htpasswd_line = false;
		}
		return $this->htpasswd_line;
	}
	
	function write($dir, $message = 'This is protected directory!') {
		$dir = trim($dir,'\\/');
		$file = str_replace(array('/','\\'),DIRECTORY_SEPARATOR,$dir).DIRECTORY_SEPARATOR.'.htpasswd';
		$write = 'AuthName "'.str_replace('"','\"',$message).'"
AuthType basic
AuthUserFile '.$file.'
require valid-user';
		$fp = @fopen($dir.'/.htaccess','w');
		@fwrite($fp,$write);
		@fclose($fp);
		if ($fp) $this->addUser($dir);
		else die('Cannot write .htaccess file!');
	}
	
	function addUser($dir, $username=false, $password=false) {
		if ((!$this->htpasswd_line || !$this->username) && $username && $password) {
			$this->genHash($username, $password);	
		}
		if (!$this->htpasswd_line || !$this->username) {
			die ('Cannot generate .htpasswd line!');	
		}
		$dir = trim($dir,'\\/');
		$file = $dir.'/.htpasswd';
		if (is_file($file)) {
			$contents = file($file);
			foreach ($contents as $line) {
				$ex = explode(':',$line);
				if ($ex[0]==$this->username) {
					$this->debug('Such username exists');
					return true;	
				}
			}
		}
		$fp = @fopen($file,'ab');
		@fwrite($fp,$this->htpasswd_line."\r\n");
		@fclose($fp);
		if (!$fp) die('Cannot write .htpasswd file!');
	}
	
	function removeUser($dir, $username) {
		$file = $dir.'/.htpasswd';
		$write = array();
		if (is_file($file)) {
			$contents = file($file);
			foreach ($contents as $line) {
				$ex = explode(':',$line);
				if ($ex[0]==$username) continue;
				$write[] = $line;
			}
		}
		$fp = @fopen($file,'w');
		@fwrite($fp,join("\r\n",$write)."\r\n");
		@fclose($fp);
		if (!$fp) die('Cannot write .htpasswd file!');
	}

	function changePass($dir, $username, $new_pass) {
		$this->removeUser($dir,$username);
		$this->genHash($username,$new_pass);
		$this->addUser($dir);
	}
	
	function debug($m = false) {
		if ($m===false) echo join('<br />',$this->debug);
		else $this->debug[] = $m;
	}
}
