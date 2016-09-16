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
* @file       mod/AdminSettings.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminSettings extends Admin {
	
	public function __construct() {
		parent::__construct(__CLASS__);
	}
}


class AdminSettings_global extends Admin {
	public function __construct() {
		$this->title = 'Global settings';
		parent::__construct(__CLASS__);
	}

	
	public function listing() {
		$settings = array(
			'a'		=> array('sep'=>'Site settings'),
			/*
			'PREFIX'=> array(
				't'	=> 'input',
				'n'	=> 'Template DB prefix',
				'h'	=> 'Each template is able to have different data, and you can always select wich data of template you want to edit in admin panel.',
				's'	=> 3
			),
			*/
			'HTTP_BASE'=> array(
				't'	=> 'input',
				'n'	=> 'Domain URL',
				'h'	=> 'Your domain URL with trailing slash',
				's'	=> 1
			),
			'UNDER_CONSTRUCTION'=> array(
				't'	=> 'checkbox',
				'n'	=> 'Close website?',
				'h'	=> 'Useful for maintenance mode',
				's'	=> 2
			),
			'DEFAULT_LANGUAGE'	=> array(
				't'	=> 'select',
				'a'	=> array_label(Site::getLanguages()),
				'n'	=> 'Default Language',
				'h'	=> '',
				's'	=> 3
			),
			'DEFAULT_CURRENCY'	=> array(
				't'	=> 'select',
				'a'	=> array_label(($this->currencies ? $this->currencies : Site::getCurrencies()),1),
				'n'	=> 'Default Currency',
				'h'	=> '',
				's'	=> 3
			),
			'DEFAULT_REGION'	=> array(
				't'	=> 'select',
				'a'	=> Site::getRegions(),
				'n'	=> 'Default Region',
				'h'	=> '',
				's'	=> 3
			),
			'm'		=> array('sep'=>'Email settings'),
			'MAIL_NAME'	=> array(
				't'	=> 'input',
				'n'	=> 'Site name for emails',
				'h'	=> 'Site name for emails',
				's'	=> 2
			),
			'MAIL_EMAIL'	=> array(
				't'	=> 'input',
				'n'	=> 'E-mail address',
				'h'	=> 'Site email address for emails',
				's'	=> 3
			),
			'USE_SMTP' => array(
				't'	=> 'checkbox',
				'n'	=> 'Enable SMTP mail server?',
				'h'	=> 'Wether to enable SMTP authorization to send e-mails',
				's'	=> 2
			),
			'SMTP_HOST'	=> array(
				't'	=> 'input',
				'n'	=> 'SMTP hostname',
				'h'	=> 'eg. smtp.mail.com',
				's'	=> 3
			),
			'SMTP_PORT'	=> array(
				't'	=> 'input',
				'n'	=> 'SMTP port',
				'h'	=> 'Examples: 25, 2525, 43, 443',
				's'	=> 5
			),
			'SMTP_USERNAME'	=> array(
				't'	=> 'input',
				'n'	=> 'SMTP username',
				'h'	=> 'eg. myname@mail.com',
				's'	=> 3
			),
			'SMTP_PASSWORD'	=> array(
				't'	=> 'password',
				'n'	=> 'SMTP password',
				'h'	=> 'Enter your email password',
				's'	=> 3
			),
			
			'b'		=> array('sep'=>'URL engine settings'),
			'KEEP_LANG_URI'=> array(
				't'	=> 'checkbox',
				'n'	=> 'Keep language in URL?',
				'h'	=> 'Good for search engines and linking to certain page',
				's'	=> 2
			),
			'KEEP_TPL_URI'=> array(
				't'	=> 'checkbox',
				'n'	=> 'Keep template in URL?',
				'h'	=> 'Good for search engines and linking to certain page',
				's'	=> 2
			),
			'HTACCESS_WRITE'	=> array(
				't'	=> 'checkbox',
				'n'	=> 'Enable friendly URLs',
				'h'	=> 'Good for search engines and linking to certain page',
				's'	=> 2
			),
			'c'		=> array('sep'=>'User settings'),
			'NO_USER_REGISTER'	=> array(
				't'	=> 'checkbox',
				'n'	=> 'Disable user registration',
				'h'	=> 'You can always disable user registraton',
				's'	=> 2
			),
			/*
			'USER_EMAIL_CONFIRM' => array(
				't'	=> 'checkbox',
				'n'	=> 'Send e-mail validation link',
				'h'	=> 'Sends the confirmation email for email address validation',
				's'	=> 2
			),
			*/
			'USE_IM'	=> array(
				't'	=> 'checkbox',
				'n'	=> 'Enable instant messenger',
				'h'	=> 'Whether to allow users to chat together using instant messenger (IM)',
				's'	=> 2
			),
			
			'x'		=> array('sep'=>'Admin settings'),
			'MAIL_WEBMASTER'	=> array(
				't'	=> 'input',
				'n'	=> 'Debug E-mail',
				'h'	=> 'Site error messages will be sent to this email',
				's'	=> 2
			),
			/*
			'WRITE_LOG'	=> array(
				't'	=> 'checkbox',
				'n'	=> 'Save all actions to log?',
				'h'	=> 'System will store all actions of the site',
				's'	=> 2
			),
			*/
			'UI_ADMIN'	=> array(
				't'	=> 'select',
				'a'	=> array_label(self::getUIsAdmin(),'[[:KEY:]]'),
				'n'	=> 'Admin UI style',
				'h'	=> '',
				'c'	=> ' onchange="$(\'#s-theme_admin\').attr(\'href\',\''.FTP_EXT.'tpls/css/ui/\'+this.value+\'/'.JQUERY_CSS.'\');"',
				's'	=> 3
			),
			'site_notes'	=> array(
				't'	=> 'textarea',
				'n'	=> 'Notes',
				'h'	=> '',
				's'	=> 0
			),
		);
		


		if ($this->submitted) {
			
			$err = array();
			if (!$this->data['HTTP_BASE']) {
				$this->msg_text = lang('$Domain URL cannot be empty');
			}
			if ($this->msg_text) {
				$this->msg_type = 'error';
				$this->msg_reload = false;
			} else {
				$this->allow('settings','edit',$this->rs,$this->data);
				
				$this->lang = $this->data['DEFAULT_LANGUAGE'];
				$this->Index->Session->Lang = $this->lang;
				Cache::saveSmall('get('.self::KEY_LANG.')',$this->lang);
				$_SESSION['UI_ADMIN'] = $this->data['UI_ADMIN'];
			
				$this->data['HTTP_BASE'] = rtrim($this->data['HTTP_BASE'],'/').'/';
				foreach ($settings as $k => $v) {
					if (strlen($k)>1) {
						$data = array(
							'template'	=> $this->template,
							'name'		=> $k,
							'val'		=> strjoin($this->data[$k])
						);
						$this->current[$k] = $this->data[$k];
						DB::replace('settings',$data);
					}
				}
			}
		}
		foreach ($settings as $k => $v) {
			if (!isset($v['sep']) || !$v['sep']) {
				$settings[$k]['v'] = strform(isset($this->data[$k]) ? $this->data[$k] : (isset($this->current[$k]) ? $this->current[$k] : ''));
				if (!$this->submitted && !$settings[$k]['v'] && strtoupper($k)==$k && defined($k) && constant($k)) {
					$settings[$k]['v'] = constant($k);
				}
				$settings[$k]['n'] = lang('$'.$v['n']);	
				if ($v['h']) $settings[$k]['h'] = lang('$'.$v['h']);
			} else {
				$settings[$k]['sep'] = lang('$'.$v['sep']);
			}
		}
		$this->json_data = json_encode($settings);
	}
}

class AdminSettings_db extends Admin {
	public $db_name = DB_NAME;
	public $table = '';
	public $default_separator = ";\r\n-- //..\r\n";
	
