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
* @file       mod/functions.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

function concat() {
	$args = func_get_args();
	return join('',$args);
}
function ext($file, $pathinfo = PATHINFO_EXTENSION) {
	if (!$file) return '';
	if (preg_match('/\.(php|php3|php4|php5|phtml|inc|phpcgi)(\.([^\.]+))?$/i', $file)) return 'php';
	$ex = explode('.',$file);
	//return strtolower($ex[count($ex)-1]);
	return strtolower(pathinfo($file, $pathinfo));
}
function filename($file) {
	$ex = explode('.',$file);
	array_pop($ex);
	$ret = join('.',$ex);
	return $ret ? $ret : $file;
}
function fileOnly($file) {
	$ex = explode('/',trim($file,'/'));
	return end($ex);
}
function nameOnly($file) {
	$file = fileOnly($file);
	return filename($file);
}
function fixFileName($f) {
	$f = preg_replace('/\s+/','_',trim($f));
	//$f = Parser::toLat($f);
	$f = str_replace('?','_',$f);
	$f = preg_replace("/[^ %!$&\(\)\[\],-\.0-9:;=@A-Z^_`a-z~\\x80-\\xFF+]|:/u", '',$f);
	$f = str_replace('+','_',$f);
	$ext = ext($f);
	$name = nameOnly($f);
	return ($name ? $name.($ext?'.'.$ext:'') : date('Hi_d-m-Y').($ext?'.'.$ext:''));
}
function fixFolderName($name='') {
	if ($name) $name = preg_replace('/[\/\?*\\\|><"]/', '',$name);
	return $name ? $name : date('d.m.Y');
}

function strQuotes($str) {
	$quotes = array("\x27","\x22","\x60","\t","\n","\r",'\'',',','/','¬',';',':','@','~','[',']','{','}','=','*','&','^','$','<','>','?','!','"','-');
	return str_replace($quotes,'',$str);
}
function strRegex($text) {
	$goodquotes = array('-','+','#');
	$repquotes = array('\-','\+','\#');
	$text = stripslashes($text);
	$text = trim(strip_tags($text));
	$text = strQuotes($text);
	$text = str_replace($goodquotes, $repquotes, $text );
	return $text;
}

function randomString($length = 5, $str = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhijkmnpqrstuvwxyz2345678') {
	$str = str_repeat($str,ceil(strlen($str)/$length));
	return substr(str_shuffle($str),0,$length);
}


function last($arr, $key = '') {
	$last = max($arr);
	if ($key) return $last[$key];
	return $last;
}

function rssBody($str, $noimg = false) {
	//$str = str_replace('&amp;','&',$str);
	$str = str_replace(' href="/', ' href="'.HTTP_BASE.'', $str);
	$str = str_replace(' href="?', ' href="'.HTTP_BASE.'?', $str);
	$str = str_replace(' src="/', ' src="'.HTTP_BASE.'', $str);
	$str = str_replace('<p>&nbsp;</p>','',$str);
	$str = preg_replace('/<br( \/)?><br( \/)?>/','<br />',$str);
	if ($noimg) {
		$str = preg_replace('/<img([^>]+)>/Us','',$str);	
	}
	return $str;
}

function name2id($id) {
	$s = '_';
	return trim(str_replace($s.$s,$s,str_replace(array(']','['),$s,$id)),$s);
}

function isSysString($str) {
	$check = strQuotes($str);
	if (!is_numeric($check)) return true;
	return false;
}

function isDateTime($dt) {
	if (!$dt) return false;
	$ft = substr($dt,0,2);
	if ($ft!='19' && $ft!='20') return false;
	$l = strlen($dt);
	if ($l!=19 && $l!=10) return false;
	return true;
}

