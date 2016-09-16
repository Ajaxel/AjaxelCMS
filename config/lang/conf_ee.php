<?php

/**
* Eesti
*/
$_conf['LOCALE'] = array(
	'locale'		=> 'ee_EE',
	'date_format'	=> 'm.d.Y',
	'date_input'	=> '%m.%d.%Y',
	'time_format'	=> 'H:i',
	'time_input'	=> '%I:%M %p',
	'money_format'	=> '%€',
	'number_format'	=> '2.,',
	'week_start'	=> 1
);


$_conf['ARR_DATE_FORMAT'] = array (
	'short'		=> '%d.%m.%Y',
	'short2' 	=> '%d %b %Y',
	'long'		=> '%H:%M %d-%m-%Y',
	'long2'		=> '%H:%M %d-%b-%Y'
);

$_conf['ARR_PERIOD'] = array('sekund', 'minut', 'tund', 'p&auml;ev', 'n&auml;dal', 'kuu', 'aasta', 'aastak&uuml;mme');
$_conf['ARR_PERIODS'] = array('sekundit', 'minutit', 'tundi', 'p&auml;eva', 'n&auml;dalat', 'kuud', 'aastat', 'aastak&uuml;mneid');
$_conf['ARR_PERIODS_MED'] = array('sekundit', 'minutit', 'tundi', 'p&auml;eva', 'n&auml;dalat', 'kuud', 'aastat', 'aastak&uuml;mneid');

$_conf['ARR_NUMBERS'] = array('null', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety' , 'hundred' => 'hundred', 'thousand'=> 'thousand', 'million'=>'million', 'separator'=>' ja ', 'minus'=>'minus','ago'=>' tagasi','since'=>' since');
						   
$_conf['ARR_MONTHS'] = array('jaanuar', 'veebruar', 'm&auml;rts', 'aprill', 'mai', 'juuni', 'juuli', 'august', 'september', 'oktoober', 'november', 'detsember');
$_conf['ARR_MONTHS_MED'] = array('jaanuar', 'veebruar', 'm&auml;rts', 'aprill', 'mai', 'juuni', 'juuli', 'august', 'september', 'oktoober', 'november', 'detsember');
$_conf['ARR_SHORT_MONTHS'] = array('jan', 'veb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'det');
$_conf['ARR_WEEKDAYS'] = array ('pühapäev', 'esmaspäev', 'teisipäev', 'kolmapäev', 'neljapäev', 'reede', 'laupäev');
$_conf['ARR_SHORT_WEEKDAYS'] = array('p&uuml;', 'es', 'te', 'ko', 'ne', 're', 'la');
$_conf['ARR_ONE_WEEKDAYS'] = array('P', 'E', 'T', 'K', 'N', 'R', 'L');