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
* @file       mod/AdminGrid_articles.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminGrid_articles extends AdminGrid {

	public function __construct() {
		parent::__construct(__CLASS__);
	}
	public function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'grid_articles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catref` varchar(255) NOT NULL,
  `comnum` int(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `teaser` text,
  `added` int(10) NOT NULL default \'0\',
  `userid` int(11) unsigned NOT NULL default \'0\',
  `main_photo` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `active` tinyint(1) NOT NULL,
  `sort` tinyint(3) NOT NULL,
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `catref` (`catref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
		DB::run($sql);
		$data = array(
			'table'	=> 'articles',
			'type'	=> 'grid',
			'title'	=> 'Articles',
			'icon'	=> '',
			'descr'	=> 'Articles as grid',
			'active'=> 1,
			'userid'=> $this->UserID,
			'edited'=> $this->time
		);
		DB::insert('modules',$data);
		return DB::id();
	}
	protected function init() {
		parent::init();
		$this->idcol = 'id';
		$this->image_sizes = array(
			1	=> array(800, 600)
			,2	=> array(310, 0)
			,3	=> array(170, 0)
			,4	=> array(70, 0)
		);
		$this->has_main_photo = true;
		$this->has_lang = false;
	}
	
	protected function sql() {
		$this->filter = '';
		if ($this->find) {
			$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';	
		}
		$this->order = 'id DESC';
	}
	

	
	
	
}