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
* @file       mod/Poll.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Poll extends Object {
	
	public $sess = array();

	public function __construct() {
		$this->Index =& Index();
		$this->class = __CLASS__;
		parent::load($this->Index);
		$this->table = 'poll';
		$this->sess =& $_SESSION;
	}
	public function __destruct() {
		$_SESSION = array_merge($_SESSION, $this->sess);		
	}
	public function init() {
		
		return $this;
	}
	/*
	public function toHtml() {
		
	}
	
	public function toArray() {
		
	}
	
	public function __toString() {
		
		echo $this->toHtml();
	}
	*/
	public function getQuizResult($name) {
			
	}
	public function saveQuizResult($name) {
		
	}
	

	
	public function getQuiz($name, $limit = 1) {
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.$this->table.' WHERE quiz='.e($name).' AND lang=\''.$this->lang.'\' AND active=1 ORDER BY sort';
		if (!isset($this->sess['QUIZ']) || !is_array($v['QUIZ'])) $this->sess['QUIZ'] = array();
		if (isset($_GET[URL_KEY_RESET]) || !isset($this->sess['QUIZ'][$name])) $this->sess['QUIZ'][$name] = 0;
		if (!$this->sess['QUIZ'][$name] || !isset($this->sess['QUIZ_RESULT'][$name])) $this->sess['QUIZ_RESULT'][$name] = array();
		
		$this->offset = $this->sess['QUIZ'][$name];
		$this->limit = $limit;

		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			$_qry = DB::qry('SELECT id, answer, answers, score FROM '.$this->prefix.$this->table.'_map WHERE setid='.$rs['id'].' ORDER BY sort, id',0,0);
			$rs['map'] = array();
			while ($_rs = DB::fetch($_qry)) {
				if ($rs['passes']) {
					$_rs['percent'] = round($_rs['answers'] / $rs['passes'] * 100);
				}
				array_push($rs['map'], $_rs);
			}
			array_push($this->data, $rs);
			DB::free($_qry);
		}
		
		DB::free($qry);
		
		return array(
			'total'	=> $this->total,
			'current'=> $this->sess['QUIZ'][$name]+1,
			'list'	=>& $this->data,
			'result'=> $this->sess['QUIZ_RESULT'][$name],
			'row'	=>& $this->data[0]
		);
	}
	
	
	public function getContent($name = false) {
		if ($name) {
			if ($name===true) {
				$this->filter = '';
			} else {
				$this->filter = ' AND name LIKE '.e($name);
			}
			$this->filter .= ' AND quiz=\'\'';
			$this->row = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' AND lang=\''.$this->lang.'\' AND active=1 ORDER BY '.$this->order);
			if (!$this->row['id']) return;
			if (!$this->sess['POLL'] || !is_array($this->sess['POLL'])) $this->sess['POLL'] = array();
			if (isset($this->sess['POLL'][$this->row['id']])) {
				$this->row['voted'] = $this->sess['POLL'][$this->row['id']];
			}
			$this->row['map'] = array();
			$qry = DB::qry('SELECT id, answer, answers FROM '.$this->prefix.'poll_map WHERE setid='.$this->row['id'].' ORDER BY sort',0,0);
			while ($rs = DB::fetch($qry)) {
				if ($this->row['passes']) {
					$rs['percent'] = round($rs['answers'] / $this->row['passes'] * 100);
				}
				array_push($this->row['map'], $rs);
			}
			DB::free($qry);
			// var p=(a.map[j].answers>0?Math.round(a.map[j].answers/a.passes*100):0);
		}
		return $this->row;
	}

	
	
	public function vote($vote) {
		if ($vote) {
			$rs = DB::row('SELECT setid, quiz FROM '.$this->prefix.$this->table.'_map WHERE id='.(int)$vote);
			if ($rs['setid'] && !@$this->sess['POLL'][$rs['setid']]) {
				$_SESSION['POLL'][$rs['setid']] = $vote;
				if ($rs['quiz']) {
					
				}
				DB::run('UPDATE '.$this->prefix.$this->table.'_map SET answers=answers+1 WHERE id='.$vote);
				DB::run('UPDATE '.$this->prefix.$this->table.' SET passes=passes+1 WHERE id='.$rs['setid']);	
			}
		}
		return array(
			'html'	=> $this->Index->Smarty->fetch('poll.tpl')
		);
	}	
}