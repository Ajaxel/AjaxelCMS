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
* @file       mod/AdminStats.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminStats extends Admin {
	
	public function __construct() {
		parent::__construct(__CLASS__);
	}
}


class AdminStats_clicks extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Clicks';
		parent::__construct(__CLASS__);
		$this->array['click_types'] = array(
			'date'			=> lang('$Date'),
			'date_user'		=> lang('$Date user'),
			'popular'		=> lang('$Most popular'), // clicks, 1,2,3
			'continuous'	=> lang('$Most continuous'), // duration
			'visited' 		=> lang('$Most visited') // times
		);
		$this->click_type = get('click','','[[:CACHE:]]');
		if (!$this->click_type || !array_key_exists($this->click_type,$this->array['click_types'])) {
			$this->click_type = 'popular';	
		}
		$this->array['time_types'] = array(
			'all' => lang('$All'),
			'today' => lang('$Today'),
			'24h' => '24h',
			'yesterday' => lang('$Yesterday'),
			'month' => lang('$This month'),
			'year' => lang('$This year')
		);
		$this->time_type = get('time','','[[:CACHE:]]');
		/*
		$this->array['andor_types'] = array(
			'AND' => 'AND',
			'OR' => 'OR'
		);
		$this->andor_type = get('andor','','[[:CACHE:]]');
		*/
		$this->limit = 50;
		$this->offset = $this->page * $this->limit;
		DB::run('DELETE FROM '.$this->prefix.'visitor_clicks WHERE visit_id=0 AND added<'.($this->time-86400).'');
	}
	public function listing() {
		$this->button['save'] = false;
		$select = '';
		$group = '';
		$f = ' WHERE TRUE';
		if (get('visit_id')) {
			$f .= ' AND visit_id='.(int)get('visit_id');	
		}
		
		if ($this->date_from_int) {
			$f .= ' AND c.added > '.$this->date_from_int.'';
			$this->time_type = '';				
		}
		if ($this->date_to_int) {
			$f .= ' AND c.added < '.$this->date_to_int.'';
			$this->time_type = '';
		}		
		// nowon($years=0,$months=0,$days=0,$hours=0,$minutes=0) {
		switch ($this->time_type) {
			case 'today':
				// 2012-05-30 01:38:48
				$f .= ' AND clicked > \''.sprintf('%04d-%02d-%02d 00:00:00',date('Y'),date('m'),date('d')).'\'';				
			break;
			case '24h':
				$f .= ' AND added > '.($this->time-86400).'';
			break;
			case 'yesterday':
				$f .= ' AND clicked BETWEEN \''.Date::nowon(0,0,-1,NULL,NULL).'\' AND \''.Date::nowon(0,0,0,NULL,NULL).'\'';
			break;
			case 'month':
				$f .= ' AND MONTH(clicked)='.date('m').' AND YEAR(clicked)='.date('Y').'';
			break;
			case 'year':
				$f .= ' AND YEAR(clicked)='.date('Y');
			break;
		}
		$f .= ' AND visit_id>0';
		$this->data = array();
		
		$find = $this->find('location');
		$select .= $find['select'];
		if ($find['order']) $order .= $find['order'].', ';
		if ($find['group']) $group = $find['group'];
		$f .= $find['where'];
		
		switch ($this->click_type) {
			case 'popular':
			case 'continuous':
			case 'visited':
			
				
				if (!$group) $group = ' GROUP BY c.md5_location';
				switch ($this->click_type) {
					case 'popular':
						$order .= '(MIN(click) * MAX(click) - COUNT(1)) ASC, sum_duration DESC';
					//	$order = 'min_click ASC, sum_duration DESC';
					break;
					case 'continuous':
						$order .= 'avg_duration DESC';
					break;
					case 'visited':
						$order .= 'cnt DESC, added DESC';
					break;
				}
					
					
				//	d($f);
				//	$f .= ' AND (location LIKE \'%'.$this->find.'%\')';
				$sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(1) AS cnt, MIN(click) AS min_click, AVG(click) AS avg_click, location, md5_location, MAX(added) AS added, SUM(duration) AS sum_duration, AVG(duration) AS avg_duration'.$select.' FROM '.$this->prefix.'visitor_clicks c'.$f.$group.' ORDER BY '.$order;
				// diameeter=16&laius=205&korgus=60 diameeter=15&laius=195&korgus=65
				
				$qry = DB::qry($sql,$this->offset,$this->limit);
				$this->total = DB::rows();
				while ($rs = DB::fetch($qry)) {
					$l = URL::ht(strform('?'.$rs['location']));
					$r = array(
						0	=> '<a href="'.$l.'" target="_blank">'.Parser::stripLink($l).'</a>',
						1	=> $rs['min_click'],
						2	=> 'md5'.$rs['md5_location'],
						3	=> $rs['cnt'],
						4	=> Date::secondsToTime($rs['sum_duration'],'short_color'),
						5	=> date('H:i d.m.Y',$rs['added']),
						6	=> round($rs['avg_click']),
						7	=> Date::secondsToTime(round($rs['avg_duration']),'short_color'),
					);
					array_push($this->data, $r);
				}
			break;
			default:
				switch ($this->click_type) {
					case 'date_user':
						$group = ' GROUP BY user';
					//	$f .= ' AND s.userid>0';
						$select .= 'c.id, c.added, c.location, c.md5_location, COUNT(1) AS click, SUM(c.duration) AS duration, s.userid';
					break;
					default:
						$group = '';
						$select .= 'c.id, c.added, c.location, c.md5_location, c.click, c.duration, s.userid, s.ip';
					break;	
				}
				if ($this->find) {
				//	$f .= ' AND ((SELECT login FROM '.DB_PREFIX.'users u WHERE u.id=s.userid) LIKE \''.$this->find.'\')';
				}
				$sql = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT '.$select.', (CASE WHEN visit_id>0 THEN (CASE WHEN s.userid>0 THEN (SELECT login FROM '.DB_PREFIX.'users u WHERE u.id=s.userid) ELSE s.ip END) ELSE \'\' END) AS user FROM '.$this->prefix.'visitor_clicks c LEFT JOIN '.$this->prefix.'visitor_stats s ON s.id=c.visit_id'.$f.$group.' ORDER BY c.id DESC';
				$qry = DB::qry($sql,$this->offset,$this->limit);
				$this->total = DB::rows();
				while ($rs = DB::fetch($qry)) {
					$l = URL::ht(strform('?'.$rs['location']));
					$r = array(
						0	=> date('H:i d.m.Y',$rs['added']),
						1	=> '<a href="'.$l.'" target="_blank">'.Parser::stripLink($l).'</a>',
						2	=> array($rs['userid'], $rs['user']),
						3	=> $rs['click'],
						4	=> Date::secondsToTime($rs['duration'],'short_color'),
						5	=> 'md5'.$rs['md5_location']
					);
					array_push($this->data, $r);
				}
			break;
		}
		DB::free($qry);
		$this->nav();
		$this->json_data = json($this->data);
	}
	public function window() {
		
		$sql = 'SELECT click, added FROM '.$this->prefix.'visitor_clicks WHERE md5_location='.e($this->id);
		$qry = DB::qry($sql,$this->offset, $this->limit);
		while ($rs = DB::fetch($qry)) {
			
		}
		DB::free($qry);
		
		$this->win('stats_clicks');
		
	}
}

