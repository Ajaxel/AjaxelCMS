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
* @file       inc/Spider.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

function Spider($arr = array()) {
	if (get('spider') && array_key_exists(get('spider'),$arr)) {
		$class = 'Spider_'.get('spider');
		require FTP_DIR_TPL.'classes/Spiders/'.$class.'.php';
		$spider = new $class;
		$spider->init();
	} else {
		echo '<h3 style="border-top:1px solid #dedede;border-bottom:1px solid #dedede;background:#f9f9f9;padding:10px">Run spider:</h3>';	
		foreach ($arr as $k => $v) {
			echo '<p><button type="button"'.(!$v[1]?' disabled="disabled"':'').' style="text-align:left;padding-left:5px;width:200px;border:1px solid #dedede;background:buttonface" onclick="location.href=\''.HTTP_EXT.'spider-'.$k.'\';this.disabled=true">'.$k.'. '.$v[0].'</button></p>';
		}
	}
	exit;
}



abstract class Spider {
	
	protected $ext = '_';
	protected $name = '';
	protected $key = '';
	protected $parsed = array();
	protected $url = '';
	protected $cache = true;
	protected $cron = false;
	
	private $old_prefix = '';

	protected function __construct($name) {
		$this->name = strtolower($name);
		$ex = explode('_',$this->name);
		$this->ext = '_'.$ex[1];
		$key = substr($this->name,7);
		$this->dir = FTP_DIR_TPL.'classes/Spiders/'.$this->name.'/';
		if (!is_dir($this->dir) || !is_dir($this->dir.'parsed/')) {
			@mkdir($this->dir);
			@mkdir($this->dir.'parsed/');
			@mkdir($this->dir.'img/');
			@mkdir($this->dir.'cache/');
		}
		if (isset($_GET['delete']) && is_dir($this->dir)) {
			File::rmdir($this->dir.'parsed/',false);
			File::rmdir($this->dir.'cache/',false);
			$this->refresh();
		} else {
			$dh = opendir($this->dir.'parsed/');
			$this->cron = isset($_GET['cron']) ? true : false;
			while ($file = readdir($dh)) {
				if ($file=='.' || $file=='..' || $file=='.htaccess') continue;
				$name = str_replace('.txt','',$file);
				$this->parsed[$name] = @unserialize(@file_get_contents($this->dir.'parsed/'.$name.'.txt'));
			}
			closedir($dh);
		}
	}
	public function __destruct() {
		if (!isset($_GET['delete'])) {
			foreach ($this->parsed as $k => $v) {
				file_put_contents($this->dir.'parsed/'.$k.'.txt',serialize($v));
			}
		}
		DB::setPrefix($this->old_prefix);
		DB::change();
	}
	protected function init() {
		ignore_user_abort(true);
		set_time_limit(0);
		ob_flush();
		DB::resetCache();
		$this->old_prefix = DB::getPrefix();
		DB::setPrefix('');
		$this->start($this->url);
		//DB::connect(1,DB_HOST,DB_USERNAME,DB_PASSWORD,$this->name);
	}
	protected function start($s) {
		echo '<style>body{background:#fff url(/tpls/img/line-numbers.png) top left no-repeat;padding-left:26px;}.s{padding:5px 10px;margin:2px 2px;border-bottom:1px solid #ddd;border-top:1px solid #ddd;background:#f9f9f9}.e{padding:5px;border-top:1px dotted #ccc}.b{width:150px;border:1px solid #dedede;background:buttonface}small{color:#888;font:9px Arial}.d{font:11px Tahoma}.m{font:12px Arial;padding-left:22px}a:hover{text-decoration:none}</style>';
		$this->scroll('<h2>Spidering '.$s.', please hold..</h2>');	
	}
	protected function end() {
		echo '<h1>All done! Enjoy;)</h1>';
		echo '<hr /><button onclick="location.href=\''.HTTP_EXT.'spider\';this.disabled=true">Back</button> <a href="javascript:;" onclick="location.href=\''.HTTP_EXT.'spider-'.get('spider').'/delete\';this.disabled=true">re-parse?</button>';	
	}
	protected function scroll($message = '', $hr = true) {
		ob_start();
		if ($message) {
			if ($hr) {
				echo '<div class="s">'.$message.'</div>';
			} else {
				echo $message.'<br />';	
			}
		}
		if (!$this->cron) echo '<script>window.scroll(0,9999999);</script>';	
		ob_flush();
	}
	protected function refresh($message = '') {
		if ($this->cron) {
			echo $message;
			return;	
		}
		ob_start();
		echo $message;
		if ($this->cron) {
			header ('Location: /spider-'.get('spider').'/cron');
		} else {
			echo '<script>window.scroll(0,9999999);window.location.href=window.location.href.replace(\'delete\',\'\');</script>';
		}
		ob_flush();
		exit;		
	}
	protected function total($m) {
		$this->scroll('<div style="color:#steelblue;font:13px Verdana;padding:10px 0">'.$m.'</div>');	
	}
	protected function msg() {
		if ($this->cron) return false;
		ob_start();
		echo '<br /><div class="e"><i>You may always stop/resume this process anytime you want...<br />But just wait.. and you will get it all soon!</i>';
		echo '<hr /><button class="b" onclick="location.href=\''.HTTP_EXT.'spider\';this.disabled=true">Pause?</button>';
		echo '<br><br><small>Powered by Ajaxel CMS v'.Site::VEESION.'</small></div>';
		echo '<script>window.scroll(0,9999999);</script>';
		ob_flush();
	}
	protected function url($url, $strip = false, $post = false) {
		$h = $this->getCacheText($url);
		if (!$h) {
			$this->scroll('<span class="d">Downloading source from: '.$url.'</span>');
			$h = File::url($url, $post);
			if ($strip) $h = $this->strip($h, $strip);
			$this->saveCacheText($url, $h);
		}
		return $h;
	}
	protected function contents($url, $strip = false) {
		$h = $this->getCacheText($url);
		if (!$h) {
			$this->scroll('<span class="d">Downloading contents from: '.$url.'</span>');
			$h = File::contents($url);
			if ($strip) $h = $this->strip($h, $strip);
			$this->saveCacheText($url, $h);
		}
		return $h;
	}
	protected function stripped($h, $from=false, $to=false) {
		if ($from && ($p = strpos($h,$from))!==-1) {
			$h = substr($h,$p);
		}
		if ($to && ($p = strpos($h,$to))) {
			$h = substr($h,0,$p);
		}
		return $h;
	}
	protected function remFirst($e,$t) {
		$ex = explode($e,$t);
		array_shift($ex);
		return join($e,$ex);
	}
	protected function remLast($e,$t) {
		$ex = explode($e,$t);
		array_pop($ex);
		return join($e,$ex);
	}
	protected function getFirst($e,$t) {
		$ex = explode($e,$t);
		return $ex[0];
	}
	protected function getLast($e,$t) {
		$ex = explode($e,$t);
		return $ex[count($ex)-1];
	}
	protected function getLevel($url) {
		$ret = substr(trim($url),-1);
		if (!is_numeric($ret)) $ret = false;
		return $ret;
	}
	protected function getCityID($city, $state) {
		$ret = DB::row('SELECT id FROM cities'.$this->ext.' WHERE state='.e($state).' AND name LIKE '.e($city),'id');
		return $ret;
	}
	
