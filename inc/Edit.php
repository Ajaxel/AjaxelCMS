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
* @file       inc/Edit.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class Edit {
	
	public
		$Index,
		$rs = array(),
		$table = '',
		$id = 0,
		$setid = 0,
		$idcol = 'id',
		$cols = array(),
		$editable_cols = array(),
		$lang = '',
		$value = '',
	
		$snippet = array(),
		$visual_index = 0,
		$dummy = '',
		$id_file = '',
		$file = ''
	;
	
	public static $index = 1, $time;
	private static $skip = false, $parsing = array();
	private $admin = false;
	
	const ADMIN_TAG = 'e';
	
	public function __construct(&$Index) {
		OB::$friendly = Site::$Mode==MODE_FRIENDLY && HTACCESS_WRITE;
		$this->Index =& $Index;
		$this->lang =& $this->Index->Session->Lang;
		
		if (!defined('EDIT_LOADED')) {
			if (!IS_ADMIN || !$this->Index->My->use_edit || ADMIN || in_array(SITE_TYPE, array('pdf', 'xml', 'rss', 'download', 'print')) || IM) {
				define ('EDIT_LOADED', false);
				return false;
			} else {
				define ('EDIT_LOADED', true);	
			}
		}
		if (IS_VISUAL) {
			$this->dummy = '_dummy';	
		} else {
			if (isset($_SESSION['VISUAL_FILES'])) unset($_SESSION['VISUAL_FILES']);	
		}
		$this->admin = true;

		if (get('a-edit')=='quick_save' && isset($_POST['id'])) $this->quick_save();
		if ($this->admin && isset($_SESSION['EDIT_INDEX'])) {
			if ($_SESSION['EDIT_INDEX']>6000) $_SESSION['EDIT_INDEX'] = 1;
			self::$index = $_SESSION['EDIT_INDEX'] + 1;
		}
	}

	public function visual_parse($file) {
		if (SITE_TYPE!='index' || !IS_VISUAL || !defined('VISUAL_TAGS') || !VISUAL_TAGS) return $file;
		require_once FTP_DIR_ROOT.'inc/EditFunctions.php';
		return edit_visual_parse($file);
	}
	public function unvisual($html) {
		require_once FTP_DIR_ROOT.'inc/EditFunctions.php';
		return edit_unvisual($html);
	}
	public function visual($html) {
		if (!$html || !IS_VISUAL) return false;
		require_once FTP_DIR_ROOT.'inc/EditFunctions.php';
		return edit_visual($html);
	}
	
	public function parse() {
		switch ($this->table) {
			case 'lang':
			case 'vars':
				$this->parseText($this->rs,'text_'.$this->lang);
				
			break;
			default:
				if (!in_array($this->table,Conf()->g('global_tables')) && isset($this->rs['is_admin']) && !$this->rs['is_admin']) {
					return $this;
				}
				$arr = array('descr','body','teaser','title');
				foreach ($arr as $col) {
					if (isset($this->rs[$col]) && $this->rs[$col]) {
					//	if (is_array($this->rs)) {
							$this->parseText($this->rs[$col],$col);
					//	} else {

					//	}
					}
				}
			break;
		}
		return $this;
	}
	private function fixStr(&$str) {
		$str = str_replace('[LANG]',Session()->Lang,$str);
		$str = str_replace('[USERID]',Session()->UserID,$str);
		$str = str_replace('[LOGIN]',Session()->Login,$str);
		$str = str_replace('[EMAIL]',Session()->Email,$str);
	}
	
	
			
	private function tpl_if($match) {
		list($_, $if, $c) = $match;
	//	unset($GLOBALS['GLOBALS']);
		$if = create_function('', "extract(\$GLOBALS); return $if;");
		if ($if()) {
			return $c;
		}
	}
	
	private function parseText(&$str,$col) {
		
		$id = $this->id;
		$table = $this->table;
		$rs =& $this->rs;
		$value = $this->value;
		$idcol = $this->idcol;
		
		if (strstr($str,'}')) {
			if (strstr($str,'{Session-')) {
				$str = preg_replace('/\{Session-(>|&gt;)([^!]+)\}/Uei',"\$this->session('$2','$col');",$str);
			}
			if (strstr($str,'{lang|')) {
				$str = preg_replace('/\{lang\|([^!]+)\}/Ue',"\$this->lang('$1','$col');",$str);
			}
			if (strstr($str,'{request|')) {
				$str = preg_replace('/\{request\|([^!]+)\}/Ue',"\$this->request('$1','$col');",$str);
			}
			/*
			{if $User.UserID}
			{/if}
			*/
			/*
			// THINK ABOUT IT!
			if (strstr($str,'{/if}')) {
				self::parsing($col);
				$str = preg_replace_callback('/\{if\s([^\}]+)\}(.+?)\{\/if\}/Us', array($this,'tpl_if'), $str);
			}
			*/
		}
		/*
		[!snippet_name!]
			some text
		[/!snippet_name!]
		*/
		if (strstr($str,'[!/')) {
			//$str = preg_replace('/\[!smarty!\](.+)\[!\/smarty!\]/Ue',"\$this->smarty('$1','$col');",$str);
			//$str = preg_replace('/\[!(([^!]+)(\|([^!]+))?)!\]([\s\S]+)\[!\/\2!\]/Ue',"\$this->snippet('$1','$5','$col');",$str);
			$str = preg_replace('/\[!(([^!]+)(\|(.+))?)!\](.+)\[!\/\2!\]/Ue',"\$this->snippet('$1','$5','$col');",$str);
		}
		/*
		[!snippet_name|argument1|argument2!]
		*/
		if (strstr($str,'!]')) {
			$str = preg_replace('/\[!([^!]+)!\]/Ue',"\$this->snippet('$1','','$col');",$str);
		}
		/*
		[#table|id|column#]
		*/
		if (strstr($str,'[#') && strstr($str,'#]')) {
			$str = preg_replace('/\[#([^#]+)#\]/Ue',"\$this->db('$1','$col','$table','$id');",$str);
		}
		
		
		$this->id = $id;
		$this->table = $table;
		$this->rs =& $rs;
		$this->value = $value;
		$this->idcol = $idcol;
	}
	
	private function pr(&$str) {
		$str = str_replace('\"','"',$str);
	}	
	
	private function session($str,$col) {
		if (!$str) return '';
		self::pr($str);
		self::parsing($col);
		if (substr($str,0,9)=='profile->') {
			$str = substr($str,9);
			return $this->Index->Session->profile[$str];
		} else {
			return $this->Index->Session->$str;
		}
	}
	
	public static function edit_id() {
		self::$index++;
		return 'a-edit_'.self::$time.'_'.self::$index;
	}
	
	private function lang($str,$col) {
		self::pr($str);
		self::fixStr($str);
		self::parsing($col);
		$ex = explode('|',$str);
		if (!@$ex[0]) return $str;
		$ex[0] = '!'.$ex[0];
		$ret = call_user_func_array('lang',$ex);
		$ret = '<'.self::ADMIN_TAG.' id="'.self::edit_id().'" title="'.strform($ret['str']).'" alt="'.strform($ret['orig_return']).'" class="a-lang [\'text_'.$this->lang.'\',\'lang\',0,6]">'.$ret['return'].'</'.self::ADMIN_TAG.'>';
		return $ret;
	}
	
	private function request($str,$col) {
		self::pr($str);
		self::fixStr($str);
		self::parsing($col);
		$ex = explode('|',$str);
		if (!$ex[0]) return $str;
		self::$index++;
		if (@$ex[1]) $ret = html(@$_REQUEST[$ex[0]][$ex[1]]);
		else $ret = html(@$_REQUEST[$ex[0]]);
		return $ret;
	}

	public function snippet($str,$text = '',$col = false) {
		self::pr($str);
		self::pr($text);
		if ($col) self::parsing($col);
		switch ($str) {
			case 'smarty':
				$contents = $this->Index->Smarty->fetch('string:'.$text);
			break;
			case 'inc':
				$contents = $this->Index->Smarty->fetch($text);
			break;
			case 'php':
				ob_start();
				eval(rtrim(str_replace('<br />','',$text),';').';');
				$contents = ob_get_contents();
				ob_end_clean();
			break;
			default:
				self::fixStr($str);
				$ex = explode('|',$str);
				$name = $ex[0];
				switch ($name) {
					case 'inc':
						$contents = $this->Index->Smarty->fetch($ex[1]);
					break;
					default:
						$this->snippet = array();
						array_shift($ex);
						if (!$name) return '';
						$__snippet = DB::row('SELECT id, `source` FROM '.DB_PREFIX.'snippets WHERE name='.e($name).' AND active=1');
						if (!$__snippet) {
							$contents = '[!'.$str.'!]';
							$__snippet = array();
						}
						else {
							$this->snippet = $__snippet;
							$this->snippet['text'] = $text;
							$this->snippet['args'] = $ex;
							if (!$this->snippet['source']) $contents = '[!'.$str.'!]';
							else {
								if ($this->Index->Smarty) $this->Index->Smarty->assign('snippet',$this->snippet);
								ob_start();
								eval('?>'.$this->snippet['source']);
								$contents = ob_get_contents();
								ob_end_clean();
							}
						}
						if ($this->admin) {
							$contents = '<'.self::ADMIN_TAG.' class="a-snippet [\'source\',\'snippets\','.$this->snippet['id'].',0,0,0,'.(isset($this->rs['active'])?(int)$this->rs['active']:1).']">'.(!$contents?'<'.self::ADMIN_TAG.' class="a-snippet_empty ui-corner-all"><i>'.lang('$Snippet').': <b>'.$name.'</b></i></'.self::ADMIN_TAG.'>':$contents).'</'.self::ADMIN_TAG.'>';
						}
					break;
				}
			break;
		}
		return $contents;
	}
	private function db($str,$col) {
		self::fixStr($str);
		self::parsing($col);
		self::pr($str);
		$ex = explode('|',$str);
		if (count($ex)!=3) return '<b>Must be 3 arguments: [#table_name|id|column#]</b>';
		if (!strstr($ex[2],'(') && !in_array($ex[2],DB::columns($ex[0]))) return '<b>Wrong column or table: ('.$ex[0].': '.$ex[2].')</b>';
		$idcol = 'id';
		if ($ex[0]=='users_profile') $idcol = 'setid';
		$sql = 'SELECT '.$ex[2].' FROM '.DB::prefix($ex[0]).' WHERE `'.$idcol.'`='.e($ex[1]);
		return DB::one($sql);
	}
	
	private function parsing($column) {
		if ($this->table=='lang' || $this->table=='vars') return false;
		if (is_array($this->table)) extract($this->table);
		self::$parsing[$this->table.'|'.$column.'|'.$this->id] = true;	
	}
	
	public function isParsed($col) {
		if (array_key_exists($this->table.'|'.$col.'|'.$this->id,self::$parsing)) return true;
		return false;
	}
	
	private function quick_save() {
		require_once FTP_DIR_ROOT.'inc/EditFunctions.php';
		return edit_quick_save();
		
	}
	
	public function load() {
		if (!$this->admin) return false;
		require_once FTP_DIR_ROOT.'inc/EditFunctions.php';
		edit_load($this);
	}
	
	public function skip() {
		self::$skip = true;
	}
	
	public function set(&$rs, $table, $id, $idcol = 'id', $value = '', $cols_only = false, $force_rich = true) {	
		$this->rs =& $rs;
		$this->table = $table;
		$this->id = $id;
		$this->idcol = $idcol;
		$this->value = $value;

		if (!$this->admin || !$id) return $this;
		if (self::$skip) {
			self::$skip = false;
			return $this;	
		}
		$textareas = array();
		switch ($this->table) {
			case 'content_article':
			case 'content_html':
			case 'content_banner':
				$textareas[] = 'teaser';
			break;
		}
		switch ($this->table) {
			case 'lang':
			case 'vars':
				
			break;
			default:				
				$not_editable_cols = Conf()->g('not_editable_cols');
				$this->cols = DB::describe($this->table);
				$texts = array('text','blob','mediumblob','mediumtext','longblob','longtext');
				foreach ($this->cols as $c => $d) {
					if ($cols_only && !in_array($c,$cols_only)) continue;
					if (in_array($c,$not_editable_cols)) continue;
					if (in_array($d,$texts)) {
						
						if (in_array($c, $textareas) || !$force_rich) {
							$type = 3; // textarea, 5
						} else {
							$type = 1; // editor, 6
						}
					} else {
						$type = 2; // input
					}
					$this->editable_cols[$c] = $type;
				}
			break;
		}
		return $this;
	}
	
	
	public function admin($force_editor = false) {
		if (!$this->admin || !$this->id) return array();
		require_once FTP_DIR_ROOT.'inc/EditFunctions.php';
		edit_admin($this, $force_editor);
	}
	
	public function no_edit($c) {
		if (isset($this->rs[$c]) && is_numeric($this->rs[$c]) && strlen($this->rs[$c])>=10) return true;
		return false;
	}
}