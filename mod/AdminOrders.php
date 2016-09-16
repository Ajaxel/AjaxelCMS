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
* @file       mod/AdminOrders.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminOrders extends Admin {
	
	public function __construct() {
		$this->title = 'Orders';
		parent::__construct(__CLASS__);
	}
	
	public function init() {
		$this->table = $this->name;
		$this->id = (int)$this->id;
		$this->idcol = 'id';
	}
	
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			
		}
	}
	
	private function validate() {
		$err = array();
		$this->errors($err);
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->validate();
				$this->global_action('save');
				$this->global_action('msg');
			break;
			case 'sort':
				$this->global_action();
			break;
			case 'act':
				$this->global_action();
			break;
		}
	}
	
	private function sql() {
		
		$this->order = 'id DESC';
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = false;
		$this->sql();
		if ($this->submitted) {
			
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, status, price, email, active, userid, added, DATE_FORMAT(FROM_UNIXTIME(added), \'%H:%i %d %b %Y\') AS added, active FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			$rs['s'] = '<span style="color:'.Conf()->g3('order_statuses',$rs['status'],1).'">'.Conf()->g3('order_statuses',$rs['status'],0).'</span>';
			$this->data[] = $rs;
		}
		$this->total = DB::rows();
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function window() {
		
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
			if (!$this->post) $this->id = 0;
			switch ($this->post['table']) {
				case 'content_product':
					$this->post['table_data'] = DB::row('SELECT rid, code, CONCAT(\'<img src="/'.HTTP_DIR_UPLOAD.'content_product/\',rid,\'/th2/\',main_photo,\'">\') AS photo, CONCAT(\'<b>\',title,\'</b>\') AS title, descr, body, DATE_FORMAT(FROM_UNIXTIME(added), \'%H:%i %d %b %Y\') AS added, DATE_FORMAT(FROM_UNIXTIME(edited), \'%H:%i %d %b %Y\') AS edited, CONCAT(price,\' \',currency) AS price, CONCAT(price_old,\' \',currency) AS price_old, instock, options FROM '.$this->prefix.$this->post['table'].' WHERE id='.$this->post['setid']);
				break;
				default:
					if ($this->post['table'] && in_array(DB::remPrefix($this->post['table']), DB::tables())) {
						$this->post['table_data'] = DB::row('SELECT * FROM '.$this->prefix.DB::remPrefix($this->post['table']).' WHERE id='.$this->post['setid']);
					}
				break;
			}
		}
		if (!$this->post || !$this->post['id']) {
		//	$this->id = 0;
			$this->post = post('data');
		} else {
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
		}

		$this->win($this->name);
	}
	
}