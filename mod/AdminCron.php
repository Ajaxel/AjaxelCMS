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
* @file       mod/AdminCron.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminCron extends Admin {
	
	public function __construct() {
		$this->title = 'Cron &amp; Utilities';
		parent::__construct(__CLASS__);
	}
	public function init() {		
		$this->button['save'] = false;

		$this->array['do'] = array(
			'go'				=> 'Run cron',
		//	'install_tables'	=> 'Re-install/repair tables',
		//	'translate_m'		=> 'Translate menu',
		//	'translate_c'		=> 'Translate content',
			'translate_l'		=> 'Translate vocabulary with '.USE_TRANSLATE,
			'1'					=> '',
			'write_db'			=> 'Write stats data',
		//	'fix_countries'		=> 'Fix countries',
			'write_is_admin'	=> 'Write/Check is_admin column',
			'calc_speed'		=> 'Detect internet speed',
			'2'					=> '',
		);
		if (!USE_TRANSLATE) unset($this->array['do']['translate_l']);
	}
	public function listing() {
		$do = get('do');
		if ($do=='restore' && get('file')) {
			$this->restore(get('file'));
			exit;
		}
		elseif ($do && strlen($do)>1 && array_key_exists($do, $this->array['do'])) {
			if (method_exists($this, $do)) {
				$this->$do();
				$this->done();
				exit;
			} else {
				echo 'Undefined action: '.get('do').'';
				exit;
			}
		}
		elseif ($do) {
			echo 'Undefined action: '.get('do').'';
			exit;
		}
	}
	
	private function go() {
		$this->scroll('<b>Removing temporary files:</b>');
		$this->_clear_temp();
		$this->scroll('<br /><b>Removing temporary data:</b>');
		$this->_clean_db();
		$this->scroll('<br /><b>Re-calculating data:</b>');
		$this->_calc_all();
	}

	private function delMin($dir, $ext) {
		if (!is_dir($dir)) return;
		$dh = opendir($dir);
		$i = 0;
		while ($f = readdir($dh)) {
			if ($f=='.' || $f=='..' || !is_file($dir.$f)) continue;
			$ext1 = ext($f);
			if ($ext1!=$ext) continue;
			$n = nameOnly($f);
			$ext2 = ext($n);
			if ($ext2=='min' && $n.'.'.$ext1!=$f && is_file($dir.$n.'.'.$ext1) && @unlink($dir.$f)) {
				$i++;
			}
		}
		return $i;
	}
	
	private function _clear_temp() {
		$files = File::delFolder(FTP_DIR_ROOT.DIR_FILES.'temp/',true,true,false);
		$dirs = File::delFolder(FTP_DIR_ROOT.DIR_FILES.'temp/',true,false,false);
		$files2 = File::delFolder(FTP_DIR_TPL.'temp/',true,true,false);
		$files3 = File::delFolder(FTP_DIR_ROOT.'files/temp/',true,true,false);
		
		$files4 = 0;
		$files4 += $this->delMin(FTP_DIR_TPLS.'css/','css');
		$files4 += $this->delMin(FTP_DIR_TPLS.'js/','js');
		$files4 += $this->delMin(FTP_DIR_TPLS.'js/plugins/','js');
		$files4 += $this->delMin(FTP_DIR_TPLS.'css/plugins/','css');
		$files4 += $this->delMin(FTP_DIR_TPL.'css/','css');
		$files4 += $this->delMin(FTP_DIR_TPL.'js/','js');

		$this->scroll(lang('$%1 temp folders and %2 temp files were removed!',$dirs,$files+$files2+$files3+$files4));
		$this->_clean_empty_folders();
	}

	private function done() {
		$msg = '';
		echo '<hr /><h3>'.lang('$Done!').'</h3><button onclick="location.href=\''.URL::ht('/?'.URL_KEY_ADMIN.'=cron').'\';this.disabled=true">'.lang('$Back to cron').'</button>';
		$this->scroll();
	}
	
	private function scroll($message = '') {
		ob_start();
		if ($message) {
			echo '<div style="font:14px Geneva">'.$message.'</div>';
		}
		echo '<script>window.scroll(0,9999999);</script>';	
		ob_flush();
	}

	private function locate($url) {
		$this->scroll('<hr>Please wait..<script>window.location.href=\'/?'.URL_KEY_ADMIN.'=cron&do='.get('do').$url.'\';</script>');
	}
	
	private function body() {
		echo '<html><head><title>Admin Cron :: '.$this->array['do'][get('do')].'</title><style type="text/css">body{font:12px Verdana;color:#242424;line-height:130%}</style><body>';	
	}
	
	private function write_db() {
		require FTP_DIR_ROOT.'config/system/stats_db.php';
		Stats_DB::write();
	}

	private function _calc_all() {
		$sql = 'UPDATE '.$this->prefix.'content a SET comments=(SELECT COUNT(1) FROM '.$this->prefix.'comments b WHERE b.setid=a.id AND (b.table IS NULL OR b.table=\'\' OR b.table=\'content\') AND b.active!=\'2\')';
		DB::run($sql);
		$this->scroll('Content comments were updated. Affected: '.intval(DB::affected()));
		
		$sql = 'UPDATE '.$this->prefix.'entries a SET comments=(SELECT COUNT(1) FROM '.$this->prefix.'comments b WHERE b.setid=a.id AND b.table=\'entries\' AND b.active!=\'2\')';
		DB::run($sql);
		$this->scroll('Entries comments were updated. Affected: '.intval(DB::affected()));
		
		$sql = 'UPDATE '.$this->prefix.'menu a SET cnt3=(SELECT COUNT(1) FROM '.$this->prefix.'entries b WHERE b.menuid=a.id AND b.active!=\'2\')';
		DB::run($sql);
		$this->scroll('Entries count for menu was updated. Affected: '.intval(DB::affected()));
		
		/*
		$sql = 'UPDATE '.$this->prefix.'menu a SET cnt=(SELECT COUNT(1) FROM '.$this->prefix.'content b WHERE b.menuid=a.id AND b.active!=\'2\')';
		DB::run($sql);
		$this->scroll('Content count for menu was updated. Affected: '.intval(DB::affected()).'<br />');
		*/
		
		$aff = 0;
		$tables = DB::tables();
		$sql = 'SELECT id, menuid FROM '.$this->prefix.'content ORDER BY id';
		$_qry = DB::qry($sql,0,0);
		$menu_cnt2 = array(); // entries total
		while ($_rs = DB::fetch($_qry)) {
			$setid = $_rs['id'];
			$data = array();
			$unions = array();
			foreach ($this->modules as $m => $arr) {
				if (!in_array('content_'.$m, $tables)) continue;
				$unions[] = '(SELECT '.e($m).' AS module, \''.$arr['id'].'\' AS modid, rid, sort FROM '.$this->prefix.'content_'.$m.' WHERE setid='.$setid.' AND lang=\''.$this->lang.'\' AND active!=2)';
			}
			if (!$unions) break;
			$sql = ''.join(' UNION ',$unions).' ORDER BY sort';
			$qry = DB::qry($sql,0,0);
			
			$i = 0;
			while ($rs = DB::fetch($qry)) {
				$data[] = $rs['modid'].':'.$rs['rid'];
				$i++;
			}
			if (!$i) {
				if (NO_DELETE) {
					DB::run('UPDATE '.$this->prefix.'content SET inserts=\'\', cnt=0, active=2 WHERE id='.$setid);
				} else {
					DB::run('DELETE FROM '.$this->prefix.'content WHERE id='.$setid);
				}
			}
			else {
				DB::run('UPDATE '.$this->prefix.'content SET inserts=\''.join(',',$data).'\', cnt='.$i.' WHERE id='.$setid);
				$aff += DB::affected();
			}
			if (!isset($menu_cnt2[$_rs['menuid']])) $menu_cnt2[$_rs['menuid']] = 0;
			$menu_cnt2[$_rs['menuid']] += $i;
		}
		$this->scroll('Menu content blocks were updated. Affected: '.intval($aff));
		$aff = 0;
		foreach ($menu_cnt2 as $menuid => $count) {
			$sql = 'UPDATE '.$this->prefix.'menu SET cnt2='.$count.' WHERE id='.$menuid.'';	
			DB::run($sql);
			$aff += DB::affected();
		}
		$this->scroll('Menu inserts were updated. Affected: '.intval($aff));
		
		if (in_array('forum_categories',DB::tables())) {
			$aff = 0;
			$qry = DB::qry('SELECT id FROM '.$this->prefix.'forum_categories',0,0);
			while ($rs = DB::fetch($qry)) {
				DB::run('UPDATE '.$this->prefix.'forum_threads a SET posts=(SELECT COUNT(1) FROM '.$this->prefix.'forum_posts b WHERE b.setid=a.id) WHERE catid='.$rs['id']);
				$aff += DB::affected();
				DB::run('UPDATE '.$this->prefix.'forum_categories a SET posts=(SELECT COUNT(1) FROM '.$this->prefix.'forum_posts b WHERE b.catid=a.id) WHERE id='.$rs['id']);
				$aff += DB::affected();
				DB::run('UPDATE '.$this->prefix.'forum_categories a SET threads=(SELECT COUNT(1) FROM '.$this->prefix.'forum_threads b WHERE b.catid=a.id) WHERE id='.$rs['id']);
				$aff += DB::affected();
				
			}
			DB::free($qry);
			$this->scroll('Forum was updated. Affected: '.intval($aff));
		}
	}

	

	
	private function write_is_admin() {
		DB::resetCache();
		$tables = DB::tables();
		foreach ($tables as $table) {
			if (substr($table,0,8)=='content_') {
				$cols = DB::columns($table);
				if (!in_array('is_admin',$cols)) {
					$sql = 'ALTER TABLE '.$this->prefix.$table.' ADD is_admin ENUM (\'0\',\'1\') NOT NULL DEFAULT \'0\'';
					sql($sql);
					DB::run($sql);
					$i++;
				}
			}
			elseif (substr($table,0,5)=='grid_') {
				$cols = DB::columns($table);
				if (!in_array('is_admin',$cols)) {
					$sql = 'ALTER TABLE '.$this->prefix.$table.' ADD is_admin ENUM (\'0\',\'1\') NOT NULL DEFAULT \'0\'';
					sql($sql);
					DB::run($sql);
					$i++;
				}
			}
		}
		if (!$i) echo 'column <em>is_admin</em> is in all tables now, nothing to add';
	}

	
	
	private function _clean_db() {
		if (defined('USE_CLICKS') && USE_CLICKS && defined('CLICKS_DAYS')) {
			$del = CLICKS_DAYS * 86400;
			DB::run('DELETE FROM '.$this->prefix.'visitor_clicks WHERE added<'.($this->time-$del).'');	
			$this->scroll(DB::affected().' old clicks were removed');
		}
		if (defined('USE_SEARCHES') && USE_SEARCHES && defined('SEARHES_DAYS')) {
			$del = SEARHES_DAYS * 86400;
			DB::run('DELETE FROM '.$this->prefix.'visitor_searches WHERE added<'.($this->time-$del).'');	
			$this->scroll(DB::affected().' old searches were removed');
		}
		DB::run('TRUNCATE TABLE '.$this->prefix.'cache');
		$this->scroll('Cache table truncated');
		DB::run('TRUNCATE TABLE '.$this->prefix.'counts');
		$this->scroll('Count table truncated');
	}
	
	private $empty_removed_total = 0;
	private $empty_removed = 0;
	private $empty_files_total = 0;
	private $empty_files_size = 0;
	
	private function _clean_empty_folders($dir = false, $sub = false) {
		if (!$dir) {
			$dh = opendir(FTP_DIR_ROOT);
			while ($file = readdir($dh)) {
				if ($file=='.' || $file=='..') continue;
				if (is_file(FTP_DIR_ROOT.$file)) continue;
				if (substr($file,-6)=='_files' && is_dir(FTP_DIR_ROOT.$file.'/temp') && is_dir(FTP_DIR_ROOT.$file.'/email')) {
					$this->_clean_empty_folders(FTP_DIR_ROOT.$file.'/');
				}
			}
			closedir($dh);
			$this->scroll(lang('$%1 empty ID folders were removed, %2 total folders removed',$this->empty_removed,$this->empty_removed_total));
			$this->scroll(lang('$%1 total files found. Total size: %2',$this->empty_files_total, File::display_size($this->empty_files_size)));
			return;
		}
		elseif ($sub) {
			$files = $size = 0;
			$dh = opendir($dir);
			while ($file = readdir($dh)) {
				if ($file=='.' || $file=='..') continue;
				if (is_file($dir.$file)) {
					$files++;
					$size += filesize($dir.$file);
				}
				elseif (is_dir($dir.$file)) {
					$_dh = opendir($dir.$file.'/');	
					while ($_file = readdir($_dh)) {
						if ($_file=='.' || $_file=='..') continue;
						if (is_file($dir.$file.'/'.$_file)) {
							$files++;
							$size += filesize($dir.$file.'/'.$_file);
						}
					}
					closedir($_dh);
				}
			}
			closedir($dh);
			if (!$files) {
				$dirs = File::delFolder($dir,true,false,true);
				$this->scroll(lang('$Removing folder: %1',$dir));
				$this->empty_removed_total += $dirs;
				$this->empty_removed += 1;
			} else {
				$this->empty_files_total += $files;
				$this->empty_files_size += $size;
			}
		} else {
			$dh = opendir($dir);
			while ($file = readdir($dh)) {
				if ($file=='.' || $file=='..') continue;
				if (substr($file,0,8)=='content_' || $file=='entries' || substr($file,0,5)=='grid_') {
					$_dh = opendir($dir.$file);
					while ($_file = readdir($_dh)) {
						if ($_file=='.' || $_file=='..') continue;
						if (is_dir($dir.$file.'/'.$_file)) {
							$this->_clean_empty_folders($dir.$file.'/'.$_file.'/', true);
						}
					}
					closedir($_dh);
				}
			}
			closedir($dh);
		}
	}
	
	
	
	
	
	private function calc_speed() {
		$server_load = $this->_server_load();
		if ($server_load) {
			$this->scroll('<h3>'.lang('$Server load is %1',$server_load));
		}
		$speed = File::display_size($this->_detect_inet_speed());
		$this->scroll('<h3>'.lang('$Your internet speed is %1/s',$speed));
		
	}

	private function _server_load() {
		if (strstr(strtolower(PHP_OS),'win')) {
			$serverstats = @shell_exec("typeperf \"Processor(_Total)\% Processor Time\" -sc 1");
			if ($serverstats) {
				$server_reply = explode("\n",str_replace("\r","",$serverstats));
				$serverstats = array_slice($server_reply,2,1);
				$statline = explode(',',str_replace('"','',@$serverstats[0]));
				$server_load = round(@$statline[1],2);
			}
		} elseif ($serverstats = @exec("uptime")) {
			preg_match( "/(?:averages)?\: ([0-9\.]+)[^0-9\.]+([0-9\.]+)[^0-9\.]+([0-9\.]+)\s*/", $serverstats, $load );
			$server_load = $load[1];
		}
		return $server_load;
	}
	
	private function _detect_inet_speed($size = 1024000) {
		flush();
		$start_time = microtime();
		$comment = "<!--O-->";
		$len = strlen($comment);
		for($i = 0; $i < $size; $i += $len) {
			echo $comment;
		}
		flush();
		list($a_dec, $a_sec) = explode(' ', $start_time);
		list($b_dec, $b_sec) = explode(' ', microtime());
		$duration = $b_sec - $a_sec + $b_dec - $a_dec;
		if($duration != 0) {
			return $size / $duration;
		} else {
			return log(0);
		}
	}	
	
	private function translate_l() {
		ignore_user_abort(0);
		set_time_limit(1800);
		$this->body();
		$id = get('lang_id',0);
		$i = $j = 0;
		$w = array();
		foreach ($this->langs as $l => $x) {
			$w[] = '`text_'.$l.'`=\'\'';
		}
		$where = join(' OR ',$w);
		$where = 'TRUE';
		$more = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'lang WHERE '.$where.' AND id>'.(int)$id);
		$total = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'lang WHERE '.$where);
		$this->scroll('<div style="color:#CC0000;font:bold 15px Tahoma;">'.($total?intval(($total-$more)/$total*100):'0').'% language phrases were translated <span style="font:normal 15px Tahoma;color:#000">('.($total-$more).' of '.$total.')</span></div>');
		$w = array();
		foreach ($this->langs as $l => $x) {
			$w[] = '`text_'.$l.'`=\'\'';
		}
		$where = ' AND ('.join(' OR ',$w).')';
		$sql = 'SELECT * FROM '.DB_PREFIX.'lang WHERE id>'.(int)$id.$where.' ORDER BY id';
		$qry = DB::qry($sql,0,0);
		while ($rs = DB::fetch($qry)) {
			if (substr($rs['name'],0,1)=='$') $name = substr($rs['name'],1);
			else $name = $rs['name'];
			$_name = $name;
			if (preg_match('/[a-z]/i',$name)) $from_lang = 'en'; else $from_lang = MY_LANGUAGE;
			
			foreach ($this->langs as $l => $x) {
				if (!$rs['text_'.$l]) {
					$i++;
					$this->scroll('<span style="color:green">['.$rs['template'].']</span> <img src="/tpls/img/flags/16/'.$from_lang.'.png" alt="'.$from_lang.'" /> <b>'.$name.'</b>');
					break;
				}
			}
			$this->_translate($name);
			foreach ($this->langs as $l => $x) {
				if ($from_lang==$l) {
					DB::run('UPDATE '.DB_PREFIX.'lang SET text_'.$l.'='.e($_name).' WHERE id='.$rs['id'].' AND text_'.$l.'=\'\'');
					continue;
				}
				if (!$rs['text_'.$l]) {
					$word = translate($name, $from_lang, $l, true);
					usleep(200);
					if (!$word || ($l!=$from_lang && $word==$name)) {
						if ($j==10) {
							return $this->scroll('<a href="javascript:window.location.reload()"><h3 style="font:19px Tahoma;color:red">'.ucfirst(USE_TRANSLATE).' is exausted, please try again later</h3></a> (<a href="/?'.URL_KEY_ADMIN.'=cron&do='.get('do').'&lang_id='.$rs['id'].'">next &gt;&gt;</a>)');
						}
						$j++;
					}
					elseif (strlen(trim($word))) {
						if ($l!='en') $j = 0;
						$this->_translate($word, false);
						DB::run('UPDATE '.DB_PREFIX.'lang SET text_'.$l.'='.e($word).' WHERE id='.$rs['id'].' OR (text_'.$l.'=\'\' AND name LIKE '.e($rs['name']).')');
						$this->scroll('<img src="/tpls/img/flags/16/'.$l.'.png" alt="'.$x[0].'" title="'.$x[0].'" /> '.$word);
					}
				}
			}
			if ($i==10) {
				return $this->locate('&lang_id='.$rs['id']);
			}
		}
		$this->scroll('<hr /><a href="?'.URL_KEY_ADMIN.'=lang&l=all">Done, please click this link to check everything</a>');
	}

}