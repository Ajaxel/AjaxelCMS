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
* @file       mod/AdminUsers.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminUsers extends Admin {
	
	public function __construct() {
		$this->title = 'User management';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->table = $this->name;
		$this->table2 = $this->table.'_profile';
		$this->image_sizes = array(
			1	=> array(640, 480) // 640, 480
			,2	=> array(130, 120) // 112, 83 | 100, 100
			,3	=> array(85, 85) // 60x60
		);
		$this->has_main_photo = true;
		$this->has_multi_photo = true;
		$this->has_global_files = true;
		$this->has_currency = in_array('currency', DB::columns('users_profile'));
		$this->has_balance = in_array('balance',DB::columns('users_transfers'));
		
		/*
		$this->change[0] = array(
			'columns'	=> array('money'),
			'table'		=> 'users_profile',
			'id_col'	=> 'setid',
			'array_key'	=> 'profile'
		);
		*/
		
		$this->idcol = 'id';
		$this->id = (int)$this->id;
		$this->user_groupid = intval(get('user_groupid','',999));
		$this->user_classid = intval(get('user_classid','',999));
		$this->user_statusid = intval(get('user_statusid','',999));
		$this->user_online = intval(get('user_online'));
		
	}
	
	public function install() {
		$sql = 'CREATE TABLE `users_transfers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price` double(16,2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `currency` varchar(3) DEFAULT NULL,
  `userid` int(11) unsigned NOT NULL,
  `added` int(10) unsigned NOT NULL,
  `account` int(8) NOT NULL,
  PRIMARY KEY (`id`)
)';
		DB::run($sql);	
	}
	
	public function sql() {
		if ($fid = $this->findID('u.id')) {
			$this->filter .= $fid.' OR login LIKE \'%'.$this->find.'%\'';	
		} else {
			if ($this->find) {
				$this->filter .= ' AND (u.login LIKE  \'%'.$this->find.'%\' OR u.email LIKE \'%'.$this->find.'%\')';
			}
			if ($this->user_groupid!=999) {
				$this->filter .= ' AND groupid='.$this->user_groupid;	
			}
			if ($this->user_classid!=999) {
				$this->filter .= ' AND classid='.$this->user_classid;	
			}
			if ($this->user_statusid!=999) {
				$this->filter .= ' AND active='.$this->user_statusid;	
			}
			if ($this->user_online) {
				$this->filter .= ' AND (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=u.id LIMIT 1)=1';	
			}
		}
		if ($this->user_statusid!=Site::ACCOUNT_DELETED) {
			$this->filter .= ' AND active!=2';
		}
		$this->order = 'u.id';
		$this->user_statuses = Conf()->g('user_statuses');
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->sql();
		if ($this->submitted) {
			
		}
		

		$sql = 'SELECT SQL_CALC_FOUND_ROWS u.id, u.login, u.groupid, p.firstname, p.lastname, p.city, p.state, p.country, p.company, u.classid, u.email, u.registered, u.main_photo, '.DB::sqlGetString('age','p.dob').' AS age'.(!$this->user_online?', (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=u.id LIMIT 1) AS online':', 1 AS online').', u.active FROM '.DB_PREFIX.$this->table.' u LEFT JOIN '.DB_PREFIX.$this->table2.' p ON p.setid=u.id WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql, $this->offset, $this->limit);
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			if ($rs['main_photo']) {
				$photo = $this->ftp_dir_upload.'users/'.$rs['id'].'/th1/'.$rs['main_photo'];
				if (is_file($photo)) {
					$rs['main_photo_size'] = @getimagesize($photo);
					if (!$rs['main_photo_size'][0]) $rs['main_photo'] = '';
				} else {
					$rs['main_photo'] = '';
				}
			}
			$rs['country'] = Data::country($rs['country']);
			$rs['city'] = Data::city($rs['country'], $rs['state'], $rs['city']);
			if (!$rs['age']) $rs['age'] = '';
			$rs['group_name'] = lang('$'.Conf()->g2('user_groups',$rs['groupid']));
			$rs['class_name'] = Conf()->g2('user_classes',$rs['classid']);
			$rs['status'] = $this->user_statuses[$rs['active']];
			$this->data[] = $rs;	
		}
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	
	
	public function validate() {
		$err = array();
		if (GROUP_ID==DEMO_GROUP) {
			$err['error'] = 'You are not allowed to edit users';
		}
		elseif (!$this->id && !$this->data['login']) $err['login'] = lang('$Login must be filled in');
		elseif (!$this->id && strlen(trim($this->data['login']))<3) $err['login'] = lang('$Login must have more than 2 characters');
		elseif (!$this->id && User::isLoginExists($this->data['login'], $this->id)) {
			$err['login'] = lang('$Such username already exists');
		}
		if (!$this->data['email']) $err['email'] = lang('$Email cannot be empty');
		elseif (!Parser::isEmail($this->data['email'])) $err['email'] = lang('$Email address is incorrect');
		//elseif (!User::isEmailExists($this->data['email'], $this->id)) $err['email'] = lang('$Such email address already exists');
		if (!$this->data['password'] && !$this->id) $err['password'] = lang('$Password cannot be empty');
		elseif ($this->data['password'] && $this->data['password']!=$this->data['re-password']) {
			$err['re-password'] = lang('$Passwords don\'t match');	
		}
		elseif (GROUP_ID==BOT_GROUP) {
			unset($this->data['password'], $this->data['email'], $this->data['login']);	
		} 
		elseif ($this->data['password']) {
			Factory::call('user');
			$this->data['password'] = User::password($this->data['password'], $this->data['login']);
		} else {
			unset($this->data['password']);	
		}
		unset($this->data['re-password']);
		$this->set_msg(lang('$New user %1 was registered',@$this->data['login']), lang('User %1 profile was updated',@$this->rs['login']));
		if (isset($this->data['profile']['signature'])) {
			$this->data['profile']['signature_conv'] = Parser::parse('code_bb',$this->data['profile']['signature']);
		}
		foreach (array('country','state','district','city') as $a) if (!$this->data[$a]) unset($this->data[$a]);
		$this->errors($err);
	}
	
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'save_images':
				$this->global_action('save_image_multi');
				$this->json_ret = array('images' => $this->filesToJson());
			break;
			case 'set_main_photo':
				$this->global_action('set_main_photo');
			break;
			case 'delete_image':
				$this->global_action('delete_image');
			break;
			case 'delete_images':
				$this->global_action('delete_images');
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	protected function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'money':
				if (!is_numeric(post('sum')) || !is_numeric(post('userid'))) break;
				$user_currency = false;
				if ($this->has_currency) {
					$user_currency = DB::one('SELECT currency FROM '.DB_PREFIX.'users_profile WHERE setid='.(int)post('userid'));
				}
				if (!$user_currency) $user_currency = DEFAULT_CURRENCY;
				$data = array(
					'price'		=> (post('todo')=='add' ? '' : '-').abs(post('sum')),
					'currency'	=> post('currency'),
					'userid'	=> post('userid'),
					'added'		=> time(),
					'title'		=> post('title'),
					'account'	=> '',
					'status'	=> (post('todo')=='add' ? Site::TRANSFER_ADD : Site::TRANSFER_REMOVE)
				);
				DB::insert('users_transfers',$data);
				$id = DB::id();
				
				$sum = $this->count_money(post('userid'), '', $user_currency);
				if ($this->has_balance) {
					DB::run('UPDATE '.DB_PREFIX.'users_transfers SET balance='.(float)$sum.' WHERE id='.$id);		
				}
				$rs = DB::row('SELECT price, currency, title, added'.($this->has_balance?', balance':'').' FROM '.DB_PREFIX.'users_transfers WHERE id='.$id);
				DB::run('UPDATE '.DB_PREFIX.'users_profile SET money='.(float)$sum.' WHERE setid='.(int)post('userid'));
				$this->post['profile']['currency'] = $user_currency;
				$return = true;
				$html = include FTP_DIR_TPLS.'admin/inc/users_window_money.php';
				$this->json_ret = array(
					'html'	=> $html,
					'total'	=> number_format($sum,2).' '.$user_currency
				);
			break;
			case 'save':			
				$this->rs2 = false;
				if ($this->id) {
					$this->rs = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
					$this->rs2 = DB::row('SELECT * FROM '.DB_PREFIX.$this->table2.' WHERE setid='.$this->id);
				} else {
					$this->data['registered'] = $this->time;	
				}
				$this->validate();
				$this->allow('users',($this->id?'edit':'save'),$this->rs,$this->data);
				$this->global_action('save');
				if ($this->data['profile']) {
					if ($this->data['profile'] && $this->data['profile']['dob']) Date::date2db('dob',$this->data['profile']);
					$this->data['profile']['setid'] = $this->id;
					if (isset($this->data['profile']['options'])) {
						$this->data['profile']['options'] = serialize($this->data['profile']['options']);
					}
					if ($this->rs2) {
						DB::update($this->table2, $this->data['profile'], $this->id, 'setid');
					} else {
						DB::insert($this->table2, $this->data['profile']);
					}
					if (!$this->affected) $this->affected = DB::affected();
				}
				$this->select_msg();
				
				require_once(FTP_DIR_ROOT.'mod/User.php');
				if (@$this->rs2['money']!=@$this->data['profile']['money']) {
					if ($this->updated==Site::ACTION_UPDATE) {
						$price = @$this->data['profile']['money'] - @$this->rs2['money'];
					} else {
						$price = @$this->data['profile']['money'];
					}
					$trans = array(
						'userid'	=> $this->id,
						'bank'		=> 'admin',
						'price'		=> $price,
						'added'		=> $this->time
					);
					DB::insert('users_trans',$trans);
				}
				
				if (post('transfer') && is_array(post('transfer'))) {
					foreach (post('transfer') as $id => $t) {
						if (!$t) continue;
						DB::run('UPDATE '.DB_PREFIX.'users_transfers SET title='.e($t).' WHERE userid='.$this->id.' AND id='.(int)$id);
					}
				}
				
				if ($this->updated==Site::ACTION_INSERT) {
					User::doInsert($this->id, $this);
				}
				elseif ($this->updated==Site::ACTION_UPDATE) {
					User::doUpdate($this->id, $this);
				}
				$this->Index->My->toDB(true, $this->updated, $this->affected, $this->id, $this->data);
				$new = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
				$this->log($this->id, $new, $this->rs);
				$this->table = 'users_profile';
				$new = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE setid='.$this->id);
				$this->log($this->id, $new, $this->rs2);
				$this->global_action('msg');
				$this->msg_reload = true;
			break;
			case 'add':
				$this->global_action('add_content');
			break;
			case 'act':
				$this->allow('act',($this->id?'edit':'save'),$this->rs,$this->data);
				if ($this->id==$this->Index->Session->UserID) {
					$this->msg_text = 'Try not to deactivate yourself';
					$this->msg_type = 'warning';
					break;
				}
				$this->global_action();
			break;
			case 'copy':
				if (!$this->id) break;
				$this->action('save');
			break;
			case 'delete':
				if (!$this->id) break;
				if ($this->isEdit()) break;
				$this->rs = DB::row('SELECT * FROM '.DB_PREFIX.'users WHERE id='.$this->id);
				$this->allow('users','delete',$this->rs,$this->data);
				if ($this->id==$this->Index->Session->UserID) {
					$this->msg_text = 'Try not to delete yourself';
					$this->msg_type = 'warning';
					break;
				}
				$sub = DB::getAll('SELECT * FROM '.DB_PREFIX.'users_profile WHERE setid='.$this->id);
				require_once(FTP_DIR_ROOT.'mod/User.php');
				$this->affected = User::doDelete($this->id, $this);
				$this->msg_text = 'User account '.$this->rs['login'].' was deleted.';
				$this->updated = Site::ACTION_DELETE;
				$this->table = 'users';
				
				$old = array();
				$old['top'] = $this->rs;
				$old['sub'] = array();
				foreach ($sub as $s) {
					$old['sub'][$this->table2.':'.$s['setid']] = $s;
				}
				$this->log($this->id, false, $old);
			break;
		}
	}
	
	public function window() {
		$allow = Allow()->admin($this->table, 'view', false, false, $this->table, $this->id);
		if ($allow) {
			$this->inc('allow', array('allow' => $allow));
			return;	
		}
		$this->isEdit();
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
			if (!$this->post) $this->id = 0;
			else $this->post['profile'] = DB::row('SELECT * FROM '.DB_PREFIX.$this->table2.' WHERE setid='.$this->id);
			$this->fetchChange();
		}
		if (!$this->post && get('id')) {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE login='.e(get('id')));
			if (!$this->post) $this->id = 0;
			else {
				$this->id = $this->post['id'];
				$this->post['profile'] = DB::row('SELECT * FROM '.DB_PREFIX.$this->table2.' WHERE setid='.$this->id);
			}
			$this->fetchChange();
		}
		if (!$this->post || !$this->post['id']) {
		//	$this->id = 0;
			$this->post = post('data');
			$this->post['profile'] = post('data','profile',array());
			$this->post['active'] = 1;
		} else {
			$this->post['profile']['options'] = unserialize($this->post['profile']['options']);
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
			/*
			$sql = 'SELECT a.id, a.status, b.title, b.price, b.quantity, b.currency, a.ordered, b.type, b.sellerid, b.table, b.itemid FROM '.DB_PREFIX.'orders2_map b LEFT JOIN '.DB_PREFIX.'orders2 a ON (a.id=b.orderid) WHERE a.userid='.$this->id.' AND a.status!='.Site::STATUS_NOT_PAID.' ORDER BY ordered DESC LIMIT 20';
			$this->post['orders'] = DB::getAll($sql);
			*/
		}
		require_once(FTP_DIR_ROOT.'mod/User.php');
		User::doOpen($this->id, $this);
		$this->Index->My->toForm(true, $this->post, $this->table);
		$this->uploadHash();
		$this->json_array['files'] = json($this->filesToJson());
		$this->win('users');
	}
	
	public function upload() {
		$this->global_action('upload');
	}
}