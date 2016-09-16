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
* @file       mod/Data.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/



abstract class Data {
	
	private static
		$cache = array('user'=>array(),'get_user'=>array()),
		$only = array(),
		$link = array()
	;
	
	/*
	* Links configuration
	*/
	public static function link($type, $set = false) {
		
		if ($set) self::$link[$type] = $set;
		elseif (isset(self::$link[$type])) return self::$link[$type];
		
		$http = HTTP_BASE;
		if (!SSL_TO_NORMAL && URL::is_ssl()) {
			//$http = str_replace('http://','https://',$http);	
		}
		$http = '';
		switch ($type) {
			case 'user_logged':
			//	return '/';
				if (Session()->login_cookie) return false;
				elseif (request(URL_KEY_JUMP)) return URL::ht($http.request(URL_KEY_JUMP));
				else {
					if (url(0)=='user' && isset($_GET['login'])) {
						return $http;	
					}
					return URL::ht($http.'?'.URL::get(URL_KEY_LOGIN,URL_KEY_PASSWORD));
				}
			break;
			case 'admin_logged':
				if (Session()->login_cookie) return false;
				elseif (request(URL_KEY_JUMP)) return URL::ht($http.request(URL_KEY_JUMP));
				else return URL::ht($http.'?'.URL_KEY_ADMIN);
			break;
			case 'user_registered':
				return $http;
			break;
			case 'user_updated':
				return false;
			break;
			case 'logged_redirect':
				if (Session()->login_cookie) return false;
				elseif (request(URL_KEY_JUMP)) return URL::ht($http.request(URL_KEY_JUMP));
				else return $http;
			break;
			default:
				return false;
			break;
		}	
	}
	
	/*
	* System emails links
	*/
	public static function mailData($name, $rs) {
		$ret = array();
		switch ($name) {
			case 'user_confirm':
			case 'company_registered':
				$ret = array(
					'link'	=> HTTP_BASE.ltrim(URL::ht('?'.URL_KEY_EMAIL_CONFIRM.'='.$rs['id'].'.'.$rs['code'].AMP.URL_KEY_JUMP.'='.urlencode($rs['profile']['referer'])),'/')
				);
			break;
			case 'user_lostpass':
				$ret = array(
					'link'	=> HTTP_BASE.ltrim(URL::ht('?user'.AMP.'lostpass'.AMP.$rs['id'].AMP.'code='.$rs['code']),'/')
				);
			break;
			case 'user_registered':
				$ret = array(
					'link'	=> HTTP_BASE.ltrim(URL::ht('?'.URL_KEY_EMAIL_CONFIRM.'='.$rs['id'].'.'.$rs['code']),'/')
				);
			break;
			case 'user_updated':
			
			break;	
		}
		return $ret;
	}
	
	/*
	* Old function of array_label()
	* Getting an index from 2 level array and combining
	*/
	/*
	public static function arrayLabelize($arr, $k = 0) {
		return array_label($arr, $k);
	}
	*/
	
	/*
	* Getting something small
	*/
	/*
	public static function getString($type) {
		switch ($type) {
			case 'my_city':
				return Session()->City;
			break;
		}
	}
	*/
	/*
	* Getting some arrays from database, mostly user's functions
	*/
	public static function DB($type, $forID = 0, $arg2 = 0, $arg3 = '') {
		if (strtolower(substr($type,0,3))=='my:') {
			return Index()->My->DB(substr($type,3), $forID, $arg2, $arg3);
		}
		$ret = '';
		switch ($type) {
			/*
			case 'online_users':
				return DB::one('SELECT SQL_SMALL_RESULT COUNT(1) FROM '.DB_PREFIX.'users');
			break;
			case 'city_users':
				return DB::one('SELECT SQL_SMALL_RESULT COUNT(1) FROM '.DB_PREFIX.'users_profile WHERE country='.e(Session()->Country).' AND city='.e(Session()->City).'');
			break;			
			case 'wall':
				break;
				$ret = DB::getAll('SELECT *, (SELECT up.firstname FROM '.DB_PREFIX.'users_profile up WHERE up.setid=from_user) AS firstname, (SELECT login FROM '.DB_PREFIX.'users WHERE id=from_user) AS login FROM '.DB::getPrefix().'wall WHERE to_user='.(int)$forID.' ORDER BY added DESC LIMIT 10');
			break;
			case 'friends':
				break; // have no friends table yet
				$ret = DB::getAll('SELECT u.id, u.login, p.firstname, '.DB::sqlGetString('age','p.dob').' AS age, p.city, (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=u.id LIMIT 1) AS online FROM '.DB::getPrefix().'friends f LEFT JOIN '.DB_PREFIX.'users u ON u.id=f.to_user INNER JOIN '.DB_PREFIX.'users_profile p ON p.setid=f.to_user WHERE f.from_user='.$forID.($arg2?' AND f.to_user='.$arg2:'').' ORDER BY f.added DESC LIMIT 10');
			break;
			case 'front_users':
				break;
				$ret = DB::getAll('SELECT u.id, u.login, p.firstname, '.DB::sqlGetString('age','p.dob').' AS age, p.city, (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=u.id LIMIT 1) AS online FROM '.DB_PREFIX.'users u INNER JOIN '.DB_PREFIX.'users_profile p ON p.setid=u.id ORDER BY RAND() LIMIT 20');
			break;
			
			case 'banners':
				if (!$forID) $forID = 'banners';
				$ret = DB::getAll('SELECT a.id, a.rid, a.title, a.url, a.descr, (CASE WHEN a.main_photo!=\'\' THEN a.main_photo ELSE (SELECT b.main_photo FROM '.prefix().'content_banner b WHERE b.id!=a.id AND b.rid=a.rid AND b.main_photo!=\'\' LIMIT 1) END) AS main_photo FROM '.prefix().'content_banner a WHERE a.setid=(SELECT c.id FROM '.prefix().'content c WHERE c.name='.e($forID).' AND c.active=1) AND a.lang=\''.Session()->Lang.'\' AND a.active=1 HAVING main_photo!=\'\' OR a.descr ORDER BY sort');
			break;
			case 'list':
				$ret = DB::getAll('SELECT a.id, a.rid, a.title,  a.descr, (CASE WHEN a.main_photo!=\'\' THEN a.main_photo ELSE (SELECT b.main_photo FROM '.prefix().'content_'.$forID.' b WHERE b.id!=a.id AND b.rid=a.rid AND b.main_photo!=\'\' LIMIT 1) END) AS main_photo FROM '.prefix().'content_'.$forID.' a WHERE a.setid=(SELECT c.id FROM '.prefix().'content c WHERE '.($arg2?'c.name='.e($arg2).' AND ':'').'c.active=1) AND a.lang=\''.Session()->Lang.'\' AND a.active=1 ORDER BY sort');
			break;
			case 'resent_comments':
				$ret = array();
				$qry = DB::qry('SELECT a.id, a.original, c.name, c.menuid FROM '.DB_PREFIX.PREFIX.'comments a INNER JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE a.active=\'1\' AND (SELECT 1 FROM '.DB_PREFIX.PREFIX.'menu m WHERE m.id=c.menuid'.Index()->sql('menu_active').')=1 ORDER BY a.id DESC', 0, 12);
				$Menu = Factory::call('menu');
				while ($rs = DB::fetch($qry)) {
					$menu = $Menu->get($rs['menuid']);
					$rs['original'] = preg_replace('/\[([^\]]+)\]/','',$rs['original']);
					$ret[] = array(
						'title'		=> trunc($rs['original'], 90, true, true),
						'url_open'	=> $menu[0]['url'].AMP.$rs['name'].'#c'.$rs['id']
					);
				}
			break;
			case 'last_views':
				$ret = array();
				$qry = DB::qry('SELECT a.id, a.setid, a.viewed, c.title_'.Session()->Lang.' AS title, c.name, c.menuid FROM '.DB_PREFIX.PREFIX.'views a INNER JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE c.active=\'1\' AND a.userid='.Session()->UserID.' GROUP BY a.setid ORDER BY a.viewed DESC', 0, 10);
				$Menu = Factory::call('menu');
				while ($rs = DB::fetch($qry)) {
					$menu = $Menu->get($rs['menuid']);
					$rs['original'] = preg_replace('/\[(.+)\]/','',$rs['original']);
					$ret[] = array(
						'title'		=> $rs['title'],
						'url_open'	=> ($menu[0]['url']?$menu[0]['url'].AMP.$rs['name']:'?contentID='.$rs['setid'])
					);
				}
			break;
			*/
		}
		return $ret;
	}
	
