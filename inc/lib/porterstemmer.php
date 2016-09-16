<?php


define('PREG_CLASS_SEARCH_EXCLUDE', '\x{0}-\x{2f}\x{3a}-\x{40}\x{5b}-\x{60}\x{7b}-\x{bf}\x{d7}\x{f7}\x{2b0}-'.
'\x{385}\x{387}\x{3f6}\x{482}-\x{489}\x{559}-\x{55f}\x{589}-\x{5c7}\x{5f3}-'.
'\x{61f}\x{640}\x{64b}-\x{65e}\x{66a}-\x{66d}\x{670}\x{6d4}\x{6d6}-\x{6ed}'.
'\x{6fd}\x{6fe}\x{700}-\x{70f}\x{711}\x{730}-\x{74a}\x{7a6}-\x{7b0}\x{901}-'.
'\x{903}\x{93c}\x{93e}-\x{94d}\x{951}-\x{954}\x{962}-\x{965}\x{970}\x{981}-'.
'\x{983}\x{9bc}\x{9be}-\x{9cd}\x{9d7}\x{9e2}\x{9e3}\x{9f2}-\x{a03}\x{a3c}-'.
'\x{a4d}\x{a70}\x{a71}\x{a81}-\x{a83}\x{abc}\x{abe}-\x{acd}\x{ae2}\x{ae3}'.
'\x{af1}-\x{b03}\x{b3c}\x{b3e}-\x{b57}\x{b70}\x{b82}\x{bbe}-\x{bd7}\x{bf0}-'.
'\x{c03}\x{c3e}-\x{c56}\x{c82}\x{c83}\x{cbc}\x{cbe}-\x{cd6}\x{d02}\x{d03}'.
'\x{d3e}-\x{d57}\x{d82}\x{d83}\x{dca}-\x{df4}\x{e31}\x{e34}-\x{e3f}\x{e46}-'.
'\x{e4f}\x{e5a}\x{e5b}\x{eb1}\x{eb4}-\x{ebc}\x{ec6}-\x{ecd}\x{f01}-\x{f1f}'.
'\x{f2a}-\x{f3f}\x{f71}-\x{f87}\x{f90}-\x{fd1}\x{102c}-\x{1039}\x{104a}-'.
'\x{104f}\x{1056}-\x{1059}\x{10fb}\x{10fc}\x{135f}-\x{137c}\x{1390}-\x{1399}'.
'\x{166d}\x{166e}\x{1680}\x{169b}\x{169c}\x{16eb}-\x{16f0}\x{1712}-\x{1714}'.
'\x{1732}-\x{1736}\x{1752}\x{1753}\x{1772}\x{1773}\x{17b4}-\x{17db}\x{17dd}'.
'\x{17f0}-\x{180e}\x{1843}\x{18a9}\x{1920}-\x{1945}\x{19b0}-\x{19c0}\x{19c8}'.
'\x{19c9}\x{19de}-\x{19ff}\x{1a17}-\x{1a1f}\x{1d2c}-\x{1d61}\x{1d78}\x{1d9b}-'.
'\x{1dc3}\x{1fbd}\x{1fbf}-\x{1fc1}\x{1fcd}-\x{1fcf}\x{1fdd}-\x{1fdf}\x{1fed}-'.
'\x{1fef}\x{1ffd}-\x{2070}\x{2074}-\x{207e}\x{2080}-\x{2101}\x{2103}-\x{2106}'.
'\x{2108}\x{2109}\x{2114}\x{2116}-\x{2118}\x{211e}-\x{2123}\x{2125}\x{2127}'.
'\x{2129}\x{212e}\x{2132}\x{213a}\x{213b}\x{2140}-\x{2144}\x{214a}-\x{2b13}'.
'\x{2ce5}-\x{2cff}\x{2d6f}\x{2e00}-\x{3005}\x{3007}-\x{303b}\x{303d}-\x{303f}'.
'\x{3099}-\x{309e}\x{30a0}\x{30fb}-\x{30fe}\x{3190}-\x{319f}\x{31c0}-\x{31cf}'.
'\x{3200}-\x{33ff}\x{4dc0}-\x{4dff}\x{a015}\x{a490}-\x{a716}\x{a802}\x{a806}'.
'\x{a80b}\x{a823}-\x{a82b}\x{d800}-\x{f8ff}\x{fb1e}\x{fb29}\x{fd3e}\x{fd3f}'.
'\x{fdfc}-\x{fe6b}\x{feff}-\x{ff0f}\x{ff1a}-\x{ff20}\x{ff3b}-\x{ff40}\x{ff5b}-'.
'\x{ff65}\x{ff70}\x{ff9e}\x{ff9f}\x{ffe0}-\x{fffd}');

