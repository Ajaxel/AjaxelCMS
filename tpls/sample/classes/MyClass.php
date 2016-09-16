<?php

define ('GOOGLE_MAPS_KEY','AIzaSyCKg9KQKi-GSO48xwUDEDG2PFAN4_4LkXc');


final class MyClass extends My {
	public function __construct(&$Index) {
		parent::__construct($Index);
		$this->version = 1;
	}
	
	public function init() {

	}
	
	public function head() {
		parent::head();
		if (!IS_ADMIN) $this->Index->addCSSA('ui/selene/'.JQUERY_CSS);
		asset('jquery.ennui.contentslider.css');
		asset('jquery.easing.1.3.js');
		asset('jquery.ennui.contentslider.js');
		Factory::call('uploadify',$this->Index)->head();
	}
	


	public function download() {
		if (URL::clean($_SERVER['HTTP_REFERER'])!=URL::clean('http://'.DOMAIN)) die('No access');
		$name = str_replace(array('/','\\'),'',get('download'));
		if (substr($name,0,9)=='template:') {
			$id = substr($name,9);
			$name = DB::one('SELECT url FROM '.$this->prefix.'grid_templates WHERE id='.(int)$id);
			if ($name) {
				if (!$_SESSION['downloads'] || !is_array($_SESSION['downloads'])) $_SESSION['downloads'] = array();
				if (!@$_SESSION['downloads'][$id]) DB::run('UPDATE '.$this->prefix.'grid_templates SET downloads=downloads+1 WHERE id='.(int)$id);
				$_SESSION['downloads'][$id] = 1;
				parent::download(FTP_DIR_TPL.'download/templates/'.$name.'/AjaxelCMS-'.$name.'_template.rar');
			}
		}
		elseif (is_dir(FTP_DIR_TPL.'download/'.$name.'/')) {
			$file = FTP_DIR_ROOT.'files/downloads/'.$name.'.txt';
			if (!is_file($file)) touch($file);
			$lines = file($file);
			file_put_contents($file, (intval(trim($lines[0])) + 1)."\n".date('H:i d-m-Y'));
			$dir = File::openDirectory(FTP_DIR_TPL.'download/'.$name.'/','/\.(rar|zip|exe)$/');
			if ($dir[0]) parent::download(FTP_DIR_TPL.'download/'.$name.'/'.$dir[0]);
		}
		elseif (is_file(FTP_DIR_TPL.'download/'.$name)) {
			parent::download(FTP_DIR_TPL.'download/'.$name);
		}
	}

	public function json() {
		switch (url(0)) {
			case 'slots':
				$this->slots();
			break;
			
			default:
				parent::json(true);
			break;
		}
		$this->end();
	}
	
