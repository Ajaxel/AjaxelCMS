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
* @file       inc/Site.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

// Site modes
define ('MODE_NORMAL', 0);
define ('MODE_FRIENDLY', 1);

// form elements, coming in the future
/*
define ('EL_QUESTION',1);
define ('EL_TEXT',2);
define ('EL_PASSWORD',3);
define ('EL_HIDDEN',4);
define ('EL_TEXTAREA',5);
define ('EL_SELECT',6);
define ('EL_RADIO',7);
define ('EL_CHECKBOX',8);
define ('EL_FILE',9);
define ('EL_BUTTON',10);
define ('EL_CHECKBOX_TEXT',11);

define ('EL_DATETIME',20);
define ('EL_HTML',22);
define ('EL_PHP',23);
define ('EL_SMARTY',24);
define ('EL_CAPTCHA',25);

define ('EL_CUSTOM',30);
define ('EL_BLOCK',31);

define ('EL_PAGEBREAK', 50);
*/

// login floud
define ('LOGIN_FAILURE', 0);
define ('LOGIN_SUCCESS', 1);
define ('LOGIN_DELETED', 2);
define ('LOGIN_FLOUD', 3);

final class CMS {
	const
		VERSION = '8.1',
		NAME = 'Ajaxel CMS',
		URL = 'http://ajaxel.com/',
		EMAIL = 'ajaxel.com@gmail.com'
	;
}

final class Site {
	
	const VERSION = CMS::VERSION;
	
	public static
		$keys = array(),
		$URL_KEYS = false,
		$URL = '',
		$Mode = MODE_NORMAL,
		$vars = array()
	;
	public static
		$upload = NULL,
		$halted = NULL,
		$exit = false,
		$mini = NULL,
		$loop = NULL,
		$cli = NULL,
		$ajax = false,
		$db_sql = NULL,
		$data = array(),
		$cache_enabled = false,
		$cookie_vars = array(),
		$mem = MEMORY,
		$mem_str = '',
		$token_key = '_t'
	;
	
	private static $_instance;

	const
		SESSION_NAME = 'PHPSESSID',
		
		MENU_LEVELS 		= 2,
		
		ACTION_INSERT		= 1,
		ACTION_UPDATE		= 2, 
		ACTION_DELETE		= 3,
		ACTION_ACTIVE		= 4,
		ACTION_ERROR		= 5,
		ACTION_UNKNOWN		= 6,
 
		ACCOUNT_DEACTIVATED = 0,
		ACCOUNT_ACTIVE		= 1,
		ACCOUNT_DELETED		= 2,
		ACCOUNT_PENDING		= 3,
		ACCOUNT_BANNED		= 4,
		ACCOUNT_CONFIRM		= 5,
		ACCOUNT_AWAY		= 6,
		ACCOUNT_BUSY		= 7,
		ACCOUNT_INVISIBLE	= 8,
		ACCOUNT_AWAY2		= 9,

		STATUS_NOT_PAID		= 0,
		STATUS_PAID			= 1,
		STATUS_ACCEPTED		= 2,
		STATUS_CANCELLED	= 3,
		STATUS_SENT			= 4,
		STATUS_REFUNDED		= 5,
		STATUS_CREDIT		= 6,
		STATUS_ERROR		= 7,

		ORDER_TYPE_PRODUCT		= 0,
		ORDER_TYPE_SERVICE		= 1,
		ORDER_TYPE_ACTIVATION	= 2,
		ORDER_TYPE_DOWNLOAD		= 3,
		ORDER_TYPE_LICENSE		= 4,
		ORDER_TYPE_OTHER		= 5,

		TRANSFER_ADD		= 1, // add
		TRANSFER_REMOVE		= 2, // remove
		TRANSFER_SEND		= 3, // sent
		TRANSFER_NULL		= 4, // nulled by admin
		TRANSFER_CONFIRM	= 5, // confirmed by admin
		TRANSFER_REJECT		= 6  // removed (nulled)
	;
	
	
	private function __construct() {
		spl_autoload_register(array($this,'ajaxel_autoloader'));
	}

	public static function &getInstance() {
		if (!self::$_instance) self::$_instance = new self;	
		return self::$_instance;
	}
	
	/**
	* Class call priority:
	* 1 - inc/
	* 2 - tpls/template_name/classes
	* 3 - mod/
	*/
	private function ajaxel_autoloader($class) {
		if (is_file($f = FTP_DIR_ROOT.'inc/'.$class.'.php')) require $f;
		elseif (defined('FTP_DIR_TPL') && is_file($f = FTP_DIR_TPL.'classes/'.$class.'.php')) require $f;
		elseif (is_file($f = FTP_DIR_ROOT.'mod/'.$class.'.php')) require $f;
		self::mem($class.'.php load');
	}
	
	public function init($config = false, $vars = array()) {
		
		session_name(self::SESSION_NAME);
		if(!@session_start()){
			session_regenerate_id(true);
			session_start();
		}
		
		$this->htaccess();
		self::$vars = $vars;
		self::$vars['config'] = ($config ? $config : FTP_DIR_ROOT.'config/config.php');
		require FTP_DIR_ROOT.'inc/Conf.php';
		
		self::server();
		
		$_arg_num = func_num_args();
		self::install($_arg_num);
		
		$_conf = array();
		require self::$vars['config'];
		require FTP_DIR_ROOT.'config/defines.php';
		
		self::$cookie_vars = array(
			URL_KEY_LANG => true,
			URL_KEY_REGION => true,
			URL_KEY_CURRENCY => true,
			URL_KEY_TEMPLATE => true,
		);
		
		if (!isset($_conf['site_types'])) {
			Message::halt('System ERROR','Cannot pass variables from file');
		}
		
		if (NO_WWW && substr($_SERVER['HTTP_HOST'],0,4)=='www.') {
			$url = 'http://'.substr($_SERVER['HTTP_HOST'],4).$_SERVER['REQUEST_URI'];
			self::header('Location: '.$url);
			exit;
		}		
		
		Conf()->merge($_conf);
		
		self::$cli = php_sapi_name() == 'cli' ? true : false;
		self::$mini = ((defined('SITE_MINI') && SITE_MINI) || @$vars['mini'] || self::$cli);
		if (!self::$mini) self::$mini = isset($_GET['mini']) ? true : false;
		
		self::$db_sql = DB_SQL ? DB_SQL : DEBUG;
		
		require FTP_DIR_ROOT.'mod/functions.php';
		
		$mtime = explode(' ',microtime());
		Conf()->s('start_time', $mtime[1] + $mtime[0]);
		if (!self::$mini) {
			set_error_handler('Debug::myErrorHandler');
			set_exception_handler('Debug::myExceptionHandler');
		}

		self::$keys['skipURL'] = Conf()->g('skip_url');
		self::$keys['siteTypes'] = Conf()->g('site_types');

		self::$keys['Months'] = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
		self::$keys['Mo'] = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		
		self::$keys['Reserved'] = array(
			URL_KEY_LOGOUT,
			URL_KEY_LOGIN,
			URL_KEY_REMEMBER,
			URL_KEY_NOTMYPC,
			URL_KEY_REGISTER,
			URL_KEY_LIMIT
		);
		
		Session()->init();
		self::load();
		URL::query();
		self::superActions();
		if (!self::$loop) Session()->stats();
		if (self::$exit) return false;
		self::userActions();
		
		//Data::world_find(Session()->profile);
		if (Index()->init()) {
			Index()->document();
		}
		self::end();
	}


	public function htaccess() {
		if (!is_file(FTP_DIR_ROOT.'.htaccess')) return false;
		$ex = explode('?',$_SERVER['REQUEST_URI']);
		$_ex = explode('.',$ex[0]);
		$ext = end($_ex);
		$arr = array('png','gif','jpg','jpeg','bmp','swf','css','js','woff','otf','ttf','svg');
		if (in_array($ext,$arr)) {

			Message::htaccess($ext,$ex);
			self::$exit = true;
			exit;
		}
	}
	
	
	public static function mem($label) {
		if (!DEBUG) return false;
		$kb = memory_get_usage(true);
		Message::mem($label, $kb);
	}

