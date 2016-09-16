<?php /* Smarty version 3.1.27, created on 2015-12-23 16:07:16
         compiled from "D:\home\alx\www\tpls\sample\content\bottom.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:14546567ac6b4b5e681_38540688%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '498359b36823141a7a27cd67054b104d272ea9a1' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\content\\bottom.tpl',
      1 => 1450886835,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14546567ac6b4b5e681_38540688',
  'variables' => 
  array (
    'content' => 0,
    'menu' => 0,
    'page' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac6b4c544a0_68253535',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac6b4c544a0_68253535')) {
function content_567ac6b4c544a0_68253535 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '14546567ac6b4b5e681_38540688';
if ($_smarty_tpl->tpl_vars['content']->value['is_open'] && $_smarty_tpl->tpl_vars['content']->value['comment'] == 'Y') {
echo $_smarty_tpl->getSubTemplate ('pages/inc/comment_form.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0);
}
if ($_smarty_tpl->tpl_vars['menu']->value[0]['name'] == 'home' && $_smarty_tpl->tpl_vars['content']->value['id']) {?><div class="hr"></div><div class="bottom"><div class="back"><a href="?home" onclick="S.G.get('?home');return false;"><?php echo lang('Back to Home');?>
</a></div></div><?php } elseif ($_smarty_tpl->tpl_vars['page']->value['name_back']) {?><div class="hr"></div><div class="bottom"><div class="back"><a href="<?php echo $_smarty_tpl->tpl_vars['page']->value['url_back'];?>
" class="ajax_link"><?php if ($_smarty_tpl->tpl_vars['page']->value['type'] == 'sitemap') {
echo lang('Back to sitemap');
} elseif ($_smarty_tpl->tpl_vars['page']->value['type'] == 'tag') {
echo strform(lang('Back to tag: %1',$_smarty_tpl->tpl_vars['page']->value['name_back']));
} elseif ($_smarty_tpl->tpl_vars['page']->value['type'] == 'search') {
echo lang('Back to %1',$_smarty_tpl->tpl_vars['page']->value['name_back']);
} else {
echo lang('Back to %1',$_smarty_tpl->tpl_vars['page']->value['name_back']);
}?></a></div></div><?php }
}
}
?>