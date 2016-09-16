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
* @file       mod/AdminGlobal.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminGlobal extends Admin {
	
	
	
	public function __construct() {
		parent::__construct(__CLASS__);	
	}
	
	protected function init() {
		$this->action();
	}
	
	protected function action($action = false) {
		switch ($this->action) {
			case 'user_data':
				$this->json_ret = Data::getUser($this->id);
			break;
			case 'user_search':
				$term = post('term');
				$table = post('table');
				if ($table && !in_array($table, DB::tables())) $table = false; else $table = DB::prefix($table);
				$sql = 'SELECT u.id AS value, login AS label FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile up ON up.setid=u.id'.($table?' INNER JOIN '.$table.' t ON t.userid=u.id':'').' WHERE active!=2 AND (login LIKE '.escape($term.'%').' OR u.email LIKE '.escape($term.'%').' OR CONCAT(up.firstname,\' \',up.lastname) LIKE '.escape($term.'%').') GROUP BY u.id ORDER BY login LIMIT 0,20';
				$data = DB::getAll($sql);
				if (!$data) {
					$sql = 'SELECT u.id AS value, login AS label FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile up ON up.setid=u.id'.($table?' INNER JOIN '.$table.' t ON t.userid=u.id':'').' WHERE active!=2 AND (login LIKE '.escape('%'.$term.'%').' OR u.email LIKE '.escape('%'.$term.'%').' OR CONCAT(up.firstname,\' \',up.lastname) LIKE '.escape('%'.$term.'%').') GROUP BY u.id ORDER BY login LIMIT 0,20';
					$data = DB::getAll($sql);
				}
				$this->json_ret = $data;
			break;
			case 'col_value':
				$this->colValue(post('column'), post('table'), post('id'));
			break;
			case 'excel':
				$this->allow('qry','export');
				if (post('db_name','',DB_NAME)!=DB_NAME) mysqli_select_db(DB::link(),post('db_name','',DB_NAME));
				$file = $this->export_excel(post('sql'),post('table'));
				if (post('db_name','',DB_NAME)!=DB_NAME) mysqli_select_db(DB::link(),DB_NAME);
				$this->json_ret = array(
					'js'	=> 'S.G.alert(\'Download from here: <a style="color:inherit" target="_blank" href="'.$file.'">'.$file.'</a><br /><br />If file does not exists yet, then wait few seconds until it generates completely and try to click again!\',\''.post('table').' export\');'
				);
			break;
			case 'csv':
				$this->allow('qry','export');
				if (post('db_name','',DB_NAME)!=DB_NAME) mysqli_select_db(DB::link(),post('db_name','',DB_NAME));
				$file = $this->export_csv(post('sql'),post('table'),post('db_name'));
				if (post('db_name','',DB_NAME)!=DB_NAME) mysqli_select_db(DB::link(),DB_NAME);
				$this->json_ret = array(
					'js'	=> 'S.G.alert(\'Download from here: <a style="color:inherit" target="_blank" href="'.$file.'">'.$file.'</a><br /><br />If file does not exists yet, then wait few seconds until it generates completely and try to click again!\',\''.post('table').' export\');'
				);
			break;
			case 'sql':
				$this->allow('qry','export');
				if (post('db_name','',DB_NAME)!=DB_NAME) mysqli_select_db(DB::link(),post('db_name','',DB_NAME));
				$file = $this->export_sql(post('sql'),post('table'),post('db_name'));
				if (post('db_name','',DB_NAME)!=DB_NAME) mysqli_select_db(DB::link(),DB_NAME);
				$this->json_ret = array(
					'js'	=> 'S.G.alert(\'Download from here: <a style="color:inherit" target="_blank" href="'.$file.'">'.$file.'</a><br /><br />If file does not exists yet, then wait few seconds until it generates completely and try to click again!\',\''.post('table').' export\');'
				);
			break;
			case 'codebb':
				$this->json_ret = array('text' => Parser::parse('code_bb', $_POST['text']));
			break;
			case 'main_chart':
				$this->main_chart();
			break;
			case 'link':
				$this->link($_POST['table'], $_POST['itemid']);
			break;
			case 'session':
				if (!is_array($_SESSION['AdminGlobal'])) $_SESSION['AdminGlobal'] = array();
				if ($_POST['value']==='false') $_POST['value'] = false;
				elseif ($_POST['value']==='true') $_POST['value'] = true;
				$_SESSION['AdminGlobal'][$_POST['key']] = $_POST['value'];
				$this->json_ret = array(0=>0);
			break;
			case 'design':
				$this->designEdit();
			break;
			case 'undo':
				$col = post('column');
				$table = post('table');
				if (!$this->id || !$table || !$col || is_array($col) || is_array($table)) {
					$this->json_ret = array('error'=>'Invalid arguments');
					break;	
				}
				$rs = DB::row('SELECT `id`, `data`, `title`, `added`, (SELECT login FROM '.DB_PREFIX.'users u WHERE u.id=userid) AS user FROM `'.DB_PREFIX.'log` WHERE template=\''.$this->tpl.'\' AND action='.Site::ACTION_UPDATE.' AND `table`='.e($this->current['PREFIX'].$table).' AND `setid`='.(int)$this->id.' ORDER BY added DESC, id DESC');
				$ser = false;
				if ($rs && $rs['data']) {
					$ser = strexp($rs['data']);
					if (!isset($ser['old'][$col])) {
						$this->json_ret = array('error'=>'Fatal error, column does not exists');
						break;	
					}
					$html = $ser['old'][$col];
					if ($html) {
						$this->json_ret = array(
							'html'	=> $html,
							'quick' => strpos($rs['title'],'was quickly'),
							'title'	=> $rs['title'],
							'added'	=> date('d M H:i, Y',$rs['added']),
							'user'	=> $rs['user'],
							'id'	=> $rs['id']
						);	
						break;
					}
				}
				$this->json_ret = array('html'=>0,'col'=>$col,'table'=>$table,'id'=>$this->id);
			break;
			default:
				if (substr($this->action,0,7)=='visual_') {
					$method = 'visual'.ucfirst(substr($this->action,7));
					if (method_exists($this, $method)) {
						$this->$method();	
					}
				}
			break;
		}
	}
	
	protected function listing() {
		exit;	
	}
	
	public function window() {
		switch ($this->action) {
			case 'visual_add':
				$this->allow('visual','preview');
				$this->post = array(
					'visual_add'=> get('visual_add'),
					'above'		=> get('above'),
					'html'		=> $from['html'],
					'title'		=> (get('above')?lang('_$Add new HTML above the selected block'):lang('_$Add new HTML below the selected block'))
				);
				$this->win('visual');
			break;
			case 'visual_edit':
				$this->allow('visual','preview');
				if (!get('visual_id')) return false;
				$from = $this->visualHTML(true, get('visual_id'), true);
				$this->post = array(
					'visual_id'	=> get('visual_id'),
					'html'		=> $from['html'],
					'title'		=> lang('_$Edit template block %1 on line %2','&quot;'.str_replace('_visual_','',$from['file']).'&quot;',$from['line'])
				);
				$this->win('visual');
			break;
			case 'image_browser':
				$this->name_id = 'visual_image';
				$this->win('visual_image');
			break;
		}
	}	
	
	private function colValue($col, $table, $id) {
		if (!$col || !$table || !$id) {
			$this->json_ret = array('error' => 'Missing arguments');
			return;
		}
		$columns = DB::columns($table);
		$id = (int)$id;
		if (!$columns) {
			$this->json_ret = array('error' => 'No such table ('.$table.')');
			return;
		}
		if (!in_array($col,$columns)) {
			$this->json_ret = array('error' => 'Missing column or table ('.DB::prefix($table).':'.$col.')');
			return;
		}
		if (in_array('rid', $columns)) {
			$w = ' WHERE rid='.$id.' AND lang='.e($this->lang).'';
		}
		else {
			$w = ' WHERE id='.$id;
		}
		$sql = 'SELECT `'.$col.'` FROM '.DB::prefix($table).' WHERE id='.$id;
		$this->json_ret = array('value'=>DB::one($sql));
	}	
	
	private function main_chart() {
		$ret = $data = array();
		$ret['debug'] = false;
		$ret['multiple'] = false;
		$ret['height'] = 290;
		$ret['swf'] = 'Line';
		$date_type = request('date_type','','[[:CACHE:]]');
		
		switch ($date_type) {
			case 'year':
				$ago = $this->time - 86400;
				$format = '%M %Y';
			break;
			case 'week':
				$ago = $this->time - 604800;
				$format = '%H %d %M %Y';
			break;
			case 'day':
				$ago = $this->time - 86400;
				$format = '%H:%i %d %M %Y';
			break;
			default:
				$ago = $this->time - 2592000;
				$format = '%d %M %Y';
			break;
		}
		
		$this->array['date_types'] = array (
			'month'	=> 'Month',
			'week'	=> 'Week',
			'day'	=> 'Day',
			'year'	=> 'Year'
		);
		
		
		
		
		switch ($this->type) {
			case 'views':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(viewed),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'views WHERE viewed>'.$ago.' GROUP BY x ORDER BY viewed';
				$ret['y'] = 'Page views';
			break;
			case 'comments':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'comments WHERE added>'.$ago.' GROUP BY x ORDER BY added';
				$ret['y'] = 'Comments added';
			break;
			case 'pages':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'content WHERE added>'.$ago.' GROUP BY x ORDER BY added';
				$ret['y'] = 'Content blocks added';
			break;
			case 'entries':
				$modules = Site::getModules('content');
				$unions = array();
				foreach ($modules as $m => $a) {
					$unions[] = '(SELECT added, DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'content_'.$m.' WHERE lang=\''.$this->lang.'\' AND added>'.$ago.' GROUP BY x ORDER BY added)';
				}
				$sql = 'SELECT tmp.x, tmp.y FROM ('.join(' UNION ',$unions).') AS tmp ORDER BY tmp.added';
				$ret['y'] = 'Content entries added';
			break;
			case 'grids':
				$modules = Site::getModules('grid');
				$unions = array();
				foreach ($modules as $m => $a) {
					$unions[] = '(SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'grid_'.$m.' WHERE added>'.$ago.' GROUP BY x ORDER BY added)';
				}
				$sql = 'SELECT tmp.x, tmp.y FROM ('.join(' UNION ',$unions).') AS tmp';
				$ret['y'] = 'Grid entries added';
			break;
			case 'users':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(registered),\''.$format.'\') AS x, COUNT(1) AS y FROM '.DB_PREFIX.'users WHERE registered>'.$ago.' GROUP BY x ORDER BY registered';
				$ret['y'] = 'Users registered';
			break;
			case 'orders':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(ordered),\''.$format.'\') AS x, COUNT(1) AS y FROM '.DB_PREFIX.'orders2 WHERE ordered>'.$ago.' GROUP BY x ORDER BY ordered';
			//	$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'orders WHERE added>'.$ago.' GROUP BY x ORDER BY added';
				$ret['y'] = 'Total orders';
			break;
			case 'sold':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(ordered),\''.$format.'\') AS x, COUNT(1) AS y FROM '.DB_PREFIX.'orders2 WHERE ordered>'.$ago.' AND `status` IN ('.join(', ',Conf()->g('order_statuses_ok')).') GROUP BY x ORDER BY ordered';
				$ret['y'] = 'Paid orders';
			break;
			case 'log':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.DB_PREFIX.'log WHERE added>'.$ago.' GROUP BY x ORDER BY added';
				$ret['y'] = 'Total orders';
			break;
			case 'im':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.DB_PREFIX.'im WHERE added>'.$ago.' GROUP BY x ORDER BY added';
				$ret['y'] = 'Total IM connects';
			break;
			case 'im_sub':
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(sent),\''.$format.'\') AS x, COUNT(1) AS y FROM '.DB_PREFIX.'im_sub WHERE sent>'.$ago.' GROUP BY x ORDER BY sent';
				$ret['y'] = 'Total IM messages';
			break;
			case 'hits_weekdays':
			case 'unique_weekdays':
			case 'clicks_weekdays':
			case 'duration_weekdays':
				$data = array();
				$ret['y'] = array(
					2	=> 'Monday',
					3	=> 'Tuesday',
					4	=> 'Wednesday',
					5	=> 'Thursday',
					6	=> 'Friday',
					0	=> 'Saturday',
					1	=> 'Sunday',
				);
				switch ($this->type) {
					case 'hits_weekdays':
						$g = 'SUM(cnt)';
					break;
					case 'unique_weekdays':
						$g = 'COUNT(1)';
					break;
					case 'clicks_weekdays':
						$g = 'SUM(clicks)';
					break;
					case 'duration_weekdays':
						$g = 'ROUND(SUM(duration)/60)';
					break;
				}
				$ret['multiple'] = true;
				$ret['swf'] = 'MSLine';
				foreach ($ret['y'] as $i => $n) {
					$sql = 'SELECT HOUR(cameon) AS x, '.$g.' AS y FROM '.$this->prefix.'visitor_stats WHERE WEEKDAY(cameon)='.$i.' GROUP BY x ORDER BY x';
					$data[$i] = DB::getAll($sql);
				}
			break;
			
			case 'hits':
				$sql = 'SELECT DATE_FORMAT(cameon,\''.$format.'\') AS x, SUM(cnt) AS y1, COUNT(1) AS y2 FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$ago.' GROUP BY x ORDER BY cameon';
				$ret['y'] = array(1=>'Hits',2=>'Unique visits');
				$ret['swf'] = 'MSArea';
			break;
			case 'unique':
				$sql = 'SELECT DATE_FORMAT(cameon,\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$ago.' GROUP BY x ORDER BY cameon';
				$ret['y'] = 'Unique visits';
				$ret['swf'] = 'Area2D';
			break;
			case 'clicks':
				$sql = 'SELECT DATE_FORMAT(cameon,\''.$format.'\') AS x, SUM(clicks) AS y FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$ago.' GROUP BY x ORDER BY cameon';
				$ret['y'] = 'Clicks';
			break;
			case 'duration':
				$sql = 'SELECT DATE_FORMAT(cameon,\''.$format.'\') AS x, ROUND(SUM(duration)/60) AS y1 FROM '.$this->prefix.'visitor_stats WHERE microtime>'.$ago.' GROUP BY x ORDER BY cameon';
				$ret['y'] = array(1=>'Visiting time');
				$ret['swf'] = 'MSLine';
			break;
			

			
			case 'c_click':
			case 'c_click_avg':
			case 'c_cnt':
			case 'c_duration':
			case 'c_duration_avg':
				$this->id = post('id');
				switch ($this->type) {
					case 'c_click':
						$y = 'MIN(click)';
						$t = 'Page popularity';
					break;
					case 'c_click_avg':
						$y = 'AVG(click)';
						$t = 'Page popularity average';
					break;
					case 'c_cnt':
						$y = 'COUNT(1)';
						$t = 'Page attendance';
					break;
					case 'c_duration':
						$y = 'SUM(duration/60)';
						$t = 'Page continuance';
					break;
					case 'c_duration_avg':
						$y = 'AVG(duration/60)';
						$t = 'Page average continuance';
					break;
					default:
						return false;
					break;
				}
				$where = '';
				if (substr($this->id,0,3)=='md5' && strlen($this->id)==35) {
					$where = 'md5_location='.e(substr($this->id,3)).'';
					$location = DB::one('SELECT location FROM '.$this->prefix.'visitor_clicks WHERE '.$where);
					$ret['title'] = lang('$'.$t.' for: %1',URL::ht('?'.$location));
					$sql = 'SELECT DATE_FORMAT(clicked,\''.$format.'\') AS x, '.$y.' AS y FROM '.$this->prefix.'visitor_clicks WHERE '.$where.' AND added>'.$ago.' AND visit_id>0 GROUP BY x ORDER BY added';

				} else {
					if (!$this->id) break;
					$s = urldecode($this->id);
					$ret['title'] = lang('$URL comparison for &quot;%1&quot; keywords',html($s));
					$ex = explode(' ',trim($s));
					$j = array();
					$ret['y'] = array();
					$ret['multiple'] = true;
					$ret['swf'] = 'MSLine';
					$data = array();
					$_data = array();
					
					foreach ($ex as $e) $j[] = 'location LIKE \'%'.$e.'%\'';
					$f .= ' AND ('.join(' OR ',$j).')';
					$main = DB::getAll('SELECT DATE_FORMAT(clicked,\''.$format.'\') AS x, '.$y.' AS y FROM '.$this->prefix.'visitor_clicks WHERE added>'.$ago.' AND visit_id>0 AND location LIKE '.e('%'.$e.'%').' GROUP BY x ORDER BY clicked');
					$keys = array();
					foreach ($main as $r) {
						$keys[$r['x']] = $r['y'];
					}
					$_keys = array();
					foreach ($ex as $i => $e) {
						if (!$e) continue;
						$ret['y'][] = $e;
						$_main = DB::getAll('SELECT DATE_FORMAT(clicked,\''.$format.'\') AS x, '.$y.' AS y FROM '.$this->prefix.'visitor_clicks WHERE added>'.$ago.' AND visit_id>0 AND location LIKE '.e('%'.$e.'%').' GROUP BY x ORDER BY clicked');
						foreach ($_main as $r) {
							$_keys[$i][$r['x']] = $r['y'];
						}
					}
					$data = array();

					foreach ($keys as $x => $y) {
						foreach ($_keys as $i => $a) {
							if (!isset($_keys[$i][$x])) $_keys[$i][$x] = '';
							$data[$i][] = array(
								'x'	=> $x,
								'y' => $_keys[$i][$x]
							);
						}
					}
					
				}
				$this->array['click_types'] = array(
					'c_click'		=> lang('$Most popular'),
					'c_click_avg'	=> lang('$Most popular average'),
					'c_duration'	=> lang('$Most continuous'),
					'c_duration_avg'=> lang('$Most continuous average'),
					'c_cnt' 		=> lang('$Most visited')
				);
				$ret['filter'] = '<select onchange="S.A.L.chart_type(this.value)">'.Html::buildOptions($this->type,$this->array['click_types']).'</select> ';

				$ret['height'] = 440;
			break;
			case 'searches':
				$this->id = post('id');
				$ex = explode('|',$this->id);
				$keys = explode(',',$ex[0]);
				array_shift($ex);
				$vals = explode(' / ',join('|',$ex));
				$ret['title'] = lang('$Searches for: %1',html(join(' / ',$vals)));
				$sql = 'SELECT x, COUNT(1) AS y FROM (SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS cnt, GROUP_CONCAT(`value` ORDER BY key_index separator \' / \') AS v, GROUP_CONCAT(`key_index` ORDER BY key_index separator \',\') AS k FROM '.$this->prefix.'visitor_searches WHERE key_index IN ('.join(', ',$keys).') AND `value` IN ('.join(', ',e($vals)).') AND added>'.$ago.' GROUP BY visit_id, click_id ORDER BY v) AS t WHERE cnt='.count($keys).' GROUP BY x';
			break;
			case 'search':
				$this->id = post('id');
				$ex = explode('|',$this->id);
				$ret['title'] = lang('$Search by: %1 - %2',Conf()->g3('searches',$ex[0],2), $ex[1]);
				$sql = 'SELECT DATE_FORMAT(FROM_UNIXTIME(added),\''.$format.'\') AS x, COUNT(1) AS y FROM '.$this->prefix.'visitor_searches WHERE key_index='.(int)$ex[0].' AND `value`='.e($ex[1]).' GROUP BY x ORDER BY added';
			break;
			default:
				$ret['title'] = 'Undefined: '.$this->type.'';
			break;
		}
		
		$ret['filter'] .= '<select onchange="S.A.L.chart_date(this.value)">'.Html::buildOptions($date_type,$this->array['date_types']).'</select>';
		
		if (!$data && $sql) $data = DB::getAll($sql);

		if (!is_array($ret['y'])) {
			$func = '';
			$ret['single'] = true;
			$ret['xml'] = $this->genFusionXmlSingle($data, $ret);
		}
		else {
			$func = 'genFusionXml';
			$ret['single'] = false;
			$ret['xml'] = $this->genFusionXml($data, $ret['y'], $ret);
		}
		$ret['filter'] = '<div class="a-search">'.$ret['filter'].'</div>';
		$this->json_ret = $ret;
	}
}
