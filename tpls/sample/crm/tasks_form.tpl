{strip}
<script type="text/javascript">
CRM.callback=function(){
	CRM.win_name='{$name}';
	$('#c-{$win_id}_win .c-button').button();
	$('#c-{$win_id}_win').submit(function(){
		return CRM.save(this,'{$row.id}','{$name}');
	});
	CRM.fill($('#c-{$win_id}-win_project'),$.extend(CRM.data.projects, { '#new':'-- New project --' }),'{$row.project|strJS}','&nbsp;');
	CRM.fill($('#c-{$win_id}-win_access'),CRM.data.access,{$row.access|intval},'Access');
	CRM.fill($('#c-{$win_id}-win_status'),CRM.data.status_tasks,{$row.status|intval},'Status');
	CRM.fill($('#c-{$win_id}-win_assign'),CRM.data.users,{$row.assign|intval},'&nbsp;');
	if ($('#c-title_{$win_id}').val()) {
		CRM.focus('c-descr_{$win_id}');
	} else {
		CRM.focus('c-title_{$win_id}');	
	}
	var project = CRM.temp.project;
	if (!project) project=$('#c-filter_{$name}_project').val();
	if (project) {
		$('#c-{$win_id}-win_i_project').val(project);
		$('#c-{$win_id}-win_project').val(project);
	}
	CRM.upload('file','{$upload.hash}', function(r){
		var a=r.split('/');
		$('<li>').addClass('pad').html('<input type="hidden" name="data[attachments][]" value="'+r+'" /><a href="javascript:;" onclick="CRM.file(\''+r+'\',this);">'+a[a.length-1]+'</a>').appendTo($('#c-files_{$win_id}'));
	});
};
</script>
<tr>
	<th>{'Project'|lang}:</th>
	<td>
		<input type="text" name="data[project]" tabindex=1 onblur="if(!this.value) $(this).hide().next().show().val(''); else CRM.temp.project=this.value" value="{$row.project|strform}" id="c-{$win_id}-win_i_project" class="c-input" style="display:none" />
		<select onchange="if(this.value=='#new'){ $(this).hide().prev().show().focus() }else{ $(this).prev().val(this.value);CRM.temp.project=this.value }" tabindex=1 class="c-select" id="c-{$win_id}-win_project"></select>
	</td>
	<th>{'Sum'|lang}:</th>
	<td><input type="text" name="data[price]" style="width:40%" onclick="if(this.value=='0.00') this.select();" class="c-input" tabindex=4 value="{$row.price|strform}" />&nbsp;
	</td>
</tr>
<tr>
	<th>{'Subject'|lang}:</th>
	<td colspan="3"><input type="text" name="data[title]" class="c-input" id="c-title_{$win_id}" tabindex=2 value="{$row.title|strform}" /></td>
</tr>
<tr>
	<th>{'Status / Access'|lang}:</th>
	<td>
		<select name="data[status]" style="width:auto" tabindex=5 class="c-select" id="c-{$win_id}-win_status"></select>&nbsp;
		{if !$row.id || $User.UserID==$row.userid}<select name="data[access]" style="width:auto" tabindex=6 class="c-select" id="c-{$win_id}-win_access"></select>{else}{'Data'|Call:'getVal':'my:access':$post.access}{/if}
	</td>
	<th>{'Startdate'|lang}:</th>
	<td><input type="text" name="data[dated]" style="width:40%" class="c-input c-date" tabindex=8 value="{$row.dated|strform}" />&nbsp;
		<select name="data[dated_Hour]" class="c-select" style="width:50px" tabindex=9>
			{'Data'|Call:'getArray':'my:hours':$row.dated_Hour}
		</select>:
		<select name="data[dated_Minute]" class="c-select" style="width:50px" tabindex=10>
			{'Data'|Call:'getArray':'my:minutes':$row.dated_Minute}
		</select>
	</td>
</tr>
<tr>
	<th>{'Assign for'|lang}:</th>
	<td>
		<select tabindex=7 class="c-select" name="data[assign]" id="c-{$win_id}-win_assign"></select>
	</td>
	<th>{'Deadline'|lang}:</th>
	<td><input type="text" name="data[deadline]" style="width:40%" class="c-input c-date" tabindex=11 value="{$row.deadline|strform}" />&nbsp;
		<select name="data[deadline_Hour]" class="c-select" style="width:50px" tabindex=12>
			{'Data'|Call:'getArray':'my:hours':$row.deadline_Hour}
		</select>:
		<select name="data[deadline_Minutes]" class="c-select" style="width:50px" tabindex=13>
			{'Data'|Call:'getArray':'my:minutes':$row.deadline_Minute}
		</select>
	</td>
</tr>
<tr>
	<th>{'Attachments'|lang}:</th>
	<td colspan="3">
		<ul id="c-files_{$win_id}" class="c-uploaded"><li><input type="file" id="c-file_{$win_id}" /></li>
		{foreach from=$row.files key=name item=file}
			<li class="pad"><input type="hidden" name="data[attachments][]" value="{$file}" /><a href="javascript:;" onclick="CRM.file('{$file}',this);">{$name}</a></li>
		{/foreach}
		</ul>
	</td>
</tr>
<tr>
	<td colspan="4" style="padding-left:2%;">
		<textarea name="data[descr]" class="c-textarea" id="c-descr_{$win_id}" style="height:400px;" tabindex=3>{$row.descr}</textarea>
	</td>
</tr>
{/strip}