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
* @file       mod/AdminLog.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminLog extends Admin {

	public function __construct() {
		$this->title = 'Log';
		parent::__construct(__CLASS__);
	}
	public function init() {		
		$this->table = 'log';
		$this->array['dropdown'] = array(
			 Site::ACTION_INSERT => lang('$Insert action'),
			 Site::ACTION_UPDATE => lang('$Update action'),
			 Site::ACTION_DELETE => lang('$Delete action'),
			 Site::ACTION_ERROR => lang('$Error action'),
			 Site::ACTION_UNKNOWN => lang('$Unknown')
		);
		if (USE_LOG>1 && !@$_SESSION['log_deleted']) {
			DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE added < '.($this->time - USE_LOG * 86400));
			$_SESSION['log_deleted'] = true;
		}
	}

	private function fixRS(&$rs) {
		if (!$rs['title']) {
			switch ($rs['action']) {
				case  Site::ACTION_INSERT:
					$rs['title'] = lang('New entry was added',$rs['setid']);
				break;
				case  Site::ACTION_UPDATE:
					$rs['title'] = lang('Entry ID: %1 was updated',$rs['setid']);
				break;
				case  Site::ACTION_DELETE:
					$rs['title'] = lang('Entry ID: %1 was deleted',$rs['setid']);
				break;
				case  Site::ACTION_ERROR:
					$rs['title'] = 'Error';
				break;
				case  Site::ACTION_UNKNOWN:
					$rs['title'] = 'Unknown';
				break;
			}
		}
		$table = $rs['table'];
		$rs['time'] = '<span title="'.date('d.m.Y H:i',$rs['added']).'">'.Date::timeAgo($rs['added']).' ago</span>';
		$rs['time2'] = date('d.m.Y H:i',$rs['added']);
		$rs['table'] = DB::remPrefix($rs['table']);
		if (isset($rs['data']) && $rs['action']!=Site::ACTION_UNKNOWN && $rs['action']!=Site::ACTION_ERROR) {
			$rs['data'] = strexp($rs['data']);
			
			if ($rs['data'] && is_array($rs['data'])) {
				$idcol = 'id';
				if ($table=='users_profile') $idcol = 'setid';
				$rs['data']['cur'] = DB::row('SELECT * FROM `'.DB::prefix($table).'` WHERE `'.$idcol.'`='.$rs['setid']);
			} else {
				$rs['data'] = array(
					'old'	=> array(),
					'cur'	=> array(),
				);
			}
		}
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
		}
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		$this->id = get('id');
		if (!$this->id) $this->id = request('id');
		switch ($this->action) {
			case 'save':
				$this->allow($this->table,'revert');
				$rs = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
				$this->fixRs($rs);
				if (!$rs) break;
				
				$idcol = 'id';
				if ($rs['table']=='users_profile') $idcol = 'setid';
				
				$ser = strexp($rs['data']);
				$this->msg_text = 'alloha';
				switch ($rs['action']) {
					case Site::ACTION_INSERT:
						DB::run('DELETE FROM `'.DB::prefix($rs['table']).'` WHERE `'.$idcol.'`='.$rs['setid']);
						DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
						$this->name = DB::remPrefix($rs['table']);
						$this->deleteFiles($rs['setid']);
						DB::run('DELETE FROM '.$this->prefix.'changes WHERE `table`=\''.$rs['table'].'\' AND setid='.$rs['setid']);
						if ($rs['table']=='content') {
							DB::run('DELETE FROM '.$this->prefix.'comments WHERE setid='.$rs['setid'].' AND `table`=\'\'');
							DB::run('DELETE FROM '.$this->prefix.'views WHERE setid='.$rs['setid']);
							DB::run('DELETE FROM '.$this->prefix.'rates WHERE setid='.$rs['setid']);
						}
						if (substr($rs['table'],0,8)=='content_') {
							$setid = DB::one('SELECT setid FROM `'.DB::prefix($rs['table']).'` WHERE `'.$idcol.'`='.$rs['setid']);
							$this->affected = Site::ACTION_DELETE;
							$this->global_action('reorder_content',$setid);
						}
						$this->msg_text = lang('Log entry: "%1" was reverted. Entry was dropped', $rs['title']);
					break;
					case Site::ACTION_UPDATE:
						$ex = DB::row('SELECT 1 FROM `'.DB::prefix($rs['table']).'` WHERE `'.$idcol.'`='.$rs['setid']);
						$data = $ser['old'];
						if ($data) DB::replace($rs['table'],$data);
						DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id.' OR (`table`=\''.DB::prefix($rs['table']).'\' AND `setid`='.$rs['setid'].' AND `added`>'.$rs['added'].')');
						DB::run('DELETE FROM '.$this->prefix.'changes WHERE `table`=\''.$rs['table'].'\' AND setid='.$rs['setid'].' AND added>'.$rs['added']);
						if ($rs['table']=='content') {
							DB::run('DELETE FROM '.$this->prefix.'comments WHERE setid='.$rs['setid'].' AND added>'.$rs['added'].' AND `table`=\'\'');
						}
						$this->msg_text = lang('Log entry: "%1" was reverted. Entry was updated', $rs['title']);
					break;
					case Site::ACTION_DELETE:
						$ex = DB::row('SELECT 1 FROM `'.DB::prefix($rs['table']).'` WHERE `'.$idcol.'`='.$rs['setid']);
						$data = $ser['old'];
						$sub = false;
						if (!$data) break;
						if (isset($data['top']) && isset($data['sub'])) {
							$sub = $data['sub'];
							$data = $data['top'];
						}
						DB::replace($rs['table'],$data);
						if ($sub) {
							foreach ($sub as $table_id => $d) {
								$ex = explode(':',$table_id);
								DB::replace($ex[0], $d);
							}
						}
						DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
						
						if (substr($rs['table'],0,8)=='content_') {
							$setid = DB::one('SELECT setid FROM `'.DB::prefix($rs['table']).'` WHERE `'.$idcol.'`='.$rs['setid']);
							$this->affected = Site::ACTION_INSERT;
							$this->global_action('reorder_content',$setid);
						}
						$this->msg_text = lang('Log entry: "%1" was reverted. Entry was inserted', $rs['title']);
					break;
				}
			break;
			case 'delete':
				if (!$this->id) break;
				if ($this->isEdit()) break;
				$this->allow($this->table,'delete');
				$this->rs = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.(int)$this->id);
				if (($this->rs==Site::ACTION_UNKNOWN || $this->rs==Site::ACTION_ERROR) && $this->UserID!=SUPER_ADMIN) {
					break;
				}
				DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.(int)$this->id);
				if ($this->rs==Site::ACTION_UNKNOWN || $this->rs==Site::ACTION_ERROR) {
					break;
				}
				$this->name = DB::remPrefix($this->rs['table']);
				if ($this->name && $this->rs['action']==Site::ACTION_DELETE) {
					$this->deleteFiles($this->rs['setid']);
				}
				if ($this->rs['table']=='content') {
					DB::run('DELETE FROM '.$this->prefix.'comments WHERE setid='.$this->rs['setid'].' AND `table`=\'\'');
					DB::run('DELETE FROM '.$this->prefix.'views WHERE setid='.$this->rs['setid']);
					DB::run('DELETE FROM '.$this->prefix.'rates WHERE setid='.$this->rs['setid']);
				}
				$this->affected = DB::affected();
				$this->updated = Site::ACTION_DELETE;
				$this->fixRS($this->rs);
				$this->msg_text = lang('$Log entry: "%1" has been destroyed',$this->rs['title']);
				$this->msg_delay = 2500;
				$this->log($this->id, false, $this->rs);
			break;
		}
	}
	
	public function sql() {
		$this->select = 'id, setid, `table`, action, template, title, changes, userid, added';
		$this->order = '`id` DESC';
		$this->limit = 30;
		if ($this->type && array_key_exists($this->type, $this->array['dropdown'])) {
			$this->filter .= ' AND `action`='.$this->type;	
		}
		$this->filter .= ' AND template='.e($this->tpl);
		if ($this->find) {
			if (is_numeric($this->find)) {
				$this->filter .= ' AND (id='.$this->find.' OR setid='.$this->find.')';
			} else {
				$ex = explode(':',$this->find);
				if (count($ex)==2 && is_numeric($ex[1])) {
					$this->filter .= ' AND (`table`='.e($this->current['PREFIX'].$ex[0]).' OR `table`='.e($ex[0]).') AND setid='.(int)$ex[1];
				} else {
					$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR `data` LIKE \'%'.$this->find.'%\')';
				}
			}
		}
		$this->offset = $this->page * $this->limit;
	}
	
	public function action_types($int = 0, $x = 0) {
		$ret = array(
			Site::ACTION_INSERT => array('green',lang('$Insert')),
			Site::ACTION_UPDATE => array('silver',lang('$Update')),
			Site::ACTION_DELETE => array('maroon',lang('$Delete')),
			Site::ACTION_ERROR => array('red',lang('$Error')),
			Site::ACTION_UNKNOWN => array('magenta',lang('$Unknown')),
		);
		return $int ? $ret[$int][$x] : $ret;
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->allow($this->table,'list');
		$this->sql();
		$this->json_array['action_types'] = json($this->action_types());

		$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$this->select.' FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql, $this->offset, $this->limit);
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			$this->fixRS($rs);
			$this->data[] = $rs;
		}
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function window() {
		$allow = Allow()->admin($this->table, 'view', false, false, $this->table, $this->id);
		if ($allow) {
			$this->inc('allow', array('allow' => $allow));
			return;	
		}
		$this->post = DB::row('SELECT a.*, (SELECT CONCAT(b.login,\'|\',b.groupid) FROM '.DB_PREFIX.'users b WHERE b.id=a.userid) AS user FROM '.DB_PREFIX.$this->table.' a WHERE a.id='.$this->id);
		$ex = explode('|',$this->post['user']);
		$this->post['user'] = $ex[0];
		$this->post['groupid'] = $ex[1];
		$this->fixRS($this->post);
		$this->win('log');
	}
}