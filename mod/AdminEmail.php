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
* @file       mod/AdminEmail.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminEmail extends Admin {
	
	public function __construct() {
		parent::__construct(__CLASS__);
	}
}

class AdminEmail_campaigns extends Admin {	
	public function __construct() {
		$this->title = 'Campaigns';
		parent::__construct(__CLASS__);
		$this->idcol = 'id';
		$this->table = 'emails_camp';
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
		switch ($this->action) {
			case 'send':
				
			break;
			case 'save':
				
			break;
			case 'delete':
				if ($this->id) {
					DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
					DB::run('DELETE FROM '.DB_PREFIX.'emails_read WHERE campaign='.$this->id);
				}
			break;
		}
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['form'] = true;
		if ($this->submitted) {

		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS *, DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b %H:%i\') AS date FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY id DESC LIMIT '.$this->offset.', '.$this->limit;
		$data = DB::getAll($sql);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data = json($data);
	}
	
	public function window() {
		
		if ($this->id) {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
		}
		else {
			$this->post['type'] = get('add');
		}
		$this->win('email_campaigns');	
	}
}


class AdminEmail_mail extends Admin {	
	public function __construct() {
		$this->title = 'Message inbox';
		parent::__construct(__CLASS__);
		$this->idcol = 'id';
		$this->table = 'mail';
	//	$this->install();
		$folder = get(URL_KEY_FOLDER,false,'[[:CACHE:]]');
		if (!$folder) $folder = 'INBOX';
		if (SITE_TYPE!='json' && $this->action=='move') {
			$this->action();
		}
		
		if ($folder!='INBOX' && SITE_TYPE!='json') {
			if (!DB::getNum('SELECT 1 FROM '.DB_PREFIX.'mail WHERE folder='.e($folder))) {
				$folder = 'INBOX';
			}
		}
		$this->folder = $folder;		
		$this->limit = 30;
		$this->offset = $this->limit * $this->page;		
		$this->folders_options = Factory::call('mail')->getFolders()->setSelected($this->folder)->toOptions();
	}
	public function install() {
		$tables = DB::tables();
		if (!in_array('mail', $tables)) {
			$sql = 'CREATE TABLE `mail` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `rid` int(11) unsigned NOT NULL,
  `from_id` int(10) unsigned NOT NULL,
  `to_id` int(10) unsigned NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text,
  `body_conv` blob NOT NULL,
  `read` int(10) unsigned NOT NULL,
  `folder` varchar(255) NOT NULL,
  `added` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `to_id` (`to_id`),
  KEY `from_id` (`from_id`),
  KEY `folder` (`folder`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
			DB::run($sql);
		}
		if (!in_array('mail_templates',$tables)) {
			$sql = 'CREATE TABLE `mail_templates` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `userid` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `body` mediumblob NOT NULL,
  `type` enum(\'\',\'Q\',\'F\') NOT NULL,
  `added` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
			DB::run($sql);
		}
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
	public function validate() {
		$err = array();
		$this->data['arr_to'] = $this->parseUserLine($this->data['to']);
		if (!$this->data['to']) $err['to'] = lang('$Field to is empty');
		elseif (!$this->data['arr_to']) $err['arr_to'] = lang('$Cannot find any user');
		if (!$this->data['subject']) $err['subject'] = lang('$Subject cannot be empty');
		if (!$this->data['body']) $err['body'] = lang('$Message shall not be empty');
		$this->errors($err);
	}
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'send':
				$err = array();
				if (!$this->data['compose_message']) $err['compose_message'] = lang('$Message is empty');
				
				$this->errors($err);
				$this->rs = Factory::call('mail')->get($this->id);
				$this->rs['user'] = DB::row('SELECT login, email FROM '.DB_PREFIX.'users WHERE id='.(int)$this->rs['from_id']);
				if ($this->data['compose_save']) {
					$data = array(
						'userid'	=> $this->Index->Session->UserID,
						'name'		=> $this->data['compose_subject'],
						'body'		=> $this->data['compose_message'],
						'type'		=> 'Q',
						'added'		=> $this->time
					);
					DB::insert('mail_templates',$data);
				}

				
				Factory::call('mail')->send($this->rs['from_id'], $this->data['compose_subject'], $this->data['compose_message']);
				Factory::call('email')->send($this->rs['user']['email'], $this->data['compose_subject'], Parser::parse('code_bb', $this->data['compose_message']).(isset($this->Index->Session->profile['signature'])?'<br /><br />'.@$this->Index->Session->profile['signature']:''));
				$this->msg_text = 'Message was sent';
				$this->msg_type = 'letter';
			break;
			
			// full send
			case 'save':
				$this->validate();
				
				if ($this->data['save']) {
					$data = array(
						'userid'	=> $this->Index->Session->UserID,
						'name'		=> $this->data['save'],
						'body'		=> $this->data['body'],
						'type'		=> 'F',
						'added'		=> $this->time
					);
					DB::insert('mail_templates',$data);
				}
				$i = 0;
				$this->data['arr_to'] = array_unique($this->data['arr_to']);
				foreach ($this->data['arr_to'] as $to_id) {
					if (Factory::call('mail')->send($to_id, $this->data['subject'], $this->data['body'], true, 0, !$this->data['copy'])) {
						$i++;
					}
				}
				
				$this->msg_text = 'Mail has been sent to '.$i.' user'.($i>1?'s':'').'.'.($this->data['copy']?' You have a copy':'');
				$this->msg_type = 'letter';
				$this->updated = Site::ACTION_INSERT;
				$this->msg_reload = true;
			break;
			case 'delete':
				if ($this->id) {
					Factory::call('mail')->delete($this->id);
					$this->json_ret = false;
				}
				elseif ($_REQUEST['arr']) {
					$this->affected = Factory::call('mail')->delete(explode('|',$_REQUEST['arr']));
					$this->msg_text = lang('$%1 email letters were removed');
					$this->msg_type = 'trash';
				}
			break;
			case 'get_msg':
				$ret = array();
				$data = Factory::call('mail')->get($this->id);
				$data['user'] = DB::row('SELECT login, email FROM '.DB_PREFIX.'users WHERE id='.(int)$data['from_id']);
				$ret['sent'] = date('d M Y H:i',$data['added']).' <a href="javascript:;" onclick="S.A.W.open(\'?'.URL_KEY_ADMIN.'=users&id='.$data['from_id'].'\')">'.$data['user']['login'].' &lt;'.$data['user']['email'].'&gt;</a>';
				$ret['subject'] = $data['subject'];
				if ($data['body_conv']) {
					$ret['body'] = $data['body_conv'];	
				}
				elseif (!strstr($data['body'],'<')) {
					$ret['body'] = Parser::strPrint($data['body']);	
				} else {
					$ret['body'] = $data['body'];	
				}
				$ret['id'] = $data['id'];
				if ($data['folder']==Mail::FOLDER_SENT) {
					$ret['from'] = lang('$Message for %1:',$data['user']['login'].' &lt;'.$data['user']['email'].'&gt;');
				} else {
					$ret['from'] = lang('$Message from %1:',$data['user']['login'].' &lt;'.$data['user']['email'].'&gt;');
				}
				$this->json_ret = $ret;
			break;
			case 'move':
				Factory::call('mail')->move(explode('|',$_REQUEST['arr']), request('to'));
				$this->json_reload = true;
			break;
		}
	}
	protected function parseUserLine($line) {
		$lines = array_unique(preg_split("/(\n|\s|,|;)/",$line));
		$ret = array();
		foreach ($lines as $s) {
			if (!strlen(trim($s))) continue;
			$id = 0;
			if (is_numeric($s) && DB::one('SELECT 1 FROM '.DB_PREFIX.'users WHERE id='.$s)) {
				$id = $s;	
			}
			elseif (Parser::isEmail($s)) {
				$id = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE email LIKE '.e($s));
				if (!$id) {
					$ret[] = $s;
					continue;
				}
			}
			elseif (strstr($s,'-')) {
				$ex = explode('-',$s);
				$from = trim($ex[0]);
				$to = trim($ex[1]);
				if (is_numeric($from) && is_numeric($to)) {
					$_ret = DB::getAll('SELECT id FROM '.DB_PREFIX.'users WHERE id>='.$from.' AND id<='.$to.' AND active=1','id');
					if ($_ret) {
						$ret = array_merge($ret,$_ret);
						unset($_ret);
					}
					continue;
				}
			}
			else {
				$id = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE login LIKE '.e($s));
				if (!$id) continue;
			}
			if ($id) $ret[] = $id;
		}
		return array_unique($ret);
	}
	public function sql() {
		$this->filter = ' AND to_id='.$this->Index->Session->UserID.'';
		if ($this->folder) {
			$this->filter .= ' AND folder='.e($this->folder);
		}
		if ($this->find) {
			$this->filter .= ' AND (subject LIKE '.e('%'.$this->find.'%').' OR body_conv LIKE '.e('%'.$this->find.'%').')';
		}
	}
	public function listing() {
		$this->button['save'] = false;
		$this->button['form'] = true;
		if ($this->submitted) {

		}
		$this->folder = $this->folder;
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, rid, from_id, (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=from_id) AS user, subject, `read`, folder, DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b %H:%i\') AS date FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY added DESC LIMIT '.$this->offset.', '.$this->limit;
		$data = DB::getAll($sql);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data = json($data);
	}
	public function window() {
		$this->array['replies']['Q'] = array(
			'' => lang('$-- select --')
		);
		$this->array['replies']['F'] = array(
			'' => lang('$-- select --')
		);
		
		$data = DB::getAll('SELECT id, name, type FROM '.DB_PREFIX.'mail_templates ORDER BY type, name');
		foreach ($data as $rs) {
			if ($rs['type']=='F') {
				$this->array['replies']['F'][$rs['id']] = $rs['name'];
			} else {
				$this->array['replies']['Q'][$rs['id']] = $rs['name'];
			}
		}
		
		if (get('compose')) {
			$this->json_array['files'] = $this->filesToJson();
			if (request('to')) $this->post['to'] = request('to');
			if (request('subject')) $this->post['subject'] = request('subject');
			
			$this->win('email_compose');
		}
		elseif ($this->id) {
			$this->post = Factory::call('mail')->get($this->id);
		//	$this->post = DB::row('SELECT * FROM '.DB_PREFIX.'mail WHERE id='.$this->id);
			
			
			if ($this->post && ($this->folder!=Mail::FOLDER_SENT || 1)) {
				$this->post['user'] = DB::row('SELECT login, email FROM '.DB_PREFIX.'users WHERE id='.(int)$this->post['from_id']);
				$rn = "\r\n";
				$from = 'From: '.$this->post['user']['email'].$rn.'To: '.$this->Index->Session->Email.$rn.'Subject: Re: '.$this->post['subject'].$rn.'Date: '.date('r',$this->post['added']).$rn.$rn;
				
				$this->post['compose_subject'] = $this->post['subject'];
				$this->post['compose_body'] = $rn.$rn.$rn.$from.$this->post['body'];
				$this->post['compose_body'] = '';
				$this->post['replies'] = array();
				
				$this->post['replies'] = Factory::call('mail')->setLimit(100)->getAllByUser($this->post['from_id']);
				/*
				$sql = 'SELECT id, subject, `read`, added, from_id FROM '.DB_PREFIX.$this->table.' WHERE (from_id='.(int)$this->post['from_id'].' AND to_id='.$this->Index->Session->UserID.') OR (to_id='.(int)$this->post['from_id'].' AND from_id='.$this->Index->Session->UserID.') ORDER BY added DESC LIMIT '.$this->offset.', '.$this->limit;
				
				$this->post['replies'] = DB::getAll($sql);
				*/
			}
			$this->win('email_mail');	
		}
	}
}




class AdminEmail_mailtpl extends Admin {
	
