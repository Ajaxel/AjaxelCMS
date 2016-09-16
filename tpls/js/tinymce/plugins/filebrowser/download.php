<?php
require("config.php");

if(isset($_GET['file']) && isset($_GET['dir'])){
	$_GET['dir'] = check_dir_addres($_GET['dir']);
	$work_dir = $work_folder.check_dir_addres($_GET['dir']);
	
	if(file_exists($work_dir.$_GET['file'])){
		
		header( "Content-Type: application/octet-stream");
		header( "Content-Length: ".filesize($work_dir.$_GET['file']));
		header( "Content-Disposition: attachment; filename=".$_GET['file']);
		readfile($work_dir.$_GET['file']);
		
		exit;
	}
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
 <Meta name="author" Content="web">
 <Meta Name="Data" Content="11:32 03.12.2006">
 <Meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <style type="text/css">
  body, table, div, p, form, input, textarea{
	font-family: arial;
	font-size: 11px;
  }
 </style>
 <title>File Downloader</title>
</head>

 <body>
<?php
if(isset($_GET['file']) && isset($_GET['dir'])){
	echo "File not found.";
}else{
	echo "No File selected";
}
?>
 </body>
</html>