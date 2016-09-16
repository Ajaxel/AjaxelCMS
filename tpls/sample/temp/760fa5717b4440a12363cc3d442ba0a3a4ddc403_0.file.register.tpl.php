<?php /* Smarty version 3.1.27, created on 2015-12-23 21:30:00
         compiled from "D:\home\alx\www\tpls\sample\user\register.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:15030567b1258a97537_95921221%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '760fa5717b4440a12363cc3d442ba0a3a4ddc403' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\user\\register.tpl',
      1 => 1438550330,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15030567b1258a97537_95921221',
  'variables' => 
  array (
    'URL' => 0,
    'form_errors' => 0,
    'time' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567b1258c85983_47941900',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567b1258c85983_47941900')) {
function content_567b1258c85983_47941900 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '15030567b1258a97537_95921221';
?>
<h2><?php echo lang('Join us');?>
</h2><form class="form ajax_form" method="post" action="?<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
" id="register_form"><table cellspacing="0"><tbody><?php echo $_smarty_tpl->getSubTemplate ('includes/form_errors.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('table'=>4), 0);
?>
<tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['login']) {?> class="err"<?php }?>><div style="width:170px"><?php echo lang('Username');?>
<span class="ast">*</span>:</div></th><td><input type="text" name="login" class="textbox" value="<?php echo strform($_POST['login']);?>
" /></td></tr><tr><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['password']) {?> class="err"<?php }?>><?php echo lang('Password');?>
<span class="ast">*</span>:</th><td><input type="password" name="password" class="textbox"  value="<?php echo strform($_POST['password']);?>
" /></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['re_password']) {?> class="err"<?php }?>><?php echo lang('Reenter password');?>
<span class="ast">*</span>:</th><td><input type="password" name="re_password" class="textbox" value="<?php echo strform($_POST['re_password']);?>
" /></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['re_password']) {?> class="err"<?php }?>><?php echo lang('Location');?>
<span class="ast">*</span>:</th><td><select class="selectbox" name="profile[country]" id="find_countries" onchange="S.G.populateGeo(this)"><option value="" style="color:#666"><?php echo lang('_Country');?>
</option><?php echo Call('Data','getArray','countries',$_POST['profile']['country']);?>
</select><select class="selectbox" name="profile[state]" id="find_states" onchange="S.G.populateGeo(this)"><option value="" style="color:#666"><?php echo lang('_State');?>
</option><?php echo Call('Data','getArray','states',$_POST['profile']['state']);?>
</select><select class="selectbox" name="profile[city]" id="find_cities"><option value="" style="color:#666"><?php echo lang('_City');?>
</option><?php echo Call('Data','getArray','cities',$_POST['profile']['city']);?>
</select></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['email']) {?> class="err"<?php }?>><?php echo lang('E-mail');?>
<span class="ast">*</span>:</th><td><input type="text" name="email" class="textbox" value="<?php echo strform($_POST['email']);?>
" /></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['firstname']) {?> class="err"<?php }?>><?php echo lang('Firstname');?>
<span class="ast">*</span>:</th><td><input type="text" name="profile[firstname]" class="textbox" value="<?php echo strform($_POST['profile']['firstname']);?>
" /></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['lastname']) {?> class="err"<?php }?>><?php echo lang('Lastname');?>
<span class="ast">*</span>:</th><td><input type="text" name="profile[lastname]" class="textbox" value="<?php echo strform($_POST['profile']['lastname']);?>
" /></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['phone']) {?> class="err"<?php }?>><?php echo lang('Phone number');?>
:</th><td><input type="text" name="profile[phone]" class="textbox" value="<?php echo strform($_POST['profile']['phone']);?>
" /></td></tr><tr class="bb"><th<?php if ($_smarty_tpl->tpl_vars['form_errors']->value['captcha']) {?> class="err"<?php }?>><a href="javascript:;" onclick="$(this).children().attr('src','/captcha.php?name=comment&n='+Math.random())"><img src="/captcha.php?name=comment&n=<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
" alt="Captcha" /></a><span class="ast">*</span>:</th><td><input type="text" name="captcha" style="width:80px" class="textbox" /></td></tr><tr><td>&nbsp;</td><td class="b"><button type="submit" class="btn float_l"><?php echo lang('Register');?>
</button></td></tr></tbody></table><input type="hidden" name="<?php echo @constant('URL_KEY_REGISTER');?>
" /></form><?php }
}
?>