	public function __construct() {
		$this->title = 'Mail templates';
		parent::__construct(__CLASS__);
		$this->idcol = 'id';
		$this->table = 'mail_templates';
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
	public function validate() {
		$err = array();
		if (!$this->data['name']) $err['name'] = lang('$Title is empty');
		if (!$this->data['body']) $err['body'] = lang('$Message is empty');
		return $err;
	}
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->global_action('validate');
				$this->global_action('save');
			break;
			case 'delete':
				$this->global_action('delete');
			break;
			case 'mail_template':
				if (!$this->id) break;
				$ret = DB::row('SELECT `type`, subject, body FROM '.DB_PREFIX.$this->table.' WHERE id='.(int)$this->id);
				if ($ret['type']=='Q') $ret['body'] = Parser::parse('code_bb',$ret['body']);
				$ret['body'] = AdminEmail_templates::parse_email_body($ret['body']);
				$this->json_ret = $ret;
			break;
		}
	}
	public function sql() {
		if ($this->find) {
			$this->filter = ' AND (name LIKE '.e('%'.$this->find.'%').' OR body LIKE '.e('%'.$this->find.'%').')';
		}
		if ($this->type) {
			$this->filter .= ' AND type='.e($this->type);
		}
	}
	public function listing() {
		if ($this->submitted) {

		}
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, name, type, DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b %H:%i\') AS date FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY added DESC LIMIT '.$this->offset.', '.$this->limit;
		$data = DB::getAll($sql);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data =  json($data);
	}
	