	/*
	* Getting the grid array
	*/
	public static function Grid($name, $select = '*', $limit = 20, $where = '', $order = 'id', $sel = '', $func = false) {
		$force_rich = false;
		if (substr($name,0,1)=='@') {
			$force_rich = true;	
			$name = substr($name,1);
		}
		$langed = in_array('rid',DB::columns('grid_'.$name));
		$sql = 'SELECT '.$select.' FROM '.DB_PREFIX.PREFIX.'grid_'.$name.' WHERE active=\'1\''.($langed?' AND lang=\''.Session()->Lang.'\'':'').$where.' ORDER BY '.$order;
		if (is_array($limit)) {
			$sql .= ' LIMIT '.(int)$limit[0].','.(int)$limit[1];	
		} elseif ($limit) {
			$sql .= ' LIMIT '.$limit;
		}
		if (IS_ADMIN) {
			$_data = DB::getAll($sql);
			$data = array();
			foreach ($_data as $i => $rs) {
				$rs['url_open'] = '?grid='.$name.AMP.'id='.$rs['id'];
				if (isset($rs['rid']) && $rs['rid']) {
					Index()->Edit->set($rs, 'grid_'.$name, $rs['rid'], 'rid','',false,$force_rich)->parse()->admin();
				} else {
					Index()->Edit->set($rs, 'grid_'.$name, $rs['id'], 'id','',false,$force_rich)->parse()->admin();	
				}
				$data[$i] = $rs;
			}
		} else $data = DB::getAll($sql,$sel,$func);
		return $data;
	}
	
	public static function Poll($name) {
		return Factory::call('poll')->getContent($name);
	}
	
	/*
	* Tag cloud, each time when user searches something using ?search=my%20text, system will add a number per this tag "my text"
	*/
	/*
	public static function getSearches($limit = 20, $cloud = false) {
		$sql = 'SELECT keyword, cnt FROM '.DB_PREFIX.PREFIX.'searches ORDER BY cnt, searched DESC';
		$qry = DB::qry($sql, 0, $limit);
		$ret = array();
		while ($rs = DB::fetch($qry)) {
			$ret[$rs['keyword']] = $rs['cnt'];
		}
		if ($cloud) return self::printTagCloud($ret);
		return $ret;
	}
	*/
	
