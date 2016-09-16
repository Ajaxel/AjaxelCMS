<?php /* Smarty version 3.1.27, created on 2015-12-23 15:45:12
         compiled from "D:\home\alx\www\tpls\sample\includes\form_errors.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:22801567ac188290603_31259102%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f7b0f350e6671c7da4cc1e5d9fa3e5bbaba05f1' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\includes\\form_errors.tpl',
      1 => 1438356733,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22801567ac188290603_31259102',
  'variables' => 
  array (
    'table' => 0,
    'form_errors' => 0,
    'top_message' => 0,
    'msg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac188341597_22119680',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac188341597_22119680')) {
function content_567ac188341597_22119680 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '22801567ac188290603_31259102';
if ($_smarty_tpl->tpl_vars['table']->value && ($_smarty_tpl->tpl_vars['form_errors']->value || $_smarty_tpl->tpl_vars['top_message']->value)) {?><tr><td colspan="<?php if ($_smarty_tpl->tpl_vars['table']->value == 1) {?>2<?php } else {
echo $_smarty_tpl->tpl_vars['table']->value;
}?>" style="padding:0;padding-bottom:2px;"><?php }
if ($_smarty_tpl->tpl_vars['form_errors']->value['text']) {?><table class="report <?php if ($_smarty_tpl->tpl_vars['form_errors']->value['type'] == 'tick' || $_smarty_tpl->tpl_vars['form_errors']->value['type'] == 'success') {?>ok<?php } else { ?>bad<?php }?>"><tr><td><label><?php echo $_smarty_tpl->tpl_vars['form_errors']->value['text'];?>
</label></td></tr></table><?php } elseif ($_smarty_tpl->tpl_vars['form_errors']->value) {?><table class="report bad"><tr><td><?php
$_from = $_smarty_tpl->tpl_vars['form_errors']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['msg'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['msg']->_loop = false;
$_smarty_tpl->tpl_vars['name'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['msg']->value) {
$_smarty_tpl->tpl_vars['msg']->_loop = true;
$foreach_msg_Sav = $_smarty_tpl->tpl_vars['msg'];
?><div class="li"><?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
</label><?php
$_smarty_tpl->tpl_vars['msg'] = $foreach_msg_Sav;
}
?></td></tr></table><?php } elseif ($_smarty_tpl->tpl_vars['top_message']->value) {?><table class="report <?php if ($_smarty_tpl->tpl_vars['top_message']->value['type'] == 'success') {?>ok<?php } else { ?>bad<?php }?>"><tr><td><div class="li"><?php echo $_smarty_tpl->tpl_vars['top_message']->value['text'];?>
</div></td></tr></table><?php }
if ($_smarty_tpl->tpl_vars['table']->value && ($_smarty_tpl->tpl_vars['form_errors']->value || $_smarty_tpl->tpl_vars['top_message']->value)) {?></td></tr><?php }
}
}
?>