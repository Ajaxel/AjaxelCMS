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
* @file       mod/AdminContent_advert.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminContent_advert extends Admin {
	
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
		$this->checkboxes = array('bodylist','nobus','nobuy');
		$this->has_main_photo = true;
		$this->has_multi_photo = true;
		$this->has_lang = true;
		$this->has_files = true;
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

		if ($this->id) {
			foreach (post('data_map') as $id => $a) {
				$a['setid'] = $this->id;
				if ($a['date']) {
					if ($id>0) {
						DB::update('content_advert_map',$a,$id);
					} else {
						DB::insert('content_advert_map',$a);
					}
				} elseif ($id>0) {
					DB::delete('content_advert_map',$id);
				}
			}
			foreach (post('data_map2') as $id => $a) {
				$a['setid'] = $this->id;
				if ($a['date'] && $a['descr']) {
					if ($id>0) {
						DB::update('content_advert_map2',$a,$id);
					} else {
						DB::insert('content_advert_map2',$a);
					}
				} elseif ($id>0) {
					DB::delete('content_advert_map2',$id);
				}
			}
			foreach (post('data_map4') as $id => $a) {
				$a['setid'] = $this->id;
				if ($a['sort'] && $a['stop']) {
					if ($id>0) {
						DB::update('content_advert_map4',$a,$id);
					} else {
						DB::insert('content_advert_map4',$a);
					}
				} elseif ($id>0) {
					DB::delete('content_advert_map4',$id);
				}
			}
			foreach (post('data_map5') as $id => $a) {
				$a['setid'] = $this->id;
				if ($a['title']) {
					if ($id>0) {
						DB::update('content_advert_map5',$a,$id);
					} else {
						DB::insert('content_advert_map5',$a);
					}
				} elseif ($id>0) {
					DB::delete('content_advert_map5',$id);
				}
			}
			$this->affected = 1;
		}
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