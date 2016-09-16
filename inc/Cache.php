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
* @file       inc/Cache.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Cache {
	
	private static $cache = array(), $cache_ext = 'cache', $cache_name = '', $cache_reset = false;
	public static $cache_action = '', $in_file = true;
	
	public static function getPageName() {
		$url = URL::get().(get(URL_KEY_PAGE)?'&page='.get(URL_KEY_PAGE):'');
		if ($url==URL_KEY_HOME) $url = '';
		return SITE_TYPE.'-'.sha1($url.'.'.Session()->Lang.'.'.Session()->Currency.'.'.Session()->Template);	
	}
	
	public function inFile($in_file) {
		self::$in_file = $in_file;	
	}
	
	public static function getPageHtml() {
		self::$cache_name = 'html_'.self::getPageName();
		$reset = false;
		if (isset($_SESSION['reset_cache']) && $_SESSION['reset_cache']) {
			self::$cache_reset = true;
			$_SESSION['reset_cache'] = false;
		}
		if (!IS_POST && !self::$cache_reset && !isset($_GET['reset'])) {
			$ret = self::get(self::$cache_name, HTML_CACHE_TIME, true, true, 'OB::handler_cached');
			self::$cache_action = $ret ? 'get' : 'new';
			return $ret;
		} else {
			if (!self::$cache_reset) $_SESSION['reset_cache'] = true;
			self::$cache_action = 'delete';
			// self::delete(self::$cache_name, true);
			return false;
		}
	}
	
	public static function savePageHtml(&$html) {
		if (!Site::$cache_enabled || !Index()->My->cacheEnabled()) return false;
		if (self::$cache_action=='delete' || self::$cache_action=='new') {
			self::$cache_action = 'save';
			if (SITE_TYPE=='index' || SITE_TYPE=='popup' || SITE_TYPE=='ajax') {
				self::$cache_action=='saved';
				return self::save(self::$cache_name, $html, true);
			}
		}
		return false;
	}

	public static function getSmall($name) {
		if (!self::$cache) {
			$key = Session()->SID;
			if (self::$in_file) {
				$file = FTP_DIR_ROOT.DIR_FILES.'temp/'.$key.'.temp';
				if (is_file($file)) {
					$data = file_get_contents($file);
					if ($data) self::$cache = unserialize($data);
				}
			} else {
				$data = DB::row('SELECT `data` FROM `'.DB::getPrefix().'cache` WHERE `name`='.e($key),'data');
				if ($data) self::$cache = unserialize($data);
			}
		}
		if (!$name) return;
		return ((isset(self::$cache[$name]) && self::$cache[$name]) ? self::$cache[$name] : '');
	}
	
	public static function saveSmall($name, $value) {
		if (!$name) return;
		self::$cache[$name] = $value;
	}
	
	public static function storeSmall() {
		$key = Session()->SID;
		if (self::$in_file) {
			file_put_contents(FTP_DIR_ROOT.DIR_FILES.'temp/'.$key.'.temp',serialize(self::$cache));
		} else {
		//	DB::noerror();
			DB::run('REPLACE INTO `'.DB::getPrefix().'cache` SET `data`='.e(serialize(self::$cache)).', `name`='.e($key).', saved='.time());
		}
	}

	public static function save($name,$data,$use_file = false) {
		if (!$name || !$data) return false;
		$data = strjoin($data);
		if ($use_file) {
			$name = fixFileName($name);
			if (!$name) return false;
			$fp = fopen(FTP_DIR_CACHE.$name.'.'.self::$cache_ext,'w');
			fwrite($fp,$data);
			fclose($fp);
			@chmod(FTP_DIR_CACHE.$name.'.'.self::$cache_ext,0666);
			return true;
		} else {
			DB::run('REPLACE INTO `'.DB::getPrefix().'cache` SET `data`='.e($data).', `saved` = '.time().', `name`='.e($name),true);
			DB::commit();
			return true;
		}
	}
	
	public static function get($name, $incTime=3600, $use_file=false, $echo = false, $ob_func = false) {
		if ($use_file) {
			$name = fixFileName($name);
			if (($file = FTP_DIR_CACHE.$name.'.'.self::$cache_ext) && file_exists($file) && (is_numeric(ext($name)) || (!$incTime || ($incTime>0 && filemtime($file) > (time() - $incTime))))) {
				if ($echo) {
					//if ($ob_func) ob_start($ob_func);
					readfile($file);
					echo '<!-- cached '.$file.'-->';
					//if ($ob_func) ob_end_flush();
					return true;
				} else {
					return strexp(file_get_contents($file));
				}
			}
			return false;
		} else {
			return strexp(DB::row('SELECT `data` FROM `'.DB::getPrefix().'cache` WHERE `name`='.e($name).($incTime? ' AND saved > '.(time() - $incTime):''),'data'));
		}
	}
	
	public static function delete($name, $use_file=false) {
		if ($use_file) {
			$name = fixFileName($name);
			@unlink(FTP_DIR_CACHE.$name.'.'.self::$cache_ext);
		} else {
			DB::run('DELETE FROM `'.DB::getPrefix().'cache` WHERE `name`='.e($name));
		}
	}
	
	public static function delete_all($name='', $use_file=false) {
		if ($use_file) {
			$name = fixFileName($name);
			@unlink(FTP_DIR_CACHE.$name.'.'.self::$cache_ext);
		} else {
			if ($name) {
				DB::run('DELETE FROM `'.DB::getPrefix().'cache` WHERE `name` LIKE '.e($name.'%'));
			} else {
				DB::run('TRUNCATE TABLE `'.DB::getPrefix().'cache`');
			}
		}
	}

	
	
	
	

	public static function getText($name, $time = 3600) {
		/*
		global $MEM;
		if ($MEM) {
			//return '<!-- {{MEMcache:_text_'.$name.'}} -->'
			return $MEM->get('text_'.$name);	
		}
		*/
		$row = DB::fetch(DB::qry('SELECT `data` FROM `'.DB::getPrefix().'cache` WHERE `name`='.e($name).($time?' AND `saved`>'.(time()-$time):'')));
		return $row['data']?$row['data']:false;
	}
	
	public static function saveText($name,$data) {
		/*
		global $MEM;
		if ($MEM) {
			return $MEM->set('text_'.$name,$data,3600);	
		}
		*/
		$result = DB::run('REPLACE INTO `'.DB::getPrefix().'cache` SET `name`='.e($name).', `data`='.e($data).', `saved`='.time());
		return $result;
	}
	
	public static function deleteText($name) {
		/*
		global $MEM;
		if ($MEM) {
			return $MEM->delete('text_'.$name);	
		}
		*/
		DB::run('DELETE FROM `'.DB::getPrefix().'cache` WHERE `name`='.e($name));
	}
	
	
	
	public static function getCachedNames($name = '', $type='', $fromFile = true, $limit = 20) {
		$ret = array();
		$l = strlen($type);
		if ($fromFile) {
			$name = fixFileName($name);
			$dir = FTP_DIR_ROOT.'temp/';
			$arr = File::openDirectory($dir);
			$l = strlen($type);
			foreach ($arr as $file) {
				if ($type && substr($file,0,$l)!=$type) continue;
				if (strstr($file,$name)) $ret[]['name'] = substr(substr($file,0,-2),$l);
			}		
		} else {
			$sql = 'SELECT '.($l?'SUBSTR(`name`,'.($l+2).') AS name':'`name`').' FROM `'.DB::getPrefix().'cache` WHERE TRUE';
			if ($type) $sql .= ' AND name LIKE \''.e($type).'.%\'';
			$sql .= ' AND `name` LIKE \'%'.e($name).'%\' ORDER BY saved DESC LIMIT '.$limit;
			$ret = DB::getAll($sql);
		}
		return $ret;
	}
}