function d($arr = 'Debug Backtrace', $is_arr = true) {
	if ($_SERVER['REQUEST_URI']=='/d') return;
	if (!Conf()->g('dump')) Conf()->s('dump',new D);
	$db = debug_backtrace();
	Conf()->s('debug_seen',true);
	Conf()->g('dump')->add($arr, $is_arr, ''.date('H:i:s d-m-Y').': '.$db[0]['file'].' on line '.$db[0]['line'].'');
	Conf()->s('debug_seen',false);
}

class D {
	private $vars = array();
	private $i = 0;
	public function __construct() {
		$this->vars = array();
		$_SESSION['vars'] = array();
	}
	public function add($arr, $is_arr, $db = '') {
		$this->vars[] = ($is_arr ? p($arr, 0) : $arr).($db?'<div style="clear:both"><small><b>'.++$this->i.'.</b> '.$db.'</small></div>':'');
	}
	public function __destruct() {
		if (!defined('DUMP_HTML')) define('DUMP_HTML',FTP_DIR_ROOT.'files/temp/d.html');
		file_put_contents(DUMP_HTML, join('<hr />',$this->vars));
	}
}

function p($v = 'Debug Backtrace', $echo = true, $trace = true, $depth = 0, $objects = array()) {
	if ($echo && SITE_TYPE=='json') return;
	File::load();
	return _p($v, $echo, $trace, $depth, $objects);
}
function __sql($s) {
	return '['.date('H:i d-m-Y',intval($s)).']';	
}
function sql($sql, $echo = true) {
	$sql = preg_replace('/(^\d)([0-9]{10})(^\d)/e','\'$1\'.__sql(\'$2\').\'$3\'',$sql);
	if ($echo) {
		echo Message::sql($sql);
	} else {
		return Message::sql($sql);	
	}

}

function getKeywords($data) {
	$p['content'] = $data;
	$p['min_word_length'] = 5;
	$p['min_word_occur'] = 1;
	$p['min_2words_length'] = 3;
	$p['min_2words_phrase_length'] = 10;
	$p['min_2words_phrase_occur'] = 2;
	$p['min_3words_length'] = 3;
	$p['min_3words_phrase_length'] = 10;
	$p['min_3words_phrase_occur'] = 0;
	return Factory::call('keyword', s)->get_keywords();	
}

function RssReader($url = '', $cache = 'file', $curl = true, $enc = 'UTF-8', $int_enc = 'UTF-8') {
	if (!$url) return 'URL missing';
	if ($cache && ($arr = Cache::get('rss.'.$url,3600,$cache==='file'))) {
		return $arr;
	}
	$rss = new RssReader($url, $curl, $enc, $int_enc);
	$arr = $rss->parse();
	if (!$arr['ITEMS'] || !is_array($arr['ITEMS'])) $arr['ITEMS'] = array();
	if ($rss->conf('error')) $arr = array('ERROR' => $rss->conf('error'),'URL' => $url);
	if ($cache) Cache::save('rss.'.$url,$arr,$cache==='file');
	return $arr;
}


function sendMail($to, $subject='', $body='', $attachments=array(), $fromname=MAIL_NAME, $fromaddress=MAIL_EMAIL, $arr_headers = array()) {
	return Email::send($to, $subject, $body, $attachments, $fromaddress, $fromname, $arr_headers);	
	/*
	$ex = explode('@',$to);
	if ($ex[1]=='gmail.com') {
		$email = $ex[0].'@googlemail.com';
		$ret = Email::send($email, $subject, $body, $attachments, $fromaddress, $fromname, $arr_headers);
	} else {
		$ret = Email::send($to, $subject, $body, $attachments, $fromaddress, $fromname, $arr_headers);	
	}
	return $ret;
	*/
}

function array_unset(&$row, $keys) {
	foreach ($keys as $key) unset($row[$key]);	
}

function array_check($arr, $vals) {
	$new_arr = array();
	foreach ($arr as $k => $v) {
		if (!in_array($v, $vals)) continue;
		$new_arr[$k] = $v;	
	}
	return $new_arr;
}

