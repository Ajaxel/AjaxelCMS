<?php /* Smarty version 3.1.27, created on 2015-12-23 18:10:18
         compiled from "D:\home\alx\www\tpls\sample\pages\scripts.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:2842567ae38a40aa54_93247519%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '549eb79f952c40679e5d13078af7de4c76b61f7c' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\scripts.tpl',
      1 => 1450893849,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2842567ae38a40aa54_93247519',
  'variables' => 
  array (
    'menu' => 0,
    'm' => 0,
    'url1' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ae38a4fb950_87235468',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ae38a4fb950_87235468')) {
function content_567ae38a4fb950_87235468 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2842567ae38a40aa54_93247519';
?>
<h2><?php echo $_smarty_tpl->tpl_vars['menu']->value[9]['title2'];?>
</h2><?php echo $_smarty_tpl->tpl_vars['menu']->value[9]['descr'];?>
<div class="hr"></div><div class="content"><?php
$_from = $_smarty_tpl->tpl_vars['menu']->value['sub'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['m'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['m']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['m']->value) {
$_smarty_tpl->tpl_vars['m']->_loop = true;
$foreach_m_Sav = $_smarty_tpl->tpl_vars['m'];
?><div><div class="pic"><a href="<?php echo $_smarty_tpl->tpl_vars['m']->value['url'];?>
" class="ajax<?php if ($_smarty_tpl->tpl_vars['m']->value['name'] == $_smarty_tpl->tpl_vars['url1']->value) {?> current<?php }?>"><img src="http://ajaxel.com/<?php echo @constant('HTTP_DIR_TPL');?>
images/scripts/<?php echo $_smarty_tpl->tpl_vars['m']->value['name'];?>
.png" width="200" alt="<?php echo $_smarty_tpl->tpl_vars['m']->value['title'];?>
" /></a></div><h4><a href="<?php echo $_smarty_tpl->tpl_vars['m']->value['url'];?>
" class="ajax<?php if ($_smarty_tpl->tpl_vars['m']->value['name'] == $_smarty_tpl->tpl_vars['url1']->value) {?> current<?php }?>"><?php echo $_smarty_tpl->tpl_vars['m']->value['title'];?>
</a></h4><?php echo $_smarty_tpl->tpl_vars['m']->value['descr'];?>
</div><div class="hr"></div><?php
$_smarty_tpl->tpl_vars['m'] = $foreach_m_Sav;
}
?></div><div class="btn_more"><a href="?order"><?php echo l('Order');?>
</a></div><?php }
}
?>