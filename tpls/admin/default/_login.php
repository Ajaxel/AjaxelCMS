<?php $this->tpl('_html')?>

<script type="text/javascript">
<?php echo Index::CDA?>
function AdminLogin_resize() {
	if (!$('#a-menus').length) {
		var top = $(window).height()/2-200;
		if (top<0) top = 0;
		$('#a-wrapper').css({
			marginTop: top-112
		});
	} else {
		
	}
}
$().ready(function() {
	$('.a-button').button();
	AdminLogin_resize();
	$(window).resize(AdminLogin_resize);
	var login=$('#login');
	var password=$('#password');
	<?php 
		if (Session()->login_try) {
			$msg = Session()->getMsg();
			if ($msg['type']!='success') {
				$error = $msg;
			} else {
				$error = $allow;
			}
			echo 'S.G.message([\''.strjs(lang('$Cannot authorize you')).'\',\''.strjs($error['text']).'\'],\''.$error['type'].'\',false,\''.$error['focus'].'\',0,'.$error['delay'].');';
		}
	?>
	if (!login.val()) {
		login.focus();
	}
	else {
		password.focus();
	}
	$('#a-login').submit(function(e){
		AdminLoginSubmit();
		e.preventDefault();
		return false;
	}).find('.a-fl').disableSelection();
});
var AdminLoginSubmitted = false;
function AdminLoginSubmit() {
	if (AdminLoginSubmitted) return false;
	AdminLoginSubmitted = true;
	$.unblockUI();
	var login=$('#login');
	var password=$('#password');
	var template=$('#template');
	var ui=$('#ui');
	var lang=$('#lang');
	var remember=$('#remember').is(':checked') ? 1 : 0;
	$('#a-login_button').button('disable');
	S.G.json('?<?php echo URL_KEY_ADMIN?>'+(template.val()?'&template='+template.val():'')+(ui.val()?'&ui='+ui.val():''), {login:login.val(),password:password.val(),lang:lang.val(),remember:remember,jump: '?<?php echo strJS(URL::get())?>'}, function(data){
		if (data.ok) {
			data.js2=function(){
				$(document.body).fadeOut('fast');
			}
		} else {
			$('#a-login_button').button('enable');
			data.text=['<?php echo strjs(lang('$Cannot authorize you'))?>',data.text];
		}
		if (data.focus=='password') password.focus();
		else if (data.focus=='login') login.focus();
		S.G.msg(data);
		
		AdminLoginSubmitted = false;
	});	
}
function getOpts(db){
	S.G.json('?&db='+$('#db').val()+'&<?php echo URL_KEY_ADMIN?>&template='+$('#template').val()+'&<?php echo URL_KEY_ADMIN?>&get=action&a=getTemplates',{},function(data){
		if (db) {
			var o=$('#template'), i=0;
			o.find('option').remove().end();
			for (name in data.templates) i++;
			if (i>1) {
				for (name in data.templates) o.append($('<option value="'+name+'">'+data.templates[name][0]+'</option>'));
				o.parent().parent().show();
			} else {
				o.parent().parent().hide();
			}
		}
		var o=$('#lang'), i=0;
		o.find('option').remove().end();
		for (name in data.langs) i++;
		if (i>1) {
			for (name in data.langs) o.append($('<option value="'+name+'">'+data.langs[name][0]+'</option>'));
			o.parent().parent().show();
		} else {
			o.parent().parent().hide();
		}
	})
}
<?php echo Index::CDZ?>
</script>
<?php

