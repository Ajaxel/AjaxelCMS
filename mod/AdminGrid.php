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
* @file       mod/AdminGrid.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminGrid extends Admin {
	
	protected $has_orders = false;
	
	public function __construct() {
		parent::__construct(__CLASS__);
	}
	protected function init() {
		$this->idcol = 'id';
		$this->title = ucfirst($this->module).' grid manager';
		$this->Index->setVar('title','Admin > Grids > '.ucfirst($this->module).'');
		$this->name = 'grid_'.$this->module;
		$this->table = 'grid_'.$this->module;
		$this->name_id = $this->table.'_'.$this->id;
		
		
		$this->cols = DB::columns($this->table);
		if (in_array('rid',$this->cols)) {
			$this->idcol = 'rid';
			$this->has_lang = true;
		}
		else {
			$this->idcol = 'id';
			$this->has_lang = false;
		}
		
		$this->has_main_photo = in_array('main_photo',$this->cols);
		$this->has_multi_photo = true;
		
		$this->sortby = array();
		$skip = array(
			'descr','is_admin','userid','options','body'
		);
		$this->image_sizes = array(
			1	=> array(800, 600)
			,2	=> array(0, 250)
			,3	=> array(0, 120)
		);
		
		foreach ($this->cols as $col) {
			if (in_array($col, $skip)) continue;
			$this->sortby[$col.' ASC'] = lang('$'.$col).' ASC';
			$this->sortby[$col.' DESC'] = lang('$'.$col).' DESC';
		}
		$this->button['save'] = false;
		$this->button['add'] = is_file(FTP_DIR_TPLS.'admin/grid_'.$this->module.'_window.php');
		if (!$this->button['add']) {
			$this->button['add'] = 	is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_grid_'.$this->module.'_window.php');
		}
		if (!$this->button['add']) {
			$this->button['add'] = 	is_file(FTP_DIR_TPLS.$this->tpl.'/admin/grid_'.$this->module.'_window.php');
		}
		
		$module_title = rtrim($this->module,'s');
		$this->set_msg(lang('$New '.$module_title.' %1 was added',''), lang('$'.ucfirst($module_title).' %1 was updated',''));
		
		if ($this->tab && isset($_GET[self::KEY_LOAD])) {
			$this->no_inc(array('top','bot'));
		}
	}
	
	protected function json() {
		switch ($this->get) {
			case 'url_name':
				$this->json_ret= array('name' => Parser::name(post('title')));
			break;
			case 'action':
				$this->action();
			break;
			case 'save_images':
				$this->global_action('save_image_multi');
				$this->json_ret = array('images' => $this->filesToJson());
			break;
			case 'save_image':
			case 'save_image_one':
				$this->global_action('save_image_one');
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
			default:
				$this->json_ret = array('0'=>'Cannot use: '.post('get'));
			break;
		}
		
	}
	
	protected function validate() {
		$err = array();
		//if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
		$this->errors($err);
		return $err;
	}
	
	protected function action($action = false) {
		switch ($this->action) {
			case 'act':
				$this->allow('grid','activate',$this->rs,$this->data,$this->table,$this->id);
				$this->global_action('act');
			break;
			case 'sort':
				$this->allow('grid','sort',$this->rs,$this->data,$this->table,$this->id);
				$this->global_action('sort');
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'save':
			case 'add':
				$this->rs = DB::select($this->table, $this->id);
				$msg = $this->validate();
				$this->allow('grid','save');
				$this->global_action('save');
				if ($this->has_main_photo) $this->global_action('save_main_photo_one');
				$this->global_action('msg');			
				if ($this->action=='add' && $this->updated) {
					$this->msg_js = 'S.A.W.open(\'?'.URL_KEY_ADMIN.'=grid&'.self::KEY_MODULE.'='.$this->module.'&add=new\');';
				}
			break;
			case 'copy':
				$this->global_action('copy');
			break;
			case 'delete':
				$this->global_action('delete');
				/*
				if (!$this->id) break;
				if ($this->isEdit()) break;
				$this->rs = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.(int)$this->id);
				$this->allow('grid','delete',$this->rs,$this->data,$this->table,$this->id);
				DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE id='.(int)$this->id);
				$this->affected = DB::affected();
				$this->updated = Site::ACTION_DELETE;
				$this->msg_text = lang('$Grid entry: %1 (ID: %2) was deleted',$this->rs['title'], $this->rs['id']);
				$this->msg_delay = 2500;
				$this->log($this->id, false, $this->rs);
				*/
			break;
			case 'excel':
				$file = $this->excel(post('sql'),post('table'));
				$this->msg_js = 'S.G.download(\''.$file.'\', true);';
			break;
		}
	}
	
	protected function sql() {

		$this->filter = '';
		if ($this->find) {
			if (is_numeric($this->find)) {
				$this->filter .= ' AND (id='.$this->find.' OR title='.$this->find.')';
			} else {
				$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\')';	
			}
		}
		if ($this->idcol=='rid') {
			$this->filter .= ' AND lang=\''.$this->lang.'\'';	
		}
		elseif (get('language') && in_array('language', $this->cols)) {
			$this->filter .= ' AND language='.e(get('language'));
		}
		$this->order = 'id DESC';	
	}

	protected function listing() {
		
		$allow = Allow()->admin('grid', 'view', false, false, $this->table, $this->id);
		if ($allow) {
			$this->inc('allow', array('allow' => $allow));
			return;	
		}
		if ($this->button['save'] && $this->submitted && post('data')) {
			foreach (post('data') as $id => $rs) {
				if (!$rs || !isset($rs['title']) || !$rs['title']) continue;
				DB::update($this->table,$rs,$id);	
			}
		}
		
		$this->sql();
		$this->sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.$this->table.' WHERE active!=2'.$this->filter.' ORDER BY '.$this->order.'';
		parent::listing();
	}
	

	public function window() {
		
		$allow = Allow()->admin('grid', 'view', false, false, $this->table, $this->id);
		if ($allow) {
			$this->inc('allow', array('allow' => $allow));
			return;	
		}
		
		
		$this->isEdit();
		if ($this->id) {
			$sql = 'SELECT * FROM '.$this->prefix.$this->table.' WHERE '.($this->idcol=='rid'?'rid='.$this->id.' AND lang=\''.$this->lang.'\'':'id='.$this->id);
			$this->post = DB::row($sql);
		}
		
		
		$this->fixWindow();
		$this->win($this->table);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}