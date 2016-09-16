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
* @file       mod/AdminMods.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminMods extends Admin {
	public function __construct() {
		parent::__construct(__CLASS__);
	}
}


if (is_file(FTP_DIR_TPL.'classes/MyAdminMods.php')) {
	require FTP_DIR_TPL.'classes/MyAdminMods.php';
}
elseif (is_file(FTP_DIR_ROOT.'mod/custom/'.TEMPLATE.'_MyAdminMods.php')) {
	require FTP_DIR_ROOT.'mod/custom/'.TEMPLATE.'_MyAdminMods.php';
}
elseif (is_file(FTP_DIR_ROOT.'mod/MyAdminMods.php')) {
	require FTP_DIR_ROOT.'mod/MyAdminMods.php';
}


class AdminMods_poll extends Admin {
	
	public function __construct() {
		$this->title = 'Polls and Quizzes';
		parent::__construct(__CLASS__);
	}
	
	public function init() {
		$this->table = 'poll';
		$this->id = (int)$this->id;
		$this->idcol = 'rid';
		$this->array['types'] = array(
			's'	=> 'Single',
			'm'	=> 'Multiple'
		);
		$this->options['quizzes'] = DB::getAll('SELECT DISTINCT quiz FROM '.$this->prefix.$this->table.' WHERE quiz!=\'\' ORDER BY quiz','quiz');
	}
	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			
		}
	}
	
	private function validate() {
		$err = array();
		if (!$this->data['name']) {
			$err['name'] = lang('$Poll name is required');
		}
		elseif (strlen($this->data['name'])>50) {
			$err['name'] = lang('$Poll name must be no longer than 50 characters');	
		}
		elseif (!$this->data['quiz'] && $this->data['name'] && DB::getNum('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE '.($this->id?'rid!='.$this->id.' AND ':'').'name LIKE '.e($this->data['name']).' AND lang=\''.$this->lang.'\' AND active!=2')) {
			$err['name'] = lang('$Such name already presents, try different one');
		}
		if (!$this->data['title']) {
			$err['title'] = lang('$Question cannot stay empty');	
		}
		$this->errors($err);
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->validate();
				if (!isset($this->data['passes'])) $this->data['passes'] = 0;
				$this->global_action('save');
				
				if ($this->id && $this->updated) {
					$aff = $i = $j = $y = 0;
					$ids = array();
					$setid = DB::one('SELECT id FROM '.$this->prefix.'poll WHERE rid='.$this->id.' AND lang=\''.$this->lang.'\'');
					$setids = DB::getAll('SELECT id FROM '.$this->prefix.'poll WHERE rid='.$this->id,'id');

					DB::run('UPDATE '.$this->prefix.'poll SET name='.e($this->data['name']).', quiz='.e($this->data['quiz']).', `type`='.e($this->data['type']).', sort='.e($this->data['sort']).' WHERE rid='.$this->id);
					
					foreach ($this->data['map'] as $id => $a) {
						if ($id) $j++;
					}
					foreach ($this->data['map'] as $id => $a) {
						if ($id) {
							if (!strlen(trim($a['answer']))) continue;
							$data = array(
								'answer'	=> $a['answer'],
								'score'		=> $a['score'],
								'sort'		=> $i
							);
							DB::update('poll_map',$data,$id);
							$ids[] = $id;
							$aff += DB::affected();
							$i++;
						} else {
							foreach ($a as $y => $_a) {
								if (!strlen(trim($_a['answer']))) continue;
								if ($this->updated==Site::ACTION_INSERT) {
									foreach ($setids as $_setid) {
										$data = array(
											'setid'		=> $_setid,
											'answer'	=> $_a['answer'],
											'score'		=> $_a['score'],
											'answers'	=> 0,
											'sort'		=> ($y?$i+$j:0)
										);
										DB::insert('poll_map',$data);
										$aff++;
									}
								} else {
									if (!$setid) 
									if (!$setid) return;
									$data = array(
										'setid'		=> $setid,
										'answer'	=> $_a['answer'],
										'score'		=> $_a['score'],
										'answers'	=> 0,
										'sort'		=> ($y?$i+$j:0)
									);
									DB::insert('poll_map',$data);
									$ids[] = DB::id();
									$aff++;
								}
								$i++;
							}
						}
					}
					if ($this->updated==Site::ACTION_UPDATE && $setid) {
						DB::run('DELETE FROM '.$this->prefix.'poll_map WHERE '.($ids?'id NOT IN ('.join(', ',$ids).') AND ':'').'setid='.$setid);
						$aff += DB::affected();
					}
					$this->global_action('msg');
					if ($aff) {
						$this->msg_reload = true;
						if (!$this->msg_text) $this->msg_text = lang('$Poll answers were saved');
						if ($this->updated==Site::ACTION_INSERT) {
							DB::run('UPDATE '.$this->prefix.'poll SET answers='.$i.' WHERE rid='.$this->id);
						} else {
							DB::run('UPDATE '.$this->prefix.'poll SET answers='.$i.' WHERE id='.$this->id);
						}
					}
				}
			break;
			case 'sort':
				$this->global_action();
			break;
			case 'act':
				$this->global_action();
			break;
			case 'delete':
				$setids = DB::getAll('SELECT id FROM '.$this->prefix.'poll WHERE rid='.$this->id,'id');
				if (!$setids) break;
				DB::run('DELETE FROM '.$this->prefix.'poll WHERE rid='.$this->id);
				DB::run('DELETE FROM '.$this->prefix.'poll_map WHERE setid IN ('.join(', ',$setids).')');
			break;
		}
	}
	
	private function sql() {
		if ($this->find) {
			$this->filter .= ' AND (title LIKE \'%'.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';	
		}
		$this->order = 'id DESC';
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->sql();
		if ($this->submitted) {
			
		}
		$vote = (int)get('vote');
		if ($vote) {
			$setid = DB::row('SELECT setid FROM '.$this->prefix.'poll_map WHERE id='.$vote,'setid');
			if ($setid) {
				DB::run('UPDATE '.$this->prefix.'poll_map SET answers=answers+1 WHERE id='.$vote);
				DB::run('UPDATE '.$this->prefix.'poll SET passes=passes+1 WHERE id='.$setid);	
			}
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, rid, name, title, passes, answers, active, quiz, DATE_FORMAT(FROM_UNIXTIME(added), \'%d %b %Y\') AS added FROM '.$this->prefix.$this->table.' WHERE lang=\''.$this->lang.'\''.$this->filter.' ORDER BY quiz, sort, '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			$rs['map'] = DB::getAll('SELECT id, answer, answers, score FROM '.$this->prefix.'poll_map WHERE setid='.$rs['id'].' ORDER BY sort');
			$this->data[] = $rs;
		}
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function window() {
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE rid='.$this->id.' AND lang=\''.$this->lang.'\'');
			if (!$this->post) $this->id = 0;
			else $this->post['answers'] = DB::getAll('SELECT * FROM '.$this->prefix.$this->table.'_map WHERE setid='.$this->post['id'].' ORDER BY sort');
		}
		if (!isset($this->post['answers'])) $this->post['answers'] = array();
		if (!$this->post || !isset($this->post['id'])) {
			$this->post = post('data');
		} else {
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
		}

		$this->win($this->name);
	}
	
}

