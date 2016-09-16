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
* @file       inc/Slots.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Slots {
	
	private
		$My,
	
		$temp = 0,
		$spins  = 0,
		$barrel = array(),
		$game = array(),
		$line_num = 9,
		$coins = 1,
		$first = 0,
		$credits = 0,
		$nuggets = 0,
		$win = 0,
		$free = 0,
		$losses = 0,
		$winnings = 0,
		$wins = array(),
		$matched = 1,
		$scatters,
		$double_up = array(),
		$blink = array(),
		$blink_lines = array(),
		$win_icon = false,
		$bigwin = false,
		$by_nuggets = false,
		$error = ''
	;
	
	const 
		NORMAL	= 0,
		WIN		= 1,
		LOOSE	= 2,
		START	= 3,
		JACKPOT	= 4,
		KILL	= 5,
		DOUBLE	= 6
	;
	
	public $barrels = array(
		self::START => array (
			1, 1, 1, 1, 1, 1, 1, 1, 1,
			2, 2, 2, 2, 2, 2, 2, 2,
			4, 4, 4, 4, 4, 4, 4,
			5, 5, 5, 5, 5, 5,
			6, 6, 6, 6, 6,
			7, 7, 7, 7,
			8, 8, 8,
			9, 9,
			10, 10, 
			3, 3, 3, 
		),
		self::NORMAL => array (
			1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
			2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
			4, 4, 4, 4, 4, 4, 4, 4, 4, 4,
			5, 5, 5, 5, 5, 5, 5, 5,
			6, 6, 6, 6, 6, 6,
			7, 7, 7, 7, 7,
			8, 8, 8, 8,
			9, 9, 9,
			10, 10,
			3, 3,
		),
		self::LOOSE => array (
			1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
			2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2,
			4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4,
			5, 5, 5, 5, 5,
			6, 6, 6,
			7, 7,
			8,
			9,	
			10, 10,
		),
		self::WIN => array (
			4, 4, 4, 4,
			5, 5, 5, 5, 5,
			6, 6, 6, 6,
			7, 7, 7,
			8, 8, 8, 8,
			9, 9, 9, 9, 9,
			3, 3, 3, 3,
		),
		self::JACKPOT => array (
			1, 1, 1, 1, 1, 1,
			2, 2, 2, 2, 2, 2,
			4, 4, 4, 4, 4, 4,
			5, 5, 5, 5, 5,
			6, 6, 6,
			7, 7, 7,
			8, 8, 8,
			9, 9, 9,
			10,
			3, 3, 3, 3, 3, 3
		),
		self::KILL => array(
			1, 1, 1, 1, 1, 1, 1,
			2, 2, 2, 2, 2, 2, 2,
			4, 4, 4, 4,
			5, 5, 5,
			6, 6,
			7, 7,
			8,
			9,
			10, 10, 10, 10,
		),
		self::DOUBLE => array(
			1, 2, 3, 4, 5, 6, 7, 8, 9, 10
		)
	);
	
	/*
	public $_prices = array(
		1 => array(
			3 => 1,
			4 => 3,
			5 => 25
		),
		2 => array(
			3 => 2,
			4 => 5,
			5 => 50
		),
		4 => array(
			3 => 5,
			4 => 10,
			5 => 100
		),
		5 => array(
			3 => 5,
			4 => 25,
			5 => 150
		),
		6 => array(
			2 => 1,
			3 => 10,
			4 => 50,
			5 => 250
		),
		7 => array(
			2 => 2,
			3 => 25,
			4 => 100,
			5 => 500
		),
		8 => array(
			2 => 5,
			3 => 50,
			4 => 250,
			5 => 1000
		),
		9 => array(
			2 => 10,
			3 => 100,
			4 => 500,
			5 => 2500
		),
		
		10 => array(
			5 => 5000
		),
		3 => array(
			5 => 10000
		)
	);
	*/
	
	
	public $prices = array(
		1 => array(
			3 => 1,
			4 => 2,
			5 => 20
		),
		2 => array(
			3 => 1,
			4 => 3,
			5 => 40
		),
		4 => array(
			3 => 2,
			4 => 5,
			5 => 80
		),
		5 => array(
			3 => 5,
			4 => 20,
			5 => 120
		),
		6 => array(
			2 => 1,
			3 => 8,
			4 => 40,
			5 => 200
		),
		
		7 => array(
			2 => 2,
			3 => 20,
			4 => 80,
			5 => 500
		),
		8 => array(
			2 => 5,
			3 => 50,
			4 => 200,
			5 => 1000
		),
		9 => array(
			2 => 10,
			3 => 100,
			4 => 500,
			5 => 2500
		),
		
		10 => array(
			5 => 5000
		),
		3 => array(
			5 => 10000
		)
	);
	

	public $lines = array(
		1	=> array(array(0,1), array(1,1), array(2,1), array(3,1), array(4,1)),
		2	=> array(array(0,0), array(1,0), array(2,0), array(3,0), array(4,0)),
		3	=> array(array(0,2), array(1,2), array(2,2), array(3,2), array(4,2)),
		4	=> array(array(0,0), array(1,1), array(2,2), array(3,1), array(4,0)),
		5	=> array(array(0,2), array(1,1), array(2,0), array(3,1), array(4,2)),
		
		6	=> array(array(0,0), array(1,0), array(2,1), array(3,2), array(4,2)),
		7	=> array(array(0,2), array(1,2), array(2,1), array(3,0), array(4,0)),
		8	=> array(array(0,1), array(1,0), array(2,1), array(3,2), array(4,1)),
		9	=> array(array(0,1), array(1,2), array(2,1), array(3,0), array(4,1)),
	);
	
	public
		$total_lines = 9,
		$wild = 3,
		$scatter = 10,
		$bigwin_price = 250
	;
	
	public 
		$for_first = array(self::KILL,self::NORMAL,self::LOOSE,self::START),
		$for_all = array(self::NORMAL,self::WIN,self::LOOSE,self::START,self::JACKPOT),
		$for_winnings = array(self::NORMAL,self::WIN,self::JACKPOT),
		$for_losses= array(self::NORMAL,self::LOOSE,self::KILL)
	;
	
	public $for_double = array(self::DOUBLE);
	
	public $callback = false;
	
	public function __construct(&$My, $credits = 0, $nuggets = 0, $by_nuggets = false) {
		$this->My =& $My;

		if (!isset($_SESSION['SLOTS']) || !$_SESSION['SLOTS']) $_SESSION['SLOTS'] = array(
			'free'		=> 0,
			'lines'		=> $this->line_num,
			'spins'		=> 0,
			'losses'	=> 0,
			'winnings'	=> 0,
			'coins'		=> $this->coins
		);
		
		$this->credits = $credits;
		$this->nuggets = $nuggets;
		$this->by_nuggets = $by_nuggets;
		
		$this->spins = $_SESSION['SLOTS']['spins'];
		$this->free = $_SESSION['SLOTS']['free'];
		$this->losses = $_SESSION['SLOTS']['losses'];
		$this->winnings = $_SESSION['SLOTS']['winnings'];
		$this->coins = $_SESSION['SLOTS']['coins'];
		
	}
	
	public function config($arr) {
		foreach ($arr as $k => $v) $this->$k = $v;
		return $this;
	}	
	
	public function callback($callback) {
		$this->callback = $callback;
		return $this;
	}
	
	private function barrel($barrel = false) {	
		if ($barrel==6) {
			$s = $this->for_double;
			$t = 0;
		}
		elseif ($barrel==1 && (($this->by_nuggets && $this->nuggets%2==0) || (!$this->by_nuggets && $this->credits%2==0))) {
			$t = 3;
			$s = $this->for_first; 
		}
		else {
			$t = 4;
			$s = $this->for_all;
		}
		
		if ($this->winnings>4) {
			$t = 2;
			$s = $this->for_winnings;
			$_SESSION['SLOTS']['winnings'] = 0;		
		}
		elseif ($this->losses>4) {
			$t = 2;
			$s = $this->for_losses;
			$_SESSION['SLOTS']['losses'] = 0;
		}
		elseif ($this->losses>2) {
			$t = 4;
			$s = $this->for_all;
		}
		elseif ($t && ($this->spins%2===0 || $this->free)) {
			$t = 3;
			unset($s[4]);
		}
		
		if ($t) {
			$this->temp = $s[mt_rand(0,$t)];
		} else {
			$this->temp = $s[0];	
		}

		shuffle($this->barrels[$this->temp]);		
		$this->total = count($this->barrels[$this->temp]);
		$part1 = floor($this->total / 3);
		$part2 = $part1 * 2;
		//return array(3,3,2);
		return array(
			$this->barrels[$this->temp][mt_rand(0,$part1)],
			$this->barrels[$this->temp][mt_rand($part1+1,$part2)],
			$this->barrels[$this->temp][mt_rand($part2+1,$this->total-1)]	
		);
		
	}
	
	private function matched($b) {
		$this->matched = 1;
				
		$this->first = $b[1];
		if ($b[1]==$this->wild) {
			$this->first = $b[2];
			if ($this->first==$this->wild) {
				$this->first = $b[3];
				if ($this->first==$this->wild) {
					$this->first = $b[4];
					if ($this->first==$this->wild) {
						$this->first = $b[5];	
					}
				}
			}
		}
		
		$b[1] = $this->first;
		if ($b[1]==$b[2] || $b[2]==$this->wild) {
			$this->matched = 2;
			$b[2] = $b[1];
			if ($b[2]==$b[3] || $b[3]==$this->wild) {
				$this->matched = 3;
				$b[3] = $b[2];
				if ($b[3]==$b[4] || $b[4]==$this->wild) {
					$this->matched = 4;
					$b[4] = $b[3];
					if ($b[4]==$b[5] || $b[5]==$this->wild) {
						$this->matched = 5;
					}
				}
			}
		}
		if ($this->matched==5) {
			$this->win_icon = $this->first;
		}
	}
	
	private function arr($a) {
		return $this->spin[$a[0]][$a[1]];	
	}
	
	private function sum($number) {
		$a = array();
		foreach ($this->lines[$number] as $i => $_a) {
			$a[$i+1] = $this->spin[$_a[0]][$_a[1]];
		}
		$this->matched($a);
		$win = 0;
		
		if (isset($this->prices[$this->first]) && array_key_exists($this->matched,$this->prices[$this->first])) {
			$win = $this->prices[$this->first][$this->matched];
		}
		
		$this->wins[$number] = $win;
		if ($win>0) $this->blink($number);
		if ($win>=$this->bigwin_price && (!$this->bigwin || $win > $this->bigwin[2])) {
			$this->bigwin = array($number, $this->matched, $win, $this->blink[$number]);
		}
	}
	
	private function blink($number) {
		if (!$this->blink[$number]) $this->blink[$number] = array();
		for ($i=0;$i<$this->matched;$i++) {
			$this->blink[$number][$i] = $this->lines[$number][$i];
		}
	}
	
	private function wins() {
		if (!$this->spin) return;
		foreach ($this->spin as $i => $a) {
			foreach ($a as $j => $_a) {
				if ($_a==$this->scatter) $this->scatters++;
			}
		}
		for ($i=1;$i<=$this->total_lines;$i++) {
			if ($this->line_num>=$i) $this->sum($i);
		}
	}
	
	public function lines($n) {
		if ($this->free) $this->line_num = $_SESSION['SLOTS']['lines'];
		else {
			$this->line_num = $n;
			$_SESSION['SLOTS']['lines'] = $n;
		}
		return $this;
	}
	
	public function coins($n) {
		if ($this->free) $this->coins = $_SESSION['SLOTS']['coins'];
		else {
			$this->coins = $n;
			$_SESSION['SLOTS']['coins'] = $n;
		}
		return $this;
	}
	
	private function not13($arr) {
		return array(0,$arr[1],0);	
	}
	
	private function double() {
		if (!$_SESSION['SLOTS']['win']) return false;
		$spin = array($this->not13($this->barrel(6)),$this->not13($this->barrel(6)),$this->not13($this->barrel(6)),$this->not13($this->barrel(6)),$this->not13($this->barrel(6)));
		$rand = mt_rand(1,4);
		$find = $spin[$rand][1];
		$spin[0][1] = $spin[$rand][1];
		$_SESSION['SLOTS']['double'] = $spin;
		$_SESSION['SLOTS']['find'] = $find;
		$this->spin = array(array(0,$find,0),array(0,0,0),array(0,0,0),array(0,0,0),array(0,0,0));
		return array('spin'=>$this->spin);
	}
	
	private function doubleup($answer) {
		if (!$_SESSION['SLOTS']['double'] || !$_SESSION['SLOTS']['win']) return false;
		$ex = explode('.',$answer);
		if ($_SESSION['SLOTS']['find']==$_SESSION['SLOTS']['double'][$ex[0]][$ex[1]]) {
			$this->credits = $this->credits + $_SESSION['SLOTS']['win'];
			$_SESSION['SLOTS']['win'] *= 2;
			$_SESSION['SLOTS']['double'][2][0] = 12;
			$ret = array('ok'=>true,'credits'=>$this->credits,'nuggets'=>$this->nuggets,'win'=>$_SESSION['SLOTS']['win'],'lost'=>0,'spin'=>$_SESSION['SLOTS']['double'],'by_nuggets' => $this->by_nuggets,'free'=>$_SESSION['SLOTS']['free'],'coins'=>$this->coins,'lines'=>$_SESSION['SLOTS']['lines']);	
		} else {
			$this->credits = $this->credits - $_SESSION['SLOTS']['win'];
			$lost = $_SESSION['SLOTS']['win'];
			$_SESSION['SLOTS']['win'] = 0;
			$_SESSION['SLOTS']['double'][2][0] = 11;
			$ret = array('ok'=>false,'credits'=>$this->credits,'nuggets'=>$this->nuggets,'win'=>0,'lost'=>$lost,'spin'=>$_SESSION['SLOTS']['double'],'by_nuggets' => $this->by_nuggets,'free'=>$_SESSION['SLOTS']['free'],'coins'=>$this->coins,'lines'=>$_SESSION['SLOTS']['lines']);
		}
		unset($_SESSION['SLOTS']['double'], $_SESSION['SLOTS']['find']);
		if ($this->callback && is_callable($this->callback)) $ret = call_user_func_array($this->callback, array('doubleup',$ret));
		return $ret;
	}
	
	public function json() {
		switch (post('do')) {
			case 'spin':
				//if ($_SESSION['SLOTS']['double']) break;
				return $this->lines(post('lines'))->coins(post('coins'))->game();
			break;
			case 'double':
				//if ($_SESSION['SLOTS']['double']) break;
				return $this->double();	
			break;
			case 'doubleup':
				return $this->doubleup(post('answer'));	
			break;
		}
	}
	
	public function game($fake = false) {	
		$this->spin = array($this->barrel(1),$this->barrel(2),$this->barrel(3),$this->barrel(4),$this->barrel(5));
		if (!$fake) {
			$this->win = $this->scatters = 0;
			$this->blink_lines = $this->blink = array();
			if ($this->free>0) $this->free--;
			elseif ($this->by_nuggets) {
				if ($this->nuggets>=$this->line_num * $this->coins) {
					$this->nuggets -= $this->line_num * $this->coins;
				} else {
					$this->spin = false;
					$this->error = 'NOT ENOUGH COINS';
				}
			}
			else {
				if ($this->credits >= $this->line_num * $this->coins) {
					$this->credits -= $this->line_num * $this->coins;
				} else {
					$this->spin = false;
					$this->error = 'NOT ENOUGH CREDITS';	
				}
			}
			if (!$this->error) {
				$this->wins();
				$this->win = array_sum($this->wins) * $this->coins;
				$_SESSION['SLOTS']['win'] = $this->win;
				if ($this->win > $this->line_num) {
					$_SESSION['SLOTS']['losses'] = 0;
				}
				else {
					$_SESSION['SLOTS']['losses']++;
				}
				if ($this->win) {
					$_SESSION['SLOTS']['winnings']++;
				} else {
					$_SESSION['SLOTS']['winnings'] = 0;
				}
				
				$this->credits += $this->win;
				if ($this->scatters==3) $this->free += 3;
				elseif ($this->scatters==4) $this->free += 10;
				elseif ($this->scatters==5) $this->free += 30;

				$_SESSION['SLOTS']['credits'] = $this->credits;
				$_SESSION['SLOTS']['nuggets'] = $this->nuggets;
				$_SESSION['SLOTS']['free'] = $this->free;
				$_SESSION['SLOTS']['spins']++;
				
				$blink = array();
				foreach ($this->blink as $line => $a) {
					$blink[] = $a;
					$this->blink_lines[] = $line;
				}
				$this->blink = $blink;
				
			}
		}
		
		$ret = array(
			'spin'	=> &$this->spin,
			'credits' => &$this->credits,
			'nuggets' => &$this->nuggets,
			'by_nuggets' => $this->by_nuggets,
			'coins'	=> $this->coins,
			'win' 	=> &$this->win,
			'free'	=> &$this->free,
			'blink'	=> &$this->blink,
			'blink_lines' => &$this->blink_lines,
			'win_icon' => &$this->win_icon,
			'bigwin'=> &$this->bigwin,
			'lines'	=> $_SESSION['SLOTS']['lines'],
			'error'	=> $this->error
		);

		if (!$fake) {
			if ($this->callback && is_callable($this->callback)) {
				$ret = call_user_func_array($this->callback, array('game',$ret));
			}
		}
		return $ret;
	}
	



	
}