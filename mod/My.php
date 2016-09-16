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
* @file       mod/My.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Config {
	protected $conf = array(
		'comment_user_columns' => 'login, main_photo, firstname, lastname',
		'foo' => 'bar'
	);
	
	public static function get($key,$_key = -1) {
		if ($_key!==-1) return Index()->Config->$key[$_key];
		return Index()->Config->$key;
	}
	
	public function __get($key) {
		return $this->conf[$key];	
	}
	
	public function __set($key, $val) {
		$this->conf[$key] = $val;	
	}
}


interface MyInterface {
	public function init();
	public function document();
	public function ob(&$html);
	public function ajax();
	public function upload();
	public function download();
	public function rss();
	public function js();
	public function xml();
	public function pdf();
	public function json();
	public function head();
	public function index();
	public function page();
	public function run();
	
	public function actions();
	
	public function set($key, $val);
	public function display($file);
	public function fetch($file);
	public function cacheEnabled();
	public function end();
}


class My implements MyInterface {
	public
		$Index,
		$params = array(),
		$cache = array(),
		$UserID = 0,
		$ClassID = 0,
		$GroupID = 0,
		$lang = '',
		$prefix = DB_PREFIX,
		$time = 0,
		$id = 0,
	
		$area = '',
		$name = '',
		
		$use_edit = true,
	
		$catref = '',
		$catid = 0,
		$action = '',
		$page = 0,
		$order = 'id',
		$by = 'DESC',
		$offset = 0,
		$limit = 20,
		$total = 0,
		$pager = array(),
		$langs = array(),
	
		$msg = false,
		$msg_title = '',
		$msg_url = '',
		$json_ret = false,
		$msg_type = 'tick',
		$msg_ok = true,
		$msg_redirect = false,
		$msg_reload = false,
		$msg_delay = 2200,
		$msg_focus = '',
		$msg_js = '',
		$msg_get = '',
	
		$data = array()
	;
	
	private $ok_set = false;
	protected $exit = false;
	
	public function __construct() {
		$this->time = time();
	}
	public function init() {
		
	}
	public function document() {
		
	}
	public function ob(&$html) {
			
	}
	public function actions() {
		
	}

	public function setLimit($limit = false, $cached = false) {
		if ($cached) {
			$old_limit = Cache::getSmall('post('.URL_KEY_LIMIT.')');
			if (post(URL_KEY_LIMIT,'',0) > $old_limit) {
				$_POST[URL_KEY_PAGE] = 0;
				$this->page = 0;
				$this->offset = 0;
				$this->limit = post(URL_KEY_LIMIT);
			}
			$l = post(URL_KEY_LIMIT,'','[[:CACHE:]]');
			if (!$l || $l>400 || $l<5) $l = $limit;
			$limit = $l;
		}
		if ($limit===false) {
			$limit = intval(request(URL_KEY_LIMIT));
			if ($limit<=0) return;
		}
		if ($limit) {
			$this->limit = $limit;
			$this->offset = $this->page * $this->limit;
		}
		return $this;	
	}
	
	public function ajax() {
		$this->Index->hide('head');
		$this->Index->hide('header');
		$this->Index->hide('footer');
		$this->Index->printIndex();
	}
	
	public function json($childCalled = false) {
		if (Session()->login_try) {
			$this->toDialog(Session()->getMsg());
		} else {
			if (url(0)=='im') $this->action = 'im';
			$this->action();
		}
		if (!$childCalled) $this->end();	
	}
	
	
	public function window() {
		URL::redirect();
	}
	
	
	public function upload() {
		return Factory::call('uploadify')->upload();
	}
	
	public function uploaded($name, $f, $id) {

	}
	public function deleted($name, $file, $id, $next_file = false) {
		
	}
	public function mained($name, $file, $id) {
		
	}
	public function uploadify() {
		
	}
	
	public function filebrowser() {
		if (!$this->UserID) {
			$_SESSION['RIGHTS']['filebrowser'] = false;
			$_SESSION['RIGHTS']['filebrowser_user_path'] = false;
			return false;
		}
		$i = ceil($this->UserID / 5000);
		$i_path = FTP_DIR_ROOT.'files/user/'.$i.'/';
		if (!is_dir($i_path)) mkdir($i_path,0777);	
		if (!is_dir($i_path.$this->UserID.'/')) mkdir($i_path.$this->UserID.'/',0777);
		$_SESSION['RIGHTS']['filebrowser_user_path'] = $i.'/'.$this->UserID;
		$_SESSION['RIGHTS']['filebrowser'] = true;
		$_SESSION['FTP_EXT'] = FTP_EXT;
		$_SESSION['HTTP_EXT'] = HTTP_EXT;
	}
	
