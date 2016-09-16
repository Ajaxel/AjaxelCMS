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
* @file       inc/Date.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!function_exists('cal_days_in_month')) {
	function cal_days_in_month($type = CAL_GREGORIAN, $month=1, $year=2015) {
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}
}

abstract class Date {
	
	private static $loaded = false;
	
	public static function load() {
		if (self::$loaded) return;
		self::$loaded = true;
		require FTP_DIR_ROOT.'inc/DateFunctions.php';
		Site::mem('DateFunctions.php load');
	}
	
	public static function lang($d) {
		return strtr(ucfirst($d), Site::$data['date_months_med']);	
	}

	public static function read($ts, $format, $month_format = 'SHORT_MONTHS') {
		$months = Conf()->g('ARR_'.$month_format);
		$month = date('m',$ts);
		$format = str_replace('[m]','[_4569874_]',$format);
		$ret = date($format,$ts);
		$ret = str_replace('[_4569874_]',$months[intval($month)],$ret);
		return $ret;
	}

	public static function exactTime() {
		$mtime = explode(' ',microtime());
		return number_format($mtime[1] + $mtime[0],2,'.','');
	}
	public static function format($datetime, $format='short') {
		return strftime(Conf()->g2('ARR_DATE_FORMAT',$format,$format),strtotime($datetime));
	}
	public static function now($years=0,$months=0,$days=0,$hours=0,$minutes=0) {
		return date('Y-m-d H:i:s',self::now_ts($years,$months,$days,$hours,$minutes));
	}
	public static function now_ts($years=0,$months=0,$days=0,$hours=0,$minutes=0) {
		/*
		if ($hours || $minutes) {
			$time = mktime($hours,$minutes,0,date('m'),date('d'),date('Y'));
		} else {
			$time = time();
		}
		*/
		$time = time() - (Session()->Timezone * 60) + (31536000*$years) + (2592000*$months) + (86400*$days) + (3600*$hours) + (60*$minutes);
		return $time;
	}
	public static function nowon($years=0,$months=0,$days=0,$hours=0,$minutes=0,$seconds=0) {
		$t = mktime(0,0,0,($months!==NULL?date('m'):0),($days!==NULL?date('d'):0),date('Y'));
		
		
		
		$time = $t - (Session()->Timezone * 60) + (31536000*$years) + ($months!==NULL?(2592000*$months):0) + ($days!==NULL?(86400*$days):0) + ($hours!==NULL?(3600*$hours):0) + ($minutes!==NULL?(60*$minutes):0) + ($seconds!==NULL?($seconds):0);
		return date('Y-m-d H:i:s',$time);
	}
	
	public static function thenow($years=0,$months=0,$days=0,$hours=0,$minutes=0,$seconds=0) {
		$t = time() - Session()->Timezone * 60;
		return date(date('Y',$t-$years*31536000).'-'.date('m',$t-$months*2592000).'-'.date('d',$t-$days*86400).' '.$hours.':'.$minutes.':'.$seconds,$time);
	}
	
	
	public static function DateTime2TimeSpan($datetime = '', $end = false) {
		$l = strlen($datetime);
		if (is_numeric($datetime)) return $datetime;
		if (!($l == 10 || $l == 19)) return 0;	
		$date = $datetime;
		if ($end) {
			$hours = 23;
			$minutes = 59;
			$seconds = 59;			
		} else {
			$hours = 0;
			$minutes = 0;
			$seconds = 0;
		}
		if($l == 19) {
			list($date, $time) = explode(' ', $datetime);
			list($hours, $minutes, $seconds) = explode(':', $time);
		}
		if ($date) {
			@list($year, $month, $day) = explode('-', $date);
			$ret = @mktime($hours, $minutes, $seconds, $month, $day, $year);
		}
		if (!$ret) return 0;
		return (int)$ret;
	}
	public static function DateTime2Date($datetime = '', $format = 'd.m.Y, H:i:s') {
		return date($format,DateTime2TimeSpan($datetime));
	}
	public static function TimeSpan2DateTime($timestamp='', $datetime = true) {
		if(empty($timestamp) || !is_numeric($timestamp)) $timestamp = time();
		return ($datetime) ? date('Y-m-d H:i:s', $timestamp) : date('Y-m-d', $timestamp);
	}
	public static function dd($datetime = '', $format = 'd.m.Y, H:i:s') {
		return self::DateTime2Date($datetime, $format);
	}
	public static function dt($datetime, $end = false) {
		return self::DateTime2TimeSpan($datetime, $end);
	}
	public static function td($timestamp='', $datetime = true) {
		return self::TimeSpan2DateTime($timestamp, $datetime);
	}
	
	
	public static function pubDate($timestamp) {
		if (!$timestamp) $timestamp = time();
		elseif (!is_numeric($timestamp)) {
			$timestamp = self::DateTime2TimeSpan($timestamp);
		}
		return gmdate('D, d M Y H:i:s', $timestamp).' GMT';
	}
	
