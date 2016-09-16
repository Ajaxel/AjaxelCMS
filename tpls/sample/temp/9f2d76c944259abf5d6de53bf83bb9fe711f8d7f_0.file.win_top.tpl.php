<?php /* Smarty version 3.1.27, created on 2015-12-24 13:01:21
         compiled from "D:\home\alx\www\tpls\sample\crm\inc\win_top.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:27802567beca10ca085_94693346%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9f2d76c944259abf5d6de53bf83bb9fe711f8d7f' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\crm\\inc\\win_top.tpl',
      1 => 1379211501,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '27802567beca10ca085_94693346',
  'variables' => 
  array (
    'win_id' => 0,
    'conf' => 0,
    'row' => 0,
    'diff_id' => 0,
    'diff_name' => 0,
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567beca115da78_76194956',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567beca115da78_76194956')) {
function content_567beca115da78_76194956 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '27802567beca10ca085_94693346';
?>
<form method="post" action="" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
_win" class="c-form" style="width:<?php echo $_smarty_tpl->tpl_vars['conf']->value['window_width'];?>
px"><input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
" /><input type="hidden" name="act" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
_win-act" value="" /><input type="hidden" name="table" value="<?php echo $_POST['table'];?>
" /><input type="hidden" name="diff_id" value="<?php echo $_smarty_tpl->tpl_vars['diff_id']->value;?>
" /><input type="hidden" name="diff_name" value="<?php echo $_smarty_tpl->tpl_vars['diff_name']->value;?>
" /><table class="c-form ui-widget ui-corner-all ui-widget ui-resizable"><thead><tr><td colspan="4" class="c-win-top ui-widget-header ui-corner-top"><div class="l"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</div><div class="r"><a href="javascript:;" onclick="CRM.close()" title="[alt+q]" class="c-win_close"></a></div></td></tr></thead><tbody class="ui-widget ui-widget-content">	<?php }
}
?>