<?php /* Smarty version 3.1.27, created on 2015-12-23 18:03:54
         compiled from "D:\home\alx\www\tpls\sample\pages\scripts\ajaxel-cms.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:6455567ae20ad61639_05919440%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '255b6245ac0c83df140d6b0344a6c97615c53f05' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\scripts\\ajaxel-cms.tpl',
      1 => 1450893751,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6455567ae20ad61639_05919440',
  'variables' => 
  array (
    'sp' => 0,
    'j' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ae20aea4715_67526573',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ae20aea4715_67526573')) {
function content_567ae20aea4715_67526573 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '6455567ae20ad61639_05919440';
?>
<h2><?php echo l('Ajaxel content management system %1 and framework',(Func('CMS::VERSION')));?>
</h2><?php echo l('@[ajaxel-cms-i]Very simple ajaxified CMS and framework for any project needs. Edit your website content from backend or front end. Try and see how good this stuff is!');?>
<br/><br/><?php if ($_smarty_tpl->tpl_vars['sp']->value) {?><h4><?php echo l('Price: free - 100 eur / license.');?>
</h4><?php }
echo l('@[ajaxel-offered]Free is for non-comercial businesses.');?>
<div class="hr"></div><?php echo l('@ajaxel_intro_text');?>
<div class="btn_more"><a href="http://ajaxel.com/order"><?php echo l('Order');?>
</a><a href="http://ajaxel.com"><?php echo l('Download');?>
</a><a href="http://ajaxel.com/readme"><?php echo l('Readme');?>
</a><a href="http://demo.ajaxel.com/do-login/login-demo/password-1234" target="_blank"><?php echo l('Try demo!');?>
</a><a href="?scripts&about"><?php echo l('About AJAX');?>
</a></div><div class="hr"></div><center><iframe width="100%" height="500" src="//www.youtube.com/embed/-5zPNx-Mh6k" frameborder="0" allowfullscreen></iframe></center><div class="hr"></div><?php
$_from = Call('Html','arrRange',1,20);
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['i']->_loop = false;
$_smarty_tpl->tpl_vars['j'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['j']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->_loop = true;
$foreach_i_Sav = $_smarty_tpl->tpl_vars['i'];
?><div class="image_wrapper image_p"<?php if ($_smarty_tpl->tpl_vars['j']->value%2 == 0) {?> style="margin-right:0"<?php }?>><a style="background-image:url(http://ajaxel.com/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/o/ajaxel-cms<?php if ($_smarty_tpl->tpl_vars['i']->value > 1) {
echo $_smarty_tpl->tpl_vars['i']->value;
}?>.png);" title="<?php echo l('_Ajaxel content management system %1 and framework',(Func('CMS::VERSION')));?>
" rel="cms" href="http://ajaxel.com/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/o/ajaxel-cms<?php if ($_smarty_tpl->tpl_vars['i']->value > 1) {
echo $_smarty_tpl->tpl_vars['i']->value;
}?>.png" class="colorbox bg"></a></div><?php
$_smarty_tpl->tpl_vars['i'] = $foreach_i_Sav;
}
?><div class="hr"></div><?php echo l('@ajaxel-info');?>
<div class="hr"></div><div class="back" style="float:left;position:relative;top:10px"><a href="?scripts"><?php echo lang('Back to other scripts');?>
</a></div><div class="btn_more"><a href="http://ajaxel.comorder"><?php echo l('Order');?>
</a><a href="http://ajaxel.com"><?php echo l('Download');?>
</a><a href="http://ajaxel.com/readme"><?php echo l('Readme');?>
</a><a href="http://demo.ajaxel.com/do-login/login-demo/password-1234" target="_blank"><?php echo l('Try demo!');?>
</a><a href="http://ajaxel.com/scripts&about"><?php echo l('About AJAX');?>
</a></div><?php }
}
?>