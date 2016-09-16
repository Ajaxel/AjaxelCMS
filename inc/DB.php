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
* @file       inc/DB.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class DB {
	
	private static
		$DB = array(9=>array()),
		$nDB = 0,
		$err = true,
		$prefix = '',
		$session_cache = false,
		$lock = false,
		$delayed = false,
		$ignore = false,
		$no_prefix = false,
		$qry = false
	;
	
	const MAX_OFFSET = 4294967296;
	const FOUND_ROWS = 'FOUND_ROWS()';
	
	public static function commit() {
		return false;
		return self::$DB[self::$nDB]->commit();
	}
	public static function setPrefix($prefix) {
		self::$prefix = $prefix;
	}
	public static function getPrefix() {
		return self::$prefix;	
	}
	public static function prefix($table) {
		if (self::$no_prefix || self::$nDB || self::withPrefix($table)) return $table;
		if (in_array($table, Site::getGlobalTables())) return DB_PREFIX.$table;
		return self::$prefix.$table;
	}
	
	public static function withPrefix($table) {
		$l = strlen(self::$prefix);
		$p = substr($table, 0, $l);
		return $p==self::$prefix;
	}
	
	public static function remPrefix($table) {
		if (self::$nDB>0) return $table;
		if (!self::withPrefix($table)) return $table;
		$l = strlen(self::$prefix);
		if ($l) $table = substr($table, $l);
		return $table;
	}
	public static function remDBprefix($table) {
		if (!DB_PREFIX) return $table;
		$l = strlen(DB_PREFIX);
		if (substr($table,0,$l)==DB_PREFIX) $table = substr($table,$l);
		return $table;
	}
	
	public static function getPrefixes() {
		return DB::getAll('SELECT DISTINCT(val) FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\'','val');
	}
	public static function delayed($set = true) {
		self::$delayed = $set;
	}
	public static function ignore($set = true) {
		self::$ignore = $set;
	}
	public static function no_prefix($set = true) {
		self::$no_prefix = $set;
	}

	public static function is_connected() {
		return self::$DB[self::$nDB] ? true : false;	
	}
	public static function change($n = 0, $name = NULL) {
		if ($n!=9 && isset(self::$DB[$n]) && is_object(self::$DB[$n])) {
			self::$nDB = $n;
			if (!@self::$DB[self::$nDB]->select_db(self::$DB[9][self::$nDB]['dbname'])) {
				Message::Halt('MySQLi error. Cannot change to database: &quot;'.$name.'&quot;.',self::errorMsg());
			}
			elseif ($name) self::$DB[9][self::$nDB]['dbname'] = $name;
		}
		else {
		//	trigger_error('No database connection with number: '.$n,E_USER_WARNING);
		}
	}

	public static function info($n=0, $val='') {
		$info = self::$DB[9][$n];
		if ($val===true) p($info);
		elseif ($val) $info = $info[$val];
		return $info;
	}
	
	public static function addGlobalTable($table) {
		Conf()->fill('global_tables', $table);	
	}
	/*
	public static function reconnect($num = NULL) {
		if ($num===NULL) {
			foreach (self::$DB[9] as $num => $a) {
				self::reconnect($num);
			}
		} else {
			self::connect($num, self::$DB[9][$num]['host'], self::$DB[9][$num]['username'], self::$DB[9][$num]['passwd'], self::$DB[9][$num]['name'], self::$DB[9][$num]['persistent']);
		}
	}
	*/
	public static function connect($n=0, $host=DB_HOST, $username=DB_USERNAME, $passwd=DB_PASSWORD, $dbname=DB_NAME, $port=DB_PORT, $socket=DB_SOCKET, $e=true) {
		if (!isset($_SESSION['DB::tables']) || !is_array($_SESSION['DB::tables'])) $_SESSION['DB::tables'] = array();
		if (!isset($_SESSION['DB::columns']) || !is_array($_SESSION['DB::columns'])) $_SESSION['DB::columns'] = array();
		
		if (!defined('DB_PERSISTENT')) $persistent = false;

		if (isset(self::$DB[9][self::$nDB]) && self::$DB[9][self::$nDB] && $host==self::$DB[9][self::$nDB]['host'] && $username==self::$DB[9][self::$nDB]['username'] && $passwd==self::$DB[9][self::$nDB]['passwd']) {
			self::$DB[$n] =& self::$DB[self::$nDB];
			
			if ($dbname && self::$DB[9][self::$nDB]['name']!=$dbname && !self::$DB[$n]->select_db($dbname) && $e) {
				Message::halt('Site unavalable, Cannot select the database: &quot;'.$dbname.'&quot;.',self::errorMsg());
			} else {
				self::run('SET NAMES \'utf8\'');	
			}
			
			self::$nDB = $n;
			self::$DB[9][self::$nDB] = array();
			self::$DB[9][self::$nDB]['host'] = $host;
			self::$DB[9][self::$nDB]['username'] = $username;
			self::$DB[9][self::$nDB]['passwd'] = $passwd;
			self::$DB[9][self::$nDB]['dbname'] = $dbname;
			return true;
		}
		self::$nDB = $n;
		if (isset(self::$nDB[self::$nDB]) && is_object(self::$DB[self::$nDB])) return true;
		
		self::on();
		self::$DB[9][self::$nDB] = array();
		self::$DB[9][self::$nDB]['host'] = $host;
		self::$DB[9][self::$nDB]['username'] = $username;
		self::$DB[9][self::$nDB]['passwd'] = $passwd;
		self::$DB[9][self::$nDB]['dbname'] = $dbname;

		self::$DB[self::$nDB] = @new mysqli($host, $username, $passwd, false, $port, $socket);
		if (self::$DB[self::$nDB]->connect_errno && $e) {
			Message::halt('Site unavalable, MySQLi is not running.','Can\'t connect to DB host: '.$host.'; DB user: '.$username.' (using password: '.($passwd?'YES':'NO').'); DB name: '.$dbname.'<br />'.self::$DB[self::$nDB]->connect_error);
		}
		elseif (!@self::$DB[self::$nDB]->select_db($dbname) && $e) {
			Message::halt('Site unavalable, Cannot select the database: &quot;'.$dbname.'&quot;.',self::errorMsg());
		}
		if ($n==0) {
			if (!defined('DB_SET')) self::run('SET NAMES \'utf8\'');
			elseif (DB_SET) self::run(DB_SET);
		}
		self::off(false);
		return true;
	}
	
	public static function i() {
		return self::$nDB;
	}
	
	public static function link($int = false) {
		if (is_object($int) || is_resource($int)) {
			self::$DB[0] = $int;
			self::$nDB = 0;
			$int = false;
		}
		if ($int===false) $int = self::$nDB;
		return self::$DB[$int];
	}
	
	public static function qry($sql,$offset=0,$limit=1) {
		self::on();
		$offset = abs($offset); $limit = abs($limit);
		if ($offset>self::MAX_OFFSET) $offset = self::MAX_OFFSET;
		if ($limit>self::MAX_OFFSET) $limit = self::MAX_OFFSET;
		if ($limit) $sql = $sql.' LIMIT '.$offset.','.$limit;
		elseif ($offset) $sql = $sql.' LIMIT '.$offset.',0';
		$qry = self::$DB[self::$nDB]->query($sql);
		if (!$qry) self::error($sql);
		self::off(true,$sql);
		if (Site::$db_sql) self::fill($sql,$qry);
		self::yeserror();
		return $qry;
	}
	
	/*
	private static function func($row, $func) {
		if (!$func) return $row;
		if (is_array($row)) {
			foreach ($row as $k => $v) {
				$row[$k] = self::func($v,$func);	
			}
		} else {
			return call_user_func_array($func, $row);
		}
		return $row;
	}
	*/
	
	public static function data($sql, $func = false, $offset = NULL, $limit = 20) {
		if ($offset===NULL) {
			//if (function_exists('Index') && Index()->My) {
			$offset = Index()->My->offset;
			$limit = Index()->My->limit;
			
		}
		$qry = self::qry($sql, $offset, $limit);
		$data = array('list'=>array(),'total'=>self::rows());
		$i = 0;
		while ($row = self::fetch($qry)) {
			if ($func && !($row = call_user_func_array($func,array($row, $i, $data['total'])))) continue;
			$i++;
			array_push($data['list'], $row);
		}
		$data['pager'] = Pager::get(array(
			'total'	=> $data['total'],
			'limit'	=> $limit
		));
		return $data;
	}
	
	public static function all($sql,$sel=false,$func=false) {
		return self::getAll($sql,$sel,$func);
	}
	
	public static function getAll($sql,$sel=false,$func=false) {
		self::on();
		$qry = self::$DB[self::$nDB]->query($sql);
		if (!$qry) self::error($sql);
		$ret = array();
		if ($sel) {
			if (strstr($sel,'|')) {
				$ex = explode('|',$sel);
				if ($ex[1]=='[[:ARRAY:]]') {
					while($row = $qry->fetch_assoc()) {
						$k = $row[$ex[0]];
						if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
						$ret[$k] = $row;
					}			
				} 
				elseif ($ex[1]=='[[:EMPTY:]]') {
					while($row = $qry->fetch_assoc()) {
						$ret[$row[$ex[0]]] = '';
					}
				}
				elseif ($ex[0]=='[[:INDEX:]]') {
					while($row = $qry->fetch_assoc()) {
						if ($func) {
							if (!($row = call_user_func_array($func,array($row[$ex[1]])))) continue;
							array_push($ret, $row);
						}
						else array_push($ret, $row[$ex[1]]);
					}
				}
				else {
					while($row = $qry->fetch_assoc()) {
						if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
						$ret[$row[$ex[0]]] = $row[$ex[1]];
					}
				}
			} else {
				while($row = $qry->fetch_assoc()) {
					if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
					array_push($ret,$row[$sel]);
				}
			}
		} else {
			while($row = $qry->fetch_assoc()) {
				if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
				array_push($ret,$row);
			}
		}
		
		self::off(true,$sql);
		if (self::errorMsg()) {
			self::$err = true;
			return false;	
		}
		if ($ret && stristr($sql,'SQL_CALC_FOUND_ROWS')) $ret[self::FOUND_ROWS] = DB::one('SELECT FOUND_ROWS()');
		if (Site::$db_sql) self::fill($sql,$qry);
		self::free($qry);
		self::yeserror();
		return $ret;
	}
	
	public static function fill($sql, $qry, $affected = false) {
		if ($affected) {
			$r = (int)self::$DB[self::$nDB]->affected_rows;
			$w = 'Affected';
		} else {
			$r = (int)@$qry->num_rows;
			$w = 'Returned';
		}
		$mtime = explode(' ',microtime());
		Conf::getInstance()->fill('DB_SQL', array('sql'=>"$sql\n-- $w: $r row".($r==1?'':'s').self::backTrace(),'time'=>$mtime[1]+$mtime[0]));
	}
	
	public static function one($sql) {
		self::on();
		if (!strpos($sql,'LIMIT')) $sql = $sql.' LIMIT 1';
		$qry = self::$DB[self::$nDB]->query($sql);
		if (!$qry) return self::error($sql, false);
		$row = $qry->fetch_row();
		if (Site::$db_sql) self::fill($sql,$qry);
		$qry->free();
		self::off(true,$sql);
		self::yeserror();
		return $row[0];
	}
	
	public static function row($sql, $name = false, $offset = 0, $limit = 1) {
		return self::fetch(self::qry($sql,$offset,$limit), $name);	
	}

	public static function run($sql) {
		if (is_array($sql)) {
			$i = 0;
			foreach ($sql as $s) {
				if (!self::run($s)) break;
				$i++;
			}
			if ($i) self::commit();
			return $i;
		}
		self::on();
		$qry = self::$DB[self::$nDB]->query($sql);
		if ($qry===false) self::error($sql);
		self::off(true,$sql);
		if (Site::$db_sql) self::fill($sql,$qry,true);
		self::yeserror();
		return $qry;
	}
	
	public static function escape($s) {
		return self::$DB[self::$nDB]->real_escape_string($s);
	}
	
	public static function escape2($s) {
		if (is_array($inp)) return array_map(__METHOD__, $s);
		if (!empty($s) && is_string($s)) {
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $s); 
		}
		return $s; 
	}
	
	private static function backTrace() {
		$d = array_reverse(debug_backtrace());
		foreach ($d as $i => $a) {
			if (strstr($a['file'],'DB.php')) break;	
		}
		$i--;
		if (!isset($d[$i]) || !$d[$i]['file']) return '';
		return "\n-- Executed in ".$d[$i]['file'].' ('.(isset($d[$i]['class'])?$d[$i]['class'].'::':'').$d[$i]['function'].') on line '.$d[$i]['line'].'';
	}
	
	public static function string($s, $esc = true) {
		if (is_array($s)) $s = '';
		return ($esc ? e($s) : $s);
	}
	
	public static function insert($table, $data, $replace = false, $cols_only = false) {
		if (!$data) return false;
		$_data = array();
		if ($cols_only===true) {
			foreach ($data as $k => $v) $_data[$k] = self::string($v);
		} else {
			if (!$cols_only) $cols = self::columns($table);
			foreach ($data as $k => $v) {
				$_k = ltrim($k,'.');
				if (!$cols_only && !in_array($_k, $cols)) continue;
				if ($cols_only && !in_array($_k,$cols_only)) continue;
				$_data[$_k] = self::string($v, (substr($k,0,1)=='.' ? false : true));
			}
		}
		if (!$_data) return false;
		$_table = self::prefix($table);
		if (self::$lock) self::run("LOCK TABLE $_table WRITE");
		$sql = ($replace?'REPLACE':'INSERT').(self::$delayed?' DELAYED':(self::$ignore?' IGNORE':'')).' INTO `'.$_table.'` (`'.join('`, `',array_keys($_data)).'`) VALUES ('.join(', ',array_values($_data)).')';
		self::delayed(false);
		self::ignore(false);
		self::no_prefix(false);
		$ret = self::run($sql);
		if (self::$lock) self::run('UNLOCK TABLES');
		self::commit();
		return $ret;
	}
	
	public static function replace($table, $data, $cols_only = false) {
		return self::insert($table, $data, true, $cols_only);
	}
	
	public static function update($table, $data, $id, $idcol='id', $where = '', $cols_only = false) {
		if (!$table || !$data || !$id || !$idcol) return false;
		$up = '';
		if (!$cols_only) $cols = self::columns($table);
		foreach ($data as $k => $v) {
			$_k = ltrim($k,'.');
			if ($cols_only!==true) {
				if (!$cols_only && !in_array($_k, $cols)) continue;
				if ($cols_only && !in_array($_k,$cols_only)) continue;
			}
			$up .= ', `'.$_k.'`='.self::string($v, (substr($k,0,1)=='.' ? false : true));
		}
		if (!$up) return false;
		if (!is_numeric($id)) $id = e($id); else $id = (int)$id;
		$_table = self::prefix($table);
		if (self::$lock) self::run("LOCK TABLE $_table WRITE");
		$sql = 'UPDATE `'.$_table.'` SET '.substr($up,2).' WHERE `'.$idcol.'`='.$id.$where;
		self::no_prefix(false);
		$ret = self::run($sql);
		if (self::$lock) self::run('UNLOCK TABLES');
		self::commit();
		return $ret;
	}
	
	public static function select($table, $id, $select = '*', $idcol = 'id') {
		return DB::row('SELECT '.$select.' FROM `'.self::prefix($table).'` WHERE `'.$idcol.'`='.e($id));
	}
		
	public static function delete($table, $id, $idcol='id') {
		if (is_array($id)) {
			$del = array();
			foreach ($id as $k => $v) $del[] = '`'.$k.'`='.e($v);
			return self::run('DELETE FROM `'.self::prefix($table).'` WHERE '.join(' AND ',$del));
		} else {
			return self::run('DELETE FROM `'.self::prefix($table).'` WHERE `'.$idcol.'`='.$id);
		}
	}
	
	public static function rows(&$data = NULL) {
		if ($data!==NULL) {
			if (is_array($data)) {
				if (isset($data[self::FOUND_ROWS])) {
					$ret = $data[self::FOUND_ROWS];
					unset($data[self::FOUND_ROWS]);
					return $ret;
				}
				else return count($data);
			} else return 0;
		} else {
			return DB::one('SELECT FOUND_ROWS()');
		}
	}
	
	public static function search($f,$arr,$a = '') {
		$ret = '';
		$is_int = $is_string = $like = $regexp = $from_to = $merged = array();
		$id_from_to = false;
		extract($arr);
		if ($a) $a = trim($a,'.').'.';
		
		if ($id_from_to && (isset($f[$id_from_to.'_from']) && isset($f[$id_from_to.'_to']))) {
			if ($f[$id_from_to.'_from'] && $f[$id_from_to.'_to']) $ret .= ' AND '.$a.$id_from_to.'>='.(int)$f[$id_from_to.'_from'].' AND '.$a.$id_from_to.'<='.(int)$f[$id_from_to.'_to'];
			elseif ($f[$id_from_to.'_from']) {
				return ' AND '.$a.$id_from_to.'='.(int)$f[$id_from_to.'_from'];
			}
			elseif ($f[$id_from_to.'_to']) {
				return ' AND '.$a.$id_from_to.'='.(int)$f[$id_from_to.'_to'];
			}
		}
		foreach ($is_int as $c) {
			if (isset($f[self::search_name($c)]) && strlen($s = trim($f[self::search_name($c)]))) $ret .= ' AND '.self::search_col($a,$c).'='.(int)$s;
		}
		foreach ($from_to as $c) {
			if (isset($f[self::search_name($c).'_from']) && $f[self::search_name($c).'_from'])
				$ret .= ' AND '.self::search_col($a,$c).'>'.(int)$f[self::search_name($c).'_from'];
			if (isset($f[self::search_name($c).'_to']) && $f[self::search_name($c).'_to'])
				$ret .= ' AND '.self::search_col($a,$c).'<'.(int)$f[self::search_name($c).'_to'];
		}
		foreach ($is_string as $c) {
			if (isset($f[self::search_name($c)]) && strlen($s = trim($f[self::search_name($c)]))) $ret .= ' AND '.self::search_col($a,$c).'='.e($s);
		}
		foreach ($like as $k => $c) {
			if (is_array($c)) {
				if (isset($f[self::search_name($k)]) && is_string($f[self::search_name($k)]) && strlen($s = trim($f[self::search_name($k)]))) {
					$j = array();
					foreach ($c as $_c) {
						$j[] = self::search_col($a,$_c).' LIKE \'%'.e($s,false).'%\'';
					}
					$ret .= ' AND ('.join(' OR ',$j).')';
				}
			}
			elseif (isset($f[self::search_name($c)]) && is_string($f[self::search_name($c)]) && strlen($s = trim($f[$c]))) 
				$ret .= ' AND '.self::search_col($a,$c).' LIKE \'%'.e($s,false).'%\'';
		}
		foreach ($regexp as $c) {
			if (isset($f[self::search_name($c)]) && is_string($f[self::search_name($c)]) && $f[self::search_name($c)]) 
				$ret .= ' AND '.self::search_col($a,$c).' RLIKE ('.e($f[self::search_name($c)]).')';
		}
		foreach ($merged as $c) {
			if (isset($f[self::search_name($c)]) && !is_array($f[self::search_name($c)]) && $f[self::search_name($c)]) 
				$ret .= ' AND '.self::search_col($a,$c).' LIKE \'%,'.e($f[self::search_name($c)],false).',%\'';
		}
		return $ret;
	}
	
	private static $search_name = array();
	
	private static function search_col($a,$c) {
		if (strstr($c,'.')) {
			return $c;
		}
		return $a.$c;
	}
	
	private static function search_name($c) {
		if (isset(self::$search_name[$c])) return self::$search_name[$c];
		if ($pos = strpos($c,'.')) {
			self::$search_name[$c] = substr($c,$pos+1);
		} else {
			self::$search_name[$c] = $c;
		}
		return self::$search_name[$c];
	}
	
	public static function getNum($sql) {
		return self::num(self::run($sql));	
	}

	public static function num(&$qry) {
		if (!$qry) return 0;
		return $qry->num_rows;
	}
	
	public static function affected() {
		return self::$DB[self::$nDB]->affected_rows;
	}
	
	public static function fetch($qry = false,$col = false) {
		if (!$qry) return false;
		$rs = $qry->fetch_assoc();
		if ($col) return isset($rs[$col])?$rs[$col]:'';
		return $rs;
	}
	public static function free($qry) {
		$qry->free();
	}
	public static function id() {
		return self::$DB[self::$nDB]->insert_id;
		//$row = self::$DB[self::$nDB]->query('SELECT LAST_INSERT_ID()')->fetch_array(MYSQLI_NUM);
		//return $row[0];
	}
	public static function close($n = NULL) {
		if ($n!==NULL) {
			self::$DB[$n]->close();
			unset(self::$DB[$n]);
		} else {
			foreach (self::$DB as $i => $d) {
				if ($i==9) break;
				$d->close();
			}
			self::$DB = array();
		}
	}
	
	public static function tables($cache = true, $all = false, $add_db_prefix = false) {
		$n = 'DB::tables';if ($all) $n .= '_all';if ($add_db_prefix) $n .= '_p';
		if ($cache && isset($_SESSION[$n][self::$nDB]) && $_SESSION[$n][self::$nDB]) return $_SESSION[$n][self::$nDB];
		self::on();
		if (self::$nDB>0) {
			$sql = 'SHOW TABLES';
			$qry = self::$DB[self::$nDB]->query($sql);
			while ($row = $qry->fetch_array(MYSQLI_NUM)) {
				$rs[] = $row[0];
			}
			Conf()->s2('DB::tables',self::$nDB,$rs);
		} else {
			$sql = 'SHOW TABLES'.(DB_PREFIX?' LIKE \''.DB_PREFIX.'%\'':'');
			$qry = self::$DB[self::$nDB]->query($sql);
			$rs = array();
			$l2 = strlen(DB_PREFIX);
			if ($l2) $prefix = substr(self::$prefix,$l2);
			else $prefix = self::$prefix;
			if (!$all) {
				$no_skip = Site::getGlobalTables();
				$l = strlen($prefix);
			}		
			while ($row = $qry->fetch_array(MYSQLI_NUM)) {
				if ($l2) $row[0] = substr($row[0], $l2);
				if (!$all && !in_array($row[0], $no_skip)) {
					if (substr($row[0], 0, $l)!=$prefix) {
						continue;	
					} elseif ($l) {
						$row[0] = substr($row[0], $l);
					}
				}
				if ($add_db_prefix) $row[0] = DB_PREFIX.$row[0];
				$rs[] = $row[0];
			}
		}
		if ($cache) $_SESSION[$n][self::$nDB] = $rs;
		if (Site::$db_sql) self::fill($sql,$qry);
		self::free($qry);
		self::off();

		return $rs;
	}
	
	public static function checkColumns($table, $data = array()) {
		$cols = self::columns($table, false);
		$ret = array();
		foreach ($data as $k => $v) {
			if (in_array($k,$cols)) $ret[$k] = $v;
		}
		return $ret;
	}
	
	public static function like($col, $arr, $operand = 'OR', $sepA = '%', $sepB = '') {
		$j = array();
		foreach ($arr as $a) {
			if (!is_string($a)) continue;
			$j[] = $col.' LIKE '.e($sepA.$sepB.$a.$sepB.$sepA);
		}
		if (!$j) return '';
		return ' AND ('.join(' '.$operand.' ', $j).')';
	}
	
	public static function columns($table, $cache = true) {
		if ($cache) {
			if (!isset($_SESSION['DB::columns'][self::$nDB]) || !is_array($_SESSION['DB::columns'][self::$nDB])) $_SESSION['DB::columns'][self::$nDB] = array();
			if (isset($_SESSION['DB::columns'][self::$nDB][$table]) && $_SESSION['DB::columns'][self::$nDB][$table]) return $_SESSION['DB::columns'][self::$nDB][$table];
		}
		if (!in_array(self::prefix($table),self::tables(true,true,true))) {
			return array();
		}
		$ret = array();
		$qry = self::qry('SHOW COLUMNS FROM `'.self::prefix($table).'`',0,0);
		while ($c = self::fetch($qry)) array_push($ret,$c['Field']);
		if ($cache) $_SESSION['DB::columns'][self::$nDB][$table] = $ret;
		return $ret;
	}
	
	public static function errorNo() {
		return self::$DB[self::$nDB]->errno;
	}
	public static function errorMsg() {
		return self::$DB[self::$nDB]->error;
	}
	
	public static function setCacheMode($bool = true) {
		self::$session_cache = $bool;
	}
	
	/*
	public static function getDefaults($table, $lower = true) {
		$ret = array();
		$qry = self::qry('SHOW FIELDS FROM `'.$table.'`',0,0);
		while ($row = self::fetch($qry)) {
			if (!$row['Default'] || $row['Default']=='NULL') {
				if (strtolower($row['Type'])=='datetime') $row['Default'] = '0000-00-00 00:00:00';
			}
			$ret[$row['Field']] = array('default'=>$row['Default'],'type'=>($lower?strtolower($row['Type']):$row['Type']));
		}
		self::free($qry);
		return $ret;
	}
	*/
	
	public static function describe($table,$showFull=false, $cache = true) {
		$n = $n;
		if ($showFull) $n .= '_f';
		if ($cache) {
			if (!isset($_SESSION[$n][self::$nDB]) || !is_array($_SESSION[$n][self::$nDB])) $_SESSION[$n][self::$nDB] = array();
			if (isset($_SESSION[$n][self::$nDB][$table]) && $_SESSION[$n][self::$nDB][$table]) return $_SESSION[$n][self::$nDB][$table];
		}
		$data = array();
		$q = self::qry('DESCRIBE `'.self::prefix($table).'`',0,0);
		while ($rs = self::fetch($q)) array_push($data,$rs);
		self::free($q);
		$ret = array();
		if ($showFull) foreach ($data as $d) $ret[$d['Field']] = strtolower($d['Type']);
		else {
			foreach ($data as $d) {
				$type = explode('(',$d['Type']);
				$ret[$d['Field']] = strtolower($type[0]);
			}
		}
		if ($cache) $_SESSION[$n][self::$nDB][$table] = $ret;
		return $ret;
	}
	
	public static function optimize($tables) {
		$tables = (array)$tables;
		ignore_user_abort(true);
		DB::run('OPTIMIZE TABLE `'.join('`, `',$tables).'`');
	}
	
	public static function analyze($tables) {
		$tables = (array)$tables;
		ignore_user_abort(true);
		DB::run('ANALYZE TABLE `'.join('`, `',$tables).'`',true);
	}
	
	public static function resetCache() {
		$_SESSION['DB::columns'] = $_SESSION['DB::describe'] = $_SESSION['DB::describe_f'] = 
		$_SESSION['DB::tables'] = $_SESSION['DB::tables_all'] = $_SESSION['DB::tables_all_p'] = 
		$_SESSION['cnt'] = array();
	}
	public static function clearCache() {
		return self::resetCache();
	}
	
	public static function clearCount($table) {
		$_SESSION['cnt'][$table] = array();
		DB::run('DELETE FROM '.self::$prefix.'counts WHERE `table`=\''.$table.'\'',true);
	}
	
	public static function getCount($table, $condition = false) {
		if ($condition) $sess_cond = md5($condition); else $sess_cond = '';
		$time = time();
		$backTime = $time - 3600;
		if (!isset($_GET[URL_KEY_RESET])) {
			if (isset($_SESSION['cnt'][$table][$sess_cond][1]) && $_SESSION['cnt'][$table][$sess_cond][0] > $backTime) {
				return $_SESSION['cnt'][$table][$sess_cond][1];
			}
			$row = self::fetch(self::qry('SELECT `total` FROM '.self::$prefix.'counts WHERE `table`=\''.$table.'\' AND `query`='.e($sess_cond).' AND `saved`>'.$backTime));
			if (isset($row['total'])) {
				$_SESSION['cnt'][$table][$sess_cond][1] = $row['total'];
				$_SESSION['cnt'][$table][$sess_cond][0] = $time;
				return (int)$row['total'];
			}
		}
		$cnt = self::one('SELECT COUNT(*) FROM '.self::prefix($table).' WHERE TRUE '.$condition);
		if ($cnt) {
			$data = array(
				'table'	=> $table,
				'query'	=> $sess_cond,
				'total'	=> $cnt,
				'saved'	=> $time
			);
			DB::replace('counts',$data);
		}
		$_SESSION['cnt'][$table][$sess_cond][1] = $cnt;
		$_SESSION['cnt'][$table][$sess_cond][0] = $time;
		return (int)$cnt;
	}
	
	
	
	public static function keywords($text, $sep = ', ') {
		$text = strip_tags($text);
		$text = str_replace(array('&nbsp;',"\n","\r","\t",'  '),' ',$text);
		$text = preg_split('/(\s|,|\.|"|\/|\\|\(|\)|\]|\[|\}|\{)/',$text);
		$tags = array();
		foreach ($text as $i => $t) {
			if (!$t) continue;
			$l = strlen($t);
			if (is_numeric($t)) {
				if ($l>3) $tags[] = $t;
			}
			elseif ($l>1) $tags[] = $t;
		}
		$tags = array_unique($tags);
		$text = join($sep,$tags);
		$text = str_replace(array('&gt;','&lt;','&quot;','&#039;','&#39','&nbsp;','&amp;'),array('>','<','"','\'','\'',' ','&'),$text);
		return $text;
	}
	
	/*
	public static function updateKeywords($for_cols, $id, $table, $keyword_col = 'keywords', $id_col = 'id') {
		if (!$for_cols || !$id || !$table) return false;
		$cols = self::columns($table);
		if (!in_array($keyword_col,$cols)) return false;
		$checked = array();
		foreach ($for_cols as $c) {
			if (in_array($c,$cols)) $checked[] = $c;
		}
		if (!$checked) return false;
		$sql = 'SELECT `'.join('`, `',$checked).' FROM `'.$table.'` WHERE `'.$id_col.'`='.$id;
		$rs = self::fetch(self::qry($sql));
		$keywords = '';
		foreach ($rs as $r) $keywords .= $r.' ';	
		if (!$keywords) return false;
		self::run('UPDATE `'.$table.'` SET `'.$keyword_col.'`='.e(self::makeKeywords($keywords,' ')).' WHERE `'.$id_col.'`='.$id);
	}
	
	function dbExportExec($file = 'backup', $host=DB_HOST,$username=DB_USERNAME,$passwd=DB_PASSWORD,$name=DB_NAME) {
		$file .= '_'.$name.'_'.date('Hi-dmY').'.sql';
		$command = 'mysqldump --opt --skip-extended-insert --complete-insert -h '.$host.' -u '.$username.' -p '.$passwd.' '.$name.' > '.$file;
		exec($command, $ret_arr, $ret_code);
		if ($ret_code!=0) {
			if (!function_exists('notice')) {
				function notice($m) {echo $m;}	
			}
			Notice("Export failed with an error code of $ret_code",'error');
			exit;
		} else {
			return $ret_arr;	
		}
	}
	
	function dbImportExec($file,$host=DB_HOST,$username=DB_USERNAME,$passwd=DB_PASSWORD,$name=DB_NAME) {
		exec("mysql -u {$username} -p{$passwd} {$name} < {$file}", $ret_arr, $ret_code);
		if ($ret_code!=0) {
			if (!function_exists('notice')) {
				function notice($m) {echo $m;}	
			}
			Notice("Export failed with an error code of $ret_code",'error');
			exit;
		} else {
			return $ret_arr;	
		}
	}
	*/
	private static function log($sql,$state=0) {
		return false;
	}
	
	private static function on() {
		$mtime = explode(' ',microtime());
		Conf::getInstance()->s('dbStartTime', $mtime[1] + $mtime[0]);
	}
	private static function off($accesses = true, $sql = '') {
		$mtime = explode(' ',microtime());
		if (!Conf::getInstance()->g('dbEndTime')) Conf::getInstance()->s('dbBeginTime', $mtime[1] + $mtime[0]);
		Conf::getInstance()->s('dbEndTime', $mtime[1] + $mtime[0]);
		$total_time = (Conf::getInstance()->g('dbEndTime') - Conf::getInstance()->g('dbStartTime'));
		Conf::getInstance()->s('dbTotalTime', Conf::getInstance()->g('dbTotalTime') + $total_time);
		if ($accesses) Conf::getInstance()->plus('dbAccesses',1);
	}
	

	public static function sqlGetString($type, $arg='', $arg2='') {
		switch ($type) {
			case 'age':
				return '(DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS('.$arg.')), \'%Y\')+0)';
			break;
			case 'age1':
				return 'FLOOR((CURDATE() – '.$arg.')/10000)';
			break;
			case 'age2datetime':
				return (date('Y')-$arg).'-'.sprintf('%02d',date('m')).'-'.sprintf('%02d',date('d')).' '.sprintf('%02d',date('H')).':'.sprintf('%02d',date('i')).':00';
			break;
			case 'where_dob_close':
				return 'DATE(CONCAT(YEAR(CURDATE()), RIGHT('.$arg.', 6))) BETWEEN DATE_SUB(CURDATE(), INTERVAL 0 DAY) AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)';
			break;
			case 'where_dob':
				return 'AND MONTH(dob)='.date('m').' AND DAY(dob)='.date('d').'';
			break;
			case 'length':
				return '(CASE WHEN LENGTH(`'.$arg.'`)>'.$arg2.' THEN CONCAT(SUBSTRING(`'.$arg.'`,1,'.$arg2.'),\'&#8230;\') ELSE `'.$arg.'` END) AS '.$arg;
			break;
			case 'distance':
				return '((ACOS(SIN('.$arg.' * PI() / 180) * SIN(`lat` * PI() / 180) + COS('.$arg.' * PI() / 180) * COS(`lat` * PI() / 180) * COS(('.$arg2.' – `lon`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515)';
			break;
			case 'fill':
				return 'CONCAT('.$arg.',\''.$arg2.',\')';
			break;
			case 'remove':
				return 'REPLACE('.$arg.',\','.$arg2.',\',\',\')';
			break;
			case 'check':
				return $arg.' LIKE \'%,'.$arg2.',%\'';
			break;
			case 'online':
				if (!$arg) $arg = DB_PREFIX.'users.id';
				return '(SELECT '.(time()+SESSION_LIFETIME+1).'-expiration FROM '.DB_PREFIX.'sessions WHERE '.DB_PREFIX.'sessions.userid='.$arg.' ORDER BY expiration DESC LIMIT 1)';
			break;
			case 'add':
				return 'IFNULL('.$arg.',0)+1';
			break;
			case 'orderby':
				preg_match('/^\s*([a-z0-9_]+(\s+(ASC|DESC))?(\s*,\s*|\s*$))+|^\s*RAND\(\s*\)\s*$/i', $arg, $m);
				if (!$m) return '';
				return $arg;
			break;
		}
	}
	
	public static function noerror() {
		self::$err = false;	
	}
	public static function yeserror() {
		self::$err = true;	
	}
	
	public static function error($sql, $default = array()) {
		if (!self::$err/* || self::$nDB>0*/) return $default;
		$err_msg = self::errorMsg();
		$err_no = self::errorNo();
		$die = !preg_match('@^SELECT[[:space:]]+@i', $sql);
		Site::$exit = true;
		return Message::dbError($err_no,$err_msg,$sql,$die,self::$DB[9][self::$nDB]['name'].' ['.self::$DB[9][self::$nDB]['host'].']');
	}

}


