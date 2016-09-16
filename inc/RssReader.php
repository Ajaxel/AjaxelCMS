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
* @file       inc/RssReader.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class RssReader {
	var $channel = array();
	var $cur_write = false;
	var $main = '';
	var $cnt = 0;
	var $conf = array();
	
	function RssReader($url, $curl = true, $enc = 'UTF-8', $int_enc = 'UTF-8') {
		$this->conf['encoding'] = $enc;
		$this->conf['internal_encoding'] = $int_enc;
		$this->conf['url'] = $url;
		$this->conf['error'] = '';
		$this->conf['use_curl'] = $curl;
	}
	function conf($type = '') {
		if ($type=='cnt') return $this->cnt;
		elseif ($type)return $this->conf[$type];
		else return $this->conf;
	}
	function set($type, $val) {
		$this->conf[$type] = $val;
	}
	
	function get($url) {
		$rss = new RssReader($url);
		$data =& $rss->parse();
		if ($rss->conf('error')) {
			die($rss->conf('error'));
		}
		return $data;
	}

	function parse() {
		if (!$this->conf['url'] || !function_exists('xml_parser_create')) return '';
		$xml = xml_parser_create($this->conf['encoding']);
		xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, true);
		xml_parser_set_option($xml, XML_OPTION_TARGET_ENCODING, $this->conf['internal_encoding']);
		xml_set_element_handler($xml, array(&$this,'startElement'), array(&$this,'endElement'));
		xml_set_character_data_handler($xml, array(&$this,'characterData'));		
		$this->conf['encoding'] = xml_parser_get_option($xml, XML_OPTION_TARGET_ENCODING);
		if ($this->conf['use_curl'] && function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_URL, $this->conf['url']);
			$data = curl_exec($ch);
			curl_close($ch);
			if (!$data) {
				$this->conf['error'] = 'Unable to open '.$this->conf['url'];
				return false;
			}
			elseif (!xml_parse($xml, $data)) {
				$this->conf['error'] = sprintf('XML error: %s at line %d',xml_error_string(xml_get_error_code($xml)),xml_get_current_line_number($xml));
				xml_parser_free($xml);
				return false;
			}
		} else {
			if (!($fp = @fopen($this->conf['url'], 'r'))) {
				$this->conf['error'] = 'Unable to open '.$this->conf['url'];
				return false;
			}
			while ($data = fread($fp, 4096)) {
				if (!xml_parse($xml, $data, feof($fp))) {
					$this->conf['error'] = sprintf('XML error: %s at line %d',xml_error_string(xml_get_error_code($xml)),xml_get_current_line_number($xml));
					xml_parser_free($xml);
					return false;
				}
			}
		}
		xml_parser_free($xml);
		return $this->channel;
	}
	
	function decode($data) {
		return str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$data);
	}
	
	function startElement($parser, $name, $attrs) {
		switch($name) {
			case 'RSS':
			case 'RDF:RDF':
			case 'ITEMS':
				$this->cur_write = false;
			break;
			case 'CHANNEL':
				$this->main = 'CHANNEL';
			break;
			case 'IMAGE':
				$this->main = 'IMAGE';
				$this->channel['IMAGE'] = array();
			break;
			case 'ITEM':
				$this->main = 'ITEMS';
			break;
			default:
				$this->cur_write = $name;
			break;
		}
	}
	function endElement($xml, $name) {
		$this->cur_write = false;
		if ($name=='ITEM') $this->cnt++;
	}
	function characterData($xml, $data) {
		if (!$this->cur_write) return;
		switch($this->main) {
			case 'CHANNEL':
				if (isset($this->channel[$this->cur_write])) {
					$this->channel[$this->cur_write] .= $data;
				} else {
					$this->channel[$this->cur_write] = $data;
				}
			break;
			case 'IMAGE':
				if (isset($this->channel[$this->main][$this->cur_write])) {
					$this->channel[$this->main][$this->cur_write] .= $data;
				} else {
					$this->channel[$this->main][$this->cur_write] = $data;
				}
			break;
			case 'ITEMS':
				if (isset($this->channel[$this->main][$this->cnt][$this->cur_write])) {
					$this->channel[$this->main][$this->cnt][$this->cur_write] .= $data;
				} else {
					$this->channel[$this->main][$this->cnt][$this->cur_write] = $data;
				}
			break;
		}
	}
}