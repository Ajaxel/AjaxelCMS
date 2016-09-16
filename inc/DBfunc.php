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
* @file       inc/DBfunc.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class DBfunc {
	
	const
		MAX_BACKUP = 30000,
		MAX_BACKUP_SIZE = 4096000,
		MAX_RESTORE = 2000,
		MAX_RESTORE_SIZE = 256000,
		DELIMITER = ";\r\n"
	;
	
	// backup stuff
	public
		$ext = '',
		$filename = '',
		$name = '',
		$tables = array(),
		$full_insert = false,
		$normal = false,
		$exclude_data_tables = array(),
		$template = '',
		$structure = true,
		$content = true,
		$prefix = '',
		$aff = 0,
		$file = '',
		$dir = '',
		$cron = false,
		$all = false,
		$db_name = DB_NAME,
		$restore_template = false
	;

	private
		$area = 'backup',
	
		$backup = array(),
		$restore = array(),
	
		$start = false,
		$backup_path = './',
		$tables_cnt = 0,
		$starttime = 0,
		$fp = false,
		$error = '',
		$func_open = 'fopen',
		$func_close = 'fclose',
		$func_write = 'fwrite',
		$tables_content = array(),
		$tables_structure = array(),
		$structure_done = false,
		$arr_backup_values = array(
			'start',
			'offset',
			'totals',
			'total',
			'backup_path',
			'all',
			'starttime',
			'tables_cnt',
			'tables',
			'filename',
			'name',
			'template',
			'ext',
			'exclude_data_tables',
			'full_insert',
			'normal',
			'structure',
			'content',
			'tables_content',
			'tables_structure',
			'structure_done',
			'affs',
			'aff',
			'db_name'
		),
		$arr_restore_values = array(
			'start',
			'filename',
			'ext',
			'prefix',
			'starttime',
			'aff',
			'errors',
			'db_name',
			'template',
			'prefix_len',
			'restore_template',
			'sql_tables',
			'table_add'
		),
		$done = false,
		$has_data = false,
		$offset = 0,
		$totals = array(),
		$total = 0,
	
		$affs = array(),
		$errors = 0,
	
		$percent = 0,
	
		$prefix_len = 0,
		$sql_tables = array(),
		$table_add = array()
	;
	
	public function __construct() {
		
	}

	public function backup() {
		$this->error = '';
		$this->area = 'backup';
		if ($this->ext=='php') {
			if (!$this->filename) {
				$this->error = 'No filename specified';
				return $this->msg();
			}
			$this->backup = array();
			$time = Date::exactTime();
			$this->generateTables(FTP_DIR_TPLS.$this->template.'/backup/'.$this->filename.'.php', $this->tables, self::tableCharset());
			$this->done = true;
			DB::resetCache();
			return $this->msg('PHP backup file was generated to '.FTP_DIR_TPLS.$this->template.'/backup/'.$this->filename.'.php. '.number_format(Date::exactTime()-$time,3).' seconds taken.');	
		}
		
		$this->backup = Cache::getSmall('db_'.$this->area);
		if ($this->backup && $this->backup['db_name']) {
			$this->db_name = $this->backup['db_name'];
		}
		if ($this->db_name!=DB_NAME) {
			DB::link()->select_db($this->db_name);
		}
		
		if (isset($this->backup['start']) && $this->backup['start']) {
			if (is_file(FTP_DIR_TPLS.$this->backup['template'].'/backup/'.$this->backup['filename'])) {
				return $this->run_backup();
			} else {
				$this->backup = array();
				$this->done = true;
				$this->error = 'File is missing, resetting..';
				$this->toSession();
				return $this->msg();
			}
		} else {
			if (!$this->tables || !is_array($this->tables)) {
				$this->backup = array();
				$this->done = true;
				$this->error = 'You need to pick at least one table to backup, resetting..';
				$this->toSession();
				return $this->msg();	
			}	
		}
		if (!$this->filename) {
			$this->error = 'No filename specified';
			return $this->msg();
		}
		
		$this->backup_path = FTP_DIR_TPLS.$this->template.'/backup/';
		$this->tables_cnt = count($this->tables);
		
		$this->starttime = microtime(true);
		if (!is_dir($this->backup_path)) mkdir($this->backup_path, 0766);
		
		$this->name = $this->filename;
		
		if ($this->ext=='gz') {
			$this->filename = $this->filename.'.sql.gz';
		} else {
			$this->filename = $this->filename.'.sql';
		}
		
		$this->toSession();
		return $this->run_backup();
	}
	
	private function run_backup() {
		$this->fromSession();		
		if ($this->ext=='gz') {
			$func_open = 'gzopen';
			$func_write = 'gzwrite';
			$func_close = 'gzclose';
		} else {
			$func_open = 'fopen';
			$func_write = 'fwrite';
			$func_close = 'fclose';
		}
		
		$this->start = true;
		if (!$this->db_name) $this->db_name = DB_NAME;

		if (!$this->totals) {
			$i = 0;
			DB::noerror();
			if ($this->db_name==DB_NAME) {
				DB::run('TRUNCATE TABLE '.$this->prefix.'counts');
			}
			foreach ($this->tables as $table) {
			//	if ($table==PREFIX.'cache' || $table==PREFIX.'counts') continue;
				if ($this->exclude_data_tables && in_array($table, $this->exclude_data_tables)) {
					if ($table!='users' && $table!='sessions' && $table!='languages') {
						continue;
					}
				}
				$i++;
				$this->aff++;
				$this->total++;
				$this->totals($table);
			}
			$this->fp = $func_open($this->backup_path.$this->filename,'w');
			$func_write($this->fp,"-- Table backup with: Ajaxel CMS v".Site::VERSION."\r\n-- Site URL: ".HTTP_BASE."\r\n-- Creation date: ".date("d-M-Y H:s",time())."\r\n-- Database: ".$this->db_name."\r\n-- MySQL Server version: ".mysqli_get_server_info(DB::link())."\r\n-- Amount of tables: ".$this->tables_cnt."\r\n-- Number of instructions: ".$this->total."\r\n\r\n");
			$this->totals['__prepared'] = 1;
			
			$func_close($this->fp);
			File::chown2($this->backup_path.$this->filename);
			$this->toSession();
			return $this->msg('Database backup prepared, '.$i.' tables, '.$this->total.' total entries');
		}
		if ($this->db_name!=DB_NAME) {
			DB::link()->select_db($this->db_name);
		}

		foreach ($this->tables as $table) {
			if ($this->structure) {
				if (!in_array($table, $this->tables_structure)) {
					DB::optimize($table);
					$this->fp = $func_open($this->backup_path.$this->filename,'ab');
					$this->structure($table);
					$func_close($this->fp);
					$this->tables_structure[] = $table;
					$this->toSession();
				}
			}
			if ($this->content) {
				if (in_array($table, $this->tables_content)) continue;
				$time = Date::exactTime();
				$this->fp = $func_open($this->backup_path.$this->filename,'ab');
				$this->content($table);
				$func_close($this->fp);
				if (!$this->has_data) {
					$this->tables_content[] = $table;
					$this->total++;
				} else {
					$this->toSession();
					return $this->msg('&nbsp;&nbsp;&nbsp;&nbsp;'.$this->percent.'% <em>'.$table.'</em> - '.number_format(Date::exactTime()-$time,2).' sec - '.intval($this->affs[$table]).' row'.($this->affs[$table]!=1?'s':''));
				}
			}
		}
		
		if ($this->ext=='zip') {
			Factory::call('zip_compress')->addFile(file_get_contents($this->backup_path.$this->filename),$this->filename);
			$f = fopen($this->backup_path.$this->filename.'.zip','w');
			fwrite($f,Factory::call('zip_compress')->file());
			fclose($f);
			unlink($this->backup_path.$this->filename);
			$this->filename = $this->filename.'.zip';
		}

		$this->backup = array();
		$this->start = false;
		$this->done = true;
		DB::resetCache();
		$this->toSession();
		return $this->msg('Database '.$this->db_name.' backup was created and saved on:<br />'.$this->backup_path.$this->filename.' ('.sprintf('%01.4f',microtime(true)-$this->starttime).' seconds taken)');
	}
	
	private function remDBprefix($table) {
		return DB::remDBprefix($table);
	}
	
	private function is_not_empty($s) {
		
		if ($s || $s==='0') return true;
		return false;
	}
	
	private function row($sql) {
		$qry = mysqli_query(DB::link(), $sql);
		return mysqli_fetch_assoc($qry);
	}
	
	private function structure($table) {
		$rn = "\r\n";
		$p = ((!$this->all && !$this->normal)?DB_PREFIX:'');
		$t = ((!$this->all && !$this->normal)?self::prefix().self::remDBprefix($table):$table);
		$status = $this->row('SHOW TABLE STATUS FROM `'.$this->db_name.'` WHERE Name='.e($p.$table));
	//	$ret = "\r\n\r\n".'-- Table structure for `'.self::remDBprefix($table).'`'."\r\n\r\n";
		$ret = "\r\n";
		//if (!in_array($table, Site::getGlobalTables())) {			
		//	$ret .= 'SET AUTOCOMMIT=0'.self::DELIMITER;
		//	$ret .= 'SET FOREIGN_KEY_CHECKS=0'.self::DELIMITER;
			$ret .= 'DROP TABLE IF EXISTS `'.$t.'`'.self::DELIMITER;
		//}

		$ret .= 'CREATE TABLE `'.$t.'` ('.$rn;
		$result = mysqli_query(DB::link(), 'SHOW FULL FIELDS FROM '.$p.$table);
		if (!$result) {
			$this->error = "Table $table not existing in database";
			return false;
		}
		$affected = 0;
		while($row = mysqli_fetch_array($result)) {
			$ret .= ' `'.$row['Field'].'` '.$row['Type'];
			if ($this->is_not_empty($row['Default']))
				if (in_array($row['Default'], array('HOUR_MICROSECOND','HOUR_MINUTE','HOUR_SECOND','CURRENT_TIMESTAMP'))) $ret .= ' DEFAULT CURRENT_TIMESTAMP';
				else $ret .= ' DEFAULT '.e($row['Default']);
			if ($row['Null']!='YES') $ret .= ' NOT NULL';
			if ($row['Extra']) $ret .= ' '.$row['Extra'];
			if ($row['Comment']) $ret .= ' COMMENT '.e($row['Comment']);
			$ret .= ",\r\n";
			$affected++;
		}
		//$ret = preg_replace("/,\r\n$/",'', $ret);
		$ret = substr($ret,0,-3);
		$result = mysqli_query(DB::link(), 'SHOW KEYS FROM '.$p.$table);
		while($row = mysqli_fetch_array($result)) {
			$kname = $row['Key_name'];
			if($kname!='PRIMARY' && $row['Non_unique']==0) $kname = "UNIQUE|$kname";
			elseif($kname!='PRIMARY' && $row['Index_type']=='FULLTEXT') $kname = "FULLTEXT|$kname";
			if(!isset($index[$kname])) $index[$kname] = array();
			$index[$kname][] = $row['Column_name'];
		}
		while(list($x, $columns) = @each($index)) {
			$ret .= ",\r\n";
			if($x == 'PRIMARY') $ret .= '   PRIMARY KEY (`'.join($columns, '`, `').'`)';
			else if (substr($x,0,6)=='UNIQUE') $ret .= '   UNIQUE KEY `'.substr($x,7).'` (`'.join($columns, '`, `').'`)';
			else if (substr($x,0,8)=='FULLTEXT') $ret .= '   FULLTEXT KEY '.substr($x,9).' (`'.join($columns, '`, `').'`)';
			else $ret .= '   KEY `'.$x.'` (`'.join($columns, '`, `').'`)';
		}
		$ret .= $rn.') ENGINE='.$status['Engine'].' DEFAULT CHARSET='.self::tableCharset().self::DELIMITER.$rn;
		if ($this->ext=='gz') {
			gzwrite($this->fp, $ret);
		} else {
			fwrite($this->fp, $ret);
		}
		return $affected;
	}
	
	private function where($table) {
		$where = '';
		if ($this->all) return $where;
		if ($this->template) {
			switch ($table) {
				case 'lang':
				case 'vars':
					$where = ' AND template='.e($this->template);
					$where = ' AND template='.e($this->template);
				break;
			}
		}
		return $where;
	}
	
	private function totals($table, $where = -1) {
		if ($this->db_name==DB_NAME && $where===-1) $where = $this->where(self::remDBPrefix($table));
		if ($where===-1) $where = '';
		
		if (!$this->all && $this->db_name==DB_NAME) {
			$this->totals[$table] = DB::getCount($table,$where);
		} else {
			$this->totals[$table] = DB::one('SELECT COUNT(1) FROM '.$table.' WHERE TRUE'.$where);
		}
		$this->total += $this->totals[$table];
	}
	
	private function content($table) {
		if (!isset($this->totals[$table]) || !$this->totals[$table] || $this->offset >= $this->totals[$table]) {
			$this->offset = 0;
			$this->percent = 100;
			$this->has_data = false;
			return false;
		}
		$p = ((!$this->all && !$this->normal)?DB_PREFIX:'');
		$t = ((!$this->all && !$this->normal)?self::prefix().self::remDBprefix($table):$table);
		$where = $this->where($table);
		if (!($result = mysqli_query(DB::link(), 'SELECT * FROM '.$p.$table.' WHERE TRUE'.$where.' LIMIT '.$this->offset.', '.self::MAX_BACKUP))) {
			$this->error = 'Cannot get contents from table '.$p.$table.'.';
			$this->offset = 0;
			return false;
		}
		$this->percent = ceil($this->offset / $this->totals[$table] * 100);
		
		$by_full = '';
		$size = $index = 0;
		if ($this->full_insert) $by_full = ' (`'.join('`, `',DB::columns($table)).'`)';
		if (!isset($this->affs[$table])) $this->affs[$table] = 0;
		while ($row = mysqli_fetch_row($result)) {
			$this->has_data = true;
			$do = true;
			if ($this->exclude_data_tables && in_array($table, $this->exclude_data_tables)) {
				$do = false;
			}
			elseif ($table=='users') {
				if ($row[0]===USER_ID) $do = false;
			}
			elseif ($table=='sessions') {
				if ($row[2]===USER_ID) $do = false;
			}
			if ($do) {
				$insert = 'INSERT INTO `'.$t."`$by_full VALUES (";
				$num = mysqli_num_fields($result);
				for($j=0;$j < $num;$j++) {
					if (!isset($row[$j]) || $row[$j]===NULL) $insert .= 'NULL,';
					elseif (isset($row[$j])) {
						if (is_numeric($row[$j])) {
							$insert .= $row[$j].',';
						} else {
							$insert .= '\''.str_replace('\"','"',mysqli_real_escape_string(DB::link(), $row[$j])).'\',';
						}
					}
				}
				$insert  = substr($insert,0,-1).')'.self::DELIMITER;
				
				if ($this->ext=='gz') {
					gzwrite($this->fp, $insert);
				} else {
					fwrite($this->fp, $insert);
				}
				$this->aff++;
				$this->affs[$table]++;
				
				$size += strlen($insert);
				$index++;
				
				if ($size > self::MAX_BACKUP_SIZE) {
					$this->offset += $index;
					return;
				}
			}
		}
		if ($this->has_data) {
			$this->offset += self::MAX_BACKUP;
			if ($this->totals[$table] < self::MAX_BACKUP) $this->percent = 100;
		}
		else {
			$this->offset = 0;
			$this->percent = 100;
		}
	}
	
	public function msg($msg = '') {
		if (SITE_TYPE=='json') {
			if ($this->ext=='php' || !$msg) {
				$percent = 100;
			} else {
				if (!$this->total) $percent = 100;
				else $percent = ceil($this->aff / $this->total * 100);
				if ($this->area=='backup') $this->backup['percent'] = $percent;
				else $this->restore['percent'] = $percent;
			}
			if (!$msg) $msg = $this->error;
			$this->toSession();
			return array(
				'type'	=> ($this->area=='restore' && $this->done ? 'folder' : ''),
				'text'	=> $msg,
				'done'	=> $this->done,
				'error'	=> $this->error,
				'percent'=> $percent,
				'area'	=> $this->area
			);
		} else {
			ob_start();
			echo $msg.'<br /><script>window.scrollTo(0,999999)</script>';
			ob_flush();
		}
	}
	/*
	private function toSession($name = 'backup') {
		$arr = 'arr_'.$name.'_values';
		foreach ($this->$arr as $k) $this->$name[$k] = $this->$k;
		Cache::saveSmall('db_'.$name,$this->$name);
	}
	
	private function fromSession($name = 'backup') {
		$arr = 'arr_'.$name.'_values';
		foreach ($this->$arr as $k) $this->$k = $this->$name[$k];
	}
	*/
	
	private function toSession() {
		if ($this->area=='backup') {
			foreach ($this->arr_backup_values as $k) $this->backup[$k] = $this->$k;
			Cache::saveSmall('db_backup',$this->backup);
		} else {
			foreach ($this->arr_restore_values as $k) $this->restore[$k] = $this->$k;
			Cache::saveSmall('db_restore',$this->restore);
		}
	}
	
	private function fromSession() {
		if ($this->area=='backup') {
			foreach ($this->arr_backup_values as $k) $this->$k = $this->backup[$k];
		} else {
			foreach ($this->arr_restore_values as $k) $this->$k = $this->restore[$k];
		}
	}
	
	public static function prefix($sql = false) {
		$db_prefix = '[[:DB_PREFIX:]]';
		$prefix = '[[:PREFIX:]]';
		if ($sql===false) {
			return $db_prefix;
		}
		return str_replace($prefix,PREFIX,str_replace($db_prefix,DB_PREFIX,$sql));
	}
	public static function tableCharset() {
		if (post('data','collation')) {
			list($charset) = explode('_',post('data','collation'));
			if ($charset) return $charset;
		}
		return 'utf8';
	}
	
	public function generateTables($file = false, $tables = array(), $charset = 'utf8') {
		if (!is_array($tables)) $tables = DB::tables(false);
		$php = '<?php
/**
* Ajaxel CMS v'.Site::VERSION.'
* Table install without any risk, simple update / refill
* Exported from '.HTTP_BASE.' v.'.Index()->My->version.' on '.date('H:i d-m-Y').'
*/

$sql = array();';
		foreach ($tables as $t) {
			if (!$this->all) $t = self::remDBprefix($t);
			$php .= '
$sql[\''.$t.'\'] = array(\''.str_replace('\'','\\\'',$this->exportTable($t,false,'',$charset,false)).';\',
	array (';
			$qry = DB::qry('SHOW FIELDS FROM `'.DB_PREFIX.$t.'`',0,0);
			$i = 0;
			while($row = DB::fetch($qry)) {
				$ret = '';
				//if (!$this->is_not_empty($row['Default']) && !$row['Extra'] && strtolower(substr($row['Type'],0,3)=='int')) $row['Default'] = '0';
				if ($this->is_not_empty($row['Default'])) $ret .= 'DEFAULT '.e($row['Default']);
				if ($row['Null']!='YES') $ret .= ' NOT NULL';
if ($row['Extra']!='') $ret .= ' '.$row['Extra'];
				$php .= '
		\''.$row['Field'].'\' => array(\''.str_replace('\'','\\\'',$row['Type']).'\', \''.str_replace('\'','\\\'',$ret).'\'),';
				$i++;
			}
			$php .= '
	)
);
';	
		}
		$php .= '

return $sql;
';
		if ($file) {
			$fp = fopen($file,'w');
			fwrite($fp,$php);
			fclose($fp);
			return $file;
		}
		return $php;
	}

	public function exportTable($table,$incData=true,$where='',$charset = 'utf8',$admin = true,$exclude = array(),$copy = false) {
		mysqli_select_db(DB::link(), $this->db_name);
		if (!in_array($table,DB::tables(false, true))) return false;
		mysqli_select_db(DB::link(), DB_NAME);
	//	$ret = "DROP TABLE IF EXISTS `$table`$sep";
		if (!$this->db_name) $this->db_name = DB_NAME;
		$ret = '';
		$ret .= 'CREATE TABLE `'.($copy ? DB_PREFIX.$copy : self::prefix().$table).'` ('."\n";
		$status = $this->row('SHOW TABLE STATUS FROM `'.$this->db_name.'` WHERE Name=\''.$table.'\'');
		$result = mysqli_query(DB::link(), 'SHOW FULL FIELDS FROM '.$this->db_name.'.'.(!$this->all?($this->db_name==DB_NAME?DB_PREFIX:''):'').$table);
		$i = 0;
		if (!$exclude) $exclude = array();
		while($row = mysqli_fetch_assoc($result)) {
			if (in_array($row['Field'], $exclude)) continue;
			
			// FIX!
			//if (!$admin && $i && $row['Field']!='id' && $row['Field']!='os' && $row['Field']!='ip' && (($row['Field']!='en' && strlen($row['Field'])==2) || (substr($row['Field'],-3)!='_en' && substr($row['Field'],-3)!='_id' && preg_match('/_([a-z]{2})$/',$row['Field'])))) continue;
			$ret .= ' `'.$row['Field'].'` '.$row['Type'];
			if ($this->is_not_empty($row['Default']))
				if (in_array($row['Default'], array('HOUR_MICROSECOND','HOUR_MINUTE','HOUR_SECOND','CURRENT_TIMESTAMP'))) $ret .= ' DEFAULT CURRENT_TIMESTAMP';
				else $ret .= ' DEFAULT '.e($row['Default']);
			if ($row['Null']!='YES') $ret .= ' NOT NULL';
			if ($row['Extra']) $ret .= ' '.$row['Extra'];
			if ($row['Comment']) $ret .= ' COMMENT '.e($row['Comment']);
			$ret .= ",\r\n";
			$i++;
		}
		$ret = substr($ret,0,-3);
		$result = mysqli_query(DB::link(), 'SHOW KEYS FROM '.$this->db_name.'.'.($this->db_name==DB_NAME?DB_PREFIX:'').$table);

		$affected = 0;
		while($row = mysqli_fetch_assoc($result)) {
			$kname = $row['Key_name'];
			if($kname!='PRIMARY' && $row['Non_unique']==0) $kname = "UNIQUE|$kname";
			elseif($kname!='PRIMARY' && $row['Index_type']=='FULLTEXT') $kname = "FULLTEXT|$kname";
			if(!isset($index[$kname])) $index[$kname] = array();
			if (in_array($row['Column_name'], $exclude)) continue;
			$index[$kname][] = $row['Column_name'];
			$affected++;
		}
		while(list($x, $columns) = @each($index)) {
			$ret .= ",\r\n";
			if($x == 'PRIMARY') $ret .= '   PRIMARY KEY (`'.join($columns, '`, `').'`)';
			else if (substr($x,0,6)=='UNIQUE') $ret .= '   UNIQUE KEY `'.substr($x,7).'` (`'.join($columns, '`, `').'`)';
			else if (substr($x,0,8)=='FULLTEXT') $ret .= '   FULLTEXT KEY '.substr($x,9).' (`'.join($columns, '`, `').'`)';
			else $ret .= '   KEY `'.$x.'` (`'.join($columns, '`, `').'`)';
		}
		if (!$charset) $charset = 'utf8';
		$ret .= "\r\n) ENGINE=".$status['Engine'];
		$ret .= ' DEFAULT CHARSET='.$charset;
		if ($copy) {
			DB::run($ret);
			$ret = '';
		}
		if ($incData) {
			if ($copy) self::exportTableData($table,$where,$exclude,$copy);
			else $ret .= self::exportTableData($table,$where,$exclude,false);
		}
		return $ret;
	}
	public static function exportTableData($table,$where='',$exclude = array(),$copy = false) {
		$ret = '';
		$qry = mysqli_query(DB::link(), 'SELECT * FROM '.($this->db_name==DB_NAME?DB_PREFIX:'').$table.($where?' WHERE '.$where:''));
		while($row = mysqli_fetch_assoc($qry)) {
			$insert = 'INSERT INTO `'.($copy ? DB_PREFIX.$copy : self::prefix().$table).'` VALUES (';
			$in = array();
			$i = $id = false;
			foreach ($row as $key => $val) {
				if (in_array($key, $exclude)) continue;
				if (!isset($val)) $in[] = '\'\'';
				else $in[] = '\''.str_replace('\"','"',e($val, false)).'\'';
				$i = true;
			}
			if ($i) $insert .= join(',',$in).')'.self::DELIMITER;
			$ret .= $insert;
			if ($copy) {
				DB::run($ret);
				$ret = '';
			}
		}
		return $ret;
	}

	
	private static function splitSqlString(&$ret, $sql) {
		$sql          = rtrim($sql, "\n\r");
		$sql_len      = strlen($sql);
		$char         = '';
		$string_start = '';
		$in_string    = false;
		$nothing      = true;
		$time0        = time();
		for ($i = 0; $i < $sql_len; ++$i) {
			$char = $sql[$i];
			if ($in_string) {
				for (;;) {
					$i = strpos($sql, $string_start, $i);
					if (!$i) {
						$ret[] = array('query' => $sql, 'empty' => $nothing);
						return true;
					}
					else if ($string_start == '`' || $sql[$i-1] != '\\') {
						$string_start = '';
						$in_string = false;
						break;
					}
					else {
						$j = 2;
						$escaped_backslash = false;
						while ($i-$j > 0 && $sql[$i-$j] == '\\') {
							$escaped_backslash = !$escaped_backslash;
							$j++;
						}
						if ($escaped_backslash) {
							$string_start  = '';
							$in_string     = false;
							break;
						}
						else {
							$i++;
						}
					}
				}
			}
			else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*')) {
				$i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
				if ($i === FALSE) {
					break;
				}
				if ($char == '/') $i++;
			}
			else if ($char == ';') {
				$ret[]      = array('query' => substr($sql, 0, $i), 'empty' => $nothing);
				$nothing    = true;
				$sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
				$sql_len    = strlen($sql);
				if ($sql_len) {
					$i      = -1;
				} 
				else {
					return true;
				}
			}
			else if (($char == '"') || ($char == '\'') || ($char == '`')) {
				$in_string    = true;
				$nothing      = false;
				$string_start = $char;
			}
			elseif ($nothing) {
				$nothing = false;
			}
			$time1     = time();
			if (headers_sent()) {
				if ($time1 >= $time0 + 2) {
					$time0 = $time1;
					ob_start();
					echo '. ';
					ob_flush();
				}				
			} else {
				if ($time1 >= $time0 + 30) {
					$time0 = $time1;
					header('X-pmaPing: Pong');
				}
			}
		}
		return true;
	}
	
	private static function runQuery($sql_query,&$total,&$tot,$ret_data) {
		$sql_query = self::prefix($sql_query);
		if (preg_match('@DROP[[:space:]]+DATABASE[[:space:]]+@i', $sql_query)) {
			Conf()->s('msg','Dropping databases is unallowed!');
			return false;
		}
		
		$is_select = preg_match('@^SELECT[[:space:]]+@i', $sql_query);
		$is_explain = $is_count = $is_export = $is_delete = $is_insert = $is_affected = $is_show = $is_maint = $is_analyse = $is_group = $is_func = FALSE;
		if ($is_select) {
			$is_group = preg_match('@(GROUP[[:space:]]+BY|HAVING|SELECT[[:space:]]+DISTINCT)[[:space:]]+@i', $sql_query);
			$is_func =  !$is_group && (preg_match('@[[:space:]]+(SUM|AVG|STD|STDDEV|MIN|MAX|BIT_OR|BIT_AND)\s*\(@i', $sql_query));
			$is_count = !$is_group && (preg_match('@^SELECT[[:space:]]+COUNT\((.*\.+)?.*\)@i', $sql_query));
			$is_export   = (preg_match('@[[:space:]]+INTO[[:space:]]+OUTFILE[[:space:]]+@i', $sql_query));
			$is_analyse  = (preg_match('@[[:space:]]+PROCEDURE[[:space:]]+ANALYSE@i', $sql_query));
		} else if (preg_match('@^EXPLAIN[[:space:]]+@i', $sql_query)) {
			$is_explain  = TRUE;
		} else if (preg_match('@^DELETE[[:space:]]+@i', $sql_query)) {
			$is_delete   = TRUE;
			$is_affected = TRUE;
		} else if (preg_match('@^(INSERT|LOAD[[:space:]]+DATA|REPLACE)[[:space:]]+@i', $sql_query)) {
			$is_insert   = TRUE;
			$is_affected = TRUE;
		} else if (preg_match('@^UPDATE[[:space:]]+@i', $sql_query)) {
			$is_affected = TRUE;
		} else if (preg_match('@^SHOW[[:space:]]+@i', $sql_query)) {
			$is_show     = TRUE;
		} else if (preg_match('@^(CHECK|ANALYZE|REPAIR|OPTIMIZE)[[:space:]]+TABLE[[:space:]]+@i', $sql_query)) {
			$is_maint    = TRUE;
		}
		
		$pos = (int)$_GET['pos'];
	
		if (isset($pos)
			&& !($is_count || $is_export || $is_func || $is_analyse)
			&& preg_match('@^SELECT[[:space:]]+@i', $sql_query)
			&& !preg_match('@[[:space:]]LIMIT[[:space:]0-9,-]+$@i', $sql_query)) {
			$sql_limit_to_append = " LIMIT $pos, 30";
			if (preg_match('@(.*)([[:space:]](PROCEDURE[[:space:]](.*)|FOR[[:space:]]+UPDATE|LOCK[[:space:]]+IN[[:space:]]+SHARE[[:space:]]+MODE))$@i', $sql_query, $regs)) {
				$full_sql_query  = $regs[1] . $sql_limit_to_append . $regs[2];
			} else {
				$full_sql_query  = $sql_query . $sql_limit_to_append;
			}
			if (isset($display_query)) {
				if (preg_match('@((.|\n)*)(([[:space:]](PROCEDURE[[:space:]](.*)|FOR[[:space:]]+UPDATE|LOCK[[:space:]]+IN[[:space:]]+SHARE[[:space:]]+MODE))|;)[[:space:]]*$@i', $display_query, $regs)) {
					$display_query  = $regs[1] . $sql_limit_to_append . $regs[3];
				} else {
					$display_query  = $display_query . $sql_limit_to_append;
				}
			}
		} else {
			$full_sql_query = $sql_query;
		}
		list($usec, $sec) = explode(' ',microtime());
		$querytime_before = ((float)$usec + (float)$sec);
		$result = mysqli_query(DB::link(), $full_sql_query);
		Conf()->plus('QUERIES',1);
		list($usec, $sec) = explode(' ',microtime());
		$querytime_after = ((float)$usec + (float)$sec);
		Conf()->s('querytime', $querytime_after - $querytime_before);
		if (mysqli_errno()) {
			Conf()->s('DB_MSG',$full_sql_query);
			return false;
		}
		if (!$is_affected) {
			$num_rows = ($result ? @mysqli_num_rows($result) : 0);
		} 
		elseif (!$num_rows && $is_affected) {
			$num_rows = @mysqli_affected_rows(DB::link());
		}
		
		if ($ret_data) {
			if ($total <= 100) {
				if (!mysqli_errno()) {
					Conf()->fill('SQL_RESULT',$full_sql_query."\n-- Time taken: ".sprintf('%0.5f',Conf()->g('querytime')).($num_rows?($is_affected?" seconds, Rows Affected: $num_rows":" seconds, Rows Returned: $num_rows"):($is_affected?" seconds, Rows Affected: $num_rows":'')));
				}
				if ($result && $num_rows && !$is_affected) {
					$dump = array();
					$i = 0;
					while ($rs = mysqli_fetch_assoc($result)) {
						$dump[] = $rs;
						$i++;
						if ($i>500) break;
					}
					$ret = p($dump, 0);
					Conf()->s('CLOSE_REST', true);
					Conf()->s('DB_MSG',$ret);
				}	
			} 
			else {
				Conf()->s('DB_MSG',lang('$There are first 100 queries, the others are just hidden'));
			}
		}
		return $result;
	}
	
	public static function parseSqlString($sql_query, $ret_data = false) {
		if (!$sql_query) {
			Conf()->s('DB_MSG',lang('$Query is empty'));
			return false;	
		}
		
	//	$sql_query = str_replace(';##% |;##%%',';',$sql_query);
		$pieces = array();
		self::splitSqlString($pieces, $sql_query);
		$pieces_count = count($pieces);
		$max_nofile_length = 0;
		$max_nofile_pieces = 50;
		$max_file_length   = 50000;
		$max_file_pieces   = 50;	
		$db = DB_NAME;	
		$total = 0;
		$tot = 0;
		if ($sql_file != 'none' && (($max_file_pieces != 0 && ($pieces_count > $max_file_pieces)) || ($max_file_length != 0 && (strlen($sql_query) > $max_file_length)))) {
			$sql_query_cpy = $sql_query = '';
			$save_bandwidth = true;
			$save_bandwidth_length = $max_file_length;
			$save_bandwidth_pieces = $max_file_pieces;
		} else {
			$sql_query_cpy = $sql_query;
			if (($max_nofile_length != 0 && (strlen($sql_query_cpy) > $max_nofile_length)) || ($max_nofile_pieces != 0 && $pieces_count > $max_nofile_pieces)) {
				$sql_query_cpy = $sql_query = '';
				$save_bandwidth = TRUE;
				$save_bandwidth_length = $max_nofile_length;
				$save_bandwidth_pieces = $max_nofile_pieces;
			}
		}
		if ($pieces_count == 1 && !empty($pieces[0]['query'])) {
			$sql_query = $pieces[0]['query'];
			if (!self::runQuery($sql_query,$total,$tot,$ret_data)) {				
				Conf()->s('DB_MSG', mysqli_error(DB::link()));
				return false;
			}
		}
		else {
			
			$mult = true;
			$info_msg = '';
			$info_count = 0;
			$count = $pieces_count;
			if ($pieces[$count - 1]['empty']) $count--;
			for ($i = 0; $i < $count; $i++) {
				$a_sql_query = $pieces[$i]['query'];
				if ($i == $count - 1 && preg_match('@^((-- |#)[^\n]*\n|/\*.*?\*/)*(SELECT|SHOW)@i', $a_sql_query)) {
					$complete_query = $sql_query;
					$display_query = $sql_query;
					$sql_query = $a_sql_query;
					if (isset($display_query)) {
						if (preg_match('@((.|\n)*)(([[:space:]](PROCEDURE[[:space:]](.*)|FOR[[:space:]]+UPDATE|LOCK[[:space:]]+IN[[:space:]]+SHARE[[:space:]]+MODE))|;)[[:space:]]*$@i', $display_query, $regs)) {
							$display_query  = $regs[1] . $sql_limit_to_append . $regs[3];
						} else {
							$display_query  = $display_query . $sql_limit_to_append;
						}
					}				
				}
				$total++;
				self::runQuery($a_sql_query,$total,$tot,$ret_data);
			}
		}
	}
	
	
	public static function installTables($sql, $tables = false, $ret_data = false) {
		$arrTables = DB::tables(false, true);
		foreach ($sql as $table => $arrSet) {
			if (in_array($table,$arrTables)) {
				if ($tables && is_array($tables) && !in_array($table, $tables)) continue;
				$describe = DB::describe($table, true, false);
				$prev_col = '';
				foreach ($arrSet[1] as $col => $set) {
					if (!array_key_exists($col, $describe)) {
						$s = 'ALTER TABLE `'.DB_PREFIX.$table.'` ADD `'.$col.'` '.$set[0].' '.$set[1].' '.($prev_col?' AFTER `'.$prev_col.'`':'');
						if (!DB::run($s)) {
							return false;
						}
						$prev_col = $col;
						if ($ret_data) {
							Conf()->plus('QUERIES',1);
							Conf()->fill('SQL_RESULT',$s);
						}
					}
					elseif ($describe[$col]!=$set[0]) {
						$s = 'ALTER TABLE `'.DB_PREFIX.$table.'` CHANGE `'.$col.'` `'.$col.'` '.$set[0].' '.$set[1];
						if (!DB::run($s)) {
							return false;
						}
						if ($ret_data) {
							Conf()->plus('QUERIES',1);
							Conf()->fill('SQL_RESULT',$s);
						}
					}
					$prev_col = $col;
				}
				foreach ($describe as $col => $x) {
					if (!array_key_exists($col, $arrSet[1])) {
						if (strlen($col)==2 || preg_match('/_([a-z]{2})$/',$col)) continue;
						$s = 'ALTER TABLE `'.DB_PREFIX.$table.'` DROP `'.$col.'`';
						if (!DB::run($s)) {
							return false;
						}
						if ($ret_data) {
							Conf()->plus('QUERIES',1);
							Conf()->fill('SQL_RESULT',$s);
						}
					}
				}
			}
			elseif (!DB::run(self::prefix($arrSet[0]),true)) {
				return false;
			}
			else Conf()->plus('QUERIES',1);
		}
		/*
		if (!in_array('users_profile',$arrTables)) {
			if (!DB::run('CREATE TABLE `'.DB_PREFIX.'users_profile` (`setid` int(11) NOT NULL, PRIMARY KEY (`setid`)) ENGINE=INNODB',true)) {
				return false;	
			}
			Conf()->plus('QUERIES',1);
		}
		*/
		return true;	
	}
	
	
	public function restore() {
		$time = Date::exactTime();
		$this->area = 'restore';
		$this->prefix_len = strlen($this->prefix);
		$this->restore = Cache::getSmall('db_'.$this->area);
		
		
		if (isset($this->restore['start']) && $this->restore['start']) {
			$this->backup_path = FTP_DIR_TPLS.$this->restore['template'].'/backup/';
			if (is_file($this->backup_path.$this->restore['filename'])) {
				return $this->run_restore();
			} else {
				$this->restore = array();
				$this->done = true;
				$this->error = 'File is missing, resetting..';
				$this->toSession();
				return $this->msg('File is missing, resetting..');
			}
		}
		elseif (!$this->filename) {
			Cache::saveSmall('db_'.$this->area,NULL);
			return array(
				'error'	=> 'No backup file specified'
			);
		}
		$this->backup_path = FTP_DIR_TPLS.$this->template.'/backup/';
		if (!is_file($this->backup_path.$this->filename)) {
			Cache::saveSmall('db_'.$this->area,NULL);
			return array(
				'error'	=> 'File '.$this->backup_path.$this->filename.' is missing'
			);
		}
		if ($this->ext=='php') {
			$sql = require $this->backup_path.$this->filename;
			$this->installTables($sql);
			$this->aff = Conf()->g('QUERIES');
			$this->total = $this->aff;
		}
		elseif ($this->ext=='zip') {
			require_once(FTP_DIR_ROOT.'inc/lib/PclZip.php');
			$cwd = getcwd();
			chdir($this->backup_path);
			$Zip = new PclZip($this->filename);
			if ($Zip->extract(PCLZIP_OPT_REMOVE_ALL_PATH)) {
				chdir($cwd);
				$file = str_replace('.zip','',$this->filename).'.sql';
				if (file_exists($this->backup_path.$file)) {
					unlink($this->backup_path.$this->filename);
					$this->ext = 'sql';
					$this->filename = $file;
					$this->toSession();
				} else {
					$this->error = 'ERROR: Couldn\'t find a file in your zip-archive named: '.$file;
				}
			} else {
				chdir($cwd);
				$this->error = 'ERROR: Couldn\'t extract your file';
			}
			return $this->msg('Extracted to '.$this->filename);
		}
		$this->starttime = microtime(true);
		$this->aff = 0;
		$this->total = 0;
		$this->toSession();
		return $this->run_restore();
	}
	
	private function create($create) {
		if (!$create) return;
		if (!@mysqli_query(DB::link(), self::prefix($create)) && ($e = mysqli_error(DB::link()))) {
			d(array($create,$e));
			$this->errors++;
		}	
	}

	private function run_restore() {
		$this->fromSession();
		$this->start = true;
		
		if ($this->ext=='gz') {
			$func_open = 'gzopen';
			$func_close = 'gzclose';
			$func_seek = 'gzseek';
			$func_tell = 'gztell';
			$func_getc = 'gzgetc';
			$func_gets = 'gzgets';
		} else {
			$func_open = 'fopen';
			$func_close = 'fclose';
			$func_seek = 'fseek';
			$func_tell = 'ftell';
			$func_getc = 'fgetc';
			$func_gets = 'fgets';
		}
		
		if (!$this->db_name) $this->db_name = DB_NAME;
		
		if (!$this->total) {
			if ($this->ext=='gz') {
				$fp = fopen($this->backup_path.$this->filename, 'rb');
				fseek($fp, -4, SEEK_END);
				$unpack = unpack('V', fread($fp, 4));
				$this->total = end($unpack);
				unset($unpack);
				fclose($fp);
				if (!$this->total) $this->total = filesize($this->backup_path.$this->filename) * 9.5;
			}
			else $this->total = filesize($this->backup_path.$this->filename);
		}
		
		$handle = $func_open($this->backup_path.$this->filename,'r');
		if (!$handle) {
			$this->error = 'ERROR: Couldn\'t open '.$this->backup_path.$this->filename.'';
			return $this->msg('Error in opening the file');
		}
		$func_seek($handle,$this->aff);
		
		/*
		if ($tell!=$this->aff) {
			$this->error = $tell.' != '.$this->aff.', ftell and fseek mismatch with '.$func_tell.'';
			return $this->msg('For fuck sake!');
		}
		*/		
		
		$sql = '';
		$i = 0;
		$size = 0;

		$prev = '';
		if ($this->db_name) {
			mysqli_select_db(DB::link(), $this->db_name);
		}
		$create = '';
		while (($buffer = $func_gets($handle))!==false) {
			$size += strlen($buffer);
			$buffer = trim($buffer);
			if (!$buffer) continue;
			$s2 = substr($buffer,0,2);
			$s7 = substr($buffer,0,7);
			$s6 = substr($buffer,0,6);
			$s5 = substr($buffer,0,5);
			$insert = false;

			if ($s2==='--' || $s2==='/*' || $buffer===$s2) {
				$this->create($create);
				if ($create) {
					$this->create($create);
					$create = '';
					$prev = '';
				}
				$sql = '';
				continue;
			}
			if ($s7==='INSERT ' || $s7==='UPDATE ' || $s5==='DROP ' || $s6==='ALTER ' || $s5==='LOAD ') {
				if ($create) {
					$this->create($create);
					$create = '';
					$prev = '';
					$sql = '';
				}
				$prev = $buffer;
				$sql .= $buffer;
				$insert = true;
			}
			elseif ($s7==='CREATE ') {
				if ($create) {
					$this->create($create);
					$create = '';
				}
				$create = $buffer;
				$prev = '';
				$sql = '';
			}
			elseif ($s6==='CHECK ' || $s7==='ANALYZE' || $s7==='REPAIR ' || $s7==='OPTIMIZ' || $s7==='TRIGGER') {
				if ($create) {
					$this->create($create);
					$create = '';
					$prev = '';
					$sql = '';
				}
				$prev = $buffer;
				$sql .= $buffer;
				$insert = true;
			}
			elseif ($create) {
				$create .= $buffer."\n";
			}
			
			
			if ($insert) {
				if (1 || $this->allow_restore_sql($sql)) {
					$sql = self::prefix($sql);
					if (!@mysqli_query(DB::link(), $sql) && ($e = mysqli_error(DB::link()))) {
						d(array($sql,$e));
						$this->aff = $func_tell($handle);
						if ($this->count_as_error($e)) {
							$this->errors++;
							if ($this->errors > 100) {
								$this->error = $e.'<br />'.$sql.' '.$this->aff.'';
								$this->start = false;
								$this->aff = 0;
								$this->total = 0;
								$this->backup = array();
								$this->toSession();
							}
							return $this->msg('MySQL ERROR: '.$e.'<br />'.$sql);
						}
					}
				}
			
				$sql = '';
				
				if ($size > self::MAX_RESTORE_SIZE || $i > self::MAX_RESTORE) {
					$this->aff = $func_tell($handle);
					$this->toSession();
					$i = 0;
					$ex = explode(' ',self::prefix($prev),5);
					return $this->msg($ex[0].' '.$ex[1].' '.$ex[2].' ('.File::display_size($this->aff, true).')');
				}
				$i++;
			}
			else {
				$prev = self::prefix($buffer);
				$sql .= $prev."\n";
			}
		}		
		$func_close($handle);
		
		$this->backup = array();
		$this->start = false;
		$this->done = true;
		$this->toSession();
		if (!$this->aff && !$i && $this->ext!='php') {
			$this->error = 'ERROR: Couldn\'t open '.$this->backup_path.$this->filename.'. File is damaged.';
			return $this->msg('Error in opening the file');
		}
		return $this->msg('Backup file '.$this->filename.' has been imported to '.$this->db_name.' database ('.sprintf('%01.4f',microtime(true)-$this->starttime).' seconds taken).');
	}
	
	private function count_as_error($e) {
		if (strstr($e,'Duplicate entry')) return false;
		return true;
	}
	
	
	private function build_col($r) {
		return $r['Type'].($this->is_not_empty($r['Default'])?' DEFAULT '.e($r['Default']):'').($r['Null']!='Yes'?' NOT NULL':'').($r['Extra']?' '.$r['Extra']:'');
	}
	
	
	public function fix_sql(&$sql) {
		return false;
		if (substr($sql,0,22)=='DROP TABLE IF EXISTS `') {
			return false;
		}
		elseif (substr($sql,0,14)=='CREATE TABLE `') {
			// create table
			if (preg_match('/CREATE TABLE `([^`]+)`/',$sql,$m)) {
				$table = $m[1];
				$q = mysqli_query(DB::link(), 'SHOW FIELDS FROM `'.$table.'`');
				if ($q) { // if exists
					$old = $new = $this->sql_tables[$table] = array();
					while ($r = mysqli_fetch_assoc($q)) {
						$old[] = $r['Field'];
					}
					mysqli_free_result($q);
					$s = str_replace('CREATE TABLE `','CREATE TEMPORARY TABLE `temp_',$sql);
					mysqli_query(DB::link(), $s);
					$q = mysqli_query(DB::link(), 'SHOW FIELDS FROM `temp_'.$table.'`');
					while ($r = mysqli_fetch_assoc($q)) {
						$a = array($r['Field'],$this->build_col($r));
						$new[] = $a;
					//	$this->sql_tables[$table][] = $a;
					}
					mysqli_free_result($q);
					$changed = $prev_col = false;
					foreach ($new as $i => $a) {
						if ($a[0]=='id') {
							$this->sql_tables[$table][] = false;
							continue;	
						}
						if (!in_array($a[0],$old)) {
							$s = 'ALTER TABLE `'.DB_PREFIX.$table.'` ADD `'.$a[0].'` '.$a[1].' '.($prev_col?' AFTER `'.$prev_col.'`':'');
							//mysqli_query($s);
							$this->sql_tables[$table][] = $a[0];
						} else {
							if ($old[$i]!=$a[0]) {
								$this->sql_tables[$table][] = false;
							} else {
								$this->sql_tables[$table][] = $a[0];
							}
						}
						$prev_col = $a[0];
						if ($old[$i]!=$a[0]) {
							$changed = true;
						}
					}
					if (!$changed) $this->sql_tables[$table] = false;
					return false;
				} else {
					$this->table_add[$table] = true;
					return true;	
				}
			}
		}
		elseif (substr($sql,0,13)=='INSERT INTO `') {
			

			if ($this->sql_tables[$table]) {
				
			}
			
			return true;
		}

		
	}
	
	public function restored_sql() {
		if ($this->table_add[$table]) {
			
		}
	}
	
	
	private function allow_restore_sql(&$sql) {
		$sql = trim($sql);
		if (!$sql || strlen($sql)<10) return false;

		if ($this->restore_template) {
			$s = str_replace('[[:DB_PREFIX:]]','',$sql);
			
			if (substr($s,0,22+$this->prefix_len)=='DROP TABLE IF EXISTS `'.$this->prefix) {
				return true;	
			}
			if (substr($s,0,14+$this->prefix_len)=='CREATE TABLE `'.$this->prefix) {
				return true;	
			}
			if (substr($s,0,13+$this->prefix_len)=='INSERT INTO `'.$this->prefix) {
				return true;	
			}
			if (preg_match('/^(INSERT INTO|DROP TABLE IF EXISTS|CREATE TABLE) `('.join('|',Conf()->g('global_tables')).')`/',$s)) {
				return false;
			}
			return true;
			
			/*
			elseif ($this->fix_sql($sql)) {
				return true;	
			}
			
			return false;
			
			
			elseif (substr($sql,0,18)=='INSERT INTO `lang`') {
				// lang problems, column mismatch
				// INSERT INTO `lang` VALUES ('200','Hosted on %1','posh','','');
				$sql = preg_replace('/^INSERT INTO `lang` VALUES \(\'([0-9]+)\',/','INSERT INTO `lang` VALUES (\'\',',$sql);
				return true;
			}
			elseif (substr($sql,0,19)=='INSERT INTO `users`') {
				$sql = preg_replace('/^INSERT INTO `users` VALUES \(\'([0-9]+)\',/','INSERT INTO `users` VALUES (\'\',',$sql);
				return true;
			}
			
			elseif (substr($sql,0,27)=='INSERT INTO `users_profile`') {
				$sql = preg_replace('/^INSERT INTO `users_profile` VALUES \(\'([0-9]+)\',/','INSERT INTO `users_profile` VALUES (\'\',',$sql);	
				return true;
			}
			
			elseif (substr($sql,0,29)=='INSERT INTO `users_transfers`') {
				$sql = preg_replace('/^INSERT INTO `users_transfers` VALUES \(\'([0-9]+)\',/','INSERT INTO `users_transfers` VALUES (\'\',',$sql);	
				return true;
			}
			
			elseif (substr($sql,0,33)=='INSERT INTO `[[:DB_PREFIX:]]lang`') {
				$sql = preg_replace('/^INSERT INTO `\[\[:DB_PREFIX:\]\]lang` VALUES \(\'([0-9]+)\',/','INSERT INTO `[[:DB_PREFIX:]]lang` VALUES (\'\',',$sql);
				return true;
			}
			elseif (substr($sql,0,34)=='INSERT INTO `[[:DB_PREFIX:]]users`') {
				$sql = preg_replace('/^INSERT INTO `\[\[:DB_PREFIX:\]\]users` VALUES \(\'([0-9]+)\',/','INSERT INTO `[[:DB_PREFIX:]]users` VALUES (\'\',',$sql);
				return true;
			}
			
			elseif (substr($sql,0,42)=='INSERT INTO `[[:DB_PREFIX:]]users_profile`') {
				$sql = preg_replace('/^INSERT INTO `\[\[:DB_PREFIX:\]\]users_profile` VALUES \(\'([0-9]+)\',/','INSERT INTO `[[:DB_PREFIX:]]users_profile` VALUES (\'\',',$sql);	
				return true;
			}
			
			elseif (substr($sql,0,44)=='INSERT INTO `[[:DB_PREFIX:]]users_transfers`') {
				$sql = preg_replace('/^INSERT INTO `\[\[:DB_PREFIX:\]\]users_transfers` VALUES (\'([0-9]+)\',/','INSERT INTO `[[:DB_PREFIX:]]users_transfers` VALUES (\'\',',$sql);	
				return true;
			}
			if (preg_match('/^(INSERT INTO|DROP TABLE IF EXISTS|CREATE TABLE) `('.join('|',Conf()->g('global_tables')).')`/',$sql)) {
				return false;
			}
			*/
		}
		return true;
	}
}