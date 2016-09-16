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
* @file       inc/Menu.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Menu {
	
	private
		$lang = false,
		$data = array(),
		$select = '',
		$func = false,
		$admin = false,
		$func_arg1 = false,
		$func_arg2 = false,
		$friendly_urls = false,
		$prefix_submenu = '&nbsp;&nbsp; - ',
		$disable_onsub = false,
		$menu = array(),
		$position = false,
		$name_as_key = false,
		$and_active = '',
		$prefix = '',
		$search = '',
		$filter = '',
		$filter_sub = '',
		$tpl = TEMPLATE
	;
	
	public $parentid_key = 'parentid', $position_key = 'position';
	
	public function __construct($params = array()) {
		$this->prefix = DB::getPrefix();
		if (!$this->lang) $this->lang = Session()->Lang;
		if ($params) $this->set($params);
	}
	
	public function set($params = array()) {
		if ($params) foreach ($params as $k => $v) $this->$k = $v;
		if (!$this->select) {
			$this->select = 'id, position, parentid, title_'.$this->lang.' AS title, icon, name, sort, target, `display`, url, cnt, cnt2, active, descr_'.$this->lang.' AS descr';
		}
		$this->Index = Index();
		if ($this->admin) $this->and_active = ' AND active!=2';
		else $this->and_active = Index()->sql('menu_active');
		return $this;
	}
	
	public function init($params = array()) {
		$this->set($params);
		return $this;	
	}
	
	public function setTree() {
		$arr_title = $arr_description = $arr_keywords = array();
		for ($i=0;$i<Site::MENU_LEVELS;$i++) {
			if (!isset($this->Index->menu[$i]) || !$this->Index->menu[$i]) break;
			if ($this->Index->menu[$i]['name']==URL_KEY_HOME) continue;
			$arr_title[$i] = $this->Index->menu[$i]['title'];
			$this->Index->tree[$i] = array(
				'title'	=> $this->Index->menu[$i]['title'],
				'name'	=> $this->Index->menu[$i]['name'],
				'url'	=> $this->Index->menu[$i]['url'],
				'type'	=> 'menu'
			);
			if ($this->Index->menu[$i]['title']) $arr_title[$i]= trim($this->Index->menu[$i]['title']);
			if ($this->Index->menu[$i]['descr']) $arr_description[$i] = $this->Index->menu[$i]['descr'];
			if ($this->Index->menu[$i]['keywords']) $arr_keywords[$i] = $this->Index->menu[$i]['keywords'];
		}
		if ($this->Index->menu[1]['title2']) $arr_title = array(trim($this->Index->menu[1]['title2']));
		elseif ($this->Index->menu[0]['title2'] && !$this->Index->menu[1]) $arr_title = array(trim($this->Index->menu[0]['title2']));
		
		/*
		
		if ($arr_description) $this->Index->setVar('description', html(join('. ',$arr_description)));
		if ($arr_keywords) $this->Index->setVar('keywords', html(join(', ',$arr_keywords)));
		*/
		
		if ($arr_title) $this->Index->setVar('title', html(join(lang('_#site_title_preparator'),$arr_title)));
		if ($arr_description) $this->Index->setVar('description', html($arr_description[count($arr_description)-1]));
		if ($arr_keywords) $this->Index->setVar('keywords', html($arr_keywords[count($arr_keywords)-1]));
	}
	
	public function get($id, $_id = 0, $fromIndex = false) {
		if (!$id) return array();
		if (in_array($_id, array('id',''))) $_id = 0;
		$ret = array();
		if (Conf()->g2('Menu::get',$id.'/'.$_id)) return Conf()->g2('Menu::get',$id.'/'.$_id);
		if (is_numeric($id) && !$_id) {
			$rs = DB::row('SELECT m.id, m.parentid FROM '.$this->prefix.'menu m WHERE id='.(int)$id.$this->and_active);
			if ($rs['parentid']) {
				$id = $rs['parentid'];
				$_id = $rs['id'];
			}
		}
		$select = 'id, title_'.$this->lang.' AS title, title2_'.$this->lang.' AS title2, descr_'.$this->lang.' AS descr, keywords_'.$this->lang.' AS keywords, name, `position`, parentid, url, icon, cnt, cnt2, cnt3';
		$where = '';
		if (is_numeric($id)) {
			$where .= 'm.id='.(int)$id;	
		} 
		elseif (is_array($id)) {
			$where .= 'm.name IN ('.join(', ', e($id)).')';
		} else {
			$where .= 'm.name='.e($id);
		}
		/*
		if ($_id) {
			$where .= ' AND parentid>0';
		} else {
			$where .= ' AND parentid=0';	
		}
		*/
		$where .= ' AND parentid=0';
		
		$sql = 'SELECT '.$select.' FROM '.$this->prefix.'menu m WHERE '.$where.$this->and_active;

		$ret[0] = DB::row($sql);
		if ($ret[0]) {
			$ret[0]['url'] = $this->url($ret[0]['position'], $ret[0]['name'], '', $ret[0]['url']);
		}
		if ($_id && $ret[0]) {
			$sql = 'SELECT '.$select.' FROM '.$this->prefix.'menu m WHERE '.(is_numeric($_id)?'m.id='.(int)$_id:'m.name LIKE '.e($_id).' AND m.parentid='.$ret[0]['id']).$this->and_active;
			$ret[1] = DB::row($sql);
			if ($ret[1]) {
				$ret[1]['url'] = $this->url($ret[1]['position'], $ret[1]['name'], $ret[0]['name'], $ret[1]['url']);
			}
		}
		$names = $titles = $urls = $tree = array();
		foreach ($ret as $i => $rs) {
			$names[$i] = $rs['name'];
			$titles[$i] = $rs['title2'] ? $rs['title2'] : $rs['title'];
			$urls[$i] = $rs['url'];
			$tree[] = '<a href="'.$urls[$i].'">'.$rs['title'].'</a>';
		}
		$ret[9] = end($ret);
		$ret['names'] = $names;
		$ret['titles'] = $titles;
		$ret['urls'] = $urls;
		$ret['tree'] = $tree;
		if ($fromIndex && $ret[0] && $ret[0]['id'] && !$ret[0]['parentid']) {
			$ret['sub'] = array();
			$qry = DB::qry('SELECT '.$select.' FROM '.$this->prefix.'menu m WHERE m.parentid='.$ret[0]['id'].$this->and_active.' ORDER BY sort, name',0,0);
			while ($rs = DB::fetch($qry)) {
				$rs['url'] = $this->url($ret[0]['position'], $rs['name'], $ret[0]['name'], $rs['url']);
				array_push($ret['sub'], $rs);
			}
			DB::free($qry);
		}
		Conf()->s2('Menu::get',$id.'/'.$_id,$ret);
		return $ret;
	}
	
	public function getByPosition($position = false) {
		if (!$this->select) return array();
		if (!$position) $position = $this->position;
		if ($this->search) {
			$this->filter .= ' AND ((m.title_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR m.title2_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR m.descr_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR m.keywords_'.$this->lang.' LIKE \'%'.$this->search.'%\') OR (SELECT 1 FROM '.$this->prefix.'menu y WHERE (y.title_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR y.title2_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR y.descr_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR y.keywords_'.$this->lang.' LIKE \'%'.$this->search.'%\') AND y.parentid=m.id LIMIT 1)=1)';
			$this->filter_sub .= ' AND (m.title_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR m.title2_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR m.descr_'.$this->lang.' LIKE \'%'.$this->search.'%\' OR m.keywords_'.$this->lang.' LIKE \'%'.$this->search.'%\')';
		}
		$sql = 'SELECT '.$this->select.' FROM '.$this->prefix.'menu m WHERE '.(is_array($position)?'m.position IN ('.join(', ',e($position)).')':'m.position='.e($position)).$this->and_active.$this->filter.' ORDER BY m.position, m.sort, m.name';
		$this->tree = DB::getAll($sql);
		if (is_array($position)) {
			foreach ($position as $p) {
				$ret[$p] = $this->buildTree($p);
			}
		} else {
			$ret = 	$this->buildTree();
		}
		return $ret;
	}
	
	public function buildTree($position = false, $parent_id = 0, $level = 0, $parent_rs = array()) {
		$ret = array();
		$i = 0;
		$fn = $this->func;
		foreach ($this->tree as $j => $rs) {
			if (isset($rs[$this->parentid_key]) && $rs[$this->parentid_key]==$parent_id && (!$position || ($position && $position==$rs[$this->position_key]))) {
				$index = ($this->name_as_key?$rs['name']:$i);
				if (!isset($rs[$this->position_key])) $rs[$this->position_key] = $position;
				if (isset($rs['name']) && isset($rs['url'])) {
					$rs['url'] = $this->url($rs[$this->position_key], $rs['name'], ($parent_rs?$parent_rs['name']:false), $rs['url'], $this->friendly_urls);
				}
				$rs['sub'] = $parent_id;
				$rs['alt'] = $rs['title'];
				if (!ADMIN && !Site::$mini && Index()->Edit) {
					$rs['admin'] = Index()->Edit->set($rs, 'menu', $rs['id'], 'id')->admin();
				}
				if ($fn) {
					$ret[$index] = $fn($rs, $this->func_arg1, $this->func_arg2);
				} else {
					$ret[$index] = $rs;
				}
				$_ret = $this->buildTree($position, $rs['id'], $level + 1, $rs);
				$ret[$index]['sub'] = $_ret;
				if ($_ret) {
					$ret[$index]['has_subs'] = true;	
				} else {
					$ret[$index]['has_subs'] = false;
					$ret[$index]['sub'] = false;
				}
				unset($this->tree[$j]);
				$i++;
			}
		}
		return $ret;
	}
	
	public function getAll($params = array(),$positions = array()) {
		$this->init($params);
		$qry = DB::qry('SELECT DISTINCT m.position FROM '.$this->prefix.'menu m WHERE TRUE'.($positions?' AND position IN (\''.join('\', \'',$positions).'\')':'').$this->and_active.' ORDER BY m.position',0,0);
		while ($rs = DB::fetch($qry)) {
			if (!$rs['position']) $rs['position'] = 'Untitled';
			$this->data[$rs['position']] = $this->getByPosition($rs['position']);
		}
		DB::free($qry);
		return $this;
	}

	public function toArray() {
		return $this->data;	
	}
	
	public function setSelected($selected) {
		$this->selected = $selected;
		return $this;
	}

	public function toOptions() {
		$ret = '';
		$is_arr = is_array($this->selected);
		foreach ($this->data as $position => $menu) {
			$ret .= '<optgroup label="'.$position.'">';
			foreach ($menu as $i => $m) {
				if (!$m['title']) continue;
				$s = ($is_arr ? (in_array($m['id'], $this->selected)) : $this->selected==$m['id']);
				$ret .= '<option value="'.$m['id'].'"'.($s?' selected="selected"':(($this->disable_onsub && $m['sub'])?' disabled="disabled"':'')).'>'.$m['title'].'</option>';
				if ($m['sub']) {
					foreach ($m['sub'] as $j => $_m) {
						if (!$_m['title']) continue;
						$s = ($is_arr ? (in_array($_m['id'], $this->selected)) : $this->selected==$_m['id']);
						$ret .= '<option value="'.$_m['id'].'"'.($s?' selected="selected"':'').'>'.$this->prefix_submenu.$_m['title'].'</option>';
					}
				}
			}
			$ret .= '</optgroup>';
		}
		return $ret;
	}
	
	public function getMenu() {
		return $this->Index->menu;	
	}
	
	public function printTree($id, $separator = ' &gt; ', $attr = ' target="_blank"') {
		if (!$id) return '';
		$rs = $this->getById($id);
		$ret = array();
		if (@$rs['parentid']) {
			$rs2 = $this->getById($rs['parentid']);
			$url = self::url($rs2['position'], $rs2['name'], '', $rs2['url']);
			$ret[] = '<a href="'.$url.'"'.$attr.'>'.$rs2['title'].'</a>';
			$url = self::url($rs2['position'], $rs2['name'], $rs['name'], $rs['url']);
			$ret[] = '<a href="'.$url.'"'.$attr.'>'.$rs['title'].'</a>';
			$this->menu = array(1=>$rs2, 2=>$rs);
		} else {
			$url = self::url($rs['position'], $rs['name'], '', $rs['url']);
			$ret[] = '<a href="'.$url.'"'.$attr.'>'.$rs['title'].'</a>';
			$this->menu = array(1=>$rs);
		}
		return join($separator, $ret);
	}
	
	public function getById($id) {
		$id = (int)$id;
		if (Conf()->g2('Menu::getById',$id)) return Conf()->g2('Menu::getById',$id);
		$ret = DB::row('SELECT `position`, name, url, parentid, title_'.$this->lang.' AS title FROM '.$this->prefix.'menu m WHERE id='.$id.$this->and_active);
		Conf()->s2('Menu::getById',$id,$ret);
		return $ret;
	}
	
	public function url($position, $name = '', $_name = '', $url = '', $friendly_urls = false) {
		if (!$url && $position && is_numeric($position)) {
			$rs = $this->getById($position);
			if (@$rs['parentid']) {
				$rs2 = $this->getById($rs['parentid']);
				$_name = $rs2['name'];
				$name = $rs['name'];
				$url = $rs['url'];
			} else {
				$name = $rs['name'];
				$url = $rs['url'];
			}
			$position = $rs['position'];
		}
		if (!$url) {
			$url = '?'.($this->tpl!=TEMPLATE?'template='.$this->tpl.AMP:'').$name;
			if ($_name) {
				$url = '?'.$_name.AMP.trim($url,'?');
			}
		}
		if (substr($url,0,1)=='?') $url = '/'.$url;
		return $url;
	}
	
	public function getPosition() {
		$ret = DB::one('SELECT `position` FROM '.$this->prefix.'menu');
		return $ret ? $ret : 'Untitled';
	}
	public function getPositions() {
		return DB::getAll('SELECT a.position, CONCAT(a.position,\' (\',(SELECT COUNT(1) FROM '.$this->prefix.'menu b WHERE b.position=a.position AND b.parentid=0),\')\') AS label FROM '.$this->prefix.'menu a GROUP BY a.position ORDER BY `position`','position|label');
	}
	public function getParentMenus($position, $not = 0) {
		return DB::getAll('SELECT id, title_'.$this->lang.' AS title FROM '.$this->prefix.'menu WHERE parentid=0 AND id!='.(int)$not.' AND `position` = '.e($position).' ORDER BY sort, title','id|title');	
	}
	public function positionName($position) {
		return $position;	
	}
	public function isID($id,$select = false) {
		if ($select) {
			return DB::row('SELECT '.$select.' FROM '.$this->prefix.'menu m WHERE id='.(int)$id.$this->and_active);
		} else {
			return DB::getNum('SELECT 1 FROM '.$this->prefix.'menu m WHERE id='.(int)$id.$this->and_active);
		}
	}
}