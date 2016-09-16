<?php /* Smarty version 3.1.27, created on 2015-12-24 12:59:21
         compiled from "D:\home\alx\www\tpls\sample\pages\inc\comment_form.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:5706567bec291209a5_64541788%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9e9e55aa30e58a53f2737c9b781f5c1754ce0787' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\inc\\comment_form.tpl',
      1 => 1450961959,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5706567bec291209a5_64541788',
  'variables' => 
  array (
    'row' => 0,
    'Tpl' => 0,
    'User' => 0,
    'URL' => 0,
    'comments' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567bec29228057_89025201',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567bec29228057_89025201')) {
function content_567bec29228057_89025201 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '5706567bec291209a5_64541788';
$_smarty_tpl->tpl_vars['comments'] = new Smarty_Variable($_smarty_tpl->tpl_vars['Tpl']->value->Index->My->comments($_smarty_tpl->tpl_vars['row']->value['content']['rid'],$_smarty_tpl->tpl_vars['row']->value['content']['table']), null, 0);?><div class="hr"></div><div class="form"><h5><?php echo lang('Comment something:');?>
</h5><?php if ($_smarty_tpl->tpl_vars['User']->value['UserID']) {?><form method="post" id="comment_form" class="ajax_form no_scroll" action="?<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
"><table class="details"><?php echo $_smarty_tpl->getSubTemplate ('includes/form_errors.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('table'=>2,'form_errors'=>$_smarty_tpl->tpl_vars['comments']->value['form_errors']), 0);
?>
<tr><td colspan="2"><textarea name="comment[comment]" rows="5" cols="20" style="width:465px;height:77px" class="textbox"><?php echo strform($_POST['comment']['comment']);?>
</textarea></td></tr><tr><td class="no_e" style="line-height:32px"><?php echo lang('Did you like it?:');?>
<a href="javascript:;" class="v-up" id="v-up" onclick="$('#v-down').removeClass('active');$(this).addClass('active');$('#comment-rate').val(1)"></a><a href="javascript:;" class="v-down" id="v-down" onclick="$('#v-up').removeClass('active');$(this).addClass('active');$('#comment-rate').val(-1)"></a></td><td style="padding:5px;text-align:center"><button type="submit" class="btn"><?php echo lang('Comment!');?>
</button></td></tr></table><?php } else { ?><div class="please_login"><a href="?user&login" class="ajax"><?php echo lang('Please login to leave your comments');?>
</a></div><?php }
echo $_smarty_tpl->getSubTemplate ('pages/inc/comments.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('comments'=>$_smarty_tpl->tpl_vars['comments']->value), 0);
if ($_smarty_tpl->tpl_vars['User']->value['UserID']) {?><input type="hidden" name="comment[rate]" id="comment-rate" value="" /><input type="hidden" name="submitted" value="comment" /></form><?php }?></div><?php }
}
?>