class AdminStats_searches extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Searches';
		parent::__construct(__CLASS__);
		$this->limit = 50;
		$this->offset = $this->page * $this->limit;
		
		$this->array['search_keys'] = array(
			'all'	=> lang('$All')
		);
		$this->array['s_keys'] = Conf()->g('searches');
		if ($this->array['s_keys']) {
			foreach ($this->array['s_keys'] as $k => $x) {
				$this->array['search_keys'][$k] = $x[2];
			}
		}
		
		$this->search_key = get('skey','','[[:CACHE:]]');
		$this->search_keys = get('skeys','','[[:CACHE:]]');
		if (!is_array($this->search_keys)) $this->search_keys = array();
		$this->button['save'] = false;
	}
	protected function listing() {
		$data = array();

		$this->filter = '';
		
		if ($this->date_from_int) {
			$this->filter .= ' AND h.added > '.$this->date_from_int.'';				
		}
		if ($this->date_to_int) {
			$this->filter .= ' AND h.added < '.$this->date_to_int.'';
		}

		if ($this->search_keys):
		
		
		
		/*
	
			
			$sql = 'CREATE TABLE IF NOT EXISTS '.$this->prefix.'visitor_searches_temp (
				k VARCHAR(200) NOT NULL DEFAULT \'0\',
				v VARCHAR(255) NOT NULL DEFAULT \'\'
			)';
			DB::run($sql);
			DB::run('TRUNCATE TABLE '.$this->prefix.'visitor_searches_temp');
			
			
			$qry = DB::qry('SELECT GROUP_CONCAT(key_index ORDER BY key_index) AS k, GROUP_CONCAT(value ORDER BY key_index) AS v, COUNT(1) AS cnt FROM '.$this->prefix.'visitor_searches WHERE TRUE'.$this->filter.' GROUP BY visit_id, click_id HAVING cnt='.count($this->search_keys).' ORDER BY v',0,0);
			while ($rs = DB::fetch($qry)) {
				DB::run('INSERT INTO '.$this->prefix.'visitor_searches_temp (k,v) VALUES ('.e($rs['k']).', '.e($rs['v']).')');
			}
			DB::free($qry);

			$sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(1) AS cnt, k, v FROM '.$this->prefix.'visitor_searches_temp GROUP BY k ORDER BY v';

			$qry = DB::qry($sql,$this->offset,$this->limit);
			while ($rs = DB::fetch($qry)) {
				//$rs['cnt2'] = DB::one('SELECT COUNT(1) FROM '.$this->prefix.'visitor_searches WHERE 
				
				$data[] = $rs;
			}
			$this->total = DB::rows();
			
			d($data);
		*/
			$j = array();
			foreach ($this->search_keys as $i) {
				$j[] = $this->array['search_keys'][$i];
			}
			$this->search_long = join(' / ',$j);
		
			
			$sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(1) AS cnt, v, k FROM (SELECT GROUP_CONCAT(`value` ORDER BY key_index SEPARATOR \' / \') AS v, GROUP_CONCAT(`key_index` ORDER BY key_index SEPARATOR \',\') AS k, COUNT(1) AS cnt FROM '.$this->prefix.'visitor_searches h WHERE key_index IN ('.join(', ',array_numeric($this->search_keys)).')'.$this->filter.' GROUP BY visit_id, click_id ORDER BY v) AS tbl WHERE cnt='.count($this->search_keys).' GROUP BY v ORDER BY cnt DESC, v';

			$qry = DB::qry($sql,$this->offset,$this->limit);
			while ($rs = DB::fetch($qry)) {				
				$data[] = array(
					0	=> $rs['v'],
					1	=> $rs['cnt'],
					2	=> $rs['k'],
				);
			}
			$this->total = DB::rows();

		else:	
			$this->group = ' h.key_index, h.value';
			
			$this->order = 'cnt DESC';
			if (isset($this->find) && $this->find) {
				$this->filter .= ' AND (`value` LIKE \'%'.$this->find.'%\')';	
			}
			if ($this->search_key && $this->array['s_keys'] && array_key_exists($this->search_key, $this->array['s_keys'])) {
				$this->group = 'h.value';
				$this->filter = ' AND key_index='.$this->search_key;
			}
			if ($this->search_key && $this->array['s_groups'] && array_key_exists($this->search_key, $this->array['s_groups'])) {
				$ex = explode('|',$this->search_key);
				if ($ex) {
					$this->group = 'h.key_index, h.value';
					$this->filter .= ' AND key_index IN ('.join(', ',$ex).')';
				}
			}
	
			$keys = Conf()->g('searches');
			$sql = 'SELECT SQL_CALC_FOUND_ROWS h.key_index, h.value, COUNT(1) AS cnt, MAX(h.added) AS added'.(USE_CLICKS?', c.location':', \'\' AS location').' FROM '.$this->prefix.'visitor_searches h LEFT JOIN '.$this->prefix.'visitor_stats s ON s.id=h.visit_id'.(USE_CLICKS?' LEFT JOIN '.$this->prefix.'visitor_clicks c ON c.id=h.click_id':'').' WHERE TRUE'.$this->filter.($this->group?' GROUP BY '.$this->group:'').' ORDER BY '.$this->order;
			$qry = DB::qry($sql,$this->offset,$this->limit);
			while ($rs = DB::fetch($qry)) {
				if ($rs['location']) $l = URL::ht(strform('?'.$rs['location'])); else $l = '';
				$data[] = array(
					0	=> $keys[$rs['key_index']][2],
					1	=> $keys[$rs['key_index']][0],
					2	=> html($rs['value']),
					4	=> date('H:i d.m.Y',$rs['added']),
					5 	=> $rs['cnt'],
					6	=> $l,
					7	=> $rs['key_index']
				);
			}
			$this->total = DB::rows();
		endif;
		$this->nav();
		$this->json_data = json($data);
	}
}



class AdminStats_summary extends Admin {
	public function __construct() {
		$this->title = 'Visitor statistics';
		parent::__construct(__CLASS__);
	}
	public function listing() {
		$name = 'Stats chart ['.$this->template.']';
		$name2 = 'Stats chart2 ['.$this->template.']';
		$start = $end = '';
		if ($this->reset || ($this->date_from && $this->date_to && $this->date_to > $this->date_from) || !is_file($this->ftp_dir_files.'temp/'.$name.'.png') || !is_file($this->ftp_dir_files.'temp/'.$name2.'.png')) {
			
			if ($this->date_from_int && $this->date_to_int) {
				$from = $this->date_from_int;
				$to = $this->date_to_int;
				$diff = ($this->date_to_int - $this->date_from_int) / 86400;
				if ($diff > 60) {
					$group = '%M %Y';
					$x = '%m.%Y';
				}
				elseif ($diff > 2) {
					$group = '%D %M %Y';
					$x = '%d.%m';
				}
				elseif ($diff > 1) {
					$group = '%H %D %M %Y';
					$x = '%H %d.%m';	
				}
				else {
					$group = '%H:%i %D %M %Y';
					$x = '%H:%i %d.%m';	
				}
			} else {
				$group = '%D %M %Y';
				$x = '%d.%m';
				$from = $this->time - 5184000; // 2m
				$to = $this->time;
			}
			
			$sql = 'SELECT DATE_FORMAT(cameon, \''.$x.'\') as x, DATE_FORMAT(cameon, \''.$group.'\') AS d, SUM(cnt) AS y1, COUNT(1) AS y2, SUM(duration) AS y3, SUM(clicks) AS y4 FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$from.' AND microtime<'.$to.' GROUP BY d ORDER BY cameon';
			$qry = DB::qry($sql,0,0);
			$data = $data2 = array();
			$i = 0;
			while ($rs = DB::fetch($qry)) {
				if (!$i) $start = $rs['d'];
				$data['hits'][$rs['x']] = $rs['y1'];
				$data['unique'][$rs['x']] = $rs['y2'];
				$data2['duration'][$rs['x']] = round($rs['y3']/60);
				$data2['clicks'][$rs['x']] = $rs['y4'];
				$end = $rs['d'];
				$i++;
			}
			$names = $names2 = array();
			$names['hits'] = 'Hits';
			$names['unique'] = 'Unique';
			$names2['duration'] = 'Duration (minutes)';
			$names2['clicks'] = 'Clicks';
			$this->chart['stats_chart'] = $this->chart($data, $names, $name, 700, 350, lang('$Hits and unique visits statistics chart (%1 of %2 [%3])',$start.' - '.$end,$_SERVER['HTTP_HOST'],$this->template), 'Hits and unique visits', 'Days', 0.1, 3, 7);
			$this->chart['stats_chart2'] = $this->chart($data2, $names2, $name2, 700, 350, lang('$Clicks and duration statistics chart (%1 of %2 [%3])',$start.' - '.$end,$_SERVER['HTTP_HOST'],$this->template), 'Clicks and duration', 'Days', 0.1, 3, 1);
		}
		else {
			$this->chart['stats_chart'] = $this->http_dir_files.'temp/'.$name.'.png';
			$this->chart['stats_chart2'] = $this->http_dir_files.'temp/'.$name2.'.png';
		}
	}
}


class AdminStats_when extends Admin {
	public function __construct() {
		$this->title = 'Statistics: When';
		parent::__construct(__CLASS__);
		$this->array['date_types'] = array(
			'date' 		=> lang('$Days'),
			'hour' 		=> lang('$Hours'),
			'weekday'	=> lang('$Weekdays'),
			'week' 		=> lang('$Weeks'),
			'month' 	=> lang('$Months &amp; Year'),
			'monthall' 	=> lang('Months'),
			'year' 		=> lang('$Years'),
			'max_day' 	=> lang('$Max day')
		);
		$this->array['hit_types'] = array(
			'hits'		=> lang('$Hits'),
			'unique'	=> lang('$Unique IPs'),
			'clicks'	=> lang('$Clicks'),
			'duration'	=> lang('$Duration'),
		);
		
		/*
		$this->array['countries'] = DB::getAll('SELECT a.country, (CASE WHEN a.country=\'un\' THEN '.e(lang('$Unknown')).' ELSE (SELECT b.name FROM '.DB_PREFIX.'countries b WHERE b.code=a.country) END) AS name FROM '.$this->prefix.'visitor_stats a GROUP BY a.country HAVING name!=\'\' ORDER BY a.country','country|name');
		if ($this->country && !$this->all['country'] && $this->all['country']!='un') {
			$this->array['cities'] = DB::getAll('SELECT DISTINCT(city) FROM '.$this->prefix.'visitor_stats WHERE country='.e($this->country).' ORDER BY city','city|city');
		}
		*/
		
		
		$this->date_type = get('date','','[[:CACHE:]]');
		$this->hit_type = get('hit','','[[:CACHE:]]');
		
		if (!$this->date_type) $this->date_type = 'date';
		if (!array_key_exists($this->date_type, $this->array['date_types'])) {
			$this->date_type = 'date';	
		}
		if (!$this->hit_type) $this->hit_type = 'hits';
		if (!array_key_exists($this->hit_type, $this->array['hit_types'])) {
			$this->hit_type = 'hits';	
		}
		$this->url_full = $this->url_full.URL::build('date','hit');
	}
	private function sql() {
		$where = '\'\'';
		$group_name = '';
		switch ($this->date_type) {
			case 'hour':
				$group = 'HOUR(cameon)';
				$order = 'HOUR(cameon)';
				$limit = 24;
			break;
			case 'week':
				$group = 'DATE_FORMAT(cameon, \'%U %Y\')';
				$order = 'YEAR(cameon) DESC, WEEK(cameon) DESC';
				$limit = 26;
			break;
			case 'weekday':
				$group = 'DATE_FORMAT(cameon, \'%W\')';
				$order = 'WEEKDAY(cameon)';
				$limit = 7;
			break;
			case 'month':
				$group = 'DATE_FORMAT(cameon, \'%M %Y\')';
				$order = 'YEAR(cameon) DESC, MONTH(cameon) DESC';
				$limit = 28;
			break;
			case 'monthall':
				$group = 'DATE_FORMAT(cameon, \'%M\')';
				$order = 'MONTH(cameon) DESC';
				$limit = 12;
			break;
			case 'year':
				$group = 'YEAR(cameon)';
				$order = 'YEAR(cameon) DESC';
				$limit = 10;
			break;
			case 'max_day':
				$group_name = 'DATE_FORMAT(cameon, \'%D %M %Y\')';
				$group = 'DATE(cameon)';
				switch ($this->hit_type) {
					case 'unique':
						$order = 'unique_hits DESC';
					break;
					case 'clicks':
						$order = 'clicks DESC';
					break;
					case 'duration':
						$order =  'dur DESC';
					break;
					default:
						$order = 'hits DESC';
					break;
				}
				$limit = 31;
			break;
			default:
				$group_name = 'DATE_FORMAT(cameon, \'%D %M %Y\')';
				$group = 'DATE(cameon)';
				$order = 'id DESC';
				$limit = 31;
				$where = 'CONCAT(\' WHERE YEAR(cameon)=\',YEAR(cameon),\' AND MONTH(cameon)=\',MONTH(cameon))';
			break;
		}
		if (!$group_name) $group_name = $group;
		$this->group_name = $group_name;
		$this->group = $group;
		$this->limit = $limit;
		$this->order = $order;
		$this->where = $where;
		$this->filter = '';
		if ($this->country && !$this->all['country']) {
			$this->filter = ' AND country='.e($this->country);	
			if ($this->city && !$this->all['city']) {
				$this->filter = ' AND city='.e($this->city);	
			}
		}
		$this->offset = $this->page * $this->limit;
	}
	
	
	
	public function listing() {
		$this->sql();
		$this->offset = intval($this->limit * $this->page);
		
		$sql = 'SELECT '.$this->group_name.' as name, '.$this->where.' AS wh, SUM(cnt) as hits, COUNT(1) AS unique_hits, AVG(duration) AS dur, AVG(clicks) AS avg_clicks, SUM(clicks) AS sum_clicks FROM '.$this->prefix.'visitor_stats WHERE TRUE'.$this->filter.' GROUP BY '.$this->group.' ORDER BY '.$this->order;

		// , MIN(duration) AS min_dur, MAX(duration) AS max_dur
		
		if ($this->date_type=='date' || $this->date_type=='max_day') {
			$start = intval(DB::one('SELECT microtime FROM '.$this->prefix.'visitor_stats ORDER BY id'));
			$qry = DB::qry($sql,$this->offset,$this->limit);
			$this->total = ($start ? floor((time() - $start) / 86400) : 0);
			$this->nav();
		} else {
			$qry = DB::qry($sql,0,$this->limit);
		}
		$data = array();
		$for_chart = $hits_total = array();

		while ($rs = DB::fetch($qry)) {
			
			switch ($this->hit_type) {
				case 'unique':
					$for_chart[$rs['name']] = $r['unique_hits'];
					$graph = $rs['unique_hits'];
				break;
				case 'duration':
					$for_chart[$rs['name']] = round($r['dur']/3600);
					$graph = $rs['dur'];
				break;
				case 'clicks':
					$for_chart[$rs['name']] = round($r['click']/3600);
					$graph = $rs['clicks'];
				break;
				default:
					if (isset($r)) $for_chart[$rs['name']] = $r['hits'];
					$graph = $rs['hits'];
				break;
			}
			if (!isset($hits_total[$rs['wh']])) {
				$hits_total[$rs['wh']] = self::totalCount($this->prefix,$this->hit_type!='unique',($this->hit_type=='duration'),($this->hit_type=='clicks'),$rs['wh']);
			}
			$rs['min_dur'] = self::duration($rs['min_dur']);
			$rs['max_dur'] = self::duration($rs['max_dur']);
			$rs['clicks'] = round($rs['avg_clicks']).'<small> /'.$rs['sum_clicks'].'</small>';
			$rs['dur'] = str_replace('00:','<span style="color:#888">00:</span>',self::duration($rs['dur']));
			$rs['percent'] = self::percentValue($graph,$hits_total[$rs['wh']]);
			$rs['chart'] = self::graphValue($graph,$hits_total[$rs['wh']],true);
			unset($rs['wh'], $rs['sum_clicks'], $rs['avg_clicks']);
			array_push($data, $rs);
		}
		
		DB::free($qry);
		$this->json_data = json($data);
	}
	
	private static function startDate() {
		return DB::row('SELECT cameon FROM '.$this->prefix.'visitor_stats ORDER BY cameon','cameon');
	}
	private static function todayCount($sum=true,$date=NULL) {
		if ($sum) $s = 'SUM'; else $s = 'COUNT';
		if (!$date) $date = date("Y-m-d");
		$sql = 'SELECT '.$s.'(cnt) AS cnt'.($sum?', SUM(duration) AS dur, MIN(duration) AS min_dur, MAX(duration) AS max_dur':'').' FROM '.$this->prefix.'visitor_stats WHERE cameon LIKE \''.$date.'%\'';
		return DB::row($sql);
	}
	public static function totalCount($prefix,$sum=0,$dur=0,$clicks=0,$where='') {
		if ($sum) $s = 'SUM'; else $s = 'COUNT';
		if (Conf()->g2('AdminStats_when::TotalCount',$where)) return Conf()->g2('AdminStats_when::TotalCount',$where);
		$sql = 'SELECT '.($dur?'SUM(duration)':($clicks?'SUM(clicks)':$s.'(cnt)')).' AS c FROM '.$prefix.'visitor_stats'.$where;
		$ret = DB::row($sql,'c');
		Conf()->s2('AdminStats_when::TotalCount',$where,$ret);
		return $ret;
	}
	private static function totalTime($prefix) {
		$dur = DB::row('SELECT SUM(duration) AS dur FROM '.$prefix.'visitor_stats','dur');
		return $dur>0 ? Date::secondsToTime($dur,'full') : '&nbsp;';
	}
	private static function totalClicks($prefix) {
		return DB::row('SELECT SUM(clicks) AS clicks FROM '.$prefix.'visitor_stats','clicks');
	}
	public static function periodStart($ptype) {
		$r = '';
		switch ($ptype) {
			case 'day': 
				$r = sprintf("%04d-%02d-%02d %02d:%02d:%02d",strftime("%Y"),strftime("%m"),strftime("%d"),0,0,0); 
			break;
			case 'week': 
				$offset = date('w',mktime(0,0,0,strftime("%m"),strftime("%d"),strftime("%Y")));
				if ($offset==7 || $offset==0) {
					$r = sprintf("%04d-%02d-%02d %02d:%02d:%02d",strftime("%Y"),strftime("%m"),strftime("%d"),0,0,0);
				} else {
					//$r = strftime("%Y-%m-%d",DateSub('d',$offset,time()))." 00:00:00";
				}
			break;
			case 'month':
				$r = sprintf("%04d-%02d-%02d %02d:%02d:%02d",strftime("%Y"),strftime("%m"),1,0,0,0);
			break;
			case 'year': 
				$r = sprintf("%04d-%02d-%02d %02d:%02d:%02d",strftime("%Y"),1,1,0,0,0);
			break;
		}
		return $r;
	}	
	public static function periodCount($prefix, $pstart,$sum=true,$where = '') {
		$ret = DB::row('SELECT '.($sum?'SUM':'COUNT').'(cnt) AS c FROM '.$prefix.'visitor_stats WHERE TRUE'.($pstart?' AND cameon>=\''.$pstart.'\'':'').$where,'c');
		return $ret;
	}
	private static function dayAverage($startdate,$totalcount) {
		$today = time() / 86400;
		$base = strtotime($startdate) / 86400;
		$daydiff = floor($today - $base) + 1;
		$dayaverage = $totalcount / $daydiff;
		if ($dayaverage < 1) $dayaverage = round($dayaverage,2);
		elseif ($dayaverage < 10) $dayaverage = round($dayaverage,1);
		else $dayaverage = round($dayaverage,0);
		return $dayaverage;
	}	
	private static function topCount($prefix,$sum=true) {
		if ($sum) $s = 'SUM'; else $s = 'COUNT';
		$topvisitors['topdate'] = date('Y-m-d');
		$topvisitors['tophits'] = 0;
		$sql = 'SELECT DATE_FORMAT(cameon,\'%Y-%m-%d\') AS date, '.$s.'(cnt) AS hits FROM '.$prefix.'visitor_stats GROUP BY DATE_FORMAT(cameon,\'%Y-%m-%d\') ORDER BY hits DESC';
		$rs = DB::row($sql);
		if ($rs['hits'] >= $topvisitors['tophits']) {
			$topvisitors['topdate'] = $rs['date'];
			$topvisitors['tophits'] = $rs['hits'];
		}
		return $topvisitors;
	}
	
	private static function topClick($prefix) {
		$sql = 'SELECT DATE_FORMAT(cameon,\'%Y-%m-%d\') AS date, SUM(clicks) AS click FROM '.$prefix.'visitor_stats GROUP BY DATE_FORMAT(cameon,\'%Y-%m-%d\') ORDER BY clicks DESC';
		return DB::row($sql);
	}
	private static function percentValue($hits,$hitstotal) {
		if (!$hitstotal) return 0.00;
		return number_format(($hits / $hitstotal) * 100, 2).'%';
	}
	public static function graphValue($hits,$t,$retName = false){
		if (!$t) return '';
		$bars = array('blue','pink','yellow','darkgreen','purple','gold','green','brown','orange','aqua','grey','red');
		shuffle($bars);
		$ret = '';
		$p = ceil(($hits / $t) * 100);
		if ($t>0 && $p>0) {
			$l = round($p * 3);
			if ($retName) return array($bars[0],$p);
			$ret = '<img src="/tpls/img/sys/graphbar/'.$bars[0].'.gif" height="10" width="'.$l.'">';
		}
		return $ret;
	}	
	public static function browser($id) {
		if (!$id) return '';
		require_once (FTP_DIR_ROOT.'inc/Stats.php');
		$ret = Stats::getDB(2,'',$id);
		return ucfirst(@$ret[0]['k']);
	}	
	public static function OS($id) {
		if (!$id) return '';
		require_once (FTP_DIR_ROOT.'inc/Stats.php');
		$ret = Stats::getDB(1,'',$id);
		return ucfirst(@$ret[0]['k']);
	}	
	private static function uniqueIPs($prefix) {
		if (isset($_SESSION['V_UniqueIPs']) && !isset($_GET['reset'])) return $_SESSION['V_UniqueIPs'];
		$cnt = DB::row('SELECT COUNT(DISTINCT(ip)) AS cnt FROM '.$prefix.'visitor_stats','cnt');
		$_SESSION['V_UniqueIPs'] = $cnt;
		return $cnt;
	}	
	private static function duration($t) {
		if ($t<=0) return '&nbsp;';
		return Date::secondsToTime($t,'short');
	}
}


class AdminStats_who extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Who';
		parent::__construct(__CLASS__);
		$this->array['sort'] = array(
			'date' 			=> lang('$Visit date'),
			'domain_top' 	=> lang('$Top referer'),
			'hits' 			=> lang('$Hits'),
			'duration' 		=> lang('$Duration'),
			'clicks'		=> lang('$Clicks'),
			'referer' 		=> lang('$Referer'),
			'browser' 		=> lang('$Browser'),
			'os' 			=> lang('$Operation system'),
			'country' 		=> lang('$Country')
		);
		$this->array['by'] = array('DESC' => 'DESC','ASC' => 'ASC');
		
		if (!array_key_exists($this->sort, $this->array['sort'])) {
			$this->sort = 'date';	
		}
		if (!array_key_exists($this->by, $this->array['by'])) {
			$this->by = 'DESC';	
		}
	}
	
	public function json() {
		switch ($this->action) {
			case 'delete':
				if (!$this->id) break;
				$this->allow('stats','delete');
				$rs = DB::row('SELECT ua, ip FROM '.$this->prefix.'visitor_stats WHERE id='.$this->id);
				if ($rs['ua']) {
					$total = DB::row('SELECT COUNT(1) AS cnt FROM '.$this->prefix.'visitor_stats WHERE ua='.(int)$rs['ua']);
					if ($total<=1) DB::run('DELETE FROM '.DB_PREFIX.'db WHERE t=6 AND k='.$rs['ua']);
				}
				DB::run('DELETE FROM '.$this->prefix.'visitor_stats WHERE id='.$this->id);
			break;
			case 'block_toggle':
				$ip = post('ip');
				$this->allow('stats','block');
				if (DB::row('SELECT 1 FROM '.DB_PREFIX.'ipblocker WHERE ip='.e($ip))) {
					DB::run('DELETE FROM '.DB_PREFIX.'ipblocker WHERE ip='.e($ip));
				} else {
					$ex = explode('&',@$_SERVER['HTTP_REFERER']);
					DB::run('INSERT INTO '.DB_PREFIX.'ipblocker (ip, blocked, reason) VALUES ('.e($ip).', '.$this->time.', \'['.@$ex[1].@$ex[2].']\')');
				}
			break;
		}
	}
	
	private function sql() {
		$this->select = array('s.id, s.cameon AS date, s.userid, s.ua, (CASE WHEN s.userid>0 THEN (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=s.userid) ELSE \'\' END) AS user, s.duration AS dur, s.width, s.height, s.referer, s.cnt, s.clicks, s.ip, s.browser, s.os, s.country AS code, s.city, s.device');
		switch ($this->sort) {
			case 'date':
				$order = 's.cameon '.$this->by;
			break;
			case 'duration':
				$order = 's.duration '.$this->by;
			break;
			case 'clicks':
				$order = 's.clicks '.$this->by.', s.cameon DESC';
			break;
			case 'referer':
				$this->filter .= ' AND s.referer!=\'\'';
				$order = 's.referer '.$this->by;
			break;
			/*
			case 'referer_top':
				$this->select[] = 'SUBSTRING_INDEX(REPLACE(s.referer,\'www.\',\'\'), \'/\', 4) AS domain';
				$l = strlen(HTTP_BASE);
				$this->filter .= ' AND s.referer!=\'\' AND SUBSTRING(s.referer,1,'.$l.')!=\''.HTTP_BASE.'\' AND SUBSTRING(s.referer,1,'.($l+4).')!=\''.str_replace('http://','http://www.',HTTP_BASE).'\' GROUP BY domain';
				$order = 'cnt '.$this->by;
			break;
			*/
			case 'domain_top':
				$this->select[] = 'SUM(s.cnt) AS cnt, SUM(clicks) AS clicks, SUM(duration) AS dur';
				$this->select[] = 'SUBSTRING_INDEX(REPLACE(s.referer,\'www.\',\'\'), \'/\', 3) AS domain';
				$this->filter .= ' GROUP BY domain';
				$order = 'cnt '.$this->by;
			break;
			case 'ip':
				$order = 's.ip '.$this->by;
			break;
			case 'browser':
				$order = 'br '.$this->by;
			break;
			case 'os':
				$order = 's.os '.$this->by;
			break;
			case 'country':
				$order = 's.country, s.city '.$this->by;
			break;
			case 'hits':
				$order = 's.cnt '.$this->by;
			break;
			default:
				$order = 's.cameon '.$this->by;
			break;
		}	
		$this->order = $order;
		
		if ($this->sort!='cnt' && $this->sort!='referer_top') {
			$this->order .= ',s.cnt DESC';
		}
		if ($this->sort=='referer_top') {
			$this->select[] = 'SUM(s.cnt) AS cnt';
		}
		/*
		if (isset($_REQUEST['hour'])) {
			$t = str_replace('+',' ',$_REQUEST['hour']);
			$adSql = 'WHERE HOUR(s.cameon) = \''.e($t).'\'';
			$msg = lang('$Filtering by hour: %1',0,0,$t);
			$u = '&hour='.$_REQUEST['hour'];
		}
		elseif ($_REQUEST['weekday']) {
			$t = $_REQUEST['weekday'];
			$adSql = 'WHERE DATE_FORMAT(s.cameon, \'%W\') = '.e($t);
			$msg = lang('$Filtering by weekday: %1',0,0,$t);
			$u = '&weekday='.$_REQUEST['weekday'];
		}
		elseif ($_REQUEST['date']) {
			$t = str_replace('+',' ',$_REQUEST['date']);
			$adSql = 'WHERE DATE_FORMAT(s.cameon, \'%D %M %Y\') = '.e($t);
			$msg = lang('$Filtering by date: %1',0,0,$t);
			$u = '&date='.$_REQUEST['date'];
		}
		elseif ($_REQUEST['week']) {
			$t = str_replace('+',' ',$_REQUEST['week']);
			$adSql = 'WHERE DATE_FORMAT(s.cameon, \'%U %Y\') = '.e($t);
			$msg = lang('$Filtering by week: %1',0,0,$t);
			$u = '&week='.$_REQUEST['week'];
		}
		elseif ($_REQUEST['month']) {
			$t = str_replace('+',' ',$_REQUEST['month']);
			$adSql = 'WHERE DATE_FORMAT(s.cameon, \'%M %Y\') = '.e($t);
			$msg = lang('$Filtering by month &amp; year: %1',0,0,$t);
			$u = '&month='.$_REQUEST['month'];
		}
		elseif ($_REQUEST['monthall']) {
			$t = $_REQUEST['monthall'];
			$adSql = 'WHERE DATE_FORMAT(cameon, \'%M\') = '.e($t);
			$msg = lang('$Filtering by month: %1',0,0,$t);
			$u = '&monthall='.$_REQUEST['monthall'];
		}
		elseif ($_REQUEST['year']) {
			$t = $_REQUEST['year'];
			$adSql = 'WHERE YEAR(cameon) = \''.e($t).'\'';
			$msg = lang('$Filtering by year: %1',0,0,$t);
			$u = '&year='.$_REQUEST['year'];
		}
		*/
	}
	
	public function listing() {
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS '.join(', ',$this->select).', (SELECT 1 FROM '.DB_PREFIX.'ipblocker b WHERE b.ip=s.ip) AS blocked, CONCAT(browser,\' \',b_version) as br, (SELECT r.userid FROM '.$this->prefix.'visitor_referals r WHERE r.visit_id=s.id LIMIT 1) AS refered FROM '.$this->prefix.'visitor_stats s WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		// LEFT JOIN '.DB_PREFIX.'countries c ON c.code=s.country 
		
		/*
		if ($this->sort=='referer_top') {
			$this->total = DB::row('SELECT COUNT(DISTINCT(referer)) AS cnt FROM '.$this->prefix.'visitor_stats','cnt');
		} else {
			$this->total = DB::row('SELECT COUNT(1) AS cnt FROM '.$this->prefix.'visitor_stats s WHERE TRUE'.$this->filter,'cnt');
		}
		*/
		$user_agents = DB::getAll('SELECT k, v FROM '.DB_PREFIX.'db WHERE t=6','k|v');
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		$data = array();
		while ($rs = DB::fetch($qry)) {
			if ($rs['referer'] && substr($rs['referer'],0,8)!='http:///') {
				$url_parts = @parse_url($rs['referer']);
				$host = $url_parts['host'];
				if (!$host) {
					$host = $rs['referer'];
					$rs['referer'] = 'http://'.$r['referer'];
				}
				$h = '';
				if ($this->sort=='referer_top' || $this->sort=='domain_top') {
					$h .= '<a target="_blank" href="'.sprintf(IP_LOOKUP,$rs['ip']).'"><span style="color:green">('.$rs['cnt'].')</span></a> ';
				}
				$h .= '<a href="'.$rs['referer'].'" title="Referer: '.strform(wrap($rs['referer'],30)).'
IP:'.$rs['ip'].'" target="_blank">'.str_replace('www.','',$host).'</a>';
				if ($this->sort!='referer_top' && $this->sort!='domain_top') {
					$h .= ' <a target="_blank" href="'.sprintf(IP_LOOKUP,$rs['ip']).'"> ('.$rs['cnt'].')</a> ';
				}
			} else {
				$h = '<a target="_blank" href="'.sprintf(IP_LOOKUP,$rs['ip']).'">'.$rs['ip'].' ('.$rs['cnt'].')</a>';
			}
			$rs['act'] = false;
			if ($this->sort!='referer_top' && $this->sort!='domain_top') {
				if ($rs['ip'] && $this->allowAct($rs['ip'])) {
					$rs['act'] = true;
				}
			}
			$rs['country'] = Data::country($rs['code']);
			$rs['host'] = $h;
			$rs['ua_name'] = $user_agents[$rs['ua']];
			$rs['date'] = Date::format($rs['date'],'%H:%M %d %b');
			if ($rs['dur']<=0) $rs['dur'] = '&nbsp;';
			else $rs['dur'] = Date::secondsToTime($rs['dur'],'short_color');
			unset($rs['referer']);
			$data[] = $rs;
		}
		$this->nav();
		$this->json_data = json($data);
	}
	
	private $act = array();
	private function allowAct($ip) {
		if (isset($this->act[$ip])) return $this->act[$ip];
		$long = ip2long($ip);
		$ret = !DB::row('SELECT 1 FROM '.DB_PREFIX.'users WHERE ip='.(int)$long.' AND groupid='.ADMIN_GROUP);
		$this->act[$ip] = $ret;
		return $ret;
	}
}


