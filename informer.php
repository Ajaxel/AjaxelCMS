<?php

class Database {
	private static 
		$db,
		$db2
	;
	protected static
		$conn
	;
	
	protected static function connect() {
		require 'config/config.php';
		if (!defined('DB_HOST')) {
			define ('DB_HOST', '127.0.0.1');
			define ('DB_USERNAME', 'root');
			define ('DB_PASSWORD', '');
			define ('DB_NAME', 'adamant');
		}
		self::$db = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
		self::$db->query('SET NAMES \'utf8\'');
		self::$conn =& self::$db;
	}
	protected static function change($to) {
		if ($to==2) self::$conn =& self::$db2;
		else elf::$conn =& self::$db;
	}
	protected static function run($sql) {
		$qry = @self::$conn->query($sql);
		if (!$qry) self::err(self::$conn->error);
		return $qry;	
	}
	protected static function row($sql) {
		$qry = self::run($sql.' LIMIT 1');
		if (!$qry) self::err(self::$conn->error);
		$ret = $qry->fetch_assoc();
		return $ret;
	}
	protected static function all($sql,$sel=false,$func=false) {
		$qry = self::$conn->query($sql);
		if (!$qry) self::err(self::$conn->error);
		$ret = array();
		if ($sel) {
			if (strstr($sel,'|')) {
				$ex = explode('|',$sel);
				if ($ex[1]=='[[:ARRAY:]]') {
					while($row = $qry->fetch_assoc()) {
						$k = $row[$ex[0]];
						if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
						$ret[$k] = $row;
					}			
				} 
				elseif ($ex[0]=='[[:INDEX:]]') {
					while($row = $qry->fetch_assoc()) {
						if ($func) {
							if (!($row = call_user_func_array($func,array($row[$ex[1]])))) continue;
							array_push($ret, $row);
						}
						else array_push($ret, $row[$ex[1]]);
					}
				}
				else {
					while($row = $qry->fetch_assoc()) {
						if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
						$ret[$row[$ex[0]]] = $row[$ex[1]];
					}
				}
			} else {
				while($row = $qry->fetch_assoc()) {
					if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
					array_push($ret,$row[$sel]);
				}
			}
		} else {
			while($row = $qry->fetch_assoc()) {
				if ($func) if (!($row = call_user_func_array($func,array($row)))) continue;
				array_push($ret,$row);
			}
		}
		$qry->free();
		return $ret;
	}
	protected static function e($s, $addapos = true) {
		if (is_array($s)) {
			$n = array();
			foreach ($s as $_s) {
				array_push($n, self::e($_s, $addapos));
			}
			return $n;
		}
		else return ($addapos ? '\'':'').self::$conn->real_escape_string($s).($addapos ? '\'' : '');
	}

	protected static function one($sql) {
		$sql = $sql.' LIMIT 1';
		$qry = self::run($sql);
		$row = $qry->fetch_row();
		$qry->free();
		return $row[0];
	}
	protected static function id() {
		return self::$conn->insert_id;
	}
	protected static function err($t) {
		die('<strong style="color:red">Error</strong><br> <em>'.$t.'</em>');	
	}
	
