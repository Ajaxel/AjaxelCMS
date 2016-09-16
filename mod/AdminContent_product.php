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
* @file       mod/AdminContent_product.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminContent_product extends Admin {
	
	function __construct() {
		parent::__construct(__CLASS__);
	}
	function init() {
		$this->table = $this->name;
		$this->title = 'Products manager';
		$this->image_sizes = array(
			1	=> array(640, 480) // 640, 480
			,2	=> array(177, 192) // 112, 83 | 100, 100
			,3	=> array(85, 85) // 60x60
		);
		$this->columns_all_lang = array(
			'catref','price','price_old','currency','code','instock','sort','active','bestseller','main_page'
		);
		$this->has_main_photo = true;
		$this->has_multi_photo = true;
		$this->has_lang = true;
	}
	function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'content_product` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `rid` int(11) NOT NULL default \'0\',
  `setid` int(11) unsigned NOT NULL default \'0\',
  `catref` varchar(255) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descr` text,
  `body` mediumblob NOT NULL,
  `notes` text NOT NULL,
  `price` double(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `instock` int(8) NOT NULL default \'0\',
  `price_old` double(10,2) default NULL,
  `code` varchar(20) NOT NULL,
  `options` text,
  `added` int(10) NOT NULL default \'0\',
  `edited` int(10) default \'0\',
  `userid` int(11) default \'0\',
  `main_photo` varchar(255) NOT NULL,
  `views` int(6) unsigned NOT NULL default \'0\',
  `comments` enum(\'1\',\'0\') default \'0\',
  `bestseller` enum(\'1\',\'0\') NOT NULL default \'0\',
  `main_page` enum(\'1\',\'0\') NOT NULL default \'0\',
  `active` tinyint(1) NOT NULL,
  `sort` tinyint(3) NOT NULL,
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `rid` (`rid`,`setid`,`catref`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		DB::run($sql);
		$data = array(
			'table'	=> 'product',
			'type'	=> 'content',
			'title'	=> 'Product',
			'icon'	=> 'devices/input-mouse',
			'descr'	=> 'Products module, with title, description, full details, price, options, in stock column and etc..',
			'active'=> 1,
			'userid'=> $this->UserID,
			'edited'=> $this->time
		);
		DB::insert('modules',$data);
		return DB::id();
	}
	function validate() {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
		if (!$this->data['descr']) $err['descr'] = lang('$Description cannot be empty');
		return $err;
	}
	
	function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'save_images':
				$this->global_action('save_image_multi');
				$this->json_ret = $this->filesToJson();
			break;
			case 'set_main_photo':
				$this->global_action('set_main_photo');
			break;
			case 'sort_images':
				$this->global_action('sort_images');
			break;
			case 'delete_image':
				$this->global_action('delete_image');
			break;
			case 'delete_images':
				$this->global_action('delete_images');
			break;
			case 'save_option_group':
				$this->msg_text = $_POST['post'];
				foreach (post('post') as $i => $a) {
					if (!$a['value']) continue;
					$keys[] = $a['value'];
				}
				DB::run('REPLACE INTO '.$this->prefix.'product_options (lang, `group`, name, val) VALUES (\''.$this->lang.'\', '.e(post('vars','option_group')).', '.e(post('vars','group')).', '.e(serialize($keys)).')');
				$ret = DB::getAll('SELECT name FROM '.$this->prefix.'product_options WHERE lang=\''.$this->lang.'\' AND `group` LIKE '.e(post('vars','option_group')),'name');
				$this->json_ret = $ret;
			break;
			case 'select_group':
				$opts = array();
				if (post('name')) {
					$vals = unserialize(DB::row('SELECT val FROM '.$this->prefix.'product_options WHERE lang=\''.$this->lang.'\' AND `group`= '.e(post('vars','option_group')).' AND name LIKE '.e(post('name')),'val'));
					$_vals = array();
					foreach ($vals as $l => $v) {
						$_vals[$v] = '';
					}
					$opts[post('vars','option_group')] = $_vals;
				} else {
					$options = DB::row('SELECT options FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND lang=\''.$this->lang.'\'','options');
					$opts = unserialize($options);
				}
				$group = DB::getAll('SELECT name FROM '.$this->prefix.'product_options WHERE lang=\''.$this->lang.'\' AND `group` LIKE '.e(post('vars','option_group')),'[[:INDEX:]]|name');
				$this->json_ret = array('opts'=>$opts,'group'=>$group);
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	function toDB() {
		
		foreach ($this->data['option_key'] as $group => $arr) {
			$models = count($this->data['option_val'][$group]) / count($this->data['option_key'][$group]);
			foreach ($arr as $i => $key) {
				$vals = array();
				$j = 0;
				for ($s=0;$s<$models;$s++) {
					$v = $this->data['option_val'][$group][$s + $i * $models];	
					if ($v) $j++;
					$vals[$s] = $v;
				}
				if ($j)	$ret[$group][$key] = $vals;
			}
		}
		$this->data['options'] = serialize($ret);
		
		/*
		$ret = array();
		foreach ($this->data['option_val'] as $group => $arr) {
			foreach ($arr as $i => $key_val) {
				if ($i%2) $val = $key_val;
				else $key = $key_val;
				if (!$val || !$key) {
					continue;
				}
				if ($key && $val) $ret[$group][$key] = $val;
				$key = $val = false;
			}
		}
		$this->data['options'] = serialize(array('option_key'=>$this->data['option_key'],'option_val'=>$this->data['option_val']));
		*/
	}
	
	function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->global_action('validate');
				$this->global_action('save_content');
				$this->global_action('save_main_photo_multi');
				$this->global_action('msg');
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'add':
				if (!$this->id) break;
				$this->action('save');
				$this->msg_close = true;
				$this->msg_reload = false;
				$this->msg_text = '';
				$this->msg_js = 'S.A.W.go(\'?admin='.$this->name.'&'.self::KEY_CONID.'='.$this->conid.'\');';
			break;
			case 'copy':
				if (!$this->id) break;
				$this->action('save');
			break;
			case 'delete':
				$this->global_action('delete_content');
			break;
		}
	}
	
	function toWindow() {
		if (!is_array($this->post['options'])) $this->post['options'] = unserialize($this->post['options']);
		$this->json_array['options'] = json($this->post['options']);
		$spec = DB::getAll('SELECT name FROM '.$this->prefix.'product_options WHERE lang=\''.$this->lang.'\' AND `group`=\'spec\' ORDER BY name','name');
		$access = DB::getAll('SELECT name FROM '.$this->prefix.'product_options WHERE lang=\''.$this->lang.'\' AND `group`=\'access\' ORDER BY name','name');
		$this->json_array['group_spec'] = json($spec);
		$this->json_array['group_access'] = json($access);
	}
	
	function window() {
		$this->global_action('content_window');
	}
	
	function upload() {
		$this->global_action('upload');
	}
}