function array_numeric($r, $skipZero = false) {
	if (!$r || !is_array($r)) return array();
	$nr = array();
	foreach ($r as $i) {
		if (is_numeric($i)) {
			if (!$i && $skipZero) continue;
			$nr[] = $i;
		}
	}
	return array_unique($nr);
}
function array_string($r) {
	if (!$r || !is_array($r)) return array();
	$nr = array();
	foreach ($r as $i) {
		if (strlen(trim($i))) $nr[] = trim($i);
	}
	return array_unique($nr);
}
function array_noempty($a) {
	if (!$a || !is_array($a)) return array();
	$r = array();
	foreach ($a as $k => $v) {
		if ($v) $r[$k] = $v;
	}
	return $r;
}

function array_flat($arr){
	$ret = array();
	foreach($arr as $val) {
		if(!is_array($value)) $ret[] = $val;
		else array_splice($ret, count($ret), 0, array_flat($val));
	}
	return $ret;
}

function array_label($arr, $k = 0, $j = 0, $skip_empty = true) {
	if (!is_array($arr)) return array();
	$ret = array();
	$toKeys = $k==='[[:KEY:]]';
	$toValues = $k==='[[:VALUE:]]';
	$ex = false;
	if (strpos($k,'|')) {
		$ex = explode('|',$k);	
	}
	foreach ($arr as $key => $a) {
		if ($ex) {
			$ret[$a[$ex[0]]] = $a[$ex[1]];
		}
		elseif ($toKeys) {
			$ret[$key] = $key;
		}
		elseif ($toValues) {
			if ($skip_empty && !$a) continue;
			$ret[$a] = $a;
		}
		elseif (isset($a[$k])) {
			$ret[$key] = $a[$k];
		}
		elseif ($j && isset($a[$j])) {
			$ret[$key] = $a[$j];
		}
		if (!$toValues && $skip_empty && isset($ret[$key]) && !$ret[$key]) {
			unset($ret[$key]);
		}
	}
	return $ret;
}



function js_unesc($str) {
	return urldecode(preg_replace('/%u([0-9A-F]{4})/se','iconv("UTF-16BE", "UTF-8", pack("H4", "$1"))', $str));
}

function is_utf8($st) {
	if (!is_string($st)) return false;
	return preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]			# ASCII
		| [\xC2-\xDF][\x80-\xBF]			# non-overlong 2-byte
		|  \xE0[\xA0-\xBF][\x80-\xBF]		# excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}	# straight 3-byte
		|  \xED[\x80-\x9F][\x80-\xBF]		# excluding surrogates
		|  \xF0[\x90-\xBF][\x80-\xBF]{2}	# planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3}			# planes 4-15
		|  \xF4[\x80-\x8F][\x80-\xBF]{2}	# plane 16
   )*$%xs', $st);
}

function charset_decode_utf_8($s) { 
	if (! ereg("[\200-\237]", $s) and ! ereg("[\241-\377]", $s)) return $s; 
	$s = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e","'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",$s); 
	return preg_replace("/([\300-\337])([\200-\277])/e","'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'", $s);
}

/*
function calc($equation) {
	// Remove whitespaces
	$equation = preg_replace('/\s+/', '', $equation);	
	$number = '(?:-?\d+(?:[,.]\d+)?|pi|π)'; // What is a number
	$functions = '(?:sinh?|cosh?|tanh?|abs|acosh?|asinh?|atanh?|exp|log10|deg2rad|rad2deg|sqrt|ceil|floor|round)'; // Allowed PHP functions
	$operators = '[+\/*\^%-]'; // Allowed math operators
	$regexp = '/^(('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns
	
	if (preg_match($regexp, $equation)) {
		$equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
		eval('$result = '.$equation.';');
	}
	else {
		$result = false;
	}
	return $result;
}
*/

