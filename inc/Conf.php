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
* @file       inc/Conf.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

function &Site() {
	return Site::getInstance();	
}
function &Conf() {
	return Conf::getInstance();	
}
function &Allow() {
	return Allow::getInstance();	
}
function &Session($var = NULL) {
	if ($var) return Session::getInstance()->$var;
	return Session::getInstance();	
}
function &Index() {
	return Index::getInstance();	
}
function &Action() {
	if (!is_file(FTP_DIR_TPL.'classes/MyAction.php')) return Action::getInstance();
	return MyAction::getInstance();	
}
function Parser($method = 'print', $t = '', $param = NULL, $err3 = NULL, $with_error = true) {
	return Parser::parse($method, $t, $param);	
}


function asset($file, $add = '', $full = false) {
	$ext = ext($file);
	if ($ext=='js') {
		Index::getInstance()->addJS($file, $add, $full);	
	}
	elseif ($ext=='css') {
		Index::getInstance()->addCSS($file, $add, $full);
	}
}
function asset_a($file, $add = '', $full = false) {
	$ext = ext($file);
	if ($ext=='js') {
		Index::getInstance()->addJSA($file, $add, $full);	
	}
	elseif ($ext=='css') {
		Index::getInstance()->addCSSA($file, $add, $full);
	}
}

/*
function Reflect($class) {
	return new ReflectionClass($class);	
}
function AI($question) {
	return Factory::call('ai')->clear()->question($question)->result();
}
*/

function prefix($template = false, $table = false) {
	$site_template = Session()->Template;
	if (!$template) $template = $site_template;	
	if ($template==$site_template) {
		$prefix = PREFIX;
	} else {
		$prefix = Conf()->g2('prefix',$template);
		if (!$prefix) {
			$prefix = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template='.e($template).' AND name=\'PREFIX\'','val');
			Conf()->s2('prefix',$template,$prefix);
		}
	}
	if ($table) {
		if (in_array($table,Site::getGlobalTables())) return DB_PREFIX.$table;
		return DB_PREFIX.$table.$prefix;
	}
	return DB_PREFIX.$prefix;	
}

// {'Data::getArray()'|Func}
function Func($eval) {
	$ret = array();
	eval('$ret = '.$eval.';');
	return $ret;
}

function Call($class, $method) {
	$args = func_get_args();
	array_shift($args); array_shift($args);
	return call_user_func_array(array($class,$method),$args);	
}

function url($num, $default = '', $giveAll = false) {
	$keys = Site::$URL_KEYS;
	$ret = (isset($keys[$num]) ? $keys[$num] : $default);
	if (is_array($ret)) {
		$ret = key($ret);
	}
	if ($giveAll && ($u = get($ret))) {
		$ret = $ret.URL_VALUE.$u;	
	}
	return $ret;
}

function http($http, $name, $key, $_key, $default = '') {
	if ($_key) $return = ((isset($http[$key]) && isset($http[$key][$_key])) ? $http[$key][$_key] : $default);
	else {
		if (is_array($key)) $key = key($key);
		$return = (isset($http[$key]) ? $http[$key] : $default);
	}
	if ($return==='[[:CACHE:]]') {
		$return = Cache::getSmall($name.'('.$key.($_key?','.$_key:'').')');
	}
	elseif ($default==='[[:CACHE:]]') {
		Cache::saveSmall($name.'('.$key.($_key?','.$_key:'').')',$return);
	}
	return $return;
}

function get($key,$_key=false,$default = '') {
	return http($_GET, 'get', $key, $_key, $default);
}
function post($key,$_key=false, $default = '') {
	return http($_POST, 'post', $key, $_key, $default);	
}
function files($key,$_key=false, $default = '') {
	if ($_key) return ((isset($_FILES[$key]) && isset($_FILES[$key][$_key])) ? $_FILES[$key][$_key] : $default);
	else return (isset($_FILES[$key]) ? $_FILES[$key] : $default);	
}
function request($key, $_key=false, $default = '') {
	return http($_REQUEST, 'request', $key, $_key, $default);
}
function cookie($key, $default = '') {
	return (isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default);	
}
function server($key, $default = '') {
	return (isset($_SERVER[$key]) ? $_SERVER[$key] : $default);	
}
function is($var, $key) {
	return ($var && isset($var[$key]) && $var[$key]);
}
function ret($var, $key, $default = false) {
	return (($var && isset($var[$key]) && $var[$key]) ? $var[$key] : $default);
}
/*
function v($var, $default = '') {
	return ($var ? $var : $default);
}
*/