	/**
	* Actial index, show html here
	*/
	private static function load($return = false) {
		
		define ('AMP','&amp;');
	//	define ('AMP',ini_get('arg_separator.output'));
		define ('HTTP_DIR_TPLS','tpls/');
		define ('FTP_DIR_TPLS', FTP_DIR_ROOT.'tpls/');
		define ('DIR_FILES','files/');
		
		define ('IS_POST', $_SERVER['REQUEST_METHOD']==='POST' ? true : false);
		define ('IS_DEV', $_SERVER['REMOTE_ADDR']=='127.0.0.1');
		
		self::fixGPC();
		
		
		
		if (defined('TEMPLATE_ONLY') && TEMPLATE_ONLY) {
			$tpl = TEMPLATE_ONLY;
			$set = true;
		}
		elseif (isset($_GET[URL_KEY_TEMPLATE]) && !is_array($_GET[URL_KEY_TEMPLATE]) && array_key_exists($_GET[URL_KEY_TEMPLATE], self::getTemplates(true))) {
			$tpl = $_REQUEST[URL_KEY_TEMPLATE];
			$set = true;
		}
		elseif (!self::$mini && $_SERVER['REQUEST_URI'] && HTACCESS_WRITE && ($_tpl = URL::catchHT(URL_KEY_TEMPLATE)) && array_key_exists($_tpl, self::getTemplates(true))) {
			$tpl = $_tpl;
			$set = true;
		}
		elseif (COOKIE_VARS && self::$cookie_vars[URL_KEY_TEMPLATE] && $_COOKIE[URL_KEY_TEMPLATE]) {
			$tpl = $_COOKIE[URL_KEY_TEMPLATE];
			$set = false;
		}
		elseif (Session()->Template) {
			$tpl = Session()->Template;
			$set = false;	
		}
		else {
			$tpl = DEFAULT_TEMPLATE;
			$set = false;
			if ($_COOKIE[URL_KEY_TEMPLATE]) {
				self::setcookie(URL_KEY_TEMPLATE, $tpl, COOKIE_LIFETIME);
			}
		}
		if ($set || !Session()->Template) {
			Session()->Template = $tpl;
			if ($set && COOKIE_VARS && self::$cookie_vars[URL_KEY_TEMPLATE]) {
				self::setcookie(URL_KEY_TEMPLATE, $tpl, COOKIE_LIFETIME);
			}
		}
		if (!is_dir(FTP_DIR_ROOT.'tpls/'.$tpl)) {
			$tpl = DEFAULT_TEMPLATE;
			Session()->Template = $tpl;
		}
		$default_currency = false;
		Conf()->s('hooks',array());
		$qry = DB::qry('SELECT name, val, template FROM '.DB_PREFIX.'settings WHERE template IN ('.e($tpl).', \'global\', \'hook\')',0,0);
		while ($rs = DB::fetch($qry)) {
			if ($rs['template']=='hook') {
				Conf()->s2('hooks', $rs['name'], $rs['val']);
				continue;
			}
			if (strtoupper($rs['name'])==$rs['name']) {
				if (!defined($rs['name'])) define ($rs['name'], (!$rs['val'] ? false : $rs['val']));	
			} else {
				switch ($rs['name']) {
					case 'languages':
						$val = array();
						if (!defined('DEFAULT_LANGUAGE')) {
							$lang = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template='.e($tpl).' AND name=\'DEFAULT_LANGUAGE\'','val');
							if (!$lang) $lang = 'en';
							define ('DEFAULT_LANGUAGE', $lang);
						}
						$arr = strexp($rs['val']);
						if ($arr && is_array($arr)) {
							foreach ($arr as $l => $a) {
								if (!$a[3] && $l!=DEFAULT_LANGUAGE) continue;
								$val[$l] = $a;
							}
						}
					break;
					case 'currencies':
						$_val = strexp($rs['val']);
						if (!is_array($_val)) $_val = array();
						$val = array();
						foreach ($_val as $cur => $a) {
							if ($a[3] || $a[0]==1) $val[$cur] = $a;
							if ($a[0]==1) $default_currency = $cur;
						}
					break;
					default:
						$val = strexp($rs['val']);
					break;	
				}
				Conf()->s($rs['name'], $val);	
			}
		}
		DB::free($qry);
		if (!defined('HTTP_BASE')) {
			$request_uri = dirname($_SERVER['REQUEST_URI']);
			define ('HTTP_BASE', 'http'.(URL::is_ssl()?'s':'').'://'.trim($_SERVER['HTTP_HOST'].'/'.trim($request_uri,'/').'/','/').'/');
		}
		define ('TEMPLATE', $tpl);
		
		self::checkBanned();
		if (!defined('PREFIX')) {
			$prefix = '';
			$tables = DB::tables(false, true);
			$pref = array();
			foreach ($tables as $table) {
				if (substr($table,-8)=='_content') {
					$pref['content'] = substr($table,0,strpos($table,'_content')).'_';
					continue;
				}
				if (substr($table,-5)=='_menu') {
					$pref['menu'] = substr($table,0,strpos($table,'_menu')).'_';
					continue;
				}
			}
			if ($pref['content']==$pref['menu']) {
				$prefix = $pref['content'];
			}
			if ($prefix && !isset($_GET['tab'])) {
				DB::run('INSERT INTO '.DB_PREFIX.'settings (name, val, template) VALUES (\'PREFIX\', '.e($prefix).', '.e($tpl).')');
			}
			define('PREFIX', $prefix);
		}
		
		if (DEFAULT_CURRENCY!=$default_currency) {
			$default_currency = DEFAULT_CURRENCY;
			$currencies = Conf()->g('currencies');
			if ($currencies) {
				$rate = $currencies[$default_currency][0];
				if ($rate) {
					foreach ($currencies as $cur => $a) {
						$currencies[$cur][0] = $currencies[$cur][0] / $rate;
					}
				}
				Conf()->s('currencies',$currencies);
			}
		}
		if (!$default_currency) $default_currency = 'EUR';

		if (!defined('DEFAULT_LANGUAGE')) define('DEFAULT_LANGUAGE', 'en');
		if (!defined('DEFAULT_REGION')) define('DEFAULT_REGION', 'us');
		if (!defined('DEFAULT_CURRENCY')) define('DEFAULT_CURRENCY', $default_currency);
		if (!defined('KEEP_LANG_URI')) define('KEEP_LANG_URI', false);
		if (!defined('KEEP_TPL_URI')) define('KEEP_TPL_URI', false);
		if (!defined('UI_ADMIN')) define('UI_ADMIN', 'selene');
		if (!defined('SESSION_CLEAN_CLICKS')) define('SESSION_CLEAN_CLICKS',3);
		if (!defined('SESSION_LIFETIME')) define ('SESSION_LIFETIME', 3600);
		if (!defined('DB_SQL')) define('DB_SQL',false);
		if (!defined('DB_CACHE')) define('DB_CACHE',true);
		if (!defined('DEBUG')) define('DEBUG',true);
		if (!defined('USE_GOOGLE_TRANSLATE')) define('USE_GOOGLE_TRANSLATE',false);
		if (!defined('UNDER_CONSTRUCTION')) define('UNDER_CONSTRUCTION', false);
		if (!defined('USE_IM')) define('USE_IM', false);
		if (!defined('USE_AUTH_LOG')) define ('USE_AUTH_LOG', false);
		
		DB::setPrefix(DB_PREFIX.PREFIX);
		DB::setCacheMode(DB_CACHE);

		Conf()->s('total_languages',count(Conf()->g('languages')));
		Conf()->s('total_templates',count(Conf()->g('templates')));
		Conf()->s('total_currencies',count(Conf()->g('currencies')));
		
		if (!defined('HTTP_URL')) define ('HTTP_URL', HTTP_BASE);
		define ('HTTP_DIR_TPL', HTTP_DIR_TPLS.TEMPLATE.'/');
		define ('FTP_DIR_TPL', FTP_DIR_TPLS.TEMPLATE.'/');
		define ('DIR_TPL',HTTP_DIR_TPLS.TEMPLATE.'/');
		if (!defined('HTTP_EXT')) define ('HTTP_EXT', '/'.join('/',array_slice(explode('/',HTTP_URL),3)));
		if (!defined('FTP_EXT')) define ('FTP_EXT', HTTP_EXT);
		$_SESSION['HTTP_EXT'] = HTTP_EXT;
		$_SESSION['FTP_EXT'] = FTP_EXT;
		define ('LEVEL', count(explode('/',HTTP_EXT))-2);
		
		
		define ('FTP_DIR_FILES',FTP_DIR_ROOT.DIR_FILES.trim(PREFIX,'_').'/');
		define ('HTTP_DIR_FILES',HTTP_EXT.DIR_FILES.trim(PREFIX,'_').'/');
		define ('FTP_DIR_CACHE',FTP_DIR_ROOT.DIR_FILES.'temp/');
		define ('FTP_DIR_MATH_IMG',FTP_DIR_ROOT.DIR_FILES.'temp/math/');
		define ('HTTP_DIR_MATH_IMG',HTTP_EXT.DIR_FILES.'temp/math/');
		define ('FTP_DIR_FONTS',FTP_DIR_ROOT.'config/fonts/');
		define ('FRIENDLY', (HTACCESS_WRITE && strlen(HTTP_EXT)<2));
		
		define ('DUMP_HTML',FTP_DIR_ROOT.'files/temp/d.html');
		
		if (FRIENDLY) self::$Mode = MODE_FRIENDLY;
		else self::$Mode = MODE_NORMAL;	
		