	public function __construct() {
		$this->title = 'Database management';
		parent::__construct(__CLASS__);
	//	if (!isset($this->data['type'])) $this->data['type'] = '';
		$this->db_name = request('db_name','',DB_NAME);
		$this->id = request('id');
		if ($this->db_name!=DB_NAME) DB::link()->select_db($this->db_name);
		if ($this->id && !in_array($this->id, DB::tables(1,1))) {
			$this->id = '';
		}
		if ($this->db_name!=DB_NAME) DB::link()->select_db(DB_NAME);
		$this->table = request('table');
		$this->params['no_length_fields'] = array('TEXT','DATE','DATETIME','TIMESTAMP','TIME','YEAR','TINYTEXT','MEDIUMTEXT','LONGTEXT','BINARY','VARBINARY','BLOB','TINYBLOB','MEDIUMBLOB','LONGBLOB');
		$this->params['number_fields'] = array('INT','TINYINT','BIGINT','SMALLINT','MEDIUMINT','FLOAT','DECIMAL','DOUBLE','TIMESTAMP');
		$this->params['types'] = array(
			'MyISAM', 'InnoDB','MEMORY','MRG_MYISAM'
		);
		$this->params['charsets'] = array(
			'armscii8' => array(
				'armscii8_bin',
				'armscii8_general_ci',
			),
			'ascii' => array(
				'ascii_bin',
				'ascii_general_ci',
			),
			'big5' => array(
				'big5_bin',
				'big5_chinese_ci',
			),
			'binary' => array(
				'binary',
			),
			'cp1250' => array(
				'cp1250_bin',
				'cp1250_croatian_ci',
				'cp1250_czech_cs',
				'cp1250_general_ci',
				'cp1250_polish_ci',
			),
			'cp1251' => array(
				'cp1251_bin',
				'cp1251_bulgarian_ci',
				'cp1251_general_ci',
				'cp1251_general_cs',
				'cp1251_ukrainian_ci',
			),
			'cp1256' => array(
				'cp1256_bin',
				'cp1256_general_ci',
			),
			'cp1257' => array(
				'cp1257_bin',
				'cp1257_general_ci',
				'cp1257_lithuanian_ci',
			),
			'cp850' => array(
				'cp850_bin',
				'cp850_general_ci',
			),
			'cp852' => array(
				'cp852_bin',
				'cp852_general_ci',
			),
			'cp866' => array(
				'cp866_bin',
				'cp866_general_ci',
			),
			'cp932' => array(
				'cp932_bin',
				'cp932_japanese_ci',
			),
			'dec8' => array(
				'dec8_bin',
				'dec8_swedish_ci',
			),
			'eucjpms' => array(
				'eucjpms_bin',
				'eucjpms_japanese_ci',
			),
			'euckr' => array(
				'euckr_bin',
				'euckr_korean_ci',
			),
			'gb2312' => array(
				'gb2312_bin',
				'gb2312_chinese_ci',
			),
			'gbk' => array(
				'gbk_bin',
				'gbk_chinese_ci',
			),
			'geostd8' => array(
				'geostd8_bin',
				'geostd8_general_ci',
			),
			'greek' => array(
				'greek_bin',
				'greek_general_ci',
			),
			'hebrew' => array(
				'hebrew_bin',
				'hebrew_general_ci',
			),
			'hp8' => array(
				'hp8_bin',
				'hp8_english_ci',
			),
			'keybcs2' => array(
				'keybcs2_bin',
				'keybcs2_general_ci',
			),
			'koi8r' => array(
				'koi8r_bin',
				'koi8r_general_ci',
			),
			'koi8u' => array(
				'koi8u_bin',
				'koi8u_general_ci',
			),
			'latin1' => array(
				'latin1_bin',
				'latin1_danish_ci',
				'latin1_general_ci',
				'latin1_general_cs',
				'latin1_german1_ci',
				'latin1_german2_ci',
				'latin1_spanish_ci',
				'latin1_swedish_ci',
			),
			'latin2' => array(
				'latin2_bin',
				'latin2_croatian_ci',
				'latin2_czech_cs',
				'latin2_general_ci',
				'latin2_hungarian_ci',
			),
			'latin5' => array(
				'latin5_bin',
				'latin5_turkish_ci',
			),
			'latin7' => array(
				'latin7_bin',
				'latin7_estonian_cs',
				'latin7_general_ci',
				'latin7_general_cs',
			),
			'macce' => array(
				'macce_bin',
				'macce_general_ci',
			),
			'macroman' => array(
				'macroman_bin',
				'macroman_general_ci',
			),
			'sjis' => array(
				'sjis_bin',
				'sjis_japanese_ci',
			),
			'swe7' => array(
				'swe7_bin',
				'swe7_swedish_ci',
			),
			'tis620' => array(
				'tis620_bin',
				'tis620_thai_ci',
			),
			'ucs2' => array(
				'ucs2_bin',
				'ucs2_czech_ci',
				'ucs2_danish_ci',
				'ucs2_esperanto_ci',
				'ucs2_estonian_ci',
				'ucs2_general_ci',
				'ucs2_hungarian_ci',
				'ucs2_icelandic_ci',
				'ucs2_latvian_ci',
				'ucs2_lithuanian_ci',
				'ucs2_persian_ci',
				'ucs2_polish_ci',
				'ucs2_roman_ci',
				'ucs2_romanian_ci',
				'ucs2_slovak_ci',
				'ucs2_slovenian_ci',
				'ucs2_spanish2_ci',
				'ucs2_spanish_ci',
				'ucs2_swedish_ci',
				'ucs2_turkish_ci',
				'ucs2_unicode_ci',
			),
			'ujis' => array(
				'ujis_bin',
				'ujis_japanese_ci',
			),
			'utf8' => array(
				'utf8_bin',
				'utf8_czech_ci',
				'utf8_danish_ci',
				'utf8_esperanto_ci',
				'utf8_estonian_ci',
				'utf8_general_ci',
				'utf8_hungarian_ci',
				'utf8_icelandic_ci',
				'utf8_latvian_ci',
				'utf8_lithuanian_ci',
				'utf8_persian_ci',
				'utf8_polish_ci',
				'utf8_roman_ci',
				'utf8_romanian_ci',
				'utf8_slovak_ci',
				'utf8_slovenian_ci',
				'utf8_spanish2_ci',
				'utf8_spanish_ci',
				'utf8_swedish_ci',
				'utf8_turkish_ci',
				'utf8_unicode_ci',
			),
		);
		$this->msg_reload = false;
		$this->msg_close = false;
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'columns':
				$this->json_ret = array('0') + DB::columns(post('table'));
			break;
		}
	}
	
	private function is_not_empty($s) {
		
		if ($s || $s==='0') return true;
		return false;
	}
	
	private function join_fields($in) {
		if ($in) $columns = DB::getAll('SHOW COLUMNS FROM `'.$this->id.'`','Field');
		else $columns = array();
		$j = array();
		$prevField = '';
		foreach ($this->data as $field => $a) {
			if (!$a['Field']) continue;
			if (in_array($a['Type'], $this->params['no_length_fields'])) {
				$s = ' '.$a['Type'].'';
			} else {
				if (!$a['Length']) continue;
				$s = ' '.$a['Type'].'('.$a['Length'].')';
			}
			if ($a['Attr']) {
				$s .= ' '.$a['Attr'];	
			}
			if ($a['Null']=='YES' && !$this->is_not_empty($a['Default'])) {
				$s .= ' DEFAULT NULL';	
			}
			elseif ($a['Null']=='NO') {
				$s .= ' NOT NULL';
				if ($this->is_not_empty($a['Default'])) {
					$s .= ' DEFAULT '.e($a['Default']).'';
				}
			}
			if ($a['Extra']) {
				$s .= ' '.$a['Extra'];
			}
			if ($field && !is_numeric($field) && in_array($field, $columns)) {
				$j[] = "\nCHANGE `".$field.'` `'.$a['Field'].'`'.$s;
			} else {
				if ($in) {
					if (!$prevField) {
						$s .= ' FIRST';	
					} else {
						$s .= ' AFTER `'.$prevField.'`';	
					}
					$j[] = "\nADD `".$a['Field'].'`'.$s;
				} else {
					$j[] = "\n`".$a['Field'].'`'.$s;	
				}	
			}
			$prevField = $a['Field'];
		}
		return $j;
	}
	
	private function save_table() {
		$time = time();
		if ($this->db_name!=DB_NAME) DB::link()->select_db($this->db_name);
		$tables = DB::tables(false,true,true);
		$this->id = post('id','',get('id'));
		$in = in_array($this->id, $tables);
		if ($in) {
			$sql .= 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'`';
		} else {
			$sql .= 'CREATE TABLE `'.$this->db_name.'`.`'.$this->id.'` (';
		}
		if ($j = $this->join_fields($in)) {
			$sql .= join(', ',$j);
		} else {
			$this->msg_type = 'error';
			$this->msg_error = true;
			$this->msg_text = 'No table columns were added';
			$this->msg_delay = 5000;
			return;	
		}
		if (!$in) {
			$keys = array();
			$fulltexts = array();
			foreach ($this->data as $a) {
				switch ($a['func']) {
					case 'primary':
						$keys[] = 'PRIMARY KEY (`'.$a['Field'].'`)';
					break;
					case 'unique':
						$keys[] = 'UNIQUE (`'.$a['Field'].'`)';
					break;
					case 'index':
						$keys[] = 'INDEX (`'.$a['Field'].'`)';
					break;
				}
				if ($a['fulltext']) {
					$fulltexts[] = $a['Field'];
				}
			}
			if ($keys) $sql .= ",\n".join(",\n",$keys);
			if ($fulltexts) $sql .= ",\nFULLTEXT (`".join('`, `',$fulltexts).'`)';
			$sql .= "\n) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		}
		DB::noerror();
		DB::run($sql);
		if ($err = DB::errorMsg()) {
			$this->msg_type = 'error';
			$this->msg_error = true;
			$this->msg_text = '<span style="color:red">Error in `'.$this->db_name.'`.`'.$this->id.'`:</span><div class="a-sql">'.$err.'</div><div style="text-align:left">'.Message::sql($sql).'</div>';
			$this->msg_delay = 20000;
		}
		else {
			$this->saveToLog($sql);
			if (!$in) {
				$this->msg_type = 'tick';
				$this->msg_text = 'New table `'.$this->db_name.'`.`'.$this->id.'` was added.<div style="text-align:left">'.Message::sql($sql).'</div>';
				$this->msg_close = true;
				$this->msg_delay = 4000;
				$this->msg_js  = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->name.'&'.self::KEY_TAB.'='.$this->tab.'&db_name='.$this->db_name.'&reset\', false, \''.$this->tab.'\');';
			} else {
				$this->msg_delay = 1500;
				$this->msg_type = 'save';
				$this->msg_text = 'Table `'.$this->db_name.'`.`'.$this->id.'` was saved.';
			}
		}
		$this->msg_text .= '<br><small>'.(time()-$time).' seconds taken</small>';
		if ($this->db_name!=DB_NAME) DB::link()->select_db(DB_NAME);
	}
	
	protected function saveToLog($sql, $err = false) {
		if ($this->db_name!=DB_NAME) return;
		$ser = serialize(array('sql'=>$sql,'title'=>($err?'<span style="color:red">[Error]</span> ':'').Parser::geshiHighlight($sql,'mysql')));
		$prev = DB::one('SELECT `data` FROM `'.DB_PREFIX.'log` WHERE `table`=\'SQL\' ORDER BY id DESC');
		if (strlen($sql)<1000 && md5($prev)!=md5($ser)) {
			DB::insert('log',array(
				'setid'		=> 0,
				'table'		=> 'SQL',
				'action'	=> ($err ? Site::ACTION_ERROR : Site::ACTION_UNKNOWN),
				'template'	=> $this->tpl,
				'title'		=> $sql,
				'changes'	=> 0,
				'userid'	=> $this->Index->Session->UserID,
				'added'		=> $this->time,
				'data'		=> $ser
			));
		}
	}
	
	protected function action($action = false){
		$time = time();
		
		switch ($this->action) {
			case 'save':
				$this->allow('db','edit');
				$this->save_table();
			break;
			case 'run':
				$this->allow('db','edit');
				$sql = $this->data;
				if (!$sql) {
					$this->msg_type = 'error';
					$this->msg_error = true;
					$this->msg_text = 'No SQL query';
				}
				if (!AdminSettings_qry::sql_allowed($sql)) {
					$this->msg_type = 'error';
					$this->msg_error = true;
					$this->msg_text = '<span style="color:red">This SQL is not allowed:</span><div style="text-align:left">'.Message::sql($sql).'</div>';
					$this->msg_delay = 20000;
					break;	
				}
				
				DB::noerror();
				$qry = DB::qry($this->data,0,0);
				if ($err = DB::link()->error) {
					$this->msg_type = 'error';
					$this->msg_error = true;
					$this->msg_text = '<span style="color:red">Error in `'.$this->db_name.'`.`'.$this->id.'`:</span><div class="a-sql">'.$err.'</div><div style="text-align:left">'.Message::sql($sql).'</div>';
					$this->msg_delay = 20000;
				} else {
					$data = array();
					while ($row = DB::fetch($qry)) {
						foreach ($row as $i => $v) {
							if ($i=='password' || ($i=='code' && strlen($v)>5)) $v = str_repeat('*',strlen($v));
							$rs[$i] = html($v);	
						}
						array_push($data,$rs);
					}
					DB::free($qry);
					$this->json_push = array('result' => $data);
				}
				
			break;
			case 'drop':
			case 'primary':
			case 'index':
			case 'unique':
			case 'fulltext':
			case 'drop_primary':
			case 'drop_index':
			case 'collate':
			case 'truncate':
			case 'rename':
			case 'tabletype':
			case 'orderby':
			case 'table':
				$this->allow('db','edit');
				
				
				switch ($this->action) {
					case 'drop':
						$columns = DB::getAll('SHOW COLUMNS FROM `'.$this->db_name.'`.`'.$this->id.'`','Field');
						if (count($columns)==count(array_unique($this->data))) {
							$sql = 'DROP TABLE `'.$this->db_name.'`.`'.$this->id.'`';
							$this->msg_error = true;
							$this->msg_close = true;
							$this->msg_reload = true;
							
						} else {
							$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` DROP `'.join('`, DROP `',$this->data).'`';
						}
					break;
					case 'primary':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` ADD PRIMARY KEY(`'.$this->data[0].'`);';
					break;
					case 'index':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` ADD INDEX (`'.join('`, `',$this->data).'`)';
					break;
					case 'unique':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` ADD UNIQUE (`'.join('`, `',$this->data).'`)';
					break;
					case 'fulltext':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` ADD FULLTEXT (`'.join('`, `',$this->data).'`)';
					break;
					case 'drop_primary':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` DROP PRIMARY KEY';
					break;
					case 'drop_index':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` DROP INDEX `'.$this->data.'`';
					break;
					case 'truncate':
						$sql = 'TRUNCATE TABLE `'.$this->db_name.'`.`'.$this->id.'`';
					break;
					case 'rename':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` RENAME `'.$this->db_name.'`.`'.$this->data.'`';
						$this->msg_close = true;
						$this->msg_error = true;
						$this->msg_js = 'S.A.M.edit(\''.$this->data.'&db_name='.$this->db_name.'\');';
					break;
					case 'tabletype':
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` TYPE = '.$this->data.'';
					break;
					case 'orderby':
						$ex = explode(':',$this->data);
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` ORDER BY `'.$ex[0].'` '.$ex[1].'';
					break;
					case 'table':
						if (!in_array($this->data, array('check','analyze','repair','optimize','flush'))) {
							break;
						}
						$sql = ''.strtoupper($this->data).' TABLE `'.$this->db_name.'`.`'.$this->id.'`';
					break;
					case 'collate':
						$j = array();
						$charset = false;
						foreach ($this->params['charsets'] as $group => $a) {
							if (in_array($this->data['charset'], $a)) {
								$charset = $group;
								break;	
							}
						}
						$ret = array();
						$qry = DB::qry('SHOW FULL COLUMNS FROM `'.e($this->db_name.'`.`'.$this->id,false).'`',0,0);
						while ($a = DB::fetch($qry)) {
							if ($a['Collation']) {
								$f = $a['Type'];
								$l = '';
								if ($a['Attr']) {
									$l .= ' '.$a['Attr'];	
								}
								if ($a['Null']=='YES' && !$this->is_not_empty($a['Default'])) {
									$l .= ' DEFAULT NULL';	
								}
								elseif ($a['Null']=='NO') {
									$l .= ' NOT NULL';
									if ($this->is_not_empty($a['Default'])) {
										$l .= ' DEFAULT '.e($a['Default']).'';
									}
								}
								if ($a['Extra']) {
									$l .= ' '.$a['Extra'];
								}
								$ret[$a['Field']] = array($f,$l);
							}
						}
						
						foreach ($this->data['columns'] as $col) {
							if (!isset($ret[$col])) continue;
							$j[] = 'MODIFY `'.$col.'` '.$ret[$col][0].' CHARACTER SET '.$charset.' COLLATE '.$this->data['charset'].$ret[$col][1];
						}
						if (!$j) {
							$this->msg_type = 'warning';
							$this->msg_text = 'No columns to collate';
							$this->msg_error = true;
							return;
						}
						$sql = 'ALTER TABLE `'.$this->db_name.'`.`'.$this->id.'` '.join(', ',$j).'';
						$this->msg_delay = 4000;
					break;
				}
				
				$qry = DB::link()->query($sql);
				if (!$qry && ($err = DB::link()->error)) {
					$this->msg_type = 'error';
					$this->msg_error = true;
					$this->msg_text = 'Error in `'.$this->db_name.'`.`'.$this->id.'`:<div class="a-sql">'.$err.'</div><div style="text-align:left">'.Message::sql($sql).'</div>';
					$this->msg_delay = 10000;
				}
				else {
					$this->saveToLog($sql);
					$this->msg_type = 'gear';
					$this->msg_text = 'Operation successful.<div style="text-align:left">'.Message::sql($sql).'</div>';
					if ($this->action=='table') {
						Conf()->s('debug_seen_i',1);
						$this->msg_error = true;
						$this->msg_delay = 9000;
						if (is_object($qry)) {
							$this->msg_text .= p($qry->fetch_row(), false);
						}
					}
					$this->msg_text .= '<br><small>'.(time()-$time).' seconds taken</small>';
				}
			break;
			case 'backup':
				
				$set = Cache::getSmall('db_backup');
				if (isset($set) && isset($set['start']) && $set['start']) {
					$this->json_ret = Factory::call('dbfunc')->backup();
					if ($set['db_name']!=DB_NAME) {
						DB::link()->select_db(DB_NAME);
					}
					break;
				}
				if ($this->data['type']) {
					Cache::saveSmall('db_backup',NULL);
				}

				$this->allow('db','backup',$this->rs,$this->data);

				if ((!isset($this->data['table']) || !count($this->data['table'])) && $this->data['type']!='php') {
					/*
					$this->msg_text = lang('$No tables were selected');
					$this->msg_type = 'error';
					*/
					break;
				}
				if (!$this->data['name']) {
					$this->msg_text = lang('$Please enter the file name');
					$this->msg_type = 'error';
					break;
				}
				if (!isset($this->data['table'])) {
					$this->msg_text = lang('$No tables were selected');
					$this->msg_type = 'error';
					break;
				}
				if (file_exists(FTP_DIR_TPLS.$this->template.'/backup/'.$this->data['name'])) {
					$this->msg_text = lang('$Such file %1 already exists',0,0,$this->template.'/backup/'.$this->data['name']);
					$this->msg_type = 'warning';
					break;
				}
				if (isset($this->data['no_separator'])) {
					$sep = ";\n";
					$this->data['name'] = $this->data['name'].'i';
				} else {
					$sep = $this->default_separator;
				}
				$db = Factory::call('dbfunc');
				$db->prefix = $this->prefix;
				$db->ext = $this->data['type'];
				$db->filename = $this->data['name'];
				$db->db_name = $this->db_name;
				$db->tables = $this->data['table'];
				$db->all = (($this->db_name!=DB_NAME || post('all')) ? true : false);
				$db->full_insert = $this->data('full_insert');
				$db->normal = $this->data('normal');
				$db->sep = $sep;
				$db->exclude_data_tables = $this->data('excl');
				$db->template = $this->template;
				$db->structure = $this->data('structure');
				$db->content = $this->data('data');
				$this->json_ret = $db->backup();
				if ($this->db_name!=DB_NAME) {
					DB::link()->select_db(DB_NAME);
				}
			break;
			case 'delete':
				$this->allow('db','delete',$this->rs,$this->data);
				if (!$this->file) break;
				if (@unlink(FTP_DIR_TPLS.$this->template.'/backup/'.$this->file)) {
					$this->msg_text = lang('$Backup file %1 was removed',$this->file);
					$this->msg_type = 'trash';
				} else {
					$this->msg_text = lang('$Cannot delete backup file: %1',$this->file);
					$this->msg_type = 'error';	
				}
				$this->msg_reload = false;
			break;
			
			case 'restore':
				$set = Cache::getSmall('db_restore');
				if (isset($set) && isset($set['start']) && $set['start']) {
					$this->json_ret = Factory::call('dbfunc')->restore();
					if ($set['db_name']!=DB_NAME) {
						DB::link()->select_db(DB_NAME);
					}
					break;
				}
				$this->allow('db','restore',$this->rs,$this->data);
				$db = Factory::call('dbfunc');
				$db->prefix = $this->prefix;
				$db->db_name = $this->db_name;
				$db->filename = post(self::KEY_FILE);
				$db->ext = ext($db->filename);
				$db->template = $this->template;
				$this->json_ret = $db->restore();
				if ($this->db_name!=DB_NAME) {
					DB::link()->select_db(DB_NAME);
				}
			break;
		}
	}
	
	public function upload() {

		$this->allow('db','upload');
		
		$handler = new UploadHandler;
		
		$filename = File::unchar($_FILES['Filedata']['name']);
		$tmp_name = $_FILES['Filedata']['tmp_name'];
		
		$nameonly = nameOnly($filename);
		$ext = ext($filename);
		$filename = File::getUnique($dir,$nameonly,$ext);
		
		$dir = FTP_DIR_TPLS.$this->template.'/backup/';
		if (!is_dir($dir)) mkdir($dir,0777);
		$to = $dir.$filename;
		
		$handler->prepare($tmp_name,$filename,$to);
		$filename = $handler->file_name;
		
		$from = $_FILES['Filedata']['tmp_name'];
		
		$handler->prepare($tmp_name,$filename,$to);
		
		$filename = $handler->file_name;

		$handler->move($from, $to);
		echo json_encode($handler->response());
	}
	
	public function listing() {
		
		if (get('new_database')) {
			$sql = 'CREATE DATABASE `'.get('new_database').'`';
			DB::run($sql);
			$this->db_name = get('new_database');
		}
		
		$this->params['db']['databases'] = DB::getAll('SHOW DATABASES','Database');
		$db = $this->db_name;
		
		$no_skip = Site::getGlobalTables();
		$all = get('all');
		if ($all || $db!=DB_NAME) {
			$sql = 'SHOW TABLE STATUS FROM `'.$db.'`';
		}
		elseif ($this->prefix) {
			$sql = 'SHOW TABLE STATUS FROM `'.DB_NAME.'` WHERE (Name IN (\''.DB_PREFIX.join('\', \''.DB_PREFIX,$no_skip).'\') OR Name RLIKE  \'^'.$this->prefix.'\')';
		} else {
			$and = '';
			foreach (DB::getPrefixes() as $prefix) {
				if ($prefix) $and .= ' AND Name NOT LIKE \''.$prefix.'%\'';
			}
			$sql = 'SHOW TABLE STATUS FROM `'.DB_NAME.'` WHERE TRUE'.$and.'';
		}
		$qry = DB::qry($sql,0,0);
		$data = array();
		$rows = $size = $i_size = 0;
		
		$l = strlen($this->prefix);
		while ($rs = DB::fetch($qry)) {
			if (!$all) {
				$name = DB::remDBprefix($rs['Name']);
			} else {
				$name = $rs['Name'];	
			}
			$data[$name] = array(
				0 	=> $rs['Name'],
				1	=> File::display_size($rs['Data_length'], true),
				2	=> File::display_size($rs['Index_length']),
				3	=> $rs['Rows'],
				4	=> Date::format($rs['Create_time'] ? $rs['Create_time'] : $rs['Update_time']),
				5	=> $rs['Auto_increment'],
				6	=> ''.$rs['Engine'].' ('.$rs['Collation'].') '.$rs['Comment'],
			);
			
			$rows += $rs['Rows'];
			$size += $rs['Data_length'];
			$i_size += $rs['Index_length'];
		}
		
		$this->params['db']['total_rows'] = $rows;
		$this->params['db']['total_size'] = File::display_size($size,true);
		$this->params['db']['total_i_size'] = File::display_size($i_size,true);
		$this->params['db']['backup_types'] = array(
			'sql'	=> '.sql'
			,'gz'	=> '.sql.gz'
			,'zip'	=> '.zip'
			,'php'	=> '.php'
		);
		ksort($data);
		$restore = array();
		$dir = FTP_DIR_TPLS.$this->template.'/backup/';
		if (!is_dir($dir)) mkdir($dir);
		$dh = opendir($dir);
		$i = 0;
		$allowed_ext = array('gz','sql','zip','php');
		while ($file = readdir($dh)) {
			if (is_dir($dir.$file)) continue;
			$ext = ext($file);
			if (!array_key_exists($ext, $this->params['db']['backup_types'])) continue;
			$time = filemtime($dir.$file);
			$restore['a'.$time.'_'.$i] = array(
				'time'	=> Date::timeAgo($time,false,false).Conf()->g2('ARR_NUMBERS','ago'),
				'time_full'	=> date('H:i d.m.Y',$time),
				'file'	=> $file,
				'size'	=> File::display_size(filesize($dir.$file),true)
			);
			$i++;
		}
		krsort($restore);
		closedir($dh);
		

		$this->uploadHash();
		$this->output['restore'] = json($restore);
		$this->json_data = json($data);
	}
	
	protected function window() {
		$this->id = get('id');
		if ($this->db_name!=DB_NAME) {
			DB::link()->select_db($this->db_name);
			$table = $this->id;
		} else {
			$table = DB_PREFIX.DB::remDBprefix($this->id);	
		}

		$data = array();
		
		if (!$table || $table==DB_PREFIX || $table=='new') {
			if (get('table_name')) {
				$this->id = get('table_name');
				$fields = (int)get('fields');
				if ($fields<1) $fields = 1;
				$data = array('structure'=>array(),'keys'=>array(),'data'=>array());
				for ($i=1;$i<=$fields;$i++) {
					$data['structure'][] = array(
						'Field'	=> $i,
						'Extra' => '',
						'Type'	=> 'VARCHAR',
						'Length'=> '50',
						'Attr'	=> '',
						'Attr'	=> '',
						'Default'=> '',
					);
				}
				$data['data']['sql'] = '';
				$data['data']['new'] = true;
				$this->options['new'] = true;
			} else return;
		} else {
			$keys = DB::getAll('SHOW KEYS FROM `'.e($this->db_name.'`.`'.$table,false).'`');
			$_keys = $_fields = array();
			foreach ($keys as $k) {
				$d = array();
				$d['Keyname'] = $k['Key_name'];
				$d['Cardinality'] = $k['Cardinality'];
				$d['Field'] = $k['Column_name'];
				$d['Type'] = $k['Index_type'];
				if ($d['Keyname']=='PRIMARY') {
					$d['Type'] = 'PRIMARY';
				}
				elseif (!$k['Non_unique']) {
					$d['Type'] = 'UNIQUE';
				}
				array_push($_keys,$d);
			}
			
			$data['keys'] = $_keys;
			$id_col = '';
			foreach ($keys as $k) {
				if ($k['Key_name']=='PRIMARY') {
					$id_col = $k['Column_name'];
				}
			}
			if (!$id_col) {
				$j = array();
				if ($k['Key_name']=='UNIQUE') {
					$j[] = $k['Column_name'];
				}
				$id_col = join('-',$j);
			}
			$cols = array();
			$data['structure'] = array();
			
			$qry = DB::qry('SHOW FULL COLUMNS FROM `'.e($this->db_name.'`.`'.$table,false).'`',0,0);
			while ($rs = DB::fetch($qry)) {
				$ex = explode('(',$rs['Type']);
				$rs['Type'] = strtoupper($ex[0]);
				$rs['Length'] = '';
				$rs['Attr'] = '';
				if (isset($ex[1])) {
					$_ex = explode(')',$ex[1]);
					if (isset($_ex[1])) {
						$rs['Length'] = $_ex[0];
						$rs['Attr'] = strtoupper(trim($_ex[1]));
					} else {
						$rs['Length'] = trim(substr($ex[1],0,-1));
					}
				}
				$rs['Default'] = strtoupper($rs['Default']);
				$rs['Null'] = strtoupper($rs['Null']);
				array_push($data['structure'],$rs);
				$cols[$rs['Field']] = $rs['Type'];
				array_push($_fields, '`'.$rs['Field'].'`');
			}
			DB::free($qry);
			
			$data['data'] = array();
			$data['data']['new'] = false;
			$data['data']['set'] = array(
				'id_col'	=> $id_col
			);
			$data['data']['list'] = array();
			$sql = 'SELECT * FROM '.($this->db_name!=DB_NAME?'`'.$this->db_name.'`.':'').'`'.e($table,false).'`';
			$data['data']['sql'] = str_replace('SELECT ','SELECT SQL_CALC_FOUND_ROWS ',$sql).' LIMIT 0, 25';
			
			$qry = DB::qry($sql,0,25);
			$this->total = DB::rows();
			while ($rs = DB::fetch($qry)) {
				foreach ($rs as $i => $v) {
					if (USER_ID!=SUPER_ADMIN && ($i=='password' || ($i=='code' && strlen($v)>4))) $v = str_repeat('*',strlen($v));
					$rs[$i] = html($v);	
				}
				array_push($data['data']['list'],$rs);
			}
			DB::free($qry);
			$data['data']['cols'] = $cols;
			
			$data['data']['status'] = DB::row('SHOW TABLE STATUS WHERE Name = '.e($table),false,0,0);
			
			$this->options['new'] = false;
		}
		
		$this->json_data = json($data);

		DB::link()->select_db(DB_NAME);
		$this->win('settings_db');
	}
}

// still so much todo
class AdminSettings_langs extends Admin {
	public function __construct() {
		$this->title = 'Languages management';
		$this->idcol = 'code';
		parent::__construct(__CLASS__);
		$this->languages = include(FTP_DIR_ROOT.'config/system/languages.php');
		ksort($this->languages);
		$this->lang_cols = array(
			'content'	=> array (
				'type'		=> 1,
				'fields'	=> array('title_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'')
			),
			'lang'	=> array (
				'type'		=> 1,
				'fields'	=> array('text_' => 'TEXT NOT NULL DEFAULT \'\'')
			),
			'vars'	=> array (
				'type'		=> 1,
				'fields'	=> array('val_' => 'TEXT NOT NULL DEFAULT \'\'')
			),
			'entries_files'	=> array (
				'type'		=> 1,
				'fields'	=> array('title_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'descr_' => 'TEXT NOT NULL DEFAULT \'\'')
			),
			'pages_files'	=> array (
				'type'		=> 1,
				'fields'	=> array('title_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'descr_' => 'TEXT NOT NULL DEFAULT \'\'')
			),
			'menu'	=> array (
				'type'		=> 1,
				'fields'	=> array('title_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'keywords_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'descr_' => 'TEXT NOT NULL DEFAULT \'\'', 'title2_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', )
			),
			'tree'	=> array (
				'type'		=> 1,
				'fields'	=> array('title_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'keywords_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'descr_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', 'title2_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'', )
			),
			'forum_cats'	=> array (
				'type'		=> 1,
				'fields'	=> array('catname_' => 'VARCHAR(255) NOT NULL DEFAULT \'\'','descr_' => 'TEXT NOT NULL DEFAULT \'\'')
			),
		);	
		$this->lang_cols['entries'] = array (
			'type'	=> 2,
			'field'	=> 'lang'
		);
		$this->tables = DB::tables();
		foreach ($this->tables as $table) {
			if (substr($table,0,9)=='category_') {
				$this->lang_cols[$table] = array (
					'type'	=> 1,
					'fields'=> array('catname_'=>'VARCHAR(255) NOT NULL DEFAULT \'\'')
				);
			}
			elseif (substr($table,0,8)=='content_' && substr($table,-6)!='_files') {
				$this->lang_cols[$table] = array (
					'type'	=> 2,
					'field'	=> 'lang'
				);
			}
			elseif (substr($table,0,8)=='content_' && substr($table,-6)=='_files') {
				$this->lang_cols[$table] = array (
					'type'	=> 1,
					'fields'=> array('title_'=>'VARCHAR(255) NOT NULL DEFAULT \'\'','descr_'=>'TEXT')
				);
			}
		}
		$this->lang_files = array(
			FTP_DIR_ROOT.'config/conf_[LANG].php',
			FTP_DIR_ROOT.'config/lang/lang_admin_[LANG].php',
			FTP_DIR_ROOT.'config/lang/lang_'.$this->tpl.'_[LANG].php'
		);
	}
	
	/*
	private function fix(&$langs) {
		$this->langs_add = array();
		$cols = DB::columns('content', false);
		$unistall = array();
		foreach ($cols as $c) {
			if (substr($c,0,6)=='title_' && ($l = substr($c,6)) && strlen(trim($l))==2 && strtolower($l)==$l && !array_key_exists($l, $langs) && array_key_exists($l,$this->languages)) {
				$this->langs_add[$l] = $this->languages[$l];
				$this->installLanguage($l, DEFAULT_LANGUAGE, $langs);
			}
		}
	}
	*/
	

	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'languages':
				$ret = array();
				$langs = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'languages\' AND template='.e($this->tpl),'val');
				if (!$langs) {
					$langs = $this->langs;
				} else {
					$langs = strexp($langs);	
				}
				if (!is_array($langs)) $langs = array('en'=>array('name'=>'English','u_name'=>'English'));
				
				foreach ($this->languages as $code => $rs) {
					if (array_key_exists($code, $langs) || $code=='un') continue;
					if (!is_file(FTP_DIR_TPLS.'img/flags/16/'.$code.'.png')) continue;
					$ret[$code] = strtoupper($code).': '.$rs['name'].($rs['u_name']?' ('.$rs['u_name'].')':'');
				}
				$this->json_ret = array('0'=>lang('$Select language to install')) + $ret;
			break;
		}
	}
	
	protected function action($action = false) {
		switch ($this->action) {
			case 'act':
				$this->id = post('id');
				if (!$this->id) break;
				$this->allow('languages','activate');
				$langs = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->template.'\' AND name=\'languages\'','val');
				$langs = strexp($langs);
				if (!$langs || !is_array($langs)) break;
				$active = @$langs[$this->id][3]==1;
				$langs[$this->id][3] = ($active ? 0 : 1);
				$sql = 'UPDATE '.DB_PREFIX.'settings SET val='.e(strjoin($langs)).' WHERE template='.e($this->template).' AND name=\'languages\'';
				DB::run($sql);

				$this->msg_text = lang('$%1 language was '.($active ? 'deactivated' : 'activated'),(isset($langs[$this->id][0])?$langs[$this->id][0]:$this->id));
				$this->msg_reload = false;
				$this->msg_close = false;
			break;
			case 'sort':
				$langs = strexp(DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->template.'\' AND name=\'languages\'','val'));
				$sort = post('sort');
				$ex = explode('|',trim($sort,'|'));
				$_langs = array();
				foreach ($ex as $e) {
					list(,$cur,) = explode('-',$e);
					$_langs[$cur] = $langs[$cur];
				}
				$sql = 'UPDATE '.DB_PREFIX.'settings SET val='.e(strjoin($_langs)).' WHERE template='.e($this->template).' AND name=\'languages\'';
				DB::run($sql);
			break;
		}
	}
	
	public function listing() {
		$output = array();
		foreach ($this->lang_cols as $table => $arr) {
			switch ($arr['type']) {
				case 1:
				$cols = DB::columns($table);
				foreach ($arr['fields'] as $field => $type) {
					foreach ($this->langs as $l => $x) {
						if (!in_array($field.$l,$cols)) {
							$output['errors'][] = lang('$Column %1 doesn\'t exist in table %2',$field.$l,$table);
						}
					}
				}
			}
		}

		$langs = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'languages\' AND template='.e($this->tpl),'val');
		
		if (!$langs) {
			$langs = $this->langs;
		} else {
			$langs = strexp($langs);
		}
		
		if (!is_array($langs)) {
			$langs = array('en'=>array('English','english','ENG'));
		}
		
		/*
		$cols = DB::columns('lang');
		$ret = array();
				
		foreach ($cols as $c) {
			if (substr($c,0,5)=='text_') {
				$l = substr($c,5);
				if (!array_key_exists($l,$langs)) {
					$langs[$l] = array(
						$this->languages[$l]['name'],
						$this->languages[$l]['u_name'],
						strtoupper(substr($this->languages[$l]['name'],0,3)),
						0
					);
				}
			}
		}
		*/
		
		if ($langs && is_array($langs)) {
			foreach ($langs as $l => $a) {
				if (!@$a[3] && $l==DEFAULT_LANGUAGE) $a[3] = 1;
				$langs[$l] = array(@$a[0], @$a[1], @$a[2], @$a[3]);
			}
		}
		/*
		foreach ($this->langs_add as $l => $a) {
			$langs[$l] = array($a['name'], $a['u_name'], $a['short'], 0);	
		}
		*/
		//$this->fix($langs);
		
		if (post('translate')==1) {
			$this->array['translated_text'] = translate(post('translate_query'), post('translate_from'), post('translate_to'), true);	
		}
		elseif ($this->submitted) {
			$_langs = array();
			foreach ($this->data as $code => $arr) {
				if ($code=='new' || $code=='from') continue;
				if (array_key_exists($code, $langs)) {
					$_langs[$code] = $arr;
					$_langs[$code][3] = $langs[$code][3];
				}
			}
			$langs = $_langs;
			if ($this->action=='install' && $this->data['new'] && $this->data['from'] && array_key_exists($this->data['from'], $langs)) {
				$this->allow('languages','add');
				$this->installLanguage($this->data['new'], $this->data['from'], $langs);
			}
			elseif ($this->action=='uninstall' && $this->data['uninstall'] && $this->data['uninstall']!=$this->current['DEFAULT_LANGUAGE'] && array_key_exists($this->data['uninstall'], $langs)) {
				$this->allow('languages','delete');
				$this->uninstallLanguage($this->data['uninstall'], $langs);
			}
			if ($langs) {
				$this->allow('languages','save',$this->rs,$this->data);	
				$sql = 'REPLACE INTO '.DB_PREFIX.'settings (template, name, val) VALUES ('.e($this->tpl).', \'languages\', '.e(strjoin($langs)).')';
				DB::run($sql);
			}
			$this->msg_reload = false;
		}
		$this->langs = $langs;
		$this->output = $output;
		$this->json_data = json($langs);
	}
	
	private function uninstallLanguage($code, &$langs) {
		DB::setPrefix($this->prefix);
		foreach ($this->lang_cols as $table => $arr) {
			switch ($arr['type']) {
				case 1:
					foreach ($arr['fields'] as $field => $type) {
						DB::noerror();
						$sql = 'ALTER TABLE `'.DB::prefix($table).'` DROP `'.$field.$code.'`';
						DB::run($sql);
					}
				break;
				case 2:
					DB::noerror();
					$sql = 'DELETE FROM `'.DB::prefix($table).'` WHERE `'.$arr['field'].'`=\''.$code.'\'';
					DB::run($sql);
				break;
			}
		}
		if (!is_dir(FTP_DIR_ROOT.'config/old/')) mkdir(FTP_DIR_ROOT.'config/old/');
		foreach ($this->lang_files as $f) {
			$from = str_replace('[LANG]',$code,$f);
			$ex = explode('/',$f);
			$file = end($ex);
			$to = str_replace('[LANG]',$code,FTP_DIR_ROOT.'config/old/'.$this->time.'_'.$file);
			@rename($from, $to);
		}
		if (Session()->Lang==$code) {
			$new_lang = '';
			foreach ($langs as $c => $a) {
				if ($code!=$c) {
					$new_lang = $c;
					break;	
				}
			}
			
			Session()->Lang = $new_lang;
		}
		$new_langs = array();
		foreach ($langs as $l => $a) {
			if ($code==$l) {
				continue;	
			}
			$new_langs[$l] = $a;
		}
		$langs = $new_langs;
		Conf()->s('languages', $langs);
		$this->msg_type = 'trash';
		$this->msg_text = lang('$%1 language was uninstalled',$langs[$code][0]);
		$this->msg_delay = 3000;
		DB::resetCache();
		unset($langs[$code]);
	}
	
	private function installLanguage($code, $from, &$langs) {
		DB::setPrefix($this->prefix);
		$code = strtolower($code);
		if (!$this->languages[$code]) return false;
		$langs[$code] = array(
			0 => $this->languages[$code]['name'],
			1 => $this->languages[$code]['u_name'],
			2 => $this->languages[$code]['short'],
			3 => 1
		);
		
		foreach ($this->lang_cols as $table => $arr) {
			switch ($arr['type']) {
				case 1:
					foreach ($arr['fields'] as $field => $type) {
						DB::noerror();
						$sql = 'ALTER TABLE `'.DB::prefix($table).'` ADD `'.$field.$code.'` '.$type;
						DB::run($sql);
						$err = DB::errorNo();
						DB::noerror();
						if (!$err) {
							$sql = 'UPDATE `'.DB::prefix($table).'` SET `'.$field.$code.'`=`'.$field.$from.'`';
							DB::run($sql);
						}
					}
				break;
				case 2:
					$ex = DB::one('SELECT 1 FROM `'.DB::prefix($table).'` WHERE '.$arr['field'].'=\''.$code.'\'');
					if ($ex) break;
					$_cols = DB::columns($table, false);
					$cols = array();
					foreach ($_cols as $c) {
						if ($c!='id' && $c!=$arr['field']) $cols[] = $c;
					}
					$sql = 'INSERT INTO `'.DB::prefix($table).'` (`'.join('`, `',$cols).'`, `'.$arr['field'].'`) SELECT b.'.join(', b.',$cols).', \''.$code.'\' FROM `'.DB::prefix($table).'` b WHERE b.'.$arr['field'].'=\''.$from.'\'';
					DB::run($sql);

				break;	
			}
		}
		foreach ($this->lang_files as $f) {
			$from_file = str_replace('[LANG]',$from,$f);
			$to = str_replace('[LANG]',$code,$f);
			if (!is_file($to)) {
				@copy($from_file, $to);
			}
		}
		
		DB::resetCache();
		
		$this->msg_type = 'monitor';
		$this->msg_text = lang('$%1 language was successfully installed (copied from: %2)! Time taken: %3 seconds',$this->languages[$code]['name'], $langs[$from][1], time()-$this->time);
		$this->msg_delay = 3000;
	}
	
}