function escape($s, $addapos = true) {
	return e($s, $addapos);
}

function e($s, $addapos = true) {
	if (is_array($s)) {
		return array_map('e', $s);
	}
	//if (is_null($s)) return 'NULL';
	if ($addapos) $s = '\''.DB::escape($s).'\'';
	else $s = DB::escape($s);
	return $s;
}
function is_n($n) {
	return is_numeric($n) && $n>0;
}




function strjs($st, $inJS = false) {
	if (is_array($st)) {
		$_st = array();
		foreach ($st as $k => $v) {
			$_st[$k] = strjs($v, $inJS);
		}
		return $_st;
	}
//	$st = str_replace('%','%25',$st);
	$pairs = array(
		"\\" => "\\\\",
		'\'' => '\\\'',
		"\n" => "\\n",
		"\r" => "\\r",
		"\xe2\x80\x8c" => "\\u200c",
		"\xe2\x80\x8d" => "\\u200d",
		'"'	=> '\"'
	);
	$st = trim(strtr($st,$pairs));
	$st = str_replace('script>','scr\'+\'ipt>',$st);
	return $st;
}
function strajax($st, $html = false) {
	if (is_array($st)) {
		$_st = array();
		foreach ($st as $k => $v) $_st[$k] = strajax($v, $html);
		return $_st;
	}
	if (!strlen($st)) return '';
	if ($html) {
		$st = str_replace('"','&quot;',$st);
	}
	$st = addslashes($st);
	$st = str_replace("\r\n",'\n',$st);
	$st = str_replace("\n",'\n',$st);
	$st = str_replace("\r",'',$st);
	return $st;
}
function strjava($st) {
	return strjs(str_replace('&amp;nbsp;','&nbsp;',htmlspecialchars($st)));
}