	protected static function url($url, $postdata = false, $use_cookie = false, $loop = 0, $timeout = 5) {	
		$url = str_replace('&amp;', '&',trim($url));
		if (!function_exists('curl_init')) return 'CURL library is not installed.';
		$ch = curl_init();
		$a = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
		curl_setopt($ch, CURLOPT_USERAGENT, $a);
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($use_cookie) {
			if ($use_cookie===1) {
				$cookie = tempnam('/tmp', 'CURL');
				curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
			}
			elseif ($use_cookie===2) {
				curl_setopt($ch, CURLOPT_COOKIESESSION, true);
				curl_setopt($ch, CURLOPT_COOKIEJAR, FTP_DIR_ROOT.'cookie.txt');
			}
			else {
				if (is_array($use_cookie)) {
					curl_setopt($ch, CURLOPT_COOKIESESSION, true);
					curl_setopt($ch, CURLOPT_COOKIE, is_array($use_cookie[0]) ? join('&',$use_cookie[0]) : $use_cookie[0]);
				} else {
					curl_setopt($ch, CURLOPT_COOKIE, $use_cookie);
				}
			}
		}
		
		if ($postdata) {
			if (is_array($postdata)) $postdata = http_build_query($postdata);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, '&'.ltrim($postdata,'&'));
		}
		curl_setopt($ch, CURLOPT_ENCODING, 'utf-8');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
		$content = curl_exec($ch);
		$response = curl_getinfo($ch);
		curl_close($ch);
		if ($response['url'] && $response['http_code']==400 && !$loop) {
			ini_set('user_agent', $a);
			return self::url($response['url'], $postdata, $use_cookie, $loop+1, $timeout);
		}
		elseif ($response['url'] && $response['http_code']==301 || $response['http_code']==302) {
			ini_set('user_agent', $a);
			if ($headers = @get_headers($response['url'])) {
				foreach($headers as $value ) {
					if (substr(strtolower($value), 0, 9)=='location:') return self::url(trim(substr($value, 9, strlen($value))), $postdata, $use_cookie, $loop+1, $timeout);
				}
			}
		}
		return $content;
	}
	
	protected static function xml2array($contents, $get_attributes=1, $priority = 'tag') { 
		if (!$contents) return array(); 
		$parser = xml_parser_create(''); 
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
		xml_parse_into_struct($parser, trim($contents), $xml_values); 
		xml_parser_free($parser); 
		if (!$xml_values) return array();
		$xml_array = array(); 
		$parents = array(); 
		$opened_tags = array(); 
		$arr = array(); 
		$current = &$xml_array;
		$repeated_tag_index = array();
		foreach($xml_values as $data) { 
			unset($attributes,$value);
			extract($data);
			$result = array(); 
			$attributes_data = array(); 
			if (isset($value)) {
				if ($priority == 'tag') $result = $value; 
				else $result['value'] = $value;
			} 
			if (isset($attributes) and $get_attributes) { 
				foreach($attributes as $attr => $val) { 
					if ($priority == 'tag') $attributes_data[$attr] = $val; 
					else $result['attr'][$attr] = $val;
				} 
			} 
			if ($type == "open") {
				$parent[$level-1] = &$current; 
				if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
					$current[$tag] = $result; 
					if ($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
					$repeated_tag_index[$tag.'_'.$level] = 1;
					$current = &$current[$tag];
				} else {
					if (isset($current[$tag][0])) {
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
						$repeated_tag_index[$tag.'_'.$level]++; 
					} else {
						$current[$tag] = array($current[$tag],$result);
						$repeated_tag_index[$tag.'_'.$level] = 2; 
						 
						if (isset($current[$tag.'_attr'])) {
							$current[$tag]['0_attr'] = $current[$tag.'_attr']; 
							unset($current[$tag.'_attr']); 
						} 
					} 
					$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
					$current = &$current[$tag][$last_item_index]; 
				} 
	
			} elseif ($type == "complete") {
				if (!isset($current[$tag])) {
					$current[$tag] = $result; 
					$repeated_tag_index[$tag.'_'.$level] = 1; 
					if ($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
				} else {
					if (isset($current[$tag][0]) and is_array($current[$tag])) {
						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
						if ($priority == 'tag' and $get_attributes and $attributes_data) { 
							$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
						} 
						$repeated_tag_index[$tag.'_'.$level]++; 
					} else {
						$current[$tag] = array($current[$tag],$result);
						$repeated_tag_index[$tag.'_'.$level] = 1; 
						if ($priority == 'tag' and $get_attributes) { 
							if (isset($current[$tag.'_attr'])) {
								$current[$tag]['0_attr'] = $current[$tag.'_attr']; 
								unset($current[$tag.'_attr']); 
							} 
							if ($attributes_data) { 
								$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
							} 
						} 
						$repeated_tag_index[$tag.'_'.$level]++;
					} 
				}
			} elseif ($type == 'close') {
				$current = &$parent[$level-1]; 
			} 
		}
		return($xml_array); 
	}
}


class MQ extends Database {
	static $prev = array();
	static $prev_s = array();
	static $today = array();
	
	static $folders = array(
		'Classic','MarketPro','MarketPrime','CopyFx','SignalFx','Bonus50'
	);
	
	public static function getSet($s) {
		
		
		if (!$s || !in_array($s, self::$folders)) $s = 'Classic';
		if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {
			$file = './tpls/adamants/classes/instruments/'.$s.'.txt';
		} else {
			$file  = '/var/www/alx/data/www/adamantfinance.com/tpls/adamants/classes/instruments/'.$s.'.txt';	
		}
		$_GET['s'] = $s;
		if (!is_file($file)) {
			self::err($file.' file was not found');	
		}
		$lines = file($file);
		
		$set = array();
		$group = false;
		$name = 'Forex';
		$next_name = false;
		$set[$name] = array();
		$quotes = array();
		foreach ($lines as $i => $l) {
			if (!$i || !strlen(trim($l))) continue;
			if ($next_name) {
				$name = trim($l);
				$set[$name] = array();
				$next_name = false;
				continue;
			}
			if (substr($l,0,2)=='--') {
				$next_name = true;
				continue;
			}
			if (strstr($l,'***')) {
				$group = trim($l);
				continue;
			}
			if ($name=='Forex') {
				$set[$name][$group][] = trim($l);
				$quotes[] = trim($l);
			} else {
				$set[$name][] = trim($l);
				$quotes[] = trim($l);
			}
		}	
		return array($set, $quotes);
	}
	
