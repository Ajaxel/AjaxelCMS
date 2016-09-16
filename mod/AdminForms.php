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
* @file       mod/AdminForms.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminForms extends Admin {
	
	public function __construct() {
		$this->title = 'Forms manager';
		parent::__construct(__CLASS__);
		$this->editor = new AdminFormsEditor($this->id, $this->prefix);
	}
	public function init() {
		$this->table = $this->name;
		$this->fid = intval(request('fid'));
		$this->idcol = 'id';
		if (!$this->fid) $this->fid = intval(request('el','id'));
	}
	

	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'sort_element':
				$sort = post('sort');
				$ex = explode('|',$sort);
				foreach ($ex as $e) {
					list ($id, $sort) = explode('-',$e);
					if (!$id || !$sort) continue;
					DB::run('UPDATE '.$this->prefix.'formfields SET sort='.(int)$sort.' WHERE id='.(int)$id);	
				}
			break;
			case 'edit_element':
				if ($this->fid) {
					$this->el = DB::row('SELECT * FROM '.$this->prefix.'formfields WHERE id='.$this->fid);
					$this->el['settings'] = unserialize($this->el['settings']);
				} else {
					$this->el = array('type'=>intval(post('type')));
				}
				$sel = $this->editor->types($this->el['type']);
				$type_name = $sel[0];
				$settings = $sel[1];
				$set = array();
				if ($this->fid) {
					$set = unserialize(DB::row('SELECT fs FROM '.$this->prefix.'forms WHERE id=(SELECT setid FROM '.$this->prefix.'formfields WHERE id='.$this->fid.')','fs'));
				} else {
					$set['colspan'] = 7;	
				}
				ob_start();
				$this->inc('field_settings', array('settings'=>$settings,'type_name'=>$type_name,'max_colspan'=>$set['colspan']));
				$field_settings = ob_get_contents();
				ob_end_clean();
				$this->json_ret = array('field_settings'=>$field_settings);
			break;
			case 'delete_element':
				if (!$this->fid) break;
				DB::run('DELETE FROM '.$this->prefix.'formfields WHERE id='.$this->fid);
				$this->data = DB::row('SELECT * FROM '.$this->prefix.'forms WHERE id='.$this->id);
				$this->data['fs'] = unserialize($this->data['fs']);
				$this->data['ds'] = unserialize($this->data['ds']);
				$this->form = $this->editor->build($this->id, $this->data);
				ob_start();
				$this->inc('form_preview',array('form'=>$this->form));
				$form_preview = ob_get_contents();
				ob_end_clean();		
				$this->json_ret = array('form_preview'=>$form_preview);		
			break;
			case 'save_element':
				/*
				if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
				if (!$this->data['descr']) $err['descr'] = lang('$Description cannot be empty');
				$this->errors($err);
				*/
				
			
				if (!$this->data['fs']) break;
				$data = array(
					'name'		=> $this->data['name'],
					'fs'		=> serialize($this->data['fs']),
					'ds'		=> serialize($this->data['ds']),
				);
				if (!$this->id) {
					DB::insert($this->table,$data);
					$this->id = DB::id();	
					$this->updated = 1;
				} else {
					DB::update($this->table,$data,$this->id);
					$this->updated = 2;
				}
				$this->affected = DB::affected();
				if ($this->affected) {
					$data = array(
						'edited'	=> $this->time,
						'userid'	=> $this->Index->Session->UserID
					);	
					DB::update($this->table,$data,$this->id);
				}
				$this->el = post('el');
				if ($this->el['type']) {
					$data = array(
						'setid'		=> $this->id,
						'type'		=> $this->el['type'],
						'name'		=> $this->el['name'],
						'settings'	=> serialize($this->el['settings']),
						'step'		=> 1,
						'nodata'	=> 1,
						'display'	=> ''
					);
					if (!$this->fid) {
						DB::insert('formfields',$data);
						$this->fid = DB::id();	
					} else {
						DB::update('formfields',$data,$this->fid);
					}
				}
				
				$this->form = $this->editor->build($this->id, $this->data);
				ob_start();
				$this->inc('form_preview',array('form'=>$this->form));
				$form_preview = ob_get_contents();
				ob_end_clean();
				$this->json_ret = array('form_preview'=>$form_preview);
			break;
		}
	}
	
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				if ($this->isEdit()) break;
			break;
			case 'act':
				$this->global_action();
			break;
			case 'delete';
				DB::run('DELETE FROM '.$this->prefix.'formfields WHERE setid='.(int)$this->id);
				DB::run('DELETE FROM '.$this->prefix.'forms  WHERE id='.(int)$this->id);
			break;
		}
	}
	
	
	public function sql() {
		$this->order = 'id';
		$this->filter = '';
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' LIMIT '.$this->offset.','.$this->limit;
		$this->data = DB::getAll($sql);
		$this->total = DB::rows($this->data);
		$this->data = strform($this->data);
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function window() {
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
			if (!$this->post) $this->id = 0;
			else {
				$this->isEdit();
				$this->post['username'] = Data::user($this->post['userid'], 'login');
				if (!$this->post['username']) $this->post['username'] = 'unknown user';
				$this->post['fs'] = unserialize($this->post['fs']);
				$this->post['ds'] = unserialize($this->post['ds']);
			}
		}
		$this->el = array();

		$this->form = $this->editor->build($this->id, $this->post);
		$this->win($this->name);
	}
}



