	public function window() {
		
		$this->uploadHash();
		if ($this->id) {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
		}
		else {
			$this->post['type'] = get('add');
		}
		$this->win('email_mailtpl');	
	}
	
	public function upload() {
		$this->allow('templates','upload');
		
		$handler = new UploadHandler;
		
		$filename = File::unchar($_FILES['Filedata']['name']);
		$tmp_name = $_FILES['Filedata']['tmp_name'];
		
		$nameonly = nameOnly($filename);
		$ext = ext($filename);
		$filename = File::getUnique($dir,$nameonly,$ext);
		
		$handler->prepare($tmp_name,$filename);
		$filename = $handler->file_name;
		
		$from = $_FILES['Filedata']['tmp_name'];
		if (!is_dir($this->ftp_dir_files.'mail/')) mkdir($this->ftp_dir_files.'mail/',0777);
		$dir = $this->ftp_dir_files.'mail/'.Site::$upload[2].'/';
		if (!is_dir($dir)) mkdir($dir,0777);
		$to = $dir.$filename;
		
		$handler->prepare($tmp_name,$filename);
		$filename = $handler->file_name;
		$handler->move($from, $to);
		echo json_encode($handler->response());
	}
}




class AdminEmail_log extends Admin {
	
	public function __construct() {
		$this->title = 'Email log';
		parent::__construct(__CLASS__);
		$this->idcol = 'id';
		$this->table = 'mail';
		$this->folder = request(URL_KEY_FOLDER,false,'[[:CACHE:]]');
	}
	public function json() {
		
	}
	public function sql() {
		if ($this->find) {
			$this->filter = ' AND (subject LIKE '.e('%'.$this->find.'%').' OR body_conv LIKE '.e('%'.$this->find.'%').')';
		}
		if ($this->folder) {
			$this->folder .= ' AND folder='.(int)$this->folder;	
		}
	}
	public function listing() {
		if ($this->submitted) {

		}
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS from_id, to_id, subject, `read`, folder, DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b %H:%i\') AS date FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY added DESC LIMIT '.$this->offset.', '.$this->limit;
		$data = DB::getAll($sql);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data = json($data);
	}	
}




class AdminEmail_templates extends Admin {
	
	public function __construct() {
		$this->title = 'Email templates and newsletter mass send';
		parent::__construct(__CLASS__);
		$this->table = 'emails';
		$this->dir = $this->ftp_dir_files.'email/';
		if (!is_dir($this->dir)) File::mkdir($this->dir,0777);
	}
	
