<?php

/**
* Tpl class, used for small area data calls where is dificult to assign in other areas
* simply call {assign var='main_news' value=$Tpl->call('main_news')} wherever you want to placeit
*/

class Tpl extends Template {
	
	private $cache_get = array();
	private $cache_block = array();
	
	public function __construct(&$Index) {
		parent::__construct($Index);
	}
	
	public function get($what) {
		if (isset($this->cache_get[$what])) return $this->cache_get[$what];
		$ret = array();
		switch ($what) {
	
		}
		$this->cache_get[$what] =& $ret;
		return $ret;
	}
	
	public function mediaName($m) {
		switch ($m) {
			case 'doc':
				$m = 'Document';
			break;	
			case 'image':
				$m = 'Image';
			break;
			case 'unknown':
				$m = 'Unknown';
			break;
		}
		return $m;
	}
	
	public function block($type, $id = 0) {
		switch ($type) {

		}
		return;
	}
}