	protected function quotes($quotes, $by_set = false) {
		
		if ($by_set) {
			list ($set, $quotes) = self::getSet($quotes);
		}
		if (!is_array($quotes)) return false;
		
		$dir = './tpls/adamant/';
		$file_prev = $dir.'data/quotes_prev'.$suffix.'.txt';
		$file_spread = $dir.'data/quotes_spread'.$suffix.'.txt';
		$file_today = $dir.'data/quotes'.$suffix.'_'.date('d-m-Y').'.txt';
		$file_yesterday = $dir.'data/quotes'.$suffix.'_'.date('d-m-Y',time()-86400).'.txt';
		
		self::$prev = self::$today = array();
		if (is_file($file_prev)) {
			$lines = file($file_prev);
			foreach ($lines as $line) {
				$ex = explode(' ',$line);
				self::$prev[trim($ex[0])] = trim($ex[1]);
			}
		}
		if (is_file($file_spread)) {
			$lines = file($file_spread);
			foreach ($lines as $line) {
				$ex = explode(' ',$line);
				self::$prev_s[trim($ex[0])] = floatval(trim($ex[1]));
			}
		}
		
		if (is_file($file_today)) {
			$lines = file($file_today);
			foreach ($lines as $line) {
				$ex = explode(' ',$line);
				self::$today[trim($ex[0])] = array(trim($ex[1]),trim($ex[2]));
			}
		}
		
		$ret = self::all('SELECT * FROM mt4_prices WHERE SYMBOL IN (\''.join('\', \'',self::e($quotes, false)).'\')','SYMBOL|[[:ARRAY:]]',function($row) {
			if ($row['BID'] > MQ::$prev[$row['SYMBOL'].'-BID']) $row['BID_c'] = 'green';
			elseif ($row['BID'] < MQ::$prev[$row['SYMBOL'].'-BID']) $row['BID_c'] = 'orange';
			else $row['BID_c'] = '';
			if ($row['ASK'] > MQ::$prev[$row['SYMBOL'].'-ASK']) $row['ASK_c'] = 'green';
			elseif ($row['ASK'] < MQ::$prev[$row['SYMBOL'].'-ASK']) $row['ASK_c'] = 'orange';
			else $row['ASK_c'] = '';
			
			if ($row['SPREAD'] > MQ::$prev_s[$row['SYMBOL']]) $row['SPREAD_c'] = 'green';
			elseif ($row['SPREAD'] < MQ::$prev_s[$row['SYMBOL']]) $row['SPREAD_c'] = 'orange';
			else $row['SPREAD_c'] = '';
			
			if ($row['BID'] > MQ::$today[$row['SYMBOL']][0]) $row['MODIFY'] = number_format(100 - MQ::$today[$row['SYMBOL']][0] / $row['BID'] * 100,3,'.','');
			elseif ($row['BID'] < MQ::$today[$row['SYMBOL']][0]) $row['MODIFY'] = number_format(-100 + $row['BID'] / MQ::$today[$row['SYMBOL']][0] * 100,3,'.','');
			else $row['MODIFY'] = 0;
			
			return $row;
		});
		if ($by_set) return array($ret, $set);
		return $ret;
	}
	
	
	protected function calendar() {		
		$url = 'http://www.forexfactory.com/ffcal_week_this.xml';
		$h = self::url($url);
		$arr = self::xml2array($h);
		$ret = array();
		foreach ($arr['weeklyevents']['event'] as $e) {
			if (!$e['forecast']) continue;
			switch ($e['impact']) {
				case 'High':$s='3';break;
				case 'Medium':$s='2';break;
				case 'Low':$s='1';break;
				default:$s='0';break;
			}
			$a = 0;
			if (substr($e['time'],-2)=='pm') {
				$a = 12;
			}
			$e['time'] = substr($e['time'],0,-2);
			if ($a) {
				$ex = explode(':',$e['time']);
				$e['time'] = ($ex[0]+$a).':'.$ex[1];
			}
			$ex = explode('-',$e['date']);
			$e['date'] = $ex[1].'.'.$ex[0].'.'.$ex[2];
			$index = strtotime($e['date']);
		//	if ($curs_only && !in_array($e['country'],$curs_only)) continue;
		//	if ($imp_only && !in_array($s,$imp_only)) continue;
			$ret[$index][] = array(
				'time'	=> (string)$e['time'],
				'imp'	=> $s,
				'cur'	=> $e['country'],
				'title'	=> $e['title'],
				'p'	=> $e['previous'] ? $e['previous'] : 0,
				'f'=> $e['forecast'] ? $e['forecast'] : 0,
				's'=> $e['previous'] ? $e['previous'] : 0,
				's' => 0
			);
		}
		return $ret;
	}
	