define('PREG_CLASS_NUMBERS','\x{30}-\x{39}\x{b2}\x{b3}\x{b9}\x{bc}-\x{be}\x{660}-\x{669}\x{6f0}-\x{6f9}'.
'\x{966}-\x{96f}\x{9e6}-\x{9ef}\x{9f4}-\x{9f9}\x{a66}-\x{a6f}\x{ae6}-\x{aef}'.
'\x{b66}-\x{b6f}\x{be7}-\x{bf2}\x{c66}-\x{c6f}\x{ce6}-\x{cef}\x{d66}-\x{d6f}'.
'\x{e50}-\x{e59}\x{ed0}-\x{ed9}\x{f20}-\x{f33}\x{1040}-\x{1049}\x{1369}-'.
'\x{137c}\x{16ee}-\x{16f0}\x{17e0}-\x{17e9}\x{17f0}-\x{17f9}\x{1810}-\x{1819}'.
'\x{1946}-\x{194f}\x{2070}\x{2074}-\x{2079}\x{2080}-\x{2089}\x{2153}-\x{2183}'.
'\x{2460}-\x{249b}\x{24ea}-\x{24ff}\x{2776}-\x{2793}\x{3007}\x{3021}-\x{3029}'.
'\x{3038}-\x{303a}\x{3192}-\x{3195}\x{3220}-\x{3229}\x{3251}-\x{325f}\x{3280}-'.
'\x{3289}\x{32b1}-\x{32bf}\x{ff10}-\x{ff19}');

define('PREG_CLASS_PUNCTUATION','\x{21}-\x{23}\x{25}-\x{2a}\x{2c}-\x{2f}\x{3a}\x{3b}\x{3f}\x{40}\x{5b}-\x{5d}'.
'\x{5f}\x{7b}\x{7d}\x{a1}\x{ab}\x{b7}\x{bb}\x{bf}\x{37e}\x{387}\x{55a}-\x{55f}'.
'\x{589}\x{58a}\x{5be}\x{5c0}\x{5c3}\x{5f3}\x{5f4}\x{60c}\x{60d}\x{61b}\x{61f}'.
'\x{66a}-\x{66d}\x{6d4}\x{700}-\x{70d}\x{964}\x{965}\x{970}\x{df4}\x{e4f}'.
'\x{e5a}\x{e5b}\x{f04}-\x{f12}\x{f3a}-\x{f3d}\x{f85}\x{104a}-\x{104f}\x{10fb}'.
'\x{1361}-\x{1368}\x{166d}\x{166e}\x{169b}\x{169c}\x{16eb}-\x{16ed}\x{1735}'.
'\x{1736}\x{17d4}-\x{17d6}\x{17d8}-\x{17da}\x{1800}-\x{180a}\x{1944}\x{1945}'.
'\x{2010}-\x{2027}\x{2030}-\x{2043}\x{2045}-\x{2051}\x{2053}\x{2054}\x{2057}'.
'\x{207d}\x{207e}\x{208d}\x{208e}\x{2329}\x{232a}\x{23b4}-\x{23b6}\x{2768}-'.
'\x{2775}\x{27e6}-\x{27eb}\x{2983}-\x{2998}\x{29d8}-\x{29db}\x{29fc}\x{29fd}'.
'\x{3001}-\x{3003}\x{3008}-\x{3011}\x{3014}-\x{301f}\x{3030}\x{303d}\x{30a0}'.
'\x{30fb}\x{fd3e}\x{fd3f}\x{fe30}-\x{fe52}\x{fe54}-\x{fe61}\x{fe63}\x{fe68}'.
'\x{fe6a}\x{fe6b}\x{ff01}-\x{ff03}\x{ff05}-\x{ff0a}\x{ff0c}-\x{ff0f}\x{ff1a}'.
'\x{ff1b}\x{ff1f}\x{ff20}\x{ff3b}-\x{ff3d}\x{ff3f}\x{ff5b}\x{ff5d}\x{ff5f}-'.
'\x{ff65}');




