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
* @file       mod/AdminHelp.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminHelp extends Admin {
	
	public function __construct() {
		parent::__construct(__CLASS__);
	}
	
	protected function install() {
		$tables = DB::tables();
		if (!in_array('help_board', $tables)) {
			$sql = 'CREATE TABLE `'.DB_PREFIX.'help_board` (
`id` int(5) unsigned NOT NULL AUTO_INCREMENT,
`parentid` int(5) unsigned NOT NULL DEFAULT \'0\',
`title` varchar(255) NOT NULL,
`descr` text NOT NULL,
`userid` int(5) unsigned NOT NULL,
`added` int(10) unsigned NOT NULL,
`edited` int(10) unsigned DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
			DB::run($sql);
		}
		if (!in_array('help_contents', $tables)) {
			$sql = 'CREATE TABLE `'.DB_PREFIX.'help_contents` (
`id` int(5) unsigned NOT NULL AUTO_INCREMENT,
`parentid` int(5) unsigned NOT NULL DEFAULT \'0\',
`title` varchar(255) DEFAULT NULL,
`descr` text,
`edited` int(10) unsigned DEFAULT NULL,
`sort` bigint(4) NOT NULL DEFAULT \'0\',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;';
			DB::run($sql);
		}
	}
}


class AdminHelp_board extends Admin {
	public function __construct() {
		$this->title = 'Message board for developers';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->table = 'help_board';
		$this->idcol = 'id';
		
	}
	
	
	
	public function json() {
		switch ($this->action) {
			case 'save':
			case 'delete':
				$this->global_action($this->action);
			break;
		}
	}
	
	public function listing() {
		$this->limit = 25;
		$this->offset = $this->page * $this->limit;
		
		$s = ',descr AS d, DATE_FORMAT(FROM_UNIXTIME(added), \'%H:%i %d-%b\') AS date, (SELECT COUNT(1) AS cnt FROM '.DB_PREFIX.$this->table.' b WHERE b.parentid=a.id) AS c, (SELECT CONCAT(\'<a href="javascript:" onclick="S.A.W.open(\\\'?'.URL_KEY_ADMIN.'=users&id=\',userid,\'\\\')">\',login,\'</a>\') FROM '.DB_PREFIX.'users u WHERE u.id=a.userid) AS user';
		
		$f = '';
		if ($this->find) {
			$f .= ' AND (title LIKE '.e('%'.$this->find.'%').' OR descr LIKE '.e('%'.$this->find.'%').')';
		}
		
		if ($this->id && ($this->row = DB::row('SELECT *'.$s.' FROM '.DB_PREFIX.$this->table.' a WHERE id='.$this->id))) {
			if (get('do')=='delete') {
				DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id.' OR parentid='.$this->id);
				URL::redirect('?'.URL_KEY_ADMIN.'=help&tab=board&load');
			}
			$this->json_row = json($this->row);
			
			if ($this->submitted && $this->data['descr']) {
				DB::insert($this->table,array(
					'parentid'	=> $this->row['id'],
					'title'		=> $this->data['title'],
					'descr'		=> Parser::parse('code_bb',$this->data['descr']),
					'userid'	=> $this->UserID,
					'added'		=> $this->time
				));
				$this->data = array();
			}
			
			$this->Tree = Factory::call('tree')->clear()->set(array(
				'table'			=> $this->table,
				'offset'		=> $this->offset,
				'limit'			=> $this->limit,
				'select'		=> $s,
				'uname_col'		=> 'n',
				'name_col_select'=> 'title AS t',
				'name_col'		=> 't',
				'sort_col'		=> 'id',
				'sort_order'	=> 'ASC',
				'where'			=> ' AND parentid='.$this->row['id'].$f,
			))->get();
			$this->json_data = json($this->Tree->toArray($this->row['id']));
			
		} else {
			if ($this->submitted && $this->data['descr'] && $this->data['title']) {
				DB::insert($this->table,array(
					'parentid'	=> 0,
					'title'		=> $this->data['title'],
					'descr'		=> Parser::parse('code_bb',$this->data['descr']),
					'userid'	=> $this->UserID,
					'added'		=> $this->time
				));
				return URL::redirect('?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.DB::id().'&tab='.$this->tab.'&load');
			}
			$this->Tree = Factory::call('tree')->clear()->set(array(
				'table'			=> $this->table,
				'offset'		=> $this->offset,
				'limit'			=> $this->limit,
				'select'		=> $s,
				'uname_col'		=> 'n',
				'name_col_select'=> 'title AS t',
				'name_col'		=> 't',
				'sort_col'		=> 'id',
				'sort_order'	=> 'DESC',
				'where'			=> ' AND parentid=0'.$f,
			))->get();
			$this->json_data = json($this->Tree->toArray());
			
		}
		$this->total = $this->Tree->total;
		$this->nav();
	}
	
	public function window() {
		$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
		$this->win('help_board');	
	}
}

class AdminHelp_contents extends Admin {
	public function __construct() {
		$this->title = 'Help contents';
		parent::__construct(__CLASS__);
	}
	protected function init() {
		$this->table = 'help_contents';
		$this->idcol = 'id';
		
	}
	