class AdminStats_where extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Where';
		parent::__construct(__CLASS__);
		$this->array['when_type'] = array(
			'all' => lang('$All'),
			'day' => lang('$Today'),
		//	'week' => 'On this week',
			'month' => lang('$This month'),
			'year' => lang('$This year')
		);
		$this->sel_type = get(self::KEY_SORT,'','[[:CACHE:]]');
		$this->when_type = get(self::KEY_SORT,'','[[:CACHE:]]');
		if (!array_key_exists($this->when_type, $this->array['when_type'])) {
			$this->when_type = 'all';	
		}
	}
	
	private function sql() {
		switch ($this->sel_type) {
			case 'all':
				$this->filter = '';
				$this->hitstotal = AdminStats_when::PeriodCount($this->prefix,'');
			break;
			default:
				$from = AdminStats_when::PeriodStart($this->sel_type);
				$this->filter = ' AND cameon>=\''.$from.'\'';
				$this->hitstotal = AdminStats_when::PeriodCount($this->prefix,$from);
			break;	
		}
	}
	public function listing() {
		$this->sql();
		$sql = 'SELECT SUM(cnt) as hits, SUM(1) AS unique_visits, country FROM '.$this->prefix.'visitor_stats WHERE TRUE'.$this->filter.' GROUP BY country ORDER BY hits DESC';
		$qry = DB::qry($sql,0,0);
		$data = array();
		$countries = require FTP_DIR_ROOT.'config/system/countries.php';
		while ($rs = DB::fetch($qry)) {
			$data[] = array(
				0	=> (isset($countries[$rs['country']])?$countries[$rs['country']]:''),
				1	=> $rs['hits'],
				2	=> $rs['unique_visits'],
				3	=> $rs['country'],
				4	=> $this->hitstotal,
				5	=> AdminStats_when::GraphValue($rs['hits'],$this->hitstotal,true),
				6	=> is_file(FTP_DIR_TPLS.'img/flags/16/'.$rs['country'].'.png')?1:0
			);
		}
		$this->json_data = json($data);
	}
}


