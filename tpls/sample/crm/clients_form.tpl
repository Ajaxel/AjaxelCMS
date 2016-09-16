{strip}
<script type="text/javascript">
CRM.callback=function(){
	CRM.win_name='{$name}';
	CRM.data.date='{'d/m/Y'|date}';
	$('#c-{$win_id}_win .c-button').button();
	$('#c-{$win_id}_win').submit(function(){
		return CRM.save(this,'{$row.id}','{$name}','{$div_id}');
	});
	CRM.fill($('#c-{$win_id}-win_access'),CRM.data.access,{$row.access|intval},'Access');
	CRM.fill($('#c-{$win_id}-win_status'),CRM.data.status_clients,{$row.status|intval},'Status');
	CRM.fill($('#c-{$win_id}-win_activities'),CRM.data.activities,{$row.activities|json_encode});
	CRM.fill($('#c-{$win_id}-win_languages'),CRM.data.languages,{$row.languages|json_encode});
	CRM.fill($('#c-{$win_id}-win_interests'),CRM.data.interests,{$row.interests|json_encode});
	CRM.focus('c-name_{$win_id}');
};
</script>
{assign var='left' value=1}
{assign var='right' value=50}
<tr>
	<th width="14%">{'Client name'|lang}:</th>
	<td width="36%"><input type="text" name="data[name]" class="c-input" id="c-name_{$win_id}" tabindex={$left} value="{$row.name|strform}" /></td>
	<th width="14%">{'Sex'|lang} / {'SUM'|lang}:</th>
	<td width="36%">
		<div class="l">
		<table style="width:auto" class="c-radios">
		{$holder->destruct()}
		{$holder->set('type','radio')}
		{$holder->set('name','data[sex]')}
		{$holder->set('colsPerRow','2')}
		{'Data'|Call:'getArray':'my:sex':$row.sex:$holder->getAll()}
		</table>
		</div>
		<div class="r" style="padding-right:2%;padding-top:3px;">
			<input type="text" name="data[price]" style="width:65%" onfocus="if(this.value<=0)this.select();" class="c-input" tabindex={$right} value="{$row.price|strform}" />
		</div>
	</td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Status / Access'|lang}:</th>
	<td>
		<select name="data[status]" style="width:auto" tabindex={$left} class="c-select" id="c-{$win_id}-win_status"></select>&nbsp;
		{if !$row.id || $User.UserID==$row.userid}<select name="data[access]" style="width:auto" tabindex={$left} class="c-select" id="c-{$win_id}-win_access"></select>{else}{'Data'|Call:'getVal':'my:access':$post.access}{/if}
	</td>
	<th>{'Dated'|lang}:</th>
	<td><input type="text" name="data[dated]" style="width:40%" class="c-input c-date" tabindex={$right} value="{$row.dated|strform}" />&nbsp;
		<select name="data[dated_Hour]" class="c-select" style="width:50px">
			{'Data'|Call:'getArray':'my:hours':$row.dated_Hour}
		</select>:
		<select name="data[dated_Minute]" class="c-select" style="width:50px">
			{'Data'|Call:'getArray':'my:minutes':$row.dated_Minute}
		</select>
		&nbsp;<input type="checkbox" name="data[calendar]" value="Y" title="Show in calendar" style="position:relative;top:2px"{if $row.calendar=='Y'} checked="checked"{/if} />
	</td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>E-mail:</th>
	<td><input type="text" name="data[email]" class="c-input" id="c-email_{$win_id}" tabindex={$left} value="{$row.email|strform}" /></td>
	<th>Skype / MSN:</th>
	<td><input type="text" name="data[skype]" class="c-input" style="width:46%" tabindex={$right} value="{$row.skype|strform}" /> <input type="text" name="data[msn]" class="c-input" style="width:47%" tabindex={$right} value="{$row.msn|strform}" /></td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Company'|lang}:</th>
	<td><input type="text" name="data[company]" class="c-input" tabindex={$left} value="{$row.company|strform}" /></td>		
	<th>{'Position'|lang}:</th>
	<td><input type="text" name="data[position]" tabindex={$right} class="c-input" value="{$row.position|strform}" /></td>
</tr>