/*
function strdb($t, $strip_sep = false, $addapos = false) {
	if (is_array($t)) return '';
	$t = preg_replace("/&amp;#([0-9]+);/s","&#\\1;",$t);
	$t = preg_replace("/&#(\d+?)([^\d;])/i","&#\\1;\\2",$t);
	$t = preg_replace("/\\\(?!&amp;#|\?#)/","\\",$t);
	$t = str_replace(chr(0xCA),'',$t);
	$t = str_replace("\r",'',$t);
	$t = str_replace("\b",'',$t);
	$t = str_replace("\0","\\\0",$t);
	return e($t, $addapos);
}
*/
function strdbjoin($t) {
	if (is_array($t) || is_object($t)) return e('[[:ARRAY:]]'.serialize($t));
	return e($t,false);
	//return strdb($t);
}

function strjoin($t) {
	if (is_array($t) || is_object($t)) $t = '[[:ARRAY:]]'.serialize($t);
	return $t;
}
function strexp($t) {
	if (!is_array($t) && !is_object($t) && substr($t,0,11)=='[[:ARRAY:]]') {
		$r = @unserialize(substr($t,11));
		if ($r===false) return substr($t,11);
	} else $r = $t;
	return $r;
}


function html($t) {
	if (is_array($t)) return array_map('html', $t);
	if (IS_ADMIN && class_exists('Edit') && Edit::ADMIN_TAG && substr($t,0,strlen(Edit::ADMIN_TAG)+2)=='<'.Edit::ADMIN_TAG.' ') {
		$t = preg_replace('/<'.Edit::ADMIN_TAG.' ([^>]+)>/','',$t);
		$t = str_replace('</'.Edit::ADMIN_TAG.'>','',$t);
	}
	return htmlspecialchars($t,ENT_COMPAT,'UTF-8');
}
function strform($t) {
	if (is_array($t)) return array_map('strform', $t);
	$t = htmlspecialchars($t,ENT_COMPAT,'UTF-8');
	return $t;
}