function cp1251_to_utf8($s){
	$c209 = chr(209); $c208 = chr(208); $c129 = chr(129);
	$t = '';
	for($i=0; $i<strlen($s); $i++) {
		$c=ord($s[$i]);
		if ($c>=192 and $c<=239) $t.=$c208.chr($c-48);
		elseif ($c>239) $t.=$c209.chr($c-112);
		elseif ($c==184) $t.=$c209.$c209;
		elseif ($c==168) $t.=$c208.$c129;
		else $t .= $s[$i];
	}
	return $t;
}

function utf8_to_cp1251($s) {
	$out = '';
	$byte2 = false;
	$l = strlen($s);
	for ($c=0; $c<$l; $c++) {
		$i = ord($s[$c]);
		if ($i<=127) $out.=$s[$c];
		if ($byte2) {
			$new_c2 = ($c1&3) * 64 + ($i&63);
			$new_c1 = ($c1>>2)&5;
			$new_i = $new_c1 * 256 + $new_c2;
			if ($new_i==1025) $out_i = 168;
			elseif ($new_i==1105) $out_i = 184;
			else $out_i = $new_i-848;
			$out .= chr($out_i);
			$byte2 = false;
		}
		if (($i>>5)==6) {
			$c1 = $i;
			$byte2 = true;
		}
	}
	return $out;
}
function strsplit($str) { 
	$split=1;
	$array = array();
	$c = strlen($str);
	for ($i = 0; $i < $c;) { 
		$value = ord($str[$i]); 
		if($value > 127) {
			if($value >= 192 && $value <= 223) $split=2; 
			elseif($value >= 224 && $value <= 239) $split=3; 
			elseif($value >= 240 && $value <= 247) $split=4; 
		} else { 
			$split=1; 
		} 
		$key = NULL; 
		for ($j=0;$j<$split;$j++,$i++) $key .= $str[$i]; 
		array_push($array,$key); 
	}
	return $array; 
}
function len($s) {
	return strlen(preg_replace("/&#([0-9]+);/","-",$s));
}

function first($s, $words = true) {
	if (!$s) return $s;
	if (is_array($s)) {
		$r = array();
		foreach ($s as $i=>$_s) {
			$r[$i] = first($_s);
		}
		return $r;
	}
	if ($words && strstr($s,' ')) {
		$ex = explode(' ',$s);
		$r = array();
		foreach ($ex as $e) {
			if (!$e) continue;
			array_push($r, first($e, false));
		}
		return join(' ',$r);
	}
	
	$a = strsplit($s);
	$one = $a[0];
	array_shift($a);
	return upper($one).join('',$a);
}

function trunc($s, $len = 200, $wordsafe = false, $dots = false) {
	$s = str_replace(array('<!--','-->'),'',$s);
	$slen = strlen($s);
	if ($slen <= $len) {
		return $s;
	}
	if ($wordsafe) {
		$end = $len;
		while (($s[--$len] != ' ') && ($len > 0)) {};
		if ($len == 0) {
			$len = $end;
		}
	}
	if ((ord($s[$len]) < 0x80) || (ord($s[$len]) >= 0xC0)) {
		return substr($s, 0, $len).($dots?'&#8230;':'');
	}
	while (--$len >= 0 && ord($s[$len]) >= 0x80 && ord($s[$len]) < 0xC0) {};
	return substr($s,0,$len).($dots?'&#8230;':'');
}

/*
function reverse($str, $reverse = false) {
	preg_match_all('/./us', $str, $ar);
	if($reverse) return join('',array_reverse($ar[0]));
	else {
		$temp = array();
		foreach($ar[0] as $value) {
			if(is_numeric($value) && !empty($temp[0]) && is_numeric($temp[0])) {
				foreach ($temp as $key => $value2) {
					if (is_numeric($value2)) $pos = ($key + 1);
					else break;
					$temp2 = array_splice($temp, $pos);
					$temp = array_merge($temp, array($value), $temp2);
				}
			}
			else array_unshift($temp, $value);
		}
		return implode('', $temp);
	}
}
*/