	/*
	* Prints the tag cloud html
	*/
	/*
	public static function printTagCloud($tags) {
		arsort($tags);
		$max_size = 32;
		$min_size = 12;
		$max_qty = max(array_values($tags));
		$min_qty = min(array_values($tags));
		$spread = $max_qty - $min_qty;
		if ($spread==0) $spread = 1;
		$step = ($max_size - $min_size) / $spread;
		$ret = '';
		foreach ($tags as $key => $value) {
			$size = round($min_size + (($value - $min_qty) * $step));
			$ret .= '<a href="?search='.$key.'" style="font-size:'.$size.'px" title="'.lang('%1 keywords tagged with %2', $value, $key).'">'.$key.'</a> ';
		}
		return $ret;
	}
	*/
	/*
	* Getting all data from certain modules nomatter what menu
	* @see below function is getting everything from menu
	*/
	public static function moduleContent($modules, $select, $limit = 20, $order = 'added DESC', $use_lang = true, $menu = false, $where = '') {
		$ret = array();
		DB::yeserror();
		$unions = array();
		if (!$select) $select = 'title';
		$Menu = Factory::call('menu');
		if (!$modules) $modules = array_keys(Site::getModules('content'));
		elseif (!is_array($modules)) $modules = array($modules);
		$menu_row = false;
		if ($menu && !is_numeric($menu)) {
			$menu_row = Factory::call('menu')->get($menu);	
		} elseif ($menu) {
			$menu_row = array(9=>array('id'=>$menu));
		} else {
			$menu_row = false;
		}
		foreach ($modules as $m) {
			if ($m=='html' || $m=='banner') continue;
			$unions[] = '(SELECT '.$select.', rid, \''.$m.'\' AS module, c.name AS content_name, c.menuid, '.DB_PREFIX.PREFIX.'content_'.$m.'.added FROM '.DB_PREFIX.PREFIX.'content_'.$m.' LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=setid WHERE '.DB_PREFIX.PREFIX.'content_'.$m.'.active=1'.($menu_row?' AND c.menuid='.$menu_row[9]['id']:'').$where.($use_lang?' AND '.DB_PREFIX.PREFIX.'content_'.$m.'.lang=\''.Session()->Lang.'\'':'').' AND (SELECT 1 FROM '.DB_PREFIX.PREFIX.'menu m WHERE m.id=c.menuid'.Index()->sql('menu_active').')=1)';
		}
		$sql = 'SELECT * FROM ('.join(' UNION ', $unions).') AS tmp ORDER BY '.$order;
		$qry = DB::qry($sql, 0, $limit);
		$lang = Session()->Lang;
		while ($rs = DB::fetch($qry)) {
			if (!$menu) {
				$rs['menu'] =& $Menu->get($rs['menuid']);
				if (!$use_lang && $rs['lang']) {
					Conf()->s('URLext'.false,'');
					Session()->Lang = $rs['lang'];
				}
				$rs['url_open'] = URL::ht(last($rs['menu'],'url').AMP.$rs['content_name']/*.AMP.$rs['module'].'ID='.$rs['rid']*/);
			} else {
				$rs['url_open'] = URL::ht(Index()->menu[9]['url'].AMP.$rs['content_name']/*.AMP.$rs['module'].'ID='.$rs['rid']*/);	
			}
			array_push($ret, $rs);
		}
		Session()->Lang = $lang;
		return $ret;
	}

	
	/**
	* Returns array of module entries
	* Remember the site structure:
	  POSITION	-| "/" (no url for this, menu url names should be totally unique)
				 |- MENU, SUBMENU	-| /menu_name/submenu_name"
				 					 |- CONTENT (page)	-| "/content/menu_name/submenu_name"
									 					 |- ENTRY ID (article, product, banner and etc..; 
												 			can have files subtable and categories sub table) 
												 			"/content/menu_name/submenu_name/articleID-14"
	* @param (int) max - maximum of contents or entries to show, 0 - unlimited (html, php module skipped)
	* @param (string) menu name
	* @param (string) submenu name
	* @param (string) content name
	* @param (string|array) in only module(s)
	* @param (int) maximum number of pages
	* @param (string) select statement (id, rid, setid - is required)
	* @param (bool) whether to print using default templates
	*/
	// {assign var='data' value='Data'|Call:'Content':10:'uspeshnye_trejdery':'forex_articles':'':'':array($smarty.get.page_b,10,'page_b')}
	public static function content($max, $menu, $submenu = '', $content_name = '', $modules_only = false, $limit=10, $my_select = 'id, rid, setid, title, descr, lang, added', $print = false, $filter = '', $filter2 = '', $order = '') {
		$params = array('lang'=>LANG);
		return Factory::call('datacontent')->init($params)->setLimit($limit)->setPage(get(URL_KEY_PAGE))->setFilter($filter)->setFilter2($filter2)->setOrder(($order ? $order : 'dated DESC, sort, id DESC'))->getContent($menu, $submenu, $print, !$print, array(
			'row_by_row'	=> !$print,
			'max_entries'	=> $max,
			'content_name'	=> $content_name,
			'modules_only'	=> $modules_only,
			'my_select'		=> $my_select
		), true);
	}
	
	public static function contentRow($id, $module = '') {
		if (!$id) return false;
		if (!$module || $module=='entries') {
			$sql = 'SELECT * FROM '.DB_PREFIX.PREFIX.'entries WHERE rid='.$id.' AND lang=\''.Session()->Lang.'\'';			
		} else {
			$sql = 'SELECT * FROM '.DB_PREFIX.PREFIX.'content_'.$module.' WHERE rid='.$id.' AND lang=\''.Session()->Lang.'\'';
		}
		return DB::row($sql);
	}
	
	public static function isSpam($s, $t = '') {
		if (!Session()->Country || Session()->Country=='un' || Session()->Country=='unknown') return true;
		if (strstr($s,'<a href=')) return true;
		if (strstr($s,'[link=')) return true;
		return false;	
	}
	
