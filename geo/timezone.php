<?php 

function get_time_zone($country,$region) {
	switch ($country) { 
		case 'US':
		switch ($region) { 
		case 'AL':
			$t = 'America/Chicago';
		break; 
		case 'AK':
			$t = 'America/Anchorage';
		break; 
		case 'AZ':
			$t = 'America/Phoenix';
		break; 
		case 'AR':
			$t = 'America/Chicago';
		break; 
		case 'CA':
			$t = 'America/Los_Angeles';
		break; 
		case 'CO':
			$t = 'America/Denver';
		break; 
		case 'CT':
			$t = 'America/New_York';
		break; 
		case 'DE':
			$t = 'America/New_York';
		break; 
		case 'DC':
			$t = 'America/New_York';
		break; 
		case 'FL':
			$t = 'America/New_York';
		break; 
		case 'GA':
			$t = 'America/New_York';
		break; 
		case 'HI':
			$t = 'Pacific/Honolulu';
		break; 
		case 'ID':
			$t = 'America/Denver';
		break; 
		case 'IL':
			$t = 'America/Chicago';
		break; 
		case 'IN':
			$t = 'America/Indianapolis';
		break; 
		case 'IA':
			$t = 'America/Chicago';
		break; 
		case 'KS':
			$t = 'America/Chicago';
		break; 
		case 'KY':
			$t = 'America/New_York';
		break; 
		case 'LA':
			$t = 'America/Chicago';
		break; 
		case 'ME':
			$t = 'America/New_York';
		break; 
		case 'MD':
			$t = 'America/New_York';
		break; 
		case 'MA':
			$t = 'America/New_York';
		break; 
		case 'MI':
			$t = 'America/New_York';
		break; 
		case 'MN':
			$t = 'America/Chicago';
		break; 
		case 'MS':
			$t = 'America/Chicago';
		break; 
		case 'MO':
			$t = 'America/Chicago';
		break; 
		case 'MT':
			$t = 'America/Denver';
		break; 
		case 'NE':
			$t = 'America/Chicago';
		break; 
		case 'NV':
			$t = 'America/Los_Angeles';
		break; 
		case 'NH':
			$t = 'America/New_York';
		break; 
		case 'NJ':
			$t = 'America/New_York';
		break; 
		case 'NM':
			$t = 'America/Denver';
		break; 
		case 'NY':
			$t = 'America/New_York';
		break; 
		case 'NC':
			$t = 'America/New_York';
		break; 
		case 'ND':
			$t = 'America/Chicago';
		break; 
		case 'OH':
			$t = 'America/New_York';
		break; 
		case 'OK':
			$t = 'America/Chicago';
		break; 
		case 'OR':
			$t = 'America/Los_Angeles';
		break; 
		case 'PA':
			$t = 'America/New_York';
		break; 
		case 'RI':
			$t = 'America/New_York';
		break; 
		case 'SC':
			$t = 'America/New_York';
		break; 
		case 'SD':
			$t = 'America/Chicago';
		break; 
		case 'TN':
			$t = 'America/Chicago';
		break; 
		case 'TX':
			$t = 'America/Chicago';
		break; 
		case 'UT':
			$t = 'America/Denver';
		break; 
		case 'VT':
			$t = 'America/New_York';
		break; 
		case 'VA':
			$t = 'America/New_York';
		break; 
		case 'WA':
			$t = 'America/Los_Angeles';
		break; 
		case 'WV':
			$t = 'America/New_York';
		break; 
		case 'WI':
			$t = 'America/Chicago';
		break; 
		case 'WY':
			$t = 'America/Denver';
		break; 
		} 
		break; 
		case 'CA':
		switch ($region) { 
		case 'AB':
			$t = 'America/Edmonton';
		break; 
		case 'BC':
			$t = 'America/Vancouver';
		break; 
		case 'MB':
			$t = 'America/Winnipeg';
		break; 
		case 'NB':
			$t = 'America/Halifax';
		break; 
		case 'NL':
			$t = 'America/St_Johns';
		break; 
		case 'NT':
			$t = 'America/Yellowknife';
		break; 
		case 'NS':
			$t = 'America/Halifax';
		break; 
		case 'NU':
			$t = 'America/Rankin_Inlet';
		break; 
		case 'ON':
			$t = 'America/Rainy_River';
		break; 
		case 'PE':
			$t = 'America/Halifax';
		break; 
		case 'QC':
			$t = 'America/Montreal';
		break; 
		case 'SK':
			$t = 'America/Regina';
		break; 
		case 'YT':
			$t = 'America/Whitehorse';
		break; 
		} 
		break; 
		case 'AU':
		switch ($region) { 
		case '01':
			$t = 'Australia/Canberra';
		break; 
		case '02':
			$t = 'Australia/NSW';
		break; 
		case '03':
			$t = 'Australia/North';
		break; 
		case '04':
			$t = 'Australia/Queensland';
		break; 
		case '05':
			$t = 'Australia/South';
		break; 
		case '06':
			$t = 'Australia/Tasmania';
		break; 
		case '07':
			$t = 'Australia/Victoria';
		break; 
		case '08':
			$t = 'Australia/West';
		break; 
		} 
		break; 
		case 'AS':
			$t = 'US/Samoa';
		break; 
		case 'CI':
			$t = 'Africa/Abidjan';
		break; 
		case 'GH':
			$t = 'Africa/Accra';
		break; 
		case 'DZ':
			$t = 'Africa/Algiers';
		break; 
		case 'ER':
			$t = 'Africa/Asmera';
		break; 
		case 'ML':
			$t = 'Africa/Bamako';
		break; 
		case 'CF':
			$t = 'Africa/Bangui';
		break; 
		case 'GM':
			$t = 'Africa/Banjul';
		break; 
		case 'GW':
			$t = 'Africa/Bissau';
		break; 
		case 'CG':
			$t = 'Africa/Brazzaville';
		break; 
		case 'BI':
			$t = 'Africa/Bujumbura';
		break; 
		case 'EG':
			$t = 'Africa/Cairo';
		break; 
		case 'MA':
			$t = 'Africa/Casablanca';
		break; 
		case 'GN':
			$t = 'Africa/Conakry';
		break; 
		case 'SN':
			$t = 'Africa/Dakar';
		break; 
		case 'DJ':
			$t = 'Africa/Djibouti';
		break; 
		case 'SL':
			$t = 'Africa/Freetown';
		break; 
		case 'BW':
			$t = 'Africa/Gaborone';
		break; 
		case 'ZW':
			$t = 'Africa/Harare';
		break; 
		case 'ZA':
			$t = 'Africa/Johannesburg';
		break; 
		case 'UG':
			$t = 'Africa/Kampala';
		break; 
		case 'SD':
			$t = 'Africa/Khartoum';
		break; 
		case 'RW':
			$t = 'Africa/Kigali';
		break; 
		case 'NG':
			$t = 'Africa/Lagos';
		break; 
		case 'GA':
			$t = 'Africa/Libreville';
		break; 
		case 'TG':
			$t = 'Africa/Lome';
		break; 
		case 'AO':
			$t = 'Africa/Luanda';
		break; 
		case 'ZM':
			$t = 'Africa/Lusaka';
		break; 
		case 'GQ':
			$t = 'Africa/Malabo';
		break; 
		case 'MZ':
			$t = 'Africa/Maputo';
		break; 
		case 'LS':
			$t = 'Africa/Maseru';
		break; 
		case 'SZ':
			$t = 'Africa/Mbabane';
		break; 
		case 'SO':
			$t = 'Africa/Mogadishu';
		break; 
		case 'LR':
			$t = 'Africa/Monrovia';
		break; 
		case 'KE':
			$t = 'Africa/Nairobi';
		break; 
		case 'TD':
			$t = 'Africa/Ndjamena';
		break; 
		case 'NE':
			$t = 'Africa/Niamey';
		break; 
		case 'MR':
			$t = 'Africa/Nouakchott';
		break; 
		case 'BF':
			$t = 'Africa/Ouagadougou';
		break; 
		case 'ST':
			$t = 'Africa/Sao_Tome';
		break; 
		case 'LY':
			$t = 'Africa/Tripoli';
		break; 
		case 'TN':
			$t = 'Africa/Tunis';
		break; 
		case 'AI':
			$t = 'America/Anguilla';
		break; 
		case 'AG':
			$t = 'America/Antigua';
		break; 
		case 'AW':
			$t = 'America/Aruba';
		break; 
		case 'BB':
			$t = 'America/Barbados';
		break; 
		case 'BZ':
			$t = 'America/Belize';
		break; 
		case 'CO':
			$t = 'America/Bogota';
		break; 
		case 'VE':
			$t = 'America/Caracas';
		break; 
		case 'KY':
			$t = 'America/Cayman';
		break; 
		case 'MX':
			$t = 'America/Chihuahua';
		break; 
		case 'CR':
			$t = 'America/Costa_Rica';
		break; 
		case 'DM':
			$t = 'America/Dominica';
		break; 
		case 'SV':
			$t = 'America/El_Salvador';
		break; 
		case 'GD':
			$t = 'America/Grenada';
		break; 
		case 'FR':
			$t = 'Europe/Paris';
		break; 
		case 'GP':
			$t = 'America/Guadeloupe';
		break; 
		case 'GT':
			$t = 'America/Guatemala';
		break; 
		case 'EC':
			$t = 'America/Guayaquil';
		break; 
		case 'GY':
			$t = 'America/Guyana';
		break; 
		case 'CU':
			$t = 'America/Havana';
		break; 
		case 'JM':
			$t = 'America/Jamaica';
		break; 
		case 'BO':
			$t = 'America/La_Paz';
		break; 
		case 'PE':
			$t = 'America/Lima';
		break; 
		case 'NI':
			$t = 'America/Managua';
		break; 
		case 'MQ':
			$t = 'America/Martinique';
		break; 
		case 'AR':
			$t = 'America/Mendoza';
		break; 
		case 'UY':
			$t = 'America/Montevideo';
		break; 
		case 'MS':
			$t = 'America/Montserrat';
		break; 
		case 'BS':
			$t = 'America/Nassau';
		break; 
		case 'PA':
			$t = 'America/Panama';
		break; 
		case 'SR':
			$t = 'America/Paramaribo';
		break; 
		case 'PR':
			$t = 'America/Puerto_Rico';
		break; 
		case 'KN':
			$t = 'America/St_Kitts';
		break; 
		case 'LC':
			$t = 'America/St_Lucia';
		break; 
		case 'VC':
			$t = 'America/St_Vincent';
		break; 
		case 'HN':
			$t = 'America/Tegucigalpa';
		break; 
		case 'YE':
			$t = 'Asia/Aden';
		break; 
		case 'KZ':
			$t = 'Asia/Almaty';
		break; 
		case 'JO':
			$t = 'Asia/Amman';
		break; 
		case 'TM':
			$t = 'Asia/Ashgabat';
		break; 
		case 'IQ':
			$t = 'Asia/Baghdad';
		break; 
		case 'BH':
			$t = 'Asia/Bahrain';
		break; 
		case 'AZ':
			$t = 'Asia/Baku';
		break; 
		case 'TH':
			$t = 'Asia/Bangkok';
		break; 
		case 'LB':
			$t = 'Asia/Beirut';
		break; 
		case 'KG':
			$t = 'Asia/Bishkek';
		break; 
		case 'BN':
			$t = 'Asia/Brunei';
		break; 
		case 'IN':
			$t = 'Asia/Calcutta';
		break; 
		case 'MN':
			$t = 'Asia/Choibalsan';
		break; 
		case 'CN':
			$t = 'Asia/Chongqing';
		break; 
		case 'LK':
			$t = 'Asia/Colombo';
		break; 
		case 'BD':
			$t = 'Asia/Dhaka';
		break; 
		case 'AE':
			$t = 'Asia/Dubai';
		break; 
		case 'TJ':
			$t = 'Asia/Dushanbe';
		break; 
		case 'HK':
			$t = 'Asia/Hong_Kong';
		break; 
		case 'TR':
			$t = 'Asia/Istanbul';
		break; 
		case 'ID':
			$t = 'Asia/Jakarta';
		break; 
		case 'IL':
			$t = 'Asia/Jerusalem';
		break; 
		case 'AF':
			$t = 'Asia/Kabul';
		break; 
		case 'PK':
			$t = 'Asia/Karachi';
		break; 
		case 'NP':
			$t = 'Asia/Katmandu';
		break; 
		case 'KW':
			$t = 'Asia/Kuwait';
		break; 
		case 'MO':
			$t = 'Asia/Macao';
		break; 
		case 'PH':
			$t = 'Asia/Manila';
		break; 
		case 'OM':
			$t = 'Asia/Muscat';
		break; 
		case 'CY':
			$t = 'Asia/Nicosia';
		break; 
		case 'KP':
			$t = 'Asia/Pyongyang';
		break; 
		case 'QA':
			$t = 'Asia/Qatar';
		break; 
		case 'MM':
			$t = 'Asia/Rangoon';
		break; 
		case 'SA':
			$t = 'Asia/Riyadh';
		break; 
		case 'KR':
			$t = 'Asia/Seoul';
		break; 
		case 'SG':
			$t = 'Asia/Singapore';
		break; 
		case 'TW':
			$t = 'Asia/Taipei';
		break; 
		case 'UZ':
			$t = 'Asia/Tashkent';
		break; 
		case 'GE':
			$t = 'Asia/Tbilisi';
		break; 
		case 'BT':
			$t = 'Asia/Thimphu';
		break; 
		case 'JP':
			$t = 'Asia/Tokyo';
		break; 
		case 'LA':
			$t = 'Asia/Vientiane';
		break; 
		case 'AM':
			$t = 'Asia/Yerevan';
		break; 
		case 'PT':
			$t = 'Atlantic/Azores';
		break; 
		case 'BM':
			$t = 'Atlantic/Bermuda';
		break; 
		case 'CV':
			$t = 'Atlantic/Cape_Verde';
		break; 
		case 'FO':
			$t = 'Atlantic/Faeroe';
		break; 
		case 'IS':
			$t = 'Atlantic/Reykjavik';
		break; 
		case 'GS':
			$t = 'Atlantic/South_Georgia';
		break; 
		case 'SH':
			$t = 'Atlantic/St_Helena';
		break; 
		case 'BR':
			$t = 'Brazil/Acre';
		break; 
		case 'CL':
			$t = 'Chile/Continental';
		break; 
		case 'NL':
			$t = 'Europe/Amsterdam';
		break; 
		case 'AD':
			$t = 'Europe/Andorra';
		break; 
		case 'GR':
			$t = 'Europe/Athens';
		break; 
		case 'YU':
			$t = 'Europe/Belgrade';
		break; 
		case 'DE':
			$t = 'Europe/Berlin';
		break; 
		case 'SK':
			$t = 'Europe/Bratislava';
		break; 
		case 'BE':
			$t = 'Europe/Brussels';
		break; 
		case 'RO':
			$t = 'Europe/Bucharest';
		break; 
		case 'HU':
			$t = 'Europe/Budapest';
		break; 
		case 'DK':
			$t = 'Europe/Copenhagen';
		break; 
		case 'IE':
			$t = 'Europe/Dublin';
		break; 
		case 'GI':
			$t = 'Europe/Gibraltar';
		break; 
		case 'FI':
			$t = 'Europe/Helsinki';
		break; 
		case 'UA':
			$t = 'Europe/Kiev';
		break; 
		case 'SI':
			$t = 'Europe/Ljubljana';
		break; 
		case 'GB':
			$t = 'Europe/London';
		break; 
		case 'LU':
			$t = 'Europe/Luxembourg';
		break; 
		case 'ES':
			$t = 'Europe/Madrid';
		break; 
		case 'MT':
			$t = 'Europe/Malta';
		break; 
		case 'BY':
			$t = 'Europe/Minsk';
		break; 
		case 'MC':
			$t = 'Europe/Monaco';
		break; 
		case 'RU':
			$t = 'Europe/Moscow';
		break; 
		case 'NO':
			$t = 'Europe/Oslo';
		break; 
		case 'CZ':
			$t = 'Europe/Prague';
		break; 
		case 'LV':
			$t = 'Europe/Riga';
		break; 
		case 'IT':
			$t = 'Europe/Rome';
		break; 
		case 'SM':
			$t = 'Europe/San_Marino';
		break; 
		case 'BA':
			$t = 'Europe/Sarajevo';
		break; 
		case 'MK':
			$t = 'Europe/Skopje';
		break; 
		case 'BG':
			$t = 'Europe/Sofia';
		break; 
		case 'SE':
			$t = 'Europe/Stockholm';
		break; 
		case 'EE':
			$t = 'Europe/Tallinn';
		break; 
		case 'AL':
			$t = 'Europe/Tirane';
		break; 
		case 'LI':
			$t = 'Europe/Vaduz';
		break; 
		case 'VA':
			$t = 'Europe/Vatican';
		break; 
		case 'AT':
			$t = 'Europe/Vienna';
		break; 
		case 'LT':
			$t = 'Europe/Vilnius';
		break; 
		case 'PL':
			$t = 'Europe/Warsaw';
		break; 
		case 'HR':
			$t = 'Europe/Zagreb';
		break; 
		case 'IR':
			$t = 'Asia/Tehran';
		break; 
		case 'NZ':
			$t = 'Pacific/Auckland';
		break; 
		case 'MG':
			$t = 'Indian/Antananarivo';
		break; 
		case 'CX':
			$t = 'Indian/Christmas';
		break; 
		case 'CC':
			$t = 'Indian/Cocos';
		break; 
		case 'KM':
			$t = 'Indian/Comoro';
		break; 
		case 'MV':
			$t = 'Indian/Maldives';
		break; 
		case 'MU':
			$t = 'Indian/Mauritius';
		break; 
		case 'YT':
			$t = 'Indian/Mayotte';
		break; 
		case 'RE':
			$t = 'Indian/Reunion';
		break; 
		case 'FJ':
			$t = 'Pacific/Fiji';
		break; 
		case 'TV':
			$t = 'Pacific/Funafuti';
		break; 
		case 'GU':
			$t = 'Pacific/Guam';
		break; 
		case 'NR':
			$t = 'Pacific/Nauru';
		break; 
		case 'NU':
			$t = 'Pacific/Niue';
		break; 
		case 'NF':
			$t = 'Pacific/Norfolk';
		break; 
		case 'PW':
			$t = 'Pacific/Palau';
		break; 
		case 'PN':
			$t = 'Pacific/Pitcairn';
		break; 
		case 'CK':
			$t = 'Pacific/Rarotonga';
		break; 
		case 'WS':
			$t = 'Pacific/Samoa';
		break; 
		case 'KI':
			$t = 'Pacific/Tarawa';
		break; 
		case 'TO':
			$t = 'Pacific/Tongatapu';
		break; 
		case 'WF':
			$t = 'Pacific/Wallis';
		break; 
		case 'TZ':
			$t = 'Africa/Dar_es_Salaam';
		break; 
		case 'VN':
			$t = 'Asia/Phnom_Penh';
		break; 
		case 'KH':
			$t = 'Asia/Phnom_Penh';
		break; 
		case 'CM':
			$t = 'Africa/Lagos';
		break; 
		case 'DO':
			$t = 'America/Santo_Domingo';
		break; 
		case 'TL':
			$t = 'Asia/Jakarta';
		break; 
		case 'ET':
			$t = 'Africa/Addis_Ababa';
		break; 
		case 'FX':
			$t = 'Europe/Paris';
		break; 
		case 'GL':
			$t = 'America/Godthab';
		break; 
		case 'HT':
			$t = 'America/Port-au-Prince';
		break; 
		case 'CH':
			$t = 'Europe/Zurich';
		break; 
		case 'AN':
			$t = 'America/Curacao';
		break; 
		case 'BJ':
			$t = 'Africa/Porto-Novo';
		break; 
		case 'EH':
			$t = 'Africa/El_Aaiun';
		break; 
		case 'FK':
			$t = 'Atlantic/Stanley';
		break; 
		case 'GF':
			$t = 'America/Cayenne';
		break; 
		case 'IO':
			$t = 'Indian/Chagos';
		break; 
		case 'MD':
			$t = 'Europe/Chisinau';
		break; 
		case 'MP':
			$t = 'Pacific/Saipan';
		break; 
		case 'MW':
			$t = 'Africa/Blantyre';
		break; 
		case 'NA':
			$t = 'Africa/Windhoek';
		break; 
		case 'NC':
			$t = 'Pacific/Noumea';
		break; 
		case 'PG':
			$t = 'Pacific/Port_Moresby';
		break; 
		case 'PM':
			$t = 'America/Miquelon';
		break; 
		case 'PS':
			$t = 'Asia/Gaza';
		break; 
		case 'PY':
			$t = 'America/Asuncion';
		break; 
		case 'SB':
			$t = 'Pacific/Guadalcanal';
		break; 
		case 'SC':
			$t = 'Indian/Mahe';
		break; 
		case 'SJ':
			$t = 'Arctic/Longyearbyen';
		break; 
		case 'SY':
			$t = 'Asia/Damascus';
		break; 
		case 'TC':
			$t = 'America/Grand_Turk';
		break; 
		case 'TF':
			$t = 'Indian/Kerguelen';
		break; 
		case 'TK':
			$t = 'Pacific/Fakaofo';
		break; 
		case 'TT':
			$t = 'America/Port_of_Spain';
		break; 
		case 'VG':
			$t = 'America/Tortola';
		break; 
		case 'VI':
			$t = 'America/St_Thomas';
		break; 
		case 'VU':
			$t = 'Pacific/Efate';
		break; 
		case 'RS':
			$t = 'Europe/Belgrade';
		break; 
		case 'ME':
			$t = 'Europe/Podgorica';
		break; 
		case 'AX':
			$t = 'Europe/Mariehamn';
		break; 
		case 'GG':
			$t = 'Europe/Guernsey';
		break; 
		case 'IM':
			$t = 'Europe/Isle_of_Man';
		break; 
		case 'JE':
			$t = 'Europe/Jersey';
		break; 
		case 'BL':
			$t = 'America/St_Barthelemy';
		break; 
		case 'MF':
			$t = 'America/Marigot';
		break; 
		default:
			$t = '';
		break;
	}
	return 	$t; 
} 
