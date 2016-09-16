<?php /* Smarty version 3.1.27, created on 2015-12-24 13:01:21
         compiled from "D:\home\alx\www\tpls\sample\crm\inc\win_bot.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:17832567beca13beae3_35055654%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2cf4620f9b370cf28251926520a447b855aa88f' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\crm\\inc\\win_bot.tpl',
      1 => 1343909108,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17832567beca13beae3_35055654',
  'variables' => 
  array (
    'bottom' => 0,
    'row' => 0,
    'save' => 0,
    'conf' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567beca141bad9_04648151',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567beca141bad9_04648151')) {
function content_567beca141bad9_04648151 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '17832567beca13beae3_35055654';
?>
</tbody><tfoot><tr class="c-buttons"><td colspan="4" class="ui-corner-bottom ui-widget-header ui-corner-bottom"><div class="l"><?php echo $_smarty_tpl->tpl_vars['bottom']->value;?>
</div><?php if ($_smarty_tpl->tpl_vars['row']->value['edited']) {?><div class="l" style="line-height:25px">Last time edited: <?php echo date('H:i d.m.Y',$_smarty_tpl->tpl_vars['row']->value['edited']);?>
</div><?php }?><div class="r"><?php if ($_smarty_tpl->tpl_vars['save']->value) {?><button type="submit" class="c-button" tabindex=100><?php if ($_smarty_tpl->tpl_vars['row']->value['id']) {
echo lang($_smarty_tpl->tpl_vars['conf']->value['buttons'][1]);
} else {
echo lang($_smarty_tpl->tpl_vars['conf']->value['buttons'][0]);
}?></button><?php } else { ?><button type="button" class="c-button" onclick="CRM.close()" tabindex=100><?php echo lang('Close');?>
</button><?php }?></div></td></tr></tfoot></table></form><?php }
}
?>