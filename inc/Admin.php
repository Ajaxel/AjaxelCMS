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
* @file       inc/Admin.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class AdminObject {
	public
		$Index,
		$UserID,
		$time,
		$date,
	
		$checkboxes = array(),
		$button = array(),
		$tables = array(),
		$images = array(),
		$array = array(false,false,false,false),
		$dates = array('dated','expires'),
		$output = '',
		$table,
	
		$updated = false,
		$affected = 0,
		$active = true,
		$total = 0,
		$error = false,
	
		$lang = DEFAULT_LANGUAGE,
		$template = TEMPLATE,
		$lang_name = '',
		
		$rs = array(),
		$cat = array(),
		$row = array(),
		$data = array(),
		$post = array(),
		
		$referer = '',
		$url = '&admin',
		$url_full = '?admin',
		$url_save = '?admin',
		$url_full_tab = '?admin',
	
		$ui = array(),
		$all = array(),
		$options = array(),
		$show = array(),
		
		$load_all = true,
		$tpl_file,
		
		$admin = '',
		$name = '',
		$id = 0,
		$idlang = '',
		$name_id,
		$width = 700,
		$reset = false,
	
		$tab = '',
		$tab_class = '',
	
		$title = 'Ajaxel admin panel',
		
		$msg_text = '',
		$msg_error = '',
		$msg_text2 = '',
		$msg_url = '',
		$msg_type = 'tick',
		$msg_ok = true,
		$msg_close = true,
		$msg_reload = true,
		$msg_delay = 1500,
		$msg_focus = '',
		$msg_response = '',
		$msg_redirect = '',
		$msg_js = '',
		$msg_action = 0,
		$json_ret = false,
		$json_push = array(),
		
		$columns_all_lang = array(),
		$columns_set_change = array(),
		$change = array(),
		$columns_skip = array('is_admin','body','sort','options'),
 
		$ftp_dir_files = '',
		$http_dir_files = 'files/default/',
		$ftp_dir_upload = '',
		$http_dir_upload = 'files/',
		$prefix = '',
		$upload = '',

		$sql = '',
		$select = '',
		$filter = '',
		$filter2 = '',
		$order = 'id DESC',
		$offset = 0,
		$limit = ADMIN_LIMIT,
		$offset2 = 0,
		$limit2 = 5,
		$idcol = 'rid',
		
		$has_lang = false,
		$has_global_files = false,
		$has_main_photo = false,
		$has_multi_photo = false,
		$has_files = false,
		
		$current = array(),
		$lang_cnt = 0,
		$langs = array(),
		$currencies = array(),
		$templates = array(),
		$modules = array(),
		$category_modules = array(),
		$grid_modules = array(),
		$content = array(),
		$nav = array(),
		
		$category_options = false,
		
		$get = '',
		$action = false,
		$file = false,
		$tpl = TEMPLATE,
		$currency = '',
		$type = '',
		
		$find = '',
		$andor = 'OR',
		$date_from = '',
		$date_to = '',
		$date_from_int = 0,
		$date_to_int = 0,
		
		$module = '',
		$catid = 0,
		$catref = '',
		$parent_catref = '',
		$modid = 0,
		$sort = 'id',
		$by = 'DESC',
		$country = '',
		$city = '',
		$sortby = array(
			'id'	=> 'ID',
			'sort'	=> 'Sort',
			'edited'=> 'Time edited',
			'added'	=> 'Time added',
			'title'	=> 'Title',
			'name'	=> 'URL name'
		),
		$menuid = 0,
		$treeid = 0,
		$conid = 0,
		$setid = 0
	;
	
	protected
		$hl_name = '',
		$hl_id = 0,
		$hl_group = false,
		$charts = array(),
		
		$json_data = '[]',
		$json_row = '[]',
		$json_templates = '[]',
		$json_styles = '[]',
		$json_langs = '[]',
		$json_currencies = '[]',
		$json_array = array('files'=>'[]'),
		
		$arr_msg = array(),
	
		$exit = false,
		$json_exit = false,
		
		$exact = false,
		
		$status = '',
		$pop = false,
		
		$submitted = false,
		$parent_category = 0,
		
		$image_sizes = array(
			1	=> array(0, 480) // 640, 480
			,2	=> array(0, 120) // 112, 83 | 100, 100
			,3	=> array(0, 85) // 60x60
		)
	;
	
	private
		$lang_collect = array(),
		$lang_number = 902,
		$visual_id
	;
	
	const
		KEY_CATID = 'cid',
		KEY_MENUID = 'mid',
		KEY_TREEID = 'tid',
		KEY_PAGE = 'p',
		KEY_LIMIT = 'limit',
		KEY_LANG = 'l',
		KEY_ACTION = 'a',
		KEY_FIND = 'f',
		KEY_AND_OR = 'ao',
		KEY_DATE_FROM = 'df',
		KEY_DATE_TO = 'dt',
		KEY_MODULE = 'm',
		KEY_GRID = 'g',
		KEY_CONID = 'conid',
		KEY_GET = 'get',
		KEY_SORT = 's',
		KEY_BY = 'b',
		KEY_FILE = 'file',
		KEY_TAB = 'tab',
		KEY_LOAD = 'load',
		KEY_ALL = 'all',
		KEY_TPL = 'tpl',
		KEY_CURRENCY = 'currency',
		KEY_EXACT = 'exact',
		KEY_NEW = 'new',
		KEY_RESET = 'reset',
		KEY_COUNTRY = 'cn',
		KEY_CITY = 'ct',
		KEY_ADD = 'add',
		KEY_TYPE = 't',
		KEY_LANG_ALL = 'la',
		KEY_NO_RELOAD = 'nr'
	;
	
	protected function __construct($class, $mini = false) {
		Site::mem('Admin start');
		$this->id = get('id');
		if (!$this->id) $this->id = post('id');
		if (!$this->id || $this->id=='undefined' || $this->id=='null') $this->id = 0;

		if (is_object($class)) {
			$class = get_class($class);	
		}
		$this->tab = html(request(self::KEY_TAB));
		$this->name = strtolower(substr($class,5));
		$this->admin = html(get(URL_KEY_ADMIN));
		if ($this->tab) {
			$this->name .= '_tab';
		}
		
		$this->id = (int)$this->id;
		$this->name_id = $this->name.'_'.str_replace('.','_',$this->id);
		
		$this->date();
		$this->Index = Index::getInstance();
		$this->class = $class;
		$this->UserID = $this->Index->Session->UserID;
		
		if ($mini) {
			Site::mem('Admin end');
			return;
		}
			
		if (!$this->title) $this->title = DOMAIN.' admin panel';
		$this->Index->setVar('title','Admin > '.$this->title);
		
		if (SITE_TYPE=='json' && get(self::KEY_GET)=='action' && get(self::KEY_ACTION)=='getTemplates') {
			echo json(array(
				'templates'	=> Site::getTemplates(true),
				'langs'		=> Site::getLanguages()
			));
			$this->exit = true;
			exit;
		}
		
		$allow = Allow()->admin('admin','view');
		if (SITE_TYPE=='json') {
			if ($this->Index->Session->login_try) {
				$ret = $this->Index->Session->getMsg();
				if ($this->Index->Session->login_error) {
					echo json($ret);
				}
				elseif ($allow) {
					echo json($allow);
				}
				else {
					if (GROUP_ID==3) $ret['redirect'] = '/';
					echo json($ret);
				}
				if (get('ui')) $_SESSION['UI_ADMIN'] = get('ui');
				$this->exit = true;
				exit;
			}
		}
		if ((SITE_TYPE=='window' || SITE_TYPE=='json' || SITE_TYPE=='upload') && GROUP_ID==3) {
			// allow him to edit on public 
			$_SESSION['RIGHTS']['filebrowser'] = true;
			$_SESSION['RIGHTS']['filebrowser_user_path'] = false;
			$_SESSION['RIGHTS']['template'] = TEMPLATE;
			$_SESSION['RIGHTS']['files'] = TEMPLATE;	
		} else {
			
			if ($allow) {
				if (in_array(SITE_TYPE, array('index','popup','print','ajax','window','html'))) {
					$this->login($allow);
				} else {
					if (SITE_TYPE=='json') {
						Site::header('Content-Type: text/json; charset=utf-8');
						echo json($allow);
					} else {
						echo $allow['text'];	
					}
				}
				exit;
			}
		}
		
		if ($this->Index->Session->login_done) {
			URL::redirect('?'.URL_KEY_ADMIN);
			return;
		}
		$this->super();
		$this->genHead();
		$this->init();
		Site::mem('Admin end');
	}
	
		
		
	private function test() {

	}
		
	private function date() {		
		$cm = intval(date('m'));
		$cy = intval(date('Y'));
		$cd = intval(date('d'));
		$pm = $cm - 1;
		$ppm = $pm - 1;
		$py = $cy;
		$ppy = $py;
		if ($cm==1) {
			$py = $cy - 1;
			$ppy = $py;
			$pm = 12;
			$ppm = 11;
		}
		
		$tt = mktime(0,0,0,$cm, $cd, $cy);
		$yt = $tt - 86400;
		
		$this->time = $_SERVER['REQUEST_TIME'];
	
		$this->date = array(
			'yesterday_time'					=> $yt,
			'today_time'						=> $tt,
			'current_month_step_month'			=> $cm,
			'current_year_step_month'			=> $cy,
			'previous_month_step_month'			=> $pm,
			'double_previous_month_step_month'	=> $ppm,
			'previous_year_step_month'			=> $py,
			'double_previous_year_step_month'	=> $ppy,
		);	
	}
	
	private function login($allow) {
		$this->ui();
		$this->Index->addJSconf();
		$this->Index->setVar('title', lang('$Ajaxel admin panel'));

		$this->Index->addJSA('jquery/'.JQUERY);
		if (defined('JQUERY_MIGRATE') && JQUERY_MIGRATE) $this->Index->addJSA('jquery/'.JQUERY_MIGRATE);
		$this->Index->addJSA('jquery/'.JQUERY_UI);
		$this->Index->addJSA('plugins/jquery.blockUI.js');
		$this->Index->addJSA('global.js');
		$this->Index->addCSSA('ui/'.($_SESSION['UI_ADMIN']?$_SESSION['UI_ADMIN']:((defined('UI_ADMIN') && UI_ADMIN)?UI_ADMIN:'selene')).'/'.JQUERY_CSS,' id="s-theme_admin"');
		$this->Index->addCSSA('admin_'.TEMPLATE_ADMIN.'.css');
		$this->Index->addCSSA('edit_'.TEMPLATE_ADMIN.'.css');
		$this->Index->addCSSA('global.css');
		$this->show['header'] = false;
		if (in_array(SITE_TYPE,array('index','popup','print'))) {
			$this->show['html'] = true;
			$this->show['footer'] = true;	
		} else {
			$this->show['html'] = false;
			$this->show['footer'] = false;	
		}
		ob_start(($this->Index->My->OBparse()?array('OB','handler'):false));
		$this->tpl('_login', array('allow' => $allow));
		ob_end_flush();
	}
	
	protected function ui() {
		if (USE_ADMIN_UI):
			$this->ui['body'] = ' id="a-body" class="ui-widget-content ui-widget-header"';
			$this->ui['wrapper'] = ' ui-widget-content ui-corner-all';
			$this->ui['buttons'] = ' ui-widget-content ui-state-default ui-corner-bottom';
			$this->ui['a-fm'] = ' ui-widget ui-dialog-titlebar ui-widget-header';
			$this->ui['a-m'] = ' ui-dialog-titlebar ui-widget-header';
			$this->ui['top-s'] = ' ui-widget ui-widget-content ui-corner-all';
			$this->ui['top'] = ' ui-widget ui-widget-content ui-corner-all';
			$this->ui['h1'] = ' ui-dialog-titlebar ui-widget-header ui-corner-top';
			$this->ui['sub-h1'] = ' ui-dialog-titlebar ui-widget-header ui-corner-top';
			$this->ui['sub-m'] = ' ui-state-highlight ui-widget-header';
			$this->ui['logo'] = ' ui-state-default ui-widget-header ui-corner-top';
			$this->ui['i_bot'] = ' ui-state-default ui-widget-header ui-corner-bottom';
			$this->ui['i_mid'] = ' ui-state-default ui-dialog-titlebar ui-widget-header';
			$this->ui['title'] = ' ui-widget ui-state-highlight ui-widget-header ui-corner-top';
			$this->ui['bot'] = '';
		endif;	
	}
	
	protected function getTplFile() {

		if (is_file(FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.($this->name=='main'?'_main':$this->name).'.php')) {
			$this->tpl_file = FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.($this->name=='main'?'_main':$this->name).'.php';
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.($this->name=='main'?'_main':$this->name).'.php')) {
			$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.($this->name=='main'?'_main':$this->name).'.php';
		}
		else {
			if (!is_file(FTP_DIR_ROOT.'tpls/admin/'.($name=='main'?'_main':$name).'.php')) {
				if (substr($name,0,5)=='grid_') {
					$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/grid.php';
				}
				else {
				//	Message::halt('Not implemented', 'No file: '.FTP_DIR_ROOT.'tpls/admin/'.($this->name=='main'?'_main':$this->name).'.php'.'');	
					$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/'.($this->name=='main'?'_main':$this->name).'.php';
				}
			} else {
				$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/'.($name=='main'?'_main':$name).'.php';
			}
		}
	}
	
	public function getUIsAdmin() {
		$d = FTP_DIR_ROOT.'tpls/css/ui/';
		if (!is_dir($d)) return array();
		$dh = opendir($d);
		$tpls = array();
		while (($file = readdir($dh))!==false) {
			if ($file=='.' || $file=='..') continue;
			if (is_dir($d.$file)) $tpls[$file] = array($file);
		}
		ksort($tpls);
		return $tpls;
	}
	public function getTemplate() {
		$this->templates = Site::getTemplates();
		$tpl = get(self::KEY_TPL,'',request('template'));
		$this->tpl = TEMPLATE;
		
		if ($tpl=='admin') {
			$this->tpl = $_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN];
			if ($this->tpl=='admin' || !$this->tpl) $this->tpl = TEMPLATE;
			$this->template = 'admin';
		}
		elseif ($tpl && array_key_exists($tpl,$this->templates)) {
			$this->tpl = $tpl;
			$this->template = $this->tpl;
			$_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN] = $this->tpl;
		}
		elseif (@$_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN] && array_key_exists($_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN],$this->templates)) {
			$this->tpl = $_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN];
			$this->template = $this->tpl;
		}
		return $this->tpl;
	}
	
	public function getLang() {

		$this->lang = $this->Index->Session->Lang;
		$lang = get(self::KEY_LANG,false,'[[:CACHE:]]');
		if ($lang=='all') {
			$this->all['lang'] = true;
			$lang = $this->Index->Session->Lang;
		} else {
			$this->all['lang'] = false;	
		}
		
		if ($lang && array_key_exists($lang, $this->langs)) {
			$this->lang = $lang;
		}
		return $this->lang;
	}
	
	
	protected function super() {
		
		$this->table = $this->name;
		$this->load_all = !in_array(SITE_TYPE, array('upload','download'));
		if (is_file(FTP_DIR_ROOT.'config/lang/lang_admin_'.$this->Index->Session->Lang.'.php')) {
			$_lang = array();
			require FTP_DIR_ROOT.'config/lang/lang_admin_'.$this->Index->Session->Lang.'.php';
			Conf()->merge2('_lang',$_lang);
		}
		
		if (substr($this->table,0,9)=='settings_') {
			$this->table = substr($this->table,9);
			$this->name_id = 'settings_'.$this->table.'_'.$this->id;
		}
		
		if ($this->tab) $this->table = substr($this->table,0,-4);

		$this->post = post('data','',array());
		//$this->postVars();
		$this->button['save'] = false;
		$this->button['sort'] = false;
		$this->button['add'] = false;
		$this->button['form'] = false;
		$this->nav['pages'] = array();
		$this->button['limits'] = array(
			5,10,20,30,50,100,200,500,5000
		);
		if (!in_array(ADMIN_LIMIT, $this->button['limits'])) {
			$this->button['limits'][] = ADMIN_LIMIT;
			natsort($this->button['limits']);
		}

		$this->limit = get(self::KEY_LIMIT,false,'[[:CACHE:]]');
		if (!$this->limit || !in_array($this->limit, $this->button['limits'])) $this->limit = ADMIN_LIMIT;
		
		$this->page = intval(get(self::KEY_PAGE));
		$this->offset = $this->page * $this->limit;
		
		$this->getTemplate();

		$this->current = DB::getAll('SELECT name, val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->tpl.'\'','name|val');
		if (!$this->current) {
			$this->tpl = DB::one('SELECT name FROM '.DB_PREFIX.'templates WHERE name NOT IN (\'global\',\'hook\',\'admin\')');
			$this->current = DB::getAll('SELECT name, val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->tpl.'\'','name|val');
		}
		$this->getTplFile();
		
		$this->prefix = DB_PREFIX.$this->current['PREFIX'];
		if (!in_array($this->current['PREFIX'].'content', DB::tables(false, true))) {			
			$this->tpl = DEFAULT_TEMPLATE;
			$this->template = $this->tpl;
			$_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN] = $this->tpl;
			$this->current = DB::getAll('SELECT name, val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->tpl.'\'','name|val');	
			$this->prefix = DB_PREFIX.$this->current['PREFIX'];
		}

		$this->ftp_dir_files = FTP_DIR_ROOT.DIR_FILES.trim($this->current['PREFIX'],'_').'/';
		$this->http_dir_files = FTP_EXT.DIR_FILES.trim($this->current['PREFIX'],'_').'/';

		$this->ftp_dir_upload = FTP_DIR_ROOT.DIR_FILES;
		$this->http_dir_upload = FTP_EXT.DIR_FILES;
		$this->fixUploadFolders();
		DB::setPrefix($this->prefix);
		
		$this->country = request(self::KEY_COUNTRY,false,'[[:CACHE:]]');
		if ($this->country=='all') $this->all['country'] = true;
		$this->city = request(self::KEY_CITY);
		if ($this->city=='all') $this->all['city'] = true;
		
		if ($this->template==$this->Index->Session->Template) {
			$this->langs = Site::getLanguages();
			$this->currencies = Site::getCurrencies();
		}
		else {
			$langs = strexp($this->current['languages']);
			if (!$langs || !is_array($langs)) $langs = array('en'=>array('English','English','ENG', 1));
			foreach ($langs as $l => $a) {
				if (!$a[3] && $l!=DEFAULT_LANGUAGE) continue;
				$this->langs[$l] = $a;	
			}
			$this->currencies = strexp($this->current['currencies']);
			unset($this->current['currencies'],$this->current['templates'],$this->current['languages']);
		}

		$this->getLang();
		$this->lang_cnt = count($this->langs);
		$this->lang_name = $this->langs[$this->lang][0];
		$this->reset = isset($_GET[self::KEY_RESET]) || get(URL_KEY_ADMIN)==self::KEY_RESET;
		if ($this->reset) {
			DB::resetCache();
		}
		if (get('clean')) {
			Cache::saveSmall(get('clean'),NULL);
		}
		
		// name_id must have rid if content for quick_edit
		if (substr($this->name,0,8)=='content_' && in_array($this->name,DB::tables())) {
			$rid = DB::one('SELECT rid FROM '.$this->prefix.$this->name.' WHERE id='.(int)$this->id);
			$this->name_id = $this->name.'_'.$rid;
		}
		
		if ($this->load_all):
			$this->json_langs = json(array_label($this->langs));
			if (is_file(FTP_DIR_ROOT.'tpls/'.$this->template.'/classes/MyClass.php')) {
				require_once FTP_DIR_ROOT.'tpls/'.$this->template.'/classes/MyClass.php';
				$this->Index->My = new MyClass($this->Index);
			}
			$this->json_templates = json($this->editorTemplates());
			$arr = DB::getAll('SELECT template, val FROM '.DB_PREFIX.'settings WHERE name=\'UI_ADMIN\'','template|val');
			$styles = array();
			foreach ($arr as $t => $ui) {
				if (!isset($this->templates[$t])) continue;
				$styles[$t] = array(
					$ui,
					$this->templates[$t][0]
				);
			}
			$this->json_styles = json($styles);
		endif;
		
		// modules
		$this->modules = Site::getModules('content', true, $this->template);
		$this->category_modules = Site::getModules('category', true, $this->template);
		$this->grid_modules = Site::getModules('grid', true, $this->template);
		
		if (in_array($this->table, array('content','category','categories','grid')) || substr($this->table,0,5)=='grid_') {
			$module = request(self::KEY_MODULE,false,'[[:CACHE:]]');
		} else {
			$module = request(self::KEY_MODULE);
		}
		
		
		
		if (is_array($module)) $module = '';
		if ($this->class=='AdminCategories' || $this->class=='AdminEntries' || $this->class=='AdminContent') {
			if ($this->name=='categories') {
				if ($module && ($module=='entries' || $module==self::KEY_ALL || array_key_exists($module, $this->category_modules))) {
					$this->module = $module;
				}
			} else {
				if (array_key_exists($module, $this->modules)) {
					$this->module = $module;
				}
			}
		} else {
			if ($this->table=='grid' || substr($this->table,0,5)=='grid_') {
				if ($module && ($module!==self::KEY_ALL && array_key_exists($module, $this->grid_modules))) {
					$this->module = $module;
				}
				else {
					$this->module = key($this->grid_modules);
				}
			}
		}
		


		// currency
		$currency = request(self::KEY_CURRENCY,false,'[[:CACHE:]]');
		if (!is_array($currency) && array_key_exists($currency, Site::getCurrencies())) {
			$this->currency = $currency;
		}
		elseif ($this->Index->Session->Currency) {
			$this->currency = $this->Index->Session->Currency;
		}
		else {
			$this->currency = DEFAULT_CURRENCY;	
		}
		
		// category
		$this->catid = intval(request(self::KEY_CATID));
		if ($this->catid && $this->module && array_key_exists($this->module, $this->category_modules)) {
			$this->catref = Factory::call('category')->catRef($this->catid, 'category_'.$this->module);
			$this->parent_catref = Category::catRef($this->catref,'category_'.$this->module);
		}
		elseif ($this->catid && $this->table && array_key_exists($this->table, $this->category_modules)) {
			$this->catref = Factory::call('category')->catRef($this->catid, 'category_'.$this->table);
			$this->parent_catref = Category::catRef($this->catref,'category_'.$this->table);
		}
		
		
		// menu
		if (!USE_TREE) {
			$this->menuid = intval(request(self::KEY_MENUID,false,'[[:CACHE:]]'));
			if ($this->menuid && !DB::getNum('SELECT 1 FROM '.$this->prefix.'menu WHERE id='.$this->menuid.' AND active!=2')) {
				$this->menuid = 0;	
			}
		}
		
		// tree
		if (USE_TREE) {
			$this->treeid = intval(request(self::KEY_TREEID,false,'[[:CACHE:]]'));
			if ($this->treeid && !DB::getNum('SELECT 1 FROM '.$this->prefix.'tree WHERE id='.$this->treeid.' AND active!=2')) {
				$this->treeid = 0;	
			}
		}
		
		// find
		$this->find = post(self::KEY_FIND);
		if (!$this->find) {
			$this->find = get(self::KEY_FIND);
		} else {
			$_GET[self::KEY_FIND] = $_POST[self::KEY_FIND];	
		}
		
		if (is_array($this->find)) {
			$find = array();
			foreach ($this->find as $k => $v) {
				$find[$k] = e(Parser::strSearch($v), false);
			}
			$this->find = $find;
		} else {
			$this->find = e(Parser::strSearch($this->find), false);	
		}
		
		// other
		$this->action = request(self::KEY_ACTION);
		$this->type = request(self::KEY_TYPE,false,'[[:CACHE:]]');
		
		$this->array['andor_types'] = array(
			'AND' => 'AND',
			'OR' => 'OR',
			'REGEXP' => 'REGEXP'
		);
		$this->andor = request(self::KEY_AND_OR,false,'[[:CACHE:]]');
		if (!array_key_exists($this->andor,$this->array['andor_types'])) {
			$this->andor = '';
		}
		$this->date_from = request(self::KEY_DATE_FROM);
		$this->date_to = request(self::KEY_DATE_TO);
		$this->date_from_int = $this->toTimestamp($this->date_from);
		$this->date_to_int = $this->toTimestamp($this->date_to, true);
		
		$this->file = request(self::KEY_FILE);
		$this->get = request(self::KEY_GET);
		$this->pop = request('popup');
		$this->conid = intval(request(self::KEY_CONID));
		$this->data = post('data', '', array());	
		$this->findCheckBoxes($this->data);	
		
		$this->sort = request(self::KEY_SORT,false,'[[:CACHE:]]');
		$this->by = request(self::KEY_BY,false,'[[:CACHE:]]');
		$this->exact = request(self::KEY_EXACT);
		if ($this->id===self::KEY_NEW) $this->id = 0;
		
		if ($this->exact && $this->id && in_array('rid',DB::columns($this->table))) {
			if ($this->table=='entries') {
				$rs = DB::row('SELECT rid, lang FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				$this->id = (int)$rs['rid'];
				$this->lang = $rs['lang'];				
			} else {
				$rs = DB::row('SELECT rid, setid, lang FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				$this->id = (int)$rs['rid'];
				$this->lang = $rs['lang'];
				$this->conid = $rs['setid'];
			}
		}
		
		$this->url = URL::build(URL_KEY_ADMIN,self::KEY_LANG,self::KEY_TYPE,self::KEY_CATID,self::KEY_FIND,self::KEY_MENUID,self::KEY_TREEID,self::KEY_MODULE,self::KEY_TAB,self::KEY_TPL,'country','state','city','date_from','date_to','andor',self::KEY_LOAD);
		$this->url = str_replace('&amp;','&',$this->url);
		$this->url_full = '?'.rtrim($this->url,'&');
		$this->url_save = '?'.URL::make(array(URL_KEY_ADMIN=>get(URL_KEY_ADMIN),'id'=>$this->id,self::KEY_LANG=>$this->lang,self::KEY_CONID=>$this->conid,self::KEY_TAB=>$this->tab)).URL::build(self::KEY_MODULE);
		$this->url_save = str_replace('&amp;','&',$this->url_save);
		$this->url_full_tab = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,self::KEY_TAB=>$this->tab,self::KEY_LOAD=>'',self::KEY_PAGE=>get(self::KEY_PAGE)));
		
		$this->referer = URL::get();
		$this->referer = str_replace('&amp;','&',$this->referer);
		if ($this->tab) {
			$this->submitted = post($this->name.'_'.$this->tab.'-submitted');
			$this->tab_class = 'Admin'.ucfirst($this->name).'_'.$this->tab;
			if (!class_exists($this->tab_class)) {
				$class_file = FTP_DIR_ROOT.'mod/'.$this->tab_class.'.php';
				if (is_file($class_file)) {
					require_once($class_file);
					if (!class_exists($this->tab_class)) {
						$this->tab_class = false;
					}
				} else {
					$this->tab_class = false;	
				}
			}
		} else {
			$this->submitted = post($this->name.'-submitted');	
		}
		
		if (get('hl')) {
			list ($name, $id, $group) = explode('-',get('hl'));
			$this->hl_name = $name;
			$this->hl_id = $id;
			$this->hl_group = $group;
		}
		
		$this->options['translate_cols'] = array('title','teaser','descr','body');
		
		if (SITE_TYPE=='window') {
			$this->options['content_save'] = 'S.A.W.save(\''.$this->url_save.'&'.self::KEY_ACTION.'=save\', this.form, this);';
			$this->options['content_lang_save'] = 'S.A.L.lang=this.value;S.A.W.close(\''.$this->name_id.'\');S.A.W.open(\'?'.URL_KEY_ADMIN.'='.get(URL_KEY_ADMIN).'&id='.$this->id.'&'.self::KEY_CONID.'='.$this->conid.($this->tab?'&'.self::KEY_TAB.'='.$this->tab:'').'&'.self::KEY_LANG.'=\'+S.A.L.lang);';
			
		}
		
		$_SESSION['RIGHTS']['filebrowser'] = true;
		$_SESSION['RIGHTS']['filebrowser_user_path'] = false;
		$_SESSION['RIGHTS']['template'] = $this->tpl;
		$_SESSION['RIGHTS']['files'] = $this->tpl;	
		
		$this->test();	
	}
	
	public function editorTemplates() {
		$ftp_dir = FTP_DIR_TPL.'editor/';
		if (!is_dir($ftp_dir)) {
			return array();
		}
		$html_dir = '/'.HTTP_DIR_TPL.'editor/';
		$files = File::openDirectory($ftp_dir);
		$ret = array();
		foreach ($files as $file) {
			$ret[] = array(
				'title'			=> $file,
				'src'			=> $html_dir.$file,
				'description'	=> $html_dir.$file
			);
		}
		return $ret;
	}
	
	private function findCheckBoxes(&$rs) {
		if (isset($rs['checkboxes']) && is_array($rs['checkboxes'])) {
			foreach ($rs['checkboxes'] as $i => $n) {
				if (is_array($n)) {
					foreach ($n as $_i => $_n) {
						$rs[$i][$_n] = $rs[$i][$_n];
					}
				} else {
					$rs[$n] = $rs[$n];
				}
			}
			unset($rs['checkboxes']);
		}
		
	}
	
	/*
	private function postVars() {
		
		$arr = array(
			'title','prefix','name','data_from','descr','body','teaser','edited','added',
			'password','login','email','inuse','active','profile','notes','id','options'
		);	
		foreach ($arr as $k) if (!isset($this->post[$k])) $this->post[$k] = '';
		if ($this->name=='users') {
			$arr = array(
				'firstname','lastname','genre','classid','groupid','dob','phone','fax','zip','city','country','street','about'
			);
			foreach ($arr as $k) if (!isset($this->post['profile'][$k])) $this->post['profile'][$k] = '';
		}
	}
	*/
	
	protected function prefix() {
		return $this->prefix;	
	}
	
	protected function isEdit($justSet = false) {
		$ret = $this->Index->Session->isEdit($this->table,$this->id,$this->lang,$justSet);
		if ($justSet) return false;
	//	$ret = array('userid'=>2,'expiration'=>$this->time + COOKIE_LIFETIME);
		if ($ret) {
			$seconds = $ret['expiration'] - COOKIE_LIFETIME + 2;
			$this->json_ret = '';
			$this->msg_text = lang('$Sorry, but another administrator: %1 started to edit<br />this content %2 ago.<br />Please revisit again in %3!','<a href="javascript:;" onclick="S.A.W.open(\'?'.URL_KEY_ADMIN.'=users&id='.$userid.'\')">'.Data::user($ret['userid'], 'login').'</a>',Date::timeAgo($seconds, true), Date::timeAgo($this->time - EDIT_LIFETIME));
			$this->msg_reload = false;
			$this->msg_delay = 10000;
			$this->msg_type = 'clock';
			$this->msg_close = false;
			if (SITE_TYPE=='json') {
				ob_clean();
				Site::header('Content-Type: text/json; charset=utf-8'); 
				echo json($this->json_ret());
				exit;
			}
			return $ret;	
		}
	}
	
	protected function fetchChange() {
		if (!$this->id || $this->id===self::KEY_NEW) return false;
		
		if ($this->change) {
			foreach ($this->change as $i => $a) {
				if ($a['array_key']) {
					foreach ($a['columns'] as $col) {
						$this->post['changes'][$a['array_key']][$col] = DB::getAll('SELECT added, val FROM '.$this->prefix.'changes WHERE `table`=\''.$a['table'].'\' AND setid='.$this->id.' AND `col`=\''.$col.'\' ORDER BY id','added|val');
					}
				} else {
					foreach ($a['columns'] as $col) {
						$this->post['changes'][$col] = DB::getAll('SELECT added, val FROM '.$this->prefix.'changes WHERE `table`=\''.$a['table'].'\' AND setid='.$this->id.' AND `col`=\''.$col.'\' ORDER BY id','added|val');
					}
				}
			}
		}
		foreach ($this->columns_set_change as $col) {
			$this->post['changes'][$col] = DB::getAll('SELECT added, val FROM '.$this->prefix.'changes WHERE `table`=\''.$this->table.'\' AND setid='.$this->id.' AND `col`=\''.$col.'\' ORDER BY id','added|val');
		}
	}
	
	protected function fixWindow() {
		
		if (!$this->post) {
			$this->id = 0;
			$this->post = post('data');
		} else {
			$this->isEdit();
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			//if (!isset($this->post['name'])) $this->post['name'] = Parser::name($this->post['title']);

			$this->fetchChange();			

			if ($this->has_orders) {
				
			//	$sql = 'SELECT userid, sellerid, price, currency, quantity, options, ';
				
			//	$sql = 'SELECT a.id, a.status, b.title, b.price, b.quantity, b.currency, a.ordered, b.type, b.sellerid, b.table, b.itemid FROM '.DB_PREFIX.'orders2_map b LEFT JOIN '.DB_PREFIX.'orders2 a ON (a.id=b.orderid) WHERE a.userid='.$this->id.' AND a.status!='.Site::STATUS_NOT_PAID.' ORDER BY ordered DESC LIMIT 20';
			//	$this->post['orders'] = DB::getAll($sql);	
			}
			
		}
		
		if ($this->has_main_photo || $this->has_multi_photo || $this->has_files) {
			$this->uploadHash();
			$this->global_action('main_photo_window');
			if ($this->has_files || $this->has_multi_photo) {
				$this->json_array['files'] = json($this->filesToJson());
			}
		}
		
		if (!isset($this->post['options'])) $this->post['options'] = array();
		elseif (!is_array($this->post['options'])) $this->post['options'] = unserialize($this->post['options']);
		if (isset($this->post['catrefs']) && !is_array($this->post['catrefs'])) $this->post['catrefs'] = explode(',',trim($this->post['catrefs'],','));
		
		foreach ($this->dates as $col) {
			if (isset($this->post[$col]) && $this->post[$col]) {
				$this->post[$col.'_time'] = $this->fromTimestampTime($this->post[$col]);
				$this->post[$col] = $this->fromTimestamp($this->post[$col]);
			}
			else {
				$this->post[$col] = '';
				$this->post[$col.'_time'] = '';
			}
		}
	}
	
	protected function fixRow(&$rs, $listing = true) {
		if (isset($rs['added']) && $rs['added']) {
			$rs['date'] = date('d M, H:i', $rs['added'] - Session()->Timezone * 60 + TIMEZONE_PHP_DIFF * 60);
		}
		if (isset($rs['updated']) && $rs['updated']) {
			$rs['date2'] = date('d M, H:i', $rs['updated'] - Session()->Timezone * 60 + TIMEZONE_PHP_DIFF * 60);
		}
		foreach ($this->columns_skip as $col) {
			if (isset($rs[$col])) unset($rs[$col]);
		}
	}
	
	protected function listing() {
		$allow = Allow()->admin($this->name, 'view', false, false, $this->table, $this->id);
		if ($allow) {
			$this->inc('allow', array('allow' => $allow));
			return;	
		}
		if ($this->sql && is_string($this->sql)) {
			$qry = DB::qry($this->sql, $this->offset, $this->limit);
			if (strstr($this->sql, 'SQL_CALC_FOUND_ROWS')) {
				$this->total = DB::rows();
			}
			$this->data = array();
			while ($rs = DB::fetch($qry)) {
				$this->fixRow($rs);
				array_push($this->data, $rs);
			}
			if (!$this->total) $this->total = count($this->data);
			DB::free($qry);
		}
		elseif (isset($this->data['FOUND_ROWS()'])) {
			$this->total = $this->data['FOUND_ROWS()'];
			unset($this->data['FOUND_ROWS()']);
		} else {
			$this->total = count($this->data);	
		}

		$this->json_data = json(html($this->data));
		$this->nav();
	}
	
	protected function find($col) {
		if (!$this->find) return array(
			'where' => '',
			'select'=> '',
			'order' => '',
			'group' => ''
		);
		$ex = explode(' ',trim($this->find));
		$j = array();
		$where = $select = $order = $group = '';
		foreach ($ex as $e) $j[] = $col.' LIKE \'%'.$e.'%\'';
		switch ($this->andor) {
			case 'AND':
				$where .= ' AND ('.join(' AND ',$j).')';
			break;
			case 'OR':
				$where .= ' AND ('.join(' OR ',$j).')';
				$a = array();
				foreach ($ex as $e) {
					$a[] = '(CASE WHEN '.$col.' LIKE \''.$e.'\' THEN 1 ELSE 0 END)';
				}
				$select .= ', ('.join(' + ',$a).') AS relevance';
				$order = 'relevance DESC';
			//	$group = 'GROUP BY TRUE';
			break;
			case 'REGEXP':
				$where .= ' AND '.$col.' REGEXP (\''.$this->find.'\')';
			break;
		}	
		return array(
			'where' => $where,
			'select'=> $select,
			'order' => $order,
			'group' => $group
		);
	}
	
	protected function findID($col, $find = false) {
		if (!$find) $find = $this->find;
		$ret = '';
		if (is_numeric($find)) {
			$ret .= ' AND '.$col.'='.$find;
		}
		$ex = explode('-',$find);
		if (count($ex)==2 && is_numeric($ex[0]) && is_numeric($ex[1])) {
			$ret .= ' AND '.$col.'>='.trim($ex[0]).' AND '.$col.'<='.trim($ex[1]).'';	
		}
		return $ret;
	}
	
	protected function allow($area, $todo, $rs = array(), $data = array(), $table = false, $id = 0) {
		if (!$rs) $rs = $this->rs;
		if (!$data) $data = $this->data;
		if (!$table) $table = $this->table;
		if (!$id) $id = $this->id;
		$allow = Allow()->admin($area, $todo, $rs, $data, $table, $id);
		if (!$allow) return false;
		if ($allow['text']===true) $allow['text'] = '';
		$this->msg_text = $allow['text'];
		$this->msg_error = $allow['text'];
		$this->msg_type = $allow['type'];
		$this->msg_delay = $allow['delay'];
		$this->msg_reload = false;
		$this->msg_ok = false;
		$this->msg_close = false;
		if (SITE_TYPE!='window' && SITE_TYPE!='json' && $this->tab && !$this->action) {
			$ret = $this->json_ret();
			if (isset($ret['text'])) {
				echo '<div id="center-area" class="admin-area"><script type="text/javascript">
	var data='.json($ret).';S.A.W.dialog(data);
</script></div>';
			}
		} else {
			Site::header('Content-Type: text/json; charset=utf-8'); 
			echo json_encode($this->json_ret());
		}
		/*
		if (SITE_TYPE=='json') {
			Site::header('Content-Type: text/json; charset=utf-8'); 
			echo json($this->json_ret());			
		} else {
			$ret = $this->json_ret();
			if (isset($ret['text'])) {
				echo '<div id="center-area" class="admin-area"><script type="text/javascript">
	var data='.json($ret).';S.A.W.dialog(data);
</script></div>';
			}
		}
		*/
		exit;
	}
	
	protected function post($key, $form = true) {
		if (isset($this->post[$key])) {
			if ($form===true) {
				return strform($this->post[$key]);
			}
			elseif ($form) {
				return (isset($this->post[$key][$form])?$this->post[$key][$form]:'');
			} else {
				return $this->post[$key];	
			}
		}
		return '';
	}
	
	protected function data($key) {
		if (isset($this->data[$key])) {
			return $this->data[$key];
		}
		return '';
	}
	
	protected function log($id = 0, $new = array(), $old = array()) {
		if (!USE_LOG) return;	
		if (!$id || !$this->affected) return false;
		if (!$this->updated) return false;
		if (!$old) $old = $this->rs;
		if (!$old) $old = array();
		if (!$new) $new = $this->data;
		$arr = array();
		if ($new && isset($new['edited'])) unset($new['edited']);
		switch ($this->updated) {
			case Site::ACTION_INSERT:
				$arr = array('new'=>$new);
			break;
			case Site::ACTION_UPDATE:
				$arr = array('old'=>$old,'new'=>$new);
			break;
			case Site::ACTION_DELETE:
				$arr = array('old'=>$old);
			break;
			case Site::ACTION_ERROR:
				$arr = $new;
			break;
		}
		Site::log($this->updated,$this->msg_text, DB::prefix($this->table), $id, $arr, $this->tpl);
	}
	
	protected function nav($total = 0) {
		$url = '?'.URL::get().(post(self::KEY_FIND)?AMP.self::KEY_FIND.'='.post(self::KEY_FIND):'');
		if ($this->date_from) {
			$url .= '&'.self::KEY_DATE_FROM.'='.$this->date_from;
		}
		if ($this->date_to) {
			$url .= '&'.self::KEY_DATE_TO.'='.$this->date_to;
		}	
		
		$this->nav = Pager::get(array(
			'total'		=> ($total ? $total : $this->total),
			'limit'		=> $this->limit,
			'url'		=> $url,
			'page_key'	=> self::KEY_PAGE,
			'limit_key'	=> self::KEY_LIMIT,
			'buttons'	=> ADMIN_NAV_BUTTONS
		));
	}
	
	// TODO:: find tpl file
	
	// todo: remove
	protected function tab($name) {
		$ex = explode('_',$name);
		$this->tab = $ex[1];
		if (is_file(FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.$name.'_tab.php')) {
			include FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.$name.'_tab.php';	
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'_tab.php')) {
			include FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'_tab.php';	
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.$name.'_tab.php')) {
			include FTP_DIR_ROOT.'tpls/admin/'.$name.'_tab.php';	
		} 
		else {
			echo 'File "'.FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'_tab.php" does not exist';
		}
	}
	public function win($name) {
		if (is_file(FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.$name.'_window.php')) {
			include FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.$name.'_window.php';
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'_window.php')) {
			include FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'_window.php';	
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.$name.'_window.php')) {
			include FTP_DIR_ROOT.'tpls/admin/'.$name.'_window.php';	
		}
		else {
			$this->inc('window_top');
			echo 'File "'.FTP_DIR_ROOT.'tpls/admin/'.$name.'_window.php" does not exist';
			$this->inc('window_bottom');
		}
	}
	
	private $no_inc = array();
	protected function no_inc($arr) {
		$this->no_inc = $arr;	
	}
	protected function inc($name, $vars = array()) {
		if ($this->no_inc && in_array($name,$this->no_inc)) return false;
		$ext = ext($name);
		if (in_array($ext,array('js','php','html','txt'))) $e = ''; else $e = '.php';
		if ($vars) extract($vars);
		if (is_file(FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/inc/'.$name.$e)) {
			include FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/inc/'.$name.$e;	
		}
		if (is_file(FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/inc/'.$name.$e)) {
			include FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/inc/'.$name.$e;	
		}
		if (is_file(FTP_DIR_ROOT.'tpls/admin/inc/'.$name.$e)) {
			include FTP_DIR_ROOT.'tpls/admin/inc/'.$name.$e;	
		} 
		else {
			echo 'File "'.FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/inc/'.$name.$c.'" does not exist';
		}
	}
	
	public function errors($err = false) {
		if (!$err) return;
		$this->msg_text = lang('$Sorry, you\'ve made few errors...').'<div class="a-errorlist"><div>'.join('</div><div>',$err).'</div></div>';
		$this->msg_error = $this->msg_text;
		$this->affected = 0;
		$this->msg_focus = 'a-w-'.key($err).'_'.$this->name_id;
		$this->msg_type = 'warning';
		$this->msg_close = false;
		$this->msg_reload = false;
		$this->msg_ok = false;
		$this->msg_delay = 2000 + 200 * count($err);
		ob_clean();
		Site::header('Content-Type: text/json; charset=utf-8'); 
		echo json($this->json_ret());
		exit;
	}
	protected function json_ret() {
		if ($this->json_ret || $this->json_ret===NULL) return $this->json_ret;
		if ($this->msg_close===true) $this->msg_close = $this->name_id;
		if ($this->msg_type && !is_file(FTP_DIR_ROOT.'tpls/img/icons/'.$this->msg_type.'_48.png')) {
			$this->msg_type = 'stop';	
		}
		$ret = array(
			'id'		=> $this->id,
			'affected'	=> $this->affected,
			'text'		=> $this->msg_text.($this->msg_text2?'<br /><div class="a-sub">'.$this->msg_text2.'</div>':''),
			'error'		=> $this->msg_error,
			'type'		=> $this->msg_type,
			'ok'		=> $this->msg_ok,
			'close'		=> $this->msg_close,
			'reload'	=> $this->msg_reload,
			'redirect'	=> $this->msg_redirect,
			'action'	=> $this->action,
			'delay'		=> $this->msg_delay,
			'focus'		=> $this->msg_focus,
			'js'		=> $this->msg_js,
			'url'		=> $this->msg_url,
			'response'	=> $this->msg_response,
			'referer'	=> '/?'.$this->referer
		);
		if ($this->json_push) $ret = array_merge($ret, $this->json_push);
		$this->json_ret = array();
		return $ret;
	}
	protected static function is($type, $str) {
		switch ($type) {
			case 'new':
				return substr($str,0,4)=='#'.self::KEY_NEW;
			break;
		}
	}
	
	protected function parse($value, $col = '', $module = false, $from_arr = false) {
		if (is_array($value) && !$from_arr) {
			$ret = array();
			foreach ($value as $k => $v) {
				if ($k=='checkboxes' || $k=='files') {
					$ret[$k] = $v;
					continue;
				}
				$ret[$k] = $this->parse($v, $k, $module, true);
			}
			if ($module) {
				if ($this->id) {
					if (isset($this->rs['userid']) && $this->rs['userid']==$this->Index->Session->UserID) $ret['is_admin'] = 1;
				}
				else $ret['is_admin'] = 1;
			}
			if (isset($ret['options']) && is_array($ret['options'])) $ret['options'] = serialize($ret['options']);
			if (isset($ret['dob']) && is_array($ret['dob'])) {
				$ret['dob'] = sprintf('%04d-%02d-%02d %02d:%02d:00',$ret['dob']['Year'],$ret['dob']['Month'],$ret['dob']['Day'],$ret['dob']['Hour'],$ret['dob']['Hour']);	
			}
			if (isset($ret['catrefs']) && is_array($ret['catrefs'])) $ret['catrefs'] = ','.join(',',$ret['catrefs']).',';
			return $ret;
		}
		switch ($col) {
			case 'title';
			
			break;
			case 'body':
			case 'descr':
				if (substr($value,0,7)=='http://') break;
				if (!Parser::isHTML($value)) break;
				$value = Parser::parse('code_and_smilies', $value);
			break;
			case 'code_bb':
				$value = Parser::parse('code_bb', $value);
			break;
		}
		return $value;
	}
	
	protected function set_msg($insert, $update, $delete = '', $error = '') {
		$this->arr_msg = array($insert, $update, $delete, $error);
	}
	
	protected function get_msg($i) {
		return (isset($this->arr_msg[$i-1]) ? $this->arr_msg[$i-1] : false);	
	}
	
	protected function select_msg() {
		$nr = get(self::KEY_NO_RELOAD);
		if ($nr) {
			$nrl = $this->langs[$nr][0];
			$this->msg_reload = false;
		}
		if (!$this->updated) {
			if (!$this->msg_text) {
				$this->msg_text = ($this->get_msg(4) ? $this->get_msg(4) : lang('$You are not authorized to commit any changes'));
				$this->msg_delay = 3500;
				$this->msg_type = 'warning';
			}
		}
		elseif ($this->updated==Site::ACTION_UPDATE) {
			$this->msg_type = 'tick';
			
			if (!$this->affected) {
				$this->msg_reload = false;
				$this->msg_text = false;
			} else {
				if ($this->get_msg(2)) {
					$this->msg_text = $this->get_msg(2);	
				}
				elseif ($this->rs && isset($this->rs['title'])) {
					$this->msg_text = lang('$Entry %1 was updated',$this->rs['title']);
				}
				else {
					$this->msg_text = lang('$Your entry details were updated');
				}
				if ($nr) {
					$this->msg_text2 = lang('$Selecting %1 language','<img src="'.FTP_EXT.'tpls/img/flags/24/'.$nr.'.png" alt="'.$nrl.'" style="position:relative;top:4px;" />'.$nrl).'...';	
				}
				if (!EDIT_NOTIFY) {
					$this->msg_text = '';
					$this->msg_text2 = '';
				}
			}
		}
		elseif ($this->updated==Site::ACTION_INSERT) {
			$this->msg_close = true;
			$this->msg_type = 'tick';
			if ($this->tab) {
				//$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->name.'&'.self::KEY_LOAD.'&'.self::KEY_TAB.'='.$this->tab.'\',false,\''.$this->tab.'\');';
			} elseif (!post('force_no_open')) {
				$this->msg_js = 'S.A.W.go(\'?'.URL_KEY_ADMIN.'='.$this->name.'&id='.$this->id.'\');';
			}
			if ($this->get_msg(2)) {
				$this->msg_text = $this->get_msg(1);	
			}
			elseif ($this->data && isset($this->data['title'])) {
				$this->msg_text = lang('$New entry %1 was added',$this->data['title']);
			}
			else {
				$this->msg_text = lang('$New entry was added');
			}
			if (!EDIT_NOTIFY) {
				$this->msg_text = '';
				$this->msg_text2 = '';
			}
		}
		
	}
	

	protected function run_hook($_hook, $type = 0, $row = array()) {
		Site::hook($_hook,$type,$row,true);
	}
	
	public function convertMoney($amount = NULL, $from = NULL, $to = DEFAULT_CURRENCY) {
		if (!$from) return $amount;
		if (!$to) $to = $this->currency;
		if ($this->currencies[$to][0]<>0) {
			$ret = $amount * $this->currencies[$to][0] / $this->currencies[$from][0];
		}
		else $ret = $amount;
		return $ret;
	}
	
	public function WhereByCurrency($a,$b,$currency,$price_col = 'price', $cur_col = 'currency') {
		$sql = '';
		$currency = strtoupper($currency);
		$currencies = $this->currencies;
		if ((!$a&&!$b) || !$currencies[$currency]) return '';
		if (!$currency) $currency = DEFAULT_CURRENCY;
		if ($a) {
			$sql .= ' AND (CASE';
			foreach ($currencies as $cur => $x) {
				if (!$x[0]) continue;
				if ($cur!=$currency) {
					$sql .= ' WHEN '.$cur_col.'=\''.$cur.'\' THEN ('.$price_col.' * '.($currencies[$currency][0] / $x[0]).')';
				}
			}
			$sql .= ' ELSE '.$price_col.' END) >= '.(float)$a;
		}
		
		if ($b) {
			$sql .= ' AND (CASE';
			foreach ($currencies as $cur => $x) {
				if (!$x[0]) continue;
				if ($cur!=$currency) {
					$sql .= ' WHEN '.$cur_col.'=\''.$cur.'\' THEN ('.$price_col.' * '.($currencies[$currency][0] / $x[0]).')';
				}
			}
			$sql .= ' ELSE '.$price_col.' END) <= '.(float)$b;
		}
		return $sql;
	}
	
	public function selectByCurrency($currency,$price_col = 'price', $cur_col = 'currency') {
		$sql = '';
		$currency = strtoupper($currency);
		$currencies = $this->currencies;
		if (!$currencies) return $price_col;
		if (!$currency) $currency = DEFAULT_CURRENCY;

		$sql .= '(CASE';
		$i = 0;
		foreach ($currencies as $cur => $x) {
			if (!$x[0]) continue;
			if ($cur!=$currency) {
				$i++;
				$sql .= ' WHEN '.$cur_col.'=\''.$cur.'\' THEN ('.$price_col.' * '.($currencies[$currency][0] / $x[0]).')';
			}
		}
		$sql .= ' ELSE '.$price_col.' END)';
		if (!$i) return $price_col;
		return $sql;
	}	
	
	protected function count_money($userid, $account, $currency) {
		$s = $this->selectByCurrency($currency);
		$sum = DB::one('SELECT SUM('.$s.') FROM '.DB_PREFIX.'users_transfers WHERE userid='.(int)$userid); // .' AND account='.e($account)
		DB::run('UPDATE '.DB_PREFIX.'users_profile SET money='.e($sum).' WHERE setid='.(int)$userid);
		return $sum;
	}
	
	private function setChange() {
		if ($this->change) {
			foreach ($this->change as $i => $a) {
				if ($a['array_key']) {
					foreach ($a['columns'] as $col) {
						Session::setChange($a['table'], $col, $this->id, $this->data[$a['array_key']][$col], $this->rs[$a['array_key']][$col], false, $a['id_col']);
					}
				} else {
					foreach ($a['columns'] as $col) {
						Session::setChange($a['table'], $col, $this->id, $this->data[$col], $this->rs[$col], false, $a['id_col']);
					}
				}
			}
		}
		
		if (!$this->columns_set_change) return false;
		foreach ($this->columns_set_change as $col) {
			Session::setChange($this->table, $col, $this->id, $this->data[$col], $this->rs[$col], false);
		}
	}
	
	private function unique_name($name, $i=0) {
		$_name = $name.($i?'.'.$i:'');
		DB::noerror();
		if (DB::one('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE name='.e($_name))) {
			return $this->unique_name($name,$i+=1);
		}
		return $_name;
	}

	protected function fixEditor($v) {
		$v = preg_replace('/ abp="([0-9]+)"/','',$v);
		return $v;
	}
	
	protected function global_action($act = false, $arg1 = false, $arg2 = false) {
		$this->idlang = ($this->idcol=='id'?'id='.(int)$this->id:'rid='.$this->id.' AND lang=\''.$this->lang.'\'');
		if (!$act) $act = $this->action;
		switch ($act) {
			case 'translate':
				if (!USE_TRANSLATE || !$this->data['translate']) return false;
				$translated = array();
				foreach ($this->options['translate_cols'] as $col) {
					if (isset($this->rs[$col]) && $this->rs[$col]) {
						$translated[$col] = $this->translate($this->rs[$col], $this->data['translate'], $this->rs['lang']);
					}
				}
				$this->msg_close = false;
				$this->msg_js = '';
				foreach ($this->options['translate_cols'] as $col) {
					if (isset($translated[$col]) && $translated[$col]) {
						$this->msg_js .= '$(\'#a-w-'.$col.'_'.$this->name_id.'\').val(\''.strjs($translated[$col]).'\');';
					}
				}
				$this->msg_js .= '$(\'#lang_translate_'.$this->name_id.'\').val(\'\');';
				$ex = explode('_', $this->name);
				if ($this->name=='entries') $ex[1] = lang('$entry');
				else $ex[1] = $this->name;
				$dialog = array(
					'type'	=> 'globe',
					'text'	=> lang('$Text was translated from %1 to %2. Save %3 now to commit changes', $this->langs[$this->data['translate']][0], $this->langs[$this->rs['lang']][0], $ex[1])
				);
				$this->msg_js .= 'var data='.json($dialog).';S.A.W.dialog(data);';
				/*
				$id = DB::one('SELECT id FROM '.$this->prefix.$this->table.' WHERE rid='.$this->rs['rid'].' AND lang='.e($this->data['translate']));
				$this->msg_js .= 'S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$this->name.'&id='.$id.'\');';
				*/
				$this->msg_close = false;
				$this->msg_reload = false;
				return true;
			break;
			case 'edit':
			
				$col = post('column');
				$value = post('value');
				if (!$this->table || !$this->id || !$col || !$value) break;
				$value = $this->parse($value, $col);
				$value = $this->fixEditor($value);				
				$columns = DB::columns($this->table);
				
				
				/*
				if ($this->rs['rid']==$this-> && $this->lang==$this->rs['lang']) {
					$w = ' WHERE rid='.$this->id.' AND lang='.e($this->lang);
				} else {
					$w = ' WHERE id='.$this->id;
				}
				*/
				$w = ' WHERE id='.$this->id;
				$this->rs = DB::row('SELECT * FROM '.$this->prefix.$this->table.$w);
				/*
				if (in_array('rid', $columns)) {
					if (post(self::KEY_ALL)) {
						$w = ' WHERE rid='.$this->id.' OR id='.$this->id;
					} else {
						$w = ' WHERE rid='.$this->id.' AND lang='.e($this->lang);
					}
				}
				else {
					$w = ' WHERE id='.$this->id;
				}
				*/
				
				$allow = Allow()->admin('quick_edit', 'save', $this->rs, array($col=>$value), $this->table, $this->id);
				if ($allow) {
					die(json(array(
						'error'	=> true,
						'text'	=> $allow['text'],
						'type'	=> $allow['type'],
						'delay'	=> 2000
					)));
				}
				if (in_array($this->table, array('tree','menu','forum_cats','pages_files','entries_files','content')) && post('all')) {
					$ex = explode('_', $col);
					foreach ($this->langs as $l => $a) {
						$sql = 'UPDATE '.$this->prefix.$this->table.' SET `'.$ex[0].'_'.$l.'`='.e($value).$w;	
						DB::run($sql);
					}
				} else {
					$sql = 'UPDATE '.$this->prefix.$this->table.' SET `'.$col.'`='.e($value).$w;
					DB::run($sql);
				}
				$this->affected = DB::affected();
				$this->updated = Site::ACTION_UPDATE;
				if ($this->affected) {
					$this->msg_text = 'Entry ID: '.$this->rs['id'].' ('.$col.') was quickly updated';
					$new = $this->rs;
					$new[$col] = $value;
					$this->log($this->id, $new);
				}
			break;
			case 'validate':
				$msg = $this->validate();
				if ($msg) {
					$this->error = true;
					$this->errors($msg);
				}
				if ($this->id) {
					$this->rs = DB::row('SELECT * FROM '.DB::prefix($this->table).' WHERE '.$this->idlang);
					if (!$this->rs && $this->idcol=='rid') $this->rs = DB::row('SELECT * FROM '.DB::prefix($this->table).' WHERE rid='.$this->id);
					if (!$this->rs) $this->id = 0;
					if (isset($this->rs['setid'])) $this->conid = $this->rs['setid'];
				}
				elseif (!$this->conid) {
					break;
				} else {
					$this->id = 0;
					$this->data['added'] = $this->time;
					if ($this->conid) $this->data['setid'] = $this->conid;
				}
			break;
			
			case 'save':
				if ($this->idcol=='rid') {
					return $this->global_action('save_lang',$arg1,$arg2);	
				}
				if ($this->error) break;
				if (!$this->data) {
					$this->msg_text = 'Data is empty';
					$this->msg_type = 'error';
					return false;
				}
				if ($this->global_action('translate')) break;
				foreach ($this->checkboxes as $c) {
					$this->data[$c] = (isset($this->data[$c]) ? $this->data[$c] : '');
				}
				if (isset($this->data['body']) && (!$this->data['body'] || $this->data['body']=='undefined' || $this->data['body']=='null')) {
					unset($this->data['body']);
				}

				if (!$this->rs && $this->id) $this->rs = DB::select($this->table, $this->id);
				$this->data = $this->parse($this->data, false, true);
				
				if (!@$this->rs['name'] && $this->data['title']) {
					$cols = DB::columns($this->table);
					if (in_array('name', $cols)) $this->data['name'] = $this->unique_name(substr(Parser::name($this->data['title']),0,170));
				}
				
				$this->toDB();
				foreach ($this->dates as $col) {
					if (isset($this->data[$col])) $this->data[$col] = $this->toTimestamp($this->data[$col]);
				}
				
				if ($this->id) {
					$this->setChange();
					if ($this->isEdit()) break;
					DB::update($this->table,$this->data,$this->id);
					$this->affected = DB::affected();
					$this->updated = Site::ACTION_UPDATE;
				} else {
					if (!isset($this->data['active'])) $this->data['active'] = 1;
					if (!$this->data['userid']) $this->data['userid'] = $this->UserID;
					$this->data['added'] = $this->time;
					//$this->data['statused'] = $this->time;
					DB::insert($this->table,$this->data);
					$this->updated = Site::ACTION_INSERT;
					$this->affected = DB::affected();
					$this->id = DB::id();
				}
				$this->global_action('reorder');
				$this->toFix($this->table,'id',$this->id);
				$this->toUpdate();
				$this->select_msg();
				$new = DB::select($this->table, $this->id);
				if ($this->rs) $this->log($this->id, $new);
			break;
			case 'copy':
				$this->global_action('validate');
				unset($this->rs['id']);
				$this->rs['added'] = time();
				$this->rs['userid'] = $this->UserID;
				DB::insert($this->table,$this->rs);
				$this->rs['id'] = DB::id();
				$this->affected = true;
				$this->updated = Site::ACTION_INSERT;
				$this->log($this->rs['id'], $this->rs);
				$this->msg_js = 'S.A.L.get(S.A.L.cur_url,false,S.A.L.tab_name);S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$this->name.'&id='.$this->rs['id'].'\');';
			break;
			case 'save_lang':
				if ($this->error) break;
				$this->allow('module','save',$this->rs,$this->data,$this->table,$this->id);
				if (!$this->data) {
					$this->msg_text = 'Data is empty';
					$this->msg_type = 'error';
					return false;
				}
				if ($this->global_action('translate')) break;
				foreach ($this->checkboxes as $c) {
					$this->data[$c] = (isset($this->data[$c]) ? $this->data[$c] : '');
				}
				if (isset($this->data['body']) && (!$this->data['body'] || $this->data['body']=='undefined' || $this->data['body']=='null')) {
					unset($this->data['body']);
				}
				$this->data = $this->parse($this->data, false, true);
				$this->toDB();
				foreach ($this->dates as $col) {
					if (isset($this->data[$col])) $this->data[$col] = $this->toTimestamp($this->data[$col]);
				}
				$lang_all = post(self::KEY_LANG_ALL);
				if ($this->id) {
					if ($this->isEdit()) break;
					$this->setChange();
					if ($lang_all) {
						$arr_rs = array();
						foreach ($this->langs as $l => $x) {
							if ($l==$this->lang) continue;
							$arr_rs[$l] = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND lang=\''.$l.'\'');	
						}
					}
					DB::update($this->table,$this->data,$this->id,'rid',(!$lang_all?' AND lang=\''.$this->lang.'\'':''));
					$this->affected = DB::affected();
					$id = DB::row('SELECT id FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND lang=\''.$this->lang.'\'','id');
					if (!$lang_all) {
						$data_all = array();
						foreach ($this->columns_all_lang as $col) {
							if (array_key_exists($col,$this->data)) {
								$data_all[$col] = $this->data[$col];
							}
						}
						if ($data_all) {
							DB::update($this->table,$data_all,$this->id,'rid',' AND lang!=\''.$this->lang.'\'');
						}
					}
					$this->updated = Site::ACTION_UPDATE;
				} else {
					$this->data['active'] = 1;
					$this->data['userid'] = $this->UserID;
					$this->data['added'] = $this->time;
					$ids = array();
					$_data = $this->data;
					foreach ($this->langs as $l => $a) {
						if (USE_TRANSLATE && USE_AUTO_TRANSLATE) {
							foreach ($this->options['translate_cols'] as $col) {
								if (isset($_data[$col]) && $_data[$col]) {
									$translated = $this->translate($_data[$col], $_data['lang'], $l);
									if ($translated) $this->data[$col] = $translated;
								}							
							}
						}
						$this->data['lang'] = $l;
						DB::insert($this->table,$this->data);
						if ($this->lang==$l) {
							$id = DB::id();
							$this->id = $id;
							$ids[] = $this->id;
						} else {
							$ids[] = DB::id();
						}
					}
					if ($ids) {
						DB::run('UPDATE '.$this->prefix.$this->table.' SET rid='.$this->id.' WHERE id IN ('.join(', ',$ids).')');
						$this->updated = Site::ACTION_INSERT;
						$this->affected = DB::affected();
					}
				}
				$this->global_action('reorder');
				$this->select_msg();
				if ($id) {
					$new = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$id);
					$this->log($id, $new);
					if ($lang_all && $arr_rs) {
						foreach ($arr_rs as $l => $rs) {
							$new['lang'] = $rs['lang'];
							$new['id'] = $rs['id'];
							$this->log($rs['id'], $new, $rs);
						}
					}
				}
			break;
			case 'save_content':
				if ($this->error) break;
				$this->allow('module','save',$this->rs,$this->data,$this->table,$this->id);
				if (!$this->data) {
					$this->msg_text = 'Data is empty';
					$this->msg_type = 'error';
					return false;
				}
				if ($this->global_action('translate')) break;
				foreach ($this->checkboxes as $c) {
					$this->data[$c] = (isset($this->data[$c]) ? $this->data[$c] : '');
				}
				if (isset($this->data['body']) && (!$this->data['body'] || $this->data['body']=='undefined' || $this->data['body']=='null')) {
					unset($this->data['body']);
				}
				$this->data = $this->parse($this->data, false, true);
				$this->toDB();
				foreach ($this->dates as $col) {
					if (isset($this->data[$col])) $this->data[$col] = $this->toTimestamp($this->data[$col]);
				}
				$lang_all = post(self::KEY_LANG_ALL);
				if ($this->id) {
					if ($this->isEdit()) break;
					$this->setChange();
					unset($this->data['setid']);
					if ($lang_all) {
						$arr_rs = array();
						foreach ($this->langs as $l => $x) {
							if ($l==$this->lang) continue;
							$arr_rs[$l] = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND lang=\''.$l.'\'');	
						}
					}
					DB::update($this->table,$this->data,$this->id,'rid',(!$lang_all?' AND lang=\''.$this->lang.'\'':''));
					$this->affected = DB::affected();
					$id = DB::row('SELECT id FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND lang=\''.$this->lang.'\'','id');
					if (!$lang_all) {
						$data_all = array();
						foreach ($this->columns_all_lang as $col) {
							if (array_key_exists($col,$this->data)) {
								$data_all[$col] = $this->data[$col];
							}
						}
						if ($data_all) {
							DB::update($this->table,$data_all,$this->id,'rid',' AND lang!=\''.$this->lang.'\'');
						}
					}
					$this->updated = Site::ACTION_UPDATE;
					$_data = $this->data;
				} else {
					if (!$this->conid && $this->table!='entries') {
						$this->msg_text = 'Fatal error $_GET['.self::KEY_CONID.'] is missing';
						$this->msg_type = 'fatal';
						$this->msg_delay = 5000;
						$this->msg_reload = false;
						return false;	
					}
					if ($this->table!='entries') {
						$this->data['setid'] = $this->conid;
					}
					$this->data['active'] = 1;
					$this->data['userid'] = $this->UserID;
					$this->data['added'] = $this->time;
					$ids = array();
					$_data = $this->data;
					foreach ($this->langs as $l => $a) {
						if (USE_TRANSLATE && USE_AUTO_TRANSLATE) {
							foreach ($this->options['translate_cols'] as $col) {
								if (isset($_data[$col]) && $_data[$col]) {
									$translated = $this->translate($_data[$col], $_data['lang'], $l);
									if ($translated) $this->data[$col] = $translated;
								}							
							}
						}
						$this->data['lang'] = $l;
						DB::insert($this->table,$this->data);
						if ($this->lang==$l) {
							$id = DB::id();
							$this->id = $id;
							$ids[] = $this->id;
						} else {
							$ids[] = DB::id();
						}
					}
					if ($ids) {
						DB::run('UPDATE '.$this->prefix.$this->table.' SET rid='.$this->id.' WHERE id IN ('.join(', ',$ids).')');
						$this->updated = Site::ACTION_INSERT;
						$this->affected = DB::affected();
					}
					if (!$this->affected) $this->affected = 1;
					$this->msg_reload = true;
				}
				$this->data = array();
				$this->toUpdate();
				if ($this->data) {
					$this->data['edited'] = $this->time;
				//	$this->data['userid'] = $this->UserID;
					DB::update($this->table,$this->data,$this->id,'rid',' AND lang!=\''.$this->lang.'\'');
				}
				elseif ($this->affected) {
					$data = array(
						'edited'	=> $this->time,
				//		'userid'	=> $this->Session->UserID
					);
					DB::update($this->table,$data,$this->id,'rid',' AND lang=\''.$this->lang.'\'');
					if ($this->table!='entries') $this->global_action('reorder_content');
				}
				
				$this->data = $_data;
				$this->global_action('reorder');
				$this->select_msg();
				if ($id) {
					$new = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$id);
					$this->log($id, $new);
					if ($lang_all && $arr_rs) {
						foreach ($arr_rs as $l => $rs) {
							$new['lang'] = $rs['lang'];
							$new['id'] = $rs['id'];
							$this->log($rs['id'], $new, $rs);
						}
					}
				}
			break;
			case 'msg':
				if (USE_TRANSLATE && $this->data['translate']) break;
				$this->select_msg();
			break;
			case 'reorder':
				if (!$this->affected) return false;
				if ($this->table=='pages') {
					$ex = $ex_old = array();
					if ($this->data) $ex = array_unique(array($this->data['treeid']) + explode(',',trim($this->data['treeids'],',')));
					if ($this->rs) $ex_old = array_unique(array($this->rs['treeid']) + explode(',',trim($this->rs['treeids'],',')));
					if ($this->updated==Site::ACTION_INSERT && $ex) {
						DB::run('UPDATE '.$this->prefix.'tree SET cnt=cnt+1 WHERE id IN ('.join(',',$ex).')');
					}
					elseif ($this->updated==Site::ACTION_UPDATE) {
						if ($ex_old) DB::run('UPDATE '.$this->prefix.'tree SET cnt=cnt-1 WHERE id IN ('.join(',',$ex_old).') AND cnt>0');
						if ($ex) DB::run('UPDATE '.$this->prefix.'tree SET cnt=cnt+1 WHERE id IN ('.join(',',$ex).')');
					}
					elseif ($this->updated==Site::ACTION_DELETE) {
						if ($ex_old) DB::run('UPDATE '.$this->prefix.'tree SET cnt=cnt-1 WHERE id IN ('.join(',',$ex_old).') AND cnt>0');
					}
				}
				if ($this->table=='entries') {
					if ($this->updated==Site::ACTION_INSERT && $this->data['menuid']) {
						DB::run('UPDATE '.$this->prefix.'menu SET cnt3=cnt3+1 WHERE id='.$this->data['menuid']);
					}
					elseif ($this->updated==Site::ACTION_UPDATE && $this->rs['menuid']!=$this->data['menuid']) {
						if ($this->rs['menuid']) DB::run('UPDATE '.$this->prefix.'menu SET cnt3=cnt3-1 WHERE id='.$this->rs['menuid'].' AND cnt3>0');
						if ($this->data['menuid']) DB::run('UPDATE '.$this->prefix.'menu SET cnt3=cnt3+1 WHERE id='.$this->data['menuid']);
					}
					elseif ($this->updated==Site::ACTION_DELETE) {
						if ($this->rs['menuid']) DB::run('UPDATE '.$this->prefix.'menu SET cnt3=cnt3-1 WHERE id='.$this->rs['menuid'].' AND cnt3>0');
					}
				}
			break;
			case 'reorder_content':
				if (!$this->affected) return false;
				$unions = array();
				if ($arg1) $setid = $arg1; else $setid = $this->conid;
				$tables = DB::tables();
				foreach ($this->modules as $m => $arr) {
					if (!in_array('content_'.$m, $tables)) continue;
					$unions[] = '(SELECT '.e($m).' AS module, \''.$arr['id'].'\' AS modid, rid, sort FROM '.$this->prefix.'content_'.$m.' WHERE setid='.$setid.' AND lang=\''.$this->lang.'\' AND active!=2)';
				}
				if (!$unions) break;
				$sql = ''.join(' UNION ',$unions).' ORDER BY sort';
				$qry = DB::qry($sql,0,0);
				$data = array();
				$i = 0;
				while ($rs = DB::fetch($qry)) {
					$data[] = $rs['modid'].':'.$rs['rid'];
					$i++;
				}
				DB::free($qry);
				$menuid = DB::row('SELECT menuid FROM '.$this->prefix.'content WHERE id='.$setid,'menuid');
				if (!$i) {					
					if (NO_DELETE) {
						DB::run('UPDATE '.$this->prefix.'content SET inserts=\'\', cnt=0, active=2 WHERE id='.$setid);
					} else {
						DB::run('DELETE FROM '.$this->prefix.'content WHERE id='.$setid);
					}
				}
				else {
					DB::run('UPDATE '.$this->prefix.'content SET inserts=\''.join(',',$data).'\', cnt='.$i.' WHERE id='.$setid);
				}
				if ($menuid) {
					$totals = DB::row('SELECT SUM(cnt) AS entries, COUNT(1) AS contents FROM '.$this->prefix.'content WHERE menuid='.$menuid.' AND active!=2 AND inserts!=\'\'');
					DB::run('UPDATE '.$this->prefix.'menu SET cnt='.(int)$totals['contents'].', cnt2='.(int)$totals['entries'].' WHERE id='.$menuid);
				}
			break;
			case 'reorder_content_all':
				$qry = DB::qry('SELECT id FROM '.$this->prefix.'content ORDER BY id',0,0);
				while ($rs = DB::fetch($qry)) {
					$this->global_action('reorder_content',$rs['id']);	
				}
				DB::free($qry);
			break;
			case 'save_main_photo_multi':
				if ($this->error) return false;
				if (!$this->id) return false;
				if ($main_photo = $this->moveFiles()) {
					$data = array();
					$data['main_photo'] = ($this->has_files ? $main_photo[0]['file'] : $main_photo);
					if ($this->idcol=='rid') {
						DB::update($this->table,$data,$this->id,'rid',' AND lang=\''.$this->lang.'\' AND main_photo=\'\'');
					} else {
						DB::update($this->table,$data,$this->id);	
					}
				}
			break;
			case 'save_gallery':
				if ($this->error) {
					return false;
				}
				if (!$this->id || !@$this->data['files']) {
					return false;
				}
				$sort = DB::one('SELECT MAX(sort) AS s FROM '.$this->prefix.$this->table.'_files WHERE setid='.$this->id);
				$new_files = $this->moveFiles();
				$sort = $a = 0;
				$files = array();
				$first = '';
				if ($this->has_global_files) {
					$dir = $this->ftp_dir_upload.$this->table.'/'.$this->id.'/';
				} else {
					$dir = $this->ftp_dir_files.$this->table.'/'.$this->id.'/';
				}
				foreach ($this->data['files'] as $i => $a) {
					$file = $a['file'];
					$files[] = $file;
					if ($a['id']) {
						$data = array(
							'title_'.$this->lang	=> $a['title'],
							'descr_'.$this->lang	=> $a['descr']
						);
						DB::update($this->table.'_files',$data,$a['id']);
					}
					elseif (in_array($a['file'], $new_files)) {
						$sort++;
						$media = File::getMedia($file);
						$width = $height = 0;
						$mime = File::arrMime(ext($file));
						
						$file_path = $dir.'th1/'.$file;
						if (!$first && File::isPicture($file)) $first = $file;
						$size = filesize($file_path);
						if ($media=='image' || $media=='flash') {
							list ($width, $height) = @getimagesize($file_path);
						}
						$data = array(
							'setid'	=> $this->id,
							'file'	=> $file,
							'width'	=> $width,
							'height'=> $height,
							'media'	=> $media,
							'mime'	=> $mime,
							'size'	=> $size,
							'added'	=> $this->time,
							'userid'=> $this->Index->Session->UserID,
							'active'=> 1,
							'sort'	=> $sort
						);
						foreach ($this->langs as $l => $x) {
							$data['title_'.$l] = $a['title'];
							$data['descr_'.$l] = $a['descr'];
						}
						DB::insert($this->table.'_files',$data);
					}
				}
				$dh = opendir($dir.'th1/');
				while (($file = readdir($dh))!==false) {
					if ($file=='.' || $file=='..' || in_array($file, $files)) continue;
					$sort++;
					$media = File::getMedia($file);
					$width = $height = 0;
					$mime = File::arrMime(ext($file));
					$file_path = $dir.'th1/'.$file;
					$size = filesize($file_path);
					if ($media=='image' || $media=='flash') {
						list ($width, $height) = @getimagesize($file_path);
					}
					if (!$first && File::isPicture($file)) $first = $file;
					$data = array(
						'setid'	=> $this->id,
						'file'	=> $file,
						'width'	=> $width,
						'height'=> $height,
						'media'	=> $media,
						'mime'	=> $mime,
						'size'	=> $size,
						'added'	=> $this->time,
						'userid'=> $this->Index->Session->UserID,
						'active'=> 1,
						'sort'	=> $sort
					);
					DB::insert($this->table.'_files',$data);
				}
				closedir($dh);
				if ($this->updated==Site::ACTION_INSERT && $first && in_array('main_photo',DB::columns($this->table))) {
					DB::run('UPDATE '.$this->prefix.$this->table.' SET main_photo='.e($first).' WHERE rid='.$this->id.' AND main_photo=\'\'');
				}
			break;
			case 'add_content':
				if (!$this->id) break;
				$this->action('save');
				$this->msg_close = true;
				$this->msg_reload = false;
				$this->msg_text = '';
				$this->msg_js = 'S.A.W.go(\'?'.URL_KEY_ADMIN.'='.$this->name.'&'.self::KEY_CONID.'='.$this->conid.'\');';
			break;
			case 'act':
				$col = $this->idcol;
				
				if (get('file')=='true') {
					if ($this->table=='entries') {
						$table = 'entries_files';
					} else {
						$table = $this->table.'_'.$this->module.'_files';
					}
					$col = 'id';
				}
				elseif (substr($this->table,0,8)=='content_' || substr($this->table,0,5)=='grid_') {
					$table = $this->table;	
				}
				elseif ($this->module && array_key_exists($this->module, $this->modules)) {
					$table = $this->table.'_'.$this->module;
				}
				else {	
					$table = $this->table;
				}
				if (!FLAG_ALL_LANG && $col=='rid') $col = 'lang=\''.$this->lang.'\' AND rid';
				if ($table=='content') $col = 'id';
				if (!$this->id) $this->id = post('id');
				if (!is_numeric($this->id)) $this->id = e($this->id);
				
				$w = 'id='.$this->id;
				if (in_array('rid',DB::columns($this->table))) {
					$is = DB::one('SELECT 1 FROM '.DB::prefix($table).' WHERE rid='.$this->id);
					if ($is) {
						$w = 'rid='.$this->id;	
					} else {
						$rid = DB::one('SELECT rid FROM '.DB::prefix($table).' WHERE id='.$this->id);
						if ($rid) $w = 'rid='.$rid;
					}
				}
				
				$sql = 'SELECT * FROM '.DB::prefix($table).' WHERE id='.$this->id;
				$this->rs = DB::row($sql);
				$this->active = $this->rs['active'];
				$old_active = isset($this->rs['old_active']);
				if ($this->active) {
					$sql = 'UPDATE '.DB::prefix($table).' SET '.($old_active?'old_active=active, ':'').'active=0 WHERE '.$w;
					$this->data['active'] = 0;
					DB::run($sql);
					$this->msg_text = 'Entry '.$this->id.' was deactivated';
				} else {
					$this->data['active'] = ($old_active ? $this->rs['old_active'] : 1);
					$sql = 'UPDATE '.DB::prefix($table).' SET active='.($old_active?'old_active':'1').' WHERE '.$w;
					DB::run($sql);
					$this->msg_text = 'Entry '.$this->id.' activated';
				}
				$this->affected = DB::affected();
				$this->updated = Site::ACTION_ACTIVE;
				$this->toFix($table,$col,$this->id);
				$this->msg_text = false;
				$this->msg_close = false;
				$this->msg_reload = false;
			break;
			case 'sort':
				$sort = post('sort');
				if (!$sort) return false;
				$tables = DB::tables();
				$arr_sort = explode('|',$sort);
				$aff = 0;
				foreach ($arr_sort as $table_id_sort) {
					if (!$table_id_sort) continue;
					list($table, $id, $s) = explode('-',$table_id_sort);
					if (!$id || !$table || !in_array($table, $tables)) continue;
					if (!is_numeric($id)) $col = 'name';
					elseif (substr($table,0,8)=='content_') $col = 'rid';
					else $col = 'id';
					$sql = 'UPDATE `'.DB::prefix($table).'` SET `sort`='.(int)$s.' WHERE `'.$col.'`='.e($id);
					DB::run($sql);
					$aff+=DB::affected();
				}
				if ($aff) {
					$this->affected = $aff;
					$first = $arr_sort[0];
					list($table, $id, $s) = explode('-',$first);
					if (substr($table,0,8)=='content_') {
						$setid = DB::row('SELECT setid FROM '.$this->prefix.$table.' WHERE rid='.(int)$id, 'setid');
						if ($setid) $this->global_action('reorder_content',$setid); 
					}
					$this->msg_text = 'Sorted';
				} else {
					$this->msg_text = 'Failure';	
				}
				$this->msg_reload = false;
			break;
			case 'sort_images':
				$sort = post('sort');
				if (!$sort || !$this->table) {
					return false;
				}
				if ($this->has_files) {
					foreach (explode('|',trim($sort,'|')) as $b) {
						list ($id, $s) = explode('-',$b);
						if (!$id || !$s) continue;
						DB::run('UPDATE '.$this->prefix.$this->table.'_files SET sort='.(int)$s.' WHERE id='.(int)$id);
					}
					break;	
				}
				$arr_sort = explode('|',$sort);
				/*
				if ($this->id) {
					if ($this->has_global_files) {
						$dir = $this->ftp_dir_upload.$this->table.'/'.$this->id.'/';
					} else {
						$dir = $this->ftp_dir_files.$this->table.'/'.$this->id.'/';
					}
				} else {
					$dir = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/';
				}
				if (!is_dir($dir)) return false;
				*/
				$_sort = array();
				foreach ($arr_sort as $file_sort) {
					list ($cur_sort, $new_sort) = explode('-',$file_sort);
					$_sort[(int)$cur_sort] = (int)$new_sort;
				}
				list ($files, $_dir, $dir) = $this->getFiles($this->id);
				$dir = str_replace('/th1/','',$dir);
				
				$main_photo = $this->global_action('main_photo');
				$total = $cur_sort = 0;
				$ret = array();
				foreach ($files as $file) {
					$ex = explode('.',$file);
					$cur_sort++;
					$new_sort = $_sort[$cur_sort];
					if (!$new_sort || $cur_sort==$new_sort) continue;
					if (count($ex)>2 && is_numeric($ex[0])) {
						unset($ex[0]);
						$to = $new_sort.'.'.join('.',$ex);
					} else {
						$to = $new_sort.'.'.$file;	
					}
					$to = str_replace('..','.',$to);
					if ($this->id) {
						foreach ($this->image_sizes as $i => $size) {
							if (!is_file($dir.'th'.$i.'/'.$file)) continue;
							@rename($dir.'th'.$i.'/'.$file, $dir.'th'.$i.'/'.$to);
						}
					} else {
						@rename($dir.$file, $dir.$to);
					}
					if ($file==$main_photo) {
						DB::run('UPDATE '.$this->prefix.$this->table.' SET main_photo=\''.$to.'\' WHERE rid=\''.$this->id.'\' AND main_photo=\''.$file.'\'');
					}
					$ret[] = array($new_sort, $to);
					$total++;
				}
				$this->json_ret = $ret;
			break;
			case 'delete':
				$w = 'id='.$this->id;
				if (in_array('rid',DB::columns($this->table))) {
					$is = DB::one('SELECT 1 FROM '.DB::prefix($this->table).' WHERE rid='.$this->id);
					if ($is) {
						$w = 'rid='.$this->id;	
					} else {
						$rid = DB::one('SELECT rid FROM '.DB::prefix($this->table).' WHERE id='.$this->id);
						if ($rid) $w = 'rid='.$rid;
					}
				}
				$all = DB::all('SELECT * FROM '.DB::prefix($this->table).' WHERE '.$w);
				DB::run('DELETE FROM '.DB::prefix($this->table).' WHERE '.$w);
				$this->affected = DB::affected();
				if ($this->affected) {
					$this->updated = Site::ACTION_DELETE;
					$this->msg_text = lang('$Entry with ID: %1 has been deleted',$this->id);
					$this->global_action('reorder');
					foreach ($all as $rs) {
						$this->rs = $rs;
						$this->log($rs['id']);
					}
				}
				else {
					$this->msg_text = lang('$Cannot delete entry with ID: %1, it does not exist in database',$this->id);
					$this->msg_delay = 1500;
					$this->msg_type = 'warning';
				}
			break;
			case 'delete_content':
				$this->msg_text = 'ID is not defined';
				if (!$this->id) break;
				if ($this->table!='entries') {
					$setid = DB::row('SELECT setid FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND setid>0', 'setid');
				}
				$this->rs = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				$this->allow('module','delete',$this->rs,$this->data,$this->table,$this->id);
				if ($setid) {
					$menuid = DB::row('SELECT menuid FROM '.$this->prefix.'content WHERE id='.$setid,'menuid');
					if ($menuid) {
						DB::run('UPDATE '.$this->prefix.'menu SET cnt2=cnt2-1 WHERE id='.$menuid.' AND cnt2>0');
					}
				}
				elseif ($this->table!='entries') {
					break;	
				}
				/*
				if (NO_DELETE) {
					DB::run('UPDATE '.$this->prefix.$this->table.' SET active=2 WHERE rid='.$this->id);
					$this->affected = DB::affected();
				} else {
					$this->deleteFiles($this->id);
					DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id);
					$this->affected = DB::affected();
					if ($this->has_files) {
						DB::run('DELETE FROM '.$this->prefix.$this->table.'_files WHERE setid='.$this->id);
					}
				}
				*/
				
				DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id);
				$this->affected = DB::affected();
					
				if ($this->affected) {
					$this->updated = Site::ACTION_DELETE;
					$this->msg_text = lang('$Entry with ID: %1 has been deleted',$this->id);
					if (NO_DEL_CONFIRM) $this->msg_text = '';
					if ($setid) $this->global_action('reorder_content',$setid);
					$this->log($this->id);
				}
				else {
					$this->msg_text = lang('$Cannot delete entry with ID: %1, it does not exist in database',$this->id);
					$this->msg_delay = 1500;
					$this->msg_type = 'warning';
				}
			break;
			case 'upload':
				
				if (is_file(FTP_DIR_ROOT.'tpls/'.$this->template.'/classes/MyClass.php')) {
					require_once FTP_DIR_ROOT.'tpls/'.$this->template.'/classes/MyClass.php';
					$this->Index->My = new MyClass($this->Index);
					$this->Index->My->prefix = PREFIX;
					$this->Index->My->uploadify();
					if (@Factory::call('uploadify', $this->Index)->names[Site::$upload[1]]==1) {
						Factory::call('uploadify', $this->Index)->name(Site::$upload[1])->tpl(false);
						
						$ret = Factory::call('uploadify')->upload();
						return $ret;
					}
				}
				
				
			
				$filename = $_FILES['Filedata']['name'];
				if (!File::valid($filename)) {
					$this->msg_text = 'This uploading file format cannot be uploaded to server';
					$this->msg_reload = false;
					$this->msg_close = false;
					$this->msg_type = 'error';
					$this->msg_delay = 1500;
					return false;
				}
				$path = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/';
				if (!is_dir($path)) mkdir($path,0777);
				if ($arg1) $this->cleanTemp($this->Index->Session->UserID);
				$ex = explode('.',$filename);
				array_pop($ex);
				$ext = ext($filename);
				$name = join('.',$ex);
				$file = File::fixFileName($name.'.'.$ext);
				if (is_file($path.$file)) @unlink($path.$file);
				if (move_uploaded_file($_FILES['Filedata']['tmp_name'],$path.$file)) {
					if (isset(Site::$upload[3]) && Site::$upload[3]) {
						if (!is_file($path.$file)) break;
						if (is_file($path.Site::$upload[3].'.'.$ext)) @unlink($path.Site::$upload[3].'.'.$ext);
						@rename(
							$path.$file, 
							$path.Site::$upload[3].'.'.$ext
						);
						$file = Site::$upload[3].'.'.$ext;
					}
					$this->cropImage($path,$file);
					echo '1/'.urlencode($file);
				} else {
					$this->msg_text = 'Cannot upload this image: ('.$file.'). Please try again';
					$this->msg_reload = false;
					$this->msg_close = false;
					$this->msg_type = 'error';
					$this->msg_delay = 1500;
					return false;
				}
						
			break;
			case 'save_main_photo_one':
				if ($this->error) return false;
				if (!$this->id) return false;
				if ($main_photo = $this->moveFiles(false, array('jpg', 'jpe', 'jpeg', 'png', 'gif', 'bmp','tif','tiff'))) {
					$data = array();
					$data['main_photo'] = ($this->has_files ? $main_photo[0]['file'] : $main_photo);
					if (@$this->rs['main_photo']) {
						$this->deleteFile($this->rs['main_photo']);
					}
					if ($this->idcol=='rid') {
						DB::update($this->table,$data,$this->id,'rid',' AND lang=\''.$this->lang.'\' AND main_photo=\'\'');
					} else {
						DB::update($this->table,$data,$this->id);	
					}
				}
			break;
			case 'save_image':
			case 'save_image_one':
				if (!$this->id) return false;
				$ret = $this->moveFiles();
				$ret = ($this->has_files ? $ret[0]['file'] : $ret);
				if ($ret && File::isPicture($ret)) {
					$main_photo = DB::row('SELECT main_photo FROM '.$this->prefix.$this->table.' WHERE '.$this->idlang,'main_photo');
					if ($main_photo) {
						$this->deleteFile($main_photo);
					}
					DB::run('UPDATE '.$this->prefix.$this->table.' SET main_photo='.e($ret).' WHERE '.$this->idlang);
				}
				return $ret;
			break;
			case 'delete_image':
				if (!$this->id) {
					if (post('file')) {
						$files = explode('|',post('file'));
						foreach ($files as $file) {
							$ex = explode('/',$file);
							$file = $ex[count($ex)-1];
							if ($this->deleteFile($file)) {
								$this->msg_text = lang('$File %1 was deleted', $file);
							}
						}
					} else {
						$this->cleanTemp($this->Index->Session->UserID);
					}
				}
				elseif (post('file')) {
					$files = explode('|',post('file'));
					$main_photo = $this->global_action('main_photo');
					foreach ($files as $file) {
						$ex = explode('/',$file);
						$file = $ex[count($ex)-1];
						if ($this->deleteFile($file)) {
							if ($file==$main_photo) {
								DB::run('UPDATE '.DB::prefix($this->table).' SET main_photo=\'\' WHERE '.$this->idcol.'='.$this->id.' AND main_photo LIKE '.e($main_photo));
							}
							$this->msg_text = lang('$File %1 was deleted', $file);
						} else {
							$this->msg_type = 'error';
							$this->msg_text = lang('$File %1 cannot be deleted', $file);	
						}
					}
				} else {
					$file = $this->global_action('main_photo');
					if ($this->deleteFile($file)) {
						$this->msg_text = lang('$File %1 was deleted', $file);
						DB::run('UPDATE '.DB::prefix($this->table).' SET main_photo=\'\' WHERE '.$this->idcol.'='.$this->id.' AND main_photo LIKE '.e($file));
						if ($this->has_global_files) {
							$path = $this->ftp_dir_upload.$this->name.'/'.$this->id.'/';
						} else {
							$path = $this->ftp_dir_files.$this->name.'/'.$this->id.'/';
						}
						$name = $this->global_action('main_photo');
						$this->msg_response = '1'.($name?trim($path,'.').'th3/'.urlencode($name):'');
					} else {
						$this->msg_type = 'error';
						$this->msg_text = lang('$File %1 cannot be deleted', $file);
					}
				}
			break;
			case 'delete_images':
				$deleted = 0;
				foreach ($_POST['arr'] as $file) {
					if ($this->deleteFile($file)) {
						$deleted++;	
					}
				}
				$this->msg_text = lang('$%1 files were deleted', $deleted);	
			break;
			case 'set_main_photo':
				if (!$this->id || !post('file') || post('file')=='undefined') return false;
				$ex = explode('/',post('file'));
				$file = end($ex);
				if ($file && File::isPicture($file)) {
					DB::run('UPDATE '.DB::prefix($this->table).' SET main_photo='.e($file).' WHERE '.$this->idcol.'='.$this->id);
				}
				
			break;
			case 'save_image_multi':
				if (!$this->id) return false;
				$ret = $this->moveFiles($arg1);
				if (!$ret) {
					return false;
				}
				if ($arg1) {
					$data = array();
					$images = unserialize(DB::row('SELECT `'.$arg1.'` FROM `'.$this->prefix.$this->table.'` WHERE rid='.(int)$this->id.' AND lang=\''.$this->lang.'\'',$arg1));
					$i = 0;
					$files = $this->findFiles($arg1);
					foreach ($this->images as $name => $x) {
						$file = fileOnly($data[$i]['file']);
						$data[$i] = $videos[$i];
						if ($files[$i]) {
							$data[$i]['video'] = ($files[$i]['flash'] ? $files[$i]['flash'] : $files[$i]['video']);
							$data[$i]['image'] = $files[$i]['image'];
						}
						if ($images) {
							$data[$i]['title'] = $images[$i]['title'];
							$data[$i]['descr'] = $images[$i]['descr'];
						}
						$i++;
					}
					DB::run('UPDATE `'.$this->prefix.$this->table.'` SET `'.$arg1.'`='.e(serialize($data)).' WHERE rid='.(int)$this->id);
				}
				elseif ($this->has_files) {
					$main_photo = false;
					$sort = DB::one('SELECT MAX(sort) AS s FROM '.$this->prefix.$this->table.'_files WHERE setid='.$this->id);
					if (!is_array($ret)) $ret = array($ret);
					
					foreach ($ret as $file) {
						if (!$main_photo) $main_photo = $file;
						$sort++;
						$media = File::getMedia($file);
						$width = $height = 0;
						$mime = File::arrMime(ext($file));
						if ($this->has_global_files) {
							$file_path = $this->ftp_dir_upload.$this->table.'/'.$this->id.'/th1/'.$file;
						} else {
							$file_path = $this->ftp_dir_files.$this->table.'/'.$this->id.'/th1/'.$file;
						}
						$size = filesize($file_path);
						if ($media=='image' || $media=='flash') {
							list ($width, $height) = @getimagesize($file_path);
						}
						$data = array(
							'setid'	=> $this->id,
							'file'	=> $file,
							'width'	=> $width,
							'height'=> $height,
							'media'	=> $media,
							'mime'	=> $mime,
							'size'	=> $size,
							'added'	=> $this->time,
							'userid'=> $this->Index->Session->UserID,
							'active'=> 1,
							'sort'	=> $sort
						);							
						DB::insert($this->table.'_files',$data);
					}
				} else {
					$main_photo = $ret;				
				}
				if ($main_photo && File::isPicture($main_photo)) {
					if (!DB::row('SELECT main_photo FROM '.DB::prefix($this->table).' WHERE '.$this->idcol.'='.(int)$this->id.($this->idcol=='id'?'':' AND lang=\''.$this->lang.'\''),'main_photo')) {
						DB::run('UPDATE '.DB::prefix($this->table).' SET main_photo='.e($main_photo).' WHERE '.$this->idcol.'='.(int)$this->id.($this->idcol=='id'?'':' AND lang=\''.$this->lang.'\''));
					}
				}
				$this->cleanTemp($this->Index->Session->UserID);
			break;
			case 'content_printed':
				echo 'Nothing to print out..';
			break;
			case 'content_window':
				if (!in_array($this->table, DB::tables())) {
					return false;
				}
				$this->isEdit();
				if ($this->idcol=='id') {
					$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
					if ($this->tab) $this->win(preg_replace('/_tab$/','',$this->name));
					else $this->win($this->name);
					break;	
				}
				$this->Content = Factory::call('content')->init($this->classParams());
				$this->Menu = Factory::call('menu');
				if ($this->id && $this->id!=self::KEY_NEW) {
					$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.(int)$this->id.' AND lang=\''.$this->lang.'\' AND active!=2');
					if (!$this->post) {
						$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.(int)$this->id.' AND active!=2');
						$this->post['lang'] = $this->lang;
					}
				}
				if (!$this->post('id', false)) {
					if (!$this->conid) {
						$this->msg_text = 'Content ID is missing';
						return false;	
					}
				//	$this->id = 0;
					$this->post = post('data');
					$this->post['sort'] = DB::row('SELECT (MAX(sort)+1) AS s FROM '.$this->prefix.$this->table.' WHERE setid='.(int)$this->setid.' AND active!=2','s');
				} else {
					$this->post['username'] = Data::user($this->post['userid'], 'login');
					if (!$this->post['username']) $this->post['username'] = 'unknown user';
					else $this->post['username'] = '<a href="javascript:;" onclick="S.A.W.open(\'?'.URL_KEY_ADMIN.'=users&id='.$this->post['userid'].'\')">'.$this->post['username'].'</a>';
					if (!isset($this->post['name'])) $this->post['name'] = Parser::name($this->post['title']);
					if ($this->has_main_photo && !@$this->post['main_photo']) {
						$this->post['main_photo'] = $this->global_action('main_photo');
					}
				}
				$this->global_action('main_photo_window');
				$this->json_array['files'] = json($this->filesToJson());
				$this->post['content'] = $this->Content->get(isset($this->post['setid'])?$this->post['setid']:$this->conid);
				$this->post['menu'] = $this->Menu->init(array('lang'=>$this->lang))->get($this->post['content']['menuid']?$this->post['content']['menuid']:$this->menuid);
				foreach ($this->dates as $col) {
					if (isset($this->post[$col])) $this->post[$col] = $this->fromTimestamp($this->post[$col]);
				}
				$links = array();
				if ($this->post['menu']) {
					if ($this->post['menu'][0]['title']) {
						$links[] = '<a href="'.URL::addExt($this->post['menu'][0]['url']).'" target="_blank">'.$this->post['menu'][0]['title'].'</a>';
					}
					if (isset($this->post['menu'][1])) {
						$links[] = ' :: <a href="'.URL::addExt($this->post['menu'][1]['url']).'" target="_blank">'.$this->post['menu'][1]['title'].'</a>';
					}
					if ($this->post['content']['name'] && $this->post['menu'][0]['name']!=$this->post['content']['name']) {
						$links[] = ($links?'&gt; ':'').'<a href="'.URL::addExt($this->post['menu'][0]['url']?$this->post['menu'][0]['url'].AMP.$this->post['content']['name']:'?contentID='.$this->post['content']['id']).'" target="_blank">'.$this->post['content']['title'].'</a>';
					}
				}
				if ($this->post('rid', false)) {
					$links[] = '&gt; <a href="'.URL::addExt($this->post['menu'][0]['url']?$this->post['menu'][0]['url'].AMP.$this->post['content']['name']:'?contentID='.$this->post['content']['id']).AMP.substr($this->table,8).'ID='.$this->post['rid'].'" target="_blank">ID: '.$this->post['rid'].'</a>';
				}
				$this->post['content']['name_url'] = join(' ',$links);
				$this->uploadHash();
				$this->toWindow();
				$this->win($this->name);
			break;
			case 'main_photo_window':
				if (!$this->has_main_photo || !$this->id || !@$this->post['main_photo']) break;
				$this->Index->My->uploadify();
				$id = $this->id;
				if (@Factory::call('uploadify')->names[$this->table]) {
					Factory::call('uploadify')->name($this->table);
					if (Factory::call('uploadify')->in_folder[$this->table]) {
						$id = ceil($this->id/Factory::call('uploadify')->in_folder[$this->table]);
					}
				}
				
				
				$this->post['main_photo_thumb'] = $this->http_dir_files.$this->table.'/'.$id.'/th3/'.$this->post['main_photo'];
				$th_i = 3;
				if (!is_file(FTP_DIR_ROOT.$this->post['main_photo_thumb'])) {
					$this->post['main_photo_thumb'] = $this->http_dir_files.$this->table.'/'.$id.'/th2/'.$this->post['main_photo'];
					$th_i = 2;
				}
				if (!is_file(FTP_DIR_ROOT.$this->post['main_photo_thumb'])) {
					$this->post['main_photo_thumb'] = $this->http_dir_files.$this->table.'/'.$id.'/th1/'.$this->post['main_photo'];
					$th_i = 1;
				}
				if (!is_file(FTP_DIR_ROOT.$this->post['main_photo_thumb'])) {
					$this->post['main_photo_thumb'] = $this->http_dir_files.'temp/'.$this->Index->Session->UserID.'/th3/'.$this->post['main_photo'];
					$th_i = 3;
				}
				if (!is_file(FTP_DIR_ROOT.$this->post['main_photo_thumb'])) {
					$this->post['main_photo_thumb'] = $this->http_dir_files.'temp/'.$this->Index->Session->UserID.'/th2/'.$this->post['main_photo'];
					$th_i = 2;
				}
				if (!is_file(FTP_DIR_ROOT.$this->post['main_photo_thumb'])) {
					$this->post['main_photo_thumb'] = $this->http_dir_files.'temp/'.$this->Index->Session->UserID.'/th1/'.$this->post['main_photo'];
					$th_i = 1;
				}
				/*
				if (!is_file(FTP_DIR_ROOT.$this->post['main_photo_thumb'])) {
					DB::run('UPDATE '.DB::prefix($this->table).' SET main_photo=\'\' WHERE '.$this->idlang);
					unset($this->post['main_photo_thumb'], $this->post['main_photo']);
				}
				*/
				
				if (@$this->post['main_photo_thumb']) {
					$this->post['main_photo_preview'] = str_replace('/th'.$th_i.'/','/th1/',$this->post['main_photo_thumb']);
					if (is_file(FTP_DIR_ROOT.$this->post['main_photo_preview'])) {
						$this->post['main_photo_size'] = @getimagesize(FTP_DIR_ROOT.$this->post['main_photo_preview']);
					} else {
						$this->post['main_photo'] = '';	
					}
				}
			break;
			case 'main_photo':
				if (!$this->has_main_photo) return false;
				$ret = DB::row('SELECT main_photo FROM '.DB::prefix($this->table).' WHERE '.$this->idcol.'='.$this->id.($this->idcol=='id'?'':' AND lang=\''.$this->lang.'\''),'main_photo');
				if (!$ret) $ret = DB::row('SELECT main_photo FROM '.DB::prefix($this->table).' WHERE '.$this->idcol.'='.$this->id.' AND main_photo!=\'\'','main_photo');	
				return $ret;
			break;
			default:
				$this->msg_reload = false;
				$this->msg_text = 'Global admin action: "'.$act.'" does not exist';
			break;
		}
	}
	
	protected function fixUploadFolders() {

			if (!is_dir($this->ftp_dir_files)) {
				@mkdir($this->ftp_dir_files);	
			}
			if (!is_dir($this->ftp_dir_files.'temp/')) {
				@mkdir($this->ftp_dir_files.'temp/');
			}
			if (!is_dir($this->ftp_dir_files.'email/')) {
				@mkdir($this->ftp_dir_files.'email/');	
			}
			/*
			// later
			$dh = opendir($this->ftp_dir_files);
			while ($file = readdir($dh)) {
				if ($file!='.' && $file!='..' && is_dir($this->ftp_dir_files.$file) && $file!='temp' && $file!='email') {
					$_dh = opendir($this->ftp_dir_files.$file);
					while ($_file = readdir($_dh)) {
						
					}
				}
			}
			*/
	}
	
	public function filesToJson() {
		list ($arr_files, $_dir, $dir) = $this->getFiles($this->id);
		$files = array();
		$i = 0;
		if (!$this->post('main_photo', false) && $this->id && in_array('main_photo', DB::columns($this->table))) {
			$this->post['main_photo'] = DB::one('SELECT main_photo FROM '.DB::prefix($this->table).' WHERE id='.$this->id);
		}
		if ($arr_files) {
			if ($this->has_files && $this->id) {
				foreach ($arr_files as $file) {
					$file_path_th1 = $dir.$file['file'];
					$file_path_th2 = str_replace('/th1/','/th2/',$dir).$file['file'];
					
					$resized = true;
					if (!is_file($file_path_th2) ||	(is_file($file_path_th1) && filesize($file_path_th1)==filesize($file_path_th2))) {
						$resized = false;
					}

					$files[$i] = array(
						'id'=> $file['id'],
						'f'	=> $file['file'],
						'd'	=> $_dir,
						't'	=> $file['media'],
						'w'	=> $file['width'],
						'h'	=> $file['height'],
						's'	=> File::display_size($file['size'],true),
						'title'	=> strform($file['title']),
						'descr'	=> strform($file['descr']),
						'added'	=> date('d.m.Y',$file['added']),
						'a'	=> $file['active'],
						'e'	=> ext($file['file']),
						'r'	=> $resized,
					);
					if ($this->post['main_photo']==$file['file']) $files[$i]['m'] = true;
					$i++;
				}
			} 
			elseif ($this->has_files) {
				foreach ($arr_files as $file) {
					$media = File::getMedia($file);
					$file_path = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/th1/'.$file; 
					if (!is_file($file_path)) {
						$resized = false;
						$file_path = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/'.$file;
					}
					else {
						$file_path_th1 = $dir.$file; 
						$file_path_th2 = str_replace('/th1/','/th2/',$dir).$file; 
						$resized = true;
						if (!is_file($file_path_th2) ||	(is_file($file_path_th1) && filesize($file_path_th1)==filesize($file_path_th2))) {
							$resized = false;
						}
					}
					if ($media=='image' || $media=='flash') {
						list ($width, $height) = @getimagesize($file_path);
					}
					$files[$i] = array(
						'id'=> 0,
						'f'	=> $file,
						'd'	=> $_dir,
						't'	=> $media,
						'w'	=> $width,
						'h'	=> $height,
						's'	=> @filesize($file_path),
						'added'	=> lang('$now'),
						'title'	=> '',
						'descr'	=> '',
						'e'	=> ext($file),
						'r'	=> $resized
					);
					if ($this->post['main_photo']==$file) $files[$i]['m'] = true;
					$i++;
				}
			}
			else {

				foreach ($arr_files as $file) {
					$media = File::getMedia($file);
					$files[$i] = array(
						'f'	=> $file,
						'd'	=> $_dir,
						't'	=> $media
					);
					if ($media=='image' || $media=='flash') {						
						$file_path = $dir.$file;
						$file_path_th1 = $dir.$file;
						$file_path_th2 = str_replace('/th1/','/th2/',$dir).$file;
						if (!is_file($file_path_th2) ||	(is_file($file_path_th1) && filesize($file_path_th1)==filesize($file_path_th2))) {
							$resized = false;
						}
						$size = @getimagesize($file_path);
						$files[$i]['w']	= $size[0];
						$files[$i]['h']	= $size[1];
						$files[$i]['r']	= $resized;
						if ($this->post && $this->post['main_photo']==$file) $files[$i]['m'] = true;
					}
					$i++;
				}
			}
		}
		return $files;
	}
	
	private $toTimestampTime = false;
	public function toTimestamp($str, $end = false) {
		return Date::toTimestamp($str, $end);
		/*
		if (!$str) return 0;
		if (is_array($str)) {
			$w = true;
			$ex = explode('/',$str['date']);
			if (count($ex)!=3) {
				return 0;	
			}
			if (!$this->toTimestampTime) {
				$this->toTimestampTime = $str['time'];
				$w = false;
			}
			$ex2 = explode(':',$this->toTimestampTime);
			if (!$w) $this->toTimestampTime = false;
			return mktime((int)$ex2[0],(int)$ex2[1],0,(int)$ex[1],(int)$ex[0],(int)$ex[2]);
		}
		elseif (is_numeric($str)) {
			return $str;
		} else {
			$ex = explode('/',$str);
			if (count($ex)!=3) {
				return 0;
			}
			if ($end) {
				return mktime(23,59,59,(int)$ex[1],(int)$ex[0],(int)$ex[2]);
			} else {
				return mktime(0,0,0,(int)$ex[1],(int)$ex[0],(int)$ex[2]);	
			}
		}
		*/
	}
	public function fromTimestamp($int) {
		return Date::fromTimestamp($int);
		/*
		if (strstr($int,'/')) return $int;
		if (!$int) $int = $this->time;
		return date('d/m/Y',$int);
		*/
	}
	public function fromTimestampTime($int) {
		return Date::fromTimestampTime($int);
		/*
		return date('H:i',$int);
		*/
	}
	
	protected function toDB() {
		
	}
	
	protected function toInsert() {
		return $this->toDB();
	}
	
	protected function toWindow() {
		
	}
	
	protected function toUpdate() {
		
	}
	
	protected function toFix($table, $id_col, $id = 0, $where = '') {
		$data = array();
		$statused = false;
		if ($this->affected && ($this->updated==Site::ACTION_ACTIVE || $this->updated==Site::ACTION_UPDATE)) {
			if (array_key_exists('statused',$this->rs) && array_key_exists('active',$this->data) && array_key_exists('active',$this->rs) && $this->rs['active']!=$this->data['active']) {
				$data['statused'] = $this->time;
				if (array_key_exists('statuser',$this->rs)) $data['statuser'] = $this->Index->Session->UserID;
				elseif (array_key_exists('editor',$this->rs)) $data['editor'] = $this->Index->Session->UserID;
				$statused = true;
			}
		}
		if (!$statused && $this->updated==Site::ACTION_UPDATE && $this->affected) {
			if (array_key_exists('edited',$this->rs)) $data['edited'] = $this->time;
			if (array_key_exists('editor',$this->rs)) $data['editor'] = $this->Index->Session->UserID;
		}
		if ($data) DB::update($table,$data,$id,$id_col);
	}
	
	
	
	protected function chart($arr, $names = array(), $name = 'Serie1', $width = 600, $height = 300, $title = 'Sample', $titleY = '', $titleX = '',$curve = 0.1, $trans, $pallete = '1') {
		$DataSet = Factory::call('pData');
		if (!$arr) {
			$arr = array(
				'nothing'	=> false
			);	
		}
 		foreach ($arr as $serie_name => $a) {
			if (!$a) {
				$a = array(1=>1);
			}
			foreach ($a as $k => $v) {
				$DataSet->AddPoint($v,$serie_name,$k);
			}
		}
		$DataSet->AddAllSeries();
		$DataSet->SetAbsciseLabelSerie();
		foreach ($names as $s => $n) {
			$DataSet->SetSerieName($n,$s);
		}
		if ($titleY) $DataSet->SetYAxisName($titleY);
		if ($titleX) $DataSet->SetXAxisName($titleX);
		$Chart = Factory::call('pChart',$width,$height);
		$Chart->loadColorPalette(FTP_DIR_FONTS.'tones-'.$pallete.'.txt');
		list ($br, $bg, $bb) = Parser::html2rgb('ffffff');
		$Chart->drawBackground($br,$bg,$bb);
		$Chart->setFontProperties(FTP_DIR_FONTS.'tahoma.ttf',8);
		$Chart->setGraphArea(55,($title ? 30: 10),$width-10,$height - 60);
		$Chart->drawGraphArea($br,$bg,$bb, false);
		list ($br, $bg, $bb) = Parser::html2rgb('#FFFFFF');
		$Chart->drawGraphAreaGradient($br,$bg,$bb,30,TARGET_GRAPHAREA);
		list ($r, $g, $b) = Parser::html2rgb('#444444');
		$Chart->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,$r,$g,$b,TRUE,90,0,false,1);
		list ($r, $g, $b) = Parser::html2rgb('b4b4b4');
		$Chart->drawGrid(2,false,$r,$g,$b,50,1,1);
		$Chart->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),$curve,$trans,false,$serie_name);
		$Chart->setFontProperties(FTP_DIR_FONTS.'tahoma.ttf',10);
		if ($title) {
			list ($r, $g, $b) = Parser::html2rgb('#0066CC');
			$Chart->drawTitle(15,15,$title,$r,$g,$b,-1,-1,false);
		}
		$Chart->drawLegend(60,35,$DataSet->GetDataDescription(),255,255,255);
		$Chart->Render($this->ftp_dir_files.'temp/'.$name.'.png');
		$Chart->clearScale();
		$DataSet->clear();
		return $this->http_dir_files.'temp/'.$name.'.png';
	}
	
	protected function genFusionXml($data, $y = array(), $ret = array()) {
		$cat = '<categories>';
		$join = array();
		$colors = array(
			0	=> 'color="DBDC25" anchorBorderColor="DBDC25" anchorBgColor="DBDC25"',
			1	=> 'color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A"',
			2	=> 'color="F1683C" anchorBorderColor="F1683C" anchorBgColor="F1683C"',
			3	=> 'color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1"'
		);
		if ($ret['swf']=='MSArea') {
			$colors = array(
				0	=> 'color="DBDC25" plotBorderColor="DBDC25"',
				1	=> 'color="F1683C" plotBorderColor="1D8BD1"'
			);
		}
		if ($ret['multiple']) {
			$colors = array(
				0	=> 'color="DBDC25" anchorBorderColor="DBDC25" anchorBgColor="DBDC25"',
				1	=> 'color="2AD62A" anchorBorderColor="2AD62A" anchorBgColor="2AD62A"',
				2	=> 'color="F1683C" anchorBorderColor="F1683C" anchorBgColor="F1683C"',
				3	=> 'color="1D8BD1" anchorBorderColor="1D8BD1" anchorBgColor="1D8BD1"',
				4	=> 'color="008040" anchorBorderColor="008040"',
				5	=> 'color="FF0080" anchorBorderColor="FF0080"',
				6	=> 'color="0080C0" anchorBorderColor="0080C0"',
			);
		}

		foreach ($y as $i => $name) {
			$join[$i] = '<dataset seriesName="'.html($name).'" '.$colors[$i].'>';
		}
		
		if ($ret['multiple']) {			
			$z = 0;
			foreach ($data as $i => $a) {
				foreach ($a as $v) {
					if (!$z) $cat .= '<category label="'.html($v['x']).'" />
					';
					$join[$i] .= '<set value="'.html($v['y']).'" />
					';
				}
				$z++;
			}
		} else {
			foreach ($data as $i => $a) {
				$cat .= '<category label="'.html($a['x']).'" />';
				foreach ($y as $i => $name) {
					$join[$i] .= '<set value="'.html($a['y'.$i]).'" />';
				}
			}
		}
		foreach ($y as $i => $name) {
			$join[$i] .= '</dataset>';
		}

		
		$cat .= '</categories>';
		$ret = $cat.join('',$join);
		
		
		//$ret .= '<styles><definition><style name="CaptionFont" type="font" size="11"/><style name="Bevel" type="bevel" distance="4" blurX="2" blurY="2"/><style name="DataValuesFont" type="font" borderColor="1D8BD1" bgColor="1D8BD1" color="153E7E" /><style name="myAnim" type="animation" param="_alpha" start="0" duration="1" /><style name="dummyShadow" type="Shadow" alpha="0" /></definition><application><apply toObject="CAPTION" styles="CaptionFont" /><apply toObject="SUBCAPTION" styles="CaptionFont" /></application></styles>';
		
		
		return $ret;
	}
	
	
	protected function genFusionXmlSingle($data, $ret) {
		$ret = '';
		foreach ($data as $i => $a) {
			$ret .= '<set label="'.$a['x'].'" value="'.$a['y'].'" />';
		}
		return $ret;
	}

	protected function getFiles($id = 0) {
		$th1 = ($this->image_sizes ? 'th1/' : '');
		if ($id) {
			$name = $this->name;
			if ($name=='grid' && $this->module) $name = 'grid_'.$this->module;
			if ($this->has_global_files) {
				$dir = $this->ftp_dir_upload.$name.'/'.$id.'/'.$th1;
				$_dir = $this->http_dir_upload.$name.'/'.$id.'/'.$th1;
			} else {
				$dir = $this->ftp_dir_files.$name.'/'.$id.'/'.$th1;
				$_dir = $this->http_dir_files.$name.'/'.$id.'/'.$th1;
			}
		} else {
			$dir = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/'.$th1;
			$_dir = $this->http_dir_files.'temp/'.$this->Index->Session->UserID.'/'.$th1;
		}
		
		if (!is_dir($dir)) {
			return false;
		}
		$dh = opendir($dir);
		if (!$dh) return false;
		$ret = array();
		while (($file = readdir($dh))!==false) {
			if ($file=='.' || $file=='..' || is_dir($dir.$file)) continue;
			$ret[] = $file;
		}
		closedir($dh);
		natsort($ret);
		
		
		if ($this->has_files && $id) {
			// check existing
			$files = $ret;
			$ret = $exist = array();
			
			$qry = DB::qry('SELECT file, id, media, mime, width, height, size, title_'.$this->lang.' AS title, descr_'.$this->lang.' AS descr, added, active FROM '.$this->prefix.$this->table.'_files WHERE setid='.$id.' ORDER BY sort',0,0);
			$del = $ret = array();
			while ($rs = DB::fetch($qry)) {
				if ($this->has_global_files) {
					$f = $dir.$rs['file'];
				} else {
					$f = $dir.$rs['file'];
				}
				if (is_file($f)) {
					$exist[] = $rs['file'];
					$ret[] = $rs;
				} else {
					$del[] = $rs['id'];	
				}
			}
			$sort = count($exist);
			foreach ($files as $file) {
				if (!in_array($file, $exist)) {
					$sort++;
					$media = File::getMedia($file);
					$width = $height = 0;
					$mime = File::arrMime(ext($file));
					$file_path = $dir.$file;
					$size = filesize($file_path);
					if ($media=='image' || $media=='flash') {
						list ($width, $height) = @getimagesize($file_path);
					}
					$data = array(
						'setid'	=> $id,
						'file'	=> $file,
						'width'	=> $width,
						'height'=> $height,
						'media'	=> $media,
						'mime'	=> $mime,
						'size'	=> $size,
						'added'	=> $this->time,
						'userid'=> $this->Index->Session->UserID,
						'active'=> 1,
						'sort'	=> $sort
					);
					DB::insert($this->table.'_files',$data);
					$data['id'] = DB::id();
					$data['title'] = '';
					$data['descr'] = '';
					$ret[] = $data;
				}
			}
			
			DB::free($qry);
			if ($del) {
				DB::run('DELETE FROM '.$this->prefix.$this->table.'_files WHERE id IN ('.join(',',$del).')');
			}
			return array($ret, $_dir, $dir);
		}
		return array($ret, $_dir, $dir);
	}
	
	protected function cleanTemp($id, $dir = false) {
		if (!$id) return false;
		if (!$dir) $dir = $this->ftp_dir_files.$id.'/';
		$i = File::rmdir($dir, false);
		return $i;
	}
	
	protected function deleteFiles($id, $dir = false) {
		if (is_array($id)) {
			$total = 0;
			foreach ($id as $_id) {
				$total += $this->deleteFiles($_id);	
			}
			return $total;
		}
		if (!$id) return false;
		if (!$dir) {
			if ($this->has_global_files) {
				$dir = $this->ftp_dir_upload.$this->name.'/'.$id.'/';
			} else {
				$dir = $this->ftp_dir_files.$this->name.'/'.$id.'/';	
			}
		}
		if (!is_dir($dir)) return false;
		$i = File::rmdir($dir);
		return $i;
	}
	
	protected function deleteFile($file) {
		if (!$file) return false;
		if ($this->has_files && $this->id) {
			DB::run('DELETE FROM '.$this->prefix.$this->table.'_files WHERE setid='.$this->id.' AND file LIKE '.e($file));
		}
		$dir = $this->findPath();
		$i = 0;
		foreach ($this->image_sizes as $i => $size) {
			if (is_file($dir.'th'.$i.'/'.$file) && @unlink($dir.'th'.$i.'/'.$file)) $i++;
		}
		if (is_file($dir.$file) && @unlink($dir.$file)) $i++;
		@unlink($dir);
		return $i;
	}
	
	protected function moveFiles($serialize = false, $extensions = array()) {
		if (!$this->id) return false;
		if (!is_dir($this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/')) return false;
		if ($this->has_global_files) {
			$dir = $this->ftp_dir_upload;	
		} else {
			$dir = $this->ftp_dir_files;	
		}
		if (!is_dir($dir.$this->table.'/')) @mkdir($dir.$this->table.'/',0777);
		if (!is_dir($dir.$this->table.'/')) {
			Message::halt('Fuck that','0777 needed for '.$dir.', coz cant create '.$dir.$this->table.'/');	
		}
		
		$this->Index->My->uploadify();
		$id = $this->id;
		if (@Factory::call('uploadify')->names[$this->table]) {
			Factory::call('uploadify')->name($this->table);
			if (Factory::call('uploadify')->in_folder[$this->table]) {
				$id = ceil($this->id/Factory::call('uploadify')->in_folder[$this->table]);
			}
		}
		
		
		
		
		if (!is_dir($dir.$this->table.'/'.$id.'/')) @mkdir($dir.$this->table.'/'.$id.'/',0777);
		$f = false;
		$renamed = array();
		$files = array();
		if ($this->image_sizes) {
			foreach ($this->image_sizes as $i => $size) {
				$from = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/th'.$i.'/';
				$to = $dir.$this->table.'/'.$id.'/th'.$i.'/';
				if (!is_dir($from)) continue;
				if (!is_dir($to)) @mkdir($to, 0777);
				$dh = @opendir($from);
				while (($file = readdir($dh))!==false) {
					if ($file=='.' || $file=='..' || is_dir($from.$file)) continue;
					$ext = ext($file);
					if ($extensions) {
					//	if (!in_array($ext,$extensions)) continue;
					}
					if (!$serialize) {
						$nameonly = nameOnly($file);
						$new_file = File::getUnique($to,$nameonly,$ext);
					} else {
						$new_file = $file;
						if (is_file($to.$new_file)) @unlink($to.$new_file);
					}
					if (!$f && (!$extensions || in_array($ext,$extensions))) $f = $new_file;
					if (@rename($from.$file, $to.$new_file)) {
						@unlink($this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/'.$file);
						$files[] = $new_file;	
					}
				}
				@closedir($dh);
			}
		} else {
			$from = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/';
			$to = $dir.$this->table.'/'.$id.'/';
			if (is_dir($from)) {
				if (!is_dir($to)) @mkdir($to, 0777);
				$dh = @opendir($from);
				while (($file = readdir($dh))!==false) {
					if ($file=='.' || $file=='..' || is_dir($from.$file)) continue;
					$ext = ext($file);
					if ($extensions) {
					//	if (!in_array($ext,$extensions)) continue;
					}
					if (!$serialize) {
						$nameonly = nameOnly($file);
						$new_file = File::getUnique($to,$nameonly,$ext);
					} else {
						$new_file = $file;
						if (is_file($to.$new_file)) @unlink($to.$new_file);
					}
					if (!$f && (!$extensions || in_array($ext,$extensions))) $f = $new_file;
					if (@rename($from.$file, $to.$new_file)) {
						@unlink($this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/'.$file);
						$files[] = $new_file;	
					}
				}
				@closedir($dh);
			}
		}
		$from = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/';
		$to = $dir.$this->table.'/'.$id.'/';
		if (!is_dir($to)) @mkdir($to, 0777);
		$dh = @opendir($from);
		if ($dh) {
			while (($file = readdir($dh))!==false) {
				if ($file=='.' || $file=='..' || is_dir($from.$file) || File::isPicture($file)) continue;
				if (!$serialize) {
					$nameonly = nameOnly($file);
					$ext = ext($file);
					$new_file = File::getUnique($to,$nameonly,$ext);
				} else {
					$new_file = $file;
					if (is_file($to.$new_file)) @unlink($to.$new_file);
				}
				if (@rename($from.$file, $to.$new_file)) {
					$files[] = $new_file;	
				}
			}
			@closedir($dh);
		}
		$ret = ($this->has_files ? array_unique($files) : $f);
		if (!$ret) {
			if (Factory::call('uploadify')->names[$this->table]) {
				$ret = Factory::call('uploadify')->getOneFile();	
			}
		}
		return $ret;
	}
	
	private function findPath($id = 0) {
		if (!$id) $id = $this->id;
		if ($this->id) {
			if ($this->has_global_files) {
				$dir = $this->ftp_dir_upload.$this->table.'/'.$id.'/';
			} else {
				$dir = $this->ftp_dir_files.$this->table.'/'.$id.'/';	
			}
		} else {
			$dir = $this->ftp_dir_files.'temp/'.$this->Index->Session->UserID.'/';
		}
		return $dir;	
	}
	
	protected function findFiles($name, $exact = false) {
		$dir = $this->findPath();
		$dh = @opendir($dir);
		if (!$dh) return array();
		$ret = array();
		while ($file = readdir($dh)) {
			if ($file=='.' || $file=='..') continue;
			$name_only = nameOnly($file);
			if ($exact) {
				if ($exact!=$name) continue;
			}
			$ex = explode('_',$name_only);
			if ($ex[0]!=$name) continue;
			$ext = ext($file);
			$media = File::getMedia($file);
			$index = intval($ex[1]);
			if (!$ret[$index]) $ret[$index] = array();
			$ret[$index][$media] = array(
				'name'	=> $name_only,
				'url'	=> $this->http_dir_files.$this->table.'/'.$this->id.'/'.$file,
				'file'	=> $file
			);
		}
		closedir($dh);
		$dh = @opendir($dir.'th1/');
		if (!$dh) return $ret;
		$ret = array();
		while ($file = readdir($dh)) {
			if ($file=='.' || $file=='..') continue;
			$name_only = nameOnly($file);
			if ($exact) {
				if ($exact!=$name) continue;
			}
			$ex = explode('_',$name_only);
			if ($ex[0]!=$name) continue;
			$ext = ext($file);
			$media = File::getMedia($file);
			$index = intval($ex[1]);
			if (!$ret[$index]) $ret[$index] = array();
			$ret[$index][$media] = array(
				'name'	=> $name_only,
				'url'	=> $this->http_dir_files.$this->table.'/'.$this->id.'/th1/'.$file,
				'file'	=> $file
			);
		}
		closedir($dh);
		return $ret;
	}
	
	protected function cropImage($path, $file) {
		if (!$this->image_sizes) return;
		$im = Factory::call('image')->driver('GD')->load($path.$file);
		$im->massResize($this->image_sizes, $path, $file);
		@unlink($path.$file);
	}	

	protected function tpl($name, $vars = array()) {
		switch ($name) {
			case '_html':
				if (!$this->show['html']) return false;
			break;
			case '_header':
				if (!$this->show['header']) return false;
			break;
			case '_footer':
				if (!$this->show['footer']) return false;
			break;
		}
		if ($vars) extract($vars);
		if (is_file(FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.$name.'.php')) {
			return include FTP_DIR_ROOT.'tpls/'.$this->tpl.'/admin/'.$name.'.php';
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'.php')) {
			return include FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'.php';
		}
		elseif (is_file(FTP_DIR_ROOT.'tpls/admin/'.$name.'.php')) {
			return include FTP_DIR_ROOT.'tpls/admin/'.$name.'.php';
		}
		else {
			echo 'File "'.FTP_DIR_ROOT.'tpls/admin/'.TEMPLATE_ADMIN.'/'.$name.'.php" does not exist';
		}
	}
	
	private function tpl_file() {
		if ($this->module) {
		//	p($this->name);
		
			if (substr($this->name,0,5)=='grid_') $this->name = 'grid';
			if (($this->name=='grid' || substr($this->name,0,5)=='grid_') && !$this->tab) {
				$this->tpl_file = FTP_DIR_ROOT.'tpls/admin/grid.php';
				return;
			}
			if ($this->tab && isset($_GET[self::KEY_LOAD])) {
				$this->no_inc(array('top','bot'));
			}
			$file = File::fixFileName($this->name.'_'.$this->module);

			if (($f = FTP_DIR_TPLS.$this->tpl.'/admin/'.$file.'.php') && is_file($f)) {
				$this->tpl_file = $f;
			}
			elseif (($f = FTP_DIR_TPLS.'admin/'.$file.'.php') && is_file($f)) {
				$this->tpl_file = $f;
			}
			elseif (($f = FTP_DIR_TPLS.'admin/'.$this->name.'.php') && is_file($f)) {
				if ($this->name=='grid' && $this->tab) {
					$this->tpl_file = FTP_DIR_TPLS.'admin/'.$this->name.'_tab.php';
					$this->name  = $this->name.'_'.$this->tab;
				} else {
					$this->tpl_file = $f;	
				}
			}
			if ($this->name==='grid') $this->name = 'grid_'.$this->module;
		//	p($this->tpl_file);
		}
	}
	
	protected function upload() {
		$this->global_action('upload', true);
	}	

	protected function content() {
		if (SITE_TYPE=='json') {
			throw new Exception('Trying to load content for JSON as HTML');
		}
		$this->tpl_file();

		if (!$this->tpl_file) {
			if ($this->tab && !strstr($this->class,'_')) {
				echo 'Class is missing for "<u>'.$this->tab.'</u>" tab in <u>'.$this->class.' {...}</u> class.<br>Please create new class calling <u>'.$this->class.'_'.$this->tab.' {...}</u> in <u>'.$this->class.'.php</u> file';
			}
			else {
				echo 'Template file was not found: <u>'.$this->name.'.php</u><br>Please create new template file with this name';
			}
			return false;
		}
		if (!is_file($this->tpl_file)) {
			echo $this->tpl_file.' is missing';
			return false;
		}

		$allow = Allow()->admin($this->name, 'view', false, false, $this->table, $this->id);
		if (!$allow) $this->listing();
		
		$this->tpl('_html');
		$this->tpl('_header');
		echo '<div id="center-area" class="admin-area">';
		if ($allow && $this->name!='grid') {
			$this->inc('allow', array('allow' => $allow));
		} else {
			include $this->tpl_file;
		}
		echo '</div>';
		$this->tpl('_footer');
	}
	protected function window() {
		$allow = Allow()->admin($this->name, 'view', false, false, $this->table, $this->id);
		if ($allow) return;
		if ($this->tab_class) {
			$o = new $this->tab_class($this);
			$o->window();
		} else {
			$this->global_action('content_window');
		}
	}
	protected function printed() {
		if ($this->tab_class) {
			$o = new $this->tab_class($this);
			$o->printed();
		} else {
			$this->global_action('content_printed');
		}
	}	
	protected function json() {
		if ($this->tab_class) {
			$o = new $this->tab_class($this);
			return $o->json();
		}
	}

	public static function filePHPok($body) {
		$b = 0;
		$s = 0;
		ob_start();
		$eval = token_get_all($body);
		$output = ob_get_contents();
		ob_end_clean();
		if ($output) {
			$eval = false;
			$output = 'Cannot validate this php code: '.$output.'';
		}
		else {
			foreach ($eval as $t)	{
				if (is_array($t)) {
					switch ($t[0]) {
						case T_CURLY_OPEN:
						case T_DOLLAR_OPEN_CURLY_BRACES:
						case T_START_HEREDOC: ++$s; break;
						case T_END_HEREDOC:   --$s; break;
					}
				} elseif ($s & 1) {
					switch ($t) {
						case '`':
						case '"': --$s; break;
					}
				} else {
					switch ($t) {
					case '`':
					case '"': ++$s; break;
					case '{': ++$b; break;
					case '}':
						if ($s) --$s;
						else {
							--$b;
							if ($b < 0) break 2;
						}
						break;
					}
				}
			}
		}
		$s = @ini_set('log_errors', false);
		$t = @ini_set('display_errors', true);
		if ($b<>0) {
			Conf()->s('phpERROR','Braces mismatch: '.$b.'. Please check for { and } in your document');
			return false;
		}
		if (!$output) {
			ob_start();
			$eval = eval('?><?php if(0){?>'. $body.'?><?php }?>');
			$output = ob_get_contents();
			ob_end_clean();
			$eval===false ? Conf()->s('phpERROR', str_replace('<b>Parse error</b>:  parse error,','Parse error:',str_replace('<!--error--><br />','',substr($output,0,strpos($output,' in ')))).' '.substr($output,strpos($output,'on line')-5)):'';
			if (Conf()->g('phpERROR')) {
				if ($pos = strpos(Conf()->g('phpERROR'),'<script language')) {
					Conf()->s('phpERROR', substr(Conf()->g('phpERROR'),0,$pos));
				}
				Conf()->s('phpERROR', preg_replace('/<br\s?\/>/i','',Conf()->g('phpERROR')));
				if ($pos = strpos(Conf()->g('phpERROR'),' in ')) {
					Conf()->s('phpERROR', substr(Conf()->g('phpERROR'),0,$pos));
				}
			}
		} else {
			Conf()->s('phpERROR',$output);
		}
		@ini_set('display_errors', $t);
		@ini_set('log_errors', $s);
		return $eval===false?false:true;
	}
	

	protected function translate($text, $from, $to) {
		$this->_translate($text, true);
		$text = translate($text, $from, $to);
		$this->_translate($text, false);
		return $text;
	}
	
	protected function _translate(&$word, $start = true, $fix = false) {
		if ($fix) {
			return 	str_replace(array(',',' ','.'),'',$fix);
		}
		$a = '(';
		$b = ')';
		if ($start) {
			$this->lang_collect = array();
			$this->lang_number = 902;
			if (preg_match_all('/%([0-9]+)/U', $word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'1'.$b,$word);
					$this->lang_collect[0][$this->lang_number.'1'] = $v;
				}
			}
			if (preg_match_all('/\{([^\}]+)\}/U',$word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'2'.$b,$word);
					$this->lang_collect[1][$this->lang_number.'2'] = $v;
				}
			}
			if (preg_match_all('/\[([^\]]+)\]/U',$word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'3'.$b,$word);
					$this->lang_collect[2][$this->lang_number.'3'] = $v;
				}
			}
			if (preg_match_all('/<([^>]+)>/U',$word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'4'.$b,$word);
					$this->lang_collect[3][$this->lang_number.'4'] = $v;
				}
			}
			$word = str_replace("\r",$a.'111'.$b,$word);
			$word = str_replace("\n",$a.'112'.$b,$word);
			krsort($this->lang_collect);
		} else {
			$word = preg_replace('/\s*'.preg_quote($a,'/').'\s*([0-9\s,]{4,6})\s*'.preg_quote($b,'/').'\s*/Ue','$this->_translate($this->time,0,\''.$a.'\\1'.$b.'\')',$word);
			$word = str_replace($a.'111'.$b,"\r",$word);
			$word = str_replace($a.'112'.$b,"\n",$word);
			foreach ($this->lang_collect as $i => $arr) {
				foreach ($arr as $n => $v) {
					$word = str_replace($a.$n.$b, $v, $word);	
				}
			}
			$word = preg_replace('/\ +/', ' ',$word);
			$word = str_replace("> <", "><", $word);
		}
	}
	
	
	protected function fixLangName(&$word, $end, $reset = false, $fix = false) {
		if ($fix) {
			$fix = str_replace(',','',$fix);
			return $fix;
		}
		elseif ($reset) {
			
			$this->lang_collect = array();
			$this->lang_number = 902;
			return;	
		}
		$a = '(';
		$b = ')';
		if (!$end) {
			$this->lang_collect = array();
			if (preg_match_all('/%([0-9]+)/U', $word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'1'.$b,$word);
					$this->lang_collect[0][$this->lang_number.'1'] = $v;
				}
			}
			if (preg_match_all('/\{([^\}]+)\}/U',$word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'2'.$b,$word);
					$this->lang_collect[1][$this->lang_number.'2'] = $v;
				}
			}
			if (preg_match_all('/\[([^\]]+)\]/U',$word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'3'.$b,$word);
					$this->lang_collect[2][$this->lang_number.'3'] = $v;
				}
			}
			if (preg_match_all('/<([^>]+)>/U',$word, $m)) {
				foreach ($m[0] as $i => $v) {
					$this->lang_number += 1;
					$word = str_replace($v,$a.$this->lang_number.'4'.$b,$word);
					$this->lang_collect[3][$this->lang_number.'4'] = $v;
				}
			}
			krsort($this->lang_collect);
		} else {
			$word = preg_replace('/'.preg_quote($a,'/').'\s*([0-9,]{4,5})\s*'.preg_quote($b,'/').'/Ue','$this->fixLangName($this->time,0,0,\''.$a.'\\1'.$b.'\')',$word);
			foreach ($this->lang_collect as $i => $arr) {
				foreach ($arr as $n => $v) {
					$word = str_replace($a.$n.$b, $v, $word);	
				}
			}
		}
	}
	
	
	protected function visualCommit() {
		$this->allow('visual','commit');
		$this->msg_delay = 4000;
		$ok = 0;
		foreach ($_SESSION['VISUAL_FILES'] as $file => $changes) {
			if (!is_file(FTP_DIR_TPL.$file)) continue;
			$html = $this->Index->Edit->unvisual(file_get_contents(FTP_DIR_TPL.$file));
			if (!$html) {
				$this->msg_text = 'Contents are empty in '.FTP_DIR_TPL.$file.'';
				$this->msg_type = 'error';
				unset($_SESSION['VISUAL_FILES'][$file]);
				return;
			}
			$new_file = str_replace('_visual_','',$file);
			if (!is_file(FTP_DIR_TPL.$new_file)) {
				$this->msg_text = 'File does not exist: '.FTP_DIR_TPL.$new_file.'';
				$this->msg_type = 'error';
				unset($_SESSION['VISUAL_FILES'][$file]);
				return;
			}
			$html = str_replace("\r\n\r\n","\r\n",$html);
			$html = str_replace("\n\n","\n",$html);
			if (file_put_contents(FTP_DIR_TPL.$new_file, $html)) {
				unlink(FTP_DIR_TPL.$file);
				$ok++;
			}
		}
		$this->msg_reload = true;
		$this->msg_delay = 1600;
		$this->msg_text = lang('_$Changes were committed!').'<br />'.lang('_$%1 {number=%1,file was,files were} affected!',$ok).' '.lang('_$please wait').'..';
		$_SESSION['VISUAL_FILES'] = array();
	}
	
	protected function visualDiscard() {
		$this->allow('visual','discard');
		foreach ($_SESSION['VISUAL_FILES'] as $file => $changes) {
			if (is_file(FTP_DIR_TPL.$file)) unlink(FTP_DIR_TPL.$file);	
		}
		$_SESSION['VISUAL_FILES'] = array();
	}
	

	
	protected function fileArrayToJson($file) {
		$files = array();
		foreach ($this->addVisualFile($file) as $f => $c) {
			$files[] = array(
				'f'	=> $f,
				'c'	=> $c
			);
		}
		return $files;
	}
	
	protected function visualSave() {
		if (post('visual_add')) {
			$this->allow('visual','add');
			if (!post('html')) {
				$this->msg_text = lang('$Template HTML source was empty');
				$this->msg_type = 'error';
				$this->msg_reload = false;
				return;	
			}
			$from = $this->visualHTML(false, post('visual_add'), true);
			$ex = explode('_',substr($from['id'],2));
			$f = $ex[0];
			array_shift($ex);
			if (!isset($_SESSION['VISUAL_INDEX'])) $_SESSION['VISUAL_INDEX'] = 0;
			$_SESSION['VISUAL_INDEX']++;
			$id = 'a-'.$_SESSION['VISUAL_INDEX'].'i_'.join('_',$ex);
			if (post('tag')) {
				$html = '<visual id='.$id.'><'.post('tag').'>'.post('html').'</'.post('tag').'></visual>';
			} else {
				$html = '<visual id='.$id.'>'.post('html').'</visual>';
			}
			if (post('above')==1) {
				$new_html = str_replace($from['html'],$html.$from['html'],$from['contents']);
			} else {
				$new_html = str_replace($from['html'],$from['html'].$html,$from['contents']);
			}
			if (@file_put_contents($from['dir'].$from['file'],$new_html)) {
				$this->json_ret = array(
					'text'	=> lang('_$Element has been added!').'<br />'.lang('_$Refresh the page to see parsed result..'),
					'id'	=> $from['id'],
					'type'	=> 'tick',
					'delay'	=> 2000,
					'html'	=> $html,
					'above'	=> post('above'),
					'visual_add' => post('visual_add'),
					'files'	=> $this->fileArrayToJson($from['file'])
				);
			}
		} elseif (post('visual_id')) {
			$this->allow('visual','edit');
			$from = $this->visualHTML(false, post('visual_id'), true);
			$html = post('html');
			if (trim(str_replace(array("\r","\n","\t"),'',$this->Index->Edit->unvisual($from['html'])))==trim(str_replace(array("\r","\n","\t"),'',$html))) {
				return;	
			}
			$new_html = str_replace($from['html'],'<visual id='.$from['id'].''.($from['nomove'] ? ' class=a-visual_nomove':'').'>'.$html.'</visual>',$from['contents']);
			if (@file_put_contents($from['dir'].$from['file'],$new_html)) {
				$this->json_ret = array(
					'text'	=> lang('_$Element has been saved!').'<br />'.lang('_$Refresh the page to see parsed result..'),
					'id'	=> $from['id'],
					'type'	=> 'tick',
					'delay'	=> 2000,
					'html'	=> $html,
					'files'	=> $this->fileArrayToJson($from['file'])
				);
			}
		}
	}
	

	
	protected function addVisualFile($file) {
		if (!isset($_SESSION['VISUAL_FILES']) || !is_array($_SESSION['VISUAL_FILES'])) $_SESSION['VISUAL_FILES'] = array();
		if (!isset($_SESSION['VISUAL_FILES'][$file])) $_SESSION['VISUAL_FILES'][$file] = 0;
		$_SESSION['VISUAL_FILES'][$file]++;
		return $_SESSION['VISUAL_FILES'];
	}
	
	protected function visualDelete() {
		if (!post('visual_id')) {
			$this->json_ret = array(
				'error'	=> 'All undefined!'
			);
			return false;
		}
		$this->allow('visual','delete');
		$from = $this->visualHTML(false, post('visual_id'), true);
		if (!$from['file'] || $from['fucked']) {
			$this->json_ret = array(
				'error'	=> lang('_$Did not match, range is too high or attempt to truncate the whole file: %1',$from['file']).'<br /><br />'.lang('_$Will not delete this element!')
			);
			return false;
		}
		$contents = str_replace("\r\n\r\n","\r\n",str_replace($from['html'],'',$from['contents']));
		if (!$contents) {
			$this->json_ret = array(
				'error'	=> lang('_$Attempt to truncate the whole file')
			);
			return false;
		}
		if (@file_put_contents(FTP_DIR_TPL.$from['file'],$contents)) {
			$this->json_ret = array(
				'from'	=> $from['id'],
				'files'	=> $this->fileArrayToJson($from['file'])
			);
		}
	}
	
	protected function visualMove() {
		$this->allow('visual','move');
		$before = post('before','',false);
		if (!post('from') || !post('to')) {
			$this->json_ret = array(
				'error'	=> 'All undefined!'
			);
			return false;
		}
		elseif (post('from')==post('to')) {
			$this->json_ret = array(
				'error'	=> lang('_$Elements cannot be same!')
			);
			return false;
		}
		$from = $this->visualHTML(false, post('from'), true);
		$to = $this->visualHTML(false, post('to'), true);
		if (!$to['file'] || !$from['file']) {
			$this->json_ret = array(
				'error'	=> 'Did not match, range is too high!'
			);
			return false;
		}
		$contents = str_replace("\r\n\r\n","\r\n",str_replace($from['html'],'',$from['contents']));
		$same_file = false;
		if ($from['file']==$to['file']) {
			$same_file = true;
			$to['contents'] = $contents;
		} else {
			if (file_put_contents(FTP_DIR_TPL.$from['file'],$contents)) {
				$this->addVisualFile($from['file']);	
			}
		}
		$contents = str_replace("\r\n\r\n","\r\n",str_replace($to['html'],($before ? $from['html']."\r\n".$to['html'] : $to['html']."\r\n".$from['html']),$to['contents']));
		$to_id = $from['id'];
		if (!$same_file) {
			$from_ex = explode('_',substr($from['id'],2));
			$ex = explode('_',substr($to['id'],2));
			$to_id = 'a-'.$from_ex[0].'_visual_'.$ex[2];
			$contents = str_replace('<visual id='.$from['id'].'>','<visual id='.$to_id.'>',$contents);
		}
		
		if (file_put_contents(FTP_DIR_TPL.$to['file'],$contents)) {
			$this->json_ret = array(
				'same_file'=> $same_file,
				'from'	=> $from['id'],
				'to'	=> $to['id'],
				'to_new'=> $to_id,
				'files'	=> $this->fileArrayToJson($to['file'])
			);
		}
	}
	
	protected function visualHTML($clean = true, $id = 0, $ret = false) {
		$this->allow('visual','preview');
		if ($id) $this->visual_id = $id;
		else $this->visual_id = post('visual_id');
		if (!$this->visual_id) return false;
		$dir = FTP_DIR_TPL;
		if (!is_dir($dir)) return false;
		$ex = explode('_',$this->visual_id);
		array_shift($ex);
		$file = '_'.Parser::iDfile(join('_',$ex), true);
		if (substr($file,0,8)!='_visual_') {
			$file = substr($file,1);
		}
		if (!is_file($dir.$file)) {
			$this->json_ret = array(
				'html'	=> 'Fatal error, file '.$dir.$file.' is missing"&gt;',
				'file'	=> false
			);
			return false;
		}
		$contents = file_get_contents($dir.$file);
		if (!$contents) {
			$this->json_ret = array(
				'html'	=> 'Fatal error, contents are empty from &lt;visual id='.$this->visual_id.'&gt; '.$dir.$file.'',
				'file'	=> false
			);
			return false;	
		}
		$nomove = false;
		$old_contents = $contents; 
		$explode = '<visual id='.$this->visual_id.'>';
		if (!strstr($contents,$explode)) {
			$explode = '<visual class="a-visual_nomove" id="'.$this->visual_id.'">';
			$nomove = true;
			if (!strstr($contents,$explode)) {
				if (preg_match('/(<visual\s+(class="a\-visual_nomove"\s)?id="'.preg_quote($this->visual_id,'/').'"\s*>)/i',$m)) {
					$explode = $m[1];
					$nomove = $m[2] ? true : false;
				}
				if (!$explode) {
					$this->json_ret = array(
						'html'	=> 'Fatal error, cannot find this tag: &lt;visual id='.$this->visual_id.'&gt; in '.$dir.$file.'',
						'file'	=> false
					);
					return false;
				}
			}
		}
		$lines = explode("\n",$contents);
		foreach ($lines as $line => $str) if (strstr($str,$explode)) break;
		$last = $lines[count($lines)-1];
		$last = substr($last,0,strpos($last,'<'));
		$tabs = count(explode("\t",$last)) - 1;
		$contents = Parser::getTagAttrContents($explode,'visual',$contents,true);
		$fucked = false;
		if ($old_contents && !$contents) {
			$fucked = true;
			$contents = $old_contents;
			$line = 1;
		}
		if ($clean) {
			$contents = $this->Index->Edit->unvisual($contents);
			$contents = Parser::strTabs($contents);
		}
		$data = array(
			'html'	=> $contents,
			'dir'	=> $dir,
			'line'	=> $line,
			'tabs'	=> $tabs,
			'file'	=> $file,
			'nomove'=> $nomove,
			'fucked'=> $fucked,
			'id' 	=> $this->visual_id
		);
		if ($ret) {
			$data['contents'] = $old_contents;
			return $data;	
		} else {
			$this->json_ret = $data;
			return true;
		}
	}
	

	

	
	protected function designEdit() {
		$this->allow('visual','move');
		if (!$_POST['to'] || !$_POST['current'] || !$_POST['ul']) return array('type'=>'error','$_POST data is missing','delay'=>2000);
		$dir = FTP_DIR_TPL;
		$n = "\n";
		$ul = $_POST['ul'];
		$current_file = $dir.$_POST['current']['file'];
		$current_id = $_POST['current']['id'];
		if (!is_file($current_file)) return array('type'=>'error','File '.$current_file.' is missing (current)','delay'=>2000);
		$current_html = file_get_contents($current_file);
		if (!preg_match('/\{li\sname=\''.preg_quote($current_id,'/').'\'\}([\s\S]+)\{\/li\}/U',$current_html,$match)) {
			return array('type'=>'error','text'=>'Cannot match {li name=\''.current_id.'\'} ... {/li} (current)','delay'=>2000);
		}
		$current_html = str_replace($match[0],'',$current_html);
		file_put_contents($current_file, $current_html);
		$current_block = '{li name=\''.$current_id.'\'}'.$n.trim($match[1]).$n.'{/li}';
		$current_id = $_POST['current']['id'];
		if ($_POST['to']['prev']) $key = 'prev';
		elseif ($_POST['to']['next']) $key = 'next';
		else return array('type'=>'error','text'=>'Prev/To is missing','delay'=>2000);
		$to_file = $dir.$_POST['to'][$key]['file'];
		if (!is_file($to_file)) return array('type'=>'error','text'=>'File '.$to_file.' is missing (next)','delay'=>2000);
		$to_html = file_get_contents($to_file);
		$to_id = $_POST['to'][$key]['id'];
		preg_match('/\{ul name=\''.preg_quote($ul,'/').'\'\}([\s\S]+)?\{\/ul\}/U',$to_html, $_match);
		if (strlen(trim($_match[1]))) {
			if ($key=='next') $replace = $current_block.'\\1';
			else $replace = '\\1'.$current_block;
			$to_html = preg_replace('/(\{li\sname=\''.preg_quote($to_id,'/').'\'\}([\s\S]+)\{\/li\})/U',$replace,$to_html);
		} else {
			$to_html = preg_replace('/(\{ul\sname=\''.preg_quote($to_id,'/').'\'\})/U','\\1'.$current_block,$to_html);
		}
		file_put_contents($to_file, $to_html);
		$this->json_ret = array('ok'=>true);
	}

	/*
	private function _designEdit() {
		if (!$_POST['to'] || !$_POST['current']) return array('type'=>'error','$_POST data is missing');
		$dir = FTP_DIR_TPL;
		$n = "\n";
		$current_file = $dir.$_POST['current']['file'];
		$current_id = $_POST['current']['id'];
		if (!is_file($current_file)) return array('type'=>'error','File '.$current_file.' is missing (current)');
		$current_html = file_get_contents($current_file);
		if (!preg_match('/\{div\sname=\''.preg_quote($current_id).'\'\}([\s\S]+)\{\/div\}/U',$current_html,$match)) {
			return array('type'=>'error','text'=>'Cannot match {div name=\''.current_id.'\'} ... {/div} (current)');
		}		
		// clear block
		if ($_POST['current']['is_single']=='true') {
			$clear_html = '{div name=\''.$current_id.'_old\'}'.$n.'{/div}';
		} else {
			$clear_hmtl = '';
			$clear_html = '{div name=\''.$current_id.'_old\'}'.$n.'{/div}';
		}
		$clear_regex = '/\{div\sname=\''.preg_quote($current_id).'_old\'\}\s*\{\/div\}/U';
		
		$current_html = preg_replace($clear_regex,'',$current_html);
		$current_html = str_replace($match[0],$clear_html,$current_html);
		
		file_put_contents($current_file, $current_html);
		// new block
		$current_block = '{div name=\''.$current_id.'\'}'.$n.trim($match[1]).$n.'{/div}';
		
		$current_id = $_POST['current']['id'];
		if ($_POST['to']['prev']) $key = 'prev';
		elseif ($_POST['to']['next']) $key = 'next';
		else {
			return false;	
		}
		$to_file = $dir.$_POST['to'][$key]['file'];
		if (!is_file($to_file)) return array('type'=>'error','text'=>'File '.$to_file.' is missing (next)');
		$to_html = file_get_contents($to_file);
		
		//if ($to_file!=$current_file) $to_html = preg_replace($clear_regex,'',$to_html);
		
		$to_id = $_POST['to'][$key]['id'];
		if ($key=='next') $replace = $current_block.'\\1';
		else $replace = '\\1'.$current_block;
		if (strstr($to_html, '{div name=\''.$to_id.'\'}')) {
			$to_html = preg_replace('/(\{div\sname=\''.preg_quote($to_id).'\'\}([\s\S]+)\{\/div\})/U',$replace,$to_html);
		} else {
			$to_html = preg_replace('/(\{div\sname=\''.preg_quote($to_id).'_old\'\}([\s\S]+)\{\/div\})/U',$replace,$to_html);	
		}
		file_put_contents($to_file, $to_html);
		return array('div'=>$to_id);
	}
	*/
	
	protected function link($table, $id) {
		$table = DB::remPrefix($table);
		if (substr($table,0,5)=='grid_') {
			$module = substr($table,5);
			$this->json_ret = array(
				'js'	=> 'S.A.W.open(\'?'.URL_KEY_ADMIN.'=grid&'.self::KEY_MODULE.'='.$module.'&id='.$id.'\');'
			);	
		}
		elseif (substr($table,0,8)=='content_') {
			$module = substr($table,8);
			$this->json_ret = array(
				'js'	=> 'S.A.W.open(\'?'.URL_KEY_ADMIN.'=content&'.self::KEY_MODULE.'='.$module.'&id='.$id.'\');'
			);	
		} else {
			$this->json_ret = array(
				'js'	=> 'S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$table.'&id='.$id.'\');'
			);
		}
	}
	
	
	protected function classParams() {
		return array(
			'lang'			=> $this->lang,
			'table'			=> $this->table,
			'prefix'		=> $this->prefix,
			'tpl'			=> $this->tpl
		);
	}
	protected function uploadHash() {
		$this->upload = Site::genHash($this->id, str_replace('_tab','',$this->name), UPLOAD_SECONDS);
		
	}
	
	public function admin_edit() {
		if (SITE_TYPE!='index') return;
		if (!isset($_SESSION['VISUAL_FILES'])) $_SESSION['VISUAL_FILES'] = array();
		$js = 'var Conf={'.jsConfig($this->Index->conf).'};$(function(){S.A.Conf='.json($this->admin_conf()).';S.A.Lang='.json($this->admin_lang()).';'.(!ADMIN?'S.A.E.init({visual:'.(($_SESSION['AdminGlobal']['visual'] && SITE_TYPE=='index') ? 'true' : 'false').', template:\''.$this->tpl.'\',templates:'.json($this->editorTemplates()).($_SESSION['AdminGlobal']['visual']?',visual_files:'.json($_SESSION['VISUAL_FILES']):'').'})':'S.A.L.ready(\'\','.(SITE_TYPE=='index'?'true':'false').');').'});';
		
		$this->Index->addJScode($js);	
	}
	public function admin_conf() {
		return array(
			'open_in_popup'	=> WIN_OPEN_IN_POPUP,
			'lang'			=> $this->lang,
			'dir_files'		=> DIR_FILES,
			'crm'			=> is_file(FTP_DIR_TPL.'classes/MyCrm.php'),
			'jquery_css'	=> JQUERY_CSS,
			'editor'		=> WYSIWYG,
			'visual'		=> defined('VISUAL_TAGS') && VISUAL_TAGS && GROUP_ID!=3
		);
	}
	public function admin_lang() {
		return array(
			'website'				=> lang('_$Website'),
			'image_dc'				=> lang('_$Are you sure to delete this image'),
			'set_as_main'			=> lang('_$Set as main'),
			'preview'				=> lang('_$Preview'),
			'del'					=> lang('_$Delete'),
			'properties'			=> lang('_$Properties'),
			
			'content_dc'			=> lang('_$Are you sure to delete this content'),
			'subc'					=> lang('_$and all sub contents in it'),
			'image_dc'				=> lang('_$Are you sure to delete this image'),
			'entry_dc'				=> lang('_$Are you sure to delete this entry?'),
			'visit_dc'				=> lang('_$Are you sure to delete this visit?'),
			'ip_dc'					=> lang('_$Are you sure to delete this block with ip'),
			'ip_bc'					=> lang('_$Are you sure to block this ip'),
			'ip_uc'					=> lang('_$Are you sure to unblock this ip'),
			'db_dc'					=> lang('_$Are you sure to delete this database value with key'),	
			'set_as_main'			=> lang('_$Set as main'),
			'del'					=> lang('_$Delete'),
			'dc'					=> lang('_$Unpublish'),
			'properties'			=> lang('_$Properties'),
			'thumbnail'				=> lang('_$Thumbnail'),
			'original'				=> lang('_$Original'),
			'window_data_empty'		=> lang('_$Window data is empty on'),
			'preview'				=> lang('_$Preview'),
			'close'					=> lang('_$Close'),
			'cancel'				=> lang('_$Cancel'),
			'save'					=> lang('_$Save'),
			'new_folder'			=> lang('_$New folder name'),
			'and_move'				=> lang('_$and move'),
			'admin'					=> lang('_$Admin'),
			'edit'					=> lang('_$Edit'),
			'visual'				=> lang('_$Visual'),
			'commit'				=> lang('_$Commit'),
			'discard'				=> lang('_$Discard'),
			'qe_lang'				=> lang('_$Quick edit this language'),
			'qe_entry'				=> lang('_$Quick edit this entry'),
			'fe_entry'				=> lang('_$Full edit this entry'),
			'e_snippet'				=> lang('_$Edit this snippet'),
			'e_thtml'				=> lang('_$Edit this template HTML'),
			'p_thtml'				=> lang('_$Preview template HTML'),
			'p_ghtml'				=> lang('_$Preview generated HTML'),
			'm_ebefore'				=> lang('_$Move this element before'),
			'm_eafter'				=> lang('_$Move this element after'),
			'a_ea'					=> lang('_$Add element above'),
			'a_eb'					=> lang('_$Add element below'),
			'd_e'					=> lang('_$Delete this element'),
			'html_gp'				=> lang('_$HTML generated preview'),
			'html_pf'				=> lang('_$HTML part preview from'),
			'move_t1'				=> lang('_$Are you sure to move green element after yellow?<br />You will see the preview after `OK` button'),
			'move_s1'				=> lang('_$Element move confirm'),
			'move_t2'				=> lang('_$Click `OK` to save this.<br /><br />Click `Cancel` to recover.<hr />After all do not forget to `Commit` changes!'),
			'move_s2'				=> lang('_$Are you happy with changes?'),
			'ec_del'				=> lang('_$Are you sure to delete this element?'),
			'ect_del'				=> lang('_$Element removal confirmation dialog'),
			'f_e'					=> lang('_$Full edit'),
			'q_e'					=> lang('_$Quick edit'),
			'pd_e'					=> lang('_$Edit page details'),
			'n_em'					=> lang('_$Add new entry'),
			'n_et'					=> lang('_$Add new entry to'),
			'n_pm'					=> lang('_$Add new page'),
			'n_psm'					=> lang('_$Add new page to the same menu'),
			'a_nmp'					=> lang('_$Add another module to this page'),
			'a_nm'					=> lang('_$Add new menu')
		);
	}	
}