	/*
	* Comments. List and submittion
	*/
	public static function comments($setid, $table = 'content', $usePost = true, $get = true) {
		if (!$table) $table = 'content';
		$data = array();
		if (!$setid || !is_numeric($setid)) return false;
		if ($table && $table!='content') {
			if (!in_array($table, DB::tables())) return false;
		} else {
			$content = DB::row('SELECT `comment` FROM '.DB_PREFIX.PREFIX.'content WHERE id='.$setid);
			if (!$content || $content['comment']!='Y') return false;
		}	
		
		if ($usePost && isset($_POST['comment']) && isset($_POST['comment']['body'])) {
			$data = Action()->comment($table, $setid, $get);
		}
		if ($get) {
			
			Factory::call('spam')->getCode();
			$limit = 100;
			$offset = $limit * intval(request(URL_KEY_P));
			
			$select = 'id, subject, name, email, parentid, url, body, userid, added, edited, rate';
			$where = ' AND (active=\'1\''.(Session()->UserID?' OR userid='.Session()->UserID:' OR ip='.e(Session()->IPlong)).')';

			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$select.' FROM '.DB_PREFIX.PREFIX.'comments WHERE setid='.$setid.' AND `table`='.e($table).' AND parentid=0'.$where.' ORDER BY added DESC';

			$qry = DB::qry($sql, $offset, $limit);
			$total = DB::one('SELECT FOUND_ROWS()');
			$data['list'] = array();
			while ($rs = DB::fetch($qry)) {
				if ($rs['userid']) {
					$rs['user'] = self::user($rs['userid'], Config::get('comment_user_columns'));
				}
				Index()->Edit->set($rs, 'comments', $rs['id'], 'id')->admin();
				$rs['url'] = Parser::formatUrl($rs['url']);
				
				//$rs['added'] = Date::timezone($rs['added']);
				$rs['added'] = $rs['added'] + 3600 * 4;
				
				$rs['sub'] = array();
				$_sql = 'SELECT '.$select.' FROM '.DB_PREFIX.PREFIX.'comments WHERE parentid='.$rs['id'].$where.' ORDER BY added';
				$_qry = DB::qry($_sql, 0, 0);
				while ($_rs = DB::fetch($_qry)) {
					if ($_rs['userid']) {
						$_rs['user'] = self::user($_rs['userid'], Config::get('comment_user_columns'));
					}
					$_rs['added'] = Date::timezone($_rs['added']);
					Index()->Edit->set($_rs, 'comments', $_rs['id'], 'id')->admin();
					$_rs['url'] = Parser::formatUrl($_rs['url']);
					$rs['sub'][] = $_rs;
				}
				DB::free($_qry);
				$data['list'][] = $rs;
			}
			DB::free($qry);
			$data['pager'] = Pager::get(array(
				'total'	=> $total,
				'limit'	=> $limit,
				'page_key'=>URL_KEY_P
			));
		}
		return $data;
	}
	
	/*
	* For Smarty templates call:
	*	{assign var='files' value='Data'|call:'getFiles':$row.table:$row.rid:true:false}
	*	{foreach from=$files key=i item=f}...
	*/	
	public static function getFiles($table, $id, $sub = false, $retWithMedia = false, $rs = array(), $limit = 0) {
		if (!$id) return array();
		if ($sub) {
			$rs_main_photo = false;
			$del = $ret = array();
			$qry = DB::qry('SELECT file, id, media, mime, width, height, size, title_'.Session()->Lang.' AS title, descr_'.Session()->Lang.' AS descr, added, (SELECT main_photo FROM '.DB_PREFIX.PREFIX.$table.' WHERE rid='.$id.' AND main_photo!=\'\' LIMIT 1) AS main_photo FROM '.DB_PREFIX.PREFIX.$table.'_files WHERE setid='.$id.' AND active=1 ORDER BY sort',0,$limit);
			while ($rs = DB::fetch($qry)) {
				
				$rs['admin'] = '';
				Index()->Edit->set($rs, $table.'_files', $rs['id'])->parse()->admin();
				$rs['folder'] = HTTP_DIR_FILES.$table.'/'.$id.'/';
				
				$rs['th1'] = HTTP_DIR_FILES.$table.'/'.$id.'/th1/'.$rs['file'];
				$rs['th2'] = str_replace('/th1/','/th2/',$rs['th1']);
				$rs['th3'] = str_replace('/th1/','/th3/',$rs['th1']);
				$rs['th4'] = str_replace('/th1/','/th4/',$rs['th1']);
				
				if (is_file(FTP_DIR_FILES.$table.'/'.$id.'/th1/'.$rs['file'])) {
					if ($rs['file']==$rs['main_photo']) {
						$rs_main_photo = $rs;
						continue;	
					}
					$ret[] = $rs;
				} else {
					$del[] = $rs['id'];	
				}
			}
			if ($rs_main_photo) {
				$return = array();
				$return[0] = $rs_main_photo;
				foreach ($ret as $i => $rs) {
					$return[] = $rs;
				}
				return $return;
			}
		//	if ($del) DB::run('DELETE FROM '.DB_PREFIX.PREFIX.$table.'_files WHERE id IN ('.join(',',$del).')');
			return $ret;
		}
		$dir = FTP_DIR_FILES.$table.'/'.$id.'/th1/';
		if (!is_dir($dir)) return array();
		$dh = @opendir($dir);
		if (!$dh) {
			Message::halt('System error','Cannot open folder '.$dir);
		}
		$ret = array();
		while (($file = readdir($dh))!==false) {
			if ($file=='.' || $file=='..' || is_dir($dir.$file)) continue;
			if ($retWithMedia) {
				$media = File::getMedia($file);
				$ret[$media][] = self::sortFile($file, $table, $id, $rs);
			} else {
				$ret[] = self::sortFile($file, $table, $id, $rs);
			}
		}
		natsort($ret);
		return $ret;
	}
	