class AdminSettings_templates extends Admin {
	public function __construct() {
		$this->title = 'Templates management';
		parent::__construct(__CLASS__);
		$this->idcol = 'name';
		$this->options['engines'] = array(
			'ajaxel'	=> 'Ajaxel',
		//	'wordpress'	=> 'Wordpress',
		//	'drupal'	=> 'Drupal',
		//	'joomla'	=> 'Joomla'
		);
		$this->id = post('id','',get('id'));
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
		}
	}
	protected function action($action = false) {
		if ($action) $this->action = $action;
		
		switch ($this->action) {
			case 'act':
				$this->allow('templates','activate',$this->rs,$this->data);
				$row = DB::row('SELECT active FROM '.DB_PREFIX.'templates WHERE name='.e($this->id));
				if (!$row) return false;
				DB::run('UPDATE '.DB_PREFIX.'templates SET active='.($row['active']?0:1).' WHERE name='.e($this->id));
				$templates = Site::getTemplates(true);
				DB::run('UPDATE '.DB_PREFIX.'settings SET val='.e(strjoin($templates)).' WHERE name=\'templates\'');
				$this->msg_text = lang('$%1 template was '.($row['active'] ? 'deactivated' : 'activated'),$this->id);
				$this->msg_reload = false;
				$this->msg_close = false;
			break;
			case 'sort':
				$this->allow('templates','sort',$this->rs,$this->data);
				$this->global_action();
			break;
			case 'save':
			
				/*
				if ($this->submitted) {
					$this->allow('templates','save');
					foreach ($data as $tpl => $a) {
						$data[$tpl][0] = $this->data[$tpl][0];
						$save = array(
							'name'	=> $tpl,
							'descr'	=> $this->data[$tpl][0],
							'active'=> $data[$tpl][1],
							'added'	=> $data[$tpl][2]
						);
						DB::replace('templates',$save);
					}
					DB::run('REPLACE INTO '.DB_PREFIX.'settings (template, name, val) VALUES (\'global\', \'templates\', '.e(strjoin($data)).')');
				}
				*/
				
				if (!strlen(trim($this->data['name']))) {
					$err['name'] = lang('$Folder name is empty');
				}
				elseif (!is_dir(FTP_DIR_TPLS.$this->data['name'].'/')) {
					$err['name'] = lang('$Such folder does not exist, cannot rename');
				}
				if (!strlen(trim($this->data['prefix']))) {
					$err['prefix'] = lang('$Template prefix cannot be empty');
				}
				if (!strlen(trim($this->data['title']))) {
					$err['title'] = lang('$Template title cannot be empty');
				}
				if (!$this->data['engine']) {
					$err['engine'] = lang('$Engine must be chosen');
				}
				$this->errors($err);
				
				DB::update('templates',$this->data,$this->id,'name');
				if (DB::affected()) {
					$this->msg_text = lang('$Template %1 has been saved',$this->id);
					$this->msg_reload = true;
				}
				$this->data['prefix'] = rtrim(trim(preg_replace('/[\/\?:*\\\|><"]/', '', $this->data['prefix'])),'_').'_';
				
				$prefix = DB::one('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\' AND template='.e($this->id));
				
				if ($this->data['prefix']!=$prefix) {
					$tables = DB::tables();
					$all_tables = DB::tables(false, true);
					$skip = Site::getGlobalTables();
					$prefixes = DB::getAll('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\'','val');
					foreach ($tables as $table) {
						if (in_array($table, $skip)) continue;
						/*
						foreach ($prefixes as $prefix) {
							$l = strlen($prefix);
							if (substr($table,0,$l)==$prefix) {
								continue(2);	
							}
						}
						*/
						
						if (!in_array($prefix.$table, $all_tables)) {
							
						} elseif (in_array($this->data['PREFIX'].$table, $all_tables)) {
								
						} else {
							DB::noerror();
							DB::run('ALTER TABLE `'.DB_PREFIX.$prefix.$table.'` RENAME `'.DB_PREFIX.$this->data['prefix'].$table.'`');
						}
					}
					@rename(FTP_DIR_ROOT.'files/'.rtrim($prefix,'_').'/', FTP_DIR_ROOT.'files/'.rtrim($this->data['prefix'],'_').'/');
					DB::run('UPDATE '.DB_PREFIX.'settings SET val='.e($this->data['prefix']).' WHERE name=\'PREFIX\' AND val='.e($prefix));
					$this->msg_text = lang('$Template %1 has been saved',$this->id);
					$this->msg_reload = true;
				}
				
			
			break;
			case 'uninstall':
				if (!$this->id) break;
				$this->allow('templates','delete',$this->rs,$this->data);
				$current = DB::getAll('SELECT name,val FROM '.DB_PREFIX.'settings WHERE template='.e($this->id),'name|val');
				DB::run('DELETE FROM '.DB_PREFIX.'templates WHERE name='.e($this->id));
				$is_other_prefix = DB::getNum('SELECT 1 FROM '.DB_PREFIX.'settings WHERE template!='.e($this->id).' AND name=\'PREFIX\' AND val='.e($current['PREFIX']));
				if ($is_other_prefix) {
					$this->data['u_files'] = false;
				}
				
				if (!$current) {
					if (@$this->data['t_files']) {
						File::delFolder(FTP_DIR_TPLS.$this->id.'/',true,true);
						File::delFolder(FTP_DIR_TPLS.$this->id.'/',true,false);
					}
					if (@$this->data['data']) {
						DB::run('DELETE FROM '.DB_PREFIX.'lang WHERE template='.e($this->id).' OR template=\'\'');	
						DB::run('DELETE FROM '.DB_PREFIX.'vars WHERE template='.e($this->id).' OR template=\'\'');
					}
					if (!DB::affected()) {
						$this->msg_text = 'Such template does not exist';
						$this->msg_type = 'error';
					} else {
						$this->msg_text = 'This template was removed from the list';
						$this->msg_type = 'tick';						
					}
					$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=templates\',false,\'templates\');';
					$this->msg_reload = false;
					break;
				}
				DB::run('DELETE FROM '.DB_PREFIX.'settings WHERE template='.e($this->id));
				$templates = strexp(DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\'global\' AND name=\'templates\'','val'));
				unset($templates[$this->id]);
				$data = array(
					'val'		=> strjoin($templates),
					'template'	=> 'global',
					'name'		=> 'templates'
				);
				DB::replace('settings',$data);
				if ($this->data['data']) {
					DB::setPrefix(DB_PREFIX.$current['PREFIX']);
					$tables = DB::tables(false);
					foreach ($tables as $table) {
						if (in_array($table, Site::getGlobalTables())) continue;
						DB::run('DROP TABLE `'.DB_PREFIX.$current['PREFIX'].$table.'`');	
					}
					DB::run('DELETE FROM '.DB_PREFIX.'vars WHERE template='.e($this->id).' OR template=\'\'');
					DB::run('DELETE FROM '.DB_PREFIX.'lang WHERE template='.e($this->id).' OR template=\'\'');
					DB::setPrefix(DB_PREFIX.$this->current['PREFIX']);
				}
				if ($this->data['t_files']) {
					File::delFolder(FTP_DIR_TPLS.$this->id.'/',true,true);
					File::delFolder(FTP_DIR_TPLS.$this->id.'/',true,false);
				}
				if ($this->data['u_files']) {
					File::delFolder(FTP_DIR_ROOT.$current['PREFIX'].'files/',true,true);
					File::delFolder(FTP_DIR_ROOT.$current['PREFIX'].'files/',true,false);
				}
				$this->msg_text = lang('$%1 template was uninstalled', $this->id);
				$this->msg_type = 'trash';
				$this->msg_reload = false;
				$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=templates\',false,\'templates\');';
			break;
			
			case 'create':
				$this->allow('templates','add',$this->rs,$this->data);
				$err = array();
				if (!isset($this->data['files_from'])) $this->data['files_from'] = '';
				$this->data['name'] = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1', $this->data['name']);
				$this->data['prefix'] = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1', $this->data['prefix']);
				if ($this->data['prefix']) $this->data['prefix'] = trim($this->data['prefix'],'_').'_';

				if (!strlen(trim($this->data['name']))) {
					$err['name'] = lang('$Template folder name is empty');
				}
				elseif (in_array($this->data['name'], array('editor','temp','users','default','email','admine','sample','js','css','img'))) {
					$err['name'] = lang('$Template folder name shall be different');
				}
				if (!strlen(trim($this->data['engine']))) {
					$err['engine'] = lang('$Engine is not selected');
				}
				if (!strlen(trim($this->data['prefix']))) {
					$err['prefix'] = lang('$Data prefix is empty');
				}
				elseif ($this->data['data_from'] && DB::one('SELECT 1 FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\' AND val='.e($this->data['prefix']))) {
					//$err['prefix'] = lang('$Same prefix already exists, cannot duplicate data');
				}
				
				if (!strlen(trim($this->data['title']))) {
					$err['title'] = lang('$Title is empty');
				}
				
				if (!strlen(trim($this->data['files_from'])) && !is_dir(FTP_DIR_TPLS.$this->data['name'].'/')) {
					$err['files_from'] = lang('$Select the original template path you want to copy files from');
				}
				elseif (!is_dir(FTP_DIR_TPLS.$this->data['files_from'])) {
					$err['files_from'] = lang('$Fatal error, such folder does not exist');
				}
				
				if ($this->data['prefix']) {
					$exist = DB::getNum('SELECT 1 FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\' AND val='.e($this->data['prefix']));
					$from = ($this->data['data_from'] ? $this->data['data_from'] : $this->data['files_from']);
				}
				
				$this->errors($err);
				ignore_user_abort(true);
				set_time_limit(3600);
				DB::resetCache();
				
				if ($this->data['data_from']) {
					$prefix = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template='.e($from).' AND name=\'PREFIX\'','val');
				} else {
					$prefix = $this->current['PREFIX'];	
				}
				
				
				if ($this->data['data_from']!='default') {
					if (!$exist) {
						
						DB::setPrefix(DB_PREFIX.$prefix);
						$tables = DB::tables();
						$all_tables = DB::tables(false, true);
						$skip = array('orders','cache');
						
						$db = Factory::call('dbfunc');
						$db->prefix = $this->prefix;
						$db->db_name = DB_NAME;
						
						foreach ($tables as $table) {
							if (in_array($table, Site::getGlobalTables())) continue;
							$copy = $this->data['prefix'].$table;
							if (in_array($copy, $all_tables)) continue;
							$exclude = array();
							$incData = false;
							
							$db->exportTable($prefix.$table,$incData,'','utf8',true,$exclude,$copy);
							if ($this->data['data_from']) {
								if (substr($table,0,7)!='visitor' && !in_array($table,$skip)) {
									DB::noerror();
									DB::run('INSERT INTO `'.DB_PREFIX.$copy.'` SELECT * FROM `'.DB_PREFIX.$prefix.$table.'`');
								}
							}
						}
						/*
						if (!$this->data['data_from']) {
							DB::noerror();
							DB::run('INSERT INTO `'.DB_PREFIX.$this->data['prefix'].'modules` SELECT * FROM `'.DB_PREFIX.$prefix.'modules`');
						}
						*/	
					}

					$current = DB::getAll('SELECT name,val FROM '.DB_PREFIX.'settings WHERE template='.e($from),'name|val');
					$current['PREFIX'] = $this->data['prefix'];
					foreach ($current as $k => $v) {
						$data = array(
							'template'	=> $this->data['name'],
							'name'		=> $k,
							'val'		=> $v
						);
						DB::replace('settings',$data);
					}
					if (@$current['languages'] && ($l = strexp($current['languages'])) && is_array($l)) {
						$langs = array_keys($l);
					} else {
						$langs = array_keys($this->langs);
					}
					if (!$langs) $langs = array('en');
					
					if ($this->data['name']!=$from) {
						DB::run('INSERT INTO '.DB_PREFIX.'lang (name, template, text_'.join(', text_',$langs).') SELECT b.name, '.e($this->data['name']).', b.text_'.join(', b.text_',$langs).' FROM '.DB_PREFIX.'lang b WHERE b.template='.e($from));
						DB::run('INSERT INTO '.DB_PREFIX.'vars (name, template, val_'.join(', val_',$langs).') SELECT b.name, '.e($this->data['name']).', b.val_'.join(', b.val_',$langs).' FROM '.DB_PREFIX.'vars b WHERE b.template='.e($from));
					}
				} else {
					
					$sql = require FTP_DIR_ROOT.'config/system/tables.php';
					$db_prefix = '[[:DB_PREFIX:]]';
					$_prefix = '[[:PREFIX:]]';
					foreach ($sql as $s) {
						if (!strpos($s, $_prefix)) continue;
						$s = str_replace($_prefix,$this->data['prefix'],str_replace($db_prefix,DB_PREFIX,$s));
						DB::noerror();
						DB::run($s);
					}
					
					$sql = array();
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'PREFIX\', \''.($this->data['prefix']).'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'HTTP_BASE\', \''.HTTP_BASE.'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'MAIL_NAME\', \''.MAIL_NAME.'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'MAIL_EMAIL\', \''.MAIL_EMAIL.'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'DEFAULT_LANGUAGE\', \'en\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'DEFAULT_CURRENCY\', \'EUR\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'KEEP_LANG_URI\', \'1\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'KEEP_TPL_URI\', \'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'HTACCESS_WRITE\', \'1\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'NO_USER_REGISTER\', \'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'USER_EMAIL_CONFIRM\', \'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'USE_IM\', \'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'MAIL_WEBMASTER\', \''.MAIL_WEBMASTER.'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'UI_ADMIN\', \''.UI_ADMIN.'\')';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'site_notes\', \'Template was installed on '.date('H:i d M Y').', from '.$_SERVER['HTTP_HOST'].' with IP: '.Session::getIP().' by '.Session()->Login.'\')';
				
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'currencies\', '.e('[[:ARRAY:]]'.serialize(Conf()->g('currencies'))).')';
					
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]settings` VALUES (\''.$this->data['name'].'\', \'languages\', '.e('[[:ARRAY:]]'.serialize(Conf()->g('languages'))).')';
					
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]]templates` VALUES (\''.$this->data['name'].'\', \'\', \'\', \'\', '.e($this->data['title']).', 1, \'\', '.time().', 0);';


					$time = time();
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'article\', \'content\', \'Article\', \'\', \'apps/accessories-text-editor\', 1, \'\', 1, '.$time.', 1);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'gallery\', \'content\', \'Gallery\', \'\', \'actions/fileview-preview\', 1, \'\', 1, '.$time.', 2);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'banner\', \'content\', \'Banner\', \'\', \'actions/get-hot-new-stuff\', 1, \'\', 1, '.$time.', 4);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'product\', \'content\', \'Product\', \'\', \'apps/basket\', 1, \'\', 1, '.$time.', 4);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'html\', \'content\', \'HTML code\', \'\', \'apps/preferences-plugin-script\', 1, \'\', 1, '.$time.', 5);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'links\', \'grid\', \'Links\', \'\', \'places/network-wired\', 1, \'\', 1, '.$time.', 6);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'articles\', \'grid\', \'Articles\', \'\', \'actions/kdeprint-testprinter\', 1, \'\', 1, '.$time.', 7);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'entries\', \'category\', \'Entries categories\', \'\', \'places/folder-red\', 1, \'\', 1, '.$time.', 8);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'product\', \'category\', \'Product categories\', \'\', \'places/folder-blue\', 1, \'\', 1, '.$time.', 9);';
					$sql[] = 'INSERT INTO `[[:DB_PREFIX:]][[:PREFIX:]]modules` VALUES (\'\', \'gallery\', \'category\', \'Gallery categories\', \'\', \'places/folder-violet\', 1, \'\', 1, '.$time.', 10);';
					
					foreach ($sql as $s) {
						$s = str_replace($_prefix,$this->data['prefix'],str_replace($db_prefix,DB_PREFIX,$s));
						DB::noerror();
						DB::run($s);
					}	
				}
				
				// add new template
				$templates = strexp(DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\'global\' AND name=\'templates\'','val'));
				if (!is_array($templates)) $templates = array();
				$templates[$this->data['name']] = array(
					$this->data['title'],
					1,
					$this->time
				);
				$data = array(
					'val'		=> strjoin($templates),
					'template'	=> 'global',
					'name'		=> 'templates'
				);
				DB::replace('settings',$data);
				
				$data = array(
					'name'		=> $this->data['name'],
					'engine'	=> $this->data['engine'],
					'title'		=> $this->data['title'],
					'descr'		=> $this->data['descr'],
					'options'	=> serialize($this->data['options']),
					'active'	=> 1,
					'added'		=> $this->time
				);
				DB::replace('templates',$data);
				
				if (!$exists) {
					$new_prefix = rtrim($this->data['prefix'],'_');
					DB::setPrefix(DB_PREFIX.$this->current['PREFIX']);
					if ($this->data['data_from'] && !is_dir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'')) {
						File::copyDirectory(FTP_DIR_ROOT.DIR_FILES.$prefix.'/', FTP_DIR_ROOT.DIR_FILES.$new_prefix.'/');
					}
					if (!is_dir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'/')) {
						mkdir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'',0777);	
					}
					if (!is_dir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'/temp/')) {
						mkdir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'/temp',0777);	
					}
					if (!is_dir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'/email/')) {
						mkdir(FTP_DIR_ROOT.DIR_FILES.$new_prefix.'/email',0777);	
					}	
				}

				if (!is_dir(FTP_DIR_TPLS.$this->data['name']) && FTP_DIR_TPLS.$this->data['files_from']) {
					File::copyDirectory(FTP_DIR_TPLS.$this->data['files_from'], FTP_DIR_TPLS.$this->data['name']);
				}
				$this->msg_text = lang('$Congratulations, %1 template was installed', $this->data['name']);
				$this->msg_reload = false;
				$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=templates\',false,\'templates\');';
			break;
			
			case 'restore':
				$set = Cache::getSmall('db_restore');
				if (isset($set) && isset($set['start']) && $set['start']) {
					$this->json_ret = Factory::call('dbfunc')->restore();
				}							
			break;
			
			case 'install':
			
				$this->allow('templates','add',$this->rs,$this->data);
				$err = array();
				if (!isset($this->data['files_from'])) $this->data['files_from'] = '';
				$this->data['name'] = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1', $this->data['name']);
				$this->data['prefix'] = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1', $this->data['prefix']);
				if ($this->data['prefix']) $this->data['prefix'] = trim($this->data['prefix'],'_').'_';

				if (!strlen(trim($this->data['name']))) {
					$err['name'] = lang('$Template folder name is empty');
				}
				elseif (in_array($this->data['name'], array('editor','temp','users'))) {
					$err['name'] = lang('$Template folder name must be different');
				}
				if (!strlen(trim($this->data['engine']))) {
					$err['engine'] = lang('$Engine is not selected');
				}
				if (!strlen(trim($this->data['prefix']))) {
					$err['prefix'] = lang('$Data prefix is empty');
				}
				elseif ($this->data['data_from'] && DB::one('SELECT 1 FROM '.DB_PREFIX.'settings WHERE name=\'PREFIX\' AND val='.e($this->data['prefix']))) {
					$err['prefix'] = lang('$Same prefix already exists, cannot duplicate data');
				}
				
				if (!strlen(trim($this->data['title']))) {
					$err['title'] = lang('$Title is empty');
				}
				$no_sql = in_array($this->data['prefix'].'menu',DB::tables(true,true));
				if (!$no_sql && !$this->data['sql_file']) {
					$err['sql_file'] = lang('$Select the SQL file');
				}
				$this->errors($err);
				
				
				// add new template
				$templates = strexp(DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\'global\' AND name=\'templates\'','val'));
				if (!is_array($templates)) $templates = array();
				$templates[$this->data['name']] = array(
					$this->data['title'],
					1,
					$this->time
				);
				$data = array(
					'val'		=> strjoin($templates),
					'template'	=> 'global',
					'name'		=> 'templates'
				);
				DB::replace('settings',$data);
				
				$current = DB::getAll('SELECT name,val FROM '.DB_PREFIX.'settings WHERE template='.e($this->tpl),'name|val');
				$current['PREFIX'] = $this->data['prefix'];
				foreach ($current as $k => $v) {
					$data = array(
						'template'	=> $this->data['name'],
						'name'		=> $k,
						'val'		=> $v
					);
					DB::replace('settings',$data);
				}
				$data = array(
					'name'		=> $this->data['name'],
					'engine'	=> $this->data['engine'],
					'title'		=> $this->data['title'],
					'descr'		=> $this->data['descr'],
					'active'	=> 1,
					'added'		=> $this->time
				);
				DB::replace('templates',$data);
				
				if ($no_sql) {
					$this->msg_reload = false;
					$this->msg_text = lang('$Template %1 has been added',$this->data['title']);

					break;
				}
				
				
				ignore_user_abort(true);
				set_time_limit(3600);
				DB::resetCache();
				
				$this->allow('db','restore',$this->rs,$this->data);
				$db = Factory::call('dbfunc');
				$db->prefix = $this->data['prefix'];
				$db->filename = $this->data['sql_file'];
				$db->ext = ext($this->data['sql_file']);
				$db->template = $this->data['name'];
				$db->db_name = DB_NAME;
				$db->restore_template = true;
				$this->json_ret = $db->restore();
				
			break;
			case 'prefix_hint':
				$tables = DB::tables(true,true);
				$prefix = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1',trim(post('prefix'),'_').'_');
				$this->json_ret = array('has_tables' => in_array($prefix.'menu',$tables),'prefix'=>$prefix);
			break;
		}
	}
	
	public function listing() {
		$qry = DB::qry('SELECT name, title, active, added FROM '.DB_PREFIX.'templates ORDER BY sort,name',0,0);
		$data = array();
		while ($rs = DB::fetch($qry)) {
			if (!is_dir(FTP_DIR_TPLS.$rs['name'])) continue;
			if (!is_dir(FTP_DIR_TPLS.$rs['name'].'/')) continue;
			$rs['added'] = date('H:i d.m.Y', $rs['added']);
			$data[$rs['name']] = $rs;
		}
		
		
		$skip = array('admin','css','js','img');
		$dh = opendir(FTP_DIR_TPLS);
		$this->tables = DB::tables(false, true);
		$data2 = array();
		while ($file = readdir($dh)) {
			if ($file=='.' || $file=='..' || !is_dir(FTP_DIR_TPLS.$file) || in_array($file, $skip)) continue;
			if (!is_dir(FTP_DIR_TPLS.$file.'/js/') || !is_dir(FTP_DIR_TPLS.$file.'/css/') || !is_dir(FTP_DIR_TPLS.$file.'/user/') || !is_dir(FTP_DIR_TPLS.$file.'/temp/')) continue;
			if (!array_key_exists($file,$data)) {
				
				$data2[$file] = array(
					'name'	=> $file,
					'title'	=> $file,
					'to_install'=> true,
					'active'=> 0,
					'added'	=> date('H:i d.m.Y', filemtime(FTP_DIR_TPLS.$file.'/'))
				);
			}
		}
		closedir($dh);
		ksort($data2);
		
		$this->json_data = json_encode($data + $data2);
	}
	
	public function window() {
		if (get('create')) {
			$this->templates['default'] = array(
				0 => 'Blank'
			);
			$this->win('settings_templates_create');
		}
		elseif (get('uninstall')) {
			
			$this->win('settings_templates_uninstall');
		}
		elseif (get('install')) {
			$this->post = $_POST;
			$this->post['prefix'] = $this->post['name'].'_';
			$files = File::dir(FTP_DIR_TPLS.$this->post['name'].'/backup/','/\.(sql|gz)$/','time_DESC');
			
			$this->params['sql_files'] = array_label($files,'[[:VALUE:]]');
			$tables = DB::tables(true,true);
			$this->params['preview'] = (is_file(FTP_DIR_TPLS.$this->post['name'].'/preview.jpg') ? HTTP_DIR_TPLS.$this->post['name'].'/preview.jpg' : false);
			if ($this->params['preview']) {
				$this->params['preview_th']	= HTTP_DIR_TPLS.$this->post['name'].'/preview_th.jpg'; 
			}
			/*
			if ($files) {
				$c = preg_match_all('/\nCREATE TABLE `(\[\[:DB_PREFIX:\]\])?([^_]+)_menu`/',file_get_contents(FTP_DIR_TPLS.$this->post['name'].'/backup/'.$files[0]),$m);
				$this->params['prefixes'] = $m[2];
			}
			*/
			$this->params['has_tables'] = in_array($this->post['prefix'].'menu',$tables);
			
			$this->win('settings_templates_install');
		}
		else {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.'templates WHERE name='.e($this->id));
			$this->post['prefix'] = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template='.e($this->id).' AND name=\'PREFIX\'','val');
			$this->win('settings_templates');
		}
	}
}

