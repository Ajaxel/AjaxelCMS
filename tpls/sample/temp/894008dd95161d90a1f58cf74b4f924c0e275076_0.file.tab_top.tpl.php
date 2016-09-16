<?php /* Smarty version 3.1.27, created on 2015-12-24 13:01:00
         compiled from "D:\home\alx\www\tpls\sample\crm\inc\tab_top.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:18379567bec8c466363_04387222%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '894008dd95161d90a1f58cf74b4f924c0e275076' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\crm\\inc\\tab_top.tpl',
      1 => 1323935498,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18379567bec8c466363_04387222',
  'variables' => 
  array (
    'name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567bec8c484e61_52651522',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567bec8c484e61_52651522')) {
function content_567bec8c484e61_52651522 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '18379567bec8c466363_04387222';
?>
<div class="c-area"><form method="post" id="c-form_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" autocomplete="off"><div class="c-content" id="c-content_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
"><input type="hidden" name="<?php echo @constant('URL_KEY_PAGE');?>
" id="c-page_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" value="0" /><input type="hidden" name="order" id="c-order_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" value="id" /><input type="hidden" name="by" id="c-by_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" value="DESC" /><?php }
}
?>