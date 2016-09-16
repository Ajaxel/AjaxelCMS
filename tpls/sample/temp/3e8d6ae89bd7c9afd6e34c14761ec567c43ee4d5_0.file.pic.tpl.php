<?php /* Smarty version 3.1.27, created on 2015-12-23 21:26:47
         compiled from "D:\home\alx\www\tpls\sample\includes\pic.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:3864567b1197c24172_58385102%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e8d6ae89bd7c9afd6e34c14761ec567c43ee4d5' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\includes\\pic.tpl',
      1 => 1450893693,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3864567b1197c24172_58385102',
  'variables' => 
  array (
    'f' => 0,
    'time' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567b1197d55ad4_31302315',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567b1197d55ad4_31302315')) {
function content_567b1197d55ad4_31302315 ($_smarty_tpl) {
if (!is_callable('smarty_modifier_replace')) require_once 'D:/home/alx/www/inc/lib/Smarty/plugins\\modifier.replace.php';

$_smarty_tpl->properties['nocache_hash'] = '3864567b1197c24172_58385102';
?>
<div class="p<?php if ($_smarty_tpl->tpl_vars['f']->value['main']) {?> main<?php }?>"><?php if (Call('File','isPicture',$_smarty_tpl->tpl_vars['f']->value['file'])) {?><div class="pic"><a href="<?php echo $_smarty_tpl->tpl_vars['f']->value['dir'];
echo $_smarty_tpl->tpl_vars['f']->value['file'];?>
?t=<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
" class="colorbox" rel="gal" style="background-image:url('<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['f']->value['dir'],'/th1/',$_smarty_tpl->tpl_vars['f']->value['th']);
echo $_smarty_tpl->tpl_vars['f']->value['file'];?>
?t=<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
')" /></a></div><?php } else { ?><div class="pic"><a href="<?php echo $_smarty_tpl->tpl_vars['f']->value['dir'];
echo $_smarty_tpl->tpl_vars['f']->value['file'];?>
?t=<?php echo $_smarty_tpl->tpl_vars['time']->value;?>
" target="_blank" rel="gal" title="<?php echo $_smarty_tpl->tpl_vars['f']->value['file'];?>
" style="background-image:url('/<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['f']->value['ext_file'],'/16/','/130/');?>
')" /></a></div><?php }?><div class="c"><div class="name"><?php echo $_smarty_tpl->tpl_vars['f']->value['file'];?>
</div><div class="edit"><a href="javascript:;" onclick="S.G.delFile('<?php echo $_smarty_tpl->tpl_vars['f']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['f']->value['file'];?>
','<?php echo $_smarty_tpl->tpl_vars['f']->value['name'];?>
',this.parentNode.parentNode.parentNode)"><?php echo lang('Delete');?>
</a></div></div></div><?php }
}
?>