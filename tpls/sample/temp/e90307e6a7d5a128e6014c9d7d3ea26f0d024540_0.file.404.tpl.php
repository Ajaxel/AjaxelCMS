<?php /* Smarty version 3.1.27, created on 2015-12-23 15:26:19
         compiled from "D:\home\alx\www\tpls\sample\404.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:13062567abd1ba667a1_66416950%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e90307e6a7d5a128e6014c9d7d3ea26f0d024540' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\404.tpl',
      1 => 1438356733,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13062567abd1ba667a1_66416950',
  'variables' => 
  array (
    'content' => 0,
    'tree' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567abd1bb0b3b6_73533853',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567abd1bb0b3b6_73533853')) {
function content_567abd1bb0b3b6_73533853 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '13062567abd1ba667a1_66416950';
if ($_smarty_tpl->tpl_vars['content']->value) {?><div class="post"><h2><?php echo lang('Nothing found');?>
</h2><div class="content"><a href="<?php echo $_smarty_tpl->tpl_vars['content']->value['url'];?>
"><?php echo lang('Go back');?>
</a></div></div><?php } elseif ($_smarty_tpl->tpl_vars['tree']->value) {?><div class="post"><h2><?php echo lang('This page has no content yet');?>
</h2><div class="content"><?php echo lang('Please stay patient, this page will be filled soon');?>
...</div></div><?php } else { ?><div class="post"><h2><?php echo lang('404 - page not found');?>
</h2><div class="content">The Web server is pleased to announce you that this place is not exactly what you asked for. But we don't want this to be a problem to you, so sit down, relax, have a cup of coffee and wait.<br />There's someone now who's taking care of your problems... <br /><br />Do not contact the server's administrator if this problem persists, you won't need it.</div></div><?php }
}
}
?>