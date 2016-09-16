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
* @file       mod/Action.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Action extends Object {
	
	// Sick, instances for both classes shall present,diplicated
	private static $_instance = false;
	public static function &getInstance() {
		if (!self::$_instance) self::$_instance = new self($Index);	
		return self::$_instance;
	}
	
	
	protected function __construct() {
		$this->load(Index());
	}
	
	public function comment($table, $setid, $get) {
		$comment = $_POST['comment'];
		if (!is_array($comment)) {
			trigger_error('Invalid input');
			exit;
		}
		if (!@$comment['name'] && !IS_USER) {
			Index()->Session->setFormError(lang('Please enter your name'), 'cName');
		}
		elseif (@$comment['name'] && DB::one('SELECT 1 FROM '.DB_PREFIX.'users WHERE login LIKE '.e($comment['name']).' AND login!='.e(Session()->Login))) {
			Index()->Session->setFormError(lang('This name "%1" is registered as username in our database and it\'s not you, please type another name',$comment['name']), 'cName');
		}

		if (!IS_USER || $comment['email']) {
			if (!$comment['email']) {
				Index()->Session->setFormError(lang('Please enter your email'), 'cEmail');
			}
			elseif (!Parser::isEmail($comment['email'])) {
				Index()->Session->setFormError(lang('Email address is invalid'), 'cEmail');
			}
		}
		if (strlen(trim($comment['body']))<2) {
			Index()->Session->setFormError(lang('Please leave your comment..'), 'cBody');
		}
		elseif (DB::one('SELECT 1 FROM '.DB_PREFIX.PREFIX.'comments WHERE setid='.(int)$setid.' AND original='.e($comment['body']))) {
			Index()->Session->setFormError(lang('Same comment was already posted'), 'cBody');
		}
		/*
		elseif (self::isSpam($comment['body'],$comment['subject'])) {
			Index()->Session->setFormError(lang('Please do not comment anything'), 'cBody');
		}
		*/
		if (!Session()->UserID) {
			if (!$comment['captcha']) {
				Index()->Session->setFormError(lang('Please enter a verification code'), 'cCaptcha');
			}
			elseif (strtolower(trim($comment['captcha']))!=strtolower(trim($_SESSION['Captcha_sessioncode']))) {
				Index()->Session->setFormError(lang('Verification code is invalid'), 'cCaptcha');
			}
		}

		$spam = Factory::call('spam');
		$spam->comment = $comment['body'];
		$spam->subject = $comment['subject'];
		$spam->email = $comment['email'];
		$spam->name = $comment['name'];			
		$spam->www = $comment['url'];
		$spam->captcha = $comment['captcha'];
		if ($r = $spam->is()) {
			Index()->Session->setFormError(lang('Please do not spam!').' ('.$spam->text.')', 'cBody');
		}
		
		$data = array();
		$errors = Index()->Session->getFormError();
		if ($errors) {
			Index()->Smarty->assign('form_errors',$errors);
			if (!$get) return $errors;
		} else {
			if ($comment['remember']) {
				$_SESSION['POST']['comment'] = array(
					'name'	=> @$comment['name'],
					'email'	=> @$comment['email'],
					'url'	=> @$comment['url']
				);
			} else {
				$_SESSION['POST']['comment'] = array();
			}
			$data['comment'] = array(
				'setid'		=> $setid,
				'parentid'	=> @$comment['parentid'],
				'table'		=> $table,
				'subject'	=> $comment['subject'],
				'name'		=> @$comment['name'],
				'email'		=> @$comment['email'],
				'url'		=> @$comment['url'],
				'original'	=> @$comment['body'],
				'body'		=> Parser::parse('code_bb', $comment['body']),
				'userid'	=> Session()->UserID,
				'ip'		=> Session()->IPlong,
				'edited'	=> time(),
				'active'	=> 1
			);
			if (@$comment['id']) {
				DB::update('comments', $data['comment'], $comment['id'], 'id', (Session()->UserID?' AND userid='.Session()->UserID:' AND ip='.e(Session()->IP)));
			} else {
				$data['comment']['added'] = $data['comment']['edited'];
				DB::insert('comments', $data['comment']);
				$comment['id'] = DB::id();
				if (!$table) DB::run('UPDATE '.DB_PREFIX.PREFIX.'content SET comments=comments+1 WHERE id='.(int)$setid);
				elseif (in_array('comments',DB::columns($table))) {
					DB::run('UPDATE '.DB::prefix($table).' SET comments=comments+1 WHERE id='.(int)$setid);
				}
			}
			if ($_POST['comment']['remember']) {
				$_POST['comment']['body'] = '';
			} else {
				$_POST['comment'] = array();
			}
			$data['comment']['id'] = $comment['id'];
		}
		return $data;
	}
	
	public function rate($table_id, $rate) {
		if (!$table_id || !$rate) {
			return array('id' => 0, 'error' => 'no table_id');
		}
		list ($table, $id) = explode('-',$table_id);
		$id = (int)$id;
		if (!$table || !$id) {
			return array('id' => 0, 'error' => 'no table and id');	
		}
		if (!Conf()->in('rate_tables',$table)) {
			return array('id' => 0, 'error' => 'table '.$table.' is not allowed by rate_tables config variable');
		}
		if ($this->UserID) {
			$and = ' AND userid='.$this->UserID;
		}
		elseif (!$this->Index->Session->IPlong) {
			return array('id' => 0, 'error' => 'IP long is missing');
		}
		else {
			$and = ' AND ip='.$this->Index->Session->IPlong;
		}
		$row = DB::row('SELECT id, rate FROM '.DB_PREFIX.'votes WHERE setid='.$id.' AND `table`='.e($table).$and);
		if ($row) {
			DB::run('UPDATE '.DB_PREFIX.'votes SET rate='.e($rate).' WHERE id='.$row['id']); 
			return array('rateid' => $row['id'], 'rate_old' => $row['rate'], 'rate' => $rate, 'table' => $table, 'id' => $id);
		}
		
		$data = array(
			'setid'	=> $id,
			'table'	=> $table,
			'rate'	=> $rate,
			'rated'	=> time(),
			'userid'=> $this->UserID,
			'ip'	=> $this->Index->Session->IPlong
		);
		DB::insert('votes',$data);
		return array('rateid' => DB::id(), 'rate' => $rate, 'table' => $table, 'id' => $id);
	}
	
	
	public function geo($from,$area = '') {
		$ret = array();
		
		if ($area=='map' || $area=='all') {
			Factory::call('world')->driver()->only = array();
		}

		switch ($from) {
			case 'countries':
				$ret['list'] = Data::states(post('country'));
			break;
			case 'states':
				$ret['list'] = Data::cities(post('country'), post('state'));
			break;
			case 'cities':
				$ret['list'] = Data::districts(post('country'), post('state'), post('city'));
			break;
		}
		switch ($area) {
			case 'map':
			case 'a_map':
				$maps = new GoogleMaps();
				$maps->api_key = $this->Index->Tpl->maps_key;
				Data::world_params(array('lang'=>'en'));
				$maps->country = Data::country(post('country'));
				$maps->state = str_replace(array('-','\''),' ',Data::state(post('country'), post('state')));
				$maps->city = str_replace(array('-','\''),' ',Data::city(post('country'), post('state'), post('city')));
				$maps->district = str_replace(array('-','\''),' ',Data::district(post('country'), post('state'), post('city'), post('district')));
				Data::world_params(array('lang'=>LANG));
				$maps->street = $maps->district.' '.post('street').' '.post('house');
				$ret['js'] = $maps->search()->getJS();
			break;	
		}
		return $ret;
	}
	
	public function toColumn($col, $table = '') {
		switch ($col) {
			case 'added':
				return 'Date added';
			break;
			case 'edited':
				return 'Date added';
			break;
			case 'descr':
				return 'Description';
			break;
			case 'id':
				return 'ID';
			break;
			case 'rid':
				return 'Relate ID';
			break;
			case 'setid':
				return 'Content ID';
			break;
			case 'lang':
				return 'Language';
			break;
			case 'userid':
				return 'User ID';
			break;
			default:
				$ret = str_replace('_',' ',$col);
				$ex  = explode(' ',$ret);
				if (isset($ex[1])) {
					$langs = Site::getLanguages();
					$ret = '';
					foreach ($ex as $e) {
						if (strlen($e)==2 && isset($langs[$e])) {
							$ret .= ' <img src="/tpls/img/flags/16/'.$e.'.png">';
						} else {
							$ret .= ' '.$e;	
						}
					}
				}
				return trim($ret);
			break;	
		}
	}
	
	public function toVal($col, $value, $table) {
		switch ($value) {
			case 'Y': return lang('$Yes'); break;
			case 'N': return lang('$No'); break;
			case '': return '<span class="a-info">'.lang('$empty').'</span>'; break;
		}
		switch ($col) {
			case 'edited':
			case 'added':
				if (strlen($value)==10) {
					$value = date('d.m.Y H:i',$value);
					if (substr($value,-5)=='00:00') $value = substr($value,0,10);
				}
			break;
			case 'status':
				
			break;
			case 'lang':
				$langs = Site::getLanguages();
				$value = '<img src="/tpls/img/flags/16/'.$value.'.png"> '.$langs[$value][0];
			break;
			case 'userid':
				$value = DB::row('SELECT login FROM '.DB_PREFIX.'users WHERE id='.(int)$value,'login').' (ID: '.$value.')';
			break;
			default:
				$value = nl2br(trim($value));
			break;
		}
		return $value;
	}

	
	public function the_comment() {
		$data = Data::Comments($_POST['comment']['content_id'], '', true, false);	
		$errors = Index()->Session->getFormError();
		if ($errors) {
			$this->toDialog($errors);
			$this->end();
		} else {
			$this->Index->Smarty->assign('row', $data['comment']);
			$ret = array(
				'text'	=> lang('Thank you for your comment'),
				'type'	=> 'success',
				'append' => array(
					'thecomments' => array(
						$this->Index->Smarty->fetch('content/comment.tpl')
					),
				),
				'js'	=> '$(\'#cBody\').val(\'\');'
			);
			return $ret;
		}	
	}
	
	public function commentPage() {
		$data = Data::comments($_GET['content_id'], '', false, true);
		$thecomments = array();
		foreach ($data['list'] as $row) {
			$this->Index->Smarty->assign('row', $row);
			$thecomments[] = $this->Index->Smarty->fetch('content/comment.tpl');
		}
		$this->set('json', '?comment_page&content_id='.$_GET['content_id']);
		$this->set('pager', $data['pager']);
		$ret = array(
			'js'	=> '$(\'#thecomments\').html(\'\');',
			'html'	=> array(
				'comment_pager'	=> $this->Index->Smarty->fetch('content/pager.tpl'),
				'thecomments'	=> join('', $thecomments)
			),
			'js2'	=> '$(\'#thecomments\').show(\'highlight\');'
		);
		return $ret;
	}
	
	public function contact($params = array()) {
		$contact = $_POST['contact'];
		$_params = array(
			'error_mail' => lang('Please enter your email address'),
			'error_text' => lang('Please leave your message'),
			'subject'	=> ''.DOMAIN.': New message from {$name} {$email}',
			'message'	=> '{$text}<hr />{$name} {$email} {$phone} [{$ip}] User: {$login} ({$userid})',
			'plain'	=> false,
			'nl2br'	=> true,
			'to' => MAIL_EMAIL,
			'from_address' => MAIL_EMAIL,
			'from_name' => MAIL_NAME,
			'success' => lang('[contact_success]Your message has been sent.<br>Thank you, we will review and reply you soon!'),
			'func'	=> false
		);
		
		$params = array_merge($_params, $params);
				
		if (!$contact['email'] || !Parser::isEmail($contact['email'])) {
			$contact['email'] = '';
			$this->Index->Session->setFormError($params['error_mail'], 'email');	
		}
		if (!$contact['text'] || strlen(trim($contact['text']))<5) {
			$this->Index->Session->setFormError($params['error_text'], 'text');	
		}
		if (!isset($contact['name'])) $contact['name'] = '';
		if ($contact['email']) {
			DB::noerror();
			$times = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'grid_contact WHERE url='.e(Session::getIP()).' AND added > '.(time()-86400));
			if ($times > 2) {
				$this->Index->Session->setFormError($times.' '.lang('You may not post more than 3 messages per day, send direct email to %1',MAIL_EMAIL), 'text');	
			}
		}
		$spam = Factory::call('spam');
		$spam->comment = $contact['text'];
		$spam->email = $contact['email'];
		$spam->name = $contact['name'];
		if ($r = $spam->is()) {
			$this->Index->Session->setFormError($spam->text, 'test');
		}
		$errors = $this->Index->Session->getFormError();
		if ($errors) {
			$this->Index->Smarty->assign('form_errors',$errors);
		} else {

			if (!$params['plain'] && $params['nl2br']) $contact['text'] = nl2br($contact['text']);
			$contact['ip'] = Session::getIP();
			$contact['login'] = Session()->Login;
			$contact['userid'] = Session()->UserID;
			if (!isset($contact['email'])) $contact['email'] = '';
			if (!isset($contact['phone'])) $contact['phone'] = '';
			$attachments = array();
			if (isset($params['callback']) && is_callable($params['callback'])) {
				$files = call_user_func_array($params['callback'], array($contact));
				foreach ($files as $file) {
					$attachments[] = array(
						'file'	=> $file['path'].$file['file']
					);
				}
			}
			
			if ($params['plain'] && Email::sendPlain($params['to'],Email::parseVariables($params['subject'], $contact),Email::parseVariables($params['message'], $contact),$params['from_address'], $params['from_name'])) {
				$top_message = array(
					'type'	 => 'success',
					'text'	 => $params['success']
				);
				$_POST['contact']['text'] = '';
				$this->Index->Smarty->assign('top_message',$top_message);
			}
			elseif (!$params['plain'] && Email::send($params['to'],Email::parseVariables($params['subject'], $contact),Email::parseVariables($params['message'], $contact),$attachments,$params['from_address'], $params['from_name'])) {
				$top_message = array(
					'type'	 => 'success',
					'text'	 => $params['success']
				);
				$_POST['contact']['text'] = '';
				$this->Index->Smarty->assign('top_message',$top_message);
			} else {
				$this->Index->Session->setFormError('Unable to send your email. Our mail server temporarily does not work.<br />Please <a href="mailto:'.MAIL_EMAIL.'?subject='.rawurlencode('New message from '.$contact['name']).'&body='.rawurlencode($contact['text']."\r\n\r\nName: ".rawurlencode($contact['name'])."\r\nPhone: ".rawurlencode($contact['phone'])."\r\nEmail: ".$contact['email']).'&cc='.rawurlencode($contact['email']).'">click here</a> or copy/paste and send your message from your e-mail account to <b>'.MAIL_EMAIL.'</b>', 'fatal');
				$this->Index->Smarty->assign('form_errors',$this->Index->Session->getFormError());
			}

			
			DB::noerror();
			DB::insert('grid_contact',array(
				'descr'	=> $contact['text'],
				'title'	=> ($contact['title'] ? $contact['title'] : $contact['email']),
				'email'	=> $contact['email'],
				'phone'	=> $contact['phone'],
				'added'	=> time(),
				'url'	=> Session::getIP()
			));
			$contact['id'] = DB::id();
			if ($params['func']) call_user_func_array($params['func'], array($contact));
		}
	}
}