	/*
	* Information about file which is not stored in database
	*/
	public static function sortFile($file, $table, $id, $rs) {
		
		if (is_array($file)) {
			$files = array();
			foreach ($file as $f) {
				$files[] = self::sortFile($f);
			}
			return $files;	
		}
		$ext = ext($file);
		$media = File::getMedia($file, $ext);
		$files = array(
			'f'	=> $file,
			't'	=> $media,
			'e'	=> $ext
		);
		if ($id) {
			$dir = $table.'/'.$id.'/';
		} else {
			$dir = 'temp/'.Session()->UserID.'/';
		}
		$files['th1'] = HTTP_DIR_FILES.$dir.'th1/'.$file;
		$files['th2'] = str_replace('/th1/','/th2/',$files['th1']);
		$files['th3'] = str_replace('/th1/','/th3/',$files['th1']);
		$files['th4'] = str_replace('/th1/','/th4/',$files['th1']);
		
		if (!is_file(FTP_DIR_FILES.$dir.'th2/'.$file) || 
		filesize(FTP_DIR_FILES.$dir.'th1/'.$file)==filesize(FTP_DIR_FILES.$dir.'th2/'.$file)) {
			$files['th2'] = $files['th3'] = $files['th4'] = $files['th1'];
		}
		/*		
		if ($media=='image' || $media=='flash') {
			if (is_file($dir.'th1/'.$file)) {				
				$size = getimagesize($dir.'th1/'.$file);
				$files['w']	= $size[0];
				$files['h']	= $size[1];
			//	$files['html'] = self::htmlFile($dir.'th1/'.$file, $files, $media, @$rs['url'], $table, $id, @$rs['url_open'], @$rs['title']);
			}
		}
		*/
		return $files;
	}
	
	
	private static $swf = 0;
	public static function banner($rs, $width = 0,$height = 0, $attr = '', $style = '') {
		$file_path = $rs['main_photo_th1'];
		$file = $rs['main_photo'];

		$ext = ext($file);
		$media = File::getMedia($file, $ext);
		
		if ($media=='flash') {
			if ($width) {
				$w = $width;
				$h = $height;
			} else {
				list($w, $h) = getimagesize(FTP_DIR_ROOT.$file_path);
			}
			$url = '';
			if ($rs['body']) {
				$url = '?bannerID='.$rs['id'];
			}
			elseif ($rs['url']) {
				$url = $rs['url'];
			}
			
			$ret = '<div id="swf_banner_'.++self::$swf.'" class="swf_banner"><script type="text/javascript">$().ready(function(){$(\'#swf_banner_'.self::$swf.'\').html(flash_play(\''.$file_path.'\',\''.$url.'\',\''.$w.'\',\''.$h.'\', true))});</script></div>';
		}
		elseif ($media=='image') {
			$ret = '';
			if ($width && $height) $ret .= '<div style="width:'.$width.'px;height:'.$height.'px;overflow:hidden;'.$style.'">';
			if ($rs['body']) {
				$ret .= '<a href="?bannerID='.$rs['id'].'" class="banner-my"><img src="'.$file_path.'" alt="'.strform($rs['alt']).'"'.($attr?' '.$attr:' class="banner-my"').' /></a>';
			}
			elseif ($rs['url']) {
				$ret .= '<a href="'.$rs['url'].'"'.(strstr($rs['url'],'://')?' target="_blank" rel="nofollow"':'').' class="banner"><img src="'.$file_path.'" alt="'.strform($rs['alt']).'"'.($attr?' '.$attr:' class="banner"').' /></a>';
			} else {
				$ret .= '<img src="'.$file_path.'" alt="'.strform($rs['alt']).'"'.($attr?' '.$attr:' class="banner"').' />';
			}
			if ($width && $height) $ret .= '</div>';
		} else {
			$ret = '<a class="download" href="'.$filepath.'">'.$rs['main_photo'].'</a>';	
		}
		
		return $ret;
	}
	
	/*
	* Html wrapper for a file
	*/
	/*
	private static function htmlFile($file_path, $files, $media, $url, $table, $id, $url_open, $title) {
		if ($media=='flash') {
			$ret = '<script type="text/javascript">flash_play(\''.$file_path.'\',\''.$url.'\',\''.$files[$i]['w'].'\',\''.$files[$i]['h'].'\');</script>';
		} else {
			if ($table!='content_banner' && is_file(FTP_DIR_ROOT.str_replace('/th1/','/th3/',$file_path))) {
				list ($w, $h) = getimagesize(FTP_DIR_ROOT.str_replace('/th1/','/th3/',$file_path));
				$ret = '<div style="width:'.$w.'px;height:'.$h.'"><a href="javascript:;" class="show-photo"><img src='.$file_path.'" alt="'.strform($title).'" class="{p:\'[/th1/]\',w:\''.$files[$i]['w'].'\',h:\''.$files[$i]['h'].'\'}" /></a></div>';
			} elseif ($table=='content_banner') {
				if ($url) {
					if (strstr($url,'://')) {
						$ret = '<a href="'.$url.'" target="_blank" rel="nofollow" class="banner"><img src="'.$file_path.'" alt="'.strform($title).'" class="banner" /></a>';
					} else {
						$ret = '<a href="'.$url.'" class="banner"><img src="'.$file_path.'" alt="'.strform($title).'" class="banner" /></a>';	
					}
				} else {
					if ($id) {
						$ret = '<img src="/'.$file_path.'" alt="'.strform($title).'" class="banner" />';
					} else {
						$ret = '<a href="'.$url_open.'" class="banner"><img src="'.$file_path.'" alt="'.strform($title).'" class="banner" /></a>';
					}
				}
			} else {
				$ret = '<img src="'.$file_path.'" alt="'.strform($title).'" />';
			}
		}
		return $ret;
	}
	*/
	
	public static function youtube($src,$width = 500, $height = 360, $autoplay = false) {
		if (substr($src,0,7)=='http://') {
			if (!preg_match('/(\?v=|\/v\/)([0-9a-z_\-]+)(&(.*))/i',$src,$m)) {
				return $src;
			}
			$src = 'http://www.youtube.com/v/'.$m[2];
			if ($autoplay) $src .= '?autoplay=1';
			$ret = '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="'.$src.'"></param><param name="wmode" value="transparent"></param><embed src="'.$src.'" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed></object>';	
		}
		return $ret;
	}
	