	public function rss() {
		if (url(0) && url(0)!=URL_KEY_HOME) {
			$data = Data::Content(30, explode(',',url(0)), url(1), '', false, 30, 'setid, rid, title, descr, added');
		} else {
			$data = array('list' => Data::ModuleContent(array('article','gallery'),'title, body'),30);
		}
		$this->Index->Smarty->assign('data', $data);
		$this->Index->Smarty->display('rss.tpl');
	}
	
	public function xml() {
		switch (url(0)) {
			case 'google_sitemap':
				$data = Data::ModuleContent(array('article','gallery'),'edited, lang, body, keywords, title, title_en AS content_title',500,'edited DESC',false);
				$this->Index->Smarty->assign('data', $data);
				$this->Index->Smarty->display('google_sitemap.tpl');
				return true;
			break;
		}
		return false;
	}
	
	public function js() {
		
	}
	
	public function pdf() {
		
	}
	
	public function download($file_path = '') {
		if (!$file_path && !get('download')) {
			echo '<script type="text/javascript">alert(\'Download name is empty\');window.location.href=\'/'.URL_EXT.'\'</script>';
			exit;	
		}
		if (!$file_path && get('download')) {
			$file_path = FTP_DIR_TPL.'download/'.str_replace('..','',get('download'));
		}
		/*
		$ex = explode(':',$name);	
		if (count($ex)==3 && is_numeric($ex[1])) {
			if (in_array($ex[2], DB::columns($ex[0]))) {
				$rs = DB::row('SELECT '.$ex[2].' FROM '.$this->prefix.$ex[0].' WHERE id='.(int)$ex[1]);
				$file_path = PREFIX.'files/'.$ex[0].'/'.$ex[1].'/th1/'.$rs[$ex[2]];
			}
		}
		*/
		if ($file_path && is_file($file_path)) {
			File::download($file_path);
			return true;
		} else {
			echo '<script type="text/javascript">alert(\'Error! File\r\n"'.$file_path.'"\r\ndoes not exists!\');window.location.href=\'/'.URL_EXT.'\'</script>';
			exit;
		}
	}
	
	public function index() {
		$this->arrMenu = Factory::call('menu')->getByPosition(array('top','bottom'));	
		$this->set('arrMenu', $this->arrMenu);
		$this->set('url0', url(0,false,true));
		$this->set('url1', url(1,false,true));
		$this->set('url2', url(2));
	}

	public function head($prefix = '') {
		
		if (!$this->Index->show('head')) return;
		$this->Index->addJSA('jquery/'.JQUERY);
		if (defined('JQUERY_MIGRATE') && JQUERY_MIGRATE) $this->Index->addJSA('jquery/'.JQUERY_MIGRATE);
		$this->Index->addJSA('jquery/'.JQUERY_UI);
		$this->Index->addJSA('global.js');
		$this->Index->addJS('scripts'.$prefix.'.js');
		$this->Index->addCSS('styles'.$prefix.'.css');
		$this->Index->addJSA('plugins/jquery.blockUI.js');
		if (defined('JQUERY_COLORBOX') && JQUERY_COLORBOX) {
			$this->Index->addJSA('plugins/jquery.colorbox.js');
			$this->Index->addCSSA('colorbox/'.JQUERY_COLORBOX.'/colorbox.css');
		}
		
		if (USE_IM) Factory::call('im')->html('init');
	}
	
	public function page() {
		switch (url(0)) {
			case 'user':
				if (!$this->Index->call('User')->getContent()) {
					$this->Index->mainarea = false;
				}
			break;
			case 'search':
				$this->Index->setVar('page_title',lang('Search results'));
				$this->Index->setVar('title',lang('Search results'));
				Factory::call('content')->init()->getSearch(get('search'));
			break;
			case 'contact':
				$this->contact();
			break;
			case URL_KEY_HOME:
				$this->Index->displayFile('main.tpl');
			break;
			default:
				if (url(0)) {
					if (!$this->Index->staticPage(url(0),url(1)) && 
						!Factory::call('content')->setLimit(20)->setOrder('dated DESC, sort, id DESC')->setFilter('')->getContent()) {
						$this->Index->mainarea = false;
					}
				}
			break;
		}
	}
	/*
	* Runs after static page class caller
	*/
	public function run() {
			
	}
	
