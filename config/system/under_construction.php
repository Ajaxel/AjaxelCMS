<?php
	if (is_file(FTP_DIR_TPL.'classes/under_construction.php')) {
		readfile(FTP_DIR_TPL.'classes/under_construction.php');
		return;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo DOMAIN?> - under construction</title>
<style>
body {background:#fff;font:13px Arial;color:#222;-background:url(b.jpg) center top;}

.logo {text-shadow: #666 1px 1px 1px;text-transform:uppercase;font:27px 'Trebuchet MS', Verdana;width:800px;margin:0 auto;position:relative;top:60px;color:#242424;text-align:center}
.uc {text-align:center;}
.ajaxel {color:#777;text-align:center;padding-top:4px;position:relative;top:-40px;width:500px;margin:0 auto}
.ajaxel a {color:#777;text-decoration:none}
.ajaxel a:hover {text-decoration:underline;color:#444}
</style>
</head>

<body>

<div class="wrap">
<div class="logo"><?php echo DOMAIN?></div>
<div class="uc"><img src="/tpls/img/under-construction.jpg" /></div>
<div class="ajaxel">or visit <a href="http://ajaxel.com">ajaxel.com</a> for now</div>
</div>

</body>
</html>