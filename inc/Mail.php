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
* @file       inc/Mail.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class Mail {
	
	protected
		$limit = 20,
		$total = 0,
		$offset = 0,
		$folder = 'INBOX',
		$data = array(),
		$selected = '',
		$data_type = '',
		$show_all = false,
		$filter = '',
		$group = false
	;
	
	const 
		FOLDER_SENT = 'SENT',
		FOLDER_INBOX = 'INBOX',
		FOLDER_ARCHIVE = 'ARCHIVE'
	;
	
	public function __construct($params = array()) {
		if (request(URL_KEY_FOLDER)) $this->folder = request(URL_KEY_FOLDER);
		$this->UserID = Session()->UserID;
		$this->offset = intval(request(URL_KEY_PAGE)) * $this->limit;
		$this->order = 'id DESC';
		if ($params) {
			foreach ($params as $k => $v) $this->$k = $v;
		}
		return $this;
	}
	
	public function setLimit($limit) {
		$this->limit = $limit;
		$this->offset = intval(request(URL_KEY_PAGE)) * $this->limit;
		return $this;
	}
	
	public function setOrder($order) {
		$this->order = $order;	
		return $this;
	}
	
	public function get($id) {
		$id = (int)$id;
		if (!$id) return false;
		$select = '*';
		
		if ($this->folder==self::FOLDER_SENT) {
			return DB::row('SELECT '.$select.' FROM '.DB_PREFIX.'mail WHERE rid='.$id.' AND to_id='.$this->UserID.' AND folder=\''.self::FOLDER_SENT.'\'');			
		} else {
			$ret = DB::row('SELECT '.$select.' FROM '.DB_PREFIX.'mail WHERE rid='.$id.' AND to_id='.$this->UserID);
			if ($ret && !$ret['read'] && $this->folder==$ret['folder']) {
				if ($ret['rid']) {
					DB::run('UPDATE '.DB_PREFIX.'mail SET `read`='.time().' WHERE rid='.$ret['rid'].'');
				} else {
					DB::run('UPDATE '.DB_PREFIX.'mail SET `read`='.time().' WHERE id='.$ret['id'].'');	
				}
			}
			return $ret;
		}
	}
	
	public function unread($id) {
		if ($this->folder==self::FOLDER_SENT) {
			
		} else {
			DB::run('UPDATE '.DB_PREFIX.'mail SET `read`=0 WHERE id='.(int)$id.' AND to_id='.$this->UserID);
		}
	}
	
	public function getAll($userid = 0, $select = '') {
		if (!$this->UserID) return array('list'=>array(),'pager'=>array());
		if (!$select) $select = 'id, rid, from_id, to_id, folder, subject, `read`, added, hook';
		$w = '';


		if ($this->show_all && $userid>0) {
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.', to_id AS `to` FROM '.DB_PREFIX.'mail WHERE ((from_id='.$this->UserID.' AND to_id='.$userid.') OR (to_id='.$this->UserID.' AND from_id='.$userid.')) AND rid!=id'.$this->filter.' ORDER BY '.$this->order;
		}
		elseif ($this->folder==self::FOLDER_SENT) {
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.', from_id AS `to`, from_id AS to_open FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.($userid?' AND from_id='.$userid:'').' AND folder=\''.self::FOLDER_SENT.'\''.$this->filter.($this->group?' GROUP BY to_id':'').' ORDER BY '.$this->order;
		}
		elseif ($this->folder==self::FOLDER_ARCHIVE) {
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.', from_id AS `to`, from_id AS to_open FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.($userid>0?' AND from_id='.$userid:'').' AND folder=\''.self::FOLDER_ARCHIVE.'\''.$this->filter./*($this->group?' GROUP BY from_id':'').*/' ORDER BY '.$this->order;
		} else {
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.', from_id AS `to`, from_id AS to_open FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.($userid?' AND from_id='.$userid:'').' AND folder='.e($this->folder).$this->filter.($this->group?' GROUP BY from_id':'').' ORDER BY '.$this->order;
		}
		
		$qry = DB::qry($sql,$this->offset, $this->limit);
		$data = array();
		while ($rs = DB::fetch($qry)) {
			$data['list'][] = $rs;
		}

		$this->total = DB::rows();
		$data['pager'] = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit,
		));
		return $data;
	}
	
	public function getAllByUser($userid, $select = '') {
		if (!$select) $select = 'id, rid, from_id, to_id, subject, folder, `read`, added, hook';
		if (!$userid) return array();
		if ($this->folder==self::FOLDER_SENT) {
			$qry = DB::qry('SELECT '.$select.' FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.' AND from_id='.$userid.' AND folder=\''.self::FOLDER_SENT.'\' ORDER BY '.$this->order, $this->offset, $this->limit);
		} else {
			$qry = DB::qry('SELECT '.$select.' FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.' AND from_id='.$userid.' ORDER BY '.$this->order,$this->offset, $this->limit);
		}
		$data = array();
		while ($rs = DB::fetch($qry)) {
			$data[] = $rs;
		}
		return $data;
	}
	
	public function getFolders($cnt = true,$exclude = '') {
		$this->data_type = 'folders';
		$this->data = array();
		if ($exclude!=self::FOLDER_INBOX && $exclude!=self::FOLDER_SENT) {
			$this->data[self::FOLDER_INBOX] = array (
				'name'	=> self::FOLDER_INBOX,
				'title'	=> lang('_$Inbox'),
				'cnt'	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.' AND folder='.e(self::FOLDER_INBOX))
			);
		}
		if (!$exclude) {
			$this->data[self::FOLDER_SENT] = array(
				'name'	=> self::FOLDER_SENT,
				'title'	=> lang('_$Sent'),
				'cnt'	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.' AND folder='.e(self::FOLDER_SENT))
			);
			$this->data[self::FOLDER_ARCHIVE] = array(
				'name'	=> self::FOLDER_ARCHIVE,
				'title'	=> lang('_$Archive'),
				'cnt'	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'mail WHERE to_id='.$this->UserID.' AND folder='.e(self::FOLDER_ARCHIVE))
			);
		}
		$sql = 'SELECT DISTINCT folder FROM '.DB_PREFIX.'mail WHERE (from_id='.$this->UserID.' OR to_id='.$this->UserID.') AND folder NOT IN (\''.self::FOLDER_INBOX.'\', \''.self::FOLDER_SENT.'\''.(($exclude && $exclude!=self::FOLDER_INBOX)?', '.e($exclude):'').')';
		$qry = DB::qry($sql,0,0);
		while ($rs = DB::fetch($qry)) {
			$this->data[$rs['folder']] = array(
				'name'	=> $rs['folder'],
				'title' => $rs['folder'],
				'cnt'	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'mail WHERE (from_id='.$this->UserID.' OR to_id='.$this->UserID.') AND folder='.e($rs['folder']))
			);
		}
		return $this;
	}
	public function setSelected($selected) {
		$this->selected = $selected;
		return $this;
	}
	public function toOptions() {
		$ret = '';
		switch ($this->data_type) {
			case 'folders':
				foreach ($this->data as $k => $rs) {
					$ret .= '<option value="'.strform($rs['name']).'"'.($this->selected==$rs['name']?' selected="selected"':'').'>'.strform($rs['title']).' ('.$rs['cnt'].')</option>';	
				}
			break;
		}
		return $ret;
	}
	public function toArray() {
		return $this->data;	
	}
	/**
	*
	*/
	public function send($to_userid, $subject, $body, $richtext = false, $from_userid = 0, $no_copy = false, $hook = '', $mail = false) {
		if (!$to_userid) return -1;
		if (!$subject && !$body) return -1;
		if (!$richtext) $body_conv = Parser::parse('code_bb', $body);
		else $body_conv = Parser::parse('code_smilies', $body);
		$subject = strip_tags($subject);
		if (!$from_userid && $from_userid!==-1) $from_userid = $this->UserID;
		
		$sent = $rid = false;	
		$sign = ((isset($this->Index->Session->profile['signature']) && $this->Index->Session->profile['signature']) ? '<br /><br />'.$this->Index->Session->profile['signature']:'');
		
		if (!is_numeric($to_userid) && ($id = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE login LIKE '.e($to_userid).' OR email LIKE '.e($to_userid)))) {
			$to_userid = $id;
		}

		if (!is_numeric($to_userid)) {
			if (Parser::isEmail($to_userid)) {
				$sent = Email::send($to_userid, $subject, $body_conv.$sign, false);
				$email = $to_userid;
				$to_userid = 0;
			}
		} else {
			if ($mail || (ADMIN && IS_ADMIN)) {
				$email = DB::row('SELECT email FROM '.DB_PREFIX.'users WHERE id='.$to_userid,'email');
				$sent = Email::send($email, $subject, $body_conv.$sign, false);
			}
			
			if (($sent || !$mail) && $to_userid) {
				// send to user
				$data = array(
					'from_id'	=> $from_userid,
					'to_id'		=> $to_userid,
					'subject'	=> $subject,
					'body'		=> $body,
					'body_conv'	=> $body_conv,
					'read'		=> 'N',
					'folder'	=> self::FOLDER_INBOX,
					'hook'		=> $hook,
					'added'		=> time(),
					'replied' 	=> 0
				);
				DB::insert('mail',$data);
				$rid = DB::id();
				DB::run('UPDATE '.DB_PREFIX.'mail SET rid='.$rid.' WHERE id='.$rid);
			}
		}
		
		if ($sent && $to_userid && $from_userid) {
			DB::run('UPDATE '.DB_PREFIX.'mail SET replied='.time().' WHERE to_id='.$from_userid.' AND from_id='.$to_userid.' AND folder=\''.self::FOLDER_INBOX.'\'');
		}
		
		if (($sent || !$mail) && !$no_copy) {
			// save a copy		
			DB::insert('mail',array(
				'rid'		=> $rid,
				'from_id'	=> $to_userid,
				'to_id'		=> $from_userid,
				'email'		=> $email,
				'subject'	=> $subject,
				'body'		=> $body,
				'body_conv'	=> $body_conv,
				'read'		=> 'N',
				'folder'	=> self::FOLDER_SENT,
				'added'		=> time(),
				'replied' 	=> 0
			));
		}
		return $rid ? $rid : $sent;
	}
	
	public function move($ids, $folder) {
		if ($this->folder==self::FOLDER_SENT || $folder==self::FOLDER_SENT) return false;
		$ids = array_unique(array_numeric((array)$ids));
		if (!$ids) return false;
		DB::run('UPDATE '.DB_PREFIX.'mail SET folder='.e($folder).' WHERE id IN ('.join(',',$ids).') AND to_id='.$this->UserID.' AND folder!=\''.self::FOLDER_SENT.'\'');
		return DB::affected();
	}
	
	public function delete($ids, $UserID = 0) {
		$ids = array_numeric((array)$ids);
		if (!$ids) return false;
		
		if ($this->folder!=self::FOLDER_SENT) {
			DB::run('DELETE FROM '.DB_PREFIX.'mail WHERE rid IN ('.join(',',$ids).') AND to_id='.$this->UserID);
		} else {
			$sql = 'DELETE FROM '.DB_PREFIX.'mail WHERE rid IN ('.join(',',$ids).') AND to_id='.$this->UserID.' AND folder=\''.self::FOLDER_SENT.'\'';
			DB::run($sql);
		}
		return DB::affected();
	}
	
}