<?
$urlProtocols = array(
	'http://',
	'https://',
	'ftp://',
	'irc://',
	'gopher://',
	'telnet://', // Well if we're going to support the above.. -ævar
	'nntp://', // @bug 3808 RFC 1738
	'worldwind://',
	'mailto:',
	'news:',
	'svn://',
);

$htmlEntityAliases = array(
	'רלמ' => 'rlm',
	'رلم' => 'rlm',
);
$htmlEntities = array(
	'Aacute'   => 193,
	'aacute'   => 225,
	'Acirc'    => 194,
	'acirc'    => 226,
	'acute'    => 180,
	'AElig'    => 198,
	'aelig'    => 230,
	'Agrave'   => 192,
	'agrave'   => 224,
	'alefsym'  => 8501,
	'Alpha'    => 913,
	'alpha'    => 945,
	'amp'      => 38,
	'and'      => 8743,
	'ang'      => 8736,
	'Aring'    => 197,
	'aring'    => 229,
	'asymp'    => 8776,
	'Atilde'   => 195,
	'atilde'   => 227,
	'Auml'     => 196,
	'auml'     => 228,
	'bdquo'    => 8222,
	'Beta'     => 914,
	'beta'     => 946,
	'brvbar'   => 166,
	'bull'     => 8226,
	'cap'      => 8745,
	'Ccedil'   => 199,
	'ccedil'   => 231,
	'cedil'    => 184,
	'cent'     => 162,
	'Chi'      => 935,
	'chi'      => 967,
	'circ'     => 710,
	'clubs'    => 9827,
	'cong'     => 8773,
	'copy'     => 169,
	'crarr'    => 8629,
	'cup'      => 8746,
	'curren'   => 164,
	'dagger'   => 8224,
	'Dagger'   => 8225,
	'darr'     => 8595,
	'dArr'     => 8659,
	'deg'      => 176,
	'Delta'    => 916,
	'delta'    => 948,
	'diams'    => 9830,
	'divide'   => 247,
	'Eacute'   => 201,
	'eacute'   => 233,
	'Ecirc'    => 202,
	'ecirc'    => 234,
	'Egrave'   => 200,
	'egrave'   => 232,
	'empty'    => 8709,
	'emsp'     => 8195,
	'ensp'     => 8194,
	'Epsilon'  => 917,
	'epsilon'  => 949,
	'equiv'    => 8801,
	'Eta'      => 919,
	'eta'      => 951,
	'ETH'      => 208,
	'eth'      => 240,
	'Euml'     => 203,
	'euml'     => 235,
	'euro'     => 8364,
	'exist'    => 8707,
	'fnof'     => 402,
	'forall'   => 8704,
	'frac12'   => 189,
	'frac14'   => 188,
	'frac34'   => 190,
	'frasl'    => 8260,
	'Gamma'    => 915,
	'gamma'    => 947,
	'ge'       => 8805,
	'gt'       => 62,
	'harr'     => 8596,
	'hArr'     => 8660,
	'hearts'   => 9829,
	'hellip'   => 8230,
	'Iacute'   => 205,
	'iacute'   => 237,
	'Icirc'    => 206,
	'icirc'    => 238,
	'iexcl'    => 161,
	'Igrave'   => 204,
	'igrave'   => 236,
	'image'    => 8465,
	'infin'    => 8734,
	'int'      => 8747,
	'Iota'     => 921,
	'iota'     => 953,
	'iquest'   => 191,
	'isin'     => 8712,
	'Iuml'     => 207,
	'iuml'     => 239,
	'Kappa'    => 922,
	'kappa'    => 954,
	'Lambda'   => 923,
	'lambda'   => 955,
	'lang'     => 9001,
	'laquo'    => 171,
	'larr'     => 8592,
	'lArr'     => 8656,
	'lceil'    => 8968,
	'ldquo'    => 8220,
	'le'       => 8804,
	'lfloor'   => 8970,
	'lowast'   => 8727,
	'loz'      => 9674,
	'lrm'      => 8206,
	'lsaquo'   => 8249,
	'lsquo'    => 8216,
	'lt'       => 60,
	'macr'     => 175,
	'mdash'    => 8212,
	'micro'    => 181,
	'middot'   => 183,
	'minus'    => 8722,
	'Mu'       => 924,
	'mu'       => 956,
	'nabla'    => 8711,
	'nbsp'     => 160,
	'ndash'    => 8211,
	'ne'       => 8800,
	'ni'       => 8715,
	'not'      => 172,
	'notin'    => 8713,
	'nsub'     => 8836,
	'Ntilde'   => 209,
	'ntilde'   => 241,
	'Nu'       => 925,
	'nu'       => 957,
	'Oacute'   => 211,
	'oacute'   => 243,
	'Ocirc'    => 212,
	'ocirc'    => 244,
	'OElig'    => 338,
	'oelig'    => 339,
	'Ograve'   => 210,
	'ograve'   => 242,
	'oline'    => 8254,
	'Omega'    => 937,
	'omega'    => 969,
	'Omicron'  => 927,
	'omicron'  => 959,
	'oplus'    => 8853,
	'or'       => 8744,
	'ordf'     => 170,
	'ordm'     => 186,
	'Oslash'   => 216,
	'oslash'   => 248,
	'Otilde'   => 213,
	'otilde'   => 245,
	'otimes'   => 8855,
	'Ouml'     => 214,
	'ouml'     => 246,
	'para'     => 182,
	'part'     => 8706,
	'permil'   => 8240,
	'perp'     => 8869,
	'Phi'      => 934,
	'phi'      => 966,
	'Pi'       => 928,
	'pi'       => 960,
	'piv'      => 982,
	'plusmn'   => 177,
	'pound'    => 163,
	'prime'    => 8242,
	'Prime'    => 8243,
	'prod'     => 8719,
	'prop'     => 8733,
	'Psi'      => 936,
	'psi'      => 968,
	'quot'     => 34,
	'radic'    => 8730,
	'rang'     => 9002,
	'raquo'    => 187,
	'rarr'     => 8594,
	'rArr'     => 8658,
	'rceil'    => 8969,
	'rdquo'    => 8221,
	'real'     => 8476,
	'reg'      => 174,
	'rfloor'   => 8971,
	'Rho'      => 929,
	'rho'      => 961,
	'rlm'      => 8207,
	'rsaquo'   => 8250,
	'rsquo'    => 8217,
	'sbquo'    => 8218,
	'Scaron'   => 352,
	'scaron'   => 353,
	'sdot'     => 8901,
	'sect'     => 167,
	'shy'      => 173,
	'Sigma'    => 931,
	'sigma'    => 963,
	'sigmaf'   => 962,
	'sim'      => 8764,
	'spades'   => 9824,
	'sub'      => 8834,
	'sube'     => 8838,
	'sum'      => 8721,
	'sup'      => 8835,
	'sup1'     => 185,
	'sup2'     => 178,
	'sup3'     => 179,
	'supe'     => 8839,
	'szlig'    => 223,
	'Tau'      => 932,
	'tau'      => 964,
	'there4'   => 8756,
	'Theta'    => 920,
	'theta'    => 952,
	'thetasym' => 977,
	'thinsp'   => 8201,
	'THORN'    => 222,
	'thorn'    => 254,
	'tilde'    => 732,
	'times'    => 215,
	'trade'    => 8482,
	'Uacute'   => 218,
	'uacute'   => 250,
	'uarr'     => 8593,
	'uArr'     => 8657,
	'Ucirc'    => 219,
	'ucirc'    => 251,
	'Ugrave'   => 217,
	'ugrave'   => 249,
	'uml'      => 168,
	'upsih'    => 978,
	'Upsilon'  => 933,
	'upsilon'  => 965,
	'Uuml'     => 220,
	'uuml'     => 252,
	'weierp'   => 8472,
	'Xi'       => 926,
	'xi'       => 958,
	'Yacute'   => 221,
	'yacute'   => 253,
	'yen'      => 165,
	'Yuml'     => 376,
	'yuml'     => 255,
	'Zeta'     => 918,
	'zeta'     => 950,
	'zwj'      => 8205,
	'zwnj'     => 8204
);

