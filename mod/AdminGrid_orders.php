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
* @file       mod/AdminGrid_orders.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminGrid_orders extends AdminGrid {

	public function __construct() {
		parent::__construct(__CLASS__);

		$this->idcol = 'id';
	}

	public function init() {
		parent::init();
		
		$this->module = substr(__CLASS__,10);
		$this->grid_modules = Site::getModules('grid');
		$this->title = ucfirst($this->module).' grid manager';
		$this->Index->setVar('title','Admin > Grids');
		$this->table = 'grid_'.$this->module;
		$this->idcol = 'rid';
		$this->button['save'] = false;
		$this->button['add'] = true;
		if (in_array('grid_product',DB::tables())) {
			$this->connect_table = 'grid_product';
		} else {
			$this->connect_table = false;
		}
	}
	
	public function validate() {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
		if (!$this->id) $this->data['is_admin'] = 1;
		$this->set_msg(lang('$New link %1 was added',$this->data['title']), lang('$Link %1 was updated',$this->rs['title']));
		return $err;
	}
	
	protected function sql() {
		$this->filter = '';
		if ($this->find) {
		//	$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';	
		}
		$this->order = 'id DESC';
	}
	

	public function listing() {
		if (!$this->connect_table) {
			return parent::listing();	
		}
		$this->sql();
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS o.rid, o.status, CONCAT(o.qty,\' * \',o.price) AS price, qty, o.email, s.title, o.active, o.userid, o.added, DATE_FORMAT(FROM_UNIXTIME(o.added), \'%H:%i %d %b %Y\') AS date FROM '.$this->prefix.$this->table.' o LEFT JOIN '.$this->prefix.$this->connect_table.' s ON s.id=o.setid WHERE o.rid=o.id ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			$rs['s'] = '<span style="color:'.Conf()->g3('order_statuses',$rs['status'],1).'">'.Conf()->g3('order_statuses',$rs['status'],0).'</span>';
			$this->data[] = $rs;
		}
		$this->total = DB::one('SELECT FOUND_ROWS()');
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	
}