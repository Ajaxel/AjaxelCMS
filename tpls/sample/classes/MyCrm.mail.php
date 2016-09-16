<?php

final class MyCrm_mail extends MyCrm {
	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->id_custom = $this->name;
		$this->conf['window_title'] = array('Send mail','Send mail to: %1','Preview mail: %1');
		$this->conf['buttons'] = array('Send','Send');
		if (!isset($_SESSION['MAIL_ID'])) $_SESSION['MAIL_ID'] = 0;
		$this->conf['window_width'] = 680;
		$this->conf['datepickers'] = array(
			'dated'	=> $this->conf['time_added'].' d M',
		);
		parent::run();
	}
	
	protected function find() {
		

	}
	
	protected function parseMail($s, $col = 'body') {
		$rs = DB::row('SELECT * FROM '.$this->sub_prefix.'clients WHERE id='.$this->id);
		$table = post('table');
		foreach ($this->data('variables') as $v) {
			$s = str_replace('{$'.$table.'.'.$v.'}',$rs[$v],$s);
		}
		return $s;
	}

	protected function save() {
		$this->id = (int)post('id');
		$this->data = post('data');
		$this->table = post('table');
		$act = post('act');
		$this->err('Send to shall not be empty','to')->err('Body shall not be empty','body');
		if ($act=='save') {
			if (!$this->data['save']) $this->data['save'] = $this->data['template'];
			$this->err('Please specify the save as name','save');
		}
		if (!$this->errors()) {
			switch ($act) {
				case 'save':
					$this->saveTpl();
				break;
				case 'delete':
					$this->delTpl();
				break;
				default:
					if ($this->data['save']) {
						$this->saveTpl();
					}
					$this->mail();
				break;
			}
		}
	}
	
	private function delTpl(){
		DB::run('DELETE FROM '.$this->sub_prefix.'mailtpl WHERE name LIKE '.escape($this->data['template']));	
	}
	
	private function saveTpl(){
		$data = array(
			'name'		=> $this->data['save'],
			'userid'	=> $this->UserID,
			'subject'	=> $this->data['subject'],
			'body'		=> $this->data['body']
		);
		DB::replace('crm_mailtpl',$data);	
	}
	
	
	public function window() {

		$this->row['from_name'] = MAIL_NAME;
		$this->row['from_email'] = MAIL_EMAIL;
		if ($this->id) {
			if (post('table')=='clients') {
				$this->conf['window_suffix'] = ' [Client ID: '.$this->id.']';
			}
			elseif (post('table')=='users') {
				$this->conf['window_suffix'] = ' [User ID: '.$this->id.']';
			}
		}
		
		$data = post('data');
		
		if (isset($data['all']) && $data['all']) {
			$total = DB::one('SELECT COUNT(1) FROM '.$this->sub_prefix.'clients WHERE id IN ('.join(', ',$data['ids']).') AND email!=\'\'');
			$this->title = 'Mass send to '.$total.' emails';
		}
		elseif (isset($data['ids']) && $data['ids']) {
			$total = DB::one('SELECT COUNT(1) FROM '.$this->sub_prefix.'clients WHERE id IN ('.join(', ',$data['ids']).') AND email!=\'\'');
			$this->title = 'Mass send to '.$total.' emails';
		}
		$this->row['dated_Hour'] = date('H');
		
		parent::window();
	}
	
	
	protected function one($form = false) {
		$this->id_custom = post('email');
		$row['to'] = $this->id_custom;
		$row['id'] = $this->id;
		return $row;
	}
	
}