abstract class Admin extends AdminObject {
	
	
	protected function __construct($class) {
		parent::__construct($class);
	}
	
	public static function start() {
		
		if (SITE_TYPE=='upload') {
			$ex = explode('.',get('upload'));
			$_GET['id'] = $ex[2];
			$key = $ex[1];
		} else {
			$key = get(URL_KEY_ADMIN,'','Main');	
		}
		$key = ucfirst(strtolower($key));
		
		if (substr($key,-4)=='_tab') {
			$ex = explode('_',$key);
			array_pop($ex);
			$tab = $ex[count($ex)-1];
			array_pop($ex);
			$key = join('_',$ex);
		} else {
			$tab = request('tab');	
		}
		
		$key = str_replace('_tab','',$key);
		$_key = $key;

		if (substr($key,0,5)=='Grid_') {
			if (!get(Admin::KEY_MODULE)) $_GET[Admin::KEY_MODULE] = substr($key,5);
			$key = 'Grid';
		}
		
		$tpl = (@$_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN] ? $_SESSION[URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN] : TEMPLATE);
		if ($tpl!=TEMPLATE) {
			$prefix = DB::one('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\' AND template=\''.$tpl.'\'');
			DB::setPrefix($prefix);
		}
		$module = false;
		if ($key=='Grid') {
			$module = request(Admin::KEY_MODULE,'','[[:CACHE:]]');
			$modules = Site::getModules('grid', true);
			if (!$module) {
				$module = key($modules);
			}
			elseif (!array_key_exists($module, $modules)) {
				$module = key($modules);
			}
			if (!$module) {
				Message::halt('No grid modules installed','Please install at least one');	
			}
			require FTP_DIR_ROOT.'mod/AdminGrid.php';
			$key = 'Grid_'.$module;
		}
		/*
		elseif ($key=='Content' && get(Admin::KEY_MODULE) && get('exact')=='true') {
			$module = get(Admin::KEY_MODULE);
			$modules = Site::getModules('content', true, (sget(URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN) ? sget(URL_KEY_TEMPLATE.'_'.URL_KEY_ADMIN) : TEMPLATE));
			if (!$module) {
				$module = key($modules);
			}
			elseif (!array_key_exists($module, $modules)) {
				$module = key($modules);
			}
			$key = 'Content_'.$module;
		}
		*/
		$key_tabs = array('Mods','Settings','Stats','Grid','Content');
		if (!$key) $key = 'Main';
		
		
		if (!in_array($key,$key_tabs)) {
			if (is_file(FTP_DIR_TPLS.$tpl.'/admin/Admin'.$key.'.php')) {
				$file = FTP_DIR_TPLS.$tpl.'/admin/Admin'.$key.'.php';
			}
			elseif (is_file(FTP_DIR_TPL.'admin/Admin'.$key.'.php')) {
				$file = FTP_DIR_TPL.'admin/Admin'.$key.'.php';
			}
			elseif (is_file(FTP_DIR_ROOT.'mod/Admin'.$key.'.php')) {
				$file = FTP_DIR_ROOT.'mod/Admin'.$key.'.php';
			}
			/*
			elseif (is_file(FTP_DIR_ROOT.'inc/Admin'.$key.'.php')) {
				$file = FTP_DIR_ROOT.'inc/Admin'.$key.'.php';
			}
			*/
			elseif ($module) {
				$className = 'AdminGrid';
				return new $className();
			}
			elseif ($key=='Grid') {
				exit;	
			}
			else {
				$key = 'Main';
				if (is_file(FTP_DIR_TPLS.$tpl.'/admin/Admin'.$key.'.php')) {
					FTP_DIR_TPL.'admin/Admin'.$key.'.php';
				} else {
					$file = FTP_DIR_ROOT.'mod/Admin'.$key.'.php';
				}
			}
		} else {
			if (is_file(FTP_DIR_TPLS.$tpl.'/admin/Admin'.$key.'.php')) {
				FTP_DIR_TPLS.$tpl.'/admin/Admin'.$key.'.php';
			} else {
				$file = FTP_DIR_ROOT.'mod/Admin'.$key.'.php';
			}
		}