function strfile($st, $stripLines=false) {
	if (is_array($st)) {
		return array_map('strfile', $st);
	}
	$st = str_replace("\r\n","\n",$st);
	$st = str_replace("\r",'',$st);
	if ($stripLines) $st = Parser::strlines($st);
	return trim($st);
}


function strcode($st) {
	return trim(str_replace("\r",'',$st));
}

function utf82utf16($utf8) {
	// oh please oh please oh please oh please oh please
	if(function_exists('mb_convert_encoding')) {
		return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
	}

	switch(strlen($utf8)) {
		case 1:
			// this case should never be reached, because we are in ASCII range
			// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
			return $utf8;

		case 2:
			// return a UTF-16 character from a 2-byte UTF-8 char
			// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
			return chr(0x07 & (ord($utf8{0}) >> 2))
				 . chr((0xC0 & (ord($utf8{0}) << 6))
					 | (0x3F & ord($utf8{1})));

		case 3:
			// return a UTF-16 character from a 3-byte UTF-8 char
			// see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
			return chr((0xF0 & (ord($utf8{0}) << 4))
					 | (0x0F & (ord($utf8{1}) >> 2)))
				 . chr((0xC0 & (ord($utf8{1}) << 6))
					 | (0x7F & ord($utf8{2})));
	}

	// ignoring UTF-32 for now, sorry
	return '';
}