	/*
	* User's media files
	*/
	public static function photo($id, $size = NULL, $get = 'photos') {
		if (!$id) return false;
		$http_dir = DIR_FILES.'users/'.$id.'/th1/';
		if (!Conf()->g2('user_photos',$id)) {
			$photos = $videos = array();
			if (is_dir(FTP_DIR_ROOT.$http_dir)) {
				$dh = opendir(FTP_DIR_ROOT.$http_dir);
				if (!$dh) return false;
				while (($file = readdir($dh))!==false) {
					if ($file=='.' || $file=='..' || is_dir(FTP_DIR_ROOT.$http_dir.$file)) continue;
					if (File::isPicture($file)) {
						$photos[] = $file;
					} else {
						$videos[] = $file;
					}
				}
			}
			Conf()->s2('user_photos',$id,array($photos, $videos));
		} else {
			list ($photos, $videos) = Conf()->g2('user_photos',$id);
		}

		switch ($get) {
			case 'photos':
				if ($size!==NULL) {
					if (!$photos) return '/i/no_photo.jpg'; // no_img here
					elseif ($size==0) return '/'.$http_dir.$photos[0]; 
					else return '/'.str_replace('th1/','',$http_dir).'th'.$size.'/'.$photos[0];
				}
				return $photos;
			break;
			case 'videos':
				if ($size!==NULL) {
					if (!$videos) return false; // no_img here
					else return '/'.str_replace('th1/','',$http_dir).$videos[0];
				}
				return $videos;
			break;
			default:
				return array($photos, $videos);
			break;
		}
	}
	
	/*
	* Checking function, whether user is friend to someone or was already posted/switched something and etc..
	*/
	/*
	public static function is($type, $id) {
		switch ($type) {
			case 'friend':
			
			break;
		}
	}
	*/
	
	/*
	* Getting user's data
	*/
	public static function user($id, $select = '*') {
		if ($id <= 0) return;
		$sql_select = is_array($select) ? join(', ',$select) : $select;
		$sql_soundex = soundex($sql_select);
		
		$online = false;
		if (find($sql_select,'online')) {
			$online = true;
			$sql_select = trim(str_replace('online','',$sql_select),',');
		}
		if (isset(self::$cache['user']) && isset(self::$cache['user'][$id]) && isset(self::$cache['user'][$id][$sql_soundex])) {
			return self::$cache['user'][$id][$sql_soundex];
		}
		$sql = 'SELECT '.$sql_select.', '.DB::sqlGetString('age','up.dob').' AS age'.($online?', '.DB::sqlGetString('online','u.id').' AS online':'').' FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile up ON u.id=up.setid WHERE u.id='.(int)$id;
		$ret = DB::row($sql);
		if (isset($ret['main_photo'])) {
			if ($ret['main_photo'] && is_file(FTP_DIR_ROOT.DIR_FILES.'users/'.$id.'/th1/'.$ret['main_photo'])) {
				/*
				$media = File::getMedia($ret['main_photo']);
				$width = $height = 0;
				if ($media=='image' || $media=='flash') {
					list ($width, $height) = @getimagesize(FTP_DIR_ROOT.DIR_FILES.'users/'.$id.'/th1/'.$ret['main_photo']);
				}
				
				$ret['photo'] = array(
					'th1' => 'upload/users/'.$id.'/th1/'.$ret['main_photo'],
					'th2' => 'upload/users/'.$id.'/th2/'.$ret['main_photo'],
					'th3' => 'upload/users/'.$id.'/th3/'.$ret['main_photo'],
					'width'	=> $width,
					'height'=> $height
				);
				*/
			} else {
				$ret['main_photo'] = '';
			}
		}
		if (!strstr($sql_select,',') && isset($ret[$sql_select])) $ret = $ret[$sql_select];
		self::$cache['user'][$id][$sql_soundex] = $ret;
		return $ret;
	}
	
	/*
	* Getting full info about user. For user's profile preview
	*/
	public static function getUser($id=0) {
		if (!$id) $id = Session()->UserID;
		if (!$id) return array();
		if (isset(self::$cache['get_user']) && isset(self::$cache['get_user'][$id])) {
			return self::$cache['get_user'][$id];
		}
		$sql = 'SELECT *, '.DB::sqlGetString('online','id').' AS online FROM '.DB_PREFIX.'users WHERE '.(is_numeric($id)?'id='.(int)$id:'login='.e($id));
		$row = DB::row($sql);
		unset($row['password'], $row['code']);
		if (!$row) return array();
		$profile = DB::row('SELECT *, '.DB::sqlGetString('age','dob').' AS age FROM '.DB_PREFIX.'users_profile WHERE setid='.$row['id']);
		if ($profile) foreach ($profile as $k => $v) $profile[$k] = strexp($v);
		$profile = self::world_names($profile);

		/*
		$arrTextsSet = array('looking_for','looking_as');
		foreach ($arrTextsSet as $t) {
			$set = explode(',',$profile[$t]);
			$profile[$t.'_arr'] = $set;
			$j = array();
			foreach ($set as $s) {
				$j[] = self::getVal($t,$s);
			}
			$profile[$t.'_text'] = join(', ',$j);
		}
		
		if (Session()->UserID!=$id) {
			$ret['is_friend'] = DB::one('SELECT 1 FROM '.DB_PREFIX.'friends WHERE from_user='.Session()->UserID.' AND to_user='.$id);
		}
		*/
		Conf()->s('my_country', $profile['country']);
		$row['photos'] = self::photo($row['id'], 0);
		$profile['zodiac'] = Date::zodiac($profile['dob']);
		$dob = Date::datetime2timespan($profile['dob']);
		$dob = $profile['dob'];
		$profile['dob'] = array();
		$profile['dob']['Year'] = substr($dob, 0, 4);
		$profile['dob']['Month'] = substr($dob, 5, 2);
		$profile['dob']['Day'] = substr($dob, 8, 2);
		$row['profile'] = $profile;
		self::$cache['get_user'][$id] = $row;
		return $row;
	}
	
	/*
	* Value of an array below
	*/
	public static function getVal($in, $key = -1, $sep = false, $trunc = false) {
		if ($sep && (strstr($key,$sep) || is_array($key))) {
			if (!is_array($key)) $key = explode($sep,trim($key,$sep));
			$ret = array();
			foreach ($key as $e) if ($e) {
				$ret[$e] = self::getArray($in.':'.$e); 
				if ($trunc) $ret[$e] = trunc($ret[$e],0,$trunc);
			}
			return $ret;
		} else return self::getArray($in.':'.$key);
	}
	
