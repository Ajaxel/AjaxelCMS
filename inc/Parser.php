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
* @file       inc/Parser.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Parser {
	
	private $params = array();

	private $stripped = false;
	public $no_smilies = false;
	private $length = 0;
	
	private static $_instance = false;
	
	public static function getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self;	
		}
		return self::$_instance;
	}	
	
	
	public function __construct() {
		$this->admin = false;
		$this->params['striphtml'] = true;
		$this->params['smilies_only'] = false;
		$this->params['fieldset'] = false;
		$this->params['title'] = '';
		$this->params['for_email'] = false;
		$this->params['numbers'] = false;
		$this->params['editor'] = false;

		$_instance =& $this;
	}
	
	public static function parse($method = 'print', $t = '', $param = NULL, $err3 = NULL, $with_error = false) {
		$self = self::getInstance();
		
		$self->length = strlen($t);
		
		switch($method) {
			// text, single
			case 'code':
				$t = $self->strLines($t);
				$t = $self->code($t,$param);
				$t = $self->nobb_after_code($t);
				$t = self::after_preg($t);
				$self->params['editor'] = false;
			break;
			case 'code_and_bb':
			case 'code_bb':
				$t = $self->strLines($t);
				$t = $self->strTabs($t);
				$t = $self->code($t,$param);
				$t = $self->bb_after_code($t);
				$t = self::after_preg($t);
				$self->params['editor'] = false;
			break;
			case 'code_and_smilies':
			case 'code_smilies':
				$t = self::formatURLs($t);
				$t = self::fixMce($t);
				$self->params['smilies_only'] = true;
				$self->params['striphtml'] = false;
				$self->params['editor'] = true;
				$t = $self->code($t);
				$t = $self->plain_after_code($t);
				$t = self::bb_videos($t);
				$t = self::bb_smilies($t);
				$t = self::after_preg($t);
				$self->params['smilies_only'] = false;
			break;
			case 'bb':
				$t = $self->bbcode($t);
				$t = self::formatURLs($t);
				$t = self::after_preg($t);
				$self->params['editor'] = false;
			break;

			case 'email':
			case 'admin_email':
				//$self->stripped = true;
				$self->params['for_email'] = true;
				$t = $self->code($t,$param);
				$t = $self->bb_after_code($t);
				$self->params['editor'] = false;
				$self->params['for_email'] = false;
			break;			
			
			default:
				// nothing
			break;
		}
		return $t;
	}
	
	public function transl($s) {
		static $pairs = array(
			'&iexcl;' => '¡',
			'&cent;' => '¢',
			'&pound;' => '£',
			'&curren;' => '¤',
			'&yen;' => '¥',
			'&brvbar;' => '¦',
			'&sect;' => '§',
			'&uml;' => '¨',
			'&copy;' => '©',
			'&ordf;' => 'ª',
			'&laquo;' => '«',
			'&not;' => '¬',
			'&reg;' => '®',
			'&macr;' => '¯',
			'&deg;' => '°',
			'&plusmn;' => '±',
			'&sup2;' => '²',
			'&sup3;' => '³',
			'&acute;' => '´',
			'&micro;' => 'µ',
			'&para;' => '¶',
			'&middot;' => '·',
			'&cedil;' => '¸',
			'&sup1;' => '¹',
			'&ordm;' => 'º',
			'&raquo;' => '»',
			'&frac14;' => '¼',
			'&frac12;' => '½',
			'&frac34;' => '¾',
			'&iquest;' => '¿',
			'&times;' => '×',
			'&divide;' => '÷',
			'&Agrave;' => 'À',
			'&Aacute;' => 'Á',
			'&Acirc;' => 'Â',
			'&Atilde;' => 'Ã',
			'&Auml;' => 'Ä',
			'&Aring;' => 'Å',
			'&AElig;' => 'Æ',
			'&Ccedil;' => 'Ç',
			'&Egrave;' => 'È',
			'&Eacute;' => 'É',
			'&Ecirc;' => 'Ê',
			'&Euml;' => 'Ë',
			'&Igrave;' => 'Ì',
			'&Iacute;' => 'Í',
			'&Icirc;' => 'Î',
			'&Iuml;' => 'Ï',
			'&ETH;' => 'Ð',
			'&Ntilde;' => 'Ñ',
			'&Ograve;' => 'Ò',
			'&Oacute;' => 'Ó',
			'&Ocirc;' => 'Ô',
			'&Otilde;' => 'Õ',
			'&Ouml;' => 'Ö',
			'&Oslash;' => 'Ø',
			'&Ugrave;' => 'Ù',
			'&Uacute;' => 'Ú',
			'&Ucirc;' => 'Û',
			'&Uuml;' => 'Ü',
			'&Yacute;' => 'Ý',
			'&THORN;' => 'Þ',
			'&szlig;' => 'ß',
			'&agrave;' => 'à',
			'&aacute;' => 'á',
			'&acirc;' => 'â',
			'&atilde;' => 'ã',
			'&auml;' => 'ä',
			'&aring;' => 'å',
			'&aelig;' => 'æ',
			'&ccedil;' => 'ç',
			'&egrave;' => 'è',
			'&eacute;' => 'é',
			'&ecirc;' => 'ê',
			'&euml;' => 'ë',
			'&igrave;' => 'ì',
			'&iacute;' => 'í',
			'&icirc;' => 'î',
			'&iuml;' => 'ï',
			'&eth;' => 'ð',
			'&ntilde;' => 'ñ',
			'&ograve;' => 'ò',
			'&oacute;' => 'ó',
			'&ocirc;' => 'ô',
			'&otilde;' => 'õ',
			'&ouml;' => 'ö',
			'&oslash;' => 'ø',
			'&ugrave;' => 'ù',
			'&uacute;' => 'ú',
			'&ucirc;' => 'û',
			'&uuml;' => 'ü',
			'&yacute;' => 'ý',
			'&thorn;' => 'þ',
			'&yuml;' => 'ÿ'
		);
		return strtr($s,$pairs);
	}
	
	/*
	function psl(&$pwd){
		$match = create_function('$reg, &$pwd', 'return preg_match($reg, $pwd);');
		$length = strlen($pwd);
		$level = 0;
		if($length > 5) {
			$level = 1;
			if($length > 6) {
				if($match('/[a-z]/', $pwd) && $match('/[A-Z]/', $pwd) && $match('/[0-9]/', $pwd))
					$level = 3;
				else if(
					($match('/[a-z]/', $pwd) && ($match('/[A-Z]/', $pwd) || $match('/[0-9]/', $pwd))) ||
					($match('/[0-9]/', $pwd) && ($match('/[A-Z]/', $pwd) || $match('/[a-z]/', $pwd))) ||
					($match('/[A-Z]/', $pwd) && ($match('/[a-z]/', $pwd) || $match('/[0-9]/', $pwd)))
				)
					$level = $match('/[\x20-\x2F]|[\x3A-\x40]|[\x5b-\x60]|[\x7B-\xFF]/', $pwd) ? 3 : 2;
			}
		}
		return $level;
	}
	*/
	public static function set_status_header($code = 200, $text = '')	{
		$stati = array(
			200	=> 'OK',
			201	=> 'Created',
			202	=> 'Accepted',
			203	=> 'Non-Authoritative Information',
			204	=> 'No Content',
			205	=> 'Reset Content',
			206	=> 'Partial Content',

			300	=> 'Multiple Choices',
			301	=> 'Moved Permanently',
			302	=> 'Found',
			304	=> 'Not Modified',
			305	=> 'Use Proxy',
			307	=> 'Temporary Redirect',

			400	=> 'Bad Request',
			401	=> 'Unauthorized',
			403	=> 'Forbidden',
			404	=> 'Not Found',
			405	=> 'Method Not Allowed',
			406	=> 'Not Acceptable',
			407	=> 'Proxy Authentication Required',
			408	=> 'Request Timeout',
			409	=> 'Conflict',
			410	=> 'Gone',
			411	=> 'Length Required',
			412	=> 'Precondition Failed',
			413	=> 'Request Entity Too Large',
			414	=> 'Request-URI Too Long',
			415	=> 'Unsupported Media Type',
			416	=> 'Requested Range Not Satisfiable',
			417	=> 'Expectation Failed',

			500	=> 'Internal Server Error',
			501	=> 'Not Implemented',
			502	=> 'Bad Gateway',
			503	=> 'Service Unavailable',
			504	=> 'Gateway Timeout',
			505	=> 'HTTP Version Not Supported'
		);


		if (isset($stati[$code]) && $text == '') {
			$text = $stati[$code];
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi')	{
			header("Status: {$code} {$text}", true);
		}
		elseif ($server_protocol == 'HTTP/1.1' || $server_protocol == 'HTTP/1.0') {
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}
		else {
			header("HTTP/1.1 {$code} {$text}", true, $code);
		}
	}
	
	public static function remove_invisible_characters($str, $url_encoded = TRUE)	{
		$non_displayables = array();
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		
		if ($url_encoded) {
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);
		return $str;
	}
	
	public static function html2rgb($color) {
		if ($color[0] == '#') $color = substr($color, 1);
		if (strlen($color) == 6) list($r, $g, $b) = array($color[0].$color[1],$color[2].$color[3],$color[4].$color[5]);
		elseif (strlen($color) == 3) list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
		else return false;
		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);
		return array($r, $g, $b);
	}
	
	/*
	public static function rgb2html($r, $g=-1, $b=-1) {
		if (is_array($r) && sizeof($r) == 3) list($r, $g, $b) = $r;
		$r = intval($r); $g = intval($g);
		$b = intval($b);
		$r = dechex($r<0?0:($r>255?255:$r));
		$g = dechex($g<0?0:($g>255?255:$g));
		$b = dechex($b<0?0:($b>255?255:$b));
		$color = (strlen($r) < 2?'0':'').$r;
		$color .= (strlen($g) < 2?'0':'').$g;
		$color .= (strlen($b) < 2?'0':'').$b;
		return '#'.$color;
	}
	*/
			

	
	
	public static function name($s) {
	//	if (Conf()->g2('Content::name',$s)) return Conf()->g2('Content::name',$s);
		if (LAT_MENU_NAME) $s = self::toLat($s);
		$s = preg_replace('/\s+/u',' ',trim($s));
		$s = str_replace(array('\'','"'),'',$s);
		$wrong = '';
		$space = URL_SPACE;
		$s = self::strQuotes($s,$space);
		$s = str_replace("%",$wrong,$s);
	/*	$s = self::remove_accents($s);*/
		$s = str_replace(" ",$space,$s);
		if ($wrong) {
			$s = str_replace($wrong.$wrong,$wrong,$s);
			$s = str_replace($wrong.$wrong,$wrong,$s);
		}
		$s = str_replace($wrong.$space,$space,$s);
		$s = str_replace($space.$wrong,$space,$s);
		$s = str_replace($space.$space,$space,$s);
		$s = str_replace($space.$space,$space,$s);
		$s = trim($s,$wrong.$space);
	//	Conf()->s2('Content::name', $title, $s);
		return $s;
	}
	
	/*
	public function isEmpty($t) {
		if (!strlen(trim($t)) || ($this->params['defaultvalue'] && $t==$this->params['defaultvalue'])) {
			return false;	
		}
		return $t;
	}
	public function isMatchTo($t) {
		$to = trim($this->params['match']);
		if ($t!==$to) return false;
		return $t;
	}
	public function isLength($t) {
		if (!$t) return $t;
		if ($t && 
		 	(
			 	($this->params['minlength']>0 && strlen(trim($t)) < $this->params['minlength'])
			|| 
				($this->params['maxlength']>1 && strlen(trim($t)) > $this->params['maxlength'])
			)
		) {
			return false;
		}
		return $t;
	}
	public function isUnique($t) {
		if (!$t) return $t;
		if (!$this->params['table']) trigger_error('$this->params[\'table\'] is missing',E_USER_WARNING);
		if (!$this->params['name']) trigger_error('$this->params[\'name\'] is missing',E_USER_WARNING);
		if (DB::one('SELECT id FROM '.$this->params['table'].' WHERE '.$this->params['name'].' LIKE \''.strdb($t).'\' AND lang=\''.Session()->Lang.'\''.$this->params['where_unique_id'])) {
			return false;
		}
		return $t;
	}
	public function isInSelRange($t) {
		if (!$t) return $t;
		if (!is_array($t)) {
			trigger_error('isInSelRange argument should be an array',E_USER_WARNING);
			return false;	
		}
		$cnt = sizeof($t);
		if (($this->params['min_sel'] && $cnt < $this->params['min_sel'] && $this->params['required']) || ($this->params['max_sel'] && $cnt > $this->params['max_sel'])) {
			return false;
		}
		return $t;
	}
	*/
	

	public static function fixMce($s) {
		$s = preg_replace('/\s_mce_style="([^"]*)"/','',$s);
		$s = preg_replace('/\sabp="([^"]+)"/','',$s);
		$s = preg_replace('/<(span|div)\sid="_mce([^>]+)>(.*)<\/\\1>/U','\\3',$s);
		$s = str_replace(' class="mceSelected"','',$s);
		$s = str_replace('<li><p>','<li>',$s);
		$s = str_replace('</p></li>','</li>',$s);
		
		return $s;
	}

	/*
	public function latinTextPlain($st) {
		if (!$st) return '';
		$st = Parser::toLat($st);
		$st = str_replace(' ','',$st);
		return preg_replace("/[^0-9A-Za-z\(\)\._\s]/i",'\\1', $st);
	}
	
	public function latinText($st) {
		if (!$st) return '';
		return self::toLat($st);
	}
	
	public function latinSystemName($st) {
		if (!$st) return '';
		$st = Parser::toLat($st);
		$st = str_replace('&#039;','',$st);
		$st = str_replace(' ',URL_SPACE,$st);
		$st = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1', $st);
		return trim($st);
	}
	public static function isCharAlpha($s) {
		
	}
	
	*/
	const ALPHA_LATIN = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	const ALPHA_CYRILLIC = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯабвгдеёжзийклмнопрстуфхцчшщьыъэюя';
	const ALPHA_ROMAN = 'ÜÕÄÖŠŽüõäöšž';
	const NUMBERS = '1234567890_-.()';
	
	public static function isAlphaNumeric($str,$retAlpha = false,$latinOnly = false) {
		$alpha = self::NUMBERS;
		$alpha .= self::ALPHA_LATIN;
		if (!$latinOnly) {
			$alpha .= self::ALPHA_CYRILLIC;
			$alpha .= self::ALPHA_ROMAN;	
		}
		if (!$str && $retAlpha) return $alpha;
		$arrAlpha = str_split($alpha,1);
		$return = $str;
		$arrStr = str_split($str,1);
		$c = count($arrStr);
		for ($i=0; $i<$c; $i++) {
			if (!in_array($arrStr[$i],$arrAlpha)) {
				$return = false;
				break;
			}
		}
		return $return;
	}
	
	/*
		Html_SideBySide
		Html_Inline
		Html_Array
		Text_Unified
		Text_Context
	*/
	public static function textDiff($old, $new, $rend = 'Html_Inline') {
		require_once FTP_DIR_ROOT.'inc/lib/Diff.php';
		$options = array(
			//'ignoreWhitespace' => true,
			//'ignoreCase' => true,
		);

		// Initialize the diff class
		$diff = new Diff(explode("\n",$old), explode("\n",$new), $options);
		$e = explode('_',$rend);
		
		require_once FTP_DIR_ROOT.'inc/lib/Diff/Renderer/'.$e[0].'/'.$e[1].'.php';
		$class = 'Diff_Renderer_'.$rend;
		return $diff->render(new $class);
		
	}
	
	public static function _textDiff($old,$new,$repl = true) {
		if (!is_file(FTP_DIR_ROOT.'inc/lib/textdiff.php')) return 'No textDiff library detected';
		$er = error_reporting();
		error_reporting(E_ALL ^ E_NOTICE);
		include_once(FTP_DIR_ROOT.'inc/lib/textdiff.php');
		$diff = new diff(htmlspecialchars($old),htmlspecialchars($new));
		$colors = array (
			'delete'		=> 'FAA596' // red
			,'add'			=> 'B8E8AC' // green
			,'change_from'	=> 'E7EBF5' // blue
			,'change_to'	=> 'F0FD97' // yellow
		);
		
		if ($repl) $ret = str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',nl2br($diff->render($colors)));
		$ret = $diff->render($colors);
		error_reporting($er);
		return $ret;
	}
	/*
	public static function strHtmlLines($t,$l = 2,$replace = "\r\n") {
		$lines = preg_split('/<(br\s?\/?|p|\/p|hr|\/div|li)>/i',$t);
		$t = '';
		$n = $j = 0;
		foreach ($lines as $line) {
			if ($j>$l) break;
			$line = trim($line);
			if (!strlen($line)) {
				if ($n>=$m) continue;
				$n++;
			} else $n = 0;
			$j++;
			$t .= $line.$replace;
		}
		return $t;
	}
	*/
	public static function strLines($st, $m = 2) {
		$st = str_replace("\r\n","\n",$st);
		$st = str_replace("\r",'',$st);
		$st = preg_replace("/\s?\n+\s?/","\n",$st);
		$lines = explode("\n",$st);
		$st = '';
		$n = 0;
		foreach ($lines as $line) {
			if (!strlen(trim($line))) {
				if ($n>=$m) continue;
				$n++;
			} else $n = 0;
			$st .= $line."\n";
		}
		return trim($st);
	}
	public static function strTabs($st) {
		if (!strstr($st,"\t")) return $st;
		$lines = explode("\n",$st);
		$st = '';
		$tabs = array();
		foreach ($lines as $i => $line) {
			if (trim($line)=='') continue;
			$line = str_replace(" ",'',$line);
			$tabs[$i] = strlen($line)-strlen(ltrim($line));
		}
		if (@$tabs[0]==0 && @$tabs[1]>1) {
			array_shift($tabs);
		}
		$min = min($tabs);
		foreach ($lines as $i => $line) {
			$st .= preg_replace("/^".str_repeat(" *[\t] *",$min)."(.*)/",'\\1',$line)."\n";
		}
		return rtrim($st);
		return $st;
	}
	private function _strPrint($t, $by_this = false) {
		if (!$by_this || (!$this->stripped && !$this->params['editor'])) {
			$t = self::strPrint($t);
			if ($by_this) $this->stripped = true;
		}
		return $t;
	}
	public static function strPrint($t) {
		$t = htmlspecialchars($t,ENT_QUOTES,'utf-8');
		$t = str_replace("\r",'',$t);
		$t = str_replace("\n","<br />\n",$t);
		$t =  str_replace("\t",'&nbsp;&nbsp;&nbsp;&nbsp;',$t);	
		return $t;
	}
	/*
	public static function stripBuffer($buffer){
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $buffer);
		$buffer = preg_replace('/\s+/', ' ',$buffer);
		$buffer = str_replace("> <", "><", $buffer);
		$buffer = str_replace(" &nbsp;", "&nbsp;", $buffer);
		$buffer = str_replace("&nbsp; ", "&nbsp;", $buffer);
		return $buffer;
	}
	*/
	
	// parse
	public static function noEmails($s){
		$r = '***@\\1';
		//$s = ereg_replace("[-a-z0-9!#$%&\'*+/=?^_`{|}~]+@([.]?[a-zA-Z0-9_/-])*",$e,$s);
		return preg_replace("/[^@\s]*@([^@\s]*\.[^@\s])*/",$r,$s);
	}
	public static function noLinks($s){
		
		$p = "/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+([A-Za-z0-9\.\/%&=\?\-_]+)/i";
		$r = '[removed].com';
		return preg_replace($p, $r, $s);
		
		/*
		$l = 'www.****.com';
		$s = eregi_replace('(<a [^<]*href=["|\']?([^ "\']*)["|\']?[^>].*>([^<]*)</a>)',$l,$s);
		$s = ereg_replace("[a-zA-Z]+://([.]?[a-zA-Z0-9_/-]*)*",$l,$s);
		$s = ereg_replace("(^| )(www([-]*[.]?[a-zA-Z0-9_/-?&%])*)",$l,$s); 
		$s = ereg_replace("[-a-z0-9!#$%&\'*+/=?^_`{|}~]+[.]+([-a-z0-9]*)",$l,$s);
		return $s;
		*/
	}
	
	
	// Is
	public static function isNoHTML($t) {
		return !self::isHTML($t);
	}
	public static function isHTML($t) {
		if (!$t) return $t;
		return preg_match('/<+([\S\s]+)>+/i', $t) ? $t : false;
	}
	public static function isAbused($s) {
		$words = str_word_count($s,1);
		$count = 0;	
		foreach($words as $w) {
			if(strlen($w)>3&&$w==upper($w)) $count++;
		}	
		return $count > 3;
	}
	
	
	
	// parse
	public static function formatURLs($t) {
		$t = ' '.$t;
		$t = preg_replace("#([\s]+)([a-z]+?)://([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)#i", "\\1<a href=\"\\2://\\3\" rel=\"nofollow\" target=\"_blank\">\\3</a>", $t);
		$t = preg_replace("#([\s]+)www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" rel=\"nofollow\" target=\"_blank\">\\2.\\3\\4</a>", $t);
		$t = preg_replace("#([\s]+)([a-z0-9\-_.]+)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $t);
		$t = substr($t, 1);
		return $t;
	}
	
	public static function stripLink($l,$start = STRIP_LINK_START, $end = STRIP_LINK_END) {
		if (!$l) return '';
		if (($start + $end) < strlen($l)) {
			$l = substr($l,0,$start).'...'.substr($l, -$end);
		}
		return $l;
	}
	public static function shortenLinks($t) {
		return preg_replace('/([^"])(https?://(.+)(\s|\n|"|\'|<|>|)/Ue',"$1.self::stripLink('$2').$3",$t);
	}
	public static function shortenLinksReplace($t) {
		if (!$t) return '';
		if (strpos($t,'</a>') || strpos($t,'</A>')) {
			
		} else {
			$t = ereg_replace('(^| |\n)[-a-z0-9!#$%&\'*+/=?^_{|}~]+@([.]?[a-zA-Z0-9_/-])*','<a href="mailto:\\1">\\1</a>',$t);
			$t = ereg_replace('[^(http|ftp|https|call)+://](www([-]*[.]?[a-zA-Z0-9\-]*)\.([a-zA-Z]{2,4}))','<a target="_blank" rel="nofollow" href="http://\\1">{{%ls%}}\\1{{%le%}}</a>',$t);
			$t = preg_replace('/{{%ls%}}(.*){{%le%}}/e',"self::stripLink('$1')",$t);
		}
		return $t;
	}
	
	public static function thImages($t) {
		$max_w = $this->params['image_max_width'];
		$max_h = $this->params['image_max_height'];
		if (!preg_match_all('/\ssrc="([^\?]+)"/Ui',$t,$matches)) return $t;
		foreach ($matches[1] as $i => $src) {
			$src = str_replace('../','',trim($src,'/'));
			if ($src && is_file($src)) {
				list ($w, $h) = getimagesize($src);
				if (!$w) continue;
				if ($w > $max_w || $h > $max_h) {
					$replace = ' src="/th.php?p='.$src.'&w='.$max_w.'&h='.$max_h.'" onClick="ShowPicture(\''.strjava($src).'\','.$w.','.$h.')"';
					$t = str_replace($matches[0][$i],$replace,$t);
				}
			}
		}
		return $t;
	}
	
	
	private static function detectImg($n,$path) {
		$ret = 0;
		$handle=opendir($path);
		while ($fi = readdir($handle)) {
			$info = pathinfo($fi);
			if ($fi!="." && $fi!=".." && $info["extension"]=="png") {
				list($v,$name)=explode("_",$fi);
				if ($name==$n) {
					$ret = $v;
					break;
				}
			}
		}
		closedir($handle);
		return $ret;
	}
	public function mathImage($text,$size=10,$bbcode=false) {
		if (!is_file(FTP_DIR_ROOT.'inc/lib/mathpublisher.php') || strlen($text)>200) return $text;
		$nameimg = md5(trim($text).$size).'.png';

		if (!Conf()->g('MATH_FORMULA')) {
			require FTP_DIR_ROOT.'inc/lib/mathpublisher.php';	
		}
		$v = self::detectimg($nameimg,rtrim(FTP_DIR_MATH_IMG,'/'));
		if ($v==0) {
			//the image doesn't exist in the cache directory. we create it.
			$formula = new expression_math(tableau_expression(trim($text)));
			$formula->dessine($size);
			$v = 1000-imagesy($formula->image)+$formula->base_verticale+3;
			//1000+baseline ($v) is recorded in the name of the image
			ImagePNG($formula->image,FTP_DIR_MATH_IMG.$v.'_'.$nameimg);
		}
		$valign=$v-1000;
		Conf()->s('MATH_FORMULA', $text);
		Conf()->s('MATH_IMAGE', HTTP_DIR_MATH_IMG.$v.'_'.$nameimg);
		if ($bbcode) return '[math_img=/'.HTTP_DIR_MATH_IMG.$v.'_'.$nameimg.' '.$valign.']'.$text.'[/math_img]';
		else return '<img src="/'.HTTP_DIR_MATH_IMG.$v.'_'.$nameimg.'" style="vertical-align:'.$valign.'px;display:inline-block;" alt="'.$text.'" title="'.$text.'"/>';
	}
	
	
	public function formatURL($URL) {
		if (!$URL) return '';
		if (strpos($URL,'://')) {
			$host = substr(substr($URL,strpos($URL,'://')),3);
			$server = substr($URL,0,strpos($URL,'://')).'://';
		} else {
			$host = $URL;
			$server = 'http://';
		}
		return $server.$host;
	}
	public function formatPrice($price) {
		if (!$price) return false;
		if (preg_match("/[^\-0-9\.\,\s]/s",$price)) {
			return false;
		}
		if (!strpos($price,' ') && !strpos($price,',')) {
			number_format($price,Site()->locale['number_format'][0],Site()->locale['number_format'][1],Site()->locale['number_format'][2]);
		}
		$price = preg_replace("/[^\-0-9\.]/", '', str_replace(',','.',$price));
		if (substr($price,-3,1)=='.') {
			$sents = '.'.substr($price,-2);
			$price = substr($price,0,strlen($price)-3);
		} elseif (substr($price,-2,1)=='.') {
			$sents = '.'.substr($price,-1);
			$price = substr($price,0,strlen($price)-2);
		} else {
			$sents = '.00';
		}
		$price = preg_replace('/[^\-0-9]/', '', $price);
		return number_format($price.$sents,Conf()->g3('LOCALE','number_format',0),Conf()->g3('LOCALE','number_format',1),Conf()->g3('LOCALE','number_format',2));
	}
	
	/*
	public function format_price($price, $currency) {
		return $price.' '.$currency;	
	}
	*/
	
	
	
	
	// check, returns FALSE if error '==='

	/*
	public static function stripTags($string, $replace = false) {
		if ($replace && $replace!==true) {
			$search = array('@<script[^>]*?>.*?</script>@si',
							'@<style[^>]*?>.*?</style>@siU',
							'@<[\/\!]*?[^<>]*?>@si',
							'@<![\s\S]*?--[ \t\n\r]*>@'
			);
			return preg_replace($search, $replace, $string);	
		}
		elseif ($replace===true) {
			return Parser::strip_tags($string);
			
			$stripped = '';	
			$pos1 = 0;
			$pos2 = strpos($string, '<', $pos1);
			while ($pos2 !== false) {
				$stripped .= substr($html, $pos1, $pos2-$pos1);
				$pos1 = strpos($html, '>', $pos2+1);
				if ($pos1 === false) {
					$pos1 = strlen($string);
					break;
				}
				$pos1 = $pos1 + 1;
				$pos2 = strpos($string, '<', $pos1);
			}
			$stripped .= substr($string, $pos1);
			return $stripped;
		} else {
			return preg_replace('/<([\s\S].*)>([\s\S].*)<\/(\w+)>/Usim','$2',$string);
		}
	}
	*/
	
	/*
	public static function isICQ($icq) {
		return ($icq > 100000)?$icq:false;
	}
	public static function isSkype($skype) {
		return (strlen($skype) > 5 && preg_match("/^[A-Za-z0-9_\\.\\-]+\$/",$skype))?$skype:false;
	}
	
	public static function isIP($ip){
		if(!eregi("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$", $ip)) $return = false;
		else $return = true;	
		$tmp = explode(".", $ip);
		if($return == true){
			foreach($tmp as $sub){
				$sub = $sub * 1;
				if($sub<0 || $sub>256) $return = false;
			}
		}
		return $return===false?false:$ip;
	}
	*/
	public static function isDate($date,$r = '-') {
		$date = str_replace('/',$r,$date);
		$date = str_replace(' ',$r,$date);
		$date = str_replace('|',$r,$date);
		$date = str_replace('.',$r,$date);
		$date = str_replace(',',$r,$date);
		if (preg_match('/^(0[1-9]|[1-2][0-9]|3[0-1])\\'.$r.'?(0[1-9]|1[0-2])\\'.$r.'?([0-9]{2,4})$/i',$date,$match) ) { 
			return array_splice($match,1,4);
		} 
		elseif (preg_match('/^([0-9]{2,4})\\'.$r.'?(0[1-9]|1[0-2])\\'.$r.'?(0[1-9]|[1-2][0-9]|3[0-1])$/i',$date,$match) ) { 
			return array_splice($match,1,4);
		} else { 
			return false; 
		} 
	}
	public static function isURL($url, $absolute = true) {
		if (!strpos($url,'://')) $url = 'http://'.$url;
		$chars = '[a-z0-9\/:_\-_\.\?\$,;~=#&%\+]';
		if ($absolute) {
			return preg_match("/^(http|https|ftp):\/\/". $chars ."+$/i", $url)?$url:false;
		} else {
			return preg_match("/^". $chars ."+$/i", $url)?$url:false;
		}
	}

	public static function isPhone($phone) {
		if (!$phone) return $phone;
		return preg_match('/^((\(?\+([0-9]{1,3})(\)?))?)+\s?([\-]?)\s?+(([0-9\s]{7,12}))$/Ui',$phone)?$phone:false;
	}
	/*
	public static function isExt($t, $ext, &$e) {
		if (!is_array($ext)) $ext = explode(',',str_replace(' ','',str_replace('.','',trim($ext))));
		$e = ext($t);
		return in_array($e, $ext) ? $t : false;
	}
	*/
	public static function isPrice($t) {
		return Parser::formatPrice($t);	
	}
	
	
	
	public static function bb_sm_get($shuffle = true, $amount=0) {
		if ($sm = Cache::get('dir_images/sm/',true)) {
			if ($shuffle) shuffle($sm);
			if ($amount) $sm = array_splice($sm,0,$amount);
			return $sm;
		}
		$sm = array();
		if ($handle = opendir(FTP_DIR_ROOT.'tpls/img/sm/')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..' && substr($file,-4)=='.gif') $sm[] = substr($file,0,-4);
			}
			closedir($handle);
		}
		Cache::save('dir_images/sm/',$sm,true);
		if ($amount) $sm = array_splice($sm,0,$amount);
		if ($shuffle) shuffle($sm);
		return $sm;
	}
	
	public static function bb_sm_replace() {
		if (Conf()->is('smiles')) return Conf()->g('smiles');
		return array (
			'smile'			=> array(':)',':-)')
			,'wink'			=> array(';)',';-)')
			,'surprised'	=> array(':O')
			,'tongue'		=> array(':P')
			,'cry'			=> array(':&#039;(')
			,'dry'			=> array('&lt;_&lt;')
			,'happy'		=> array('^_^')
			,'sad'			=> array(':-(',':(')
			,'wacko'		=> array('%-)')
			,'angry'		=> array('&gt;;(')
			,'biggrin'		=> array(':D')
			,'heart'		=> array('(l)','(L)')
			,'cool'			=> array('B-)','b-)')
		);
	}
	
	private static function bb_flash_video($src,$width=425,$height=355,$player = 0, $autoplay = 'false') {
		$player = (int)$player;
		$autoplay = $autoplay ? $autoplay : 'false';
		$players = array (
			0	=> array('url'=>'/misc/flvplayer.swf?autostart='.$autoplay.'&file=','width'=>425,'height'=>355),
			1	=> array('url'=>'/misc/_player.swf?play='.$autoplay.'&videosrc=','width'=>289,'height'=>230)
		);
		if (!strstr($src,'://')) $src = '/'.trim($src,'/');
		$ex = explode('.',$src);
		if (end($ex)=='flv') {
			$src = $players[$player]['url'].$src;
			if (!$width) $width = $players[$player]['width'];
			if (!$height) $height = $players[$player]['height'];
		}
		// set dimensions
		if (!$height && $width) $height = $width;
		if (!$width || $width<50 || $width>700) $width = 425;
		if (!$height || $height<50 || $height>700) $height = 350;
		
		return '<div align="center" class="flash"><object width="'.$width.'" height="'.$height.'"><param name="movie" value="'.$src.'"></param><param name="wmode" value="transparent"></param><embed src="'.$src.'" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed></object></div>';
	}
	/*
	* Parse BB video helper function
	*/
	private static function bb_parse_video($t) {
		$t = self::after_preg($t);
		$t = '<object '.str_replace(array('&gt;','&lt;','&quot;','&#039;','&#39;','&amp;'),array('>','<','"','\'','\'','&'),$t).'</object>';
		if (!preg_match('/\ssrc="(https?:\/\/([^"]+))"/i',$t,$match)) return htmlspecialchars($t);
		return self::bb_flash_video($match[1]);
	}
	
	
	public static function bb_smilies($t) {
		foreach (self::bb_sm_replace() as $filename => $sm) {
			$t = preg_replace('~(^|>|[[:space:]])('.str_replace('\|','|',preg_quote(join('|',$sm),'~')).')~', '\\1<img src="/tpls/img/sm/'.$filename.'.gif" alt="'.$filename.'" title="'.$filename.'" style="vertical-align:-6px;display:inline-block;" />', $t);
		}
		$t = preg_replace('~:('.str_replace('.gif','',join('|',self::bb_sm_get(false))).'):~','<img src="/tpls/img/sm/\\1.gif" alt="\\1" title="\\1" style="vertical-align:-6px;display:inline-block;" />', $t);
		return $t;
	}
	
	public static function bb_videos($t) {
		$t = self::before_preg($t);
		$t = preg_replace('/&lt;object(.*)&lt;\/object&gt;/ie',"self::bb_parse_video('$1')",$t);
		$t = self::after_preg($t);
		$t = @preg_replace('/\[youtube=(.+)(\?v=|\/v\/)([0-9a-z_\-]+)(&(.*))?(\swidth=([0-9]+))?(\sheight=([0-9]+))?\]/Uei',"self::bb_flash_video('http://www.youtube.com/v/$3','$7','$9')",$t);
		$t = @preg_replace('/\[flash=(.*)(\swidth=([0-9]*))?(\sheight=([0-9]*))?(\sstyle=([1-9]))?\]/Ue',"self::bb_flash_video('$1','$3','$5','$7')",$t);
		return $t;
	}
	
	public static function bb_is_empty($m) {
		$arr  = array('img','youtube','video','flash');
		$m = str_replace('http://','',$m);
		foreach ($arr as $a) {
			$m = preg_replace('/\['.$a.'=([^\]]+)/','\\1',$m);	
		}
		
		$m = str_replace(array(' ',"\t","\r","\n"),'',preg_replace('/\[\/?([^\]]+)\]/','',$m));
		return !$m;
	}
	
	
	
	
	
	// BBCODE
	public function bbcode($t, $e = false) {
		$t = self::after_preg($t);
		$t = $this->removeInvisibleCharacters($t);
		
		
		if ($this->length>25000) {
			$t = $this->_strPrint($t, true);
			return $t;
		}
		
		if (defined('USE_MATHLIB') && USE_MATHLIB) {
			$t = $this->mathfilter($t,12,$this->params['striphtml']);
		}
		
		$this->bb_prepare($t);
		
		if ($this->params['striphtml'] || $e) {
			if ($e) $this->stripped = false;
			$t = preg_replace('/>\s+</',' ',$t);
			$t = str_replace('  ',' ',$t);
			$t = str_replace('  ',' ',$t);
			$t = $this->strip_tags($t);
			$t = $this->_strPrint($t, true);
		}
		
		$t = self::bb_quotes($t);
		
		if ($this->params['smilies_only']) return self::bb_smilies($t);
		$this->bb_tags($t);
		/*
		if (!$this->stripped && !$this->params['editor']) $t = nl2br($t);
		*/
		if (!$this->no_smilies) {
			$t = self::bb_smilies($t);
		}
		$t = self::bb_videos($t);
		
		$this->bb_img($t);
		$this->bb_urls($t);
		$this->bb_mailto($t);
		$this->bb_fix($t);
		$this->invalid_html($t);
		return $t;
	}
	public function mathfilter($t,$size,$bbcode = false) {
		$t = stripslashes($t);
		$size = max($size,10);
		$size = min($size,24);
		if (!preg_match_all("|\[m\](.+)\[/m\]|s", $t, $regs, PREG_SET_ORDER)) return $t;
		if (!$regs) return $t;
		foreach ($regs as $math) {
			$m = str_replace('[m]','',$math[0]);
			$m = str_replace('[/m]','',$m);
			$m = str_replace('&amp;','&',$m);
			$m = str_replace('&gt;','>',$m);
			$m = str_replace('&lt;','<',$m);
			$m = str_replace('&quot;','"',$m);
			$code = self::mathimage(trim($m),$size,$bbcode);
			$t = str_replace($math[0], $code, $t);
		}
		return $t;
	}
	
	public static function bb_quotes($t) {
	//	$t = str_replace('"','&quot;',$t);
		$t = preg_replace('/\'(.{0,2})(\s|$)/','&#8217;\\1\\2',$t); // '
		$t = preg_replace('/(\d)%%/','\\1&#8240',$t); // %%
		$t = preg_replace('/\s--\s/',' &#8213; ',$t); // --
		$t = preg_replace('/\s-\s/',' &#8211; ',$t); // -
		$t = preg_replace('/(^|[^a-z])"([^"]+)"([^a-z]|$)/','\\1&#8220;\\2&#8221;\\3',$t); // "texts"
		$t = preg_replace('/([a-z0-9;])([\.]{3})($|\s)/','\\1&#8230;\\3',$t); // ...
		//$t = str_replace('/:)/','#12484;',$t); // :)
		$t = str_replace(' 1/2 ',' ½ ',$t);
		return $t;
	}
	
	private function bb_prepare(&$t) {

		$arr = array('table','tr','th','td');
		foreach ($arr as $a) {
			$t = preg_replace('/\s*<(\/)?'.$a.'[^>]*>\s*/i','[\\1'.$a.']',$t);
		}
		$t = preg_replace('/<(\/)?(o|u)l[^>]*>/i','[\\1list]',$t);
		$t = preg_replace('/\s*<li[^>]*>/i','[*]',$t);
		$t = str_replace('</li>','[/*]',$t);
		$t = preg_replace('/<img\s+[^s]*src="http:\/\/([^"]+)"[^>]*>/Ui','[img=http://\\1]',$t);
		
		$t = preg_replace('/<a\s+[^h]*href="http:\/\/([^"]+)".*>(.+)<\/a>/Ui','[url=http://\\1]\\2[/url]',$t);
		
		$t = preg_replace('/\[\s+/','[',$t);
		$t = preg_replace('/\s+\]/',']',$t);
		$t = preg_replace('/\]\s+\[/','] [',$t);
		$arr  = array('img','youtube','video','flash');
		foreach ($arr as $a) {
			$t = str_replace('['.$a.'=http://]','',$t);
			$t = str_replace('['.$a.'=]','',$t);
		}
		$t = str_replace('[url=http://][/url]','',$t);
		$t = str_replace('[url=http://]','',$t);
		/*
		if (strstr($t,'[list')) {
			$t = preg_replace('/\[list([\s\S]+)\[\/list\]/ie',"'[list'.\$this->bb_list_fix('$1').'[/list]'",$t);
		}
		if (strstr($t,'[table]')) {
			$t = preg_replace('/\[table]([\s\S]+)\[\/table\]/ie',"'[table]'.\$this->bb_table_fix('$1').'[/table]'",$t);
		}
		*/
	}
	
	private function bb_list_fix($t) {
		$t = str_replace('\"','"',$t);
		$t = str_replace("\n*",'[*]',$t);
		$t = str_replace(array("\r","\n"), array('',' '), $t);
		$t = str_replace('  ',' ',$t);
		$t = str_replace('  ',' ',$t);
		return $t;
	}
	private function bb_table_th_fix($t) {
		$t = str_replace('\"','"',$t);
		
		return '[tr][th]'.str_replace('|','[/th][th]',$t).'[/th][/tr][tr][td]';
	}
	private function bb_table_fix($t) {
		$t = str_replace('\"','"',$t);
		$t = preg_replace('/\n\-(.*)\n\*/Ue','$this->bb_table_th_fix(\'$1\')',$t);
		$t = str_replace("\n*",'[/td][/tr] [tr][td]',$t);
		$t = str_replace("|",'[/td][td]',$t);
		return $t;
	}
	
	
	
	private function bb_tags(&$t) {
		//$t = preg_replace('/\:[0-9a-z\:]+\]/si',']',$t);
		static $r = array (
			'[b]'		=> '<b>',
			'[/b]'		=> '</b>',
			'[i]'		=> '<i>',
			'[/i]'		=> '</i>',
			'[u]'		=> '<u>',
			'[/u]'		=> '</u>',
			'[s]'		=> '<s>',
			'[/s]'		=> '</s>',
			'[small]'	=> '<small>',
			'[/small]'	=> '</small>',
			'[big]'		=> '<big>',
			'[/big]'	=> '</big>',
			'[sup]'		=> '<sup>',
			'[/sup]'	=> '</sup>',
			'[sub]'		=> '<sub>',
			'[/sub]'	=> '</sub>',
			'[hr]'		=> '<hr />',
			'[br]'		=> '<br />',
			'[p]'		=> '<p>',
			'[/p]'		=> '</p>',
			
			'[center]'	=> '<div align=center>',
			'[left]'	=> '<div align=left>',
			'[right]'	=> '<div align=right>',
			'[justify]'	=> '<div align=justify>',
			'[c]'		=> '<div align=center>',
			'[l]'		=> '<div align=left>',
			'[r]'		=> '<div align=right>',
			'[j]'		=> '<div align=justify>',
			
			'[/center]'	=> '</div>',
			'[/left]'	=> '</div>',
			'[/right]'	=> '</div>',
			'[/justify]'=> '</div>',
			'[/c]'		=> '</div>',
			'[/l]'		=> '</div>',
			'[/r]'		=> '</div>',
			'[/j]'		=> '</div>',
		);
		$t = strtr($t,$r);
		unset($r);
		// H1-6
		$t = preg_replace('/\[h([1-6])](.*)\[\/h\\1]/Usi','<h\\1>\\2</h\\1>',$t);	

		// [#]
		$t = str_replace('[#]','<a href=javascript:; onclick="$(this).next().slideDown();$(this).fadeOut()">'.lang('show hidden text..').'</a><div style=display:none class=hidden_text>',$t);
		$t = str_replace('[/#]','</div>',$t);
		
		
		$t = preg_replace('/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/si','<span style=color:\\1>', $t);
		$t = preg_replace('/\[font=([^\]]+)\]/si','<span style="font-family:\'\\1\'">',$t);
		$t = preg_replace("/\[highlight=(\#[0-9A-F]{6}|[a-z]+)\]/si", "<span style=background-color:\\1;>", $t);
		$t = preg_replace("/\[size=([1-7]?[0-9])\]/Usi", '<span style="font-size:\\1px;line-height:normal">', $t);
		$t = preg_replace('/\[quote=([^\]]*)?\]/Usi','<fieldset class=quote><legend>'.lang('Quote').': \\1</legend>',$t);
		$t = preg_replace('/\[reply=([^\]]+)\]/Usi','<fieldset class=reply><legend>'.lang('Reply').': \\1</legend>',$t);

		static $r2 = array(
			'[code]' => '<div class="code_wrap text" style="white-space:nowrap"><div class="code_top">Code</div><div class="code">',
			'[/code]' => '</fieldset>',
			'[/reply]' => '</fieldset>',
			'[/quote]' => '</fieldset>',
			'[/color]' => '</span>',
			'[/font]' => '</span>',
			'[/highlight]' => '</span>',
			'[/size]' => '</span>',
			
			'[list=1]' => '<ol style=list-style-type:decimal;>',
			'[list=a]' => '<ol style=list-style-type:lower-alpha;>',
			'[list=A]' => '<ol style=list-style-type:upper-alpha;>',
			'[list=i]' => '<ol style=list-style-type:lower-roman;>',
			'[list=I]' => '<ol style=list-style-type:upper-roman;>',
			'[list]' => '<ol style=list-style-type:disc>',
			'[/list]' => '</ol>',
			'[*]' => '<li>',
			'[/*]' => '</li>',
			
			//'[table]'	=> '<table>',
			//'[/table]'	=> '</table>',
		);
		$t = strtr($t,$r2);
		
		/*
		if (strstr($t,'<table>')) {
			$t = preg_replace('/\<table>([\s\S]+)\<\/table\>/ie',"'<table>'.\$this->bb_table_fill('$1').'</table>'",$t);
		}
		*/
	}
	

	private function bb_table_fill($t) {
		$t = str_replace('\"','"',$t);
		static $r = array(
			'[tr]'	=> '<tr>',
			'[/tr]'	=> '</tr>',
			'[td]'	=> '<td>',
			'[/td]'	=> '</td>',
			'[th]'	=> '<th>',
			'[/th]'	=> '</th>',
		);
		return strtr($t,$r);
	}

	private function bb_fix(&$t) {
		$err = false;
		$tag = 'table';
		$ex = explode('</'.$tag.'>',$t);
		$opened = 0;
		foreach ($ex as $closed => $e) {
			$opened += substr_count($e, '<'.$tag.'>');
			if (!$closed && $opened < 1) {
				$err = true;
				break;
			}
			elseif ($closed && $opened < $closed) {
				$err = true;
				break;
			}
		}
		if ($err) {
			static $r = array(
				'<tr>' => '[tr]',
				'</tr>' => '[/tr]',
				'<td>' => '[td]',
				'</td>' => '[/td]',
				'<th>' => '[th]',
				'</th>' => '[/th]',
				'<table>' => '[table]',
				'</table>' => '[/table]'
			);
			$t = strtr($t,$r);
		}
		
		$tags = array('table','fieldset','list','div','span');
		foreach ($tags as $g) {
			$c1 = substr_count($t, '<'.$g);
			if (!$c1) continue;
			$c2 = substr_count($t, '</'.$g.'>');
			if ($c1 > $c2) $t .= str_repeat('</'.$g.'>',$c1-$c2);
		}
		$tags = array('b','i','u','s','small','big','sup','sub','p');
		foreach ($tags as $g) {
			$c1 = substr_count($t,'<'.$g.'>');
			if (!$c1) continue;
			$c2 = substr_count($t,'</'.$g.'>');
			if ($c1 > $c2) $t .= str_repeat('</'.$g.'>',$c1-$c2);
		}
	}
	
	
	public function bb_img(&$t) {
		$t = preg_replace('/\[img(=([^\]]+))?\]([^\[]+)\[\/img\]/','<img src="\\3" alt="\\1" />',$t);
		$t = preg_replace('/\[img=\s*([[:alpha:]]+:\/\/)?([^\]]+)\s*\]/','<img src="\\1\\2" class="img" alt="" />',$t);
		$t = preg_replace('/\[math_img=\s*([^\s]+)\s([^\]]+)\]([^\[]+)\[\/math_img\]/Us','<img src="\\1" alt="\\3" title="\\3" style="vertical-align:\\2px;display:inline-block;" class="math_img" />',$t);
	}
	public function bb_urls(&$t) {
		$t = preg_replace('/\[url=\s*([[:alpha:]]+:\/\/)?([^\]]+)\s*\]([^\[]+)\[\/url\]/','<a href="http://\\2" rel="nofollow" target="_blank" class="href wrap_href">\\3</a>',$t);
		$t = preg_replace('/(([[:alpha:]]+:\/\/|www\.)[^<>[:space:]]+[[:alnum:]\/?])/','<a href="\\1" rel="nofollow" target="_blank" class="href">\\1</a>', $t);
	}
	public function bb_mailto(&$t) {
		$t = preg_replace('/(^|\s|\n)([a-z0-9_\-\.]+)@([a-z0-9-]{1,64})\.([a-z]{2,6})($|\s|\n)/', '\\1<a class="mailto" href="mailto:\\2@\\3.\\4">\\2@\\3.\\4</a>\\5', $t);
	}

	public function invalid_html(&$t) {
		$t = preg_replace('/on(mouseover|mouseout|mouseup|mousemove|mousedown|mouseenter|mouseleave|mousewheel|contextmenu|click|dblclick|load|unload|submit|blur|focus|resize|scroll|change|reset|select|selectionchange|selectstart|start|stop|keydown|keyup|keypress|abort|error|dragdrop|move|moveend|movestart|activate|afterprint|afterupdate|beforeactivate|beforecopy|beforecut|beforedeactivate|beforeeditfocus|beforepaste|beforeprint|beforeunload|begin|bounce|cellchange|controlselect|copy|cut|paste|dataavailable|datasetchanged|datasetcomplete|deactivate|drag|dragend|dragleave|dragenter|dragover|drop|end|errorupdate|filterchange|finish|focusin|focusout|help|layoutcomplete|losecapture|mediacomplete|mediaerror|outofsync|pause|propertychange|progress|readystatechange|repeat|resizeend|resizestart|resume|reverse|rowsenter|rowexit|rowdelete|rowinserted|seek|syncrestored|timeerror|trackchange|urlflip)/i',"0n\\1",$t);
		$t = preg_replace("/javascript/i","j&#097;v&#097;script",$t);
		$t = preg_replace("/alert/i","&#097;lert",$t);
		$t = preg_replace("/behavior/i","beh&#097;vior",$t);
	}
	

	##	CODE
	public static function before_preg($t) {
		$t = str_replace('"','[[:QUOTE:]]',$t);
		$t = str_replace('\\','[[:BACKSLASH:]]',$t);
		return $t;
	}
	public static function after_preg($t) {
		$t = str_replace('[[:QUOTE:]]','"',$t);
		$t = str_replace('[[:BACKSLASH:]]','\\',$t);
		return $t;
	}
	private function code($t,$only = false) {
		self::replace_code_vars($t);
		self::prepare_code($t);
		$t = self::before_preg($t);
		$t = preg_replace("/<%%codestart(\w+)%%>([\s\S].+)<%%codeend\\1%%>/seU","\$this->geshi('$2','$1')",$t);
		return $t;
	}
	private function geshi($t, $code) {
		$t = str_replace("\r","\n",$t);
		$t = str_replace(array('<br />','<BR />'),"\n",$t);
		$t = preg_replace('/<(p|div|font)([^>]*)>/i',"\n",$t);
		$t = str_replace(array('</P>','</p>','</div>','</DIV>','font','FONT'),'',$t);
		$t = str_replace('&gt;','>',$t);
		$t = str_replace('&lt;','<',$t);
		$t = str_replace('&nbsp;',' ',$t);
		$t = str_replace('&amp;','&',$t);
		$t = str_replace("\n\n","\n",$t);
		$t = str_replace("\n\n","\n",$t);
		$t = $this->strTabs($t);
		$t = trim(self::after_preg($t));
		$t = self::GeshiHighlight($t,$code,$this->params['numbers'],false);
		$t = preg_replace('/(\n\s)?&nbsp;$/','',$t);
		return $t;
	}
	private function bb_after_code($t) {
		$t = self::before_preg($t);
		$t = preg_replace("/(^|<!--codeend-->)(.*)(<!--codestart-->|$)/seU","\$this->bbcode('$2',true)",$t);
		$t = str_replace(array('<!--codestart-->','<!--codeend-->'),'',$t);
		return $t;
	}
	private function nobb_after_code($t) {
		$t = self::before_preg($t);
		$t = preg_replace("/(^|<!--codeend-->)(.*)(<!--codestart-->|$)/seU","\$this->_nobb_after_code('$2')",$t);
		$t = self::after_preg($t);
		$t = str_replace(array('<!--codestart-->','<!--codeend-->'),'',$t);
		return $t;
	}
	private function plain_after_code($t) {
		$t = $this->fixPre($t);
		$t = str_replace(array('<!--codestart-->','<!--codeend-->'),'',$t);
		return $t;
	}
	private function fixPre($t, $after = false) {
		if ($after) {
			$t = self::after_preg($t);
			$t = str_replace('<br />','',$t);
			$t = str_replace('<BR />','',$t);
			return '<pre>'.$t.'</pre>';
		} else {
			$t = self::before_preg($t);
			return preg_replace('/<pre>([\s\S]+)<\/pre>/seU',"\$this->fixPre('$1',true)",$t);
		}
	}
	private function _nobb_after_code($t) {
		$t = self::after_preg($t);
		$t = $this->_strPrint($t);
		$t = preg_replace('/&lt;(.*)&gt;/Ue',"\$this->_addColors('$1', true)",$t);	
		return $t;
	}
	private function _addColors($t, $e = false) {
		// if ($e) $t = str_replace('\"','"',$t);
		$t = preg_replace('/&quot;(.*)&quot;/U','&quot;<span style="color:#918B2F">\\1</span>&quot;',$t);
		$t = '<span style="color:#888">&lt;'.$t.'&gt;</span>';
		return $t;
	}
	
	
	public static function code_tags($t) {
		$t = str_replace(array('<?php','<?=','<?','?>','[script]','[/script]'), array('[php]','[php]echo ','[php]','[/php]','[javascript]','[/javascript]'), $t);
		return $t;
	}
	private static function prepare_code(&$st) {
		foreach (self::arrGeshiCodes() as $code => $name) {
			$st = str_replace('['.$code.']','<!--codestart--><div class="code_wrap '.$code.'" style="white-space:nowrap"><div class="code_top">'.$name.'</div><div class="code"><%%codestart'.$code.'%%>',$st);
			$st = str_replace('[/'.$code.']','<%%codeend'.$code.'%%></div></div><!--codeend-->',$st);
		}
	}	
	private static function replace_code_vars(&$st) {
		$st = str_replace('[script]','[javascript]',$st);
		$st = str_replace('[/script]','[/javascript]',$st);
		$st = str_replace('[style]','[css]',$st);
		$st = str_replace('[/style]','[/css]',$st);
		$st = str_replace('[html]','[html4strict]',$st);
		$st = str_replace('[/html]','[/html4strict]',$st);
	}
	
	
	public static function geshiHighlight($str, $language='php', $numbers = false, $PCRE = false) {
		if (!is_file(FTP_DIR_ROOT.'inc/lib/geshi/geshi.php')) {
			return '<pre>'.$str.'</pre>';
		}
		require_once(FTP_DIR_ROOT.'inc/lib/geshi/geshi.php');
		$str = self::after_preg($str);
		$path = FTP_DIR_ROOT.'inc/lib/geshi/';
		$geshi = new GeSHi($str, $language, $path);
		$geshi->set_header_type(GESHI_HEADER_NONE);
		$geshi->set_link_target(true);
		$geshi->enable_keyword_links(false);
		$str = '';
	/*
		$geshi->set_header_content('<div style="display:none">'.self::arrGeshiCodes($language).'</div>');
		$geshi->set_footer_content();
	*/
	/*
		$geshi->enable_classes();
		$str .= '<style>'.$geshi->get_stylesheet(true).'</style>';
		$geshi->set_header_type(GESHI_HEADER_PRE_VALID);
	*/
		if ($numbers) $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 5);
		$str .= $geshi->parse_code();
		return $str;
	}

	public static function arrGeshiCodes($code = NULL) {
		static $ret = array (
			'actionscript'	=> 'ActionScript', 
			'asp'			=> 'ASP',
			'c'				=> 'C', 
			'cfm'			=> 'ColdFusion', 
			'cpp'			=> 'C++', 
			'csharp'		=> 'C#', 
			'css'			=> 'CSS',
			'delphi'		=> 'Delphi',
			'html4strict'	=> 'HTML (4.0.1)', 
			'java'			=> 'Java', 
			'java5'			=> '&nbsp;&nbsp;Java 5', 
			'javascript'	=> 'Javascript', 
			'latex'			=> 'LaTeX', 
			'matlab'		=> 'Matlab', 
			'mirc'			=> 'mIRC', 
			'mysql'			=> 'MySQL',
			'oracle8'		=> 'Oracle 8', 
			'pascal'		=> 'Pascal', 
			'perl'			=> 'Perl', 
			'php'			=> 'PHP', 
			'phpbrief'		=> '&nbsp;&nbsp;PHP (Brief version)', 
			'python'		=> 'Python', 
			'reg'			=> 'Windows Registry', 
			'robots'		=> 'robots.txt', 
			'ruby'			=> 'Ruby', 
			'smalltalk'		=> 'Smalltalk', 
			'smarty'		=> 'Smarty', 
			'smartyhtml'	=> 'Smarty &amp; HTML', 
			'sql'			=> 'SQL', 
			'text'			=> 'Plain text',
			'tsql'			=> 'T-SQL', 
			'vb'			=> 'VisualBasic', 
			'vbnet'			=> 'VB.NET', 
			'xml'			=> 'XML'
		);
		return $code ? $ret[$code] : $ret;
	}
	
	
	
	
	
	
	public static function highlightWords($text,$highlight,$style=1) {
		if (is_array($highlight)) $highlight = join(' ',$highlight);
		$loosematch = strstr($highlight,'*')?1:0;
		$keywords   = str_replace('*', '', str_replace("+"," ",str_replace("++","+",str_replace('-','', trim($highlight)))));
		$keywords	= str_replace('\\', '&#092;', $keywords);
		$word_array = array();
		$endmatch   = "(.)?";
		$beginmatch = "(.)?";
		switch($style){
			case 0 :
				$s  = '<span style="background-color:#FFFF00;border-left:1px dotted #E8E204">';
				$e	= '</span>';
			break;
			case 1 :
				$s  = '<span style="background-color:#F5FD66;">';
				$e	= '</span>';
			break;
			case 3 :
				$s  = '<b>';
				$e	= '</b>';
			break;
			default:
				$s  = '<span style="background-color:#FFFF00;padding:2px;margin:2px;">';
				$e	= '</span>';
			break;
		}
		if ($keywords) {
			if (preg_match("/,(and|or),/i",$keywords)) {
				while (preg_match("/,(and|or),/i",$keywords,$match)) {
					$word_array = explode(",".$match[1].",", $keywords);
					$keywords = str_replace($match[0],'',$keywords);
				}
			} 
			else if (strstr($keywords,' ')) {
				$word_array = explode(' ',str_replace('  ',' ',$keywords));
			} else {
				$word_array[] = $keywords;
			}		
			if (!$loosematch) {
				$beginmatch = '(^|\'|\s|>|;|\|\{)';
				$endmatch   = '(\s|\'|,|\.|!|<|&|$|\}|")';
			}
			if (is_array($word_array)) {
				foreach ($word_array as $keywords) {
					preg_match_all("/{$beginmatch}(".preg_quote($keywords,'/')."){$endmatch}/is",$text,$matches);
					for ($i=0;$i<count($matches[0]);$i++) {
						$text = str_replace($matches[0][$i],$matches[1][$i].$s.$matches[2][$i].$e.$matches[3][$i],$text);
					}
				}
			}
		}
		return $text;
	}
	
	public static function isEmail($email,$type = 'normal') {
		if ((!$email || !is_string($email)) && !in_array($type,array('get','ret','return'))) return false;
		if (strlen($email)>100) return false;
		if (!is_bool($email) && !strstr($email,'@') && !strstr($email,'.')) return false;
		switch ($type) {
			case 'normal':
				$email = trim($email);
				return preg_match('/^[\'_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.(([a-z]{2,3})|(aero|coop|info|museum|name))$/i',$email)?$email:false;
			break;
			case 'fast':
				$email = trim($email);
				return strpos($email,'@');
			break;
			case 'hard':
			default:
				$email = trim($email);
				$user = '[a-zA-Z0-9_\-\.\+\^!#\$%&*+\/\=\?\`\|\{\}~\']+';
				$domain = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.?)+';
				$ipv4 = '[0-9]{1,3}(\.[0-9]{1,3}){3}';
				$ipv6 = '[0-9a-fA-F]{1,4}(\:[0-9a-fA-F]{1,4}){7}';
				return preg_match("/^$user@($domain|(\[($ipv4|$ipv6)\]))$/", $email)?$email:false;
			break;
		}
	}
	
	

	public static function url_encode($st) {
		if (Site::$Mode==MODE_FRIENDLY) return str_replace(array('%2F', '%26', '%23', '//'),array('/', '%2526', '%2523', '/%252F'),rawurlencode($st));
		else return str_replace('%2F', '/', rawurlencode($st));
	}
	public static function replaceBadChars($str) {
		static $replacement,$toreplace;
		if (!isset($toreplace)) {
			include_once('lib_replace.php');
		}
		return str_replace($toreplace,$replacement,$str);
	}
	public static function strQuotes($str, $replace = ' ') {
		$quotes = array('\\',"\x27","\x22","\x60","\t","\n","\r",'\'',',','/','¬',';',':','@','~','[',']','{','}','=',URL_VALUE,'*','&','^','$','<','>','?','!','"','+','#');
		$ret = str_replace($quotes,$replace,$str);
		if (strlen($replace)) {
			$ret = str_replace($replace.$replace,$replace,$ret);
			$ret = str_replace($replace.$replace,$replace,$ret);
		}
		return $ret;
	}
	public static function strSearch($st) {
		if (empty($st)) return $st;
		elseif (is_array($st)) $st = $st[key($st)];
		if (is_array($st)) return '';
		$st = stripslashes($st);
		$st = preg_replace("/ +/", "  ",' '.$st.' ');
		$st = self::decodeEntities($st);
		$st = trim($st);
		$st = preg_replace("/ +/",' ',$st);
		return $st;
	}
	public static function strRegex($text) {
		$goodquotes = array('-','+','#');
		$repquotes = array('\-','\+','\#');
		$text = stripslashes($text);
		$text = self::strQuotes($text);
		$text = str_replace($goodquotes, $repquotes, $text);
		return $text;
	}
	public static function strip_tags($str) {
		do {
			$count = 0;
			$str = preg_replace('/(<)([^>]*?<)/' , ' &lt;$2' , $str , -1 , $count);
		} while ($count > 0);
		
		$str = strip_tags($str);
		$str = str_replace('&lt;','<',$str);
		return $str;
	}
	/*
	public function br2nl($t) {
		$t = preg_replace("#(?:\n|\r)?<br(\s\/)?>(?:\n|\r)?#i","\n",$t);
		return $t;
	}
	*/
	public static function CyrLat($st,$dir='lat') {
		if (is_array($st)) {
			foreach ($st as $k => $s) {
				$st[$k] = self::CyrLat($s, $dir);
			}
			return $st;
		}
		// lowercase
		static $utf_cyr_array = array('&#1072;','&#1073;','&#1074;','&#1075;','&#1076;','&#1077;','&#1105;','&#1078;','&#1079;','&#1080;','&#1081;','&#1082;','&#1083;','&#1084;','&#1085;','&#1086;','&#1087;','&#1088;','&#1089;','&#1090;','&#1091;','&#1092;','&#1093;','&#1094;','&#1095;','&#1096;','&#1097;','&#1100;','&#1099;','&#1098;','&#1101;','&#1102;','&#1103;');
		static $cyr_array = array('кс','ч','ш','щ','ь','ы','ъ','ю','я','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','э');
		static $lat_array = array('x','ch','sh','shh','\'','y','`','ju','ja','a','b','v','g','d','e','jo','zh','z','i','j','k','l','m','n','o','p','r','s','t','u','f','h','c','e');
		// UPPERCASE
		static $UTF_CYR_ARRAY = array('&#1040;','&#1041;','&#1042;','&#1043;','&#1044;','&#1045;','&#1025;','&#1046;','&#1047;','&#1048;','&#1049;','&#1050;','&#1051;','&#1052;','&#1053;','&#1054;','&#1055;','&#1056;','&#1057;','&#1058;','&#1059;','&#1060;','&#1061;','&#1062;','&#1063;','&#1064;','&#1065;','&#1068;','&#1067;','&#1066;','&#1069;','&#1070;','&#1071;');
		static $CYR_ARRAY = array('Ч','Ч','Ш','Ш','Щ','Щ','Ь','Ы','Ъ','Ю','Ю','Я','Я','А','Б','В','Г','Д','Е','Ё','Ё','Ж','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Э');
		static $LAT_ARRAY = array('Ch','CH','Sh','SH','Shh','SHH','\'','Y','`','Ju','JU','Ja','JA','A','B','V','G','D','E','Jo','JO','Zh','ZH','Z','I','J','K','L','M','N','O','P','R','S','T','U','F','H','C','E');
		if ($dir=='lat') {
	//		$st = htmlentities($st, ENT_NOQUOTES, 'UTF-8');
			$st = str_replace($utf_cyr_array,$lat_array,$st);
			$st = str_replace($cyr_array,$lat_array,$st);
			$st = str_replace($UTF_CYR_ARRAY,$LAT_ARRAY,$st);
			$st = str_replace($CYR_ARRAY,$LAT_ARRAY,$st);		
		}
		else {
			
			static $cyr_array2 = array('кс','ч','ш','щ','ю','я','ё','ж');
			static $lat_array2 = array('x','ch','sh','shh','ju','ja','jo','zh');
			static $CYR_ARRAY2 = array('Ч','Ч','Ш','Ш','Щ','Щ','Ю','Ю','Я','Я','Ё','Ё','Ж','Ж');
			static $LAT_ARRAY2 = array('CH','Ch','SH','Sh','SHH','Shh','JU','Ju','JA','Ja','JO','Jo','ZH','Zh');
			
			$st = str_replace($lat_array2,$cyr_array2,$st);
			$st = str_replace($LAT_ARRAY2,$CYR_ARRAY2,$st);
			$st = str_replace($lat_array,$cyr_array,$st);
			$st = str_replace($LAT_ARRAY,$CYR_ARRAY,$st);
		}
		return $st;
	}
	public static function toLat($st) {
		static $from = array('õ','Õ','ä','Ä','Ü','ü','Ö','ö','&otilde;','&Otilde;','&auml;','&Auml;','&Uuml;','&uuml;','&Ouml;','&ouml;','&Scaron;','&scaron;','Š','š','Ž','ž','&szlig;','ß');
		static $to = array('o','O','a','a','U','u','O','o','o','O','a','A','U','u','O','o','Sh','sh','Sh','sh','Z','z','B','B');
		$st = str_replace($from,$to,$st);
		$st = Parser::CyrLat($st);
		return $st;
	}
	private static function _decodeEntities($prefix,$codepoint,$original,&$table,&$exclude) {
		if (!$prefix) {
			if (isset($table[$original])) return $table[$original];
			else return $original;
		}
		if ($prefix=='#x') $codepoint = base_convert($codepoint,16,10);
		else $codepoint = preg_replace('/^0+/','',$codepoint);
		if ($codepoint<0x80) $str = chr($codepoint);
		else if ($codepoint<0x800) $str = chr(0xC0|($codepoint>>6)).chr(0x80|($codepoint&0x3F));
		else if ($codepoint<0x10000) $str = chr(0xE0|($codepoint>>12)).chr(0x80|(($codepoint>>6)&0x3F)).chr(0x80|($codepoint&0x3F));
		else if ($codepoint<0x200000)$str=chr(0xF0|($codepoint>>18)).chr(0x80|(($codepoint>>12)&0x3F)).chr(0x80|(($codepoint>>6)&0x3F)).chr(0x80|($codepoint&0x3F));
		if (in_array($str, $exclude)) {
			return $original;
		} else {
			return $str;
		}
	}
	public static function decodeEntities($text, $exclude = array()) {
		if (!$text) return '';
		if (!isset($GLOBALS['EntityTable'])) {
			$GLOBALS['EntityTable'] = array_flip(get_html_translation_table(HTML_ENTITIES));
			$GLOBALS['EntityTable'] = array_map('utf8_encode', $GLOBALS['EntityTable']);
			$GLOBALS['EntityTable']['&apos;'] = "'";
		}
		$newtable = array_diff($GLOBALS['EntityTable'], $exclude);
		$text = preg_replace('/&(#x?)?([A-Za-z0-9]+);/e', 'self::_decodeEntities("$1", "$2", "$0", $newtable, $exclude)', $text);
		$text = str_replace(array('|;','  '),' ',$text);
		$text = str_replace('  ',' ',$text);
		return trim($text);
	}

    public static function removeInvisibleCharacters($str) {
        $non_displayables = array('/%0[0-8bcef]/','/%1[0-9a-f]/','/[\x00-\x08]/','/\x0b/','/\x0c/','/[\x0e-\x1f]/');
        do {
            $cleaned = $str;
            $str = preg_replace($non_displayables, '', $str);
        } while ($cleaned != $str);

        return $str;
    }
	
	
	
	
	public static function getTagAttrContents($explode, $tag, $contents, $getOuter = false) {
	//	$old_contents = $contents;
		if (!$explode) $explode = '<'.$tag;
		$contents = substr($contents, strpos($contents,$explode));
		$ex = explode('<'.$tag,$contents);
		array_shift($ex);
		$contents = '';
		$closed = 0;
		foreach ($ex as $i => $e) {
	//		if (strstr($e,'</'.$tag.'>')) $closed += count(explode('</'.$tag.'>',$e)) - 1;
			if (strstr($e,'</'.$tag.'>')) $closed += substr_count($e, '</'.$tag.'>');
			if ($closed >= $i+1) break;
			$contents .= '<'.$tag.$e;
		}
		$last = '<'.$tag.$ex[$i];
		$ex = explode('</'.$tag.'>',$last);
		$total = count($ex) - 1;
		if ($total) {
			foreach ($ex as $j => $e) {
				$contents .= $ex[$j].'</'.$tag.'>';
				if ($closed - $total + $j == $i) break;
			}
			if (!$getOuter) {
				$contents = substr(substr($contents,strlen($explode)),0,-strlen($tag)-3);
			}
		} else {
			return false;
			if (!$getOuter) {
				$contents = substr($contents,strlen($explode));
			}
		}
		return $contents;
	}
	public static function iDfile($file, $back = false) {
		if (!$back) {
			return str_replace(array('/','.'),array('-s7h-','-d6t-'),$file);
		} else {
			return str_replace(array('-s7h-','-d6t-'),array('/','.'),$file);
		}
	}
}