	/**
	* Called right before index template file
	*/
	final public function index() {
		$this->arrMenu = Factory::call('menu')->getByPosition(array('top','bottom'));	
		$this->set('arrMenu', $this->arrMenu);
		$this->set('url0', url(0));
		$this->set('url1', url(1));
	}
	
	
	public function submit_comment($id, $table) {
		if (post('submitted')=='comment' && $this->UserID && $id) {
			$c = post('comment');
			if (post('anonymous')) $c['name'] = 'anonymous';
			if (!$c['name']) $c['name'] = Session()->Login;
			if (!$c['email']) $c['email'] = Session()->Email;
			
			$this->init_error($c);
			if (!is_numeric($id) || !$id) {
				$this->error('ID is lost, how did you do that?', 'fatal');
			}
			$this->err('Your comment is empty','comment');

			$spam = Factory::call('spam');
			$spam->comment = $c['comment'];
			$spam->email = $c['email'];
			$spam->name = $c['name'];			
			$spam->captcha = $c['captcha'];
			if ($r = $spam->is()) {
				$this->error(lang('Please do not spam!').' ('.$spam->text.')', 'comment',0,false);
			}

			if (!$this->errors()) {
				$c['title'] = trunc($c['title'],0,200);
				$data = array(
					'setid'		=> $id,
					'table'		=> $table,
					'subject'	=> html($c['title']),
					'name'		=> $c['name'],
					'email'		=> $c['email'],
					'original'	=> $c['comment'],
					'body'		=> Parser::parse('code_bb', $c['comment']),
					'added'		=> time(),
					'userid'	=> $this->UserID,
					'ip'		=> Session()->IPlong,
					'active'	=> 1,
					'rate'		=> $c['rate']
				);
				DB::insert('comments',$data);
				$sql = 'UPDATE '.$this->prefix.$table.' SET comments=comments+1 WHERE id='.$id;
				DB::run($sql);
				if (!DB::affected()) {
					$sql = 'UPDATE '.$this->prefix.$table.' SET comments=1 WHERE id='.$id;
					DB::run($sql);
				}
				$_POST['comment'] = array();
			} else {
				$this->ok('Thank you for your feedback!');	
			}
		}
		return $this->errors();
	}
	public function comments($id,$table = false) {
		if (!$id) return;
		$data = array();
		$data['list'] = array();
		if ($table!='entries') $table = 'content';
		$data['form_errors'] = $this->submit_comment($id, $table);
		Factory::call('spam')->getCode();
		$this->limit = 8;
		$this->offset = $this->page * $this->limit;
		
		$qry = DB::qry('SELECT SQL_CALC_FOUND_ROWS id, name, added, body, userid, rate FROM '.$this->prefix.'comments WHERE `table`=\''.$table.'\' AND setid='.$id.' AND active=\'1\' ORDER BY id DESC',$this->offset,$this->limit);
		$this->total = DB::rows();
		while ($row = DB::fetch($qry)) {
			$this->Index->Edit->set($row, 'comments', $row['id'], 'id')->admin();
			$row['added'] = Date::timezone($row['added']);
			$row['user'] = Data::user($row['userid'],'id,firstname,lastname,login,main_photo,facebook,online');
			$this->catchUser($row['user']);
			$row['user']['photo'] = str_replace('/th3/','/th4/',$row['user']['photo']);
			array_push($data['list'], $row);	
		}
		DB::free($qry);
		$data['pager'] = Pager::get(array(
			'total'	=> $this->total,
			'limit'	=> $this->limit,
			'page'	=> $this->page,
		));

		return $data;
	}
	public function catchUser(&$row) {
		if ($row['firstname'] && $row['lastname']) $row['name'] = $row['firstname'].' '.$row['lastname'];
		else $row['name'] = $row['login'];
	}
	
	public function data($type, $toOptions = -1, $optSettings = 'dropdown') {
		list ($type, $key, $keyAsVal, $lang) = Html::startArray($type);
		switch ($type) {
			case 'found':
				$keyAsVal = true;
				$lang = true;
				$ret = array(
					'Just surfed',
					'Received newsletter',
					'Friend suggests',
					'Google search',
					'hotscripts.com',
					'Forums',
					'Advertisement, banner',
					'Facebook or another social',
				);
			break;
			case 'programming':
				$lang = true;
				$ret = array(
					3=>'Design creation',
					1=>'HTML cut from Design',
					2=>'Programming extra features',
					5=>'Search engine optimization',
					6=>'Hosting',
					7=>'Domain registration',
				);
			break;
			case 'pages':
				$lang = true;
				$ret = array(
					5 => '< 5 pages',
					10 => '< 10 pages',
					20 => '< 20 pages',
					50 => '< 50 pages',
					51 => 'more than 50 pages'
				);
			break;
			case 'type':
				$keyAsVal = true;
				$ret = array(
					'Personal homepage'
					,'Company site'
					,'Portfolio'
					,'Newspaper'
					,'Blog'
					,'Social network'
					,'Shopping'
					,'Auction site'
					,'Real estate'
					,'Car selling'
					,'Entertainment'
					,'Classifieds'
					,'Intranet'
					,'Game'
					,'Other'
				);
			break;
			case 'plan':
				$keyAsVal = true;
				$ret = array(
					'I am lazy to read options below'
					,'I will learn and create website myself'
					,'I use designer to draw the template for my future website'
					,'I am not sure about design and layout, help needed'
					,'I have ideas, but no design was chosen'
					,'I have a design in Photoshop (PSD) format'
					,'I have a design in HTML, CSS, images'
					,'I have a design in other format'
					,'I have a template, but it\'s made for another CMS'
					,'I have a website and I want to change it\'s engine to Ajaxel'
					,'I want to test this program, I am developer'
					,'I want to look this program, I am designer'
					,'I want to feel this program, I am investor'
					,'I want to use this program for my personal needs'
					,'I am very interested and I want to help'
					,'Participating to become the best programmer :)'
				);
			break;
			case 'percent_agent':
				$keyAsVal = true;
				$ret = array(
					'', 10, 15
				);
			break;
			case 'trader_currency':
				$ret = array(
					'EUR'	=> 'EUR',
					'USD'	=> 'USD'
				);
			break;
		}
		return Html::endArray($ret, $keyAsVal, $lang, $key, $toOptions, $optSettings);		
	}


