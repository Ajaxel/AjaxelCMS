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
* @file       inc/World.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

interface World_interface {
	public function set($params);
	public function country($id);
	public function countries();
	public function state($country_id, $state_number);
	public function states($country_id);
	public function city($country_id, $state_number, $city_number);
	public function cities($country_id, $state_number);
	public function district($country_id, $state_number, $city_number, $district_number);
	public function districts($country_id, $state_number, $city_number);
}

class World {	
	
	public $only = array(), $find = array(), $all = false, $by_code = false;
	private static $_instances = array();
	private $cache = array(), $find_prefix = '';
	
	const DEFAULT_DRIVER = 'geo';
	
	public function __construct() {
		$this->has_geo_table = in_array('geo_countries',DB::tables(true, true));
		$this->lang = LANG;
	}
	
	public function driver($driver = '') {
		if (!$driver) $driver = self::DEFAULT_DRIVER;
		if ($driver=='geo') {
			if (!$this->has_geo_table) $driver = 'default';	
		}
		elseif (false && USE_GEO) $driver = 'full';
		$className = 'World_'.$driver;
		if (!isset(self::$_instances[$driver])) self::$_instances[$driver] = new $className;
		$this->by_code = false;
		return self::$_instances[$driver];
	}
	public function only($name, $only) {
		$this->only[$name] = $only;	
	}
	public function by_code($by_code) {
		$this->by_code = $by_code;
		return $this;	
	}
	public function find($data, $set = false, $prefix = '') {
		if ($set) {
			$this->find_prefix = $prefix;
			$this->find = $data;
		}
		else return (isset($this->find[$this->find_prefix.$data])?$this->find[$this->find_prefix.$data]:'');
	}
	
	public function super(&$S) {
		if (!@$S['country']) {
			$S['state'] = '';
			$S['city'] = '';
			$S['district'] = '';
		}
		elseif (!@$S['state']) {
			$S['city'] = '';
			$S['district'] = '';
		}	
		elseif (!@$S['city']) {
			$S['district'] = '';
		}	
	}
	public function one($country_id, $state_number = -1, $city_number = -1, $district_number = -1) {
		if (!$country_id) return false;
		if ($district_number!==-1) {
			return $this->district($country_id, $state_number, $city_number, $district_number);
		}
		elseif ($city_number!==-1) {
			return $this->city($country_id, $state_number, $city_number);
		}
		elseif ($state_number!==-1) {
			return $this->state($country_id, $state_number);
		}
		else {
			return $this->country($country_id);
		}
	}
	
	public function all($country_id = -1, $state_number = -1, $city_number = -1) {
		if ($city_number!==-1) {
			return $this->districts($country_id, $state_number, $city_number);
		}
		elseif ($state_number!==-1) {
			return $this->cities($country_id, $state_number);
		}
		elseif ($country_id!==-1) {
			return $this->states($country_id);
		}
		else {
			return $this->countries();
		}
	}
	public function getList($country_id = 0, $state_number = 0, $city_number = 0) {
		if ($city_number) {
			return $this->districts($country_id, $state_number, $city_number);
		}
		elseif ($state_number) {
			return $this->cities($country_id, $state_number);
		}
		elseif ($country_id) {
			return $this->states($country_id);
		}
		else {
			return $this->countries();
		}
	}
	public function set($params) {
		foreach ($params as $k => $v) $this->$k = $v;
		return $this;
	}
}

