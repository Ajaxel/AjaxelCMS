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
* @file       mod/AdminContent_gallery.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminContent_gallery extends Admin {
	
	function __construct() {
		parent::__construct(__CLASS__);
	}

	function init() {
		$this->table = $this->name;
		$this->title = 'Gallery manager';

		$this->image_sizes = array(
			1	=> array(1000, 0)
			,2	=> array(270, 0) // 112, 83 | 100, 100
			,3	=> array(120, 0) // 60x60
		);

		$this->columns_all_lang = array(
			'catref'
		);
		$this->checkboxes = array('bodylist','comments');
		$this->has_main_photo = true;
		$this->has_multi_photo = true;
		$this->has_lang = true;
		$this->has_files = true;
	}
	
	function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'content_gallery` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `rid` int(11) NOT NULL default \'0\',
  `setid` int(11) unsigned NOT NULL default \'0\',
  `catref` varchar(255) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `body` text,
  `url` varchar(255) NOT NULL,
  `edited` int(10) unsigned NOT NULL default \'0\',
  `added` int(10) NOT NULL default \'0\',
  `userid` int(11) unsigned NOT NULL default \'0\',
  `main_photo` varchar(255) NOT NULL,
  `views` int(6) unsigned NOT NULL default \'0\',
  `comments` enum(\'Y\',\'N\') NOT NULL default \'N\',
  `notes` text NOT NULL,
  `options` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `bodylist` enum(\'1\',\'0\') NOT NULL default \'0\',
  `sort` tinyint(3) NOT NULL,
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `rid` (`rid`,`setid`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';	
		DB::run($sql);
		
		$sql = 'CREATE TABLE `'.$this->prefix.'content_gallery_files` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `setid` int(11) unsigned NOT NULL default \'0\',
  `file` varchar(255) NOT NULL,
  `width` int(4) unsigned NOT NULL default \'0\',
  `height` int(4) unsigned NOT NULL default \'0\',
  `media` enum(\'image\',\'audio\',\'flash\',\'video\',\'doc\') default NULL,
  `mime` varchar(100) NOT NULL,
  `size` int(10) NOT NULL default \'0\',';
  		foreach ($this->langs as $l => $x) {
		  	$sql .= '`title_'.$l.'` varchar(255) NOT NULL,
  `descr_'.$l.'` text NOT NULL,';
		}
 		$sql .= '`copyright` varchar(255) NOT NULL,
  `added` int(10) unsigned NOT NULL default \'0\',
  `userid` int(10) unsigned NOT NULL default \'0\',
  `active` tinyint(1) NOT NULL,
  `sort` int(5) NOT NULL default \'0\',
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `setid` (`setid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		DB::run($sql);
		$data = array(
			'table'	=> 'gallery',
			'type'	=> 'content',
			'title'	=> 'Gallery',
			'icon'	=> 'actions/view-preview',
			'descr'	=> 'Multiple media files upload with category, title, description and full body details',
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
		if (!$this->data['descr'] && !$this->data['body']) $err['descr'] = lang('$Description cannot be empty');
		return $err;
	}
	
	function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'save_images':
				$this->global_action('save_image_multi');
				$this->json_ret = array('images' => $this->filesToJson());
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
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	function toDB() {

	}
	
	function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->global_action('validate');
				$this->global_action('save_content');
				$this->global_action('save_gallery');
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
			case 'act':
				$this->global_action();
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

	}
	
	function window() {
		$this->global_action('content_window');
	}
	
	function upload() {
		$this->global_action('upload');
	}
}