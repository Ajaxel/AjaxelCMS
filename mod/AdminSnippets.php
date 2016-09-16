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
* @file       mod/AdminSnippets.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminSnippets extends Admin {
	
	
	public function __construct() {
		$this->title = 'Snippets';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->id = (int)$this->id;
		$this->array['types'] = array(
			'html'	=> 'HTML',
			'php'	=> 'PHP',
			'tpl'	=> 'Smarty',
			'sql'	=> 'SQL data',
			'range'	=> 'Range'
		);
		$this->idcol = 'id';
		$this->table = $this->name;
		$this->array['categories'] = DB::getAll('SELECT DISTINCT(category) FROM '.DB_PREFIX.'snippets ORDER BY category','category');
	}
	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			
		}
	}
	
	private function validate() {
		$err = array();
		if (!$this->data['category']) $err['category'] = lang('$Category cannot be empty');
		if (!$this->data['name']) $err['name'] = lang('$Name cannot be empty');
		elseif (DB::getNum('SELECT 1 FROM '.DB_PREFIX.$this->table.' WHERE '.($this->id?'id!='.$this->id.' AND ':'').'name LIKE '.e($this->data['name']))) {
			$err['name'] = lang('$Such name already exists, please write another');
		}
		if (!$this->data['source']) $err['source'] = lang('$Source code cannot be empty');
		elseif (!self::filePHPok($this->data['source'])) {
			$err['source'] = Conf()->g('phpERROR');
		}
		$this->errors($err);
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->validate();
				$this->allow('snippets',($this->id?'edit':'save'),$this->rs,$this->data);
				$this->global_action('save');
				$this->global_action('msg');
			break;
			case 'sort':
				$this->global_action();
			break;
			case 'act':
				$this->global_action();
			break;
			case 'delete':
				if (!$this->id) break;
				if ($this->isEdit()) break;
				$this->allow('snippets','delete',$this->rs,$this->data);
				$this->rs = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.(int)$this->id);
				$this->allow('grid','delete',$this->rs,$this->data,$this->table,$this->id);
				DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.(int)$this->id);
				$this->affected = DB::affected();
				$this->updated = Site::ACTION_DELETE;
				$this->msg_text = lang('$Snippet: %1 (ID: %2) was deleted',$this->rs['name'], $this->rs['id']);
				$this->msg_delay = 1500;
				$this->log($this->id, false, $this->rs);
			break;
		}
	}
	
	private function sql() {
		if (get('category')) {
			$this->filter .= ' AND category='.e(get('category'));	
		}
		if ($this->find) {
			$this->filter .= ' AND (name LIKE \'%'.$this->find.'%\' OR title LIKE \'%'.$this->find.'%\' OR source LIKE \'%'.$this->find.'%\')';
		}
		$this->order = 'name';
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->sql();
		if ($this->submitted) {
			
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, name, title, category, active, added FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			$this->data[] = $rs;
		}
		$this->total = DB::rows($this->data);
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function window() {
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
			if (!$this->post) $this->id = 0;
		}
		if (!$this->post || !$this->post['id']) {
			$this->post = post('data');
		} else {
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
		}

		$this->win($this->name);
	}
}