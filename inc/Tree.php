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
* @file       inc/Tree.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Tree {	
	public
		$prefix = DB_PREFIX,
		$table = 'tree',
		$select = '',
		$where = '',
		$offset = 0,
		$limit = 0,
		$total = 0,
	
		$find = false,
	
		$parent_id_col = 'parentid',
		$global_id_col = 'globalid', // TODO
		$name_col_select = 'title',
		$name_col = 'title',
		$uname_col = 'name',
		$id_col = 'id',
		$level_col_as = 'level',
		$sort_col = 'sort',
		$sort_order = 'ASC',
	
		$selected = 0,
		$not = 0,
		$disable_parents = false,
		$selected_attr = ' selected="selected"',
		$level_attr = '→ ',
		$max_level = 0,
		$parent_id = 0,
		$use_open = false,
		$use_ul = true,
	
		$names = array(),
	
		$use_levels = false,
	
		$resort_min = false,
		$resort_reverse = true,
		$resort_smart = false,
	
		$offset_smart = true,
		
		$and_active = ''
	;
	
	private
		$_tree = array(),
		$__tree = array(),
		$_parent_ids = array(),
		$_selected_is_array = false,
	
		$_sort = 0,
		$_cycles = 0,
		$_max_cycles = 1000,
		$_params = array(),
		$_parents = array(),
	
		$_level = 0
	;
	
	
	public function set($params = array()) {
		$this->prefix = PREFIX;
		$this->use_levels = false;
		$this->_params = $params;
		foreach ($params as $k => $val) $this->$k = $val;
		if (!isset($params['prefix']) && Conf()->in('global_tables',$this->table)) $this->prefix = DB_PREFIX;
		$this->_tree = array();
		$this->selected();
		if ($this->resort_smart) $this->_sort = array();
		if ($this->max_level==1 || (!$this->parent_id && $this->use_open)) {
			$this->where .= ' AND `'.$this->parent_id_col.'`=0';
		}
		elseif ($this->parent_id && $this->use_open) {
			$this->where .= ' AND `'.$this->parent_id_col.'`='.$this->parent_id;
		}
		$this->_cycles = 0;
		return $this;
	}
	
	public function param($key, $val) {
		$this->$key = $val;
		return $this;
	}
	
	
	public function selected($sel = false) {

		
		if ($sel) $this->selected = $sel;
		$this->_selected_is_array = is_array($this->selected);
		return $this;
	}
	
	public function not($not) {
		$this->not = $not;
		return $this;
	}
	
	public function getAll() {
		return $this->_tree;	
	}
	
	public function get($params = false, $parent_id = 0) {
		if ($params) foreach ($params as $k => $val) $this->$k = $val;
		// TODO: $this->use_open
		$sql = 'SELECT SQL_CALC_FOUND_ROWS a.'.$this->id_col.', a.'.$this->sort_col.', a.'.$this->parent_id_col.', '.$this->name_col_select.$this->select.' FROM '.$this->prefix.$this->table.' a WHERE TRUE'.$this->where.' ORDER BY a.'.$this->sort_col.' '.$this->sort_order.', a.'.$this->id_col;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		$i = 0;
		while ($row = DB::fetch($qry)) {
			$this->_tree[$i] = $row;
			$this->_parent_ids[$row[$this->parent_id_col]] = true;
			$i++;
		}

		DB::free($qry);
		return $this;
	}
	
	public function getPrevNext($id) {

		$parent = DB::row('SELECT a.'.$this->parent_id_col.', a.'.$this->sort_col.' FROM '.$this->prefix.$this->table.' a WHERE '.$this->id_col.'='.(int)$id);
		if (!$parent) return false;
		$parentid = (int)$parent[$this->parent_id_col];
		$sort = (int)$parent[$this->sort_col];
		$ret = array();
		$ret['prev'] = DB::row('SELECT a.'.$this->id_col.', a.'.$this->sort_col.', a.'.$this->parent_id_col.', '.$this->name_col_select.$this->select.' FROM '.$this->prefix.$this->table.' a WHERE '.$this->parent_id_col.'='.$parentid.' AND a.'.$this->sort_col.'<'.$sort.' ORDER BY a.'.$this->sort_col.' DESC');
		$ret['next'] = DB::row('SELECT a.'.$this->id_col.', a.'.$this->sort_col.', a.'.$this->parent_id_col.', '.$this->name_col_select.$this->select.' FROM '.$this->prefix.$this->table.' a WHERE '.$this->parent_id_col.'='.$parentid.' AND a.'.$this->sort_col.'>'.$sort.' ORDER BY a.'.$this->sort_col.'');
		
		return $ret;
	}
	
	private function toArr($i) {
		return $this->_tree[$i];
	}
	
	public function getTree() {
		return $this->_tree;
	}
	
	public function getTotal() {
		return $this->total;
	}
	
	public function toOptions($parent_id = 0, $level = 0) {
		$r = '';
		if ($level && ($this->max_level==$level || !array_key_exists($parent_id, $this->_parent_ids))) return $r;
		$before = '';
		if ($level) $before = str_repeat($this->level_attr,$level);
		else $this->_cycles = 0;
		foreach ($this->getTree($parent_id, $level) as $i => $a) {
			if ($a[$this->id_col]==$this->not) continue;
			if ($a[$this->parent_id_col]==$parent_id) {
				$s = $d = '';
				if ($this->selected) {
					if ($this->_selected_is_array) {
						if (in_array($a[$this->id_col], $this->selected)) $s = $this->selected_attr;
					} 
					elseif ($a[$this->id_col]==$this->selected) {
						$s =  $this->selected_attr;
					}
				}
				$sub = $this->toOptions($a[$this->id_col], $level + 1);
				if ($this->disable_parents && $sub) {
					$d = ' disabled="disabled"';
				}
				$r .= '<option value="'.$a[$this->id_col].'"'.$s.$d.'>'.$before.$a[$this->name_col].'</option>';
				$r .= $sub;
				$this->_cycles++;
				if ($this->_cycles>$this->_max_cycles) Message::Halt('Infinite loop attempt','Tree class experienced an infinite loop, something went wrong.',Debug::backtrace());
			}
		}
		return $r;
	}
	
	public function toArrayOptions() {
		$arr = array();
		$this->_toArrayOptions($arr);
		return $arr;
	}
	
	public function _toArrayOptions(&$arr, $parent_id = 0, $level = 0) {
		if ($this->find) return $this->getTree();
		if ($level && ($this->max_level==$level || !array_key_exists($parent_id, $this->_parent_ids))) return;
		$before = '';
		if ($level) $before = str_repeat($this->level_attr,$level);
		else $this->_cycles = 0;
		foreach ($this->getTree() as $i => $a) {
			if ($a[$this->id_col]==$this->not) continue;
			if ($a[$this->parent_id_col]==$parent_id) {
				$arr[$a[$this->id_col]] = $before.$a[$this->name_col];
				$this->_toArrayOptions($arr, $a[$this->id_col], $level + 1);
				$this->_cycles++;
				if ($this->_cycles>$this->_max_cycles) Message::Halt('Infinite loop attempt','Tree class experienced an infinite loop, something went wrong.',Debug::backtrace());
			}
		}
	}
	
	public function level($id) {
		$parent = DB::one('SELECT '.$this->parent_id_col.' FROM '.$this->prefix.$this->table.' WHERE '.$this->id_col.'='.(int)$id);
		if ($parent>0) {
			$this->_level++;
			return $this->level($parent);
		} else {
			$l = $this->_level;
			$this->_level = 0;
			return $l;
		}
	}
	
	public function tree_array($id, $followed = false, $level = 0) {
		if (!$id) return array();
		if (!$level) $this->__tree = array();
		$sql = 'SELECT '.$this->id_col.', '.$this->sort_col.', '.$this->parent_id_col.', '.$this->name_col_select.$this->select.' FROM '.$this->prefix.$this->table.' WHERE '.(($followed && $level) ? $this->parent_id_col : $this->id_col).'='.(int)$id;
		$qry = DB::qry($sql,0,0);
		while ($rs = DB::fetch($qry)) {
			$this->__tree[] = $rs;
			if ($rs && $rs[($followed ? $this->id_col : $this->parent_id_col)]>0) {
				$this->tree_array($rs[($followed ? $this->id_col : $this->parent_id_col)], $followed, $level + 1);
			}
		}
		return ($followed ? $this->__tree : array_reverse($this->__tree));
	}
	
	
	public function _tree_array($id) {
		if (!$id) return array();
		$rs = DB::row('SELECT '.$this->id_col.', '.$this->sort_col.', '.$this->parent_id_col.', '.$this->name_col_select.$this->select.' FROM '.$this->prefix.$this->table.' WHERE '.$this->id_col.'='.(int)$id);
		if (!$rs) return array();
		$this->__tree[] = $rs;
		if ($rs[$this->parent_id_col]>0) {
			return $this->tree_array($rs[$this->parent_id_col]);
		} else {
			$r = $this->__tree;
			$this->__tree = array();
			return array_reverse($r);
		}
	}
	
	public function dir($id, $a = false, $join = ' → ') {
		if (is_array($id)) $arr = $id;
		else $arr = $this->tree_array($id);
		$names = array();
		$ret = array();
		foreach ($arr as $r) {
			$names[] = urlencode($r[$this->uname_col]);
			
			if (is_array($a)) {
				$r['link'] = str_replace(array('{$id}','{$parentid}'),array($r[$this->id_col],$r[$this->parentidid_col]),$a['link']);
				$r['attr'] = str_replace(array('{$id}','{$parentid}'),array($r[$this->id_col],$r[$this->parentidid_col]),$a['attr']);
				
				$ret[] = '<a href="'.$r['link'].'"'.$r['attr'].'>'.$r[$this->name_col].'</a>';
			}
			elseif ($a) {
				$ret[] = '<a href="'.URL::ht('?'.join(AMP,$names)).'"'.$a.'>'.$r[$this->name_col].'</a>';
			} else {
				$ret[] = $r[$this->name_col];	
			}
		}
		$this->names = $names;
		return join($join,$ret);
	}

	public function toArray($parent_id = 0, $level = 0, $names = array()) {
		if ($this->find) return $this->getTree();
		$r = array();
		if ($level && ($this->max_level==$level || !array_key_exists($parent_id, $this->_parent_ids))) return $r;
		$prev_level = -1;
		foreach ($this->getTree() as $i => $a) {
			if ($a[$this->parent_id_col]==$parent_id) {
				if ($this->use_levels) {
					$a[$this->level_col_as] = $this->level($a[$this->id_col]);
				} else $a[$this->level_col_as] = $level;
				$r[$i] = $a;
				if ($a[$this->level_col_as]==$prev_level) {
					array_pop($names);
				}
				array_push($names, $r[$i][$this->uname_col]);
				$prev_level = $a[$this->level_col_as];
				$r[$i]['url'] = URL::ht('?'.join(AMP,$names));
				
				if ($this->selected) {
					if ($this->_selected_is_array) {
						if (in_array($a[$this->id_col], $this->selected)) $r[$i]['selected'] = true;
					} 
					elseif ($a[$this->id_col]==$this->selected) {
						$r[$i]['selected'] = true;
					}
				}
				
				$r[$i]['sub'] = $this->toArray($a[$this->id_col], $level + 1, $names);
				$this->_cycles++;
				if ($this->_cycles>$this->_max_cycles) Message::Halt('Infinite loop attempt','Tree class experienced an infinite loop, something went wrong.',Debug::backtrace());
			}
		}
		return $r;
	}
	
	public function toList($parent_id = 0, $level = 0) {
		$r = '';
		if (!$parent_id && $this->use_ul) $r .= '<ul>';
		if ($level && ($this->max_level==$level || !array_key_exists($parent_id, $this->_parent_ids))) return $r;
		foreach ($this->getTree($parent_id, $level) as $i => $a) {
			if ($a[$this->parent_id_col]==$parent_id) {
				$sub = $this->toList($a[$this->id_col], $level + 1);
				$r .= '<li>'.$a[$this->name_col].($this->debug?' '.$a[$this->id_col].' ['.$a[$this->parent_id_col].'] ('.$a[$this->sort_col].')':'');
				if ($sub) $r .= '<ul>'.$sub.'</ul>';
				$r .= '</li>';
				$this->_cycles++;
				if ($this->_cycles>$this->_max_cycles) Message::Halt('Infinite loop attempt','Tree class experienced an infinite loop, something went wrong.',Debug::backtrace());
			}
		}
		if (!$parent_id && $this->use_ul) $r .= '</ul>';
		return $r;
	}
	
	/*
	public function getNode($id) {
		if (!$id) return array();
		$sql = 'SELECT '.$this->id_col.', '.$this->parent_id_col.', '.$this->name_col_select.' FROM '.$this->prefix.$this->table.' WHERE '.$this->id_col.'='.(int)$id.$this->where;
		$rs = DB::row($sql);
		return $rs;
	}
	
	public function getParents($id) {
		$rs = $this->getNode($id);
		$this->_parents[] = $rs;
		if ($rs[$this->parent_id_col]) {
			$this->getParents($rs[$this->parent_id_col]);
		}
		return array_reverse($this->_parents);
	}
	
	public function _dir($id, $selected = false, $tohref = false, $url = '') {
		$parents = $this->getParents($id);
		if ($tohref) {
			$ret = array();
			foreach ($parents as $i => $a) {
				if ($selected==$a[$this->id_col]) $ret[] = '<span>'.$a[$this->name_col].'</span>';
				else $ret[] = '<a href="'.$url.$a[$this->id_col].'">'.$a[$this->name_col].'</a>';	
			}
			$ret = join($tohref,$ret);
		}
		elseif ($selected) {
			$ret = array_label($parents,$selected);	
		}
		else $ret = $parents;
		return $ret;
	}
	*/

	public function __toString() {
		return $this->toTree();	
	}
	
	public function clear() {
		$this->select = '';
		$this->where = '';
		$this->order = 'name';
		$this->by = 'ASC';
				
		$this->selected = 0;
		$this->disable_parents = false;
		$this->max_level = 0;
		$this->use_ul = true;
		$this->find = false;
		$this->_tree = array(); 
		$this->_parent_ids = array();
		$this->_selected_is_array = false;
		$this->_sort = 0;
		$this->_all = array();
		return $this;
	}
	
	
	
	
	// manager
	

	public function add($data) {
		$this->set($this->_params);
		$this->offset = 0;
		if (!empty($data[$this->parent_id_col])) {
			DB::insert($this->table, $data);
			$this->get();
			$this->resort();
		} else {
			$data[$this->sort_col] = intval(DB::one('SELECT MAX(`sort`) AS max_sort FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->where)) + 1;
			DB::insert($this->table, $data, $this->id_col);	
		}
		$this->set($this->_params);
	}
	
	public function edit($id, $data) {
		DB::update($this->table, $data, $id, $this->id_col);
	}
	
	public function delete($id) {
		$arr = $this->tree_array($id, true);
		foreach ($arr as $i => $rs) {
			DB::delete($this->table, $rs[$this->id_col], $this->id_col);
		}
	}
	
	public function move($id, $parent_id, $after_id = 0) {
		if (!$id || $id==$after_id) return false;
		$id = (int)$id;
		$after_id = (int)$after_id;
		$parent_id = (int)$parent_id;
		$this->set($this->_params);		
		if ($after_id) {
			$sql = 'SELECT `'.$this->id_col.'`, `'.$this->sort_col.'` FROM `'.$this->prefix.$this->table.'` WHERE `'.$this->parent_id_col.'`='.$parent_id.' AND `'.$this->id_col.'`!='.$id.' ORDER BY `'.$this->sort_col.'`, `'.$this->id_col.'`';
			$qry = DB::qry($sql,0,0);
			$increase = $increased = false;
			$i = 1;
			while ($rs = DB::fetch($qry)) {
				if ($increase) {
					if ($increased) {
						$sort++;
					} else {
						$sort+=2;
						$increased = true;
					}
				} else {
					$sort = $i;
				}
				if ($rs[$this->id_col]==$after_id) {
					$increase = true;
				}			
				$sql = 'UPDATE `'.$this->prefix.$this->table.'` SET `'.$this->sort_col.'`='.$sort.' WHERE id='.$rs[$this->id_col];
				DB::run($sql);
				$i++;
			}
			$after_sort = DB::one('SELECT `'.$this->sort_col.'` FROM `'.$this->prefix.$this->table.'` WHERE `'.$this->id_col.'`='.$after_id);
			$sql = 'UPDATE `'.$this->prefix.$this->table.'` SET `'.$this->sort_col.'`='.($after_sort+1).', `'.$this->parent_id_col.'`='.$parent_id.' WHERE id='.$id;
			DB::run($sql);
		} else {
			$max = DB::one('SELECT `'.$this->sort_col.'` FROM `'.$this->prefix.$this->table.'` WHERE `'.$this->parent_id_col.'`='.$parent_id.$this->where.' ORDER BY `'.$this->sort_col.'` DESC');
			$sql = 'UPDATE `'.$this->prefix.$this->table.'` SET `'.$this->parent_id_col.'`='.$parent_id.', `'.$this->sort_col.'`='.($max+1).' WHERE id='.$id;
			DB::run($sql);
		}
		if ($this->resort_min) {
			$this->where = ' AND '.$this->parent_id_col.'='.$parent_id;
			$this->offset = 0;
			$this->get();
			$this->resort($parent_id);
		} else {
			$this->offset = 0;
			$this->get();
			$this->resort();	
		}
		$this->set($this->_params);
	}
	
	public function resort($parent_id = 0, $level = 0) {
		if ($this->offset) return false;
		foreach ($this->_tree as $i => $a) {
			if ($a[$this->parent_id_col]==$parent_id) {
				if (!$this->resort_reverse) {
					$sort = $this->getSort($level);
					DB::run('UPDATE `'.$this->prefix.$this->table.'` SET `'.$this->sort_col.'`='.$sort.' WHERE `'.$this->id_col.'`='.$a[$this->id_col]);
					$this->resort($a[$this->id_col], $level + 1);
				} else {
					$this->resort($a[$this->id_col], $level + 1);
					$sort = $this->getSort($level);
					DB::run('UPDATE `'.$this->prefix.$this->table.'` SET `'.$this->sort_col.'`='.$sort.' WHERE `'.$this->id_col.'`='.$a[$this->id_col]);
				}
			}
		}
	}
	private function getSort($level) {
		if ($this->resort_smart) {
			if ($level) {
				$this->_sort[1]++;
				$sort = $this->_sort[1];
			} else {
				$this->_sort[0]++;
				$sort = $this->_sort[0];
			}
		} else {
			$this->_sort++;
			$sort = $this->_sort;
		}	
		return $sort;
	}
	
	public function getByPosition($position = false, $max_level = 2) {
		
		// TODO tofinish, im tired very much
	}
	
	public function getPositions() {
		return DB::getAll('SELECT DISTINCT(`position`) FROM '.$this->prefix.$this->table.' WHERE `position`!=\'\' ORDER BY `position`','position');
		/*
		return DB::getAll('SELECT a.position, CONCAT(a.position,\' (\',(SELECT COUNT(1) FROM '.$this->prefix.'menu b WHERE b.position=a.position AND b.parentid=0),\')\') AS label FROM '.$this->prefix.$this->table.' a GROUP BY a.position ORDER BY `position`','position|label');
		*/
	}
	public function getPosition() {
		$ret = DB::one('SELECT `position` FROM '.$this->prefix.$this->table.' ORDER BY `position`');
		return $ret ? $ret : 'Untitled';
	}
	
	public function getParentTrees($position, $not = 0) {
		$ret = DB::getAll('SELECT '.$this->id_col.', '.$this->name_col_select.' FROM '.$this->prefix.$this->table.' WHERE id!='.(int)$not.' AND `position` LIKE '.e($position).' ORDER BY sort, '.$this->name_col.'',''.$this->id_col.'|'.$this->name_col);	
		return $ret;
	}
	public function positionName($position) {
		return $position;	
	}
	public function isID($id, $select = false) {
		if ($select) {
			return DB::row('SELECT '.$select.' FROM '.$this->prefix.$this->table.' m WHERE '.$this->id_col.'='.(int)$id.$this->and_active);
		} else {
			return DB::one('SELECT 1 FROM '.$this->prefix.$this->table.' m WHERE '.$this->id_col.'='.(int)$id.$this->and_active);
		}
	}
	
	
	
	
	
	
	
}