<?php /* Smarty version 3.1.27, created on 2015-12-24 13:05:56
         compiled from "D:\home\alx\www\tpls\sample\content\content_entries.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:32358567bedb47a1ac6_67793913%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eafe5182decc87300f4b0fd6bae616833e791fbb' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\content\\content_entries.tpl',
      1 => 1450962354,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32358567bedb47a1ac6_67793913',
  'variables' => 
  array (
    'row' => 0,
    'list' => 0,
    'files' => 0,
    'f' => 0,
    'i' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567bedb4999c71_10101022',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567bedb4999c71_10101022')) {
function content_567bedb4999c71_10101022 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '32358567bedb47a1ac6_67793913';
echo $_smarty_tpl->getSubTemplate ('content/title.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('row'=>$_smarty_tpl->tpl_vars['row']->value), 0);
if ($_smarty_tpl->tpl_vars['list']->value == true) {?><div class="content"><?php if ($_smarty_tpl->tpl_vars['row']->value['main_photo']) {?><div class="pic"><a href="<?php echo $_smarty_tpl->tpl_vars['row']->value['url_open'];?>
"><img src="<?php echo @constant('HTTP_DIR_FILES');
echo $_smarty_tpl->tpl_vars['row']->value['table'];?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['rid'];?>
/th3/<?php echo $_smarty_tpl->tpl_vars['row']->value['main_photo'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['row']->value['alt'];?>
" border="0" /></a></div><?php }
if ($_smarty_tpl->tpl_vars['row']->value['descr']) {
echo $_smarty_tpl->tpl_vars['row']->value['descr'];
} else {
echo $_smarty_tpl->tpl_vars['row']->value['body'];
}?></div><?php } else { ?><div class="content open"><?php if ($_smarty_tpl->tpl_vars['row']->value['main_photo'] && !$_smarty_tpl->tpl_vars['row']->value['bodylist']) {?><div class="pic"><a href="<?php echo @constant('HTTP_DIR_FILES');
echo $_smarty_tpl->tpl_vars['row']->value['table'];?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['rid'];?>
/th1/<?php echo $_smarty_tpl->tpl_vars['row']->value['main_photo'];?>
" class="colorbox" rel="gal"><img src="<?php echo @constant('HTTP_DIR_FILES');
echo $_smarty_tpl->tpl_vars['row']->value['table'];?>
/<?php echo $_smarty_tpl->tpl_vars['row']->value['rid'];?>
/th2/<?php echo $_smarty_tpl->tpl_vars['row']->value['main_photo'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['row']->value['alt'];?>
" border="0" /></a></div><?php }
if ($_smarty_tpl->tpl_vars['row']->value['body']) {
echo $_smarty_tpl->tpl_vars['row']->value['body'];
} else {
echo $_smarty_tpl->tpl_vars['row']->value['descr'];
}
if ($_smarty_tpl->tpl_vars['row']->value['url']) {?><div class="url"><h2><?php echo lang('Working website');?>
</h2><div class="content"> <a href="<?php echo $_smarty_tpl->tpl_vars['row']->value['url'];?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['row']->value['url'];?>
</a></div></div><?php }
$_smarty_tpl->tpl_vars['files'] = new Smarty_Variable(call('Data','getFiles',$_smarty_tpl->tpl_vars['row']->value['table'],$_smarty_tpl->tpl_vars['row']->value['rid'],true,false,$_smarty_tpl->tpl_vars['row']->value), null, 0);
if ($_smarty_tpl->tpl_vars['files']->value) {?><div style="clear:both"></div><div class="gallery" style="padding-top:15px"><?php
$_from = $_smarty_tpl->tpl_vars['files']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['f'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['f']->_loop = false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['f']->value) {
$_smarty_tpl->tpl_vars['f']->_loop = true;
$foreach_f_Sav = $_smarty_tpl->tpl_vars['f'];
if ($_smarty_tpl->tpl_vars['f']->value['media'] == 'image' && ($_smarty_tpl->tpl_vars['i']->value || $_smarty_tpl->tpl_vars['row']->value['bodylist'])) {?><div class="pic" style="margin-bottom:15px"><a href="<?php echo $_smarty_tpl->tpl_vars['f']->value['th1'];?>
" class="colorbox" rel="gal"><img src="<?php echo $_smarty_tpl->tpl_vars['f']->value['th2'];?>
" width="177" alt="<?php echo strform($_smarty_tpl->tpl_vars['row']->value['alt']);?>
" border="0" /></a><div class="c"></div><?php if (!$_smarty_tpl->tpl_vars['row']->value['bodylist'] && $_smarty_tpl->tpl_vars['f']->value['title']) {?><div class="title l"><?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
. <?php echo $_smarty_tpl->tpl_vars['f']->value['title'];?>
</div><?php }
echo $_smarty_tpl->tpl_vars['f']->value['admin'];?>
</div><?php }
$_smarty_tpl->tpl_vars['f'] = $foreach_f_Sav;
}
?></div><?php }?></div><?php }
}
}
?>