	/*
	* Database of default global arrays.
	* Additional arrays for your certain project programmed in MyClass.php in each template folder classes/
	*/
	public static function getArray($type, $options = -1, $settings = 'dropdown', $flag = false, $name = '') {
		if (strtolower(substr($type,0,3))=='my:') {
			return Index()->My->data(substr($type,3), $options, $settings, $flag, $name);
		}
		
		list ($type, $key, $keyAsVal, $lang, $getVal) = Html::startArray($type);
		switch ($type) {
			/*
			case 'salutation':
				$lang = true;
				$ret = array(
					'mr'		=> 'Mr',
					'mrs'		=> 'Mrs',
					'ms'		=> 'Ms',
					'king'		=> 'King',
					'lord'		=> 'Lord'
				);
			break;
			case 'work_status':
				$ret = array(
					1 => 'employer', //
					2 => 'self-emplyed', // except
					3 => 'employee',
					4 => 'academic',
					5 => 'student',
					6 => 'other'
				);
			break;
			case 'degree':
				$ret = array(
					1	=> 'master',
					2	=> 'bachelor',
					3	=> 'college graduate',
					4	=> 'high-school graduate',
					5	=> 'other'
				);
			break;
			case 'heard_us':
				$ret = array(
					2	=> 'From friend',
					3	=> 'Just linked in',
					4	=> 'Googled',
					5	=> 'Other, please specify..'
				);
			break;
			case 'company_type':
				$lang = true;
				$ret = array(
					1	=> 'Private',
					2	=> 'Enterprise',
					3	=> 'Non-profit',
					4	=> 'Academic',
					5	=> 'Government',
					6	=> 'Lab',
					7	=> 'Industry',
					8	=> 'Other'					
				);
			break;
			*/
			case 'currencies':
				$ret = array_label(Site::getCurrencies(), '[[:KEY:]]');
			break;
			case 'content_modules':
				$ret = Site::getModules('content');
				if ($options!==-1) {
					$_ret = array();
					foreach ($ret as $k => $a) {
						if ($a['active']) $_ret[$k] = $a['title'];
					}
					$ret = $_ret;
				}
			break;
			case 'category_modules':
				$ret = Site::getModules('category');
				if ($options!==-1) {
					$_ret = array();
					foreach ($ret as $k => $a) {
						if ($a['active']) $_ret[$k] = $a['title'];
					}
					$ret = $_ret;
				}
			break;
			case 'module_types':
				$lang = true;
				$ret = array(
					'content'	=> 'Content',
					'category'	=> 'Category',
					'grid'		=> 'Grid'
				);
			break;
			case 'menu_targets':
				$keyAsVal = true;
				$ret = array('_blank','_self','_top');
			break;
			case 'menu_display':
				$lang = true;
				$ret = array(
					0	=> '$Visible for all',
					1	=> '$Visible to authorized users only',
					2	=> '$Visible for not authorized users only',
					3	=> '$For administrators',
					4	=> '$Custom user groups and classes..',
					5	=> '$Hidden'
			);
			break;
			case 'user_groups':
				$lang = true;
				$ret = Conf()->g('user_groups');
				unset($ret[0]);
			break;
			case 'user_classes':
				$lang = true;
				$ret = Conf()->g('user_classes');
			break;
			case 'user_statuses':
				$ret = array_label(Conf()->g('user_statuses'));
			break;
			case 'user_status_login':
				$ret = array(1,3);
			break;
			case 'photo_sizes':
				$ret = array(
					1	=> array(640, 480) // 640, 480
					,2	=> array(240, 259) // 220, 239
					,3	=> array(130, 120) // 112, 83 | 100, 100
					,4	=> array(70, 70) // 50x50
				);
			break;
			case 'module_status':
				$lang = true;
				$ret = array(0=>'Inactive', 1=>'Active', 2=>'Deleted', 3=>'Pending', 4=>'Admin Deactive', 5=>'User Deactive');
			break;
			case 'login_remember':
				$ret = array(0 => 'No', 86400 => '1 day', 172800 => '2 days', 604800 => '1 week', 2592000 => '1 month');
			break;
			case 'years':
				$ret = Html::arrRange(date('Y')-11, date('Y')-90);
			break;
			case 'years_future':
				$ret = Html::arrRange(date('Y'), date('Y')+5);
			break;
			case 'months':
				$_ret = Conf()->g('ARR_MONTHS');
				$ret = array();
				for ($i=1;$i<=12;$i++) $ret[str_pad($i,2,'0',STR_PAD_LEFT)] = $_ret[$i-1];
			break;
			case 'months_short':
			case 'months2':
				$_ret = Conf()->g('ARR_SHORT_MONTHS');
				$ret = array();
				for ($i=1;$i<=12;$i++) $ret[str_pad($i,2,'0',STR_PAD_LEFT)] = $_ret[$i-1];
			break;
			case 'days':
				$ret = array();
				for ($i=1;$i<=31;$i++) $ret[str_pad($i,2,'0',STR_PAD_LEFT)] = $i;
			break;
			case 'hours':
				if ($options===0) $options = intval(date('H'));
				$ret = Html::arrRange(0, 23);
			break;
			case 'minutes':
				if ($options===0) $options = intval(date('i'));
				$ret = array();
				for ($i=0;$i<=55;$i+=5) {
					$ret[$i] = str_pad($i,2,'0',STR_PAD_LEFT);
				}
			break;
			case 'hours_minute':
				$ret = array();
				for ($i=1;$i<=24;$i++) {
					$ret[$i*60] = str_pad($i,2,'0',STR_PAD_LEFT).':00';
				}
			break;
			case 'weekdays_short':
				$start = Conf()->g2('LOCALE','week_start');
				$_ret = Conf()->g('ARR_SHORT_WEEKDAYS');
				$ret = array();
				if ($start==1) {
					for ($i=1;$i<=7;$i++) {
						$ret[$i] = $_ret[$i];
					}
					$ret[7] = $_ret[0];
				}
				elseif ($start==7) {
					$ret[7] = $_ret[1];
					for ($i=1;$i<=6;$i++) {
						$ret[$i] = $_ret[$i+1];	
					}
					$ret[6] = $_ret[0];
				}
			break;
			case 'gender':
				$lang = true;
				$ret = array(
					'M'	=> 'Male',
					'F'	=> 'Female'
				);
			break;
			case 'dob_admin':
				$params = array (
					'year_from'		=> date('Y')-10
					,'year_to'		=> date('Y')-90
					,'year_step'	=> 1
					,'year_next'	=> 0
					
					,'month_from'	=> 1
					,'month_to'		=> 12
					,'month_step'	=> 1
					,'month_next'	=> 0
					
					,'day_from'		=> 1
					,'day_to'		=> 0
					,'day_step'		=> 1
					,'day_next'		=> 0
					
				);
				return Date::DateDropDowns('data[profile][dob][',$options,'','a-select','a-select','no_time','&nbsp;',$params,true);
			break;
			case 'im_abuse':
				$ret = array(
					1	=> 'Offence',
					2	=> 'Doing something stupid',
					3	=> 'Asking for money',
					4	=> 'Being rude',
					5	=> 'Other'
				);
			break;
			
			case 'countries':
				$ret = self::countries();
			break;
			case 'countries_code':
				$ret = self::countries_code();
			break;
			case 'states':
				$ret = self::states();
			break;
			case 'cities':
				$ret = self::cities();
			break;
			case 'districts':
				$ret = self::districts();
			break;
			case 'arr':
				$ret = Conf()->g('ARR_'.strtoupper($key));
				$key = -1;
			break;
			case 'langs':
				$ret = array_label(Site::getLanguages(), 0);
			break;
			default:
				return array();
			break;
		}
		$ret = Html::endArray($ret, $keyAsVal, $lang, $key, $options, $settings, $getVal);
		return $ret;
	}
	
	
	public static function world_only($name,$arr_only) {
		self::$only[$name] = $arr_only;	
		Factory::call('world')->driver()->only($name, $arr_only);
	}
	
