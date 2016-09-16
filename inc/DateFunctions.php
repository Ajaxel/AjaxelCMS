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
* @file       inc/DateFunctions.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

function DateDropDowns($name,$sel,$attr,$class_time,$class_date,$showdate,$tag_between,$p=array(),$return=false) {
	if (!is_array($p)) {
		$p = array();	
	}
	if (is_array($sel)) {
		$sel = sprintf('%04d-%02d-%02d %02d:%02d:00', $sel['Year'],$sel['Month'],$sel['Day'],$sel['Hour'],$sel['Minute']);
	}
	elseif (is_numeric($sel)) {
		$sel = Date::td($sel);
	}
	$tag_sep = ' ';
	if (is_array($tag_between)) {
		$tag_sep = $tag_between[1];
		$tag_between = $tag_between[0];	
	}
	if (!isset($p['hour_next'])) $p['hour_next'] = 0;
	if (!isset($p['minute_next'])) $p['minute_next'] = 0;

	
	if (!isset($p['empty'])) $p['empty'] = false;
	
	if (!$sel || $sel=='0000-00-00 00:00:00') {
		if (!$p['empty']) {
			$sel = Date::now($p['year_next'],$p['month_next'],$p['day_next'],$p['hour_next'],$p['minute_next']);
		}
	}
	$year_sel = $month_sel = $day_sel = $minute_sel = $hour_sel = '';
	$l = strlen($sel);
	if ($l==19 || $l==10) {
		$year_sel = (int)substr($sel, 0, 4);
		$month_sel = (int)substr($sel, 5, 2);
		$day_sel = (int)substr($sel, 8, 2);
		if ($l!=10) {
			$hour_sel = (int)substr($sel, 11, 2);
			$minute_sel = (int)substr($sel, 14, 2);
		}
	} elseif ($l==8) {
		$hour_sel = (int)substr($sel, 0, 2);
		$minute_sel = (int)substr($sel, 3, 2);
	}
	if (!$p['empty']) {
		if (!$day_sel) $day_sel = Date::day();
		if (!$month_sel) $day_sel = date('m');
		if (!$year_sel) $year_sel = date('Y');
		if (!$hour_sel) $hour_sel = Date::hour();
		if (!$minute_sel) $minute_sel = Date::minute();
	}
	elseif ($l!=19 && $l!=10) {
		$hour_sel = -1;
		$minute_sel = -1;
	}
	$time = $date = '';	
	$attr = ' style="width:auto"'.$attr;
	
	if (!$p['hour_from']) $p['hour_from'] = 0;
	if (!$p['hour_to']) $p['hour_to'] = 23;
	if (!$p['minute_from']) $p['minute_from'] = 0;
	if (!$p['minute_to']) $p['minute_to'] = 59;
	if (!$p['month_from']) $p['month_from'] = 1;
	if (!$p['month_to']) $p['month_to'] = 12;
	if (!$p['day_from']) $p['day_from'] = 1;
	
	if (!$p['day_to']) {
		if (!$p['day_to']) $p['day_to'] = 31;
	}
	$e = '';
	
	if (strpos($name,'[')) $e = ']';
	if ($showdate=='front' || $showdate=='left' || $showdate=='end' || $showdate=='right' || $showdate=='no_date') {
		// Hours
		$time .= '<select name="'.$name.'Hour'.$e.'" id="'.name2id($name.'Hour'.$e).'" class="'.$class_time.' select-hour"'.$attr.'>';
		if ($p['empty']) $time .= '<option value=""></option>';
		$time .= dateToOpts($p['hour_from'],$p['hour_to'],$p['hour_step'],0,23,$hour_sel,'hour',false,@$p['now']);
		$time .= '</select>';
		// Minutes
		$time .= ' <select name="'.$name.'Minute'.$e.'" id="'.name2id($name.'Minute'.$e).'" class="'.$class_time.' select-minute"'.$attr.'>';
		if ($p['empty']) $time .= '<option value=""></option>';
		$time .= dateToOpts($p['minute_from'],$p['minute_to'],$p['minute_step'],0,59,$minute_sel,'minute',false,@$p['now']);
		$time .= '</select>';
	}
	
	if ($showdate!='no_date') {
		if ($showdate=='front' || $showdate=='left') $date .= $tag_between;
		// Days
		$date .= '<select name="'.$name.'Day'.$e.'" id="'.name2id($name.'Day'.$e).'" class="'.$class_date.' select-day"'.$attr.'>';
		if ($p['empty']) $date .= '<option value=""'.(!$day_sel?' selected disabled':'').'>'.lang('_Day').'</option>';
		$date .= dateToOpts($p['day_from'],$p['day_to'],$p['day_step'],0,$p['day_to'],$day_sel,'day');
		$date .= '</select>';
		// Months
		$date .= $tag_sep.'<select name="'.$name.'Month'.$e.'" id="'.name2id($name.'Month'.$e).'" class="'.$class_date.' select-month"'.$attr.'>';
		if ($p['empty']) $date .= '<option value=""'.(!$month_sel?' selected disabled':'').'>'.lang('_Month').'</option>';
		if (!isset($p['months']) || !$p['months']) $p['months'] = Data::getArray('arr:months_med');
		$date .= dateToOpts($p['month_from'],$p['month_to'],$p['month_step'],1,12,$month_sel,'month',$p['months']);
		$date .= '</select>';
		// Years
		if ($p['year_from']-$p['year_to']<>0) {
			$date .= $tag_sep.'<select name="'.$name.'Year'.$e.'" id="'.name2id($name.'Year'.$e).'" class="'.$class_date.' select-year"'.$attr.'>';
			if ($p['empty']) $date .= '<option value=""'.(!$year_sel?' selected disabled':'').'>'.lang('_Year').'</option>';
			$date .= dateToOpts($p['year_from'],$p['year_to'],$p['year_step'],date('Y')-TOTAL_YEARS,date('Y')+TOTAL_YEARS,$year_sel,'year');
			$date .= '</select>';
		}
		if ($showdate=='end' || $showdate=='right') $date .= $tag_between;
	}
	
	if ($showdate=='front') $ret = $time.$date; else $ret = $date.$time;
	if ($return) return $ret; else echo $ret;
}

