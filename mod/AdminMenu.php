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
* @file       mod/AdminMenu.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminMenu extends Admin {

	public function __construct() {
		$this->title = 'Menu manager';
		parent::__construct(__CLASS__);
	}
	public function init() {		
		$this->table = 'menu';
		$this->idcol = 'id';
		$this->Menu = Factory::call('menu', $this->classParams());
		$this->position = get('position','','[[:CACHE:]]');
		$this->id = (int)$this->id;
		$this->module = '';
	}
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$params = array(
			'func'		=> '',
			'func_arg1'	=> true,
			'admin'		=> true,
			'friendly_urls'	=> true,
			'search'	=> $this->find,
			'prefix'	=> $this->prefix,
			'parentid_key' => 'pid',
			'position_key' => 'p',
			'select'	=> 'id, parentid AS pid, position AS p, title_'.$this->lang.' AS t, name, sort, `display` AS d, options AS o, url, cnt AS c, cnt2 AS c2, cnt3 AS c3, active AS ac'
		);
		$this->data = $this->Menu->getAll($params)->toArray();

		$this->json_data = json($this->data, true);
		$this->nav = false;
	}
	
	public function json() {
		switch ($this->get) {
			case 'parent_menus':
				if (!post('position')) return false;
				$this->json_ret = array(0 => lang('$-- top menu --')) + $this->Menu->getParentMenus($this->Menu->positionName(post('position')));
			break;
			case 'url_name':
				$this->json_ret= array('name' => Parser::name(post('title')));
			break;
			case 'action':
				$this->action();
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.post('get'));
			break;
		}
	}
	private function validate($t) {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in and so as URL name');
		if (!$this->data['position']) $err['position'] = lang('$Position was not chosen, please select');
		elseif (self::is('new',$this->data['position'])) $err['position'] = lang('$Please do not call position name such as "%1"','#new');
		if (isset($this->data['name'])) {
			if (!$this->data['name']) $this->data['name'] = $this->data['title'];
			$this->data['name'] = Parser::name($this->data['name']);
		}
		if (!strlen(trim($this->data['name']))) {
			$err['name'] = lang('$URL name is empty');
		}
		elseif ($this->isReserved($this->data['name'])) {
			$err['name'] = lang('$This name %1 is actually reserved by system, please use another',$this->data['name']);
		}
		$id = false;
		if ($this->data['position']==@$this->rs['position'] && $this->data['parentid']==$this->rs['parentid']) {
			$id = $this->id;	
		}
		if ($this->data['name'] && DB::getNum('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE '.($id?'id!='.$id.' AND ':'').'name LIKE '.e($this->data['name']).' AND parentid='.(int)$this->data['parentid'].' AND active!=2 AND `position` LIKE '.e($this->data['position']))) {
			$err['name'] = lang('$Such URL name already exists in the same position and menu level');
		}
		if (@$this->rs['parentid']) {
			$this->set_msg(lang('$New sub menu %1 was added',$t),lang('$Sub menu %1 was updated',$t));
		} else {
			$this->set_msg(lang('$New menu element %1 was added',$t),lang('$Menu element %1 was updated',$t));				
		}
		$this->errors($err);
	}
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				
				if (post(self::KEY_LANG_ALL)) {
					foreach ($this->langs as $l => $a) {
						$this->data['title_'.$l] = $this->data['title'];
						$this->data['title2_'.$l] = $this->data['title2'];
						$this->data['descr_'.$l] = $this->data['descr'];
						$this->data['keywords_'.$l] = $this->data['keywords'];
					}
				} else {				
					$this->data['title_'.$this->lang] = $this->data['title'];
					$this->data['title2_'.$this->lang] = $this->data['title2'];
					$this->data['descr_'.$this->lang] = $this->data['descr'];
					$this->data['keywords_'.$this->lang] = $this->data['keywords'];
				}
				if ($this->data('groupids')) $this->data['groupids'] = ',g'.join(',g',$this->data['groupids']).',';
				else $this->data['groupids'] = '';
				if ($this->data('classids')) $this->data['classids'] = ',c'.join(',c',$this->data['classids']).',';
				else $this->data['classids'] = '';
				$this->data['options'] = str_replace(',,',',',$this->data['groupids'].$this->data['classids']);
				$cols = array('title','title2','descr','keywords');
				$s = array();
				foreach ($this->langs as $l => $a) {
					foreach ($cols as $c) {
						$s[] = $c.'_'.$l;	
					}
				}
				$s[] = '`position`, parentid';
				$select = join(', ',$s);
				if ($this->id!=self::KEY_NEW && $this->id && ($this->rs = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id))) {
					$this->data['title_'.$this->lang] = $this->data['title'];
					$this->data['title2_'.$this->lang] = $this->data['title2'];
					$this->data['descr_'.$this->lang] = $this->data['descr'];
					$this->data['keywords_'.$this->lang] = $this->data['keywords'];
					if ($this->isEdit()) break;
					$t = $this->rs['title_'.$this->lang];
				} else {
					$this->data['cnt'] = 0;
					$this->data['cnt2'] = 0;
					$this->data['cnt3'] = 0;
					$t = $this->data['title_'.$this->lang];
					$this->rs = array();
					$this->id = 0;
				}
				$this->allow('menu',($this->id?'edit':'save'),$this->rs,$this->data);
				$this->validate($t);
				foreach ($this->langs as $l => $a) {
					foreach ($cols as $c) {
						if (!isset($this->rs[$c.'_'.$l]) || !$this->rs[$c.'_'.$l]) $this->data[$c.'_'.$l] = $this->data[$c];
					}
				}
				unset($this->data['title'], $this->data['title2'], $this->data['descr'], $this->data['keywords']);
				$this->global_log = false;
				$this->global_action('save');
				if ($this->affected) {
					if ($this->data['position'] && $this->data['position']!=$this->rs['position'] && $this->affected) {
						DB::run('UPDATE '.$this->prefix.$this->table.' SET `position`='.e($this->data['position']).' WHERE parentid='.$this->id);
					}
					if ($this->data['parentid'] && !$this->rs['parentid']) {
						DB::run('UPDATE '.$this->prefix.$this->table.' SET `parentid`=0 WHERE `parentid`='.(int)$this->id.' AND position LIKE '.e($this->data['position']));	
					}
				}
				$this->global_action('msg');
				$new = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				$this->log($this->id, $new, $this->rs);
			break;
			case 'add':
				$this->action('save');
				if ($this->updated) {
					$this->msg_close = false;
					$this->msg_js = 'S.A.W.close();S.A.W.go(\'?'.URL_KEY_ADMIN.'=menu&add=new&position='.$this->data['position'].'\');';
				}
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'copy':
				if (!$this->id) break;
				$this->data['title'] .= ' (copy)';
				$this->data['name'] .= '_copy';
				$this->action('save');
			break;
			case 'act':
				$this->allow('menu','activate');
				$this->global_action();
			break;
			case 'sort':
				$this->allow('menu','sort');
				$this->global_action();
			break;
			case 'delete':
				if (!$this->id) break;
				if ($this->isEdit()) break;
				$this->rs = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				$old = array();
				$old['top'] = $this->rs;
				$old['sub'] = array();
				$sub = DB::getAll('SELECT * FROM '.$this->prefix.$this->table.' WHERE id!='.$this->id.' AND parentid='.$this->id);
				foreach ($sub as $s) {
					$old['sub'][$this->table.':'.$s['id']] = $s;
				}
				if (!$old['sub']) $old = $old['top'];
				$this->allow('menu','delete',$this->rs);
				DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE id='.$this->id.' OR parentid='.$this->id);
				$this->affected = DB::affected();
				if ($this->affected) {
					$this->updated = Site::ACTION_DELETE;
					if ($this->rs['parentid']) {
						$this->msg_text = lang('$Sub menu element %1 was deleted',$this->rs['title_'.$this->lang]);
					} else {
						if ($this->affected>1) {
							$this->msg_delay = 3000;
							$this->msg_text = lang('$Menu element %1 with %2 submenus were deleted',$this->rs['title_'.$this->lang],$this->affected - 1);
						} else {
							$this->msg_text = lang('$Menu element %1 was deleted',$this->rs['title_'.$this->lang]);	
						}
					}
					$this->log($this->id, false, $old);
				}
				else {
					$this->msg_text = lang('$Cannot delete menu element: %1, it does not exist in database',$this->id);
					$this->msg_delay = 1500;
					$this->msg_type = 'warning';
				}
			break;
			default:
				$this->msg_reload = false;
				$this->msg_text = 'Action: \''.$this->action.'\' is not defined';
				$this->msg_delay = 3000;
				$this->msg_type = 'fatal';
			break;
		}
	}

	public function window() {
		$allow = Allow()->admin($this->table, 'view', false, false, $this->table, $this->id);
		if ($allow) {
			$this->inc('allow', array('allow' => $allow));
			return;	
		}
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT id, `position`, name, icon, parentid, target, display, options, url, sort, userid, edited, title_'.$this->lang.' AS title, title2_'.$this->lang.' AS title2, active, descr_'.$this->lang.' AS descr, keywords_'.$this->lang.' AS keywords FROM '.$this->prefix.$this->table.' WHERE id='.$this->id.' AND active!=2');
		}
		if (!$this->post || !$this->post['id']) {
			$this->id = 0;
			$this->post = post('data');
			if ($this->position) $this->post['position'] = $this->position;
			else $this->post['position'] = $this->Menu->getPosition();
			$this->post['sort'] = DB::row('SELECT (MAX(sort)+1) AS s FROM '.$this->prefix.$this->table.' WHERE `position` LIKE '.e($this->post['position']),'s');
	//		$this->post['position'] = request('position');
		} else {
			$this->isEdit();
			$options = explode(',',trim($this->post['options'],','));
			$this->post['groupids'] = array();
			$this->post['classids'] = array();
			foreach ($options as $o) {
				$f = substr($o,0,1);
				$v = substr($o,1);
				if ($f=='c') $this->post['classids'][] = $v;
				elseif ($f=='g') $this->post['groupids'][] = $v;
			}
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
			if (!$this->post['name']) $this->post['name'] = Parser::name($this->post['title']);
		}
		$this->win('menu');
	}
}