{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Phone'|lang} / {'Fax'|lang}:</th>
	<td><input type="text" name="data[phone]" style="width:47%" class="c-input" tabindex={$left} value="{$row.phone|strform}" /> <input type="text" name="data[fax]" class="c-input" style="width:46%;" tabindex={$left} value="{$row.fax|strform}" /></td>
	<th>{'Website'|lang}:</th>
	<td><input type="text" name="data[www]" class="c-input" tabindex={$right} value="{$row.www|strform}" /></td>
	
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Country'|lang}:</th>
	<td><select name="data[country]" id="find_countries{$win_id}" tabindex={$left} class="c-select" onchange="S.G.populateGeo(this,'form','{$win_id}')">
	<option value=""></option>
	{'Data'|Call:'getArray':'countries':$row.country}
	</select></td>
	<th rowspan="6">{'Checkboxes'|lang}:</th>
	<td rowspan="6" style="padding:0">
		<div style="height:144px;overflow:auto;padding:2px 1%;width:95%" class="c-select">
			<table class="c-sub_form">
				{foreach from=$conf.checks key=i item=a}
				<tr>
					<th nowrap style="padding-right:4px">{$a[1]}</th>
					<td><label><input type="checkbox" id="c-ch{$i}Y_{$win_id}" onclick="CRM.ch(this)" name="data[ch{$i}]"{if $row.ch[$i].checked=='Y'} checked="checked"{/if} value="Y" />Yes</label></td>
					<td><label><input type="checkbox" id="c-ch{$i}N_{$win_id}" onclick="CRM.ch(this)" name="data[ch{$i}]"{if $row.ch[$i].checked=='N'} checked="checked"{/if} value="N" />No</label></td>
					<td><input type="text" name="data[ch{$i}_date]" id="c-ch{$i}d_{$win_id}" style="width:80px" class="c-input c-date" tabindex={$right} value="{$row.ch[$i].date}" /></td>
				</tr>	
				{/foreach}
			</table>
		</div>
	</td>
</tr>

{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'State'|lang}:</th>
	{assign var='opts' value='Data'|Call:'getArray':'states':$row.state}
	<td><select name="data[state]"{if !$opts} disabled="disabled"{/if} id="find_states{$win_id}" tabindex={$left} class="c-select" onchange="S.G.populateGeo(this,'form','{$win_id}')">
		{$opts}
	</select></td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'City'|lang}:</th>
	{assign var='opts' value='Data'|Call:'getArray':'cities':$row.city}
	<td><select name="data[city]"{if !$opts} disabled="disabled"{/if} tabindex={$left} id="find_cities{$win_id}" class="c-select" onchange="S.G.populateGeo(this,'form','{$win_id}')">
		{$opts}
	</select></td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'District'|lang}:</th>
	{assign var='opts' value='Data'|Call:'getArray':'districts':$row.district}
	<td><select name="data[district]"{if !$opts} disabled="disabled"{/if} tabindex={$left} id="find_districts{$win_id}" class="c-select" onchange="S.G.populateGeo(this,'form','{$win_id}')">
		{$opts}
	</select></td>
</tr>


{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Address'|lang}:</th>
	<td><input type="text" name="data[address]" class="c-input" tabindex={$left} value="{$row.address|strform}" /></td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Post code'|lang}:</th>
	<td><input type="text" name="data[zip]" class="c-input" style="width:40%" tabindex={$left} value="{$row.zip|strform}" /></td>
</tr>


{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Languages'|lang}:{if $row.languages}<br />{$row.languages|count} {'selected'|lang}{/if}</th>
	<td><ul class="c-select" rel="data[languages][]" id="c-{$win_id}-win_languages" style="height:50px"></ul></td>
	<th>{'Interested in'|lang}:{if $row.interests}<br />{$row.interests|count} {'selected'|lang}{/if}</th>
	<td>
		<ul class="c-select" rel="data[interests][]" id="c-{$win_id}-win_interests" style="height:50px"></ul>
	</td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
	<th>{'Notes'|lang}:</th>
	<td><textarea name="data[notes]" class="c-textarea" tabindex={$left} style="height:100px">{$row.notes|strform}</textarea></td>
	<th rowspan="2">{'Activities'|lang}:{if $row.activities}<br />{$row.activities|count} selected{/if}</th>
	<td rowspan="2"><ul class="c-select" rel="data[activities][]" id="c-{$win_id}-win_activities" style="height:124px"></ul></td>
</tr>
{assign var='left' value=$left+1}
{assign var='right' value=$right+1}
<tr>
<th>{'Notify'|lang}:</th>
	<td><input type="text" name="data[notify]" style="width:40%" class="c-input c-date" tabindex={$right} value="{$row.notify|strform}" />&nbsp;
		<select name="data[notify_Hour]" class="c-select" style="width:50px">
			{'Data'|Call:'getArray':'my:hours':$row.notify_Hour}
		</select>:
		<select name="data[notify_Minute]" class="c-select" style="width:50px">
			{'Data'|Call:'getArray':'my:minutes':$row.notify_Minute}
		</select>
	</td>
</tr>




{if $row.id && $row.email}
	{capture assign='bottom'}
		<button type="button" class="c-button c-small" onclick="CRM.mail('{$row.email}','{$name}','{$row.id}')" tabindex=90>{'Send a mail'|lang}</button>
	{/capture}
	{assign var='bottom' value=$bottom scope='global'}
{/if}
{/strip}