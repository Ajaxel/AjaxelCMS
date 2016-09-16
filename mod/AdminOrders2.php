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
* @file       mod/AdminOrders2.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class AdminOrders2 extends Admin {
	
	private $Order;
	
	public function __construct() {		
		$this->title = 'Orders';
		parent::__construct(__CLASS__);
		$this->table2 = $this->table.'_map';
		$this->array['time_status'] = array('sent','paid','accepted','cancelled','refunded');
		if (!isset($_GET['order_status'])) $_GET['order_status'] = self::KEY_ALL;
	//	$this->Order = Factory::call('order');
	}
	
	public function init() {
		$this->image_sizes = false;
		$this->has_multi_photo = true;
		$this->table = $this->name;
		$this->id = (int)$this->id;
		$this->idcol = 'id';
	}
	
	public function json() {
		$arr = array();
		switch ($this->get) {
			case 'columns':
				$this->json_ret = $this->columns_find(post('table'), true);
			break;
			case 'action':
				$this->action();
			break;
			case 'save_images':
				$this->global_action('save_image_multi');
				$this->json_ret = array('images' => $this->filesToJson());
			break;
			
		}
	}
	
	private function validate() {
		$err = array();
		$this->set_msg(lang('$New order ID: %1 was added',$this->id), lang('Order ID:%1 was updated',$this->id));
		$this->errors($err);
	}
	
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'product':
				$term = post('term');
				$table = post('table','','[[:CACHE:]]');
				$column = post('column','','[[:CACHE:]]');
				if (!$term || !$table || !$column) {
					$this->json_ret = false;
					break;	
				}
				$columns = array_keys($this->columns_find($table));
				if (!in_array($column, $columns)) {
					break;	
				}
				$sql = 'SELECT `'.join('`, `',$columns).'` FROM '.DB::prefix($table).' WHERE `'.$column.'` LIKE '.e($term.'%').' ORDER BY `'.$column.'`';
				$qry = DB::qry($sql,0,20);
				$data = array();
				$has_title = in_array('title',$columns);
				while ($rs = DB::fetch($qry)) {
					$rs['value'] = $rs['id'];
					$rs['label'] = $rs[$column];
					if (!empty($rs['options'])) {
						$rs['options'] = unserialize($rs['options']);
					}
					array_push($data,$rs);
				}
				$this->json_ret = $data;
			break;
			case 'my':
				$this->Index->My->admin($this);
			break;
			case 'print':
				$this->data = post('mail');
				if (!$this->data['subject']) $err['subject'] = lang('$Subject is empty');
				if (!$this->data['message']) $err['message'] = lang('$Message is empty');
				$this->errors($err);
				$_SESSION['mail_tpl'] = array(
					'subject' => $this->data['subject'],
					'message' => $this->data['message'],
				);
				$this->msg_reload = false;
				$this->msg_js = 'window.open(\'?print&'.URL_KEY_ADMIN.'='.$this->name.'&print='.join(',',array_numeric(post('ids'))).'\');';
			break;
			case 'send':
				$this->msg_reload = false;
				$err = array();
				$this->allow('email','send');
				$this->data = post('mail');
				if (!count(post('ids')) && !$this->data['all']) $err['ids'] = lang('$No orders were selected');
				if (!$this->data['subject']) $err['subject'] = lang('$Subject is empty');
				if (!$this->data['message']) $err['message'] = lang('$Message is empty');
				$this->errors($err);
				
				$sql = $_SESSION['mail_sql'];
				if ($this->data['all']) {
					
				}
				elseif (post('ids')) {
					$sql .= ' AND id IN ('.join(', ',array_numeric(post('ids'))).')';	
				}
				$set = array();
				$total = DB::getNum($sql);
				if (!$total) {
					$this->toErrorDialog(array(lang('$No orders were found, nothing to send')));
				}
				$set['sql'] = $sql;
				$set['start'] = 1;
				$set['sent'] = 0;
				$set['offset'] = 0;
				$set['delay'] = (int)$this->data['delay'];
				$set['portion'] = (int)$this->data['portion'];
				$set['total'] = $total;
				$set['att'] = $this->data['att'];
				$set['subject'] = $this->data['subject'];
				$set['message'] = $this->data['message'];
				$set['from_email'] = $this->data['from_email'];
				$set['from_name'] = $this->data['from_name'];
				
				$arr = array(
					'title'	=> lang('$Starting..'),
					'descr'	=> lang('$%1 emails to send, please wait',$total),
					'delay'	=> $set['delay'],
					'sent'	=> $set['sent'],
					'total'	=> $set['total'],
					'percent'=> 1 / $set['total'] * 100,
					'begin'	=> true
				);
				$this->json_ret = array(
					'js'	=> 'S.A.M.sendEmails('.json($arr).');'
				);
				Cache::saveSmall('order_send',$set);
			break;
			case 'send_next':
				$this->json_ret = array(
					'end' 	=> true,
					'title'	=> 'Enough',
					'descr'	=> '',
					'delay'	=> 500,
					'percent'=> 100	
				);
				$this->allow('email','send');
				$set = Cache::getSmall('order_send');
				if (!$set || !$set['sql'] || $set['offset'] > $set['total']) {
					$arr = array(
						'title'	=> lang('Finished %1 of %2 emails were sent',$set['sent'],$set['total']),
						'delay'	=> $set['delay'],
						'sent'	=> $set['sent'],
						'total'	=> $set['total'],
						'descr'	=> lang('$Email sending is complete'),
						'percent'=> 100,
						'end'	=> true
					);
					$set = array();
					Cache::saveSmall('order_send',$set);
					$this->json_ret = $arr;
					return;
				} else {
					$data = array();
					$qry = DB::qry($set['sql'],$set['offset'],$set['portion']);
					while ($rs = DB::fetch($qry)) {
						$this->Index->My->admin($this, $rs, $set);
						array_push($data,$rs);
					}
					if (!$data) {
						$arr = array(
							'title'	=> lang('Finished %1 of %2 emails were sent',$set['sent'],$set['total']),
							'delay'	=> $set['delay'],
							'sent'	=> $set['sent'],
							'total'	=> $set['total'],
							'descr'	=> lang('$Email sending is complete'),
							'percent'=> 100,
							'end'	=> true
						);
						$set = array();
						Cache::saveSmall('order_send',$set);
						$this->json_ret = $arr;
						return;
					}
					
					require_once(FTP_DIR_ROOT.'inc/Email.php');
					
					$sent = Email::sendMass($data, $set['subject'], $set['message'], false, $set['from_email'], $set['from_name']);
					
					$set['offset'] += $set['portion'];
					$set['sent'] += $sent;

					$arr = array(
						'title'	=> lang('%1 of %2 emails were sent',$set['sent'],$set['total']),
						'delay'	=> $set['delay'],
						'sent'	=> $set['sent'],
						'total'	=> $set['total'],
						'percent'=> ceil($set['sent'] / $set['total'] * 100),
						'descr'	=> $set['sent'].' of '.$set['total'].' emails were sent. '.$data[0]['email'].''
					);
				}
				Cache::saveSmall('order_send',$set);
				$this->json_ret = $arr;
			break;

			case 'save_tpl':
				$this->msg_reload = false;
				$this->data = post('mail');
				$err = array();
				if (!$this->data['subject'] || !$this->data['message']) $err[] = 'Subject and message body cannot be empty.';
				$this->errors($err);
				$data = array(
					'name'	=> $this->data['subject'],
					'body'	=> $this->data['message'],
				);
				if (!$this->data['tpl']) $this->data['tpl'] = DB::one('SELECT id FROM mail_templates WHERE name='.escape($this->data['subject']));
				if ($this->data['tpl']) {
					DB::update('mail_templates',$data,$this->data['tpl']);	
				} else {
					$data['userid'] = $this->UserID;
					$data['type'] = 'F';
					$data['added'] = $this->time;
					DB::insert('mail_templates',$data);
				}
				$this->msg_text = 'Template &quot;'.html($this->data['subject']).'&quot; is saved';
			break;
			
			case 'del_tpl':
				$err = array();
				if (!post('mail','tpl')) {
					$err[] = 'Template id is empty';
				}
				$this->errorz($err);
				$old = DB::row('SELECT * FROM '.DB_PREFIX.'mail_templates WHERE id='.(int)post('mail','tpl'));
				DB::run('DELETE FROM '.DB_PREFIX.'mail_templates WHERE id='.(int)post('mail','tpl').' AND userid='.$this->UserID);
				if (DB::affected()) {
						$this->msg_type = 'trash';
						$this->msg_text = 'Template &quot;'.html(post('mail','subject')).'&quot; was deleted';
						$this->log($this->id, $old);
				} else {
					$this->toErrorDialog(array(
						'You may not delete this template, you didn\'t create it.'
					));	
				}
			break;

			case 'save':
				$this->validate();
				$this->global_action('save');
				$this->global_action('msg');
			break;
			case 'sort':
				$this->global_action();
			break;
			case 'del_map':
				$row = DB::row('SELECT * FROM '.$this->table.' WHERE id='.e(post('oid')));
				$_row = DB::row('SELECT * FROM '.$this->table2.' WHERE id='.$this->id);
				if ($row && $_row) {
					DB::run('DELETE FROM '.$this->table2.' WHERE id='.$this->id);
					$totals = DB::row('SELECT SUM(price * quantity) AS price, SUM(quantity) AS qty FROM '.$this->table2.' WHERE orderid='.e($row['id']));
					if ($totals['price']) {
						$avg_discount = $row['price_discount'] / $row['price_basket'] * 100;
						DB::run('UPDATE '.$this->table.' SET price_basket='.$totals['price'].', quantity_total='.$totals['qty'].', price_total='.($totals['price'] - $totals['price'] * $avg_discount / 100).'+price_shipping, price_discount='.($totals['price'] * $avg_discount / 100).' WHERE id='.e(post('oid')));
						$this->json_ret = array(
							'js' => 'S.A.M.edit(\''.post('oid').'\');'
						);
					} else {
						DB::run('DELETE FROM '.$this->table.' WHERE id='.$row['id']);
						$this->json_ret = array(
							'js' => 'S.A.W.close();S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->name.'\');'
						);	
					}
				}
				
				
			break;
			case 'delete':
				$this->credit($this->id);
				$this->msg_text = 'Order ID:'.$this->id.' was credited.';
				$this->updated = Site::ACTION_DELETE;
			break;
		}
	}
	
	public function credit($id) {
		$credit_id = DB::one('SELECT MAX(credit_id) FROM '.DB_PREFIX.$this->table.' WHERE status='.Site::STATUS_CREDIT)+1;
		DB::run('UPDATE '.DB_PREFIX.$this->table.' SET status='.Site::STATUS_CREDIT.', credited='.time().', credit_id='.$credit_id.' WHERE id='.$id);
		$this->affected = DB::affected();	
	}
	
	private function changeStatus($id, $status) {
		list($cur_status, $col) = $this->getStatusTimeCol($id, $status);
		if ($col) $up = ', `'.$col.'`='.$this->time; else $up = '';
		DB::run('UPDATE `'.DB_PREFIX.'orders` SET `status`='.(int)$status.$up.' WHERE `id`='.(int)$id);
	}
	
	private function getStatusTimeCol($id, $status) {
		$cur_status = DB::row('SELECT `status` FROM '.DB_PREFIX.$this->table.' WHERE id='.(int)$id,'status');
		switch ((int)$status) {
			case Site::STATUS_NOT_PAID:
				$col = '';
			break;
			case Site::STATUS_PAID:
				$col = 'paid';
			break;
			case Site::STATUS_ACCEPTED:
				$col = 'accepted';
			break;
			case Site::STATUS_CANCELLED:
				$col = 'cancelled';
			break;
			case Site::STATUS_SENT:
				$col = 'sent';
			break;
			case Site::STATUS_REFUNDED:
				$col = 'refunded';
			break;
			case Site::STATUS_ERROR:
			default:
				$col = '';
			break;
		}
		return array($cur_status, $col);
	}
	
	private function sql() {
		$this->order_status = get('order_status','','[[:CACHE:]]');
		
		if ($this->order_status!==self::KEY_ALL) {
			$this->filter .= ' AND status='.(int)$this->order_status;
		} else {
			$this->filter .= ' AND status!='.Site::STATUS_CREDIT;
		}
		
		if ($f = post('userid')) $this->filter .= ' AND userid='.(int)$f;
		$this->order = 'id DESC';
	}
	
	public function listing() {
		$this->output['signature'] = DB::getAll('SELECT id, name FROM '.DB_PREFIX.'mail_templates WHERE type=\'F\' ORDER BY name','id|name');
		$this->output['columns'] = DB::columns('orders2');
		$this->output['columns'][] = '';
		$this->output['columns'][] = 'login';
		$this->output['columns'][] = 'ordered';
		$this->button['save'] = true;
		$this->button['add'] = true;
		$this->sql();

		$sql = 'SELECT SQL_CALC_FOUND_ROWS o.id, o.status, o.price_total, o.currency, o.quantity_total, o.email, o.userid, (CASE WHEN o.userid THEN (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=o.userid) ELSE o.email END) AS login, DATE_FORMAT(FROM_UNIXTIME(o.ordered), \'%H:%i %d %b %Y\') AS ordered'.($this->order_status==Site::STATUS_CREDIT?', DATE_FORMAT(FROM_UNIXTIME(o.ordered), \'%H:%i %d %b %Y\') AS credited, credit_id':'').' FROM '.DB_PREFIX.$this->table.' o WHERE TRUE'.$this->filter.' ORDER BY '.$this->order;
		
		$_SESSION['mail_sql'] = 'SELECT o.*, (CASE WHEN o.userid THEN (SELECT u.login FROM '.DB_PREFIX.'users u WHERE u.id=o.userid) ELSE o.email END) AS login, DATE_FORMAT(FROM_UNIXTIME(o.ordered), \'%H:%i %d %b %Y\') AS ordered FROM '.DB_PREFIX.$this->table.' o WHERE TRUE'.$this->filter;

		$qry = DB::qry($sql,$this->offset,$this->limit);
		$this->total = DB::rows();
		while ($rs = DB::fetch($qry)) {
			$rs['s'] = '<span style="color:'.Conf()->g3('order_statuses',$rs['status'],1).'">'.Conf()->g3('order_statuses',$rs['status'],0).'</span>';
			$this->data[] = $rs;
		}
		
		$this->json_data = json($this->data);
		$this->nav();
	}
	
	public function toUpdate() {
		$products = post('data','products');
		if ($products && $products['table']) {
			$price = $quantity = $discount = 0;

			foreach ($products as $id => $a) {
				if (!is_array($a) || !$a['itemid']) continue;
				$options = '';
				if ($a['options']) {
					$j = array();
					foreach ($a['options'] as $o) {
						if (is_array($o)) {
							foreach ($o as $_o) {
								$j[] = $_o;
							}
						} else $j[] = $o;
					}
					$options = join(', ',$j);
				}
				
				DB::insert($this->table2,array(
					'userid' 	=> $this->data['userid'],
					'sellerid'	=> USER_ID,
					'orderid'	=> $this->id,
					'itemid'	=> $a['itemid'],
					'table'		=> $products['table'],
					'quantity'	=> $a['quantity'],
					'price'		=> $a['price'],
					'currency'	=> ($a['currency'] ? $a['currency'] : DEFAULT_CURRENCY),
					'title'		=> $a['title'],
					'type'		=> Site::ORDER_TYPE_PRODUCT,
					'options'	=> $options,
					'status'	=> 0
				));
				$price += $a['price'] * $a['quantity'];
				$quantity += $a['quantity'];
				if ($a['discount']>0) {
					$discount += $a['price'] * ($a['discount'] / 100);
				}
			}
			if ($this->updated==Site::ACTION_INSERT) {
				DB::run('UPDATE '.$this->table.' SET price_total=price_shipping+'.($price - $discount).', price_discount='.e($discount).', currency='.e(DEFAULT_CURRENCY).', price_basket='.e($price).', quantity_total='.e($quantity).', ip='.e(Session()->IPlong).' WHERE id='.$this->id);
			}
			else {
				$discount = post('data','discount');
				if ($discount>0 || $discount==='0') {
					$discount = intval($discount);
					if ($price>0) {
						DB::run('UPDATE '.$this->table.' SET price_total=(((price_basket + '.$price.') - (price_basket + '.$price.') * '.($discount/100).') + price_shipping), price_discount=(price_basket+'.$price.' * '.($discount/100).'), price_basket='.e($price).', quantity_total='.e($quantity).' WHERE id='.$this->id);
					} else {
						DB::run('UPDATE '.$this->table.' SET price_total=((price_basket - price_basket * '.($discount/100).') + price_shipping), price_discount=(price_basket * '.($discount/100).') WHERE id='.$this->id);	
					}
				}
				elseif ($price>0) {
					DB::run('UPDATE '.$this->table.' SET price_total=price_total+'.$price.', price_basket='.e($price).', quantity_total='.e($quantity).' WHERE id='.$this->id);
				}
			}
			//DB::run('UPDATE '.$this->table.' SET price_total='.($this->updated!=Site::ACTION_INSERT?'price_total':'price_shipping').'+'.floatval($price-$discount).($this->updated==Site::ACTION_INSERT?', price_discount='.e($discount):($discount?', price_discount=(price_basket * '.($discount/100).')':'')).', currency='.e(DEFAULT_CURRENCY).', price_basket='.e($price).', quantity_total='.e($quantity).', ip='.e(Session()->IPlong).' WHERE id='.$this->id);
		} else {
			if (($discount = intval(post('data','discount'))) && $this->updated==Site::ACTION_UPDATE) {
				DB::run('UPDATE '.$this->table.' SET price_total=(price_basket - (price_basket * '.($discount/100).') + price_shipping), price_discount=(price_basket * '.($discount/100).') WHERE id='.$this->id);
			}
		}
		
		if ($this->data['status']==Site::STATUS_CREDIT && $this->data['status']!=$this->rs['status']) {
			$this->credit($this->id);
		}
		
		if ($this->data['userid']) {
			$p = DB::row('SELECT * FROM '.DB_PREFIX.'users_profile WHERE setid='.(int)$this->data['userid']);
			if ($p) {
				$arr = array('firstname','lastname','country','city','state','district','zip','address'=>'street','company','vat_nr','reg_nr','cellphone'=>'phone','homephone'=>'fax');
				$_data = array();
				foreach ($arr as $k => $v) {
					if (is_numeric($k)) $c = $v; else $c = $k;
					if (!$p[$v] && $this->data[$c]) $_data[$v] = $this->data[$c];
				}
				if ($_data) DB::update('users_profile',$_data,$this->data['userid'],'setid');
			}
		}	
		/*
		if ($this->id && $this->data['table'] && $this->data['item_ids']) {
			$ex = explode(',',str_replace(' ','',$this->data['item_ids']));
			$ids = array();
			$price = 0;
			foreach ($ex as $e) {
				$_ex = explode(':',$e);
				$id = intval(trim($_ex[0]));
				$qty = intval(trim($_ex[1]));
				if ($qty<=0) $qty = 1;
				if (($row = DB::row('SELECT * FROM '.DB::prefix('grid_'.$this->data['table']).' WHERE id='.$id))) {
					DB::insert($this->table2,array(
						'userid' 	=> 0,
						'sellerid'	=> USER_ID,
						'orderid'	=> $this->id,
						'itemid'	=> $id,
						'table'		=> 'grid_'.$this->data['table'],
						'quantity'	=> $qty,
						'price'		=> $row['price'],
						'currency'	=> (isset($row['currency']) ? $row['currency'] : DEFAULT_CURRENCY),
						'title'		=> $row['title'],
						'type'		=> Site::ORDER_TYPE_PRODUCT,
						'options'	=> '',
						'status'	=> 0
					));
					$price += $row['price'] * $qty;
				}
			}
			if ($price) {
				DB::run('UPDATE '.$this->table.' SET price_total='.($this->updated!=Site::ACTION_INSERT?'price_total':'price_shipping').'+'.floatval($price).' WHERE id='.$this->id);	
			}
		}
		*/
	
		
	}
	
	public function toDB() {
		if (!$this->id) $this->data['ordered'] = $this->time;
		if ($this->data['ship_date']) $this->data['ship_date'] = Date::td($this->toTimestamp($this->data['ship_date']));
		
		foreach ($this->array['time_status'] as $col) {
			if ($this->data[$col]) $this->data[$col] = $this->toTimestamp($this->data[$col]);
		}
		list ($cur_status, $col) = $this->getStatusTimeCol($this->id, $this->data['status']);
		if ($cur_status!=$this->data['status'] && $col) $this->data[$col] = $this->time;
	}
	
	public function printed() {
		$ids = array_numeric(explode(',',get('print')));
		$sql = $_SESSION['mail_sql'];
		$qry = DB::qry($sql.' AND id IN ('.join(', ',$ids).') ORDER BY id',0,0);
		while ($rs = DB::fetch($qry)) {
			$rs['map'] = DB::getAll('SELECT * FROM '.DB_PREFIX.'orders2_map WHERE orderid='.$rs['id']);
			array_push($this->data, $rs);
		}
		$this->win($this->name.'_print');
	}
	
	
	private function columns_find($table, $win = false) {
		$skip = array('descr','body','notes','views','comments','added','edited','userid','sort','is_admin','youtube','dated','expires','instock','currency','featured','active','lang','setid','catref','bodylist','top_story','most_read','data','starts','paid','bought');
		if ($win) $skip[] = 'options';
		$ret = array();
		$table = preg_replace("/[^0-9A-Za-z\(\)\._]/i",'\\1',$table);
		$columns = DB::columns($table);;
		foreach ($columns as $c) {
			if (in_array($c,$skip)) continue;
			if (strstr($c,'url') || strstr($c,'icon') || strstr($c,'photo') || strstr($c,'main') || strstr($c,'best') || strstr($c,'descr') || strstr($c,'_to') || strstr($c,'_from') || strstr($c,'starts') || strstr($c,'expires')) continue;
			$ret[$c] = $c;
		}
		return $ret;
	}
	
	
	public function window() {
		
		$tables = array();
		$first = false;
		foreach (Site::getModules('grid') as $t => $a) {
			if (!$a['active']) continue;
			if (!in_array('price',DB::columns('grid_'.$t))) continue;
			$tables['grid_'.$t] = $a['title'];
			if (!$first) $first = 'grid_'.$t;
		}
		foreach (Site::getModules('content') as $t => $a) {
			if (!$a['active']) continue;
			if (!in_array('price',DB::columns('grid_'.$t))) continue;
			$tables['content_'.$t] = $a['title'];
			if (!$first) $first = 'content_'.$t;
		}
		$this->params['tables'] = $tables;
		$this->params['columns'] = $this->columns_find($first, true);
		$this->params['column'] = post('column','','[[:CACHE:]]');
		$this->params['table'] = post('table','','[[:CACHE:]]');
		
		if ($this->id && $this->id!==self::KEY_NEW) {
			$this->post = DB::row('SELECT * FROM '.DB_PREFIX.$this->table.' WHERE id='.$this->id);
			if (!$this->post) $this->id = 0;
			else $this->post['map'] = DB::getAll('SELECT * FROM '.DB_PREFIX.$this->table2.' WHERE orderid='.$this->id);
		} else {
			

		}
		if (!$this->post || !$this->post['id']) {
			$this->post = post('data');
		} else {
			$this->post['username'] = Data::user($this->post['userid'], 'login');
			if (!$this->post['username']) $this->post['username'] = 'unknown user';
			if ($this->post['ship_date'] && $this->post['ship_date']!='0000-00-00 00:00:00') {
				$this->post['ship_date_val'] = $this->fromTimestamp(Date::dt($this->post['ship_date']));
			}
			foreach ($this->array['time_status'] as $col) {
				if ($this->post[$col]) $this->post[$col.'_val'] = $this->fromTimestamp($this->post[$col]);
			}
			$this->global_action('main_photo_window');
			$this->json_array['files'] = json($this->filesToJson());
		}
		$this->uploadHash();
		$this->win($this->name);
	}
	
	

























	
}