class AdminStats_how extends Admin {
	public function __construct() {
		$this->title = 'Statistics: How';
		parent::__construct(__CLASS__);
		$this->array['browser_type'] = array(
			'browser' 	=> lang('$Browser'),
			'browser_v' => lang('$Browser Version'),
			'device'	=> lang('$Device'),
			'os' 		=> lang('$Operation System'),
			'ua' 		=> lang('$User agent'),
			'width'		=> lang('$Screen Resolution'),
		);
		$this->browser_type = get('type','','[[:CACHE:]]');
		if (!array_key_exists($this->browser_type, $this->array['browser_type'])) {
			$this->browser_type = 'browser';	
		}
		$this->array['when_type'] = array(
			'all' => lang('$All'),
			'day' => lang('$Today'),
		//	'week' => 'On this week',
			'month' => lang('$This month'),
			'year' => lang('$This year')
		);
		$this->when_type = get(self::KEY_SORT,'','[[:CACHE:]]');
		if (!array_key_exists($this->when_type, $this->array['when_type'])) {
			$this->when_type = 'all';	
		}
	}
	private function sql() {
		$this->order = 'hits DESC';
		if ($this->browser_type=='ua') {
			$this->order = 'ua ASC';
			$this->select = 'SQL_CALC_FOUND_ROWS';
		} else {
			$this->offset = 0;
			$this->limit = 0;
			if ($this->browser_type=='width') {
				$this->order = 'width DESC';
				$this->select = 'CONCAT(width,\'x\',height) AS name,';
			} 
			elseif ($this->browser_type=='browser_v') {
				$this->order = 'browser ASC, b_version DESC';
				$this->select = 'CONCAT(browser,\' \',b_version) AS name, browser, b_version,';
			}
			elseif ($this->browser_type=='browser') {
				$this->order = 'browser ASC';
				$this->select = 'browser AS name, browser,';
			}
			else {
				$this->order = $this->browser_type.' ASC';
				$this->select = $this->browser_type.' AS name,';	
			}
		}
		$this->group = 'name';
		$from = AdminStats_when::PeriodStart($this->when_type);
		$this->hitstotal = AdminStats_when::PeriodCount($this->prefix,$from);
		if ($from) $this->filter = ' AND cameon>\''.$from.'\'';
	}
	public function listing() {
		$this->sql();
		if ($this->browser_type=='ua') {
			$sql = 'SELECT '.$this->select.' ua, SUM(cnt) as hits, b_version, COUNT(1) AS unique_hits FROM '.$this->prefix.'visitor_stats WHERE TRUE'.$this->filter.' GROUP BY ua ORDER BY hits DESC';
		} else {
			$sql = 'SELECT '.$this->select.' SUM(cnt) as hits, b_version, COUNT(1) AS unique_hits FROM '.$this->prefix.'visitor_stats WHERE TRUE'.$this->filter.' GROUP BY '.$this->group.' ORDER BY hits DESC';
		}
		$qry = DB::qry($sql,$this->offset,$this->limit);
		if ($this->limit>0) {
			$this->total = DB::rows();
			$this->nav();
		}
		$data = array();
		while ($rs = DB::fetch($qry)) {
			$img = '';
			switch ($this->browser_type) {
				case 'os':
					if ($rs['name']) $img = '<img src="/tpls/img/os/'.$rs['name'].'.png" width="14" />';
					$name = AdminStats_when::OS($rs['name']);
				break;
				case 'browser':
					if ($rs['name']) {
						if ($rs['browser']) {
							$img = '<img src="/tpls/img/browsers/'.$rs['browser'].'.png" width="14" />';
						} else {
							$img = '-';
						}
					}
					$b = AdminStats_when::Browser($rs['browser']);
					$name = ($b ? $b : '-');
				break;
				case 'browser_v':
					if ($rs['name']) {
						if ($rs['browser']) {
							$img = '<img src="/tpls/img/browsers/'.$rs['browser'].'.png" width="14" />';
						} else {
							$img = '-';
						}
					}
					$b = AdminStats_when::Browser($rs['browser']);
					$name = ($b ? $b : '-').' '.$rs['b_version'];
				break;
				case 'ua':
					$img = '<img src="/tpls/img/oxygen/16x16/apps/systemtray.png" width="14" />';
					$rs['name'] = DB::one('SELECT v FROM '.DB_PREFIX.'db WHERE k='.e($rs['ua']).' AND t=6');
					$name = $rs['name'];
				break;
				case 'device':
					if ($rs['name']=='pc') {
						$img = '<img src="/tpls/img/oxygen/16x16/devices/computer-laptop.png" width="14" />';
						$name = lang('$PC');
					}
					elseif ($rs['name']=='mobile') {
						$img = '<img src="/tpls/img/oxygen/16x16/devices/phone.png" width="14" />';
						$name = lang('$Mobile');
					}
					elseif ($rs['name']=='tablet') {
						$img = '<img src="/tpls/img/oxygen/16x16/devices/input-tablet.png" width="14" />';
						$name = lang('$Tablet');
					} else {
						$img = '<img src="/tpls/img/oxygen/16x16/devices/computer.png" width="14" />';
						$name = lang('$Unknown');
					}
					
				break;
				default:
					$img = '<img src="/tpls/img/oxygen/16x16/apps/systemtray.png" width="14" />';
					$name = $rs['name'];
				break;
			}
			$data[] = array(
				0	=> $img,
				1	=> $name,
				2	=> $rs['hits'],
				3	=> $rs['unique_hits'],
				4	=> AdminStats_when::GraphValue($rs['hits'],$this->hitstotal,true)
			);
		}
		$this->json_data = json($data);
	}
}