function dateToOpts($from,$to,$step,$min,$max,$sel,$type='day',$months = array(),$now=false) {
	$opts = array();
	$h_s = false;
	$ret = '';
	
	if ($from<$to) {
		if ($from<$min) $from = $min;
		if ($to>$max && $max) $to = $max;
	} else {
		if ($from>$max  && $max) $from = $max;
		if ($to<$min) $to = $min;
	}
	if ($step>=abs($from-$to)) $step = abs($from-$to)-1;
	
	if ($step<=0) $step = 1;
	$from = (int)$from;
	$to = (int)$to;
	$step = (int)$step;
	
	if ($type=='hour' && $now) {
		$opts[Date::hour()] = Date::hour();	
	}
	if ($type=='minute' && $now) {
		$opts[Date::minute()] = Date::minute();	
	}

	if ($from<=$to) {
		$fwd = true;
		for ($i=$from;$i<=$to;$i+=$step) {
			if (!$h_s && $sel==$i) {
				$opts[$i] = true;
				$h_s = true;
			} else $opts[$i] = false;
		}
	} else {
		$fwd = false;
		for ($i=$from;$i>=$to;$i-=$step) {
			if (!$h_s && $sel==$i) {
				$opts[$i] = true;
				$h_s = true;
			} else $opts[$i] = false;
		}
	}
	
	foreach ($opts as $i => $sel) {
		if ($sel && !$h_s && (($sel>$i && !$fwd) || ($sel<$i && $fwd))) {

			if ($type=='month') {
				$sl = $months[$sel-1];
				$si = sprintf('%02d',$sel);
			}
			elseif ($type=='year') {
				$sl = sprintf('%04d',$sel);
				$si = $sl;
			} 
			elseif ($type=='minute') {
				$si = sprintf('%02d',$sel);;
				$sl = ':'.$si;
			} else {
				$si = sprintf('%02d',$sel);
				$sl = $sel;
			}
			$ret .= '<option value="'.$si.'" selected>'.$sl.'';
			$h_s = true;
		}
		if ($type=='month') {
			$l = $months[$i-1];
			$i = sprintf('%02d',$i);
		}
		elseif ($type=='year') {
			$l = sprintf('%04d',$i);
			$i = $l;
		} 
		elseif ($type=='minute') {
			$i = sprintf('%02d',$i);;
			$l = ':'.$i;
		} else {
			$i = sprintf('%02d',$i);
			$l = $i;
		}
		$ret .= '<option value="'.$i.'"'.($sel?' selected':'').'>'.$l.'';
	}
	return $ret;
}

