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
* @file       mod/AdminMain.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminMain extends Admin {
	
	public $mode = '';
	public $chart = array();
	
	public function __construct($print = false) {
		$this->title = 'Welcome to Ajaxel admin panel';
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->content_modules = Site::getModules('content');
		$this->grid_modules = Site::getModules('grid');
	}

	
	
	public function listing() {
		if (!$this->reset) {
			$this->data = Cache::get('AdminMain_data');
			if ($this->data) return;
		}
		$orders_table = DB_PREFIX.'orders2';
		$orders_date = 'ordered';
		$order_price = 'price_total';
		$order_statuses_ok = @join(', ',Conf()->g('order_statuses_ok'));
		$this->data['contents'] = $this->data['grids'] = array();
		$total = $today = $yesterday = array();
		foreach ($this->content_modules as $m => $arr) {
			$this->data['contents']['total'][$m] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'content_'.$m.' WHERE lang=\''.$this->lang.'\'');
			if (!$this->data['contents']['total']) continue;
			$this->data['contents']['today'][$m] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'content_'.$m.' WHERE lang=\''.$this->lang.'\' AND added>'.$this->date['today_time']);
			$this->data['contents']['yesterday'][$m] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'content_'.$m.' WHERE lang=\''.$this->lang.'\' AND added>'.$this->date['yesterday_time'].' AND added<'.$this->date['today_time']);
		}
		if ($this->data['contents']['total']) $total['entries'] = array_sum($this->data['contents']['total']);
		if ($this->data['contents']['today']) $today['entries'] = array_sum($this->data['contents']['today']);
		if ($this->data['contents']['yesterday']) $yesterday['entries'] = array_sum($this->data['contents']['yesterday']);
		if ($this->grid_modules) {
			foreach ($this->grid_modules as $m => $arr) {
				DB::noerror();
				$this->data['grids']['total'][$m] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'grid_'.$m);
				if (!$this->data['grids']['total'][$m]) continue;
				$this->data['grids']['today'][$m] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'grid_'.$m.' WHERE added>'.$this->date['today_time']);
				$this->data['grids']['yesterday'][$m] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'grid_'.$m.' WHERE added>'.$this->date['yesterday_time'].' AND added<'.$this->date['today_time']);
			}
		}
		if (isset($this->data['grids']['total'])) $total['grids'] = array_sum($this->data['grids']['total']);
		if (isset($this->data['grids']['today'])) $today['grids'] = array_sum($this->data['grids']['today']);
		if (isset($this->data['grids']['yesterday'])) $yesterday['grids'] = array_sum($this->data['grids']['yesterday']);
		

		$this->data['today'] = array(
			1	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'views WHERE viewed>'.$this->date['today_time'])),
			2	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'comments WHERE added>'.$this->date['today_time'])),
			3	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'content WHERE added>'.$this->date['today_time'])),
			4	=> intval(@$today['entries']),
			5	=> intval(@$today['grids']),
			6	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users WHERE registered BETWEEN '.$this->date['today_time'].' AND '.$this->time.'')),
			7	=> intval(DB::one('SELECT COUNT(1) FROM '.$orders_table.' WHERE '.$orders_date.' BETWEEN '.$this->date['today_time'].' AND '.$this->time.'')),
			8	=> intval(DB::one('SELECT SUM('.$order_price.') FROM '.$orders_table.' WHERE '.$orders_date.'>'.$this->date['today_time'].' AND `status` IN('.$order_statuses_ok.')')),
			9	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'log WHERE template=\''.$this->tpl.'\' AND added BETWEEN '.$this->date['today_time'].' AND '.$this->time)),
			10	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'im WHERE added BETWEEN '.$this->date['today_time'].' AND '.$this->time)/2),
			11	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'im_sub WHERE sent>'.$this->date['today_time'])),
			50	=> intval(DB::one('SELECT SUM(cnt) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['today_time'])),
			51	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['today_time'])),
			52	=> DB::one('SELECT CONCAT(AVG(clicks),\'/\',SUM(clicks)) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['today_time']),
			53	=> DB::one('SELECT CONCAT(AVG(duration),\'/\',SUM(duration)) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['today_time']),
			90	=> '&ndash;'
		);
		
	
		$this->data['yesterday'] = array (
			1	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'views WHERE viewed>'.$this->date['yesterday_time'].' AND viewed<'.$this->date['today_time'])),
			2	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'comments WHERE added>'.$this->date['yesterday_time'].' AND added<'.$this->date['today_time'])),
			3	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'content WHERE added>'.$this->date['yesterday_time'].' AND added<'.$this->date['today_time'])),
			4	=> intval(@$yesterday['entries']),
			5	=> intval(@$yesterday['grids']),
			6	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users WHERE registered>'.$this->date['yesterday_time'].' AND registered<'.$this->date['today_time'])),
			7	=> intval(DB::one('SELECT COUNT(1) FROM '.$orders_table.' WHERE '.$orders_date.'>'.$this->date['yesterday_time'].' AND '.$orders_date.'<'.$this->date['today_time']).''),
			8	=> intval(DB::one('SELECT SUM('.$order_price.') FROM '.$orders_table.' WHERE '.$orders_date.'>'.$this->date['yesterday_time'].' AND '.$orders_date.'<'.$this->date['today_time'].'  AND `status` IN('.$order_statuses_ok.')')),
			9	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'log WHERE template=\''.$this->tpl.'\' AND added>'.$this->date['yesterday_time'].' AND added<'.$this->date['today_time'])),
			10	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'im WHERE added>'.$this->date['yesterday_time'].' AND added<'.$this->date['today_time'])/2),
			11	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'im_sub WHERE sent>'.$this->date['yesterday_time'].' AND sent<'.$this->date['today_time'])),
			
			50	=> intval(DB::one('SELECT SUM(cnt) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['yesterday_time'].' AND microtime<'.$this->date['today_time'])),
			51	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['yesterday_time'].' AND microtime<'.$this->date['today_time'])),
			52	=> DB::one('SELECT CONCAT(AVG(clicks),\'/\',SUM(clicks)) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['yesterday_time'].' AND microtime<'.$this->date['today_time']),
			53	=> DB::one('SELECT CONCAT(AVG(duration),\'/\',SUM(duration)) FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$this->date['yesterday_time'].' AND microtime<'.$this->date['today_time']),
			90	=> '&ndash;'
		);
		
		$cnt = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'visitor_stats');
		$cnt_ip =  DB::one('SELECT COUNT(DISTINCT(ip)) FROM '.$this->prefix.'visitor_stats');
	
		$this->data['total'] = array(
			1	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'views')),
			2	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'comments')),
			3	=> intval(DB::one('SELECT COUNT(1) FROM '.$this->prefix.'content')),
			4	=> intval($total['entries']),
			5	=> intval($total['grids']),
			6	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users')),
			7	=> intval(DB::one('SELECT COUNT(1) FROM '.$orders_table)),
			8	=> intval(DB::one('SELECT SUM('.$order_price.') FROM '.$orders_table.' WHERE `status` IN('.$order_statuses_ok.')')),
			9	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'log WHERE template=\''.$this->tpl.'\'')),
			10	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'im')/2),
			11	=> intval(DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'im_sub')),
			
			50	=> intval(DB::one('SELECT SUM(cnt) FROM '.$this->prefix.'visitor_stats')),
			51	=> intval($cnt),
			52	=> DB::one('SELECT CONCAT(AVG(clicks),\'/\',SUM(clicks)) FROM '.$this->prefix.'visitor_stats'),
			53	=> DB::one('SELECT CONCAT(AVG(duration),\'/\',SUM(duration)) FROM '.$this->prefix.'visitor_stats'),
			90	=> ($cnt ? (100 - round($cnt_ip / $cnt * 100)):'0').'%'
		);
			
		$this->data['start'] = DB::one('SELECT MIN(microtime) FROM '.$this->prefix.'visitor_stats');
		$this->data['unique'] = number_format(DB::one('SELECT COUNT(DISTINCT(ip)) FROM '.$this->prefix.'visitor_stats'),0,'',' ');
		$this->data['hits'] = number_format(DB::one('SELECT SUM(cnt) FROM '.$this->prefix.'visitor_stats'),0,'',' ');
		
		$this->data['days'] = number_format(($this->time - $this->data['start']) / 86400,0,'',' ');
		if ($this->data['days']==0) $this->data['days']= 1;
		foreach ($this->data['total'] as $i => $total) {
			if (strpos($total,'/')) {
				$ex = explode('/',$total);
				if ($i==53) $this->data['avg'][$i] = Date::secondsToTime($ex[1] / $this->data['days']);
				else $this->data['avg'][$i] = number_format($ex[1] / $this->data['days'],2,',',' ');
			} else {
				$this->data['avg'][$i] = number_format($total / $this->data['days'],2,',',' ');
			}
		}
		
		$this->data['labels'] = array(
			1	=> lang('$Page views'),
			2	=> lang('$Comments'),
			3	=> lang('$Pages added'),
			4	=> lang('$Entries added x %1',count($this->langs)),
			5	=> lang('$Grids added'),
			6	=> lang('$Users registered'),
			7	=> lang('$Orders'),
			8	=> lang('$Sold'),
			9	=> lang('$Log actions'),
			10	=> lang('$IM connects'),
			11	=> lang('$IM messages'),
			
			50	=> lang('$Hits'),
			51	=> lang('$Unique visits'),
			52	=> lang('$Clicks made'),
			53	=> lang('$Visiting time'),
			90	=> lang('$Returning visitor'),
			
			94	=> lang('$Rating'),
			95	=> lang('$Percent'),
		);

		$this->data['names'] = array(
			1	=> 'views',
			2	=> 'comments',
			3	=> 'pages',
			4	=> 'entries',
			5	=> 'grids',
			6	=> 'users',
			7	=> 'orders',
			8	=> 'sold',
			9	=> 'log',
			10	=> 'im',
			11	=> 'im_sub',
			
			50	=> 'hits',
			51	=> 'unique',
			52	=> 'clicks',
			53	=> 'duration'
		);
		
		$arr = array(50,51,52,53);
		foreach ($arr as $a) {
			$this->data['extra'][$a] = ' <a href="javascript:;" onclick="S.A.L.chart(\''.$this->data['names'][$a].'_weekdays\')">w/H</a>';
		}
		
		$this->data['factors'] = array(
			1	=> 10,
			2	=> 0.5,
			3	=> 1,
			4	=> 1,
			5	=> 1,
			6	=> 0.3,
			7	=> 0.5,
			8	=> 0.1,
			9	=> 10,
			10	=> 2,
			11	=> 150,
			
			50	=> 2,
			51	=> 1,
			52	=> 50,
			53	=> 600
		);
		$this->data['today'][94] = $this->data['yesterday'][94] = $this->data['total'][94] = 0;
		foreach ($this->data['factors'] as $i => $div) {
			if (strpos($this->data['today'][$i],'/')) {
				$ex = explode('/',$this->data['today'][$i]);
				$this->data['today'][94] += $ex[1] / $div;
				$ex = explode('/',$this->data['yesterday'][$i]);
				$this->data['yesterday'][94] += $ex[1] / $div;
				$ex = explode('/',$this->data['total'][$i]);
				$this->data['total'][94] += $ex[1] / $div;						
			} else {
				$this->data['today'][94] += $this->data['today'][$i] / $div;
				$this->data['yesterday'][94] += $this->data['yesterday'][$i] / $div;
				$this->data['total'][94] += $this->data['total'][$i] / $div;
			}
		}
		$this->data['today'][94] = round($this->data['today'][94]);
		$this->data['yesterday'][94] = round($this->data['yesterday'][94]);
		$this->data['total'][94] = round($this->data['total'][94]);
		
		
		$ex = explode('/',$this->data['today'][52]);
		$this->data['today'][52] = '<span title="'.lang('$Today average per visitor').'">'.@number_format($ex[0],2,'.',' ').'</span>/<small>'.$ex[1].'</small>';
		$ex = explode('/',$this->data['yesterday'][52]);
		$this->data['yesterday'][52] = '<span title="'.lang('$Today average per visitor').'">'.@number_format($ex[0],2,'.',' ').'</span>/<small>'.$ex[1].'</small>';
		$ex = explode('/',$this->data['total'][52]);
		$this->data['total'][52] = '<span title="'.lang('$Today average per visitor').'">'.@number_format($ex[0],2,'.',' ').'</span>/<small>'.$ex[1].'</small>';
		
		$ex = explode('/',$this->data['today'][53]);
		$this->data['today'][53] = '<span title="'.lang('$Today average per visitor').'">'.Date::secondsToTime($ex[0]).'</span>/<small>'.Date::secondsToTime($ex[1]).'</small>';
		$ex = explode('/',$this->data['yesterday'][53]);
		$this->data['yesterday'][53] = '<span title="'.lang('$Yesterday average per visitor').'">'.Date::secondsToTime($ex[0]).'</span>/<small>'.Date::secondsToTime($ex[1]).'</small>';
		
		$ex = explode('/',$this->data['total'][53]);
		$this->data['total'][53] = '<span title="'.lang('$Average per visitor from total').'">'.Date::secondsToTime($ex[0]).'</span>/<small>'.Date::secondsToTime($ex[1]).'</small>';		
		/*
		$data = $this->data;
		$this->data['today'][94] = array_sum($data['today']);
		$this->data['yesterday'][94] = array_sum($data['yesterday']);
		$this->data['total'][94] = array_sum($data['total']);
		*/
		$this->data['avg'][90] = '&ndash;';
		$this->data['avg'][94] = number_format($this->data['total'][94] / $this->data['days'],2,',',' ');
		$this->data['days'] = round($this->data['days']);

		$this->data['today'][95] = round($this->data['avg'][94]>0?$this->data['today'][94] / $this->data['avg'][94] * 100:0).'%';
		$this->data['yesterday'][95] = round($this->data['avg'][94]>0?$this->data['yesterday'][94] / $this->data['avg'][94] * 100:0).'%';
		$this->data['total'][95] = '&ndash;';
		$this->data['avg'][95] = '100%';
		$this->data['total'][94] = '&ndash;';
		
		unset($this->data['contents'], $this->data['grids']);
		
		Cache::save('AdminMain_data',$this->data);

	}

}