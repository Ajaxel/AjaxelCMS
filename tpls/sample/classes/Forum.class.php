<?php

class Forum_class extends Object {
	
	public function __construct(&$Index) {
		parent::load($Index);
		

	//	if (!IS_ADMIN) return URL::redirect();
		if (SITE_TYPE=='json') $this->json();
		else $this->run();
	}
	
	public function json() {
		switch (url(1)) {
			case 'comments':
				$this->comments(get('setid'));
				$this->My->set('setid',get('setid'));
				$this->My->json_ret = array(
					'html'	=> array(
						'comments'	=> $this->My->Index->Smarty->fetch('pages/inc/comments.tpl')
					)	
				);
			break;	
		}
		$this->My->end();
	}
	

	
	private function run() {
		$data = array('list'=>array());
		$parent = array();
		$thread = array();
		$qry = false;
		$reply = isset($_GET['reply']);
		$this->My->set('reply',$reply);
		$this->My->limit = 40;
		$this->My->offset = $this->My->limit * $this->My->page;
		
		/*
		$fn = file(FTP_DIR_TPL.'classes/firstnames.txt');
		$ln = file(FTP_DIR_TPL.'classes/surnames.txt');
		if (get('aaa')) {
			set_time_limit(0);
			$qry = DB::qry('SELECT DISTINCT(user) FROM '.$this->prefix.'forum_posts WHERE user!=\'\'',0,0);
			while ($rs = DB::fetch($qry)) {
				shuffle($fn);
				shuffle($ln);
				$name = ucfirst(strtolower($fn[0])).' '.ucfirst(strtolower($ln[0]));
				DB::run('UPDATE '.$this->prefix.'forum_posts SET user2='.escape($name).' WHERE user='.escape($rs['user']));
			}
			die('done');
		}
		*/
		

		if (get('thread') && ($thread = DB::row('SELECT * FROM '.$this->prefix.'forum_threads WHERE id='.(int)get('thread')))) {
			if (isset($_GET['delete']) && IS_ADMIN) {
				DB::run('DELETE FROM '.$this->prefix.'forum_threads WHERE id='.$thread['id']);
				if (DB::affected()) {
					DB::run('DELETE FROM '.$this->prefix.'forum_posts WHERE setid='.$thread['id']);
					if ($aff = DB::affected()) {
						DB::run('UPDATE '.$this->prefix.'forum_categories SET posts=posts-'.$aff.' WHERE id='.$thread['catid']);
					}
					DB::run('UPDATE '.$this->prefix.'forum_categories SET threads=threads-1 WHERE id='.$thread['catid']);
					URL::redirect('?forum&category='.$thread['catid']);
				}
			}
			elseif ($_GET['del'] && IS_ADMIN) {
				DB::run('DELETE FROM '.$this->prefix.'forum_posts WHERE id='.(int)$_GET['del'].' AND setid='.$thread['id']);
				if (DB::affected()) {
					DB::run('UPDATE '.$this->prefix.'forum_categories SET posts=posts-1 WHERE id='.$thread['catid']);
					DB::run('UPDATE '.$this->prefix.'forum_threads SET posts=posts-1 WHERE id='.$thread['id']);
					URL::redirect('?forum&category='.$thread['catid'].'&thread='.$thread['id']);
				}
			}
			$this->catchForum('thread',$thread);
			$this->My->Index->setVar('title','Forum: '.$thread['title']);
			$cat = DB::row('SELECT * FROM '.$this->prefix.'forum_categories WHERE id='.(int)$thread['catid']);
			$data['type'] = 'posts';
			if (!$reply) {
				$qry = DB::qry('SELECT SQL_CALC_FOUND_ROWS * FROM '.$this->prefix.'forum_posts WHERE setid='.$thread['id'].' ORDER BY id DESC',$this->My->offset,$this->My->limit);
				$this->My->total = DB::rows();
			} else {
				if ($this->isValid(post('message')) && post('message')!=lang('_Message') && $this->My->UserID) {
					
					$data = array(
						'catid'	=> $cat['id'],
						'setid'	=> $thread['id'],
						'descr'	=> Parser::parse('code_bb',post('message')),
						'userid'=> $this->My->UserID,
						'added'	=> time()
					);
					DB::insert('forum_posts',$data);
					DB::run('UPDATE '.$this->prefix.'forum_threads SET posts=posts+1 WHERE id='.$thread['id']);
					DB::run('UPDATE '.$this->prefix.'forum_categories SET posts=posts+1 WHERE id='.$cat['id']);
					URL::redirect('?forum&category='.$cat['id'].'&thread='.$thread['id']);
				}
			}
		}
		elseif (get('category') && ($cat = DB::row('SELECT * FROM '.$this->prefix.'forum_categories WHERE id='.(int)get('category')))) {
			$this->My->Index->setVar('title','Forum: '.$cat['title']);
			$data['type'] = 'threads';
			if (!$reply) {
				$qry = DB::qry('SELECT SQL_CALC_FOUND_ROWS id, title, descr, views, posts, added, userid FROM '.$this->prefix.'forum_threads WHERE catid='.$cat['id'].' ORDER BY id DESC',$this->My->offset,$this->My->limit);
				$this->My->total = DB::rows();
			} else {
				if ($this->isValid(post('message')) && post('message')!=lang('_Thread message') && $this->My->UserID) {
					if (post('title')==lang('_Thread title')) {
						$_POST['title'] = strip_tags(trunc(post('message'),100,1,1));	
					}
					$data = array(
						'catid'	=> $cat['id'],
						'title'	=> html(post('title')),
						'descr'	=> Parser::parse('code_bb',post('message')),
						'userid'=> $this->My->UserID,
						'added'	=> time()
					);
					DB::insert('forum_threads',$data);
					$id = DB::id();
					DB::run('UPDATE '.$this->prefix.'forum_categories SET threads=threads+1 WHERE id='.$cat['id']);
					URL::redirect('?forum&category='.$cat['id'].'&thread='.$id);
				}	
			}
		} else {
			$data['type'] = 'categories';
			$qry = DB::qry('SELECT * FROM '.$this->prefix.'forum_categories',0,0);
		}
		if ($qry) {
			while ($row = DB::fetch($qry)) {
				$this->catchForum($data['type'],$row);
				array_push($data['list'],$row);
			}
	
			DB::free($qry);
			
			if ($this->My->total) {
				$data['pager'] = Pager::get(array(
					'total'	=> $this->My->total,
					'limit'	=> $this->My->limit,
					'page'	=> $this->My->page
				));	
				$data['pager']['options'] = Pager::page_options();
			}
		}
		$this->catchForum('thread',$thread);
		$this->My->set('cat',$cat);
		$this->My->set('thread',$thread);
		$this->My->set('data',$data);
	}
	
	
	private function isValid($s) {
		if (!$s || is_array($s)) {
			return false;
		}
		if (!IS_USER && preg_match('/http:\/\//i',$s)) {
			$this->My->error('No external links allowed, sorry.');
			$this->My->errors();
			return false;
		}
		return true;
	}
	
	private function catchForum($type, &$row) {
		/*
		if ($row['descr']) {
			$row['descr'] = preg_replace('/<a href="showthread(.+)<\/a>/U','',$row['descr']);	
		}
		if ($row['user']) {
			$from = array(
				'DJTT',
				'techtool',
				'forum',
			);
			$to = array(
				'TOGDJR',
				'ranking',
				'community',
			);
			$row['descr'] = str_replace($from,$to,$row['descr']);
			$row['title'] = str_replace($from,$to,$row['title']);
			$row['descr'] = preg_replace('/(^|>)([^<]+)(<|$)/Ue','$this->My->words2(\'$2\',\'$1\',\'$3\')',$row['descr']);
			$row['user'] = $row['user2'];
		}
		*/
		if ($type=='thread') $row['added'] = Date::timezone($row['added']);
		else $row['added'] = Date::timezone($row['added']);
		if ($row['userid'] && !$row['user']) {
			$u = Data::user($row['userid'],'firstname, lastname');
			$row['user'] = $u['firstname'].' '.$u['lastname'];
		}
	}
	
}