function _json($var) {
	
	static $jsonReplaces = array(
		array('\\', '\'', '/', "\n", "\t", "\r","\b", "\f", "\0"),
		array('\\\\', '\\\'', '\/', '\n', '\t', '\r', '', '\f', '')
	);
	return str_replace($jsonReplaces[0], $jsonReplaces[1], $var);	
	/*
	$ascii = '';
	$strlen_var = strlen($var);

	for ($c = 0; $c < $strlen_var; ++$c) {

		$ord_var_c = ord($var[$c]);

		switch (true) {
			case $var[$c] == '\'':
				$ascii .= '\\\'';
			break;
			case $ord_var_c == 0x08:
				$ascii .= '\b';
			break;
			case $ord_var_c == 0x09:
				$ascii .= '\t';
			break;
			case $ord_var_c == 0x0A:
				$ascii .= '\n';
			break;
			case $ord_var_c == 0x0C:
				$ascii .= '\f';
			break;
			case $ord_var_c == 0x0D:
				$ascii .= '\r';
			break;

			case $ord_var_c == 0x22:
			case $ord_var_c == 0x2F:
			case $ord_var_c == 0x5C:
				// double quote, slash, slosh
				$ascii .= '\\'.$var{$c};
			break;

			case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
				// characters U-00000000 - U-0000007F (same as ASCII)
				$ascii .= $var{$c};
			break;

			case (($ord_var_c & 0xE0) == 0xC0):
				// characters U-00000080 - U-000007FF, mask 110XXXXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c, ord($var{$c + 1}));
				$c += 1;
				$utf16 = utf82utf16($char);
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
			break;

			case (($ord_var_c & 0xF0) == 0xE0):
				// characters U-00000800 - U-0000FFFF, mask 1110XXXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
							 ord($var{$c + 1}),
							 ord($var{$c + 2}));
				$c += 2;
				$utf16 = utf82utf16($char);
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
			break;

			case (($ord_var_c & 0xF8) == 0xF0):
				// characters U-00010000 - U-001FFFFF, mask 11110XXX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
							 ord($var{$c + 1}),
							 ord($var{$c + 2}),
							 ord($var{$c + 3}));
				$c += 3;
				$utf16 = utf82utf16($char);
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
			break;

			case (($ord_var_c & 0xFC) == 0xF8):
				// characters U-00200000 - U-03FFFFFF, mask 111110XX
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
							 ord($var{$c + 1}),
							 ord($var{$c + 2}),
							 ord($var{$c + 3}),
							 ord($var{$c + 4}));
				$c += 4;
				$utf16 = utf82utf16($char);
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
			break;

			case (($ord_var_c & 0xFE) == 0xFC):
				// characters U-04000000 - U-7FFFFFFF, mask 1111110X
				// see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
				$char = pack('C*', $ord_var_c,
							 ord($var{$c + 1}),
							 ord($var{$c + 2}),
							 ord($var{$c + 3}),
							 ord($var{$c + 4}),
							 ord($var{$c + 5}));
				$c += 5;
				$utf16 = utf82utf16($char);
				$ascii .= sprintf('\u%04s', bin2hex($utf16));
			break;
		}
	}

	return $ascii;
	*/
}