function getMem($var, $delete = true) {
	$ret = isset($_SESSION['MEM'][$var]) ? $_SESSION['MEM'][$var] : NULL;
	if ($delete) delMem($var);
	return $ret;
}
function setMem($var,$data) {
	if (!isset($_SESSION['MEM']) || !is_array($_SESSION['MEM'])) $_SESSION['MEM'] = array();
	$_SESSION['MEM'][$var] = $data;
}
function delMem($var) {
	unset($_SESSION['MEM'][$var]);
}
/*
function setVar($type, $text) {
	Index::getInstance()->setVar($type, $text);
}
*/

function sget() {
	$args = func_get_args();
	switch (func_num_args()) {
		case 1:
			$ret = isset($_SESSION[$args[0]]) ? $_SESSION[$args[0]] : NULL;
		break;
		case 2:
			$ret = isset($_SESSION[$args[0]][$args[1]]) ? $_SESSION[$args[0]][$args[1]] : NULL;
		break;
		case 3:
			$ret = isset($_SESSION[$args[0]][$args[1]][$args[2]]) ? $_SESSION[$args[0]][$args[1]][$args[2]] : NULL;
		break;
		default:
			$ret = NULL;
		break;
	}
	return $ret;
}



function sset() {
	$args = func_get_args();
	switch (func_num_args()) {
		case 2:
			$_SESSION[$args[0]] = $args[1];
		break;
		case 3:
			if (!is_array($_SESSION[$args[0]])) $_SESSION[$args[0]] = array();
			$_SESSION[$args[0]][$args[1]] = $args[2];
		break;
		case 4:
			if (!is_array($_SESSION[$args[0]])) $_SESSION[$args[0]] = array();
			if (!is_array($_SESSION[$args[0]][$args[1]])) $_SESSION[$args[0]][$args[1]] = array();
			$_SESSION[$args[0]][$args[1]][$args[2]] = $args[3];
		break;
		default:
			unset($_SESSION[$args[0]]);
		break;
	}
}


class Conf {
	private $vars = array();
	private static $_instance = false;
	private static $seen = false;
	
	public function __construct($default = array()) {
		$this->vars = $default;
	}
	public static function &getInstance() {
		if (!self::$_instance) self::$_instance = new self;	
		return self::$_instance;
	}
	public function __set($key, $val) {
		return $this->s($key, $val);	
	}
	public function __get($key) {
		return $this->g($key);	
	}
	public function __toString() {
		return p($this->vars,0);	
	}
	public function __call($name, $args) {
		if (!self::$seen) p($this);
		self::$seen = true;
	}
	// key
	public function key($key) {
		return @key($this->vars[$key]);	
	}
	// set
	public function s($key, $val='') {
		$this->vars[$key] = $val;
	}
	public function s2($key, $_key, $val='') {
	//	if ($_key && is_array($_key)) $_key = 0;
		$this->vars[$key][$_key] = $val;	
	}
	public function s3($key, $_key, $__key, $val='') {
		$this->vars[$key][$_key][$__key] = $val;	
	}
	// collect
	public function c($key, $add=NULL) {
		if ($add) $this->vars[$key] .= $add; else $this->vars[$key] = '';
	}
	public function c2($key, $_key, $add = '') {
		if ($add) $this->vars[$key][$_key] .= $add; else $this->vars[$key][$_key] = '';
	}
	// plus
	public function plus($key,$num = 0) {
		if (!isset($this->vars[$key])) $this->vars[$key] = 0;
		$this->vars[$key] += $num;	
	}
	public function plus2($key,$_key,$num = 0) {
		if (!is_array($this->vars[$key])) $this->vars[$key] = array();
		$this->vars[$key][$_key] += $num;
	}
	
	public function plus3($key,$_key,$__key,$num = 0) {
		if (!isset($this->vars[$key]) || !is_array($this->vars[$key])) $this->vars[$key] = array();
		if (!isset($this->vars[$key][$_key])) $this->vars[$key][$_key] = array();
		if (!isset($this->vars[$key][$_key][$__key])) $this->vars[$key][$_key][$__key] = 0;
		$this->vars[$key][$_key][$__key] += $num;
	}
	
