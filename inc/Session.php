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
* @file       inc/Session.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class Session {
	
	public
		$SID = '',
	
		$UserID = 0,
		$SubID = 0,
		$GroupID = 0,
		$ClassID = 0,
		$Login = '',
		$Password = '',
		$Photo = '',
		$Email = '',
		$Facebook = 0,
	
		$profile = array(
			'firstname'	=> '',
			'lastname'	=> ''
		),
		
		$Lang = '',
		$Currency = '',
		$Region = '',
		$Device = '',
		
		$CameON = 0,
		$Location = '',
		$Clicks = 0, // actual clicks
		$Editing = 0,
		$Views = '',
	
	//	$ViewTime = '',
	
		$Active = 1,
		$LastLogged = 0,
		$Logged = 0,
		$Timezone = 0,
		$LastIP = '',
		$IP = '',
		$IPlong = 0,

		$Template = '',
	
		$Country = '',
		$City = '',
		$State = '',
		$OS = '',
		$Browser = '',
		$BrowserVersion = 0,
		$From = '',
	
		$Width = 0,
		$Height = 0,
		$Valid = true,
	
		$VisitID = 0,
		$Visited = 0,
		$Referal = 0,
		$ReferalID = 0,
	
		$referal_try = false,
	
		$IsBot = false,
	
		$D = array(),

		$msg = array(
			'type'		=> '',
			'text'		=> '',
			'delay'		=> 0,
			'ok'		=> true,
			'focus'		=> '',
			'redirect'	=> ''
		),
		$msgs	= array(array()),
	
	
		$login_try = false,
		$login_done = false,
		$login_error = false,
		$login_cookie = false,
		$login_get = false,
		$logout_try = false,
	
		$mail = array(),
		$email_confirm_try = false,
		$email_confirm_done = false,
		
		$first = false
	;
	
	private static $_instance = false;
	

	public function d($key, $val = -1) {
		if ($val!==-1) @$this->D[$key][$val];
		else return @$this->D[$key];
	}

	public function __get($key) {
		return $this->d($key);	
	}
	public function __set($key, $val) {
		$this->d($key, $val);	
	}

	public function setMsg($type, $text, $focus = '', $delay = 1500, $redirect = '') {
		if ($type=='warning' || $type=='error') {
			$this->msgs[$focus] = $text;
		}
		
		$this->msg = array(
		   'type' 		=> $type, 
		   'text'		=> $text,
		   'ok'			=> !$this->login_error,
		   'focus'		=> $focus,
		   'delay'		=> $delay,
		   'redirect'	=> $redirect
		);
	}
	public function getMsg($val = false) {
		return $val ? $this->msg[$val] : $this->msg;
	}
	public function initFormError() {
		$this->msgs = array();	
	}
	public function getFormError() {
		$ret = array();
		ksort($this->msgs);
		if ($this->msgs) foreach ($this->msgs as $i => $arr) if (is_array($arr)) $ret = array_merge($ret,$arr);
		return $ret;
	}
	public function setFormError($msg, $name = '', $int = 1) {
		$this->msgs[$int][$name] = $msg;
	}
	
	/**
	* Runs after Site::__construct
	* connects to database
	*/
	public function init() {
		$this->connect();
		$this->readSID();
	}
	public static function &getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self;	
		}
		return self::$_instance;
	}

	/**
	* Load Domain
	*/
	private function connect() {
		if (!defined('DB_HOST')) {
			
			if (!defined('DB')) {
				if ((($DB = get(URL_KEY_DB)) || 
				(strstr($_SERVER['REQUEST_URI'],'/db'.URL_VALUE) && preg_match('/\/db'.URL_VALUE.'([^\/]+)(\/|$)/',$_SERVER['REQUEST_URI'],$match) && ($DB = $match[1])))
				&& Conf()->exists('DB',$DB)) {
					DB::clearCache();
					$tpl = Conf()->get('DB',$DB,'TEMPLATE');
					$this->Template = ($tpl ? $tpl : DEFAULT_TEMPLATE);
					$_SESSION['DB'] = $DB;
					$_COOKIE[URL_KEY_TEMPLATE] = $this->Template;
					Site::setcookie(URL_KEY_DB,$_SESSION['DB'],30);
					Site::setcookie(URL_KEY_TEMPLATE, $this->Template, COOKIE_LIFETIME);
				}
				elseif (isset($_COOKIE[URL_KEY_DB]) && ($DB = $_COOKIE[URL_KEY_DB])) {
					$_SESSION['DB'] = $DB;
					Site::setcookie(URL_KEY_DB,$_SESSION['DB'],30);
				}
				elseif (!isset($_SESSION['DB']) || !$_SESSION['DB']) {
					$_SESSION['DB'] = Conf()->key('DB');
				}
				define ('DB', $_SESSION['DB']);
			}
			
			define ('DB_HOST', Conf()->g3('DB',DB,'HOST'));
			define ('DB_USERNAME', Conf()->g3('DB',DB,'USERNAME'));
			define ('DB_PASSWORD', Conf()->g3('DB',DB,'PASSWORD'));
			define ('DB_NAME', Conf()->g3('DB',DB,'NAME'));
			define ('DB_PREFIX', Conf()->g3('DB',DB,'PREFIX'));
			define ('DEFAULT_TEMPLATE', Conf()->g3('DB',DB,'TEMPLATE'));
			
			if (Conf()->g3('DB',$DB,'define')) foreach (Conf()->g3('DB',$DB,'define') as $k => $v) if (!defined($k)) define ($k, $v);
		}
		
		DB::connect(0, DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT, DB_SOCKET);
	}


	/**
	* Find session id if cookies are turned off
	*
	private function findSID() {
		if (isset($_POST[Site::SESSION_NAME])) {
			$this->SID = $_POST[Site::SESSION_NAME];
		} elseif (isset($_GET[Site::SESSION_NAME])) { 
			$this->SID = $_GET[Site::SESSION_NAME]; 
		} elseif (($p = strpos($_SERVER['REQUEST_URI'],Site::SESSION_NAME.URL_VALUE)) && !strstr($_SERVER['REQUEST_URI'],'/'.Site::SESSION_NAME.URL_VALUE.'/')) {
			$s = substr($_SERVER['REQUEST_URI'],$p);
			$sid = substr($s,strlen(Site::SESSION_NAME)+1,32);
			if (strlen($sid)==32) $this->SID = $sid;
		}
	}
	*/
	
	/**
	* Always runs, get session id
	*/
	public function readSID($SID = false) {

		if ($SID) {
			@session_id($SID);
			$this->SID = $SID;
		}
		elseif (isset($_COOKIE[Site::SESSION_NAME])) {
			$this->SID = $_COOKIE[Site::SESSION_NAME];
		}
		
		if ($this->IsBot===NULL) {
			$this->IsBot = $this->isBot();
		}
		
		if ($this->SID) {
			if (!$this->IsBot) {
				$this->read();
			} else {
				$this->IP = self::getIP();
				$this->IPlong = intval(ip2long($this->IP));
				$this->GroupID = BOT_GROUP;
				$this->Login = $_SERVER['HTTP_USER_AGENT'];
			}
			if (@$_COOKIE[session_name()]!=$this->SID) {
				Site::setcookie(session_name(), $this->SID);
			}
		} else {
			$this->IP = self::getIP();
			$this->IPlong = intval(ip2long($this->IP));
			if (!$this->IsBot) {
				$this->UserID = 0;
				$this->SubID = 0;
				$this->Clicks = 0;
				$this->SID = self::getSID();
				$_SESSION[session_name()] = $this->SID;
			}
			$this->Visited = time();
			Site::setcookie(session_name(), $this->SID);
			$this->first = true;
		}
		
	}
	
	private function checkLongIP() {
		if (!$this->IPlong) {
			$this->IP = $_SERVER['REMOTE_ADDR'];
			$this->IPlong = intval(ip2long($this->IP));
		}
		
		if (!$this->IPlong) {
			$this->IsBOT = true;
			$this->Valid = false;
			$this->UserID = 0;
			$this->SubID = 0;
		}
	}
	
	private static function getSID() {
		return session_id();
	}
	/**
	* Unique 32x key
	*/
	private static function generateSID() {
		return md5(time().uniqid(rand(),1));
	}
	/**
	* No duplicates, check first
	*/
	private static function checkSID($SID) {
		$qry = DB::qry('SELECT 1 FROM '.DB_PREFIX.'sessions WHERE SID='.e($SID));
		if (DB::num($qry)==1) return self::checkSID(self::generateSID());
		DB::free($qry);
		return $SID;
	}

	
	/**
	* Read from sessions
	*/
	private function read() {
		$row = DB::row('SELECT views, clicks, location, cameon, visit_id, sessvalue, userid FROM '.DB_PREFIX.'sessions WHERE SID='.e($this->SID));
		if ($row && $row['sessvalue']) {
			$s = unserialize($row['sessvalue']);
			if ($s && is_array($s)) foreach ($s as $k => $v) $this->$k = $v;
			$this->Clicks = $row['clicks'];
			$this->Views = $row['views'];
			$this->Location = $row['location'];
			$this->VisitID = $row['visit_id'];
			$this->doRevisit();
			$this->CameON = $row['cameon'];
			$this->UserID = (int)$row['userid'];
			/*
			TODO:
			$this->ViewTime = $row['viewtime'];
			if ($row['viewtime']) {
				$vt = explode('|',$row['viewtime']);
				$mtime = explode(' ',microtime());
				$time = $mtime[1] + $mtime[0] + (float)$vt[1];
				if ($time>0 && $time<1000) {
					DB::run('UPDATE '.DB_PREFIX.PREFIX.'content SET viewtime=viewtime+'.$time.' WHERE id='.(int)$vt[0]);
				}
			}
			$this->ViewTime = 0;
			*/
			$this->readUser();
		} else {
			$this->Login = '';
			$this->Password = '';
			$this->UserID = 0;
			$this->SubID = 0;
			$this->GroupID = 0;
			$this->ClassID = 0;
		}
		$this->Editing = '';
	}


	private function readUser() {
		if ($this->UserID) {
			$row = DB::row('SELECT u.login, u.password, u.groupid, u.classid, u.main_photo, u.email, u.ip, u.last_logged, u.facebook, up.*, '.DB::sqlGetString('age','dob').' AS age FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile up ON up.setid=u.id WHERE u.id='.$this->UserID);
			if ($row) {
				$this->Login = $row['login'];
				$this->Password = $row['password'];
				$this->GroupID = (int)$row['groupid'];
				$this->ClassID = (int)$row['classid'];
				$this->Photo = $row['main_photo'];
				$this->Email = $row['email'];
				if (!$this->Facebook) $this->Facebook = $row['facebook'];
				$this->LastIP = $row['ip'];
				$this->LastLogged = $row['last_logged'];
				if ($row['firstname']) $row['name'] = $row['firstname'].' '.$row['lastname'];
				else $row['name'] = $row['login'];
				$this->profile = $row;
			} else {
				Factory::call('user')->doLogout();	
			}
		}
	}
	
	

	/**
	* Gets user session array
	*/
	public function get($getAll = true) {
		$ret = array();
		foreach (get_class_vars('Session') as $k => $v) {
			$f = substr($k,0,1);
			if ($getAll) {
				if ($k!='profile' && ($f=='_' || $f!=strtoupper($f))) continue;
				//if (!$this->$k) continue;
				$ret[$k] = $this->$k;
			}
			elseif ($f=='_' || $f!=strtoupper($f)) continue;
			elseif (is_scalar($this->$k) && $k!='SID' && $k!='UserID') {
				if ($this->$k && strlen(trim($this->$k))) {
					$ret[$k] = $this->$k;
				}
			}
		}
		return $ret;
	}
	
	public function set($key, $val) {
		$this->$key = $val;
		
	}
	/**
	* Write to sessions
	*/
	public function end() {
		if (!$this->SID || $this->IsBot || Site::$loop) return false;
		$location = $_SERVER['QUERY_STRING'];
		if (SITE_TYPE!='js') {
			$this->searches($this->clicks());
			$this->Clicks++;
		}
		if (!in_array(SITE_TYPE, array('index','ajax')) || $this->Location==$_SERVER['QUERY_STRING']) $location = false;
		$s = array();
		static $_sess_keys = array('SubID','Lang','Currency','Region','Timezone','Template','Country','City','State','Device','OS','Browser','BrowserVersion','Width','Height','Valid','Visited','ReferalID','Referal');
		foreach ($_sess_keys as $k) if ($this->$k) $s[$k] = $this->$k;
		if ($this->CameON==0) $this->CameON = Conf()->g('start_time');
		if (!$this->VisitID) {
			Stats::revisit($this);
		}
		
		
		if (!in_array('template',DB::columns('sessions'))) {
			DB::run('ALTER TABLE '.DB_PREFIX.'sessions ADD template varchar(20) AFTER visit_id');
			DB::resetCache();	
		}
		
		
		$sql = 'REPLACE INTO '.DB_PREFIX.'sessions VALUES ('.e($this->SID).', '.(time() + SESSION_LIFETIME).', '.(int)$this->UserID.', '.(int)$this->GroupID.', '.e(self::getIP()).', '.e(str_replace('&amp;','&',$location?$location:$this->Location)).', '.e($this->Editing).', '.e($this->Views).', '.e($this->ViewTime).', '.$this->Clicks.', '.(float)$this->CameON.', '.(int)$this->VisitID.', \''.$this->Template.'\', '.e(serialize($s)).')';
		DB::change(0);
		DB::noerror(true);
		DB::run($sql);
		if (!SESSION_MULTI && $this->UserID && !Site::$loop && $this->GroupID!=DEMO_GROUP && $this->SID && 
		(!SESSION_CLEAN_CLICKS || $this->Clicks % SESSION_CLEAN_CLICKS==0)) {
			DB::run('DELETE FROM '.DB_PREFIX.'sessions WHERE SID!='.e($this->SID).' AND userid='.$this->UserID);
		}
		$this->clear();
		DB::commit();
		@session_write_close();
	}
	
	/**
	* Clear Sessions
	*/
	private function clear() {
		if (Site::$loop || ($this->SID && (!SESSION_CLEAN_CLICKS || $this->Clicks%SESSION_CLEAN_CLICKS==0))) return false;
		$time = time();
		$sql = 'SELECT SID, location, expiration, cameon, visit_id, clicks, userid FROM '.DB_PREFIX.'sessions WHERE expiration<'.$time.' AND template=\''.$this->Template.'\' AND SID!='.e($this->SID)/*.' AND temp!=\'cron\''*/;
		DB::noerror(true);
		$qry = DB::qry($sql,0,0);
		if (!$qry) {
			if (SITE_TYPE=='index') Message::halt(DB::errorMsg(),$sql,false,false);
			return false;
		}
		$sid = array();
		while ($rs = DB::fetch($qry)) {
			if ($rs['visit_id'] && $rs['cameon']>0) {
				$was = ($rs['expiration'] - SESSION_LIFETIME) - $rs['cameon'] + 2;
				if ($was>0 && $was<86400) {
					DB::run('UPDATE '.DB_PREFIX.PREFIX.'visitor_stats SET duration=duration+'.$was.', clicks='.(floor($was)==1?'1':'clicks+'.(int)$rs['clicks']).' WHERE id='.$rs['visit_id']);
				}
			}
			$this->userLeft($rs['userid'], SESSION_LIFETIME);
			array_push($sid, $rs['SID']);
		}
		DB::free($qry);
		if ($sid) {
			DB::run('DELETE FROM '.DB_PREFIX.'sessions WHERE SID IN ('.join(', ',escape($sid)).')');
			/*
			if (defined('USE_BASKET') && USE_BASKET) {
				DB::noerror();
				DB::run('DELETE FROM '.DB_PREFIX.'orders2_basket WHERE SID IN ('.join(', ',$sid).')');
			}
			*/
		}
	}
	
	
	private function searches($click_id) {
		if (Site::$exit || ADMIN || !defined('USE_SEARCHES') || !USE_SEARCHES || $this->Clicks > USE_SEARCHES) return;
		
		$keys = Conf()->g('searches');
		if (!$keys) return;
		foreach ($keys as $k => $x) {
			if (!isset($_GET[$x[0]]) || !$x[1]) continue;
			$v = $_GET[$x[0]];
			if (is_array($v)) {
				foreach ($v as $_v) {
					if (is_array($_v)) break;
					if (strlen(trim($_v)) < $x[1]) continue;
					Stats::save_search($k,$_v,$click_id,$this->VisitID);	
				}
			} else {
				if (strlen(trim($v)) < $x[1]) continue;
				Stats::save_search($k,$v,$click_id,$this->VisitID);
			}
		}
	}
	
	
	private function clicks() {
		
		if (!defined('USE_CLICKS') || !USE_CLICKS || $this->Clicks > USE_CLICKS) return;
		$c_id = sget('last_click','id');
		$c_time = sget('last_click','time');
		if ($c_id && $c_time) {
			DB::run('UPDATE '.DB_PREFIX.PREFIX.'visitor_clicks SET duration=duration+'.round(Conf()->g('start_time') - $c_time).($this->VisitID?', visit_id='.$this->VisitID:'').' WHERE id='.$c_id);
		}
		if (ADMIN || IS_ADMIN) return;
		$c = $this->Clicks - (JS_STATS?2:1);
		$c = $c < 0 ? 0 : $c;
		$l = str_replace('&amp;','&',rtrim($_SERVER['QUERY_STRING'],'='));
		$md5_l = md5($l);
		$c_id = false;
		if ($this->VisitID) {
			$c_id = DB::one('SELECT id FROM '.DB_PREFIX.PREFIX.'visitor_clicks WHERE visit_id='.$this->VisitID.' AND md5_location='.e($md5_l).'');
		}
		if (!$c_id) {
			$data = array(
				'visit_id'	=> $this->VisitID,
				'location'	=> $l,
				'md5_location' => $md5_l,
				'click'		=> $c,
				'duration'	=> 0,
				'clicked'	=> Date::now(),
				'added'		=> time()
			);
			DB::insert('visitor_clicks',$data);
			$c_id = DB::id();
			$data['id'] = $c_id;
		}
		sset('last_click',array(
			'id'	=> $c_id,
			'time'	=> Conf()->g('start_time')
		));
		return $c_id;
	}
	
	/**
	* Stats here
	* @see self::End
	*/
	public function stats() {
		if ($this->first && JS_STATS) {
			$this->writeStats(true);
		}

		if (get('emailclick')) {
			$ex = explode('!',get('emailclick'));
			$is = DB::row('SELECT `read`, `clicked` FROM '.DB_PREFIX.'emails_read WHERE email='.e($ex[0]).' AND campaign='.e($ex[1]));
			if (!$is) {
				$sql = 'INSERT INTO '.DB_PREFIX.'emails_read (email, campaign, `read`, `clicked`) VALUES ('.e($ex[0]).', '.e($ex[1]).', '.time().', '.time().')';
			} else {
				$sql = 'UPDATE '.DB_PREFIX.'emails_read SET clicked='.time().' WHERE email='.e($ex[0]).' AND campaign='.e($ex[1]);	
			}
			DB::run($sql);
			if ($ex[1] && is_numeric($ex[1]) && DB::affected() && (!$is || !$is['clicked'])) {
				DB::run('UPDATE '.DB_PREFIX.'emails_camp SET `clicked`=`clicked`+1 WHERE id='.(int)$ex[1]);
				DB::run('UPDATE '.DB_PREFIX.'emails SET `clicked`=`clicked`+1 WHERE email='.e($ex[0]));
			}
		}
	
		if (SITE_TYPE!='js') {
			$this->doRevisit();
			$this->doReferal();
			$_SESSION['thirdvisit'] = false;
		}
		
		if (isset($_GET[URL_KEY_REFERAL])) $_SESSION['referal'] = $_GET[URL_KEY_REFERAL];
		elseif ($_SERVER['REQUEST_URI'] && HTACCESS_WRITE && ($r = URL::catchHT(URL_KEY_REFERAL))) $_SESSION['referal'] = $r;
		
		
		if (JS_STATS && (isset($_SESSION['secondvisit']) && $_SESSION['secondvisit'])) {
			if (SITE_TYPE=='js' && url(0)=='stats') {
				$_SESSION['secondvisit'] = false;
				$_SESSION['thirdvisit'] = true;
				$this->Valid = true;
				$_SERVER['HTTP_REFERER'] = @$_SESSION['HTTP_REFERER'];
				$stat_id = $this->writeStats();
				echo '/* Stats were written '.$stat_id."\r\n?".URL::get().' */';
				exit;
			} else {
				$this->Valid = false;
				$_SESSION['HTTP_REFERER'] = @$_SERVER['HTTP_REFERER'];
				Index()->setVar('js_and_css', '<script type="text/javascript">/*<![CDATA[*/document.write(\'<scr\'+\'ipt src="'.HTTP_EXT.'?js&amp;stats'.$query.'&amp;width=\'+parseInt(screen.width)+\'&amp;height=\'+parseInt(screen.height)+\'&amp;timezone=\'+(new Date()).getTimezoneOffset()+\'&amp;t='.time().'&amp;second_attempt=on" type="text/javascript"></scr\'+\'ipt>\');/*]]>*/</script>
', true);
				$_SESSION['secondvisit'] = true;	
			}
		}
		elseif ($this->first) {
			if (SITE_TYPE!='index' && SITE_TYPE!='popup' && SITE_TYPE!='print') return false;
			if (JS_STATS) {
				$_SESSION['HTTP_REFERER'] = @$_SERVER['HTTP_REFERER'];
				// Anything? or better not..
				Index()->setVar('js_and_css', '<script type="text/javascript">/*<![CDATA[*/document.write(\'<scr\'+\'ipt src="'.HTTP_EXT.'?js&amp;stats'.$query.'&amp;width=\'+parseInt(screen.width)+\'&amp;height=\'+parseInt(screen.height)+\'&amp;timezone=\'+(new Date()).getTimezoneOffset()+\'&amp;t='.time().'" type="text/javascript"></scr\'+\'ipt>\');/*]]>*/</script>
', true);
			} else {
				$this->writeStats();
			}
			$_SESSION['secondvisit'] = true;
		}
		elseif (!JS_STATS) {
			$this->Valid = true;
		}
	}
	
	private function doReVisit() {
		if (Site::$exit || $this->VisitID || !$this->Valid || !defined('PREFIX') || $this->isBot) return;
		Stats::revisit($this);
	}
	
	public function doReferal() {
		if (Site::$exit || !$this->VisitID || $this->ReferalID || !$this->Valid || !defined('PREFIX') || $this->isBot) return;
		return Stats::referal($this);
	}

	
	public function writeStats($mini = false) {
		if (Site::$exit) return;
		return Stats::init($this, $mini);
	}
	/**
	* Renew expiration time
	*/
	public function renew() {
		DB::run('UPDATE '.DB_PREFIX.'sessions SET expiration='.(time() + SESSION_LIFETIME).' WHERE SID='.e($this->SID));
	}
	
	
	public function setChange($table, $col, $id, $val, $old_val = NULL, $insert = false, $id_col = 'id') {
		if (!$insert) {
			if ($old_val!==NULL && $old_val!=$val) {
				$insert = true;
			}
			elseif (!DB::one('SELECT 1 FROM '.DB::prefix($table).' WHERE '.$id_col.'='.$id.' AND `'.$col.'`='.e($val))) {
				$insert = true;
			}
		}
		if ($insert) {
			$data = array(
				'table'	=> $table,
				'col'	=> $col,
				'setid'	=> $id,
				'val'	=> $val,
				'added'	=> time()
			);
			DB::insert('changes',$data);
			return DB::id();	
		}
		return false;
	}
	
	
	public function setViews($id, $table) {
		if ($this->IsBot || !$id) return;
		$mtime = explode(' ',microtime());
		$time = $mtime[1] + $mtime[0];
		$ins = false;
		if (!isset($_SESSION['views'])) $_SESSION['views'] = array();
		if (!isset($_SESSION['views'][$table])) $_SESSION['views'][$table] = array();
		if (!isset($_SESSION['views'][$table][$id])) {
			DB::run('UPDATE '.DB_PREFIX.PREFIX.$table.' SET views=views+1 WHERE id='.$id);
			$_SESSION['views'][$table][$id] = true;
		}
	}
	
	/*
	public function setViews($id, $table = 'content') {
		if ($this->IsBot || !$id) return;

		$mtime = explode(' ',microtime());
		$time = $mtime[1] + $mtime[0];
		$ins = false;

		if ($this->Views) {
			$this->ViewTime = $table.$id.'|'.$time;
			if (!strstr($this->Views,','.$table.$id.',')) {
				$this->Views .= ','.$table.$id.',';
				DB::run('UPDATE '.DB_PREFIX.PREFIX.$table.' SET views=views+1 WHERE id='.$id,true);
				$ins = true;
			} elseif ($this->Visited) {
				DB::run('UPDATE '.DB_PREFIX.PREFIX.'views SET viewed='.time().' WHERE setid='.$id.' AND viewed>'.$this->Visited.' AND '.($this->UserID?'userid='.$this->UserID:'ip='.$this->IPlong));
			}
		} else {
			$this->Views .= ','.$id.',';
			$this->ViewTime = $id.'|'.$time;
			DB::run('UPDATE '.DB_PREFIX.PREFIX.$table.' SET views=views+1 WHERE id='.$id,true);
			$ins = true;
		}
		if ($ins) {
			DB::run('INSERT INTO '.DB_PREFIX.PREFIX.'views (setid, viewed, userid, ip) VALUES ('.$id.', '.time().', '.$this->UserID.', '.$this->IPlong.')');	
		}
	}
	*/
	
	
	public function isEdit($name,$id,$lang = '',$justSet = false) {
		$this->Editing = $name.':'.(int)$id.($lang?':'.$lang:'');
		if ($justSet) return false;
		$expiration = time() - SESSION_LIFETIME + EDIT_LIFETIME; // 5minutes
		$sql = 'SELECT userid, expiration FROM '.DB_PREFIX.'sessions WHERE temp='.e($this->Editing).' AND userid!='.$this->UserID.' AND userid>0 AND expiration < '.$expiration;
		return DB::fetch(DB::qry($sql));
	}

	public $fb = false;
	public function doSocial($id = 0, $register = true) {
		if (!$id) $id = $this->Facebook;
		if (ADMIN || !defined('FACEBOOK_APP_ID') || !FACEBOOK_APP_ID) return false;
		if (SITE_TYPE=='index' || SITE_TYPE=='popup') {
			$reload = true;
			Index()->addJScode('try{(function(d){var js,id=\'facebook-jssdk\';if(d.getElementById(id))return;js=d.createElement(\'script\');js.id=id;js.async=true;js.src="//connect.facebook.net/en_US/all.js";d.getElementsByTagName(\'head\')[0].appendChild(js)}(document));window.fbAsyncInit=function(){FB.init({appId:'.FACEBOOK_APP_ID.',status:true,cookie:true,xfbml:true,oauth:true});'.($reload ? 'FB.Event.subscribe(\'auth.login\', function(r){/*$.cookie(\'fb\', 1);if (!$.cookie(\'fb\'))*/window.location.href=window.location.href})':'').'}}catch(e){}');
		}
		if (($this->UserID && $_SESSION['facebook']) || $this->logout_try) return false;
		
		require FTP_DIR_ROOT.'inc/lib/facebook/facebook.php';
		$this->fb = new Facebook(array(
			'appId'  => FACEBOOK_APP_ID,
			'secret' => FACEBOOK_APP_SECRET,
			'cookie' => true
		));
		Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false; 
		Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;
		if (!$id) $id = $this->fb->getUser();
		if ($id) Factory::call('user')->doFacebook($this->fb, $id, $register);
	}
	
	
	public function login($rs, $new_code = '', $addTo = '') {
		if (!$rs['id']) return false;
		if (!$new_code) Site::setcookie(ucfirst(URL_KEY_LOGIN), $rs['login'], COOKIE_LIFETIME);
		// logins=logins+1, 
		DB::run('UPDATE '.DB_PREFIX.'users SET last_logged=logged, logged='.time().($new_code ? ', code='.e($new_code):'').$addTo.' WHERE id='.$rs['id']);
		if ($this->VisitID) DB::run('UPDATE '.DB_PREFIX.PREFIX.'visitor_stats SET userid='.$rs['id'].' WHERE id='.$this->VisitID);
		if (USE_AUTH_LOG) {
			$data = array(
				'userid'	=> $rs['id'],
				'ip'		=> $this->IPlong,
				'logged'	=> time(),
				'success'	=> 1
			);
			DB::insert('logins',$data);
			DB::run('DELETE FROM '.DB_PREFIX.'logins WHERE login IS NULL AND logged<'.(time() - (USE_AUTH_LOG * 86400)));
		}
		$this->UserID = $rs['id'];
		$this->Logged = time();
		$this->readUser();
	}
	
	
	public function userLeft($id, $minus = 0) {
		if (!$id || !$this->UserID || $this->UserID===$id) return false;
		DB::run('UPDATE '.DB_PREFIX.'users SET last_click='.(time() - $minus).' WHERE id='.$id);
		if (USE_AUTH_LOG) {
			DB::noerror();
			$data = array(
				'userid'	=> $id,
				'ip'		=> $this->IPlong,
				'logged'	=> time(),
				'success'	=> 4
			);
			DB::replace('logins',$data);
		}
	}
	
	public function isBlocked() {
		if (!defined('USE_IPBLOCKER') || !USE_IPBLOCKER) return false;
		return DB::one('SELECT 1 FROM '.DB_PREFIX.'ipblocker WHERE ip='.e($this->IP).' OR (ip_from>0 AND ip_from>='.$this->IPlong.' AND ip_to>0 AND ip_to<='.$this->IPlong.')');
	}
	public static function isOnline($userid) {
		if (!$userid) return false;
		return DB::one('SELECT 1 FROM '.DB_PREFIX.'sessions WHERE userid='.(int)$userid);	
	}

	public static function isBot($cached = true) {
		if (isset($_SESSION['BOT'])) return $_SESSION['BOT'];
		$_SESSION['BOT'] = Stats::isRobot() ? true : false;
		return $_SESSION['BOT'];
	}
	public static function getIP() {
		if (isset($_SESSION['IP'])) return $_SESSION['IP'];
		$ret = Stats::getIP();
		$_SESSION['IP'] = $ret;
		return $ret;
	}
}