function json($a, $strict = true, $key = false, $depth = 0) {
	//return json_encode($a);	
	
	if (is_null($a)) return 'null';
	if (is_bool($a)) return $a ? 'true' : 'false';   
	if (is_int($a)) return $a;
	if (empty($a)) return '\'\'';
	if (is_scalar($a)) {
		/*
		if ($key) {
			if (strstr($a,'-') || !is_float($a)) $key = false;	
		}
		*/
		$k = $key;
		if ($strict) {
			static $js_func = array (
				'in', 'for', 'while', 'switch', 'function', 'break', 'continue', 'typeof', 'try', 'catch', 'return', 'throw', 'trace', 'delete', 'void', 'if', 'else', 'elseif', 'instanceof', 'with', 'default', 'true', 'false', 'null'
			);
		}
		if (!($f = substr($a,0,1))) $key = false;
		if ($key && (in_array($a, $js_func) || strstr($a,' ') || strstr($a,'.') || strstr($a,'-')/* || !preg_match('/[A-Za-z0-9]/',$a)*/)) $key = false;
		if ($k && is_numeric($f)) $key = false;
		elseif (!$k && is_numeric($a) && $f > 0) $key = true;
		
		return (!$key?'\'':'')._json($a).(!$key?'\'':'');
	}
	
	$isList = true;
	$l = count($a);
	for ($i = 0, reset($a); $i < $l; $i++, next($a)) {
		if (key($a)!==$i) {
			$isList = false;
			break;
		}
		if ($i) break;
	}
	$r = '';
	if ($isList) {
		foreach ($a as $v) $r .= json($v, $strict, false, $depth+1).',';
		return '['.substr($r,0,-1).']';
	} else {
		foreach ($a as $k => $v) $r .= json($k, $strict, true, $depth+1).':'.json($v, $strict, false, $depth+1).',';
		return '{'.substr($r,0,-1).'}';
	}
}


