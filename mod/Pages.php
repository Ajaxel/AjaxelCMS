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
* @file       mod/Pages.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Pages extends Object {

	public $page_data = array();

	public function __construct() {
		
	}
	
	public function set($params = array()) {
		$this->table = 'pages';
		if ($params) foreach ($params as $k => $v) $this->$k = $v;
		if (!$this->select) {
			$this->select = 'm.id, m.parentid, m.title_'.$this->lang.' AS title, m.title2_'.$this->lang.' AS title2, m.descr_'.$this->lang.' AS descr, m.keywords_'.$this->lang.' AS keywords, icon, name, target, url, cnt, sum';
		}
		$this->Index = Index();
		if ($this->admin) $this->and_active = ' AND active!=2';
		else $this->and_active = $this->Index->sql('menu_active');
		return $this;
	}
	
	public function init($params = array()) {
		$this->set($params);
		return $this;	
	}
	
	public function getContent($print = true, $cache = false) {
		if ($this->id) {
			if (!$this->row) $this->row = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id.' AND active=1');
			$this->catchRow($this->row, true);
			$this->Index->Session->setViews($this->row['id'],$this->table);
			
		}
		if (!$this->row && $this->Index->menu[9]['id']) {
			$this->data['list'] = array();
			$this->id = 0;
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.$this->table.' WHERE (treeid='.$this->Index->menu[9]['id'].' OR (treeids!=\'\' AND treeids LIKE \'%,'.$this->Index->menu[9]['id'].',%\')) AND lang=\''.$this->lang.'\' AND active=1';
			$qry = DB::qry($sql,$this->offset,$this->limit);
			$this->total = DB::rows();
			while ($row = DB::fetch($qry)) {
				$this->catchRow($row, false);
				array_push($this->data['list'], $row);
			}
			DB::free($qry);
			
			if (!$this->data['list']) return false;
			$this->data['pager'] = Pager::get(array(
				'total'	=> $this->total,
				'limit'	=> $this->limit,
			));
		}
		
		if ($print) {
			$this->catchPage();
			$this->Index->Smarty->assign('page',$this->page_data);
			if ($this->id) {
				$this->Index->Smarty->assign('row',$this->row);
			} else {
				$this->Index->Smarty->assign('list',true);
				$this->Index->Smarty->assign('data',$this->data);
			}
			
			if ($cache) {
				$html = $this->Index->Smarty->fetch('pages.tpl');
				SmartyCache::save($cache_name,$html,true);
				echo $html;
			} else {
				$this->Index->Smarty->display('pages.tpl');
			}
		} else return ($this->id ? $this->row : $this->data);
		
		return true;
	}
	
	private function catchPage() {
		if ($this->id) $index = 9;
		else $index = count($this->Index->menu)-4; // previous is 4, ugly, will be changed soon
		$this->page_data['url_back'] = $this->Index->menu[$index]['url'];
		$this->page_data['name_back'] = $this->Index->menu[$index]['title'];
		
	}
	
	private function catchRow(&$row, $full) {
		$row['table'] = $this->table;
		$row['url_open'] = $this->Index->menu[9]['url'].AMP.'id='.$row['id'];
		$row['alt'] = html($row['title']);
		$this->Index->Edit->set($row, 'pages', $row['id'], 'id')->parse()->admin();
		if (isset($this->catch[$this->name]) && $this->catch[$this->name]) {
			if (is_array($this->catch[$this->name])) {
				$this->catch[$this->name][0]->{$this->catch[$this->name][1]}($row, $full);
			} else {
				$this->catch[$this->name]($row, $full);
			}
		}	
	}
	
	
	public function findAll() {
		$row = DB::row('SELECT '.$this->select.', `position` FROM '.$this->prefix.'tree m WHERE name='.e(url(0)).$this->and_active);
		if (!$row) return false;
		$position = $row['position'];
		$url = $row['name'];
		if (!$row['url']) {
			$row['url'] = '?'.$url;
		}
		$this->Index->tree = array();
		$this->Index->menu = array();
		$this->Index->tree[] = array(
			'title'	=> $row['title'],
			'name'	=> $row['name'],
			'url'	=> $row['url'],
			'type'	=> 'tree'
		);
		$this->Index->menu[] = $row;
		foreach (Site::$URL_KEYS as $i => $key) {
			if (!$i) continue;
			$sql = 'SELECT '.$this->select.', `position` FROM '.$this->prefix.'tree m WHERE m.position='.e($position).' AND m.parentid='.$row['id'].' AND m.name='.e($key).$this->and_active;
			$row = DB::row($sql);
			if (!$row || $i==8) break;
			$url .= AMP.$row['name'];
			if (!$row['url']) {
				$row['url'] = '?'.$url;
			}
			$this->Index->tree[] = array(
				'title'	=> $row['title'],
				'name'	=> $row['name'],
				'url'	=> $row['url'],
				'type'	=> 'tree'
			);
			$this->Index->menu[] = $row;
		}
		$this->Index->menu[9] = $this->Index->menu[count($this->Index->menu)-1];
		
		
		$this->Index->menu['sub'] = array();
		$qry = DB::qry('SELECT '.$this->select.', `position` FROM '.$this->prefix.'tree m WHERE m.position='.e($position).' AND m.parentid='.$this->Index->menu[9]['id'].$this->and_active,0,0);
		while ($row = DB::fetch($qry)) {
			if (!$row['url']) {
				$row['url'] = '?'.$url.AMP.$row['name'];
			}
			array_push($this->Index->menu['sub'],$row);
		}
		DB::free($qry);
		

	//	p($this->Index->menu['sub']);
		
		
		if ($this->id && ($this->row = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id.' AND active=1'))) {
			$this->Index->tree[] = array(
				'title'	=> $this->row['title'],
				'name'	=> $this->row['id'],
				'url'	=> $this->Index->menu[9]['url'].'&id='.$this->row['id'],
				'type'	=> 'pages'
			);
		}
		
	}
	
	/*
	public function _findAll() {
		$position = DB::one('SELECT `position` FROM '.$this->prefix.'tree m WHERE name='.e(url(0)));
		if (!$position) return false;
		$qry = DB::qry('SELECT '.$this->select.' FROM '.$this->prefix.'tree m WHERE `position`='.e($position).' AND name IN ('.join(', ',e(Site()->URL_KEYS)).') ORDER BY FIELD(\'menu\', '.join(', ',e(Site()->URL_KEYS)).')',0,10);
		$url = '';
		$i = 0;
		while ($t = DB::fetch($qry)) {
			$url .= ($i?AMP:'').$t['name'];
			if (!$t['url']) {
				$t['url'] = '?'.$url;
			}
			$this->Index->tree[$i] = array(
				'title'	=> $t['title'],
				'name'	=> $t['name'],
				'url'	=> $t['url'],
				'type'	=> 'tree'
			);
			$this->Index->menu[$i] = $t;
			$i++;
		}
		DB::free($qry);		
	}
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}