	public function init() {

		/*
		if (!is_dir(FTP_DIR_ROOT.$this->current['PREFIX'])) mkdir(FTP_DIR_ROOT.$this->current['PREFIX'],0777);
		if (!is_dir(FTP_DIR_ROOT.$this->current['PREFIX'].'files/')) mkdir(FTP_DIR_ROOT.$this->current['PREFIX'].'files/',0777);
		if (!is_dir($this->dir)) mkdir($this->dir);
		*/
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
	
	public function validate() {
		$err = array();
		if (!$this->data['name']) $err['name'] = lang('$Filename is empty');
		else $this->data['name'] = File::fixFileName($this->data['name']);
		if (!$this->data['title']) $err['title'] = lang('$Subject is empty');
		if (!$this->data['body']) $err['body'] = lang('$Message is empty');
		$this->errors($err);
	}
	
	public static function parse_email_body($t) {
		$rs = array(
			'email'	=> MAIL_EMAIL
		);
		$link = '?user&amp;unsubscribe&amp;email='.urlencode($rs['email']);
		$link = HTTP_BASE.trim(Url::ht($link),'/');
		$t = str_replace(array('{$unsubscribelink}','#unsubscribelink#'), $link, $t);
		return $t;
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		$this->id = get('id');
		if (!$this->id) $this->id = request('id');
		switch ($this->action) {
			case 'save':
				if ($this->isEdit()) break;
				$this->allow('email',($this->id?'edit':'save'));
				$this->validate();
				$lang_all = post(self::KEY_LANG_ALL);
				if ($this->id) {
					if ($lang_all) {
						foreach ($this->langs as $l => $a) {
							$this->id = $this->write($this->id, $this->data['name'], $this->data['title'], $this->data['body'], $l);
						}
					} else {
						$this->id = $this->write($this->id, $this->data['name'], $this->data['title'], $this->data['body'], $this->lang);
					}
					$this->updated = Site::ACTION_UPDATE;
					$this->affected = 1;
				} else {
					foreach ($this->langs as $l => $a) {
						$this->id = $this->write(0, $this->data['name'], $this->data['title'], $this->data['body'], $l);
					}
					$this->updated = Site::ACTION_INSERT;
				}
				$this->global_action('msg');
			break;
			case 'send':
				$err = array();
				$this->allow('email','send');
				if (!$this->id) $err['id'] = 'No file seleted';
				if (!count($this->data['group'])) $err['group'] = lang('$Pick a group');
				if (!$this->data['from_name']) $err['from_name'] = lang('$From name is empty');
				if (!$this->data['from_email']) $err['from_email'] = lang('$From email is empty');
				$this->errors($err);

				$set = array();
				$set['cnt'] = (int)$this->data['cnt'];
				if (@$this->data['o_sent']) $set['o_sent'] = $this->toTimestamp($this->data['sent']);
				else $set['o_sent'] = false;
				$set['with_unsub'] = @$this->data['with_unsub'];
				$set['read'] = $this->data['read'];
				$set['clicked'] = $this->data['clicked'];
				$set['where'] = $this->data['where'];
				$set['where2'] = $this->data['where2'];
				
				$groups = $_groups = array();
				foreach ($this->data['group'] as $g) $groups[] = e($g,false);
				foreach ($this->data['group'] as $g) {
					if ($g=='[users]') continue;
					$_groups[] = e($g,false);
				}
				$set['group'] = $groups;
				if ($_groups) {
					$sql = 'SELECT COUNT(1) FROM '.DB_PREFIX.$this->table.' WHERE `group` IN (\''.join('\', \'', $_groups).'\')'.(!$set['with_unsub']?' AND (unsub=\'0\' OR unsub IS NULL)':'').($set['cnt']?' AND `cnt`<'.$set['cnt']:'').($set['read']?' AND `read`>='.$set['read']:'').($set['clicked']?' AND `clicked`>='.$set['clicked']:'').($set['o_sent']?' AND `sent`>='.$set['o_sent']:'').($set['where'] ? ' AND '.$set['where']:'');
					$total = DB::one($sql);
				} else {
					$total = 0;	
				}
				if (in_array('[users]',$this->data['group'])) {
					$sql = 'SELECT COUNT(1) FROM '.DB_PREFIX.'users'.($set['where2'] ? ' WHERE '.$set['where2']:'');
					$total += DB::one($sql);
				}
				if (!$total) {
					$this->errors(array(lang('$No emails available were found, nothing to send')));
				}
				$set['start'] = 0;
				$set['sent'] = 0;
				$set['offset'] = 0;
				$set['total'] = $total;
				$set['file'] = $this->id;
				$set['delay'] = (int)$this->data['delay'];
				$set['portion'] = (int)$this->data['portion'];
				$set['from_email'] = $this->data['from_email'];
				$set['from_name'] = $this->data['from_name'];
				$set['signature'] = $this->data['signature'];
				
				$last_camp = DB::one('SELECT MAX(id) FROM '.DB_PREFIX.'emails_camp');
				DB::run('DELETE FROM '.DB_PREFIX.'emails_read WHERE campaign<'.($last_camp-4));
				
				$data = array(
					'campaign'=> File::nameOnly($set['file']),
					'total' => $total,
					'read'	=> 0,
					'clicked'=> 0,
					'added'	=> time(),
					'cnt'	=> 0,
					'groups' => join('|',$this->data['group']),
					'from_email' => $this->data['from_email'],
					'from_name' => $this->data['from_name'],
					'unsubs' => 0
				);

				DB::insert('emails_camp',$data);
				$set['camp'] = DB::id();
				
				$arr = array(
					'title'	=> lang('$Starting..'),
					'descr'	=> lang('$%1 emails to send, please wait',$total),
					'delay'	=> $set['delay'],
					'sent'	=> $set['sent'],
					'total'	=> $set['total'],
					'percent'=> 1 / $set['total'] * 100,
					'begin'	=> true
				);
				$this->json_ret = array(
					'js'	=> 'S.A.M.sendEmails('.json($arr).');'
				);
				Cache::saveSmall('mass_send',$set);
			break;
			case 'send_next':
				$this->json_ret = array(
					'end' 	=> true,
					'title'	=> 'Enough',
					'descr'	=> '',
					'delay'	=> 500,
					'percent'=> 100	
				);
				$this->allow('email','send');
				$set = Cache::getSmall('mass_send');
				if (!$set || !@$set['file'] || $set['offset'] >= $set['total']) {
					$arr = array(
						'title'	=> lang('Finished %1 of %2 emails were sent',$set['sent'],$set['total']),
						'delay'	=> $set['delay'],
						'sent'	=> $set['sent'],
						'total'	=> $set['total'],
						'descr'	=> lang('$Email sending is complete'),
						'percent'=> 100,
						'end'	=> true
					);
					$set = array();
					Cache::saveSmall('mass_send',$set);
					$this->json_ret = $arr;
					return;
				} else {
					// active=1
					if (in_array('[users]',$set['group'])) {
						$q = DB::qry('SELECT id, email, login, firstname, lastname, code FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile up ON up.setid=u.id'.($set['where2'] ? ' WHERE '.$set['where2']:''),abs($set['offset']),$set['portion']);
						while ($r = DB::fetch($q)) {
							DB::run('REPLACE INTO '.DB_PREFIX.$this->table.' (email, name, `group`, added, data) VALUES ('.e($r['email']).', '.e($r['firstname']?$r['firstname'].($r['lastname']?' '.$r['lastname']:''):$r['login']).', \'[users]\', '.time().', '.e(serialize($r)).')');	
						}
					}
					$sql = 'SELECT DISTINCT email, name, lang, `group`, cnt, `sent`, data FROM '.DB_PREFIX.$this->table.' WHERE `group` IN (\''.join('\', \'', $set['group']).'\')'.(!$set['with_unsub']?' AND (unsub=\'0\' OR unsub IS NULL)':'').($set['cnt']?' AND `cnt`<'.$set['cnt']:'').($set['read']?' AND `read`>='.$set['read']:'').($set['clicked']?' AND `clicked`>='.$set['clicked']:'').($set['o_sent']?' AND `sent`>='.$set['o_sent']:'').($set['where'] ? ' AND '.$set['where']:'').' LIMIT '.abs($set['offset']).', '.$set['portion'];

					$data = DB::getAll($sql,'email|[[:ARRAY:]]');
					
					if (!$data) {
						$arr = array(
							'title'	=> lang('Finished %1 of %2 emails were sent',$set['sent'],$set['total']),
							'delay'	=> $set['delay'],
							'sent'	=> $set['sent'],
							'total'	=> $set['total'],
							'descr'	=> lang('$Email sending is complete'),
							'percent'=> 100,
							'end'	=> true
						);
						$set = array();
						Cache::saveSmall('mass_send',$set);
						$this->json_ret = $arr;
						return;
					}
					
					$offset_minus = 0;
					if ($set['o_sent'] || $set['cnt']) {
						foreach ($data as $rs) {
							if ($set['cnt']) {
								if ($rs['cnt'] < $set['cnt']) $offset_minus++;
							}
							if ($set['o_sent']) {
								if ($rs['sent'] >= $set['o_sent']) $offset_minus++;	
							}
						}
					}
					$set['offset'] -= $offset_minus;
					
					require_once(FTP_DIR_ROOT.'inc/Email.php');
					$file = File::fixFileName($set['file']);
					$ext = ext($file);
					if (!$ext) $ext = 'html';
					$name = nameOnly($file);
					$subject = $message = array();
					foreach ($this->langs as $l => $a) {
						if (is_file($this->dir.$name.'-[s.'.$l.'].'.$ext)) {
							$subject[$l] = file_get_contents($this->dir.$name.'-[s.'.$l.'].'.$ext);
							$message[$l] = file_get_contents($this->dir.$name.'-[m.'.$l.'].'.$ext);
						} else {
							foreach ($this->langs as $_l => $a) {
								if (is_file($this->dir.$name.'-[s.'.$_l.'].'.$ext)) {
									$subject[$l] = file_get_contents($this->dir.$name.'-[s.'.$_l.'].'.$ext);
									$message[$l] = file_get_contents($this->dir.$name.'-[m.'.$_l.'].'.$ext);
									break;
								}
							}
						}
					}
					if (!$subject) {
						Message::halt('Error','No template file selected or file does not exists: '.$this->dir.$name.'-[s.'.$l.'].'.$ext.'');
					}
					
					if ($set['signature']) {
						$row = DB::row('SELECT `type`, body FROM '.DB_PREFIX.'mail_templates WHERE id='.(int)$set['signature']);
						if ($row && $row['body']) {
							foreach ($message as $l => $m) {
								if ($row['type']=='Q') {
									$message[$l] .= Parser::parse('code_bb', $row['body']);	
								} else {
									$message[$l] .= $row['body'];	
								}
							}
						}
					}
					
					$plain = $smarty = $update = false;
					$update = 'REPLACE INTO '.DB_PREFIX.'emails_sent (sent, email, `group`, userid) VALUES ('.$this->time.', \'{$email}\', \'{$group}\', '.USER_ID.')';
					Email::$campaign = $set['camp'];
					$sent = Email::sendMass($data, $subject, $message, false, $set['from_email'], $set['from_name'], $smarty, $update, $plain);					
					if ($sent && $set['camp']) DB::run('UPDATE '.DB_PREFIX.'emails_camp SET cnt=cnt+'.$sent.' WHERE id='.$set['camp']);
					
					$set['sent'] += $sent;
					$set['offset'] += $sent;
					
					$set['start'] = $set['start'] + $set['portion'];
					$arr = array(
						'title'	=> lang('%1 of %2 emails were sent',$set['sent'],$set['total']),
						'delay'	=> $set['delay'],
						'sent'	=> $set['sent'],
						'total'	=> $set['total'],
						'percent'=> ceil($set['sent'] / $set['total'] * 100),
						'descr'	=> $set['sent'].' of '.$set['total'].' emails were sent. '.key($data).''
					);
				}
				Cache::saveSmall('mass_send',$set);
				$this->json_ret = $arr;
			break;
			case 'send_complete':
				$limit = 2000;
				$this->allow('email','send');
				$set = Cache::getSmall('mass_send');
				if (!isset($set['offset'])) $set['offset'] = 0;
				$i = 0;
				if (!isset($set['total'])) $set['total'] = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'emails_sent WHERE userid='.USER_ID);
				$qry = DB::qry('SELECT * FROM '.DB_PREFIX.'emails_sent WHERE userid='.USER_ID, $set['offset'], $limit);
				while ($row = DB::fetch($qry)) {
					$i++;	
					DB::run('UPDATE '.DB_PREFIX.'emails SET sent='.$row['sent'].', cnt=cnt+1 WHERE email='.e($row['email']).' AND `group`='.e($row['group']));
				}
				if ($set['total'] > $limit) $set['offset'] = $limit;
				else {
					$set['offset'] = $set['total'];
					$i = 0;
				}
				if ($set['total']>0) $p = round($set['offset'] / $set['total'] * 100); else $p = 0;
				if ($p>100) $p = 100;
				if (!$i) {
					DB::run('DELETE FROM '.DB_PREFIX.'emails_sent WHERE userid='.USER_ID);
					$arr = array(
						'title'	=> lang('Finalized %1 of %2',$set['offset'],$set['total']),
						'offset' => $set['offset'],
						'delay'	=> 400,
						'total'	=> $set['total'],
						'descr'	=> lang('$Email sending is complete'),
						'percent'=> $p,
						'complete'	=> true
					);	
					Cache::saveSmall('mass_send',array());
				} else {
					$arr = array(
						'title'	=> lang('Finalizing %1 of %2',$set['offset'],$set['total']),
						'offset' => $set['offset'] + 2000,
						'delay'	=> 400,
						'total'	=> $set['total'],
						'descr'	=> lang('$Email sending is complete'),
						'percent'=> $p,
						'end'	=> true
					);
					Cache::saveSmall('mass_send',$set);
				}
				$this->json_ret = $arr;
			break;
			case 'delete':
				$this->allow('email','delete');
				$this->delete($this->id);
				$this->msg_text = lang('$Email letter: %1 was deleted',$this->id);
			break;
		}
	}
	
