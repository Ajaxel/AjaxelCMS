<?php /* Smarty version 3.1.27, created on 2015-12-24 13:01:00
         compiled from "D:\home\alx\www\tpls\sample\crm\index.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:21146567bec8c16a955_70610780%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e93749a299c013adcc5be97c9cbb648e44322158' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\crm\\index.tpl',
      1 => 1448639528,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21146567bec8c16a955_70610780',
  'variables' => 
  array (
    'config' => 0,
    'name' => 0,
    'conf' => 0,
    'a' => 0,
    'User' => 0,
    'tpl_file' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567bec8c3ed737_85492538',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567bec8c3ed737_85492538')) {
function content_567bec8c3ed737_85492538 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '21146567bec8c16a955_70610780';
?>
<?php echo '<script'; ?>
 type="text/javascript">$(function(){CRM.config(<?php echo json($_smarty_tpl->tpl_vars['config']->value);?>
,'<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
');<?php if ($_smarty_tpl->tpl_vars['conf']->value['user']) {?>CRM.data.users=<?php echo json(Call('Data','getArray','my:crm_users'));?>
;CRM.data.activities=<?php echo json(Call('Data','getArray','my:activities'));?>
;CRM.data.interests=<?php echo json(Call('Data','getArray','my:interests'));?>
;CRM.data.interests_cnt=<?php echo json(Call('Data','getArray','my:interests_cnt'));?>
;CRM.data.status_clients=<?php echo json(Call('Data','getArray','my:status_clients'));?>
;CRM.data.status_tasks=<?php echo json(Call('Data','getArray','my:status_tasks'));?>
;CRM.data.projects=<?php echo json_encode(Call('Data','getArray','my:projects'));?>
;CRM.data.access=<?php echo json(Call('Data','getArray','my:access'));?>
;CRM.data.countries=<?php echo json(Call('Data','getArray','my:countries'));?>
;CRM.data.languages=<?php echo json(Call('Data','getArray','my:languages'));?>
;<?php if ($_smarty_tpl->tpl_vars['name']->value != 'undefined') {?>CRM.conf.statuses=<?php echo json($_smarty_tpl->tpl_vars['conf']->value['statuses']);?>
;CRM.conf.status_classes=<?php echo json($_smarty_tpl->tpl_vars['conf']->value['status_classes']);?>
;CRM.conf.accesses=<?php echo json($_smarty_tpl->tpl_vars['conf']->value['accesses']);?>
;<?php }
}?>});<?php echo '</script'; ?>
><body class="<?php echo @constant('SITE_TYPE');?>
"><div id="loading">Loading...</div><div id="wrapper" class="ui-widget-content" style="display:none;"><div id="c-tabs" class="ui-widget-content"><ul id="c-tabs-ul"><li class="c-logo" onClick="window.location.href='<?php echo @constant('HTTP_EXT');?>
?crm'+(CRM.name?'&'+CRM.name:'')"><?php echo lang('#site_title_short');?>
 CRM</li><?php
$_from = $_smarty_tpl->tpl_vars['config']->value['tabs'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['a'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['a']->_loop = false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['a']->value) {
$_smarty_tpl->tpl_vars['a']->_loop = true;
$foreach_a_Sav = $_smarty_tpl->tpl_vars['a'];
?><li><a href="<?php if ($_smarty_tpl->tpl_vars['name']->value == $_smarty_tpl->tpl_vars['a']->value[0]) {?>#c-<?php echo $_smarty_tpl->tpl_vars['a']->value[0];?>
_tab<?php } else { ?>?crm&<?php echo $_smarty_tpl->tpl_vars['a']->value[0];
}?>" class="no_ajax"><?php echo $_smarty_tpl->tpl_vars['a']->value[2];?>
</a></li><?php
$_smarty_tpl->tpl_vars['a'] = $foreach_a_Sav;
}
if ($_smarty_tpl->tpl_vars['User']->value['UserID']) {?><li><button class="c-button" onClick="location.href='?crm&logout'" style="font-size:10px!important;margin-top:4px!important">LOGOUT</button></li><?php }?><li class="c-add"><a href="#c-<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" id="c-add"><img src="/tpls/img/oxygen/16x16/actions/list-add.png" /> ADD</a></li><li class="c-search" id="c-search"><form method="post"><table><tr><td><input type="text" name="f[search]" id="c-global-search" class="c-input big" value="<?php echo strform($_GET['search']);?>
" /></td><td><button type="submit" class="c-button">FIND</button></td></tr></table></form></li></ul><div id="c-<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_tab"><?php echo $_smarty_tpl->getSubTemplate ('crm/inc/tab_top.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
if ($_smarty_tpl->tpl_vars['conf']->value['user']) {
echo $_smarty_tpl->getSubTemplate ("crm/".((string)$_smarty_tpl->tpl_vars['tpl_file']->value).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
} else {
echo $_smarty_tpl->getSubTemplate ('crm/login.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
}
echo $_smarty_tpl->getSubTemplate ('crm/inc/tab_bot.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
?>
</div></div><div id="c-footer" class="ui-state-default c-unsel"><table><td class="c-1"><?php if (!$_smarty_tpl->tpl_vars['User']->value['UserID']) {?>Welcome guest<?php } else { ?>Logged as <?php echo $_smarty_tpl->tpl_vars['User']->value['Login'];
}?></td><td class="c-2">&nbsp;</td><td class="c-3">Copyright <a href="http://ajaxel.com/" target="_blank">Ajaxel CRM</a> &copy; 2011</td></tr></table></div><?php }
}
?>