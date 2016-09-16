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
* @file       mod/Grid.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Grid extends Object {
	
	public function __construct(Index &$Index) {
		$this->Index =& $Index;
		$this->class = __CLASS__;
		parent::load($this->Index);
	}
	// TODO: array of names
	public function getContent() {

		if (!$this->name) return false;
		if ($this->id) {
			$sql = 'SELECT * FROM '.$this->prefix.'grid_'.$this->name.' WHERE id='.(int)$this->id;
			$this->row = DB::row($sql);
			$this->catchRow($this->row, true);
		}
		if (!$this->row) {
			$this->data['list'] = array();
			if (!isset($this->select[$this->name])) $this->select[$this->name] = '*';
			if (!isset($this->order[$this->name])) $this->order[$this->name] = 'sort, id DESC';
			if (!isset($this->filter[$this->name])) {
				$langed = in_array('rid',DB::columns('grid_'.$this->name));
				$this->filter[$this->name] = ' AND active=1'.($langed?' AND lang=\''.$this->lang.'\'':'');
			}
			$this->id = 0;
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$this->select[$this->name].' FROM '.$this->prefix.'grid_'.$this->name.' WHERE TRUE'.$this->filter[$this->name].' ORDER BY '.$this->order[$this->name];
			
			$qry = DB::qry($sql, $this->offset, $this->limit);
			$this->total = DB::rows();
			while ($row = DB::fetch($qry)) {
				$this->catchRow($row, false);
				array_push($this->data['list'], $row);
			}
			DB::free($qry);
		}
		$this->data['module'] = $this->name;
		$this->data['pager'] = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit,
		));
		if ($this->row) {
			$this->Index->tree[] = array(
				'title'	=> $this->row['alt'],
				'url'	=> '?'.URL::get(),
				'name'	=> $this->name,
				'type'	=> 'grid'
			);
			$this->Index->setVar('title',$this->row['alt']);
			// {$Tpl->setVar('title',$row.alt|html)}
			// {$Tpl->setVar('keywords',$row.descr|strip_tags)}
			$this->Index->setVar('keywords',$this->row['tags'] ? $this->row['tags'] : DB::keywords($this->row['descr']));
			$this->Index->Smarty->assign('row', $this->row);
			$this->Index->Smarty->assign('tree', $this->Index->tree);
		} else {
			$this->Index->Smarty->assign('data', $this->data);
		}
		$this->Index->Smarty->display('grid.tpl');
		
		return true;
	}
	
	private function catchRow(&$row, $full) {
		$row['module'] = $this->name;
		$row['url_open'] = '?grid='.$this->name.AMP.'id='.$row['id'];
		$row['alt'] = html($row['title']);
		$this->Index->Edit->set($row, 'grid_'.$this->name, $row['id'], 'id')->parse()->admin();
		if (isset($this->catch[$this->name]) && $this->catch[$this->name]) {
			if (is_array($this->catch[$this->name])) {
				$this->catch[$this->name][0]->{$this->catch[$this->name][1]}($row, $full);
			} else {
				$this->catch[$this->name]($row, $full);
			}
		}
	}
}