	protected function action() {
		$this->global_action();
	}
	
	protected function rated($data) {
		
	}
	
	protected function global_action() {
		if (!$this->action) $this->action = url(0);
		switch ($this->action) {
			case 'session_lifetime':
				$this->json_ret = array('status' => 'renewed');
			break;
			case 'im':
				$this->json_ret = Factory::call('im', array('to' => get('im')))->load()->action();
			break;
			case 'rate':
				$this->json_ret = Action()->rate(post('table_id'),post('rate'));
				
				if ($this->json_ret['id']) {
					$this->rated($this->json_ret);	
				}
			break;
			case 'login':
				$this->toDialog(Session()->getMsg());
			break;
			case 'register':
				$this->toDialog(Factory::call('user')->doRegister());
			break;
			case 'lostpass':
				$this->toDialog(Factory::call('user')->doLostPass());
			break;
			case 'categories':
				$this->json_ret = $this->categories(post('table'), post('level'), post('after'));
			break;
			case 'geo':
				$this->json_ret = Action()->geo(request('name'),request('area'));
			break;
			case 'poll':
				$this->json_ret = Factory::call('poll')->vote(request('vote'));
			break;	
			case 'comment':
				$this->json_ret = $this->comment();
			break;
			case 'comment_page':
				$this->json_ret = $this->commentPage();
			break;
			case 'uploadify':
				$this->json_ret = Factory::call('uploadify')->json(url(1));
			break;
			default:
				$page = str_replace('.class','',str_replace(array('/','\\','\''),'',url(0)));
				if (!$page) return false;
				if (is_file(FTP_DIR_TPL.'classes/'.ucfirst($page).'.class.php')) {
					require_once FTP_DIR_TPL.'classes/'.ucfirst($page).'.class.php';
					Factory::add('.'.$page,HTTP_DIR_TPL.'classes/'.ucfirst($page).'.class.php',ucfirst($page).'_class');
					$Obj = Factory::call('.'.$page, $this->Index);
					if (method_exists($Obj,'json')) {
						$this->json_ret = $Obj->json();
					}
				}
			break;
		}

		$this->end();
	}
	

	
	protected function categories($table, $level, $after) {
		$params = array(
			'table'		=> $table,
			'lang'		=> $this->lang,
			'catalogue'	=> false,
			'retCount'	=> false,
			'getAfter'	=> $after,
			'noDisable'	=> true,
			'maxLevel'	=> $level,
			'optValue'	=> 'catref',
			'getHidden'	=> false,
			'selected'	=> $after
		);
		$cats = Factory::call('category', $params)->getAll()->toArrayOptions();
		return array(
			'list' => $cats
		);
	}	
	
	public function cacheEnabled() {
		if (get('search') || $this->action) return false;
		return true;	
	}
	public function reservedWords() {
		return array(
			'user'
		);	
	}	

	protected function comment() {
		if (!post('comment','content_id')) return false;
		return Action()->comment();
	}
	protected function commentPage() {
		if (!$_GET['content_id']) return false;
		return Action()->commentPage();
	}

	protected function contact($params = array()) {
		if (isset($_POST['contact'])) Action()->contact($params);
		Factory::call('spam')->getCode();
		if (SITE_TYPE!='json') $this->Index->Smarty->display('contact.tpl');	
	}
	
	/*
	public function deleteFile($name, $folder, $id, $file) {
		switch ($name) {
			case 'users';
			case 'temp':
			case 'editor':
				if (!$folder || substr($folder,0,2)=='th') {
					$dir = FTP_DIR_ROOT.DIR_FILES.$name.'/'.$id.'/';		
				} else {
					$dir = FTP_DIR_ROOT.DIR_FILES.$name.'/'.$id.'/'.$folder.'/';
				}
			break;
			default:
				$dir = FTP_DIR_FILES.$name.'/'.$id.'/';
			break;
		}
		if (!is_dir($dir)) return false;
		$dh = opendir($dir);
		$i = 0;
		while ($f = readdir($dh)) {
			if ($f=='.' || $f=='..') continue;
			if (is_file($dir.$f)) {
				if ($f==$file && unlink($dir.$file)) $i++;
			} else {
				if (is_file($dir.$f.'/'.$file) && unlink($dir.$f.'/'.$file)) {
					$i++;
				}
			}
		}
		closedir($dh);
		return $i;
	}
	
	
	public function cropImage($path, $file, $image_sizes = array()) {
		if (!is_dir($path)) mkdir($path,0777);
		$im = Factory::call('image')->driver('GD')->load($path.$file);
		$im->massResize($image_sizes, $path, $file);
		@unlink($path.$file);
	}
	*/	
	
