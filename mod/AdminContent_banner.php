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
* @file       mod/AdminContent_banner.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminContent_banner extends Admin {
	
	function __construct() {
		parent::__construct(__CLASS__);
	}
	function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'content_banner` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `rid` int(11) NOT NULL default \'0\',
  `setid` int(11) unsigned NOT NULL default \'0\',
  `lang` varchar(2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `body` mediumblob NOT NULL,
  `url` varchar(255) NOT NULL,
  `edited` int(10) unsigned NOT NULL default \'0\',
  `added` int(10) NOT NULL default \'0\',
  `userid` int(11) unsigned NOT NULL default \'0\',
  `main_photo` varchar(255) NOT NULL,
  `views` int(6) unsigned NOT NULL default \'0\',
  `active` tinyint(1) NOT NULL,
  `sort` tinyint(3) NOT NULL,
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `rid` (`rid`,`setid`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';	
		DB::run($sql);
		$data = array(
			'table'	=> 'banner',
			'type'	=> 'content',
			'title'	=> 'Banner',
			'icon'	=> 'actions/stamp',
			'descr'	=> 'Banners module, gif, flash or custom javascript code',
			'active'=> 1,
			'userid'=> $this->UserID,
			'edited'=> $this->time
		);
		DB::insert('modules',$data);
		return DB::id();
	}
	function init() {
		$this->table = $this->name;
		$this->title = 'Banner manager';
		$this->image_sizes = array(
			1	=> false
			,3	=> false
		);
		$this->has_main_photo = true;
		$this->has_lang = true;
	}
	
	function validate() {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
	//	if (!$this->data['descr']) $err['descr'] = lang('$Description cannot be empty');
		return $err;
	}
	
	function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'save_image':
				$this->global_action('save_image_one');
			break;
			case 'delete_image':
				$this->global_action('delete_image');
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->global_action('validate');
				$this->global_action('save_content');
				$this->global_action('save_main_photo_one');
				$this->global_action('msg');
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'add':
				$this->global_action('add_content');
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
	
	function window() {
		$this->global_action('content_window');
	}
	
	function upload() {
		$this->global_action('upload', true);
	}
		
}