class AdminSettings_currencies extends Admin {
	public function __construct() {
		$this->title = 'Currencies management';
		parent::__construct(__CLASS__);
	}
	public static function updateCurrencyRates($tpl = TEMPLATE) {
		$data = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'currencies\' AND template='.e($tpl),'val');
		if ($data) $currencies = strexp($data);
		else $currencies = Site::getCurrencies();
		if (!$currencies) $currencies = array();
		$url = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
		$d = File::url($url);
		$arr = File::_xml2array($d);
		$arr = $arr['gesmes:Envelope']['Cube']['Cube']['Cube'];
		$ret = array(
			'EUR' => array(1, 'Euro','€ %1', 1), // 0128
		);
		foreach ($arr as $a) {
			if (@$a['currency']) {
				if (isset($currencies[$a['currency']])) {
					$ret[$a['currency']] = array(
						$a['rate'], $currencies[$a['currency']][1], $currencies[$a['currency']][2], $currencies[$a['currency']][3], $currencies[$a['currency']][4]
					);
				} else {
					$ret[$a['currency']] = array(
						$a['rate'], $a['currency'], '%1 '.$a['currency'], 1, $a['rate']
					);
				}
			}
		}
		Conf()->s('currencies',$ret);
		DB::run('UPDATE '.DB_PREFIX.'settings SET val='.e(strjoin($ret)).' WHERE name=\'currencies\'');
	}
	protected function action($action = false) {
		switch ($this->action) {
			case 'act':
				$this->allow('currencies','activate');
				$currencies = strexp(DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->template.'\' AND name=\'currencies\'','val'));
				$active = $currencies[$this->id][3];
				$currencies[$this->id][3] = ($active ? 0 : 1);
				$sql = 'UPDATE '.DB_PREFIX.'settings SET val='.e(strjoin($currencies)).' WHERE template='.e($this->template).' AND name=\'currencies\'';
				DB::run($sql);
			//	$this->msg_text = lang('$%1 currency was '.($active ? 'deactivated' : 'activated'),$currencies[$this->id][1]);
				$this->msg_reload = false;
				$this->msg_close = false;
			break;	
			case 'sort':
				$currencies = strexp(DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE template=\''.$this->template.'\' AND name=\'currencies\'','val'));
				$sort = post('sort');
				$ex = explode('|',trim($sort,'|'));
				$_currencies = array();
				foreach ($ex as $e) {
					list(,$cur,) = explode('-',$e);
					$_currencies[$cur] = $currencies[$cur];
				}
				$sql = 'UPDATE '.DB_PREFIX.'settings SET val='.e(strjoin($_currencies)).' WHERE template='.e($this->template).' AND name=\'currencies\'';
				DB::run($sql);
			break;
			/*
			case 'update_rates':
				Data::updateCurrencyRates($this->tpl);
				$this->msg_reload = true;
			break;
			*/
		}
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
		}
	}
	public function listing() {
		if (get('do')=='update_rates') {
			self::updateCurrencyRates($this->tpl);
		}
		$data = DB::row('SELECT val FROM '.DB_PREFIX.'settings WHERE name=\'currencies\' AND template='.e($this->tpl),'val');
		if ($data) $data = strexp($data);
		if (!$data) $data = Site::getCurrencies();
		if ($this->submitted) {
			$this->allow('currencies','save');
			$data = array();
			foreach ($this->data as $cur => $a) {
				if ($this->action=='uninstall' && $this->data['uninstall'] && $this->data['uninstall']==$cur && $this->data['uninstall']!=$this->current['DEFAULT_CURRENCY']) {
					continue;
				}
				if (!isset($a[1]) || !$a[1]) continue;
				if ($cur=='uninstall') continue;
				if ($cur=='new') {
					if ($a[9]) {
						$a[3] = 1;
						$data[$a[9]] = $a;
					} 
				} elseif (is_array($this->currencies)) {
					$data[$cur] = $a;
					$data[$cur][3] = $this->currencies[$cur][3];
				}
			}
			if ($data) {
				if ($this->tpl==TEMPLATE) Site::getCurrencies();
				DB::replace('settings',array(
					'val'	=> strjoin($data),
					'name'	=> 'currencies',
					'template'	=> $this->tpl
				));
			}
		}
		ksort($data);
		$this->json_data = json($data);
	}
}