$templates = Site::getTemplates(true);
$langs = Site::getLanguages();
if ($db = Conf()->g('DB')) {
	ksort($db);
}
$ui = array_label(self::getUIsAdmin(),'[[:KEY:]]');
?>
<table style="width:100%;" id="a-wrapper"><tr valign="top">
<td>
	<div class="a-cmslogo" style="margin-top:10px;">
		<a href="http://ajaxel.com/" target="_blank"><img src="<?php echo FTP_EXT?>tpls/img/AJAXEL_LOGO.png" alt="Ajaxel CMS" title="Ajaxel CMS" /></a>
	</div>
	<div class="a-login_wrapper" id="a-login_form">
		<div class="a-login_form a-window-wrapper ui-widget ui-widget-content ui-corner-all">
			<div class="a-h1 ui-dialog-titlebar ui-widget-header ui-corner-top ui-helper-clearfix" style="padding:2px 0 0 10px;height:auto;" id="a-login_title">
				<div class="a-window-title" style="font-size:14px!important;line-height:20px"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/kgpg-identity-kgpg.png" style="float:left;margin-right:6px;margin-top:1px" /><?php echo lang('$Administration panel')?></div>
			</div>
			<div class="ui-dialog-content ui-widget-content">
				<form method="post" action="?<?php echo URL_KEY_ADMIN?>" id="a-login">
				<table class="a-login_form">
					<tr><td class="a-fl a-first"><?php echo lang('$Username:')?></td><td class="a-fr a-first"><input type="text" id="login" name="<?php echo URL_KEY_LOGIN?>" class="a-input" tabindex="1" value="<?php echo post(URL_KEY_LOGIN)?>" /></td></tr>
					<tr><td class="a-fl"><?php echo lang('$Password:')?></td><td class="a-fr"><input type="password" id="password" name="<?php echo URL_KEY_PASSWORD?>" class="a-input" tabindex="2" value="" /></td></tr>
					<tr>
						<td class="a-fl"><a href="?user&lostpass"><?php echo lang('$Lost password?')?></a></td>
						<td class="a-fr"><label for="remember"><input type="checkbox" id="remember" tabindex="3" name="<?php echo URL_KEY_REMEMBER?>" /> <?php echo lang('$Remember?')?></label><!--<td><label for="notmypc"><input type="checkbox" id="notmypc" name="<?php echo URL_KEY_NOTMYPC?>" /> <?php echo lang('$Not my computer?')?></label></td>--></td>
					</tr>
					<?php if (count($db)>1):
					//ksort($db);
					?>
					<tr><td class="a-fl"><?php echo lang('$Database:')?></td><td class="a-fr"><select name="db" tabindex="5" id="db" class="a-select" onchange="getOpts(true)"><?php echo Html::buildOptions($_SESSION['DB'],array_label($db,'[[:KEY:]]'))?></select></td></tr>
					<?php endif?>
					<tr<?php if (count($templates)<2):?> style="display:none"<?php endif;?>><td class="a-fl"><?php echo lang('$Template:')?></td><td class="a-fr"><select name="template" id="template" tabindex="6" onchange="getOpts();" class="a-select"><?php echo Html::buildOptions(TEMPLATE,array_label($templates,0))?></select></td></tr>
					<tr<?php if (count($langs)<2):?> style="display:none"<?php endif;?>><td class="a-fl"><?php echo lang('$Language:')?></td><td class="a-fr"><select name="lang" id="lang" tabindex="7" class="a-select"><?php echo Html::buildOptions(LANG,array_label($langs,0))?></select></td></tr>
					<tr><td class="a-fl"><?php echo lang('$UI:')?></td><td class="a-fr"><select id="ui" onchange="$('#s-theme_admin').attr('href','<?php echo FTP_EXT?>tpls/css/ui/'+this.value+'/<?php echo JQUERY_CSS?>');" class="a-select"><?php echo Html::buildOptions($_SESSION['UI_ADMIN'] ? $_SESSION['UI_ADMIN'] : UI_ADMIN,$ui)?></select></td></tr>
				</table>
				<div class="a-login_button">
					<div class="a-l a-author">
						<a href="http://ajaxel.com" target="_blank">Ajaxel CMS</a> v<?php echo Site::VERSION?>
					</div>
					<div class="a-r">
						<button type="submit" id="a-login_button" tabindex="4" class="a-button" onclick="$('#a-login').submit();"><table><tr><td><?php echo lang('$Enter to system')?></td><td><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/go-next.png" style="margin-left:5px;margin-right:0" /></td></tr></table></button>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</td></tr></table>
</body>
<?php echo $this->Index->getVar('conf').$this->Index->getVar('js')?>
</html>