		if (!Session()->Currency) Session()->Currency = DEFAULT_CURRENCY;
		
		if (!Session()->Lang) {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && array_key_exists($l = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2), self::getLanguages())) {
				Session()->Lang = $l;
				if (COOKIE_VARS) self::setcookie(URL_KEY_LANG, $l, COOKIE_LIFETIME);
				self::$cookie_vars[URL_KEY_LANG] = false;
			} else {
				Session()->Lang = DEFAULT_LANGUAGE;
			}
		}

		if (!Session()->Region) Session()->Region = DEFAULT_REGION;

		

		define ('FILE_BROWSER', false);
	//	define ('FILE_BROWSER', !strpos($_SERVER['HTTP_USER_AGENT'],'Chrome') && is_file(FTP_DIR_ROOT.'tpls/js/plugins/browser/browser.js'));
		$months = array('Jan','Veb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		foreach ($months as $i => $m) {
			self::$data['date_months'][$m] = Conf()->get('ARR_MONTHS',$i);
			self::$data['date_months_med'][$m] = Conf()->get('ARR_MONTHS_MED',$i);
		}
		
		
	}
	

	private static function checkBanned() {
		if (!Session()->isBlocked()) return;
		$c = false;
		if (is_file(FTP_DIR_ROOT.'config/system/ipblocked.php')) {
			ob_start();
			require FTP_DIR_ROOT.'config/system/ipblocked.php';
			$c = trim(ob_get_contents());
			if ($c) {
				ob_end_clean();
				self::$exit = true;
				exit;
			}
		}
		if (!$c) {
			die('<h3>Your IP address has been banned!</h3><div>You cannot visit our website anymore unless you give a big hug to site administrator by email: <a href="mailto:'.MAIL_EMAIL.'">'.MAIL_EMAIL.'</a></div>');
		}
	}

	public static function header($header,$replace = true, $code = 0) {
		if (headers_sent()) ob_end_clean();
		header($header, $replace, $code);
	}
	
	public static function setcookie($name, $value, $days = 0) {
		if (!headers_sent()) {
			if ($days > 0) $expires = time() + (86400 * $days);
			else $expires = 0;
			setcookie($name, $value, $expires, COOKIE_PATH, COOKIE_DOMAIN);
		} else {
			echo '<script type="text/javascript">
$(function(){
	$.cookie(\''.$name.'\', \''.addslashes($value).'\');
});
</script>';
		}
	}
	
	public static function log($action, $title, $table, $id, $data = false, $template = TEMPLATE) {
		if (!USE_LOG) return;
		if ($table=='log') return false;
		$changes = 0;
		if ($data && is_array($data) && isset($data['old']) && isset($data['new'])) {
			foreach ($data['old'] as $k => $v) {
				if ($k=='edited' || $k=='id') continue;
				if (isset($data['new'][$k]) && $data['new'][$k]!=$v) {
					$changes++;
				}
			}
			if (!$changes) return false;
		}
		
		$data = array(
			'action'	=> $action,
			'title'		=> $title,
			'changes'	=> $changes,
			'setid'		=> $id,
			'template'	=> $template,
			'table'		=> $table,
			'userid'	=> Session()->UserID,
			'added'		=> time(),
			'data'		=> ($data ? strjoin($data) : '')
		);
		DB::insert('log',$data);
	}	
	
	public static function mail($to, $name, $vars = array()) {
		return Email::site_mail($to, $name, $vars);
	}
	
	/**
	Site::userActions
	Session::writeStats
	Session::login
	Session::logout
	Site::end
	Site::__destruct
	*/
	public static function hook($_hook,$type = false,&$row = array(),$isHook = false) {
		if (!$isHook) {
			$_hook = Conf()->g2('hooks',$_hook);
		}
		if (!$_hook) return;
		$_ex = explode('[[:DELIMITER:]]',$_hook);
		$id = $affected = $total = 0;
		$data =& $row;
		foreach ($_ex as $i => $hook) {
			if (substr($hook,0,9)=='[[:SQL:]]') {
				$total++;
				$_hook = substr($hook,9);
				$ex = explode('[[:SEPARATOR:]]',$_hook);
				if (count($ex)>1) {
					$sql = @$ex[$type];
				} else {
					$sql = $_hook;
				}
				if ($sql) {
					$sql = str_replace('{$time}', time(), $sql);
					DB::run($sql);
					if (substr(strtolower($sql),0,6)=='insert') $id = DB::id();
					else $affected = DB::affected();
				}
			}
			elseif (substr($hook,0,9)=='[[:PHP:]]') {
				$_hook = substr($hook,9);
				eval($_hook);
				$total++;
			}
		}
		if (!$total) eval($_hook);
		return $id;
	}
	
	/**
	* magic quotes fix
	*/
	private static function fixGPC() {
		if (get_magic_quotes_gpc()==0) return;
		function __magic_quotes_gpc($requests) {
			foreach ($requests as $k => &$v) {
				if (is_array($v)) {
					$requests[stripslashes($k)] = __magic_quotes_gpc($v);
				} else {
					$requests[stripslashes($k)] = stripslashes($v);
				}
			}
			return $requests;
		} 
		$_GET = __magic_quotes_gpc($_GET);
		$_POST = __magic_quotes_gpc($_POST);
		$_COOKIE = __magic_quotes_gpc($_COOKIE);
		$_REQUEST = __magic_quotes_gpc($_REQUEST);
		$_FILES = __magic_quotes_gpc($_FILES);
	}
	
	/**
	* Site type, Language, Currency, Region finder and token check
	*/
	private static function superActions() {
		
		$base_ext = '';
		$site_type = 'index';
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') {
			self::$ajax = true;	
		}
		if (!is_array(self::$URL_KEYS[0]) && in_array(strtolower(self::$URL_KEYS[0]),self::$keys['siteTypes'])) {
			$site_type = strtolower(self::$URL_KEYS[0]);
			array_shift(self::$URL_KEYS);
			if (!self::$URL_KEYS) self::$URL_KEYS = array(0=>URL_KEY_HOME);
		}
		elseif ($site_type!='xml') {
			if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') !== false) $_REQUEST['wap2'] = 1;
			elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') !== false) {
				if (strpos($_SERVER['HTTP_USER_AGENT'], 'DoCoMo/') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'portalmmm/') !== false) $_REQUEST['imode'] = 1;
				else $_REQUEST['wap'] = 1;
			}
			if (isset($_REQUEST['wap']) || isset($_REQUEST['wap2']) || isset($_REQUEST['imode'])) {
				if (isset($_REQUEST['wap']) ? 'wap' : (isset($_REQUEST['wap2']) ? 'wap2' : (isset($_REQUEST['imode']) ? 'imode' : ''))) {
					$site_type = 'wap';
				}
			}
			if ($site_type!='json' && $site_type!='window' && $site_type!='download' && self::$ajax) {
				$site_type = 'ajax';	
			}
			elseif (FRIENDLY && strlen($_SERVER['REQUEST_URI'])>2 && substr($_SERVER['REQUEST_URI'],0,1)=='/') {
				if ($_SERVER['REQUEST_URI']=='/rss.xml' || substr($_SERVER['REQUEST_URI'],0,9)=='/rss.xml?') {
					$site_type = 'rss';
				}
			}
			if (!DEBUG && $site_type=='json' && !self::$ajax) {
				$site_type = 'index';	
			}
		}
		
		define('SITE_TYPE', $site_type);
		
		/*
		if (!DEBUG && (SITE_TYPE=='ajax' || SITE_TYPE=='json' || SITE_TYPE=='download' || SITE_TYPE=='loop' || (ADMIN && SITE_TYPE=='-window' && (Session()->Browser!='IE' || (Session()->Browser=='IE' && Session()->BrowserVersion<9)))) && (!isset($_SERVER['HTTP_REFERER']) || URL::clean(@$_SERVER['HTTP_REFERER'])!=URL::clean(HTTP_BASE))) {
			Message::halt('403 - Illegal Access','Please use the original website, <p><a href="'.HTTP_BASE.'" style="color:#333;font:13px Arial">Click here to redirect..</a></p>',NULL, false);
			self::$exit = true;
			exit;
		}
		*/
		
		if (DB_SQL && SITE_TYPE!='index' && SITE_TYPE!='popup') self::$db_sql = false;
		define ('IM', (self::$URL_KEYS[0]=='im' || SITE_TYPE=='im'));
		define ('ADMIN', self::$URL_KEYS[0]==URL_KEY_ADMIN || (SITE_TYPE=='upload' && substr(get('upload'),0,strlen(URL_KEY_ADMIN)+1)==URL_KEY_ADMIN.'.'));
		
		define('SITE_EXT',$base_ext);
		
		// request build
		//$_REQUEST = array_merge($_GET, $_POST);
		
		// check token

		self::$token_key = ADMIN ? '_ta' : '_t';
		if (0 && IS_POST) {
			if (!isset($_POST['_t']) || $_SESSION[self::$token_key]!==$_POST['_t']) {
				if ($_SESSION['_t']!==$_POST['_t'] && $_SESSION['_ta']!==$_POST['_t'] && $_SESSION['_'.self::$token_key]!==$_POST['_t']) {
					die('Invalid CSRF token');	
				}
			}
			$_SESSION['__t'] = $_SESSION['_t'];
			$_SESSION['__ta'] = $_SESSION['_ta'];
		}
		
		// set token
		if (SITE_TYPE=='index' || SITE_TYPE=='popup' || SITE_TYPE=='print') {
			$_SESSION[self::$token_key] = substr(md5(rand(1000000,9999999).time()),7,8);
		}
		
		// find
		$arrSwitch = array(
			array(URL_KEY_LANG,'getLanguages','Lang', false),
			array(URL_KEY_CURRENCY,'getCurrencies','Currency', false),
			array(URL_KEY_REGION,'getRegions','Region', false)
		);
		foreach ($arrSwitch as $a) {
			if (isset($_REQUEST[$a[0]]) && !is_array($_REQUEST[$a[0]]) && ((!$a[3] && array_key_exists($_REQUEST[$a[0]],self::$a[1]())) || ($a[3] && in_array($_REQUEST[$a[0]],self::$a[1]())))) {
				Session()->$a[2] = $_REQUEST[$a[0]];
				if (COOKIE_VARS && self::$cookie_vars[$a[0]]) self::setcookie($a[0], $_REQUEST[$a[0]], COOKIE_LIFETIME);
			}
			elseif (COOKIE_VARS && self::$cookie_vars[$a[0]] && isset($_COOKIE[$a[0]]) && !is_array($_COOKIE[$a[0]]) && ((!$a[3] && array_key_exists($_COOKIE[$a[0]],self::$a[1]())) || ($a[3] && in_array($_COOKIE[$a[0]],self::$a[1]())))) {
				Session()->$a[2] = $_COOKIE[$a[0]];
			}
		}
		
		if (defined('LANGUAGE_ONLY') && LANGUAGE_ONLY) Session()->Lang = LANGUAGE_ONLY;
		define ('LANG', Session()->Lang);
		define ('CURRENCY', Session()->Currency);
		if (Session()->Device) define ('DEVICE', Session()->Device);
		
		if (is_file($f = FTP_DIR_TPL.'classes/config.php')) {
			$_conf = array();require $f;Conf()->merge($_conf);
		}
		if (is_file($f = FTP_DIR_ROOT.'config/lang/conf_global.php')) {
			$_conf = array();require $f;Conf()->merge($_conf);
		}
		if (is_file($f = FTP_DIR_ROOT.'config/lang/conf_'.LANG.'.php')) {
			$_conf = array();require $f;Conf()->merge($_conf);
		}
		elseif (is_file($f = FTP_DIR_ROOT.'config/lang/conf_en.php')) {
			$_conf = array();require $f;Conf()->merge($_conf);
		}
		
		if (!ADMIN) {
			if (is_file($f = FTP_DIR_ROOT.'config/lang/lang_'.TEMPLATE.'_'.LANG.'.php')) {
				$_lang = array();require $f;Conf()->s('_lang',$_lang);
			}
			elseif (is_file($f = FTP_DIR_ROOT.'config/lang/lang_'.LANG.'.php')) {
				$_lang = array();require $f;Conf()->s('_lang',$_lang);
			}
		}
		
		
		
		self::$loop = ((IM || SITE_TYPE=='loop' || isset($_GET['loop']) || isset($_GET['cron'])) ? true : false);
		if (self::$mini && ADMIN) self::$mini = false;
		if (!self::$mini) self::$mini = (SITE_TYPE=='upload');
		self::$cache_enabled = HTML_CACHE && !ADMIN && in_array(SITE_TYPE,array('index','popup','ajax')) && !get(URL_KEY_DO);
	}
	
	/**
	* Login, Logout
	*/
	private static function userActions() {
		if (self::$loop) return;

		if (isset($_REQUEST[URL_KEY_EMAIL_CONFIRM]) && $_REQUEST[URL_KEY_EMAIL_CONFIRM]) {
			$ex = explode('.',$_REQUEST[URL_KEY_EMAIL_CONFIRM]);
			Factory::call('user')->doEmailConfirm($ex[0], $ex[1]);
		}
		elseif (!isset($_POST[URL_KEY_REGISTER]) && !isset($_POST[URL_KEY_LOSTPASS])) {
			if (isset($_GET[URL_KEY_LOGOUT])) {
				Factory::call('user')->doLogout();
			}
			if (isset($_GET[URL_KEY_DO]) && isset($_GET[URL_KEY_LOGIN]) && isset($_GET[URL_KEY_PASSWORD])) {
				Factory::call('user')->doLogin($_GET[URL_KEY_LOGIN], $_GET[URL_KEY_PASSWORD], get(URL_KEY_REMEMBER), get(URL_KEY_NOTMYPC), false, false, true);	
			}
			elseif (isset($_POST[URL_KEY_LOGIN]) && isset($_POST[URL_KEY_PASSWORD])) {
				Factory::call('user')->doLogin($_POST[URL_KEY_LOGIN], $_POST[URL_KEY_PASSWORD], post(URL_KEY_REMEMBER), post(URL_KEY_NOTMYPC));	
			}
			elseif (!USER_ID && isset($_COOKIE[ucfirst(URL_KEY_LOGIN)]) && isset($_COOKIE[ucfirst(URL_KEY_PASSWORD)])) {
				Factory::call('user')->doLoginCookie($_COOKIE[ucfirst(URL_KEY_LOGIN)], $_COOKIE[ucfirst(URL_KEY_PASSWORD)]);
			}
			Session()->doSocial();
		}
		
		if (defined('URL_KEY_POST_IGNORE') && URL_KEY_POST_IGNORE) {
			$arr = post(URL_KEY_POST_IGNORE);
			if ($arr && is_array($arr)) {
				foreach ($arr as $k => $v) {
					if (is_array($v)) {
						foreach ($v as $_k => $_v) {
							if (post($k,$_k)==$_v) @$_POST[$k][$_k] = '';
						}
					} else {
						if (post($k)==$v) $_POST[$k] = '';
					}
				}
				unset($_POST[URL_KEY_POST_IGNORE]);
			}
			unset($arr);
		}
		
		if (isset($_SESSION['POST']) && $_SESSION['POST'] && is_array($_SESSION['POST'])) $_POST = array_merge($_SESSION['POST'], $_POST);
		
		if (!isset($_POST[URL_KEY_EMAIL_LOGIN]) && !isset($_POST[URL_KEY_LOGIN]) && isset($_COOKIE[ucfirst(URL_KEY_LOGIN)])) {
			if (HTML_CACHE && !ADMIN && !Session()->UserID) {
				$_POST[URL_KEY_LOGIN] = Index()->cacheVar('login',$_COOKIE[ucfirst(URL_KEY_LOGIN)]);
			}
			else {
				$_POST[URL_KEY_LOGIN] = $_COOKIE[ucfirst(URL_KEY_LOGIN)];
			}
		}
		
		if (SITE_TYPE=='upload') {
			if (!$_FILES) {
				self::$exit = true;
				print('{text:\'Cannot upload this file, system error. Try to upload using FTP client or another way\',delay:4000,type:\'stop\'}');
				exit;
			}
			self::checkHash(request(URL_KEY_FOLDER));
			if (!self::$upload || !self::$upload[2]) {
				self::$exit = true;
				print('{text:\'Illegal access. You are not allowed to upload here.\',delay:5000,type:\'stop\'}');
				exit;
			} else {
				Session()->readSID(self::$upload[2]);
			}
		}
		
		define ('IS_USER', Session()->UserID > 0 ? true : false);
		define ('USER_ID', Session()->UserID);
		define ('GROUP_ID', Session()->GroupID);
		define ('CLASS_ID', Session()->ClassID);
		define ('IS_ADMIN', USER_ID && GROUP_ID > 1);
		

		if (USER_ID && !IS_ADMIN && ADMIN) {
			Factory::call('user')->doLogout();
		}
		
		$im_board = !USE_IM;
		define ('IM_BOARD', $im_board);
		
		if (SITE_TYPE=='index' && (IS_ADMIN || DEBUG) && ($_SERVER['REQUEST_URI']=='/d' || substr($_SERVER['REQUEST_URI'],0,3)=='/d/' || substr($_SERVER['QUERY_STRING'],0,2)=='d=')) {
			if (is_file(DUMP_HTML)) {
				readfile(DUMP_HTML);
			} else {
				echo 'File &quot;'.DUMP_HTML.'&quot; does not exists';
			}
			if (count($_GET)>1) p($_GET);
			self::$exit = true;
			exit;
		}
		
		define ('IS_VISUAL', (SITE_TYPE=='index' || SITE_TYPE=='js') && (VISUAL_TAGS && IS_ADMIN && !ADMIN && isset($_SESSION['AdminGlobal']) && $_SESSION['AdminGlobal']['visual']) ? true : false);
		
		if (IS_ADMIN) {
			if (is_file(FTP_DIR_ROOT.'config/lang/lang_admin_'.LANG.'.php')) {
				$_lang = array();
				require FTP_DIR_ROOT.'config/lang/lang_admin_'.LANG.'.php';
				Conf()->s('_lang_a',$_lang);
				unset($_lang);
			}	
		}
		
		if (UNDER_CONSTRUCTION && SITE_TYPE!='js' && !ADMIN && !IS_ADMIN) {
			if (is_file(FTP_DIR_TPL.'common/under_construction.php')) {
				require FTP_DIR_TPL.'common/under_construction.php';
			} else {
				require FTP_DIR_ROOT.'config/system/under_construction.php';
			}
			echo Index()->getVar('js_and_css',true,false);
			exit;
		}	
	}
	
	/**
	* Hash for upload
	*/
	public static function genHash($id, $name, $seconds = UPLOAD_SECONDS) {
		if (!$id) $id = '0';
		return '/'.$id.'/'.$name.'/'.Session()->SID.'/'.Factory::call('hash', array($id, $name, Session()->SID), $seconds)->getHash();	
	}
	public static function checkHash($str, $seconds = UPLOAD_SECONDS) {
		if (!$str) {
			$ex = explode('.',$_GET['upload']);
			$str = $ex[3];	
			if (!$str) return false;
		}
		$ex = explode('/',trim($str,'/'));
		$id = $ex[0];
		$table = $ex[1];
		$sid = $ex[2];
		$hash = $ex[3];
		if (isset($ex[4])) $name = $ex[4];
		else $name = '';
		self::$upload = (Factory::call('hash', array($id, $table, $sid), $seconds)->isValid($hash) ? array($id, $table, $sid, $name) : false);
	}
	
	/**
	* get some arrays of data
	*/
	public static function getGlobalTables() {
		return Conf()->g('global_tables');
	}
	public static function getLanguages() {
		$ret = Conf()->g('languages');
		if (!$ret || !is_array($ret)) {
			$ret = array('en'=>array('English','English','ENG', 1));
			Conf()->s('languages',$ret);
		}
		return $ret;
	}
	public static function getRegions() {
		if (is_file(FTP_DIR_TPL.'classes/regions.php')) {
			return require FTP_DIR_TPL.'classes/regions.php';
		}
		return require FTP_DIR_ROOT.'config/system/regions.php';
	}
	public static function getCurrencies() {
		$ret = Conf()->g('currencies');
		if (!$ret || !is_array($ret)) {
			$ret = array();
			Conf()->s('currencies',$ret);
		}
		return $ret;
	}
	public static function getTemplates($reGet = false, $getAll = false) {
		if (!$reGet && Conf()->g('templates2')) {
			$ret = Conf()->g('templates2');
			if (!$ret || !is_array($ret)) $ret = array(DEFAULT_TEMPLATE => DEFAULT_TEMPLATE, 1);
			return $ret;
		}
		$getAll = false;
		$qry = DB::qry('SELECT * FROM '.DB_PREFIX.'templates'.($getAll?'':' WHERE active=1').' ORDER BY sort, name',0,0);
		$ret = array();
		while ($rs = DB::fetch($qry)) {
			if (!is_dir(FTP_DIR_TPLS.$rs['name'])) continue;
			$ret[$rs['name']] = array($rs['title'] ? $rs['title'] : $rs['descr'], $rs['active']);
		}
		Conf()->s('templates2', $ret);
		
		return $ret;
	}
	public static function getModules($type = 'content', $cache = true) {
		if ($ret = Conf()->g('modules') && isset($ret[$type])) return (array)$ret[$type];
		$qry = DB::qry('SELECT id, `table`, type, title, icon, `options`, active FROM '.DB::prefix('modules').' WHERE active=\'1\' ORDER BY sort, type, `table`',0,0);
		$ret = array();
		while ($rs = DB::fetch($qry)) {
			$options = explode(' |;',$rs['options']);
			$rs['options'] = array();
			foreach ($options as $o) {
				$o = trim($o);
				$pos = strpos($o,':');
				$key = substr($o, 0, $pos);
				$val = substr($o,$pos+1);
				$rs['options'][$key] = $val;
			}
			$ret[$rs['type']][$rs['table']] = $rs;	
		}
		if (!isset($ret[$type]) || !is_array($ret[$type])) $ret[$type] = array();
		$r = array();
		foreach ($ret[$type] as $k => $v) {
			if ($v['active']==1) $ret[$type][$k] = $v;
		}
		Conf()->s('modules',$ret);
		return (array)$ret[$type];
	}
	
	private static function end() {
		if (SITE_TYPE=='index') $_SESSION['Captcha_sessioncode'] = '';
		if (self::$mem_str && (in_array(SITE_TYPE,array('index','print','popup')) || (!ADMIN && SITE_TYPE=='ajax'))) {
			echo Message::mem();
		}
		elseif (SHOW_END && in_array(SITE_TYPE, array('index','ajax','popup','print'))) {
			echo self::info(true);
		}
	}
	
	
	private static function info($display = false) {
		$sql = false;
		if ((IS_ADMIN && self::$db_sql) || (!IS_ADMIN &&  self::$db_sql===2)) {
			$sql = true;
			echo Message::sqls();
		}
		$mtime = explode(' ',microtime());
		$pageload = number_format($mtime[1] + $mtime[0],2,'.','') - Conf()->g('start_time');
		$memory = memory_get_usage(true) - MEMORY;
		if (function_exists('memory_get_peak_usage')) {
			$full_memory = memory_get_peak_usage(true);
		} else {
			$full_memory = memory_get_usage(true);
		}
		$t = '[Ajaxel CMS v'.Site::VERSION.'] Memory: '.File::display_size($memory).'/'.File::display_size($full_memory).', DB: '.sprintf('%01.4fs',Conf()->g('dbEndTime')-Conf()->g('dbBeginTime')).' ['.Conf()->g('dbAccesses').'], Page: '.sprintf('%01.4fs',$pageload);
		
		if (SITE_TYPE=='ajax') {
			if ($pageload>3 && IS_ADMIN) {
				return '<br /><table id="a-end" class="ui-header ui-widget ui-widget-content ui-corner-all ui-state-default"><tr><td>'.$t.'</td></tr></table>';
			}
			return '';
		}
		if (!$sql) {
			return "\r\n".'<!-- '.$t.' -->';
		}
		return '<br /><table id="a-end" class="ui-header ui-widget ui-widget-content ui-corner-all ui-state-default"><tr><td>'.$t.'</td></tr></table>';
	}
	
	public function __destruct() {
		if (self::$exit || (defined('IM') && IM) || !defined('PREFIX')) {
			@session_write_close();
			return;
		}
		Cache::storeSmall();
		if (!class_exists('DB') || !DB::is_connected()) return;
		DB::setPrefix(DB_PREFIX.PREFIX);
		self::hook('Site::__destruct');
		Session()->end();
		DB::close();
	}
	
	private static function install($arg_num) {
		if (is_file(self::$vars['config'])) {
			return true;
		}
		elseif ($arg_num) {
			require FTP_DIR_ROOT.'mod/functions.php';
			set_error_handler('Debug::myErrorHandler',E_ALL);
			Message::halt('Config error','Configuration file which is defined in index.php does not exist: <em>'.self::$vars['config'].'</em>');
		} else {
			require FTP_DIR_ROOT.'mod/functions.php';
			set_error_handler('Debug::myErrorHandler',E_ALL);
			require FTP_DIR_ROOT.'inc/Install.php';
			InstallAjaxel::getInstance()->init();
		}
		self::$exit = true;
		exit;
	}

	/**
	* Fix SERVER vars
	*/
	private static function server() {
		if (defined('DOMAIN')) {
			die('Constant <em>DOMAIN</em> shall not be defined');	
		}
		
		if (isset($_SERVER['SERVER_NAME'])) {
			$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'];
		} elseif (isset($_SERVER['HOSTNAME'])) {
			$_SERVER['HTTP_HOST'] = $_SERVER['HOSTNAME'];
		} elseif (isset($_SERVER['HTTP_HOST'])) {
			$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'];
		} elseif (isset($_SERVER['SERVER_ADDR'])) {
			$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_ADDR'];
		} else {
			$_SERVER['HTTP_HOST'] = 'localhost';
		}
		$_SERVER['HTTP_HOST'] = trim($_SERVER['HTTP_HOST'],'.');
		if (substr(strtolower($_SERVER['HTTP_HOST']),0,4)=='www.') {
			$domain = substr($_SERVER['HTTP_HOST'],4);	
		} else {
			$domain = $_SERVER['HTTP_HOST'];	
		}
		$_SERVER['REQUEST_METHOD'] = strtoupper($_SERVER['REQUEST_METHOD']);
		unset(
			  $GLOBALS['HTTP_ENV_VARS'],
			  $GLOBALS['HTTP_SERVER_VARS'],
			  $GLOBALS['HTTP_GET_VARS'],
			  $GLOBALS['HTTP_SESSION_VARS'],
			  $GLOBALS['HTTP_POST_VARS'],
			  $GLOBALS['HTTP_COOKIE_VARS'],
			  $GLOBALS['HTTP_POST_FILES']
		);
		define ('DOMAIN', $domain);
	}
}



