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
* @file       mod/AdminCategories.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminCategories extends Admin {
	
	public function __construct($params = array()) {
		$this->title = 'Categories';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->output = array();
		$this->tables = DB::tables();
		$this->category_modules = Site::getModules('category');
		if (!$this->module || !array_key_exists($this->module, $this->category_modules)) {
			$this->module = key($this->category_modules);
		}
		else $this->options['descr'] = false;
		$this->button['save'] = true;
		$this->module_data = $this->category_modules[$this->module];
		if ($this->module_data['table']) $this->table = $this->module_data['type'].'_'.$this->module_data['table'];
		else $this->table = '';
		if (in_array('descr_'.$this->lang, DB::columns($this->table))) $this->options['descr'] = true;
		$this->order = 'c.sort, c.catname_'.$this->lang;
		if (get('update')=='cnt') {
			$this->updateCounts();
		}
	}
	
	
	public function json() {
		switch (post('get')) {
			case 'update_counts':
				$this->updateCounts();
			break;	
		}
	}
	
	private function updateCounts() {
		return false;
		/*
		$up = $up2 = $sel = $sum = array();
		foreach ($this->langs as $l => $a) {
			$up[] = 'cnt_'.$l.'=0, sum_'.$l.'=0';
			$up2[] = 'cnt_'.$l.'=cnt_'.$l.'+1';
			$sel[] = 'cnt_'.$l;
			$sum[] = '(SELECT SUM(b.cnt_en) FROM '.$this->prefix.$this->table.' b WHERE b.catref=a.catref OR b.catref LIKE CONCAT(a.catref,\'.%\')) AS s_'.$l;
		}
		DB::run('UPDATE '.$this->prefix.$this->table.' SET '.join(', ',$up));
		$sql = 'SELECT catref FROM '.$this->prefix.'content_'.$this->module.' GROUP BY rid';
		$qry = DB::qry($sql,0,0);
		while ($rs = DB::fetch($qry)) {
			$sql = 'UPDATE '.$this->prefix.$this->table.' SET '.join(', ',$up2).' WHERE catref=\''.$rs['catref'].'\'';
			DB::run($sql);
		}
		DB::free($qry);
		$sql = 'SELECT a.catid, a.catref, '.join(', ',$sum).' FROM '.$this->prefix.$this->table.' a WHERE a.cnt_en>0 ORDER BY a.catref';
		$qry = DB::qry($sql,0,0);
		while ($rs = DB::fetch($qry)) {
			$up = array();
			foreach ($this->langs as $l => $a) {
				$up[] = 'sum_'.$l.' = '.$rs['s_'.$l];
			}
			DB::run('UPDATE '.$this->prefix.$this->table.' SET '.join(', ',$up).' WHERE catid='.$rs['catid']);
		}
		$this->msg_text = 'Counts are updated';
		*/
	}
	
	public function sql() {
		$this->select = 'c.catid, c.catref, ';
		foreach ($this->langs as $l => $a) {
			$this->select .= 'c.catname_'.$l.', ';
			if (isset($this->options['descr']) && $this->options['descr']) {
				$this->select .= 'c.descr_'.$l.', ';
			}
		}
		$this->select .= 'c.icon, c.sort, c.name, c.hidden';
		$this->filter = '';
		if ($this->find) {
			$j = array();
			$j[] = 'c.name LIKE \'%'.$this->find.'%\'';
			foreach ($this->langs as $l => $a) {
				$j[] = 'c.catname_'.$l.' LIKE \'%'.$this->find.'%\'';
			}
			$this->filter .= ' AND ('.join(' OR ', $j).')';
		}
		if ($this->parent_catref) {
			$this->filter .= ' AND c.catref REGEXP \'^'.self::strRegex($this->parent_catref).'\.([0-9]+)$\'';
		} else {
			$this->filter .= ' AND c.catref NOT LIKE \'%.%\'';
		}
		$this->select .= ', (SELECT COUNT(1) FROM '.$this->prefix.$this->table.' mc WHERE mc.catref LIKE CONCAT(c.catref,\'.%\')) AS sublevels';
	}
	
	public static function strRegex($catref) {
		return str_replace('.','\.',e($catref,false));
	}
	
	public function listing() {
		if (!$this->table) {
			$this->data = false;
			$this->json_data = json($this->data);
			return;
		}
		$params = array(
			'table'		=> $this->table,
			'lang'		=> $this->lang,
			'catalogue'	=> false,
			'retCount'	=> false,
			'getAfter'	=> false,
			'noDisable'	=> true,
			'maxLevel'	=> 0,
			'optValue'	=> 'catid',
			'getHidden'	=> true,
			'selected'	=> $this->catid,
			'prefix'	=> $this->prefix,
			'order'		=> 'c.sort, catname'
		);
		$Category = Factory::call('category', $params);
		
		if (!$this->isEdit() && $this->submitted) {
			if (!Allow()->admin('categories','save')):
				foreach (post('new_cat','',array()) as $i => $a) {
					$data = array();
					foreach ($this->langs as $l => $x) {
						$v = $a[$l];
						if (!strlen(trim($v))) {
							foreach ($this->langs as $_l => $_x) {
								if (strlen(trim($a[$_l]))) {
									$data['catname_'.$l] = trim($a[$_l]);
									break;
								}
							}
						} else {
							$data['catname_'.$l] = trim($v);
						}
					}
					if (!$data) continue;
					$data['icon'] = trim(post('new_icon',$i));
					$data['sort'] = trim(post('new_sort',$i));
					$data['name'] = trim(post('new_name',$i));
					if (!$data['name']) $data['name'] = Parser::name($data['catname_'.DEFAULT_LANGUAGE]);
					$data['hidden'] = '0';
					DB::insert($this->table, $data);
					$id = DB::id();
					$data = array();
					if ($this->parent_catref) {
						$data['catref'] = $this->parent_catref.'.'.$id;
					} else {
						$data['catref'] = $id;
					}
					DB::update($this->table, $data, $id, 'catid');
				}
			endif;
			if (!Allow()->admin('categories','edit')):
				foreach (post('catname','',array()) as $catid => $a) {
					$data = array();
					foreach ($this->langs as $l => $x) {
						$data['catname_'.$l] = trim($a[$l]);
					}
					$data['icon'] = trim(post('icon',$catid));
					$data['sort'] = trim(post('sort',$catid));
					$data['name'] = trim(post('name',$catid));
					if (!$data['name'] || $data['name']==='null') $data['name'] = Parser::name($data['catname_'.DEFAULT_LANGUAGE]);
					$data['hidden'] = intval(post('hidden',$catid));
					DB::update($this->table, $data, $catid, 'catid');
				}
			endif;
			if (!Allow()->admin('categories','delete')):
				foreach (post('d','',array()) as $catid => $to_del) {
					if (!$to_del) continue;
					$catref = Category::catRef($catid, $this->table);
					DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE catref='.e($catref).' OR catref LIKE '.e($catref.'.%'));
				}
			endif;
		}
		$this->category_options = $Category->getAll()->toOptions();
		$this->output['category_links'] = $Category->tree($this->catref, $this->table);
		$a = array();

		foreach ($this->output['category_links'] as $c) {
			if ($c['catid']==$this->catid) {
				$a[] = $c['catname'];
			} else {
				$a[] = '<a href="javascript:;" onclick="S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->name.'&'.self::KEY_MODULE.'='.$this->module.'&'.self::KEY_CATID.'='.$c['catid'].'\');">'.$c['catname'].'</a>';
			}
		}
		$this->output['category_links_html'] = join(' :: ',$a);
		$this->sql();
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$this->select.' FROM '.$this->prefix.$this->table.' c WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;

		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		$max_sort = 1;
		while ($rs = DB::fetch($qry)) {
			$rs['c'] = array();
			if ($this->options['descr']) $rs['d'] = array();
			foreach ($this->langs as $l => $a) {
				$rs['catname'][$l] = strform($rs['catname_'.$l]);
				if ($this->options['descr']) {
					$rs['descr'][$l] = strform($rs['descr_'.$l]);
				}
				$rs['icon'] = strform($rs['icon']);
				unset($rs['catname_'.$l]);
				if ($this->options['descr']) {
					unset($rs['descr_'.$l]);
				}
			}
			if ($rs['sort'] > $max_sort) $max_sort = $rs['sort'];
			$this->data[] = $rs;	
		}
		
		$this->output['max_sort'] = $max_sort;
		$this->json_data = json($this->data);
		$this->nav();
	}
}