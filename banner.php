<?php

class Banner {
	private static 
		$conn = false,
		$prefix = ''
	;
	
	private $types = array(
		'html','swf','jpg'
	);
	
	public 
		$id = 0,
		$click = 0,
		$type = false,
		$lang = 'en',
		$size = '468x60',
		$iframe = false,
		$ext = 'jpg',
		$file = '',
		$w = 0,
		$h = 0
	;
	
	static $campaigns = array(
		'adamantfinance-megbank' => 1,
		'adamantfinance-partners' => 2,
		'adamantfinance-komanda' => 3,
		'welcome50'	=> 4,
		'freeVPS'	=> 5
	);
	
	static $urls = array(
		'adamantfinance-megbank' => 'https://adamantfinance.com/ru/trading/real-accounts',
		'adamantfinance-partners' => 'https://adamantfinance.com/ru/for-partner/partner-types',
		'adamantfinance-komanda' => 'https://adamantfinance.com/ru/investments',
		'welcome50'	=> 'https://adamantfinance.com/ru/campaigns/welcome-bonus',
		'freeVPS'	=> 'https://adamantfinance.com/ru/campaigns/EA-trading'
	);
	
	static $banner_sizes = array(
		'468x60','428x60','728x90','990x90','240x400','200x300','120x600','160x600','250x250','360x300'
	);
	static $banner_langs = array('ru');
	static $db = '';
	
	
	private static function run($sql) {
		
		$qry = self::$conn->query($sql) or die(self::$conn->error);
		return $qry;	
	}
	private static function row($sql) {
		$qry = self::run($sql.' LIMIT 1');
		$ret = $qry->fetch_assoc();
		return $ret;
	}
	private function e($s) {
		return '\''.self::$conn->real_escape_string($s).'\'';
	}
	private function one($sql) {
		$sql = $sql.' LIMIT 1';
		$qry = self::run($sql);
		$row = $qry->fetch_row();
		$qry->free();
		return $row[0];
	}
	public static function id() {
		return self::$conn->insert_id;
	}
	private static function getHost($ip) {
		$u = 'unknown';
		if (!$ip) return $u;
		$ret = trim(strtolower(@gethostbyaddr($ip)));
		return $ret;
	}
	private static function getFirstIpFromList($ip) {
		$p = strpos($ip, ',');
		if($p!==false) return substr($ip, 0, $p); else return $ip;
	}

