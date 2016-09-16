<?php
class Keyword {
	var $contents;
	var $keywords;
	var $wordLengthMin;
	var $wordOccuredMin;
	var $word2WordPhraseLengthMin;
	var $phrase2WordLengthMinOccur;
	var $word3WordPhraseLengthMin;
	var $phrase2WordLengthMin;
	var $phrase3WordLengthMinOccur;
	var $phrase3WordLengthMin;

	public function __construct($params) {
		$this->contents = $this->replace_chars($params['content']);
		if (!defined('FTP_DIR_ROOT')) define('FTP_DIR_ROOT','./');
		$this->common_words = require(FTP_DIR_ROOT.'config/system/words.php');

		$this->wordLengthMin = $params['min_word_length'];
		$this->wordOccuredMin = $params['min_word_occur'];

		$this->word2WordPhraseLengthMin = $params['min_2words_length'];
		$this->phrase2WordLengthMin = $params['min_2words_phrase_length'];
		$this->phrase2WordLengthMinOccur = $params['min_2words_phrase_occur'];

		$this->word3WordPhraseLengthMin = $params['min_3words_length'];
		$this->phrase3WordLengthMin = $params['min_3words_phrase_length'];
		$this->phrase3WordLengthMinOccur = $params['min_3words_phrase_occur'];
	}

	public function get_keywords() {
		$r = $this->parse_words();
		if ($this->phrase2WordLengthMinOccur) $r .= $this->parse_2words();
		if ($this->phrase3WordLengthMinOccur) $r .= $this->parse_3words();
		$r = preg_replace("/\s+/", " ", $r);
		$r = str_replace(', ,',',',$r);
		return $r;
	}

	public function parse_words() {
		$s = preg_split('/(\s|\n|\.|\\|\/|\*|,|\+|\?|\!)/U', $this->contents);
		$k = array();
		foreach ($s as $key=>$val) {
			$val = trim($val);
			if (!$val) continue;
			if(len($val) >= $this->wordLengthMin  && !in_array($val, $this->common_words) && !is_numeric($val)) {
				$k[] = $val;
			}
		}
		$k = array_count_values($k);
		$occur_filtered = $this->occure_filter($k, $this->wordOccuredMin);
		arsort($occur_filtered);
		$imploded = $this->implode(", ", $occur_filtered);
		unset($k, $s);
		return $imploded;
	}

	public function parse_2words() {
		$x = split(" ", $this->contents);
		$y = array();
		for ($i=0; $i < count($x)-1; $i++) {
			if( (len(trim($x[$i])) >= $this->word2WordPhraseLengthMin ) && (len(trim($x[$i+1])) >= $this->word2WordPhraseLengthMin) )
			{
				$y[] = trim($x[$i])." ".trim($x[$i+1]);
			}
		}
		$y = array_count_values($y);
		$occur_filtered = $this->occure_filter($y, $this->phrase2WordLengthMinOccur);
		arsort($occur_filtered);
		$imploded = $this->implode(", ", $occur_filtered);
		unset($y, $x);
		return $imploded;
	}

	public function parse_3words() {
		$a = split(" ", $this->contents);
		$b = array();
		for ($i=0; $i < count($a)-2; $i++) {
			if( (len(trim($a[$i])) >= $this->word3WordPhraseLengthMin) && (len(trim($a[$i+1])) > $this->word3WordPhraseLengthMin) && (len(trim($a[$i+2])) > $this->word3WordPhraseLengthMin) && (len(trim($a[$i]).trim($a[$i+1]).trim($a[$i+2])) > $this->phrase3WordLengthMin) )
			{
				$b[] = trim($a[$i])." ".trim($a[$i+1])." ".trim($a[$i+2]);
			}
		}
		$b = array_count_values($b);
		$occur_filtered = $this->occure_filter($b, $this->phrase3WordLengthMinOccur);
		arsort($occur_filtered);
		$imploded = $this->implode(', ', $occur_filtered);
		unset($a, $b);
		return $imploded;
	}

	private function occure_filter($array_count_values, $min_occur) {
		$occur_filtered = array();
		foreach ($array_count_values as $word => $occured) {
			if ($occured >= $min_occur) {
				$occur_filtered[$word] = $occured;
			}
		}
		return $occur_filtered;
	}

	private function implode($gule, $array) {
		return join($gule, array_keys($array));
	}

	private function replace_chars($content) {
		$content = str_replace(array('&nbsp;','>','<','&',';'),array(' ','> ',' <',' &','; '),$content);
		$content = strip_tags($content);
		$punctuations = array(',', ')', '(', '.', "'", '"',
		'<', '>', '!', '?', '/', '-', '{', '}', '&amp;',
		'_', '[', ']', ':', '+', '=', '#',
		'$', '&quot;', '&copy;', '&gt;', '&lt;', 
		'&nbsp;', '&trade;', '&reg;', '&ldquo;', '&rsquo;', 
		chr(10), chr(13), chr(9));
		$content = str_replace($punctuations, " ", $content);
		$content = preg_replace('/ {2,}/si', " ", $content);
		return $content;
	}
}