if (!class_exists('AdminMods_results')) {
class AdminMods_results extends Admin {
	public function __construct() {
		$this->title = 'Quiz results';
		parent::__construct(__CLASS__);
	}
	
	public function init() {
		$this->table = 'poll_results';
		$this->id = (int)$this->id;
		$this->idcol = 'rid';
		$this->options['quizzes'] = DB::getAll('SELECT DISTINCT quiz FROM '.$this->prefix.'poll WHERE quiz!=\'\' ORDER BY quiz','quiz');
	}
	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			
		}
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = false;

		if ($this->submitted) {
			
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS *, DATE_FORMAT(FROM_UNIXTIME(added), \'%d %b %Y\') AS added FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			
			$this->data[] = $rs;
		}
		
		$this->total = DB::rows($this->data);
		$this->json_data = json($this->data);
		$this->nav();
	}
}
}

class AdminMods_ai extends Admin {

	protected $content_modules = array();

	public function __construct() {
		$this->title = 'Chat bot manager';
		parent::__construct(__CLASS__);
	}
	public function init() {		
		$this->id = (int)$this->id;
		$this->idcol = 'id';
		$this->table = 'ai';
		$this->options['anc'] = '';
		$this->array['emotions'] = Conf()->g('emotions');
		$this->array['anchors'] = DB::getAll('SELECT DISTINCT anc FROM '.$this->prefix.'ai WHERE anc!=\'\' ORDER BY anc','anc');
		$this->array['anchors'] = array_merge($this->array['anchors'], DB::getAll('SELECT DISTINCT anc FROM '.$this->prefix.'ai_answers WHERE anc!=\'\' ORDER BY anc','anc'));
	}
	