	public function uploadify() {
		Factory::call('uploadify',$this->Index)->name('grid_requests')->folder('grid_requests', false)->sizes(false)->ext(array('jpeg','jpg','gif','png','psd','rar','html','txt','doc','docx','zip'))->desc('Image or document files (10 max.)')->tpl('includes/pic.tpl')->limit(10)->end();
		
		return;
		Factory::call('uploadify',$this->Index)->name('profile')->id($this->UserID)->folder('users', true)->sizes(array(
			1	=> false
			,2	=> array(640, 0)
			,3	=> array(0, 175)
			,4	=> array(0, 117)
		))->th(2)->tpl('includes/pic.tpl')->ext(array('jpeg','jpg','gif','png'))->desc('Image files')->one('photo')->end();

	}
	
	public function uploaded($name, $f, $id) {
		if (!$id) return false;
		if ($name=='profile') {
			if ($id!=$this->UserID) {
				throw new Exception('ID is not userid');
			}
			$this->Index->Session->Photo = $f['file'];
			DB::run('UPDATE '.DB_PREFIX.'users SET main_photo='.escape($f['file']).' WHERE id='.$this->UserID);
		}
		elseif ($name=='grid_requests') {
			$_SESSION['grid_requests_photo'] = $f['file'];
		}
		
	}
	
	public function deleted($name, $file, $id, $next_file = false) {
		if ($name=='profile') {
			if ($id!=$this->UserID) {
				throw new Exception('ID is not userid');
			}
			$this->Index->Session->Photo = '';
			DB::run('UPDATE '.DB_PREFIX.'users SET main_photo=\'\' WHERE id='.$this->UserID);
		}
		elseif ($name=='grid_requests' && is_numeric($id) && $id!=$this->UserID) {
			if (!$next_file) {
				DB::run('UPDATE '.$this->prefix.'grid_requests SET main_photo=\'\' WHERE id='.$id);
			} else {
				DB::run('UPDATE '.$this->prefix.'grid_requests SET main_photo=\''.$next_file.'\' WHERE id='.$id);
			}
		}
	}

	public function upload() {
		parent::upload();	
	}
	
	protected function rated($data) {

		// data = id, [rate_old], rate, table, id
		if (!$data['id']) return;
		$rate = DB::one('SELECT SUM(rate)/COUNT(1) FROM '.DB_PREFIX.'votes WHERE setid='.$data['id'].' AND `table`='.escape($data['table']).'');
		DB::run('UPDATE '.$this->prefix.'grid_djs SET rate_logo='.(float)$rate.' WHERE id='.$data['id']);
	}

	public function catchArticlesGrid(&$row) {
			
	}
	
	public function catchTemplatesGrid(&$row) {
		

		if ($row['url']) {
			$file = FTP_DIR_TPL.'download/templates/'.$row['url'].'/AjaxelCMS-'.$row['url'].'_template.rar';
			if (is_file($file)) {
				$row['size'] = File::display_size(filesize($file));
				$row['updated'] = date('d.m.Y',filemtime($file));
			}
		}
	}
	
	
	private $slots_params = array(
		'week_start'=> 16,
		'year_start'=> 2015,
		'week_end'	=> 14,
		'year_end'	=> 2017,
		'credits'	=> 2500,
		'credits_final'=> 5000
	);
	
	public function userInserted($id, $data) {
		DB::run('UPDATE '.DB_PREFIX.'users_profile SET slots_credits=0, slots_spins=0, slots_today='.$this->slots_params['credits'].' WHERE setid='.$id);
		return false;
	}


