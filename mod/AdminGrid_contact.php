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
* @file       mod/AdminGrid_contact.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminGrid_contact extends AdminGrid {

	public function __construct() {
		parent::__construct(__CLASS__);
	}
	public function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'grid_contact` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(50) default NULL,
  `email` varchar(50) default NULL,
  `phone` varchar(30) default NULL,
  `body` text NOT NULL,
  `added` int(10) NOT NULL default \'0\',
  `userid` int(11) unsigned NOT NULL default \'0\',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		d($sql);
		DB::run($sql);
		$data = array(
			'table'	=> 'contact',
			'type'	=> 'grid',
			'title'	=> 'Contact',
			'icon'	=> 'devices/phone',
			'descr'	=> '',
			'active'=> 1,
			'userid'=> $this->UserID,
			'edited'=> $this->time
		);
		DB::insert('modules',$data);
		return DB::id();
	}
	public function init() {
		$this->module = substr(__CLASS__,10);
		$this->idcol = 'id';
		if (is_file(FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/grid.php')) {
			$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/grid.php';
		} else {
			$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/grid.php';	
		}
		$this->grid_modules = Site::getModules('grid');
		$this->title = ucfirst($this->module).' grid manager';
		$this->Index->setVar('title','Admin > '.$this->title);
		$this->table = 'grid_'.$this->module;
		$this->idcol = 'id';
	}
	
	public function validate() {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
		$this->set_msg(lang('$New contact %1 was added',$this->data['title']), lang('$Contact %1 was updated',$this->rs['title']));
		return $err;
	}
	
	protected function sql() {
		$this->filter = '';
		if ($this->find) {
			$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';	
		}
		$this->order = 'active, added DESC';
	}
	
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		
		if ($this->submitted && post('data')) {
			foreach (post('data') as $id => $rs) {
				if (!$rs || !isset($rs['title']) || !$rs['title']) continue;
				DB::update($this->table,$rs,$id);	
			}
		}
		
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, title, DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b\') AS date, active FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' LIMIT '.$this->offset.', '.$this->limit;
		$this->data = DB::getAll($sql);
		$this->total = DB::rows($this->data);
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	
	
	public function window() {
		if ($this->id && $this->id!=='new') {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id.' AND active!=2');
		}
		if (!$this->post) {
			$this->id = 0;
			$this->post = post('data');
		} else {
			$this->isEdit();
		}
		$this->win('grid_'.$this->module);
	}
	
	
	
	
	
	
	
}