<?php


define ('FTP_DIR_ROOT', str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__)).'/'));
require FTP_DIR_ROOT.'config/config.php';
if (!defined(DB_HOST)) {
	$DB = $_COOKIE['db'];
	if (!$DB) $DB = key($_conf['DB']);
	define('DB_HOST', $_conf['DB'][$DB]['HOST']);
	define('DB_USERNAME', $_conf['DB'][$DB]['USERNAME']);
	define('DB_PASSWORD', $_conf['DB'][$DB]['PASSWORD']);
	define('DB_NAME', $_conf['DB'][$DB]['NAME']);
	define('DB_PREFIX', $_conf['DB'][$DB]['PREFIX']);
}

function ex($e = false) {
	if ($e) {
		die($e);	
	}
	header('Content-Type: image/png');
	echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
	exit;
}


if ($_GET['email']) {
	(mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) && mysql_select_db(DB_NAME)) or ex(mysql_error());
	if (!mysql_fetch_row(mysql_query('SELECT 1 FROM '.DB_PREFIX.'emails WHERE email=\''.mysql_real_escape_string($_GET['email']).'\''))) {
		ex();
	}
	if ($_GET['campaign']) {
		if (mysql_fetch_row(mysql_query('SELECT 1 FROM '.DB_PREFIX.'emails_read WHERE email=\''.mysql_real_escape_string($_GET['email']).'\' AND campaign='.(int)$_GET['campaign']))) {
			ex();	
		}
	}
	$sql = 'UPDATE '.DB_PREFIX.'emails SET `read`=`read`+1 WHERE email=\''.mysql_real_escape_string($_GET['email']).'\'';
	
	@mysql_query($sql) or ex(mysql_error());
	if ($_GET['campaign']) {
		$sql = 'UPDATE '.DB_PREFIX.'emails_camp SET `read`=`read`+1 WHERE id='.(int)$_GET['campaign'].'';
		
		@mysql_query($sql) or ex(mysql_error());
		
		$sql = 'REPLACE INTO '.DB_PREFIX.'emails_read (email, campaign, `read`, `clicked`) VALUES (\''.mysql_real_escape_string($_GET['email']).'\', '.(int)$_GET['campaign'].', '.time().', 0)';
		@mysql_query($sql) or ex(mysql_error());
	}
}
ex();