	public static function only($name, $arr_only) {
		return self::world_only($name, $arr_only);
	}
	
	public static function countries() {
		return Factory::call('world')->driver()->by_code(false)->all();	
	}
	public static function countries_code() {
		return Factory::call('world')->driver()->by_code(true)->all();	
	}
	public static function states() {
		return Factory::call('world')->driver()->all(self::find('country'));
	}
	public static function cities() {
		return Factory::call('world')->driver()->all(self::find('country'), self::find('state'));
	}
	public static function districts() {
		return Factory::call('world')->driver()->all(self::find('country'), self::find('state'), self::find('city'));
	}
	

	public static function country($country, $double = false) {
		if ($country==='en') $country = 'us';
		return Factory::call('world')->driver()->one($double ? array($country) : $country);
	}
	public static function state($country, $state) {
		return Factory::call('world')->driver()->one($country, $state);
	}
	public static function city($country, $state, $city) {
		return Factory::call('world')->driver()->one($country, $state, $city);
	}
	public static function district($country, $state, $city, $district) {
		return Factory::call('world')->driver()->one($country, $state, $city, $district);
	}
	
	public static function world($country, $state, $city, $district) {
		
	}
	
	public static function world_find($data, $prefix = '') {
		Factory::call('world')->find($data, true, $prefix);
	}
	
	public static function world_params($params) {
		Factory::call('world')->set($params);
	}
	public static function world_unset(&$S) {
		Factory::call('world')->super($S);
	}
	
	public static function find($name) {
		if ($ret = post('profile',$name)) return $ret;
		if ($ret = post('data',$name)) return $ret;
		if ($ret = Factory::call('world')->find($name)) return $ret;
		if ($ret = Conf()->g('find_'.$name)) return $ret;
		if ($ret = post($name)) return $ret;
		if ($ret = get($name)) return $ret;
		if ($ret = request('profile',$name,Conf()->g('my_'.$name))) return $ret;
		return '';
	}
	
	private static $world_prefix = '';
	public static function world_prefix($prefix) {
		self::$world_prefix = $prefix;
	}
	
	public static function world_names($row) {
		if (!isset($row[self::$world_prefix.'country'])) return;
		$c = self::country($row[self::$world_prefix.'country'], true);
		if (is_array($c)) {
			$row[self::$world_prefix.'country_name'] = $c['country_name'];
			$row[self::$world_prefix.'country_code'] = $c['country_code'];
		} else {
			$row[self::$world_prefix.'country_code'] = $row[self::$world_prefix.'country'];
			$row[self::$world_prefix.'country_name'] = $c;
		}
		if (isset($row['state']) && !isset($row[self::$world_prefix.'state_name'])) {
			$row[self::$world_prefix.'state_name'] = self::state($row[self::$world_prefix.'country'], $row[self::$world_prefix.'state']);	
		}
		if (isset($row['city']) && !isset($row[self::$world_prefix.'city_name'])) {
			$row[self::$world_prefix.'city_name'] = self::city($row[self::$world_prefix.'country'], $row[self::$world_prefix.'state'], $row[self::$world_prefix.'city']);	
		}
		if (isset($row['district']) && !isset($row[self::$world_prefix.'district_name'])) {
			$row[self::$world_prefix.'district_name'] = self::district($row[self::$world_prefix.'country'], $row[self::$world_prefix.'state'], $row[self::$world_prefix.'city'], $row[self::$world_prefix.'district']);	
		}
		return $row;
	}
	
	public static function test() {
		//p(self::states('ru'));
		//exit;
	}
}