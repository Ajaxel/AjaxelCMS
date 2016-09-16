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
* @file       mod/Object.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Object {
	public
		$Index,
		$My,
		$class = '',
	
		$UserID = 0,
		$lang = DEFAULT_LANGUAGE,
	
		$catref = '',
		$catid = 0,
		$action,
		$id = 0,
		$name = '',
		$order = 'id DESC',
		$group = '',
		$filter = '',
		$filter2 = '',
		$select = '',
		$limit = 20,
		$offset = 0,
		$total = 0,
		$catch = array(),
		$page,
		$time,
		$page_key = URL_KEY_PAGE,
		$limit_key = URL_KEY_LIMIT,
	
		$prefix,
	
		$row = array(),
		$data = array(),
		$post = array(),
		$rs = array(),
	
		$modules = array(),
		$langs = array(),
	
		$params = array()
	;

	public function load(Index &$Index) {
		$this->Index =& $Index;
		$this->My =& $this->Index->My;
		$this->time =& $this->My->time;
		$this->lang = $this->Index->Session->Lang;
		$this->UserID = $this->Index->Session->UserID;

		$this->prefix = $this->My->prefix;
		$this->langs = $this->My->langs;
		$this->id = $this->My->id;
		$this->catid = $this->My->catid;
		$this->action = $this->My->action;
		$this->page = $this->My->page;
		$this->limit = $this->My->limit;
		$this->offset = $this->My->offset;

		return $this;
	}
	
	public function init() {
		return $this;
	}
	
	public function setParams(&$params = array()) {
		foreach ($params as $k => $v) $this->$k = $v;
		return $this;
	}
	
	public function set($k, $v) {
		$this->My->set($k, $v);
	}
	
	public function setName($name) {
		$this->modules = Site::getModules(strtolower($this->class));
		if (is_array($name)) {
			foreach ($name as $n) {
				if (array_key_exists($n, $this->modules)) {
					$this->name[] = $n;
				}				
			}
		} else {
			if (array_key_exists($name, $this->modules)) {
				$this->name = $name;
			}
		}
		return $this;
	}
	
	public function setLimit($limit) {
		if ($limit===false) {
			if (request(URL_KEY_LIMIT)<=0) return false;
			$this->limit = request(URL_KEY_LIMIT);
		}
		elseif (is_array($limit)) {
			list ($page, $_limit, $page_key, $limit_key) = $limit;
			$this->params['limit_as_array'] = true;
			$this->limit = (int)$_limit;
			$this->page = (int)$page;
			if ($page_key) $this->page_key = $page_key;
			if ($limit_key) $this->limit_key = $limit_key;
		} else {
			$this->limit = (int)$limit;
		}
		$this->offset = $this->page * $this->limit;
	//	if ($this->offset>DB::MAX_OFFSET) $this->offset = DB::MAX_OFFSET;
		return $this;
	}
	
	public function setPage($page = NULL) {
		if ($this->params['limit_as_array']) return $this;
		$this->page = (int)$page;
		$this->offset = $this->limit * $this->page;
	//	if ($this->offset>DB::MAX_OFFSET) $this->offset = DB::MAX_OFFSET;
		return $this;	
	}
	
	public function setOrder($order) {
		$this->order = $order;
		return $this;	
	}
	public function setGroup($group) {
		$this->group = $group;
		return $this;	
	}
	
	public function setFilter($filter) {
		$this->filter = $filter;
		return $this;	
	}
	public function setFilter2($filter2) {
		$this->filter2 = $filter2;
		return $this;	
	}
	
	public function nav() {
		$pager = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit
		));
		return $pager;
	}
	
	public function setSelect($select) {
		$this->select = $select;
		return $this;
	}
	
	public function setCatch($catch) {
		$this->catch = $catch;
		return $this;
	}
	
	public function addParams($params) {
		$this->params = array_merge($this->params, $params);
		return $this;	
	}
	
	public function setParam($key, $val) {
		$this->params[$key] = $val;
		return $this;	
	}
	
	public function __toString() {
		return '';	
	}
}