<?php /* Smarty version 3.1.27, created on 2015-12-23 16:16:07
         compiled from "D:\home\alx\www\tpls\sample\footer.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:19656567ac8c72de5d1_07425024%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '630f1d6977d603776c028074d8c12c098c60ab3c' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\footer.tpl',
      1 => 1450887365,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19656567ac8c72de5d1_07425024',
  'variables' => 
  array (
    'arrMenu' => 0,
    'm' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac8c7354db3_99462327',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac8c7354db3_99462327')) {
function content_567ac8c7354db3_99462327 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '19656567ac8c72de5d1_07425024';
?>
<div class="cleaner"></div></div><div id="content_wrapper_bottm"></div><div id="footer">Copyright © <?php echo date('Y');?>
 <?php echo @constant('DOMAIN');?>
 &ndash; <?php
$_from = $_smarty_tpl->tpl_vars['arrMenu']->value['bottom'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['m'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['m']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['m']->value) {
$_smarty_tpl->tpl_vars['m']->_loop = true;
$foreach_m_Sav = $_smarty_tpl->tpl_vars['m'];
?><a href="<?php echo $_smarty_tpl->tpl_vars['m']->value['url'];?>
" class="ajax"><?php echo $_smarty_tpl->tpl_vars['m']->value['title'];?>
</a> &ndash; <?php
$_smarty_tpl->tpl_vars['m'] = $foreach_m_Sav;
}
?> +372-56-769-366 &ndash; <a href="mailto:ajaxel.com@gmail.com">ajaxel.com@gmail.com</a></div></div></div></body><?php }
}
?>