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
* @file       mod/AdminForum.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminForum extends Admin {
	
	public function __construct() {		
		parent::__construct(__CLASS__);
		$this->title = 'Forum';
	}
}


class AdminForum_categories extends Admin {
	public function __construct() {
		$this->title = 'Forum categories';
		parent::__construct(__CLASS__);		
		$this->table = 'forum_categories';
		$this->idcol = 'id';
	}

	public function json() {
		switch ($this->action) {
			case 'save':
				$this->global_action('save');
			break;
			case 'sort':
				$this->global_action();
			break;
		}
	}

	public function listing() {
		if ($this->catid && ($this->cat = DB::row('SELECT * FROM '.$this->prefix.'forum_categories WHERE id='.$this->catid))) {
			$this->filter .= ' AND parentid='.$this->catid;
		}
		elseif (get(self::KEY_CATID)!=self::KEY_ALL) {	
			$this->filter .= ' AND parentid=0';
		} else $this->catid = 0;
		
		if ($this->find) {
			$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';
		}
		
		$this->Tree = Factory::call('tree')->clear()->set(array(
			'table'			=> 'forum_categories',
			'select'		=> $s,
			'name_col_select'=> '(CASE WHEN threads>0 THEN CONCAT(title, \' (\',threads,\')\') ELSE title END) AS t',
			'name_col'		=> 't',
			'sort_col'		=> 'id',
			'sort_order'	=> 'ASC',
		))->selected($this->catid)->get();
		
		if ($this->submitted) {
			$this->allow('forum','save');
			foreach ($this->data as $id => $rs) {
				if (!$rs['title']) continue;
				if ($id<0) {
					$rs['parentid'] = $this->catid;
					DB::insert($this->table,$rs);
				} else {
					DB::update($this->table,$rs,$id);
				}
			}
			if (post('data_del')) {
				$this->allow('forum','delete');
				foreach (post('data_del') as $del) {
					$arr = $this->Tree->tree_array($del, true);
					foreach ($arr as $a) {
						DB::run('DELETE FROM '.$this->prefix.'forum_posts WHERE catid='.$a['id']);
						DB::run('DELETE FROM '.$this->prefix.'forum_threads WHERE catid='.$a['id']);
						DB::run('DELETE FROM '.$this->prefix.'forum_categories WHERE id='.$a['id']);
					}
				}
			}
		}
		
		$this->output['tree'] = $this->Tree->dir($this->catid, array(
			'link'	=> 'javascript:;',
			'attr'	=> ' onclick="S.A.L.get(\'?'.URL_KEY_ADMIN.'=forum&tab=categories&'.self::KEY_CATID.'={$id}\',\'\',\'categories\')"'
		));

		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY sort LIMIT '.$this->offset.','.$this->limit;
		$data = DB::getAll($sql);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data = json($data);
	}
	
	
	public function window() {
		
		$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
		$this->win('help_contents');
	}
}




