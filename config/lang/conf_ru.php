<?php

/**
* Русский
*/
$_conf['LOCALE'] = array(
	'locale'		=> 'ru_RU',
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

$_conf['ARR_PERIOD'] = array('секунда', 'минута', 'час', 'день', 'неделя', 'месяц', 'год', 'декада');
$_conf['ARR_PERIODS'] = array('секунд', 'минут', 'часов', 'дней', 'недель', 'месяцев', 'лет', 'декад');
$_conf['ARR_PERIODS_MED'] = array('секунды', 'минуты', 'часа', 'дня', 'недели', 'месяца', 'года', 'декады');

$_conf['ARR_NUMBERS'] = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety' , 'hundred' => 'hundred', 'thousand'=> 'thousand', 'million'=>'million', 'separator'=>' и ', 'minus'=>'minus','ago'=>' ago','since'=>' since');
						   
$_conf['ARR_MONTHS'] = array('январь', 'февраль', 'март', 'апрель', 'май', 'июнь', 'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
$_conf['ARR_MONTHS_MED'] = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
$_conf['ARR_SHORT_MONTHS'] = array('янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
$_conf['ARR_WEEKDAYS'] = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');
$_conf['ARR_SHORT_WEEKDAYS'] = array('вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб');
$_conf['ARR_ONE_WEEKDAYS'] = array('В', 'П', 'В', 'С', 'Ч', 'П', 'С');