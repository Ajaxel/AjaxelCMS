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
* @file       inc/Index.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class Index extends Mainarea {
	
	public
		$Session,
		$Smarty,
		$My,
		$Edit,
		$Tpl,
	
		$engine = 'ajaxel',
		$printsite = true,	
		$vars = array('css' => '', 'js' => '', 'jsc' => ''),
		$nocache_vars = array('js_and_css'=>''),
		$prefix = array(),
		$vars_unique = array('css' => array(), 'js' => array()),
		$content = array(),
		$modules = array(),
		$modules_id = array(),
		$module_name = '',
		$module_id = 0,
		$menu = array(),
		$tree = array(),
		$conf = array(),
		$skip = array(),
		$db_vars = array(),	
		$params = array(),

		$mainarea = true,
		$combine = 1,
		$js_modify = false,
		$js_arr = array(),
		$css_arr = array(),
	
		$html = array(),
		$show = array(
			'all'		=> true,
			'head'		=> true,
			'header'	=> true,
			'footer'	=> true,
			'ajax_header'	=> true,
			'ajax_footer'	=> true,
			'main'		=> true,
			'html'		=> true,		
			'css'		=> true,
			'js'		=> true,
			'jsc'		=> true
		)
	;
	
	private static $_instance = false;
	private $combined = false;
	
	const N = "\n", T = "\t";
	const CDA = '/*<![CDATA[*/', CDZ = '/*]]>*/';
	
	private function __construct() {
		$this->Session = Session::getInstance();	
	}
	
	public static function &getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	public function getParam($param, $default = '') {
		return (isset($this->params[$param]) ? $this->params[$param] : $default);
	}
	
	public function setParam($param, $value = '') {
		$this->params[$param] = $value;
	}
	
	private function sub_class($url0,$url1,$secondTime = false) {
		if ($url0=='action' || $url0=='public' || $url0=='admin' || $url0=='home') return;
		$name = strtolower(str_replace(array('/','\\','\''),'',$url0));
		if ($name=='class') $name = false;
		$n_uc = ucfirst($name);
		if (!ADMIN && $name && is_file(FTP_DIR_TPL.'classes/My'.$n_uc.'.php')) {
			if (!$secondTime) {
				require FTP_DIR_TPL.'classes/MyClass.php';
			}
			$c = 'My'.$n_uc;
			$c_allow = 'My'.$n_uc.'_config';
			require FTP_DIR_TPL.'classes/'.$c.'.php';
			$n_sub = strtolower(str_replace(array('/','\\','\''),'',$url1));
			$Config = new $c_allow($this,$name,$n_sub);
			if (is_file(FTP_DIR_TPL.'classes/'.$c.'.'.$n_sub.'.php')) {
				require FTP_DIR_TPL.'classes/'.$c.'.'.$n_sub.'.php';
				$c_sub = $c.'_'.$n_sub;
				$this->My = new $c_sub($this, $Config);
				/*
				$this->My->name = $n_sub;
				$this->My->area = $name;
				*/
			} else {
				$this->My = new $c($this, $Config);
				/*
				$this->My->name = $name;
				*/
			}
			return true;
		}
		return false;
	}
	
	
	public function init() {
		if (!is_dir(FTP_DIR_TPL)) {
			Message::halt(FTP_DIR_TPL.' is missing or deleted','Please check the <em>'.FTP_DIR_TPL.'</em> path on your server.<br>Or switch do a different template or database the system.<br><br>Try the following:<br>- <a href="/?db=default">http://'.DOMAIN.'/?db=default</a><br>- <a href="/?template=default">http://'.DOMAIN.'/?template=default</a>');
		}
		$this->vars_unique['js'] = array();
		$this->vars_unique['css'] = array();
		
		
		if (SITE_TYPE=='ajax' || SITE_TYPE=='json') {
			$this->combine = false;
			$this->show['js'] = false;
			$this->show['css'] = false;
		}
		if (!MINIFY_JS_CSS) $this->combine = false;
		require FTP_DIR_ROOT.'mod/My.php';
		if (is_file(FTP_DIR_TPL.'classes/MyConfig.php')) {
			require FTP_DIR_TPL.'classes/MyConfig.php';
			$this->Config = new MyConfig;
		} else {
			$this->Config = new Config;
		}
		
		if (!$this->sub_class(url(0),url(1))) {
			if (!ADMIN && is_file(FTP_DIR_TPL.'classes/MyClass.php')) {
				require FTP_DIR_TPL.'classes/MyClass.php';
				$this->My = new MyClass($this);
			}
			else {
				$this->My = new My($this);	
			}
		}
		
		Site::mem('Index::init');
		
		if (Site::$cache_enabled && $this->My->cacheEnabled() && !USER_ID && Cache::getPageHtml()) {
			/*
			$this->_load(false); // trouble, no smarty. loads for setviews
			$this->Session->setViews($this->content['id']);
			*/
			return false;
		} else {
			Site::$cache_enabled = false;	
		}
		
		/*
		if (!Site::$mini && IS_ADMIN) {
			$this->Edit = new Edit($this);
			$this->Edit->load();
		}
		*/

		$this->db_vars = DB::getAll('SELECT name, val_'.$this->Session->Lang.' AS val FROM '.DB_PREFIX.'vars WHERE template='.e($this->Session->Template),'name|val');
		$this->vars['conf']	= '';
		$this->vars['title'] = lang('_#site_title');
		$this->vars['description'] = lang('_#site_description');
		$this->vars['keywords'] = lang('_#site_keywords');
		$this->vars['language']	= LANG;
		$this->vars['charset'] = 'utf-8';
		if (is_file(FTP_DIR_TPL.'favicon.ico')) $this->vars['favicon'] = '/'.HTTP_DIR_TPL.'favicon.ico';	
		else $this->vars['favicon'] = '/favicon.ico';
		
		return true;
	}
	
	
	
	public function document() {
		Site::mem('Index::document');
		if (Session()->mail) {
			Site::mail(Session()->mail['email'], Session()->mail['type'], Session()->mail['data']);
		}
		if (!Site::$mini) {
			$this->load();
			if (ADMIN) {
				// needed for visual
				if (!Site::$mini && !$this->Edit) {
					$this->Edit = new Edit($this);
					$this->Edit->load();
				}
				
			} else {
				require FTP_DIR_ROOT.'mod/Template.php';
				if (is_file(FTP_DIR_TPL.'classes/Tpl.php')) {
					require FTP_DIR_TPL.'classes/Tpl.php';
					$this->Tpl = new Tpl($this);
				} else {
					$this->Tpl = new Template($this);
				}
				if (!Site::$mini && !$this->Edit) {
					$this->Edit = new Edit($this);
					$this->Edit->load();
				}
			}
		}
		if (!$this->printsite) return;
		
		if (ADMIN) {
			require FTP_DIR_ROOT.'inc/Admin.php';
			Site::mem('Admin.php load');
			Admin::start()->index();
		} else {
			$this->start();
			$method = 'print'.ucfirst(strtolower(SITE_TYPE));
			if (method_exists($this,$method)) {
				$this->$method();	
			}
		}
		Site::mem('Index::document end');
	}
	
	
	public function start() {
		Site::mem('Index::start start');
		$this->My->Index =& $this;
		$this->My->prefix = DB::getPrefix();
		$this->My->UserID =& $this->Session->UserID;
		
		$this->My->lang =& $this->Session->Lang;
		$this->My->time = time();
		
		$this->My->langs = Site::getLanguages();
		$this->My->catid = request(URL_KEY_CATID);
		$this->My->action = request(URL_KEY_ACTION);
		$this->My->page = intval(request(URL_KEY_PAGE));
		
		$this->My->offset = $this->My->page * $this->My->limit;
		if ($this->My->offset>DB::MAX_OFFSET) $this->My->offset = DB::MAX_OFFSET;
		$this->My->id = (int)request('id');
		
		if (SITE_TYPE!='im' && !IM) {
			$this->My->init();
			$this->My->uploadify();	
		}
				
		if (SITE_TYPE=='upload') {
			$ex = explode('.',get('upload'));
			if (count($ex)==3) {					
			//	$this->sub_class($ex[0],$ex[1],true);
				Site::header('Content-Type: text/html; charset=utf-8');
				$this->My->upload();
				exit;
			}
		}
		elseif (!Site::$mini) {
			$this->loadSmarty();
			if (in_array(SITE_TYPE, array('index','window','json','ajax','popup','print'))) {
				$this->addJSconf();
				if (USE_TREE) {
					$this->catchTreeURL();	
				} else {
					$this->catchContentURL();
				}
			}
			$this->My->document();
		} else {
			
		}
		Site::mem('Index::start end');
	}
	
	/*
	private function _load($full = true) {

	}
	*/
	

	private static $submit_called = false;
	public function submit() {
		if (self::$submit_called) return;
		self::$submit_called = true;
		$_SESSION['submit'] = md5(time().'hash%d');
		if ($this->Smarty) $this->Smarty->assign('submit',$_SESSION['submit']);	
	}
	
	public function staticPage($url0, $url1 = false) {
		$page = str_replace(array('.class','.html','/','\\','\''),'',$url0);
		if (!$page) return false;
		$u_page = ucfirst($page);
		
		if (is_file(FTP_DIR_TPL.'classes/'.$u_page.'.class.php')) {
			require_once FTP_DIR_TPL.'classes/'.$u_page.'.class.php';
			Factory::add('.'.$page,'tpls/'.TEMPLATE.'/classes/'.$u_page.'.class.php',$u_page.'_class');
			Factory::call('.'.$page, $this);
		}
		$this->submit();
		if (!Site::$mini) $this->My->run();
		if ($url1) {
			$page1 = str_replace(array('.class','.html','/','\\','\''),'',$url1);
			if ($page1=='inc') {
				
			}
			elseif ($page1) {
				if (!Site::$mini && is_file(FTP_DIR_TPL.'pages/'.$page.'/'.$page1.'.tpl')) {
					$this->Smarty->display('pages/'.$page.'/'.$page1.'.tpl');
					return true;
				}
				elseif (is_file(FTP_DIR_TPL.'pages/'.$page.'/'.$page1.'.php')) {
					include FTP_DIR_TPL.'pages/'.$page.'/'.$page1.'.php';
					return true;
				}
				elseif (is_file(FTP_DIR_TPL.'pages/'.$page.'/'.$page1.'.html')) {
					readfile(FTP_DIR_TPL.'pages/'.$page.'/'.$page1.'.html');
					return true;
				}
			}
		}
		if (!Site::$mini && is_file(FTP_DIR_TPL.'pages/'.$page.'.tpl')) {
			$this->Smarty->display('pages/'.$page.'.tpl');
			return true;
		}
		elseif (is_file(FTP_DIR_TPL.'pages/'.$page.'.php')) {
			include FTP_DIR_TPL.'pages/'.$page.'.php';
			return true;
		}
		elseif (is_file(FTP_DIR_TPL.'pages/'.$page.'.html')) {
			readfile(FTP_DIR_TPL.'pages/'.$page.'.html');
			return true;
		}
		
		return false;
	}
	
	private function catchTreeURL() {
		if (!$this->Smarty) return;
		Factory::call('pages')->load($this)->init()->findAll();
		$this->Smarty->assign('tree',$this->tree);
		$this->Smarty->assign('menu',$this->menu);
	}
	
	public function content() {
		if (USE_TREE) {
			return Factory::call('pages');
		} else {
			return Factory::call('content');
		}
	}
	
	public function menu() {
		if (USE_TREE) {
			return Factory::call('tree');
		} else {
			return Factory::call('menu');
		}
	}
	
	private function catchContentURL() {
		if (!$this->Smarty) return;
		$content_name = '';
		$this->module_id = intval(get('id'));
		$this->modules = Site::getModules('content');
		$this->find_id = strpos($_SERVER['QUERY_STRING'],'ID=');

		foreach ($this->modules as $module => $m) {
			if (!$this->module_id && $this->find_id && get($module.'ID')) {
				$this->module_name = $module;
				$this->module_id = intval(request($module.'ID'));
			}
			if (isset($m['id'])) {
				$m['module'] = $module;
				$this->modules_id[$m['id']] = $m;
			}
		}
		if ($this->module_id && $this->module_name) {
			Factory::call('content')->load($this)->init()->findAll();
		}
		if (!$this->menu) {
			$this->menu = Factory::call('menu')->init()->get(url(0), url(1), true);
		}
		if ($this->menu && !$this->tree) {
			Factory::call('menu')->setTree();
		}
		if (!isset($this->menu[1]) || !$this->menu[1]) {
			$content_name = url(1,'',true);
			$this->menu[9] =& $this->menu[0];
		} else {
			$content_name = url(2,'',true);
			$this->menu[9] =& $this->menu[1];	
		}
		/*
		$submenus = array();
		if (url(0)) {
			foreach ($menus['top'] as $i => $m) {
				if ($m['name']==$url0) {
					$submenus = $menu['sub'];
					break;
				}
			}
		}
		$this->set('submenus', $submenus);
		
		*/
		$this->Smarty->assign('content',$this->content);
		
		if (!$this->content) {
			Factory::call('content')->load($this)->init();
			if ($this->menu[9] && $content_name) {
				$this->content = Factory::call('content')->get($content_name);
				if ($this->content) $this->content['is_open'] = true;
			}
			elseif (get('contentID')) {
				$this->content = Factory::call('content')->get(get('contentID'));
				if ($this->content) $this->content['is_open'] = true;
			}
		}

		$this->Smarty->assign('tree',$this->tree);
		$this->Smarty->assign('menu',$this->menu);
		/*
		elseif ($this->module_id && $this->module_name) {
			$this->content['module_id'] = $this->module_id;
			$this->content['module_name'] = $this->module_name;
		}
		$this->Smarty->assign('content',$this->content);
		*/	
	}
	
	/*
	public function admin() {
		require FTP_DIR_ROOT.'inc/Admin.php';
		return Admin::start();
	}
	*/

	public function loadSmarty() {
		if (defined('SMARTY_DIR')) return;
		Site::mem('Index::loadSmarty start');
		define('SMARTY_DIR', FTP_DIR_ROOT.'inc/lib/Smarty/');
		require_once (SMARTY_DIR.'Smarty.class.php');
		require_once (FTP_DIR_ROOT.'mod/MySmarty.php');
		$this->Smarty = new MySmarty($this);
		$this->Smarty->template_dir = FTP_DIR_TPL;
		$this->Smarty->compile_dir = FTP_DIR_TPL.'temp/';
		$this->Smarty->config_dir = FTP_DIR_TPL.'temp/config/';
		$this->Smarty->cache_dir = FTP_DIR_TPL.'temp/cache';
		Site::mem('Index::loadSmarty end');
	}
	

	/**
	* Whether show/hide something
	*/
	public function show($key) {
		return isset($this->show[$key])	? $this->show[$key] : false; 
	}
	public function hide($key) {
		$this->show[$key] = false;	
	}
	public function hideAll() {
		$this->hide('js');
		$this->hide('css');
		$this->printsite = false;	
	}
	public function isHidden() {
		return $this->printsite;
	}
	
	
	public function includeFile($path, $include = true) {
		if ($include && !is_file($path)) {
			$arr_path = explode('/',$path);
			$arr_file = explode('.',end($arr_path));
			array_pop($arr_path);
			$arr_file[0] = 'index';
			$path = join('/',$arr_path).'/'.join('.',$arr_file);
		}
		$path = $this->visual($path);
		$path = FTP_DIR_TPL.$path;
		if (is_file($path)) {
			if ($include) {
				include($path);
				return true;
			} else {
				return $path;
			}
		} else {
			return;
			/*
			$msg = 'Template file '.$path.' for <em>'.SITE_TYPE.'</em> does not exist';
			if (Site::$mini) die($msg);
			Message::Halt('User error',$msg,Debug::backTrace());
			*/
		}
	}
	public function displayFile($path) {
		$php_file = substr($path,0,-4).'.php';
		if (Site::$mini) {
			if (!is_file(FTP_DIR_TPL.$php_file)) {
			//	echo '<pre>File '.FTP_DIR_TPL.str_replace('.tpl','.php',$path).' does not exists.</pre>';
				return;
			}
			require FTP_DIR_TPL.$php_file;
			return;
		}
		
		if (!$this->Smarty || !is_file(FTP_DIR_TPL.$path)) {
			if (is_file(FTP_DIR_TPL.$php_file)) return include FTP_DIR_TPL.$php_file;
		}
		if ($this->Session->msg['text']) {
			$this->Smarty->assign('top_message',$this->Session->msg);
		}
		$this->Smarty->display($path);
	}
	public function fetchFile($path) {
		$this->Smarty->fetch($path);
	}

	public function visual($file) {
		if (Site::$mini || !$this->Edit) return $file;
		return $this->Edit->visual_parse($file);
	}

	/**
	* Var names collector, html bits to be replaced with OB at the end
	* return void()
	*/
	public function cacheVar($key, $value) {
		$this->setVar($key,$value,true);
		return $this->getVar($key, true, true);	
	}
	public function setVar($key, $value, $no_cache = false) {
		if ($no_cache) {
			$this->nocache_vars[$key] = $value;
		} else {
			$this->vars[$key] = $value;
		}
		return $this;
	}
	public function setMeta($key, $value, $no_cache = false) {
		return $this->setVar($key, $value, $no_cache);
	}
	public function addVar($key, $value, $no_cache = false) {
		if ($no_cache && isset($this->nocache_vars[$key])) $this->nocache_vars[$key] .= $value;
		else $this->vars[$key] .= $value;
		return $this;
	}
	public function preVar($key, $value, $no_cache = false) {
		if ($no_cache && isset($this->nocache_vars[$key])) $this->nocache_vars[$key] = $value.$this->nocache_vars[$key];
		else $this->vars[$key] = $value.@$this->vars[$key];
		return $this;
	}
	
	public function getVar($key = NULL, $no_cache = false, $for_ob = true) {
		if ($no_cache) {
			if ($for_ob) {
				return '<!-- var-nocache:'.$key.' -->';
			}
			elseif ($key) {
				return $this->nocache_vars[$key];
			}
			else {
				return $this->nocache_vars;
			}
		} elseif ($for_ob) {
			return '<!-- var:'.$key.' -->';
		} else {
			if ($this->vars['title']!=$this->prefix['title_orig']) $this->vars['title'] = $this->vars['title'].$this->prefix['title'];
			//$this->vars['jsc'] = ($this->vars['jsx'] ? 'ajax_ready_function=function(){'.self::N.$this->vars['jsx'].self::N.'};'.self::N.$this->vars['jsc'] : $this->vars['jsc']);
			//$this->vars['jsc'] = ($this->vars['jsr'] ? '$().ready(function(){'.self::N.$this->vars['jsr'].self::N.'});'.self::N.$this->vars['jsc'] : $this->vars['jsc']);
			$this->vars['jsc'] = ($this->vars['jsc'] ? '<script>'.self::CDA.self::N.$this->vars['jsc'].self::N.self::CDZ.'</script>':'');
			if ($this->combine) {
				$this->vars['js'] = self::getVar('js_and_css',true, true).$this->vars['jsc'].Message::combineJS().$this->vars['js'];
			} else {
				$this->vars['js'] = self::getVar('js_and_css',true, true).$this->vars['jsc'].$this->vars['js'];
			}
			if (!$key) return $this->vars;
			return isset($this->vars[$key])	? $this->vars[$key] : '';
		}
	}
	
	public function setTree($i, $key, $value) {
		$this->tree[$i][$key] = $value;
	}
	public function addTree($title, $url = false, $type = '') {
		$this->tree[] = array(
			'title'	=> $title,
			'url'	=> $url,
			'type'	=> $type
		);	
	}
	
	/**
	* Minify JS and CSS
	*/
	private function minify($http, $ftp, $path, $add) {
		$dir = dirname($path).'/';
		$file = fileOnly($path);
		$ext = ext($file);
		$name = nameOnly(str_replace(array('.pack.','.min.'),'.',$file));
		if ($ext!='js' && $ext!='css') return $http.$path;
		if (!defined('MINIFY_JS_CSS') || !MINIFY_JS_CSS) {
			if (!is_file($ftp.$path)) {
				if (is_file($ftp.$dir.$name.'.pack.'.$ext)) {
					if ($ext=='js') {
						$this->js_arr[] = $ftp.$dir.$name.'.pack.'.$ext;
						if ($this->combine) $this->combined = true;
					}
					return $http.$dir.$name.'.pack.'.$ext;
				}
				elseif (is_file($ftp.$dir.$name.'.min.'.$ext)) {
					if ($ext=='js') {
						$this->js_arr[] = $ftp.$dir.$name.'.min.'.$ext;
						if ($this->combine) $this->combined = true;
					}
					return $http.$dir.$name.'.min.'.$ext;
				}
				elseif (is_file(str_replace(array('/js/','/css/'),'/',$ftp.'assets/'.$path))) {
					if ($ext=='js') {
						$this->js_arr[] = str_replace(array('/js/','/css/'),'/',$ftp.'assets/'.$path);
						if ($this->combine) $this->combined = true;
					}
					return str_replace(array('/js/','/css/'),'/',$http.'assets/'.$path);
				}
				elseif (is_file(str_replace(array('/js/','/css/'),'/',$ftp.'plugins/'.$path))) {
					if ($ext=='js') {
						$this->js_arr[] = str_replace(array('/js/','/css/'),'/',$ftp.'plugins/'.$path);
						if ($this->combine) $this->combined = true;
					}
					return str_replace(array('/js/','/css/'),'/',$http.'plugins/'.$path);
				}
			}
			return $http.$path;
		}
		if (is_file($ftp.$dir.$name.'.pack.'.$ext)) {
			if ($ext=='js') {
				$this->js_arr[] = $ftp.$dir.$name.'.pack.'.$ext;
				if ($this->combine) $this->combined = true;
			}
			return $http.$dir.$name.'.pack.'.$ext;
		}
		elseif (is_file($ftp.$dir.$name.'.min.'.$ext)) {
			if (!is_file($ftp.$path)) {
				if ($ext=='js') {
					$this->js_arr[] = $ftp.$dir.$name.'.min.'.$ext;
					if ($this->combine) $this->combined = true;
				}
				return $http.$dir.$name.'.min.'.$ext;
			}
			elseif (filemtime($ftp.$path) <= filemtime($ftp.$dir.$name.'.min.'.$ext)) {
				if ($ext=='js') {
					$this->js_arr[] = $ftp.$dir.$name.'.min.'.$ext;
					if ($this->combine) $this->combined = true;
				}
				return $http.$dir.$name.'.min.'.$ext;
			}
		}
		elseif (is_file(str_replace(array('/js/','/css/'),'/',$ftp.'assets/'.$path))) {
			if ($ext=='js') {
				$this->js_arr[] = str_replace(array('/js/','/css/'),'/',$ftp.'assets/'.$path);
				if ($this->combine) $this->combined = true;
			}
			return str_replace(array('/js/','/css/'),'/',$http.'assets/'.$path);
		}
		elseif (is_file(str_replace(array('/js/','/css/'),'/',$ftp.'plugins/'.$path))) {
			if ($ext=='js') {
				$this->js_arr[] = str_replace(array('/js/','/css/'),'/',$ftp.'plugins/'.$path);
				if ($this->combine) $this->combined = true;
			}
			return str_replace(array('/js/','/css/'),'/',$http.'plugins/'.$path);
		}


		$from = $ftp.$dir.$file;
		$to = $ftp.$dir.$name.'.min.'.$ext;
		
		if (!is_file($from)) {
			return $http.$path;
		}

		if ($ext=='js') {
			$src = Factory::call('minifier')->minify_js(file_get_contents($from));
			//$src = str_replace("}\n","}",$src);
			//$src = str_replace(")\n.",").",$src);
			$this->js_arr[] = $ftp.$dir.$name.'.min.'.$ext;
			$this->js_modify = true;
			if ($this->combine) $this->combined = true;
		} else {
			$src = Factory::call('minifier')->minify_css(file_get_contents($from));
		}
		if ($ext=='css' && defined('MINIFY_IMAGE') && MINIFY_IMAGE) {
			Message::minify_image($ftp.$dir, $src, $dir.$file);
		}
		if ($src) $ret = file_put_contents($to, $src);

		if ($ret) return $http.$dir.$name.'.min.'.$ext;
		else return $http.$path;
	}
	
	
	/**
	* JS/CSS
	*/
	public function addJSconf() {
		if (!$this->conf) return;
		if (IS_ADMIN && EDIT_LOADED && !Site::$mini) return false;
		$conf = jsConfig($this->conf);
		$this->vars['conf'] .= '<script>var Conf={'.$conf.'}</script>'.self::N;
	}
	public function addConf($key, $val) {
		$this->conf[$key] = $val;
	}
	public function addJS($path, $add = '',$full = false) {
		$this->combined = false;
		if (!$this->show('js')) return false;
		if (strstr($path,'://')) $full = true;
		$base = $version = '';
		if (!$full) {
			$path = $this->minify(HTTP_DIR_TPL,FTP_DIR_TPL,'js/'.$path,$add);
			if ($this->combined) return;
			$base = FTP_EXT;
			$version = (strstr($path,'?')?'&':'?').'v='.$this->My->version;
		}
		if (in_array($path, (array)$this->vars_unique['js'])) return false;
		$this->vars['js'] .= '<script src="'.$base.$path.$version.'"'.$add.'></script>'.self::N;
		$this->vars_unique['js'][] = $path;
		return $this;
	}
	public function addJSA($path,$add = '',$custom = false) {
		$this->combined = false;
		if (!$this->show('js')) return false;
		if (strstr($path,'://')) $custom = true;

		if (!$custom) {
			$path = $this->minify(HTTP_DIR_TPLS,FTP_DIR_TPLS,'js/'.$path,$add);
			if ($this->combined) return;
			$base = FTP_EXT;
			$version = (strstr($path,'?')?'&':'?').'v='.Site::VERSION;
		} else {
			$base = '';
			$version = '';
		}
		if (in_array($path, (array)$this->vars_unique['js'])) return false;
		$this->vars['js'] .= '<script src="'.$base.$path.$version.'"'.$add.'></script>'.self::N;
		$this->vars_unique['js'][] = $path;
		return $this;
	}
	public function addCSS($path,$add = '',$full = false) {
		$this->combined = false;
		if (!$this->show('css')) return false;
		$base = $version = '';	
		if (strstr($path,'://')) $full = true;
		if (!$full) {
			$path = $this->minify(HTTP_DIR_TPL,FTP_DIR_TPL,'css/'.$path,$add);
			if ($this->combined) return;
			$base = FTP_EXT;
			$version = (strstr($path,'?')?'&':'?').'v='.$this->My->version;
		}
		if (in_array($path, (array)$this->vars_unique['css'])) return false;
		$this->vars['css'] .= '<link rel="stylesheet" href="'.$base.$path.$version.'"'.$add.' />'.self::N;
		$this->vars_unique['css'][] = $path;
		return $this;
	}
	public function addCSSA($path ,$add = '', $custom = false) {
		$this->combined = false;
		if (!$this->show('css')) return false;
		if (strstr($path,'://')) $custom = true;
		if (!$custom) {
			$path = $this->minify(HTTP_DIR_TPLS,FTP_DIR_TPLS,'css/'.$path,$add);
			if ($this->combined) return;
			$base = FTP_EXT;
			$version = (strstr($path,'?')?'&':'?').'v='.Site::VERSION;
		} else {
			$base = '';
			$version = '';	
		}
		if (in_array($path, (array)$this->vars_unique['css'])) return false;
		$this->vars['css'] .= '<link rel="stylesheet" href="'.$base.$path.$version.'"'.$add.' />'.self::N;
		$this->vars_unique['css'][] = $path;
		return $this;
	}
	public function addJScode($code) {
		if ($this->show('js') || $this->show('jsc')) $this->vars['jsc'] .= $code;
		return $this;
	}
	/*
	public function addJSready($code, $always = false) {
		if ($this->show('js') || $this->show('jsc')) {
			if ($always) {
				$this->vars['jsx'] .= $code;
			} else {
				$this->vars['jsr'] .= $code;
			}
		}
		return $this;
	}
	*/
	/*
	public function addCSScode($code,$add = '') {
		if ($this->show('css')) $this->vars['css'] .= '<style type="text/css"'.$add.'>'.self::N.$code.self::N.'</style>'.self::N;
		return $this;
	}
	*/	
	public function getCSSfiles($var) {
		if (!$this->vars_unique['css']) $this->vars_unique['css'] = array(TEMPLATE.'css/styles.css');
		return $this->vars_unique['css'];
	}
	public function __toString() {
		return '';	
	}
}