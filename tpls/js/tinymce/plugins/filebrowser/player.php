<?php
if (isset($_GET['song']) && $_GET['song']){
	$curname = explode("/", str_replace('\\','/',$_GET['song']));
	$curname = array_pop($curname);
	$curname = str_replace(".mp3", "", $curname);
	$curname = str_replace("_", " ", $curname);
	$curname = htmlspecialchars($curname);
	$_GET['song'] = addslashes(htmlspecialchars($_GET['song']));
} else {
	exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Player: <?php $curname?></title>
<script src="../../../../templates/default/scripts/AC_RunActiveContent.js" type="text/javascript"></script>
<style type="text/css">
body{
	padding: 0px;
	margin: 0px;
	border: none;
}
</style>
</head>
<body>
<table width=100% height=160>
<tr>
<td valign=middle>
<center>
<script type="text/javascript">
AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0','width','250','height','84','src','music?curname=<?=$curname?>&curmusic=<?=$_GET['song']?>','quality','high','pluginspage','http://www.macromedia.com/go/getflashplayer','movie','music?curname=<?=$curname?>&curmusic=<?=$_GET['song']?>' ); //end AC code
</script><noscript><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="260" height="120">
  <param name="movie" value="music.swf?curname=<?=$curname?>&curmusic=<?=$_GET['song']?>" />
  <param name="quality" value="high" />
  <embed src="music.swf?curname=<?=$curname?>&curmusic=<?=$_GET['song']?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="250" height="84"></embed>
</object></noscript>
</center>
</td>
</tr>
</table>
</body>
</html>
