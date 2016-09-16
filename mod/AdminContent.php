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
* @file       mod/AdminContent.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminContent extends Admin {
	
	public $Menu;
	protected $types = array();
	protected $content_modules = array();


	public function __construct() {
		parent::__construct(__CLASS__);
	}
	public function init() {
		$this->table = 'content';
		$this->Menu = Factory::call('menu', $this->classParams());		
		$this->content_modules = Site::getModules($this->table);
		if ($this->module && $this->module!=self::KEY_ALL) {
			$this->Index->setVar('title','Admin > '.$this->content_modules[$this->module]['title']);
			$this->title = 'Content manager: '.$this->content_modules[$this->module]['title'].'';
		} else {
			$this->Index->setVar('title','Admin > Contents');
			$this->title = 'Content manager';
		}
		$this->id = (int)$this->id;
		if (!$this->module && !$this->conid && !$this->id && !$this->menuid && !$this->find) $this->module = '';
		$this->fetch_with_unions = false;
		$this->sortby = array(
			'id'	=> 'ID',
			'sort'	=> 'Sort',
			'title_'.$this->lang => 'Title',
			'name'	=> 'URL name'
		);	
	}
	
	public function sql() {
		if ($this->menuid) {
			$this->filter .= ' AND (menuid='.$this->menuid.' OR menuids LIKE \'%,'.$this->menuid.',%\')';
		}
		if ($this->module && $this->module!=self::KEY_ALL) {
			$this->filter .= ' AND (SELECT 1 FROM '.$this->prefix.$this->table.'_'.$this->module.' WHERE setid='.$this->prefix.$this->table.'.id LIMIT 1)=1';	
		}
		$this->filter2 = ' AND lang=\''.$this->lang.'\'';
		if ($this->find) {
			$this->filter2 .= ' AND (title LIKE  \'%'.$this->find.'%\' OR descr LIKE  \'%'.$this->find.'%\')';
		}
		if ($this->sort && @array_key_exists($this->sort, $this->sortby)) {
			$this->order = $this->sort.', dated DESC, name';
		}
		else {
			$this->order = 'dated DESC, name';
			$this->sort = 'dated DESC, name';
		}
		
		$this->limit2 = 0;
		$this->offset = $this->page * $this->limit;
	}


	public function getModulesById($type, $cache = true, $tpl = false) {
		$ret = array();
		foreach (Site::getModules($type, $cache, $tpl) as $m => $a) {
			$ret[$a['id']] = $a + array('table' => $m);
		}
		return $ret;
	}
	
	public function listing() {
		$this->sql();
		$this->button['save'] = false;
		$this->button['add'] = true;
		$this->data = array();
		
		if ($this->find || $this->catref) {
			$qry = DB::qry('SELECT id FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' AND active!=2 ORDER BY '.$this->order, 0, 0);
			while ($rs = DB::fetch($qry)) {
				$unions = array();
				foreach ($this->content_modules as $m => $arr) {
					if ($this->module && $this->module!=self::KEY_ALL && $m!=$this->module) continue;
					$unions[] = '(SELECT COUNT(1) FROM '.$this->prefix.$this->table.'_'.$m.' WHERE setid='.$rs['id'].$this->filter2.(($has_catref[$m] && $this->catref)?' AND (catref=\''.$this->catref.'\' OR catref LIKE \''.$this->catref.'.%\')':'').' LIMIT 1)';
				}
				$sql = 'SELECT ('.join('+',$unions).') AS cnt';
				$total = DB::one($sql);
				if ($total) $this->total++;
			}
		} else {
			$this->total = DB::row('SELECT COUNT(1) AS cnt FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter,'cnt');
		}
		$qry = DB::qry('SELECT id, menuid, (CASE WHEN dated THEN DATE_FORMAT(FROM_UNIXTIME(dated),\'%d %b\') ELSE DATE_FORMAT(FROM_UNIXTIME(added),\'%d %b\') END) AS date, name, title_'.$this->lang.' AS title, cnt, active'.(!$this->fetch_with_unions?', inserts':'').' FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' AND active!=2 ORDER BY '.$this->order, $this->offset, $this->limit);
		$i = 0;
		$tables = DB::tables();
		$arr_select = $cols = $has_catref = array();
		foreach ($this->content_modules as $m => $arr) {
			$cols[$m] = DB::columns('content_'.$m);
			$arr_select[$m] = array('title', 'active');
			if (in_array('descr',$cols[$m])) {
				$arr_select[$m][] = DB::sqlGetString('length','descr',135);	
			}
			elseif (in_array('body',$cols[$m])) {
				$arr_select[$m][] = DB::sqlGetString('length','body',135);
			}
			if (in_array('sort',$cols[$m])) {
				$arr_select[$m][] = 'sort';
			}
			$extra_select = array('main_photo','price','currency');
			foreach ($extra_select as $s) {
				if (in_array($s, $cols[$m])) {
					$arr_select[$m][] = $s;
				} else {
					$arr_select[$m][] = '\'\' AS '.$s;	
				}
			}
			$has_catref[$m] = in_array('catref', $cols[$m]);
		}
		$modules = $this->getModulesById('content');
		while ($rs = DB::fetch($qry)) {
			
			$rs['url'] = $this->Menu->url($rs['menuid']).AMP.$rs['name'].($this->template!=$this->Index->Session->Template?AMP.URL_KEY_TEMPLATE.'='.$this->template:'');
			
			if ($rs['inserts']) $inserts = explode(',',$rs['inserts']); else $inserts = array();
			$total_inserts = count($inserts);
			$menu = Conf()->g2('Menu::getById',$rs['menuid'],array());
			$t = array();
			$t[] = ($menu['title'] ? $menu['title']:'<span style="color:red">'.lang('$menu is missing').'</span>');
			if (1 || $menu['name']!=$rs['name'] && $total_inserts>1) $t[] = $rs['title'];
			$rs['title'] = join(' :: ',$t);
			unset($rs['inserts']);
			
			$this->data[$i] = $rs;
			$j = 0;
			if ($this->fetch_with_unions):
				$unions = array();
				foreach ($this->content_modules as $m => $arr) {
					if ($this->module && $this->module!=self::KEY_ALL && $m!=$this->module) continue;
					if (!in_array('content_'.$m, $tables)) continue;
					$unions[] = '(SELECT '.e($m).' AS module, '.e($arr['icon']).' AS icon, rid, '.join(', ',$arr_select[$m]).' FROM '.$this->prefix.$this->table.'_'.$m.' WHERE setid='.$rs['id'].$this->filter2.(($has_catref[$m] && $this->catref)?' AND (catref=\''.$this->catref.'\' OR catref LIKE \''.$this->catref.'.%\')':'').' ORDER BY '.$this->order.($this->limit2?' LIMIT '.$this->offset2.','.$this->limit2:'').')';
				}
				if (!$unions) continue;
				$sql = ''.join(' UNION ',$unions).' ORDER BY sort';
				$_qry = DB::qry($sql,$this->offset,$this->limit);
				while ($_rs = DB::fetch($_qry)) {
					$_rs['url'] = URL::ht(URL::addExt($rs['url'].'&'.$rs['name'].'&'.$_rs['module'].'ID='.$_rs['rid']));
					$this->catchRow($_rs, $cols);
					unset($_rs['sort']);
					$this->data[$i]['sub'][$j] = $_rs;
					$j++;
				}
			else:
				$size = 16;
				foreach  ($inserts as $insert) {
					list ($modid, $id) = explode(':', $insert);
					$m = $modules[$modid]['table'];
					$icon = $modules[$modid]['icon'];
					if (!$icon) $icon = FTP_EXT.'tpls/img/oxygen/'.$size.'x'.$size.'/apps/kcmkwm.png';
					elseif (!strstr($icon,'://') && is_file(FTP_DIR_TPLS.'img/oxygen/16x16/'.$icon.'.png')) {
						$icon = FTP_EXT.'tpls/img/oxygen/'.$size.'x'.$size.'/'.$icon.'.png';
					}
					if ($this->module && $this->module!=self::KEY_ALL && $m!=$this->module) continue;
					if (!$arr_select[$m]) continue;
					$_rs = DB::row('SELECT '.e($m).' AS module, '.e($icon).' AS icon, rid, '.join(', ',$arr_select[$m]).' FROM '.$this->prefix.'content_'.$m.' WHERE rid='.(int)$id.$this->filter2.(($has_catref[$m] && $this->catref)?' AND (catref=\''.$this->catref.'\' OR catref LIKE \''.$this->catref.'.%\')':''));
					if (!$_rs) continue;
					$_rs['url'] = URL::ht(URL::addExt($rs['url'].AMP.$m.'ID='.$_rs['rid']));
					$this->catchRow($_rs, $cols);
					$this->data[$i]['sub'][$j] = $_rs;
					$j++;
				}
			endif;
			if ((($this->module && $this->module!=self::KEY_ALL) || $this->find) && !$this->data[$i]['sub'][0]) unset($this->data[$i]);
			else $i++;
		}
		DB::free($qry);
		$this->json_data = json($this->data);
		$this->nav();
		if ($this->module && $this->module!=self::KEY_ALL) {
			$params = array(
				'table'		=> 'category_'.$this->module,
				'lang'		=> $this->lang,
				'catalogue'	=> false,
				'retCount'	=> true,
				'getAfter'	=> false,
				'noDisable'	=> true,
				'maxLevel'	=> 0,
				'optValue'	=> 'catid',
				'getHidden'	=> true,
				'prefix'	=> $this->prefix,
				'selected'	=> $this->catid
			);
			$this->category_options = Factory::call('category', $params)->getAll()->toOptions();
		}
	}
	
	
	private function catchRow(&$_rs, $cols) {
		if (in_array('main_photo', $cols[$_rs['module']])) {
			if (!$_rs['main_photo']) {
				$_rs['main_photo'] = DB::row('SELECT main_photo FROM '.$this->prefix.'content_'.$_rs['module'].' WHERE rid='.$_rs['rid'].' AND main_photo!=\'\'','main_photo');
			}
			if ($_rs['main_photo']) {
				$photo = $this->ftp_dir_files.$this->table.'_'.$_rs['module'].'/'.$_rs['rid'].'/th1/'.$_rs['main_photo'];
				if (is_file($photo)) {
					$_rs['main_photo_size'] = @getimagesize($photo);
					if (!$_rs['main_photo_size'][0]) $_rs['main_photo'] = '';
				} else {
					$_rs['main_photo'] = '';
				}
			}
		}
		unset($_rs['sort']);
		if (!$_rs['main_photo']) {
			unset($_rs['main_photo']);
		}
		if (!$_rs['currency']) {
			unset($_rs['currency']);
		}
		if (!$_rs['price']) {
			unset($_rs['price']);
		}
		if (isset($_rs['body'])) $_rs['descr'] = $_rs['body'];
		$_rs['descr'] = Parser::strip_tags($_rs['descr']);
		
		if (isset($_rs['price']) && $_rs['price']) {
			$_rs['descr'] = '<div class="a-l">'.$_rs['descr'].'</div><div class="a-r a-price" style="text-align:right;white-space:nowrap">'.$_rs['price'].' '.$_rs['currency'].'</div>';	
		}
	}
	
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'content_blocks':
				if (!post('menuid')) {
					$this->json_ret = NULL;
					break;
				}
				$this->json_ret = DB::getAll('SELECT id, title_'.$this->lang.' AS label FROM '.$this->prefix.'content WHERE menuid='.(int)post('menuid').' ORDER BY dated DESC, title_'.$this->lang.'');
				if (!$this->json_ret) $this->json_ret = NULL;
				else $this->json_ret = array_merge(array(0=>array('id'=>0,'label'=>lang('$-- please select --'))),$this->json_ret);
			break;
			default:
				$this->json_ret = array('0'=>'Cannot use: '.post('get'));
			break;
		}
	}
	protected function validate() {
		$err = array();
		if (!$this->data['title']) {
			$this->data['title'] = date('Hi_dmY');
		//	$err['title'] = lang('$Title must be filled in and so as URL name');
		}
		if (!$this->data['menuid']) $err['menuid'] = lang('$This content must be assigned to menu');
		if (isset($this->data['name'])) {
			if (!strlen(trim($this->data['name']))) $this->data['name'] = $this->data['title'];
			$this->data['name'] = Parser::name($this->data['name']);
		}
		if ($this->data['name'] && $this->isReserved($this->data['name'])) {
			$err['name'] = lang('$This name %1 is actually reserved by system, please use another',$this->data['name']);
		}
		$id = false;
		if ($this->id && $this->data['menuid']==$this->rs['menuid']) {
			$id = $this->id;	
		}
		if (DB::one('SELECT 1 FROM '.$this->prefix.$this->table.' WHERE '.($id?'id!='.$id.' AND ':'').'name LIKE '.e($this->data['name']).' AND menuid='.(int)$this->data['menuid'])) {
			$err['name'] = lang('$Such URL name %1 already exists in the same menu',$this->data['name']);
		}
		$this->set_msg(lang('$New content block %1 was added',$this->data['title']), lang('$Content block %1 was updated',@$this->rs['title_'.$this->lang]));
		$this->errors($err);
	}
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'move':
				$this->allow('content',(post('setid') ? 'edit' : 'save'),$this->rs,$this->data);
				$setid = intval(post('setid'));
				if ($setid) {
					$old_setid = DB::row('SELECT setid FROM '.$this->prefix.$this->table.'_'.$this->module.' WHERE rid='.$this->id,'setid');
					$sql = 'UPDATE '.$this->prefix.$this->table.'_'.$this->module.' SET setid='.$setid.' WHERE rid='.$this->id.'';
					DB::run($sql);
					$old_inserts = DB::row('SELECT inserts FROM '.$this->prefix.$this->table.' WHERE id='.$old_setid,'inserts');
					$new_old_inserts = array();
					if ($old_inserts) {
						$ex = explode(',',$old_inserts);
						foreach ($ex as $e) {
							$_e = explode(':',$e);
							if ($_e[1]==$this->id) continue;
							$new_old_inserts[] = $e;
						}
						$sql = 'UPDATE '.$this->prefix.$this->table.' SET inserts='.e(join(',',$new_old_inserts)).' WHERE id='.$old_setid;
						DB::run($sql);
					}
					$inserts = DB::row('SELECT inserts FROM '.$this->prefix.$this->table.' WHERE id='.$setid,'inserts');
					$new_inserts = array();
					if ($inserts) {
						$ex = explode(',',$inserts);
						foreach ($ex as $e) {
							$_e = explode(':',$e);
							if ($_e[1]==$this->id) continue;
							$new_inserts[] = $e;
						}
					}
					$new_inserts[] = $this->content_modules[$this->module]['id'].':'.$this->id;
					$sql = 'UPDATE '.$this->prefix.$this->table.' SET inserts='.e(join(',',$new_inserts)).' WHERE id='.$setid;
					DB::run($sql);
				} else {
					$old_setid = DB::row('SELECT setid FROM '.$this->prefix.$this->table.'_'.$this->module.' WHERE rid='.$this->id,'setid');
					$old_inserts = DB::row('SELECT inserts FROM '.$this->prefix.$this->table.' WHERE id='.$old_setid,'inserts');
					$new_old_inserts = array();
					if ($old_inserts) {
						$ex = explode(',',$old_inserts);
						foreach ($ex as $e) {
							$_e = explode(':',$e);
							if ($_e[1]==$this->id) continue;
							$new_old_inserts[] = $e;
						}
						if ($new_old_inserts) {
							$sql = 'UPDATE '.$this->prefix.$this->table.' SET inserts='.e(join(',',$new_old_inserts)).' WHERE id='.$old_setid;
						} else {
							$sql = 'DELETE FROM '.$this->prefix.$this->table.' WHERE id='.$old_setid;
						}
						DB::run($sql);
					}
					$this->data['inserts'] = $this->content_modules[$this->module]['id'].':'.$this->id;
					$this->module = self::KEY_ALL;
					$this->id = 0;
					$this->action('save');
					$this->affected = 1;
					$this->updated = Site::ACTION_INSERT;
					$this->msg_reload = true;
				}
			break;
			case 'save':
				if ($this->id!==self::KEY_NEW && $this->id) {
					$this->id = (int)$this->id;
					$this->rs = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
					if ($this->rs['menuid']!=$this->data['menuid']) DB::run('UPDATE '.$this->prefix.'menu SET cnt=ABS(cnt-1) WHERE id='.(int)$this->rs['menuid']);
				} else {
					$this->rs = array();
					$this->id = 0;
				}
				$this->data['comment'] = (isset($this->data['comment']) ? $this->data['comment'] : 'N');
				if ($this->data['dated']) $this->data['dated'] = $this->toTimestamp($this->data['dated']);
				$this->allow('content',($this->id?'edit':'save'),$this->rs,$this->data);
				$this->validate();
				if (isset($this->data['menuids'])) $this->data['menuids'] = ','.join(',',$this->data['menuids']).',';
				if ($this->id) {
					$this->data['title_'.$this->lang] = $this->data['title'];
					DB::update($this->table,$this->data,$this->id);
					$this->updated = Site::ACTION_UPDATE;
					$this->affected = DB::affected();
				} else {
					foreach ($this->langs as $l => $a) {
						$this->data['title_'.$l] = $this->data['title'];
					}
					unset($this->data['title']);
					$this->data['added'] = $this->time;
					$this->data['active'] = 1;
					DB::insert($this->table,$this->data);
					$this->updated = Site::ACTION_INSERT;
					$this->affected = true;
					$this->id = DB::id();
				}
				if ($this->updated==Site::ACTION_UPDATE && $this->rs['menuid']!=$this->data['menuid']) DB::run('UPDATE '.$this->prefix.'menu SET cnt=ABS(cnt+1) WHERE id='.(int)$this->data['menuid']);
				$this->global_action('msg');
				$new = DB::select($this->table, $this->id);
				$this->log($this->id, $new, $this->rs);
				if ($this->module && $this->module!=self::KEY_ALL && $this->affected && $this->updated==1) {
					$this->msg_js = 'S.A.C.open('.$this->id.', \''.$this->module.'\');';
					$this->msg_reload = false;
				}
			break;
			case 'add':
			//	if (!$this->id) break;
				$this->action('save');
				if ($this->updated) {
					$this->msg_js = 'S.A.W.open(\'?'.URL_KEY_ADMIN.'=menu&add=new\');';
				}
			break;
			case 'copy':
				if (!$this->id) break;
				return $this->action('save');
			break;
			case 'act':
				$this->allow('content','activate');
				$this->global_action('act');
			break;
			case 'sort':
				$this->allow('content','sort');
				$this->global_action('sort');
			break;
			case 'delete':
				if (!$this->id) break;
				$this->allow('content','delete');			
				$this->rs = array();
				$this->rs['top'] = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				DB::run('DELETE FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				$this->affected = DB::affected();
				DB::run('DELETE FROM '.$this->prefix.'views WHERE setid='.$this->id);
				$files = $pages = 0;
				if (!$this->affected) {
					$this->msg_text = lang('$Cannot delete entry with ID: %1, it does not exist in database',$this->id);
					$this->msg_delay = 1500;
					$this->msg_type = 'warning';
					break;	
				}
				foreach ($this->content_modules as $m => $arr) {
					$this->name = $this->table.'_'.$m;
					$ids = DB::getAll('SELECT id FROM '.$this->prefix.$this->name.' WHERE setid='.$this->id,'id');
					foreach ($ids as $id) {
						$this->rs['sub'][$this->prefix.$this->name.':'.$id] = DB::row('SELECT * FROM '.$this->prefix.$this->name.' WHERE id='.$id);
					}
					DB::run('DELETE FROM '.$this->prefix.$this->name.' WHERE setid='.$this->id);
					$aff = DB::affected();
					if ($aff) {
						$pages += floor($aff / $this->lang_cnt);	
					}
				}
				if ($this->rs['top']['menuid'] && $this->rs['top']['cnt']>0) {
					DB::run('UPDATE '.$this->prefix.'menu SET cnt=ABS(cnt-1), cnt2=ABS(cnt2-'.$pages.') WHERE id='.$this->rs['top']['menuid']);
				}
				$this->updated = Site::ACTION_DELETE;
				//$this->msg_text = lang('$Content: "%1" with %2 pages were deleted',$this->rs['top']['name'], $pages);
				$this->msg_delay = 1500;
				$this->log($this->id, false, $this->rs);
			break;
			default:
				$this->msg_reload = false;
				$this->msg_text = 'Action: \''.$this->action.'\' is not defined';
				$this->msg_delay = 3000;
				$this->msg_type = 'fatal';
			break;
		}
	}
	public function window() {
		if ($this->conid && $this->conid!==self::KEY_NEW) {
			$this->post = DB::row('SELECT *, title_'.$this->lang.' AS title FROM '.$this->prefix.$this->table.' WHERE id='.$this->conid);
		}
		elseif ($this->id && $this->id!=self::KEY_NEW) {
			$this->post = DB::row('SELECT *, title_'.$this->lang.' AS title FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
		}
		if (!$this->post || !$this->post['id']) {
			$this->conid = 0;
			$this->post = post('data');
			if ($this->menuid && !isset($this->post['menuid'])) $this->post['menuid'] = $this->menuid;
			$this->post['sort'] = DB::row('SELECT (MAX(sort)+1) AS s FROM '.$this->prefix.$this->table.' WHERE menuid='.(int)$this->menuid,'s');
		} else {
			$this->isEdit();
			if (isset($this->post['dated']) && $this->post['dated']) $this->post['dated'] = $this->fromTimestamp($this->post['dated']);
			else $this->post['dated'] = '';
			if (!$this->post['name']) $this->post['name'] = Parser::name($this->post['title']);
			if (isset($this->post['userid'])) $this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post('username', false)) $this->post['username'] = 'unknown user';
		}
		
		if (get('move')) {
			$this->win('content_move');
		} else {
			$this->win('content');
		}
	}
}