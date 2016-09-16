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
* @file       inc/AI.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AI {
	
	public $q = '';
	public $bbcode = true;
	public $ancs = array();
	public $keywords = array();
	public $anc = '';
	public $params = array(
		'save'	=> false
	);
	
	public $row = false;
	public $answers = array();
	public $answer = false;
	
	public $result = '';
	
	private $q_like = '';
	private $q_regex = '';
	private $one = true;
	
	public function __construct() {
		if (isset($_SESSION['AI']) && $_SESSION['AI']) {
			$this->anc = $_SESSION['AI']['anc'];
			$this->ancs = $_SESSION['AI']['ancs'];
		}
	}
	public function __destruct() {
		if (!isset($_SESSION['AI']) || !is_array($_SESSION['AI'])) $_SESSION['AI'] = array();
		$_SESSION['AI']['anc'] = $this->anc;
		$_SESSION['AI']['ancs'] = $this->ancs;
	}
	public function params($params) {
		foreach ($params as $k => $v) $this->params[$k] = $v;
		return $this;
	}
	public function question($q) {
		$this->q = $q;
		$this->one = true;
		$this->q_like = trim(Parser::strSearch($q));
		$this->q_regex = trim(Parser::strRegex($q));
		$replace = array(
			'u'		=> 'you',
			'wher' 	=> 'where',
			'hou'	=> 'how',
		);
		foreach ($replace as $from => $to) {
			$this->q_regex = str_replace(' '.$from.' ',' '.$to.' ',$this->q_regex);	
		}
		return $this;
	}
	public function anc($anc) {
		$this->anc = $anc;
		return $this;
	}
	public function clear() {
		$this->q = '';
		$this->said_id = 0;
		$this->row = false;
		$this->answers = array();
		$this->answer = false;
		return $this;
	}
	public function __toString() {
		return $this->result();	
	}
	public function getAnswer() {
		return $this->answer;	
	}
	public function getQuestion() {
		return $this->row;
	}
	public function getResult() {
		return $this->result;
	}
	
	public function result() {
		if (!$this->q) return $this->talk();
		
		// start here
		$this->math();
		$this->find_reverse();
		$this->find();
		
		if (!$this->row) return $this->talk();
		
		$this->answer();
		
		if ($this->one) {
			$this->said();
			if (!$this->answer) return $this->talk();
			$this->parse();
			$this->answered();
			$this->anchor();
			$this->result = $this->answer['answer'];
			return $this->answer['answer'];
		} else {
			$this->answer = $this->row[0]['answers'][0];
			$this->parse();
			$this->anchor();
			$this->result = $this->answer['answer'];
		}
	}
	
	public function find_all() {
		$this->one = false;
		$this->result();
		return $this->row;
	}
	
	private function talk() {
		return 'Sorry I didn\'t get it.. :(';
	}
	
	private function anchor() {
		if ($this->answer['anc']) {
			$this->anc = $this->answer['anc'];
			$this->ancs[] = $this->anc;
		}
	}

	private function parse() {
		if ($this->bbcode && $this->row['type']=='db') $this->answer['answer'] = Parser::parse('bb',$this->answer['answer']);
		if (strstr($this->answer['answer'],'%')) {
			if ($this->one) $question = $this->row['question'];
			else $question = $this->row[0]['question'];
			preg_match('/^'.str_replace('%','(.+)',preg_quote($question,'/')).'$/Ui',$this->q,$m);
			$t = count($m)-1;
			for ($i=1;$i<=$t;$i++){
				$this->keywords[] = $m[$i];
				$this->answer['answer'] = str_replace('%1',$m[$i],$this->answer['answer']);
			}			
		}
	}
	
	
	
	
	// private funcs
	private function answer() {
		if ($this->one) {
			if ($this->row['type']=='db') {
				$this->answers = DB::getAll('SELECT id, answer, emotion, anc FROM '.DB_PREFIX.PREFIX.'ai_answers WHERE setid='.$this->row['id'].' ORDER BY id');
				if (!isset($_SESSION['AI']['answer'][$this->row['id']]) || !isset($this->answers[$_SESSION['AI']['answer'][$this->row['id']]])) $_SESSION['AI']['answer'][$this->row['id']] = 0;
				$this->answer = $this->answers[$_SESSION['AI']['answer'][$this->row['id']]];
				$_SESSION['AI']['answer'][$this->row['id']]++;
				$this->row['answer'] = $this->answer;
				return $this->answer;
			}
			else {
				$this->answer = $this->answers[0];	
			}
		} else {
			foreach ($this->row as $i => $row) {
				$this->row[$i]['answers'] = DB::getAll('SELECT id, answer, emotion, anc FROM '.DB_PREFIX.PREFIX.'ai_answers WHERE setid='.$row['id'].' ORDER BY id');
			}
			$this->answer = $this->row[0]['answers'];
		}
	}
	private function find_reverse() {
		if ($this->row || !$this->q_regex) return false;
		$db_func = $this->one ? 'row' : 'getAll';
		if ($this->anc) {
			$sql = 'SELECT id, \'db\' AS type, question, anc, tags, MATCH(question) AGAINST (\''.$this->q_regex.'\') AS rel FROM '.DB_PREFIX.PREFIX.'ai WHERE anc=\''.$this->anc.'\' AND (\''.$this->q_regex.'\' REGEXP CONCAT(\'^\',REPLACE(question,\'%\',\'(.*)\'),\'$\') OR \''.$this->q_like.'\' LIKE question) ORDER BY rel DESC';
			$this->row = DB::$db_func($sql);
		}
		if (!$this->row) {
			$sql = 'SELECT id, \'db\' AS type, question, anc, tags, MATCH(question) AGAINST (\''.$this->q_regex.'\') AS rel FROM '.DB_PREFIX.PREFIX.'ai WHERE \''.$this->q_regex.'\' REGEXP CONCAT(\'^\',REPLACE(question,\'%\',\'(.*)\'),\'$\') OR \''.$this->q_like.'\' LIKE question ORDER BY rel DESC';
			$this->row = DB::$db_func($sql);
		}
	}
	
	private function find() {
		if ($this->row || !$this->q_regex) return false;
		$db_func = $this->one ? 'row' : 'getAll';
		if ($this->anc) {
			$sql = 'SELECT id, \'db\' AS type, question, anc, tags, MATCH(question) AGAINST (\''.$this->q_regex.'\') AS rel FROM '.DB_PREFIX.PREFIX.'ai WHERE anc=\''.$this->anc.'\' AND (question REGEXP \''.$this->q_regex.'\' OR question LIKE \''.$this->q_like.'\') ORDER BY rel DESC';
			$this->row = DB::$db_func($sql);
		}
		if (!$this->row) {
			$sql = 'SELECT id, \'db\' AS type, question, anc, tags, MATCH(question) AGAINST (\''.$this->q_regex.'\') AS rel FROM '.DB_PREFIX.PREFIX.'ai WHERE question REGEXP \''.$this->q_regex.'\' OR question LIKE \''.$this->q_like.'\' ORDER BY rel DESC';
			$this->row = DB::$db_func($sql);
		}
		
	}
	
	private function math() {
		if ($this->row) return false;
		$r = false;
		$q = $this->q;
		// %
		if (preg_match_all('/([0-9\.,\s]+)%/',$q,$m)) {
			foreach ($m[1] as $i => $a) {
				$n1 = str_replace(',','.',$m[1][$i]);
				$q = str_replace($m[1][$i].'%',round($n1/100,2),$q);
			}
		}
		// pow
		if (preg_match_all('/([0-9\.,\s]+)\^([0-9\.,\s]+)/',$q,$m)) {
			foreach ($m[1] as $i => $a) {
				$n1 = str_replace(',','.',$m[1][$i]);
				$n2 = str_replace(',','.',$m[2][$i]);
				$q = str_replace($m[1][$i].'^'.$m[2][$i],pow($n1,$n2),$q);
			}
		}
		if (!preg_match('/[^E0-9\.\(\)\+\-\/\*\s]/',$q) && !preg_match('/[(\*|\/|\+|\-)]{2,}/',$q)) {
			$r = @eval('return ('.str_replace(' ','',$q).');');
		}
		if ($r) {
			$q = $this->q;
			$this->q = str_replace(' ','',$this->q);
			$this->q = preg_replace('/(\+|\-|\\/|\*|\^)/',' \\1 ',$this->q);
			$this->q = str_replace(' ^ ','^',$this->q);
			$this->row = array(
				'question'	=> $q,
				'type'		=> 'math'
			);
			$this->answers = array(
				0	=> $this->q.' = '.$r
			);
		}
	}
	private function answered() {
		if ($this->said_id && $this->answer['answer']) {
			DB::run('UPDATE '.DB_PREFIX.PREFIX.'ai_talk SET answer='.e($this->answer['answer']).', answerid='.$this->answer['id'].' WHERE id='.$this->said_id);
		}
	}
	
	private function said() {
		if (!$this->save) return false;
		$data = array(
			'question'	=> $this->q,
			'setid'		=> $id,
			'userid'	=> $this->Index->Session->UserID,
			'ip'		=> $this->Index->Session->IPlong,
			'added'		=> time(),
		);
		DB::insert('ai_talk',$data);
		$this->said_id = DB::id();
	}
}