class AdminStats_keywords extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Keywords';
		parent::__construct(__CLASS__);
		
	}
	private function sql() {
		$this->group = '';
		$this->filter = '';
		$this->order = 'cnt DESC, visited DESC';
		if (isset($this->find) && $this->find) {
			$this->filter .= ' AND (keyword LIKE \'%'.$this->find.'%\' OR engine LIKE \''.$this->find.'%\')';	
		}
	}
	protected function json($action = false){
		switch ($this->action) {
			case 'delete':
				$this->allow('stats','delete');
				DB::run('DELETE FROM '.$this->prefix.'visitor_keywords WHERE keyword='.escape(post('keyword')));
			break;		
		}
	}
	
	public function listing() {
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.'visitor_keywords WHERE TRUE'.$this->filter.($this->group?' GROUP BY '.$this->group:'').' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$data = array();
		while ($rs = DB::fetch($qry)) {
			$data[] = array(
				0	=> strform($rs['keyword']),
				1	=> strlen($rs['engine'])>1?$rs['engine']:Conf()->g3('searches',$rs['engine'],2),
				2	=> strlen($rs['engine'])>1?$rs['url']:'/?'.Conf()->g3('searches',$rs['engine'],0).'='.strform($rs['keyword']),
				3	=> date('H:i d.m.Y',$rs['visited']),
				4	=> $rs['cnt']
			);
		}
		$this->total = DB::rows();
		$this->nav();
		$this->json_data = json($data);
	}
}

