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
* @file       inc/Stats.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Stats {

	public static function init(&$obj, $mini = false) {
		$stats = self::write($mini);
		$obj->Country = $stats['geo']['code'];
		if (array_key_exists($stats['geo']['code'], Site::getRegions())) $obj->Region = $stats['geo']['code'];
		if ($stats['geo']['city']) $obj->City = $stats['geo']['city'];
		if (isset($stats['geo']['timezone'])) $obj->Timezone = $stats['geo']['timezone'];
		if (isset($stats['geo']['region'])) $obj->State = $stats['geo']['region'];
		if (isset($stats['os'])) $obj->OS = $stats['os'];
		if (isset($stats['browser'])) $obj->Browser = $stats['browser'];
		$obj->BrowserVersion = $stats['b_version'];
		$obj->From = $stats['from'];
		$obj->CameON = Conf()->g('start_time');
		$obj->Device = $stats['device'];
		define ('DEVICE', $stats['device']);
		if (isset($stats['id'])) {
			$obj->VisitID = $stats['id'];
		}
		$obj->IP = $stats['ip'];
		if (JS_STATS && isset($stats['width'])) {
			$obj->Width = $stats['width'];
			$obj->Height = $stats['height'];
			if ($stats['timezone']) $obj->Timezone = $stats['timezone'];
		}
		$obj->IPlong = ip2long($obj->IP);
		Site::hook('Session::writeStats',false,$stats);
		return @$stats['id'];	
	}
	
	
	private static function write($mini = false) {
		$ip	= Session::getIP();
		if (!$ip) {
			Message::Halt('Bad access!','Cannot get your IP address, probable hack attempt');
		}
		$agent = $_SERVER['HTTP_USER_AGENT'];
		
		if (defined('ANTIHACK_SECONDS') && ANTIHACK_SECONDS>0 && SITE_TYPE!='ajax' && SITE_TYPE!='json' && SITE_TYPE!='upload' && !Session::isBot()) {
			$check_time = $microtime - ANTIHACK_SECONDS;
			$agent_id = self::getAgentID($agent);
			$sql = 'SELECT 1 FROM '.DB_PREFIX.PREFIX.'visitor_stats WHERE ip='.e($ip).' AND ua='.$agent_id.' AND microtime>'.$check_time;
			if (DB::one($sql)) {
				Message::halt('Probable hack attack!','Too many requests');
			}
		}
		
		
		
		$os			= (string)self::os();
		$browser	= self::browser();
		$device		= self::device($agent);
		$b_version	= substr($browser[1],0,4);
		$browser 	= $browser[0];
		$host		= self::getHost($ip);

		/*
		$geo 		= array(
			'code'	=> '',
			'city'	=> ''
		);
		if (self::isIpIntranet($ip)) {
			$from = 'intranet';
			$geo = array(
				'code'	=> MY_LOCATION,
				'city'	=> 'intranet'
			);
		}
		elseif (self::isIpLocalHost($ip)) {
			$from = 'localhost';
			$geo = array(
				'code'	=> MY_LOCATION,
				'city'	=> 'localhost'
			);
		}
		elseif (!self::isIpValid($ip)) {
			$from = 'invalid';
			$geo = array(
				'code'	=> '',
				'city'	=> 'invalid'
			);
		}
		else {
			$from = self::getDomainFromNS($host);
			$geo = self::geoIP($ip, false,$host);
		}
		*/
		$from = self::getDomainFromNS($host);
		$geo = self::geoIP($ip, false,$host);
		
		$country 	= $geo['code'];
		$city 		= $geo['city'];
		$agent_id	= 0;
		$referer 	= '';
		$mtime 		= explode(' ',microtime());
		$microtime 	= $mtime[1] + number_format($mtime[0],2,'.','');
		$today 		= sprintf('%04d-%02d-%02d', strftime('%Y'), strftime('%m'), strftime('%d'));		

		
		$ref_domain = '';
		$no_clean = false;
		$keyword_id = 0;
		
		if (isset($_SERVER['HTTP_REFERER']) && (($ref_domain = URL::clean($_SERVER['HTTP_REFERER']))!=URL::clean(HTTP_BASE) || $no_clean)) {
			$referer = $_SERVER['HTTP_REFERER'];
		}
		if ((JS_STATS && SITE_TYPE!='js') || !JS_STATS) {
			$keyword_id = self::catchKeyword($referer, $ref_domain);
		}
		
		if ($mini) {
			return array (
				'keyword_id'=> $keyword_id,
				'device'	=> $device,
				'ip'		=> $ip,
				'os'		=> $os,
				'browser'	=> $browser,
				'b_version'	=> $b_version,
				'geo'		=> $geo,
				'microtime'	=> $microtime,
				'from'		=> $from
			);
		}
		
		
		$width = intval(get('width','',0));
		$height = intval(get('height','',0));
		$timezone = intval(get('timezone','',0));
		if (!$agent_id) $agent_id = self::getAgentID($agent);
		
	//	$and_where .= ' AND browser='.e($browser).' AND b_version='.e($b_version).'';
		$and_where = ' AND ua='.$agent_id;
		
		$sql = 'SELECT id FROM '.DB_PREFIX.PREFIX.'visitor_stats WHERE ip='.e($ip).$and_where.' AND cameon > (NOW() - INTERVAL 1 DAY) ORDER BY id DESC';
		$id = DB::one($sql);
		//DB::noerror();
		if ($id) {
			DB::run('UPDATE '.DB_PREFIX.PREFIX.'visitor_stats SET referer='.e($referer).', cnt=cnt+1, microtime=\''.$microtime.'\' WHERE id='.$id);
			$inserted = false;
		} else {
			DB::run('INSERT INTO '.DB_PREFIX.PREFIX.'visitor_stats VALUES (NULL, 0, '.$agent_id.', NOW(), '.e($ip).', '.e($os).', '.e($browser).', '.e($b_version).', '.e($referer).', '.e($country).', '.e($city).', 1, \''.$microtime.'\', 0.00, 0, '.$width.', '.$height.', '.$timezone.', '.e($device).')');
			$id = DB::id();
			$inserted = true;
		}
		return array (
			'id'		=> $id,
			'keyword_id'=> $keyword_id,
			'device'	=> $device,
			'ip'		=> $ip,
			'os'		=> $os,
			'browser'	=> $browser,
			'b_version'	=> $b_version,
			'geo'		=> $geo,
			'inserted'	=> $inserted,
			'width'		=> $width,
			'height'	=> $height,
			'timezone'	=> $timezone,
			'from'		=> $from
		);
	}
		

	public static function revisit(&$obj) {
		$agent_id = self::getAgentID();
		$sql = 'SELECT * FROM '.DB_PREFIX.PREFIX.'visitor_stats WHERE ip='.e($obj->IP).' AND ua='.$agent_id.' AND cameon > (NOW() - INTERVAL 1 DAY) ORDER BY id DESC';
		$row = DB::row($sql);
		if (!$row) {
			self::init($obj, false);
			return;
		}

		$obj->VisitID = $row['id'];
		$obj->Country = $row['country'];
		$obj->City = $row['city'];
		$obj->Browser = $row['browser'];
		$obj->BrowserVersion = $row['b_version'];
	//	if ($row['clicks']) $obj->Clicks = $row['clicks'];
		$obj->Timezone = $row['timezone'];
		$obj->Width = $row['width'];
		$obj->Height = $row['height'];	
	}
	
	public static function referal(&$obj) {
		$ref = get(URL_KEY_REFERAL,'',@$_SESSION['referal']);
		if (!$ref) return false;
		$ref = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE '.(is_numeric($ref)?'id='.(int)$ref:'login='.e($ref)));
		if (!$ref) return false;
		// check if user was already within 24h
		$was = DB::row('SELECT id, userid FROM '.DB_PREFIX.PREFIX.'visitor_referals WHERE visit_id='.$obj->VisitID.' AND added>'.(time()-86400));
		if ($was) {
			if (get(URL_KEY_REFERAL)) $obj->referal_try = true;
			$obj->ReferalID = $was['id'];
			$obj->Referal = $was['userid'];
			return false;
		}
		if (defined('REFERALS_PER_DAY') && REFERALS_PER_DAY) {
			$total = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.PREFIX.'visitor_referals WHERE userid='.$ref.' AND added>'.(time()-86400));
			if ($total>=REFERALS_PER_DAY) return false;
		}
		if (isset($_SESSION['HTTP_REFERER'])) {
			$referer = @$_SESSION['HTTP_REFERER'];	
		} else {
			$referer = @$_SERVER['HTTP_REFERER'];
		}
		if (($ref_domain = URL::clean($referer))==URL::clean(HTTP_BASE)) {
			$ref_domain = '';
		}
		$data = array(
			'userid'	=> $ref,
			'visit_id'	=> $obj->VisitID,
			'domain'	=> $ref_domain,
			'location'	=> $_SERVER['QUERY_STRING'],
			'referer' 	=> $referer,
			'added'		=> time()
		);
		DB::insert('visitor_referals',$data);
		$data['id'] = DB::id();
		$obj->ReferalID = DB::id();
		$obj->Referal = $ref;
		$obj->referal_try = true;
		Site::hook('Session::doReferal',false,$data);
		return true;	
	}
	
	public function save_search($k, $v, $click_id,$visit_id) {
		if (SEARCHES_GROUP) {
			if (!DB::one('SELECT 1 FROM '.DB_PREFIX.PREFIX.'visitor_keywords WHERE engine='.escape($k).' AND keyword='.escape($v))) {
				$data = array(
					'keyword'	=> $v,
					'engine'	=> $k,
					'url'		=> '',
					'visited'	=> time(),
					'cnt'		=> 1
				);
				DB::insert('visitor_keywords',$data);
			} else {
				DB::run('UPDATE '.DB_PREFIX.PREFIX.'visitor_keywords SET cnt=cnt+1, visited='.time().' WHERE engine='.escape($k).' AND keyword='.escape($v));
			}
			
		} else {
			$is = DB::one('SELECT 1 FROM '.DB_PREFIX.PREFIX.'visitor_searches WHERE `key_index`='.$k.' AND `value`='.e($v).' AND `visit_id`='.$visit_id);
			if ($is) return;
			$data = array(
				'key_index'	=> $k,
				'value'	=> $v,
				'added'	=> time(),
				'visit_id' => $visit_id,
				'click_id'=> $click_id
			);
			DB::insert('visitor_searches',$data);
		}
	}	
	
	public static function getHost($ip) {
		$u = 'unknown';
		if (!$ip) return $u;
		$ret = trim(strtolower(@gethostbyaddr($ip)));
		return $ret;
	}
	private static function getFirstIpFromList($ip) {
		$p = strpos($ip, ',');
		if($p!==false) return substr($ip, 0, $p); else return $ip;
	}

	public static function &getIP() {
		if (isset($_SESSION['IP'])) return $_SESSION['IP'];
		if(isset($_SERVER['HTTP_CLIENT_IP']) && ($ip = self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP'])) && strpos($ip, 'unknown')===false && self::getHost($ip) != $ip) $ret = $ip;
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($ip) && !empty($ip) && strpos($ip, 'unknown')===false && self::getHost($ip) != $ip) $ret = $ip;
		elseif(isset($_SERVER['HTTP_CLIENT_IP']) && strlen(self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']))!=0) $ret = self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']);
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen(self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']))!=0) $ret = self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']);
		else $ret = self::getFirstIpFromList($_SERVER['REMOTE_ADDR']);
		$ret = $ret?$ret:'127.0.0.1';
		$_SESSION['IP'] = $ret;
		return $ret;
	}
	
	public static function geoIP($ip = false) {
		$rs = false;
		if (is_file(FTP_DIR_ROOT.'geo/geoip.php')) {
			if (!$ip) $ip = self::getIP();
			$ipv6 = strpos($ip,':');
			
			if (!function_exists('geoip_open')) {
				require_once FTP_DIR_ROOT.'geo/geoip'.($ipv6 ? '_v6':'').'.php';
				require_once FTP_DIR_ROOT.'geo/geoipcity.php';
				require_once FTP_DIR_ROOT.'geo/geoipregionvars.php';
				require_once FTP_DIR_ROOT.'geo/timezone.php';
			}
			if ($ipv6) {
				$gi = geoip_open(FTP_DIR_ROOT.'/geo/GeoIPv6.dat',GEOIP_STANDARD);
				$rs = new stdClass;
				$rs->code = geoip_country_code_by_addr_v6($gi, $ip);
				geoip_close($gi);
			}
			elseif (is_file(FTP_DIR_ROOT.'/geo/GeoLiteCity.dat')) {
				$gi = geoip_open(FTP_DIR_ROOT.'/geo/GeoLiteCity.dat', GEOIP_STANDARD);
				$rs = geoip_record_by_addr($gi, $ip);
				geoip_close($gi);
			}
			elseif (is_file(FTP_DIR_ROOT.'/geo/GeoIP.dat')) {
				$gi = geoip_open(FTP_DIR_ROOT.'/geo/GeoIP.dat', GEOIP_STANDARD);
				$rs = geoip_record_by_addr($gi, $ip);
				geoip_close($gi);
			}
		}
		if (!$rs || !is_object($rs)) {
			$rs = array(
				'country_name'	=> '',
				'code'			=> '',
				'city'			=> '',
				'region'		=> 0,
				'latitude'		=> 0,
				'longitude'		=> 0
			);
		} else {
			$rs = get_object_vars($rs);
		}
		if ($rs['code'] && function_exists('get_time_zone')) {
			$rs['timezone'] = get_time_zone($rs['code'],$rs['region']);
		}
		$ret = array();
		foreach ($rs as $k => $v) {
			$ret[$k] = utf8_encode($v);
		}
		$ret['code'] = strtolower($ret['country_code']);
		return $ret;
	}

	
	public static function isIpIntranet($ip) {
		$local = '/^10|^169\.254|^172\.16|^172\.17|^172\.18|^172\.19|^172\.20|^172\.21|^172\.22|^172\.23|^172\.24|^172\.25|^172\.26|^172\.27|^172\.28|^172\.29|^172\.30|^172\.31|^192|0:0:0:0:0:0:0:1/';
		return preg_match($local,$ip);
	}
	public static function isIpLocalHost($ip) {
		return substr($ip, 0, 4)==='127.';
	}
	public static function getDomainFromNS($visitor_nslookup) {
		$ret = '';
		$pos = strrpos($visitor_nslookup, '.') + 1;
		if ($pos > 1) {
			$xt = trim(substr($visitor_nslookup, $pos));
			if (preg_match('/([a-zA-Z])/', $xt)) {
				$ret = strtolower($xt);
			}
		}
		return $ret;
	}
	
	private static function catchKeyword($referer, $ref_domain) {
		if (!$ref_domain) return false;
		$sel = self::getSearchEngine($ref_domain);
		$engine = @$sel['v'];
		@parse_str($referer, $res);
		if ($res) {
			$keyword = @$res[@$sel['s']];
			if ($keyword && $engine) {
				Conf()->s('keyword', $keyword);
				Conf()->s('keywords', array());
				$ex = preg_split('(\s|\.|,|\!|\?)',$keyword);
				foreach ($ex as $k) {
					if (strlen(trim($k))>2) Conf()->fill('keywords', $k);
				}
				$keyword_id = DB::one('SELECT id FROM '.DB_PREFIX.PREFIX.'visitor_keywords WHERE keyword LIKE '.e($keyword).' AND engine LIKE '.e($engine).' ORDER BY id DESC');
				if ($keyword_id) {
					DB::run('UPDATE '.DB_PREFIX.PREFIX.'visitor_keywords SET cnt=cnt+1, visited='.time().' WHERE id='.$keyword_id);
				} else {
					DB::run('INSERT INTO '.DB_PREFIX.PREFIX.'visitor_keywords VALUES(NULL, '.e($keyword).', '.e($engine).', '.e($referer).', '.time().', 1)');
					$keyword_id = DB::id();
				}
			}
		}
	}
	
	public static function getAgentID($agent = false) {
		if (!$agent) $agent = $_SERVER['HTTP_USER_AGENT'];
		$ret = DB::row('SELECT k FROM '.DB_PREFIX.'db WHERE t=6 AND v='.e($agent),'k');
		if (!$ret) {
			$max = DB::row('SELECT MAX(k+1) AS m FROM '.DB_PREFIX.'db WHERE t=6','m');
			if ($max) $ret = $max; else $ret = 1;
			DB::noerror();
			DB::run('INSERT INTO '.DB_PREFIX.'db (t,k,v) VALUES (6,'.$ret.','.e($agent).')');
		}
		return $ret;
	}
	
	public static function isRobot() {
		if (defined('ROBOT')) return ROBOT;
		$is = self::getDB(3, $_SERVER['HTTP_USER_AGENT']);
		define('ROBOT', (bool)$is);
		define('IS_ROBOT', (bool)$is);
		return $is;
	}
	public static function getSearchEngine($k) {
		$db = self::getDB(4, $k);
		return ($db ? $db[0] : array());
	}
	public static function getDB($t, $k = '', $v = '',$kv = '') {
		if ($t<=3 && $k) {
			if ($t==3 || $t==1) {
				$sql = 'SELECT v FROM '.DB_PREFIX.'db WHERE t='.$t.' AND k='.e($k).'';
			} else {
				$sql = 'SELECT v FROM '.DB_PREFIX.'db WHERE t='.$t.' AND '.e($k).' LIKE CONCAT(\'%\',k,\'%\')';	
			}
			return DB::one($sql);
		} else {
			return DB::getAll('SELECT * FROM '.DB_PREFIX.'db WHERE t='.$t.($k ? ' AND k='.e($k) : ($v?' AND v='.e($v):'')),$kv);
		}
	}
	
	public static function os($a = NULL) {
		if (!$a) $a = $_SERVER['HTTP_USER_AGENT'];
		return self::getDB(1, $a);
	}
	
	public static function device($a = false) {
		if (!$a) $a = $_SERVER['HTTP_USER_AGENT'];
		require_once FTP_DIR_ROOT.'inc/lib/Mobile_Detect.php';
		$d = new Mobile_Detect($_SERVER, $a);
		if ($d->isTablet()) $r = 'tablet';	
		elseif ($d->isMobile()) $r = 'mobile';
		else $r = 'pc';
		return $r;
	}
	
	public static function browser($a = NULL) { 
		if (!$a && isset($_SESSION['Browser'])) return $_SESSION['Browser'];
		if (!$a) $a = $_SERVER['HTTP_USER_AGENT'];
		$b = $v = $u = '';
		if(preg_match('/MSIE/i',$a) && !preg_match('/Opera/i',$a) && ($u = 'MSIE')) $b = 'IE';
		elseif(preg_match('/Firefox/i',$a) && ($u = 'Firefox')) $b = 'FF';
		elseif(preg_match('/Chrome/i',$a) && ($u = 'Chrome')) $b = 'CR';
		elseif(preg_match('/Safari/i',$a) && ($u = 'Safari')) $b = 'SF'; 
		elseif(preg_match('/Opera/i',$a) && ($u = 'Opera')) $b = 'OP';
		elseif(preg_match('/Netscape/i',$a) && ($u = 'Netscape')) $b = 'NS';
		elseif ((preg_match_all('/(mozilla)[\/\sa-z;.0-9-(]+rv:([0-9]+)([.0-9a-z]+)\) gecko\/[0-9]{8}$/i', $a, $r)) || (preg_match_all("/($b)[\/\sa-z(]*([0-9]+)([\.0-9a-z]+)?/i", $a, $r))) {
			$browsers = self::getDB(2,'','','k|v');
			$count = count($r[0])-1;		
			$name = @$browsers[strtolower($r[1][$count])];
			$major_number = @$r[2][$count];
			$match = array();		
			preg_match('/([.\0-9]+)?([\.a-z0-9]+)?/i', $r[3][$count], $match);		
			if(isset($match[1])) {
				$minor_number = substr($match[1], 0, 2);
			} else 	{
				$minor_number = '.0';
			}
			$v = $major_number.$minor_number;
		}
		if (!$v) {
			$k = array('Version', $u, 'other');
			if (!@preg_match_all('#(?<browser>'.join('|', $k).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#', $a, $m)) {
				// we have no matching number just continue
			}
			$i = count(@$m['browser']);
			if ($i != 1) {
				if (strripos($a,'Version') < strripos($a,$u)) $v = $m['version'][0];
				else $v = $m['version'][1];
			}
			else $v = $m['version'][0];
		}
		$ret = array($b,$v);
		if (!$a) $_SESSION['Browser'] = $ret;
		return $ret;
	}
}