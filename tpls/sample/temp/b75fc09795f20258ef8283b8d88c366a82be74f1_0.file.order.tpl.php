<?php /* Smarty version 3.1.27, created on 2015-12-23 21:45:26
         compiled from "D:\home\alx\www\tpls\sample\pages\order.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:27368567b15f6589718_42012439%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b75fc09795f20258ef8283b8d88c366a82be74f1' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\order.tpl',
      1 => 1450907123,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27368567b15f6589718_42012439',
  'variables' => 
  array (
    'upload' => 0,
    'URL' => 0,
    'form_errors' => 0,
    'f' => 0,
    'arr' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567b15f685e0e7_42231646',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567b15f685e0e7_42231646')) {
function content_567b15f685e0e7_42231646 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '27368567b15f6589718_42012439';
?>
<h2><?php echo lang('Order our services');?>
</h2><?php echo l('[order-service]Greetings, I hope you have enjoyed with our works and willing to get something for your own purposes also. Just let me know if so, send detailed information about what you ant and wait for my reply.');?>
<div class="hr"></div><?php echo '<script'; ?>
 type="text/javascript">$(function(){S.G.upload({name	: '<?php echo $_smarty_tpl->tpl_vars['upload']->value['name'];?>
',id		: '<?php echo $_smarty_tpl->tpl_vars['upload']->value['id'];?>
',hash	: '<?php echo $_smarty_tpl->tpl_vars['upload']->value['hash'];?>
',file	: 'main_photo',queueID	: 'uploadQueue',multi	: true,auto	: true,buttonImg: '/<?php echo @constant('HTTP_DIR_TPL');?>
images/select-a-file2.png',width	: 133,height	: 46,fileExt	: '<?php echo $_smarty_tpl->tpl_vars['upload']->value['ext'];?>
',fileDesc: '<?php echo $_smarty_tpl->tpl_vars['upload']->value['desc'];?>
',start	: function(){$('#uploadQueue').fadeIn();},func	: function(response){if (response.substring(0,2)!='1/') {alert(response);} else {var to=$('#photos').find('li:last');if (to.length) {$('<li>').html(response.substring(2)).hide().insertBefore($('#photos').find('li:last')).fadeIn();} else {$('<li>').html(response.substring(2)).hide().appendTo($('#photos')).fadeIn();}S.I.init();}}});$('#order-form input.datepicker').datepicker({dateFormat: 'dd.mm.yy',defaultDate: "+1w",numberOfMonths: 2,minDate: 0});$('#order-form textarea').autosize();});<?php echo '</script'; ?>
><table style="width:100%;" cellspacing="0"><tr valign="top"><td style="border-right:1px solid #1E1C20;padding-right:20px"><div class="content" style="padding-top:0;width:590px"><form class="form ajax" method="post" action="?<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
" id="order-form" style="margin-left:0;padding-left:0"><?php if ($_POST['data']['id']) {?><h4><?php echo lang('_Edit your request');?>
<p style="float:right;color:#fff;font-weight:bold;font-size:17px;margin-top:1px;margin-bottom:-15px;">ID: <?php echo $_POST['data']['id'];?>
</p></h4><div class="content"><?php echo lang('[request_is_sent]Your request has been sent successfuly, but you may update details or <a href="?buy&reset">click here</a> to send new request');?>
</div><?php } else { ?><h4><?php echo lang('Request for a project form');?>
</h4><?php }?><table cellspacing="0"><tbody><?php echo $_smarty_tpl->getSubTemplate ('includes/form_errors.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('table'=>2), 0);
?>
<tr<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['type']) {?> class="err"<?php }?>><th><div style="width:190px"><?php echo lang('Project type');?>
<span class="ast">*</span>:</div></th><td><select class="selectbox" id="fld_type" name="data[type]" style="width:160px"><option value="" style="color:#666"></option><?php echo Data::getArray('my:type',$_POST['data']['type']);?>
</select></td></tr><tr<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['descr']) {?> class="err"<?php }?>><th><?php echo lang('About project');?>
<span class="ast">*</span>:</th><td><textarea class="textbox" name="data[descr]" style="width:348px;height:200px;margin-bottom:4px;max-height:350px"><?php echo strform($_POST['data']['descr']);?>
</textarea></td></tr><tr><th><?php echo lang('Design files, documents, technical tasks');?>
:</th><td><div id="uploadQueue" style="width:375px;clear:both;margin-left:4px"></div><div style="padding-left:3px;padding-top:4px;"><input type="file" name="data[file]" id="main_photo" /></div><ul class="photos" id="photos" style="padding-top:4px;"><?php
$_from = $_smarty_tpl->tpl_vars['upload']->value['files'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['f'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['f']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['f']->value) {
$_smarty_tpl->tpl_vars['f']->_loop = true;
$foreach_f_Sav = $_smarty_tpl->tpl_vars['f'];
?><li><?php echo $_smarty_tpl->getSubTemplate ('includes/pic.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('f'=>$_smarty_tpl->tpl_vars['f']->value), 0);
?>
</li><?php
$_smarty_tpl->tpl_vars['f'] = $foreach_f_Sav;
}
?></ul></td></tr><tr><th><?php echo lang('Characteristic of typical clients, visitors of web-site');?>
:</th><td><textarea class="textbox" name="data[descr2]" style="width:348px;height:60px;margin-bottom:4px;max-height:250px"><?php echo strform($_POST['data']['descr2']);?>
</textarea></td></tr><tr><th><?php echo lang('Similar websites to your project, URL-links');?>
:</th><td><textarea class="textbox" name="data[descr3]" style="width:348px;height:60px;margin-bottom:4px;max-height:250px"><?php echo strform($_POST['data']['descr3']);?>
</textarea></td></tr><tr><th><?php echo lang('Extra works');?>
:</th><td><table cellspacing="0" class="checkboxes"><?php $_smarty_tpl->tpl_vars['arr'] = new Smarty_Variable(Call('Data','getArray','my:programming'), null, 0);
echo Call('Html','buildRadios','checkbox','data[programming][]',$_POST['data']['programming'],$_smarty_tpl->tpl_vars['arr']->value,2);?>
</table></td></tr><tr<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['url']) {?> class="err"<?php }?>><th><?php echo lang('Your website domain');?>
:</th><td><input type="text" name="data[url]" style="width:200px" id="fld_url" class="textbox" value="<?php if ($_POST['data']['url']) {
echo strform($_POST['data']['url']);
} else { ?>http://<?php }?>" /></td></tr><tr><th><?php echo lang('FTP details, MySQL or cPanel username with password');?>
:</th><td><textarea class="textbox" name="data[descr4]" style="width:348px;height:60px;margin-bottom:1px;max-height:250px"><?php echo strform($_POST['data']['descr4']);?>
</textarea><br><small><?php echo lang('We use it to upload results directly to hosting.');?>
</small></td></tr><tr<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['budget']) {?> class="err"<?php }?>><th><?php echo lang('Budget');?>
:</th><td><input type="text" name="data[budget]" id="fld_budget" class="textbox" style="width:80px" value="<?php echo strform($_POST['data']['budget']);?>
" /> eur</td></tr><tr><th><?php echo lang('Deadline');?>
:</th><td><input type="text" name="data[dated]" id="fld_dated" readonly class="textbox datepicker" value="<?php if ($_POST['data']['dated']) {
echo strform($_POST['data']['dated']);
}?>" /></td></tr><tr><th><?php echo lang('Company');?>
:</th><td><input type="text" name="data[company]" style="width:200px" id="fld_cpmpany" class="textbox" value="<?php echo strform($_POST['data']['company']);?>
" /></td></tr><tr<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['email']) {?> class="err"<?php }?>><th><?php echo lang('Your E-mail');?>
<span class="ast">*</span>:</th><td><input type="text" name="data[email]" style="width:200px" id="fld_email" class="textbox" value="<?php echo strform($_POST['data']['email']);?>
" /></td></tr><tr><th><?php echo lang('Phone or Skype');?>
:</th><td><input type="text" name="data[phone]" style="width:200px" id="fld_phone" class="textbox" value="<?php echo strform($_POST['data']['phone']);?>
" /></td></tr><tr><th><?php echo lang('How have you found us?');?>
:</th><td><select class="selectbox" id="fld_type" name="data[found]" style="width:160px"><option value="" style="color:#666"></option><?php echo Data::getArray('my:found',$_POST['data']['found']);?>
</select></td></tr><tr><td>&nbsp;</td><td class="b"><?php if ($_POST['data']['id']) {?><button type="submit" class="btn float_l" style="margin-left:3px"><?php echo lang('Update');?>
</button><?php } else { ?><button type="submit" id="order-btn" class="btn float_l" style="margin-left:3px"><?php echo lang('Send');?>
</button><?php }?></td></tr></tbody></table><input type="hidden" name="submitted" value="buy" /></form></div></td><td style="padding-left:20px;border-left:1px solid #606060;font-size:14px"><h4><?php echo lang('Information');?>
</h4><?php echo lang('@how_to_buy_text');?>
</td></tr></table><?php }
}
?>