class AdminFormsEditor {
	public $type = '';
	public $num = array();
	
	
	public function __construct($setid, $prefix) {
		$this->setid = $setid;
		$this->prefix = $prefix;
		if ($this->setid) {
			$this->num = DB::getAll('SELECT DISTINCT type FROM '.$this->prefix.'formfields WHERE setid='.$this->setid,'type|type');
		}
		$this->type = request('ft');
		$this->id = request('fid');
	}
	
	public function build($setid, $post) {
		$qry = DB::qry('SELECT * FROM '.$this->prefix.'formfields WHERE setid='.$setid.' ORDER BY sort',0,0);
		$data = array();
		if (!$post['fs']['colspan']) $post['fs']['colspan'] = 1;
		$w = (100 / $post['fs']['colspan']);
		$i = 0;
		while ($rs = DB::fetch($qry)) {
			$rs['settings'] = unserialize($rs['settings']);
			if (!$rs['settings']) $rs['settings'] = array();
			if (!$rs['settings']['colspan']) $rs['settings']['colspan'] = 1;
			$rs['settings']['li_width'] = $w * $rs['settings']['colspan'];
			if ($rs['settings']['li_width']>100) $rs['settings']['li_width'] = 100;
			if ($i % $post['fs']['colspan']==0) $rs['settings']['li_clear'] = true; else $rs['settings']['li_clear'] = false;
			if ($rs['settings']['colspan']==$post['fs']['colspan']) $rs['settings']['li_auto'] = true;
			$rs['arr'] = array();
			$data[] = $rs;
			$i+=$rs['settings']['colspan'];
		}
		return $data;
	}
	

	public static function isReservedColumn($name) {
		$reserved = array('ADD','ALL','ALTER','ANALYZE','AND','AS','ASC','ASENSITIVE','AUTO_INCREMENT','BDB','BEFORE','BERKELEYDB','BETWEEN','BIGINT','BINARY','BLOB','BOTH','BY','CALL','CASCADE','CASE','CHANGE','CHAR','CHARACTER','CHECK','COLLATE','COLUMN','COLUMNS','CONDITION','CONNECTION','CONSTRAINT','CONTINUE','CREATE','CROSS','CURRENT_DATE','CURRENT_TIME','CURRENT_TIMESTAMP','CURSOR','DATABASE','DATABASES','DAY_HOUR','DAY_MICROSECOND','DAY_MINUTE','DAY_SECOND','DEC','DECIMAL','DECLARE','DEFAULT','DELAYED','DELETE','DESC','DESCRIBE','DETERMINISTIC','DISTINCT','DISTINCTROW','DIV','DOUBLE','DROP','ELSE','ELSEIF','ENCLOSED','ESCAPED','EXISTS','EXIT','EXPLAIN','FALSE','FETCH','FIELDS','FLOAT','FOR','FORCE','FOREIGN','FOUND','FRAC_SECOND','FROM','FULLTEXT','GRANT','GROUP','HAVING','HIGH_PRIORITY','HOUR_MICROSECOND','HOUR_MINUTE','HOUR_SECOND','IF','IGNORE','IN','INDEX','INFILE','INNER','INNODB','INOUT','INSENSITIVE','INSERT','INT','INTEGER','INTERVAL','INTO','IO_THREAD','IS','ITERATE','JOIN','KEY','KEYS','KILL','LEADING','LEAVE','LEFT','LIKE','LIMIT','LINES','LOAD','LOCALTIME','LOCALTIMESTAMP','LOCK','LONG','LONGBLOB','LONGTEXT','LOOP','LOW_PRIORITY','MASTER_SERVER_ID','MATCH','MEDIUMBLOB','MEDIUMINT','MEDIUMTEXT','MIDDLEINT','MINUTE_MICROSECOND','MINUTE_SECOND','MOD','NATURAL','NOT','NO_WRITE_TO_BINLOG','NULL','NUMERIC','ON','OPTIMIZE','OPTION','OPTIONALLY','OR','ORDER','OUT','OUTER','OUTFILE','PRECISION','PRIMARY','PRIVILEGES','PROCEDURE','PURGE','READ','REAL','REFERENCES','REGEXP','RENAME','REPEAT','REPLACE','REQUIRE','RESTRICT','RETURN','REVOKE','RIGHT','RLIKE','SECOND_MICROSECOND','SELECT','SENSITIVE','SEPARATOR','SET','SHOW','SMALLINT','SOME','SONAME','SPATIAL','SPECIFIC','SQL','SQLEXCEPTION','SQLSTATE','SQLWARNING','SQL_BIG_RESULT','SQL_CALC_FOUND_ROWS','SQL_SMALL_RESULT','SQL_TSI_DAY','SQL_TSI_FRAC_SECOND','SQL_TSI_HOUR','SQL_TSI_MINUTE','SQL_TSI_MONTH','SQL_TSI_QUARTER','SQL_TSI_SECOND','SQL_TSI_WEEK','SQL_TSI_YEAR','SSL','STARTING','STRAIGHT_JOIN','STRIPED','TABLE','TABLES','TERMINATED','THEN','TIMESTAMPADD','TIMESTAMPDIFF','TINYBLOB','TINYINT','TINYTEXT','TO','TRAILING','TRUE','UNDO','UNION','UNIQUE','UNLOCK','UNSIGNED','UPDATE','USAGE','USE','USER_RESOURCES','USING','UTC_DATE','UTC_TIME','UTC_TIMESTAMP','VALUES','VARBINARY','VARCHAR','VARCHARACTER','VARYING','WHEN','WHERE','WHILE','WITH','WRITE','XOR','YEAR_MONTH','ZEROFILL');
		return !in_array(strtoupper($name),$reserved);
	}
	