class AdminForum_threads extends Admin {
	public function __construct() {
		$this->title = 'Forum threads';
		parent::__construct(__CLASS__);
	}
	public function init() {		
		$this->table = 'forum_threads';
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
		
		$this->Tree = Factory::call('tree')->clear()->set(array(
			'table'			=> 'forum_categories',
			'select'		=> $s,
			'name_col_select'=> '(CASE WHEN threads>0 THEN CONCAT(title, \' (\',threads,\')\') ELSE title END) AS t',
			'name_col'		=> 't',
			'sort_col'		=> 'id',
			'sort_order'	=> 'ASC',
		))->selected($this->catid)->get();
		
		
		$s = 'id, descr, catid, DATE_FORMAT(FROM_UNIXTIME(added), \'%H:%i %d-%b\') AS date, (SELECT CONCAT(\'<a href="javascript:" onclick="S.A.W.open(\\\'?'.URL_KEY_ADMIN.'=users&id=\',userid,\'\\\')">\',login,\'</a>\') FROM '.DB_PREFIX.'users u WHERE u.id=a.userid) AS user';
		
		if ($this->id && ($this->row = DB::row('SELECT title,views,posts,'.$s.' FROM '.$this->prefix.$this->table.' a WHERE id='.$this->id))) {
			if (get('do')=='delete') {
				DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				if (DB::affected()) {
					DB::run('DELETE FROM '.$this->prefix.'forum_posts WHERE setid='.$this->row['id']);
					if ($aff = DB::affected()) {
						DB::run('UPDATE '.$this->prefix.'forum_categories SET posts=posts-'.$aff.' WHERE id='.$this->row['catid']);
					}
					DB::run('UPDATE '.$this->prefix.'forum_categories SET threads=threads-1 WHERE id='.$this->row['catid']);
					return URL::redirect('?'.URL_KEY_ADMIN.'='.$this->admin.'&tab='.$this->tab.'&'.self::KEY_CATID.'='.$this->catid.'&load');
				}
			}
			$this->catid = $this->row['catid'];
			$this->json_row = json($this->row);
			if ($this->submitted && $this->data['descr']) {
				DB::insert('forum_posts',array(
					'setid'		=> $this->id,
					'catid'		=> $this->row['catid'],
					'descr'		=> Parser::parse('code_bb',$this->data['descr']),
					'userid'	=> $this->UserID,
					'added'		=> $this->time
				));
				$this->data = array();
			}
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$s.' FROM '.$this->prefix.'forum_posts a WHERE setid='.$this->row['id'].$this->filter.' ORDER BY id LIMIT '.$this->offset.','.$this->limit;
			$data = DB::getAll($sql);
			$this->total = DB::rows($data);
			$this->nav();
			$this->json_data = json($data);
			
		} else {			
			
			if ($this->catid && $this->submitted && $this->data['descr'] && $this->data['title']) {
				DB::insert($this->table,array(
					'catid'		=> $this->catid,
					'title'		=> $this->data['title'],
					'descr'		=> Parser::parse('code_bb',$this->data['descr']),
					'userid'	=> $this->UserID,
					'added'		=> $this->time
				));
				return URL::redirect('?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.DB::id().'&tab='.$this->tab.'&load');
			}
			if ($this->catid && ($this->cat = DB::row('SELECT * FROM '.$this->prefix.'forum_categories WHERE id='.$this->catid))) {
				$this->filter .= ' AND catid='.$this->catid;
			}
			if ($this->find) {
				$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';
			}
			$sql = 'SELECT SQL_CALC_FOUND_ROWS title,views,posts,'.$s.' FROM '.$this->prefix.$this->table.' a WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' LIMIT '.$this->offset.','.$this->limit;
			
			$data = DB::getAll($sql);
			$this->total = DB::rows($data);
			$this->nav();
			$this->json_data = json($data);
		}
	}
	
	public function window() {

		$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
		$this->win('help_contents');
	}
}




class AdminForum_posts extends Admin {
	public function __construct() {
		$this->title = 'Forum posts';
		parent::__construct(__CLASS__);
	}
	public function init() {		
		$this->table = 'forum_posts';
		$this->idcol = 'id';
	}
	
	public function json() {
		switch ($this->action) {
			case 'save':
				$this->global_action('save');
			break;
			case 'delete':
				$rs = DB::one('SELECT setid, catid FROM '.$this->prefix.'forum_posts WHERE id='.(int)$this->id);
				DB::run('DELETE FROM '.$this->prefix.'forum_posts WHERE id='.(int)$this->id);
				if (DB::affected()) {
					DB::run('UPDATE '.$this->prefix.'forum_categories SET posts=posts-1 WHERE id='.$rs['catid']);
					DB::run('UPDATE '.$this->prefix.'forum_threads SET posts=posts-1 WHERE id='.$rs['id']);
				}
			break;
		}
	}	
	
	public function listing() {
		
		$this->Tree = Factory::call('tree')->clear()->set(array(
			'table'			=> 'forum_categories',
			'select'		=> $s,
			'name_col_select'=> '(CASE WHEN posts>0 THEN CONCAT(title, \' (\',posts,\')\') ELSE title END) AS t',
			'name_col'		=> 't',
			'sort_col'		=> 'id',
			'sort_order'	=> 'ASC',
		))->selected($this->catid)->get();
		
		
		$s = 'id, descr, catid, DATE_FORMAT(FROM_UNIXTIME(added), \'%H:%i %d-%b\') AS date, (SELECT CONCAT(\'<a href="javascript:" onclick="S.A.W.open(\\\'?'.URL_KEY_ADMIN.'=users&id=\',userid,\'\\\')">\',login,\'</a>\') FROM '.DB_PREFIX.'users u WHERE u.id=a.userid) AS user';
		if ($this->catid && ($this->cat = DB::row('SELECT * FROM '.$this->prefix.'forum_categories WHERE id='.$this->catid))) {
			$this->filter .= ' AND catid='.$this->catid;
		}
		if ($this->find) {
			$this->filter .= ' AND descr LIKE \'%'.$this->find.'%\'';
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$s.' FROM '.$this->prefix.$this->table.' a WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' LIMIT '.$this->offset.','.$this->limit;
		$data = DB::getAll($sql);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data = json($data);
	}
	
	public function window() {
		$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
		$this->win('forum_posts');
	}
}



