	private function write($id, $file, $subject, $message, $lang) {
		$file = File::fixFileName($file);
		$ext = ext($file);
		if (!$ext) $ext = 'html';
		
		$message = preg_replace('/\sabp="([^"]+)"/','',$message);
		
		$name = nameOnly($file);
		if ($id && $id!=$file) {
			$_file = File::fixFileName($id);
			$_ext = ext($id);
			if (!$_ext) $_ext = 'html';
			$_name = nameOnly($id);
			foreach ($this->langs as $l => $a) {
				if ($l==$lang) {
					unlink($this->dir.$_name.'-[s.'.$lang.'].'.$ext);
					unlink($this->dir.$_name.'-[m.'.$lang.'].'.$ext);
				} else {
					rename($this->dir.$_name.'-[s.'.$l.'].'.$_ext, $this->dir.$name.'-[s.'.$l.'].'.$ext);
					rename($this->dir.$_name.'-[m.'.$l.'].'.$_ext, $this->dir.$name.'-[m.'.$l.'].'.$ext);
				}
			}
		}
		file_put_contents($this->dir.$name.'-[s.'.$lang.'].'.$ext, $subject);
		file_put_contents($this->dir.$name.'-[m.'.$lang.'].'.$ext, $message);
		return $name.'.'.$ext;
	}
	
