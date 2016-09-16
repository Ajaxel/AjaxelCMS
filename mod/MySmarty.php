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
* @file       mod/MySmarty.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class holder {
	private $items = array();
	public function get($key, $index = false) {
		if ($index!==false) return @$this->items[$key][$index];
		return @$this->items[$key];
	}
	public function add($key, $val = NULL) {
		if ($val===NULL) $this->items[] = $key;
		else $this->items[$key] = $val;
		return $this;
	}
	public function set($key, $val = NULL) {
		return $this->add($key,$val);
	}
	public function fill($key, $val, $index = false) {
		if ($index!==false) $this->items[$key][$index] = $val;
		else $this->items[$key][] = $val;
		return $this;
	}
	public function getAll() {
		return $this->items;
	}
	public function destruct() {
		$this->items = array();
	}
	public function clear() {
		return $this->destruct();
	}
	public function __toString() {
		return '';	
	}
}

class SmartyCache {
	private static $cache_enabled = true;
	private $name = '';
	public function __construct() {
		if (IS_ADMIN || Session()->UserID) {
			self::$cache_enabled = false;
		}
		$this->name = '';
	}
	private static function name($name) {
		if (!$name) {
			Conf()->plus('smarty_cache_block',1);
			$name = Conf()->g('smarty_cache_block');
		}
		return 'cache_'.crc32($name.'_'.Session()->Lang);
	}
	public function get($name, $time = 3600) {
		if (!self::$cache_enabled) {
			if (IS_ADMIN) Cache::delete(self::name($name), true);
			return false;
		}
		if ($cache = Cache::get(self::name($name),$time,true,true)) {
			return true;	
		}
		return false;
	}
	public static function save($name, $content) {
		if (!self::$cache_enabled) return false;
		return Cache::save(self::name($name),$content,true);
	}
}


// DEPRECATED with new version of Smarty
function smarty_function_inc($params, &$smarty) {
	if (!isset($params['file'])) return false;
	foreach ($params as $k => $v) $smarty->assign($k,$v);
	$smarty->display($params['file']);
}


function smarty_block_ul($params, $content, &$smarty, &$repeat) {
	if ($repeat) return false;
	if (!IS_ADMIN || !EDIT_LOADED || IS_VISUAL) return $content;
	$params['file'] = $smarty->template_resource;
	return '<ul class="a-block" id="a-block_'.$params['name'].'">'.$content.'</ul><span class="a-arguments" id="a-arguments_'.$params['name'].'">'.json($params).'</span>';
}

function smarty_block_li($params, $content, &$smarty, &$repeat) {
	if ($repeat) return false;
	if (!IS_ADMIN || !EDIT_LOADED || IS_VISUAL) return $content;
	$params['file'] = $smarty->template_resource;
	$edit = '<div class="a-div_bottom"><span class="a-arguments" id="a-arguments_'.$params['name'].'">'.json($params).'</span><a href="javascript:;" onclick=""><img src="/tpls/img/oxygen/16x16/actions/edit.png" alt="Edit?" /></a></div>';
	$edit = '';
	return '<li id="a-div_'.$params['name'].'" class="a-div">'.($content?$content:'&nbsp;').$edit.'</li>';
}

function smarty_block_cache($params, $content, &$smarty, &$repeat) {
	if ($repeat) return false;
	if ($params['content'] || $params['smarty'] || $params['open']) Message::halt('Wrong arguments', 'in {cache}...{/cache} block', true);
	SmartyCache::save($params['name'],$content);
	return $content;
}

function smarty_compiler_break($tag_attrs, &$smarty) {
	return 'break';
}

function smarty_compiler_continue($tag_attrs, &$smarty) {
	return 'continue';
}
function smarty_compiler_exit($tag_attrs, &$smarty) {
	return 'exit';
}
function smarty_compiler_return($tag_attrs, &$smarty) {
	return 'return';
}


function smarty_compiler_test($tag_attrs, &$smarty) {
	p($tag_attrs);
	p($smarty);
}

function smarty_function_options($params, &$smarty) {
	if (isset($params['data'])) {
		return Html::buildOptions($params['selected'], $params['data'], @$params['keyAsVal']);
	} else {
		return Data::getArray($params['get'], $params['selected'], (isset($params['settings'])?$params['settings']:'dropdown'), @$params['flag'], @$params['name']);
	}
}
function smarty_function_form_errors($params, &$smarty) {
	foreach ($params as $k => $v) {
		$smarty->assign($k,$v);
	}
	return $smarty->fetch('includes/form_errors.tpl');
}



/*
function smarty_compiler_inc($tag_attrs, &$smarty) {
	// let's think!
}
*/


class MySmarty extends Smarty {
	public $Index;
	public function __construct(&$Index) {
		$this->Index =& $Index;
		$this->compile_check = IS_DEV;
		$this->compile_check = true; // <-- remove after your website work is completed
		$this->debugging = false;
		parent::__construct();
		$this->error_reporting = E_ALL ^ E_NOTICE;

		$this->assign('lang', LANG);
		$this->assign('device', DEVICE);
		$this->assign('langs', Site::getLanguages());
		$this->assign('ajax', Site::$ajax);
		$this->assign('holder', new holder);
		$this->assign('cache', new SmartyCache);
		$this->assign('URL', URL::get());
		$this->assign('Tpl', $this->Index->Tpl);
		$this->assign('User', Session()->get());
		$this->assign('time', time());
	}
	
	public function display($template = NULL, $cache_id = NULL, $compile_id = NULL, $parent = NULL) {
		self::fetch($template, $cache_id, $compile_id, $parent, true);
	}
	
	public function fetch($template = NULL, $cache_id = NULL, $compile_id = NULL, $parent = NULL, $display = false, $merge_tpl_vars = true, $no_output_filter = false) {
		$template = Index()->visual($template);
		$ret = parent::fetch($template, $cache_id, $compile_id, $parent, $display, $merge_tpl_vars, $no_output_filter);	
		return $ret;
	}
}