	// minus
	public function minus($key,$num = 0) {
		$this->vars[$key] -= $num;
	}
	public function minus2($key,$_key,$num = 0) {
		if (!is_array($this->vars[$key])) $this->vars[$key] = array();
		$this->vars[$key][$_key] -= $num;
	}
	// array fill
	public function fill($key,$val) {
		if (!isset($this->vars[$key]) || !is_array($this->vars[$key])) $this->vars[$key] = array();
		$this->vars[$key][] = $val;	
	}
	public function unfill($key, $val) {
		unset($this->vars[$key][array_search($val,$this->vars[$key])]);
	}
	
	public function get() {
		$a = func_get_args();
		switch (func_num_args()) {
			case 1:
				return @$this->vars[$a[0]];
			break;
			case 2:
				return @$this->vars[$a[0]][$a[1]];
			break;
			case 3:
				return @$this->vars[$a[0]][$a[1]][$a[2]];
			break;
			case 4:
				return @$this->vars[$a[0]][$a[1]][$a[2]][$a[4]];
			break;
		}
	}
	public function set() {
		$a = func_get_args();
		switch (func_num_args()) {
			case 2:
				@$this->vars[$a[0]] = $a[1];
			break;
			case 3:
				@$this->vars[$a[0]][$a[1]] = $a[2];
			break;
			case 4:
				@$this->vars[$a[0]][$a[1]][$a[2]] = $a[3];
			break;
		}
		return $this;
	}
	// get
	public function g($key,$default = false) {
		return (isset($this->vars[$key]) ? $this->vars[$key] : $default);
	}
	public function g2($key,$_key,$default = false) {
		if ($_key && is_array($_key)) $_key = 0;
		if (!isset($this->vars[$key]) || !is_array($this->vars[$key])) $this->vars[$key] = array();
		return isset($this->vars[$key][$_key]) ? $this->vars[$key][$_key] : $default;
	}
	public function g3($key,$_key,$__key,$default = false) {
		if (!isset($this->vars[$key]) || !is_array($this->vars[$key])) $this->vars[$key] = array();
		return isset($this->vars[$key][$_key][$__key]) ? $this->vars[$key][$_key][$__key] : $default;
	}
	/*
	public function g4($key,$_key,$__key,$___key,$default = false) {
		if (!isset($this->vars[$key]) || !is_array($this->vars[$key])) $this->vars[$key] = array();
		return isset($this->vars[$key][$_key][$__key][$___key]) ? $this->vars[$key][$_key][$__key][$___key] : $default;
	}
	*/
	// is
	public function is($key) {
		if (isset($this->vars[$key])) return true;
		return false;
	}
	// exists
	public function exists($key, $_key) {
		if (isset($this->vars[$key]) && is_array($this->vars[$key])) return array_key_exists($_key, $this->vars[$key]);
		return false;
	}
	// in
	public function in($key, $_key) {
		if (isset($this->vars[$key]) && is_array($this->vars[$key])) return in_array($_key, $this->vars[$key]);
		return false;
	}
	// nulls
	public function n_array() {
		$args = func_get_args();
		foreach ($args as $a) $this->vars[$a] = array();
	}
	public function n_zero() {
		$args = func_get_args();
		foreach ($args as $a) $this->vars[$a] = 0;
	}
	public function n_false() {
		$args = func_get_args();
		foreach ($args as $a) $this->vars[$a] = false;
	}
	public function n_null() {
		$args = func_get_args();
		foreach ($args as $a) $this->vars[$a] = NULL;
	}
	public function n_empty() {
		$args = func_get_args();
		foreach ($args as $a) $this->vars[$a] = '';
	}
	public function n_unset() {
		$args = func_get_args();
		foreach ($args as $a) unset($this->vars[$a]);
	}
	// other
	public function merge($array = array()) {
		if (!is_array($array)) return false;
		$this->vars = array_merge((array)$this->vars,$array);
	}
	public function merge2($key, $array = array()) {
		if (!is_array($array)) return false;
		if (isset($this->vars[$key])) {
			$this->vars[$key] = array_merge((array)$this->vars[$key],$array);
		} else {
			$this->vars[$key] = $array;	
		}
	}
	public function getAll() {
		return $this->vars;	
	}
}
