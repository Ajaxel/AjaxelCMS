<?php /* Smarty version 3.1.27, created on 2015-12-23 18:00:53
         compiled from "D:\home\alx\www\tpls\sample\pages\scripts\time-monkey.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:5153567ae155891c10_69284246%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '39602f7f38d166d98619381ed0ecd73cd46aa783' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\scripts\\time-monkey.tpl',
      1 => 1438721227,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5153567ae155891c10_69284246',
  'variables' => 
  array (
    'j' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ae15596df34_84048168',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ae15596df34_84048168')) {
function content_567ae15596df34_84048168 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '5153567ae155891c10_69284246';
?>
<h2><?php echo l('TimeMonkey - Efficient time management web-based');?>
</h2><?php echo l('@[timemonkey-text]This is long time ago good project, year 2009th. Giving for free, if someone needs it.');?>
<div class="hr"></div><?php
$_from = Call('Html','arrRange',1,4);
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['i']->_loop = false;
$_smarty_tpl->tpl_vars['j'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['j']->value => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->_loop = true;
$foreach_i_Sav = $_smarty_tpl->tpl_vars['i'];
?><div class="image_wrapper image_p"<?php if ($_smarty_tpl->tpl_vars['j']->value%2 == 0) {?> style="margin-right:0"<?php }?>><a style="background-image:url(/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/o/timemonkey<?php if ($_smarty_tpl->tpl_vars['i']->value > 1) {
echo $_smarty_tpl->tpl_vars['i']->value;
}?>.png);" title="<?php echo l('_TimeMonkey - Efficient time management web-based');?>
}" rel="cms" href="/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/o/timemonkey<?php if ($_smarty_tpl->tpl_vars['i']->value > 1) {
echo $_smarty_tpl->tpl_vars['i']->value;
}?>.png" class="colorbox bg"></a></div><?php
$_smarty_tpl->tpl_vars['i'] = $foreach_i_Sav;
}
?><div class="hr"></div><div class="back" style="float:left;position:relative;top:10px"><a href="?scripts"><?php echo lang('Back to other scripts');?>
</a></div><div class="btn_more"><a href="javascript:;" onclick="S.G.download('timemonkey')"><?php echo l('Download');?>
</a><a href="http://time.ajaxel.com" target="_blank"><?php echo l('Try now');?>
</a></div><?php }
}
?>