class AdminSettings_qry extends Admin {
	public function __construct() {
		$this->title = 'Execute custom SQL query';
		parent::__construct(__CLASS__);
		$this->db_name = request('db_name','',DB_NAME);
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'columns':
				$table = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1',post('table'));
				$ret = array('');
				$qry = DB::qry('SHOW COLUMNS FROM `'.$table.'`',0,0);
				while ($c = DB::fetch($qry)) array_push($ret,$c['Field']);
				$this->json_ret = $ret;
			break;
		}
	}
	
	private static function _sql_allowed($q) {
		if (preg_match('@(^|\s)(ALTER|CREATE|LOAD)[[:space:]]+@si', $q)) {
			return false;
		}
		if (preg_match('@(^|\s)(DELETE|DROP|TRUNCATE)[[:space:]]+@si', $q)) {
			return false;
		}
		if (preg_match('@(^|\s)(INSERT|LOAD[[:space:]]+DATA|REPLACE)[[:space:]]+@si', $q)) {
			return false;
		}
		if (preg_match('@(^|\s)(INSERT|UPDATE|REPLACE)[[:space:]]+@si', $q)) {
			return false;	
		}
		return true;
	}
	
	
	public function sql_allowed($q) {
		if ($this->UserID==SUPER_ADMIN) return true;
		if (preg_match('@(^|\s)DROP[[:space:]]+DATABASE[[:space:]]+@si', $s)) {
			return false;
		}
		if (preg_match('/delete\s+from\s+`?log`?/is',$q)) {
			return false;	
		}
		if (preg_match('/truncate\s+table\s+`?log`?/is',$q)) {
			return false;	
		}
		if (preg_match('/update\s+`?log`?/is',$q)) {
			return false;	
		}
		if (preg_match('/alter\s+table\s+`?log`?/is',$q)) {
			return false;	
		}
		if (preg_match('/insert\s+into\s+`?log`?/is',$q)) {
			return false;
		}
		if (ADMIN_QRY_HIGH_SECURITY && !self::_sql_allowed($q)) {
			return false;	
		}
		return true;
	}
	

	public function listing() {

		if ($this->submitted) {
			switch ($this->action) {
				case 'qry':
					if (!strlen(trim($this->data['qry']))) break;
					$this->allow('qry','run',$this->rs,$this->data);
					if (!$this->sql_allowed($this->data['qry'])) {
						$this->output = 'You not allowed to run this query';
						break;
					}					
					$GLOBALS['__p_debug_b_been'] = true;
					require FTP_DIR_ROOT.'inc/DBfunc.php';
					Conf()->s('SQL_RESULT',array());
					Conf()->s('DB_MSG','');
					$er = error_reporting();
					error_reporting(0);
					ob_start();
					if ($this->db_name!=DB_NAME) {
						DB::link()->select_db($this->db_name);
					}
					Conf()->s('debug_seen_i',true);
					$GLOBALS['__p_debug_b_been'] = false;
					
					DBfunc::parseSqlString(rtrim($this->data['qry'],';').';', true);
					$err = DB::errorMSG();
					$c = ob_get_contents();
					ob_end_clean();
					error_reporting($er);
					$data = '';
					DB::link()->select_db(DB_NAME);

					if (Conf()->g('SQL_RESULT')) {
						foreach (Conf()->g('SQL_RESULT') as $sql) {
							$data .= Message::sql($sql,true,true);
						}
						Conf()->s('SQL_RESULT',NULL);
					}
					$data .= Conf()->g('DB_MSG');
					if ($err) {
						$data .= '<span style="color:#CC0000;font-family:\'Lucida Console\';">'.$err.'</span>';	
					}
					Conf()->s('DB_MSG',NULL);
					$this->output = $data;
					
					if ($this->data['qry']) {
						$ser = serialize(array('sql'=>$this->data['qry'],'title'=>($err?'<span style="color:red">[Error]</span> ':'').Parser::geshiHighlight($this->data['qry'],'mysql')));
						$prev = DB::one('SELECT `data` FROM `'.DB_PREFIX.'log` WHERE `table`=\'SQL\' ORDER BY id DESC');
						if (strlen($this->data['qry'])<1000 && md5($prev)!=md5($ser)) {
							DB::insert('log',array(
								'setid'		=> 0,
								'table'		=> 'SQL',
								'action'	=> ($err ? Site::ACTION_ERROR : Site::ACTION_UNKNOWN),
								'template'	=> $this->tpl,
								'title'		=> $this->data['qry'],
								'changes'	=> 0,
								'userid'	=> $this->Index->Session->UserID,
								'added'		=> $this->time,
								'data'		=> $ser
							));
						}
					}

				break;
				case 'export_table':
					$db = Factory::call('dbfunc');
					$db->prefix = $this->prefix;
					$db->db_name = $this->db_name;
					$sql = trim($db->exportTable($this->data['table'],false));
					DB::link()->select_db(DB_NAME);
					$this->output = Message::sql(DBfunc::prefix($sql), true, true, true);
				break;
				case 'clear':
					$this->data = array();
				break;
			}
		}
		$this->params['db']['databases'] = DB::getAll('SHOW DATABASES','Database');
		
		$data = array();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, data, title, added, userid, `data`, action, (SELECT login FROM '.DB_PREFIX.'users WHERE '.DB_PREFIX.'users.id='.DB_PREFIX.'log.userid) AS user FROM `'.DB_PREFIX.'log` WHERE `table`=\'SQL\' ORDER BY id DESC';
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			$rs['added'] = date('d M Y, H:i',$rs['added']);
			$s = unserialize($rs['data']);
			$rs['sql'] = $s['sql'];
			$rs['title'] = $s['title'];
			unset($rs['data'],$s);
			array_push($data,$rs);
		}
		$this->json_data = json($data);
		DB::free($qry);
		$this->nav();
	}
}