class World_default extends World implements World_interface {
	public function __construct() {
		// nothing
	}
	public function set($params) {
		foreach ($params as $k => $v) $this->$k = $v;
		return $this;
	}
	public function country($code) {
		if (is_array($code)) $code = $code[key($code)];
		if ($this->cache[$this->lang]['countries']) {
			return $this->cache[$this->lang]['countries'][$code];
		} else {
			$this->cache[$this->lang]['countries'] = DB::all('SELECT code,name FROM '.DB_PREFIX.'countries','code|name');
			$ret = $this->cache[$this->lang]['countries'][$code];
		}
		//$ret = DB::row('SELECT name FROM '.DB_PREFIX.'countries WHERE code='.e($code),'name');
		if (!$ret) {
			$arr = include(FTP_DIR_ROOT.'config/system/countries.php');
			return (isset($arr[$code]) ? $arr[$code] : '');
		}
		if (!$ret) $ret = $code;
		return $ret;
	}
	public function countries() {
		if (is_file(FTP_DIR_ROOT.'config/system/countries.php')) {
			$arr = include(FTP_DIR_ROOT.'config/system/countries.php');
			if (isset($this->only['countries']) && is_array($this->only['countries'])) {
				$_arr = array();
				foreach ($arr as $k => $v) {
					if (in_array($k,$this->only['countries'])) $_arr[$k] = $v;
				}
				$arr = $_arr;
			}
			natsort($arr);
			if (Session()->Country) {
				$name = $arr[Session()->Country];
				unset($arr[Session()->Country]);
				return array_merge(array(Session()->Country=>$name),$arr);
			} else {
				return $arr;	
			}
		}
		else {
			$f = '';
			if (isset($this->only['countries']) && is_array($this->only['countries'])) {
				$f .= ' WHERE code IN (\''.join('\', \'',$this->only['countries']).'\')';
			}
			return DB::getAll('SELECT code, name FROM '.DB_PREFIX.'countries'.$f.' ORDER BY name','code|name');
		}
	}
	public function state($country_id, $state_number) {
		return $state_number;
	}
	public function states($country_id) {
		return false;
	}
	public function city($country_id, $state_number, $city_number) {
		return $city_number;
	}
	public function cities($country_id, $state_number) {
		if ($country_id && $country_id!='un') {
			$country_id = self::country($country_id);
			if ($country_id) {
				$ret = array('    '.lang('_No cities found in %1 country.',$country_id));
			}
			$city = Data::find('city');
			if ($city && $city!='unknown') $ret[] = $city;
		} else {
			$ret = array('    '.lang('_Please select your country'));	
		}
	}
	public function district($country_id, $state_number, $city_number, $district_number) {
		return $district_number;
	}
	public function districts($country_id, $state_number, $city_number) {
		return false;
	}
}

class World_geo extends World implements World_interface {
	const DEFAULT_LANGUAGE = 'en';
	public $lang = '';
	public $langs = array();
	private $prefix = 'geo_';
	
	public function __construct() {
		$cols = DB::columns('geo_countries');
		foreach ($cols as $col) if (substr($col,0,5)=='name_') array_push($this->langs,substr($col,5));
		$this->lang = LANG;
		if (!in_array($this->lang,$this->langs)) $this->lang = $this->langs[0];
	}
	
	public function set($params) {
		foreach ($params as $k => $v) $this->$k = $v;
		if (!$this->lang || !in_array($this->lang,$this->langs)) $this->lang = self::DEFAULT_LANGUAGE;
		return $this;
	}
	
	private function and_only($name) {
		switch ($name) {
			case 'countries':
				if (isset($this->only['countries']) && ($j = join(',',array_numeric($this->only['countries'])))) {
					return ' AND id IN ('.$j.')';
				}
			break;
			case 'states':
			case 'cities':
			case 'districts':
				if (isset($this->only[$name]) && $this->only[$name] && ($j = join(',',array_numeric($this->only[$name])))) {
					return ' AND number IN ('.$j.')';
				}
			break;
		}
	}
	
	// country
	public function country($id) {
		if (!$id) return false;
		$double = false;
		$name = 'country';
		if (is_array($id)) {
			$id = $id[0];
			$double = true;
			$name = 'country_d';
		}
		if (!isset($this->cache[$this->lang][$name][$id])) $this->cache[$this->lang][$name][$id] = DB::row('SELECT SQL_SMALL_RESULT name_'.$this->lang.' AS country_name'.($double ? ', code AS country_code, id AS country_id':'').' FROM '.DB_PREFIX.$this->prefix.'countries WHERE '.(is_numeric($id)?'id='.(int)$id:'code='.e($id)),(!$double ? 'country_name' : false));
		return $this->cache[$this->lang][$name][$id] ? $this->cache[$this->lang][$name][$id] : $id;
	}
	
