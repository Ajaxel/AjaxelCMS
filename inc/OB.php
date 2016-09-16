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
* @file       inc/OB.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class OB {	
	public static $friendly = false;
	public static $add_fn = array();
	private static $cache_name = '';
	
	public static function handler($html) {
		Site::mem('OB start');
		if (Site::$halted) return Site::$halted;
		self::ftp_ext($html);
		self::token($html);
		
		foreach (Index()->getVar(0,false,false) as $key => $value) {
			$html = str_replace (
				Index()->getVar($key,false,true),
				self::ob_value($key, $value),
				$html
			);
			if ($key=='title') {
				$html = str_replace (
					Index()->getVar($key.'_js',false,true),
					strjs(self::ob_value($key, $value)),
					$html
				);	
			}
		}
		if (Site::$mini) {
			self::noCache($html);
			return $html;
		}		
		unset($value,$key);
		Index()->My->ob($html);
		if (self::$friendly) {
			//$orig_html = $html;
			//$html = self::parse($html);
			//if (!$html) $html = self::friendly($orig_html);
			$html = self::friendly($html);
			//unset($orig_html);
		}
		
		if (!ADMIN && IS_ADMIN) {
		//	self::fix($html);
			self::edit($html);
		}
		self::add_fn($html);
		if (Site::$cache_enabled) Cache::savePageHtml($html);
		self::noCache($html);
		Site::mem('OB end');
		return $html;
	}
	
	private static function ob_value($key, $value) {
		if (in_array($key, array('title','description','keywords'))) {
			return trim(strip_tags(str_replace('<',' <',str_replace(array('&gt;','&lt;','&quot;'), array('>','<','"'), $value))),' ,');
		}
		return $value;
	}
	
	public static function handler_cached($html) {
		Cache::savePageHtml($html);
		self::noCache($html);
		return $html;	
	}
	
	public static function noCache(&$html) {
		foreach (Index()->getVar(0,true,false) as $key => $value) {
			$k = Index()->getVar($key,true,true);
			$_k = strform($k);
			$html = str_replace (
				array($k, $_k, strform($_k)),
				$value,
				$html
			);
		}
	}

	/*
	private static function stripBufferSkipTextareaTags($buffer){
		$poz_current = 0;
		$poz_end = strlen($buffer)-1;
		$result = '';
		while ($poz_current < $poz_end){
			$t_poz_start = stripos($buffer, '<textarea', $poz_current);
			if ($t_poz_start === false){
				$buffer_part_2strip = substr($buffer, $poz_current);
				$temp = Parser::stripBuffer($buffer_part_2strip);
				$result .= $temp;
				$poz_current = $poz_end;
			}
			else{
				$buffer_part_2strip = substr($buffer, $poz_current, $t_poz_start-$poz_current);
				$temp = Parser::stripBuffer($buffer_part_2strip);
				$result .= $temp;
				$t_poz_end = stripos($buffer, '</textarea>', $t_poz_start);
				$temp = substr($buffer, $t_poz_start, $t_poz_end-$t_poz_start);
				$result .= $temp;
				$poz_current = $t_poz_end;
			}
		}
		return $result;
	}
	*/


	
	private static function ftp_ext(&$html) {
		if (!FTP_EXT && strstr(HTTP_DIR_TPL,'://')) {
			$html = str_replace(' src="/',' src="',$html);
			$html = str_replace('url(\'/','url(\'',$html);
		}
		elseif (strlen(FTP_EXT)>1) {
		//	$html = preg_replace('/(\ssrc="|\.src=\')\/(?!('.str_replace('/','\/',ltrim(FTP_EXT,'/')).'))/','\\1'.FTP_EXT,$html);
			$html = str_replace(' src="/',' src="'.FTP_EXT,$html);
			$html = str_replace(' src="'.rtrim(FTP_EXT,'/').FTP_EXT,' src="'.FTP_EXT,$html);
			
			$html = str_replace('url(\'/','url(\''.FTP_EXT,$html);
			$html = str_replace('url(\''.rtrim(FTP_EXT,'/').FTP_EXT,'url(\''.FTP_EXT,$html);
		}
	}
	
	private static function token(&$html) {
		$html = str_replace('</form>','<input type="hidden" name="_t" value="'.$_SESSION['_t'].'" /></form>',$html);
	}
	
	public static function add($fn) {
		self::$add_fn[] = $fn;	
	}
	private static function add_fn(&$html) {
		if (self::$add_fn) {
			foreach (self::$add_fn as $fn) {
				$html = call_user_func_array($fn,array($html));
			}
		}	
	}
	/*
	private static function fix(&$html) {
		$html = str_replace('<br>','<br />',$html);
		$html = str_replace(array(' align="absmiddle"',' align="absMiddle"'),' align="middle"',$html);
		$html = preg_replace('/<img\s([^>]+)>/e','self::_fix(\'$1\')',$html);
	}
	private function _fix($html) {
		$html = str_replace('\"','"',$html);
		if (strstr($html, ' alt="')) {
			return '<img '.$html.'>';
		} else {
			return '<img alt="" '.$html.'>';
		}
	}
	*/
	
	private static function edit(&$html, $fix = false) {
		$html = preg_replace('/"<span id="a-edit([^\]]+)\]">(.+)<\/span>"/U','"\\2"',$html);
		if (self::$friendly) $html = str_replace('[[:EDIT_LINKS:]]','/',$html);
	}
	
	// for admin no edit, but copy
	public static function links($str) {
		$str = str_replace(' class="a-edit ',' class="a_edit ',$str);
		if (self::$friendly) {
			$str = preg_replace('/(\shref="|\saction="|location\.href=\')\/?\?/i','\\1[[:EDIT_LINKS:]]?',$str);
		}
		return $str;
	}
	
	/*
	public static function parse($html) {
		return substr(preg_replace('~(.*)(<textarea.*<\/textarea>)~se', "OB::friendly('$1',true).str_replace('\\\"','\"','$2')", $html.'<textarea></textarea>'),0,-21);
	}
	*/	
	public static function friendly($html, $preg = false) {
		if ($preg) $html = str_replace('\"','"',$html);
	//	$html = str_replace('&amp;','[[:AMP:]]',$html);
		if (strlen(HTTP_EXT)>1) $html = str_replace(' href="/"',' href="/?'.URL_KEY_HOME.'"',$html);
		//$html = preg_replace('~(\shref="|\saction="|location\.href=\')(\?|\/\?)([^;]*)(\'\+\$\(|"|\')~Uie',"self::_friendly('?$3','$1','$4')",$html);
		$html = preg_replace('/(\shref="|\saction=")\/?\?&?([^"]+)"/e',"self::_friendly('$2','$1')",$html);
	//	$html = str_replace('[[:AMP:]]','&amp;',$html);
		return $html;
	}
	private static function _friendly($url,$start) {
		if (!$url || $url=='?') return '/';
		$url = trim($url,'/');
		$url = str_replace('/',URL::FRIENDLY_SLASH,$url);
		$url = str_replace(array('&amp;','&','='),array('/','/',URL_VALUE),$url);
		$ext = URL::ext(true);
		if ($ext) $url = $ext.$url;
		return str_replace('\"','"',$start).'/'.$url.'"'; // (!strpos($url,'#') ? '/' : '')
	}
	
	
	
}