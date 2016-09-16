<?php

final class MyCrm_clients extends MyCrm {

	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->conf['merged'] = array(
			'languages','activities','interests'
		);
		$this->conf['datepickers'] = array(
			'dated'		=> $this->conf['time_added'].' d M',
			'notify'	=> 'H:i d M Y'
		);
		$this->conf['window_title'] = array('Add new client','Edit client (ID: %1)','Preview client (ID: %1)');
		$this->conf['checkboxes'] = array('calendar','newsletter');
		/*
		$this->conf['checks'] = array(
			1	=> array('S','Seminar'),
			2	=> array('C','Course'),
			3	=> array('D','Demo'),
			4	=> array('R','Real'),
			5	=> array('F1','Conference 1'),
			6	=> array('F2','Conference 2')
		);
		*/
		$this->conf['checks'] = array(
			1	=> array('Co','Contacted'),
			2	=> array('Ac','Accepted'),
			3	=> array('Ca','Cancelled'),
			4	=> array('So','Sold'),
			5	=> array('Re','Refund'),
			6	=> array('As','Ask again'),
		);
		$this->conf['status_classes'] = array(
			self::STATUS_HIDDEN 	=> 'hidden',
			self::STATUS_IMPORTANT	=> 'important',
			self::STATUS_DELETED	=> 'deleted',
			self::STATUS_DEFAULT	=> ''
		);
		$this->conf['lock'] = true;
		parent::run();
	}

	protected function filter() {
		$f = post('f');
		if (IS_POST) {
			if (isset($f['added_from'])) $f['added_from'] = $this->toTimestamp($f['added_from']);
			if (isset($f['added_to'])) $f['added_to'] = $this->toTimestamp($f['added_to']);
			if (isset($f['dated_from'])) $f['dated_from'] = $this->toTimestamp($f['dated_from']);
			if (isset($f['dated_to'])) $f['dated_to'] = $this->toTimestamp($f['dated_to']);
			$this->filter = DB::search($f, array(
				'id_from_to'=> 'id',
				'is_int' 	=> array('country','userid','status','access'),
				'is_string'	=> array('sex'),
				'like'		=> array('company','position', 'www' => array('www','skype'), 'name' => array('name','email')),
				'from_to'	=> array('added','dated'),
				'merged'	=> $this->conf['merged']
			),'a');
			foreach ($this->conf['checks'] as $i => $a) {
				if (isset($f['ch'.$i]) && $f['ch'.$i]) {
					$j = array();
					foreach ($f['ch'.$i] as $x) {
						$j[] = 'a.ch'.$i.'='.escape($x);
					}
					if ($j) $this->filter .= ' AND ('.join(' OR ',$j).')';
				}
			}
		}
		if (!isset($f['status']) || !strlen(trim($f['status']))) {
			$this->filter .= ' AND a.status!='.self::STATUS_DELETED;
		}
		$this->filter .= ' AND (a.access!='.self::ACCESS_PRIVATE.' OR a.userid='.$this->UserID.') AND (a.access!='.self::ACCESS_GROUP_PRIVATE.' OR a.groupid='.(int)$this->user['groupid'].')';
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
	
	protected function catchList(&$row) {
		$c = 'interests';
		if (isset($row[$c]) && strlen(trim($row[$c],','))) {
			$arr = Data::getVal('my:'.$c,$row[$c]);
			if (is_array($arr)) {
				$row[$c] = array_values($arr);
			}
		}
		if (isset($row['activities']) && strlen(trim($row['activities'],','))) {
			$row['activities'] = explode(',',trim($row['activities'],','));
			$row['activities_cnt'] = count($row['activities']);
		} else $row['activities_cnt'] = 0;	
	}
	
	protected function find() {
		$pager = $data = $lock = array();
		$lock_id = 0;

		$this->filter();	
		$this->order();
		
		$sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, a.name, a.email,'.($this->conf['checks']?'a.ch'.join(', a.ch',array_keys($this->conf['checks'])).', a.ch'.join('_date, a.ch',array_keys($this->conf['checks'])).'_date,':'').' a.status, a.sex, a.company, a.country, a.www, a.skype, a.position, a.interests, a.activities, a.added, a.notify, a.price, (CASE WHEN (a.locker>0 AND a.locker!='.$this->UserID.' AND a.locked>'.($this->time - self::LOCKING_SECONDS).' AND (SELECT 1 FROM '.DB_PREFIX.'sessions s WHERE s.userid=a.locker)=1) THEN (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=a.locker) ELSE \'\' END) AS locker_login FROM '.$this->sub_prefix.$this->name.' a WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' '.$this->by;
		$qry = DB::qry($sql,$this->offset, $this->limit);
		$this->total = DB::rows();
		while ($row = DB::fetch($qry)) {
			$this->catchRow($row);
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
		if ($this->lock_check()) return false;
		$this->data = post('data');
		$this->err('Name shall not be empty','name')->err('Wrong E-mail address','email','email');
		return $this->save_end();
	}
	


}