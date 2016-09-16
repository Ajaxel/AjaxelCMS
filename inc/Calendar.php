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
* @file       inc/Calendar.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Calendar {
	
	public $set = array();
	public $options = array();
	public $calendar = array();
	public $data = array();
	
	private $class_prefix = '';

	private $called = false;
	
	private $is_year = false;
	private $from_today = false;
	private $year_dropdown = array();
	private $month_year_dropdown = array();
	private $no_prev_next = false;
	
	private $diff = 'month';
	
	private $month_get = 'month';
	private $year_get = 'year';
	private $day_get = 'day';
	
	const SUNDAY = 7;
	const YEARS_ADD = 10;

	public function init($params = array()) {
		foreach ($params as $k => $v) $this->$k = $v;
		
		$this->set['month'] = request($this->month_get);
		$this->set['year'] = request($this->year_get);
		$this->set['day'] = request($this->day_get,'',sprintf('%02d',date('d')));
		
		
		
		if (!defined('CAL_GREGORIAN')) define('CAL_GREGORIAN',3);
		
		$this->set['week_start'] = Conf()->g2('LOCALE','week_start');
		$this->set['w'] = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
		$this->set['m'] = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
		
		$this->set['weekdays'] = Conf()->g('ARR_WEEKDAYS');
		$this->set['short_weekdays'] = Conf()->g('ARR_SHORT_WEEKDAYS');
		$this->set['one_weekdays'] = Conf()->g('ARR_ONE_WEEKDAYS');
		$this->set['months'] = Conf()->g('ARR_MONTHS');
		$this->set['months_med'] = Conf()->g('ARR_MONTHS_MED');
		$this->set['short_months'] = Conf()->g('ARR_SHORT_MONTHS');
		
		$this->set['today_year'] = date('Y');
		$this->set['today_month'] = intval(date('m'));
		$this->set['today_month_name'] = $this->set['months'][$this->set['today_month']-1];
		
		if ($this->set['month'] && !is_numeric($this->set['month'])) {
			$months = array();
			foreach ($this->set['months'] as $k => $m) {
				$months[$k] = Parser::toLat($m);
			}
			$month = Parser::toLat($this->set['month']);
			if (in_array($month,$months)) {
				$rev = array_flip($months);
				$this->set['month'] = $rev[$month];
			}
			else {
				$this->set['month'] = NULL;
			}
		} 
		elseif ($this->set['month']<1 || $this->set['month']>12) {
			$this->set['month'] = NULL;
		}
		if ($this->set['year'] && !is_numeric($this->set['year'])) {
			$this->set['year'] = NULL;
		}
		elseif ($this->from_today && $this->set['year'] < $this->set['today_year']) {
			$this->set['year'] = NULL;
		}
		elseif ($this->year_dropdown && $this->year_dropdown['year_from'] && $this->set['year']<$this->year_dropdown['year_from']) {
			$this->set['year'] = $this->year_dropdown['year_from'];
		}
		elseif ($this->year_dropdown && $this->year_dropdown['year_to'] && $this->set['year']>$this->year_dropdown['year_to']) {
			$this->set['year'] = $this->year_dropdown['year_to'];
		}
		elseif ($this->set['year'] < $this->set['today_year']-self::YEARS_ADD || $this->set['year'] > $this->set['today_year']+self::YEARS_ADD) {
			$this->set['year'] = NULL;
		}
				
		if (!$this->set['month']) $this->set['month'] = $this->set['today_month'];
		if (!$this->set['year']) $this->set['year'] = $this->set['today_year'];
		
		if ($this->from_today && $this->set['today_year']!=$this->set['year']) $this->from_today = false;
		if ($this->from_today && $this->set['year']==$this->set['today_year'] && $this->set['month'] < $this->set['today_month']) {
			$this->set['month'] = $this->set['today_month'];
		}
		$this->set['cur_year'] = $this->set['year'];
		$this->set['prev_year'] = $this->set['year'] - 1;
		$this->set['next_year'] = $this->set['year'] + 1;
		
		$this->set['month_sel'] = $this->set['month'];
		$this->set['year_sel'] = $this->set['year'];
		$this->set['day_sel'] = $this->set['day'];
		
		$this->setMonth();
		return $this;
	}

	private function setMonth($month = false, $year = false) {
		if ($month!==false) $this->set['month'] = $month;
		if ($year) $this->set['year'] = $year;
		
		if ($this->set['month']>12) {
			$this->set['month'] = $this->set['month'] - 12;
			$this->set['year'] += 1;
		}
		elseif ($this->set['month'] < 1) {
			$this->set['month'] = $this->set['month'] + 12;
			$this->set['year'] -= 1;
		}
		
		
		$this->set['days'] = cal_days_in_month(CAL_GREGORIAN, $this->set['month'], $this->set['year']);
		
		$this->set['today'] = (($this->set['month']==$this->set['today_month'] && $this->set['year']==$this->set['today_year'])?date('d'):false);
		$this->options['month'] = $this->set['month'];
		$this->options['year'] = $this->set['year'];
		$this->options['day'] = $this->set['day'];

		$this->options['month_name'] = $this->set['months'][$this->set['month']-1];
		$this->options['month_name_med'] = $this->set['months_med'][$this->set['month']-1];
		
		
		switch ($this->diff) {
			case 'year':
			
			break;
			case 'day':
			
				
				$this->options['next_year'] = (($this->set['month']==12 && $this->set['day']==$this->set['days']) ? $this->set['year'] + 1 : $this->set['year']);
				$this->options['prev_year'] = (($this->set['month']==1 && $this->set['day']==1) ? $this->set['year'] - 1 : $this->set['year']);
				$this->options['next_year'] = $this->options['prev_year'] = $this->set['year'];
				$this->options['next_month'] = $this->options['prev_month'] = $this->set['month'];
				
				if ($this->set['day']==$this->set['days']) {
					if ($this->set['month']==12) {
						$this->options['next_year'] = $this->set['year'] + 1;	
						$this->options['next_month'] = 1;
					} else {
						$this->options['next_year'] = $this->set['year'];
						$this->options['next_month'] = $this->set['month'] + 1;
					}
					$this->options['next_day'] = 1;
					$this->options['prev_day'] = $this->set['day'] - 1;
				}
				elseif ($this->set['day']==1) {
					if ($this->set['month']==1) {
						$this->options['prev_month'] = 12;
						$this->options['prev_year'] = $this->set['year'] - 1;
						$this->options['prev_day'] = cal_days_in_month(CAL_GREGORIAN, $this->options['prev_month'], $this->options['prev_year']);
					} else {
						$this->options['prev_month'] = $this->set['month'] - 1;
						$this->options['prev_day'] = cal_days_in_month(CAL_GREGORIAN, $this->options['prev_month'], $this->options['prev_year']);
					}
					$this->options['next_day'] = 2;
				}
				else {
					$this->options['next_day'] = $this->set['day'] + 1;
					$this->options['prev_day'] = $this->set['day'] - 1;
				}
					
				
				
			break;	
			default:
				$this->options['next_day'] = $this->set['day'];
				$this->options['prev_day'] = $this->set['day'];
				$this->options['prev_month'] = $this->set['month'] - 1==0 ? 12 : $this->set['month'] - 1;
				$this->options['next_month'] = $this->set['month'] + 1==13 ? 1 : $this->set['month'] + 1;
				$this->options['prev_year'] = $this->set['month'] - 1==0 ? $this->set['year'] - 1 : $this->set['year'];
				$this->options['next_year'] = $this->set['month'] + 1==13 ? $this->set['year'] + 1 : $this->set['year'];
			break;
		}
		
		$this->options['prev_days'] = cal_days_in_month(CAL_GREGORIAN, $this->options['prev_month'], $this->options['prev_year']);
		
		$this->options['prev_month_name'] = $this->set['months'][$this->options['prev_month']-1];
		$this->options['next_month_name'] = $this->set['months'][$this->options['next_month']-1];
		
		if ($this->set['days']>=30 && date('w', mktime(0, 0, 0, $this->set['month'], 1, $this->set['year']))==6 || date('w', mktime(0, 0, 0, $this->set['month'], 1, $this->set['year']))==0) {
			$this->options['is_short'] = true;
		} else {
			$this->options['is_short'] = false;
		}
	}

	public function buildYear() {
		$this->is_year = true;
		$ret = array();
		if ($this->from_today) {
			$year = $this->set['year'];
			for ($i = $this->set['today_month']; $i <= 12 + $this->set['today_month']; $i++) {
				if ($i>12) {
					$this->setMonth($i-12, $this->options['next_year']);
				} else {
					$this->setMonth($i, $this->options['year']);
				}
				$this->buildMonth();
				$ret[] = $this->calendar;
			}
			$this->setMonth($this->set['today_month'], $this->set['today_year']);
		} else {
			for ($i = 1; $i <= 12; $i++) {
				$this->setMonth($i, $this->set['year']);
				$this->buildMonth();
				$ret[] = $this->calendar;
			}
		}
		$this->calendar = $ret;
		$this->events();
		return $this;
	}
	
	
	public function buildRange($from=-1, $to=1) {
		$this->is_year = true;
		$ret = array();
		$month = $this->set['month'];
		$year = $this->set['year'];
		if ($from > $to) {
			$_to = $to;
			$to = from;
			$from = $_to;
		}
		
		for ($i = $month + $from; $i <= $month + $to; $i++) {
			$this->setMonth($i, $year);
			$this->buildMonth();
			$ret[] = $this->calendar;
		}

		$this->calendar = $ret;
		$this->events();
		return $this;
	}
	
	public function buildYearOnly() {
		$this->is_year = true;
		$ret = array();
		for ($i = 1; $i <= 12; $i++) {
			$ret[$i] = array(
				'month'	=> $this->set['months'][$i-1]
			);
			$cur_month = false;
			if ($this->set['year']==$this->set['today_year'] && $i==$this->set['month']) {
				$ret[$i]['today_month'] = true;
			}
		}
		$this->calendar['months'] = $ret;
		$this->calendar['options'] = $this->options;

		return $this;
	}
	
	public function buildMonth() {
		$ret = array();
		// prev
		if (!$this->no_prev_next) {
			if ($this->set['week_start']==self::SUNDAY) {
				$week_index = 0;
			} else {
				$week_index = 1;
			}
			
			if (date('w', mktime( 0, 0, 0, $this->set['month'], 1, $this->set['year']))!=$week_index) {
				$this->options['first_monday'] = 0;
				for ($i = $this->options['prev_days'] - 7; $i<=$this->options['prev_days']; $i++) {
					if (date('w',mktime(0,0,0,$this->options['prev_month'],$i,$this->options['prev_year']))==$week_index) {
						$this->options['first_monday'] = $i;
					}
				}
				for ($i = $this->options['first_monday']; $i<=$this->options['prev_days']; $i++) {
					if (date('w',mktime(0,0,0,$this->options['prev_month'],$i,$this->options['prev_year']))==$week_index) {
						for ($j=$i;$j<=$this->options['prev_days'];$j++) {
							$r = array();
							$r['day'] = $j;
							$r['month'] = $this->options['prev_month'];
							$r['year'] = $this->options['prev_year'];
							$r['weekday'] = date('w',mktime(0,0,0,$this->options['prev_month'],$j,$this->options['prev_year']));
							$r['type'] = 'prev';
							if ($r['weekday']==6) $r['style'] = 'saturday';
							elseif ($r['weekday']==0) $r['style'] = 'sunday';
							else $r['style'] = '';
							$this->day($r);
							$ret['p'.$j.'_'.$this->options['prev_month'].($this->is_year?'_'.$this->options['prev_year']:'')] = $r;
						}
						break;
					}
				}
			}
		}
		// this
		for ($i=1;$i<=$this->set['days'];$i++) {
			$r = array();
			$r['day'] = $i;
			$r['month'] = $this->set['month'];
			$r['year'] = $this->options['year'];
			$r['weekday'] = date('w',mktime(0,0,0,$this->set['month'],$i,$this->set['year']));
			$r['type'] = 'cur';
			if ($i==$this->set['today']) $r['style'] = 'today';
			elseif ($r['weekday']==6) $r['style'] = 'saturday';
			elseif ($r['weekday']==0) $r['style'] = 'sunday';
			else $r['style'] = '';
			$this->day($r);
			if ($r['weekday']==$this->set['week_start']-1 && $this->set['days']==$i) $this->options['no_next'] = true; else $this->options['no_next'] = false;
			if (isset($this->data['open'])) {
				$r['open'] = str_replace(
					array('{$day}','{$month}','{$year}'),
					array($i,$this->set['month'],$this->set['year']),
					$this->data['open']
				);
			}
			$ret['c'.$i.'_'.$this->set['month'].($this->is_year?'_'.$this->options['year']:'')] = $r;
		}
		// next	
		if (!$this->no_prev_next) {
			if (!$this->options['no_next'] && ($w = date('w', mktime(0, 0, 0, $this->options['next_month'], 1, $this->options['next_year']))!=$week_index)) {
				$this->options['next_week_start'] = ($w + 1==0 ? 1 : $w + 1);
				if ($this->options['next_week_start']>0) {
					for ($i=1;$i<=8;$i++) {
						$r = array();
						$nw = date('w',mktime(23,59,59,$this->options['next_month'],$i,$this->options['next_year']));
						$r['day'] = $i;
						$r['month'] = $this->options['next_month'];
						$r['year'] = $this->options['next_year'];
						$r['weekday'] = $nw;
						$r['type'] = 'next';
						if ($r['weekday']==6) $r['style'] = 'saturday';
						elseif ($r['weekday']==0) $r['style'] = 'sunday';
						else $r['style'] = '';
						$this->day($r);
						$ret['n'.$i.'_'.$this->options['next_month'].($this->is_year?'_'.$this->options['next_year']:'')] = $r;
						if ($nw==$this->set['week_start']-1) break;
					}
				}
			}
		}
		$this->calendar = array(
			'options'	=> $this->options,
			'days'		=> $ret
		);
		if (!$this->is_year) $this->events();
		return $this;
	}
	
	private function day(&$r) {
		$r['tr_open'] = $r['tr_close'] = false;
		if ($this->set['week_start']==self::SUNDAY) {
			if ($r['weekday']==0) $r['tr_open'] = true;
		} else {
			if ($r['weekday']==1) $r['tr_open'] = true;
		}
		if ($this->set['week_start']==self::SUNDAY) {
			if ($r['weekday']==6) $r['tr_close'] = true;
		} else {
			if ($r['weekday']==0) $r['tr_close'] = true;
		}
		if ($this->set['week_start']==self::SUNDAY) {
			if ($r['weekday']==6) {
				$r['weekday_name'] = $this->set['weekdays'][0];
			} else {
				$r['weekday_name'] = $this->set['weekdays'][$r['weekday']+1];
			}
		} else {
			$r['weekday_name'] = $this->set['weekdays'][$r['weekday']];
		}
		$r['month_name'] = $this->set['months'][$r['month']-1];
		$r['month_name_med'] = $this->set['months_med'][$r['month']-1];	
		if ($this->set['day_sel']==$r['day'] && $this->set['month_sel']==$r['month'] && $this->set['year_sel']==$r['year']) {
			$r['selected'] = true;
		}
	}
	
	public function data($data) {
		$this->is_year = false;
		$this->data = $data;
		return $this;
	}
	
	private function events() {
		$wheres = array();
		if ($this->is_year) {
			if ($this->from_today) {
				$sql_start = mktime(0,0,0,$this->set['month'],1,$this->set['year']);
				$sql_end = mktime(23,59,59,$this->set['month'],31,$this->set['year']+1);
				$wheres['time'] = '({$column} BETWEEN '.(int)$sql_start.' AND '.(int)$sql_end.')';
				$wheres['datetime'] = '({$column} BETWEEN \''.sprintf('%04d',$this->set['year']).'-'.sprintf('%02d',$this->set['month']).'-01\' AND \''.sprintf('%04d',$this->set['next_year']).'-'.sprintf('%02d',$this->set['month']).'-31\')';
				$wheres['date'] = '({$column} BETWEEN \''.sprintf('%04d',$this->set['year']).'-'.sprintf('%02d',$this->set['month']).'-01\' AND \''.sprintf('%04d',$this->set['next_year']).'-'.sprintf('%02d',$this->set['month']).'-31\')';				
			} else {
				$sql_start = mktime(0,0,0,1,1,$this->set['year']);
				$sql_end = mktime(23,59,59,12,31,$this->set['year']);
				$wheres['time'] = '({$column} BETWEEN '.(int)$sql_start.' AND '.(int)$sql_end.')';
				$wheres['datetime'] = '{$column} LIKE \''.sprintf('%04d',$this->set['year']).'-%\'';
				$wheres['date'] = '{$column} LIKE \''.sprintf('%04d',$this->set['year']).'-%\'';
			}
		} else {
			$sql_start = mktime(0,0,0,$this->set['month'],1,$this->set['year']);
			$sql_end = mktime(23,59,59,$this->set['month'],$this->set['days'],$this->set['year']);
			$wheres['time'] = '({$column} BETWEEN '.(int)$sql_start.' AND '.(int)$sql_end.')';
			$wheres['datetime'] = '{$column} LIKE \''.sprintf('%04d',$this->set['year']).'-'.sprintf('%02d',$this->set['month']).'-%\'';
			$wheres['datetime_repeat_yearly'] = 'MONTH({$column})=\''.sprintf('%02d',$this->set['month']).'\'';
			$wheres['date'] = '{$column} LIKE \''.sprintf('%04d',$this->set['year']).'-'.sprintf('%02d',$this->set['month']).'-%\'';
		}
		if (!$this->data) return;
		$data = $totals = array();
		$total = 0;
		foreach ($this->data['data'] as $table => $d) {
			if (!$d['sql']) continue;
			if (!isset($d['type'])) $d['type'] = 'time';
			$sql = str_replace('{$where}', str_replace('{$column}', $d['col'], $wheres[$d['type']]), $d['sql']);
			$is_datetime = $this->_is_datetime_col($d['type']);
			$qry = DB::qry($sql,0,0);
			while ($rs = DB::fetch($qry)) {
				if ($is_datetime) {
					$day = intval(substr($rs[$d['col']],8,2));
					$month = intval(substr($rs[$d['col']],5,2));
					if ($this->is_year) $year = intval(substr($rs[$d['col']],0,4));
					if ($d['type']!='date') $rs['time'] = substr($rs[$d['col']],10,6);
					else $rs['time'] = '';
				} else {
					$day = intval(date('d',$rs[$d['col']]));
					$month = intval(date('m',$rs[$d['col']]));
					if ($this->is_year) $year = intval(date('Y',$rs[$d['col']]));
					$rs['time'] = date('H:i',$rs[$d['col']]);
				}
				$rs['table'] = $table;
				$next = 't'.str_replace(':','_',$rs['time']);
				/*
				$rs['link'] = str_replace(
					array('{$day}','{$month}','{$year}'),
					array($day,$month,$this->set['year']),
					$d['link']
				);
				*/
				$key = 'c'.$day.'_'.$month.($this->is_year?'_'.$year:'');
				$data[$key][$next.str_pad($total,5,'0',STR_PAD_LEFT)] = $rs;
				if (!isset($totals[$key])) $totals[$key] = 0;
				$totals[$key]++;
				$total++;
			}
			DB::free($qry);
		}
		
		
		
		if ($this->is_year) {
			foreach ($this->calendar as $month => $calendar) {
				foreach ($calendar['days'] as $day_month_year => $a) {
					if (!isset($data[$day_month_year])) continue;
					ksort($data[$day_month_year],SORT_REGULAR);
					$this->calendar[$month]['days'][$day_month_year]['data'] = array_values($data[$day_month_year]);
					$this->calendar[$month]['days'][$day_month_year]['total'] = $totals[$day_month_year];
				}
			}
		} else {
			
			foreach ($this->calendar['days'] as $day_month => $a) {
				if (!isset($data[$day_month])) continue;
				ksort($data[$day_month],SORT_REGULAR);
				$this->calendar['days'][$day_month]['data'] = array_values($data[$day_month]);
				$this->calendar['days'][$day_month]['total'] = $totals[$day_month];
			}
		}
	}
	
	private function _is_datetime_col($c) {
		return $c=='datetime' || $c=='date'	 || $c=='datetime_repeat_yearly';
	}
	
	public function getSet() {
		return $this->set;	
	}

	public function toArray() {
		return $this->calendar;
	}
	
	public function dropdown() {
		$h = '';
		if (!$this->month_year_dropdown['year_from']) $this->month_year_dropdown['year_from'] = $this->set['today_year'];
		if (!$this->month_year_dropdown['year_to']) $this->month_year_dropdown['year_to'] = $this->set['today_year'] + self::YEARS_ADD;
		
		$h .= '<div class="'.$this->class_prefix.'months_years">';
		$h .= '<select class="'.$this->class_prefix.'years" id="cal_years" onchange="'.$this->month_year_dropdown['change_year'].'">';
		for ($i=$this->month_year_dropdown['year_from'];$i<=$this->month_year_dropdown['year_to'];$i++) {
			$h .= '<option value='.$i.($this->set['year']==$i?' selected':'').'>'.$i;
		}
		$h .= '</select>';
		$h .= ' <select class="'.$this->class_prefix.'months" id="cal_months" onchange="'.$this->month_year_dropdown['change_month'].'">';
		foreach ($this->set['months'] as $i => $month) {
			if ($this->from_today && $this->set['year']==$this->set['today_year'] && $i+1<$this->set['today_month']) continue;
			$h .= '<option value='.($i+1).($this->set['month']==$i+1?' selected':'').'>'.first($month);
		}
		$h .= '</select>';
		$h .= '</div>';
		return $h;
	}
	
	public function toHtml($month = false) {
		if ($this->is_year && !$month) {
			$h = '';
			if ($this->year_dropdown) {
				if (!$this->year_dropdown['year_from']) $this->year_dropdown['year_from'] = $this->set['today_year'];
				if (!$this->year_dropdown['year_to']) $this->year_dropdown['year_to'] = $this->set['today_year'] + self::YEARS_ADD;
				$h .= '<div class="'.$this->class_prefix.'year">';
				$c = str_replace(array(
					'{$year}'
				),array(
					'\'+this.value+\''
				),$this->year_dropdown['change']);
				$h .= '<select class="'.$this->class_prefix.'years" id="cal_years" onchange="'.$c.'">';
				for ($i=$this->year_dropdown['year_from'];$i<=$this->year_dropdown['year_to'];$i++) {
					$h .= '<option value='.$i.($this->set['year']==$i?' selected':'').'>'.$i;
				}
				$h .= '</select>';
				$h .= '</div>';
			}
			
			$_c = $this->calendar;
			$h .= '<table class="'.$this->class_prefix.'calendar_year"><tbody>';
			for ($i = 0;$i < 12;$i++) {
				if ($i%4==0) $h .= '<tr>';
				$this->calendar = $_c[$i];
				$h .= '<td class="month">'.$this->toHtml(true).'</td>';
				if ($i%4==3) $h .= '</tr>';
			}
			$h .= '</tbody></table>';
		} else {
			$h = '<div class="'.$this->class_prefix.'month">';
			$h .= '<div class="'.$this->class_prefix.'month_year">'.$this->calendar['options']['month_name'].' '.$this->calendar['options']['year'].'</div>';
			if ($this->month_year_dropdown && !$this->is_year) {
				$h .= $this->dropdown();
			}
			
			$h .= '<div class="'.$this->class_prefix.'calendar">';
			$h .= '<table class="'.$this->class_prefix.'calendar"><tbody>';
			$h .= '<tr class="'.$this->class_prefix.'weekdays">';
			foreach ($this->set['one_weekdays'] as $i => $l) {
				if (!$i) continue;
				$h .= '<th class="'.$this->class_prefix.'weekday_'.$i.'">'.$l.'</th>';
			}
			$h .= '<th class="'.$this->class_prefix.'weekday_0">'.$this->set['one_weekdays'][0].'</th>';
			$h .= '</tr>';
			
			$class = ''; // TODO, wtf

			foreach ($this->calendar['days'] as $day_month => $d) {
				if ($this->set['week_start']==self::SUNDAY) {
					if ($d['weekday']==0) $h .= '<tr>';
				} else {
					if ($d['weekday']==1) $h .= '<tr>';
				}
				$id = str_replace('.','-',$day_month);
				if ($d['type']=='prev' || $d['type']=='next') {
					$h .= '<td class="'.$this->class_prefix.'day '.$this->class_prefix.$this->set['w'][$d['weekday']].' '.$this->class_prefix.'disabled'.$class.'" id="cal_'.$id.'">';
					$h .= $d['day'];
					$h .= '</td>';
				}
				elseif (!@$d['data']) {
					$h .= '<td class="'.$this->class_prefix.'day'.($d['style']?' '.$this->class_prefix.$d['style']:'').' '.$this->set['w'][$d['weekday']].'" id="cal_'.$id.'">';
					if ($this->no_href || !$d['data'][0]['open']) {
						$h .= $d['day'];
					} else {
						$h .= '<a href="'.@$d['data'][0]['open'].'">'.$d['day'].'</a>';
					}
					$h .= '</td>';
				} else {
					$h .= '<td class="'.$this->class_prefix.'day '.$this->class_prefix.$this->set['w'][$d['weekday']].' '.$this->class_prefix.'has_data" id="cal_'.$id.'">';
					if ($this->no_href || !$d['open']) {
						$h .= $d['day'];
					} else {
						$h .= '<a href="'.@$d['open'].'">'.$d['day'].'</a>';
					}
					$h .= '<sup>'.$d['total'].'</sup>';
					$h .= '</td>';
				}
				if ($this->set['week_start']==self::SUNDAY) {
					if ($d['weekday']==6) $h .= '</tr>';
				} else {
					if ($d['weekday']==0) $h .= '</tr>';
				}
			}
			$h .= '</tbody></table>';
			$h .= '</div>';
			$h .= '</div>';
		}
		return $h;
	}
}