	private static $toTimestampTime = false;
	public static function toTimestamp($str, $end = false) {
		if (!$str) return 0;
		if (is_array($str)) {
			$w = true;
			$ex = explode('/',$str['date']);
			if (count($ex)!=3) {
				$ex = explode('.',$str['date']);	
			}
			if (count($ex)!=3) {
				return 0;	
			}
			if (!self::$toTimestampTime) {
				self::$toTimestampTime = $str['time'];
				$w = false;
			}
			$ex2 = explode(':',self::$toTimestampTime);
			if (!$w) self::$toTimestampTime = false;
			return mktime((int)$ex2[0],(int)$ex2[1],0,(int)$ex[1],(int)$ex[0],(int)$ex[2]);
		}
		elseif (is_numeric($str)) {
			return $str;
		} else {
			$ex = explode('/',$str);
			if (count($ex)!=3) {
				$ex = explode('.',$str);	
			}
			if (count($ex)!=3) {
				return 0;
			}
			if ($end) {
				return mktime(23,59,59,(int)$ex[1],(int)$ex[0],(int)$ex[2]);
			} else {
				return mktime(0,0,0,(int)$ex[1],(int)$ex[0],(int)$ex[2]);	
			}
		}
	}
	
	public static function fromTimestamp($int, $join = '/') {
		if (!$int) return '';
		if (strstr($int,$join)) return $int;
		//if (!$int) $int = time();
		if ($int=='0000-00-00' || $int=='0000-00-00 00:00:00') return '';
		return date('d'.$join.'m'.$join.'Y',$int);
	}
	public static function fromTimestampTime($int) {
		return date('H:i',$int);
	}
	
	public static function timezone($time) {
		$tz = Session()->Timezone;
		$time = $time-(date('Z')-(date('I')*60));
		if ($tz==0) return $time;
		return $time + $tz * 60 + TIMEZONE_PHP_DIFF * 60;
	}
	
	public static function untimezone($time,$tz) {
		$tz = Session()->Timezone;
		$time = $time - $tz * 60 - TIMEZONE_PHP_DIFF * 60;
		$new_time = $time-((date('I')*60)-date('Z'));	
		return $new_time;
	}
	
	public static function zodiac($date) {
		if (!$date) return '';
		if (is_array($date)) {
			$year = $date['Year'];
			$month = $date['Month'];
			$day = $date['Day'];
		} else {
			list ($year,$month,$day) = explode('-', $date);
		}
		if(($month==1 && $day>20)||($month==2 && $day<20)){
		  return 'Aquarius';
		}else if(($month==2 && $day>18)||($month==3 && $day<21)){
		  return 'Pisces';
		}else if(($month==3 && $day>20)||($month==4 && $day<21)){
		  return 'Aries';
		}else if(($month==4 && $day>20)||($month==5 && $day<22)){
		  return 'Taurus';
		}else if(($month==5 && $day>21)||($month==6 && $day<22)){
		  return 'Gemini';
		}else if(($month==6 && $day>21)||($month==7 && $day<24)){
		  return 'Cancer';
		}else if(($month==7 && $day>23)||($month==8 && $day<24)){
		  return 'Leo';
		}else if(($month==8 && $day>23)||($month==9 && $day<24)){
		  return 'Virgo';
		}else if(($month==9 && $day>23)||($month==10 && $day<24)){
		  return 'Libra';
		}else if(($month==10 && $day>23)||($month==11 && $day<23)){
		  return 'Scorpio';
		}else if(($month==11 && $day>22)||($month==12 && $day<23)){
		  return 'Sagittarius';
		}else if(($month==12 && $day>22)||($month==1 && $day<21)){
		  return 'Capricorn';
		}
	}