function intIntoWords($x=0) {
	$nwords = Conf()->g('ARR_NUMBERS');
	if(!is_numeric($x)) $w = '#';
	else if(fmod($x, 1) != 0) $w = '#';
	else {
		if($x < 0) {
			$w = 'minus ';
			$x = -$x;
		} else $w = '';
		if($x < 21) $w .= $nwords[$x];
		else if ($x < 100) {
			$w .= $nwords[10 * floor($x/10)];
			$r = fmod($x, 10);
			if($r > 0)
			$w .= ' '.$nwords[$r];
		} else if ($x < 1000) {
			$w .= $nwords[floor($x/100)] .' '.$nwords['hundred'];;
			$r = fmod($x, 100);
			if($r > 0)
			$w .= ' '.$nwords['separator'].' '. intIntoWords($r);
		} else if ($x < 1000000) {
			$w .= intIntoWords(floor($x/1000)) .' '.$nwords['thousand'];;
			$r = fmod($x, 1000);
			if($r > 0) {
				$w .= ' ';
				if($r < 100)
				$w .= $nwords['separator'].' ';
				$w .= intIntoWords($r);
			}
		} else {
			$w .= intIntoWords(floor($x/1000000)) .' '.$nwords['million'];
			$r = fmod($x, 1000000);
			if($r > 0) {
					$w .= ' ';
				if($r < 100)
				$word .= $nwords['separator'].' ';
				$w .= intIntoWords($r);
			}
		}
	}
	return $w;
}