	public function toTimestamp($str, $e = false) {
		if (!$str) return 0;
		$ex = explode('/',$str);
		if (count($ex)!=3) return 0;
		if (is_array($e)) {
			return mktime(intval($e['Hour']),intval($e['Minute']),intval($e['Second']),(int)$ex[1],(int)$ex[0],(int)$ex[2]);
		}
		elseif ($e) {
			return mktime(23,59,59,(int)$ex[1],(int)$ex[0],(int)$ex[2]);
		} else {
			return mktime(0,0,0,(int)$ex[1],(int)$ex[0],(int)$ex[2]);
		}
	}
	public function fromTimestamp($int) {
		$int = trim($int);
		if (!$int || strlen($int)!=10) return '';
		return date('d/m/Y',$int);
	}

	protected function msg($msg, $type = 'tick', $delay = 1100, $ok = -1, $focus = false) {
		$this->msg = $msg;
		$this->msg_type = $type;
		$this->msg_delay = $delay;
		if ($focus) $this->msg_focus = $focus;
		if ($ok!==-1) {
			$this->msg_ok = $ok;
			$this->ok_set = true;
		}
		elseif (!$this->ok_set) {
			if (in_array($type,array('error','shield','warning','block','delete','stop'))) {
				$this->msg_ok = false;	
			}
		}
	}

	protected function json_ret() {
		if ($this->json_ret!==false) {
			if (!$this->json_ret) $this->json_ret = array(0=>'');
			return $this->json_ret;
		}
		if ($this->msg_type && !is_file(FTP_DIR_ROOT.'tpls/img/icons/'.$this->msg_type.'_48.png')) {
			$this->msg_type = 'stop';	
		}
		$ret = array(
			'text'	=> $this->msg,
			'title'	=> $this->msg_title,
			'type'	=> $this->msg_type,
			'ok'	=> $this->msg_ok,
			'reload'=> $this->msg_reload,
			'redirect'=> $this->msg_redirect,
			'delay'	=> $this->msg_delay,
			'focus'	=> $this->msg_focus,
			'js'	=> $this->msg_js,
			'get'	=> $this->msg_get
		);
		$this->json_ret = false;
		return $ret;
	}
		
	protected function toDialog($ret) {
		if (!$ret) return array();
		if (isset($ret['text'])) {
			$this->msg = $ret['text'];
			$arr = array('type','delay','focus','redirect','reload','get','ok');
			foreach ($arr as $k) {
				$n = 'msg_'.$k;
				if (isset($ret[$k])) $this->$n = $ret[$k];	
			}
		} 
		elseif ($ret) {
			$this->msg = lang('Sorry, you\'ve made few errors').'<div class="a-errorlist">'.join('<br />',$ret).'</div>';
			$this->msg_type = 'error';
			$this->msg_ok = false;
			$this->msg_delay = count($ret)*500 + 1200;
			$this->msg_focus = key($ret);
		}
	}
	public function DB($type, $forID = NULL, $arg2 = NULL, $arg3 = NULL) {
		switch ($type) {
			
		}
	}
	public function data($type, $toOptions = -1, $optSettings = 'dropdown') {
		list ($type, $key, $keyAsVal, $lang) = Html::startArray($type);
		switch ($type) {

		}
		return Html::endArray($ret, $keyAsVal, $lang, $key, $toOptions, $optSettings);		
	}
	public function showTopMessage($bottom = false) {
		$this->params['top_message'] = getMem('top_message');		
		$msg = $this->Index->Session->getMsg();
		if (is($msg, 'text')) {
			if ($msg['redirect']) {
				setMem('top_message',$msg);
				return URL::Redirect($msg['redirect'],SITE_TYPE=='ajax');
			} else {
				$this->params['top_message'] = $msg;
			}
		}
		$this->set('top_message', $this->params['top_message']);	
	}
	
	public function prefix() {

	}
	public function OBparse() {
		return in_array(SITE_TYPE, array('index','popup','print','ajax','window','im'));
	}
	
