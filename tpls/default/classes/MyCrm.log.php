<?php

final class MyCrm_log extends MyCrm {
	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->conf['added'] = $this->conf['time_added'].' d M Y';
		$this->conf['window_title'] = array('Log (undefined)','Log preview (ID: %1)','Preview task (ID: %1)');
		parent::run();
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
				'is_int' 	=> array('userid'),
				'like'		=> array('title','data'),
				'from_to'	=> array('added')
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
		$sql = 'SELECT SQL_CALC_FOUND_ROWS a.id, a.setid, a.table, a.title, a.area, a.added, a.userid FROM '.$this->sub_prefix.$this->name.' a WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' '.$this->by;
		$qry = DB::qry($sql,$this->offset, $this->limit);
		$this->total = DB::rows();
		while ($row = DB::fetch($qry)) {
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

	protected function save() {
		$this->id = (int)post('id');
		return $this->save_end();
	}	
}