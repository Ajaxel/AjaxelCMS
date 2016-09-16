<?php

class Embed {
	
	private static 
		$conn = false
	;
	private $name, $i, $bg;
	
	private static function run($sql) {
		$qry = mysql_query($sql, self::$conn) or die(mysql_error());
		return $qry;	
	}
	private static function row($sql) {
		$qry = self::run($sql.' LIMIT 1');
		$ret = mysql_fetch_assoc($qry);
		return $ret;
	}
	private function one($sql) {
		$sql = $sql.' LIMIT 1';
		$qry = self::run($sql);
		$row = mysql_fetch_array($qry,MYSQL_NUM);
		mysql_free_result($qry);
		return $row[0];
	}

	
	public function __construct() {
		session_start();
		$this->name = $_GET['name'];
		if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {
			self::$conn = mysql_connect('127.0.0.1','root','');
			mysql_select_db('dj', self::$conn);
		} else {
			self::$conn = mysql_connect('localhost','djrankingsorg','x7FL4vV2MdusNfxU');
			mysql_select_db('djrankingsorg', self::$conn);
		}
		$this->bg = '#005596';
		$this->img = './tpls/dj/dj-logo-small.png';
		$this->name = str_replace('_and_','_&_',$this->name);
		if ($this->name) $this->row = self::row('SELECT id, rank, title, country FROM dj_grid_djs WHERE name=\''.mysql_real_escape_string($this->name).'\'');
		if (!$this->row) $this->row = array('title' => 'Not found', 'rank' => '-999');
		
		if ($_GET['country']) {
			$this->row['country_name'] = self::one('SELECT name FROM djs_country WHERE code=\''.mysql_real_escape_string($this->row['country']).'\'');
			if ($this->row['country_name']) {
				$this->row['rank'] = self::one('SELECT COUNT(1) FROM dj_grid_djs WHERE country=\''.mysql_real_escape_string($this->row['country']).'\' AND rank>0 AND rank <= '.$this->row['rank']);
			} else {
				$_GET['country'] = false;
			}
		}
		
	}
	
	private static function setcookie($name, $value, $days = 0) {
		if (!headers_sent()) {
			if ($days > 0) $expires = time() + (60 * 60 * 24 * $days);
			else $expires = 0;
			setcookie($name, $value, $expires, '/');
		} else {
			exit;	
		}
	}
	
	public function save($ref) {
		if (!$ref || $_SESSION['seen'] || strstr($ref,'http://djrankings.org') || strstr($ref,'www.google')
		 || strstr($ref,'bing.com') || strstr($ref,'http://dj-rankings.com') ||  @$_COOKIE['dj_seen']) return $this;
		self::setcookie('dj_seen', true, 5);
		self::setcookie('dj_seen_id', $this->row['id'], 1);
		$_SESSION['seen'] = true;
		$f = 'embed.txt';
		$t = filectime($f);
		if ($t < time() - 86400*24) {
			unlink($f);touch($f);
		}
		$fp = fopen($f, 'ab');
		fwrite($fp, time()."\t".$this->row['id']."\t".$ref."\t".$this->name."\t".$this->row['title']."\t".$_SERVER['REMOTE_ADDR']."\t"."\n");
		fclose($fp);
		
		return $this;
	}
	
	
	public function write() {
		$i = imagecreatefrompng($this->img);
		$color = imagecolorallocate($i, 255, 216, 0);
		$color2 = imagecolorallocate($i, 255, 255, 255);
		///imagestring($i,12,140,15,'Official ranking',$color);
		//imagestring($i,15,146,52,$this->row['rank'],$color);
		
		imagefttext($i, 19, 0, 150, 25, $color, './config/fonts/MyriadPro-Semibold.ttf', $this->row['title'], array());
		imagefttext($i, 15, 0, 150, 59, $color, './config/fonts/MyriadPro-Light.ttf', ($this->row['country_name'] ? 'RANKING '.strtoupper($this->row['country_name']):'GLOBAL RANKING'), array());
		imagefttext($i, 30, 0, 170, 113, $color2, './config/fonts/MyriadPro-Semibold.ttf', $this->row['rank'], array());
				
		header('Content-type: image/png'); 
		imagepng($i); 

	}
	
	
	
	
	
	
	
	
}

if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') @include 'alx/p.php';

$e = new Embed;
$e->save($_SERVER['HTTP_REFERER'])->write();