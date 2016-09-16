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
* @file       inc/Html.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Html {
	
	public static function startArray($type) {
		$keyAsVal = false;
		$lang = false;
		$key = -1;
		$getVal = false;
		if ($pos = strpos($type,':')) {
			$key = substr($type,$pos + 1);
			$type = substr($type,0,$pos);
			$getVal = true;
		}
		return array($type, $key, $keyAsVal, $lang, $getVal);
	}
	public static function endArray($ret, $keyAsVal, $lang, $key, $options, $settings, $getVal = false) {
		$_l  = '';
		if ($options!==-1 && is_array($ret) && $settings==='dropdown') {
			$_l = '_';
		}
		if ($lang && is_array($ret)) {
			foreach ($ret as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $_k => $_v) {
						$ret[$k][$_k] = lang($_l.$_v);	
					}
				} else {
					$ret[$k] = lang($_l.$v);
				}
			}
		}

		if ($key!==-1) {
			if (!$key) return '';
			if (strpos($key,':')) {
				$ex = explode(':',$key);
				$k = $ex[1];
				if (!$k || is_array($ret[$ex[0]])) $k = key($ret[$ex[0]]);
				return $ret[$ex[0]][$k];
			} else {
				if (isset($ret[$key]) && ($getVal || is_array($ret[$key]))/* IF IN then return VALUE not ARRAY! if getVal() FFS :(*/) {
					return $ret[$key];
				}
				elseif (isset($ret[key($ret)])) {
					foreach ($ret as $arr) {
						if (isset($arr[$key])) return $arr[$key];	
					}
				}
			}
		}
		if ($options!==-1 && is_array($ret)) {
			switch ($settings) {
				case 'dropdown':
					$ret = self::buildOptions($options, $ret, $keyAsVal);
				break;
				default:
					if (!is_array($settings)) return '2nd argument must be an array';
					$type = isset($settings['type']) ? $settings['type'] : 'checkbox';
					$name = isset($settings['name']) ? $settings['name'] : 'undefined';
					$colsPerRow = isset($settings['colsPerRow']) ? $settings['colsPerRow'] : 1;
					$attr = isset($settings['attr']) ? $settings['attr'] : '';
					$colAttr = isset($settings['colAttr']) ? $settings['colAttr'] : '';
					$selected = isset($settings['selected']) ? $settings['selected'] : $options;
					$fn = isset($settings['func']) ? $settings['func'] : false;
					$ret = self::buildRadios(
						$type,
						$name,
						$selected,
						$ret,
						$colsPerRow,
						$attr,
						$colAttr,
						$fn
					);
				break;
			}
		}
		return $ret;
	}
	
	private static $options_next = 0;
	private static $options_name = '';
	private static $options_filled = array();
	private static $options_select_names = array();
	private static $options_go = false;
	
	public static function select_id($name = false) {
		self::$options_select_names[++self::$options_next] = $name;
		self::$options_go = true;
		return 'select_'.self::$options_next;
	}
	
	private static $options = array();
	
	public static function js_options($selected, $array, $keyAsVal) {
		if (self::$options_go) {
			$name = self::$options_select_names[self::$options_next];
			if (!$name) $name = self::$options_next;
			$name = 'select_'.$name;
			$id = 'select_'.self::$options_next;
			if (!in_array($name, self::$options_filled)) {
				$_array = array();
				foreach ($array as $k => $v) $_array[] = array('k'=>$k,'v'=>$v);
				$js = 'var '.$name.'='.json($_array).';';
				Index()->addJScode($js);
			}
			self::$options_filled[] = $name;
			Index()->addJSready('S.G.options('.$name.',\''.$id.'\','.($keyAsVal ? 'true' : 'false').');', true);
			self::$options_go = false;
			self::$options_name = '';
			return true;
		}
		return false;
	}
	
	public static function buildOptions($selected, $array, $keyAsVal = false, $s = ' selected="selected"', $fn = false, $gray = true) {
		if (!is_array($array) || !$array) return '';
		$r = '';
		$selected_array = is_array($selected);
		if (!$selected_array && !$fn && self::js_options($selected, $array, $keyAsVal)) {
			return false;	
		}
		self::$options_go = false;
		self::$options_name = '';
		$selected_string = is_string($selected);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$r .= '<optgroup label="'.$k.'">';
				$r .= self::buildOptions($selected,$v,$keyAsVal,$s,$fn, false);
				$r .= '</optgroup>';
			} else {
				if ($keyAsVal) {
					$k = $v;
				}
				if (strlen($k)>4 && substr($k,0,4)=='    ') {
					$k = '';
					$v = trim($v);
				}
				$k = strform($k);
				if (!$selected_array) {
					if (!$selected) {
						$sel = (string)$selected===(string)$k;
					} else {
						if ($selected_string) {
							$sel = (string)$selected===(string)$k;
						} else {
							$sel = $selected==$k;
						}
					}
				} else {
					$sel = in_array($k,$selected);
				}
				if ($fn) $v = $fn($v);
				$r .= '<option value="'.$k.'"'.($sel?$s:'').''.(($gray && !$k)?' style="color:#666"':'').'>'.$v.'</option>';
			}
		}
		return $r;
	}
	
	public static function buildRadios($type,$name,$sel,$arr,$cpr=4,$ch = '',$td = '',$fn = false, $tr_dir = true) {
		if (!$cpr) $cpr = 4;
		$width = intval(100/$cpr);
		$i = 0;
		$ret = '';
		foreach ((array)$arr as $key => $val) {
			$onc = '';
			if (is_array($val)) {
				$v = $val[0];
				$onc = ' onclick="'.$val[1].'"';
				if (isset($val[2])) $onc .= ' '.$val[2];
			} else $v = $val;
			if (!is_array($sel)) {
				if ($sel==$key) $onc .= ' checked="checked"';
			} elseif (in_array($key,$sel)) $onc .= ' checked="checked"';
			if ($fn) $v = $fn($v);
			
			if ($i%$cpr==0) $ret .= '<tr>';
			$ret .= '<td width="'.$width.'%"'.$td.'><label><input type="'.$type.'"'.$ch.' name="'.$name.'" id="'.str_replace(array('[',']'),'',$name).'_'.$i.'" value="'.$key.'"'.$onc.'> '.$v.'</label></td>';
			if ($i%$cpr==$cpr-1) $ret .= '</tr>';
			
			// $tr_dir = 'TODO';
			$i++;
		}
		return $ret;
	}

	
	public static function getFileImg($filename,$ext = false, $getArr = NULL) {
		if (!$ext) $ext = ext($filename);
		$arr = array (
			'rm'	=> array('rm','rpas','rpvs')
			,'mov'	=> array('mov','mp4','m4v')
			,'swf'	=> array('swf','fla','flv')
			,'wmv'	=> array('wmv','asf','avi','wmvs')
			,'wma'	=> array('wma','wmas')
			
			,'mp3'	=> array('mp3')
			,'m4a'	=> array('m4a')
			,'wav'	=> array('wav')
			,'mid'	=> array('mid','midi')
			,'mpg'	=> array('mpg','mpeg')
			
			,'php'	=> array('php','php3','php5')
			,'doc'	=> array('doc','docx')
			,'chm'	=> array('chm')
			,'rtf'	=> array('rtf')
			,'txt'	=> array('txt','log','sql','ini')
			,'pdf'	=> array('pdf')
			,'ppt'	=> array('ppt')
			,'xls'	=> array('xls','csv','xlsx')
			,'zip'	=> array('zip','rar','gs','ace')
		);
		$ret = 'unknown';
		if ($getArr) return $arr[$getArr];
		foreach ($arr as $img => $extensions) {
			if (in_array($ext,$extensions)) {
				$ret = $img;
				break;
			}
		}
		if ($ret=='unknown' && is_file(FTP_DIR_ROOT.'tpls/img/ext/'.$ext.'.gif')) {
			$ret = $ext;
		}
		return $ret.'.gif';
	}
	
	
	public static function arrRange($from = 1,$to = 20,$step = 1, $key = true, $func = false) {
		$alpha = false;
		
		if (!is_numeric($from) && $from!==0) {
			$arr_eng = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
			$ARR_ENG = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
			$arr_cyr = array('а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ы','ъ','э','ю','я');
			$ARR_CYR = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ь','Ы','Ъ','Э','Ю','Я');
			if (in_array($from,$arr_eng) && in_array($to,$arr_eng)) $arr = $arr_eng;
			elseif (in_array($from,$ARR_ENG) && in_array($to,$ARR_ENG)) $arr = $ARR_ENG;
			elseif (in_array($from,$arr_cyr) && in_array($to,$arr_cyr)) $arr = $arr_cyr;
			elseif (in_array($from,$ARR_CYR) && in_array($to,$ARR_CYR)) $arr = $ARR_CYR;
			else {
				return trigger_error('Html::arrRange couldn\'t use '.$from.' '.$to.'',E_USER_WARNING);
			}
			$f = array_search($from,$arr);
			$t = array_search($to,$arr);
			if ($step>abs($f-$t)) $step = 1;
			$ret = array();
			if ($f<$t) {
				for ($i=$f;$i<=$t;$i+=$step) {
					if (!$key) $ret[] = $arr[$i];
					else $ret[$arr[$i]] = $arr[$i];
				}
			} else {
				for ($i=$f;$i>=$t;$i-=$step) {
					if (!$key) $ret[] = $arr[$i];
					else $ret[$arr[$i]] = $arr[$i];
				}
			}
			return $ret;
		}
		if ($step > abs($from-$to)) $step = 1;
		if ($from < $to) {
			if ($from===1 && $step>1) $ret[1] = 1;
			for ($i=$from;$i<=$to;$i+=$step) {
				if ($from==1 && $step%5==0 && $i && !$was) {
					$i = 1;
					$was = true;
				} 
				else if ($was && $i==$step+1) {
					$i = $i - 1;	
				}
				if (!$key) $ret[] = $i;
				else $ret[$i] = $i;
			}
		} else {
			if ($from===0 && $step>1) $ret[1] = 1;
			for ($i=$from;$i>=$to;$i-=$step) {
				if (!$key) $ret[] = $i;
				else $ret[$i] = $i;
			}
		}
		return $ret;
	}
	
	/*
	public static function purifyHTML($html) {
		// this is shit 
		$html = str_replace("\xEF\xBB\xBF", '', $html);
		if (!$html || !is_file(FTP_DIR_ROOT.'inc/lib/htmlpurifier/HTMLPurifier.auto.php')) return $html; // disable for admin
		require_once (FTP_DIR_ROOT.'inc/lib/htmlpurifier/HTMLPurifier.auto.php');
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core', 'Encoding', 'UTF-8');
		$config->set('HTML', 'Doctype', 'XHTML 1.0 Transitional');
		$config->set('Attr', 'EnableID', true);
		$config->set('Filter', 'YouTube', true);
		$config->set('Core', 'HiddenElements', 'script','style');
		$config->set('HTML', 'Allowed', 'table,ol,ul,div,code');
		$config->set('HTML', 'DefinitionID', 'enduser-customize.html tutorial');
		$config->set('HTML', 'DefinitionRev', 1);
		$purifier = new HTMLPurifier($config);
		$html = $purifier->purify($html);
		return $html;
	}
	*/
	
	
}