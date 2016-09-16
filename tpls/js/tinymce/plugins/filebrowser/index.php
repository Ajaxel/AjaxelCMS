<?php
require("config.php");
if ($_GET['type']) {
	if (in_array($_GET['type'], array(
		'image',
		'flash'
	))) {
		$_SESSION['type'] = $_GET['type'];
	} else {
		$_SESSION['type'] = "file";
	}
	header("Location: ./");
}
if ($_GET['lang']) {
	if ($_GET['lang']) {
		$_SESSION['lang'] = $_GET['lang'];
	} else {
		$_SESSION['lang'] = "default";
	}
	header("Location: ./");
}
if ($_SESSION['type']) {
	if (!in_array($_SESSION['type'], array(
		'image',
		'flash'
	))) {
		$_SESSION['type'] = "file";
	}
}
if ($_SESSION['lang']) {
	$_SESSION['lang'] = str_replace("../", "", $_SESSION['lang']);
	if (file_exists("langs/" . $_SESSION['lang'] . ".php")) {
		require("langs/" . $_SESSION['lang'] . ".php");
	} else {
		require("langs/default.php");
	}
} else {
	require("langs/default.php");
}
if ($_GET['show_flash']) {
	$data = getimagesize($_GET['show_flash']);
	if (!$data[0]) {
		$data[0] = "100%";
	}
	if (!$data[1]) {
		$data[1] = "100%";
	}
	$array = explode(".", $_GET['show_flash']);
	unset($array[count($array) - 1]);
	$_GET['show_flash_java'] = false;
	foreach ($array as $val) {
		if ($_GET['show_flash_java']) {
			$_GET['show_flash_java'] .= ".";
		}
		$_GET['show_flash_java'] .= $val;
	}
	$_GET['show_flash_java'] = trim($_GET['show_flash_java']);
	echo "<html>\n<head>\n<title>Flash</title>\n<style type=\"text/css\">\nbody,table,div,object,p{\n padding: 0px;\n margin: 0px;\n border: 0px;\n}\n</style><script language=\"javascript\" type=\"text/javascript\" src=\"scripts/AC_RunActiveContent.js\"></script>\n</head>\n<body>\n";
	echo "<div style=\"position: absolute; z-index: 10; margin: 10px 5px;\"><input type=\"button\" value=\" " . $getword['off_flash_movie'] . " \" style=\"background-image: url('images/location_bg.gif'); background-repeat: repeat-x; border: 1px solid white; cursor: pointer; font-size: 12px; font-family: arial;\" onclick=\"document.write('');\"></div>";
	echo "<script type=\"text/javascript\">\n   AC_FL_RunContent( 'codebase','http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0','width','" . $data[0] . "','height','" . $data[1] . "','align','middle','class','','id','','src','" . $_GET['show_flash_java'] . "','menu','false','quality','high','wmode','transparent','bgcolor','#ffffff','name','" . $_GET['show_flash_java'] . "','allowscriptaccess','sameDomain','pluginspage','http://www.macromedia.com/go/getflashplayer','movie','" . $_GET['show_flash_java'] . "' );\n</script>\n";
	echo '<noscript><object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" height="' . $data[1] . '" width="' . $data[0] . '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">' . "\n";
	echo '<param value="' . $_GET['show_flash'] . '" name="movie">' . "\n";
	echo '<param value="high" name="quality"></object></noscript>' . "\n";
	echo "</body>\n</html>";
	exit;
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"><html><head>
<Meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script language="JavaScript" type="text/javascript" src="lib/JsHttpRequest.js"></script>
<script type="text/javascript" src="../../../jquery/jquery-1.11.1.min.js"></script>
<script language="javascript" type="text/javascript" src="../../tiny_mce_popup.js"></script>
<script language="javascript" type="text/javascript" src="../../tiny_mce.js"></script>
<link rel="stylesheet" type="text/css" href="styles/style.css">
<script language="JavaScript" type="text/javascript" src="script.php?to=<?php echo htmlspecialchars($_GET['to']);?>&win_id=<?php echo htmlspecialchars($_GET['win_id']);?>&name_id=<?php echo htmlspecialchars($_GET['name_id']);?>"></script>
<script language="JavaScript" type="text/javascript" src="scripts/sorttable.js"></script>
<title>File Manager</title>
</head>
<body>
<table class="primary_table">
	<tr>
		<td><center>
				<table class="border_table">
					<tr height=5>
						<td class=l1></td>
						<td></td>
						<td class=l2></td>
					</tr>
					<tr>
						<td></td>
						<td class=l3><table class="general_part_table">
								<tr>
									<td width=50%><table border=0 class="file_manager_table">
											<tr height=20 id="action_menu">
												<td><table class="up_menu">
														<tr>
															<td><a href="" onClick="upload_form('show'); return false;"><img src="images/upload.gif" title="<?= $getword['upload_file'] ?>"></a></td>
															<td><a href="" onClick="create_new_dir(); return false;"><img src="images/add_dir.gif" title="<?= $getword['add_folder'] ?>"></a></td>
															<td><a href="" onClick="up_directory(); return false;"><img src="images/up_dir.gif" title="<?= $getword['primary_folder'] ?>"></a></td>
															<td width=1%><input type="text" id="now_location" disabled></td>
														</tr>
													</table></td>
											</tr>
											<tr id="action_upload_form" style="display: none">
												<td><form method="post" enctype="multipart/form-data" onSubmit="upload_file(document.getElementById('up_file'));return false">
														<table class="upload_file_table">
															<tr>
																<td><input type="file" name="file" id="up_file" onChange="upload_file(this); return false;"></td>
																<td width=20><a href="" onClick="upload_file(document.getElementById('up_file')); return false;"><img src="images/upload.gif"></a></td>
																<td width=20><a href="" onClick="upload_form('hide'); return false;"><img src="images/delete.gif"></a></td>
															</tr>
														</table>
													</form></td>
											</tr>
											<tr id="uploading_image" style="display: none">
												<td align="center"><img src="images/loading.gif"></td>
											</tr>
											<tr height=245>
												<td><div id="files_list_div"></div></td>
											</tr>
											<tr height=22>
												<td><table id="bottom_info_table">
														<tr>
															<td class="name"><?= $getword['type'] ?></td>
															<td width=1><img src="images/bottom_spacer.gif"></td>
															<td class="info"><?= $getword['info'] ?></td>
															<td width=1><img src="images/bottom_spacer.gif"></td>
															<td class="size"><?= $getword['size'] ?></td>
														</tr>
													</table></td>
											</tr>
										</table></td>
									<td width=50%><table class="preview_part_table">
											<tr height=135>
												<td class="preview"><div id="preview">
														<table class="no_preview">
															<tr>
																<td><?= $getword['no_preview'] ?>
																	aa</td>
															</tr>
														</table>
													</div></td>
											</tr>
											<tr height=1%>
												<td><table class="file_url">
														<tr>
															<td class="text"><?= $getword['file_url'] ?>
																: </td>
															<td><input type="text" class="tbox" id="selected_file_url"></td>
															<td width=20><a href="" onClick="download_file(); return false;"><img src="images/download.gif" title="<?= $getword['download'] ?>"></a></td>
														</tr>
													</table></td>
											</tr>
											<tr>
												<td valign="top"><div id="actions_list" style="display: none">
														<table class="actions_table">
															<tr>
																<td class="name"><?= $getword['actions'] ?>
																	: </td>
																<td class="action_buttons"></td>
															</tr>
														</table>
														<table width=100%>
															<tr>
																<td class="ok_buttons"><input type="button" value="<?= $getword['ok'] ?>" onClick="openFile();">
																	<input type="button" value="<?= $getword['cancel'] ?>" onClick="tinyMCEPopup.close();"></td>
															</tr>
														</table>
													</div>
													<div id="send_file" style="display: none">
														<table class="send_table">
															<tr>
																<td></td>
																<td class="title"><?= $getword['sending_file_to_email'] ?></td>
															</tr>
															<tr>
																<td class="name"><?= $getword['send_from'] ?>
																	: </td>
																<td class="input"><input type="text" name="from" class="tbox"></td>
															</tr>
															<tr>
																<td class="name"><?= $getword['send_to'] ?>
																	: </td>
																<td class="input"><input type="text" name="to" class="tbox"></td>
															</tr>
															<tr>
																<td></td>
																<td class="title"><input type="button" value="<?= $getword['send_btn'] ?>" onClick="send_file_to_email();" class="sbtn"></td>
															</tr>
														</table>
													</div>
													<div id="copy_to" style="display: none">
														<table class="send_table">
															<tr>
																<td></td>
																<td class="title"><?= $getword['copyng_file'] ?></td>
															</tr>
															<tr>
																<td class="name"><?= $getword['copy_to'] ?>
																	: </td>
																<td class="input" id="catalog_list_td"><select>
																	</select></td>
															</tr>
															<tr>
																<td></td>
																<td class="title"><input type="button" value="<?= $getword['copy_btn'] ?>" onClick="copy_file_to();" class="sbtn"></td>
															</tr>
														</table>
													</div>
													<div id="resizing_place" style="display: none">
														<table class="send_table">
															<tr>
																<td></td>
																<td class="title"><?= $getword['image_resizing'] ?></td>
															</tr>
															<tr>
																<td class="name"><?= $getword['new_width'] ?>
																	: </td>
																<td class="input"><input type="text" class="tbox" size=5 onKeyUp="resizing_place_func('set_height');"></td>
															</tr>
															<tr>
																<td class="name"><?= $getword['new_height'] ?>
																	: </td>
																<td class="input"><input type="text" class="tbox" size=5></td>
															</tr>
															<tr>
																<td></td>
																<td class="title"><input type="button" value="<?= $getword['resize_btn'] ?>" onClick="resizing_place_func('resize');" class="sbtn"></td>
															</tr>
														</table>
													</div></td>
											</tr>
										</table></td>
								</tr>
							</table></td>
						<td></td>
					</tr>
					<tr height=5>
						<td class=l1></td>
						<td></td>
						<td class=l2></td>
					</tr>
				</table>
			</center>
		</td>
	</tr>
</table>
<div id="debug"></div>
</body>
<script language="JavaScript" type="text/javascript">open_dir('');</script>
</html>