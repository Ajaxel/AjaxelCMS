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
* @file       mod/Order.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Order extends Content {
	
	public function __construct() {
		/**
		* If basket price will be more than an index of this array
		* then price will be discounted, price must be in default currency
		* - default currency is defined in settings as 1
		*/
		$this->discounts = array (
			2500	=> 5
			,10000	=> 10
			,20000	=> 15
			,50000	=> 20
		);
	}

	public function test() {

	}

	
	/**
	* All known shipping methods
	* get
	*/
	public function shippingMethods($var = false) {
		$ret = array (
			'surface_mail'	=> lang('Surface mail'),
			'air_mail'		=> lang('Air Mail').' (+'.Basket::convertMoney(100,'USD',$this->Index->Session->Currency).' '.$this->Index->Session->Currency.')',
		);
		if ($var=='first')  return 'surface_mail';
		return $var ? $ret[$var] : $ret;
	}
	/**
	* Shipping weights and shipping price increase %%
	*/
	public static function shippingWeights() {
		return array (
			500		=> 2.5
			,1000	=> 5
			,2500	=> 10
			,10000	=> 40
		);
	}
	/**
	* Get shipping price and tax by country, city and state
	*/
	public function getShippingPriceAndTax() {
		$sp = 0; // shipping price in default currency
		$tax = 0; // tax in percent
		$country = request('country','',$this->Index->Session->Country);
		$city = request('city','',$this->Index->Session->City);
		$state = request('state','',$this->Index->Session->State);
		
		// could be in a database, but I prefer it to be here, it is much faster and easier to write
		switch ($country) {
			case 'ee':
				$tax = 18;
				switch ($city) {
					case 'Tallinn': // I live in tallinn, so... shipping price can be zero
						$sp = 0;
					break;
					default:
						$sp = 50;
					break;
				}
			break;
			case 'lt':
				$tax = 0;
				$sp = 200;
			break;
			default:
				$sp = 500; // for another countries, I set: 500 EEK
				$tax = 0; // tax can be zero here
			break;
		}
		
		$tax = 0; // comment it up, if you use tax
		
		// Add price per weght
		if ($this->order['weight']) {
			$weights = self::shippingWeights();
			krsort($weights);
			foreach ($weights as $grams => $num) {
				if ($this->order['weight'] >= $grams) {
					$this->order['weight_percent'] = $num;
					break;
				}
			}
			if ($this->order['weight_percent']) $sp = $sp + ($sp * $this->order['weight_percent'] / 100);
		}
		
		// shipping methods
		if ($this->order['shipping_method']=='air_mail') $sp += 100;
		return array ($sp, $tax);
	}
	/**
	* Recalculate shipping price, VAT and others
	* Given can be $_REQUEST[country,city and etc...]
	*/
	public function calculateOrderPrice() {		
		// get the tax and shipping price by shipping_method, country, city and state
		list ($this->order['shipping_price'], $this->order['tax_percent']) = $this->getShippingPriceAndTax($order);
		// price in default currency for discount calculations
		$price_def_currency = Basket::convertMoney($this->order['price'],$this->Index->Session->Currency,'EUR');
		// highest price must be on very beginning for the loop below, let's sort
		krsort($this->discounts);
		foreach ($this->discounts as $money => $discount) {
			if ($price_def_currency >= $money) {
				// found discount percentage
				$this->order['discount_percent'] = $discount;
				break;
			}
		}
		
		// Calculate discounts, applied before taxes and shipping
		if ($this->order['discount_percent']) {
			$this->order['discount_price'] = number_format($this->order['price'] * ($this->order['discount_percent'] / 100), 2, '.', '');
			$this->order['price'] = $this->order['price'] - $this->order['discount_price'];
		}
		
		// Calculate tax
		if ($this->order['tax_percent']) {
			$this->order['tax_price'] = number_format($this->order['price'] - $this->order['price'] / ($this->order['tax_percent'] / 100 + 1), 2, '.', '');
			$this->order['price'] = $this->order['price'] + $this->order['tax_price'];
		}
		
		// use_shipping variable, whether one of the products in basket list has a product_type: "product" nor a service, license or download
		if ($this->order['use_shipping']) {
			// total price in user selected currency without tax
			$this->order['total_price'] = $this->order['price'] + $this->order['shipping_price'];
		} else {
			$this->order['shipping_price'] = 0; // null it after getShippingPriceAndTax() method. no shipping price needed
			$this->order['total_price'] = $this->order['price']; // total price is the initial price
		}
		// return all data back to system, which it will be assigned to Smarty (basketlist.tpl) also as {$order_data} variable
		return $order;
	}		
}