	public function slots_top10($top10) {
		$h = '';
		foreach ($top10 as $d) {
			//if (!$d['credits']) continue;
			$h .= '<li>
	<div class="l l1">'.($d['country']?'<img src="/tpls/img/flags/24/'.$d['country'].'.png" width="24">':'').'</div>
	<div class="l l2">'.($d['main_photo']?'<img src="'.HTTP_DIR_FILES.'users/'.$d['id'].'/th3/'.$d['main_photo'].'">':'<img src="/tpls/gold2/images/nophoto.jpg">').'</div>
	<div class="l l3">'.$d['firstname'].'<br>'.number_format($d['credits'],0,'',',').' Gold coins</div>
</li>';		
		}
		return $h;
	}
	
	
	public function slotsCall($method, $ret) {
		if ($method=='game' || $method=='doubleup') {
			
			if ($_SESSION['tester']) {
				$_SESSION['tester']['slots_credits'] = $ret['credits'];
				$_SESSION['tester']['slots_today'] = $ret['nuggets'];
			}
			elseif ($this->UserID) {
				DB::run('UPDATE '.DB_PREFIX.'users_profile SET slots_credits='.escape($ret['credits']).', slots_today='.escape($ret['nuggets']).', slots_played='.escape(date('Y-m-d')).', slots_week='.date('W').', slots_spins=slots_spins+1 WHERE setid='.$this->UserID);
				
				$top10 = DB::getAll('SELECT setid, country, main_photo, login, firstname, slots_credits FROM '.DB_PREFIX.'users_profile up LEFT JOIN '.DB_PREFIX.'users u ON up.setid=u.id ORDER BY slots_credits DESC LIMIT 10');
				$top_score = DB::one('SELECT MAX(slots_credits) FROM '.DB_PREFIX.'users_profile');

				$c = '';
				if ($ret['win_icon']) {
					
					
					
				}
				$place = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users_profile WHERE slots_credits>='.(int)$ret['credits'].'');
				$ret['js'] = '$(\'#slots_claim\').'.($c ? 'show':'hide').'();';
				$ret['html'] = array(
					'slots_place'	=> $place,
					'slots_claim'	=> $c,
					'slots_top10'	=> $this->slots_top10($top10, $place),
					'slots_topscore' => number_format($top_score,0,'',','),
				);
			}
		}
		return $ret;
	}
	
