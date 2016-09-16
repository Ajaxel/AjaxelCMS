{strip}
<script type="text/javascript">
$(function(){
CRM.tab_callback=function(){
	CRM.data.data={$data|json};
	CRM.fill($('#c-filter_{$name}_activities'),CRM.data.activities,false,'All activities');
	CRM.fill($('#c-filter_{$name}_interests'),CRM.data.interests_cnt,false,'All interests');
	CRM.fill($('#c-filter_{$name}_countries'),CRM.data.countries,false,'All countries');
	CRM.fill($('#c-filter_{$name}_languages'),CRM.data.languages,false,'All languages');
	CRM.fill($('#c-filter_{$name}_status'),CRM.data.status_clients,false,'Any status');
	CRM.fill($('#c-filter_{$name}_access'),CRM.data.access,false,'Any access');
	CRM.fn.html_{$name}(CRM.data.data,0,'{$name}');	
	CRM.add();
};
CRM.fn.html_{$name}=function(data, id, name) {
	var h='', i=0, ticked=false;
	CRM.pager(data.pager, id, name);
	var arr=[];
	$('#c-list_'+name+' input[type=checkbox]:checked').each(function(){
		arr.push(parseInt($(this).parent().parent().attr('id').substring(8)));
	});
	h = '<table>';
	if (data.list.length) {
		for (i in data.list) {
			a=data.list[i];
			ticked=false;
			for (x in arr) if(arr[x]==a.id) {
				ticked=true;
				break
			}
			h += '<tr ondblclick="CRM.open('+a.id+',this.childNodes[2],\''+name+'\');" class="c-row c-hov'+(i%2?' c-odd':'')+(id==a.id?' c-active':'')+(ticked?' c-tick':'')+(a.locker_login?' c-locked':'')+((data.lock&&data.lock.id==a.id)?' c-lock':'')+' c-'+a.class+'" id="'+name+'_'+a.id+'">';
			
			h += '<td class="c-c1" onclick="CRM.tick(this)"><input type="checkbox"'+(ticked?' checked="checked"':'')+' onclick="if(this.checked)this.checked=false;else this.checked=true" name="data[ids][]" value="'+a.id+'" /> '+a.id+'</td>';
			h += '<td class="c-c2 c-link c-open" id="c-index_{$name}-'+a.id+'"'+(id==a.id?' title="[alt+e]"':(i<=9?' title="[alt+'+(parseInt(i)+1)+']"':''))+' onclick="CRM.open('+a.id+',this,\''+name+'\');"><span class="c-num">'+(parseInt(i)+parseInt(data.pager.page)*parseInt(data.pager.limit)+1)+'.</span> '+a.name+(a.locker_login?' [locked by '+a.locker_login+']':'')+((data.lock&&data.lock.id==a.id)?' <span class="c-countdown" id="c-countdown_'+name+'_'+a.id+'">'+data.lock.locktime+'</span>':'')+(a.email?' <a href="javascript:;" onmousedown="CRM.mail(\''+a.email+'\',\''+name+'\','+a.id+');return false;"><img class="c-ico" src="/tpls/img/mail.png" title="'+a.email+'" /></a>':'')+'</td>';
			h += '<td class="c-c3 c-status" rel="'+a.status+'" onmousedown="CRM.status(this,'+a.id+',\'status_clients\')"><div><span>'+CRM.data.status_clients[a.status]+'</span>'+(a.sex?'<img src="/tpls/img/'+(a.sex=='F'?'fe':'')+'male.gif" class="c-ico" />':'')+'</div></td>';
			h += '<td class="c-c4"'+(a.notify?' title="Notify: '+a.notify+'':'')+'">'+(a.notify?'<b>!</b>':'')+''+a.added+'</td>';
			h += '<td class="c-c5">'+a.company+'</td>';
			h += '<td class="c-c6"><a'+(!a.www.length?' style="visibility:hidden"':'')+' href="http://'+a.www.replace(/http:\/\//,'')+'" target="_blank"><img src="/tpls/img/www.png" title="'+a.www+'" width="16" /></a> <a'+(!a.skype?' style="visibility:hidden"':'')+' href="skype://'+a.skype+'?call"><img src="/tpls/img/skype.png" title="'+a.skype+'" width="16" /></a></td>';
			h += '<td class="c-c7">'+(a.code?'<img src="/tpls/img/flags/16/'+a.code+'.png" title="'+a.code+'" width="16" /></a>':'&nbsp;')+'</td>';
			{foreach from=$conf.checks key=i item=c}
			h += '<td class="c-c8 sub" title="'+(a.ch{$i}_date?a.ch{$i}_date:'{$c[1]}')+'">'+(a.ch{$i}=='Y'?'<b style="color:green">{$c[0]}</b>':(a.ch{$i}=='N'?'<span style="color:#999">no</span>':'<span style="color:#ccc">-</span>'))+'</td>';
			{/foreach}
			var r=[], c=[];
			for (i in a.activities) if (CRM.data.activities[a.activities[i]]) c.push(CRM.data.activities[a.activities[i]]); 
			for (i in a.interests) if (CRM.data.interests[a.interests[i]]) r.push(CRM.data.interests[a.interests[i]]);
			h += '<td class="c-c9" title="'+c.join(', ')+'">'+(a.activities_cnt?a.activities_cnt+' '+(a.activities_cnt==1?'activity':'activities')+'; ':'')+r.join(', ')+'</td>';
			h += '<td class="c-c90 r">'+a.price+'</td>';
			h += '</tr>';
		}
	} else {
		h += '<tr><td class="c-nothing">Nothing found.</td></tr>';
	}
	h += '</table>';
	$('#c-list_'+name).html(h);
	if (data.lock) {
		CRM.lock(data.lock.id,data.lock.locked);
		CRM.countdown($('.c-countdown'), function(){
			CRM.action('unlock');	
		});
	}
};
});
</script>
<style type="text/css">
/*Clients*/
#c-form_clients .c-c1 { width:5%;white-space:nowrap }
#c-form_clients .c-c2 { width:29% }
#c-form_clients .c-c3 { width:6%;white-space:nowrap }
#c-form_clients .c-c4 { width:6%;white-space:nowrap }
#c-form_clients td.c-c4 { text-align:center }
#c-form_clients .c-c5 { width:14% }
#c-form_clients .c-c6 { width:3%;text-align:center;white-space:nowrap }
#c-form_clients .c-c7 { width:2%;text-align:center }
#c-form_clients .c-c6.c-c7 { width:5% }
#c-form_clients .c-c8 { width:12% }
#c-form_clients .c-c8.sub { width:{12/$conf.checks_total}%;text-align:center }
#c-form_clients .c-c9 { width:18% }
#c-form_clients .c-c3>div { width:80px }
</style>
<table class="c-table">
	<thead class="c-unsel">
	<tr>
		<th class="c-c1 ui-state-highlight"><div id="c-order_{$name}-id" class="c-sort c-up">ID</div></th>
		<th class="c-c2 ui-state-highlight"><div id="c-order_{$name}-name" class="c-sort">Name / Email</div></th>
		<th class="c-c3 ui-state-highlight"><div id="c-order_{$name}-status" class="c-sort">Status / Sex</div></th>
		<th class="c-c4 ui-state-highlight"><div id="c-order_{$name}-added" class="c-sort">Added</div></th>
		<th class="c-c5 ui-state-highlight"><div id="c-order_{$name}-company" class="c-sort">Company / Dated</div></th>
		<th class="c-c6 ui-state-highlight"><div id="c-order_{$name}-www" class="c-sort" title="Website / Skype">W / S</div></th>
		<th class="c-c7 ui-state-highlight"><div id="c-order_{$name}-country" class="c-sort" title="Country">C</div></th>
		{foreach from=$conf.checks key=i item=c}
			<th class="c-c8 sub ui-state-highlight" title="{$c[1]}"><div id="c-order_{$name}-ch{$i}" class="c-sort">{$c[0]}</div></th>
		{/foreach}		
		<th class="c-c9 ui-state-highlight"><div id="c-order_{$name}-interests" class="c-sort">Interests / Activities</div></th>
		<th class="c-c90 ui-state-highlight"><div id="c-order_{$name}-sum" class="c-sort">SUM</div></th>
		<th class="c-c91 ui-state-highlight" onclick="CRM.help()"><div>?</div></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>
			<input type="text" name="f[id_from]" style="width:94%" id="c-first_{$name}" class="c-input c-number" />
		</td>
		<td>
			<input type="text" name="f[name]" class="c-input" />
		</td>
		<td>
			<select name="f[status]" class="c-select" id="c-filter_{$name}_status"></select>
		</td>
		<td>
			<input type="text" name="f[added_from]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[company]" class="c-input" />
		</td>
		<td colspan="2" class="c-c6 c-c7">
			<input type="text" name="f[www]" style="width:94%" class="c-input" />
		</td>
		{foreach from=$conf.checks key=i item=c}
			<td class="c-c8 sub"><input type="checkbox" name="f[ch{$i}][]" onclick="CRM.find()" value="Y" /></td>
		{/foreach}	
		<td>
			<select class="c-select" name="f[interests]" id="c-filter_{$name}_interests"></select>
		</td>
		<td colspan="2">
			<button type="button" class="c-button" onclick="CRM.find()">Find</button>
		</td>
	</tr>
	<tr>
		<td>
			<input type="text" name="f[id_to]" style="width:94%" class="c-input c-number" />
		</td>
		<td>
			<div style="width:280px;">
			<select name="f[languages]" id="c-filter_{$name}_languages" style="width:70px" class="c-select"></select>&nbsp;
			<select name="f[userid]" class="c-select" style="width:110px">
				<option value="" style="color:#ccc">All registrators</option>
				{'Data'|Call:'getArray':'my:authors':0}
			</select>&nbsp;
			<select name="f[access]" class="c-select" style="width:90px" id="c-filter_{$name}_access"></select>
			</div>
		</td>
		<td>
			<select name="f[sex]" class="c-select">
				<option value="" style="color:#ccc">Any</option>
				{'Data'|Call:'getArray':'my:sex':0}
			</select>
		</td>
		<td>
			<input type="text" name="f[added_to]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td nowrap>
			<input type="text" name="f[dated_from]" onchange="CRM.find()" class="c-date c-input" style="width:40%" /> - <input type="text" name="f[dated_to]" onchange="CRM.find()" class="c-date c-input" style="width:40%" />
		</td>
		<td colspan="2">
			<select class="c-select" name="f[country]" id="c-filter_{$name}_countries"></select>
		</td>
		{foreach from=$conf.checks key=i item=c}
			<td class="c-c8 sub"><input type="checkbox" name="f[ch{$i}][]" onclick="CRM.find()" value="N" /></td>
		{/foreach}	
		<td>
			<select class="c-select" name="f[activities]" id="c-filter_{$name}_activities"></select>
		</td>
		<td colspan="2">
			<button type="reset" class="c-button" onClick="CRM.reset=true;CRM.find();">Reset</button>
		</td>
	</tr>
	</tbody>
</table>
{/strip}