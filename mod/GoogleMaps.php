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
* @file       mod/GoogleMaps.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class GoogleMaps {
	public
		$load_js = true,
		$api_key = '',
		$lng = 0,
		$lat = 0,
		$zoom = 0,
		$country = '',
		$city = '',
		$state = '',
		$district = '',
		$street = '',
	
		$id = 'map_canvas',
		$width = 203,
		$height = 155,
		$style = ''
	;
	
	private
		$js = '',
		$search = '',
		$vars = array('auto'=>1)
	;
	
	public function __construct() {
		$this->vars['key'] = GOOGLE_MAPS_KEY;
	}
	
	public function setCoordsByAddress() {
		if (!$this->country) return $this;
		$ret = getCoordinates($this->country, $this->city, $this->state, $this->street);
		if ($ret && $ret[0]) {
			$this->lng = $ret[0]['longitude'];
			$this->lat = $ret[0]['latitude'];
			if ($this->street) $this->zoom = 14;
			elseif ($this->city) $this->zoom = 11;
			elseif ($this->state) $this->zoom = 9;
			elseif ($this->country) $this->zoom = 6;
			else $this->zoom = 8;
		}
		return $this;
	}
	public function search(){
		if ($this->country) {
			$this->search = 'S.M.search({country:\''.strjs($this->country).'\',state:\''.strjs($this->state).'\',city:\''.strjs($this->city).'\',street:\''.strjs($this->street).'\'});';
		}
		elseif ($this->lat) {
			$this->search .= '';
		}
		$this->js .= $this->search;
		return $this;
	}
	public function addMarker() {
		$this->js .= 'S.M.addMarker();';
		return $this;
	}
	public function remMarker() {
		$this->js .= 'S.M.remMarker();';
		return $this;
	}
	public function markers($url, $funcs = array()) {
		$this->vars['markers'] = $url;
		$this->vars['markerClick'] = $funcs['markerClick'];
		$this->vars['markerHover'] = $funcs['markerHover'];
		$this->vars['markerOut'] = $funcs['markerOut'];
		$this->vars['markerContext'] = $funcs['markerContext'];
		
		return $this;	
	}
	public function setCenter() {
		$this->js .= 'S.M.setCenter(\''.$this->lat.'\',\''.$this->lng.'\',\''.$this->zoom.'\');';
		return $this;
	}
	public function draggable() {
		$this->vars['draggable'] = 1;
		return $this;
	}
	public function manual() {
		$this->vars['auto'] = 0;
		return $this;	
	}
	public function icons($icons){
		$this->vars['icons'] = $icons;
		return $this;
	}
	public function icon($icon = false, $icon_shadow = NULL) {
		if ($icon) $this->vars['icon'] = $icon;
		else $this->vars['icon'] = array('src'=>'/'.HTTP_DIR_TPL.'images/marker.png','w'=>14,'h'=>24);
		if ($icon_shadow) $this->vars['icon_shadow'] = $icon_shadow;
		elseif ($icon_shadow===NULL) $this->vars['icon_shadow'] = array('src'=>'/'.HTTP_DIR_TPL.'images/marker_shadow.png','w'=>22,'h'=>20);	
	}
	public function getCoords() {
		return 'S.M.setCenter('.$this->lat.','.$this->lng.','.$this->zoom.');S.M.remMarket();S.M.addMarker();';
	}
	public function getJS() {
		return $this->js;	
	}
	public function getSearch() {
		return $this->search;	
	}
	public function getVars() {
		return $this->vars;
	}
	public function write() {
		$this->vars['id'] = $this->id;
		if ($this->load_js) {
			$ret .= '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key='.$this->api_key.'" type="text/javascript"></script>';
			$ret .= '<script src="/'.HTTP_EXT.'tpls/js/markerclusterer.js" type="text/javascript"></script>';
		}
		$ret .= '<script type="text/javascript">S.M.set('.json($this->vars).');S.M.run=function(){'.$this->getJS().'};S.M.init();</script>';
		$ret .= '<div class="map_wrapper" style="margin:0 auto;position:relative;overflow:hidden;height:'.$this->height.'px;width:'.$this->width.'px;'.$this->style.'"><div id="'.$this->id.'" style="width:'.$this->width.'px;height:'.($this->height+40).'px;display:block"></div></div>';
		return $ret;
	}

	public static function distance($lat1, $lon1, $lat2, $lon2, $unit) {
		$theta = $lon1 - $lon2; 
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
		$dist = acos($dist); 
		$dist = rad2deg($dist); 
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
		
		if ($unit == 'K') {
			return ($miles * 1.609344); 
		} else if ($unit == 'N') {
			return ($miles * 0.8684);
		} else {
			return $miles;
		}
	}
	/*
	echo distance(32.9697, -96.80322, 29.46786, -98.53506, "m") . " miles<br>";
	echo distance(32.9697, -96.80322, 29.46786, -98.53506, "k") . " kilometers<br>";
	echo distance(32.9697, -96.80322, 29.46786, -98.53506, "n") . " nautical miles<br>";
	*/
	private function getMarkers() {
		$select = array('id', 'title', 'main_photo','street','house','lat','lng');
				
		$lat = (float)get('lat');
		$lng = (float)get('lng');
		
		$zoom = intval(get('zoom','',10));
		$minX = intval(get('minX','',-90));
		$maxX = intval(get('maxX','',90));
		$minY = intval(get('minY','',-32));
		$maxY = intval(get('maxY','',60));

		/*
		$sel = '((ACOS(SIN('.$lat.' * PI() / 180) * SIN(lat * PI() / 180) + COS('.$lat.' * PI() / 180) * COS(lat * PI() / 180) * COS(('.$lng.' - lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance';
		$sql .= ' WHERE zoom<='.$zoom.' HAVING distance<=\'100\'';
		*/
		
		$no_zoom = true;
		$zoom += 3;
		
		$sql = 'SELECT `'.join('`, `',$select).'`, \'0\' AS type, \'green\' AS icon FROM '.$this->prefix.'grid_ads';
		$sql .= ' WHERE'.($no_zoom?' TRUE':' (zoom<='.$zoom.')').' AND hide_map=\'0\' AND (lng BETWEEN '.$minX.' AND '.$maxX.') AND (lat BETWEEN '.$minY.' AND '.$maxY.')';

		if (get('userid')) {
			$sql .= ' AND userid='.(int)get('userid');
		}
		$qry = DB::qry($sql,0,0);
		
		$select[] = 'type';
		$select[] = 'icon';
		
		if (SITE_TYPE=='xml') {
			if (class_exists('DOMDocument')) {
				$dom = new DOMDocument('1.0');
				$node = $dom->createElement('markers');
				$parnode = $dom->appendChild($node); 
				while ($rs = DB::fetch($qry)) {
					$rs['descr'] = nl2br($rs['descr']);
					$node = $dom->createElement('marker'); 
					$newnode = $parnode->appendChild($node);
				//	$rs['title'] .= p($_GET,0).p($rs,0);
					foreach ($select as $s) $newnode->setAttribute($s,$rs[$s]);
				}
				echo $dom->saveXML();
			} else {
				echo '<?xml version="1.0" ?>';
				echo "\n<markers>";
				while ($rs = DB::fetch($qry)) {
					$rs['descr'] = nl2br($rs['descr']);
					echo "\n\t<marker ";
					foreach ($select as $s) echo $s.'="'.str_replace('"','&quot;',$rs[$s]).'" ';
					echo '/>';
				}
				echo "\n</markers>";
			}
			DB::free($qry);	
		}
		elseif (SITE_TYPE=='json') {
			$ret = array();
			while ($rs = DB::fetch($qry)) {
				array_push($ret,$rs);
			}
			DB::free($qry);	
			return $ret;
		}
	}
}