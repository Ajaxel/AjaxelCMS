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
* @file       mod/Template.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Template {
	
	public $Index, $prefix, $UserID;
	private $cache_get = array(), $cache_block = array();
	
	public function __construct(&$Index) {
		$this->Index =& $Index;
		$this->prefix =& $this->Index->My->prefix;
		$this->UserID =& $this->Index->Session->UserID;
	}
	public function user($id, $select) {
		return Data::user($id, $select);	
	}
	
	public function setTree($i, $key, $value) {
		$this->Index->setTree($i, $key, $value);
	}
	public function addTree($title, $url = false, $type = '') {
		$this->Index->addTree($title, $url, $type);
	}
	
	public function cacheVar($key, $value) {
		$this->Index->cacheVar($key, $value);	
	}
	public function setVar($key, $value, $no_cache = false) {
		$this->Index->setVar($key, $value, $no-cache);	
	}
	public function getVar($key, $no_cache = false) {
		return $this->Index->getVar($key, $no-cache);	
	}
	public function addVar($key, $value, $no_cache = false) {
		return $this->Index->addVar($key, $value, $no-cache);	
	}
	public function preVar($key, $value, $no_cache = false) {
		return $this->Index->preVar($key, $value, $no-cache);	
	}
	
	
	public function im($method, $type = '', $data = false) {
		return Factory::call('im')->$method($type, $data);
	}
	public function addJS($path, $add = '',$full = false) {
		$this->Index->addJS($path, $add, $full);
	}
	public function addCSS($path, $add = '',$full = false) {
		$this->Index->addCSS($path, $add, $full);
	}
}