	protected function quotes_all() {
		$indexes = array(
			'Metals' => 'm',
			'Oils' => 'o',
			'Indexes' => 'i'
		);
		
		$data = array();
		list ($_data, $set) = self::quotes($_GET['s'], true);
		foreach ($set as $k => $arr) {
			if ($k=='Forex') {
				foreach ($arr as $symbol => $symbols) {
					$s = substr($symbol,0,3);
					if ($_POST['f'] && is_array($_POST['f']) && $_POST['f'][0]!='all' && !in_array($s, $_POST['f'])) continue;
					foreach ($symbols as $_s) {
						if (!$_data[$_s]) continue;
						$data[$s][$_s] = $_data[$_s];
					}
				}
			} else {
				$s = $k;
				$key = $indexes[$s];
				foreach ($arr as $_s) {
					if (!$_data[$_s]) continue;
					if ($_POST[$key] && $_POST[$key][0]!='all' && !in_array($_s, $_POST[$key])) continue;
					$data[$s][$_s] = $_data[$_s];
				}
			}
		}
		return $data;
	}
}




class Informer extends MQ {
	const PATH = 'http://adamantfinance.com/tpls/adamants/';
	public
		$rf = 0,
		$which = 1,
		$lang = 'en',
		$color = 'grey',
		$width = 990,
		$height = 30,
		$cur = 'AUDUSD,CADCHF,EURAUD,EURGBP,EURJPY,EURUSD,EURRUB,GBPJPY,GBPUSD,NZDUSD,USDCHF,USDJPY,USDRUB'
	;
	private
		$curs = array(),
		$data = array(),
		$trans = false
	;
	
	public function __construct() {
		self::connect();	
	}
	
	
	public function fix() {
		if (!$this->which) $this->which = 1;
		if ($this->which==1 || $this->which==3) {
			if (!$this->cur || is_array($this->cur)) $this->cur = join(',',array('EURUSD','GBPUSD','USDJPY','USDCHF','GBPJPY','EURGBP','AUDUSD','NZDUSD','CADUSD','EURAUD','EURJPY','CADCHF','USDRUB','EURRUB'));
			if (!$this->height) $this->height = 250;
			if (!$this->width) {
				if ($this->which==1) $this->width = 900;
				else $this->width = 270;	
			}
		}
		if (!in_array($this->color, array('grey','blue','white','black'))) $this->color = 'grey';
		if (!$this->width || $this->width>1024) $this->width = 900;
		if ($this->height<50) $this->height = 50;
		if (!in_array($this->lang, array('ar','en','ru','ee','ch'))) $this->lang = 'en';
		
		return $this;	
	}
	
	protected function l($s) {
		if (!$this->trans) {
			$_lang = array();
			require './config/lang/lang_adamants_'.$this->lang.'.php';
			$this->trans = $_lang;
		}
		return isset($this->trans[$s]) ? $this->trans[$s] : $s;
	}

	public function select() {
		$this->curs = explode(',',$this->cur);
		switch ($this->which) {
			case 1:
				if (!$this->curs) self::err('No quotes in parameters');
				$this->data = self::quotes($this->curs);
				$this->which = 1;
			break;
			case 2:
				$this->data = self::quotes_all();
			break;
			case 3:
				if (!$this->curs) self::err('No quotes in parameters');
				$this->data = self::quotes($this->curs);
			break;
			case 4:
				$this->data = self::calendar();
			break;
			default:
				self::err('This format is not supported');
			break;
		}
		
		return $this;	
	}
	
	private function write_1() {
		$css = '.informer-marquee {
	width:100%;
	height:30px;
	overflow:hidden;
}

.informer-marquee span.first {
	position:relative;
	z-index:1;
	height:30px;
	margin:0;
	padding:0;
	background:#e6e6e6;
	border-right:0;
	display:inline-block;
	width:45px;
	height:30px;
	float:left;
}
.informer-marquee span.first>a {
	display:block;
	width:45px;
	height:30px;
	background:url('.self::PATH.'images/logo-mini.png);
}
.informer-marquee ul {
	float:left;
	width:800px;
	list-style:none;
	padding:0;margin:0;
	height:30px;
	background:#fff;
	overflow:hidden;
}
.informer-marquee ul li {
	float:left;
	height:20px;
	margin:5px 0;
	padding:0 6px;
	border-right:1px solid #999999;
	line-height:20px;
}
.informer-marquee ul li>div {
		
}
.informer-marquee ul li span {
	padding:0 3px;
	color:#666666;	
}
.informer-marquee ul li span.bid {
	color:#333333;	
}
.informer-marquee ul li span.green {
	color:#00cc00!important;
}
.informer-marquee ul li span.red {
	color:#ff3300!important;
}

.informer-marquee.informer-grey ul {
	background:#f7f6f6;
}
.informer-marquee.informer-black ul {
	background:#333333;
}
.informer-marquee.informer-black ul li.first {
	background:#000000;
}
.informer-marquee.informer-black ul li span {
	color:#cccccc;
}
.informer-marquee.informer-black ul li span.bid {
	color:#fff;
}

.informer-marquee.informer-blue ul {
	background:#0b4960;
}
.informer-marquee.informer-blue ul li.first {
	background:#0099cc;
}
.informer-marquee.informer-blue ul li span {
	color:#cccccc;
}
.informer-marquee.informer-blue ul li span.bid {
	color:#fff;
}';


		