	private function delete($file) {
		$file = File::fixFileName($file);
		$ext = ext($file);
		$name = nameOnly($file);
		foreach ($this->langs as $l => $a) {
			unlink($this->dir.$name.'-[s.'.$l.'].'.$ext);
			unlink($this->dir.$name.'-[m.'.$l.'].'.$ext);
		}
	}
	
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->data = array();
		
		$dh = opendir($this->dir);
		$i = 0;
		$l = lower($this->find);
		while (($file = readdir($dh))!==false) {
			if ($file=='.' || $file=='..' || is_dir($this->dir.$file) || !strpos($file,'[m.'.$this->lang.']')) continue;
			$ex = explode('-[',$file);
			$subject = $this->dir.$ex[0].'-[s.'.$this->lang.'].'.substr($ex[1],6);
			if (!is_file($subject)) file_put_contents('Sample subject',$subject);
			$title = file_get_contents($subject);
			
			if ($l && !strstr(lower($file), $l) && !strstr(lower($title), $l)) continue;
			
			$this->data[$ex[0].'.'.substr($ex[1],6)] = array(
				'title'	=> $title
			);
			$i++;
		}
		closedir($dh);
		ksort($this->data);
		$this->total = $i;
		$this->json_data =  json_encode($this->data);
	}
	
	
	public function window() {
		$this->id = get('id');
		if ($this->id) {
			$this->isEdit();
			$ext = ext($this->id);
			$name = nameOnly($this->id);
			$message = $this->dir.$name.'-[m.'.$this->lang.'].'.$ext;
			$subject = $this->dir.$name.'-[s.'.$this->lang.'].'.$ext;
			if (is_file($message)) {
				if (!is_file($subject)) file_put_contents('Sample subject',$subject);
				$this->post['title'] = file_get_contents($subject);
				$this->post['body'] = file_get_contents($message);
				$this->post['name'] = $this->id;
				$this->post['edited'] = filemtime($message);
			} else {
				$this->id = 0;	
			}
		} else {
			$this->id = 0;	
		}
	
		$this->name_id = md5($this->id);
		if (get('send')) {
			$this->post['title'] = self::parse_email_body($this->post['title']);
			$this->post['body'] = self::parse_email_body($this->post['body']);
			$this->output['signature'] = DB::getAll('SELECT id, CONCAT(id,\'. \',name, \' (\',type,\')\') AS name FROM '.DB_PREFIX.'mail_templates ORDER BY name','id|name');
			$this->output['groups'] = DB::getAll('SELECT a.group, CONCAT(a.group,\' (\',(SELECT COUNT(1) FROM '.$this->table.' b WHERE b.group=a.group),\')\') AS group_cnt FROM '.DB_PREFIX.$this->table.' a GROUP BY a.group ORDER BY a.group','group|group_cnt');
			$this->output['groups']['[users]'] = 'Registered users';
			$this->win('email_send');
		} 
		else {
			$this->name = 'email';
			$this->win('email_templates');
		}
	}
}



class AdminEmail_db extends Admin {
	
	const EMAIL_REGEX = "/(\n|\s|,|;|\||\'|>|<)/";
	
