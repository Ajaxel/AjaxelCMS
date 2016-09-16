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
* @file       inc/Basket.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Basket extends Object {
	
	private $cache = array();
	
	const
		TRANS_REASON_USER_MONEY		= 'product pay with user money',
		TRANS_REASON_PAYMENT_REPAY	= 'product auto-re-pay on money transfer',
		TRANS_REASON_PRODUCT		= 'paid for product with bank transfer',
		TRANS_REASON_TRANSFER		= 'transfered money from bank'
	;
		
	public function init(&$params = array()) {
		return $this;	
	}
	
	
	
	public function getContent() {
		//$this->add('cms_content_product',1,1);
		
		
	//	p($this->basketData());
		$this->Index->Smarty->display('basket.tpl');
	}

	public function add($table, $id, $quantity = 1, $options = array()) {
		$product = $this->getProduct($table, $id);
		if (!$product) return false;
		if (!$this->Index->Session->SID) {
			Message::Halt('SessionID is empty','Please use this instance call Factory::call(\'basket\')->load($this->Index)->set($params)->');	
		}
		
		if (!isset($product['instock']) || $product['instock']<0) $product['instock'] = 1;
		if ($quantity > $product['instock']) $quantity = $product['instock'];
		$price = $product['price'];
		if (!isset($product['weight'])) $product['weight'] = 0;
		
		if ($quantity > 0) $added = 'plus';
		else $added = 'minus';
		
		$prod_type = intval(isset($product['product_type']) ? $product['product_type'] : Site::ORDER_TYPE_PRODUCT);
		$serOptions = '';
		$basket_qty = DB::one('SELECT SUM(`quantity`) FROM `'.DB_PREFIX.'orders2_basket` WHERE `SID`=\''.$this->Index->Session->SID.'\' AND `type`=\''.$prod_type.'\' AND `table`=\''.$table.'\' AND itemid='.$id);
		
		if ($options && is_array($options)) {
			$serOptions = self::serializeOptions($options);
			$added_qty = DB::one('SELECT quantity FROM '.DB_PREFIX.'orders2_basket WHERE SID=\''.$this->Index->Session->SID.'\' AND type=\''.$prod_type.'\' AND `table`=\''.$table.'\' AND itemid='.$id.' AND options=\''.$serOptions.'\'');
			$origin_quantity = $added_qty;
			foreach ($this->getPriceFormulasFromOptions($table, $id, $options) as $groupid => $id_formula) {
				if (!$id_formula) continue;
				foreach ($id_formula as $id => $price_formula) {
					if (!$price_formula) continue;
					$origin_price = $price;
					$price = $this->formulatePrice($price, $price_formula, $added_qty);
					if ($price<=1) $price = $origin_price;
				}
			}
			$added_qty = $origin_quantity;
		} else {
			$added_qty = $basket_qty;
		}
		$avalable_qty = $product['instock'] - $basket_qty;
		$added_qty = $added_qty + $quantity;
		if ($added_qty < 0) $added_qty = 1;
		$new_basket_qty = $basket_qty + $quantity;
		if ($new_basket_qty < 0) $new_basket_qty = 1;	
		
		if ($product['instock']===NULL || $product['instock'] >= $new_basket_qty) {
			$sql = 'DELETE FROM '.DB_PREFIX.'orders2_basket WHERE `SID`=\''.$this->Index->Session->SID.'\' AND `type`=\''.$prod_type.'\' AND `table`=\''.$table.'\' AND `itemid`='.$id.' AND `options`=\''.$serOptions.'\'';
			DB::run($sql);
			if ($added_qty > 0) {
				$data = array(
					'SID' 		=> $this->Index->Session->SID,
					'userid'	=> $this->Index->Session->UserID,
					'sellerid'	=> $product['userid'],
					'itemid'	=> $id,
					'table'		=> $table,
					'title'		=> $product['title'],
					'quantity'	=> $added_qty,
					'price'		=> $price,
					'currency'	=> $product['currency'],
					'options'	=> $serOptions,
					'weight'	=> $product['weight'],
					'type'		=> $prod_type,
					'added'		=> $this->time
				);
				DB::insert('orders2_basket', $data);
			}
		} else {
			$added = 'nomore';
		}
		
		return $added.'.'.($added=='nomore'?$product['instock']:$quantity).'.'.$avalable_qty;
	}
	
	public function clean($where = NULL) {
		return DB::run('DELETE FROM '.DB_PREFIX.'orders2_basket WHERE `SID`=\''.Session()->SID.'\''.$where);
	}
	
	public function getProduct($table, $id) {
		$cols = DB::columns($table);
		if (!$cols) return false;
		$ret = DB::row('SELECT price'.(in_array('instock', $cols)?', instock':'').(in_array('currency', $cols)?', currency':'').', title, userid'.(in_array('weight', $cols)?', weight':'').' FROM `'.DB::prefix($table).'` WHERE id='.$id.' AND active=1');
		if (!$ret['currency']) $ret['currency'] = DEFAULT_CURRENCY;
		return $ret;
	}
	
	
	
	private function getStatusCol($status) {
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
		return $col;
	}
	
	
	public function status($status, $id) {
		$id = (int)$id;
		if (!$id) return false;
		$order = DB::row('SELECT * FROM '.DB_PREFIX.'orders2 WHERE id='.$id);
		$old_status = $order['status'];
		if ($old_status==Site::STATUS_PAID || $status==$old_status) {
			return false;
		}
		$col = self::getStatusCol($status);
		DB::run('UPDATE '.DB_PREFIX.'orders2 SET status='.$status.', `'.$col.'`='.time().' WHERE id='.$id);
		if (in_array($status, Conf()->g('order_statuses_ok'))) {
			$s = 1;	
		} else {
			$s = 2;	
		}
		DB::run('UPDATE '.DB_PREFIX.'orders2_map SET status=\''.$s.'\' WHERE orderid='.$id);
		return $order;
	}
	
	public function clear() {
		DB::run('DELETE FROM '.DB_PREFIX.'orders2_basket WHERE SID=\''.$this->Index->Session->SID.'\'');	
	}
	
	public function checkout($params = array()) {
		$bank = $shipping_method = '';
		$clear = false;
		$shipping_price = $price_tax = $price_discount = $weight = $use_shipping = 0;
		$post = array();
		extract($params);
		$basket = $this->basketData();
		if ($basket['total']==0) return false;
		
		$price = $basket['data']['price'] + $shipping_price;
		
		$order = array(
			'userid'		=> $this->Index->Session->UserID,
			'SID'			=> $this->Index->Session->SID,
			'ip'			=> $this->Index->Session->IPlong,
			'sellerid'		=> 1,
			'price_total'	=> $price,
			'currency'		=> $params['currency'] ? $params['currency'] : DEFAULT_CURRENCY,
			'quantity_total'=> $basket['data']['quantity'],
			'price_shipping'=> $shipping_price,
			'price_tax'		=> $price_tax,
			'price_discount'=> $price_discount,
			'price_basket'	=> $basket['data']['price'],
			'weight'		=> $weight,
			'status'		=> 0,
			'bank'			=> $bank,
			'use_shipping'	=> $use_shipping,
			'shipping_method'=> $shipping_method,
			'ship_data'		=> $ship_date,
			'homephone'		=> $post['homephone'],
			'cellphone'		=> $post['cellphone'],
			'country'		=> $post['country'],
			'city'			=> $post['city'],
			'state'			=> $post['state'],
			'zip'			=> $post['zip'],
			'address'		=> $post['address'],
			'address2'		=> $post['address2'],
			'email'			=> $post['email'],
			'salutation'	=> $post['salutation'],
			'firstname'		=> $post['firstname'],
			'lastname'		=> $post['lastname'],
			'company'		=> $post['company'],
			'info'			=> $post['info'],
			'message'		=> $message,
			'ordered'		=> time()
		);
		DB::insert('orders2',$order);
		$id = DB::id();
		
		foreach ($basket['list'] as $i => $rs) {
			$map = array(
				'userid'	=> $this->Index->Session->UserID,
				'sellerid'	=> 1,
				'orderid'	=> $id,
				'itemid'	=> $rs['itemid'],
				'table'		=> $rs['table'],
				'quantity'	=> $rs['quantity'],
				'price'		=> $rs['price'],
				'currency'	=> $rs['currency'],
				'title'		=> $rs['title'],
				'type'		=> $rs['type'],
				'options'	=> $rs['options'],
				'status'	=> 0
			);
			DB::insert('orders2_map',$map);
		}
		if ($clear) $this->clear();
		return array($id, $price);
	}
	
	
	
	
	
	
	
	
	public function basketData() {
		if (isset($this->cache['basketData'])) return $this->cache['basketData'];
		$sql = 'SELECT b.userid, b.sellerid, b.itemid, b.table, b.title, b.quantity, b.price, b.currency, b.options, b.added, b.type';
		if (count(Conf()->g('currencies'))>1) {
			$sql .= ', (CASE';
			foreach (Conf()->g('currencies') as $cur => $a) {
				if ($cur!=$this->Index->Session->Currency && Conf()->g3('currencies',$this->Index->Session->Currency,0)>0) {
					$sql .= ' WHEN b.currency = \''.$cur.'\' THEN REPLACE(FORMAT((b.price * '.number_format($a[0] / Conf()->g3('currencies',$this->Index->Session->Currency,0),2,'.','').'),2),\',\',\'\')';
				}
			}
			$sql .= ' ELSE b.price END) AS conv_price';
		}
		$sql .= ', (SELECT SUM(s.quantity) FROM '.DB_PREFIX.'orders2_basket s WHERE s.table=b.table AND s.itemid=b.itemid AND s.SID=b.SID) AS added_quantity';
		$sql .= ' FROM '.DB_PREFIX.'orders2_basket b WHERE b.SID = \''.$this->Index->Session->SID.'\'';
		$data = array();
		$qry = DB::qry($sql,0,0);
		$qty = $price = $i = $called = $conv_price = 0;
		$title = array();
		
		while ($rs = DB::fetch($qry)) {
			$this->buildBasketItems($rs);
			$qty += $rs['quantity'];
			$conv_price += $rs['conv_price'] * $rs['quantity'];
			$price += ($rs['price'] * $rs['quantity']);
			$title[] = $rs['title'].' ('.($rs['quantity']?$rs['quantity'].' * ':'').Parser::format_price($rs['price'],$rs['currency']).')';	
			array_push($data, $rs);
			$i++;
		}
		$totals = array (
			'quantity'			=> $qty,
			'price'				=> $conv_price,
			'conv_currency'		=> $this->Index->Session->Currency,
			'title'				=> $title,
		);
		$ret = array('list' => $data, 'total' => $i, 'data' => $totals);
		$this->cache['basketData'] = $ret;
		return $ret;
	}
	
	private function buildBasketItems(&$rs) {
		if (!$rs['conv_price']) $rs['conv_price'] = $rs['price'];
		if ($rs['type']==Site::ORDER_TYPE_ACTIVATION) {
			if (is_numeric($rs['options'])) {
				$rs['period'] = $rs['options'];
				$rs['label'] = Conf()->get('ARR_PERIODS', $rs['options']);
				if (!$rs['label']) $rs['label'] = Date::countDown(time()+$rs['period']*3600);
				unset($rs['options']);
			} else {
				parse_str($rs['options'],$arr_options);
				$rs['options_data'] = array();
				foreach ((array)$arr_options[PROD_OPTION] as $groupid => $idtitle) {
					if (is_array($idtitle)) {
						foreach ($idtitle as $_idtitle) {
							$pos = strpos($_idtitle,'@');
							if ($pos) {
								$id = substr($_idtitle,0,$pos);
								$title = substr($_idtitle,$pos+1);
								$rs['options_data'][$groupid][] = array('id'=>$id,'title'=>$title);
							} else {
								$rs['options_data'][$groupid][] = array('id'=>0,'title'=>$_idtitle);
							}
						}
					} else {
						$pos = strpos($idtitle,'@');
						if ($pos) {
							$id = substr($idtitle,0,$pos);
							$title = substr($idtitle,$pos+1);
							$rs['options_data'][$groupid] = array('id'=>$id,'title'=>$title);
						} else {
							$rs['options_data'][$groupid] = array('id'=>0,'title'=>$idtitle);
						}
					}
				}
			}
		}
		elseif ($rs['options']) {
			$rs['options_data'] = $this->getOptionsByUrl($rs['table'], $rs['itemid'], $rs['options']);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function getOptionsByUrl($table, $itemid, $options) {
		$options = ltrim($options,'&');
		parse_str($options,$arr_options);
		$ret = array();
		foreach ((array)$arr_options[URL_KEY_PROD_OPTION] as $groupid => $id) {
			$ret[$groupid] = $this->getProductOptions($table, $itemid, $groupid, false, $id);
		}
		return $ret;
	}
	public static function serializeOptions($options) {
		if (!$options) return '';
		ksort($options);
		$ret = '';
		foreach ($options as $groupid => $id) {
			if (!$groupid || !$id) continue;
			$origin_price = $price;
			if (is_array($id)) {
				foreach ($id as $o => $i) {
					if (!$i) continue;
					$arr_opts[] = URL_KEY_PROD_OPTION.'['.$groupid.']['.$o.']='.$i;
				}
			} else {
				$arr_opts[] = URL_KEY_PROD_OPTION.'['.$groupid.']='.$id;
			}
		}
		if ($arr_opts[0]) $ret = '&'.join('&',$arr_opts);
		return $ret;
	}
	
	
	
	
	/**
	* In product options can be price formulas 
	* We simply evaluate this condition as PHP
	* Price changes according to article's currency
	* Correct price formulas are:
	*	$price = $price + 34; // add 34 // or $p = $p +34;
	*	$price = $price - ($price * 0.2); // 20% discount // or $p = $p - ($p * 0.2);
	*	$price = $price - ($quantity * 0.05 * ($price / 100)) // 5% discount, increasing the quantity discount is getting higher each time progressively
	*	Example for above condition:
	*	$price = 3500; $quantity = 100;
	*		3325 = 3500 - (100 * 0.05 * (3500 / 100))
	*	$price = 60; $quantity = 250;
	*		46.9 = 60 - (250 * 0.05 * (60 / 100))
	*	
	* Also possible to include a logical file as: 'include 1.php' where must contents of the file should be the same, folder is: './misc/priceformula/'
	*/
	public function formulatePrice($price, $price_formula, $quantity = 1) {
		if (!$price_formula || is_array($price_formula)) return $price;
		if (is_numeric($price_formula)) { // if price formula is numeric then price changes to price forumla
			return number_format($price_formula,2,'.','');	
		}
		elseif (substr($price_formula,0,7)=='include') { // include whole calculation file
			$file = './config/priceformula/'.trim(substr($price_formula,7));
			if (!file_exists($file)) return $price;
			$ret = include $file;
			if ($ret) $price = $ret;
			return number_format($price,2,'.','');
		}
		elseif (preg_match('/^(\+|\-)\s*([0-9\.]+)\s*(\%)?$/',$price_formula,$match)) {
			if ($match[3]=='%') {
				if ($match[1]=='-') $price = $price - ($price * ($match[2] / 100));
				else $price = $price + ($price * ($match[2] / 100));
			} else {
				if ($match[1]=='-') $price = $price - $match[2];
				else $price = $price + $match[2];
			}
			return number_format($price,2,'.','');
		}
		elseif (!preg_match('/^\$(price|p)\s*=/',$price_formula)) {
			$price_formula = '$price = '.$price_formula;
		}
		$groupid = $this->Index->Session->GroupID;
		$classid = $this->Index->Session->ClassID;
		$userid = $this->Index->Session->UserID;
		$q = $quantity;
		$p = $price;
		$_p = $p;
		// Use: $price or $p, $quantity or $q, $userid, $usergroup and objects..
		@eval($price_formula.';');
		if ($p && $p!=$_p) $price = $p;
		return number_format($price,2,'.','');
	}
	
	public function getPriceFormulasFromOptions($table, $itemid, $arrIN) {
		$sql = 'SELECT a.id, a.price_formula, a.groupid, a.data_'.$this->lang.' FROM product_assigned a WHERE a.table=\''.$table.'\' AND a.itemid='.(int)$itemid;
		$sql .= ' ORDER BY a.id';
		$qry = DB::qry($sql,0,0);
		$ret = array();
		while ($rs = DB::fetch($qry)) {
			if (!$arrIN[$rs['groupid']]) continue;
			$data = $rs['data_'.$this->lang];
			$type = substr($data,0,7);
			switch ($type) {
				case 'range::':
					$ret[$rs['groupid']][$s['id']] = false;
				break;
				case '__php::':
					unset($array);
					if (eval('?>'.substr($data,7))!==false && $array && is_array($array)) {
						$i = 0;
						$ret[$n]['array'] = $array;
						foreach ($array as $key => $val) {
							if (is_array($val)) {
								if ($val['id']) $key = $val['id'];
								if (is_array($arrIN[$rs['groupid']])) {
									if (in_array($key,$arrIN[$rs['groupid']])) {
										if ($val[3]) $ret[$rs['groupid']][$key] = $val[3];
										if ($val['price_formula']) $ret[$rs['groupid']][$key] = $val['price_formula'];
									}
								}
								elseif ($key==$arrIN[$rs['groupid']]) {
									if ($val[3]) $ret[$rs['groupid']][$key] = $val[3];
									if ($val['price_formula']) $ret[$rs['groupid']][$key] = $val['price_formula'];
									break;
								}
							}
							$i++;
						}
					}
				break;
				case '__sql::':
					$sql_eval = substr($data,7);
					if (substr($sql_eval,0,2)=='<?') {
						unset($SQL);
						if (eval('?>'.$sql_eval)===false || !$SQL || substr(strtolower($SQL),0,6)!='select') {
							break;
						}
					} else {
						if (!$sql_eval || substr(strtolower($sql_eval),0,6)!='select') break;
						$SQL = $sql_eval;
					}
					$q = DB::qry($SQL,0,0);
					while ($s = DB::fetch($q)) {
						if (!$s['price_formula'] || !$s['id']) continue;
						if (is_array($arrIN[$rs['groupid']])) {
							if (in_array($s['id'],$arrIN[$rs['groupid']])) {
								if ($s['price_formula']) $ret[$rs['groupid']][$s['id']] = $s['price_formula'];
							}
						}
						elseif ($s['id']==$arrIN[$rs['groupid']]) {
							if ($s['price_formula']) $ret[$rs['groupid']][$s['id']] = $s['price_formula'];
							break;
						}
					}
					DB::free($q);
				break;
				default:
					if (!$rs['price_formula']) break;
					if (is_array($arrIN[$rs['groupid']])) {
						if (in_array($rs['id'],$arrIN[$rs['groupid']])) {
							if ($rs['price_formula']) $ret[$rs['groupid']][$rs['id']] = $rs['price_formula'];
						}
					}
					elseif ($rs['id']==$arrIN[$rs['groupid']]) {
						if ($rs['price_formula']) $ret[$rs['groupid']][$rs['id']] = $rs['price_formula'];
						break;
					}
					
				break;
			}
		}
		return $ret ? $ret : array();
	}
	
	
	
	
	public function getProductOptions($table, $itemid=0, $groupid=0, $giveall = true, $ids = false) {
		
		if (!$table || !$itemid) return '';
		if (!$groupid) $ids = false;
		
		$sql = 'SELECT id,groupid,price_formula,title_'.$this->lang.',data_'.$this->lang.',sort'.($ids?', SUBSTRING(data_'.$this->lang.',1,7) AS data_type':'').' FROM '.DB_PREFIX.'product_assigned WHERE `table`=\''.$table.'\' AND itemid='.(int)$itemid.($groupid?' AND groupid='.(int)$groupid:'');
		if ($ids) {
			$sql .= ' HAVING (data_type=\'__php::\' OR data_type=\'__sql::\' OR data_type=\'range::\' OR (data_type!=\'__php::\' AND data_type!=\'__sql::\' AND data_type!=\'range::\' AND id IN (\''.join('\',\'',(array)$ids).'\')))';
		}
		$sql .= ' ORDER BY groupid, sort, title_'.$this->lang;
	
		$qry = DB::qry($sql,0,0);
		$last_group = $i = $j = $n = 0;
		$ret = $type = array();
		$break = $continue = false;
		
		while ($rs = DB::fetch($qry)) {
			if ($last_group!=$rs['groupid']) {
				$jsDiff = $jsDiffConv = array();
				$i = 0;
				$j++;
				$n = $j-1;
				$pics = 0;
				$ret[$n] = DB::row('SELECT category, type, title_'.$this->lang.' AS title, cnt FROM '.DB_PREFIX.'product_groups WHERE id='.$rs['groupid']);
				$ret[$n]['groupid'] = $rs['groupid'];
				$type = self::getOptionsDataType($rs['data_'.$this->lang]);
			}
			
			switch ($type[0]) {
				case 'write':
					if ($ids) {
						if (is_array($ids)) {
							if (!in_array($rs['id'],$ids)) {
								$continue = true;
								break;
							}
						} elseif ($rs['id']!==$ids) {
							$continue = true;
							break; 
						}
						$n = 0;
					}
					$ret[$n]['options'][$i]['id'] = $rs['id'];
					$ret[$n]['options'][$i]['title'] = $rs['title_'.$this->lang];
					if ($rs['price_formula']) {
						$this->priceFormulate($rs['price_formula'], $table, $itemid, $ret[$n]['options'][$i], true);
					}
					if (!$ids) {
						if ($ret[$n]['options'][$i]['price_formulated']) {
							$jsDiff[] = $ret[$n]['options'][$i]['price_formulated'] - $ret[$n]['options'][$i]['product_price'];
							$jsDiffConv[] = $ret[$n]['options'][$i]['price_formulated_conv'] - $ret[$n]['options'][$i]['product_price_conv'];
						} else {
							$jsDiff[] = 0;
							$jsDiffConv[] = 0;
						}
					}
					$data = explode(' |;',$rs['data_'.$this->lang]);
					if ($data[0]) {
						$ret[$n]['options'][$i]['picture'] = $data[0];
						$pics++;
					}
					if ($data[0] && $data[2]) {
						$ret[$n]['options'][$i]['pwidth'] = $data[2];
						$ret[$n]['options'][$i]['pheight'] = $data[3];
					}
					if ($data[1]) {
						$ret[$n]['options'][$i]['text'] = $data[4]?$data[4]:$data[1];
					}
					$ret[$n]['options'][$i]['sort'] = $rs['sort'];
					$ret[$n]['cnt']++;
					$i++;
				break;
				case 'range':
					$range = arrRange($type[1][1],$type[1][2],$type[1][3]);
					$i = 0;
					$break = false;
					if (!$range) break;
					foreach ($range as $key => $val) {
						if ($ids) {
							if (is_array($ids)) {
								if (!in_array($val,$ids)) continue;
							} elseif ($val!=$ids) continue;
						}
						$ret[$n]['options'][$i]['id'] = $val;
						$ret[$n]['options'][$i]['title'] = $val;
						$i++;
						
					}
				break;
				case '__php':
					unset($array, $rs['cnt']);
					if (eval('?>'.$type[1])!==false && $array && is_array($array)) {
						$i = 0;
						if ($giveall) {
							$ret[$n]['array'] = $array;
							$ret[$n]['cnt'] = count($array);
						}
						$break = false;
						foreach ($array as $key => $val) {
							if (is_array($val)) {
								if ($ids) {
									if (is_array($ids)) {
										if (!in_array($key,$ids)) continue;
									} elseif ($key!=$ids) continue;
								}
								$ret[$n]['options'][$i]['id'] = $key;
								$ret[$n]['options'][$i]['title'] = $val[0];
								$ret[$n]['options'][$i]['text'] = $val[1];
								if ($val[2]) {
									$ret[$n]['options'][$i]['picture'] = $val[2];
									$pics++;
								}
								if ($val[3]) $ret[$n]['options'][$i]['price_formula'] = $val[3];
								$c = count($val);
								if ($c>3) {
									for ($j=4;$j<=$c;$j++) {
										if ($val[$j]) $ret[$n]['options'][$i][$j] = $val[$j];
									}
								}
								if ($val) {
									foreach ($val as $a => $b) {
										if (!is_numeric($a) && $a) $ret[$n]['options'][$i][$a] = $b;
									}
								}
								if ($ret[$n]['options'][$i]['price_formula']) {
									$this->priceFormulate($ret[$n]['options'][$i]['price_formula'], $table, $itemid, $ret[$n]['options'][$i], true);
								}
								if (!$ids) {
									if ($ret[$n]['options'][$i]['price_formulated']) {
										$jsDiff[] = $ret[$n]['options'][$i]['price_formulated'] - $ret[$n]['options'][$i]['product_price'];
										$jsDiffConv[] = $ret[$n]['options'][$i]['price_formulated_conv'] - $ret[$n]['options'][$i]['product_price_conv'];
									} else {
										$jsDiff[] = 0;
										$jsDiffConv[] = 0;
									}
								}
							} else {
								if ($ids) {
									if (is_array($ids)) {
										if (!in_array($key,$ids)) continue;
									} elseif ($key!==$ids) continue;
								}
								$ret[$n]['options'][$i]['id'] = $key;
								$ret[$n]['options'][$i]['title'] = $val;
							}
							$i++;
						}
					}
				break;
				case '__sql':
					if (substr($type[1],0,2)=='<?') {
						unset($SQL);
						if (eval('?>'.$type[1])===false || !$SQL || substr(strtolower($SQL),0,6)!='select') {
							break;
						}
					} else {
						if (!$type[1] || substr(strtolower($type[1]),0,6)!='select') break;
						$SQL = $type[1];
					}
					if ($giveall) $ret[$n]['sql'] = $SQL;
					$q = dbRetrieve($SQL,true,0,0);
					$i = 0;
					while ($s = dbFetch($q)) {
						if ($ids) {
							if (is_array($ids)) {
								if (!in_array($s['id'],$ids)) continue;
							} elseif ($s['id']!=$ids) continue;
						}
						$ret[$n]['options'][$i]['id'] = $s['id'];
						$ret[$n]['options'][$i]['title'] = $s['title'];
						if ($s['text']) {
							$ret[$n]['options'][$i]['text'] = $s['text'];
							$ret[$n]['options'][$i]['conv'] = $s['conv']?$s['conv']:$s['text'];
						}
						if ($s['picture']) {
							$ret[$n]['options'][$i]['picture'] = $s['picture'];
							if ($s['width']) {
								$ret[$n]['options'][$i]['width'] = $s['width'];
								$ret[$n]['options'][$i]['height'] = $s['height'];
							}
						}
						if ($s['price_formula']) {
							$this->priceFormulate($s['price_formula'], $table, $itemid, $ret[$n]['options'][$i], true);
						}
						if (!$ids) {
							if ($ret[$n]['options'][$i]['price_formulated']) {
								$jsDiff[] = $ret[$n]['options'][$i]['price_formulated'] - $ret[$n]['options'][$i]['product_price'];
								$jsDiffConv[] = $ret[$n]['options'][$i]['price_formulated_conv'] - $ret[$n]['options'][$i]['product_price_conv'];
							} else {
								$jsDiff[] = 0;
								$jsDiffConv[] = 0;
							}
						}
						$i++;
					}
					dbFreeResult($q);
				break;
			}
			if ($continue) continue;
			$ret[$n]['total'] = $i;
			if (!$ids) {
				if ($pics) $ret[$n]['files'] = $pics;
				if ($giveall) $ret[$n]['data_type'] = $type;
				else $ret[$n]['data_type'] = $type[0];
				$last_group = $rs['groupid'];
				$ret[$n]['js_calculate_price'] = '['.join(',',$jsDiff).']';
				$ret[$n]['js_calculate_price_conv'] = '['.join(',',$jsDiffConv).']';
			} else $ret[$n]['data_type'] = $type[0];
		}
		
		if ($ids) $ret = $ret[0];
		dbFreeResult($qry);
		return $ret;
	}
	
	private function priceFormulate($formula, $table, $itemid, &$ret, $conv = true) {
		$ret['price_formula'] = $formula;
		$price = DB::one('SELECT price FROM `'.DB_PREFIX.$table.'` WHERE id='.$itemid);
		if ($price['price']<=0) return false;
		$ret['product_price'] = $price['price'];
		$ret['product_price_conv'] = self::convertMoney($price['price'],$price['currency'],$this->Index->Session->Currency);
		$ret['product_currency'] = $price['currency'];
		$formulated = $this->formulatePrice($price['price'], $formula, true);
		if ($price['price'] && $formulated && $formulated!=$price['price']) {
			$ret['price_formulated'] = $formulated;
			$ret['price_formulated_conv'] = self::convertMoney($formulated,$price['currency'],$this->Index->Session->Currency);
			$ret['price_percentage'] = $this->optionPriceDiffPercentage($formulated, $price['price']);
			$ret['price_diff'] = abs($formulated - $price['price']);
			$ret['price_diff_conv'] = self::convertMoney($ret['price_diff'],$price['currency'],$this->Index->Session->Currency);
		}
	}
	
	private function optionPriceDiffPercentage($formulated, $price) {
		return abs(number_format(100 - ($formulated / $price * 100),2,'.','')).'%';
	}	
	
	private function getOptionsDataType($setid, $orig = true) {
		if ($setid && is_numeric($setid)) {
			$data = DB::row('SELECT data_'.$this->lang.' FROM '.DB_PREFIX.($orig?'product_options WHERE setid='.$setid:'product_assigned WHERE groupid='.$setid),'data_'.$this->lang);
		} else $data = $setid;
		$type = substr($data,0,7);
		switch ($type) {
			case 'range::':
				$return = array('range',explode(',',substr($data,7)));
			break;
			case '__php::':
				$return = array('__php',substr($data,7));
			break;
			case '__sql::':
				$return = array('__sql',substr($data,7));
			break;
			default:
				$return = array('write',explode(' |;',$data));
			break;
		}
		return $return;
	}
	
	
	
	
	public static function convertMoney($amount,$from=false,$to=false) {
		if (!$from) return $amount;
		if (!$to) $to = CURRENCY;
		if (Conf()->g3('currencies',$to,0)) {
			$ret = $amount * Conf()->g3('currencies',$from,0) / Conf()->g3('currencies',$to,0);
		}
		else $ret = $amount;
		return number_format($ret,2,'.','');
	}
	
	public static function WhereByCurrency($a,$b,$currency,$price_col = 'price', $cur_col = 'currency') {
		$sql = '';
		$currency = strtoupper($currency);
		$currencies = Conf()->g('currencies');
		if ((!$a&&!$b) || !$currencies[$currency]) return '';
		if (!$currency) $currency = DEFAULT_CURRENCY;
		if ($a) {
			$sql .= ' AND (CASE';
			foreach ($currencies as $cur => $x) {
				if (!$x[0]) continue;
				if ($cur!=$currency) {
					$sql .= ' WHEN '.$cur_col.'=\''.$cur.'\' THEN ('.$price_col.' * '.($currencies[$currency][0] / $x[0]).')';
				}
			}
			$sql .= ' ELSE '.$price_col.' END) >= '.(float)$a;
		}
		
		if ($b) {
			$sql .= ' AND (CASE';
			foreach ($currencies as $cur => $x) {
				if (!$x[0]) continue;
				if ($cur!=$currency) {
					$sql .= ' WHEN '.$cur_col.'=\''.$cur.'\' THEN ('.$price_col.' * '.($currencies[$currency][0] / $x[0]).')';
				}
			}
			$sql .= ' ELSE '.$price_col.' END) <= '.(float)$b;
		}
		return $sql;
	}
	
	public static function SelectByCurrency($currency,$price_col = 'price', $cur_col = 'currency', $only = array()) {
		$sql = '';
		$currency = strtoupper($currency);
		$currencies = Conf()->g('currencies');
		if (!$currency) $currency = DEFAULT_CURRENCY;
		$is = false;
		$sql .= '(CASE';
		foreach ($currencies as $cur => $x) {
			if (!$x[0]) continue;
			if ($only && !in_array($cur, $only)) continue;
			if ($cur!=$currency && $currencies[$currency][0]>0) {
				$sql .= ' WHEN '.$cur_col.'=\''.$cur.'\' THEN ('.$price_col.' * '.($currencies[$currency][0] / $x[0]).')';
				$is = true;
			}
		}
		$sql .= ' ELSE '.$price_col.' END)';
		if (!$is) return $price_col;
		return $sql;
	}
	
	public static function _SelectByCurrency($price_col = 'price', $cur_col = 'currency') {
		$sql = '';
		$sql .= ' (CASE';
		foreach (Conf()->g('currencies') as $cur => $amount) {
			$sql .= ' WHEN '.$cur_col.' = \''.$cur.'\' THEN ('.$price_col.' * '.(float)$amount.')';
		}
		$sql .= ' ELSE '.$price_col.' END)';
		return $sql;
	}
	
	
	
	
	
	
}