	public function install() {
		$sql = 'CREATE TABLE `'.$this->prefix.'ai` (
  `id` int(11) NOT NULL auto_increment,
  `question` varchar(255) NOT NULL,
  `anc` varchar(100) default NULL,
  `catref` varchar(255) default NULL,
  `tags` varchar(255) default NULL,
  `edited` int(10) NOT NULL,
  `userid` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `question` (`question`),
  KEY `anc` (`anc`),
  FULLTEXT KEY `question_2` (`question`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8';	
		DB::run($sql);
		
		$sql = 'CREATE TABLE `'.$this->prefix.'ai_answers` (
  `id` int(11) NOT NULL auto_increment,
  `setid` int(11) NOT NULL,
  `answer` text,
  `emotion` tinyint(2) NOT NULL,
  `anc` varchar(150) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
		DB::run($sql);
		
		$sql = 'CREATE TABLE `'.$this->prefix.'ai_talk` (
  `id` int(11) NOT NULL auto_increment,
  `question` varchar(255) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `answerid` int(11) NOT NULL,
  `setid` int(11) NOT NULL,
  `userid` int(10) NOT NULL,
  `ip` int(10) NOT NULL,
  `added` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';
		DB::run($sql);
	}
	
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.$this->get);
			break;
		}
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		$this->id = get('id');
		if (!$this->id) $this->id = request('id');
		switch ($this->action) {
			case 'save':
				$this->allow('ai','save',$this->rs,$this->data);
				foreach ($this->data as $id => $rs) {
					if ($id) {
						if (isset($rs['question']) && $rs['question']) DB::update($this->table,$rs,$id);
						if (isset($rs['answers'])) {
							foreach ($rs['answers'] as $_id => $_rs) {
								if ($_id) {
									if (!$_rs['answer']) {
										DB::run('DELETE FROM '.$this->prefix.$this->table.'_answers WHERE id='.$_id);
									} else {
										DB::update($this->table.'_answers',$_rs, $_id);
									}
								} else {
									foreach ($_rs as $i => $__rs) {
										if (!$__rs['answer']) continue;
										$data = array(
											'setid'		=> $id,
											'answer'	=> $__rs['answer'],
											'emotion'	=> $__rs['emotion'],
											'anc'		=> $__rs['anc']
										);
										DB::insert($this->table.'_answers',$data);
									}
								}
							}
						}
					}
					elseif ($rs['question']) {
						$data = array(
							'question'	=> $rs['question'],
							'anc'		=> $rs['anc'],
							'tags'		=> $rs['tags'],
							'catref'	=> $this->catref,
							'edited'	=> $this->time,
							'userid'	=> $this->Index->Session->UserID,
							'active'	=> 1
						);
						DB::insert($this->table, $data);
						$ins_id = DB::id();
						if (isset($rs['answers'])) {
							foreach ($rs['answers'] as $_rs) {
								foreach ($_rs as $i => $__rs) {
									if (!$__rs['answer']) continue;
									$data = array(
										'setid'		=> $ins_id,
										'answer'	=> $__rs['answer'],
										'emotion'	=> $__rs['emotion'],
										'anc'		=> $__rs['anc']
									);
									DB::insert($this->table.'_answers',$data);
								}
							}
						}
					}
				}
			break;
			case 'act':
				$this->global_action('act');
			break;
			case 'sort':
			
			break;
			case 'delete':
				$this->allow('ai','delete',$this->rs,$this->data);
				if (post('answer')) {
					DB::run('DELETE FROM '.$this->prefix.$this->table.'_answers WHERE id='.$this->id);
					$this->json_ret = array(
						'js'	=> '$(\'#a-aia_'.$this->id.'\').hide();'
					);					
				} else {
					$this->global_action('delete');
					if ($this->affected) DB::run('DELETE FROM '.$this->prefix.$this->table.'_answers WHERE setid='.$this->id);
					$this->json_ret = array(
						'js'	=> '$(\'#a-ai_'.$this->id.'\').prev().hide().next().hide().next().hide().next().hide();'
					);
				}
			break;
			case 'ask':
				$result = Factory::call('ai')->clear()->params(array('save'=>false))->question(post('question'))->result();
				if (!$result) $result = 'answers nothing..';
				$this->msg_reload = false;
				$this->msg_delay = 2000;
				$this->msg_text = $result;
				break;
				$this->json_ret = array('js'=>'S.G.alert(\'<u>'.lang('_$Your question:').'</u><br />'.post('question').'<br /><br /><u>'.lang('_$Bot will answer:').'</u><br />'.strjs($result).'\',\''.lang('_$Result from bot chat').'\');');
			break;
		}
	}
	
	public function sql() {
		$this->order = '`id` DESC';
	//	$this->order = 'question';
		if ($this->find) {
			if (DB::row('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE anc LIKE \''.$this->find.'\'')) {
				$this->options['anc'] = $this->find;
				$this->filter .= ' AND anc LIKE \''.$this->find.'\'';
			} else {
				$this->filter .= ' AND (question LIKE \'%'.$this->find.'%\' OR (SELECT setid FROM '.$this->prefix.$this->table.'_answers WHERE answer LIKE \'%'.$this->find.'%\' LIMIT 1)=id)';
			}
		}
		if ($this->catref) {
			$this->filter .= ' AND (catref=\''.$this->catref.'\' OR catref LIKE \''.$this->catref.'.%\')';	
		}
	}
	
	public function listing() {
		$this->data = array();
		$this->button['save'] = true;
		$this->button['add'] = true;
		$this->sql();
		if ($this->submitted && post('data')) {
			$this->data = post('data');
			$this->action('save');
		}
		$params = array(
			'table'		=> 'category_'.$this->table,
			'lang'		=> $this->lang,
			'catalogue'	=> false,
			'retCount'	=> true,
			'countTable'=> $this->table,
			'getAfter'	=> false,
			'noDisable'	=> true,
			'maxLevel'	=> 0,
			'optValue'	=> 'catid',
			'getHidden'	=> true,
			'prefix'	=> $this->prefix,
			'selected'	=> $this->catid
		);
		$this->category_options = Factory::call('category', $params)->getAll()->toOptions();
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		while ($rs = DB::fetch($qry)) {
			$rs['question'] = html($rs['question']);
			$rs['anc'] = html($rs['anc']);
			$rs['tags'] = html($rs['tags']);
			$rs['answers'] = DB::getAll('SELECT id, answer, anc, emotion FROM '.$this->prefix.'ai_answers WHERE setid='.$rs['id'].' ORDER BY id DESC');
			$this->data[] = $rs;
		}
		$this->total = DB::rows($this->data);
		if ($this->find && !$this->total) {
			$row = Factory::call('ai')->clear()->params(array('save'=>false))->question($this->find)->find_all();
			$this->options['foredit'] = $row;
			$this->options['answer'] = Factory::call('ai')->getResult();
		}
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function window() {
		if ($this->id && $this->id!=self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
			$this->post['answers'] = DB::getAll('SELECT id, answer, anc, emotion FROM '.$this->prefix.'ai_answers WHERE setid='.$this->post['id'].' ORDER BY id DESC');
		}
		if (!$this->post['answers']) $this->post['answers'] = array();
		if (!$this->post || !$this->post['id']) {
			$this->post = post('data');
			$this->post['id'] = 0;
		} else {
			$this->isEdit();
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
		}
		$this->win($this->name);
	}
}