function code2utf($c) {
	if($c < 0x80) return chr($c);
	if($c <	0x800) return chr($c >>	6 & 0x3f | 0xc0) .
							 chr($c		  & 0x3f | 0x80);
	if($c <  0x10000) return chr($c >> 12 & 0x0f | 0xe0) .
							 chr($c >>	6 & 0x3f | 0x80) .
							 chr($c		  & 0x3f | 0x80);
	if($c < 0x110000) return chr($c >> 18 & 0x07 | 0xf0) .
							 chr($c >> 12 & 0x3f | 0x80) .
							 chr($c >>	6 & 0x3f | 0x80) .
							 chr($c		  & 0x3f | 0x80);
	echo "Asked for code outside of range ($c)\n";
	exit;
}

class HTML {
	
	function removeHTMLtags($text,$processCallback = null, $args = array(), $extratags = array() ) {
		global $wgUseTidy;

		static $htmlpairs, $htmlsingle, $htmlsingleonly, $htmlnest, $tabletags,
			$htmllist, $listtags, $htmlsingleallowed, $htmlelements, $staticInitialised;

		if ( !$staticInitialised ) {
			$htmlpairs = array_merge( $extratags, array( # Tags that must be closed
				'b', 'del', 'i', 'ins', 'u', 'font', 'big', 'small', 'sub', 'sup', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'cite', 'code', 'em', 's', 'strike', 'strong', 'tt', 'var', 'div', 'center', 'blockquote', 'ol', 'ul', 'dl', 'table', 'caption', 'pre', 'ruby', 'rt' , 'rb' , 'rp', 'p', 'span', 'u'));
			$htmlsingle = array(
				'br', 'hr', 'li', 'dt', 'dd'
			);
			$htmlsingleonly = array( # Elements that cannot have close tags
				'br', 'hr'
			);
			$htmlnest = array( # Tags that can be nested--??
				'table', 'tr', 'td', 'th', 'div', 'blockquote', 'ol', 'ul', 'dl', 'font', 'big', 'small', 'sub', 'sup', 'span'
			);
			$tabletags = array( # Can only appear inside table, we will close them
				'td', 'th', 'tr',
			);
			$htmllist = array( # Tags used by list
				'ul','ol',
			);
			$listtags = array( # Tags that can appear in a list
				'li',
			);

			$htmlsingleallowed = array_merge($htmlsingle, $tabletags);
			$htmlelements = array_merge($htmlsingle, $htmlpairs, $htmlnest);
			$vars = array( 'htmlpairs', 'htmlsingle', 'htmlsingleonly', 'htmlnest', 'tabletags', 'htmllist', 'listtags', 'htmlsingleallowed', 'htmlelements' );
			foreach ( $vars as $var ) {
				$$var = array_flip( $$var );
			}
			$staticInitialised = true;
		}
		$text = Html::removeComments( $text );
		$bits = explode( '<', $text );
		$text = str_replace( '>', '&gt;', array_shift( $bits ) );
		if(!$wgUseTidy) {
			$tagstack = $tablestack = array();
			foreach ( $bits as $x ) {
				$regs = array();
				if( preg_match( '!^(/?)(\\w+)([^>]*?)(/{0,1}>)([^<]*)$!', $x, $regs ) ) {
					list( /* $qbar */, $slash, $t, $params, $brace, $rest ) = $regs;
				} else {
					$slash = $t = $params = $brace = $rest = null;
				}

				$badtag = 0 ;
				if ( isset($htmlelements[$t = strtolower( $t )] ) ) {
					if ($slash) {
						if (isset($htmlsingleonly[$t])) {
							$badtag = 1;
						} elseif (($ot = @array_pop($tagstack))!=$t) {
							if ( isset( $htmlsingleallowed[$ot] ) ) {
								$optstack = array();
								array_push ($optstack, $ot);
								while ((($ot = @array_pop($tagstack))!=$t) && isset($htmlsingleallowed[$ot])) {
									array_push ($optstack, $ot);
								}
								if ( $t != $ot ) {
									$badtag = 1;
									while ( $ot = @array_pop( $optstack ) ) {
										array_push( $tagstack, $ot );
									}
								}
							} else {
								@array_push( $tagstack, $ot );
								if(!(isset( $htmllist[$ot]) && isset($listtags[$t]))) {
									$badtag = 1;
								}
							}
						} else {
							if ( $t == 'table' ) {
								$tagstack = array_pop( $tablestack );
							}
						}
						$newparams = '';
					} else {
						if (isset($tabletags[$t]) && !in_array('table',$tagstack)) {
							$badtag = 1;
						} elseif (in_array($t,$tagstack) && ! isset($htmlnest[$t])) {
							$badtag = 1 ;
						} elseif ($brace == '/>' && isset($htmlpairs[$t])) {
							$badtag = 1;
						} elseif (isset( $htmlsingleonly[$t])) {
							$brace = '/>';
						} else if( isset( $htmlsingle[$t])) {
							$brace = NULL;
						} else if( isset( $tabletags[$t]) && in_array($t ,$tagstack) ) {
							$text .= "<$t>";
						} else {
							if ( $t == 'table' ) {
								array_push( $tablestack, $tagstack );
								$tagstack = array();
							}
							array_push( $tagstack, $t );
						}
						if (is_callable($processCallback)) {
							call_user_func_array($processCallback, array(&$params,$args));
						}
						$newparams = Html::fixTags($params, $t);
					}
					if (!$badtag) {
						$rest = str_replace( '>', '&gt;', $rest );
						$close = ( $brace == '/>' && !$slash ) ? ' /' : '';
						$text .= "<$slash$t$newparams$close>$rest";
						continue;
					}
				}
				$text .= '&lt;' . str_replace( '>', '&gt;', $x);
			}
			while ( is_array( $tagstack ) && ($t = array_pop( $tagstack )) ) {
				$text .= "<$t>\n";
				if ( $t == 'table' ) { $tagstack = array_pop( $tablestack ); }
			}
		} else {
			foreach ( $bits as $x ) {
				preg_match( '/^(\\/?)(\\w+)([^>]*?)(\\/{0,1}>)([^<]*)$/',
				$x, $regs );
				@list( /* $qbar */, $slash, $t, $params, $brace, $rest ) = $regs;
				if ( isset( $htmlelements[$t = strtolower( $t )] ) ) {
					if( is_callable( $processCallback ) ) {
						call_user_func_array( $processCallback, array( &$params, $args ) );
					}
					$newparams = Html::fixTags( $params, $t );
					$rest = str_replace( '>', '&gt;', $rest );
					$text .= "<$slash$t$newparams$brace$rest";
				} else {
					$text .= '&lt;' . str_replace( '>', '&gt;', $x);
				}
			}
		}
		return $text;
	}
	static function removeComments( $text ) {
		while (($start = strpos($text, '<!--')) !== false) {
			$end = strpos($text, '-->', $start + 4);
			if ($end === false) {
				break;
			}
			$end += 3;
			$spaceStart = max($start - 1, 0);
			$spaceLen = $end - $spaceStart;
			while (substr($text, $spaceStart, 1) === ' ' && $spaceStart > 0) {
				$spaceStart--;
				$spaceLen++;
			}
			while (substr($text, $spaceStart + $spaceLen, 1) === ' ')
				$spaceLen++;
			if (substr($text, $spaceStart, 1) === "\n" and substr($text, $spaceStart + $spaceLen, 1) === "\n") {
				$text = substr_replace($text, "\n", $spaceStart, $spaceLen + 1);
			}
			else {
				$text = substr_replace($text, '', $start, $end - $start);
			}
		}
		return $text;
	}
	function validateUTF( $codepoint ) {
		return ($codepoint ==    0x09)
			|| ($codepoint ==    0x0a)
			|| ($codepoint ==    0x0d)
			|| ($codepoint >=    0x20 && $codepoint <=   0xd7ff)
			|| ($codepoint >=  0xe000 && $codepoint <=   0xfffd)
			|| ($codepoint >= 0x10000 && $codepoint <= 0x10ffff);
	}
	function fix_utf_string($text) {
		$regex = '/&([A-Za-z0-9\x80-\xff]+);
	|&\#([0-9]+);
	|&\#x([0-9A-Za-z]+);
	|&\#X([0-9A-Za-z]+);
	|(&)/x';
		return preg_replace_callback($regex,array('HTML','fix_utf_string_callback'),$text);
	}
	function fix_utf_string_callback($m) {
		if ($m[1]!='') {
			return Html::decodeEntity($m[1]);
		} elseif ($m[2]!='') {
			return Html::decodeChar(intval($m[2]));
		} elseif ($m[3]!='') {
			return Html::decodeChar(hexdec($m[3]));
		} elseif ($m[4]!='') {
			return Html::decodeChar(hexdec($m[4]));
		}
		return $matches[0];
	}
	function decodeChar($c) {
		if (Html::validateUTF($c)) {
			return code2utf($c);
		} else {
			return "\xef\xbf\xbd";
		}
	}
	function decodeEntity($name) {
		global $htmlEntities, $htmlEntityAliases;
		if (isset($htmlEntityAliases[$name])) {
			$name = $htmlEntityAliases[$name];
		}
		if (isset($htmlEntities[$name])) {
			return code2utf($htmlEntities[$name]);
		} else {
			return "&$name;";
		}
	}
	function urlProtocols() {
		global $urlProtocols;
		if (is_array($urlProtocols)) {
			$protocols = array();
			foreach ($urlProtocols as $protocol) $protocols[] = preg_quote($protocol,'/');
			return implode( '|', $protocols );
		} else {
			return $urlProtocols;
		}
	}
	function fixTags($text,$element) {
		if (trim($text)=='') {
			return '';
		}
		$stripped = Html::validateTags(Html::decodeTags($text), $element);
		$attribs = array();
		$repl = array(
			'{'    => '&#123;',
			'['    => '&#91;',
			"''"   => '&#39;&#39;',
			'ISBN' => '&#73;SBN',
			'RFC'  => '&#82;FC',
			'PMID' => '&#80;MID',
			'|'    => '&#124;',
			'__'   => '&#95;_',
		);
		$repl2  = array(
			"\n" => '&#10;',
			"\r" => '&#13;',
			"\t" => '&#9;',
		);
		foreach ($stripped as $a => $v ) {
			$a = htmlspecialchars($a);
			$v = strtr($encValue,$repl);
			$v = preg_replace_callback('/('.Html::urlProtocols().')/',array('HTML','armorLinksCallback'),$v);
			$v = htmlspecialchars( $v, ENT_QUOTES );
			$v = strtr($v,$repl2 );
			$attribs[] = "$a=\"$v\"";
		}
		return count($attribs)?' '.implode(' ',$attribs):'';
	}
	function armorLinksCallback($matches) {
		return str_replace( ':', '&#58;', $matches[1] );
	}
	function validateTags($attribs, $element) {
		return Html::validateAttributes($attribs, Html::attributeWhitelist($element));
	}
	function attributeWhitelist($element) {
		static $list;
		if (!isset($list)) {
			$list = Html::setupAttributeWhitelist();
		}
		return isset($list[$element]) ? $list[$element]	: array();
	}
	
