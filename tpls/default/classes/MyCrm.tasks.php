<?php

final class MyCrm_tasks extends MyCrm {
	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->conf['datepickers'] = array(
			'dated'		=> $this->conf['time_added'].' d M Y',
			'deadline'	=> $this->conf['time_added'].' d M Y'
		);
		$this->conf['added'] = $this->conf['time_added'].' d M';
		$this->conf['access'] = self::ACCESS_PUBLIC;
		$this->conf['window_title'] = array('Add new task','Edit task (ID: %1)','Preview task (ID: %1)');
		$this->conf['lock'] = true;

		$this->conf['status_classes'] = array(
			self::STATUS_HIDDEN		=> 'hidden',
			self::STATUS_DEFAULT	=> '',
			self::STATUS_IMPORTANT	=> 'important',
			self::STATUS_DELETED	=> 'deleted',
			4	=> 'progress',
			5	=> 'completed',
			6	=> 'testing',
			7	=> 'cancelled',
			8	=> 'unclear',
			9	=> 'redo',
		);
		parent::run();
	}

	protected function filter() {
		$f = post('f');

		if (IS_POST) {
			$arr = array('added','dated','deadline');
			foreach ($arr as $k) {
				if (isset($f[$k.'_from'])) $f[$k.'_from'] = $this->toTimestamp($f[$k.'_from']);
				if (isset($f[$k.'_to'])) $f[$k.'_to'] = $this->toTimestamp($f[$k.'_to']);
			}
			$this->filter = DB::search($f, array(
				'id_from_to'=> 'id',
				'is_int' 	=> array('userid','status','access','assign'),
				'like'		=> array('title'=>array('title','descr'),'project'),
				'from_to'	=> array('added','dated','deadline')
			),'a');
		}
		if (!isset($f['status']) || !strlen(trim($f['status']))) {
			$this->filter .= ' AND a.status!='.self::STATUS_DELETED;
		}
		$this->filter .= ' AND (a.access!='.self::ACCESS_PRIVATE.' OR a.userid='.$this->UserID.' OR a.assign='.$this->UserID.')';
	}
	
	protected function order() {
		$this->order = post('order','','id');
		switch ($this->order) {
			case 'sum':
				$this->order = 'price';
			break;
		}
		$this->by = post('by','','DESC');
		if (!in_array($this->order,$this->columns)) $this->order = 'id';
		if (!in_array($this->by,array('ASC','DESC'))) $this->by = 'ASC';
		switch ($this->order) {
			case 'added':
				$this->order = 'notify DESC, added';
			break;
		}
	}
	
	protected function find() {
		$pager = $data = $lock = array();
		$lock_id = 0;

		$this->filter();	
		$this->order();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, a.project, a.title, a.descr, a.status, (SELECT setid FROM '.$this->sub_prefix.'users WHERE id=a.assign) AS assign, a.added, a.dated, a.deadline, a.price, (CASE WHEN (a.locker>0 AND a.locker!='.$this->UserID.' AND a.locked > '.($this->time - self::LOCKING_SECONDS).' AND (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=a.locker)=1) THEN (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=a.locker) ELSE \'\' END) AS locker_login FROM '.$this->sub_prefix.$this->name.' a WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' '.$this->by;
		$qry = DB::qry($sql,$this->offset, $this->limit);
		$this->total = DB::rows();
		while ($row = DB::fetch($qry)) {
			$this->catchRow($row);
			if ($row['assign']) {
				$r = DB::row('SELECT login, email FROM '.DB_PREFIX.'users WHERE id='.$row['assign']);	
				$row['assign'] = $r['login'];
				$row['email'] = $r['email'];
			} else {
				$row['assign'] = '';	
			}
			$row['descr'] = Parser::strPrint(substr($row['descr'],0,300));
			array_push($data, $row);	
		}
		DB::free($qry);
		if ($this->UserID) {
			$qry = DB::qry('SELECT id, locked FROM '.$this->sub_prefix.$this->name.' WHERE locker='.$this->UserID.' AND access NOT IN ('.self::ACCESS_LOCKED.', '.self::ACCESS_LOCKED_READ.') ORDER BY locked DESC',0,0);
			$lock = false;
			$my_locks = array();
			while ($rs = DB::fetch($qry)) {
				if (!$lock) {
					$lock = $rs;
				} else {
					array_push($my_locks, $rs['id']);
				}
			}
			DB::free($qry);
			if ($lock) {
				$lock['locktime'] = Date::secondsToTime(self::LOCKING_SECONDS - ($this->time - $lock['locked']));
			}
			if ($my_locks) DB::run('UPDATE '.$this->sub_prefix.$this->name.' SET locker=0, locked=0 WHERE id IN ('.join(', ',$my_locks).')');
		}

		$ret = array(
			'ok'	=> true,
			'projects'	=> join('||',$this->data('projects')),
			'pager'	=> Pager::get(array(
				'total'	=> $this->total,
				'limit'	=> $this->limit,
				'page_key'=>URL_KEY_PAGE
			)),
			'order'	=> $this->order,
			'by'	=> $this->by,
			'lock'=> $lock,
			'list'	=> $data
		);
		return $ret;
	}

	protected function save() {
		$this->id = (int)post('id');
		$this->data = post('data');
		$_SESSION['project'] = $this->data['project'];
		$this->err('Subject shall not be empty','title')->err('Wrong E-mail address','email','email');
		return $this->save_end();
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}