function search_simplify($text) {
	$text = Parser::decodeEntities($text);		
	$text = preg_replace('/(['. PREG_CLASS_NUMBERS .']+)['. PREG_CLASS_PUNCTUATION .']+(?=['. PREG_CLASS_NUMBERS .'])/u', '\1', $text);
	$text = preg_replace('/[.-]+/', '', $text);
	$text = preg_replace('/['. PREG_CLASS_SEARCH_EXCLUDE .']+/u', ' ', $text);
	return $text;
}

function search_index_split($text,$stem = false) {
	$text = search_simplify($text);
	$text = preg_replace("/([A-Z]+)/e","strtolower('\\1')",$text);
	$words = preg_split('/[\s,!.;:\?()+-\/\\\\]+/', $text);
	array_walk($words, '_search_index_truncate');
	if ($stem) {
		$stemmer = new PorterStemmer();
	}
	$ret = array();
	foreach ($words as $word) {
		$word = trim($word, ' \'"');
		if (!strlen($word)) continue;
		if ($stem) {
			$s = $stemmer->stem($word, true);
			if ($s==$word) $ret[] = array($s);
			else $ret[] = array($s,$word);
		} else {
			$ret[] = $word;
		}
	}
	return $ret;
}
function _search_index_truncate(&$text) {
	$text = trunc($text, 50);
}


class PorterStemmer {
	var $regex_consonant;
	var $regex_vowel;