function countDown($then, $addWord=false, $max=2) {
	if ($then==0) {
		if ($addWord===3) {
			return array(
				array(
					'00',
					'hours'
				),
				array(
					'00',
					'minutes'
				),
				array(
					'00',
					'seconds'
				)
			);	
		}
		return ' ';
	}
	$now = time();
	if (!is_numeric($then) && strpos($then,':')) {
		$then = Date::datetime2timespan($then);
	}
//	$then = timezone($then);
	$till = $then-$now;
	if ($till < 0) {
		$passed = 1;
		$till = abs($till);
	} else {
		$passed = 0;
	}
	$z = 0;
	$t = $d = array();
	$t['years'] = floor($till/31556926);
	if ($t['years']) $z++;
	$t['months'] = floor(($till%31556926)/2629744);
	if ($t['months']) $z++;
	if ($z!=$max) {
		$t['days'] = floor((($till%31556926)%2629744)/86400);
		if ($t['days']) $z++;
		if ($z!=$max) {
			$t['hours'] = floor(((($till%31556926)%2629744)%86400)/3600);
			if ($t['hours']) $z++;
			if ($z!=$max) {
				$t['minutes'] = floor((((($till%31556926)%2629744)%86400)%3600)/60);
				if ($t['minutes']) $z++;
				if ($z!=$max) {
					$t['seconds'] = floor((((($till%31556926)%2629744)%86400)%3600)%60);
				}
			}
		}
	}
	if ($addWord===NULL) return $t;
	elseif ($addWord===3) {
		if ($t['years']>0) {
			return array(
				array(
					$t['years'],
					Date::getPeriod($t['years'],6)
				),
				array(
					$t['months'],
					Date::getPeriod($t['months'],5)
				),
				array(
					$t['days'],
					Date::getPeriod($t['days'],3)
				)
			);
		}
		elseif ($t['months']>0) {
			return array(
				array(
					$t['months'],
					Date::getPeriod($t['months'],5)
				),
				array(
					$t['days'],
					Date::getPeriod($t['days'],3)
				),
				array(
					sprintf('%02d',$t['hours']),
					Date::getPeriod($t['hours'],2)
				)
			);
		}
		elseif ($t['days']>0) {
			return array(
				array(
					$t['days'],
					Date::getPeriod($t['days'],3)
				),
				array(
					sprintf('%02d',$t['hours']),
					Date::getPeriod($t['hours'],2)
				),
				array(
					sprintf('%02d',$t['minutes']),
					Date::getPeriod($t['minutes'],1)
				)
			);
		}
		else {
			return array(
				array(
					sprintf('%02d',$t['hours']),
					Date::getPeriod($t['hours'],2)
				),
				array(
					sprintf('%02d',$t['minutes']),
					Date::getPeriod($t['minutes'],1)
				),
				array(
					sprintf('%02d',$t['seconds']),
					Date::getPeriod($t['seconds'],0)
				)
			);
		}
	}
	
	$togo = array();
	if ($t['years']) {
		$togo[] = $t['years'].' '.Date::getPeriod($t['years'],6);
	}
	if ($t['months']) {
		$togo[] = $t['months'].' '.Date::getPeriod($t['months'],5);
	}
	if ($t['days'] && ($weeks = ($t['days']%7)==0)) {
		$togo[] = $weeks.' '.Date::getPeriod($weeks,4);
	}
	elseif ($t['days']) {
		$togo[] = $t['days'].' '.Date::getPeriod($t['days'],3);
	}
	if ($t['hours']) {
		$togo[] = $t['hours'].' '.Date::getPeriod($t['hours'],2);
	}
	if ($t['minutes']) {
		$togo[] = $t['minutes'].' '.Date::getPeriod($t['minutes'],1);
	}
	if ($t['seconds']) {
		$togo[] = $t['seconds'].' '.Date::getPeriod($t['seconds'],0);
	}
	$ret = '';
	$p = sizeof($togo)-1;
	$c = $p-1;
	foreach ($togo as $i => $t) {
		if ($i==$c) $ret .= $t.Conf()->g2('ARR_NUMBERS','separator');
		else $ret .= $t.($p!=$i?', ':'');
	}
	return $ret.($addWord?($passed?Conf()->g2('ARR_NUMBERS','ago'):Conf()->g2('ARR_NUMBERS','since')):'');
}


function timeToDisplay($seconds,$minutes,$hours,$days,$toWords=false) {
	$r = '';
	$d = $days;$h = $hours;$m = $minutes;$s = $seconds;
	if ($toWords) {
		if ($days) $d = intIntoWords($days);
		if ($hours) $h = intIntoWords($hours);
		if ($minutes) $m = intIntoWords($minutes);
		if ($seconds) $s = intIntoWords($seconds);
	}
	if ($days) $r .= $d.' '.Date::getPeriod($days,3).', ';
	if ($hours) $r .= $h.' '.Date::getPeriod($hours,2);
	if ($hours && (($minutes && !$seconds) || ($seconds && !$minutes))) $r .= Conf()->g2('ARR_NUMBERS','separator');
	elseif ($hours && $minutes && $seconds) $r .= ', ';
	if ($minutes) $r .= $m.' '.Date::getPeriod($minutes,1).($seconds?Conf()->g2('ARR_NUMBERS','separator'):'');
	if ($seconds) $r .= $s.' '.Date::getPeriod($seconds,0);
	return $r;
}