	public function types($val = false) {
		$ret = array(
			EL_QUESTION		=> array('String', array('question','attr','single'))
			,EL_TEXT		=> array('Text Box', array('name','val','attr','help','ajax','db','validation'))
			,EL_PASSWORD	=> array('Password Box', array('name','sel','attr','help','db','validation'))
			,EL_TEXTAREA	=> array('Text Area', array('name','val','attr','help','db','validation'))
			,EL_SELECT		=> array('Drop Down', array('name','sel','attr','help','ajax','multiple','db','validation'))
			,EL_RADIO		=> array('Radio Buttons', array('name','sel','attr','help','db','validation'))
			,EL_CHECKBOX	=> array('Check Boxes', array('name','sel','attr','help','db','validation'))
			,EL_FILE		=> array('File Upload', array('name','attr','help','db','validation'))
			,EL_HIDDEN		=> array('Hidden Field', array('name','sel','attr','help','db','validation'))
			,EL_DATETIME	=> array('Date Picker', array('name','attr','help','db','validation'))
			,EL_HTML		=> array('HTML', array('html','single'))
			,EL_SMARTY		=> array('Smarty', array('html','single'))
			,EL_PHP			=> array('PHP', array('html','single'))
			,EL_CAPTCHA		=> array('Captcha Image', array('attr','help','validation'))
			,EL_CUSTOM		=> array('Custom Field', array('name','val','attr','help','db','validation'))
			,EL_BLOCK		=> array('Block of Fields', array('name','val','attr','help','db','validation'))
			,EL_PAGEBREAK	=> array('Pagebreak', array('attr'))
		);
		return ($val ? $ret[$val] : $ret);
	}
	
	public function settings($type) {
		$ret = array();
		switch ($type) {
			case EL_QUESTION:
				
			break;
			case EL_CATEGORY:
				
			break;
			case EL_TEXT:
			case EL_PASSWORD:
				
			break;
			case EL_TEXTAREA:
				
			break;
			case EL_SELECT:
			
			break;
			case EL_RADIO:
			
			break;
			case EL_CHECKBOX:
			
			break;
			case EL_FILE:
			
			break;
			case EL_HIDDEN:
			
			break;
			case EL_DATETIME:
			
			break;
			case EL_HTML:
		
			break;
			case EL_SMARTY:
			
			break;
			case EL_PHP:
			
			break;
			case EL_CAPTCHA:
			
			break;
			case EL_CUSTOM:
			
			break;
			case EL_BLOCK:
			
			break;
		}
		return $ret;
	}











































	
}