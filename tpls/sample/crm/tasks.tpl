{strip}
<script type="text/javascript">
$(function(){
CRM.tab_callback=function(){
	CRM.data.data={$data|json};
	CRM.fill($('#c-filter_{$name}_access'),CRM.data.access,false,'Any access');
	CRM.fill($('#c-filter_{$name}_status'),CRM.data.status_tasks,false,'Any status');
	CRM.fill($('#c-filter_{$name}_project'),CRM.data.projects,false,'Any project');
	CRM.fill($('#c-filter_{$name}_assign'),CRM.data.users,false,'Any');
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
	if (data.projects) {
		var ex=data.projects.split('||');
		CRM.data.projects=[];
		for (i in ex) CRM.data.projects[ex[i]]=ex[i];
		CRM.fill($('#c-filter_{$name}_project'),CRM.data.projects,false,'Any project');
	}
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
			h += '<td class="c-c3"><b>'+a.project+'</b> '+(a.assign?' -&gt; <a href="javascript:;" onmousedown="CRM.mail(\''+a.email+'\',\''+name+'\','+a.id+');return false;">'+a.assign+'</a>':'')+'</td>';
			h += '<td class="c-c2 c-link c-open" id="c-index_{$name}-'+a.id+'"'+(id==a.id?' title="[alt+e]"':(i<=9?' title="[alt+'+(parseInt(i)+1)+']"':''))+' onclick="CRM.open('+a.id+',this,\''+name+'\');"><span class="c-num">'+(parseInt(i)+parseInt(data.pager.page)*parseInt(data.pager.limit)+1)+'.</span> '+a.title+(a.locker_login?' [locked by '+a.locker_login+']':'')+((data.lock&&data.lock.id==a.id)?' <span class="c-countdown" id="c-countdown_'+name+'_'+a.id+'">'+data.lock.locktime+'</span>':'')+'<div class="c-descr">'+a.descr+'</div></td>';
			h += '<td class="c-c4 c-status" rel="'+a.status+'" onmousedown="CRM.status(this,'+a.id+',\'status_tasks\')"><div><span>'+CRM.data.status_tasks[a.status]+'</span></div></td>';
			h += '<td class="c-c5">'+a.added+'</td>';
			h += '<td class="c-c6">'+(a.dated?a.dated:'-')+'</td>';
			h += '<td class="c-c7">'+(a.deadline?a.deadline:'-')+'</td>';
			h += '<td class="c-c90">'+(a.price)+'</td>';
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
<style type="text/css">{literal}
/*Tasks*/
#c-form_tasks .c-c1 {width:5%;white-space:nowrap}
#c-form_tasks .c-c2 {width:52%}
#c-form_tasks .c-c3 {width:12%}
#c-form_tasks .c-c4 {width:6%;white-space:nowrap}
#c-form_tasks .c-c5 {width:6%;white-space:nowrap}
#c-form_tasks .c-c6 {width:7%;white-space:nowrap}
#c-form_tasks .c-c7 {width:7%;white-space:nowrap}
{/literal}</style>

<table class="c-table">
	<thead class="c-unsel">
	<tr>
		<th class="c-c1 ui-state-highlight"><div id="c-order_{$name}-id" class="c-sort c-up">ID</div></th>
		<th class="c-c3 ui-state-highlight"><div id="c-order_{$name}-project" class="c-sort">Project / For</div></th>
		<th class="c-c2 ui-state-highlight"><div id="c-order_{$name}-title" class="c-sort">Subject / Description</div></th>
		<th class="c-c4 ui-state-highlight"><div id="c-order_{$name}-status" class="c-sort">Status</div></th>
		<th class="c-c5 ui-state-highlight"><div id="c-order_{$name}-added" class="c-sort">Added</div></th>
		<th class="c-c6 ui-state-highlight"><div id="c-order_{$name}-dated" class="c-sort">Startdate</div></th>
		<th class="c-c7 ui-state-highlight"><div id="c-order_{$name}-deadline" class="c-sort">Deadline</div></th>
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
			<select name="f[project]" class="c-select" id="c-filter_{$name}_project"></select>
		</td>
		<td>
			<input type="text" name="f[title]" class="c-input" />&nbsp;
		</td>
		<td>
			<select name="f[status]" class="c-select" id="c-filter_{$name}_status"></select>
		</td>
		<td>
			<input type="text" name="f[added_from]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[dated_from]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[deadline_from]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
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
			<select name="f[assign]" class="c-select" id="c-filter_{$name}_assign"></select>
		</td>
		<td>
			<div style="width:280px;">
			<select name="f[userid]" class="c-select" style="width:110px">
				<option value="" style="color:#ccc">Everyone</option>
				{'Data'|Call:'getArray':'my:authors':0}
			</select>&nbsp;
			<select name="f[access]" class="c-select" style="width:90px" id="c-filter_{$name}_access"></select>
			</div>
		</td>
		<td>&nbsp;</td>
		<td>
			<input type="text" name="f[added_to]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[dated_to]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[deadline_to]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td colspan="2">
			<button type="reset" class="c-button" onClick="CRM.reset=true;CRM.find();">Reset</button>
		</td>
	</tr>
	</tbody>
</table>
{/strip}