function jsConfig($arr) {
	$conf = '';
	foreach ($arr as $c => $l) {
		$conf .= $c.':'.(is_numeric($l)?$l:(is_array($l)?'[\''.join('\',\'',$l).'\']':(is_bool($l)?($l?'true':'false'):'\''.$l.'\''))).', ';
	}
	return $conf.'temp:[]';
}

function __lang($num, $word_one, $word_mult, $word_med = '') {
	$num = intval(trim($num));
	$word_one = trim($word_one);
	$word_mult = trim($word_mult);
	$word_med = trim($word_med);
	if (!$word_med) $word_med = $word_mult;
	if ($num===1) $ret = $word_one;
	elseif ($num===0) $ret = $word_mult;
	elseif (in_array(substr($num,-2),array(11,12,13,14))) $ret = $word_mult;
	elseif (Session()->Lang=='ru' && substr($num,-1)==1) $ret = $word_one;
	elseif ((($s = substr($num,-1))>=2) && $s<=4) $ret = $word_med;
	else $ret = $word_mult;
	return $ret;
}

function find($str, $needle) {
	return strstr($str, $needle);	
}

function _lang($str, $args) {
	if (is_numeric($str) || !strstr($str,'%')) return $str;
	foreach ($args as $key => $arg) {
		if (is_array($arg)) {
			foreach ($arg as $k => $v) {
				$str = str_replace('{%'.$k.'}',$v,$str);
			}
		} else {
			$str = str_replace('%'.$key,$arg,$str);
		}
	}
	if (find($str,'{number=')) {
		$str = preg_replace('/\{number=([0-9]+),([^,]+),([^,]+)(,[^\}]+)?\}/Ue',"__lang('$1','$2','$3',trim('$4',','))",$str);
	}
	return $str;
}


