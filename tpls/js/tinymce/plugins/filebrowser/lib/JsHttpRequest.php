<?php
class JsHttpRequest {
	var $SCRIPT_ENCODING = "utf-8";
	var $SCRIPT_DECODE_MODE = '';
	var $LOADER = null;
	var $ID = null;
	var $RESULT = null;
	var $_uniqHash;
	var $_magic = 14623;
	var $_prevDisplayErrors = null;
	var $_contentTypes = array("script" => "text/javascript", "xml" => "text/plain", "form" => "text/html", "" => "text/plain");
	var $_toUtfFailed = false;
	var $_nonAsciiChars = '';
	var $_unicodeConvMethod = null;
	var $_emergBuffer = null;
	function JsHttpRequest($enc) {
		global $JsHttpRequest_Active;
		$GLOBALS['_RESULT'] =& $this->RESULT;
		if (preg_match('/^(.*)(?:&|^)JsHttpRequest=(?:(\d+)-)?([^&]+)((?:&|$).*)$/s', @$_SERVER['QUERY_STRING'], $m)) {
			$this->ID                = $m[2];
			$this->LOADER            = strtolower($m[3]);
			$_SERVER['QUERY_STRING'] = preg_replace('/^&+|&+$/s', '', preg_replace('/(^|&)' . session_name() . '=[^&]*&?/s', '&', $m[1] . $m[4]));
			unset($_GET['JsHttpRequest'], $_REQUEST['JsHttpRequest'], $_GET[session_name()], $_POST[session_name()], $_REQUEST[session_name()]);
			$this->_unicodeConvMethod = function_exists('mb_convert_encoding') ? 'mb' : (function_exists('iconv') ? 'iconv' : null);
			$this->_emergBuffer       = str_repeat('a', 1024 * 200);
			$this->_uniqHash          = md5('JsHttpRequest' . microtime() . getmypid());
			$this->_prevDisplayErrors = ini_get('display_errors');
			ini_set('display_errors', $this->_magic);
			ini_set('error_prepend_string', $this->_uniqHash . ini_get('error_prepend_string'));
			ini_set('error_append_string', ini_get('error_append_string') . $this->_uniqHash);
			if (function_exists('xdebug_disable'))
				xdebug_disable();
			ob_start(array(
				&$this,
				"_obHandler"
			));
			$JsHttpRequest_Active = true;
			$this->setEncoding($enc);
			$file        = $line = null;
			$headersSent = version_compare(PHP_VERSION, "4.3.0") < 0 ? headers_sent() : headers_sent($file, $line);
			if ($headersSent) {
				trigger_error("HTTP headers are already sent" . ($line !== null ? " in $file on line $line" : " somewhere in the script") . ". " . "Possibly you have an extra space (or a newline) before the first line of the script or any library. " . "Please note that JsHttpRequest uses its own Content-Type header and fails if " . "this header cannot be set. See header() function documentation for more details", E_USER_ERROR);
				exit();
			}
		} else {
			$this->ID             = 0;
			$this->LOADER         = 'unknown';
			$JsHttpRequest_Active = false;
		}
	}
	function isActive() {
		return !empty($GLOBALS['JsHttpRequest_Active']);
	}
	function getJsCode() {
		return file_get_contents(dirname(__FILE__) . '/JsHttpRequest.js');
	}
	function setEncoding($enc) {
		preg_match('/^(\S*)(?:\s+(\S*))$/', $enc, $p);
		$this->SCRIPT_ENCODING    = strtolower(!empty($p[1]) ? $p[1] : $enc);
		$this->SCRIPT_DECODE_MODE = !empty($p[2]) ? $p[2] : '';
		$this->_correctSuperglobals();
	}
	function quoteInput($s) {
		if ($this->SCRIPT_DECODE_MODE == 'entities')
			return str_replace(array(
				'"',
				'<',
				'>'
			), array(
				'&quot;',
				'&lt;',
				'&gt;'
			), $s);
		else
			return htmlspecialchars($s);
	}
	function php2js($a = false) {
		if (is_null($a))
			return 'null';
		if ($a === false)
			return 'false';
		if ($a === true)
			return 'true';
		if (is_scalar($a)) {
			if (is_float($a)) {
				$a = str_replace(",", ".", strval($a));
			}
			static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
			return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
		}
		$isList = true;
		for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
			if (key($a) !== $i) {
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList) {
			foreach ($a as $v) {
				$result[] = JsHttpRequest::php2js($v);
			}
			return '[ ' . join(', ', $result) . ' ]';
		} else {
			foreach ($a as $k => $v) {
				$result[] = JsHttpRequest::php2js($k) . ': ' . JsHttpRequest::php2js($v);
			}
			return '{ ' . join(', ', $result) . ' }';
		}
	}
	function _correctSuperglobals() {
		if ($this->LOADER == 'form')
			return;
		$rawPost = strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') == 0 ? (isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : @file_get_contents("php://input")) : null;
		$source  = array(
			'_GET' => !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null,
			'_POST' => $rawPost
		);
		foreach ($source as $dst => $src) {
			$s    = preg_replace('/%(?!5B)(?!5D)([0-9a-f]{2})/si', '%u00\\1', $src);
			$data = null;
			parse_str($s, $data);
			$GLOBALS[$dst] = $this->_ucs2EntitiesDecode($data);
		}
		$GLOBALS['HTTP_GET_VARS']  = $_GET;
		$GLOBALS['HTTP_POST_VARS'] = $_POST;
		$_REQUEST                  = (isset($_COOKIE) ? $_COOKIE : array()) + (isset($_POST) ? $_POST : array()) + (isset($_GET) ? $_GET : array());
		if (ini_get('register_globals')) {
		}
	}
	function _obHandler($text) {
		unset($this->_emergBuffer);
		unset($GLOBALS['JsHttpRequest_Active']);
		if (preg_match("/{$this->_uniqHash}(.*?){$this->_uniqHash}/sx", $text, $m)) {
			if (!ini_get('display_errors') || (!$this->_prevDisplayErrors && ini_get('display_errors') == $this->_magic)) {
				$text = str_replace($m[0], '', $text);
			} else {
				$text = str_replace($this->_uniqHash, '', $text);
			}
		}
		if ($m && preg_match('/\bFatal error(<.*?>)?:/i', $m[1])) {
			$this->RESULT = null;
		} else {
			if (!isset($this->RESULT)) {
				global $_RESULT;
				$this->RESULT = $_RESULT;
			}
		}
		$result   = array(
			'id' => $this->ID,
			'js' => $this->RESULT,
			'text' => $text
		);
		$text     = null;
		$encoding = $this->SCRIPT_ENCODING;
		$status   = $this->RESULT !== null ? 200 : 500;
		if (function_exists('array_walk_recursive') && function_exists('json_encode') && $this->_unicodeConvMethod) {
			$this->_nonAsciiChars = join("", array_map('chr', range(128, 255)));
			$this->_toUtfFailed   = false;
			$resultUtf8           = $result;
			array_walk_recursive($resultUtf8, array(
				&$this,
				'_toUtf8_callback'
			), $this->SCRIPT_ENCODING);
			if (!$this->_toUtfFailed) {
				$text     = json_encode($resultUtf8);
				$encoding = "UTF-8";
			}
		}
		if ($text === null) {
			$text = $this->php2js($result);
		}
		if ($this->LOADER != "xml") {
			$text = "" . ($this->LOADER == "form" ? 'top && top.JsHttpRequestGlobal && top.JsHttpRequestGlobal' : 'JsHttpRequest') . ".dataReady(" . $text . ")\n" . "";
			if ($this->LOADER == "form") {
				$text = '<script type="text/javascript" language="JavaScript"><!--' . "\n$text" . '//--></script>';
			}
			$status = 200;
		}
		if ($this->RESULT === null) {
			if (php_sapi_name() == "cgi") {
				header("Status: $status");
			} else {
				header("HTTP/1.1 $status");
			}
		}
		$ctype = !empty($this->_contentTypes[$this->LOADER]) ? $this->_contentTypes[$this->LOADER] : $this->_contentTypes[''];
		header("Content-type: $ctype; charset=$encoding");
		return $text;
	}
	function _toUtf8_callback(&$v, $k, $fromEnc) {
		if ($v === null || is_bool($v))
			return;
		if ($this->_toUtfFailed || !is_scalar($v) || strpbrk($k, $this->_nonAsciiChars) !== false) {
			$this->_toUtfFailed = true;
		} else {
			$v = $this->_unicodeConv($fromEnc, 'UTF-8', $v);
		}
	}
	function _ucs2EntitiesDecode($data) {
		if (is_array($data)) {
			$d = array();
			foreach ($data as $k => $v) {
				$d[$this->_ucs2EntitiesDecode($k)] = $this->_ucs2EntitiesDecode($v);
			}
			return $d;
		} else {
			if (strpos($data, '%u') !== false) {
				$data = preg_replace_callback('/%u([0-9A-F]{1,4})/si', array(
					&$this,
					'_ucs2EntitiesDecodeCallback'
				), $data);
			}
			return $data;
		}
	}
	function _ucs2EntitiesDecodeCallback($p) {
		$hex = $p[1];
		$dec = hexdec($hex);
		if ($dec === "38" && $this->SCRIPT_DECODE_MODE == 'entities') {
			$c = "&amp;";
		} else {
			if ($this->_unicodeConvMethod) {
				$c = @$this->_unicodeConv('UCS-2BE', $this->SCRIPT_ENCODING, pack('n', $dec));
			} else {
				$c = $this->_decUcs2Decode($dec, $this->SCRIPT_ENCODING);
			}
			if (!strlen($c)) {
				if ($this->SCRIPT_DECODE_MODE == 'entities') {
					$c = '&#' . $dec . ';';
				} else {
					$c = '?';
				}
			}
		}
		return $c;
	}
	function _unicodeConv($fromEnc, $toEnc, $v) {
		if ($this->_unicodeConvMethod == 'iconv') {
			return iconv($fromEnc, $toEnc, $v);
		}
		return mb_convert_encoding($v, $toEnc, $fromEnc);
	}
	function _decUcs2Decode($code, $toEnc) {
		static $flippedTable = null;
		if ($code < 128)
			return chr($code);
		if (isset($this->_encTables[$toEnc])) {
			if (!$flippedTable)
				$flippedTable = array_flip($this->_encTables[$toEnc]);
			if (isset($flippedTable[$code]))
				return chr(128 + $flippedTable[$code]);
		} else if ($toEnc == 'utf-8' || $toEnc == 'utf8') {
			if ($code < 0x800) {
				return chr(0xC0 + ($code >> 6)) . chr(0x80 + ($code & 0x3F));
			} else {
				return chr(0xE0 + ($code >> 12)) . chr(0x80 + (0x3F & ($code >> 6))) . chr(0x80 + ($code & 0x3F));
			}
		}
		return "";
	}
	var $_encTables = array('windows-1251' => array(0x0402, 0x0403, 0x201A, 0x0453, 0x201E, 0x2026, 0x2020, 0x2021, 0x20AC, 0x2030, 0x0409, 0x2039, 0x040A, 0x040C, 0x040B, 0x040F, 0x0452, 0x2018, 0x2019, 0x201C, 0x201D, 0x2022, 0x2013, 0x2014, 0x0098, 0x2122, 0x0459, 0x203A, 0x045A, 0x045C, 0x045B, 0x045F, 0x00A0, 0x040E, 0x045E, 0x0408, 0x00A4, 0x0490, 0x00A6, 0x00A7, 0x0401, 0x00A9, 0x0404, 0x00AB, 0x00AC, 0x00AD, 0x00AE, 0x0407, 0x00B0, 0x00B1, 0x0406, 0x0456, 0x0491, 0x00B5, 0x00B6, 0x00B7, 0x0451, 0x2116, 0x0454, 0x00BB, 0x0458, 0x0405, 0x0455, 0x0457, 0x0410, 0x0411, 0x0412, 0x0413, 0x0414, 0x0415, 0x0416, 0x0417, 0x0418, 0x0419, 0x041A, 0x041B, 0x041C, 0x041D, 0x041E, 0x041F, 0x0420, 0x0421, 0x0422, 0x0423, 0x0424, 0x0425, 0x0426, 0x0427, 0x0428, 0x0429, 0x042A, 0x042B, 0x042C, 0x042D, 0x042E, 0x042F, 0x0430, 0x0431, 0x0432, 0x0433, 0x0434, 0x0435, 0x0436, 0x0437, 0x0438, 0x0439, 0x043A, 0x043B, 0x043C, 0x043D, 0x043E, 0x043F, 0x0440, 0x0441, 0x0442, 0x0443, 0x0444, 0x0445, 0x0446, 0x0447, 0x0448, 0x0449, 0x044A, 0x044B, 0x044C, 0x044D, 0x044E, 0x044F), 'koi8-r' => array(0x2500, 0x2502, 0x250C, 0x2510, 0x2514, 0x2518, 0x251C, 0x2524, 0x252C, 0x2534, 0x253C, 0x2580, 0x2584, 0x2588, 0x258C, 0x2590, 0x2591, 0x2592, 0x2593, 0x2320, 0x25A0, 0x2219, 0x221A, 0x2248, 0x2264, 0x2265, 0x00A0, 0x2321, 0x00B0, 0x00B2, 0x00B7, 0x00F7, 0x2550, 0x2551, 0x2552, 0x0451, 0x2553, 0x2554, 0x2555, 0x2556, 0x2557, 0x2558, 0x2559, 0x255A, 0x255B, 0x255C, 0x255d, 0x255E, 0x255F, 0x2560, 0x2561, 0x0401, 0x2562, 0x2563, 0x2564, 0x2565, 0x2566, 0x2567, 0x2568, 0x2569, 0x256A, 0x256B, 0x256C, 0x00A9, 0x044E, 0x0430, 0x0431, 0x0446, 0x0434, 0x0435, 0x0444, 0x0433, 0x0445, 0x0438, 0x0439, 0x043A, 0x043B, 0x043C, 0x043d, 0x043E, 0x043F, 0x044F, 0x0440, 0x0441, 0x0442, 0x0443, 0x0436, 0x0432, 0x044C, 0x044B, 0x0437, 0x0448, 0x044d, 0x0449, 0x0447, 0x044A, 0x042E, 0x0410, 0x0411, 0x0426, 0x0414, 0x0415, 0x0424, 0x0413, 0x0425, 0x0418, 0x0419, 0x041A, 0x041B, 0x041C, 0x041d, 0x041E, 0x041F, 0x042F, 0x0420, 0x0421, 0x0422, 0x0423, 0x0416, 0x0412, 0x042C, 0x042B, 0x0417, 0x0428, 0x042d, 0x0429, 0x0427, 0x042A));
}
