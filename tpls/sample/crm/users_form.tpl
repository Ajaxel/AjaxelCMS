{strip}
<script type="text/javascript">
CRM.callback=function(){
	CRM.win_name='{$name}';
	$('#c-{$win_id}_win .c-button').button();
	$('#c-{$win_id}_win').submit(function(){
		return CRM.save(this,'{$row.id}','{$name}');
	});
	if (!$('#c-login_{$win_id}').length || $('#c-login_{$win_id}').val()) {
		CRM.focus('c-descr_{$win_id}');
	} else {
		CRM.focus('c-login_{$win_id}');	
	}
};
</script>
<tr>
	<th>{'Username'|lang}:</th>
	<td>
		{if $row.id}
			<b>{$row.login}</b>
		{else}
			<input type="text" name="data[login]" class="c-input" id="c-login_{$win_id}" tabindex=1 value="{$row.login|strform}" />
		{/if}
	</td>
	<th>SUM</th>
	<td><input type="text" name="data[price]" style="width:45%" onfocus="if(this.value<=0)this.select();" class="c-input" tabindex={$right} value="{$row.price|strform}" /></td>
</tr>
<tr>
	<th width="15%">{'Role'|lang}:</th>
	<td width="35%">
		{if !$row.id || $User.UserID==$conf.user.userid}<select name="data[roleid]" style="width:auto" tabindex=2 class="c-select" id="c-{$win_id}-win_roleids"><option value=""></option>{'Data'|Call:'getArray':'my:roles':$row.roleid}</select>{else}{'Data'|Call:'getVal':'my:roles':$row.roleid}{/if}
	</td>
	<th width="15%">{'Dated'|lang}:</th>
	<td width="35%"><input type="text" name="data[dated]" style="width:40%" class="c-input c-date" tabindex=5 value="{$row.dated|strform}" />&nbsp;
		<select name="data[dated_Hour]" class="c-select" style="width:50px" tabindex=6>
			{'Data'|Call:'getArray':'my:hours':$row.dated_Hour}
		</select>:
		<select name="data[dated_Minute]" class="c-select" style="width:50px" tabindex=7>
			{'Data'|Call:'getArray':'my:minutes':$row.dated_Minute}
		</select>
	</td>
</tr>
<tr>
	<th>{'Notes'|lang}:</th>
	<td colspan="3">
		<textarea name="data[descr]" class="c-textarea" id="c-descr_{$win_id}" style="height:110px" tabindex=3>{$row.descr}</textarea>
	</td>
</tr>
{if $row.id && $row.email}
	{capture assign='bottom'}
		<button type="button" class="c-button c-small" onclick="CRM.mail('{$row.email}','{$name}','{$row.id}')" tabindex=90>{'Send a mail'|lang}</button>
	{/capture}
	{assign var='bottom' value=$bottom scope='global'}
{/if}
{/strip}