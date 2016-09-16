<?php

final class MyCrm_users extends MyCrm {
	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->conf['added'] = $this->conf['time_added'].' d M Y';
		$this->conf['datepickers'] = array(
			'dated'		=> $this->conf['time_added'].' d M Y'
		);
		$this->conf['lock'] = false;
		$this->conf['delete'] = true;
		$this->conf['window_title'] = array('Add new user','Edit user (ID: %1)','Preview user (ID: %1)');
		parent::run();
		
		$this->conf['lock_filter'] = ' AND userid='.$this->UserID.' AND (setid!='.$this->UserID.' OR setid IS NULL)';
	}

	protected function filter() {
		$f = post('f');

		if (IS_POST) {
			$arr = array('added');
			foreach ($arr as $k) {
				if (isset($f[$k.'_from'])) $f[$k.'_from'] = $this->toTimestamp($f[$k.'_from']);
				if (isset($f[$k.'_to'])) $f[$k.'_to'] = $this->toTimestamp($f[$k.'_to']);
			}
			$this->filter = DB::search($f, array(
				'id_from_to'=> 'id',
				'is_int' 	=> array('userid','roleid'),
				'like'		=> array('login'=>array('u.login','u.email')),
				'from_to'	=> array('added','dated')
			),'a');
		}
	}
	
	protected function order() {
		$this->order = post('order','','id');
		$this->by = post('by','','DESC');
		if (!in_array($this->order,$this->columns)) $this->order = 'id';
		if (!in_array($this->by,array('ASC','DESC'))) $this->by = 'ASC';
	}
	
	protected function find() {
		$pager = $data = $lock = array();
		$lock_id = 0;

		$this->filter();	
		$this->order();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, a.userid, a.added, a.dated, a.roleid, a.price, u.login, u.email, u.groupid FROM '.$this->sub_prefix.$this->name.' a LEFT JOIN '.DB_PREFIX.'users u ON u.id=a.setid WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' '.$this->by;
		$qry = DB::qry($sql,$this->offset, $this->limit);
		$this->total = DB::rows();
		while ($row = DB::fetch($qry)) {
			$row['role'] = $this->conf['roles'][$row['roleid']];
			$row['group'] = Conf()->g2('user_groups',$row['groupid']);
			$this->catchRow($row);
			array_push($data, $row);	
		}

		DB::free($qry);

		$ret = array(
			'ok'	=> true,
			'pager'	=> Pager::get(array(
				'total'	=> $this->total,
				'limit'	=> $this->limit,
				'page_key'=>URL_KEY_PAGE
			)),
			'order'	=> $this->order,
			'by'	=> $this->by,
			'list'	=> $data
		);
		return $ret;
	}
	
	
	protected function window_row() {
		if ($this->id && $this->row['setid']) {
			$u = DB::row('SELECT login, email FROM '.DB_PREFIX.'users WHERE id='.$this->row['setid']);	
			$this->row = array_merge($this->row,$u);
		}
	}

	protected function save() {
		$this->id = (int)post('id');
		$this->data = post('data');
		
		if (!$this->id || $this->UserID==$this->user['userid']) {
			$this->err('Role must be chosen','roleid');
		} else {
			unset($this->data['role']);	
		}
		if (!$this->id) {
			$this->err('Username field shall not be empty','login');
			if ($this->data['login']) {
				if (!is_numeric($this->data['login'])) {
					$userid = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE (login LIKE '.escape($this->data['login']).' OR email LIKE '.escape($this->data['login']).') AND status!=2');
					if (!$userid) {
						$this->error('No users found in global users database with such username, please check your users in admin panel','login');
					} else {
						$this->data['setid'] = $userid;
					}
				} else {
					$this->data['setid'] = $this->data['login'];	
				}
			}
		} else {
			unset($this->data['setid']);	
		}
		return $this->save_end();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}