	function setupAttributeWhitelist() {
		$common = array( 'id', 'class', 'lang', 'dir', 'title', 'style' );
		$block = array_merge( $common, array( 'align' ) );
		$tablealign = array( 'align', 'char', 'charoff', 'valign' );
		$tablecell = array( 'abbr',
							'axis',
							'headers',
							'scope',
							'rowspan',
							'colspan',
							'nowrap', # deprecated
							'width',  # deprecated
							'height', # deprecated
							'bgcolor' # deprecated
							);
	
		# Numbers refer to sections in HTML 4.01 standard describing the element.
		# See: http://www.w3.org/TR/html4/
		$whitelist = array (
			# 7.5.4
			'div'        => $block,
			'center'     => $common, # deprecated
			'span'       => $block, # ??
	
			# 7.5.5
			'h1'         => $block,
			'h2'         => $block,
			'h3'         => $block,
			'h4'         => $block,
			'h5'         => $block,
			'h6'         => $block,
	
			# 7.5.6
			# address
	
			# 8.2.4
			# bdo
	
			# 9.2.1
			'em'         => $common,
			'strong'     => $common,
			'cite'       => $common,
			# dfn
			'code'       => $common,
			# samp
			# kbd
			'var'        => $common,
			# abbr
			# acronym
	
			# 9.2.2
			'blockquote' => array_merge( $common, array( 'cite' ) ),
			# q
	
			# 9.2.3
			'sub'        => $common,
			'sup'        => $common,
	
			# 9.3.1
			'p'          => $block,
	
			# 9.3.2
			'br'         => array( 'id', 'class', 'title', 'style', 'clear' ),
	
			# 9.3.4
			'pre'        => array_merge( $common, array( 'width' ) ),
	
			# 9.4
			'ins'        => array_merge( $common, array( 'cite', 'datetime' ) ),
			'del'        => array_merge( $common, array( 'cite', 'datetime' ) ),
	
			# 10.2
			'ul'         => array_merge( $common, array( 'type' ) ),
			'ol'         => array_merge( $common, array( 'type', 'start' ) ),
			'li'         => array_merge( $common, array( 'type', 'value' ) ),
	
			# 10.3
			'dl'         => $common,
			'dd'         => $common,
			'dt'         => $common,
	
			# 11.2.1
			'table'      => array_merge( $common,
								array( 'summary', 'width', 'border', 'frame',
										'rules', 'cellspacing', 'cellpadding',
										'align', 'bgcolor',
								) ),
	
			# 11.2.2
			'caption'    => array_merge( $common, array( 'align' ) ),
	
			# 11.2.3
			'thead'      => array_merge( $common, $tablealign ),
			'tfoot'      => array_merge( $common, $tablealign ),
			'tbody'      => array_merge( $common, $tablealign ),
	
			# 11.2.4
			'colgroup'   => array_merge( $common, array( 'span', 'width' ), $tablealign ),
			'col'        => array_merge( $common, array( 'span', 'width' ), $tablealign ),
	
			# 11.2.5
			'tr'         => array_merge( $common, array( 'bgcolor' ), $tablealign ),
	
			# 11.2.6
			'td'         => array_merge( $common, $tablecell, $tablealign ),
			'th'         => array_merge( $common, $tablecell, $tablealign ),
	
			# 13.2
			# Not usually allowed, but may be used for extension-style hooks
			# such as <math> when it is rasterized
			'img'        => array_merge( $common, array( 'alt' ) ),
	
			# 15.2.1
			'tt'         => $common,
			'b'          => $common,
			'i'          => $common,
			'big'        => $common,
			'small'      => $common,
			'strike'     => $common,
			's'          => $common,
			'u'          => $common,
	
			# 15.2.2
			'font'       => array_merge( $common, array( 'size', 'color', 'face' ) ),
			# basefont
	
			# 15.3
			'hr'         => array_merge( $common, array( 'noshade', 'size', 'width' ) ),
	
			# XHTML Ruby annotation text module, simple ruby only.
			# http://www.w3c.org/TR/ruby/
			'ruby'       => $common,
			# rbc
			# rtc
			'rb'         => $common,
			'rt'         => $common, #array_merge( $common, array( 'rbspan' ) ),
			'rp'         => $common,
	
			# MathML root element, where used for extensions
			# 'title' may not be 100% valid here; it's XHTML
			# http://www.w3.org/TR/REC-MathML/
			'math'       => array( 'class', 'style', 'id', 'title' ),
		);
		return $whitelist;
	}
	function validateAttributes( $attribs, $whitelist ) {
		$whitelist = array_flip( $whitelist );
		$out = array();
		foreach( $attribs as $attribute => $value ) {
			if( !isset( $whitelist[$attribute] ) ) {
				continue;
			}
			if( $attribute == 'style' ) {
				$value = Html::checkCss( $value );
				if ($value === false) continue;
			}
			if ($attribute==='id') $value = Html::escapeId( $value );
			$out[$attribute] = $value;
		}
		return $out;
	}
	function escapeId($id,$flags = 1) {
		$replace = array(
			'%3A' => ':',
			'%' => '.'
		);
		$id = urlencode(Html::fix_utf_string(strtr($id,' ','_')));
		$id = str_replace(array_keys($replace),array_values($replace),$id);
		if( ~$flags & 1 && !preg_match('/[a-zA-Z]/',$id[0])) {
			$id = "x$id";
		}
		return $id;
	}
	function checkCss( $value ) {
		$stripped = Html::fix_utf_string($value);
		$stripped = preg_replace( "!/*(.*)*/!s", ' ',$stripped);
		$value = $stripped;
		$stripped = preg_replace( '!\\\\([0-9A-Fa-f]{1,6})[ \\n\\r\\t\\f]?!e','code2utf(hexdec("$1"))', $stripped);
		$stripped = str_replace( '\\', '',$stripped);
		if (preg_match('/(?:expression|tps*:\/\/|url\\s*\().*/is',$stripped)) {
			# haxx0r
			return false;
		}
		return $value;
	}
	function decodeTags($text) {
		$attribs = array();
		if (trim($text)=='') {
			return $attribs;
		}
		$pairs = array();
		$attrib = '[A-Za-z0-9]';
		$space = '[\x09\x0a\x0d\x20]';
		if (!preg_match_all("/(?:^|$space)($attrib+)($space*=$space* (?:\"([^<\"]*)\"|'([^<']*)'|([a-zA-Z0-9!#$%&()*,\\-.\\/:;<>?@[\\]^_`{|}~]+)|(\#[0-9a-fA-F]+)))?(?=$space|\$|>)/sx",$text,$pairs,PREG_SET_ORDER)) {
			return $attribs;
		}
		foreach ($pairs as $set) {
			$attribute = strtolower($set[1]);
			$value = Html::getTagCallback($set);
			$value = preg_replace('/[\t\r\n ]+/',' ',$value);
			$value = trim($value);
			$attribs[$attribute] = Html::fix_utf_string($value);
		}
		return $attribs;
	}
	function getTagCallback($set) {
		if (isset($set[6])) {
			# Illegal #XXXXXX color with no quotes.
			return $set[6];
		} elseif (isset($set[5])) {
			# No quotes.
			return $set[5];
		} elseif (isset($set[4])) {
			# Single-quoted
			return $set[4];
		} elseif (isset($set[3])) {
			# Double-quoted
			return $set[3];
		} elseif (!isset($set[2])) {
			# In XHTML, attributes must have a value.
			# For 'reduced' form, return explicitly the attribute name here.
			return $set[1];
		} else {
			trigger_error("Tag conditions not met. This should never happen and is a bug.",E_USER_WARNING);
		}
	}
}