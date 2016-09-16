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
* @file       mod/AdminGrid_links.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminGrid_links extends AdminGrid {

	public function __construct() {
		parent::__construct(__CLASS__);
		$this->button['save'] = false;
	}
	public function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'grid_links` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(50) default NULL,
  `url` varchar(255) default NULL,
  `descr` text NOT NULL,
  `added` int(10) NOT NULL default \'0\',
  `userid` int(11) unsigned NOT NULL default \'0\',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		DB::run($sql);
		$data = array(
			'table'	=> 'links',
			'type'	=> 'grid',
			'title'	=> 'Links',
			'icon'	=> 'devices/phone',
			'descr'	=> 'Links database',
			'active'=> 1,
			'userid'=> $this->UserID,
			'edited'=> $this->time
		);
		DB::insert('modules',$data);
		return DB::id();
	}
	public function init() {
		parent::init();
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
		$this->button['save'] = true;
		$this->button['add'] = true;
	}
	
	public function validate() {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
		if (!$this->data['url']) $err['url'] = lang('$URL is empty');
		if (!$this->id) $this->data['is_admin'] = 1;
		$this->set_msg(lang('$New link %1 was added',$this->data['title']), lang('$Link %1 was updated',$this->rs['title']));
		return $err;
	}
	
	protected function sql() {
		$this->filter = '';
		if ($this->find) {
			$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';	
		}
		$this->order = 'title';
	}
	
	
	
	
	
	
}