class AdminStats_db extends Admin {
	public function __construct() {
		$this->title = 'Database data for statistics';
		parent::__construct(__CLASS__);
		$this->array['types'] = array(
			5	=> 'IP blocker',
			4	=> 'Search Engines',
			1	=> 'Operation systems',
			2	=> 'Browsers',
			3	=> 'Robots',
			6	=> 'User agents'
		);
		$this->type = get(self::KEY_TYPE,'','[[:CACHE:]]');
		if (!array_key_exists($this->type, $this->array['types'])) {
			$this->type = 5;	
		}
	}
	public function json() {
		switch ($this->action) {
			case 'delete_ip':
				if (!$this->id) break;
				DB::run('DELETE FROM '.DB_PREFIX.'ipblocker WHERE id='.$this->id);
			break;	
			case 'delete':
				if (!$this->type) break;
				DB::run('DELETE FROM '.DB_PREFIX.'db WHERE t='.$this->type.' AND k='.e(request('k')));
			break;
		}
	}
	
	private function sql() {
		$this->filter = '';
		if (isset($this->find) && ($s = $this->find)) {
			switch ($this->type) {
				case 1:
				case 2:
				case 3:
				case 4:
				case 6:
					$this->filter .= ' AND (k LIKE '.e($s.'%').' OR v LIKE '.e('%'.$s.'%').')';
				break;
				case 5:
					$this->filter .= ' AND (ip LIKE '.e('%'.$s.'%').' OR reason LIKE '.e('%'.$s.'%').')';
				break;
			}
		}
	}
	
