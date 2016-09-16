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
* @file       mod/DataContent.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class DataContent extends Object {
	
	public
		$table = '',
		$category_table,
		$name = '',	
		$Category,	
		$page_data = array(),
		$menu = array(),
		$content = array(),
		$content_id = 0,
		$menu_id = 0,
		$menu_ids = array(),
		$row_by_row = false,
		$total_all = 0,	
		$tables = array(),
		$options = array(),	
		$id_skip = array(),
		$skip_first_modules = array(
			'hmtl', 'banner'
		),	
		$content_search = array(),	
		$called_id = 0
	;
	
	protected
		$content_name,
		$modules_only,
		$max_entries,
		$my_select
	;
	
	private static $loaded = false;
	
	
	public function init(&$params = array()) {
		if (!$this->Index) $this->Index = Index();
		$this->setParams($params);
		if (!self::$loaded) {
			$this->_load();
			self::$loaded = true;
		}
		return $this;	
	}
	
	private function _load() {		
		$this->content_select = 'id, menuid, menuids, title_'.$this->lang.' AS title, \'content\' AS type, name, inserts, cnt, keywords, dated, `comment`, comments, views, viewtime, rate';
		$this->plain_modules = array('html','banner');

		/*
		if ($this->name && array_key_exists($this->name,$this->Index->modules)) {
			$this->id = intval(request($this->name.'ID'));
		}
		*/
		$this->name = $this->Index->module_name;
		$this->id = $this->Index->module_id;
		$this->id_skip = array(URL_KEY_CATID,'id',$this->name.'ID',URL_KEY_ADMIN);
		$this->category_modules = Site::getModules('category');
		$this->table = 'content_'.$this->name;
		$this->tables = DB::tables();
		$this->category_table = 'category_'.$this->name;
		if ($this->category_modules && array_key_exists($this->name,$this->category_modules)) {
			$this->Category = Factory::call('category');
			if ($this->catid) {
				$this->catref = $this->Category->catRef($this->catid, $this->category_table);	
			}
		}	
	}
	
	private function getMenu($menu, $submenu) {
		if ((is_array($menu) && is_numeric($menu[0])) || (is_array($submenu) && is_numeric($submenu[0]))) {
			$this->menu_ids = array();
			if (is_array($menu)) $this->menu_ids = array_numeric($menu);
			else {
				$m = Factory::call('menu')->get($menu);
				$this->menu_ids[] = $m[9]['id'];
			}
			if (is_array($submenu)) {
				$this->menu_ids = array_merge($this->menu_ids, array_numeric($submenu));
			} else {
				$m = Factory::call('menu')->get($submenu);
				$this->menu_ids[] = $m[9]['id'];
			}
			$this->menu_id = 0;
		}
		elseif ($menu && !is_numeric($menu)) {
			$this->menu = Factory::call('menu')->get($menu, $submenu);
			$this->menu_id = $this->menu[9]['id'];
		}
		elseif ($submenu) {
			$this->menu = Factory::call('menu')->get($menu, $submenu);
			$this->menu_id = $this->menu[9]['id'];
		}
		elseif ($menu) {
			$this->menu = Factory::call('menu')->get($menu);
			$this->menu_id = $this->menu[9]['id'];
		} 
		elseif ($this->Index->menu) {
			$this->menu = $this->Index->menu;
			$this->menu_id = $this->Index->menu[9]['id'];
		}
		if ($this->page) {
			$this->menu[9]['url'] .= AMP.URL_KEY_PAGE.'='.$this->page;
		}
	}
	

	private function getContentRow() {
		if ($this->menu_id):
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ((SELECT id, \'0\' AS rid, \'\' AS is_admin, \'0\' AS userid, \'content\' AS type, \'\' AS main_photo, name, title_'.$this->lang.' AS title, \'\' AS descr, \'\' AS teaser, \'\' AS body, \'\' AS bodylist, \'\' AS options, menuid, menuids, inserts, dated, added, views, viewtime, rate, `comment`, comments, keywords, sort FROM '.DB_PREFIX.PREFIX.'content WHERE (menuid='.$this->menu_id.' OR menuids LIKE \'%,'.$this->menu_id.',%\')'.($this->content_name?' AND name LIKE '.e($this->content_name):'').' AND active=1 AND inserts!=\'\''.$this->filter.') UNION (SELECT id, rid, is_admin, userid, type, main_photo, name, title, descr, teaser, body, bodylist, options, menuid, menuids, \'\' AS inserts, dated, added, views, viewtime, rate, `comment`, comments, keywords, \'0\' AS sort FROM '.DB_PREFIX.PREFIX.'entries WHERE (menuid='.$this->menu_id.' OR menuids LIKE \'%,'.$this->menu_id.',%\')'.($this->content_name?' AND name LIKE '.e($this->content_name):'').' AND lang=\''.$this->lang.'\' AND active=1'.$this->filter2.')) AS tmp ORDER BY '.$this->order.''.($this->limit?' LIMIT '.$this->offset.', '.$this->limit:'');

			$this->content = DB::getAll($sql);
			$this->total = $this->content['FOUND_ROWS()'];
			unset($this->content['FOUND_ROWS()']);
			
		elseif ($this->menu_ids && $this->menu_ids[0]):	
			$j = join(', ',$this->menu_ids);
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ((SELECT id, \'0\' AS rid, \'\' AS is_admin, \'0\' AS userid, \'content\' AS type, \'\' AS main_photo, name, title_'.$this->lang.' AS title, \'\' AS descr, \'\' AS teaser, \'\' AS body, \'\' AS bodylist, \'\' AS options, menuid, menuids, inserts, dated, added, views, viewtime, rate, `comment`, comments, keywords, sort FROM '.DB_PREFIX.PREFIX.'content WHERE menuid IN ('.$j.') AND active=1 AND inserts!=\'\''.$this->filter.') UNION (SELECT id, rid, is_admin, userid, type, main_photo, name, title, descr, teaser, body, bodylist, options, menuid, menuids, \'\' AS inserts, dated, added, views, viewtime, rate, `comment`, comments, keywords, \'0\' AS sort FROM '.DB_PREFIX.PREFIX.'entries WHERE menuid IN ('.$j.') AND lang=\''.$this->lang.'\' AND active=1'.$this->filter2.')) AS tmp ORDER BY '.$this->order.''.($this->limit?' LIMIT '.$this->offset.', '.$this->limit:'');
			$this->content = DB::getAll($sql);
			$this->total = $this->content['FOUND_ROWS()'];
			unset($this->content['FOUND_ROWS()']);
		endif;
	}
	
	
	/**
	* Catch entry row, get additional data
	*/
	private function catchRow(&$rs, $content = false) {
		if (!@$rs['module']) return;
		$rs['alt'] = $rs['title'];
		
		$this->Index->Edit->set($rs, ($content['type']!='content' ? 'entries' : 'content_'.$rs['module']), @$rs['id'], 'id')->parse()->admin();

		if ($content['type']!='content') {
			$rs['total_entries'] = $this->total;
		} else {
			$rs['total_entries'] = (int)@$this->total_entries[$content['id']];
		}
		if ($this->menu[0]['name']==URL_KEY_HOME && $this->menu[9]['name']==URL_KEY_HOME) {
			$this->menu[9]['url'] = '?'.URL_KEY_HOME;
		}
		
		if ($content['type']!='content') {
			if (!$this->id) $rs['url_is'] = true;
			else $rs['url_is'] = false;
		}
		elseif (($this->id && $this->name) || ($this->total_all==1 && $this->Index->content)) {
			$rs['url_is'] = false;
		}		
		elseif (!isset($rs['bodylist']) || !$rs['bodylist'] || !$this->Index->content || ($rs['total_entries'] > 1 && !$this->Index->content['is_open'])) {
			$rs['url_is'] = true;		
		}
		else {
			$rs['url_is'] = false;	
		}
		if ($this->menu_ids) {
			$rs['menu'] = Factory::call('menu')->get($content['menuid']);
			$rs['url_back'] = $rs['menu'][9]['url'];
		}
		elseif ($content['menuids'] && $content['menuid']!=$this->menu[0]['id'] && $content['menuid']!=$this->menu[1]['id']) {
			$rs['menu'] = Factory::call('menu')->get($content['menuid']);
			$rs['url_back'] = $rs['menu'][9]['url'];
		}
		else {
			$rs['url_back'] = $this->menu[9]['url'];
			$rs['menu'] =& $this->menu;
		}
		
		if ($content['name']) {
			if (!isset($content['many_entries']) || !$content['many_entries']) {
				//$rs['url_back'] .= AMP.$content['name'];
			} else {
				$rs['url_back'] .= AMP.$content['name'];
			}
		}
		if ($content['type']!='content') {
			$rs['url_open'] = $rs['url_back'].AMP.($content['name']!=$rs['menu'][9]['name'] ? ($content['name'] ? $content['name'] : '&id='.$content['id']) : '');
		} else {
			if ($this->page_data['type']!='content') {
				$rs['url_open'] = '?'.$this->page_data['type'].'='.get($this->page_data['type']).($content['name']!=$rs['menu'][9]['name'] ? AMP.$content['name']:'').AMP.$rs['module'].'ID='.$rs['rid'];
			} else {
				if (!$content['many_entries'] && !@$content['is_open']) {
					$rs['url_open'] = $rs['url_back'];
				}
				else {
					$rs['url_open'] = $rs['url_back'].AMP.$rs['module'].'ID='.$rs['rid'];
				}
			}
		}
		/*
		if ($this->page_data['type']!='sitemap') {
			if ($rs['bodylist']) {
				$rs['is_open'] = $this->content_id || $this->id;
			} else {
				$rs['is_open'] = $this->id;
			}
			$rs['plain'] = in_array($rs['module'], $this->plain_modules);
			if ($rs['type']=='content') $rs['content'] = $content;
		}
		*/
		if (isset($rs['options'])) $rs['options'] = unserialize($rs['options']);
		
		$rs['content'] =& $content;
		

		if ($content['type']!='content') {
			$rs['table'] = 'entries';
		} else {
			$rs['table'] = 'content_'.$rs['module'];
		}
		if (isset($rs['main_photo'])) {
			$http_file_path = HTTP_DIR_FILES.$rs['table'].'/'.$rs['rid'].'/th1/'.$rs['main_photo'];
			$ftp_file_path = FTP_DIR_FILES.$rs['table'].'/'.$rs['rid'].'/th1/'.$rs['main_photo'];
			if (is_file($ftp_file_path)) {
				$rs['main_photo_th1'] = $http_file_path;
				list ($w, $h) = @getimagesize(FTP_DIR_ROOT.$file_path);
				if ($w) {
					$rs['main_photo_w'] = $w;
					$rs['main_photo_h'] = $h;
				}
			}
		}
	}
	
	/**
	* Catch module row, get additional data
	*/
	private function catchModule($module_id) {
		$ret = array();
		$m = $this->Index->modules_id[$module_id];
		$ret['table'] = 'content_'.$m['module'];
		$ret['module'] = $m['module'];
		$ret['cols'] = DB::columns($ret['table']);
		if ($this->page_data['type']=='sitemap') {
			$ret['select'] = 'a.id, a.title, a.rid'.(in_array('descr',$ret['cols'])?', a.descr':'');
		}
		elseif ($this->my_select) {
			$ret['select'] = $this->my_select;	
		}
		else {
			$ret['select'] = '*';
		}
		if (in_array('main_photo',$ret['cols'])) {
			$ret['select'] .= ', (CASE WHEN a.main_photo!=\'\' THEN a.main_photo ELSE (SELECT b.main_photo FROM '.DB_PREFIX.PREFIX.$ret['table'].' b WHERE b.id!=a.id AND b.rid=a.rid AND b.main_photo!=\'\' LIMIT 1) END) AS main_photo';
		}
		$ret['where'] = '';
		if ($this->catid && in_array('catref',$ret['cols'])) {
			$this->Category = Factory::call('category');
			$ret['where'] = ' AND a.catref=\''.$this->Category->catRef($this->catid, 'category_'.$m['module']).'\'';
			$this->content_search[] = URL_KEY_CATID;
		}
		return $ret;
	}
	
	/**
	* Content entries
	*/
	private function getContentEntries() {
		$get = $entries = $data = array();
		$count = 0;
		foreach ($this->content as $index => $content) {
			if ($content['type']=='content') {
				$inserts = explode(',',$content['inserts']);
				$total_inserts = count($inserts);
				$this->total_all += $total_inserts;
				$this->total_entries[$content['id']] = $total_inserts;
				$many_entries = (($this->total>1 || $this->row_by_row) && $this->total_entries[$content['id']]>1);
				$total = 0;
				foreach ($inserts as $i => $entry) {
					list ($module_id, $entry_id) = explode(':',$entry);
					$module_name = $this->Index->modules_id[$module_id]['module'];
					if ($many_entries && !$this->row_by_row) {
						if (in_array($module_name, $this->skip_first_modules)) {
							continue;
						}
						elseif ($total==1) {
							break;	
						} else {
							$total++;
						}
					} else {
						$total++;	
					}
					$count++;
					if ($this->max_entries && $count > $this->max_entries) break;
					if ($entry_id) $get[$module_id][] = $entry_id;
					$content['many_entries'] = $many_entries;
					$entries[$entry] = $content;
				}
			} 
			elseif ($content['id']) {
				$this->total_all++;
				$entries[] = $content;
				$get['entries'][] = $content;
			}
		}
		foreach ($get as $module_id => $ids) {
			if ($module_id=='entries') {
				$data[] = $ids;
			} else {
				if (!$ids) continue;
				$ins = $this->catchModule($module_id);
				if ($this->modules_only && !in_array($ins['module'], $this->modules_only)) continue;
				$sql = 'SELECT '.$ins['select'].', \''.$ins['module'].'\' AS module FROM '.DB_PREFIX.PREFIX.$ins['table'].' a WHERE a.rid IN ('.join(', ',$ids).') AND a.lang=\''.$this->lang.'\' AND a.active=1'.$ins['where'];
				
				$qry = DB::qry($sql,0,0);
				while ($rs = DB::fetch($qry)) {
					$key = $module_id.':'.$rs['rid'];
					$data[$key] = $rs;
				}
			}
		}
		$this->data = array();
		if ($this->row_by_row) {
			foreach ($entries as $entry => $content) {
				if ($content['type']=='content') {
					$this->catchRow($data[$entry], $content);
					if (!isset($data[$entry]['rid'])) continue;
					$this->data[] = $data[$entry];
				} else {
					$content['module'] = 'entries';
					$this->catchRow($content, $content);
					if (!isset($content['rid'])) continue;
					$this->data[] = $content;
				}
			}
		} else {
			foreach ($this->content as $i => $content) {
				if ($content['type']!='content') {
					$content['module'] = 'entries';
					$this->catchRow($content, $content);
					$this->data[] = array(
						'content'	=> $content,
						'entries'	=> array(0=>$content),
						'total'		=> 1
					);
				} else {
					$inserts = explode(',',$content['inserts']);
					$entries = array();
					foreach ($inserts as $entry) {
						if (!isset($data[$entry])) continue;
						$this->catchRow($data[$entry], $content);
						$entries[] = $data[$entry];
					}
					$this->data[] = array(
						'content'	=> $content,
						'entries'	=> $entries,
						'total'		=> $this->total_entries[$content['id']]
					);
				}
			}
		}
	}

	
	/**
	* getContent method, should work nomatter what
	*/
	public function getContent($menu = '', $submenu = '', $print = true, $retData = false, $params = array(), $cache = false) {
		$_menu = ($menu?$menu:$this->Index->menu[0]['id']);
		$_submenu = ($submenu?$submenu:(isset($this->Index->menu[1]) ? $this->Index->menu[1]['id']: false));
		$this->arg_menu = $menu;
		$this->arg_submenu = $submenu;
		if (is_array($_menu)) $_menu = $_menu[0];
		if (is_array($_submenu)) $_submenu = $_submenu[0];
		
		Conf()->plus3('getContent',$_menu,$_submenu,1);
		if (Conf()->get('getContent',$_menu,$_submenu)>30) {
			Message::Halt('Infinite loop','Content->getContent(\''.$_menu.'\',\''.$_submenu.'\',..) has been looped 30 times');
		}
		$content_name = '';
		$modules_only = array();
		$max_entries = 20;
		$my_select = '';
		$row_by_row = false;
		
		

		extract($params);
		if (!$this->Index->content && !$this->id && $cache && $print && SITE_TYPE!='rss') {
			Conf()->plus('getContent_index',1);
			$cache_name = 'content_'.serialize(array(
				Conf()->g('getContent_index'),
				$this->prefix,
				$this->lang,
				TEMPLATE,
				$_menu,
				$_submenu
			));
			unset($db);
			if ($c = SmartyCache::get($cache_name,3600)) {
				return true;
			}
		} else {
			$cache = false;	
		}
		
		
		$this->print = $print;
		$this->total_all = $this->total = 0;
		$this->page_data = $this->data = $this->total_entries = array();
		$this->row_by_row = $row_by_row;
		$this->content_name = $content_name;
		if ($modules_only) $this->modules_only = (array)$modules_only;
		$this->max_entries = $max_entries;
		$this->my_select = $my_select;
		
		$this->page_data['type'] = 'content';
		$this->getMenu($menu, $submenu);
		$this->getContentRow();
		if (!$this->total) return false;
		
		//if (!$this->id) $this->getContentEntries();
		$this->getContentEntries();
		
		$this->content_name = '';
		$this->modules_only = array();
		$this->max_entries = 20;
		$this->my_select = '';
		
		$this->pager = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit,
			'page_key' => $this->page_key
		));
		$this->offset = 0;
		$this->limit = 20;
		$this->row_by_row = false;
		if ($retData) {
			return array(
				'list'	=>& $this->data,
				'pager'	=>& $this->pager,
				'total'	=>& $this->total
			);
		}
		
		//$this->assignToTemplate();

		if ($print) {
			$this->catchPage();
			if ($cache) {
				$html = $this->Index->Smarty->fetch('content.tpl');
				SmartyCache::save($cache_name,$html,true);
				echo $html;
			} else {
				$this->Index->Smarty->display('content.tpl');
			}
		}
		
		return true;
	}
}