		if ($file) require $file;
		if ($tab) {
			$orig_key = $key;
			$key .= '_'.$tab;
		}
		$className = 'Admin'.$key;
		
		if (class_exists($className)) {
			return new $className();
		} else {
			if ($tab) {
				$className = 'Admin'.$orig_key;
				return new $className();
			}
			Message::Halt('System error','Cannot call class <em>'.$className.'</em>, in file <em>./mod/admin/Admin'.$key.'.php</em>',Debug::backTrace());	
		}
	}
	
	protected function genHead() {
		
		$this->show['header'] = false;
		$this->show['footer'] = false;
		$this->ui();
		$add_js_css = true;
		switch (SITE_TYPE) {
			case 'window':
				if ($this->pop) {
					$this->show['html'] = true;
					$this->show['header'] = true;
					$this->show['footer'] = true;
				} else {
					return;
				}
			break;
			case 'ajax':
			case 'json';
				// do not show header and footer
				$add_js_css = false;
				$this->show['html'] = false;
				$this->show['header'] = false;
				$this->show['footer'] = false;
				return;
			break;
			case 'print':
				$this->show['html'] = true;
				$this->show['header'] = false;
				$this->show['footer'] = false;
				return;
			break;
			case 'popup':
				$this->show['html'] = true;
				$this->show['header'] = false;
				$this->show['footer'] = false;
			break;
			default:
				$this->show['html'] = true;
				$this->show['header'] = true;
				$this->show['footer'] = true;
			break;
		}
		$test = 0;

		
		
		
		if ($add_js_css) {		
			$this->Index->addJSA('jquery/'.JQUERY);
			if (defined('JQUERY_MIGRATE') && JQUERY_MIGRATE) $this->Index->addJSA('jquery/'.JQUERY_MIGRATE);
			$this->Index->addJSA('jquery/'.JQUERY_UI);
			if (SITE_TYPE!='window' || get('popup')) {
				$this->Index->addJSA('plugins/jquery.blockUI.js');
				$this->Index->addJSA('swfobject.js');
				$this->Index->addJSA('uploadify/'.JQUERY_UPLOADIFY);
				switch (WYSIWYG) {
					case 'ckeditor':
						$this->Index->addJSA('ckeditor/ckeditor.js');	
					break;
					default:
						$this->Index->addJSA(WYSIWYG.'/jquery.tinymce.js');	
				}
				
			}
			$this->Index->addJSA('plugins/jquery.colorbox.js');
			$this->Index->addCSSA('colorbox/'.JQUERY_COLORBOX.'/colorbox.css');
			$this->Index->addJSA('global.js');
			$this->Index->addJSA('admin.js');
		//	$this->Index->addJSA(FTP_EXT.'?js&amp;'.$this->Index->Session->Lang.'&amp;'.URL_KEY_ADMIN.'=true'.($this->pop?'&amp;popup=1':''),'', true);
			/*
			if (FILE_BROWSER) {
				$this->Index->addJSA('plugins/browser/browser.js');
			//	$this->Index->addJSA('plugins/browser/plugins/fileeditor/codemirror/js/codemirror.js');
			}
		//	$this->Index->addJSA('edit_area/edit_area_compressor.php?plugins');
			*/
			if (!@$this->current['UI_ADMIN'] && defined('UI_ADMIN') && UI_ADMIN) $this->current['UI_ADMIN'] = UI_ADMIN;
			elseif (!@$this->current['UI_ADMIN']) $this->current['UI_ADMIN'] = 'dark-hive';
			$this->Index->addCSSA('ui/'.($_SESSION['UI_ADMIN']?$_SESSION['UI_ADMIN']:$this->current['UI_ADMIN']).'/'.JQUERY_CSS,' id="s-theme_admin"');
			$this->Index->addCSSA('admin_'.TEMPLATE_ADMIN.'.css');
			$this->Index->addCSSA('edit_'.TEMPLATE_ADMIN.'.css');
			$this->Index->addCSSA('global.css');
			if (!$this->pop && defined('USE_IM') && USE_IM) Factory::call('im')->html('init');
			$this->admin_edit();
		}
	}
	
	

	public function isReserved($name) {
		$name = trim($name);
		if (strlen($name)==2) return true;
		if (in_array($name, Conf()->g('site_types'))) return true;
		if (in_array($name, array(URL_KEY_REFERAL,URL_KEY_DO,URL_KEY_REMEMBER,URL_KEY_CONTENT,URL_KEY_ADMIN,URL_KEY_EMAIL,URL_KEY_LIMIT,URL_KEY_PAGE,URL_KEY_CATID,URL_KEY_ACTION,URL_KEY_PROD_OPTION,URL_KEY_REGION,URL_KEY_CURRENCY,URL_KEY_LANG,URL_KEY_TEMPLATE_ADMIN))) return true;
		if (in_array($name, Index()->My->reservedWords())) return true;
		/*
		if (in_array($name, Site::$keys['Reserved'])) return true;
		if (in_array($name, Site::$keys['skipURL'])) return true;
		if (in_array($name, Site::$keys['Numbers'])) return true;
		if (in_array($name, Site::$keys['Months'])) return true;
		if (in_array($name, Site::$keys['Mo'])) return true;
		*/
		return false;
	}
	
	public function export_sql($sql, $table = false) {
		if (strstr(strtoupper($sql),'LIMIT') || $table=='SQL') {
			$this->offset = 0;
			$this->limit = 0;
		}
		$name = $table.($this->limit?'['.$this->offset.'-'.$this->limit.']':'').'_'.date('dmy');
		$file = FTP_DIR_FILES.'temp/'.$name.'.sql';
		if (is_file($file)) unlink($file);
		$fp = fopen($file,'w');
		$qry = DB::qry($sql,$this->offset,$this->limit);
		while($row = DB::fetch($qry)) {
			$insert = 'INSERT INTO `'.$table.'` VALUES (';
			$in = array();
			$i = $id = false;
			foreach ($row as $key => $val) {
				if (!isset($val)) $in[] = '\'\'';
				else $in[] = '\''.str_replace('\"','"',e($val, false)).'\'';
				$i = true;
			}
			if ($i) $insert .= join(',',$in).')'.";\r\n";
			fwrite($fp,$insert);
		}
		DB::free($qry);
		fclose($fp);
		return HTTP_DIR_FILES.'temp/'.$name.'.sql';
	}
	
	public function export_csv($sql = '',$table = false,$cols = array(), $write_names = true) {
		if (strstr(strtoupper($sql),'LIMIT') || $table=='SQL') {
			$this->offset = 0;
			$this->limit = 0;
		}
		$name = $table.($this->limit?'['.$this->offset.'-'.$this->limit.']':'').'_'.date('dmy');
		if (!$sql) {
			$select = '*';
			if ($cols) $select = join(', ',$cols);
			$sql = 'SELECT * FROM '.$table.' ORDER BY id';
		}
		$sql = str_replace('SQL_CALC_FOUND_ROWS ','',$sql);;
		
		$file = FTP_DIR_FILES.'temp/'.$name.'.csv';
		if (is_file($file)) unlink($file);
		$fp = fopen($file,'w');
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$i = 0;
		while ($rs = DB::fetch($qry)) {
			if (!$i) {
				if ($write_names) fwrite($fp,join("\t",array_keys($rs))."\r\n");
			}
			fwrite($fp,join("\t",$rs)."\r\n");
			$i++;
		}
		DB::free($qry);
		fclose($fp);
		return HTTP_DIR_FILES.'temp/'.$name.'.csv';
	}
	
	public function export_excel($sql = '', $table = false, $cols = array()) {
		require FTP_DIR_ROOT.'inc/lib/PHPExcel.php';
		set_include_path(FTP_DIR_ROOT.'inc/lib/PHPExcel/');

		$pExcel = new PHPExcel();
		$pExcel->setActiveSheetIndex(0);
		$aSheet = $pExcel->getActiveSheet();
		$aSheet->setTitle('Export');
		
		if (strstr(strtoupper($sql),'LIMIT') || $table=='SQL') {
			$this->offset = 0;
			$this->limit = 0;
		}
		$name = $table.($this->limit?'['.$this->offset.'-'.$this->limit.']':'').'_'.date('dmy');
		if (!$sql) {
			$select = '*';
			if ($cols) $select = join(', ',$cols);
			$sql = 'SELECT * FROM '.$table.' ORDER BY id';
		}
		$sql = str_replace('SQL_CALC_FOUND_ROWS ','',$sql);;
		
		$file = FTP_DIR_FILES.'temp/'.$name.'.xls';
		if (is_file($file)) unlink($file);
		$alpha = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$qry = DB::qry($sql,$this->offset,$this->limit);
		
		$i = 1;
		
		while ($rs = DB::fetch($qry)) {
			
			$j = 0;
			if ($i==1) {
				foreach ($rs as $k => $v) {
					$aSheet->getColumnDimension($alpha[$j])->setWidth(25);
					$aSheet->setCellValue(strval($alpha[$j].$i),(string)$k);
					if ($j>23) break;
					$j++;
				}
				$i++;
			}
			$j = 0;
			
			foreach ($rs as $k => $v) {
				$aSheet->setCellValue(strval($alpha[$j].$i),(string)$v);
				if ($j>23) break;
				$j++;
			}
			$i++;
		}
		DB::free($qry);
		require 'Writer/Excel5.php';
		$objWriter = new PHPExcel_Writer_Excel5($pExcel);
		$objWriter->save($file);
		restore_include_path();
		return HTTP_DIR_FILES.'temp/'.$name.'.xls';
	}
	
	

	

	public function index() {
		switch (SITE_TYPE) {
			
			case 'download':
				$file_path = get('download');
				if (strstr($file_path,':root:/')) {
					$file_path = str_replace(':root:','..',$file_path);	
				}
				if ($ret = Allow::getInstance()->admin('download','file',$file_path)) die('<script>window.parent.S.G.msg({text:\''.$ret['text'].'\',type:\'shield\',delay:2000});</script>');
				File::download($file_path, true);
			break;
			case 'window':
				Site::header('Content-Type: text/html; charset=utf-8'); 
				ob_start(array('OB','handler'));
				if ($this->pop) {
					$this->tpl('_html');
					echo '<div id="center-area" class="admin-area a-pop">';
				}
				
				$this->window();
				
				if ($this->pop) {
					echo '</div></body></html>';
				}
				ob_end_flush();
			break;
			case 'print':
				Site::header('Content-Type: text/html; charset=utf-8'); 
				ob_start(array('OB','handler'));
				$this->tpl('_html_print');
				echo '<div id="center-area" class="admin-area">';
				$this->printed();
				echo '</div></body></html>';
				ob_end_flush();
			break;
			case 'json':
				if (headers_sent()) throw new Exception('Nothing shall be passed before JSON output!');
				Site::header('Content-Type: text/json; charset=utf-8'); 
				ob_start();
				$this->json();
				echo json($this->json_ret());
				ob_end_flush();
				$this->exit = true;
				exit;
			break;
			case 'upload':
				Site::header('Content-Type: text/plain; charset=utf-8'); 
				if (!$this->id) $this->id = 0;
				if ($this->id!=Site::$upload[0] || $this->name!=Site::$upload[1]) {
					die('{text:\'Illegal access. Incorrect arguments\',delay:10000,type:\'stop\'}');
				}
				$this->upload();
				if ($this->msg_text) {
					echo json($this->json_ret());
				}
				$this->exit = true;
				exit;
			break;
			default:
				Site::header('Content-Type: text/html; charset=utf-8');
				if ($this->name=='cron' && get('do')) {
					$this->content();
				} else {
					ob_start(($this->Index->My->OBparse()?array('OB','handler'):false));
					$this->content();
					ob_end_flush();
				}
			break;
		}
	}
	

	protected function action($action = false) {
		if ($action) $this->action = $action;
		/*
		if ($this->tab_class) {
			$o = new $this->tab_class($this);
			return $o->action();
		}
		*/
		switch ($this->action) {
			case 'save':
				$this->global_action('validate');
				$this->global_action('save_content');
				$this->global_action('msg');
			break;
			case 'edit':
				$this->global_action('edit');
			break;
			case 'add':
				if (!$this->id) break;
				$this->action('save');
				$this->msg_close = true;
				$this->msg_reload = false;
				$this->msg_text = '';
				$this->msg_js = 'S.A.W.go(\'?'.URL_KEY_ADMIN.'='.$this->name.'&'.self::KEY_CONID.'='.$this->conid.'\');';
			break;
			case 'copy':
				if (!$this->id) break;
				$this->action('save');
			break;
			case 'delete':
				$this->global_action('delete_content');
			break;
		}
	}
	
	/*
	public function __destruct() {
		if ($this->exit) return false;
		
		if ($this->exit) return false;
		if (SITE_TYPE=='json') {
			if ($this->json_exit) return '';
			$ret = $this->json_ret();
			if ($ret) {
				Site::header('Content-Type: text/json; charset=utf-8'); 
				echo json($ret);
				$this->exit = true;
				exit;
			}
		}
		elseif ((SITE_TYPE=='window' || SITE_TYPE=='ajax') && $this->msg_text) {
			if ($this->json_exit) return '';
			$ret = $this->json_ret();
			if (isset($ret['text'])) {
				echo '<script type="text/javascript">var data='.json($ret).';S.A.W.dialog(data);</script>';
			}
		}
	}
	*/
	
	protected function init() {
		
	}
}


