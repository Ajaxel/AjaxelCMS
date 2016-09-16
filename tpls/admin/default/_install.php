<!DOCTYPE html><html><head><title>Ajaxel CMS installation</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">var Conf={SITE_TYPE:'index', URL_EXT:'', HTTP_EXT:'<?php echo HTTP_EXT?>',FTP_EXT:'<?php echo FTP_EXT?>', VERSION:<?php echo Site::VERSION?>, URL_KEY_ADMIN:'<?php echo URL_KEY_ADMIN?>', temp:[]}</script>
<link type="text/css" rel="stylesheet" href="<?php echo FTP_EXT?>tpls/css/ui/selene/<?php echo JQUERY_CSS?>?v=<?php echo Site::VERSION?>" id="s-theme_admin" />
<link type="text/css" rel="stylesheet" href="<?php echo FTP_EXT?>tpls/css/admin_default.css?v=<?php echo Site::VERSION?>" />
<link type="text/css" rel="stylesheet" href="<?php echo FTP_EXT?>tpls/css/edit_default.css?v=<?php echo Site::VERSION?>" />
<link type="text/css" rel="stylesheet" href="<?php echo FTP_EXT?>tpls/css/global.css?v=<?php echo Site::VERSION?>" />
<link type="text/css" rel="stylesheet" href="<?php echo FTP_EXT?>tpls/css/colorbox/5/colorbox.min.css?v=<?php echo Site::VERSION?>" />
<script type="text/javascript" src="<?php echo FTP_EXT?>tpls/js/jquery/<?php echo JQUERY?>?v=<?php echo Site::VERSION?>"></script>
<?php if (defined('JQUERY_MIGRATE') && JQUERY_MIGRATE):?><script type="text/javascript" src="<?php echo FTP_EXT?>tpls/js/jquery/<?php echo JQUERY_MIGRATE?>?v=<?php echo Site::VERSION?>"></script><?php endif;?>
<script type="text/javascript" src="<?php echo FTP_EXT?>tpls/js/jquery/<?php echo JQUERY_UI?>?v=<?php echo Site::VERSION?>"></script>
<script type="text/javascript" src="<?php echo FTP_EXT?>tpls/js/global.js?v=<?php echo Site::VERSION?>"></script>
<script type="text/javascript" src="<?php echo FTP_EXT?>tpls/js/plugins/jquery.blockUI.min.js?v=<?php echo Site::VERSION?>"></script>
<script type="text/javascript" src="<?php echo FTP_EXT?>tpls/js/plugins/jquery.colorbox.min.js?v=<?php echo Site::VERSION?>"></script>
<script type="text/javascript">
/*<![CDATA[*/
S._ = {
	stop:false,tim:0
	,wait:function(opposite){
		if (opposite) {
			$('#a-wait').fadeIn(function(){
				S._.wait();
			});
		} else {
			$('#a-wait').fadeOut(function(){
				if (!S._.stop) {
					S._.wait(true);
				}
			});
		}
	}
	,install:function() {
		$('#a-footer_line').fadeOut();
		$('#a-install').fadeOut(function(){
			$('#a-installing').fadeIn(function(){
				S._.wait();
			});
		});
		return false;
	}
	,errors:function(data){
		var ul = $('#a-errors').show();
		$('#a-installing, #a-wait').hide();
		S._.stop=true;
		$('#a-install').fadeIn();
		$('#a-footer_line').fadeIn();
		ul.children().remove();
		for (i in data.errors) {
			$('#data-'+i+'').parent().prev().css({
				color:'red'
			})
			$('<li>').html(data.errors[i]+'.').appendTo(ul);	
		}	
	}
	,restore_start:false
	,restore:function(data){
		if (!this.restore_start) {
			var html = '<table class="a-list a-list-one ui-corner-all" cellspacing="0" style="width:500px;margin:auto">';
			html += '<tr><td><div id="a-process_perc" style="height:25px;margin:5px 0;width:1%;overflow:hidden;display:block" class="ui-state-active"><div style="padding-left:5px;font-weight:bold;padding-top:5px;">1%</div></div></td></tr>';
			html += '<tr><td><ul id="a-process_ul"></ul></td></tr>';
			html += '</table>';
			$('#a-process').html(html).show();
			this.restore_start=true;
		}
		$('#a-process_perc').stop().animate({width:data.percent+'%'}).children().html(data.percent+'%'+(data.percent>100?', sorry this is because of GZ archive':''));
		if (!data.done) {
			$.ajax({
				cache: false,
				dataType: 'json',
				url: '<?php echo HTTP_EXT?>?json&db_restore=true',
				type: 'POST',
				success: function(data) {
					if (data.error) {
						alert(data.error);
					} else {
						S._.restore(data);
					}
				}
			});	
		} else {
			$.ajax({
				cache: false,
				dataType: 'json',
				url: '<?php echo HTTP_EXT?>?json&install_end=true',
				type: 'POST',
				success: function(data) {
					if (data.error) {
						alert(data.error);
					} else {
						$('#a-process').hide();
						S._.finish(data);
					}
				}
			});	
		}
	}
	,finish:function(data){
		var ul = $('#a-errors').show();
		$('#a-installing, #a-wait').hide();
		ul.css({
			width:700,
			color: 'red',
			background: '#ffffff',
			'list-style':'none',
			'margin-left':'auto'
		});
		if (data.dump_errors) {
			$('<li>').html(data.dump_errors).appendTo(ul);
		}
		$('<li>').html(data.message).appendTo(ul);	
	}
}