	function PorterStemmer() {
		$this->regex_consonant = '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';
		$this->regex_vowel = '(?:[aeiou]|(?<![aeiou])y)';
	}
	function Stem($word) {
		if (strlen($word) <= 2) {
			return $word;
		}
		$word = $this->step1ab($word);
		$word = $this->step1c($word);
		$word = $this->step2($word);
		$word = $this->step3($word);
		$word = $this->step4($word);
		$word = $this->step5($word);

		return $word;
	}
	function step1ab($word) {
		if (substr($word, -1) == 's') {
			   $this->replace($word, 'sses', 'ss')
			|| $this->replace($word, 'ies', 'i')
			|| $this->replace($word, 'ss', 'ss')
			|| $this->replace($word, 's', '');
		}
		if (substr($word, -2, 1) != 'e' || !$this->replace($word, 'eed', 'ee', 0)) {
			$v = $this->regex_vowel;
			if (   preg_match("#$v+#", substr($word, 0, -3)) && $this->replace($word, 'ing', '')
				|| preg_match("#$v+#", substr($word, 0, -2)) && $this->replace($word, 'ed', '')) {
				if (!$this->replace($word, 'at', 'ate')
					&& !$this->replace($word, 'bl', 'ble')
					&& !$this->replace($word, 'iz', 'ize')) {
					if ($this->doubleConsonant($word)
						&& substr($word, -2) != 'll'
						&& substr($word, -2) != 'ss'
						&& substr($word, -2) != 'zz') {
						$word = substr($word, 0, -1);
					} else if ($this->m($word) == 1 && $this->cvc($word)) {
						$word .= 'e';
					}
				}
			}
		}

		return $word;
	}
	function step1c($word) {
		$v = $this->regex_vowel;
		if (substr($word, -1) == 'y' && preg_match("#$v+#", substr($word, 0, -1))) {
			$this->replace($word, 'y', 'i');
		}

		return $word;
	}
	function step2($word) {
		switch (substr($word, -2, 1)) {
			case 'a':
				   $this->replace($word, 'ational', 'ate', 0)
				|| $this->replace($word, 'tional', 'tion', 0);
				break;

			case 'c':
				   $this->replace($word, 'enci', 'ence', 0)
				|| $this->replace($word, 'anci', 'ance', 0);
				break;

			case 'e':
				$this->replace($word, 'izer', 'ize', 0);
				break;

			case 'g':
				$this->replace($word, 'logi', 'log', 0);
				break;

			case 'l':
				   $this->replace($word, 'entli', 'ent', 0)
				|| $this->replace($word, 'ousli', 'ous', 0)
				|| $this->replace($word, 'alli', 'al', 0)
				|| $this->replace($word, 'bli', 'ble', 0)
				|| $this->replace($word, 'eli', 'e', 0);
				break;

			case 'o':
				   $this->replace($word, 'ization', 'ize', 0)
				|| $this->replace($word, 'ation', 'ate', 0)
				|| $this->replace($word, 'ator', 'ate', 0);
				break;

			case 's':
				   $this->replace($word, 'iveness', 'ive', 0)
				|| $this->replace($word, 'fulness', 'ful', 0)
				|| $this->replace($word, 'ousness', 'ous', 0)
				|| $this->replace($word, 'alism', 'al', 0);
				break;

			case 't':
				   $this->replace($word, 'biliti', 'ble', 0)
				|| $this->replace($word, 'aliti', 'al', 0)
				|| $this->replace($word, 'iviti', 'ive', 0);
				break;
		}

		return $word;
	}
	function step3($word) {
		switch (substr($word, -2, 1)) {
			case 'a':
				$this->replace($word, 'ical', 'ic', 0);
				break;

			case 's':
				$this->replace($word, 'ness', '', 0);
				break;

			case 't':
				   $this->replace($word, 'icate', 'ic', 0)
				|| $this->replace($word, 'iciti', 'ic', 0);
				break;

			case 'u':
				$this->replace($word, 'ful', '', 0);
				break;

			case 'v':
				$this->replace($word, 'ative', '', 0);
				break;

			case 'z':
				$this->replace($word, 'alize', 'al', 0);
				break;
		}

		return $word;
	}
	function step4($word) {
		switch (substr($word, -2, 1)) {
			case 'a':
				$this->replace($word, 'al', '', 1);
				break;

			case 'c':
				   $this->replace($word, 'ance', '', 1)
				|| $this->replace($word, 'ence', '', 1);
				break;

			case 'e':
				$this->replace($word, 'er', '', 1);
				break;

			case 'i':
				$this->replace($word, 'ic', '', 1);
				break;

			case 'l':
				   $this->replace($word, 'able', '', 1)
				|| $this->replace($word, 'ible', '', 1);
				break;

			case 'n':
				   $this->replace($word, 'ant', '', 1)
				|| $this->replace($word, 'ement', '', 1)
				|| $this->replace($word, 'ment', '', 1)
				|| $this->replace($word, 'ent', '', 1);
				break;

			case 'o':
				if (substr($word, -4) == 'tion' || substr($word, -4) == 'sion') {
				   $this->replace($word, 'ion', '', 1);
				} else {
					$this->replace($word, 'ou', '', 1);
				}
				break;

			case 's':
				$this->replace($word, 'ism', '', 1);
				break;

			case 't':
				   $this->replace($word, 'ate', '', 1)
				|| $this->replace($word, 'iti', '', 1);
				break;

			case 'u':
				$this->replace($word, 'ous', '', 1);
				break;

			case 'v':
				$this->replace($word, 'ive', '', 1);
				break;

			case 'z':
				$this->replace($word, 'ize', '', 1);
				break;
		}

		return $word;
	}
	function step5($word) {
		if (substr($word, -1) == 'e') {
			if ($this->m(substr($word, 0, -1)) > 1) {
				$this->replace($word, 'e', '');

			} else if ($this->m(substr($word, 0, -1)) == 1) {

				if (!$this->cvc(substr($word, 0, -1))) {
					$this->replace($word, 'e', '');
				}
			}
		}
		if ($this->m($word) > 1 && $this->doubleConsonant($word) && substr($word, -1) == 'l') {
			$word = substr($word, 0, -1);
		}

		return $word;
	}
	function replace(&$str, $check, $repl, $m = null) {
		$len = 0 - strlen($check);

		if (substr($str, $len) == $check) {
			$substr = substr($str, 0, $len);
			if (is_null($m) || $this->m($substr) > $m) {
				$str = $substr . $repl;
			}
			return true;
		}
		return false;
	}
	function m($str) {
		$c = $this->regex_consonant;
		$v = $this->regex_vowel;
		$str = preg_replace("#^$c+#", '', $str);
		$str = preg_replace("#$v+$#", '', $str);
		preg_match_all("#($v+$c+)#", $str, $matches);
		return count($matches[1]);
	}
	function doubleConsonant($str) {
		$c = $this->regex_consonant;

		return preg_match("#$c{2}$#", $str, $matches) && $matches[0]{0} == $matches[0]{1};
	}
	function cvc($str) {
		$c = $this->regex_consonant;
		$v = $this->regex_vowel;

		return preg_match("#($c$v$c)$#", $str, $matches)
			   && strlen($matches[1]) == 3
			   && $matches[1]{2} != 'w'
			   && $matches[1]{2} != 'x'
			   && $matches[1]{2} != 'y';
	}
}