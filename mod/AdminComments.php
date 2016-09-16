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
* @file       mod/AdminComments.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminComments extends Admin {

	protected $content_modules = array();

	public function __construct() {
		$this->title = 'Comments manager';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->table = 'comments';
		$this->content_modules = Site::getModules($this->table);		
		$this->id = (int)$this->id;
		$this->idcol = 'id';
	}
	
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		$this->id = get('id');
		if (!$this->id) $this->id = request('id');
		$this->module = '';
		switch ($this->action) {
			case 'save':
				if (!$this->id) {
					break;	
				}
				if ($this->isEdit()) break;
				$this->allow('email',($this->id?'edit':'save'));
				$this->data['body'] = $this->parse($this->data['original'],'code_bb');
				$this->global_log = true;
				$this->set_msg(false,lang('$Comment %1 was updated',$this->data['subject']));
				$this->global_action('save');
			break;
			case 'act':
				$this->global_action('act');
			break;
			case 'edit':
				if (!$this->table || !$this->id || !post('column') || !post('value')) break;
				$val = trim(post('value'));
				$sql = 'UPDATE '.$this->prefix.$this->table.' SET `original`='.e($val).', body='.e(Parser::parse('code_bb', $val)).' WHERE id='.(int)$this->id;
				DB::run($sql);
			break;
			case 'delete':
				$this->global_action('delete');
				if ($this->affected && $this->rs && $this->rs['setid']) {
					if ($this->rs['table']=='' || $this->rs['table']=='content') {
						DB::run('UPDATE '.$this->prefix.'content SET comments=comments-1 WHERE id='.$this->rs['setid']);
					}
					elseif (in_array('comments',DB::columns($this->rs['table']))) {
						DB::run('UPDATE '.$this->prefix.$this->rs['table'].' SET comments=comments-1 WHERE id='.$this->rs['setid']);
					}
				}
				$this->json_ret = array(
					'js'	=> '$(\'#a-comment_'.$this->name.'_'.$this->id.'\').hide().next().hide();'
				);
			break;
		}
	}
	
	public function sql() {
		$this->order = 'id DESC';
		$this->limit = 30;
		if ($this->find) {
			$this->filter .= ' AND (subject LIKE \'%'.$this->find.'%\' OR original LIKE \'%'.$this->find.'%\')';	
		}
		$this->offset = get('p')*$this->limit;
	}
	

	
	public function listing() {
		$this->data = array();
		$this->button['save'] = false;
		$this->button['add'] = false;
		$this->sql();
	//	$sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, a.subject, a.name AS user, c.menuid, a.email, c.name, a.body, a.ip, DATE_FORMAT(FROM_UNIXTIME(a.added), \'%H:%i %d %b %Y\') AS added, a.active FROM '.$this->prefix.$this->table.' a INNER JOIN '.$this->prefix.'content c ON c.id=a.setid WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, setid, subject, `table`, name AS user, email, body, ip, DATE_FORMAT(FROM_UNIXTIME(added), \'%H:%i %d %b %Y\') AS added, active, userid FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		
		$Menu = Factory::call('menu');
		
		while ($rs = DB::fetch($qry)) {
			if ($rs['table']=='content' || !$rs['table']) {
				$r = DB::row('SELECT menuid, name, title_'.$this->lang.' FROM '.$this->prefix.'content WHERE id='.$rs['setid']);
				$menu = $Menu->get($r['menuid']);
				$rs['url'] = $menu[0]['url'].AMP.$r['name'].'#c'.$rs['id'];
				$rs['name'] = $r['title_'.$this->lang];
			} else {
				if (in_array('title',DB::columns($rs['table']))) {
					$r = DB::select($rs['table'],$rs['setid'],'title');
					$rs['name'] = $r['title'];
					if ($rs['name']==$rs['subject']) $rs['name'] = '';
					$rs['url'] = '';
				} else {
					$rs['url'] = '';
					$rs['name'] = '';	
				}
			}
			
			$rs['ip_real'] = long2ip($rs['ip']);
			$rs['blocked'] = DB::one('SELECT 1 FROM '.DB_PREFIX.'ipblocker WHERE ip='.e($rs['ip_real']));
			$rs['s'] = '<span style="color:'.Conf()->g3('order_statuses',$rs['active'],1).'">'.Conf()->g3('order_statuses',$rs['active'],0).'</span>';
			$rs['body'] = OB::friendly($rs['body']);
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
		if ($this->id && $this->id!=self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
		}
		if (!$this->post || !$this->post['id']) {
			$this->conid = 0;
			$this->post = post('data');
		} else {
			$this->isEdit();
			if (!$this->post['name']) $this->post['name'] = Parser::name($this->post['title']);
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
		}
		$this->output['body'] = $this->post['body'];
		$this->win('comments');
	}
}