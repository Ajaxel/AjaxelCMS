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
* @file       inc/IM.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class IM {
	
	protected
		$Index = false,
		$connect = false,
		$limit = 30,
		$limit_contacts = 50,
		$total = 0,
		$offset = 0,
		$folder = 'INBOX',
		$to = 0,
		$to_id = 0,
		$to_ip = 0,
		$filter_to = '',
		$filter_from = '',
		$online = false,
		$action = false,
		$time = 0
	;
	private
		$allowed = false,
		$error = false,
		$read = array(),
		$user = array(),
		$contact = array(),
		$settings = array(),
		$auto_add = true, // need something?
		$tpl_folder = 'js/'
	;
	
	const
		FOLDER_COMMON = 1,
		FOLDER_FAVORITES = 2,
		FOLDER_IGNORED = 3,
		FOLDER_ALL = 4,

		KEY_TYPE = 'type',
		KEY_BOARD = 'board'
	;
	
	public function __construct($params = array()) {
		
		$this->UserID = Session()->UserID;		
		$this->IP = intval(Session()->IPlong);
		if (!$this->IP && !$this->UserID) return false;
		if (Session()->isBot) return $this;
		if ($params) {
			foreach ($params as $k => $v) $this->$k = $v;
		}
		$this->time = time();
		$this->Index = Index();
		$this->action = request(URL_KEY_ACTION);
		$limit = intval(request(URL_KEY_LIMIT));
		if ($limit) $this->limit = $limit;
		$this->offset = intval(request(URL_KEY_PAGE)) * $this->limit;
		$this->folder = request(URL_KEY_FOLDER);
		if (!$this->folder) $this->folder = self::FOLDER_COMMON;
		$this->online = isset($_GET[URL_KEY_ONLINE]);
		if ($this->UserID) {
			$this->filter_me = 'i.to_id='.$this->UserID;
		} else {
			$this->filter_me = 'i.to_ip='.$this->IP;	
		}
		$this->allow();
		$this->onlineperiod = ' AND s.expiration>'.($this->time-SESSION_LIFETIME);
		$this->onlineperiod = '';
		if (!isset($_SESSION['IM_new'])) $_SESSION['IM_new'] = 0;
		if (is_dir(FTP_DIR_TPL.'im/')) $this->tpl_folder = '';
		return $this;
	}
	
	private function allow() {
		$this->allowed = $this->UserID;
		$this->allowed = true;
		/*
		$this->allowed = 0;
		$this->error('You need to login to chat with such user');
		*/
	}
	
	public function load() {
		if (!$this->to) $this->to = request(URL_KEY_TO);
		if (Session()->isBot) return $this;
		if (!isset($_SESSION['IM_wins']) || !is_array($_SESSION['IM_wins'])) $_SESSION['IM_wins'] = array();
		
		if (is_numeric($this->to) && ($this->user[$this->to] = $this->getUser($this->to))) {
			$this->to_id = (int)$this->to;
			$this->filter_to = 'to_id='.$this->to_id.' AND to_ip=0';
			$this->filter_from = 'from_id='.$this->to_id.' AND from_ip=0';
		}
		elseif (is_numeric($this->to)) {
			$this->to_ip = $this->to;
			$this->filter_to = 'to_ip='.$this->to_ip.' AND to_id=0';
			$this->filter_from = 'from_ip='.$this->to_ip.' AND from_id=0';
		}
		elseif ($this->to && Parser::isIP($this->to)) {
			$this->to_ip = ip2long($this->to);
			$this->filter_to = 'to_ip='.$this->to_ip.' AND to_id=0';
			$this->filter_from = 'from_ip='.$this->to_ip.' AND from_id=0';
		}
		if ($this->to) {
			if ($this->to_id && $this->to_id==$this->UserID) {
				return $this->error('No self chatting allowed.');
			}
			if ($this->UserID) {
				$this->filter_im = '((from_id='.$this->UserID.' AND '.$this->filter_to.') OR (to_id='.$this->UserID.' AND '.$this->filter_from.'))';
				$this->select_order = '(CASE WHEN from_id='.$this->UserID.' AND '.$this->filter_to.' THEN 1 ELSE 0 END) AS num';
			} else {
				$this->filter_im = '((from_ip='.$this->IP.' AND '.$this->filter_to.') OR (to_ip='.$this->IP.' AND '.$this->filter_from.'))';
				$this->select_order = '(CASE WHEN from_IP='.$this->IP.' AND '.$this->filter_to.' THEN 1 ELSE 0 END) AS num';
			}
		}
		return $this;
	}
	
	
	public function deleteUser($id) {
		$id = (int)$id;
		$qry = DB::qry('SELECT id FROM '.DB_PREFIX.'im WHERE from_id='.$id.' OR to_id='.$id,0,0);
		while ($rs = DB::fetch($qry)) {
			DB::run('DELETE FROM '.DB_PREFIX.'im_sub WHERE setid='.$rs['id']);
		}
		DB::run('DELETE FROM '.DB_PREFIX.'im WHERE from_id='.$id);
		DB::run('UPDATE '.DB_PREFIX.'im SET deleted=\'Y\' WHERE to_id='.$id);
		DB::run('DELETE FROM '.DB_PREFIX.'im_set WHERE userid='.$id);
	}
	public function insertUser($id) {
		DB::run('INSERT INTO '.DB_PREFIX.'im_set (`on`, `sound`, `userid`) VALUES (\'Y\', \'Y\', '.(int)$id.')');
	}
	
	public function html($type, $data = false) {

		if (!$this->allowed) return false;
		switch ($type) {
			case 'init':
				$this->init();
			break;
			case 'opts':
				return array(
					'lang'	=> array(
						'more'			=> lang('More..'),
						'sending'		=> lang('Sending...'),
						'typing'		=> lang('%1 is typing...'),
						'block_title'	=> lang('Are you sure to block this person?'),
						'block_text'	=> lang('This user will be moved to ignore list and you will no longer receive any alerts from this person.'),
						'unblock_title'	=> lang('Are you sure to unblock this person?'),
						'unblock_text'	=> lang('This user will be moved back to previous folder where he was before.'),
						'off'			=> lang('Go to offline'),
						'on'			=> lang('Go to online'),
						'sound'			=> lang('Play sound for new messages'),
						'online'		=> lang('Show online contacts only'),
						'expanded'		=> lang('Leave this chat expanded'),
						'smilies'		=> lang('Smilies'),
						'color'			=> lang('Color picker'),
						'help'			=> lang('Help contents'),
						'about'			=> lang('About IM chat'),
						'switched_off'	=> lang('Your status is offline, sending messages is not allowed.'),
						'please_type'	=> lang('Please type a message'),
						'move_to'		=> lang('Move to folder:'),
						'common'		=> lang('Common'),
						'userlist'		=> lang('Userlist'),
						'settings'		=> lang('Settings'),
						'male'			=> lang('Male'),
						'female'		=> lang('Female'),
						'login_notify'	=> lang('You need to login to see more actions'),
						'no_photo'		=> lang('No photo'),
						'no_online'		=> lang('No online contacts found'),
						'no_contacts'	=> lang('No contacts found'),
						'off_mode'		=> lang('Offline mode'),
						'no_new_msg'	=> lang('No new messages')
					),
					'folders'	=> $this->folders(),
					'settings'	=> $this->settings(),
					'sm'		=> Parser::bb_sm_get(false)
				);
			break;
			case 'win':
				if ($this->tpl_folder) $this->Index->Smarty->template_dir = FTP_DIR_TPLS;
				$this->Index->Smarty->assign('tpl_folder',$this->tpl_folder);
				return OB::friendly($this->Index->Smarty->fetch($this->tpl_folder.'im/win.tpl'));
			break;
			case 'folders':
				$ret = array();
				$ret[self::FOLDER_COMMON] = lang('Common');
				$ret[self::FOLDER_FAVORITES] = lang('Favorites');
				$ret[self::FOLDER_IGNORED] = lang('Ignored');
				$ret[self::FOLDER_ALL] = lang('Show all');
				return $ret;
			break;
			case 'block_allow':
				return $this->blockAllow($data);
			break;
		}
	}
	
	
	private function error($text = false, $type = 'stop') {
		if (!$this->error) {
			$this->error = $text;
			$this->error_type = $type;
		}
		return $this;
	}
	
	public function action() {
		if ($this->error) return array('error' => $this->error, 'error_type' => $this->error_type);
		if (!$this->allowed) return false;
		
		// Init here
		if ($this->action=='init') {
			$ret['opts'] = $this->html('opts');
			$ret['board'] = $this->board();
			return $ret;	
		}
		$ret['js'] = '';
		
		switch ($this->action) {
			case 'loop':
				// loop
			break;
			case 'popup':
				$this->popup($ret, post('win'));
			break;
			case 'block':
				$this->block($ret);
			break;
			case 'unblock':
				$this->unblock($ret);
			break;
			case 'folder':
				$this->folder($ret, post('move'));
			break;
			case 'unload':
				$this->unload();
			break;
			case 'abuse':
				$post = $_POST['data'];
				$data = array(
					'userid'	=> $this->UserID,
					'ip'		=> $this->IP,
					'to'		=> $this->to,
					'reason'	=> $post['reason'],
					'title'		=> 'IM: abuse',
					'descr'		=> $post['descr'],
					'added'		=> $this->time,
					'active'	=> 1
				);
				DB::insert('abuse',$data);
				$ret['text'] = 'Your abuse has been sent to administration, we will review it and will do some action ASAP';
				$ret['type'] = 'shield';
				$ret['delay'] = 5000;
			break;
			case 'open':
				$data = $this->win();
				if (!$data) return $this->error('Cannot open chat with this user');
				$this->Index->Smarty->assign('data', $data);
				$ret['html']['im_win_'.$this->to] = $this->html('win');
				$ret['data'] = $this->get();
				$settings = array(
					'more'	=> 0,
					'offset'=> 0,
					'total'	=> $this->total
				);
				$ret['js'] .= 'S.IM.setTotal(\''.$this->to.'\','.(int)$this->total.');';
				@$_SESSION['IM_wins'][$this->to] = $settings;
			break;
			case 'close':
				$this->close();
				unset($_SESSION['IM_wins'][$this->to]);
			break;
			case 'set':
				$this->set(post('name'), post('val'), $ret);
			break;
			case 'send':
				if (!$_POST['msg']) break;
				$_POST['msg'] = Parser::code_tags($_POST['msg']);
				if (Parser::bb_is_empty($_POST['msg'])) break;
				$conv = '';
				$id = 0;
				$this->send($_POST['msg'], $conv, $id);
				if (!$conv) break;
				$arr = array(
					'my'	=> true,
					'read'	=> false,
					'id'	=> $id,
					'time'	=> Date::dayCountDown($this->time),
					'msg'	=> $conv,
					'cur'	=> true
				);
				$ret['js'] .= 'S.IM.first(\''.$this->to.'\','.json($arr).');';
			break;
			case 'more':
				@$_SESSION['IM_wins'][$this->to]['more']++;
				$this->offset = $_SESSION['IM_wins'][$this->to]['more'] * $this->limit;
				$ret['data'] = $this->get();
			break;
		}
		$ret['board'] = $this->board();
		$ret['folders'] = $this->folders();
		if ($this->action=='loop' || $this->action=='close') {
			$ret['js'] .= $this->fill();
		}
		return $ret;
	}
	
	private function popup(&$ret, $name) {
		$ret['width'] = 0;
		$this->Index->Smarty->assign('name',$name);
		switch ($name) {
			case 'userlist':
				$ret['width'] = 500;
				$ret['height'] = 400;
			break;
			case 'abuse':
				$ret['width'] = 400;
				$ret['height'] = 210;
				$data = array();
				$data['user'] = $this->getUser($this->to_id);
				if (!$data['user']) $data['user']['login'] = long2ip($this->to_ip);
				$this->Index->Smarty->assign('data',$data);
			break;
			case 'help':
				$ret['width'] = 400;
				$ret['height'] = 500;
			break;
			case 'about':
				$ret['width'] = 290;
				$ret['height'] = 130;
			break;
		}
		if ($ret['width']) {
			if ($this->tpl_folder) $this->Index->Smarty->template_dir = FTP_DIR_TPLS;
			$this->Index->Smarty->assign('tpl_folder',$this->tpl_folder);
			OB::friendly($ret['html'] = $this->Index->Smarty->fetch($this->tpl_folder.'im/p_'.$name.'.tpl'));
		}
	}
	
	private function set($name, $val, &$ret) {
		if (!$this->UserID) $_SESSION['IM_set'][$name] = $val;
		else {
			$arr = array('sound','online','on','expanded');
			if (!in_array($name, $arr)) return false;
			DB::run('UPDATE '.DB_PREFIX.'im_set SET `'.$name.'`='.e($val).($name=='on'?', user_left='.$this->time:'').' WHERE userid='.$this->UserID);
		}
	}

	private function settings() {
		if ($this->settings) return $this->settings;
		$ret = array();
		if (!$this->UserID) {
			$ret['on']	= true;
			$ret['sound'] = true;
			$ret['online'] = false;
			$ret['expanded'] = false;
		} else {
			$rs = DB::row('SELECT `sound`, `online`, `on`, `expanded` FROM '.DB_PREFIX.'im_set WHERE userid='.$this->UserID);
			if (!$rs) {
				$this->insertUser($this->UserID);
				$ret['on']	= true;
				$ret['sound'] = true;
				$ret['online'] = false;
				$ret['expanded'] = false;
			} else {
				$ret['on']	= $rs['on']=='Y';
				$ret['sound'] = $rs['sound']=='Y';
				$ret['online'] = $rs['online']=='Y';
				$ret['expanded'] = $rs['expanded']=='Y';
			}
		}
		$ret['user_name'] = ($this->Index->Session->Login ? $this->Index->Session->Login : Session()->IP);
		$ret['userid'] = $this->UserID;
		$this->settings = $ret;
		return $ret;
	}
	
	private function connect() {
		if (!$this->allowed) return false;
		if (isset($this->connect[$this->to])) return $this->connect;
		if (!$this->filter_im) return false;
		$sql = 'SELECT i.*, '.$this->select_order.' FROM '.DB_PREFIX.'im i WHERE '.$this->filter_im.' ORDER BY num DESC, i.sent DESC LIMIT 2';
		
		$ret = DB::getAll($sql);
		if (!$ret) {
			if (!$this->auto_add) return false;
			if ($this->to_id && !DB::one('SELECT 1 FROM '.DB_PREFIX.'users WHERE id='.$this->to_id)) {
				return false;
			}
			$this->add();
			return $this->connect();	
		}
		$ret[0]['win'] = $ret[0]['win']=='Y';
		$ret[1]['win'] = $ret[1]['win']=='Y';
		$ret[$this->to] = $ret[1];
		$this->Index->Smarty->assign('im_connect', $ret);
		$this->connect = $ret;
		return $ret;
	}
	
	private function add() {
		
		$data = array( // me [0]
			'from_id'	=> $this->UserID,
			'to_id'		=> $this->to_id,
			'from_ip'	=> (!$this->UserID?$this->IP:0),
			'to_ip'		=> (!$this->to_id?$this->to_ip:0),
			'folder'	=> self::FOLDER_COMMON,
			'old_folder'=> self::FOLDER_COMMON,
			'added'		=> $this->time,
			'total'		=> 0,
			'total_new'	=> 0,
			'win'		=> 'Y',
			'typing'	=> 0,
			'deleted'	=> 'N'
		);
		DB::insert('im',$data);
		$data = array( // him [1]
			'from_id'	=> $this->to_id,
			'to_id'		=> $this->UserID,
			'from_ip'	=> (!$this->to_id?$this->to_ip:0),
			'to_ip'		=> (!$this->UserID?$this->IP:0),
			'folder'	=> self::FOLDER_COMMON,
			'old_folder'=> self::FOLDER_COMMON,
			'added'		=> $this->time,
			'total'		=> 0,
			'total_new'	=> 0,
			'typing'	=> 0,
			'deleted'	=> 'N'
		);
		DB::insert('im',$data);
	}
	
	private function getTyping() {
		$ret = array();
		if (post(self::KEY_TYPE)) {
			$ex = explode('|', trim($_POST[self::KEY_TYPE],'|'));
			foreach ($ex as $e) {
				$e = explode(':', $e);
				$ret[$e[0]] = $e[1];
			}
		}
		return $ret;
	}
	
	private function blockAllow($user = false) {
		if (!$this->UserID) return false;
		if ($this->to_id) {
			if (!$user) $user = DB::row('SELECT login, groupid FROM '.DB_PREFIX.'users WHERE id='.$this->to_id);
			if ($user['groupid']>1) return false;
			return $user;
		}
		return $user ? $user : array('login' => long2ip($this->to_ip));
	}
	
	private function folder(&$ret, $move) {
		if (!$this->connect()) return false;
		if (!($user = $this->blockAllow())) {
			$ret['text'] = lang('You cannot move administrators to another folders');
			$ret['type'] = 'stop';
			return;	
		}
		DB::run('UPDATE '.DB_PREFIX.'im SET old_folder=folder, folder='.e($move).' WHERE id='.$this->connect[1]['id']);
		if (DB::affected()) {
			$folders = $this->html('folders');
			if (is_numeric($move)) $folder = $folders[$move];
			else $folder = $move;
		//	$ret['text'] = lang('%1 has been moved to %2 folder', $user['login'], $folder);
		//	$ret['type'] = 'folder';
			$ret['js'] .= 'S.IM.close(\''.$this->to.'\');S.IM.reloop();';
		}
	}
	
	private function block(&$ret) {
		if (!$this->connect()) return false;
		if (!($user = $this->blockAllow())) return false;
		DB::run('UPDATE '.DB_PREFIX.'im SET old_folder=folder, folder=\''.self::FOLDER_IGNORED.'\' WHERE id='.$this->connect[1]['id']);
		if (DB::affected()) {
			$ret['text'] = lang('%1 has been moved to ignored list', @$user['login']);
			$ret['type'] = 'folder';
			$ret['js'] .= 'S.IM.close(\''.$this->to.'\');S.IM.reloop();';
		}
	}
	
	private function unblock(&$ret) {
		if (!$this->connect()) return false;
		if (!($user = $this->blockAllow())) return false;
		DB::run('UPDATE '.DB_PREFIX.'im SET folder=old_folder WHERE id='.$this->connect[1]['id']);
		if (DB::affected()) {
			$ret['text'] = lang('User %1 was unblocked', @$user['login']);
			$ret['type'] = 'folder';
			$ret['js'] .= 'S.IM.load(\''.$this->to.'\', true);S.IM.reloop();';
		}
	}
	
	private function fill() {
		$_to = $this->to;
		$typing = $this->getTyping();
		$ret = '';
		if ($this->tpl_folder) $this->Index->Smarty->template_dir = FTP_DIR_TPLS;
		$this->Index->Smarty->assign('tpl_folder',$this->tpl_folder);
		if (!isset($_SESSION['IM_wins']) || !is_array($_SESSION['IM_wins'])) $_SESSION['IM_wins'] = array();
		foreach ($_SESSION['IM_wins'] as $to => $settings) {
			if ($this->action=='send' && $to==$this->to) continue;
			$this->to = $to;
			$this->load();
			if (!$this->connect()) continue;
			$data = array();
			$data['data'] = $this->get(true);
			$data['read'] = $this->getRead();
			$data['typing'] = $this->connect[$this->to]['typing'];
			list ($online, $userleft) = $this->isOnline($this->connect[$this->to]['from_id'], $this->connect[$this->to]['from_ip']);
			$this->Index->Smarty->assign('online', $online);
			$this->Index->Smarty->assign('userleft', $userleft);
			if (isset($this->contact[$this->to])) {
				$this->Index->Smarty->assign('data',$this->contact[$this->to]);
				$data['login'] = @$this->contact[$this->to]['user']['login'];
			}
			$this->Index->Smarty->assign('here',$this->connect[$this->to]['win']=='Y');
			if (!@$data['login'] && is_numeric($to)) {
				$user = $this->getUser($to);
				$data['login'] = $user['login'];
			}
			$data['status'] = $this->Index->Smarty->fetch($this->tpl_folder.'im/win_status.tpl');
			
			$ret .= 'S.IM.fill(\''.$to.'\', '.json($data).');';
			DB::run('UPDATE '.DB_PREFIX.'im SET typing='.(int)$typing[$to].', win=\'Y\' WHERE id='.$this->connect[0]['id']);
		}
		$this->to = $_to;
		return $ret;
	}
	
	private function isOnline($id, $ip = false) {
		$online = false;
		$userleft = false;
		if ($id) {
			$set = DB::row('SELECT `on`, `user_left` FROM '.DB_PREFIX.'im_set WHERE userid='.$id);
			if ($set['on']=='N') {
				return array(false, $set['user_left']);
			}
			$online = DB::one('SELECT expiration FROM '.DB_PREFIX.'sessions WHERE userid='.$id);
			$userleft = false;
			if (!$online && $id) {
				$userleft = DB::one('SELECT last_click FROM '.DB_PREFIX.'users WHERE id='.$id);
			}
		} 
		elseif ($ip) {
			$ip = e((is_numeric($ip)?long2ip($ip):$ip));
			$online = DB::one('SELECT expiration FROM '.DB_PREFIX.'sessions WHERE host='.$ip.' AND userid=0');
			if (!$online) $userleft = DB::one('SELECT cameon+duration FROM '.DB::getPrefix().'visitor_stats WHERE ip='.$ip); 
		}
		return array($online, $userleft);
	}
	
	private function send($message, &$conv, &$id) {
		if (!$this->connect()) return false;
		$conv = Parser::parse('code_bb', $message);
		if (!strlen(trim($conv))) {
			$conv = false;
			return false;
		}
		$data = array(
			'setid'		=> $this->connect[0]['id'],
			'sent'		=> $this->time,
			'msg'		=> $conv,
			'read'		=> 'N'
		);
		DB::insert('im_sub', $data);
		$id = DB::id();
		DB::run('UPDATE '.DB_PREFIX.'im SET total=total+1, total_new=total_new+1, sent='.$this->time.' WHERE id='.$this->connect[0]['id']);
		DB::run('UPDATE '.DB_PREFIX.'im SET total=total+1, sent='.$this->time.' WHERE id='.$this->connect[$this->to]['id']);
		DB::commit();
	}
	
	private function close() {
		if (!$this->connect()) return false;
		DB::run('UPDATE '.DB_PREFIX.'im SET win=\'N\', typing=0 WHERE id='.$this->connect[0]['id']);
		
	}
	
	private function win() {
		if (!$this->connect()) {
			$this->error('Cannot connect with this user');
			return false;
		}
		$data = $this->connect[$this->to];
		$this->fixBoard($data);
		$data['to'] = $this->to;
		DB::run('UPDATE '.DB_PREFIX.'im SET win=\'Y\' WHERE id='.$this->connect[0]['id']);
		return $data;
	}
	
	private function fixMsg($m) {
		if (strlen(FTP_EXT)>1) {
			$m = preg_replace('/(\ssrc="|\.src=\')\/(?!('.str_replace('/','\/',FTP_EXT).'))/Ui','\\1/'.FTP_EXT,$m);
		}
		return $m;	
	}
	
	private function get($unreadLastOnly = false) {
		if (!$this->connect()) exit;
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, setid, sent, msg, `read` FROM '.DB_PREFIX.'im_sub WHERE '.($unreadLastOnly?' `read`=\'N\' AND setid='.$this->connect[$this->to]['id'].'':'setid IN ('.$this->connect[0]['id'].', '.$this->connect[$this->to]['id'].')').' ORDER BY sent DESC';
		$data = $read = array();
		$qry = DB::qry($sql, $this->offset, $this->limit);
		$this->total = DB::rows();
		$id = 0;
		while ($rs = DB::fetch($qry)) {
			$rs['msg'] = $this->fixMsg($rs['msg']);
			$rs['read'] = $rs['read']=='Y';
			$rs['time'] = Date::dayCountDown($rs['sent']);
			$rs['my'] = $this->connect[0]['id']==$rs['setid'];
			if (!$rs['my'] && !$rs['read']) $read[] = $rs['id'];
			unset($rs['setid']);
			if (!$id) $id = $rs['id'];
			$data[] = $rs;
		}
		DB::free($qry);
		if ($read) {
			if ($this->connect[$this->to]['total_new'] > $read) {
				DB::run('UPDATE '.DB_PREFIX.'im SET total_new=total_new-'.count($read).' WHERE id='.$this->connect[$this->to]['id']);
			} else {
				DB::run('UPDATE '.DB_PREFIX.'im SET total_new=0 WHERE id='.$this->connect[$this->to]['id']);
			}
			DB::run('UPDATE '.DB_PREFIX.'im_sub SET `read`=\'Y\' WHERE id IN ('.join(', ',$read).')');
		}
		return $data;
	}
	
	private function getRead() {
		$ret = array();
		$sql = 'SELECT id, `read` FROM '.DB_PREFIX.'im_sub WHERE setid IN ('.$this->connect[0]['id'].', '.$this->connect[$this->to]['id'].') ORDER BY sent DESC';
		$qry = DB::qry($sql, 0, $this->limit);
		while ($rs = DB::fetch($qry)) {
			if ($rs['read']=='Y') $ret[] = $rs['id'];
		}
		return $ret;
	}
	
	private function board() {		
		$settings = $this->settings();
		$this->online = $settings['online'];
		$where = '';
		if ($this->online) {
			$select = ', 1 AS online';
			// 
			$where .= ' AND (CASE WHEN i.from_id THEN (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=i.from_id'.$this->onlineperiod.' LIMIT 1) WHEN i.from_ip THEN (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE userid=0 AND INET_ATON(s.host)=i.from_ip'.$this->onlineperiod.' LIMIT 1) ELSE 0 END)=1';
			$where .= ' AND (CASE WHEN i.from_id THEN (SELECT b.on FROM im_set b WHERE b.userid=i.from_id) ELSE \'Y\' END)=\'Y\'';
		} else {
			$select = ', (CASE WHEN i.from_id THEN (CASE WHEN (SELECT b.on FROM im_set b WHERE b.userid=i.from_id)=\'Y\' THEN (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=i.from_id'.$this->onlineperiod.' LIMIT 1) ELSE 0 END) ELSE (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=0 AND INET_ATON(s.host)=i.from_ip'.$this->onlineperiod.' LIMIT 1) END) AS online';
		}
		switch ($this->folder) {
			case self::FOLDER_COMMON:
			case self::FOLDER_FAVORITES:
				$where .= ' AND (folder='.$this->folder.' OR total_new>0) AND folder!='.self::FOLDER_IGNORED.'';
			break;
			case self::FOLDER_IGNORED:
				$where .= ' AND folder='.self::FOLDER_IGNORED;
			break;
			case self::FOLDER_ALL:
				$where .= '';
			break;
			default:
				$where .= ' AND (folder LIKE '.e($this->folder).' OR total_new>0) AND folder!='.self::FOLDER_IGNORED.'';
			break;
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS i.*'.$select.' FROM '.DB_PREFIX.'im i WHERE '.$this->filter_me.$where.' ORDER BY i.sent DESC';
		$data = array();
		$qry = DB::qry($sql, $this->offset, $this->limit_contacts);
		$this->total = DB::rows();
		$new = 0;
		while ($rs = DB::fetch($qry)) {
			$this->fixBoard($rs);
			$new += $rs['total_new'];
			$data['list'][] = $rs;	
		}
		/*
		$data['pager'] = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit,
		));
		*/
		$data['folder'] = $this->folder;
		$data['total'] = $this->total;
		$data['new_prev'] = $_SESSION['IM_new'];
		$data['new_msg'] = $new;

		$_SESSION['IM_new'] = $new;

		$data['new_msg_text'] = lang('%1 new {number=%1,message,messages}',$data['new_msg']);
		if ($settings['online']) {
			$data['online_text'] = lang('%1 {number=%1,contact,contacts} online',$data['total']);
		} else {
			$data['online_text'] = lang('%1 {number=%1,contact,contacts}',$data['total']);
		}
		/*
		$data['settings'] = $settings;
		*/
		return $data;
	}
	
	private function fixBoard(&$rs) {
		if ($rs['from_id']) {
			$rs['to'] = $rs['from_id'];
			$rs['registered'] = $rs['from_id'];
			$rs['user'] = $this->getUser($rs['from_id']);
			/*
			if (!isset($rs['user']['online'])) {
				$expiration = DB::row('SELECT expiration FROM '.DB_PREFIX.'sessions WHERE userid='.$rs['from_id'],'expiration');
				if ($expiration) $rs['user']['online'] = $expiration;
				elseif ($rs['user']['last_click']) $rs['user']['left'] = $rs['user']['last_click'];
			}
			*/
			if (!isset($rs['user']['online'])) {
				list ($online, $userleft) = $this->isOnline($rs['to']);
				$rs['user']['online'] = $online;
				$rs['user']['user_left'] = $userleft;
			}
			if ($rs['user']['main_photo'] && is_file(FTP_DIR_ROOT.DIR_FILES.'users/'.$rs['from_id'].'/th3/'.$rs['user']['main_photo'])) {
				$rs['user']['pic'] = array(
					'th3'	=> DIR_FILES.'users/'.$rs['from_id'].'/th3/'.$rs['user']['main_photo']
				);
			} else {
				$rs['user']['main_photo'] = '';
				$rs['user']['pic'] = array(
					'th3'	=> 'tpls/img/no-photo.png'
				);	
			}
		} 
		elseif ($rs['from_ip']) {
			$rs['to'] = $rs['from_ip'];
			$rs['registered'] = false;
			$ex = explode(' |;',$rs['anonym']);
			if (isset($ex[1])) {
				$rs['user']['country'] = @$ex[0];
				$rs['user']['name'] = @$ex[1];
				$rs['user']['email'] = @$ex[2];
				$rs['user']['age'] = @$ex[3];
				$rs['user']['sex'] = @$ex[4];
				$rs['user']['reason'] = @$ex[5];
				$rs['user']['login'] = @$ex[1];
			}
			if (!isset($rs['user']['name']) || !$rs['user']['name']) {
				$rs['user']['login'] = long2ip($rs['from_ip']);
			}
			
			$rs['user']['pic'] = array(
				'th3'	=> 'tpls/img/no-photo.png',
				'th2'	=> 'tpls/img/no-photo.png',
				'th1'	=> 'tpls/img/no-photo.png',
				'size'	=> getimagesize(FTP_DIR_ROOT.'tpls/img/no-photo.png')
			);
			if (!isset($rs['user']['online'])) {
				list ($online, $userleft) = $this->isOnline(false, $rs['to']);
				$rs['user']['online'] = $online;
				$rs['user']['user_left'] = $userleft;
			}
			//p($rs);
			/*
			if (!isset($rs['user']['online']) && ($ip = long2ip($rs['from_ip']))) {
				$rs['user']['online'] = false;
				$expiration = DB::row('SELECT expiration FROM '.DB_PREFIX.'sessions WHERE host='.$ip,'expiration');
				if ($expiration) $rs['user']['online'] = $expiration - SESSION_LIFETIME;
				
			}
			*/
		}
		if ($rs['id']) $rs['user']['reason'] = DB::row('SELECT msg FROM '.DB_PREFIX.'im_sub WHERE setid='.$rs['id'].' ORDER BY sent DESC','message');
		if (!isset($rs['user']['name']) || !$rs['user']['name']) $rs['user']['name'] = @$rs['user']['login'];
		if (isset($rs['user']['country'])) {
			$rs['user']['countryname'] = Data::country($rs['user']['country']);
		}
		if (isset($rs['user']['city'])) {
			$rs['user']['cityname'] = Data::city($rs['user']['country'],$rs['user']['state'],$rs['user']['city']);
		}
		$this->contact[$rs['to']] = $rs;
	}
	
	private function getUser($id) {
		if (isset($this->user[$id])) return $this->user[$id];
		$sql = 'SELECT u.id, u.login, u.email, u.main_photo, u.groupid, u.classid, p.country, p.city, p.state, u.last_click, '.DB::sqlGetString('age','p.dob').' AS age FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile p ON p.setid=u.id WHERE id='.$id;
		$this->user[$id] = DB::row($sql);
		return $this->user[$id];
	}

	private function folders($toOptions = -1) {
		$base_folders = $this->html('folders');
		$where = '';
		if ($this->online) {
			$where .= ' AND (CASE WHEN i.from_id THEN (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=i.from_id'.$this->onlineperiod.' LIMIT 1) WHEN i.from_ip THEN (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE INET_ATON(s.host)=i.from_ip'.$this->onlineperiod.' LIMIT 1) ELSE 0 END)=1';
		}
		$qry = DB::qry('SELECT folder, CONCAT(folder, \' (\',COUNT(1),\')\') AS label FROM '.DB_PREFIX.'im i WHERE '.$this->filter_me.$where.' GROUP BY folder ORDER BY folder',0,0);
		$ret = array();
		while ($rs = DB::fetch($qry)) {
			if (array_key_exists($rs['folder'], $base_folders)) {
				$rs['label'] = $base_folders[$rs['folder']].substr($rs['label'],1);
			}
			$ret[$rs['folder']] = $rs['label'];
		}
		if (!array_key_exists(self::FOLDER_COMMON, $ret)) {
			$ret[self::FOLDER_COMMON] = $base_folders[self::FOLDER_COMMON];	
		}
		if (!array_key_exists(self::FOLDER_FAVORITES, $ret)) {
			$ret[self::FOLDER_FAVORITES] = $base_folders[self::FOLDER_FAVORITES];	
		}
		if (!array_key_exists(self::FOLDER_IGNORED, $ret)) {
			$ret[self::FOLDER_IGNORED] = $base_folders[self::FOLDER_IGNORED];	
		}
		$ret[self::FOLDER_ALL] = $base_folders[self::FOLDER_ALL];
		if ($toOptions!==-1) $ret = Html::buildOptions($toOptions, $ret);
		return $ret;
	}

	private function init() {
		$_SESSION['IM_wins'] = array();
		$sql = 'UPDATE '.DB_PREFIX.'im SET typing=0, win=\'N\' WHERE '.($this->UserID?' from_id='.$this->UserID.' OR to_id='.$this->UserID:'from_ip='.$this->IP.' OR to_ip='.$this->IP);
		DB::run($sql);
		$this->Index->addJSA('im.js');
		if (is_file(FTP_DIR_TPL.'css/im.css')) {
			$this->Index->addCSS('im.css');
		} else {
			$this->Index->addCSSA('im.css');
		}
	}
	
	private function unload() {
		$sql = 'UPDATE '.DB_PREFIX.'im SET typing=0, win=\'N\' WHERE '.($this->UserID?' from_id='.$this->UserID.' OR to_id='.$this->UserID:'from_ip='.$this->IP.' OR to_ip='.$this->IP);
		DB::run($sql);
	}
}