function l() {
	$args = func_get_args();
	return call_user_func_array('lang', $args);	
}


function lang($str = LANG) {
	if (!$str) return '';
	$admin = $as_array = $editor = $default = false;
	$edit = true;
	$no_save = false;
	$first = substr($str,0,1);
	$key = $str;

	if ($first=='@') {
		$key = substr($key,1);
		$default = $key;
		$editor = true;
		$first = substr($key,0,1);			
	}
	if ($first=='_') {
		$key = substr($key,1);
		$default = $key;
		$edit = false;	
		$first = substr($key,0,1);
	}
	if ($first=='[') {
		$p = strpos($key,']');
		if ($p) {
			$key = substr($key,1,$p-1);
			$default = ltrim(substr($str,strlen($key)+2),'@_]');
		}
	}
	if (!$key) return $str;
	
	if (!$default) $default = $str;
	if ($first=='$') {
		$key = substr($key,1);
		$default = $key;
		$admin = true;
	}
	elseif ($first=='#') {
		$s = substr($key,1);
		if (!$s) return '#__NO_NAME__';
		if (!isset(Index()->db_vars[$s]) && SITE_TYPE!='json') {
			DB::run('INSERT IGNORE INTO `'.DB_PREFIX.'vars` SET `name`='.e($s).', `template`='.e(TEMPLATE));
			$ret = $default;
		} else $ret = @Index()->db_vars[$s];
		if ($edit && Index()->Edit) {
			Index()->Edit->set($ret, 'vars', $key)->parse()->admin();
		}
		return $ret;
	}
	if ($admin && $str==$first) return '$'.$str;
	$args = func_get_args();
	if (!$default) $default = $str;
	$orig = $default;
	$return = false;
	if (!$admin && Conf()->exists('_lang', $key)) {
		$return = Conf()->g2('_lang',$key);
	}
	elseif ($admin && Conf()->exists('_lang_a',$key)) {
		$return = Conf()->g2('_lang_a',$key);
	} else {
		//DB::noerror();
		$rs = DB::row('SELECT id, `text_'.Session()->Lang.'` AS str FROM `'.DB_PREFIX.'lang` WHERE `name`='.e($key).' AND `template`='.e($admin?'admin':TEMPLATE));
		if (isset($rs['id'])) {
			$return = $rs['str'];
			$id = $rs['id'];
			Conf()->s2('_lang'.($admin?'_a':''),$key,$return);
		} else {
			$e = '';
			if (!IS_ADMIN || substr($key,0,strlen(Edit::ADMIN_TAG)+2)!='<'.Edit::ADMIN_TAG.' ') {
				if (!$no_save) {
					$v = e($default);
					foreach (Site::getLanguages() as $l => $a) $e .= ', `text_'.$l.'`='.$v;
					$orig = ltrim($orig,'$');
				}
				//$locate = !$admin && !in_array(SITE_TYPE, array('json'));
				//DB::noerror();
				//$l = ', `location`='.($locate?e('?'.(SITE_TYPE=='index'?'':SITE_TYPE.'&').URL::get()):'\'\'');
				DB::run('INSERT IGNORE INTO `'.DB_PREFIX.'lang` SET `name`='.e($key).$e.', `template`='.e($admin?'admin':TEMPLATE));
				$id = DB::id();
			}
		}
	}
	if (!$return) $return = $orig;
	$orig_return = $return;
	$return = _lang($return, $args);
	/*
	if (defined('IS_ADMIN') && IS_ADMIN && get('hl_word')==$str) {
		$return = '<span style="border:1px solid #CDD206;background:#FEF7C5">'.$return.'</span>';
	}
	*/
	if ($edit && Index()->Edit) {
		if (!isset($id) || !$id) $id = '*'.$key;
		Index()->Edit->set($return, 'lang', $id, 'id', $orig_return)->parse()->admin($editor);
	}
	/*
	if ($as_array) {
		return array(
			'str'			=> $str,
			'return'		=> $return,
			'orig_return'	=> $orig_return
		);	
	}
	*/
	return $return;
}

function tr($str) {
	return lang('$'.$str);
}




function toHash($str, $check = false) {
	if ($check) {
		$p = strpos($str,'@');
		if (!$p) return false;
		$c = base64_decode(substr($str,0,$p));
		$m = substr($str, $p+1);
		if (md5('@av'.date('d-m-Y').'cv-'.$c.'%trfd')===$m) return $c;
		else return false;
	} else {
		return base64_encode($str).'@'.md5('@av'.date('d-m-Y').'cv-'.$str.'%trfd');
	}
}

/*
function getUpHash() {
	return md5('T%6aa'.$_SERVER['REMOTE_ADDR'].'^@d$3fg'.date('d-m-Y').'vr');
}
*/