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
* @file       mod/AdminEntries.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminEntries extends Admin {
	
	public function __construct() {
		$this->title = 'Entries';
		parent::__construct(__CLASS__);
	}
	
	public function init() {
		$this->table = $this->name;
		$this->id = (int)$this->id;
		$this->idcol = 'rid';
		$this->has_multi_photo = true;
		$this->has_main_photo = true;
		$this->has_lang = true;
		$this->has_files = true;
		$this->image_sizes = array(
			1	=> array(1000, 0)
			,2	=> array(300, 0)
			,3	=> array(180, 0)
		);
		$this->columns_all_lang = array(
			'catref', 'menuid', 'name', 'menuids', 'dated', 'comment', 'bodylist'
		);
		$this->checkboxes = array('bodylist');
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
	
	public function validate() {
		$err = array();
		if (!$this->post['title']) {
			$err['title'] = lang('$Title is empty');
		}
		if (!$this->post['menuid']) {
			$err['menuid'] = lang('$Please select the menu');
		}
		if (isset($this->data['menuids']) && is_array($this->data['menuids'])) $this->data['menuids'] = ','.join(',',$this->data['menuids']).',';
		else $this->data['menuids'] = '';
		if ($this->data['dated']) $this->data['dated'] = $this->toTimestamp($this->data['dated']);
		if (isset($this->data['name'])) {
			if (!$this->data['name']) $this->data['name'] = $this->data['title'];
			$this->data['name'] = Parser::name($this->data['name']);
		}
		if (!strlen(trim($this->data['name']))) {
			$err['name'] = lang('$URL name is empty');
		}
		elseif ($this->isReserved($this->data['name'])) {
			$err['name'] = lang('$This name %1 is actually reserved by system, please use another',$this->data['name']);
		}
		if ($this->data['name']) {
			if (DB::getNum('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE '.($this->id?'rid!='.$this->id.' AND ':'').'name LIKE '.e($this->data['name']).' AND active!=2')) {
				$err['name'] = lang('$Such URL name already reserved by another entry');
			}
			/*
			elseif (DB::getNum('SELECT 1 FROM '.$this->prefix.'menu WHERE name LIKE '.e($this->data['name']).' AND active!=2')) {
				$err['name'] = lang('$Such URL name already reserved by menu element');
			}
			*/
			elseif (DB::getNum('SELECT 1 FROM '.$this->prefix.'content WHERE name LIKE '.e($this->data['name']).' AND active!=2')) {
				$err['name'] = lang('$Such URL name already reserved by content block');
			}
		}
		if ($this->data['type']=='content') {
			$err['type'] = lang('$Type cannot be called as content');
		}
		return $err;
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->global_action('validate');
				$this->global_action('save_content');
				$this->global_action('save_gallery');
				$this->global_action('msg');
				$this->msg_delay = 1500;
				//$this->msg_reload = $this->updated==Site::ACTION_UPDATE;
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'add':
				$this->action('save');
				if ($this->updated) {
					$this->action('save');
					$this->msg_close = true;
					$this->msg_reload = false;
					$this->msg_text = '';
					$this->msg_js = 'S.A.W.open(\'?'.URL_KEY_ADMIN.'=entries&add=new\');';
				}
				/*
				if (!$this->id) break;
				$this->action('save');
				$this->msg_close = true;
				$this->msg_reload = false;
				$this->msg_text = '';
				$this->msg_js = 'S.A.W.go(\'?'.URL_KEY_ADMIN.'='.$this->name.'\');';
				*/
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
	
	private function sql() {
		if ($fid = $this->findID('id')) {
			$this->filter .= $fid;	
		} else {
			if ($this->find) {
				$this->filter .= ' AND (title LIKE  \'%'.$this->find.'%\' OR teaser LIKE \'%'.$this->find.'%\' OR name LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';
			}
		}
		if ($this->menuid) {
			$this->filter .= ' AND (menuid='.$this->menuid.' OR menuids LIKE \'%,'.$this->menuid.',%\')';
		}
		if ($this->catref) {
			$this->filter .= ' AND (catref=\''.$this->catref.'\' OR catref LIKE \''.$this->catref.'.%\')';
		}
		$this->order = 'id DESC';
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->sql();
		if ($this->submitted) {
			
		}
		$params = array(
			'table'		=> 'category_'.$this->name,
			'lang'		=> $this->lang,
			'catalogue'	=> false,
			'retCount'	=> true,
			'getAfter'	=> false,
			'noDisable'	=> true,
			'maxLevel'	=> 0,
			'optValue'	=> 'catid',
			'getHidden'	=> true,
			'prefix'	=> $this->prefix,
			'selected'	=> $this->catid
		);
		$Category = Factory::call('category', $params);
		$this->category_options = $Category->getAll()->toOptions();
		$Menu = Factory::call('menu');
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS rid, title, teaser, main_photo, menuid, catref, (CASE WHEN dated THEN DATE_FORMAT(FROM_UNIXTIME(dated),\'%d %b\') ELSE DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b\') END) AS date, active FROM '.$this->prefix.$this->table.' WHERE lang=\''.$this->lang.'\''.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			$this->catchRow($rs);
			$m = $Menu->get($rs['menuid']);
			if ($m) $rs['tree'] = join(' :: ',$m['tree']);
			else $rs['tree'] = 'unknown menu';
			$rs['url'] = URL::ht($m[9]['url'].AMP.'id='.$rs['rid']);
			$tree = $Category->tree($rs['catref'],'category_'.$this->name);
			if ($tree) {
				$rs['cat_tree'] = array();
				foreach ($tree as $c) {
					$rs['cat_tree'][] = $c['catname'];
				}
			}
			unset($rs['menuid']);
			$this->data[] = $rs;
		}
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	private function catchRow(&$rs) {
		if ($rs['main_photo']) {
			$photo = $this->ftp_dir_files.$this->table.'/'.$rs['rid'].'/th1/'.$rs['main_photo'];
			if (is_file($photo)) {
				$rs['main_photo_size'] = @getimagesize($photo);
				if (!$rs['main_photo_size'][0]) $rs['main_photo'] = '';
			} else {
				$rs['main_photo'] = '';
			}
		}
		if (isset($rs['descr'])) $rs['descr'] = Parser::strip_tags($rs['descr']);
		if (isset($rs['price']) && $rs['price']) {
			$rs['descr'] = '<table cellspacing="0" cellpadding="0" width="100%"><tr><td>'.$rs['descr'].'</td><td class="a-price" style="text-align:right;white-space:nowrap">'.$rs['price'].' '.$rs['currency'].'</td></tr></table>';	
		}
	}
	
	public function window() {
		if ($this->id && $this->id!=self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.(int)$this->id.' AND lang=\''.$this->lang.'\' AND active!=2');
			if (!$this->post) {
				$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.(int)$this->id.' AND active!=2');
				$this->post['lang'] = $this->lang;
			}
		}
		if (!$this->post || !$this->post['id']) {
			$this->post = post('data');
		} else {
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
		}
		if (isset($this->post['dated']) && $this->post['dated']) $this->post['dated'] = $this->fromTimestamp($this->post['dated']);
		else $this->post['dated'] = '';
		if (isset($this->post['options']) && !is_array($this->post['options'])) $this->post['options'] = unserialize($this->post['options']);
		
		$this->global_action('main_photo_window');
		$this->json_array['files'] = json($this->filesToJson());
		$this->uploadHash();
		$this->toWindow();
		$this->win($this->name);
	}
}