	public function __construct() {
		$this->title = 'Email database';
		parent::__construct(__CLASS__);
		$this->idcol = 'email';
		$this->table = 'emails';
		$this->group = request('group',false,'[[:CACHE:]]');
	}
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'import':
				$set = Cache::getSmall('email_import');
				if (!$set['file']) $set['file'] = $_SESSION['import_file'];
				if (post('group')) $set['group'] = post('group');
				if (post('language')) $set['lang'] = post('language');
				if (!$set['file']) {
					Cache::saveSmall('email_import',NULL);
					$this->json_ret = array('finished'=>true,'percent'=>0,'file'=>$set['file'],'error'=>lang('_$No file selected'));
					return false;	
				}
				if (!$set['group']) {
					Cache::saveSmall('email_import',NULL);
					$this->json_ret = array('finished'=>true,'percent'=>0,'file'=>$set['file'],'error'=>lang('_$Group cannot be empty, please enter a group name and try again'));
					return false;	
				}
				$file = FTP_DIR_ROOT.DIR_FILES.'temp/'.$this->Index->Session->UserID.'/'.$set['file'];
				if (!$set['size']) $set['size'] = filesize($file);
				if (!is_file($file)) {
					Cache::saveSmall('email_import',NULL);
					$this->json_ret = array('finished'=>true,'percent'=>0,'file'=>$set['file'],'error'=>'No file!');
					return false;
				}
				$dir = FTP_DIR_ROOT.DIR_FILES.'temp/'.$this->Index->Session->UserID.'/'.nameOnly($set['file']).'/';
				
				/*
				if ($set['tell'] && $set['tell'] > $set['size'] - 64000) {
					$i = $this->import_emails(file_get_contents($file), $set['group'], $set['lang']);
					$this->json_ret = array('finished'=>true,'percent'=>100,'file'=>$set['file'],'msg'=>'Done');
					if (!$i) {
						$this->json_ret['error'] = 'No emails were imported, wrong file structure. Please upload a text file where are emails comma, space or paragraph separated.';
					}
					else {
						$this->json_ret['text'] = $i.' e-mail addresses were imported from &quot;'.$set['file'].'&quot; file to &quot;'.$set['group'].'&quot; group';
					}
					Cache::saveSmall('email_import',NULL);
					return;	
				}
				*/
				if (!$set['tell']) {
					$set['emails'] = 0;
					$set['i'] = 1;
				}
				if (!$set['begin']) $set['begin'] = time();
				$set['started'] = time();
				$collected_buffer = '';
				$handle = fopen($file,'r');
				$set['done'] = $set['i'];
				if ($set['tell']) fseek($handle,$set['tell']);
				$j = 0;
				$max = 25000;
				while (($buffer = fgetc($handle))!==false) {
					if (!$buffer) continue;
					$collected_buffer .= $buffer;
					if ($j > $max) {
						$set['tell'] = ftell($handle);
						$set['i']++;
						
						$i = $this->import_emails($collected_buffer, $set['group'], $set['lang']);
						$set['emails'] += $i;
						$set['ended'] = time();
						
						$time_total = ($set['ended'] - $set['begin']) * 100 / ($set['tell'] / $set['size'] * 100);
						$time_left = $time_total - ($set['ended'] - $set['begin']);
						
						$this->json_ret = array(
							'finished'	=> false,
							'title'		=> 'Importing email addresses to &quot;'.$set['group'].'&quot; group from &quot;'.$set['file'].'&quot;, please wait..',
							'descr'		=> File::display_size($set['tell']).' of '.File::display_size($set['size']).'<br />'.number_format($set['emails'],0,'',' ').' email addresses were imported.<br />Time left: '.Date::secondsToTime($time_left).'',
							'percent'	=> round($set['tell'] / $set['size'] * 100),
						);
						if (!$set['emails']) {
							$this->json_ret['error'] = 'No emails were imported, wrong file structure. Please upload a text file where are emails comma, space or paragraph separated.';
						}
						fclose($handle);
						$collected_buffer = '';
						Cache::saveSmall('email_import',$set);						
						return false;
					}
					$j++;
				}
				File::rmdir($dir,false);
				DB::clearCount('emails');
				Cache::saveSmall('email_import',NULL);
				$this->json_ret = array('finished'=>true,'percent'=>100,'file'=>$set['file'],'msg'=>'End');
				if (!$set['emails']) {
					$this->json_ret['error'] = 'No emails were imported, wrong file structure. Please upload a text file where are emails comma, space or paragraph separated.';
				}
				else {
					$this->json_ret['text'] = $set['emails'].' e-mail addresses were imported from &quot;'.$set['file'].'&quot; file to &quot;'.$set['group'].'&quot; group';
				}
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	private function import_emails($str, $group, $lang) {
		$emails = preg_split(self::EMAIL_REGEX,$str,-1,PREG_SPLIT_NO_EMPTY);
		$i = 0;
		foreach ($emails as $e) {
			$e = trim($e,' ,?!');
			if (!$e || strlen($e)<5 || !Parser::isEmail($e)) continue;
			DB::run('REPLACE INTO '.DB_PREFIX.$this->table.' (`email`, `group`, `lang`, `added`, `sent`, `read`, `cnt`, `unsub`) VALUES ('.e($e).', '.e($group).', \''.$lang.'\', '.$this->time.', 0, 0, 0, \'0\')');
			if (DB::affected()) $i++;
		}
		return $i;
	}
	