	public function drop($table) {
		DB::run('DROP TABLE IF EXISTS `'.$table.'`');	
	}
	
	public function table($table) {
		if (!isset($this->parsed['table']) || !is_array($this->parsed['table'])) $this->parsed['table'] = array();
		if ($this->parsed['table'][$table['name']]) return false;
		$sql = 'CREATE TABLE IF NOT EXISTS `'.$table['name'].'` (';
		foreach ($table['cols'] as $col => $d) {
			$sql .= '`'.$col.'` '.$d.', ';
		}
		$sql .= 'PRIMARY KEY (`id`)';
		$sql .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8';
		DB::run($sql);
		$this->parsed['table'][$table['name']] = true;
	}
	
	protected function getCache($n) {
		$n = File::fixFileName(str_replace('/','.',$n).'.txt');
		if (!is_file($this->dir.'cache/'.$n)) return false;
		return unserialize(file_get_contents($this->dir.'cache/'.$n));
			
	}
	protected function saveCache($n,$d) {
		if (!$d) return false;
		$n = File::fixFileName(str_replace('/','.',$n).'.txt');
		return file_put_contents($this->dir.'cache/'.$n,serialize($d));
	}
	protected function getCacheText($n) {
		$n = File::fixFileName(str_replace('/','.',$n).'.txt');
		if (!is_file($this->dir.'cache/'.$n)) return false;
		return file_get_contents($this->dir.'cache/'.$n);
			
	}
	
	protected function delCacheText($n) {
		$n = File::fixFileName(str_replace('/','.',$n).'.txt');
		if (!is_file($this->dir.'cache/'.$n)) return false;
		return unlink($this->dir.'cache/'.$n);	
	}
	protected function del($n) {
		$this->delCacheText($n);	
	}
	
	protected function saveCacheText($n,$d) {
		if (!$this->cache) return false;
		if (!$d) return false;
		$n = File::fixFileName(str_replace('/','.',$n).'.txt');
		return file_put_contents($this->dir.'cache/'.$n,$d);
	}
	protected function fillAttr(&$data, $col, $v) {
		if (!$col || !$v) return false;
		$col = str_replace('&','',$col);
		$col = str_replace('__','_',$col);
		$cols = DB::columns('contents');
		if (!in_array($col, $cols)) {
			DB::run('ALTER TABLE `contents'.$this->ext.'` ADD `'.$col.'` TEXT NOT NULL');
		}
		$data[$col] = $v;
	}
	protected function mergeMatches(&$matches, $_matches) {
		$total = count($matches);
		for ($l=0;$l < $total;$l++) {
			foreach ($_matches[$l] as $i => $v) {
				$matches[$l][] = $v;
			}
		}
	}
}