	public function listing() {
		$this->sql();
		$data = array();
		switch ($this->type) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 6:
				if ($this->type!=6) {
					$this->button['save'] = true;
					if ($this->submitted) {
						foreach ($this->data as $k => $a) {
							if (empty($a['k'])) continue;
							if ($k==='new') {
								DB::noerror();
								DB::run('INSERT INTO '.DB_PREFIX.'db (t,k,v,s) VALUES ('.$this->type.','.e($a['k']).','.e($a['v']).','.e($a['s']).')');	
								continue;
							}
							DB::run('UPDATE '.DB_PREFIX.'db SET k='.e($a['k']).', v='.e($a['v']).' WHERE t='.$this->type.' AND k='.e($k));
						}
					}
				}
				$data = array();
				if ($this->type==6) {
					$sql = 'SELECT SQL_CALC_FOUND_ROWS k,v,(k+1) AS o FROM '.DB_PREFIX.'db WHERE t=6'.$this->filter.' ORDER BY o, v';
				} else {
					$sql = 'SELECT SQL_CALC_FOUND_ROWS k,v,s FROM '.DB_PREFIX.'db WHERE t='.$this->type.$this->filter.' ORDER BY k, v';
				}
				$qry = DB::qry($sql, $this->offset, $this->limit);
				$this->total = DB::rows();
				while ($rs = DB::fetch($qry)) {
					$data[] = $rs;	
				}
			break;
			case 5:
				$this->button['save'] = true;
				if ($this->submitted) {
					foreach ($this->data as $k => $a) {
						if (empty($a['ip'])) continue;
						if ($k==='new') {
							$data = array(
								'ip'		=> $a['ip'],
								'reason'	=> $a['reason'],
								'ip_from'	=> ($a['ip_from'] ? ip2long($a['ip_from']) : ''),
								'ip_to'		=> ($a['ip_to'] ? ip2long($a['ip_to']) : ''),
								'blocked'	=> $this->time
							);
							DB::insert('ipblocker',$data);
							continue;
						}
						$k = (int)$k;
						$data = array(
							'ip'		=> $a['ip'],
							'reason'	=> $a['reason'],
							'ip_from'	=> (($a['ip_from'] && !is_numeric($a['ip_from'])) ? ip2long($a['ip_from']) : $a['ip_from']),
							'ip_to'		=> (($a['ip_to'] && !is_numeric($a['ip_to'])) ? ip2long($a['ip_to']) : $a['ip_to'])
						);
						DB::update('ipblocker',$data,$k);
					}
				}
				$data = array();
				$qry = DB::qry('SELECT SQL_CALC_FOUND_ROWS id, ip, ip_from, ip_to, reason, blocked FROM '.DB_PREFIX.'ipblocker WHERE TRUE'.$this->filter.' ORDER BY blocked DESC', $this->offset, $this->limit);
				$this->total = DB::rows();
				while ($rs = DB::fetch($qry)) {
					if ($rs['ip_from'] && $rs['ip_to']) {
						$rs['ip_from'] = long2ip($rs['ip_from']);
						$rs['ip_to'] = long2ip($rs['ip_to']);
					} else {
						$rs['ip_from'] = '';
						$rs['ip_to'] = '';	
					}
			//		$domain	= Session::getHost($rs['ip']);
					$geo = Stats::geoIP($rs['ip']);
					$rs['country_name'] = $geo['country_name'];
					$rs['country_code'] = $geo['code'];
					$rs['city'] = $geo['city'];
					$rs['blocked_ago'] = lang('$%1 ago',Date::timeAgo($rs['blocked']));
					$rs['blocked'] = date('d.m.Y', $rs['blocked']);
					$rs['url'] = sprintf(IP_LOOKUP,$rs['ip']);
					$data[] = $rs;
				}
			break;
		}
		$this->nav();
		$this->json_data = json_encode($data);
	}
}





class AdminStats_online extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Current online visitors';
		parent::__construct(__CLASS__);
	}
	private function sql() {
		$this->filter = ' AND template=\''.$this->template.'\'';
		if (isset($this->find) && $this->find) {
			$this->filter .= ' AND (location LIKE \'%'.$this->find.'%\' OR sessvalue LIKE \''.$this->find.'%\')';	
		}
		$this->group = 'a.host';
		$this->order = 'a.expiration DESC';
	}
	public function listing() {
		$this->sql();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS a.expiration, a.host, a.location, a.clicks, a.views, a.sessvalue, a.userid, a.visit_id FROM '.DB_PREFIX.'sessions a WHERE TRUE'.$this->filter.' GROUP BY '.$this->group.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		$data = array();
		$un = lang('$Unknown');
		while ($rs = DB::fetch($qry)) {			
			$s = unserialize($rs['sessvalue']);
			$l = URL::ht(strform('?'.$rs['location']));
			if ($rs['userid']) $u = DB::one('SELECT login FROM '.DB_PREFIX.'users WHERE id='.$rs['userid']);
			else $u = '';
			
			$data[] = array(
				0	=> $rs['host'],
				1	=>  '<a href="'.$l.'" target="_blank">'.Parser::stripLink($l).'</a>',
				2	=> Date::TimeAgo($rs['expiration'] - SESSION_LIFETIME).' '.Conf()->g2('ARR_NUMBERS','ago').' ('.$rs['clicks'].')',
				3	=> ($s['Country'] ? $s['Country'] : 'un'),
				4	=> ($s['City'] ? $s['City'] : $un),
				5	=> $s['OS'],
				6	=> AdminStats_when::OS($s['OS']),
				7	=> $s['Browser'],
				8	=> AdminStats_when::Browser($s['Browser']).' '.$s['BrowserVersion'],
				9	=> $rs['userid'],
				10	=> $u,
				11 	=> $rs['visit_id']
			);
		}
		$this->nav();
		$this->json_data = json($data);
	}
}



