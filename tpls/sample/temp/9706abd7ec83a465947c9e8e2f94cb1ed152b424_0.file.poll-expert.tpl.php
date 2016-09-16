<?php /* Smarty version 3.1.27, created on 2015-12-23 18:00:46
         compiled from "D:\home\alx\www\tpls\sample\pages\scripts\poll-expert.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:11206567ae14e117683_01500250%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9706abd7ec83a465947c9e8e2f94cb1ed152b424' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\scripts\\poll-expert.tpl',
      1 => 1438721222,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '11206567ae14e117683_01500250',
  'variables' => 
  array (
    'j' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ae14e21e5d4_00069001',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ae14e21e5d4_00069001')) {
function content_567ae14e21e5d4_00069001 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '11206567ae14e117683_01500250';
?>
<h2><?php echo l('PollExpert - Professional survey web-based');?>
</h2><?php echo l('@[pollexpert-text]This is long time ago one good project, in year 2009th. Giving it for free, if someone needs it.');?>
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
images/slider/o/pollexpert<?php if ($_smarty_tpl->tpl_vars['i']->value > 1) {
echo $_smarty_tpl->tpl_vars['i']->value;
}?>.png);" title="<?php echo l('_PollExpert - Professional survey web-based');?>
}" rel="cms" href="/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/o/pollexpert<?php if ($_smarty_tpl->tpl_vars['i']->value > 1) {
echo $_smarty_tpl->tpl_vars['i']->value;
}?>.png" class="colorbox bg"></a></div><?php
$_smarty_tpl->tpl_vars['i'] = $foreach_i_Sav;
}
?><div class="hr"></div><div class="back" style="float:left;position:relative;top:10px"><a href="?scripts"><?php echo lang('Back to other scripts');?>
</a></div><div class="btn_more"><a href="javascript:;" onclick="S.G.download('pollexpert')"><?php echo l('Download');?>
</a><a href="http://poll.ajaxel.com" target="_blank"><?php echo l('Try now');?>
</a></div><?php }
}
?>