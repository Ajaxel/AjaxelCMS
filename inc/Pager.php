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
* @file       inc/Pager.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Pager {
	private static 
		$total = 0,
		$limit = 20,
		$url = '',
		$url_limit = '',
		$page_key = '',
		$limit_key = '',
		$buttons = 5,
		$page = 0,
		$pages = array(),
		$total_pages = 1,
		$desc = false
	;
	
	public static function set($params) {
		self::$page_key = URL_KEY_PAGE;
		self::$limit_key = URL_KEY_LIMIT;
		self::$url = '?'.URL::get();
		foreach ($params as $k => $v) self::$$k = $v;
	}
	
	public function __construct($params) {
		return self::get($params);
	}
	
	public static function val($key) {
		return self::$$key;	
	}
	
	private static function fix() {
		if (!self::$total) return;
		elseif (self::$total > 5555554) self::$total = 5555554;
		self::$url = '?'.trim(self::$url,'?');
		self::$url_limit = URL::rq(self::$limit_key,self::$url);
		self::$url = URL::rq(self::$page_key,self::$url);
		if (self::$limit <=0) self::$limit = 1;
		if (self::$page > (self::$total / self::$limit) + 1) {
			self::$page = ceil(self::$total / self::$limit) - 1;
		}
		if (self::$buttons>7) {
			if (self::$page>100000) self::$buttons = 5;
			elseif (self::$page>10000) self::$buttons = 6;
			elseif (self::$page>1000) self::$buttons = 7;
			elseif (self::$page>100) self::$buttons = 8;
		}
	}
	
	
	public static function get($params = array()) {	
		$params['desc'] = false;
		if ($params) self::set($params);
		self::$page = request(self::$page_key,'',0);
		self::fix();
		if (!self::$limit) self::$limit = 20;
		self::$total_pages = intval(ceil(self::$total / self::$limit));
		$pages = $allpages = array();	
		for ($i = 0; $i < self::$total_pages; $i++) {
			if (self::$desc) {
				$allpages[$i + 1] = (self::$total_pages - $i==self::$page);
			} else {
				$allpages[$i + 1] = ($i==self::$page);
			}
		}
		$buttons_2 = self::$buttons / 2;
		$begin_b = self::$page - round($buttons_2);
		if ($begin_b < 0) $begin_b = 0;	
		$end_b = $begin_b + self::$buttons;
		$minus_from_begin = 0;
		if (self::$page >= self::$total_pages - floor($buttons_2)) {
			$minus_from_begin = self::$page - self::$total_pages + round($buttons_2);
		}
		
		foreach ($allpages as $i => $s) {
			if ($i > $begin_b - $minus_from_begin && $i <= $end_b) {
				if (self::$desc) {
					$pages[self::$total_pages-$i] = $s;
				} else {
					$pages[$i] = $s;	
				}
			}
		}
		if (self::$desc) {
			$prev_page = self::$total_pages;
			$next_page = self::$total_pages - 1;
			if ($next_page <=1) $next_page = 0;
			$last_page = 1;
			$first_page = self::$total_pages;
		} else {
			$prev_page = self::$page;
			if ($prev_page<0) $prev_page = false;
			$next_page = self::$page + 1;
			if ($next_page >= self::$total_pages || $next_page<=$buttons_2) $next_page = 0;
			$last_page = self::$total_pages;
			if (self::$page >= self::$total_pages - self::$buttons) $last_page = false;
			$first_page = 1;
			if (self::$page <= $buttons_2) $first_page = false;
		}
		
		
		self::$pages = $pages;
		
		$ret = array(
			'url' 		=> self::$url,
			'url_limit' => self::$url_limit,
			'total'		=> self::$total,
			'limit'		=> self::$limit,
			'page_key' 	=> self::$page_key,
			'limit_key' => self::$limit_key,
			'pages' 	=> self::$pages,
			'page' 		=> self::$page,
			'total_pages'=> self::$total_pages,
			'prev_page'	=> $prev_page,
			'next_page'	=> $next_page,
			'first_page'=> $first_page,
			'last_page'	=> $last_page
		);
		return $ret;
	}
	
	public static function page_options() {
		 return self::selector(self::$page,self::$total_pages,100,15,5,100,10);
	}
	
	public static function limit_options($limits = '') {
		if (!$limits) $limits = array(5,10,20,50,100,200,500,5000);
		elseif (!is_array($limits)) $limits = explode(',',str_replace(' ','',$limits));
		asort($limits);
		$limit_nums = array();
		foreach ($limits as $lim) {
			//if ($lim > self::$total) break;
			$limit_nums[$lim] = $lim;
		}
		return Html::buildOptions(self::$limit, $limit_nums);
	}
	
	public static function selector($pageNow=1,$nbTotalPage=1,$showAll=100,$sliceStart=5,$sliceEnd=5,$percent=25,$range=8) {
		$gotopage = '';
		if ($nbTotalPage < $showAll) {
			$pages = range(1, $nbTotalPage);
		} else {
			$pages = array();
			for ($i = 1; $i <= $sliceStart; $i++) $pages[] = $i;
			for ($i = $nbTotalPage - $sliceEnd; $i <= $nbTotalPage; $i++) $pages[] = $i;
			$i = $sliceStart;
			$x = $nbTotalPage - $sliceEnd;
			$met_boundary = false;
			while($i <= $x) {
				if ($i >= ($pageNow - $range) && $i <= ($pageNow + $range)) {
					$i++;
					$met_boundary = true;
				} else {
					$i = $i + floor($nbTotalPage / $percent);
					if ($i > ($pageNow - $range) && !$met_boundary) $i = $pageNow - $range;
				}
				if ($i > 0 && $i <= $x) {
					$pages[] = $i;
				}
			}
			sort($pages);
			$pages = array_unique($pages);
		}
		foreach($pages as $i) {
			if ($i-1 == $pageNow) {
				$selected = ' selected="selected" style="background-color:#fff"';
			} else {
				$selected = '';
			}
			$gotopage .= '<option'.$selected.' value="'.($i - 1).'">'.$i.'</option>';
		}
		return $gotopage;
	}
}