class AdminMods_geo extends Admin {
	
	public function __construct() {
		$this->title = 'Geo editor';
		parent::__construct(__CLASS__);
		if (!in_array('geo_countries',DB::tables(true, true))) {
			return URL::redirect('?'.URL_KEY_ADMIN);	
		}
	}
	
	public function init() {
		$this->table = $this->name;
		$this->country = intval(get('country'));
		$this->state = intval(get('state'));
		$this->city = intval(get('city'));
		$this->button['save'] = true;
		if ($this->city && !$this->state) $this->city = '';
		if ($this->state && !$this->country) $this->state = '';
	}
	
	protected function listing() {
		$prefix = 'geo_';
		if ($this->submitted) {
			
			if ($this->city) {
				$table = 'districts';
				$idcol = 'number';
				$where = ' AND city_number='.$this->city.' AND state_number='.$this->state.' AND country_id='.$this->country;
				$save = array(
					'city_number'	=> $this->city,
					'state_number'	=> $this->state,
					'country_id'=> $this->country,
				);
			}
			elseif ($this->state) {
				$table = 'cities';
				$idcol = 'number';
				$where = ' AND state_number='.$this->state.' AND country_id='.$this->country;
				$save = array(
					'state_number'	=> $this->state,
					'country_id'=> $this->country,
				);
			}
			elseif ($this->country) {
				$table = 'states';
				$idcol = 'number';
				$where = ' AND country_id='.$this->country;
				$save = array(
					'country_id'=> $this->country,
				);
			} else {
				$table = 'countries';
				$idcol = 'id';
				$where = '';
				$save = array();
			}
			
			$data = post('data');
			
			
			if (!$this->country) {
				$codes = DB::getAll('SELECT code FROM '.DB_PREFIX.$prefix.'countries','code');
			}
			$deleted = array();
			if (isset($data['del'])) {
				if (!Allow()->admin('geo','delete')):
					if ($table=='countries') {
						foreach ($data['del'] as $k => $on) {
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'countries WHERE id='.(int)$k.$where);
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'states WHERE country_id='.(int)$k.$where);
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'cities WHERE country_id='.(int)$k.$where);
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'districts WHERE country_id='.(int)$k.$where);
						}
					}
					elseif ($table=='states') {
						foreach ($data['del'] as $k => $on) {
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'states WHERE number='.(int)$k.$where);
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'cities WHERE state_number='.(int)$k.$where);
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'districts WHERE state_number='.(int)$k.$where);
						}
					}
					elseif ($table=='cities') {
						foreach ($data['del'] as $k => $on) {
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'cities WHERE number='.(int)$k.$where);
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'districts WHERE city_number='.(int)$k.$where);
						}
					}
					elseif ($table=='districts') {
						foreach ($data['del'] as $k => $on) {
							DB::run('DELETE FROM '.DB_PREFIX.$prefix.'districts WHERE number='.(int)$k.$where);
						}
					}
					$deleted[] = $k;
				endif;
				unset($data['del']);
			}
			if (isset($data['new'])) {
				if (!Allow()->admin('geo','save')):
					foreach ($data['new'] as $next => $_a) {
						if (!$this->country && (!$_a['code'] || strlen($_a['code'])!=2 || in_array($_a['code'], $codes))) continue;
						if (!$_a['name_'.$this->lang] || !$next) continue;
						$_a = array_merge($_a, $save);
						if ($this->country) {
							$_a['number'] = $next;
						} else {
							$_a['id'] = $next;
						}
						DB::insert($prefix.$table,$_a);
					}
				endif;
				unset($data['new']);
			}
			if ($data && is_array($data)) {
				foreach ($data as $k => $a) {
					if (!Allow()->admin('geo','edit')):
						if (!$a['name_'.$this->lang] || in_array($k, $deleted)) continue;
						DB::update($prefix.$table,$a,$k,$idcol,$where);
					endif;
				}
			}
		}
		
		
		$t = array();
		
		$url = '?'.URL_KEY_ADMIN.'=mods&'.self::KEY_TAB.'=geo';
		
		
		$t[] = '<a href="javascript:;" onclick="S.A.L.get(\''.$url.'\',false,\'geo\')">'.lang('$World').'</a>';
		if ($this->country) {
			$t[] = '<a href="javascript:;" onclick="S.A.L.get(\''.$url.'&country='.$this->country.'\',false,\'geo\')">'.DB::one('SELECT name_'.$this->lang.' FROM '.DB_PREFIX.$prefix.'countries WHERE id='.$this->country).'</a>';
		}
		if ($this->state) {
			$t[] = '<a href="javascript:;" onclick="S.A.L.get(\''.$url.'&country='.$this->country.'&state='.$this->state.'\',false,\'geo\')">'.DB::one('SELECT name_'.$this->lang.' FROM '.DB_PREFIX.$prefix.'states WHERE country_id='.$this->country.' AND number='.$this->state).'</a>';
		}
		if ($this->city) {
			$t[] = '<a href="javascript:;" onclick="S.A.L.get(\''.$url.'&country='.$this->country.'&state='.$this->state.'&city='.$this->city.'\',false,\'geo\')">'.DB::one('SELECT name_'.$this->lang.' FROM '.DB_PREFIX.$prefix.'cities WHERE country_id='.$this->country.' AND state_number='.$this->state.' AND number='.$this->city).'</a>';
		}
		$this->output['tree'] = join(' - ',$t);
			
		
		
		if ($this->city) {
			$sql = 'SELECT * FROM '.DB_PREFIX.$prefix.'districts WHERE country_id='.$this->country.' AND state_number='.$this->state.' AND city_number='.$this->city.' ORDER BY name_'.$this->lang;
			$this->title = 'Geo - Districts';
		}
		elseif ($this->state) {
			$sql = 'SELECT * FROM '.DB_PREFIX.$prefix.'cities WHERE country_id='.$this->country.' AND state_number='.$this->state.' ORDER BY name_'.$this->lang;
			$this->title = 'Geo - Cities';
		}
		elseif ($this->country) {
			$sql = 'SELECT * FROM '.DB_PREFIX.$prefix.'states WHERE country_id='.$this->country.' ORDER BY name_'.$this->lang;	
			$this->title = 'Geo - States';
		} else {
			$sql = 'SELECT * FROM '.DB_PREFIX.$prefix.'countries ORDER BY name_'.$this->lang;
			$this->title = 'Geo - Countries';
		}
		
		$this->data = array();
		$qry = DB::qry($sql,0,0);
		$langs = array('en','ee','ru');
		$m = array();
		while ($rs = DB::fetch($qry)) {
			$a = $row = array();
			foreach ($langs as $l) {
				$a[$l] = $rs['name_'.$l];
			}
			$row['n'] = $a;
			$row['s'] = intval(@$rs['sort']);
			
			if ($this->city) {
				$row['h'] = $rs['number'];
				$row['t'] = $rs['number'];
				$row['l'] = '';
			}
			elseif ($this->state) {
				$row['h'] = $rs['number'];
				$row['t'] = $rs['number'];
				$row['l'] = '?'.URL_KEY_ADMIN.'=mods&'.self::KEY_TAB.'=geo&country='.$rs['country_id'].'&state='.$rs['state_number'].'&city='.$rs['number'];
			}
			elseif ($this->country) {
				$row['h'] = $rs['number'];
				$row['t'] = $rs['number'];
				$row['l'] = '?'.URL_KEY_ADMIN.'=mods&'.self::KEY_TAB.'=geo&country='.$rs['country_id'].'&state='.$rs['number'];
			} else {
				$f = FTP_DIR_TPLS.'img/flags/16/'.$rs['code'].'.png';
				if (is_file($f)) {
					$row['h'] = '<img src="/tpls/img/flags/16/'.$rs['code'].'.png" title="'.$rs['code'].'" /> ['.$rs['id'].']';
				} else {
					$row['h'] = $rs['code'];
				}
				$row['t'] = $rs['id'];
				$row['l'] = '?'.URL_KEY_ADMIN.'=mods&'.self::KEY_TAB.'=geo&country='.$rs['id'];
			}
			$m[] = $row['t'];
			array_push($this->data, $row);
		}
		if (count($m)) {
			$this->output['next'] = max($m)+1;
		} else {
			$this->output['next'] = 1;
		}
		$this->json_langs = json($langs);
		DB::free($qry);
		$this->json_data = json($this->data);
	}

}


