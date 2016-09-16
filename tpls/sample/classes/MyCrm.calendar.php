<?php

final class MyCrm_calendar extends MyCrm {
	public function __construct(&$Index, &$Config) {
		parent::__construct($Index,$Config);
		$this->id_custom = $this->name;
		$this->conf['lock'] = true;
		parent::run();
	}
	
	protected function find() {
		return $this->calendar_month();
	}
	
	public function calendar_month() {
		$ret = Factory::call('calendar')->init(array(
			'month_get' => 'month',
			'year_get'	=> 'year',
			'from_today'=> false,
			'no_href'	=> true,
			'month_year_dropdown'	=> array(
				'year_from'	=> date('Y')-4,
				'change_month' => 'location.href=\'/crm/calendar/year-'.get('year').'/month-\'+this.value',
				'change_year' => 'location.href=\'/crm/calendar/year-\'+this.value+\'/month-'.get('month').'\''
			)
		))->data(array(
			'data' => array (
				'clients' => array (
					'sql'	=> 'SELECT id, name AS title, (CASE WHEN dated>0 THEN dated ELSE added END) AS added FROM '.$this->sub_prefix.'clients WHERE calendar=\'Y\' AND (access!='.self::ACCESS_PRIVATE.' OR userid='.$this->UserID.') AND status!='.self::STATUS_DELETED.' AND {$where}',
					'col'	=> 'added',
					'type'	=> 'time'
				),
				'tasks' => array (
					'sql'	=> 'SELECT id, title, (CASE WHEN dated>0 THEN dated ELSE added END) AS added FROM '.$this->sub_prefix.'tasks WHERE (access!='.self::ACCESS_PRIVATE.' OR userid='.$this->UserID.' OR assign LIKE \',%'.$this->UserID.',%\') AND status!='.self::STATUS_DELETED.' AND {$where}',
					'col'	=> 'added',
					'type'	=> 'time'
				),
				'tasks_end' => array (
					'sql'	=> 'SELECT id, title, deadline FROM '.$this->sub_prefix.'tasks WHERE deadline>0 AND (access!='.self::ACCESS_PRIVATE.' OR userid='.$this->UserID.' OR assign LIKE \',%'.$this->UserID.',%\') AND status!='.self::STATUS_DELETED.' AND {$where}',
					'col'	=> 'deadline',
					'type'	=> 'time'
				)
			)
		))->buildMonth()->toArray();
		$ret['dropdown'] = Factory::call('calendar')->dropdown();
		return $ret;
	}

	public function calendar_year() {
		return Factory::call('calendar')->init(array(
			'month_get' => 'month',
			'year_get'	=> 'year',
			'from_today'=> true,
			'no_href'	=> true,
		))->data(array(
			/*
			'data' => array (
				'log' => array (
					'sql'	=> 'SELECT reserved FROM '.$this->prefix.'reserv WHERE {$where} AND setid='.$id.' GROUP BY DATE(reserved) ORDER BY reserved DESC',
					'col'	=> 'reserved',
					'type'	=> 'date'
				)
			)
			*/
		))->buildYearOnly()->toArray();	
	}
}