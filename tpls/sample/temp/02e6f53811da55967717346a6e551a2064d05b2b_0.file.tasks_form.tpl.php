<?php /* Smarty version 3.1.27, created on 2015-12-24 13:01:21
         compiled from "D:\home\alx\www\tpls\sample\crm\tasks_form.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:22987567beca116fb96_47004868%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '02e6f53811da55967717346a6e551a2064d05b2b' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\crm\\tasks_form.tpl',
      1 => 1344607472,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22987567beca116fb96_47004868',
  'variables' => 
  array (
    'name' => 0,
    'win_id' => 0,
    'row' => 0,
    'upload' => 0,
    'User' => 0,
    'post' => 0,
    'file' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567beca1381899_85999648',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567beca1381899_85999648')) {
function content_567beca1381899_85999648 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '22987567beca116fb96_47004868';
?>
<?php echo '<script'; ?>
 type="text/javascript">CRM.callback=function(){CRM.win_name='<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
';$('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
_win .c-button').button();$('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
_win').submit(function(){return CRM.save(this,'<?php echo $_smarty_tpl->tpl_vars['row']->value['id'];?>
','<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
');});CRM.fill($('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_project'),$.extend(CRM.data.projects, { '#new':'-- New project --' }),'<?php echo strJS($_smarty_tpl->tpl_vars['row']->value['project']);?>
','&nbsp;');CRM.fill($('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_access'),CRM.data.access,<?php echo intval($_smarty_tpl->tpl_vars['row']->value['access']);?>
,'Access');CRM.fill($('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_status'),CRM.data.status_tasks,<?php echo intval($_smarty_tpl->tpl_vars['row']->value['status']);?>
,'Status');CRM.fill($('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_assign'),CRM.data.users,<?php echo intval($_smarty_tpl->tpl_vars['row']->value['assign']);?>
,'&nbsp;');if ($('#c-title_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
').val()) {CRM.focus('c-descr_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
');} else {CRM.focus('c-title_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
');}var project = CRM.temp.project;if (!project) project=$('#c-filter_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
_project').val();if (project) {$('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_i_project').val(project);$('#c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_project').val(project);}CRM.upload('file','<?php echo $_smarty_tpl->tpl_vars['upload']->value['hash'];?>
', function(r){var a=r.split('/');$('<li>').addClass('pad').html('<input type="hidden" name="data[attachments][]" value="'+r+'" /><a href="javascript:;" onclick="CRM.file(\''+r+'\',this);">'+a[a.length-1]+'</a>').appendTo($('#c-files_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
'));});};<?php echo '</script'; ?>
><tr><th><?php echo lang('Project');?>
:</th><td><input type="text" name="data[project]" tabindex=1 onblur="if(!this.value) $(this).hide().next().show().val(''); else CRM.temp.project=this.value" value="<?php echo strform($_smarty_tpl->tpl_vars['row']->value['project']);?>
" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_i_project" class="c-input" style="display:none" /><select onchange="if(this.value=='#new'){ $(this).hide().prev().show().focus() }else{ $(this).prev().val(this.value);CRM.temp.project=this.value }" tabindex=1 class="c-select" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_project"></select></td><th><?php echo lang('Sum');?>
:</th><td><input type="text" name="data[price]" style="width:40%" onclick="if(this.value=='0.00') this.select();" class="c-input" tabindex=4 value="<?php echo strform($_smarty_tpl->tpl_vars['row']->value['price']);?>
" />&nbsp;</td></tr><tr><th><?php echo lang('Subject');?>
:</th><td colspan="3"><input type="text" name="data[title]" class="c-input" id="c-title_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
" tabindex=2 value="<?php echo strform($_smarty_tpl->tpl_vars['row']->value['title']);?>
" /></td></tr><tr><th><?php echo lang('Status / Access');?>
:</th><td><select name="data[status]" style="width:auto" tabindex=5 class="c-select" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_status"></select>&nbsp;<?php if (!$_smarty_tpl->tpl_vars['row']->value['id'] || $_smarty_tpl->tpl_vars['User']->value['UserID'] == $_smarty_tpl->tpl_vars['row']->value['userid']) {?><select name="data[access]" style="width:auto" tabindex=6 class="c-select" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_access"></select><?php } else {
echo Call('Data','getVal','my:access',$_smarty_tpl->tpl_vars['post']->value['access']);
}?></td><th><?php echo lang('Startdate');?>
:</th><td><input type="text" name="data[dated]" style="width:40%" class="c-input c-date" tabindex=8 value="<?php echo strform($_smarty_tpl->tpl_vars['row']->value['dated']);?>
" />&nbsp;<select name="data[dated_Hour]" class="c-select" style="width:50px" tabindex=9><?php echo Call('Data','getArray','my:hours',$_smarty_tpl->tpl_vars['row']->value['dated_Hour']);?>
</select>:<select name="data[dated_Minute]" class="c-select" style="width:50px" tabindex=10><?php echo Call('Data','getArray','my:minutes',$_smarty_tpl->tpl_vars['row']->value['dated_Minute']);?>
</select></td></tr><tr><th><?php echo lang('Assign for');?>
:</th><td><select tabindex=7 class="c-select" name="data[assign]" id="c-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-win_assign"></select></td><th><?php echo lang('Deadline');?>
:</th><td><input type="text" name="data[deadline]" style="width:40%" class="c-input c-date" tabindex=11 value="<?php echo strform($_smarty_tpl->tpl_vars['row']->value['deadline']);?>
" />&nbsp;<select name="data[deadline_Hour]" class="c-select" style="width:50px" tabindex=12><?php echo Call('Data','getArray','my:hours',$_smarty_tpl->tpl_vars['row']->value['deadline_Hour']);?>
</select>:<select name="data[deadline_Minutes]" class="c-select" style="width:50px" tabindex=13><?php echo Call('Data','getArray','my:minutes',$_smarty_tpl->tpl_vars['row']->value['deadline_Minute']);?>
</select></td></tr><tr><th><?php echo lang('Attachments');?>
:</th><td colspan="3"><ul id="c-files_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
" class="c-uploaded"><li><input type="file" id="c-file_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
" /></li><?php
$_from = $_smarty_tpl->tpl_vars['row']->value['files'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['file'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['file']->_loop = false;
$_smarty_tpl->tpl_vars['name'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['file']->value) {
$_smarty_tpl->tpl_vars['file']->_loop = true;
$foreach_file_Sav = $_smarty_tpl->tpl_vars['file'];
?><li class="pad"><input type="hidden" name="data[attachments][]" value="<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
" /><a href="javascript:;" onclick="CRM.file('<?php echo $_smarty_tpl->tpl_vars['file']->value;?>
',this);"><?php echo $_smarty_tpl->tpl_vars['name']->value;?>
</a></li><?php
$_smarty_tpl->tpl_vars['file'] = $foreach_file_Sav;
}
?></ul></td></tr><tr><td colspan="4" style="padding-left:2%;"><textarea name="data[descr]" class="c-textarea" id="c-descr_<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
" style="height:400px;" tabindex=3><?php echo $_smarty_tpl->tpl_vars['row']->value['descr'];?>
</textarea></td></tr><?php }
}
?>