$().ready(function() {
	$('.a-button').button();<?php
	$c = Cache::getSmall('db_restore');
	if ($c && is_array($c) && @$c['start']):?>
		$('#a-install').hide();
		var data=<?php echo json($c)?>;
		data.done=false;
		S._.restore(data);
	<?php
	endif;
	?>
	$('#data-template').change(function(){
		var v = this.value;
		$('#div-preview').html('');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: '<?php echo HTTP_EXT?>?json&templates=true',
			type: 'POST',
			data: 'template='+v,
			success: function(data) {
				if (data.ok && data.preview) {
					$('#div-preview').html('<a href="'+data.preview+'" class="colorbox"><img src="'+data.preview_th+'" /></a>'+(data.description?'<div style="font:12px \'Trebuchet MS\', Arial">'+data.description+'</div>':'')).css({textAlign:'center',padding:'5px'}).stop().show();
					S.I.init($('#div-preview'));
				} else {
					$('#div-preview').hide();
				}
				if (data.ok && data.template!='default' && data.template!='sample') {
					$('#table-login').hide('blind');
				} else {
					$('#table-login').show('blind');
				}
			}
		});	
	});
	$('#a-install').submit(function(){
		clearTimeout(S._.tim);
		S._.stop=false;
		S._.install();
		$('#a-errors').fadeOut(function(){
			$(this).children().remove();	
		});
		S._.tim=setTimeout(function() {
			$.ajax({
				cache: false,
				dataType: 'json',
				url: '<?php echo HTTP_EXT?>?json&install=true',
				type: 'POST',
				data: $('#a-install').serializeArray(),
				success: function(data) {
					if (data.error) {
						alert(data.error);
					}
					else if (data.area) {
						S._.restore(data);
					} else {
						if (data.errors) {
							S._.errors(data);
						}
						else if (data.success) {
							S._.finish(data);
						}
					}
				}
			});	
		},500);
		return false;
	});
	
});
/*]]>*/
</script>
<style type="text/css">
a:hover {text-decoration:none}
.a-h1 {font-size:15px}
.a-install_info {font:14px 'Trebuchet MS', Arial;padding:10px 20px;margin-top:50px;}
.a-install_next_button {text-align:center;margin:50px 0}
table.a-install {margin:0px auto;width:450px;background:#fff;color:#000}
table.a-install th {padding:8px 25px;width:40%;font:12px 'Trebuchet MS', Arial;}
table.a-install th.ui-dialog-titlebar {padding:5px 0 5px 14px;font:14px 'Trebuchet MS', Arial}
table.a-install td {padding:2px 4px;font-size:18px;border-left:1px solid #DBE6FD;border-bottom:1px solid #DBE6FD;border-left:1px solid #DBE6FD;background:#F1F5FE}
table.a-install td select, table.a-install td .a-input {width:150px;}
table.a-install td div.i {font:italic 11px Arial;padding:4px 2px 2px 4px;color:#777}
.a-button_wrapper {text-align:center;padding:10px;font:20px 'Trebuchet MS', Arial}
ul.a-errors {width:400px;margin:10px auto;color:red;display:none;padding:5px 10px;background:#F7DEDD;border:1px solid #dedede;font:14px 'Trebuchet MS', Tahoma;list-style:decimal;}
.a-sep {height:10px;}
ul.a-errors li {padding:4px 0;margin-left:20px;}
.ui-dialog-content {font-size:12px;font-weight:normal}
.ui-dialog-titlebar.ui-widget-header img {float:left;margin-right:8px}
</style>
</head>
<body style="background:url(<?php echo FTP_EXT?>tpls/img/admin_bg.jpg);background-attachment:fixed">
<div class="a-cmslogo" style="margin-top:10px;">
	<a href="http://ajaxel.com/" target="_blank"><img src="<?php echo FTP_EXT?>tpls/img/AJAXEL_LOGO.png" alt="Ajaxel CMS" title="Ajaxel CMS" /></a>
</div>
<table class="a-wrapper ui-widget-content" style="width:auto;margin-top:0" cellspacing="0" cellpadding="0"><tr valign="top"><td class="a-area">
	<div id="a-installing" class="a-install_info" style="display:none">
		<div style="font-size:16px" id="a-wait">Please wait, system is installing...</div>
	</div>
	<div id="a-process" style="display:none"></div>
	<ul id="a-errors" class="a-errors ui-corner-all"></ul>
	<form method="post" name="a-install" id="a-install">
		<table class="a-install ui-corner-all" cellspacing="0" cellpadding="0">
			<tr>
				<th colspan="2" class="ui-dialog-titlebar ui-widget-header ui-corner-top">
					<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/network.png" alt="" /> Site config &amp; Activation
				</th>
			</tr>
			<tr><th>Site URL:</th><td>
				<input type="text" class="a-input" id="data-http_base" name="data[http_base]" value="<?php echo strform(post('data','http_base'))?>" style="width:200px" />
			</td></tr>
			<tr><th>Language:</th><td>
				<select name="data[language]" class="a-select" id="data-language"><?php echo Html::buildOptions(post('data','language'),$this->preset['languages'])?></select>
			</td></tr>
			<tr><th>Template:</th><td class="ui-corner-right">
				<select name="data[template]" class="a-select" id="data-template">
					<option value=""></option>
					<?php echo Html::buildOptions(post('data','template'),array_label($this->preset['templates'],0))?>
				</select>
				<div id="div-preview"></div>
			</td></tr>
			</table>
			<div class="a-sep"></div>
			<table class="a-install ui-corner-all" id="table-login" cellspacing="0" cellpadding="0">
			<tr>
				<th colspan="2" class="ui-dialog-titlebar ui-widget-header ui-corner-top">
					<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/add-user.png" alt="" /> Login details
				</th>
			</tr>
			<tr><th>Username:</th><td>
				<input type="text" class="a-input" id="data-login" onfocus="this.select()" name="data[login]" value="<?php echo strform(post('data','login'))?>" />
			</td></tr>
			<tr><th>Password:</th><td>
				<input type="password" class="a-input" id="data-password" name="data[password]" value="<?php echo strform(post('data','password'))?>" style="width:120px" />
			</td></tr>
			<tr><th>Email:</th><td class="ui-corner-right">
				<input type="text" class="a-input" id="data-email" name="data[email]" onfocus="this.select()" value="<?php echo strform(post('data','email'))?>" />
			</td></tr>
			</table>
			<div class="a-sep"></div>
			<table class="a-install ui-corner-all" cellspacing="0" cellpadding="0">
			<tr>
				<th colspan="2" class="ui-dialog-titlebar ui-widget-header ui-corner-top">
					<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/devices/drive-harddisk.png" alt="" /> Database configuration
				</th>
			</tr>
			<tr><th>Database host:</th><td>
				<input type="text" class="a-input" id="data-db_host" name="data[db_host]" onfocus="this.select()" value="<?php echo strform(post('data','db_host'))?>" />
			</td></tr>
			<tr><th>Database prefix:</th><td>
				<input type="text" class="a-input" id="data-db_prefix" name="data[db_prefix]" value="<?php echo strform(post('data','db_prefix'))?>" style="width:100px" />
			</td></tr>
			<tr><th>Database username:</th><td>
				<input type="text" class="a-input" id="data-db_user" name="data[db_user]" onfocus="this.select()" value="<?php echo strform(post('data','db_user'))?>" />
			</td></tr>
			<tr><th>Database password:</th><td>
				<input type="text" class="a-input" id="data-db_pass" name="data[db_pass]" value="<?php echo strform(post('data','db_pass'))?>" style="width:120px" />
			</td></tr>
			<tr><th class="ui-corner-left">Database name:</th><td class="ui-corner-right">
				<input type="text" class="a-input" id="data-db_name" name="data[db_name]" onfocus="this.select()" value="<?php echo strform(post('data','db_name'))?>" />
			</td></tr>
		</table>
		<div class="a-button_wrapper">
			<button type="submit" class="a-button" style="font-weight:normal;width:100px"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/gear.png" alt="" /> Install</button>
		</div>
		
		<input type="hidden" name="install_submitted" value="1" />
	</form>
</td></tr></table>

<div style="height:15px"></div>
</body></html>