	public function ok($msg, $type = 'success', $lang = true) {
		$this->set('top_message',array(
			'type'	 => $type,
			'text'	 => ($lang ? lang($msg) : $msg)
		));
		return $this;
	}
	public function init_errors($data) {
		$this->data = $data;
		return $this;
	}
	public function init_error($data) {
		return $this->init_errors($data);
	}
	public function error($error, $name = '', $int = 0, $lang = true) {
		if ($this->no_lang) $lang = false;
		if ($name!==false) $this->Index->Session->setFormError(($lang ? lang($error) : $error), $name, $int);
		return $this;
	}
	public function errors() {
		$ret = $this->Index->Session->getFormError();
		if ($ret) $this->msg_ok = false;
		$this->set('form_errors',$ret);
		if (SITE_TYPE=='json') {
			$this->toDialog($ret);
		}
		return $ret;
	}
	public function err($error, $name, $validate = 'required', $int = 0, $lang = true) {
		if ($name=='captcha') $validate = 'captcha';
		if (!$validate || is_string($validate)) {
			switch ($validate) {
				case 'empty':
					if (!isset($this->data[$name]) || !strlen(trim($this->data[$name]))) $this->error($error,$name,$int,$lang);
				break;
				case 'captcha':
					if (!isset($this->data[$name]) || !strlen(trim($this->data[$name]))) {
						$this->error((is_array($error)?$error[0]:$error),$name,$int,$lang);
					}
					elseif (strtolower(trim($this->data[$name]))!=strtolower(trim($_SESSION['Captcha_sessioncode']))) {
						$this->error((is_array($error)?$error[1]:$error),$name,$int,$lang);
					}
				break;
				case 'email':
					if (isset($this->data[$name]) && $this->data[$name] && !Parser::isEmail($this->data[$name])) $this->error($error,$name,$int,$lang);
				break;
				case 'numeric':
					if (isset($this->data[$name]) && !is_numeric($this->data[$name])) $this->error($error,$name,$int,$lang);
				break;
				default:
					if (!isset($this->data[$name]) || !$this->data[$name]) $this->error($error,$name,$int,$lang);
				break;
			}
		}
		elseif (is_callable($validate)) {
			if ($validate($this->data[$name], $this->data)===false) $this->error($error,$name,$int,$lang);	
		}
		return $this;
	}
	public function ierrors() {
		$this->Index->Session->initFormError();	
		return $this;
	}
	
	public function admin(&$Admin, &$row = false, $settings = false) {
		// $Admin->msg_reload = false;	
	}
	
	public function toDB($from_admin, $updated, $affected, $id, $post) {

	}
	
	public function toForm($from_admin, &$post, $table = '') {
		
	}
	public function userInserted($id, $data) {
		
	}
	public function userUpdated($id, $data) {
		
	}
	public function userPasswordReminded($id, $pass) {
			
	}
		
	public function checkProfile() {
		if (!$this->Index->Session->UserID) return false;
	}
	
	public function set($key, $value) {
		if (!$this->Index || !$this->Index->Smarty) {
			return false;
		//	Message::halt('Script design error','Attempt to call My::set() when My::$Index is not defined',Debug::backtrace());
		}
		if (!$this->Index->Smarty) $this->Index->loadSmarty();
		$this->Index->Smarty->assign($key, $value);
		return $this;
	}
	public function display($file) {
		if (Site::$mini && is_file(FTP_DIR_TPL.str_replace('.tpl','.php',$file))) {
			require FTP_DIR_TPL.str_replace('.tpl','.php',$file);
			return;
		}
		if (!$this->Index->Smarty) $this->Index->loadSmarty();
		$this->Index->Smarty->display($file);	
	}
	public function fetch($file) {
		if (Site::$mini && is_file(FTP_DIR_TPL.str_replace('.tpl','.php',$file))) {
			ob_start();$c = ob_get_contents();ob_end_clean();
			return $c;
		}
		if (!$this->Index->Smarty) $this->Index->loadSmarty();
		$ret = $this->Index->Smarty->fetch($file);
		if (SITE_TYPE=='json') $ret = OB::parse($ret);
		return $ret;
	}
	
	public function setAll() {
			
	}
	
	public function end() {
		if ($this->exit) return false;
		if (SITE_TYPE=='json') {
			$ret = $this->json_ret();
			if ($ret) {
				echo json($ret);
				$this->exit = true;
				exit;
			}
		}
	}
	/*
	public function __call($method, $args) {
		Message::halt('No such method, but the child class calls it','Cannot call <em>'.__CLASS__.'::'.$method.'()</em>', Debug::backtrace());
	}
	*/
}