/**
* Factory 
*/ 
abstract class Factory {
	private static $_instances = array();
	private static $_libs = array (		
		'category'		=> array('inc/Category.php','Category'),
		'content'		=> array('mod/Content.php','Content'),
		'pages'			=> array('mod/Pages.php','Pages'),
		'grid'			=> array('mod/Grid.php','Grid'),
		'menu'			=> array('inc/Menu.php','Menu'),
		'im'			=> array('inc/IM.php','IM'),
		'world'			=> array('inc/World.php','World'),
		'mail'			=> array('inc/Mail.php','Mail'),
		'email'			=> array('inc/Email.php','Email'),
		'hash'			=> array('inc/lib/Hash.php','Hash'),
		'datacontent'	=> array('mod/DataContent.php','DataContent'),
		'image'			=> array('inc/Image.php','Image'),
		'ai'			=> array('inc/AI.php','AI'),
	//	'forum'			=> array('inc/Forum.php','Forum'),
		'calendar'		=> array('inc/Calendar.php','Calendar'),
		'poll'			=> array('mod/Poll.php','Poll'),
		'form'			=> array('inc/Form.php','Form'),
		'user'			=> array('mod/User.php','User'),
		'keyword'		=> array('inc/lib/Keyword.php','Keyword'),
		'tree'			=> array('inc/Tree.php','Tree'),
		'basket'		=> array('inc/Basket.php','Basket'),
		'dbfunc'		=> array('inc/DBfunc.php','DBfunc'),
		'spam'			=> array('inc/Spam.php','Spam'),
		'uploadify'		=> array('inc/Uploadify.php','Uploadify'),
		
		'translate'		=> array('inc/lib/Translate.php', 'Translate'),
		'pdf'			=> array('inc/lib/dompdf/dompdf_config.inc.php', 'DOMPDF'),
	//	'rssreader'		=> array('inc/lib/rssreader.php', 'RssReader'),
		'zip_extract'	=> array('inc/lib/PclZip.php', 'PclZip'),
		'zip_compress'	=> array('inc/lib/ZipFile.php', 'ZipFile'),
	//	'html_dom'		=> array('inc/lib/simple_html_dom.php', 'simple_html_dom',NULL,true),
	//	'mailer'		=> array('inc/lib/PHPMailer.php', 'PHPMailer'),
		'pass'			=> array('inc/lib/PasswordHash.php', 'PasswordHash'),
	//	'phpexcel'		=> array('inc/lib/PHPExcel.php', 'PHPExcel', 'inc/lib/'),
		'pChart'		=> array('inc/lib/Image/pChart.php','pChart'),
		'pData'			=> array('inc/lib/Image/pData.php','pData'),
		'pCache'		=> array('inc/lib/Image/pCache.php','pCache'),
		'minifier'		=> array('inc/lib/Minifier.php','Minifier'),
		'mobile'		=> array('inc/lib/Mobile_Detect.php', 'Mobile_Detect'),
	//	'json'			=> array('inc/lib/json.php', 'Services_JSON'),
	//	'protectdir'	=> array('inc/lib/protectdir.php', 'ProtectDir'),
	//	'simplepie'		=> array('inc/lib/simplepie.php', 'SimplePie'),
	//	'snoopy'		=> array('inc/lib/snoopy.php', 'Snoopy'),
	//	'browser'		=> array('inc/lib/Browser.php', 'Browser'),
	//	'http'			=> array('inc/lib/HTTP_Connection.php', 'HTTP_Connection'),
	//	'ftp'			=> array('inc/lib/FtpConnection.php', 'FtpConnection')
	);
	public static function add($name, $path, $class, $include_path = NULL, $static = false) {
		self::$_libs[$name] = array($path, $class, $include_path, $static);
	}
	public static function get($name = NULL) {
		return $name ? self::$_libs[$name] : self::$_libs;
	}
	public function instance($name) {
		return self::$_instances[$name];	
	}
	public static function call($name, $a1 = NULL, $a2 = NULL, $a3 = NULL, $a4 = NULL, $a5 = NULL) {
		$new = false;
		if (substr($name,0,4)=='new:') {
			$name = substr($name,4);
			$new = true;
		}
		if (isset(self::$_instances[$name]) && !$new) {
			if (!is_bool(self::$_instances[$name])/* && is_a(self::$_instances[$name], self::$_libs[$name][1])*/) {
				if ($a1!==NULL) {
					if (method_exists(self::$_instances[$name],'__construct')) self::$_instances[$name]->__construct($a1,$a2,$a3,$a4,$a5);
				}
				return self::$_instances[$name];
			} 
			elseif (is_bool(self::$_instances[$name])) {
				return self::$_libs[$name][1]($a1,$a2,$a3,$a4,$a5);
			} else {
				Message::halt('User error','Cannot find function or class',Debug::backTrace());
				return false;	
			}
		}
		elseif (array_key_exists($name,self::$_libs)) {
			if (isset(self::$_libs[$name][2]) && self::$_libs[$name][2]) set_include_path(FTP_DIR_ROOT.self::$_libs[$name][2]);
			else restore_include_path();
			require_once FTP_DIR_ROOT.self::$_libs[$name][0];
			if (!isset(self::$_libs[$name][3]) || !self::$_libs[$name][3]) {
				if (class_exists(self::$_libs[$name][1])) {
					self::$_instances[$name] = new self::$_libs[$name][1]($a1,$a2,$a3,$a4,$a5);
				} 
				elseif (function_exists(self::$_libs[$name][1])) {
					self::$_instances[$name] = true;
					return self::$_libs[$name][1]($a1,$a2,$a3,$a4,$a5);
				} else {
					Message::halt('User error','Cannot find function or class',Debug::backTrace());
				}
				return self::$_instances[$name];
			} else {
				self::$_instances[$name] = self::$_libs[$name][1];
			}
		}
		elseif (substr($name,0,1)=='#') {
			// mods	
		}
		else {
			Message::halt('Error','The library <em>'.$name.'</em> is not defined',Debug::backTrace());
		}
	}
}

