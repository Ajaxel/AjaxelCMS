<script type="text/javascript">
CRM.callback=function(){
	CRM.win_name='{$name}';
	$('#c-{$win_id}_win .c-button').button();
	$('#c-{$win_id}_win').submit(function(){
		return CRM.save(this,'{$row.id}','{$name}');
	});
	if (!$('#c-to_{$win_id}').val()) {
		$('#c-to_{$win_id}').focus();
	}
	else if (!$('#c-subject_{$win_id}').val()) {
		$('#c-subject_{$win_id}').focus();
	} else {
		CRM.focus('c-body_{$win_id}');
	}
	CRM.tinymce('body',300);
	CRM.upload('file','{$upload.hash}', function(r){
		var a=r.split('/');
		$('<li>').addClass('pad').html('<input type="hidden" name="data[attachments][]" value="'+r+'" /><a href="javascript:;" onclick="CRM.file(\''+r+'\',this);">'+a[a.length-1]+'</a>').appendTo($('#c-files_{$win_id}'));
	});
};
</script>
{strip}
<tr>
	<th>{'Send to'|lang}:</th>
	<td><input type="text" name="data[to]" class="c-input" id="c-to_{$win_id}" tabindex=1 value="{$row.to|strform}" /></td>
	{*<th>{'Send on'|lang}:</th>
	<td><input type="text" name="data[dated]" style="width:40%" class="c-input c-date" tabindex=5 value="{$row.dated|strform}" />&nbsp;
		<select name="data[dated_Hour]" class="c-select" style="width:50px" tabindex=6>
			{'Data'|Call:'getArray':'my:hours':$row.dated_Hour}
		</select>:
		<select name="data[dated_Minute]" class="c-select" style="width:50px" tabindex=7>
			{'Data'|Call:'getArray':'my:minutes':$row.dated_Minute}
		</select>
	</td>*}<td colspan="2"></td>
</tr>
<tr>
	<th>{'From e-mail'|lang}:</th>
	<td><input type="text" name="data[from_email]" class="c-input" id="c-fromemail_{$win_id}" tabindex=1 value="{$row.from_email|strform}" /></td>
	<th>{'Name'|lang}:</th>
	<td><input type="text" name="data[from_name]" class="c-input" id="c-fromname_{$win_id}" tabindex=1 value="{$row.from_name|strform}" /></td>
</tr>
<tr>
	<th>{'Template / Save as'|lang}:</th>
	<td><select style="width:55%" name="data[template]" class="c-select" id="c-template_{$win_id}" onchange="CRM.mailtpl(this.value);if(this.value)$('#c-{$win_id}_win-del').show(); else $('#c-{$win_id}_win-del').hide();" tabindex=2><option value=""></option>{'Data'|Call:'getArray':'my:email_tpls':''}</select> <input type="text" name="data[save]" class="c-input" style="width:39%" value="" /></td>
	<th>{'Variables'|lang}:</th>
	<td><select style="width:auto" class="c-select" unselectable="on" onchange="$('#c-body_{$win_id}').tinymce().execCommand('mceInsertContent',false,'{ldelim}${'table'|post}.'+this.value+'{rdelim}');"><option value=""></option>{'Data'|Call:'getArray':'my:variables':''}</select></td>
</tr>
<tr>
	<th>{'Subject'|lang}:</th>
	<td colspan="3"><input type="text" name="data[subject]" class="c-input" id="c-subject_{$win_id}" tabindex=3 value="{$row.subject|strform}" /></td>
</tr>
<tr>
	<th>{'Attachments'|lang}:</th>
	<td colspan="3">
		<ul id="c-files_{$win_id}" class="c-uploaded"><li><input type="file" id="c-file_{$win_id}" /></li></ul>
	</td>
</tr>
<tr>
	<td colspan="4" style="padding:0 2% 0 1%;" class="tinymce">
		<textarea name="data[body]" class="c-textarea" id="c-body_{$win_id}" style="height:345px" tabindex=4>{$row.body}</textarea>
	</td>
</tr>

{capture assign='bottom'}
	<button type="button" class="c-button c-small" onclick="$('#c-{$win_id}_win-act').val('save');CRM.no_close=true;$('#c-{$win_id}_win').submit();" tabindex=90>{'Save only'|lang}</button>
	<button type="button" class="c-button c-small" style="display:none" id="c-{$win_id}_win-del" onclick="$('#c-{$win_id}_win-act').val('delete');CRM.no_close=true;$('#c-{$win_id}_win').submit();" tabindex=90>{'Delete'|lang}</button>
	&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="c-button c-small" onclick="$('#c-{$win_id}_win-act').val('preview');CRM.no_close=true;$('#c-{$win_id}_win').submit();" tabindex=90>{'Preview'|lang}</button>
	<button type="button" class="c-button c-small" onclick="$('#c-{$win_id}_win-act').val('print');CRM.no_close=true;$('#c-{$win_id}_win').submit();" tabindex=90>{'Print'|lang}</button>
{/capture}
{assign var='bottom' value=$bottom scope='global'}
{/strip}