	private function slots() {
		$params = $this->slots_params;
		
		$by_nuggets = true;
		
		$row = false;
		if ($this->UserID) {
			$row = DB::row('SELECT * FROM '.DB_PREFIX.'users_profile WHERE setid='.$this->UserID);
		}
		if (isset($_GET['reset'])) $_SESSION['tester'] = false;
		
		
		if (SITE_TYPE=='json' && $_SESSION['tester']) {
			$row = $_SESSION['tester'];
			$slots = new Slots($this, $row['slots_credits'], $row['slots_today'], $by_nuggets);
			$this->json_ret = $slots->callback(array($this,'slotsCall'))->json();
			return;
		}
		elseif (SITE_TYPE=='json' && $this->UserID) {
			$slots = new Slots($this, $row['slots_credits'], $row['slots_today'], $by_nuggets);
			$this->json_ret = $slots->callback(array($this,'slotsCall'))->json();
			return;
		}
		
		if ($r = url(0)) {
			$r = (url(1)?url(1):'tester');
			if (!is_file($f = FTP_DIR_TPL.'classes/slots_ids.txt')) touch($f);
			$lines = file($f);
			if (!$lines) {
				$lines = array(0=>'tester');
			}
			foreach ($lines as $l) {
				if ($r==trim($l) || $r=='tester') {
					$row = array(
						'firstname' => first($r),
						'slots_credits' => 0,
						'slots_today' => 2000
					);
					if (!$_SESSION['tester'] || ($_SESSION['tester']['firstname']!=first($r) || $_SESSION['tester']['slots_today']<=10)) {
						
						$_SESSION['tester'] = $row;
						$t = DB::row('SELECT cnt, ip FROM '.$this->prefix.'slotids_visits WHERE id='.e($r));
						if ($t['ip']!=Session()->IP) {
							DB::replace('slotids_visits',array(
								'id'	=> $r,
								'last_visit'  => time(),
								'ip'	=> Session()->IP,
								'cnt'	=> (int)$t['times'] + 1
							));
						}
					} else {
						$row = 	$_SESSION['tester'];
					}
					break;	
				}
			}
		}
		if (!$row) {
			return URL::redirect('?user&login&jump=slots');
		}

		if (0 && $this->UserID) {
			$nuggets = $row['slots_today'];
			$credits = (int)$row['slots_credits'];
		} else {
			$nuggets = $row['slots_today'];
			$credits = $row['slots_credits'];
			$place = rand(1,77);
		}
		
		$this->set('row',$row);

	
		$slots = new Slots($this,$credits,$nuggets,$by_nuggets);
		$this->set('game',$slots->game(true));
		
		$top_score = DB::one('SELECT MAX(slots_credits) FROM '.DB_PREFIX.'users_profile');
		$top10 = DB::getAll('SELECT setid, country, main_photo, firstname, login, slots_credits FROM '.DB_PREFIX.'users_profile up LEFT JOIN '.DB_PREFIX.'users u ON up.setid=u.id ORDER BY slots_credits DESC LIMIT 10');
		$place = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users_profile WHERE slots_credits>='.$credits.'');
		if (date('w')==0) {
			$left = Date::countdown(strtotime('next Monday')-7200,3,3);	
		} else {
			$left = Date::countdown(strtotime('next Sunday')+86399-7200,3,3);	
		}
		$left = Date::countdown(mktime(0,0,0,6,1,2015),3,3);
		
		$l =& $left;
		$this->set('clock', $l[0][0].' '.$l[0][1].'<br>'.$l[1][0].' '.$l[1][1].'<br>'.$l[2][0].' '.$l[2][1]);
		$this->set('left',$left);
		$this->set('top_score',$top_score);
		$this->set('nuggets',$nuggets);
		
		$this->set('credits',$credits);
		$this->set('place',$place);
		$this->set('slots_top10',$this->slots_top10($top10, $place));
		$this->set('top100',$top100);
		$this->display('pages/slots.tpl');
	}
	
		
	private function winners() {
		
		$top100 = DB::getAll('SELECT country, firstname, slots_credits, slots_today FROM '.DB_PREFIX.'users_profile ORDER BY slots_credits DESC, setid DESC LIMIT 0,66');
		
		$qry = DB::qry('SELECT t.credits, o.firstname, o.lastname, o.country, t.week, t.year, o.setid FROM '.DB_PREFIX.'users_top10 t LEFT JOIN '.DB_PREFIX.'users_profile o ON o.setid=t.setid WHERE `week`>='.$this->slots_params['week_start'].' AND `year`>='.$this->slots_params['year_start'].' GROUP BY t.setid, t.week, t.year ORDER BY `year`, `week`, credits DESC',0,0);
		$final = array();
		while ($rs = DB::fetch($qry)) {
			if (is_numeric($rs['country'])) {
				$rs['country'] = DB::one('SELECT code FROM geo_countries WHERE id='.$rs['country']);
			}
			$final[$rs['year'].$rs['week']][] = $rs;
		}
		$this->set('data',$final);
		$this->display('pages/winners.tpl');	
	}
	

	
	