/**
* Debug is needed
*/
abstract class Debug {
	public static function backTrace($backtrace = false, $p = true) {
		File::load();
		return printDebugBacktrace($backtrace ? $backtrace : debug_backtrace(), $p);
	}
	
	public static function myExceptionHandler($e) {
		$ex = explode(DIRECTORY_SEPARATOR,$e->getFile());
		$smarty = strstr($e->getFile(),DIRECTORY_SEPARATOR.'Smarty');
		Message::halt('Exceptional Error: '.end($ex).' ('.$e->getLine().')',htmlspecialchars($e->getMessage()).(!$smarty?' <br /><br />in '.$e->getFile().' on line '.$e->getLine():''),(!$smarty?nl2br(substr($e->getTraceAsString(),3)):''));
	}
	/*
	public static function assertErrorHandler($file, $line, $code) {
		Site::$exit = true;
		return $file.'<br>'.$line.'<br>'.$code;
		$msg = $file.' on line '.$line.'<br />'.Parser::parse('code','[php]'.$code.'[/php]');
		Message::Halt('Assertion Failed.',$msg,self::backTrace());
	}
	*/
	
	public static function myErrorHandler($errno, $errstr, $errfile, $errline) {
		if (error_reporting()==0) return;
		if (!defined('E_DEPRECATED')) define('E_DEPRECATED',666);
		switch ($errno) {
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
			case E_DEPRECATED:
				if (error_reporting()==E_ALL) {
					echo '<br /><b>'.$errstr.'</b> in '.$errfile.' on line '.$errline.'<br />';
				}
				return;
			break;
			default:
				return Message::myErrorHandler(debug_backtrace(),$errno, $errstr, $errfile, $errline, $six);
			break;
		}
	}
}