class AdminSettings_eval extends Admin {
	public function __construct() {
		$this->title = 'Execute custom PHP code';
		parent::__construct(__CLASS__);
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
		}
	}

	public function listing() {
		if ($this->submitted) {
			switch ($this->action) {
				case 'eval':
					$this->allow('eval','run',$this->rs,$this->data);
					if ($ok = self::filePHPok('<?php '.$this->data['eval'])) {				
						ob_start();
						$GLOBALS['__p_debug_b_been'] = true;
						eval($this->data['eval'].';');
						$GLOBALS['__p_debug_b_been'] = false;
						$this->output = ob_get_contents();
						ob_end_clean();
					} else {
						$this->output = Conf()->g('phpERROR');
					}
					if ($this->data['eval']) {
						$ser = serialize(array('php'=>$this->data['eval'],'title'=>(!$ok?'<span style="color:red">[Error]</span> ':'').Parser::geshiHighlight(Parser::strTabs($this->data['eval']),'php')));
						$prev = DB::one('SELECT `data` FROM `'.DB_PREFIX.'log` WHERE `table`=\'PHP\' ORDER BY id DESC');
						if (md5($prev)!=md5($ser)) {
							DB::insert('log',array(
								'setid'		=> 0,
								'table'		=> 'PHP',
								'action'	=> (!$ok ? Site::ACTION_ERROR : Site::ACTION_UNKNOWN),
								'template'	=> $this->tpl,
								'title'		=> $this->data['eval'],
								'changes'	=> 0,
								'userid'	=> $this->Index->Session->UserID,
								'added'		=> $this->time,
								'data'		=> $ser
							));
						}
					}
				break;
			}
		}
		
		
		$data = array();
		$sql = 'SELECT SQL_CALC_FOUND_ROWS id, data, title, added, userid, `data`, action, (SELECT login FROM '.DB_PREFIX.'users WHERE '.DB_PREFIX.'users.id='.DB_PREFIX.'log.userid) AS user FROM `'.DB_PREFIX.'log` WHERE `table`=\'PHP\' ORDER BY id DESC';
		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			$rs['added'] = date('d M Y, H:i',$rs['added']);
			$s = unserialize($rs['data']);
			$rs['php'] = $s['php'];
			$rs['title'] = $s['title'];
			unset($rs['data'],$s);
			array_push($data,$rs);
		}

		$this->json_data = json($data);
		DB::free($qry);
		$this->nav();
	}
}