if (!class_exists('AdminMods_friends')) {
class AdminMods_friends extends Admin {
	public function __construct() {
		$this->title = 'User\'s friends';
		parent::__construct(__CLASS__);
	}
	
	public function init() {
		$this->table = 'users_friends';
		$this->id = (int)$this->id;
		$this->idcol = 'id';
	}
	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			
		}
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
			
			break;
			case 'delete':
				if ($this->id) {
					DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
					$this->msg_reload = true;
				}
				
			break;
			case 'confirm':
				if (!$this->id) break;
					
				$row = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);

				if (post('to')=='Y' && $row['confirmed']!='Y') {					
					
					if ($row['price']) {
						$price_default = $this->convertMoney($row['price'], $row['currency'], DEFAULT_CURRENCY);
						$total_sum_default = $this->count_money($row['userid'], '', DEFAULT_CURRENCY);

						if ($price_default < $total_sum_default) {
							DB::run('UPDATE '.DB_PREFIX.$this->table.' SET confirmed=\'Y\' WHERE id='.$this->id);
							$this->msg_text = 'Friendship was confirmed, balance was transfered';
							$data = array(
								'price'		=> -$row['price'],
								'currency'	=> $row['currency'],
								'title'		=> 'Friend was accepted by '.$this->Index->Session->Login.' and '.number_format($price_default,2,'.','').' '.DEFAULT_CURRENCY.' were transfered from this user to his friend\'s hook',
								'userid'	=> $row['userid'],
								'added'		=> time(),
								'account'	=> '',
								'status'	=> Site::TRANSFER_CONFIRM
							);
							
							DB::insert('users_transfers',$data);
							
							$this->run_hook($row['hook'],0,$row);
							$this->count_money($row['userid'], '', DEFAULT_CURRENCY);
							$this->msg_reload = true;
							
						} else {
							$this->msg_text = 'Friendship cannot be confirmed, since balance is not sufficient to accept';
							$this->msg_type = 'stop';
						}
					} else {
						DB::run('UPDATE '.DB_PREFIX.$this->table.' SET confirmed=\'Y\' WHERE id='.$this->id);
						$this->msg_text = 'Friendship was confirmed';
						$this->msg_reload = true;
					}
					
				}
				elseif (post('to')=='N' && $row['confirmed']!='N') {
					DB::run('UPDATE '.DB_PREFIX.$this->table.' SET confirmed=\'N\' WHERE id='.$this->id);
					$this->msg_text = 'Friendship was denied';
					$this->msg_reload = true;
				}
				elseif (!post('to') && $row['confirmed']) {
					DB::run('UPDATE '.DB_PREFIX.$this->table.' SET confirmed=\'\' WHERE id='.$this->id);
					$this->msg_text = 'Friendship was nulled';
					if ($row['price']) {
						$price_default = $this->convertMoney($row['price'], $row['currency'], DEFAULT_CURRENCY);
						$data = array(
							'price'		=> $row['price'],
							'currency'	=> $row['currency'],
							'title'		=> 'Friend was rejected by '.$this->Index->Session->Login.' and '.number_format($price_default,2,'.','').' '.DEFAULT_CURRENCY.' were transfered back from his friend\'s hook',
							'userid'	=> $row['userid'],
							'added'		=> time(),
							'account'	=> '',
							'status'	=> Site::TRANSFER_NULL
						);
						DB::insert('users_transfers',$data);
						
						$this->run_hook($row['hook'],1,$row);
						$this->count_money($row['userid'], '', DEFAULT_CURRENCY);
					}
					$this->msg_reload = true;
				}
				
			break;
			case 'act':
				
				if ($this->id) {
					$old = DB::one('SELECT confirmed FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
					DB::run('UPDATE '.DB_PREFIX.$this->table.' SET confirmed=\''.($old=='Y'?'N':'Y').'\' WHERE id='.$this->id);
					//$this->msg_reload = true;
				}
				
			break;
		}
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = false;
		$this->order = 'f.added DESC';

		$sql = 'SELECT SQL_CALC_FOUND_ROWS *, DATE_FORMAT(FROM_UNIXTIME(added), \'%d %b %Y\') AS date FROM '.DB_PREFIX.$this->table.' f WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->data = array();
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			$s = array();
			$rs['user'] = Data::user($rs['userid'],'login');
			$rs['friend'] = Data::user($rs['setid'],'login');
			if ($rs['confirmed']=='Y') $s[] = 'Confirmed';
			elseif ($rs['confirmed']=='N') $s[] = 'Denied';
			else $s[] = 'Pending';
			if ($rs['blocked']=='Y') $s[] = 'Blocked';
			$rs['s'] = join('; ',$s);
			$this->data[] = $rs;
		}
		
		$this->json_data = json($this->data);
		$this->nav();
	}
}
}



