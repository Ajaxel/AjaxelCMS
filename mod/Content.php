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
* @file       mod/Content.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Content extends Object {
	
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
	
	private $search = false, $tag = false, $sitemap = false;
	
	protected
		$content_name,
		$modules_only = array(),
		$max_entries,
		$my_select,
		$pager,
	
		$arg_menu,
		$arg_submenu
	;
	
	private static $loaded = false;
	
	
	public function init($params = array()) {
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
		if ($this->Index) {
			$this->name = $this->Index->module_name;
			$this->id = $this->Index->module_id;
		}
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
	
	// ??
	public function findAll() {
		$setid = DB::row('SELECT setid FROM '.$this->prefix.'content_'.$this->Index->module_name.' WHERE rid='.$this->Index->module_id,'setid');
		if (!$setid) return false;
		$this->Index->content = DB::row('SELECT '.$this->content_select.' FROM '.$this->prefix.'content WHERE id='.$setid);
		$this->Index->menu = Factory::call('menu')->init()->get($this->Index->content['menuid']);
		Factory::call('menu')->setTree();
		$this->Index->tree[] = array(
			'title'	=> $this->Index->content['title'],
			'name'	=> $this->Index->content['name'],
			'url'	=> ((isset($this->Index->menu) && $this->Index->menu[9]) ? $this->Index->menu[9]['url'].AMP.$this->Index->content['name'] : '?id='.$this->Index->content['id']),
			'type'	=> 'content'
		);
	}
	
	
	
	public function &get($id, $getBySetID = false) {
		if (Conf()->g3('Content::get',$id,$getBySetID)) return Conf()->g3('Content::get',$id,$getBySetID);
		if (!$id) return array();
		$_id = 0;
		if ($getBySetID) {
			foreach ($this->Index->Site->getModules('content') as $m => $arr) {
				$_id = DB::row('SELECT setid FROM '.$this->prefix.'content_'.$m.' WHERE id='.(int)$id,'setid');
				if ($id) break;
			}
		}
		if (!$_id) $_id = $id;
		if (!$_id) return array();

		
		if (is_numeric($_id)) {
			$ret = DB::row('SELECT '.$this->content_select.' FROM '.$this->prefix.'content WHERE id='.$_id);
		} else {
			$ret = DB::row('SELECT '.$this->content_select.' FROM '.$this->prefix.'content WHERE (menuid='.$this->Index->menu[9]['id'].' OR (menuids!=\'\' AND menuids LIKE \'%,'.$this->Index->menu[9]['id'].',%\')) AND name LIKE '.e($_id));
			if (!$ret) {
				$ret = $this->getEntryById($_id);
			}
		}
		if ($ret) {
			$url = '';
			$this->Index->tree[] = array(
				'title'	=> $ret['title'],
				'name'	=> $ret['name'],
				'url'	=> ((isset($this->Index->menu) && $this->Index->menu[9]) ? $this->Index->menu[9]['url'].AMP.$ret['name'] : '?contentID='.$ret['id']),
				'type'	=> 'content'
			);
			if (isset($this->Index->Smarty)) $this->Index->Smarty->assign('tree',$this->Index->tree);
		}
		if (!isset($this->Index->content)) {
			$this->Index->content = $ret;
			$this->content_id = $this->Index->content['id'];
		}
		Conf()->s3('Content::get',$id,$getBySetID,$ret);
		return $ret;
	}
	
	private function getMenu($menu, $submenu) {
		if ($menu && !is_numeric($menu)) {
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
	
	public function getContentByID() {
		if (!$this->id) return false;
		$this->called_id = $this->id;
		$cols = DB::columns($this->table);
		if (!$cols) return false;
		
		$select = '';
		$this->data = array();
		$content_name = ''; //?
		$this->data[0] = array();
		if (in_array('main_photo', $cols)) {
			$select .= ', (CASE WHEN a.main_photo!=\'\' THEN a.main_photo ELSE (SELECT b.main_photo FROM '.DB_PREFIX.PREFIX.$this->table.' b WHERE b.id!=a.id AND b.rid=a.rid AND b.main_photo!=\'\' LIMIT 1) END) AS main_photo';	
		}
		if ($this->menu[9]) {
			$sql = 'SELECT a.*, '.e($this->name).' AS module'.$select.' FROM '.DB_PREFIX.PREFIX.$this->table.' a LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE a.rid='.$this->id.' AND c.active=1 AND a.lang=\''.$this->lang.'\' AND (SELECT 1 FROM '.DB_PREFIX.PREFIX.'menu m WHERE m.id=c.menuid'.Index()->sql('menu_active').')=1'.($content_name?' AND name='.e($content_name):'');
			$this->data[0]['entries'][0] = DB::row($sql);
			if (!$this->data[0]['entries'][0]) {
				$sql = 'SELECT a.*, '.e($this->name).' AS module'.$select.' FROM '.DB_PREFIX.PREFIX.$this->table.' a LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE a.rid='.$this->id.' AND c.active=1 AND a.lang=\''.$this->lang.'\' AND (c.menuid=0 OR (SELECT 1 FROM '.DB_PREFIX.PREFIX.'menu m WHERE m.id=c.menuid)!=1)'.($content_name?' AND name='.e($content_name):'');
				$this->data[0]['entries'][0] = DB::row($sql);
			}
		}
		elseif (!$content_name && $this->Index->content && $this->Index->content['id']) {
			$sql = 'SELECT a.*, '.e($this->name).' AS module'.$select.' FROM '.DB_PREFIX.PREFIX.$this->table.' a LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE a.rid='.$this->id.' AND a.lang=\''.$this->lang.'\' AND a.active=1 AND c.active=1';
			$this->data[0]['entries'][0] = DB::row($sql);
		}
		elseif ($this->name && $this->id) {
			/*
			$sql = 'SELECT a.*, '.e($this->name).' AS module'.$select.' FROM '.DB_PREFIX.PREFIX.$this->table.' a LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE a.rid='.$this->id.' AND a.lang=\''.$this->lang.'\' AND a.active=1 AND c.active=1';
			$this->data[0]['entries'][0] = DB::row($sql);
			p($this->data[0]['entries'][0]);
			p($sql);
			*/
			return false;
		}
		else {
			return false;
		}
		if (!$this->data[0]['entries'][0]) return false;
		
		$this->data[0]['entries'][0]['is_open'] = true;
		$this->total = 1;
		$this->Index->content = DB::row('SELECT '.$this->content_select.' FROM '.DB_PREFIX.PREFIX.'content WHERE id='.$this->data[0]['entries'][0]['setid']);
		$this->Index->content['is_open'] = true;
		$this->Index->content['total_entries'] = 1;
		$this->data[0]['content'] = $this->Index->content;
		$this->catchRow($this->data[0]['entries'][0], $this->Index->content);
		return true;	
	}
	
	private function getEntryByID($name = false) {
		if ($this->Index->content) {
			$this->total = 1;
			$this->data[0]['content'] = $this->Index->content;
			$this->data[0]['entries'] = array(0=>$this->Index->content);
			return $this->Index->content;
		}
		if (!$this->id && !$name) return false;
		$sql = 'SELECT *, \'\' AS inserts, \'0\' AS sort FROM '.DB_PREFIX.PREFIX.'entries WHERE '.($name?'name LIKE '.e($name).' AND lang=\''.$this->lang.'\'':'id='.$this->id);
		$this->Index->content = DB::row($sql);
		if (!$this->Index->content) return false;
		$this->total = $this->Index->content ? 1 : 0;
		$this->id = $this->Index->content['id'];
		$this->Index->content['is_open'] = true;
		$this->Index->content['module'] = 'entries';
		$this->Index->content['total_entries'] = 1;
		$this->catchRow($this->Index->content, $this->Index->content);
		$this->data[0]['content'] &= $this->Index->content;
		$this->data[0]['entries'] = array(0=>$this->Index->content);
		if ($name){
			return $this->Index->content;
		}
	}
	
	private function getContentRow() {
		if ($this->id && $this->name):
			$this->getContentByID();
		elseif ($this->id):
			$this->getEntryById();
		elseif ($this->Index->content && !$this->row_by_row):
			$this->content = array($this->Index->content);
			$this->total = count($this->content);
		elseif ($this->menu_id):
			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ((SELECT id, \'0\' AS rid, \'\' AS is_admin, \'0\' AS userid, \'content\' AS type, \'\' AS main_photo, name, title_'.$this->lang.' AS title, \'\' AS descr, \'\' AS teaser, \'\' AS body, \'\' AS bodylist, \'\' AS options, menuid, menuids, inserts, dated, added, views, viewtime, rate, `comment`, comments, keywords, sort FROM '.DB_PREFIX.PREFIX.'content WHERE (menuid='.$this->menu_id.' OR (menuids!=\'\' AND menuids LIKE \'%,'.$this->menu_id.',%\'))'.($this->content_name?' AND name LIKE '.e($this->content_name):'').' AND active=1 AND inserts!=\'\''.$this->filter.') UNION (SELECT id, rid, is_admin, userid, type, main_photo, name, title, descr, teaser, body, bodylist, options, menuid, menuids, \'\' AS inserts, dated, added, views, viewtime, rate, `comment`, comments, keywords, \'0\' AS sort FROM '.DB_PREFIX.PREFIX.'entries WHERE (menuid='.$this->menu_id.' OR (menuids!=\'\' AND menuids LIKE \'%,'.$this->menu_id.',%\'))'.($this->content_name?' AND name LIKE '.e($this->content_name):'').' AND lang=\''.$this->lang.'\' AND active=1'.$this->filter2.')) AS tmp'.($this->group?' GROUP BY '.$this->group:'').' ORDER BY '.$this->order.($this->limit?' LIMIT '.$this->offset.', '.$this->limit:'');
			$this->content = DB::getAll($sql);
			if (!isset($this->content['FOUND_ROWS()'])) {
				return;
			}
			
			$this->total = $this->content['FOUND_ROWS()'];
			unset($this->content['FOUND_ROWS()']);
		endif;
	}
	
	/**
	* Catch entry row, get additional data
	*/
	private function catchRow(&$rs, $content = false) {
		
		$rs['alt'] = html($rs['title']);
		
		$this->Index->Edit->set($rs, ($content['type']!='content' ? 'entries' : 'content_'.$rs['module']), $rs['id'], 'id')->parse()->admin();
		
		
		if ($content['type']=='content' && isset($this->total_entries[$content['id']])) {
			$rs['total_entries'] = $this->total_entries[$content['id']];
		} else {
			$rs['total_entries'] = $this->total;
		}
		if (isset($this->menu[0]) && $this->menu[0]['name']==URL_KEY_HOME && $this->menu[9]['name']==URL_KEY_HOME) {
			$this->menu[9]['url'] = '?'.URL_KEY_HOME;
		}
		
		if ($content['type']!='content') {
			if (!$this->id) $rs['url_is'] = true;
			else $rs['url_is'] = false;
		}
		elseif (($this->id && $this->name) || ($this->total_all==1 && $this->Index->content)) {
			$rs['url_is'] = false;
		}		
		elseif (!$rs['bodylist'] || !$this->Index->content || ($rs['total_entries'] > 1 && !$this->Index->content['is_open'])) {
			$rs['url_is'] = true;		
		}
		else {
			$rs['url_is'] = false;	
		}
		
		if (!isset($content['is_open'])) $content['is_open'] = false;
		
		if ($content['menuids'] && $content['menuid']!=$this->menu[0]['id'] && (!isset($this->menu[1]) || $content['menuid']!=$this->menu[1]['id'])) {
			$menu = Factory::call('menu')->get($content['menuid']);
			$rs['url_back'] = $menu[9]['url'];
		}
		elseif (isset($this->menu[9])) {
			$rs['url_back'] = $this->menu[9]['url'];
		}
		else $rs['url_back'] = '';
		
		if ($content['name'] && $content['is_open'] && $content['type']=='content') {
			if (!@$content['many_entries'] && $content['is_open']) {
				//$rs['url_back'] .= AMP.$content['name'];
			} else {
				$rs['url_back'] .= AMP.$content['name'];
			}
		}
		if ($this->search) {
			$rs['url_back'] = '?search='.$this->search.($this->page?AMP.URL_KEY_PAGE.'='.get(URL_KEY_PAGE):'');
			if ($rs['type']=='content') {
				$rs['url_open'] = $rs['url_back'].AMP.$rs['module'].'ID='.$rs['rid'];
			} else {
				$rs['url_open'] = $rs['url_back'].AMP.'id='.$rs['rid'];	
			}
		}
		elseif ($content['type']!='content') {
			$rs['url_open'] = $rs['url_back'].AMP.($content['name'] ? $content['name'] : '&id='.$content['id']);
		} else {
			if ($this->page_data['type']!='content') {
				$rs['url_open'] = '?'.$this->page_data['type'].'='.get($this->page_data['type']).AMP.$content['name'].AMP.$rs['module'].'ID='.$rs['rid'];
			}
			elseif (!$content['is_open']) {
				$rs['url_open'] = $rs['url_back'].AMP.$content['name'];
			} else {
				if (!@$content['many_entries'] && !$content['is_open']) {
					$rs['url_open'] = $rs['url_back'];
				}
				else {
					$rs['url_open'] = $rs['url_back'].AMP.$rs['module'].'ID='.$rs['rid'];
				}
			}
		}
		if (@$this->page_data['type']!='sitemap') {
			if ($rs['bodylist']) {
				$rs['is_open'] = $this->content_id || $this->id;
			} else {
				$rs['is_open'] = $this->id;
			}
			$rs['plain'] = in_array($rs['module'], $this->plain_modules);
			if (isset($rs['type']) && $rs['type']=='content') $rs['content'] = $content;
		}
		if (isset($rs['options'])) $rs['options'] = unserialize($rs['options']);

		if ($content['type']!='content') {
			$rs['table'] = 'entries';
		} else {
			$rs['table'] = 'content_'.$rs['module'];
		}

		$rs['content'] =& $content;
		
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
				$many_entries = ($this->total>1 && $this->total_entries[$content['id']]>1);
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
					$this->content[$index]['many_entries'] = $many_entries;
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
				if (!$ins['module']) continue;
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
					$this->data[] = $data[$entry];
				} else {
					$content['module'] = 'entries';
					$this->catchRow($content, $content);
					$this->data[] = $content;
				}
			}			
		} else {
			foreach ($this->content as $i => $content) {
				
				if ($content['type']!='content') {
					
					$content['module'] = 'entries';
					$content['table'] = 'entries';
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
						$entries[] =& $data[$entry];
					}
					if (!$entries) continue;
					
					$this->data[] = array(
						'content'	=> $content,
						'entries'	=> $entries,
						'total'		=> $this->total_entries[$content['id']]
					);
				}
			}
		}
	}
	
	public function modulesOnly($modules) {
		$this->modules_only = $modules;
		return $this;
	}
	
	public function setHead($row) {
		if (@$this->Index->vars['title']) {
			if (!isset($row['alt'])) $row['alt'] = $row['title'];
			$this->Index->preVar('title',$row['alt'].lang('_#site_title_preparator'));
			$this->Index->preVar('description',$this->menu[9]['descr']);
			$this->Index->preVar('keywords',$this->menu[9]['keywords']);
		}
		elseif (@$row['alt']) {
			$this->Index->preVar('title',($this->menu[9]['title2']?$this->menu[9]['title2'] : $row['alt']));
			$this->Index->preVar('description',$this->menu[9]['descr']);
			$this->Index->preVar('keywords',$this->menu[9]['keywords']);	
		}
	}
	
	private function assignToTemplate() {
		if (!$this->Index->Smarty) return false;
		if (!$this->total) return false;
		if ($this->menu[9] && $this->menu[9]['name']!=URL_KEY_HOME) {
			$this->Index->setVar('title',html($this->menu[9]['title']));
		}
		if ($this->id || $this->total_all==1) {
			if (isset($_GET['search']) && !$_GET['search']) {
				$this->data['entries'][0]['url_back'] = '?'.Url::get('search',$this->data['entries'][0]['module'].'ID','id');
			}
			$this->Index->Smarty->assign('total',1);
			if ($this->total>1 || $this->Index->content) {
				if ($this->menu[9]) {
					$this->Index->setVar('title',html($this->menu[9]['title']));
				} else {
					$this->Index->setVar('title',html($this->Index->content['title']));
				}
			} else {
				$this->Index->setVar('title',false);	
			}
			$this->Index->Smarty->assign('ID',$this->data[0]['entries'][0]);
			$this->setHead($this->data[0]['entries'][0]);
			if ($this->id) {
				
				$this->Index->tree[] = array(
					'title'	=> $this->data[0]['entries'][0]['alt'],
					'url'	=> $this->data[0]['entries'][0]['url_open'],
					'name'	=> $this->data[0]['entries'][0]['module'].'ID='. $this->data[0]['entries'][0]['id'],
					'type'	=> 'entry'
				);
			}
			// changed
			if (!$this->Index->content) $this->Index->content =& $this->content[0];
			$this->Index->content['url'] = $this->menu[9]['url'].($this->Index->content?AMP.$this->Index->content['name']:'');
			// changed
			$this->Index->content['is_open'] = true;
			$this->Index->Smarty->assign('row',$this->data[0]);
			$this->Index->Smarty->assign('list',false);
		}
		elseif ($this->total==1 && !$this->Index->content) {
			$content[0]['is_open'] = false;
			
			// $this->Index->content = $content[0];
			// changed
			$this->Index->content = $this->content[0];
			$this->content_id = $this->Index->content['id'];
			$this->Index->Smarty->assign('row',$this->data[0]);
			$this->Index->setVar('title',html($this->data[0]['entries'][0]['title']));
		}
		elseif ($this->total==1 && $this->total_all==1) {
			$this->Index->Smarty->assign('row',$this->data[0]);
			$this->Index->Smarty->assign('list',false);
			$this->Index->Smarty->assign('one_row', $this->total==1);
			$this->setHead($this->data[0]['entries'][0]);
			$this->Index->setVar('title',html($this->data[0]['entries'][0]['title']));
		} else {
			$this->Index->Smarty->assign('row',false);
			$this->Index->Smarty->assign('list',$this->data);
			$this->Index->Smarty->assign('one_row', false);
			if ($this->Index->content) {
				if ($this->menu[9]) {
					$this->setHead($this->Index->content);
				} else {
					$this->Index->setVar('title',html($this->Index->content['title']));
				}
			}
		}
		$this->Index->Smarty->assign('pager',$this->pager);
		$this->Index->Smarty->assign('content',$this->Index->content);
	}
	
	private function catchPage() {
		$this->page_data['url_back'] = '';
		$this->page_data['name_back'] = '';
		
		if (get('search')) {
			$this->page_data['url_back'] = '?search='.$this->search.($this->page?AMP.URL_KEY_PAGE.'='.get(URL_KEY_PAGE):'');
			$this->page_data['name_back'] = 'Back to search';
			$this->Index->Smarty->assign('page',$this->page_data);
			return;	
		}
		
		switch ($this->page_data['type']) {
			case 'sitemap':
				
			break;
			case 'search':
				$this->page_data['url_back'] = '?search';
				$this->page_data['name_back'] = 'Back to search';
			break;
			case 'tag':
			
			break;
			default:
				if ($this->total==1) {
					if ($this->id && $this->data[0]['content']['is_open']) {
						$this->page_data['url_back'] = $this->Index->menu[9]['url'];
						$this->page_data['name_back'] = $this->Index->menu[9]['title'];
						/*
						$this->page_data['url_back'] .= AMP.$this->Index->content['name'];
						$this->page_data['name_back'] = $this->Index->content['title'];
						*/
					}
				} else {
					$tree_count = count($this->Index->tree) - 2;
					if ($tree_count>=0) {
						$this->page_data['url_back'] = $this->Index->tree[$tree_count]['url'];
						$this->page_data['name_back'] = $this->Index->tree[$tree_count]['title'];
					}
				}
			break;	
		}
		$this->Index->Smarty->assign('page',$this->page_data);
	}
	
	/**
	* getContent method, should work nomatter what
	*/
	public function getContent($menu = '', $submenu = '', $print = true, $retData = false, $params = array(), $cache = false) {
		//if (!$this->id) $this->id = $this->Index->module_id;
		
		$_menu = ($menu?$menu:$this->Index->menu[0]['id']);
		$_submenu = ($submenu?$submenu:(isset($this->Index->menu[1]) ? $this->Index->menu[1]['id']: false));
		
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
		if (!$this->id) $this->getContentEntries();
		
		
		
		if (!$this->data) return false;
		$this->content_name = '';
		$this->max_entries = 20;
		$this->my_select = '';
		$this->pager = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit,
		));
		
		if ($retData) {
			if ($this->row_by_row) {
				$this->row_by_row = false;
				$this->data['pager'] = $this->pager;
				$this->data['total'] = $this->total;
				return $this->data;	
			} else {
				return array(
					'data'	=> $this->data,
					'total'	=> $this->total
				);
			}
		}
		
		$this->assignToTemplate();

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
	
	public function getTag($s) {
		$s = e($s,false);
		$this->tag = Parser::strSearch($s);
		$this->Index->Smarty->assign('tag', $this->tag);
		$this->Index->menu[0] = array(
			'url'	=> '?tag='.$this->tag.URL::build('page','limit','catid')
		);
		$this->page_data['type'] = 'tag';
		$this->Index->menu[9] =& $this->Index->menu[0];
		if ($this->id) {
			return $this->getContent();
		}
		elseif ($this->tag && strlen($s)>1) {
			$this->page_data['type'] = 'tag';
			$sql = 'SELECT SQL_CALC_FOUND_ROWS '.$this->content_select.', MATCH(keywords) AGAINST(\''.$s.'\' IN BOOLEAN MODE) AS rel FROM '.$this->prefix.'content WHERE keywords LIKE \'%'.$s.'%\' ORDER BY rel';
			$qry = DB::qry($sql, $this->offset, $this->limit);
			$this->total = DB::rows();
			if (!$this->total) return false;
			$modules = array();
			foreach ($this->Index->modules as $module => $m) {
				$m['module'] = $module;
				$modules[$m['id']] = $m;
			}			
			while ($rs = DB::fetch($qry)) {
				$this->content[] = $rs;
			}
			
			$this->getContentEntries();
			$this->assignToTemplate();
			
			$pager = Pager::get(array(
				'total'	=> $this->total,
				'limit'	=> $this->limit,
			));
			$this->Index->Smarty->assign('pager',$pager);
			$this->Index->Smarty->assign('total',$this->total);
			$this->Index->Smarty->assign('row',false);
			$this->Index->Smarty->assign('list',$this->data);
			$this->catchPage();
			$this->Index->displayFile('content.tpl');
			return true;	
		}
	}
	
	public function getSearch($s, $modules = array(), $body = true) {
		if (is_array($s)) $s = $s[key($s)];
		if (is_array($s)) return false;
		
		$s = e($s,false);
		$this->search = Parser::strSearch($s);
		
	
		
		$this->Index->Smarty->assign('search', $this->search);
		$this->Index->menu[0] = array(
			'url'	=> '?search='.$s.URL::build('page','limit','catid')
		);
		$this->page_data['type'] = 'search';
		$this->Index->menu[9] = $this->Index->menu[0];
		if ($this->id) {
			return $this->getContent();
		}
		elseif ($this->search && strlen($s)>1) {
			$data = $unions = $unions_cnt = $arr_select = $where = $cols = $has_catref = array();
			$index = 0;
			$tables = DB::tables();
			foreach ($this->Index->modules as $m => $arr) {
				if ($modules) {
					if (!in_array($m, $modules)) continue;
				}
				if (!in_array('content_'.$m, $tables) || $m=='html') continue;
				$arr_select[$m] = array('a.id', 'a.rid', 'a.title', 'a.descr', 'a.active', 'a.sort', 'a.added', 'a.edited');
				$cols[$m] = DB::columns('content_'.$m);
				$extra_select = array('main_photo','price','price_old','currency');
				foreach ($extra_select as $sel) {
					if (in_array($sel, $cols[$m])) {
						$arr_select[$m][] = 'a.'.$sel;
					} else {
						$arr_select[$m][] = '\'\' AS '.$sel;
					}
				}
				$has_catref[$m] = in_array('catref', $cols[$m]);
				$where[$m] = ' a.lang=\''.$this->lang.'\' AND a.active=1 AND (a.title LIKE \'%'.$s.'%\' OR c.keywords LIKE \'%'.$s.'%\' OR c.title_'.$this->lang.' LIKE \'%'.$s.'%\' OR a.descr LIKE \'%'.$s.'%\''.($body && in_array('body',$cols[$m])?' OR a.body LIKE \'%'.$s.'%\'':'').')';				
				
				$u = '(SELECT '.e($m).' AS module, \'content\' AS type, c.name AS content_name, a.setid, a.rid, '.join(', ',$arr_select[$m]).' FROM '.DB_PREFIX.PREFIX.'content_'.$m.' a LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE'.$where[$m].' AND c.active=\'1\'';
				$u_cnt = '(SELECT COUNT(1) FROM '.DB_PREFIX.PREFIX.'content_'.$m.' a LEFT JOIN '.DB_PREFIX.PREFIX.'content c ON c.id=a.setid WHERE'.$where[$m];
				if ($has_catref[$m] && $this->catid) {
					$catref = $this->Category->catRef($this->catid, 'category_'.$m);
					$u .= ' AND (catref=\''.$catref.'\' OR catref LIKE \''.$catref.'.%\')';
					$u_cnt .= ' AND (catref=\''.$catref.'\' OR catref LIKE \''.$catref.'.%\')';
				}
				$u .= ' AND (SELECT 1 FROM '.DB_PREFIX.PREFIX.'menu m WHERE m.id=c.menuid'.Index()->sql('menu_active').')=1';
				$u .= ' ORDER BY '.$this->order.')';
				$u_cnt .= ' LIMIT 1)';
				$unions[] = $u;
				$unions_cnt[] = $u_cnt;
			}
			
			$m = 'entries';
			$arr_select[$m] = array('a.id', 'a.rid', 'a.title', 'a.descr', 'a.active', '\'0\' AS sort', 'a.added', 'a.edited');
			$where[$m] = ' a.lang=\''.$this->lang.'\' AND a.active=1 AND (a.title LIKE \'%'.$s.'%\' OR a.keywords LIKE \'%'.$s.'%\' OR a.descr LIKE \'%'.$s.'%\''.($body && $cols[$m] && in_array('body',$cols[$m])?' OR a.body LIKE \'%'.$s.'%\'':'').')';				
			
			$u = '(SELECT \'entries\' AS module, a.type, a.name, \'0\' AS setid, a.rid, '.join(', ',$arr_select[$m]).', main_photo, \'\' AS price, \'\' AS price_old, \'\' AS currency FROM '.DB_PREFIX.PREFIX.'entries a WHERE'.$where[$m];
			$u_cnt = '(SELECT COUNT(1) FROM '.DB_PREFIX.PREFIX.'entries a WHERE'.$where[$m];
			if ($has_catref[$m] && $this->catid) {
				$catref = $this->Category->catRef($this->catid, 'category_'.$m);
				$u .= ' AND (catref=\''.$catref.'\' OR catref LIKE \''.$catref.'.%\')';
				$u_cnt .= ' AND (catref=\''.$catref.'\' OR catref LIKE \''.$catref.'.%\')';
			}
			//$u .= ' AND (SELECT 1 FROM '.DB_PREFIX.PREFIX.'menu m WHERE m.id=a.menuid'.Index()->sql('menu_active').')=1';
			$u .= ' ORDER BY '.$this->order.')';
			$u_cnt .= ' LIMIT 1)';

			$unions[] = $u;
			$unions_cnt[] = $u_cnt;
			

			if ($unions) {
				$sql = join(' UNION ',$unions).' ORDER BY sort';
				$sql_cnt = 'SELECT ('.join(' + ',$unions_cnt).') AS cnt';
				$this->total = DB::row($sql_cnt,'cnt');
				if ($this->offset > $this->total) {
					$this->offset = $this->total - 1;	
				}
				$qry = DB::qry($sql,$this->offset,$this->limit);
				while ($rs = DB::fetch($qry)) {
					$data[$index] = $rs;
					$content = array(
						'id'	=> $rs['setid'],
						'many_entries'	=> true,
						'name'	=> $rs['content_name'],
						'type'	=> $rs['type']
					);
					$this->catchRow($data[$index], $content);
					unset($data[$index]['content_name']);
					$index++;
				}
				$pager = Pager::get(array(
					'total'	=> $this->total,
					'limit'	=> $this->limit,
				));
			}
			$this->Index->Smarty->assign('pager',$pager);
			$this->Index->Smarty->assign('total',$this->total);
			$this->Index->Smarty->assign('row',false);
			$this->Index->Smarty->assign('list',$data);
			$this->catchPage();
			$this->Index->displayFile('search.tpl');
			return true;
		} else {
			$this->catchPage();
			$this->Index->displayFile('search.tpl');
			return true;
		}
	}
	
	public function getSitemap($positions = array()) {
		$this->sitemap = true;
		$ret = array();
		$this->setLimit(0);
		$menu_data = Factory::call('menu')->getAll(array(), $positions)->toArray();
		$index = 0;
		$prevMenu = $this->Index->menu;
		foreach ($menu_data as $position => $arrMenu) {
			foreach ($arrMenu as $name => $menu) {
				$this->Index->menu[0] = $menu;
				$ret[$position][$index] = array(
					'menu'		=> $menu,
					'content' 	=> $this->getContent($menu['id'], false, false, true)
				);
				if ($menu['sub']) {
					foreach ($menu['sub'] as $_name => $_menu) {
						$this->Index->menu[1] = $_menu;
						$this->Index->menu[9] = $_menu;
						$ret[$position][$index]['sub'][] = array(
							'menu'		=> $_menu,
							'content' 	=> $this->getContent($menu['id'], $_menu['id'], false, true)
						);
					}
				}
				$index++;
			}
		}
		$this->page_data['type'] = 'sitemap';
		$this->Index->menu = $prevMenu;
		$this->Index->Smarty->assign('data',$ret);
		$this->Index->displayFile('sitemap.tpl');
	}	
}