function translate($text, $from, $to, $force = false) {
	if (!USE_TRANSLATE) return $text;
	if ($to=='ee') $to = 'et';
	if ($from=='ee') $from = 'et';
	if (!$from) {
		if (preg_match('/[a-z]/i',$text)) $from = 'en';	else $from = MY_LANGUAGE;
	}
	if ($to==$from) return $text;
	//$text = iconv("windows-1251", "utf-8", $text);
	return Factory::call('translate', USE_TRANSLATE)->translate($text, $from, $to, $force);
}
function getCoordinates2($address) {
	$address = str_replace(" ", "+", $address);
	$url = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address='.$address;
	$response = File::url($url);
	$json = json_decode($response,true);
	if ($json['results']) {
		return array('lat'=>$json['results'][0]['geometry']['location']['lat'],'lng'=>$json['results'][0]['geometry']['location']['lng']); 
	} else {
		return array();	
	}
}
function getCoordinates($country, $city = '', $state = '', $street = '',$zip = '') {
	$data = File::url('http://local.yahooapis.com/MapsService/V1/geocode?appid=YD-9G7bey8_JXxQP6rxl.fBFGgCdNjoDMACQA--&country='.$country.'&city='.$city.'&state='.$state.($street?'&street='.$street:'').($zip?'&postcode='.$zip:''));
	if (!$data) {
		return array();
	}
	$data = File::xml2array($data);
	if (!$data || !$data['ResultSet'] || !$data['ResultSet']['Result']) {
		return getCoordinates2($street.' '.$city.' '.$state.' '.$country.' '.$zip);
	}
	$ret = array();
	if ($data['ResultSet']['Result'][0]) {
		foreach ($data['ResultSet']['Result'] as $i => $arr) {
			foreach ($arr as $k => $v) {
				if (is_array($v) && isset($v['value'])) {
					$ret[$i][strtolower($k)] = $v['value'];
				} else {
					$ret[$i][strtolower($k)] = $v ? $v : '';
				}
			}
		}
	}
	else {
		foreach ($data['ResultSet']['Result'] as $k => $v) {
			if (is_array($v) && isset($v['value'])) {
				$ret[0][strtolower($k)] = $v['value'];
			} else {
				$ret[0][strtolower($k)] = $v ? $v : '';
			}
		}
	}
	
	return $ret;
}


function lower($s, $upper = false) {
	if ($upper && function_exists('mb_strtoupper')) return mb_strtoupper($s,'UTF-8');
	elseif (!$upper && function_exists('mb_strtolower')) return mb_strtolower($s,'UTF-8');
	
	static $lower_chars = array(
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 
		'v', 'w', 'x', 'y', 'z', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 
		'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 
		'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 
		'ь', 'э', 'ю', 'я',
		'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'â', 'à', 'á', 'ä', 'ã', 'ê', 'è', 'é', 'ë', 'î', 'í', 'ì',
		'ï', 'ô', 'õ', 'ò', 'ó', 'ö', 'û', 'ù', 'ú', 'ü', 'ç'
	); 
	static $upper_chars = array( 
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 
		'V', 'W', 'X', 'Y', 'Z', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 
		'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 
		'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ъ',
		'Ь', 'Э', 'Ю', 'Я',
		'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż', 'Â', 'À', 'Á', 'Ä', 'Ã', 'Ê', 'È', 'É', 'Ë', 'Î', 'Í', 'Ì',
		'Ï', 'Ô', 'Õ', 'Ò', 'Ó', 'Ö', 'Û', 'Ù', 'Ú', 'Ü', 'Ç'
	);
	if ($upper) return str_replace($lower_chars,$upper_chars,$s);
	else return str_replace($upper_chars,$lower_chars,$s);
}

function upper($s) {
	return lower($s,true);
}

