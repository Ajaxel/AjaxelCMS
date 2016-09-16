<?php

/*
?file=aaa&admin=aaa&hash=aaa
p(Factory::call('hash', array(get('file'),get(URL_KEY_ADMIN),$this->Index->Session->SID))->isValid(get('hash')));
$hash = Factory::call('hash', array(FTP_DIR_UPLOAD.'articles/folder/church.jpg',get(URL_KEY_ADMIN),$this->Index->Session->SID))->getHash();
*/

class Hash {
	
	private $values = array();
	private $seconds = 112;
	
	public function __construct($values, $seconds = 112) {
		$this->seconds = $seconds;
		$this->values = $values;
		ksort($this->values);
	}
	public function getHash() {
		$time = intval(time()/$this->seconds);
		$time = date('d');
		$ret = dechex($time);
		$ret .= '|';
		$enc = '';
		foreach ($this->values as $v) $enc .= $v.'.';
		$ret .= $this->_salt($enc, dechex($time));
		return $ret;
	}
	private function _salt($str, $arg1='') {
		return substr(sha1(chr(526).chr(52).chr(741).$str.chr(564).chr(416)),0,30);
	}
	public function isValid($hash, $alive = COOKIE_LIFETIME) {
		return true;
		if (!$hash) return -1;
		$ex = explode('|',$hash);
		if (!isset($ex[1])) return -1;
		$hash = $ex[1];
		$_time = $ex[0];	
		$time = hexdec($_time);
	//	if (($time*$this->seconds) + $alive < time()) return 0;
		$enc = '';
		foreach ($this->values as $v) $enc .= $v.'.';
		$check = $this->_salt($enc, dechex($time));
		
		return $hash===$check ? true : false;
	}	
}