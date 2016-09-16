<?php

/**
* MyCRM class, opensource
*/

require FTP_DIR_ROOT.'inc/CRM.php';


class MyCrm_config {
	
	public $area = '';
	public $name = '';
	public $config = array();
	public $tabs = array();
	public $wins = array();
	
	
	public function __construct(&$Index, &$area, &$name){
		$this->config = array(
			'tabs'	=> array(
				0 => array('tasks', true, lang('_TASKS')),
				/*0 => array('summary', true, lang('_SUMMARY')),*/
				1 => array('calendar', true, lang('_CALENDAR')),
				2 => array('clients', true, lang('_CLIENTS')),
				3 => array('mail', false, lang('_MAIL')),
				4 => array('log', true, lang('_LOG')),
				5 => array('users', true, lang('_USERS')),
			//	7 => array('stats', false, lang('_STATS')),
			),
			'roles'	=> array(
				
			)
		);
		$this->tabs = array();
		foreach ($this->config['tabs'] as $i => $a) $this->tabs[] = $a[0];
		if (!$name || !in_array($name, $this->tabs)) $name = $this->tabs[0];
		$this->wins = array('form');
		$this->area = $area;
		$this->name = $name;
	}
}


class MyCrm extends CRM {
	
	public $Config;
	

	
	const LOCKING_SECONDS = 2200;
	
	protected $id_custom = '';
	
	public function __construct(&$Index, &$Config) {
		$this->Config =& $Config;
		$this->area =& $this->Config->area;
		$this->name =& $this->Config->name;
		$this->columns = DB::columns('crm_'.$this->name);
		parent::__construct($Index);
		$this->conf['window_title'] = array('Add new entry','Edit entry (ID: %1)','Preview entry (ID: %1)');
		$this->conf['lock_seconds'] = 2200;
		$this->conf['buttons'] = array('Add','Save');
		$this->setLimit(28, true);
	}
	
	protected function allowed() {		
		return is_file(FTP_DIR_TPL.$this->area.'/'.$this->name.'.tpl');
	}
	
	protected function set_diff($name, $id) {
		switch ($name) {
			case 'calendar':
				$remove = false;
				if ($this->rs['dated']!=$this->data['dated']) {
					
				}
				return array(
					'diff_remove'	=> $remove,
					'diff_html'		=> '<span>'.date('H:i',($this->data['dated']?$this->data['dated']:$this->rs['added'])).'</span> '.($this->name=='clients'?$this->data['name']:$this->data['title']).'',
					'diff_id'		=> $id,
				);
			break;
		}
	}
	