/**
* URL catcher should be together with Site class
*/

abstract class URL {
	
	const FRIENDLY_SLASH = '|';
	
	public static function is_ssl() {
		if (isset($_SERVER['HTTPS'])) {
			if ('on'== strtolower($_SERVER['HTTPS'])) return true;
			if ('1'==$_SERVER['HTTPS']) return true;
		} 
		elseif (isset($_SERVER['SERVER_PORT']) && ('443'==$_SERVER['SERVER_PORT'])) {
			return true;
		}
		return false;
	}
	
	public static function rq($val, $url = false, $chkQ  = 0) {
		if ($url===false) $url = self::get();
		if (!$val) return $url;
		if ($chkQ && !$_SERVER['QUERY_STRING']) return '';
		if (!is_array($val) && strstr($val,',')) $val = explode(',',$val);
		if (is_array($val)) {
			foreach ($val as $v) {
				$url = self::rq($v, $url, $chkQ);
			}
			return $url;
		}
		$first = substr($url,0,1);
		$url = preg_replace('/(^|\?|&amp;|&|\/)'.preg_quote($val,'/').'(=(.*))?(&|\/|$)/U', '\\4', $url);
		if ($first=='?') $url = '?'.ltrim($url,'?');
		return $url;
	}
	
	/**
	* Getting url after ? ignoring merged reserved names
	*/
	public static function get() {
		
		$arrSkip = func_get_args();
		$num = func_num_args();
		if (!$num && Site::$URL) return Site::$URL;
		$arrSkip = array_merge((array)$arrSkip, (array)Site::$keys['skipURL']);
		$arrSkipFirst = (array)Site::$keys['siteTypes'];
		$new_url = array();
		$i = 0;
		foreach ($_GET as $k => $v) {
			if (!$k || in_array($k,$arrSkip)) continue;
			if (!$i && in_array($k, $arrSkipFirst)) continue;
			if (is_array($v)) {
				foreach ($v as $_k => $_v) {
					if (is_array($_v)) {
						foreach ($_v as $__k => $__v) {
							$new_url[$k][$_k][$__k] = $__v;
						}
					} else {
						$new_url[$k][$_k] = $_v;
					}
				}
			} else {
				$new_url[$k] = $v;
			}
			$i++;
		}
		$ret = self::make($new_url);
		if (!$num) Site::$URL = $ret;
		return $ret;

	}
	