	private static function getIP() {
		if (isset($_SESSION['IP'])) return $_SESSION['IP'];
		if(isset($_SERVER['HTTP_CLIENT_IP']) && ($ip = self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP'])) && strpos($ip, 'unknown')===false && self::getHost($ip) != $ip) $ret = $ip;
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $ip = self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']) && isset($ip) && !empty($ip) && strpos($ip, 'unknown')===false && getHost($ip) != $ip) $ret = $ip;
		elseif(isset($_SERVER['HTTP_CLIENT_IP']) && strlen(self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']))!=0) $ret = self::getFirstIpFromList($_SERVER['HTTP_CLIENT_IP']);
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen(self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']))!=0) $ret = self::getFirstIpFromList($_SERVER['HTTP_X_FORWARDED_FOR']);
		else $ret = self::getFirstIpFromList($_SERVER['REMOTE_ADDR']);
		$ret = $ret?$ret:'127.0.0.1';
		$_SESSION['IP'] = $ret;
		return $ret;
	}
	
	public function __construct() {
		error_reporting(E_ALL ^ E_NOTICE);
		ini_set('magic_quotes_runtime', 0);
		ini_set('display_errors', 1);
		ini_set('log_errors', 1);
		ini_set('iconv.internal_encoding', 'utf-8');
		session_name('PHPSESSID');
		session_start();
		define ('DOMAIN',strtolower(str_replace('www.','',$_SERVER['HTTP_HOST'])));
		if (isset($_GET['reset'])) $_SESSION = array();
	}
	public function connect($prefix = '', $db = false) {
		self::$db = $db;
		require 'config/config.php';
		if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') require 'alx/p.php';
		self::$prefix = $prefix;
		if (!defined('DB_HOST')) {
			if (!$db) die('No database selected');
			define('DB_HOST',$_conf['DB'][$db]['HOST']);
			define('DB_USERNAME',$_conf['DB'][$db]['USERNAME']);
			define('DB_PASSWORD',$_conf['DB'][$db]['PASSWORD']);
			define('DB_NAME',$_conf['DB'][$db]['NAME']);
		}
		self::$conn = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
		self::$conn->query('SET NAMES \'utf8\'');
		
		if (isset($_COOKIE['Country']) && !isset($_SESSION['Country'])) $_SESSION['Country'] = $_COOKIE['Country'];
		if (!isset($_SESSION['Country'])) {
			require 'geo/geoip.php';
			$gi = geoip_open('geo/GeoIP.dat', GEOIP_STANDARD);
			$code = geoip_country_code_by_addr($gi, self::getIP());
			$_SESSION['Country'] = $code;
			setcookie('Country',$code, time() + (60 * 60 * 24 * 365));
			geoip_close($gi);
		}
	}
	
	public function device() {
		$a = $_SERVER['HTTP_USER_AGENT'];
		require_once 'inc/lib/Mobile_Detect.php';
		$d = new Mobile_Detect($_SERVER, $a);
		if ($d->isTablet()) {
			$device = 'tablet';	
		}
		elseif ($d->isMobile()) {
			$device = 'mobile';
		}
		else {
			$device = 'pc';
		}
		return $device;	
	}
	
	
	public function select() {
		if ($this->click) $this->id = $this->click;
		if (!$this->id) {
			echo 'Wrong parameters. No ID declared';
			exit;
		}
		if ($this->type && !in_array($this->type, $this->types)) {
			$this->type = false;	
		}
		if (isset($_GET['reset'])) $_SESSION = array();
		$this->banner = self::row('SELECT * FROM '.self::$prefix.'banners WHERE id='.$this->id);
		if (!$this->banner) {
			echo 'Wrong parameters. This banner does not exist or it has been deleted';
			exit;
		}
		if (!$this->lang) $this->lang = $this->banner['lang'];
		elseif (!in_array($this->lang,self::$banner_langs)) {
			$this->lang = key(self::$banner_langs);
		}
		if (!$this->size) $this->size = $this->banner['size'];
		if (!in_array($this->size, self::$banner_sizes)) {
			echo 'Wrong parameters. This banner size '.$this->size.' is not in use';
			exit;
		}
		

		if ($this->click && $_SESSION['viewid']) {
			
			$sql = 'UPDATE '.self::$prefix.'banners SET clicks=clicks+1 WHERE id='.$this->id.'';
			self::run($sql);
			
			if (!($id = self::one('SELECT id FROM '.self::$prefix.'clicks WHERE setid='.$this->id.' AND ip='.self::e(ip2long(self::getIP())).' AND clicked > '.(time()-3600)))) {					
				$sql = 'INSERT INTO '.self::$prefix.'clicks (setid, clicked, country, userid,  ip) VALUES ('.$this->id.', '.time().', '.self::e($_SESSION['Country']).', '.$this->banner['userid'].', '.self::e(ip2long(self::getIP())).')';
				self::run($sql);
				$_SESSION['clickid'] = self::id();
				if ($_SESSION['viewid']) {
					$id = self::id();
					self::run('UPDATE '.self::$prefix.'views SET clickid='.$id.' WHERE id='.$_SESSION['viewid']);
				}
			} else {
				$_SESSION['clickid'] = $id;	
			}
			$lines = file('banners/links.txt');
			foreach ($lines as $line) {
				$ex = explode(' ',$line);
				if ($ex[0]==$this->banner['campaign']) {
					$url = $ex[1];
					break;	
				}
			}
			@session_write_close();
			header ('Location: '.$url.'rf='.$this->banner['userid'].'/a='.$_SESSION['clickid']);
			exit;
		}

		if ($this->banner['way']==0) {
			$dir = 'banners/'.$this->lang.'/';
			if (!is_dir($dir)) $dir = 'banners/ru/';
			$dh = opendir($dir);
			$ways = array();
			$n = $this->banner['size'];
			while ($b = readdir($dh)) {
				if ($b=='.' || $b=='..') continue;
				if (strstr($b,$n)) {
					$ex = explode('.',$b);
					$ext = end($ex);
					if ($this->type && $this->type!=$ext) continue;
					$way = substr(str_replace('.'.$ext,'',$b),-1);
					if (!is_numeric($way)) $way = 0;
					$name = trim(str_replace('.'.$ext,'',str_replace($this->banner['size'],'',$b)),'-');
					$ways[] = $name.':'.$way.':'.$ext;
				}
			}
			shuffle($ways);
			$this->banner['way'] = $ways[0];
		}
		elseif ($this->banner['way']==1 && $this->banner['ways']) {
			$ways = explode(',',$this->banner['ways']);
			shuffle($ways);
			$this->banner['way'] = $ways[0];
		}		
		$ex = explode(':',$this->banner['way']);
		if ($ex[2]) {
			$this->campaign = $ex[0];
			$this->way = $ex[1];
			$this->ext = $ex[2];
		} else {
			echo 'Wrong parameters. Cannot select the banner. Banner extension is missing in database';
			exit;
		}
		
		if (!$_SESSION['viewid']) {
			if (!($id = self::one('SELECT id FROM '.self::$prefix.'views WHERE setid='.$this->id.' AND ip='.self::e(ip2long(self::getIP())).' AND viewed>'.(time()-3600)))) {
				$ex = explode('/',$_SERVER['HTTP_REFERER']);
				$domain = $ex[2];
				$sql = 'UPDATE '.self::$prefix.'banners SET views=views+1 WHERE id='.$this->id.'';
				self::run($sql);
				$sql = 'INSERT INTO '.self::$prefix.'views (setid, viewed, userid, ip, device, referer, domain, country, campaign, way, ext) VALUES ('.$this->id.', '.time().', '.$this->banner['userid'].', '.self::e(ip2long(self::getIP())).', '.self::e(self::device()).', '.self::e($_SERVER['HTTP_REFERER']).', '.self::e($domain).', '.self::e($_SESSION['Country']).', '.self::e(self::$campaigns[$this->campaign]).', '.(int)$this->way.', '.self::e($this->ext).')';
				self::run($sql);
				$_SESSION['viewid'] = self::$conn->insert_id;
			} else {
				$_SESSION['viewid'] = $id;
			}
		}
		@session_write_close();
		if ($this->click) {
			// ='.(int)$_GET['click']
			header('Location: http://'.DOMAIN.'/banner.php?click='.$this->id.'');
			exit;
		}
		
		$ex = explode('x',$this->size,2);
		$this->w = $ex[0];
		$this->h = $ex[1];
		
		// ='.$this->banner['way']
		$this->banner['url'] = 'http://'.DOMAIN.'/banner.php?click='.$this->id.'';
		$this->file = 'banners/'.$this->lang.'/'.$this->size.'-'.$this->campaign.($this->way?'-'.$this->way:'').'.'.$this->ext;
		if (!is_file($this->file)) {
			$this->file = 'banners/ru/'.$this->size.'-'.$this->campaign.($this->way?'-'.$this->way:'').'.'.$this->ext;	
		}
		if (!is_file('./'.$this->file)) {
			echo 'Wrong parameters. File '.$this->file.' is missing';
			exit;
		}
		
		
		$this->banner['file_url'] = '//'.DOMAIN.'/'.$this->file;
		return $this;
	}
	
	public function write() {
		
		if ($this->ext=='html') {
			print '<!doctype html><html>
<head>
	<title>'.$this->banner['title'].'</title></head><body>';
			print '<a href="'.$this->banner['url'].'" target="_blank" title="'.htmlspecialchars($this->banner['title']).'" style="display:block;width:'.$this->w.'px;height:'.$this->h.'px;position:absolute;left:0;top:0;z-index:1"><img src="/tpls/img/1x1.gif" width="'.$this->w.'" height="'.$this->h.'" /></a>';
			print '<div style="position:absolute;left:0;top:0;z-index:0">';
			readfile($this->file);
			print '</div></body></html>';
			return;	
		}
		
		$_h = '';
		$h = '<!doctype html><html>
<head>
	<meta charset="utf-8">
	<title>'.$this->banner['title'].'</title>';
		if ($this->ext=='swf') {
			$_h .= '
	<script src="/tpls/js/swfobject_modified.js" type="text/javascript"></script>';
		}
		$h .= '
	<style>*{padding:0;margin:0;border:none;overflow:hidden;}</style>
</head>
<body>
';
		
		if ($this->ext=='swf') {
			$h .= '<a href="'.$this->banner['url'].'" target="_blank" title="'.htmlspecialchars($this->banner['title']).'" style="display:block;width:'.$this->w.'px;height:'.$this->h.'px;position:absolute;left:0;top:0;z-index:1"><img src="/tpls/img/1x1.gif" width="'.$this->w.'" height="'.$this->h.'" /></a>';
			$h .= '<div style="position:absolute;left:0;top:0;z-index:0">';
			/*
			$_h .= '<object id="'.self::$db.'-ad_'.$this->banner['id'].'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$this->w.'" height="'.$this->h.'">
	<param name="movie" value="'.$this->banner['file_url'].'">
	<param name="quality" value="high">
	<param name="wmode" value="opaque">
	<param name="swfversion" value="9.0.115.0">
	<param name="expressinstall" value="/tpls/js/expressInstall.swf">
	<!--[if !IE]>-->
	<object type="application/x-shockwave-flash" data="'.$this->banner['file_url'].'" width="'.$this->w.'" height="'.$this->h.'">
		<!--<![endif]-->
		<param name="quality" value="high">
		<param name="wmode" value="opaque">
		<param name="swfversion" value="9.0.115.0">
		<param name="expressinstall" value="/tpls/js/expressInstall.swf">
		<div>
			<h4>Content on this page requires a newer version of Adobe Flash Player.</h4>
			<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" width="112" height="33" /></a></p>
		</div>
		<!--[if !IE]>-->
	</object>
	<!--<![endif]-->
</object>';
			*/
			
			$h .= '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="'.self::$db.'-ad_'.$this->banner['id'].'" width="'.$this->w.'" height="'.$this->h.'" align="middle">
	<param name="movie" value="'.$this->banner['file_url'].'" />
	<param name="quality" value="high" />
	<param name="bgcolor" value="#ffffff" />
	<param name="play" value="true" />
	<param name="loop" value="true" />
	<param name="wmode" value="window" />
	<param name="scale" value="showall" />
	<param name="menu" value="false" />
	<param name="devicefont" value="false" />
	<param name="salign" value="" />
	<param name="allowScriptAccess" value="sameDomain" />
	<!--[if !IE]>-->
	<object type="application/x-shockwave-flash" data="'.$this->banner['file_url'].'" width="'.$this->w.'" height="'.$this->h.'">
		<param name="movie" value="'.$this->banner['file_url'].'" />
		<param name="quality" value="high" />
		<param name="bgcolor" value="#ffffff" />
		<param name="play" value="true" />
		<param name="loop" value="true" />
		<param name="wmode" value="window" />
		<param name="scale" value="showall" />
		<param name="menu" value="false" />
		<param name="devicefont" value="false" />
		<param name="salign" value="" />
		<param name="allowScriptAccess" value="sameDomain" />
	<!--<![endif]-->
		<a href="http://www.adobe.com/go/getflash">
			<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Загрузить Adobe Flash Player" />
		</a>
	<!--[if !IE]>-->
	</object>
	<!--<![endif]-->
</object>';
			
			$h .= '</div>';
			$_h .= '
<script type="text/javascript">
swfobject.registerObject("'.self::$db.'-ad_'.$this->banner['id'].'");
</script>';
		} else {
			$h .= '<a href="'.$this->banner['url'].'" target="_blank" title="'.htmlspecialchars($this->banner['title']).'" style="display:block;width:'.$this->w.'px;height:'.$this->h.'px;">';
			$h .= '<img src="'.$this->banner['file_url'].'" alt="'.htmlspecialchars($this->banner['title']).'" width="'.$this->w.'" height="'.$this->h.'" />';
			$h .= '</a>';
		}
		$h .= '
</body>
</html>';
		print $h;
	}

}


$banner = new Banner();
$banner->id = intval($_GET['id']);
$banner->size = $_GET['size'];
$banner->click = $_GET['click'];
$banner->type = $_GET['type'];
$banner->lang = $_GET['lang'];
$banner->connect('adamant_','adamant');
$banner->select()->write();