	public function countries() {
		return DB::getAll('SELECT '.($this->by_code?'code':'id').', name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'countries WHERE TRUE'.$this->and_only('countries').' ORDER BY name_'.$this->lang,($this->by_code?'code':'id').'|name_'.$this->lang);
	}

	// state
	public function state($country_id, $state_number) {
		$this->find_country($country_id);
		if (!$country_id || !$state_number) return false;
		$key = $country_id.'.'.$state_number;
		$name = 'state';
		if (!isset($this->cache[$this->lang][$name][$key])) $this->cache[$this->lang][$name][$key] = DB::row('SELECT SQL_SMALL_RESULT name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'states WHERE country_id='.(int)$country_id.' AND number='.(int)$state_number,'name_'.$this->lang);
		return $this->cache[$this->lang][$name][$key];
	}
	
	public function states($country_id) {
		$this->find_country($country_id);
		if (!$country_id) return array();
		if (!is_numeric($country_id) && strlen($country_id)==2) $country_id = DB::one('SELECT id FROM '.DB_PREFIX.$this->prefix.'countries WHERE code='.e($country_id));
		return DB::getAll('SELECT number, name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'states WHERE country_id='.(int)$country_id.$this->and_only('states').' ORDER BY name_'.$this->lang,'number|name_'.$this->lang);
	}
	
	// city
	public function city($country_id, $state_number, $city_number) {
		$this->find_country($country_id);
		if (!$country_id || !$state_number || !$city_number) return false;
		$key = $country_id.'.'.$state_number.'.'.$city_number;
		$name = 'city';
		if (!isset($this->cache[$this->lang][$name][$key])) $this->cache[$this->lang][$name][$key] = DB::row('SELECT SQL_SMALL_RESULT name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'cities WHERE country_id='.(int)$country_id.' AND state_number='.(int)$state_number.' AND number='.(int)$city_number,'name_'.$this->lang);
		return $this->cache[$this->lang][$name][$key];

	}
	
	public function cities($country_id, $state_number) {
		$this->find_country($country_id);
		if (!$country_id || !$state_number) return array();
		return DB::getAll('SELECT number, name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'cities WHERE country_id='.(int)$country_id.' AND state_number='.(int)$state_number.$this->and_only('cities').' ORDER BY name_'.$this->lang,'number|name_'.$this->lang);
	}
	
	// district
	public function district($country_id, $state_number, $city_number, $district_number) {
		$this->find_country($country_id);
		if (!$country_id || !$state_number || !$city_number || !$district_number) return false;
		$key = $country_id.'.'.$state_number.'.'.$city_number.'.'.$district_number;
		$name = 'district';
		if (!isset($this->cache[$this->lang][$name][$key])) $this->cache[$this->lang][$name][$key] = DB::row('SELECT SQL_SMALL_RESULT name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'districts WHERE country_id='.(int)$country_id.' AND state_number='.(int)$state_number.' AND city_number='.(int)$city_number.' AND number='.(int)$district_number,'name_'.$this->lang);
		return $this->cache[$this->lang][$name][$key];
	}
	
	public function districts($country_id, $state_number, $city_number) {
		$this->find_country($country_id);
		if (!$country_id || !$state_number || !$city_number) return array();
		return DB::getAll('SELECT number, name_'.$this->lang.' FROM '.DB_PREFIX.$this->prefix.'districts WHERE country_id='.(int)$country_id.' AND state_number='.(int)$state_number.' AND city_number='.(int)$city_number.$this->and_only('districts').' ORDER BY name_'.$this->lang,'number|name_'.$this->lang);
	}
	
	private function find_country(&$country_id) {
		if (!is_numeric($country_id) && strlen($country_id)==2) {
			$country_id = DB::one('SELECT id FROM '.DB_PREFIX.$this->prefix.'countries WHERE code='.e($country_id));
		}
	}
}


/*
class World_full extends World implements World_interface {
	private $as_array = false;
	public function __construct() {
		DB::connect(GEO_DB_NUM,GEO_DB_HOST,GEO_DB_USERNAME,GEO_DB_PASSWORD,GEO_DB_NAME,DB_PERSISTENT);
		DB::change();
	}
	public function country($id) {
		$this->start();
		$ret = DB::row('SELECT SQL_SMALL_RESULT '.($this->as_array?'country_id, country_title, code':'country_title').' FROM geo_country WHERE '.(is_numeric($id)?'country_id':'code').'='.e($id));
		if (!$this->as_array) $ret = $ret['country_title'];
		$this->end();
		return $ret;
	}
	public function countries() {
		if (!$this->only['countries']) {
			$this->as_array = true;
			$c = $this->country(Session()->Country);
			$this->as_array = false;
			$first = array($c['country_id'] => $c['country_title']);
			unset($c);
		}
		$this->start();
		$ret = DB::getAll('SELECT country_id, country_title FROM geo_country WHERE '.($this->only['countries']?'country_id IN ('.join(',',$this->only['countries']).')':'code!='.e(strtoupper(Session()->Country))).' ORDER BY number DESC','country_id|country_title');
		$this->end();
		if ($this->only['countries']) return $ret;
		return $first + $ret;
	}
	public function state($country_id, $state_number) {
		$this->start();
		$ret = DB::row('SELECT SQL_SMALL_RESULT state_title FROM geo_state WHERE state_id='.$country_id,'state_title');
		$this->end();
		return $ret;
	}
	public function states($country_id) {
		$this->start();
		if (!is_numeric($country_id)) {
			$country_id = DB::row('SELECT country_id FROM geo_country WHERE code='.e(strtoupper($country_id)),'country_id');	
		}
		if (!$country_id) {
			$this->end();
			return array();
		}
		$other = DB::row('SELECT state_id FROM geo_state WHERE country_id='.(int)$country_id.' AND state_title=\'OTHER PROVINCES\'','state_id');
		if ($other && $this->only['states'] && !in_array($other,$this->only['states'])) $other = false;
		$ret = DB::getAll('SELECT state_id, state_title FROM geo_state WHERE country_id='.(int)$country_id.($other?' AND state_id!='.$other:'').($this->only['states']?' AND state_id IN ('.join(',',$this->only['states']).')':'').' ORDER BY state_id','state_id|state_title');
		$this->end();
		if ($other) {
			$ret = $ret + array($other => lang('_-- other state --'));
		}
		return $ret;	
	}
	public function city($country_id, $state_number, $city_number) {
		$this->start();
		$ret = DB::row('SELECT SQL_SMALL_RESULT city_title FROM geo_city WHERE city_id='.$country_id,'city_title');
		$this->end();
		return $ret;
	}
	public function cities($country_id, $state_number) {
		if (!$country_id || !$state_number) return array();
		$this->start();
		if (!is_numeric($country_id)) {
			$country_id = DB::row('SELECT country_id FROM geo_country WHERE code='.e(strtoupper($country_id)),'country_id');	
		}
		if (!$country_id) {
			$this->end();
			return array();
		}
		if ($state_number && !is_numeric($state_number)) {
			$_state_number = DB::row('SELECT state_id FROM geo_state WHERE code='.e(strtoupper($state_number)).' OR state_title LIKE '.e($state_number),'state_id');
			if (!$_state_number) {
				$_state_number = DB::row('SELECT state_id FROM geo_state WHERE code='.e(strtoupper($state_number)).' OR state_title LIKE '.e($state_number.'%'),'state_id');
			}
			$state_number = $_state_number;
		}
		$ret = DB::getAll('SELECT city_id, city_title FROM geo_city WHERE country_id='.(int)$country_id.($state_number?' AND state_id='.(int)$state_number:'').($this->only['cities']?' AND city_id IN ('.join(',',$this->only['cities']).')':'').' ORDER BY city_id'.(!$state_number?' LIMIT 1000':''),'city_id|city_title');
		$this->end();
		return $ret;
	}
	public function district($country_id, $state_number, $city_number, $district_number) {
		return $country_id;
	}
	public function districts($country_id, $state_number, $city_number) {
		return array();
	}
	private function start() {
		DB::change(GEO_DB_NUM);	
	}
	private function end() {
		DB::change();	
	}
}
*/

/*
class World_text extends World implements World_interface {
	
	public function this_class_is_under_thoughts() {
		return 'Invest in me!';
	}
	
	public function __construct() {

	}
	
	public function set($params) {
		foreach ($params as $k => $v) $this->$k = $v;
		if (!in_array($this->lang,$this->langs)) $this->lang = self::DEFAULT_LANGUAGE;
		return $this;
	}
	
	private function and_only($name) {

	}
	
	// country
	public function country($id) {

	}
	
	public function countries() {

	}

	// state
	public function state($country_id, $state_number) {

	}
	
	public function states($country_id) {

	}
	
	// city
	public function city($country_id, $state_number, $city_number) {

	}
	
	public function cities($country_id, $state_number) {

	}
	
	// district
	public function district($country_id, $state_number, $city_number, $district_number) {

	}
	
	public function districts($country_id, $state_number, $city_number) {

	}
}
*/