	public static function addExt($url) {
		if (!HTTP_EXT || HTTP_EXT=='/') return $url;
		$url = trim($url,'/');
		if (substr($url,0,strlen(HTTP_EXT))==HTTP_EXT) $url = substr($url,strlen(HTTP_EXT));
		return $url;
	}
	
	public static function make($arr) {
		$ret = self::html(self::build_query($arr,'',AMP));
		$ret = trim(str_replace('='.AMP,AMP,$ret),'=');
		return $ret;
	}
	
	public static function getFull() {
		$ret = 'http'.(self::is_ssl()?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return $ret;
	}

	public static function html($url) {
		return str_replace(array('"','<'),array('&quot;','&lt;'),$url);
	}
	
	public static function ht($url, $force = false, $exted = true) {
		if (!$url || substr($url,0,7)=='http://' || substr($url,0,8)=='https://') return $url;
		if (substr($url,0,11)=='javascript:') return $url;
		$url = trim($url,'/');
		if ($exted) {
			$ex = explode('&',ltrim($url,'?'));
			$l = array_key_exists($ex[0],Site::getLanguages());
			$ext = self::ext(false, $l);
			$url = ($ext ? $ext.AMP.ltrim($url,'/?') : $url);
		}
		if (substr($url,0,1)=='?') {
			$url = str_replace('/','%2F',$url);	
		}
		if (!$force && (Site::$Mode!=MODE_FRIENDLY || !FRIENDLY)) return $url ? $url : '?';
		$url = trim(str_replace(array('?','&amp;','&','[[:AMP:]]'),array('','/','/','/'),$url),'/');
		$ex = $new_url = array();
		$url_ex = array_reverse(explode('/',$url));
		foreach ($url_ex as $part) {
			if (!$part || $part=='?') continue;
			if ($pos = strpos($part,'=')) {
				$key = substr($part,0,$pos);
				if (in_array($key,$ex)) continue;
				$new_url[] = substr_replace($part,URL_VALUE,$pos,1);
				$ex[] = $key;
			} else {
				$key = $part;
				if (in_array($key,$ex)) continue;
				$ex[] = $key;
				$new_url[] = $part;
			}
		}
		$new_url = ($new_url?'/'.join('/',array_reverse($new_url)):'');
		$new_url = str_replace('%2F',self::FRIENDLY_SLASH,$new_url);
		return $new_url ? $new_url : '/';
	}
	
	public static function ext($fh = false, $skip_lang = false) {
		if (!$skip_lang && Conf()->g('URLext'.$fh)) return Conf()->g('URLext'.$fh);
		$b = false;
		$s = $fh ? '/' : AMP;
		$i = $fh ? URL_VALUE : '=';
		if (HTTP_EXT!='/') {
			$r = HTTP_EXT.($fh?'/':'?');
		} else {
			$r = '';
		}
		if (!$skip_lang && KEEP_LANG_URI && LANG!=DEFAULT_LANGUAGE && Conf()->g('total_languages')>1) {
			$r .= LANG.$s;
		}
		if (!$skip_lang) Conf()->s('URLext'.$fh, $r);
		return $r;
	}	

	public static function clean($url) {
		$dom = $url;
		if (substr($dom, 0, 7) == 'http://') $dom = substr($dom, 7);
		else if (substr($dom, 0, 8) == 'https://') $dom = substr($dom, 8);
		if (strtolower(substr($dom, 0, 4))=='www.') $dom = substr($dom, 4);
		$pos = strpos($dom, '/');
		if($pos !== false) $dom = substr($dom, 0, $pos);
		return $dom;
	}
	
	
	
	
	// All private below
	private static function fixGet($v, $key = false) {
		
		if (!$v || is_array($v)) return $v;
		if ($key) {
			$v = htmlspecialchars(rawurldecode($v));
			if (!is_utf8($v)) $v = cp1251_to_utf8($v);
		//	$v = str_replace('&amp;','&',$v);
		//	$v = preg_replace("/\_\_(.+?)\_\_/" ,'',$v);
		//	$v = preg_replace("/^([\w\.\-\_]+)$/","$1",$v);
		} else {
			if (!is_utf8($v)) $v = cp1251_to_utf8($v);
			$v = urldecode($v);
			$v = preg_replace('/\\\0/','&#92;&#48;',$v);
			$v = preg_replace('/\\x00/','&#92;x&#48;&#48;',$v);
			$v = str_replace('%00','%&#48;&#48;',$v);
			$v = str_replace(self::FRIENDLY_SLASH,'/',$v);
		}
	//	$v = ltrim($v,'?& ');
		return $v;
	}
	public static function catchHT($key) {
		$ret = false;
		if (strstr($_SERVER['REQUEST_URI'],'/'.$key.URL_VALUE)) {
			$l = strlen($key);
			$ret = substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'], '/'.$key.URL_VALUE) + $l + 2);
			if ($pos = strpos($ret,'/')) {
				$ret = substr($ret,0,$pos);	
			}
			elseif ($pos = strpos($ret,'?')) {
				$ret = substr($ret,0,$pos);	
			}
		}
		return $ret;
	}
	
	/**
	* Catch keys /en/default/January/a-articles, where 'en' is language
	*/
	private static function catchGet($key, &$URL_INT, &$go) {
		$len = strlen($key);
		
		if ($len==2 && array_key_exists($key,Site::getLanguages())) {
			
			unset($_GET[$key]);
			$_GET[URL_KEY_LANG] = $key;
			$_REQUEST[URL_KEY_LANG] = $key;
			$go = true;
		}
		/*
		* What if somebody consider for project
		*/
		/*
		elseif (in_array($key,Site::$keys['Months']) || in_array($key,Site::$keys['Mo'])) {
			unset($_GET[$key]);
			$_GET['month'] = $key;
			$_REQUEST['month'] = $key;
			$go = true;
		}
		*/
		else {
			$go = true;
			return true;
		}
	}
	
	
	public static function build_query($data, $prefix='', $sep='', $key='') { 
		$ret = array(); 
		foreach ((array)$data as $k => $v) { 
			if (is_int($k) && $prefix != NULL) { 
				$k = urlencode($prefix.$k); 
			} 
			if (!empty($key) || $key===0)  $k = $key.'['.($k).']'; 
			if (is_array($v) || is_object($v)) { 
				array_push($ret, self::build_query($v, '', $sep, $k)); 
			} else { 
				array_push($ret, $k.'='.($v)); 
			} 
		}
		if (empty($sep)) $sep = AMP; 
		return join($sep, $ret);
	}

	public static function build() {
		$url = '';
		$x = AMP;$z = '=';
		foreach (func_get_args() as $a) {
			if (get($a)) $url .= $x.$a.$z.$_GET[$a];
			elseif (isset($_GET[$a])) $url .= $x.$a;
		}
		return $url;
	}
	
	public static function buildNoEmpty() {
		$url = '';
		$x = AMP;$z = '=';
		foreach (func_get_args() as $a) {
			if (get($a)) $url .= $x.$a.$z.$_GET[$a];
		}
		return $url;
	}
	
	public static function buildByAll() {
		$url = '';
		$x = AMP;$z = '=';
		foreach (func_get_args() as $a) {
			if (post($a)) $url .= $x.$a.$z.$_POST[$a];
			elseif (get($a)) $url .= $x.$a.$z.$_GET[$a];
		}
		return $url;
	}

	public static function redirect($url=false, $time = false) {
		if (!in_array(SITE_TYPE,array('index','ajax','popup','print'))) return;
		Site::$cache_enabled = false;
		$ext = self::ext();
		if (substr($url,0,1)=='?' && !strstr($ext,'/')) $url = '?'.($ext?$ext.'&':'').ltrim($url,'?');
		if (Site::$Mode==MODE_FRIENDLY) {
			if (!$url || $url=='?') $url = HTTP_BASE;
			else {
				$p = parse_url($url);
				if (isset($p['path'])) {
					$url = $url;
				} else $url = self::ht($url);
			}
		}
		elseif (!$url || $url=='?') $url = '/';
		if (!strstr($url,'/')) $url = HTTP_EXT.trim($url,'/');
		$url = str_replace('&amp;','&',$url);
		
		// this will never be understood, but try
		if (SITE_TYPE=='ajax' && !ADMIN && IS_POST && defined('REDIRECT_AJAX_TIME') && REDIRECT_AJAX_TIME && Session()->login_try) {
			$time = REDIRECT_AJAX_TIME;
		}
		
		if ($time || headers_sent()) {
			$w = 'Redirecting you to '.ltrim($url,'/').', please wait..';
			if (function_exists('lang')) $w = lang('_Redirecting you to %1, please wait..',ltrim($url,'/'));
			$html = '<h1 style="padding:10px;font-size:15px">'.$w.'</h1>';
			$title = $w;
			if (is_array($time)) {
				$html = $time['html'];
				$title = $time['title'];
				$time = $time['time'];
			}
			echo '<!DOCTYPE html><html><head><title>'.$title.'</title><meta http-equiv="refresh" content="'.ceil($time/1000).';url='.$url.'">';
			echo '<script type="text/javascript">window.document.title=\''.strjs($title).'\';window.setTimeout(function(){window.location.replace(\''.$url.'\')},'.$time.');</script></head><body>';
			echo $html;
			echo '</body></html>';
			exit;
		}
		else {
			header('Location: '.$url);
			exit;
		}
	}
	
	public static function query() {
		$URL_KEYS = array();
		$amp = '&'; // before constant AMP, that may be &amp; but not really needed
		if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']) $_SERVER['QUERY_STRING_ORIG'] = $_SERVER['QUERY_STRING'];
		$REQUEST_URI = trim($_SERVER['REQUEST_URI'],'?/');
		if (!$_SERVER['QUERY_STRING'] && substr($_SERVER['REQUEST_URI'],0,2)=='/?') {
			$_SERVER['QUERY_STRING'] = trim(substr($_SERVER['REQUEST_URI'],2),'&');
			if (!@$_GET) {
				parse_str($_SERVER['QUERY_STRING'],$_GET);
				if (!IS_POST) {
					parse_str(file_get_contents('php://input'),$_POST);
				}
				$_REQUEST = array_merge($_REQUEST,$_POST,$_GET);
			}
		}

		if ($_SERVER['QUERY_STRING']==404 || $_SERVER['QUERY_STRING']==403) {
			$_SERVER['QUERY_STRING'] = '';
			if ($pos = strpos($REQUEST_URI,'?')) {
				$REQUEST_URI = substr($REQUEST_URI,$pos+1);	
			}
			$REQUEST_URI = str_replace(array('&','='), array('/',URL_VALUE),$REQUEST_URI);
		}
		if (!defined('URL_GET_ALL')) define('URL_GET_ALL',false);
		if (!defined('URL_VALUE') || !URL_VALUE) Message::halt('Configuration error','URL_VALUE must be defined and to contain any delimiter character');
		
		if (((URL_GET_ALL && strpos($REQUEST_URI,'?')) || !$_SERVER['QUERY_STRING'] || is_numeric($_SERVER['QUERY_STRING'])) && strlen($REQUEST_URI)>0) {
			$REQUEST_URI = str_replace('?','/?',$REQUEST_URI);
			$REQUEST_URI = str_replace('//?','/?',$REQUEST_URI);
			$arrUrl = explode('/',$REQUEST_URI);
			
			if ($arrUrl && isset($arrUrl[0])) {
				Site::$Mode = MODE_FRIENDLY;
				$key_next1 = $key_next2 = array();
				foreach ($arrUrl as $level => $key) {
					if ($level < LEVEL) continue;
					if (!($key = self::fixGet($key))) continue;
					if (URL_GET_ALL && $_SERVER['QUERY_STRING'] && substr($key,0,1)=='?') {
						foreach ($arrUrl as $_level => $_key) if ($_level > $level) $key .= $_key.'/';
						$arr = array();
						parse_str(ltrim($key,'?'),$arr);
						foreach ($arr as $k => $v) {
							if (!in_array($k,Site::$keys['skipURL'])) {
								$URL_KEYS[] = $k;
							}
						}
						break;
					}
					if (($posPage = strpos($key,URL_VALUE))) {
						$index = substr($key,0,$posPage);
						$value = substr($key,$posPage+strlen(URL_VALUE));
						$g_value = $value;
						if (strstr($index,'[') && ($cnt = count($m = explode('[',$index))) && $cnt>1) {
							if ($cnt==2) {
								$m[1] = substr($m[1],0,-1);
								if (empty($m[1])) $m[1] = ++$key_next1[$m[0]] - 1;
								$_REQUEST[$m[0]][$m[1]] = $_GET[$m[0]][$m[1]] = $g_value;
								if (!in_array($m[0],Site::$keys['skipURL'])) {
									$URL_KEYS[][$m[0]][$m[1]] = $g_value;
								}
								$_SERVER['QUERY_STRING'] .= $amp.$m[0].'['.$m[1].']='.$g_value;
							}
						} else {
							$index = trim($index,'?&');
							if (!in_array($index,Site::$keys['skipURL'])) {
								$URL_KEYS[] = $index;
							}
							if (!URL_GET_ALL || !isset($_GET[$index])) $_REQUEST[$index] = $_GET[$index] = $g_value;
							$_SERVER['QUERY_STRING'] .= $amp.$index.'='.$value;
						}
					} 
					else {
						$key = trim($key,'?&');
						$go = false;
						if (self::catchGet($key,$URL_INT,$go)) {
							if (!in_array($key,Site::$keys['skipURL'])) $URL_KEYS[] = $key;
							if (!URL_GET_ALL || !isset($_GET[$key])) $_GET[$key] = $_REQUEST[$key] = '';
						}
						if ($go) {
							$_SERVER['QUERY_STRING'] .= $amp.$key;
						}
					}
				}
			}
		}
		elseif ($_GET) {		
			$GET = $_GET;
			$_SERVER['QUERY_STRING'] = '';
			foreach ($GET as $key => $value) {
				unset($_GET[$key]);
				$_key = self::fixGet($key,1);
				if (!is_array($value)) {
					$_value = self::fixGet($value);
					$cought = true;
					$go = false;
					if (!$_value) $cought = self::catchGet($key,$URL_INT,$go);
					if ($cought) {
						if (!in_array($key,Site::$keys['skipURL'])) $URL_KEYS[] = $_key;
					}
					$_SERVER['QUERY_STRING'] .= "$amp$_key=$_value";
					$_GET[$_key] = $_value;
				} else {
					foreach ($value as $ki => $val) {
						$_ki = self::fixGet($ki,1);
						$_val = self::fixGet($val);
						if (!is_array($val)) {
							$_SERVER['QUERY_STRING'] .= "$amp$_key\[$_ki\]=$_val";
							$_GET[$_key][$_ki] = $_val;
						} else {
							foreach ($val as $k => $v) {
								$_k = self::fixGet($k,1);
								$_v = self::fixGet($v);
								if (!is_array($v)) {
									$_SERVER['QUERY_STRING'] .= "$amp$_key\[$_ki\]\[$_k\]=$_v";
									$_GET[$_key][$_ki][$_k] = $_v;
								}
							}
						}
					}
				}
			}
		}
		unset($GET);
		if (substr($_SERVER['QUERY_STRING'],0,strlen($amp))==$amp) $_SERVER['QUERY_STRING'] = substr($_SERVER['QUERY_STRING'],strlen($amp));
		if (!$URL_KEYS) {
			$URL_KEYS = array(0 => URL_KEY_HOME);
			$_GET[URL_KEY_HOME] = '';
		}
		Site::$URL_KEYS = $URL_KEYS;
	}
}