	private function prepare() {
		$data = array();
		
		if (url(0)=='order') {
			if (isset($_GET['reset'])) $_SESSION['buy_id'] = 0;
			$id = $_SESSION['buy_id'];
			$row = array();
			if ($id) {
				$row = DB::row('SELECT * FROM '.$this->prefix.'grid_requests WHERE id='.$id);
				if ($row) {
					$row['programming'] = unserialize($row['programming']);
					$row['dated'] = Date::fromTimestamp($row['dated'],'.');	
				}
			}
			Factory::call('uploadify')->name('grid_requests')->id($id)->main_photo(@$row['main_photo'])->form('upload');
			if (post('submitted')=='buy') {
				$data = post('data');
				$main_photo = Factory::call('uploadify')->name('grid_requests')->getMainPhoto();
				$this->init_errors($data)
				->err('Please select a kind of website','type')
				->err('Brief project information is required','descr')
				->err('Please enter your website domain name','url')
				->err('Please speecify your email address','email');
				if (!$data['email'] && !$data['phone']) $this->error('Please speecify your email address','email');
				if ($data['email'] && !Parser::isEmail($data['email'])) $this->error('Incorrect email address, please verify','email');
				
				if (!$this->errors()) {
					$data['dated'] = Date::toTimestamp($data['dated']);
					$data['expires'] = Date::toTimestamp($data['expires'], true);
					array_unset($data, array('is_admin','shares','views','active','userid','area'));
					if ($id) {
						unset($data['added'], $data['id'], $data['userid']);
						DB::update('grid_requests',$data,$row['id']);
					} else {
						Email::send('info@ajaxel.com','New request from '.$data['email'],p($data,false));
						$data['title'] = $data['url'].'&lt;'.$data['email'].'&gt;';
						$data['programming'] = serialize($data['programming']);
						$data['main_photo'] = $main_photo;
						$data['added'] = time();
						$data['userid'] = $this->UserID;
						$data['active'] = 1;
						$data['dated'] = Date::toTimestamp($data['dated']);	
						DB::insert('grid_requests',$data);
						$id = DB::id();
					}
					Factory::call('uploadify')->name('grid_requests')->id($id)->move()->form('upload');
					
					if (!$_SESSION['buy_id']) {
						$this->ok('Thank you, your request has been sent, we will reply as soon we read it!');
					} else {
						$this->ok('Thank you, your request details were updated!');	
					}
					$_SESSION['buy_id'] = $id;
				}
				$_POST['data']['id'] = $id;
			} else {
				$_POST['data'] = $row;	
			}
			if (isset($_GET['reset'])) $_SESSION['recaptcha'] = false;
		}
	}
	
	
	public function page() {
		
		$this->prepare();

		switch (url(0)) {
			case 'slots':
				$this->slots();
			break;
			case 'user':
				if (!Factory::call('user')->getContent()) {
					$this->Index->mainarea = false;
				}
			break;
			
			case 'search':
				$this->Index->setVar('page_title',lang('Search results'));
				$this->Index->setVar('title',lang('Search results'));
				if (!Factory::call('content')->setLimit(10)->getSearch(get('search'))) {
					$this->Index->mainarea = false;
				}
			break;
			case 'sitemap':
				Factory::call('content')->getSitemap();
				$this->Index->tree[0] = array(
					'title'	=> lang('Sitemap'),
					'url'	=> '?sitemap'
				);
				$this->Index->setVar('title', lang('Sitemap'));
			break;
			case 'contact':
				$this->contact();
			break;
			case 'videos':
				$f = '';
				if (get('cid')) {
					$f .= ' AND catref LIKE '.escape(get('cid'));
				}
				$name = url(0);
				
				
				if (!Factory::call('grid', $this->Index)->setName($name)->setLimit(20)->setOrder(array(
					'articles'	=> 'added DESC'
				))->setSelect(array(
					'articles'	=> '*'
				))->setFilter(array(
					'articles'	=> $f
				))->setCatch(array(
					'articles'	=> array(
						$this, 'catchArticlesGrid'
					)
				))->getContent()) {
					$this->Index->mainarea = false;
				}
			break;
			case 'scripts':
				switch (url(1)) {
					case 'templates':
						$f = ' AND lang=\''.$this->lang.'\'';

						$name = url(1);
						
						if (!Factory::call('grid', $this->Index)->setName($name)->setLimit(20)->setOrder(array(
							'templates'	=> 'dated DESC'
						))->setSelect(array(
							'templates'	=> '*'
						))->setFilter(array(
							'templates'	=> $f
						))->setCatch(array(
							'templates'	=> array(
								$this, 'catchTemplatesGrid'
							)
						))->getContent()) {
							$this->Index->mainarea = false;
						}
					break;
					default:
						if (url(0) && url(1) && url(1)!='id' && Factory::call('content')->setLimit(20)->setOrder('dated DESC, sort, id DESC')->setFilter('')->getContent()) {
		
						}
						elseif (!$this->Index->staticPage(url(0),url(1)) && 
							(!$this->total && !Factory::call('content')->setLimit(20)->setOrder('dated DESC, sort, id DESC')->setFilter('')->getContent())) {
							$this->Index->mainarea = false;
						}
					break;
				}
			break;
			case URL_KEY_HOME:
				$this->Index->displayFile('main.tpl');				
			break;
			default:
				if (url(0) && url(1) && url(1)!='id' && Factory::call('content')->setLimit(20)->setOrder('dated DESC, sort, id DESC')->setFilter('')->getContent()) {

				}
				elseif (!$this->Index->staticPage(url(0),url(1)) && 
					(!$this->total && !Factory::call('content')->setLimit(20)->setOrder('dated DESC, sort, id DESC')->setFilter('')->getContent())) {
					$this->Index->mainarea = false;
				}
			break;
		}		
	}
		
}