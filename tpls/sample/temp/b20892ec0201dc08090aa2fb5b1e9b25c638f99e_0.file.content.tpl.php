<?php /* Smarty version 3.1.27, created on 2015-12-23 16:04:02
         compiled from "D:\home\alx\www\tpls\sample\content.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:29192567ac5f298b319_47441675%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b20892ec0201dc08090aa2fb5b1e9b25c638f99e' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\content.tpl',
      1 => 1450884386,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '29192567ac5f298b319_47441675',
  'variables' => 
  array (
    'row' => 0,
    'file' => 0,
    'menu' => 0,
    'list' => 0,
    'arr' => 0,
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac5f2b02436_80109912',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac5f2b02436_80109912')) {
function content_567ac5f2b02436_80109912 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '29192567ac5f298b319_47441675';
if ($_smarty_tpl->tpl_vars['row']->value) {
if ($_smarty_tpl->tpl_vars['row']->value['entries'][0]['module'] == 'html') {
echo $_smarty_tpl->tpl_vars['row']->value['entries'][0]['body'];
} else { ?><div class="post open"><?php $_smarty_tpl->tpl_vars['file'] = new Smarty_Variable(concat(concat('content/content_',$_smarty_tpl->tpl_vars['row']->value['entries'][0]['module']),'.tpl'), null, 0);
echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['file']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('included'=>true,'list'=>false,'row'=>$_smarty_tpl->tpl_vars['row']->value['entries'][0]), 0);
?>
</div><?php }
} else { ?><h2><?php echo $_smarty_tpl->tpl_vars['menu']->value[0]['title'];
if ($_smarty_tpl->tpl_vars['menu']->value[1]['title']) {?>: <?php echo $_smarty_tpl->tpl_vars['menu']->value[1]['title'];
}?></h2><?php
$_from = $_smarty_tpl->tpl_vars['list']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['arr'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['arr']->_loop = false;
$_smarty_tpl->tpl_vars['arr']->total= $_smarty_tpl->_count($_from);
$_smarty_tpl->tpl_vars['arr']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['arr']->value) {
$_smarty_tpl->tpl_vars['arr']->_loop = true;
$_smarty_tpl->tpl_vars['arr']->iteration++;
$_smarty_tpl->tpl_vars['arr']->last = $_smarty_tpl->tpl_vars['arr']->iteration == $_smarty_tpl->tpl_vars['arr']->total;
$foreach_arr_Sav = $_smarty_tpl->tpl_vars['arr'];
?><div class="post"><?php
$_from = $_smarty_tpl->tpl_vars['arr']->value['entries'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['row'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['row']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->_loop = true;
$foreach_row_Sav = $_smarty_tpl->tpl_vars['row'];
$_smarty_tpl->tpl_vars['file'] = new Smarty_Variable("content/content_".((string)$_smarty_tpl->tpl_vars['row']->value['module']).".tpl", null, 0);
if ($_smarty_tpl->tpl_vars['row']->value['module'] == 'html') {
echo $_smarty_tpl->tpl_vars['row']->value['descr'];
} elseif ($_smarty_tpl->tpl_vars['row']->value['bodylist'] && !$_smarty_tpl->tpl_vars['row']->value['content']['another_menu'] && ($_smarty_tpl->tpl_vars['content']->value['id'] || $_smarty_tpl->tpl_vars['row']->value['total_entries'] == 1)) {
echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['file']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('included'=>true,'list'=>false,'row'=>$_smarty_tpl->tpl_vars['row']->value), 0);
} else {
echo $_smarty_tpl->getSubTemplate ($_smarty_tpl->tpl_vars['file']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('included'=>true,'list'=>true,'row'=>$_smarty_tpl->tpl_vars['row']->value), 0);
}
$_smarty_tpl->tpl_vars['row'] = $foreach_row_Sav;
}
?></div><?php if (!$_smarty_tpl->tpl_vars['arr']->last) {?><div class="hr"></div><?php }
$_smarty_tpl->tpl_vars['arr'] = $foreach_arr_Sav;
}
}
echo $_smarty_tpl->getSubTemplate ('content/pager.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('top'=>false), 0);
echo $_smarty_tpl->getSubTemplate ('content/bottom.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);

}
}
?>