	protected function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;	
		}
	}
	protected function action() {
		switch ($this->action) {
			case 'sort':
				$this->allow($this->table,'activate');
				$this->global_action('sort');
			break;
			case 'act':
				$this->allow($this->table,'activate');
				$this->global_action();
			break;
			case 'save':
				
				if ($this->id!=self::KEY_NEW && $this->id && ($this->rs = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id))) {
					
				}
				
				if ($this->data['parentid']==$this->id) $this->data['parentid'] = 0;
				if (!$this->id || $this->rs['parentid']!=$this->data['parentid']) $this->data['sort'] = DB::one('SELECT MAX(sort) FROM '.DB_PREFIX.$this->table.' WHERE parentid='.(int)$this->data['parentid'])+1;
				$this->global_action();
			break;
			case 'delete':
				$this->global_action();
			break;
		}
	}
	
	public function listing() {
		
		if ($this->id && ($row = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id))) {
			$this->json_row = json($row);
		}
		if ($this->find) {
			$this->filter .= ' AND title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\'';	
		}
		$this->Tree = Factory::call('tree')->clear();

		$this->Tree->set(array(
			'prefix'		=> DB_PREFIX,
			'table'			=> $this->table,
			'uname_col'		=> 'n',
			'name_col_select'=> 'title AS t',
			'name_col'		=> 't',
			'sort_col'		=> 'sort',
			'level_col_as'	=> 'l',
			'where'			=> $this->filter,
			'offset'		=> $this->offset,
			'limit'			=> $this->limit,
			'selected'		=> $this->id,
			'use_levels'	=> $this->find,
			'find'			=> $this->find,
		))->get();
		if ($this->id) {
			$this->output['prev_next'] = $this->Tree->getPrevNext($this->id);
		}
		
		$this->json_data = json($this->Tree->toArray());
	}
	
	public function window() {
		$this->Tree = Factory::call('tree')->clear()->set(array(
			'prefix'		=> DB_PREFIX,
			'table'			=> $this->table,
			'uname_col'		=> 'n',
			'name_col_select'=> 'title AS t',
			'name_col'		=> 't',
			'sort_col'		=> 'sort',
			'level_col_as'	=> 'l',
			'offset'		=> $this->offset,
			'limit'			=> $this->limit
		))->get();	

		$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
		$this->win('help_contents');	
	}
}


class AdminHelp_update extends Admin {
	public function __construct() {
		$this->title = 'Update Ajaxel CMS';
		parent::__construct(__CLASS__);
	}
	public function init() {
		
	}
	
	public function json() {
		switch ($this->action) {
			case 'update':



			break;	
		}
	}
	
	private function system_update($file) {
		if (!$file) return false;
		$name = nameOnly($file);
		$dir = FTP_DIR_ROOT.'files/temp/';
		File::load();
		$files = zipFiles2Array($dir,$file);
		
		$time = date('d.m.Y');
		$tag = 'update_backup_';
		File::rmdir($dir.$tag.$time.'/',false);
		if (!is_dir($dir.$tag.$time.'/')) {
			mkdir($dir.$tag.$time.'/');
			$ret = array();
			foreach ($files['tmp_name'] as $f) {
				$from = str_replace('files/temp/'.$name.'/','',$f);
				$ret[] = $from;
				$to = str_replace('files/temp/'.$name.'/','files/temp/'.$tag.$time.'/',$f);
				File::checkDir($to);
				copy($from,$to);
			}
		} else foreach ($files['tmp_name'] as $f) $ret[] = str_replace('files/temp/'.$name.'/','',$f);
		
		$from_dir = $dir.$name.'/';
		$to_dir = FTP_DIR_ROOT;
		$this->output['update_backup_dir'] = FTP_DIR_ROOT.'files/temp/'.$tag.$time.'/';

		FuncDirFileRecursive($from_dir, $to_dir, false, 'mkdir');
		FuncDirFileRecursive($from_dir, $to_dir, 'rename', false);	
		return $ret;
	}
	
	public function listing() {
		$this->output['update'] = AjaxelUpdate::getUpdate();
		if ($this->output['update']) {
			$this->output['latest_version'] = $this->output['update']->Version;
			$this->output['file'] = $this->output['update']->File;
			if (get('update')) {
				$file = $this->output['update']->get()->download()->update()->getFile();
				
				$this->output['files'] = $this->system_update($file);
				$this->output['sqls'] = Conf()->g('SQL_RESULT');
				DB::replace('settings',array(
					'template'	=> 'global',
					'name'		=> 'system_updated',
					'val'		=> $this->time,
				));
				Conf()->s('system_updated',$this->time);
			}
		} else {
			$this->output['latest_version'] = '<span style="color:red">Error, cannot get data from remote host</span>';
		}
		
		$this->output['system_updated'] = Conf()->g('system_updated');
	}
}










class AdminHelp_faq extends Admin {
	public function __construct() {
		$this->title = 'Frequently asked questions';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->table = 'help_faq';
		$this->idcol = 'id';
		
	}
	
	public function json() {
		switch ($this->action) {
			case 'save':
				$this->global_action('save');
			break;	
		}
	}
	
	public function listing() {
		
		
	}
	
	public function window() {
		$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
		$this->win('help_contents');	
	}
}







