class AdminSettings_vars extends Admin {
	public function __construct() {
		$this->title = 'Variables management';
		parent::__construct(__CLASS__);
	}
	public function sql() {
		if ($this->find) {
			$this->filter .= ' AND (name LIKE  \'%'.$this->find.'%\' OR val_'.$this->lang.' LIKE \'%'.$this->find.'%\')';
		}
		if ($this->tpl) {
			$this->filter .= ' AND template='.e($this->tpl);
		}
		else {
			$this->filter .= ' AND (template LIKE '.e($this->Index->Session->Template).' OR template=\'\')';
		}		
		$this->order = '`name`';
		$this->limit = 50;
		$this->offset = get('p')*$this->limit;
	}
	public function listing() {
		$this->sql();
		if ($this->submitted) {
			$this->allow('vars','save');
			$sel = '';
			foreach ($this->langs as $l => $a) {
				if ($l==$this->lang) continue;
				$sel .= 'val_'.$l.',';
			}
			foreach (post('data_val','',array()) as $i => $val) {
				$name = post('data_key',$i);
				if (post('data_del',$i)) {
					DB::run('DELETE FROM '.DB_PREFIX.$this->table.' WHERE name='.e($name).' AND template='.e($this->template));
				} else {
					if (!strlen(trim($val))) continue;
					if ($sel) {
						$rs = DB::row('SELECT '.rtrim($sel,',').' FROM '.DB_PREFIX.$this->table.' WHERE name='.e($name).' AND template='.e($this->template));
					}
					$up = array('val_'.$this->lang.'='.e($val));
					foreach ($this->langs as $l => $a) {
						if ($l==$this->lang) continue;
						if (!$rs['val_'.$l]) $up[] = 'val_'.$l.'='.e($val);
					}
					DB::run('UPDATE '.DB_PREFIX.$this->table.' SET '.join(',',$up).' WHERE name='.e($name).' AND template='.e($this->template));
				}
			}
			foreach (post('data_new_key','',array()) as $i => $name) {
				if (!strlen(trim($name))) continue;
				$val = post('data_new_val',$i);
				if (DB::one('SELECT 1 FROM '.DB_PREFIX.$this->table.' WHERE name LIKE '.e($name).' AND template='.e($this->template))) {
					DB::run('UPDATE '.DB_PREFIX.$this->table.' SET val_'.$this->lang.'='.e($val).' WHERE name='.e($name).' AND template='.e($this->template));
					continue;
				}
				$data = array('template'=>$this->template,'name'=>$name);
				foreach ($this->langs as $l => $a) {
					$data['val_'.$l] = $val;
				}
				DB::insert($this->table, $data);
			}
		}
		$sql = 'SELECT SQL_CALC_FOUND_ROWS name, val_'.$this->lang.' AS val FROM '.DB_PREFIX.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' LIMIT '.$this->offset.','.$this->limit;
		$data = DB::getAll($sql);
		$data = strform($data);
		$this->total = DB::rows($data);
		$this->nav();
		$this->json_data = json($data);
	}
}


class AdminSettings_modules extends Admin {
	