	protected function action() {
		parent::action();	
	}
	

	
	public function data($type, $toOptions = -1, $optSettings = 'dropdown') {
		list ($type, $key, $keyAsVal, $lang) = Html::startArray($type);
		switch ($type) {
			case 'year_range':
				$ret = Html::arrRange(date('Y'), date('Y')+12);
			break;
			case 'countries':
				$ids = DB::getAll('SELECT DISTINCT(country) FROM '.$this->sub_prefix.'clients WHERE country>0','country');
				if (!$ids) $ret = array();
				else $ret = DB::getAll('SELECT id,name_'.$this->lang.' FROM '.DB_PREFIX.'geo_countries WHERE id IN ('.join(', ',$ids).')','id|name_'.$this->lang);
			break;
			case 'authors':
				$ids = DB::getAll('SELECT DISTINCT(userid) FROM '.$this->sub_prefix.'clients WHERE userid>0','userid');
				if (!$ids) $ret = array();
				else $ret = DB::getAll('SELECT id,login FROM '.DB_PREFIX.'users WHERE id IN ('.join(', ',$ids).')','id|login');
			break;
			case 'users':
				$ret = DB::getAll('SELECT id,login FROM '.DB_PREFIX.'users WHERE active=1 ORDER BY login','id|login');
			break;
			case 'crm_users':
				$ret = DB::getAll('SELECT id, (SELECT login FROM '.DB_PREFIX.'users WHERE '.DB_PREFIX.'users.id='.$this->sub_prefix.'users.setid) AS login FROM '.$this->sub_prefix.'users ORDER BY login','id|login');
			break;
			case 'email_tpls':
				$keyAsVal = true;
				$ret = DB::getAll('SELECT DISTINCT(name) FROM '.$this->sub_prefix.'mailtpl ORDER BY name','name');
			break;
			case 'projects':
				$keyAsVal = true;
				$ret = DB::getAll('SELECT DISTINCT(project) FROM '.$this->sub_prefix.'tasks WHERE project!=\'\' AND status!='.self::STATUS_DELETED.' AND (access!='.self::ACCESS_PRIVATE.' OR userid='.$this->UserID.' OR assign LIKE \'%,'.$this->UserID.',%\') ORDER BY project','project|project');
			break;
			case 'status_clients';
				$ret = array(
					self::STATUS_HIDDEN		=> 'Hidden',
					self::STATUS_DEFAULT	=> 'General',
					self::STATUS_IMPORTANT	=> 'VIP',
					self::STATUS_DELETED	=> 'Deleted',
					13	=> 'Unknown'
				);
			break;
			case 'status_tasks';
				$ret = array(
					self::STATUS_HIDDEN		=> 'Hidden',
					self::STATUS_DEFAULT	=> 'ToDo',
					self::STATUS_IMPORTANT	=> 'Important',
					self::STATUS_DELETED	=> 'Deleted',
					4	=> 'In progress',
					5	=> 'Completed',
					6	=> 'Testing',
					7	=> 'Cancelled',
					8	=> 'Unclear',
					9	=> 'ReDo'
				);
			break;
			case 'access':
				$ret = array (
					self::ACCESS_PUBLIC		=> 'Public',
					self::ACCESS_PRIVATE	=> 'Private',
					self::ACCESS_GROUP_PRIVATE=> 'Group/Private',
					self::ACCESS_READ		=> 'Read',
					self::ACCESS_LOCKED		=> 'Lock',
					self::ACCESS_LOCKED_READ=> 'Lock/Read'
				);
			break;
			case 'variables':
				$keyAsVal = true;
				$ret = DB::columns('crm_'.post('table'));
			break;
			case 'sex';
				$ret = array(
					'M'	=> 'Male',
					'F'	=> 'Female'
				);
			break;
			case 'roles':
				$ret = $this->conf['roles'];
			break;
			case 'hours':
				$keyAsVal = true;
				$h = date('H');
				$ret = array('');
				for ($i=$h;$i<$h+24	;$i++) {
					$v = $i;
					if ($i>24) $v = $i-24;
					if ($v==24) $v = '0';
					$ret[] = str_pad($v,2,'0',STR_PAD_LEFT);	
				}
				/*
				$ret = array('01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23','00');
				*/
			break;
			case 'minutes':
				$keyAsVal = true;
				$ret = array('00','15','30','45');
			break;
			case 'languages';
				$lang = true;
				$ret = array(
					'ee'	=> 'Eesti',
					'ru'	=> 'Vene',
					'en'	=> 'Inglise',
					'fi'	=> 'Soome',
					'sv'	=> 'Rootsi',
					'da'	=> 'Taani',
					'de'	=> 'Saksa',
					'lv'	=> 'L&auml;ti',
					'lt'	=> 'Leedu',
					'ua'	=> 'Ukraina',
					'pl'	=> 'Poola',
					'fr'	=> 'Prantuse',
					'es'	=> 'Hispaania',
					'pt'	=> 'Portugaali',
					'it'	=> 'Italia',
					'hu'	=> 'Ungari',
					'ar'	=> 'Arabia',
					'cs'	=> 'T&scaron;ehhi',
					'nl'	=> 'Hollandi',
					'ro'	=> 'Rumeenia',
					'bg'	=> 'Bulgaaria'
				);
			break;
			case 'interests':
				$ret = array(
					1	=> 'Buying',
					2	=> 'Selling',
					3	=> 'Trading',
					4	=> 'Renting',
					5	=> 'Asking',
					6	=> 'Talking'
				);
			break;
			case 'interests_cnt':
				$ret = array();
				foreach ($this->data('interests') as $i => $l) {
					$ret[$i] = $l.' ('.DB::one('SELECT COUNT(1) FROM '.$this->sub_prefix.'clients WHERE status!='.self::STATUS_DELETED.' AND (access!='.self::ACCESS_PRIVATE.' OR userid='.$this->UserID.') AND interests LIKE \'%,'.$i.',%\'').')';
				}
			break;
			case 'activities':
				$ret = array (
					1	=> 'Advertising and Marketing',
					2	=> 'Agriculture, Farming and Forestry',
					3	=> 'Animals and Supplies',
					4	=> 'Architecture and Design',
					5	=> 'Art, Relics and Antiques',
					6	=> 'Associations and Organisations',
					7	=> 'Audio visual',
					8	=> 'Automotive and Recreational vehicles',
					9	=> 'Aviation, Aerospace and Satellites',
					10	=> 'Baby supplies',
					11	=> 'Beauty supplies and Hair care',
					12	=> 'Beverages and Tobacco',
					13	=> 'Biotechnology',
					14	=> 'Boating, Yachting, Marine and Port (maritime)',
					15	=> 'Bridal and Marriage supplies',
					16	=> 'Building and Construction',
					17	=> 'Business, Franchising and Management',
					18	=> 'Ceramics, Glass and Stone industry',
					19	=> 'Chemicals and Process technology',
					20	=> 'Clothing, Fashion, Shoes, Textiles, Leather, Fabrics and Apparel',
					21	=> 'Communications and Telecommunications',
					22	=> 'Computers and IT',
					23	=> 'Defence, Military and Weapon',
					24	=> 'Domestic houseware and Consumer goods',
					25	=> 'Education and Training',
					26	=> 'Electronics and Electricity',
					27	=> 'Energy and Power',
					28	=> 'Engineering',
					29	=> 'Entertainment',
					30	=> 'Environmental',
					31	=> 'Erotic',
					32	=> 'Ethnic',
					33	=> 'Finance, Accounting, Banking, Currency and Home economics',
					34	=> 'Fishing and Hunting',
					35	=> 'Food',
					36	=> 'Funeral related',
					37	=> 'Furniture and Fixtures',
					38	=> 'Gardening and Landscaping etc.',
					39	=> 'Gas, Oil and Petroleum',
					40	=> 'Gems, Jewelry, Gold, Silver and Minerals',
					41	=> 'Gifts and Hobbies',
					42	=> 'Government',
					43	=> 'Heating and Air conditioning',
					44	=> 'Hotels and Resorts (hospitality)',
					45	=> 'Human resources',
					46	=> 'Industrial activities',
					47	=> 'Insurance',
					48	=> 'Internet',
					49	=> 'Legal activities',
					50	=> 'Lighting',
					51	=> 'Machines',
					52	=> 'Material handling and Storage',
					53	=> 'Media',
					54	=> 'Medical, Health care and Pharmaceuticals',
					55	=> 'Meetings and Events',
					56	=> 'Metal and Iron',
					57	=> 'Mining',
					58	=> 'Music and Instruments',
					59	=> 'Office equipment and Supplies',
					60	=> 'Packaging',
					61	=> 'Paper products',
					62	=> 'Photography, Optics and Watches',
					63	=> 'Plastics and Rubber',
					64	=> 'Police, Security, Fire and Safety',
					65	=> 'Publishing and Printing',
					66	=> 'Real estate and Property management',
					67	=> 'Religious',
					68	=> 'Restaurant and Food services',
					69	=> 'Retail and Store fixtures',
					70	=> 'Robotics and Automation',
					71	=> 'Sales (in general)',
					72	=> 'Sanitation, Cleaning, Waste management and Recycling',
					73	=> 'Science',
					74	=> 'Sport, Physical, Athletic and Health',
					75	=> 'Transportation and Shipping',
					76	=> 'Travel, Tourism and Recreation',
					77	=> 'Woodworking and Related industries',
					78 => 'Doors and windows',
					79 => 'Saunas',
					80 => 'Log and wooden houses',
					81 => 'Handicraft',
					82 => 'Translation'
				);
				natsort($ret);
			break;
			default:
				$ret = array();
		}
		return Html::endArray($ret, $keyAsVal, $lang, $key, $toOptions, $optSettings);		
	}
}