function wrap($str, $width = 80, $break = " ") {
	$return = '';
	$br_width = mb_strlen($break, 'UTF-8');
	$l = mb_strlen($str, 'UTF-8');
	for ($i = 0, $count = 0; $i < $l; $i++, $count++) {
		if (mb_substr($str, $i, $br_width, 'UTF-8') == $break) {
			$count = 0;
			$return .= mb_substr($str, $i, $br_width, 'UTF-8');
			$i += $br_width - 1;
		}
		if ($count > $width) {
			$return .= $break;
			$count = 0;
		}
		$return .= mb_substr($str, $i, 1, 'UTF-8');
	}
	
	return $return;
}
/*
function wrap($text, $breakpoint = 80,$char = ' ') {
	if (!$char) $char = ' ';
	$str_len = len($text);
	if ($str_len < $breakpoint) return $text;
	$str_arr = array();
	$str = '';
	$l = strlen($text);
	for ($i=0;$i<$l;$i++) {
		if (ord(substr($text,$i,1))>128 && ord(substr($text,$i,1))<256) {
			$str_arr[] = substr($text, $i, 2);
			$i += 1;
		}
		elseif (ord(substr($text,$i,1))>256) {
			$str_arr[] = substr($text,$i,3);
			$i += 2;
		} else {
			$str_arr[] = substr($text,$i,1);
		}	
	}
	$words = array();
	$i = 0;
	$word = '';
	foreach ($str_arr as $key => $val) {
		if ($i>$breakpoint || $val==" " || $val=="\n" || $val=="\r" || $val=="\t") {
			$i = 0;
			$words[] = $word;
			$word = $val;
		} else {
			$i++;
			$word .= $val;	
		}
	}
	$ret = trim(join($char,$words).$char.$word);
	return str_replace($char.$char,$char,$ret);
}
*/


 
/*
function wrapWords($str, $width=75, $break="\n") { 
	return preg_replace('#(\S{'.$width.',})#e', "chunk_split('$1', ".$width.", '".$break."')", $str); 
}

function size($text,$length=30,$showLength = true) {
	$text = str_replace(array('<!--','-->'),'',$text);
	if (!$text) return '';
	$newText = '';
	$words = explode(' ',$text);
	$c = count($words);
	for ($i=0; $i < $c; $i++) {
		$l = strlen($words[$i]);
		if ($l>$length) $newText .= trunc($words[$i],$length-3).'..'.($showLength?'('.$l.') ':' ');
		else $newText .= $words[$i].' ';
	}
	return $newText;
}
function strip($st, $replace = ' ') {
	return preg_replace('!\s+!', $replace, $st);
}

function spacify($st, $char = ' ') {
	return implode($char, preg_split('//', $st, -1, PREG_SPLIT_NO_EMPTY));
}

function sentenses($st) {
	if (empty($st)) return 0;
	return preg_match_all('/[^\s]\.(?!\w)/', $st, $m);
}
*/

if (!function_exists('json_decode')) {
	function json_decode($str) {
		return Factory::call('json')->decode($str);
	}
}


if (!function_exists('json_encode')) {
	function json_encode($arr) {
		return json($arr, true);
	}
}

if (!function_exists('sys_get_temp_dir')) {
	function sys_get_temp_dir() {
		if ($temp = getenv('TMP')) return $temp;
		if ($temp = getenv('TEMP')) return $temp;
		if ($temp = getenv('TMPDIR')) return $temp;
		$temp=tempnam(__FILE__,'');
		if (file_exists($temp)) {
			unlink($temp);
			return dirname($temp);
		}
		return NULL;
	}
}



if (!function_exists('mime_content_type')) {
	function mime_content_type($filename) {
		$mime_types = array (
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);
		$ext = ext($filename);
		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		}
		elseif (function_exists('finfo_open')) {
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		}
		else {
			return 'application/octet-stream';
		}
	}
}