	public static function age($dob) {
		if (!$dob) return NULL;
		if (strpos($dob,'/')) {
			list($m,$d,$y) = explode('/', $dob);
		} else {
			list($y,$m,$d) = explode('-', $dob);
		}
		$day = date('d');$month = date('m');$year = date('Y');
		if ($month > $m || ($month == $m && $day >= $d)) {
			$age = $year - $y;
		} else {
			$age = $year - $y - 1;
		}
		return $age;
	}

	public static function db2date($var, &$arr) {
		$v = $arr[$var];
		
		if (is_numeric($v)) {
			$v = self::td($v);
		}
		$arr[$var] = array();
		$arr[$var]['Year'] = (int)substr($v, 0, 4);
		$arr[$var]['Month'] = (int)substr($v, 5, 2);
		$arr[$var]['Day'] = (int)substr($v, 8, 2);
		$arr[$var]['Hour'] = (int)substr($v, 11, 2);
		$arr[$var]['Minute'] = (int)substr($v, 14, 2);
		$arr[$var]['Second'] = (int)substr($v, 17, 2);
	}

	public static function date2db($var, &$arr) {
		if (!is_array($arr[$var])) return;
		if (isset($arr[$var]['Year']) && isset($arr[$var]['Month']) && isset($arr[$var]['Day'])) {
			$arr[$var] = sprintf('%04d-%02d-%02d %02d:%02d:00', $arr[$var]['Year'], $arr[$var]['Month'],$arr[$var]['Day'],@$arr[$var]['Hour'],@$arr[$var]['Minute']);
		}
		elseif (isset($arr[$var.'Year'])) {
			$arr[$var] = sprintf('%04d-%02d-%02d %02d:%02d:00', $arr[$var.'Year'], $arr[$var.'Month'],$arr[$var.'Day'],@$arr[$var.'Hour'],@$arr[$var.'Minute']);
		//	unset($arr[$var.'Year'], $arr[$var.'Month'], $arr[$var.'Day'], $arr[$var.'Hour'], $arr[$var.'Minute']);
		}
	}
	
	public static function DateDropDowns($name,$sel,$attr,$class_time,$class_date,$showdate,$tag_between,$p=array(),$return=false) {
		self::load();
		return DateDropDowns($name,$sel,$attr,$class_time,$class_date,$showdate,$tag_between,$p,$return);
	}	
	
	public static function secondsToTime($time,$retWords='short',$toWords=false) {
		$time = round($time);
		if (strlen($time)==10) $time -= time();
		if ($retWords=='full') return self::countDown(time() + $time,false,4);
		$days = $hours = $minutes = $seconds = 0;
		
		
		if ($time>86400) {
			if ($retWords=='short') {
				return self::timeAgo(time()+(int)$time);
			} else {
				
			}
		}
		if ($time>=3600) {
			$hours = floor($time/3600);
			$minutes = floor(($time - $hours*3600)/60);
			$seconds = $time - $hours*3600 - $minutes*60;		
		}
		elseif ($time>=60) {
			$minutes = floor($time/60);
			$seconds = $time - $minutes*60;
		} 
		elseif ($time>0) $seconds = $time;
		
		if (!$minutes && $seconds!=(int)$seconds) {
			$seconds = number_format($seconds,2,'.','');
		} else {
			$seconds = (int)$seconds;
		}
		
		if ($retWords=='short') {
			return sprintf('%02d:%02d:%02d',$hours,$minutes,$seconds);
		}
		elseif ($retWords=='short_color') {
			return str_replace('00:','<span style="color:#888">00:</span>',sprintf('%02d:%02d:%02d',$hours,$minutes,$seconds));
		}
		elseif ($retWords) {
			$return = array('return'=>array('short','full',false),'days'=>$days,'hours'=>$hours,'minutes'=>$minutes,'seconds'=>$seconds,'full'=>self::timetoDisplay($seconds,$minutes,$hours,$days,$toWords));
			$minutes = sprintf('%02d',$minutes);
			$seconds = sprintf('%02d',$seconds);
			return array_merge($return,array('short'=>$hours.':'.$minutes.':'.$seconds));
		}
		else {
			if ($toWords) {
				if ($hours) $hours = IntIntoWords($hours);
				if ($minutes) $minutes = IntIntoWords($minutes);
				if ($seconds) $seconds = IntIntoWords($seconds);
			}
			return array('hours'=>$hours,'minutes'=>$minutes,'seconds'=>$seconds);
		}
	}
	
