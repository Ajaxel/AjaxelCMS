<?php

/**
* Lithuanian
*/
$_conf['LOCALE'] = array(
	'locale'		=> 'en_US',
	'date_format'	=> 'm.d.Y',
	'date_input'	=> '%m.%d.%Y',
	'time_format'	=> 'h:i A',
	'time_input'	=> '%I:%M %p',
	'money_format'	=> '%€',
	'number_format'	=> '2.,',
	'week_start'	=> 1 // 1 - monday, 7 - sunday
);

$_conf['ARR_DATE_FORMAT'] = array (
	'short'		=> '%d.%m.%Y',
	'short2' 	=> '%d %b %Y',
	'long'		=> '%H:%M %d-%m-%Y',
	'long2'		=> '%H:%M %d-%b-%Y'
);

$_conf['ARR_PERIOD'] = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
$_conf['ARR_PERIODS'] = array('seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years', 'decade');
$_conf['ARR_PERIOD_MED'] = array('seconds', 'minutes', 'hours', 'days', 'weeks', 'months', 'years', 'decade');

$_conf['ARR_NUMBERS'] = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety' , 'hundred' => 'hundred', 'thousand'=> 'thousand', 'million'=>'million', 'separator'=>' and ', 'minus'=>'minus','ago'=>' ago','since'=>' since');
						   
$_conf['ARR_MONTHS'] = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
$_conf['ARR_MONTHS_MED'] = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december');
$_conf['ARR_SHORT_MONTHS'] = array('jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec');
$_conf['ARR_WEEKDAYS'] = array('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday');
$_conf['ARR_SHORT_WEEKDAYS'] = array('Š', 'S', 'P', 'A', 'T', 'K', 'P');
$_conf['ARR_ONE_WEEKDAYS'] = array('Š', 'S', 'P', 'A', 'T', 'K', 'P');