		$h = '<div class="informer informer-marquee informer-'.$this->color.'">
	<span class="first"><a href="http://adamantfinance.com/'.($this->rf?'rf='.$this->rf:'').'" target="_blank" title="Adamant Finance"></a></span>
	<ul id="informer-marquee" style="width:'.($this->width-60).'px;">';
		foreach ($this->data as $q) {
		$h .= '<li id="s-'.$q['SYMBOL'].'">
			<span>'.$q['SYMBOL'].'</span>
			<span class="bid">'.$q['BID'].'</span>
			<span class="mod '.($q['MODIFY']>0?'green':'red').'">'.($q['MODIFY']>0?'+':'').''.$q['MODIFY'].'% '.($q['MODIFY']>0?'▲':'▼').'</span>
		</li>';
		}
	$h .= '</ul>
</div>';
		$h .= '
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="'.self::PATH.'js/jquery.marquee.min.js"></script>
<script>
function informer_1_ajax() {
	$.ajax({
		dataType:\'json\',
		url: \''.$_SERVER['REQUEST_URI'].'&get=json\',
		type: \'get\',
		error:function(a,c,e) {
			
		},
		success: function(data) {
			for (s in data) {
				var li=$(\'#s-\'+s);
				if (li.length) {
					li.find(\'span.bid\').html(data[s].BID);
					li.find(\'span.mod\').removeClass(\'red green\').addClass(data[s].MODIFY>0?\'green\':\'red\').html((data[s].MODIFY>0?\'+\':\'\')+data[s].MODIFY+\'% \'+(data[s].MODIFY>0?\'▲\':\'▼\'));
				}
			}
			setTimeout(function(){
				informer_1_ajax();
			},5000);
		}
	});
};
$(function(){
	$(\'#informer-marquee\').marquee({
		duration: 19000,
		gap: 10,
		delayBeforeStart: 100,
		direction: \'right\',
		duplicated: true,
		startVisible: true	
	});
	setTimeout(function(){
		informer_1_ajax();
	},5000);
});
</script>
';

		return array($css, $h);
	}
	
	
	
	private function write_2() {
		$css = '
td.l {
	text-align:left!important	
}
table.tr_inst {
	width:100%;
	cursor:default;
}
table.tr_inst tr.th th {
	background:#004A60;
	color:#fff;
	font-size:14px;
	text-align:center;
	padding:0 10px;
	font-weight:normal;
	height:36px;
	border-top:1px solid #9db6bf
	border-bottom:1px solid #9db6bf;
	border-left:1px solid #e2eaec
}
table.tr_inst tr.th td {
	background:#F5F5F5;
	text-align:center;
	padding:0 10px;
}
table.tr_inst tr.r td {
	text-align:center;
	border-bottom:1px solid #ededed;
	padding:0 10px;
	height:45px;
	font-size:14px;
	color:#666666;
}
table.tr_inst tr.r th div {
	height:20px;
}
table.tr_inst tr.r:hover td {
	background:#DDEBEF;
	color:#333;
}
table.tr_inst tr.r:hover th div {
	background:url('.self::PATH.'images/i-a.png) center no-repeat;
}
table.tr_inst tr.r td small {
	font-size:14px;	
}

table.tr_inst tr.r td div {
	height:40px;
	display:inline-block;
	padding:0 20px;
	line-height:40px;
}
table.tr_inst tr.r td div.green {
	background:#67DC68;
	outline:1px solid #d4f1c5;
	color:#fff;	
}

table.tr_inst tr.r td div.orange {
	background:#FF9785;
	outline:1px solid #fff8f7;
	color:#fff;	
}
table.tr_inst tr.l td {
	font-size:1px;	
}
table.tr_inst tr.l td div {
	height:10px;
	margin-top:5px;
	border-top:1px solid #adadad;
}

.tabs {
	height:46px;
	width:'.$this->width.'px;	
}
.tabs>a {
	display:block;
	height:35px;
	width:16.4%;
	background:#333 url('.self::PATH.'images/tab-btn.jpg);
	text-align:center;
	font-size:14px;
	color:#fff;
	line-height:35px;
	float:left;
	margin-right:0.3%;
	text-decoration:none;
}
.tabs>a:last-child {
	margin-right:0	
}
.tabs>a:hover, .tabs>a.s {
	background:#cc6600;
	text-decoration:none;
}
.tab {
	background:#f5f5f5;
	-moz-box-shadow:1px 2px 6px rgba(0,0,0,0.5);-webkit-box-shadow:1px 2px 6px rgba(0,0,0,0.5);
	box-shadow:1px 1px 4px rgba(0,0,0,0.5);	
}
.tab table {
	width:95%;
	margin:0px auto;	
}
.tab table th {
	padding-right:20px;
	font-size:24px;
	color:#cccccc;
	font-weight:normal;
	text-align:left;
	height:68px;
	border-bottom:1px solid #cfcfcf;
}
.tab table td {
	font-size:16px;
	color:#666666;	
	border-bottom:1px solid #cfcfcf;
}
.tab table td label {
	width:98px;
	float:left;
	height:28px;
	font-size:16px;
	line-height:28px;
}
.tab table tr:last-child th, .tab table tr:last-child td {
	border-bottom:none;	
}
.tab label span {
	font-size:12px;
	color:red;
	position:relative;
	top:-7px;
	left:2px;
}
';
		$h = '';
		$h .= '<div class="tabs" id="tabs">';
		foreach (self::$folders as $f) {
			$h .= '<a href="'.str_replace('&s='.$_GET['s'],'',$_SERVER['REQUEST_URI']).'&s='.$f.'" class="no_scroll'.($_GET['s']==$f?' s':'').'">'.$f.'</a>';
		}
		$h .= '</div>';
		$h .= '<table class="tr_inst no_e" cellspacing="0" style="width:'.$this->width.'px">';
		foreach ($this->data as $c => $arr) {
			$h .= '<tr class="th">
				<td>';
				if ($c!='Indexes') {
					if ($c=='Oils') $i = 'Oils.png';
					elseif ($c=='Metals' || $c=='Indexes') $i = 'XAG.png';
					else $i = $c.'.jpg';
					$h .= '<img src="'.self::PATH.'images/cur/'.$i.'" width="21">';
				}
				$h .= '</td>
				<th>'.self::l('Symbol').'</th>
				<th class="l">'.self::l('Headline').'</th>
				<th>'.self::l('Spread').'</th>
				<th>'.self::l('Swap/Long').'</th>
				<th>'.self::l('Swap/Short').'</th>
			</tr>';
			foreach ($arr as $q) {
				$h .= '<tr class="r">
				<th><div></div></th>
				<td>'.$q['SYMBOL'].'</td>
				<td class="l"><small>'.self::l($q['SYMBOL'].'-text').'</small></td>
				<td id="spread-'.str_replace('.','',$q['SYMBOL']).'"><div class="'.$q['SPREAD_c'].'">'.$q['SPREAD'].'</div></td>
				<td>'.self::l($q['SYMBOL'].'-long').'</td>
				<td>'.self::l($q['SYMBOL'].'-short').'</td>
			</tr>';
			}
		}
		
		$h .= '</table>';
		
		$h .= '<script>
Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep) {  
   var n = this, 
   c = isNaN(decimals) ? 2 : Math.abs(decimals),
   d = decimal_sep || '.',
   t = (typeof thousands_sep === \'undefined\') ? \' \' : thousands_sep,
   sign = (n < 0) ? \'-\' : \'\', 
   i = parseInt(n = Math.abs(n).toFixed(c)) + \'\',
   j = ((j = i.length) > 3) ? j % 3 : 0;  
   return sign + (j ? i.substr(0, j) + t : \'\') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : \'\');
};
function informer_2_ajax() {
	$.ajax({
		dataType:\'json\',
		url: \''.$_SERVER['REQUEST_URI'].'&get=json\',
		type: \'get\',
		error:function(a,c,e) {
			
		},
		success: function(data) {
			var a,o;
			for (s in data) {
				a=data[s];
				o=$(\'#spread-\'+(a.SYMBOL.replace(/\./g,\'\')));

				c = a.SPREAD_c;
				o.html(\'<div class="\'+c+\'">\'+(a.SPREAD).toMoney(1,\'.\',\'\')+\'</div>\');
			}
			setTimeout(function(){
				informer_2_ajax();
			},5000);
		}
	});
};
$(function(){
	setTimeout(function(){
		informer_2_ajax();
	},5000);
});
</script>';
		
		return array($css, $h);
	}
	
	
	
	private function write_3() {
		$css = '.informer-pairs {
	background:#fff;	
}
.informer-pairs table {
	width:100%;
}
.informer-pairs table thead th {
	height:30px;
	color:#0b4960;
	background:#e0e0e0;
	font-weight:normal;
	line-height:30px;
	width:23%;
}
.informer-pairs table thead th:last-child {
	width:8%;	
}
.informer-pairs table tbody td {
	height:31px;
	color:#333333;
	border-bottom:1px solid #cccccc;
	line-height:31px;
	text-align:center;
	padding:0 5px;
	white-space:nowrap;
	width:25%;
}

.informer-pairs table tbody td.green {
	color:#00cc00!important;
}
.informer-pairs table tbody td.red {
	color:#ff3300!important;
}
.informer-pairs table tfoot td {
	text-align:center;
	height:30px;
	background:#0b4960;
}
.informer-pairs table tfoot td>a {
	display:block;
	background:url('.self::PATH.'images/logo-small.png) center no-repeat;
	width:100%;
	height:30px;
}

.informer-pairs.informer-grey table thead th {
	background:#666666;
	color:#fff;
}
.informer-pairs.informer-grey table tbody td {
	background:#f7f6f6;
}
.informer-pairs.informer-grey table tbody tr.even td {
	background:#eaeaea;
}
.informer-pairs.informer-grey table tfoot td {
	background:#cee3e8;
}
.informer-pairs.informer-grey table tfoot td>a {
	background:url('.self::PATH.'images/logo-small2.png) center no-repeat;
}

.informer-pairs.informer-black table thead th {
	background:#000000;
	color:#fff;
}
.informer-pairs.informer-black table tbody td {
	background:#666666;
	color:#fff;
}
.informer-pairs.informer-black table tbody tr.even td {
	background:#333333;
}
.informer-pairs.informer-black table tfoot td {
	background:#0099cc;
}
.informer-pairs.informer-black table tfoot td>a {
	background:url('.self::PATH.'images/logo-small2.png) center no-repeat;
}

.informer-pairs.informer-blue table thead th {
	background:#0b4960;
	color:#fff;
}
.informer-pairs.informer-blue table tbody td {
	background:#cee3e8;
	color:#0b4960;
}
.informer-pairs.informer-blue table tbody tr.even td {
	background:#add5df;
}
.informer-pairs.informer-blue table tfoot td {
	background:#0099cc;
}
.informer-pairs.informer-blue table tfoot td>a {
	background:url('.self::PATH.'images/logo-small2.png) center no-repeat;
}';

		$h = '<div class="informer informer-pairs informer-'.$this->color.'" style="width:'.$this->width.'px;">
	<table cellspacing="0" width="100%">
		<thead>
			<tr>
				<th>'.self::l('Symbol').'</th>
				<th>'.self::l('Bid').'</th>
				<th>'.self::l('Ask').'</th>
				<th>'.self::l('Change').'</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
	</table>
	<div style="overflow:auto;height:'.$this->height.'px;overflow-x:hidden;width:'.$this->width.'px">
		<table cellspacing="0" width="100%">
			<tbody>';
				foreach ($this->data as $q) {
					$h .= '<tr'.($i%2?' class="even"':'').' id="s-'.$q['SYMBOL'].'">
						<td>'.$q['SYMBOL'].'</td>
						<td class="bid">'.$q['BID'].'</td>
						<td class="ask">'.$q['ASK'].'</td>
						<td class="mod '.($q['MODIFY']>0?'green':'red').'">'.($q['MODIFY']>0?'+':'').''.$q['MODIFY'].'% '.($q['MODIFY']>0?'▲':'▼').'</td>
					</tr>';
					$i++;
				}
				$h .= '
			</tbody>
		</table>
	</div>
	<table cellspacing="0" width="100%">
		<tfoot>
			<tr>
				<td colspan="4"><a href="http://adamantfinance.com/'.($this->rf?'rf='.$this->rf:'').'" target="_blank" title="Adamant Finance"></a></td>
			</tr>
		</tfoot>
	</table>
</div>';
		$h .= '
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script>
function informer_3_ajax() {
	$.ajax({
		dataType:\'json\',
		url: \''.$_SERVER['REQUEST_URI'].'&get=json\',
		type: \'get\',
		error:function(a,c,e) {
			
		},
		success: function(data) {
			for (s in data) {
				var tr=$(\'#s-\'+s);
				if (tr.length) {
					tr.find(\'td.bid\').html(data[s].BID);
					tr.find(\'td.ask\').html(data[s].ASK);
					tr.find(\'td.mod\').removeClass(\'red green\').addClass(data[s].MODIFY>0?\'green\':\'red\').html((data[s].MODIFY>0?\'+\':\'\')+data[s].MODIFY+\'% \'+(data[s].MODIFY>0?\'▲\':\'▼\'));
				}
			}
			setTimeout(function(){
				informer_3_ajax();
			},5000);
		}
	});
}
$(function(){
	setTimeout(function(){
		informer_3_ajax();
	},5000);
});
</script>
';
		
		return array($css, $h);
	}
	
	
	private function write_4() {
		$css = 'table.cal {
	width:100%;
	font-size:12px;
}
td.l {
	text-align:left!important	
}
table.cal tr.h td {
	font-size:24px;
	color:#333333;
	padding-top:25px;
	height:40px;
	vertical-align:top;
}
table.cal tr.h .w {
	padding-left:58px;
	background-repeat:no-repeat;
	background-position:left center;
}
table.cal tr.h .w1 {
	background-image:url('.self::PATH.'images/w1.jpg);
}
table.cal tr.h .w2 {
	background-image:url('.self::PATH.'images/w2.jpg);
}
table.cal tr.h .w3 {
	background-image:url('.self::PATH.'images/w3.jpg);
}
table.cal tr.h .w4 {
	background-image:url('.self::PATH.'images/w4.jpg);
}
table.cal tr.h .w5 {
	background-image:url('.self::PATH.'images/w5.jpg);
}
table.cal tr.h .w6 {
	background-image:url('.self::PATH.'images/w6.jpg);
}
table.cal tr.h .w7 {
	background-image:url('.self::PATH.'images/w7.jpg);
}
table.cal th {
	background:#004A60;
	color:#fff;
	text-align:left;
	padding-left:10px;
	height:28px;
	font-size:12px;
	border-right:1px solid #fff;
}
table.cal tr th:last-child {
	border-right:none
}
table.cal tr.i td {
	cursor:pointer;
	height:29px;
	padding:0 10px;
	text-align:center;
	border-top:1px solid #cfcfcf;
	white-space:nowrap;
}
table.cal tr.i td.l {
	width:50%;
	white-space:normal
}
table.cal tr.i:hover td, table.cal tr.i.s td {
	background:#E3E3E3;	
}
table.cal tr.i td.j img {
	width:16px;
	height:12px;
	border:1px solid #e6e8e7;
	float:left;
	position:relative;
	top:2px;
	margin-right:3px;
}
table.cal tr.o {
	display:none;
}
table.cal tr.o td {
	padding:10px;
	background:#FAFAFA;
	border-top:1px solid #cfcfcf;
	font-size:14px;
	color:#666666;
	line-height:19px;
}
.imp {
	display:inline-block;
	width:44px;
	height:12px;	
}
.imp.imp1 {
	background:url('.self::PATH.'images/imp1.png);	
}
.imp.imp2 {
	background:url('.self::PATH.'images/imp2.png);	
}
.imp.imp3 {
	background:url('.self::PATH.'images/imp3.png);	
}


div.fc-brd {
	padding:10px 30px;
}
div.fc-brd table {
	width:100%;
}
div.fc-brd table td {
	height:25px;	
}
div.fc-brd th {
	width:100px;
	font-size:18px;
	color:#cccccc;
	font-weight:normal;
	tex-align:right;	
}
div.fc-brd th label {
	color:#ff6e2d;	
}';
		
		$h = '<table cellspacing="0" class="cal" id="cal" style="width:'.$this->width.'px;">';
		foreach ($this->data as $date => $data) {
			if (!$data) continue;
			$h .= '
				<tr class="h">
					<td colspan="7">
						<div class="w w'.date('N',$date).'">'.self::l(date('l',$date)).' '.date('d.m.Y',$date).'</div>
					</td>
				</tr>
				<tr>
					<th>'.self::l('Time').'</th>
					<th>'.self::l('Country').'</th>
					<th>'.self::l('Importancy').'</th>
					<th>'.self::l('Indicator').'</th>
					<th>'.self::l('Previous').'</th>
					<th>'.self::l('Forecoast').'</th>
					<th>'.self::l('Came up').'</th>
				</tr>';
			foreach ($data as $d) {
				$h .= '
				<tr class="i">
					<td>'.$d['time'].'</td>
					<td class="j"><img src="'.self::PATH.'images/cur/'.$d['cur'].'.jpg"> '.$d['cur'].'</td>
					<td><div class="imp imp'.$d['imp'].'"></div></td>
					<td class="l"'.($d['imp']==3?' style="font-weight:bold"':'').'>'.$d['title'].'</td>
					<td>'.$d['p'].'</td>
					<td>'.$d['f'].'</td>
					<td>'.$d['s'].'</td>
				</tr>';
				
				$n = 'cal-text_'.md5($d['title']);
				
				$n2 = self::l($n);
				if ($n!=$n2) {
					$h .= '
				<tr class="o">
					<td colspan="7">
						'.$n2.'
					</td>
				</tr>';
				}
			}
		}
		$h .= '</table>';
		
		$h .= '
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script>
$(function(){		
	$(\'#cal tr.i\').click(function(){
		$(\'#cal tr.i\').removeClass(\'s\');
		$(\'#cal tr.o\').hide();
		$(this).addClass(\'s\').next().show();
	});
});
</script>
		';
		return array($css, $h);	
	}
	
	public function write() {
		if (!$this->data) self::err('No data found');
		if ($_GET['get']=='json') {
			ob_end_clean();
			header('Content-Type: text/json; charset=utf-8');
			echo json_encode($this->data);
			return;				
		}
		list ($css, $html) = $this->{write_.$this->which}();
		print '<!doctype html><html>
	<head>
		<title>AdamantFinance informer</title>
		<style>
body, html {
	padding:0;margin:0;
	font-family:Tahoma;
	font-size:13px;
}
'.$css.'
		</style>
	</head><body>';
		print $html;	
		print '</body></html>';
	}
		
}



if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') require 'alx/p.php';

$inf = new Informer;

$inf->rf = $_GET['rf'];
$inf->which = $_GET['which'];
$inf->lang = $_GET['lang'];
$inf->color = $_GET['color'];
$inf->width = $_GET['width'];
$inf->height = $_GET['height'];
$inf->cur = $_GET['cur'];
$inf->fix()->select()->write();