class AdminEdit extends AdminObject {
	public function __construct() {
		parent::__construct(__CLASS__, true);
		$this->getTemplate();
		$this->getLang();
	}
}


final class AjaxelUpdate extends stdClass {
	private $local_file;
	private $remote_file;
	public $status = '';
	
	public function update() {
		if (!$this->Version) return $this;
		$f = 'http://ajaxel.com/update_files/sql.php.txt';

		if (($c = trim(File::url($f))) && substr($c,0,2)=='<?') {
			Conf()->s('SQL_RESULT',array());
			$file = FTP_DIR_ROOT.'files/temp/tables_'.$this->Version.'.php';
			file_put_contents($file,$c);
			require FTP_DIR_ROOT.'inc/DBfunc.php';
			$sqls = require $file;
			DBfunc::installTables($sqls, false, true);
			DB::resetCache();
		}
		return $this;
	}
	
	public function _update() {
		$f = 'http://ajaxel.com/update_files/sql.txt';
		$sql = File::url($f);
		if ($sql) {
			require FTP_DIR_ROOT.'inc/DBfunc.php';
			Conf()->s('SQL_RESULT',array());
			Conf()->s('DB_MSG','');
			$er = error_reporting();
			error_reporting(0);
			ob_start();
			DBmore::parseSqlString($sql,true);
			$_c = ob_get_contents();
			ob_end_clean();
			error_reporting($er);
			$this->status = DB::errorMSG();
		}
		return $this;
	}
	public function getFile() {
		return $this->local_file;
	}
	public function get() {
		if (!$this->File) {
			$update = self::getUpdate();
			$this->File = $update->File;
		}
		$this->remote_file = 'http://ajaxel.com/update_files/'.$this->File;
		$this->local_file = FTP_DIR_ROOT.'AjaxelCMS_update_v'.$this->Version.'.zip';
		return $this;	
	}
	public static function getUpdate() {
		$f = 'http://ajaxel.com/update_files/version.txt';
		if (($c = File::url($f)) && substr(trim($c),0,8)=='Version:') {
			$ex = explode("\n",str_replace("\r",'',$c));
			$r = new AjaxelUpdate;
			foreach ($ex as $e) {
				$_ex = explode(':',$e);
				$v = trim($_ex[0]);
				$r->$v = trim($_ex[1]);
			}
			$r->File = 'AjaxelCMS_update_v'.$r->Version.'.zip';
			return $r;
		}
		return false;
	}
	public function download() {
		if (!$this->remote_file || !$this->local_file) return false;
		copy($this->remote_file,$this->local_file);
		return $this;
	}
	
}