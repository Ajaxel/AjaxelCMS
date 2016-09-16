<?php /* Smarty version 3.1.27, created on 2015-12-23 15:54:21
         compiled from "D:\home\alx\www\tpls\sample\pages\portfolio.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:25100567ac3ad64b315_61105197%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '09b71df65e2632499974487e5f0faa960ce05c4f' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\portfolio.tpl',
      1 => 1438724878,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25100567ac3ad64b315_61105197',
  'variables' => 
  array (
    'portfolio' => 0,
    'i' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac3ad70b4c9_94782024',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac3ad70b4c9_94782024')) {
function content_567ac3ad70b4c9_94782024 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_replace')) require_once 'D:/home/alx/www/inc/lib/Smarty/plugins\\modifier.replace.php';

$_smarty_tpl->properties['nocache_hash'] = '25100567ac3ad64b315_61105197';
?>
<h2><?php echo lang('Portfolio, the working websites made with Ajaxel CMS');?>
</h2><div class="content"><?php echo l('@portfolio text');?>
</div><div class="hr"></div><?php $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable(1, null, 0);
$_from = $_smarty_tpl->tpl_vars['portfolio']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['row']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->_loop = true;
$foreach_row_Sav = $_smarty_tpl->tpl_vars['row'];
?><div class="image_wrapper image_p"<?php if ($_smarty_tpl->tpl_vars['i']->value%2 == 0) {?> style="margin-right:0"<?php }?>><div class="t"><?php echo $_smarty_tpl->tpl_vars['row']->value['url'];?>
</div><a style="background-image:url('<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['row']->value['pic'],'/th/','/mid/');?>
')" title="<?php echo $_smarty_tpl->tpl_vars['row']->value['url'];?>
" rel="portfolio" href="<?php echo $_smarty_tpl->tpl_vars['row']->value['url'];?>
" class="bg" target="_blank" rel="nofollow"></a></div><?php $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable($_smarty_tpl->tpl_vars['i']->value+1, null, 0);
$_smarty_tpl->tpl_vars['row'] = $foreach_row_Sav;
}
?><div class="hr"></div><div class="btn_more"><a href="?order"><?php echo l('Order');?>
</a></div><?php }
}
?>