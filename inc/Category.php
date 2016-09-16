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
* @file       inc/Category.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Category {

	public 
		$lang = 'en',
		$table = 'categories',
		$order = '',
		$filter = '',
		$catalogue = false,
		
	//	$retCount = false,
		$retIsOpen = true,
		$countTable = '',
		$countTableExact = true,
		$countTableMulti = false,
		$countWhere = '',
		
		$getAfter = false,
		$getSubOnly = false,
		
		$maxLevel = 0,
		
		$retOptions = false,
		$optValue = 'catid',
		
		$getHidden = false,
		$noDisable = false,
		$select = '',
		$selected = false,
		$traceSelected = false,
		
		$asTree = true,
		
		$optionSet = array(
			'delimiter'	=> '&nbsp;&#9474;', // |
			'bullet'	=> '&nbsp;&#9500; ', // |-
			'ending'	=> '&nbsp;&#9492; ' // |_
		)
	;
	
	private
		$as_array = false,
		$selected_new = false,
		$_selected_arr = false,
		$_indexes = false,
		$data = array(),
		$only = array(),
		$prefix = '',
		$cache = true
	;
	
	
	public function __construct($params = array()) {
		$this->prefix = DB_PREFIX.PREFIX;
		$this->lang = LANG;
		$this->filter = '';
		foreach ((array)$params as $k => $v) $this->$k = $v;
		if ($this->maxLevel===true) {
			$this->maxLevel = 1;
			$this->traceSelected = true;
		}
	}
	
	public function cache($cache) {
		$this->cache = $cache;
		return $this;	
	}
	
	public function getAll() {
		if (!in_array($this->table, DB::tables())) {
			return $this;
		}
		$this->_selected_arr = is_array($this->selected);
		$this->indexes = array();
		if ($this->retOptions) $this->_indexes = array(); else $this->_indexes = false;
		$this->getAfter = self::catRef($this->getAfter, $this->table);
		$this->getSubOnly = self::catRef($this->getSubOnly, $this->table);
		$ex = explode('.',$this->getSubOnly);
		$this->getSubOnly = $ex[0];
		$this->selected_catref = self::catRef($this->selected, $this->table);
		$sql = $this->sql();
		$this->data = DB::getAll($sql);
		$this->trace();
		$this->data = $this->buildTree($this->data, $this->getAfter, 1);
		
		$this->traceSelected = false;
		$this->maxLevel = 0;
		$this->getAfter = false;
		$this->countTable = false;
		$this->selected = false;
		return $this;
	}
	
	private function buildTree($data, $after, $level) {
		$i = 0;
		$ret = array();
		if (!$data) return $ret;
		foreach ($data as $j => $rs) {
			$parent = self::_getParent($rs['catref']);
			if ((!$parent && !$after) || 'c'.$parent=='c'.$after) {
				$this->indexes[] = $rs['catref'];
				$ret[$i] = $rs;
				if (
						(!$this->_selected_arr && $this->selected==$rs[$this->optValue]) 
					|| 
						($this->_selected_arr && in_array($rs[$this->optValue],$this->selected))
				) $ret[$i]['selected'] = true;
				
				if ($this->traceSelected || ($this->maxLevel && $level < $this->maxLevel) || (!$this->maxLevel && $rs['sublevels'])) {
					$ret[$i]['sub'] = $this->buildTree($data, $rs['catref'], $level + 1);
				}
				$i++;
			}
		}
		
		return $ret;
	}
	
	private function trace() {
		if ($this->selected && $this->traceSelected) {
			$tree = $this->tree($this->selected);
			$getAfter = $this->getAfter;
			$maxLevel = $this->maxLevel;
			$exists = array();
			foreach ($tree as $i => $cat) {
				$this->maxLevel = 1;
				$this->getAfter = $cat['catref'];
				$sql = $this->sql();
				$qry = DB::qry($sql,0,0);
				while ($rs = DB::fetch($qry)) {
					array_push($this->data,$rs);
				}
				DB::free($qry);
			}
			$this->getAfter = $getAfter;
			$this->maxLevel = $maxLevel;
		} else $this->traceSelected = false;	
	}
	
	private function sql() {
		$cnt = $cnt2 = '';
		/*
		if ($this->retCount && 0) {
			if ($this->table=='forum_cats' || true) {
				$cnt = ', c.cnt AS total, c.sum AS totalsum';
			} else {
				$cnt = ', c.cnt_'.$this->lang.' AS cnt, (';
				foreach (Site()->getLanguages() as $l => $x) {
					$cnt .= 'cnt_'.$l.'+';
				}
				$cnt = substr($cnt,0,-1).') AS total';
	
				$cnt2 = ', c.sum_'.$this->lang.' AS sum, (';
				foreach (Site()->getLanguages() as $l => $x) {
					$cnt2 .= 'sum_'.$l.'+';
				}
				$cnt2 = substr($cnt2,0,-1).') AS totalsum';
			}
		}
		*/
		if ($this->lang!=DEFAULT_LANGUAGE) {
			$sel = '(CASE WHEN c.catname_'.$this->lang.'!=\'\' THEN c.catname_'.$this->lang.' ELSE c.catname_'.DEFAULT_LANGUAGE.' END)';
		} else {
			$sel = 'c.catname_'.$this->lang;
		}
		$sel .= ' AS catname';
		$sel .= $this->select;
		$sql = 'SELECT c.catid, c.catref, c.icon, c.name, c.sort'.(IS_ADMIN?', c.hidden':'');
		if (!$this->_selected_arr && $this->retIsOpen) {
			$sql .= ', (CASE WHEN c.catref='.e($this->selected_catref).' OR '.e($this->selected_catref.'.').' LIKE CONCAT(c.catref,\'.%\') THEN 1 ELSE 0 END) AS is_open';
		}
		$sql .= ', '.$sel.$cnt.$cnt2;
		
		if (!$this->maxLevel) {
			$sql .= ', (SELECT 1 FROM '.$this->prefix.$this->table.' mc WHERE mc.catref LIKE CONCAT(c.catref,\'.%\') LIMIT 1) AS sublevels';
		}
		
		if ($this->countTable) {
			if (is_array($this->countTable)) {
				/*
				$j = array();
				foreach ( as $ct) {
					
					if ($this->countTableExact) {
						$j[] = '(SELECT COUNT(1) FROM '.$ct['table'].' cc WHERE cc.'.$ct['column'].'=c.catref'.$this->countWhere.@$ct['where'].')';
					} else {
						$j[] = '(SELECT COUNT(1) FROM '.$ct['table'].' cc WHERE (cc.'.$ct['column'].'=c.catref OR cc.catref LIKE CONCAT(c.catref,\'.%\'))'.$this->countWhere.@$ct['where'].')';
					}
					
					$j[] = '('.$ct.')';
				}
				*/
				$sql .= ', ('.join(' + ',$this->countTable).') AS cnt';
			}
			elseif ($this->countTableMulti) {
				$sql .= ', (SELECT COUNT(1) FROM '.$this->prefix.$this->countTable.' cc WHERE cc.catrefs LIKE CONCAT(\'%,\',c.catref,\',%\')'.$this->countWhere.') AS cnt';	
			}
			elseif ($this->countTableExact) {
				$sql .= ', (SELECT COUNT(1) FROM '.$this->prefix.$this->countTable.' cc WHERE (cc.catref=c.catref OR cc.catref LIKE CONCAT(c.catref,\'.%\'))'.$this->countWhere.') AS cnt';
			} else {
				$sql .= ', (SELECT COUNT(1) FROM '.$this->prefix.$this->countTable.' cc WHERE (cc.catref=c.catref OR cc.catref LIKE CONCAT(c.catref,\'.%\'))'.$this->countWhere.') AS cnt';
			}
		}

		$sql .= ' FROM '.$this->prefix.$this->table.' c WHERE '.(!$this->getHidden?'c.hidden!=\'1\'':'TRUE');
		
		if ($this->getSubOnly) {
			$sql .= ' AND (c.catref=c.catid OR c.catref LIKE \''.$this->getSubOnly.'.%\')';
		}
		elseif ($this->getAfter && !is_array($this->getAfter)) {
			if ($this->maxLevel) $sql .= ' AND c.catref REGEXP \'^'.Parser::strRegex($this->getAfter).'\.([0-9]+)'.str_repeat('\.?([0-9]*)',$this->maxLevel-1).'$\'';
			else $sql .= ' AND c.catref LIKE \''.Parser::strRegex($this->getAfter).'.%\'';
		} else {
			if ($this->maxLevel==1) $sql .= ' AND c.catref=c.catid';
			elseif ($this->maxLevel) $sql .= ' AND c.catref REGEXP \'^([0-9]*)'.str_repeat('\.?([0-9]*)',$this->maxLevel-1).'$\'';
		}
		if ($this->catalogue) $sql .= ' AND c.catalogue='.e($this->catalogue);
		$sql .= $this->filter.' ORDER BY '.(($this->order && $this->order!='catref')?$this->order:'c.sort, catname');
		return $sql;	
	}
	
	public function _get($catid,$col = false) {
		if (!$catid) return array();
		if ($this->lang!=DEFAULT_LANGUAGE) {
			$sel = '(CASE WHEN c.catname_'.$this->lang.'!=\'\' THEN c.catname_'.$this->lang.' ELSE c.catname_'.DEFAULT_LANGUAGE.' END)';
		} else {
			$sel = 'c.catname_'.$this->lang;
		}
		$sql = 'SELECT '.$sel.' AS catname, c.catref FROM '.$this->prefix.$this->table.' c WHERE c.catid='.(int)$catid;
		return DB::row($sql,$col);
	}
	
	public function get($catid,$table,$col = false) {
		if (!$catid) return array();
		if ($this->lang!=DEFAULT_LANGUAGE) {
			$sel = '(CASE WHEN c.catname_'.LANG.'!=\'\' THEN c.catname_'.LANG.' ELSE c.catname_'.DEFAULT_LANGUAGE.' END)';
		} else {
			$sel = 'c.catname_'.LANG;
		}
		$sql = 'SELECT '.$sel.' AS catname, c.catref FROM '.DB_PREFIX.PREFIX.$table.' c WHERE c.catid='.(int)$catid;
		return DB::row($sql,$col);
	}
	
	public function selected($sel) {
		$this->selected_new = $sel;
		return $this;	
	}
	public function setSelected($sel) {
		return $this->selected($sel);
	}
	public function after($after) {
		$this->getAfter = $after;
		return $this;	
	}
	public function getAfter($after) {
		return $this->after($after);
	}
	public function isLast($catref, $table = '') {
		if (!$table) $table = $this->table;
		$catref = e($catref,false);
		if (DB::one('SELECT 1 FROM '.$this->prefix.$table.' WHERE catref=\''.$catref.'\'')) {
			return !DB::one('SELECT 1 FROM '.$this->prefix.$table.' WHERE catref LIKE \''.$catref.'.%\'');
		} else {
			return false;
		}
	}
	
	public function only($catrefs) {
		$this->only = self::joinSplit($catrefs);
		return $this;
	}
	
	public function toArray() {
		return $this->data;	
	}
	
	public function toTableCells($cols = 3, $function = false) {
		$ret = '<td>';
		$total = count($this->data);
		$per_cell = floor($total / $cols);
		$next = 1;
		foreach ($this->data as $i => $v) {
			if ($i > $per_cell*$next) {
				$ret .= '</td><td>';
				$next++;
			}
			if ($function) {
				$ret .= call_user_func_array($function,array($v));
			} else {
				$ret .= $v['catname'].'<br />';
			}
		}
		$ret .= '</td>';
		return str_replace('<br /></td>','</td>',$ret);
	}
	
	public function toTable($cols = 3, $fn = false) {
		$ret = '';
		$next = 1;
		foreach ($this->data as $i => $v) {
			if ($i%$cols==0) $ret .= '<tr>';
			$ret .= '<td>';
			if ($fn) {
				$ret .= call_user_func_array($fn,array($v));
			} else {
				$ret .= $v['catname'];
			}
			$ret .= '</td>';
			if ($i%$cols==$cols-1) $ret .= '</tr>';
		}
		return $ret;
	}
	
	public function toJSON() {
		return json($this->data);	
	}
	
	public function toOptions() {
		$i = 0;
		return $this->dropDown($this->data,$i);
	}
	
	public function toArrayOptions() {
		$this->as_array = true;
		$i = 0;
		return $this->dropDown($this->data,$i);
	}
	
	
	
	private function dropDown($data, &$i) {
		$r = ($this->as_array ? array() : '');
		if (!$data) return $r;
		
		foreach ($data as $key => $cat) {			
			if ($this->only && !in_array($cat['catref'], $this->only)) continue;
			if (!isset($cat['selected'])) $cat['selected'] = false;
			if (strpos($this->indexes[$i],'.')) $level = count(explode('.',$this->indexes[$i]));else $level = 1;
			if (isset($this->indexes[$i+1]) && strpos($this->indexes[$i+1],'.')) $next_level = count(explode('.',$this->indexes[$i+1])); else $next_level = 1;
			$sl = $cat['hidden'];
			if (!$sl && !$this->noDisable && 
				(
				 (isset($cat['sublevels']) && $cat['sublevels']) 
				 || 
				 (!isset($cat['sublevels']) && DB::one('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE catref LIKE \''.$cat['catref'].'.%\''))
				 )
			) $sl = true;
			
			$sep = '';
			if ($this->asTree) {				
				if ($level > 1) $sep = str_repeat($this->optionSet['delimiter'],$level-2); // |
				if ($next_level < $level) $sep .= $this->optionSet['ending']; // |_
				elseif ($level > 1) $sep .= $this->optionSet['bullet']; // |-
			}
			
			if ($this->as_array) {
				$r[] = array(
					'sep'		=> $sep,
					'text'		=> $cat['catname'],
					'cnt'		=> @$cat['cnt'],
					'value'		=> $cat[$this->optValue],
					'selected'	=> ($this->selected_new ? $this->selected_new==$cat[$this->optValue] : $cat['selected'])
				);
			} else {
				if ($this->selected_new) {
					$r .= '<option value="'.$cat[$this->optValue].'"'.($this->selected_new==$cat[$this->optValue]?' selected="selected"':'').($sl?($this->noDisable?' style="color:#888"':' disabled="true"'):'').'>';
				} else {
					$r .= '<option value="'.$cat[$this->optValue].'"'.($cat['selected']?' selected="selected"':'').($sl?($this->noDisable?' style="color:#888"':' disabled="true"'):'').'>';
				}
				$r .= $sep;
				
				$r .= $cat['catname'];
				if (isset($cat['cnt']) && $cat['cnt']) $r .= ' ('.$cat['cnt'].')';
				/*
				if ($cat['sum']) $r .= ' ['.$cat['sum'].']';
			//	if ($cat['sublevels']) $r .= ' - '.$cat['sublevels'];
				*/
				$r .= '</option>';
			}
			$i++;
			if (isset($cat['sub']) && $cat['sub']) {
				if ($this->as_array) {
					$r = array_merge($r,$this->dropDown($cat['sub'],$i));
				} else {
					$r .= $this->dropDown($cat['sub'],$i);
				}
			}
		}
		return $r;
	}
	
	public static function getByNames(array $names, $table) {
		$catref = $url = '';
		$catid = 0;
		$ret = array();
		foreach ($names as $i => $name) {
			$sql = 'SELECT catref, catid, catname_'.LANG.' AS catname, name FROM '.DB::prefix($table).' WHERE catref REGEXP \'^'.($catref?Parser::strRegex($catref).'\.':'').'([0-9]+)\' AND name='.e($name);
			$row = DB::row($sql);
			if ($row) {
				$ret = $row;
				$catref = $row['catref'];
				Index()->tree['?'.ltrim($url,'&').'&'.$row['name']] = array(
					'title'	=> $row['catname'],
					'name'	=> $row['name'],
					'url'	=> '?'.ltrim($url,'&').'&'.$row['name'],
					'type'	=> 'category'
				);
				$url .= '&'.$row['name'];
			} else break;
		}
		return $ret;
	}
	
	public static function exists($catid,$table) {
		if (!$catid || strpos($catid,'.')) return $catid;
		if ($table=='category_forum') $table = 'forum_cats';
		$ret = DB::row('SELECT 1 FROM '.DB::prefix($table).' WHERE catid='.(int)$catid,0,1);
		return $ret ? true : false;
	}	
	public static function catRef($catid,$table) {
		if (!$catid || is_array($catid) || strpos($catid,'.')) return $catid;
		if ($table=='category_forum') $table = 'forum_cats';
		if (Conf()->g2('Category::catRef',$table.'_'.$catid)) return Conf()->g2('Category::catRef',$table.'_'.$catid);
		$ret = DB::one('SELECT catref FROM '.DB::prefix($table).' WHERE catid='.(int)$catid,0,1);
		Conf()->s2('Category::catRef',$table.'_'.$catid,$ret);
		return $ret;
	}
	
	
	public static function _getParent($catref) {
		//if (!strstr($catref,'.')) return 0;
		return substr($catref, 0, strrpos($catref, '.'));
	}
	
	public static function getParent($catref,$pop=1) {
		if (!strstr($catref,'.')) return $catref;
		$catexp = explode('.',$catref);
		if ($pop==1) array_pop($catexp);
		else {
			for ($i=0;$i<$pop;$i++) array_pop($catexp);
		}
		return join('.',$catexp);
	}
	public function isOpen($selected, $relateTo, $table) {
		if ($table) $selected = $this->catRef($selected, $table);
		return in_array($relateTo, self::joinSplit($selected));
	}

	private static function _joinSplit($c, $del, &$ret) {
		$ex = explode($del,$c);
		$ret = array();
		$count = count($ex);
		for ($i=0;$i<$count;$i++) {
			$t = array();
			foreach ($ex as $n => $e) {
				$t[] = (int)$e;
				if ($i==$n) break;
			}
			$ret[] = join($del,$t);
		}
	}

	public static function joinSplit($catref=0,$del = '.') {
		if (is_array($catref)) {
			$ret = array();
			foreach ($catref as $c) {
				self::_joinSplit($c, $del, $ret);		
			}
			return $ret;
		}
		$ex = explode($del,$catref);
		$ret = array();
		$count = count($ex);
		for ($i=0;$i<$count;$i++) {
			$t = array();
			foreach ($ex as $n => $e) {
				$t[] = (int)$e;
				if ($i==$n) break;
			}
			$ret[] = join($del,$t);
		}
		return $ret;
	}
	
	public function split($catref) {
		return self::joinSplit($catref);
	}
	
	public function join($data, $join) {
		if (!$join) return $data;
		$j = array();
		$total = count($data);
		foreach ($data as $i => $rs) {
			if (!$join['link'] || ($join['no_last'] && $i+1==$total)) {
				$j[] = $rs['catname'];
			}
			else {
				$j[] = '<a href="'.str_replace(array('{$catid}','{$catref}','{$icon}'),array($rs['catid'],$rs['catid'],$rs['icon']),$join['link']).'"'.@$join['attr'].'>'.$rs['catname'].'</a>';
			}
		}
		return join($join['join'],$j);
	}

	public function tree($catref, $table = false, $join = false) {
		if (!$catref) return array();
		if (!$table) $table = $this->table;
		if ($this->cache && Conf()->exists('Category::tree',$table,$catref)) return $this->join(Conf()->g('Category::tree',$table,$catref),$join);
		$catref = $this->catRef($catref,$table);
		$arr = self::joinSplit($catref);
		$sql = 'SELECT catname_'.$this->lang.' AS catname, catid, catref, icon, name FROM '.$this->prefix.$table.' WHERE catref IN (\''.join('\', \'',$arr).'\') ORDER BY catref';
		$qry = DB::qry($sql,0,0);
		$data = array();
		$url = '';
		while ($rs = DB::fetch($qry)) {
			$url .= '&'.$rs['name'].'';
			$rs['url'] = '?'.ltrim($url,'&');
			array_push($data, $rs);
		}
		
		if ($this->cache) Conf()->s('Category::tree',$table,$catref,$data);
		return $this->join($data,$join);
	}

	public function links($catref, $table, $join = false) {
		if (!$catref) return array();
		if (!$table) $table = $this->table;
		$catref = e((array)$catref,false);
		$sql = 'SELECT catname_'.$this->lang.' AS catname, catid, catref, icon, name FROM '.$this->prefix.$table.' WHERE catref IN ('.join(', ',$catref).') ORDER BY catref';
		$data = DB::getAll($sql);
		return $this->join($data,$join);
	}












	/*
	function updateCatCnt($location,$catid,$lang='na',$add=true) {
		global $CD_GLOBALS;
		$cols = showColumns($location.'_cats');
		$catref = getCatRefByCatID($catid, $location);
		$catref = e($catref);
		$up = $sup = array();
		if ($lang && $lang!='na' && $lang!='xx' && in_array('cnt_'.$lang,$cols)) {
			$up[] = 'cnt_'.$lang.'=ABS(cnt_'.$lang.($add?'+1':'-1').')';
			$sum = dbFetch(dbRetrieve('SELECT ABS(SUM(cnt_'.$lang.')'.($add?'+1':'-1').') AS sum FROM '.$location.'_cats WHERE catref LIKE \''.$catref.'.%\' OR catref=\''.$catref.'\'',true,0,1),'sum');
			$sup[] = 'sum_'.$lang.'='.(int)$sum;
			
		} else {
			foreach ($CD_GLOBALS->g('langs') as $l) {
				if (in_array('cnt_'.$l['code'],$cols)) $up[] = 'cnt_'.$l['code'].'=ABS(cnt_'.$l['code'].($add?'+1':'-1').')';
				$sum = dbFetch(dbRetrieve('SELECT ABS(SUM(cnt_'.$l['code'].')'.($add?'+1':'-1').') AS sum FROM '.$location.'_cats WHERE catref LIKE \''.$catref.'.%\' OR catref=\''.$catref.'\'',true,0,1),'sum');
				if (in_array('sum_'.$l['code'],$cols)) $sup[] = 'sum_'.$l['code'].'='.(int)$sum;
			}
		}
		if (!$up[0]) return false;
		dbExecute('UPDATE '.$location.'_cats SET '.join(', ',$up).' WHERE catref=\''.$catref.'\'',true);
		$catrefs = array();
		foreach (joinSplit($catref,'.') as $c) $catrefs[] = 'catref=\''.$c.'\'';
		if ($sup) dbExecute('UPDATE '.$location.'_cats SET '.join(', ',$sup).' WHERE '.join(' OR ',$catrefs),true);
		dbCommit();
	}
	*/
}