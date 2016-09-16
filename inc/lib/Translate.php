<?php

class Translate {
	
	public $engines = array(
		'google', 'yandex', 'bing'
	);
	private $engine = '';
	
	public function __construct($engine) {
		if ($engine && in_array($engine, $this->engines)) {
			$this->engine = $engine;
		}
	}
	
	public function translate($text, $from, $to, $force) {
		if (!$this->engine) return '';
		return $this->{$this->engine}($text, $from, $to, $force);
	}
	
	public function langs($lang, $langs = array()) {
		
		$_langs = array_label(require FTP_DIR_ROOT.'config/system/languages.php','name');
		return array(
			'from'	=> $_langs,
			'to'	=> $_langs
		);
	}
	
	private function yandex($text, $from, $to, $force) {
		if (!function_exists('curl_init')) return ($force ? $text : false);
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, 'http://translate.yandex.ru/tr.json/translate?srv=tr-text&id=812c6278-0-0&reason=auto');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_POST, 0);
		$post_data = array(
			'text'	=> $text,
			'lang'	=> $from.'-'.$to
		);
		curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($post_data));
		$r = curl_exec($c);
		curl_close($c);
		if (substr($r,0,1)!='"') return ($force ? $text : false);
		return trim($r,'"');
	}
	
	private function bing($text, $from, $to, $force) {
		
		$url = 'http://api.microsofttranslator.com/v2/Http.svc/Translate?appId='.BING_APP_ID.'&text='.urlencode($text).'&from='.$from.'&to='.$to;
		$response = @file_get_contents( 
			$url, 
			NULL, 
			stream_context_create( 
				array( 
					'http'	=> array( 
						'method'=> 'GET', 
						'header'=> "Referer: http://".$_SERVER['HTTP_HOST']."/\r\n" 
					) 
				)
			) 
		);
		
		if (!$response) {
			return ($force ? $text : false);
		} else {
			return self::_unescapeUTF8EscapeSeq($response);
		}
	}
	
	
	private function google($text, $from, $to, $force) {
		if ($to=='ee') $to = 'et';
		if ($from=='ee') $from = 'et';
		$ret = self::translate_google($text, $from, $to);
		if ($ret) return trim($ret);
		$post_data = array();
		$post_data['q'] = $text;
		$post_data['langpair'] = $from.'|'.$to;
		if (!function_exists('curl_init') && function_exists('json_decode')) return ($force ? $text : false);
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_POST, 0);
		curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($post_data));
		$b = curl_exec($c);
		curl_close($c);
		$json = json_decode($b, true);
		if (is_object($json)) {
			if ($json->responseStatus!=200) return ($force ? $text : false);
			return trim($json->responseData->translatedText);
		} else {
			if ($json['responseStatus']!=200) return ($force ? $text : false);
			return trim($json['responseData']['translatedText']);
		}
	}
	
	
	/** 
	* Translate a piece of text with the Google Translate API 
	* @return String 
	* @param $text String 
	* @param $from String[optional] Original language of $text. An empty String will let google decide the language of origin 
	* @param $to String[optional] Language to translate $text to 
	*/ 
	public static function translate_google($text, $from = '', $to = 'en') { 
		if (strlen($text)>=8190) return false;
		// v2?key=AIzaSyBs9j4oyIYEq5jqakg2ZpgU9p4pRcoMU1o&q=flowers&source=en&target=fr&callback=handleResponse
		$url = 'https://www.googleapis.com/language/translate/v2?key='.GOOGLE_API_KEY.'&q='.rawurlencode($text).'&source='.$from.'&target='.$to.'';
		//$url = 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q='.rawurlencode($text).'&langpair='.rawurlencode($from.'|'.$to);
		$response = @file_get_contents( 
			$url, 
			NULL, 
			stream_context_create( 
				array( 
					'http'	=> array( 
						'method'=> 'GET', 
						'header'=> "Referer: http://".$_SERVER['HTTP_HOST']."/\r\n" 
					) 
				)
			) 
		);
		if (!$response) return false;
		if (preg_match("/{\"translatedText\":\"([^\"]+)\"/i", $response, $matches)) { 
			return self::_unescapeUTF8EscapeSeq($matches[1]); 
		} 
		return false; 
	} 
	
	/** 
	* Convert UTF-8 Escape sequences in a string to UTF-8 Bytes. Old version. 
	* @return UTF-8 String 
	* @param $str String 
	*/ 
	public static function __unescapeUTF8EscapeSeq($str) { 
		return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return html_entity_decode(\'&#x\'.$matches[1].\';\', ENT_NOQUOTES, \'UTF-8\');'), $str); 
	} 
	
	/** 
	* Convert UTF-8 Escape sequences in a string to UTF-8 Bytes 
	* @return UTF-8 String 
	* @param $str String 
	*/ 
	public static function _unescapeUTF8EscapeSeq($str) { 
		return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches', 'return self::_bin2utf8(hexdec($matches[1]));'), $str); 
	} 
	
	/** 
	* Convert binary character code to UTF-8 byte sequence 
	* @return String 
	* @param $bin Mixed Interger or Hex code of character 
	*/ 
	public static function _bin2utf8($bin) { 
		if ($bin <= 0x7F) { 
			return chr($bin); 
		} else if ($bin >= 0x80 && $bin <= 0x7FF) { 
			return pack("C*", 0xC0 | $bin >> 6, 0x80 | $bin & 0x3F); 
		} else if ($bin >= 0x800 && $bin <= 0xFFF) { 
			return pack("C*", 0xE0 | $bin >> 11, 0x80 | $bin >> 6 & 0x3F, 0x80 | $bin & 0x3F); 
		} else if ($bin >= 0x10000 && $bin <= 0x10FFFF) { 
			return pack("C*", 0xE0 | $bin >> 17, 0x80 | $bin >> 12 & 0x3F, 0x80 | $bin >> 6& 0x3F, 0x80 | $bin & 0x3F); 
		} 
	} 
}