	public function __construct() {
		$this->title = 'Modules management';
		parent::__construct(__CLASS__);
		$this->array['options']['content'] = array(
			'select_list[]'	=> array('select', lang('$Columns to select in list'),'columns',' multiple="multiple" size="6" style="width:35%"',true)
		);
		$this->array['options']['category'] = array(
			'no_icon'		=> array('checkbox', lang('$No icon')),
			'no_catalogue'	=> array('checkbox', lang('$No catalogue')),
		);
		$this->idcol = 'id';
	}
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
			case 'modules_by_type':
				$this->json_ret = array('0' => lang('$-- install default --')) + array_label(Site::getModules(request('type')),'title');
			break;
		}
	}	

	public function sql() {
		if ($this->find) {
			$this->filter .= ' AND (`table` LIKE  \'%'.$this->find.'%\' OR title LIKE \'%'.$this->find.'%\' OR title LIKE \''.$this->find.'%\' OR descr LIKE \'%'.$this->find.'%\')';
		}
		if (get('t')) {
			$this->filter .= ' AND type LIKE '.e(get('t'));	
		}
		$this->filter .= ' AND active!=2';
		$this->order = 'sort, type, `table`';
	}
	public function listing() {
		$this->sql();
		if ($this->submitted) {
			$this->action();	
		}
		$sql = 'SELECT id, `table`, type, title, descr, icon, active FROM '.$this->prefix.$this->table.' WHERE TRUE'.$this->filter.' ORDER BY '.$this->order.' LIMIT '.$this->offset.','.$this->limit;
		$qry = DB::qry($sql, 0, 0);
		$this->data = array();
		$this->exists = array();
		$size = 16;
		while ($rs = DB::fetch($qry)) {
			if (!$rs['icon']) {
				switch ($rs['type']) {
					case 'grid':
						$icon = 'apps/kcmkwm.png';
					break;	
					case 'content':
						$icon = 'categories/applications-office.png';
					break;
					case 'category':
						$icon = 'places/folder-yellow.png';
					break;
				}
				$rs['icon'] = FTP_EXT.'tpls/img/oxygen/'.$size.'x'.$size.'/'.$icon;
			}
			elseif (!strstr($rs['icon'],'://') && is_file(FTP_DIR_TPLS.'img/oxygen/16x16/'.$rs['icon'].'.png')) {
				$rs['icon'] = FTP_EXT.'tpls/img/oxygen/'.$size.'x'.$size.'/'.$rs['icon'].'.png';
			}
			$this->data[] = $rs;
			$this->exists[] = $rs['type'].'_'.$rs['table'];
		}
		$this->findModules();		
		$this->json_data = json($this->data);
	}
	private function findModules() {
		$dir = FTP_DIR_ROOT.'mod/';
		$dh = opendir($dir);
		$i = 0;
		$data = array();
		while ($file = readdir($dh)) {
			if (!is_file($dir.$file)) continue;
			if (substr($file,0,13)=='AdminContent_') {
				$key = substr(substr($file,0,strpos($file,'.')),strpos($file,'_')+1);
				if (in_array('content_'.$key,$this->exists)) continue;
				if ($this->find && !strstr($key,$this->find)) continue;
				$data['_c_'.$key.'_'.++$i] = array(
					'id'	=> 0,
					'table'	=> $key,
					'type'	=> 'content',
					'icon'	=> '/tpls/img/oxygen/16x16/actions/view-pim-news.png',
					'title'	=> ''.ucfirst($key).'',
					'tpl'	=> '',
				);	
			}
			elseif (substr($file,0,10)=='AdminGrid_') {
				$key = substr(substr($file,0,strpos($file,'.')),strpos($file,'_')+1);
				if (in_array('grid_'.$key,$this->exists)) continue;
				if ($this->find && !strstr($key,$this->find)) continue;
				$data['_g_'.$key.'_'.++$i] = array(
					'id'	=> 0,
					'table'	=> $key,
					'type'	=> 'grid',
					'icon'	=> '/tpls/img/oxygen/16x16/actions/view-list-icons.png',
					'title'	=> ''.ucfirst($key).'',
					'tpl'	=> ''
				);	
			}
		}
		closedir($dh);	
		$dir = FTP_DIR_ROOT.'mod/custom/';
		if (!is_dir($dir)) return;
		$dh = opendir($dir);
		while ($file = readdir($dh)) {
			if (!is_file($dir.$file)) continue;
			$ex = explode('_',$file);
			if (count($ex)!=3) continue;
			$tpl = $ex[0];
			$type = $ex[1];
			if (substr($type,0,5)!='Admin') continue;
			$type = strtolower(substr($type,5));
			$key = substr($ex[2],0,strpos($ex[2],'.'));
			if (in_array($type.'_'.$key,$this->exists)) continue;
			if ($this->find && !strstr($key,$this->find)) continue;
			$data[$tpl.'_'.$key.'_'.++$i] = array(
				'id'	=> 0,
				'table'	=> $key,
				'type'	=> $type,
				'icon'	=> '/tpls/img/oxygen/16x16/actions/'.($type=='grid'?'view-list-icons':'view-pim-news').'.png',
				'title'	=> ''.$tpl.' :: '.ucfirst($key).'',
				'tpl'	=> $tpl
			);	
		}
		closedir($dh);	
		ksort($data);
		reset($data);
		$data = array_values($data);
		$this->data = array_merge($this->data, $data);
	}
	private function validate() {
		$err = array();
		if (!$this->data['title']) $err['title'] = lang('$Title must be filled in');
		$this->errors($err);
	}
	protected function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->allow('modules','edit',$this->rs,$this->data);
				$this->validate();
				$options = array();
				if ($this->data['options'] && is_array($this->data['options'])) {
					foreach ($this->data['options'] as $key => $val) {
						if (is_array($val)) $val = join(',',$val);
						$options[] = $key.':'.$val;
					}
					$this->data['options'] = join(' |;',$options);
				} else {
					$this->data['options'] = '';	
				}
				$this->global_action('save');
				$this->global_action('msg');
				DB::clearCache();
				$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=modules\',false,\'modules\');';
				$this->msg_reload = false;
			break;
			case 'act':
				$this->module = '';
				$this->allow('modules','activate',$this->rs,$this->data);
				$this->global_action();
				$name = DB::row('SELECT CONCAT(type,\' :: \',title) AS title FROM '.$this->prefix.'modules WHERE id='.(int)$this->id,'title');
				$this->msg_text = lang('$%1 module was '.($this->active ? 'deactivated' : 'activated'),$name);
				$this->msg_reload = false;
				$this->msg_close = false;
			break;
			case 'sort':
				$this->allow('modules','sort',$this->rs,$this->data);
				$this->global_action();
			break;
			case 'uninstall':
				$type = post('type');
				$table = post('table');
				if (!$type || !$table) break;
				$this->allow('modules','delete',$this->rs,$type.'_'.$table);
				if (NO_DELETE) {
					foreach (DB::getPrefixes() as $prefix) {
						if (DB_PREFIX.$prefix!=$this->prefix) continue;
						DB::run('UPDATE `'.DB_PREFIX.$prefix.'modules` SET `active`=2 WHERE `table`='.e($table).' AND `type`='.e($type));
					}
				} else {
					foreach (DB::getPrefixes() as $prefix) {
						if (DB_PREFIX.$prefix!=$this->prefix) continue;
						DB::run('DROP TABLE IF EXISTS '.DB_PREFIX.$prefix.$type.'_'.$table);
						DB::run('DELETE FROM `'.DB_PREFIX.$prefix.'modules` WHERE `table`='.e($table).' AND `type`='.e($type));
					}
				}
				
				if (is_file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$type.'_'.$table.'_window.php')) {
					$lines = count(file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$type.'_'.$table.'_window.php'));
					if ($lines<5) unlink(FTP_DIR_TPLS.$this->tpl.'/admin/'.$type.'_'.$table.'_window.php');
				}
				elseif (is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$type.'_'.$table.'_window.php')) {
					$lines = count(file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$type.'_'.$table.'_window.php'));
					if ($lines<5) unlink(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$type.'_'.$table.'_window.php');
				}
				elseif (is_file(FTP_DIR_TPLS.'admin/'.$type.'_'.$table.'_window.php')) {
					$lines = count(file(FTP_DIR_TPLS.'admin/'.$type.'_'.$table.'_window.php'));
					if ($lines<5) unlink(FTP_DIR_TPLS.'admin/'.$type.'_'.$table.'_window.php');
				}
				
				if (is_file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$type.'_'.$table.'.php')) {
					$lines = count(file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$type.'_'.$table.'.php'));
					if ($lines<5) unlink(FTP_DIR_TPLS.$this->tpl.'/admin/'.$type.'_'.$table.'.php');
				}
				elseif (is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$type.'_'.$table.'.php')) {
					$lines = count(file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$type.'_'.$table.'.php'));
					if ($lines<5) unlink(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$type.'_'.$table.'.php');
				}
				elseif (is_file(FTP_DIR_TPLS.'admin/'.$type.'_'.$table.'.php')) {
					$lines = count(file(FTP_DIR_TPLS.'admin/'.$type.'_'.$table.'.php'));
					if ($lines<5) unlink(FTP_DIR_TPLS.'admin/'.$type.'_'.$table.'.php');
				}
				
				DB::clearCache();
				$this->msg_reload = false;
				$this->msg_close = false;
				$this->msg_text = lang('$%1 module was uninstalled',$type.'_'.$table);
				$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=modules\',false,\'modules\');';
			break;
			case 'create':
				$this->allow('modules','save',$this->rs,$this->data);
				$err = array();
				$this->data['name'] = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1', $this->data['name']);
				if (!strlen(trim($this->data['type']))) {
					$err['type'] = lang('$Please select a type');
				}
				if (!strlen(trim($this->data['title']))) {
					$err['title'] = lang('$Title is empty');
				}
				if (!strlen(trim($this->data['name']))) {
					$err['name'] = lang('$Name is empty');
				}
				elseif (($modules = Site::getModules($this->data['type'])) && array_key_exists($this->data['name'], $modules)) {
					$err['name'] = lang('$Such module name already exists');
				}
				$this->errors($err);
				$data = array(
					'table'	=> $this->data['name'],
					'type'	=> $this->data['type'],
					'title'	=> $this->data['title'],
					'descr'	=> $this->data['descr'],
					'icon'	=> $this->data['icon'],
					'userid'=> $this->Index->Session->UserID,
					'added'	=> $this->time,
					'edited'=> $this->time
				);	
				if ($this->data['copy_from']) {
					require FTP_DIR_ROOT.'inc/DBfunc.php';
				}
				
				$db = Factory::call('dbfunc');
				$db->prefix = $this->prefix;
				$db->db_name = DB_NAME;
				
				foreach (DB::getPrefixes() as $prefix) {
					if (DB_PREFIX.$prefix!=$this->prefix) continue;
					$data['active'] = 1;
					if ($this->data['copy_from']) {
						$data['options'] = DB::row('SELECT options FROM '.DB_PREFIX.$prefix.'modules WHERE `type`='.e($this->data['type']).' AND `table`='.e($this->data['copy_from']),'options');
					}
					DB::setPrefix($prefix);
					DB::noerror();
					DB::replace('modules',$data);
					if ($this->data['copy_from']) {
						$table = $prefix.$this->data['type'].'_'.$this->data['copy_from'];
						$copy = $prefix.$this->data['type'].'_'.$this->data['name'];
						$db->prefix = $prefix;
						$db->exportTable($table,$incData,'','utf8',true,$exclude,$copy);
						
						if (is_file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['copy_from'].'_window.php')
						&& !is_file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['name'].'_window.php')) {
							file_put_contents(FTP_DIR_TPLS.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['name'].'_window.php','<?php
include (FTP_DIR_TPLS.\''.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['copy_from'].'_window.php\');
?>');
						}
						elseif (is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['copy_from'].'_window.php')
						&& !is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['name'].'_window.php')) {
							file_put_contents(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['name'].'_window.php','<?php
include (FTP_DIR_TPLS.\''.$this->tpl.'/admin/custom/'.$this->data['type'].'_'.$this->data['copy_from'].'_window.php\');
?>');
						}
						elseif (is_file(FTP_DIR_TPLS.'admin/'.$this->data['type'].'_'.$this->data['copy_from'].'_window.php')
						&& !is_file(FTP_DIR_TPLS.'admin/'.$this->data['type'].'_'.$this->data['name'].'_window.php')) {
							file_put_contents(FTP_DIR_TPLS.'admin/'.$this->data['type'].'_'.$this->data['name'].'_window.php','<?php
include (FTP_DIR_TPLS.\'admin/'.$this->data['type'].'_'.$this->data['copy_from'].'_window.php\');
?>');
						}
						
						if (is_file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['copy_from'].'.php')
						&& !is_file(FTP_DIR_TPLS.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['name'].'.php')) {
							file_put_contents(FTP_DIR_TPLS.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['name'].'.php','<?php
include (FTP_DIR_TPLS.\''.$this->tpl.'/admin/'.$this->data['type'].'_'.$this->data['copy_from'].'.php\');
?>');
						}
						elseif (is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['copy_from'].'.php')
						&& !is_file(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['name'].'.php')) {
							file_put_contents(FTP_DIR_TPLS.'admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['name'].'.php','<?php
include (FTP_DIR_TPLS.\''.$this->tpl.'/admin/custom/'.$this->data['type'].'_'.$this->data['copy_from'].'.php\');
?>');
						}
						elseif (is_file(FTP_DIR_TPLS.'admin/'.$this->data['type'].'_'.$this->data['copy_from'].'.php')
						&& !is_file(FTP_DIR_TPLS.'admin/'.$this->data['type'].'_'.$this->data['name'].'.php')) {
							file_put_contents(FTP_DIR_TPLS.'admin/'.$this->data['type'].'_'.$this->data['name'].'.php','<?php
include (FTP_DIR_TPLS.\'admin/'.$this->data['type'].'_'.$this->data['copy_from'].'.php\');
?>');
						}
						
					}
					elseif (!in_array($this->data['type'].'_'.$this->data['name'], DB::tables())) {
						$table = $this->prefix.$this->data['type'].'_'.$this->data['name'];
						switch ($this->data['type']) {
							case 'category':
								$sql = 'CREATE TABLE `'.$table.'` (
  `catid` int(11) NOT NULL auto_increment,
  `catref` varchar(140) NOT NULL,';
  foreach ($this->langs as $l => $a) {
	  $sql .= '  `catname_'.$l.'` varchar(255) default NULL,
  `cnt_'.$l.'` int(5) NOT NULL default \'0\',';
  }
  $sql .= '
  `name` varchar(200) NOT NULL,
  `hidden` enum(\'0\',\'1\') default \'0\',
  `sort` int(4) default \'0\',
  `icon` varchar(255) default NULL,
  PRIMARY KEY  (`catid`),
  KEY `catref` (`catref`),
  KEY `hidden` (`hidden`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								DB::run($sql);
							break;
							case 'content':
								$sql = 'CREATE TABLE `'.$table.'` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `rid` int(11) NOT NULL default \'0\',
  `setid` int(11) unsigned NOT NULL default \'0\',
  `lang` varchar(2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `body` mediumblob NOT NULL,
  `edited` int(10) unsigned NOT NULL default \'0\',
  `added` int(10) NOT NULL default \'0\',
  `userid` int(8) unsigned NOT NULL default \'0\',
  `main_photo` varchar(255) NOT NULL,
  `bodylist` enum(\'1\',\'0\') default \'0\',
  `active` tinyint(1) NOT NULL,
  `sort` tinyint(3) NOT NULL,
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`),
  KEY `rid` (`rid`,`setid`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								DB::run($sql);
							break;
							case 'grid':
								$sql = 'CREATE TABLE `'.$table.'` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `added` int(10) NOT NULL default \'0\',
  `userid` int(8) unsigned NOT NULL default \'0\',
  `edited` int(10) NOT NULL default \'0\',
  `editor` int(8) unsigned NOT NULL default \'0\',
  `active` tinyint(1) NOT NULL,
  `statuser` int(8) unsigned NOT NULL default \'0\',
  `statused` int(10) unsigned NOT NULL default \'0\',
  `is_admin` enum(\'0\',\'1\') NOT NULL default \'0\',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';
								DB::run($sql);
							break;
						}
					}
				}
				DB::setPrefix($this->prefix);
				DB::clearCache();
				$this->msg_text = lang('$Congratulations, %1 module was created', $this->data['name']);
				$this->msg_reload = false;
				$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=modules\',false,\'modules\');';
			break;
			case 'install':
				if ($this->data['tpl']) {
					$file = FTP_DIR_ROOT.'mod/custom/'.$this->data['tpl'].'_Admin'.ucfirst($this->data['type']).'_'.$this->data['table'].'.php';
				} else {
					$file = FTP_DIR_ROOT.'mod/Admin'.ucfirst($this->data['type']).'_'.$this->data['table'].'.php';	
				}
				$class = 'Admin'.ucfirst($this->data['type']).'_'.$this->data['table'];
				$this->msg_reload = false;
				$this->msg_text = lang('$Something went wrong..');
				$this->msg_type = 'error';
				
				if (is_file($file)) {
					if ($this->data['type']=='grid' && !class_exists('AdminGrid')) {
						require FTP_DIR_ROOT.'mod/AdminGrid.php';
					}
					require_once $file;
					if (class_exists($class)) {
						$dummy = new $class;
						if (method_exists($dummy, 'install')) {
							DB::noerror();
							$id = $dummy->install();
							if ($id) {
								DB::run('UPDATE '.$this->prefix.'modules SET userid='.$this->Index->Session->UserID.' WHERE id='.$id);
								$this->msg_type = 'tick';
								$this->msg_text = lang('$Congratulations, %1 module was installed', $this->data['type'].' '.$this->data['table']);
								$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=modules\',false,\'modules\');';
							} else {
								$this->msg_type = 'tick';
								$this->msg_text = lang('$Congratulations, %1 module was probably installed', $this->data['type'].' '.$this->data['table']);
								$this->msg_js = 'S.A.L.get(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_LOAD.'&'.self::KEY_TAB.'=modules\',false,\'modules\');';	
							}
							unset($dummy);
							if ($this->data['tpl']) {
								copy($file, FTP_DIR_ROOT.'mod/custom/'.$this->tpl.'_Admin'.ucfirst($this->data['type']).'_'.$this->data['table'].'.php');
							}
							if (!is_file(FTP_DIR_ROOT.'tpls/admin/'.$this->data['type'].'_'.$this->data['table'].'_window.php')) {
								if ($this->data['tpl']) {
									if (is_file(FTP_DIR_ROOT.'tpls/admin/custom/'.$this->data['tpl'].'_'.$this->data['type'].'_'.$this->data['table'].'_window.php') && !is_file(FTP_DIR_ROOT.'tpls/admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['table'].'_window.php')) {
										copy(
											FTP_DIR_ROOT.'tpls/admin/custom/'.$this->data['tpl'].'_'.$this->data['type'].'_'.$this->data['table'].'_window.php',
											FTP_DIR_ROOT.'tpls/admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['table'].'_window.php'
										);
									}
								}
								if (!is_file(FTP_DIR_ROOT.'tpls/admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['table'].'_window.php') && !is_file(FTP_DIR_ROOT.'tpls/admin/custom/'.$this->data['type'].'_'.$this->data['table'].'_window.php')) {
									$dh = opendir(FTP_DIR_ROOT.'tpls/admin/custom/');
									while ($file = readdir($dh)) {
										if ($file=='.' || $file=='..') continue;
										$ex = explode($this->data['type'].'_'.$this->data['table'].'_window.php',$file);
										if (isset($ex[1])) {
											$ex = explode('_',$file);
											copy(
												FTP_DIR_ROOT.'tpls/admin/custom/'.$ex[0].'_'.$this->data['type'].'_'.$this->data['table'].'_window.php',
												FTP_DIR_ROOT.'tpls/admin/custom/'.$this->tpl.'_'.$this->data['type'].'_'.$this->data['table'].'_window.php'
											);
											break;	
										}
									}
									closedir($dh);
								}
							}
						}
					}
				}
			break;
		}
	}
	public function window() {
		if (get('create')) {
			$this->win('settings_modules_create');
		}
		elseif (get('install')) {
			
			if (post('tpl')) {
				$file = FTP_DIR_ROOT.'mod/custom/'.post('tpl').'_Admin'.ucfirst(post('type')).'_'.post('table').'.php';	
			} else {
				$file = FTP_DIR_ROOT.'mod/Admin'.ucfirst(post('type')).'_'.post('table').'.php';		
			}
			$class = 'Admin'.ucfirst(post('type')).'_'.post('table').'';
			$msg = array();
			$err = false;
			if (is_file($file)) {
				if (post('type')=='grid' && !class_exists('AdminGrid')) {
					require FTP_DIR_ROOT.'mod/AdminGrid.php';
				}
				
				$msg[] = '<span style="color:green">Class file: '.$file.' exists.</span>';
				require_once $file;
				if (class_exists($class)) {
					$msg[] = '<span style="color:green">Class '.$class.' exists.';
					$dummy = new $class;
					if (method_exists($dummy, 'install')) {
						$msg[] = '<span style="color:green">Install() method in class '.$class.' exists.</span>';
					} else {
						$msg[] = '<span style="color:red">Install() method in class '.$class.' does not exists.</span>';
						$err = true;
					}
					unset($dummy);
				} else {
					$msg[] = '<span style="color:red">Class '.$class.' does not exists.</span>';
					$err = true;
				}
			} else {
				$msg[] = '<span style="color:red">Error, file: '.$file.' does not exists.</span>';
				$err = true;
				
			}
			if ($err) {
				$msg[] = '<span style="color:maroon;font-weight:bold">Installation will not continue.</span>';	
			} else {
				$msg[] = '<span style="color:#006633;font-weight:bold">You may install this module now.</span>';		
			}
			$this->info = '<ol><li>'.join('</li><li>',$msg).'</li></ol>';
			$this->allow_install = !$err;
			
			$this->win('settings_modules_install');
		} else {
			if ($this->id && $this->id!=='new') {
				$this->post = DB::row('SELECT * FROM '.$this->prefix.$this->table.' WHERE id='.$this->id);
				if (!$this->post) $this->id = 0;
				else {
					$options = explode(' |;',$this->post['options']);
					$this->post['options'] = array();
					foreach ($options as $o) {
						$o = trim($o);
						$pos = strpos($o,':');
						$key = substr($o, 0, $pos);
						$val = substr($o,$pos+1);
						$this->post['options'][$key] = $val;
					}
					$this->post['username'] = Data::user($this->post['userid'], 'login');
					if (!$this->post['username']) $this->post['username'] = 'unknown user';
				}
				$this->array['array']['columns'] = DB::columns($this->post['type'].'_'.$this->post['table']);
			}
			$this->win('settings_modules');
		}
	}
}
