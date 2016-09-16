<?php /* Smarty version 3.1.27, created on 2015-12-23 15:23:09
         compiled from "D:\home\alx\www\tpls\sample\content\title.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:2675567abc5da5bbc3_81853057%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '35651b7c543b925f47edb7b287cf4cfed3e9683c' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\content\\title.tpl',
      1 => 1444328763,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2675567abc5da5bbc3_81853057',
  'variables' => 
  array (
    'row' => 0,
    'menu' => 0,
    'Tpl' => 0,
    'author' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567abc5daec833_27953236',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567abc5daec833_27953236')) {
function content_567abc5daec833_27953236 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2675567abc5da5bbc3_81853057';
if ($_smarty_tpl->tpl_vars['row']->value['url_is'] && (!$_smarty_tpl->tpl_vars['row']->value['bodylist'] || $_smarty_tpl->tpl_vars['row']->value['content']['comment'] == 'Y')) {?><h3 style="margin-bottom:0px"><a href="<?php echo $_smarty_tpl->tpl_vars['row']->value['url_open'];?>
" class="ajax"><?php echo $_smarty_tpl->tpl_vars['row']->value['title'];?>
</a></h3><?php } else { ?><h2 style="margin-bottom:0px" class="no_e"><span><?php echo $_smarty_tpl->tpl_vars['menu']->value[9]['title'];?>
:</span> <?php echo $_smarty_tpl->tpl_vars['row']->value['title'];?>
</h2><?php }?><div class="info"><?php $_smarty_tpl->tpl_vars['author'] = new Smarty_Variable($_smarty_tpl->tpl_vars['Tpl']->value->user($_smarty_tpl->tpl_vars['row']->value['userid'],'firstname'), null, 0);?><span class="postinfo"><?php echo Call('Date','dayCountDown',$_smarty_tpl->tpl_vars['row']->value['added']);?>
 <b><?php echo $_smarty_tpl->tpl_vars['author']->value;?>
</b> <?php if ($_smarty_tpl->tpl_vars['row']->value['content']['comment'] == 'Y') {?> <?php echo lang('%1 {number=%1,Comment,Comments}',(intval($_smarty_tpl->tpl_vars['row']->value['content']['comments'])));
}
if (!$_smarty_tpl->tpl_vars['row']->value['bodylist'] || $_smarty_tpl->tpl_vars['row']->value['content']['comment'] == 'Y') {
}?></span></div><?php }
}
?>