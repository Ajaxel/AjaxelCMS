<?php /* Smarty version 3.1.27, created on 2015-12-23 15:45:11
         compiled from "D:\home\alx\www\tpls\sample\user\profile.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:25912567ac187ee50b6_12648759%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7d916c120c72258bdfb1c5406e2d64608106f5ca' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\user\\profile.tpl',
      1 => 1438549553,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25912567ac187ee50b6_12648759',
  'variables' => 
  array (
    'URL' => 0,
    'form_errors' => 0,
    'l' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac1882828a9_26986616',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac1882828a9_26986616')) {
function content_567ac1882828a9_26986616 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '25912567ac187ee50b6_12648759';
?>
<form class="form ajax_form" method="post" action="?<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
" id="register_form"><?php echo $_smarty_tpl->getSubTemplate ('includes/form_errors.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('table'=>false), 0);
?>
<div class="col_w420"><h2><?php echo lang('Your profile');?>
</h2><table cellspacing="0"><tbody><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['email']) {?> class="err"<?php }?>><div style="width:110px"><?php echo lang('E-mail');?>
<span class="ast">*</span>:</div></th><td><?php echo strform($_POST['email']);?>
</td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['state']) {?> class="err"<?php }?>><?php echo lang('Country');?>
<span class="ast">*</span>:</th><td><select class="selectbox" name="profile[country]" id="find_countries" onchange="S.G.populateGeo(this)"><option value="" style="color:#666"></option><?php echo Call('Data','getArray','countries',$_POST['profile']['country']);?>
</select></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['state']) {?> class="err"<?php }?>><?php echo lang('State');?>
<span class="ast">*</span>:</th><td><select class="selectbox" name="profile[state]" id="find_states" onchange="S.G.populateGeo(this)"><option value="" style="color:#666"></option><?php echo Call('Data','getArray','states',$_POST['profile']['state']);?>
</select></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['city']) {?> class="err"<?php }?>><?php echo lang('City');?>
<span class="ast">*</span>:</th><td><select class="selectbox" name="profile[city]" id="find_cities" onchange="S.G.populateGeo(this)"><option value="" style="color:#666"></option><?php echo Call('Data','getArray','cities',$_POST['profile']['city']);?>
</select></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['district']) {?> class="err"<?php }?>><?php echo lang('District');?>
:</th><td><select class="selectbox" name="profile[district]" id="find_districts"><option value="" style="color:#666"></option><?php echo Call('Data','getArray','districts',$_POST['profile']['district']);?>
</select></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['street']) {?> class="err"<?php }?>><?php echo lang('Street');?>
:</th><td><input type="text" name="profile[street]" class="textbox" value="<?php echo strform($_POST['profile']['street']);?>
" /></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['firstname']) {?> class="err"<?php }?>><?php echo lang('Firstname');?>
<span class="ast">*</span>:</th><td><input type="text" name="profile[firstname]" class="textbox" value="<?php echo strform($_POST['profile']['firstname']);?>
" /></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['lastname']) {?> class="err"<?php }?>><?php echo lang('Lastname');?>
<span class="ast">*</span>:</th><td><input type="text" name="profile[lastname]" class="textbox" value="<?php echo strform($_POST['profile']['lastname']);?>
" /></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['phone']) {?> class="err"<?php }?>><?php echo lang('Phone number');?>
:</th><td><input type="text" name="profile[phone]" class="textbox" value="<?php echo strform($_POST['profile']['phone']);?>
" /></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['phone']) {?> class="err"<?php }?>><?php echo lang('Skype');?>
:</th><td><input type="text" name="profile[skype]" class="textbox" value="<?php echo strform($_POST['profile']['skype']);?>
" /></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['descr']) {?> class="err"<?php }?>><?php echo lang('About you');?>
<span class="ast">*</span>:</th><td colspan="3"><table cellspacing="0" class="flags"><?php
$_from = Call('Site','getLanguages');
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['a'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['a']->_loop = false;
$_smarty_tpl->tpl_vars['l'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['l']->value => $_smarty_tpl->tpl_vars['a']->value) {
$_smarty_tpl->tpl_vars['a']->_loop = true;
$foreach_a_Sav = $_smarty_tpl->tpl_vars['a'];
?><tr><td><img src="/tpls/img/flags/24/<?php echo $_smarty_tpl->tpl_vars['l']->value;?>
.png" /></td><td><textarea class="textbox" name="profile[about][<?php echo $_smarty_tpl->tpl_vars['l']->value;?>
]" style="width:300px;height:70px;"><?php echo strform($_POST['profile']['about'][$_smarty_tpl->tpl_vars['l']->value]);?>
</textarea></td></tr><?php
$_smarty_tpl->tpl_vars['a'] = $foreach_a_Sav;
}
?></table></td></tr><tr><td>&nbsp;</td><td colspan="3" class="b"><button type="submit" class="btn float_l"><?php echo lang('Update');?>
</button></td></tr></tbody></table></div><div class="col_w420"><h2><?php echo lang('Change your password');?>
</h2><table cellspacing="0"><tbody><tr><th><?php echo l('New password:');?>
</th><td><input type="password" class="textbox" name="password" value="<?php echo strform($_POST['password']);?>
" /></td></tr><tr><th><?php echo l('Repeat password:');?>
</th><td><input type="password" class="textbox" name="re_password" value="" onfocus="$('#cur_password').show();" onblur="$('#cur_password').find('input').select()" /></td></tr><tr id="cur_password" style="display:none"><th class="text-right col-sm-4"><?php echo l('Old password:');?>
</th><td><input type="password" class="textbox" name="cur_password" value="<?php echo strform($_POST['cur_password']);?>
" /></td></tr><tr><td>&nbsp;</td><td colspan="3" class="b"><button type="submit" class="btn float_l"><?php echo lang('Change');?>
</button></td></tr></tbody></table></div><input type="hidden" name="<?php echo @constant('URL_KEY_REGISTER');?>
" /></form><div class="hr"></div><div class="back" style="float:left;position:relative;top:10px"><a href="?scripts"><?php echo lang('Back to scripts');?>
</a></div><?php }
}
?>