class AdminStats_referals extends Admin {
	public function __construct() {
		$this->title = 'Statistics: Referals';
		parent::__construct(__CLASS__);
		$this->array['group_type'] = array(
			'all' => lang('$Date'),
			'user' => lang('$Top User'),
			'user2' => lang('$Best User'),
			'domain' => lang('$Top Domain'),
			'day' => lang('$Day'),
			'month' => lang('$Month')
		);
		$this->group = false;
		$this->group_type = get(self::KEY_SORT,'','[[:CACHE:]]');
		if (!array_key_exists($this->group_type, $this->array['group_type'])) {
			$this->group_type = 'all';	
		}
	}

	public function listing() {
		$data = array();
		switch ($this->group_type) {
			case 'all':
				$sql = 'SELECT SQL_CALC_FOUND_ROWS *, (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=r.userid) AS user FROM '.$this->prefix.'visitor_referals r WHERE TRUE'.$this->filter.($this->group?' GROUP BY '.$this->group:'').' ORDER BY r.id DESC';
				$qry = DB::qry($sql,$this->offset,$this->limit);
				while ($rs = DB::fetch($qry)) {
					$l = html(URL::ht('?'.$rs['location']));
					array_push($data, array(
						0	=> date('H:i d.m.Y',$rs['added']),
						1	=> $rs['userid'],
						2	=> $rs['user'],
						3	=> '<a href="'.html($rs['referer'] ? $rs['referer'] : 'http://'.$rs['domain']).'" target="_blank">'.html($rs['domain']).'</a>',
						4	=> '<a href="'.$l.'" target="_blank">'.Parser::stripLink($l).'</a>',
						5	=> $rs['visit_id']
					));
				}
			break;
			case 'domain':
				$sql = 'SELECT SQL_CALC_FOUND_ROWS *, COUNT(1) AS cnt, MIN(added) AS min_added, MAX(added) AS max_added FROM '.$this->prefix.'visitor_referals GROUP BY domain ORDER BY cnt DESC';
				$qry = DB::qry($sql,$this->offset,$this->limit);
				while ($rs = DB::fetch($qry)) {
					array_push($data, array(
						0 => $rs['domain'],
						1	=> date('d.m.Y',$rs['min_added']),
						2	=> date('d.m.Y',$rs['max_added']),
						3	=> Date::timeAgo($this->time + $rs['max_added'] - $rs['min_added']),
						4	=> $rs['cnt']
					));
				}
			break;
			case 'month':
				$sql = 'SELECT SQL_CALC_FOUND_ROWS *, COUNT(1) AS cnt, MONTH(FROM_UNIXTIME(added)) AS month, YEAR(FROM_UNIXTIME(added)) AS year FROM '.$this->prefix.'visitor_referals GROUP BY year, month ORDER BY cnt DESC';
				$qry = DB::qry($sql,$this->offset,$this->limit);
				while ($rs = DB::fetch($qry)) {
					array_push($data, array(
						0 => first(Conf()->g2('ARR_MONTHS',$rs['month']-1)).' '.$rs['year'],
						1	=> $rs['cnt'],
						2	=> DB::one('SELECT COUNT(DISTINCT(domain)) FROM '.$this->prefix.'visitor_referals WHERE domain!=\'\' AND MONTH(FROM_UNIXTIME(added))='.$rs['month'].' AND YEAR(FROM_UNIXTIME(added))='.$rs['year']),
						3	=> DB::one('SELECT COUNT(DISTINCT(userid)) FROM '.$this->prefix.'visitor_referals WHERE domain!=\'\' AND MONTH(FROM_UNIXTIME(added))='.$rs['month'].' AND YEAR(FROM_UNIXTIME(added))='.$rs['year']),
						4	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users WHERE userid>0 AND MONTH(FROM_UNIXTIME(registered))='.$rs['month'].' AND YEAR(FROM_UNIXTIME(registered))='.$rs['year']),
					));
				}
			break;
			case 'day':
				$sql = 'SELECT SQL_CALC_FOUND_ROWS *, COUNT(1) AS cnt, DATE_FORMAT(FROM_UNIXTIME(added),\'%D %M %Y\') AS day FROM '.$this->prefix.'visitor_referals GROUP BY day ORDER BY id DESC';
				$qry = DB::qry($sql,$this->offset,$this->limit);
				while ($rs = DB::fetch($qry)) {
					array_push($data, array(
						0 => $rs['day'],
						1	=> $rs['cnt'],
						2	=> DB::one('SELECT COUNT(DISTINCT(domain)) FROM '.$this->prefix.'visitor_referals WHERE domain!=\'\' AND DATE_FORMAT(FROM_UNIXTIME(added),\'%D %M %Y\')=\''.$rs['day'].'\''),
						3	=> DB::one('SELECT COUNT(DISTINCT(userid)) FROM '.$this->prefix.'visitor_referals WHERE domain!=\'\' AND DATE_FORMAT(FROM_UNIXTIME(added),\'%D %M %Y\')=\''.$rs['day'].'\''),
						4	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users WHERE userid>0 AND DATE_FORMAT(FROM_UNIXTIME(registered),\'%D %M %Y\')=\''.$rs['day'].'\''),
					));
				}
			break;
			case 'user':
			case 'user2':
				if ($this->group_type=='user2') {					
					$sql = 'SELECT SQL_CALC_FOUND_ROWS *, COUNT(1) AS cnt, MIN(added) AS min_added, MAX(added) AS max_added, (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=r.userid) AS user FROM '.$this->prefix.'visitor_referals r WHERE TRUE'.$this->filter.' GROUP BY r.userid HAVING cnt>1 ORDER BY (COUNT(1)/(MAX(added)- MIN(added)+1)/86400)*100 DESC';
				} else {
					$sql = 'SELECT SQL_CALC_FOUND_ROWS *, COUNT(1) AS cnt, MIN(added) AS min_added, MAX(added) AS max_added, (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=r.userid) AS user FROM '.$this->prefix.'visitor_referals r WHERE TRUE'.$this->filter.' GROUP BY r.userid ORDER BY cnt DESC';		
				}
				
				$qry = DB::qry($sql,$this->offset,$this->limit);
				while ($rs = DB::fetch($qry)) {
					if (!$rs['domain']) {
						$rs['domain'] = DB::row('SELECT domain FROM '.$this->prefix.'visitor_referals WHERE domain!=\'\' AND userid='.$rs['userid'].' ORDER BY id DESC','domain');	
					}
					
					if ($rs['max_added'] - $rs['min_added'] > 0) {
						$power = round($rs['cnt'] / (($rs['max_added'] - $rs['min_added']) / 86400) * 100).' %';
					} else {
						$power = '';
					}
					array_push($data, array(
						0	=> $rs['user'],
						1	=> $rs['cnt'],
						2	=> DB::one('SELECT COUNT(DISTINCT(domain)) FROM '.$this->prefix.'visitor_referals WHERE domain!=\'\' AND userid='.$rs['userid']),
						3	=> DB::one('SELECT COUNT(DISTINCT(location)) FROM '.$this->prefix.'visitor_referals WHERE location!=\'\' AND userid='.$rs['userid']),
						4	=> date('d.m.Y',$rs['min_added']),
						5	=> date('d.m.Y',$rs['max_added']),
						6	=> Date::timeAgo($this->time + $rs['max_added'] - $rs['min_added']),
						7	=> '<a href="'.html($rs['referer'] ? $rs['referer'] : 'http://'.$rs['domain']).'" target="_blank">'.html($rs['domain']).'</a>',
						8 	=> $rs['userid'],
						9	=> DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users WHERE userid='.$rs['userid']),
						10 	=> $power
					));
				}
			break;
		}
		$this->total = DB::rows();
		$this->nav();
		$this->json_data = json($data);
	}
}