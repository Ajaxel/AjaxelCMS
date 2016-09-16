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
* @file       inc/CRM.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class CRM extends My {

	public $config = array();
	public $sub_prefix = '';
	public $rs = array();
	public $data = array();
	public $row = array();
	public $filter = '';
	public $user = array();
	public $title = '';
	
	const 
		ACCESS_PUBLIC = 0,
		ACCESS_PRIVATE = 1,
		ACCESS_READ = 2,
		ACCESS_LOCKED = 3,
		ACCESS_LOCKED_READ = 4,
		ACCESS_GROUP_PRIVATE = 5
	;

	const 
		STATUS_HIDDEN = 0,
		STATUS_DEFAULT = 1,
		STATUS_IMPORTANT = 2,
		STATUS_DELETED = 3
	;
	
	const VERSION = 1.25;
	
	public function __construct(&$Index) {
		$this->Index =& $Index;
		$this->prefix = DB::getPrefix();
		$this->UserID =& $this->Index->Session->UserID;
		$this->GroupID =& $this->Index->Session->GroupID;
		$this->ClassID =& $this->Index->Session->ClassID;
		$this->lang =& $this->Index->Session->Lang;
		$this->time = time();
		
		$this->langs = Site::getLanguages();
		$this->catid = request(URL_KEY_CATID);
		$this->action = request(URL_KEY_ACTION);
		$this->page = intval(request(URL_KEY_PAGE));
		
		$this->offset = $this->page * $this->limit;
		$this->id = (int)request('id');
		
		$this->sub_prefix = DB_PREFIX.'crm_';
		$this->use_edit = false;
		$this->conf['countries'] = DB::getAll('SELECT id, code FROM '.DB_PREFIX.'geo_countries ORDER BY code','id|code');
		$this->conf['roles'] = array(
			1	=> 'administrator',
			2	=> 'manager',
			3	=> 'editor',
			4	=> 'viewer'
		);
		$this->conf['merged'] = array();
		$this->conf['window_title'] = array('Add new entry','Edit entry (ID: %1)','Preview entry (ID: %1)');
		$this->conf['window_suffix'] = '';
		$this->conf['lock_seconds'] = 2200;
		$this->conf['buttons'] = array('Add','Save');
		$this->conf['status'] = self::STATUS_DEFAULT;
		$this->conf['access'] = self::ACCESS_PUBLIC;
		$this->conf['checkboxes'] = array();
		$this->conf['datepickers'] = array();
		$this->conf['time_added'] = '<\s\p\a\n \c\l\a\s\s=\c-\t\i\m\e>H:i<\/\s\p\a\n>';
		$this->conf['added'] = $this->conf['time_added'].' d M';
		$this->conf['checks'] = array();
		$this->conf['lock'] = false;
		$this->conf['delete'] = false;
		$this->conf['window_width'] = 800;
		$this->conf['statuses'] = array(
			'HIDDEN'	=> self::STATUS_HIDDEN,
			'DEFAULT'	=> self::STATUS_DEFAULT,
			'IMPORTANT'	=> self::STATUS_IMPORTANT,
			'DELETED'	=> self::STATUS_DELETED
		);
		$this->conf['accesses'] = array(
			'PUBLIC'		=> self::ACCESS_PUBLIC,
			'PRIVATE'		=> self::ACCESS_PRIVATE,
			'READ'			=> self::ACCESS_READ,
			'LOCKED'		=> self::ACCESS_LOCKED,
			'LOCKED_READ'	=> self::ACCESS_LOCKED_READ,
			'GROUP_PRIVATE'	=> self::ACCESS_GROUP_PRIVATE
		);
		$this->conf['status_classes'] = array(
			self::STATUS_HIDDEN 	=> 'hidden',
			self::STATUS_IMPORTANT	=> 'important',
			self::STATUS_DELETED	=> 'deleted',
			self::STATUS_DEFAULT	=> ''
		);
		
		$this->table = $this->name;
		$this->conf['lock_filter'] = ' AND (userid='.$this->UserID.' OR access!='.self::ACCESS_PRIVATE.') AND (locker=0 OR locker='.$this->UserID.' OR locked<'.($this->time - $this->conf['lock_seconds']).' OR (SELECT 1 FROM sessions s WHERE s.userid=locker)!=1)';
		if ($this->UserID) {
			$this->user = DB::row('SELECT * FROM '.$this->sub_prefix.'users WHERE setid='.$this->UserID);
			if (!$this->user) {
				$has = DB::row('SELECT 1 FROM '.$this->sub_prefix.'users');
				if (!$has) {
					$data = array(
						'userid'	=> $this->UserID,
						'added'		=> time(),
						'groupid'	=> 1,
						'status'	=> 1,
						'setid'		=> $this->UserID
					);
					DB::insert('crm_users',$data);	
				}
			}
			$this->conf['user'] =& $this->user;
			if ($this->user['roleid']) {
				$this->is_role_admin = $this->user['userid']==$this->UserID;
			}
		}
		
	}
	
	public function run() { 
		if ($this->conf['checks']) {
			
		}
		$this->conf['checks_total'] = count($this->conf['checks']);
		if (!$this->conf['lock']) $this->conf['lock_filter'] = ' AND userid='.$this->UserID;
	}
	
	public function json() {
		if (Session()->login_try) {
			$this->toDialog(Session()->getMsg());
		} else {			
			$this->action();
		}
		$this->end();
	}
	
	public function upload() {
		if (!$this->user) return false;
		$filename = $_FILES['Filedata']['name'];
		if (!$filename) {
			echo 'No file was uploaded';
			return true;
		}
		if (!File::valid($filename)) {
			echo 'Uploading file format cannot be uploaded to server';
			return true;
		}
		$id = Site::$upload[0];
		$name = Site::$upload[1];
		$prefix = FTP_DIR_ROOT.'files/crm/';
		if (!$id || $name=='mail') {
			$name = 'temp';
			$id = $this->UserID;
		}
		$path = $name.'/'.$id.'/';
		if (!is_dir($prefix.$path)) mkdir($prefix.$path,0777);
		if (is_file($prefix.$path.$file)) unlink($prefix.$path.$file);
		if (move_uploaded_file($_FILES['Filedata']['tmp_name'],$prefix.$path.$filename)) {
			echo '1/'.$path.$filename;
		} else {
			echo 'Cannot upload this file: ('.$file.'). Please try again';
		}
		$this->upload_end($path.$filename);
		return $path.$filename;
	}
	
	protected function upload_end($file) {
			
	}
	
	public function head() {
		if (!$this->Index->show('head')) return;
		$this->Index->addJSA('jquery/'.JQUERY);
		if (defined('JQUERY_MIGRATE') && JQUERY_MIGRATE) $this->Index->addJSA('jquery/'.JQUERY_MIGRATE);
		$this->Index->addJSA('jquery/'.JQUERY_UI);
		$this->Index->addJSA('global.js');
		$this->Index->addJSA('crm.js');
		$this->Index->addJSA('tinymce/jquery.tinymce.min.js');
		$this->Index->addJSA('plugins/jquery.blockUI.js');
		$this->Index->addJSA('plugins/jquery.colorbox.js');
		$this->Index->addJSA('swfobject.min.js');
		$this->Index->addJSA('uploadify/'.JQUERY_UPLOADIFY);
		$this->Index->addCSSA('crm.css');
		$this->Index->addCSSA('colorbox/'.JQUERY_COLORBOX.'/colorbox.css');
		$this->Index->addCSSA('ui/'.($_SESSION['UI_ADMIN']?$_SESSION['UI_ADMIN']:((defined('UI_ADMIN') && UI_ADMIN)?UI_ADMIN:'start')).'/'.JQUERY_CSS);
	//	if (USE_IM) Factory::call('im')->html('init');
	}
	
	protected function allowed() {		
		return false;
	}
	protected function ids() {
		$this->ids = post('data','ids',array());
		if (!$this->ids) {
			$this->msg = 'Nothing checked!';
			$this->msg_type = 'warning';
			return false;	
		} else $this->checked = count($this->ids);
		return true;	
	}
	
	public function index() {
		$this->Index->hide('header');
		$this->Index->hide('footer');
		$this->set('conf',$this->conf);
		parent::index();
	}
	
	
	protected function find() {
		
	}
	
	protected function config() {
		// nothing
	}
	

	protected function crm_action() {
		switch ($this->action) {
			case 'find':
				if (!$this->allowed()) break;
				$this->json_ret = $this->find();
			break;
			case 'save':
				if (!$this->allowed()) break;
				$this->json_ret = $this->save();
			break;
			case 'action':
				if (!$this->allowed()) break;
				$this->json_ret = $this->action();
			break;
			default:
				$this->global_action();
			break;	
		}
	}
	
	protected function lock_check() {
		$msg = false;
		if (!$this->user) {
			$msg = 'Somehow you were logged out, please login.';
		}
		elseif ($this->id) {
			$this->rs = DB::row('SELECT * FROM '.$this->sub_prefix.$this->name.' WHERE id='.$this->id);
			if ($this->rs['userid']!=$this->UserID && $this->rs['access']==self::ACCESS_PRIVATE) {
				$msg = 'You cannot edit this entry since it is private in access level';	
			}
			elseif ($this->rs['userid'] && $this->rs['userid']!=$this->UserID && ($this->rs['access']==self::ACCESS_READ || $this->rs['access']==self::ACCESS_LOCKED_READ)) {
				$msg = 'This entry has been set to read-only access level and only original registrator may edit it';	
			}
			elseif ($this->is_locked($this->rs)) {
				$user = Data::getUser($this->rs['userid']);
				$msg = 'This entry is locked by '.$user['login'].' on '.date('H:i',$this->rs['locked']).' GMT. Editing permitted!';	
			}
			if ($msg) {
				$this->msg($msg,'shield',2500);
			}
		}
		return $msg;
	}
	
	
	protected function window_save() {
		
	}
	
	protected function window_commit() {
			
	}
	
	protected function save_end() {
		if (!$this->errors() && $this->user) {
			unset($this->data['id'], $this->data['userid'], $this->data['added']);
			foreach ($this->conf['merged'] as $col) {
				if (isset($this->data[$col]) && $this->data[$col]) $this->data[$col] = ','.@join(',',$this->data[$col]).',';
				else $this->data[$col] = '';
			}
			foreach ($this->conf['checkboxes'] as $col) $this->data[$col] = (isset($this->data[$col])?$this->data[$col]:'');
			foreach ($this->conf['checks'] as $i => $a) {
				if (isset($this->data['ch'.$i.'_date'])) $this->data['ch'.$i.'_date'] = $this->toTimestamp($this->data['ch'.$i.'_date']);
			}
			foreach ($this->conf['datepickers'] as $col => $format) {
				if (isset($this->data[$col])) $this->data[$col] = $this->toTimestamp($this->data[$col], array('Hour'=>@$this->data[$col.'_Hour'],'Minute'=>@$this->data[$col.'_Minute']));
				unset($this->data[$col.'_Hour'],$this->data[$col.'_Minute']);
			}
			/*
			if (isset($this->data['assign'])) {
				if ($this->data['assign']) {
					$ex = explode(',',trim($this->data['assign'],','));
					$j = array();
					foreach ($ex as $e) {
						$e = trim($e);
						if (!$e) continue;
						if (is_numeric($e)) {
							$j[] = $e;
						} else {
							$f = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE login LIKE '.e($e).' AND active!=2');
							if ($f) $j[] = $f;
						}
					}
					$this->data['assign'] = ','.join(', ',array_unique($j)).',';
				} else {
					$this->data['assign'] = '';	
				}
			}
			*/
			
			$this->window_save();
			
			if (!$this->id) {
				$this->data['added'] = time();
				$this->data['userid'] = $this->UserID;
				DB::insert('crm_'.$this->name,$this->data);
				$this->id = DB::id();
			} else {
				if ($this->rs['userid']!=$this->UserID) {
					unset($this->data['access']);
				}
				$this->data['edited'] = time();
				DB::update('crm_'.$this->name,$this->data,$this->id);
			}
			if ($this->data['attachments']) {
				$dir = FTP_DIR_ROOT.'files/crm/'.$this->name.'/'.$this->id.'/';
				if (!is_dir($dir)) mkdir($dir,0777);
				foreach ($this->data['attachments'] as $file) {
					if (substr($file,0,5)=='temp/') {
						$from = FTP_DIR_ROOT.'files/crm/'.$file;
						if (!is_file($from)) continue;
						$filename = fileOnly($file);
						$nameonly = nameOnly($filename);
						$ext = ext($filename);
						$new_file = File::getUnique($dir,$nameonly,$ext);
						rename($from,$dir.$new_file);
					}
				}
			}
			$this->window_commit();
			if (post('diff_name') && post('diff_id')) {
				return $this->set_diff(post('diff_name'),post('diff_id'));
			} else {
				return $this->find();
			}	
		}	
	}
	
	public function log($title, $data) {
		$data = array(
			'setid'	=> $this->id,
			'table'	=> $this->table,
			'area'	=> $this->name,
			'title'	=> $title,
			'data'	=> strjoin($data),
			'added'	=> $this->time,
			'userid'=> $this->UserID
		);
		DB::insert('crm_log', $data, false, true);
		return DB::id();
	}
	
	public function page() {
		$this->set('name',$this->name);
		$this->set('config',$this->Config->config);
		if ($this->user) {
			$this->set('data',$this->find());
		}
		if (SITE_TYPE!='ajax') {
			$tpl_file = $this->name;
			if (!is_file(FTP_DIR_TPL.'crm/'.$this->name.'.tpl')) {
				$tpl_file = 'undefined';
			}
			$this->set('tpl_file',$tpl_file);
			$this->display('crm/index.tpl');
		} else {
			if (!$this->user) $this->name = 'login';
			$this->display('crm/inc/tab_top.tpl');
			if ($this->name=='index' || is_file(FTP_DIR_TPL.'crm/'.$this->name.'.tpl')) {
				$this->display('crm/'.$this->name.'.tpl');
			} else {
				$this->display('crm/undefined.tpl');
			}
			$this->display('crm/inc/tab_bot.tpl');
		}
	}
	
	protected function catchList(&$row) {

	}
	
	protected function color(&$row) {
		if (isset($row['status']) && isset($this->conf['status_classes'][$row['status']])) {
			$row['class'] = $this->conf['status_classes'][$row['status']];
		}
	}
	
	protected function catchRow(&$row, $full = false, $form = false) {
		if (isset($row['country']) && isset($this->conf['countries'][$row['country']])) {
			$row['code'] = $this->conf['countries'][$row['country']];
		}
		if ($form) {
			$row['ch'] = array();
			foreach ($this->conf['checks'] as $i => $a) {
				if (!isset($row['ch'.$i])) continue;
				$row['ch'][$i]['checked'] = $row['ch'.$i];
				$row['ch'][$i]['date'] = $this->fromTimestamp($row['ch'.$i.'_date']);
			}
			
			foreach ($this->conf['datepickers'] as $col => $format) {
				if (isset($row[$col]) && $row[$col]>0) {
					$row[$col.'_Hour'] = date('H',$row[$col]);
					$row[$col.'_Minute'] = date('i',$row[$col]);
					$row[$col] = $this->fromTimestamp($row[$col]);
				}
				else {
					$row[$col] = '';
					$row[$col.'_Hour'] = date('H');
				}
			}
			if (!$this->id) {
				$row['id'] = 0;
				$row['status'] = $this->conf['status'];
				$row['access'] = $this->conf['access'];
			}
			foreach ($this->conf['merged'] as $col) {
				if (isset($row[$col])) $row[$col] = array_string(explode(',',trim($row[$col],',')));
			}
			
		} else {
			$this->color($row);
			foreach ($this->conf['checks'] as $i => $a) {
				if ($row['ch'.$i.'_date']>0) {
					$row['ch'.$i.'_date'] = date('d M Y',$row['ch'.$i.'_date']);
				}
			}
			if (isset($row['added']) && $row['added']>0) $row['added'] = date($this->conf['added'],$row['added']);
			foreach ($this->conf['datepickers'] as $col => $format) {
				if (isset($row[$col]) && $row[$col]>0) $row[$col] = date($format,$row[$col]);
				else $row[$col] = '';
			}
			if ($full) {
				foreach ($this->conf['merged'] as $col) {
					if (isset($row[$col]) && strlen(trim($row[$col],','))) $row[$col] = array_values(Data::getVal('my:'.$col,$row[$col]));
				}
			} else {
				$this->catchList($row);
			}
		}
		/*
		if (isset($row['assign']) && $row['assign']) {
			$row['assign'] = join(', ',DB::getAll('SELECT login FROM '.DB_PREFIX.'users WHERE id IN ('.trim($row['assign'],',').') AND active!=2','login'));
		}
		*/
	}	
	
	protected function one($form = false) {
		$this->id = (int)request($this->name);
		if (!$this->id) {
			$row = array();
			$this->catchRow($row, true, $form);
			return $row;
		}
		$row = DB::row('SELECT * FROM '.$this->sub_prefix.$this->name.' WHERE id='.$this->id);
		$this->catchRow($row, true, $form);
		return $row;
	}
	

	protected function can($id) {
		if (!$id) return false;
		$lock = DB::row('SELECT a.userid, a.locker, a.locked, a.access FROM '.$this->sub_prefix.$this->name.' a WHERE id='.$id);
		if ($lock['access']==self::ACCESS_LOCKED || $lock['access']==self::ACCESS_LOCKED_READ) {
			$this->msg('This entry is already locked by access level,<br /> locking is disabled.','shield',2000);
			return false;
		}
		if ($this->is_locked($lock)) {
			$user = Data::getUser($lock['locker']);
			$this->msg('This entry is locked by '.$user['login'].' on '.date('H:i',$lock['locked']).' GMT','clock',4000,false);
			return false;
		}
		return true;
	}
	
	protected function parseMail($s) {
		return $s;
	}
	
	protected function mail() {
		require_once FTP_DIR_ROOT.'inc/Email.php';
		$from_mail = ($this->data['from_mail']?$this->data['from_mail']:MAIL_EMAIL);
		$from_name = ($this->data['from_name']?$this->data['from_name']:MAIL_NAME);
		$arr_headers = array();
		
		$to = $this->data['to'];
		$subject = $this->parseMail($this->data['subject'] ? $this->data['subject'] : '[No subject]', 'subject');
		$body = $this->parseMail($this->data['body'], 'body');
		
		$fromname = $this->Index->Session->Login;
		$fromaddress = $this->Index->Session->Email;
		
		$attachments = array();
		if ($this->data['attachments']) {
			foreach ($this->data['attachments'] as $file) {
				$attachments[] = array(
					'file'	=> FTP_DIR_ROOT.'files/crm/'.$file
				);
			}
		}
		
		if (Email::send($to, $subject, $body, $attachments, $from_mail, $from_name, $arr_headers)) {
			$this->log('Email has been sent to '.$to.': '.$subject, array(
				'to'		=> $to,
				'subject'	=> $subject,
				'body'		=> $body
			));	
		}
		File::rmdir(FTP_DIR_FILES.'temp/'.$this->UserID.'/');
		$this->msg('Email has been sent','letter');
		$this->ok = true;
	}
	
	protected function action() {
		if (!$this->allowed()) return false;
		switch ($this->action) {
			case 'find':
				$this->json_ret = $this->find();
			break;
			case 'save':
				$this->json_ret = $this->save();
			break;
			case 'action':
				$this->json_ret = $this->action();
			break;
			case 'print':
			
			break;
			case 'status':
				if (!post('id')) break;
				$sql = 'UPDATE '.$this->sub_prefix.$this->name.' SET status='.(int)post('status').' WHERE id='.(int)post('id').$this->conf['lock_filter'];
				DB::run($sql);
				$aff = DB::affected();
				if ($aff) {
				//	$this->msg = 'Status has been changed';
					$this->msg_ok = true;
				} else {
					$this->msg_type = 'stop';
					$this->msg = 'Cannot change status';
					$this->msg_ok = false;
				}
			break;
			case 'mailtpl':
				$this->json_ret = DB::row('SELECT * FROM '.$this->sub_prefix.'mailtpl WHERE name LIKE '.e(post('name')));
			break;
			case 'lock':
				if (!$this->UserID) {
					return $this->msg('Please login!','block',2000,false);
				}
				if (!$this->ids()) return;
				if ($this->checked!=1){
					return $this->msg('You may not lock more than one','warning');
				}
				$id = (int)$this->ids[0];
				if ($this->can($id)) {
					DB::run('UPDATE '.$this->sub_prefix.$this->name.' SET locker='.$this->UserID.', locked='.$this->time.' WHERE id='.$id); 
					$this->msg_js = 'CRM.lock('.$id.');';
				}
			break;
			case 'unlock':
				DB::run('UPDATE '.$this->sub_prefix.$this->name.' SET locker=0, locked=0 WHERE locker='.$this->UserID);
				$this->msg_js = 'CRM.unlock();';
			break;
			case 'delete':
				if (!$this->UserID) {
					return $this->msg('Please login!','block',2000,false);
				}
				if (!$this->ids()) return;
				
				$data = DB::getAll('SELECT * FROM '.$this->sub_prefix.$this->name.' WHERE id IN ('.join(', ',$this->ids).')'.$this->conf['lock_filter']);
				if ($this->conf['delete']) {
					$sql = 'DELETE FROM '.$this->sub_prefix.$this->name.' WHERE id IN ('.join(', ',$this->ids).')'.$this->conf['lock_filter'];
				} else {
					$sql = 'UPDATE '.$this->sub_prefix.$this->name.' SET status='.self::STATUS_DELETED.' WHERE id IN ('.join(', ',$this->ids).')'.$this->conf['lock_filter'];
				}
				
				DB::run($sql);
				$aff = DB::affected();
				if ($aff) {
					$this->msg($aff.' entries were trashed','trash');
					$this->log('Mass delete',$data);
					$this->json_ret = $this->find();
				} else {
					$this->msg('NO entries were removed, no access','warning');	
				}
			break;
			default:
				$this->crm_action();
			break;	
		}
	}

	protected function is_locked($lock) {
		
		if (isset($lock['locker']) && $lock['locker']!=$this->UserID && Session::isOnline($lock['locker']) && $lock['locked'] > time() - $this->conf['lock_seconds']) {
			return true;
		}
		return false;
	}
	
	protected function window_row() {
		
	}
	

	
	
	public function window() {
		if (!in_array($this->action,$this->Config->wins)) {
			echo 'Wrong action: '.$this->action.' ('.join(', ',$this->Config->wins).'';
			return false;
		}
		if (!$this->name) {
			echo 'Name is undefined!';
			return false;
		}
		$this->set('conf',$this->conf);
		$this->id = request($this->name);
		if (!$this->id_custom) $this->id = (int)$this->id;
		if (!$this->UserID) {
			$this->set('title','You need to login!');
			$this->display('crm/inc/win_top.tpl');
			echo '<tr><td colspan="4" style="text-align:center">';
			$this->display('crm/login.tpl');
			echo '</td></tr>';
			$this->display('crm/inc/win_bot.tpl');
		}
		$this->row = array_merge($this->one(true), $this->row);
		$this->row['files'] = array();
		if ($this->id) {
			$dir = FTP_DIR_ROOT.'files/crm/'.$this->name.'/'.$this->id;
			if (is_dir($dir)) {
				$dh = opendir($dir);
				while ($file = readdir($dh)) {
					if (File::ok($file)) {
						$this->row['files'][$file] = $this->name.'/'.$this->id.'/'.$file;	
					}
				}
			}
		}
		if (!$this->UserID) return;
		$this->window_row();
		$_POST['data'] = $this->row;
		$this->set('id',$this->id);
		$this->set('upload',array(
			'name'	=> $this->name,
			'id'	=> $this->id,
			'hash'	=> Site()->genHash($this->id, $this->name)
		));
		if (!$this->title) {
			if ($this->id_custom) {
				$this->title = lang($this->conf['window_title'][1],$this->id_custom);
			}
			elseif ($this->row['id']) {
				if ($this->row['userid']!=$this->UserID && ($this->row['access']==self::ACCESS_LOCKED || $this->rs['access']==self::ACCESS_LOCKED_READ)) {
					$this->title = lang($this->conf['window_title'][2],$this->id).' [locked by access-level]';	
				}
				elseif ($this->is_locked($this->row)) {
					$user = Data::getUser($this->row['locker']);
					$this->title = lang($this->conf['window_title'][2],$this->id).' [locked by <a href="javascript:;" onclick="CRM.user('.$this->rs['locker'].')">'.$user['login'].'</a>, free in <span class="c-countdown" id="c-countdown_'.$this->name.'_'.$this->id.'">'.Date::secondsToTime($this->conf['lock_seconds'] - ($this->time - $this->row['locked'])).'</span>]';
				} else {
					$this->title = lang($this->conf['window_title'][1],$this->id);
					if (isset($this->row['locker']) && $this->row['locker']==$this->UserID) $this->title .= ' [locked by self, auto-unlock in <span class="c-countdown" id="c-countdown_'.$this->name.'_'.$this->id.'">'.Date::secondsToTime($this->conf['lock_seconds'] - ($this->time - $this->row['locked'])).'</span>]';
				}
			} else {
				$this->title = lang($this->conf['window_title'][0]);
			}
		}
		$this->win();
	}
	
	
	protected function win() {
		if (!$this->name) {
			echo 'Name is undefined!';
			return false;
		}
		$file = FTP_DIR_TPL.$this->area.'/'.$this->name.'_'.$this->action.'.tpl';
		if (is_file($file)) {
			$this->set('save',true);
			$this->set('name',$this->name);
			$this->set('table',post('name'));
			$this->set('title',$this->title.$this->conf['window_suffix']);
			$this->set('win_id',$this->name.'_'.$this->id);
			$this->set('diff_id',get('diff_id'));
			$this->set('diff_name',get('diff_name'));
			$this->set('row',$this->row);
			$this->display($this->area.'/inc/win_top.tpl');
			$this->display($this->area.'/'.$this->name.'_'.$this->action.'.tpl');
			$this->display($this->area.'/inc/win_bot.tpl');	
		} else {
			$this->set('title','Error');
			$this->display($this->area.'/inc/win_top.tpl');
			echo '<tr><td colspan="4" style="text-align:center">';
			echo '<h3>File: <pre>'.$file.'</pre> cannot be found.</h3>';
			echo '</td></tr>';
			$this->display($this->area.'/inc/win_bot.tpl');
		}
	}
}
























