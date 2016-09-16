<?php /* Smarty version 3.1.27, created on 2015-12-23 16:07:40
         compiled from "D:\home\alx\www\tpls\sample\pages\inc\comments.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:5686567ac6cc5d2197_05347455%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1a44ee5f7dd6b1bc2fcc08cdc35438dfa6eecff' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\inc\\comments.tpl',
      1 => 1438639033,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5686567ac6cc5d2197_05347455',
  'variables' => 
  array (
    'comments' => 0,
    'row' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac6cc651367_39817590',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac6cc651367_39817590')) {
function content_567ac6cc651367_39817590 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '5686567ac6cc5d2197_05347455';
?>
<ul class="comments"><?php
$_from = $_smarty_tpl->tpl_vars['comments']->value['list'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['row']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->_loop = true;
$foreach_row_Sav = $_smarty_tpl->tpl_vars['row'];
?><li><div class="pic"><?php if ($_smarty_tpl->tpl_vars['row']->value['user']['photo']) {?><a href="?user=<?php echo $_smarty_tpl->tpl_vars['row']->value['user']['login'];?>
" class="ajax_link" style="background-image:url(<?php echo $_smarty_tpl->tpl_vars['row']->value['user']['photo'];?>
)" /></a><?php }?></div><div class="text"><div class="l"><span class="date"><?php echo Call('Date','dayCountDown',$_smarty_tpl->tpl_vars['row']->value['added']);?>
</span><span class="name linked"><?php echo $_smarty_tpl->tpl_vars['row']->value['user']['name'];?>
</span><?php if ($_smarty_tpl->tpl_vars['row']->value['user']['online']) {?><span class="online"></span><?php } else { ?><span class="offline"></span><?php }?></div><div class="clean"><?php echo $_smarty_tpl->tpl_vars['row']->value['body'];?>
</div></div></li><?php
$_smarty_tpl->tpl_vars['row'] = $foreach_row_Sav;
}
?></ul><?php echo $_smarty_tpl->getSubTemplate ('content/pager.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('pager'=>$_smarty_tpl->tpl_vars['comments']->value['pager']), 0);

}
}
?>