	public static function timeToDisplay($seconds,$minutes,$hours,$days,$toWords=false) {
		self::load();
		return timeToDisplay($seconds,$minutes,$hours,$days,$toWord);
	}
	
	public static function intIntoWords($x=0) {
		self::load();
		return intIntoWords($x);
	}
	
	public static function dayCountDown($ts=0, $num = false, $diff = true) {
		if (!is_numeric($ts)) $ts = self::DateTime2TimeSpan($ts);
		if (!$ts) return '';
		
		if ($diff) {
			$ts = $ts/* - (Session()->Timezone * 60)*/ + TIMEZONE_PHP_DIFF * 60;
			$time = time()/* - (Session()->Timezone * 60)*/ + TIMEZONE_PHP_DIFF * 60;
		} else {
			$time = time();
		}
		$today = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$d = (int)date('d')-1;
		if ($d==0) {
			if (($m = date('m')-1)==0) {
				$m = 12;
				$y = date('Y')-1;
			} else {
				$y = date('Y');
			}
		} else {
			$m = date('m');
			$y = date('Y');
		}
		$yesterday = mktime(0,0,0,$m,$d,$y);

		if ($ts > $time - 60) $ret = lang('Just now in %1', date(' H:i',$ts));
		elseif ($today<$ts) $ret = lang('Today in %1', date(' H:i',$ts));
		elseif ($yesterday<$ts) $ret = lang('Yesterday in %1', date(' H:i',$ts));
		else {
			$ret = date('d',$ts).(($num===true||$num===1)?'.'.date('m',$ts).'.':' '.(Conf()->g('ARR_MONTHS_MED')?Conf()->g2('ARR_MONTHS_MED',(int)date('m',$ts)-1):Conf()->g2('ARR_MONTHS',(int)date('m',$ts)-1)).' ').''.date('Y',$ts).($num===2?'':' '.lang('in').' '.date('H:i',$ts));
		}
		return $ret;
	}
	
	public static function countDown($then,$addword=false,$max=2) {
		self::load();
		return countDown($then,$addword,$max);
	}
	public static function timeAgo($t,$toWords=false,$diff = true) {
		if ($t==='0000-00-00 00:00:00' || !$t) return '';
		if (!is_numeric($t)) $t = self::DateTime2TimeSpan($t);
		if (strlen($t)!=10) return $t;
		if ($diff) $t = $t/* - (Session()->Timezone * 60)*/ + TIMEZONE_PHP_DIFF * 60;
		$diff = abs(time() - $t);
		$lengths = array(60,60,24,7,4.35,12,10);
		for($j = 0; $diff >= $lengths[$j]; $j++) {
			if ($lengths[$j]>0) $diff /= $lengths[$j];
			else break;
		}
		$diff = round($diff);
		$per = self::getPeriod($diff,$j);
		if ($toWords) $diff = self::intIntoWords($diff);
		return $diff.' '.$per;
	}
	public static function getPeriod($diff,$j,$s = 'PERIOD') {
		$for_med = array(11,12,13,14);
		if ($diff==1) return Conf()->g2('ARR_'.$s,$j);
		if ($diff==0) return Conf()->g2('ARR_'.$s.'S',$j);
		if (is_array(Conf()->g('ARR_'.$s.'S_MED'))) {
			if (in_array(substr($diff,-2),$for_med)) $per = Conf()->g2('ARR_'.$s.'S',$j);
			elseif (substr($diff,-1)>=2 && substr($diff,-1)<=4) $per = Conf()->g2('ARR_'.$s.'S_MED',$j);
			else $per = Conf()->g2('ARR_'.$s.'S',$j);
		} else $per = Conf()->g2('ARR_'.$s.'S',$j);
		return $per;
	}
	public static function month($t) {
		$m = date('m',$t)-1;
		$word = Conf()->g2('ARR_MONTHS_MED',$m);
		return $word ? $word : Conf()->g2('ARR_MONTHS',$m);
	}
	public function day($t = 0) {
		if (!$t) $t = time();
		$t = $t - (Session()->Timezone * 60);
		return date('d',$t);	
	}
	public function daymonth($t) {
		return self::day($t).' '.self::month($t);	
	}
	public static function hour($h = false) {
		$time = time() - (Session()->Timezone * 60);
		$ret = date('H',$time);
		return $ret;
	}
	public static function minute($m = false) {
		$time = time() - (Session()->Timezone * 60);
		$ret = date('i',$time);
		return $ret;
	}	
}