	public function upload() {
		$this->allow('email','import');
		
		$handler = new UploadHandler;
		
		$filename = File::unchar($_FILES['Filedata']['name']);
		$tmp_name = $_FILES['Filedata']['tmp_name'];
		
		$nameonly = nameOnly($filename);
		$ext = ext($filename);
		$filename = File::getUnique($dir,$nameonly,$ext);
		
		$dir = FTP_DIR_ROOT.DIR_FILES.'temp/'.$this->Index->Session->UserID.'/';
		if (!is_dir($dir)) mkdir($dir,0777);
		$to = $dir.$filename;
		
		$handler->prepare($tmp_name,$filename,$to);
		$filename = $handler->file_name;
		
		$from = $_FILES['Filedata']['tmp_name'];
		
		$handler->prepare($tmp_name,$filename,$to);
		
		$filename = $handler->file_name;
		$_SESSION['import_file'] = $filename;

		$handler->move($from, $to);
		echo json_encode($handler->response());
	}
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'imp':
				$this->allow('email','import');
				$err = array();
				if (!$this->data['group']) $err['group'] = lang('$Group is empty');
				if (!$this->data['emails']) $err['emails'] = lang('$Paste your emails');
				else {
					$emails = array_unique(preg_split(self::EMAIL_REGEX,$this->data['emails']));
					if (!$emails || !Parser::isEmail($emails[0])) {
						$err['emails'] = lang('$First email address is incorrect');	
					}
				}
				$this->errors($err);
				$i = 0;
				foreach ($emails as $e) {
					$e = trim($e,' ,?!');
					if (!$e) continue;
					if (!Parser::isEmail($e)) continue;
					$data = array(
						'email'	=> $e,
						'group'	=> $this->data['group'],
						'lang'	=> $this->data['lang'],
						'cnt'	=> 0,
						'unsub'=> '0',
						'added'	=> $this->time
					);
					DB::replace($this->table, $data);
					if (DB::affected()==1) {
						$i++;	
					}
				}
				$this->msg_delay = 1500;
				if ($i) {
					$this->msg_type = 'bubble';
					$this->msg_text = lang('$Completed, %1 email addresses were imported',$i);
				} else {
					$this->msg_type = 'block';
					$this->msg_text = lang('$Failure, no email addresses were imported',$i);
				}
				$this->msg_js = 'S.A.W.close();S.A.L.get(\'?'.URL_KEY_ADMIN.'=email&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=db\',false,\'db\');';
				$this->msg_reload = false;
			break;	
		}
	}
	private function sql() {
		if ($this->find) {
			$this->filter = ' AND email LIKE '.e('%'.$this->find.'%').'';
		}
		if ($this->group) $this->filter .= ' AND `group`='.e($this->group).'';
		if (get('unsub')) $this->filter .= ' AND unsub=\'1\'';
		if (get('language')) $this->filter .= ' AND lang='.e(get('language'));	
	}
	public function listing() {
		$set = Cache::getSmall('email_import');
		if ($set) return false;
		if (get('do')=='delete' && $this->group) {
			DB::run('DELETE FROM '.DB_PREFIX.'emails WHERE `group`='.e($this->group));
			$this->group = false;
			DB::clearCount('emails');
		}
		elseif (get('do')=='unsub' && $this->group) {
			DB::run('UPDATE '.DB_PREFIX.'emails SET unsub=\'1\' WHERE `group`='.e($this->group));
		}
		elseif (get('do')=='sub' && $this->group) {
			DB::run('UPDATE '.DB_PREFIX.'emails SET unsub=\'0\' WHERE `group`='.e($this->group));
		}
		elseif (get('do')=='rename' && $this->group) {
			DB::run('UPDATE '.DB_PREFIX.'emails SET `group`='.e(get('rename')).' WHERE `group`='.e($this->group));
			$this->group = get('rename');
			DB::clearCount('emails');
		}
		
		
		$this->button['save'] = true;
		if ($this->submitted) {
			foreach ($this->data['old_email'] as $k => $v) {
				if ($k==='new') {
					foreach ($v as $i => $new) {
						if (!$new || !Parser::isEmail($new)) continue;
						$data = array(
							'email'	=> $new,
							'name'	=> $this->data['name']['new'][$i],
							'group'	=> $this->data['group']['new'][1],
							'lang'	=> $this->data['lang']['new'][$i],
							'unsub'	=> 0,
						);
						DB::noerror();
						DB::insert('emails',$data);
					}
				} else {
					if ($this->data['del'][$k]) {
						DB::noerror();
						DB::run('DELETE FROM '.DB_PREFIX.'emails WHERE email LIKE '.e($v).' AND `group` LIKE '.e($this->data['old_group'][$k]));
					} else {
						if (Parser::isEmail($this->data['email'][$k])) {
							$data = array(
								'email'	=> $this->data['email'][$k],
								'name'	=> $this->data['name'][$k],
								'group'	=> $this->data['group'][$k],
								'unsub'	=> $this->data['unsub'][$k],
								'lang'	=> $this->data['lang'][$k],
							);
							DB::noerror();
							DB::update('emails',$data,$v,'email',' AND `group`='.e($this->data['old_group'][$k]));
						}
					}
				}
			}
		}
		$this->array['groups'] = DB::getAll('SELECT `group` AS name, CONCAT(`group`,\' (\',COUNT(1),\')\') AS val FROM '.DB_PREFIX.'emails WHERE `group`!=\'\' GROUP BY `group` ORDER BY `group`','name|val');
		$this->sql();
		$data = array();
		$sql = 'SELECT * FROM '.DB_PREFIX.'emails WHERE TRUE'.$this->filter.' ORDER BY email';
		$qry = DB::qry($sql, $this->offset, $this->limit);
		while ($rs = DB::fetch($qry)) {
			$rs['added'] = date('d.m.Y',$rs['added']);
			$rs['sent'] = date('d.m.Y',$rs['sent']);
			$rs['name'] = strform($rs['name']);
			$rs['email'] = strform($rs['email']);
			$rs['group'] = strform($rs['group']);
			$data[] = $rs;
		}
		$this->total = DB::getCount('emails',$this->filter);
		$this->nav();
		$this->json_data =  json($data);
	}
	
	public function window() {
		if (get('import')) {
			$this->uploadHash();
			$this->win('email_import');
		}
	}
}




















