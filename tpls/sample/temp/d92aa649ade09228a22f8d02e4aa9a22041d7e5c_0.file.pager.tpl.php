<?php /* Smarty version 3.1.27, created on 2015-12-23 15:23:35
         compiled from "D:\home\alx\www\tpls\sample\content\pager.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:7606567abc77102563_01123638%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd92aa649ade09228a22f8d02e4aa9a22041d7e5c' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\content\\pager.tpl',
      1 => 1357386786,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7606567abc77102563_01123638',
  'variables' => 
  array (
    'pager' => 0,
    'top' => 0,
    'json' => 0,
    's' => 0,
    'p' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567abc77235738_78546077',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567abc77235738_78546077')) {
function content_567abc77235738_78546077 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '7606567abc77102563_01123638';
if ($_smarty_tpl->tpl_vars['pager']->value['total_pages'] > 1) {?><div class="pager"><ul class="pagenavi<?php if ($_smarty_tpl->tpl_vars['top']->value) {?> top<?php }?>"><li class="newer"><?php if ($_smarty_tpl->tpl_vars['pager']->value['page']) {
if ($_smarty_tpl->tpl_vars['json']->value) {?><a href="javascript:;" onclick="S.G.json('<?php echo $_smarty_tpl->tpl_vars['json']->value;
if ($_smarty_tpl->tpl_vars['pager']->value['page'] > 1) {?>&<?php echo $_smarty_tpl->tpl_vars['pager']->value['page_key'];?>
=<?php echo $_smarty_tpl->tpl_vars['pager']->value['page']-1;
}?>')"></a><?php } else { ?><a href="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];
if ($_smarty_tpl->tpl_vars['pager']->value['page'] > 1) {?>&<?php echo $_smarty_tpl->tpl_vars['pager']->value['page_key'];?>
=<?php echo $_smarty_tpl->tpl_vars['pager']->value['page']-1;
}?>" class="ajax_link"><?php echo lang('prev');?>
</a><?php }
}?></li><?php
$_from = $_smarty_tpl->tpl_vars['pager']->value['pages'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['s'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['s']->_loop = false;
$_smarty_tpl->tpl_vars['p'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['p']->value => $_smarty_tpl->tpl_vars['s']->value) {
$_smarty_tpl->tpl_vars['s']->_loop = true;
$foreach_s_Sav = $_smarty_tpl->tpl_vars['s'];
?><li class="page<?php if ($_smarty_tpl->tpl_vars['s']->value) {?> selected<?php }?>"><?php if ($_smarty_tpl->tpl_vars['json']->value) {?><a href="javascript:;" onclick="S.G.json('<?php echo $_smarty_tpl->tpl_vars['json']->value;
if ($_smarty_tpl->tpl_vars['p']->value-1 > 0) {?>&<?php echo $_smarty_tpl->tpl_vars['pager']->value['page_key'];?>
=<?php echo $_smarty_tpl->tpl_vars['p']->value-1;
}?>');"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a><?php } else { ?><a href="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];
if ($_smarty_tpl->tpl_vars['p']->value-1 > 0) {?>&<?php echo $_smarty_tpl->tpl_vars['pager']->value['page_key'];?>
=<?php echo $_smarty_tpl->tpl_vars['p']->value-1;
}?>" class="ajax_link"><?php echo $_smarty_tpl->tpl_vars['p']->value;?>
</a><?php }?></li><?php
$_smarty_tpl->tpl_vars['s'] = $foreach_s_Sav;
}
if ($_smarty_tpl->tpl_vars['pager']->value['next_page']) {?><li class="older"><?php if ($_smarty_tpl->tpl_vars['json']->value) {?><a href="javascript:;" onclick="S.G.json('<?php echo $_smarty_tpl->tpl_vars['json']->value;?>
&<?php echo $_smarty_tpl->tpl_vars['pager']->value['page_key'];?>
=<?php echo $_smarty_tpl->tpl_vars['pager']->value['next_page'];?>
')"></a><?php } else { ?><a href="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
&<?php echo $_smarty_tpl->tpl_vars['pager']->value['page_key'];?>
=<?php echo $_smarty_tpl->tpl_vars['pager']->value['next_page'];?>
" class="ajax_link"><?php echo lang('next');?>
</a><?php }?></li><?php }?></ul></div><?php }
}
}
?>