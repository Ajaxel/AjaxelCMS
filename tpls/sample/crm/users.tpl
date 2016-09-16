{strip}
<script type="text/javascript">
$(function(){
CRM.tab_callback=function(){
	CRM.data.data={$data|json};
	CRM.fn.html_{$name}(CRM.data.data,0,'{$name}');
	CRM.add();
};
CRM.fn.html_{$name}=function(data, id, name) {
	var h='', i=0, ticked=false;
	CRM.pager(data.pager, id, name,{ lock:1 });
	var arr=[];
	$('#c-list_'+name+' input[type=checkbox]:checked').each(function(){
		arr.push(parseInt($(this).parent().parent().attr('id').substring(8)));
	});
	
	h = '<table>';
	if (data.list&&data.list.length) {
		for (i in data.list) {
			a=data.list[i];
			ticked=false;
			for (x in arr) if(arr[x]==a.id) {
				ticked=true;
				break
			}
			h += '<tr ondblclick="CRM.open('+a.id+',this.childNodes[2],\''+name+'\');" class="c-row c-hov'+(i%2?' c-odd':'')+(id==a.id?' c-active':'')+(ticked?' c-tick':'')+(a.locker_login?' c-locked':'')+'" id="'+name+'_'+a.id+'">';
			
			h += '<td class="c-c1" onclick="CRM.tick(this)"><input type="checkbox"'+(ticked?' checked="checked"':'')+' onclick="if(this.checked)this.checked=false;else this.checked=true" name="data[ids][]" value="'+a.id+'" /> '+a.id+'</td>';
			h += '<td class="c-c2 c-link c-open" id="c-index_{$name}-'+a.id+'"'+(id==a.id?' title="[alt+e]"':(i<=9?' title="[alt+'+(parseInt(i)+1)+']"':''))+' onclick="CRM.open('+a.id+',this,\''+name+'\');"><span class="c-num">'+(parseInt(i)+parseInt(data.pager.page)*parseInt(data.pager.limit)+1)+'.</span> '+a.login+' <span class="c-num">['+a.group+']</span>'+(a.email?' <a href="javascript:;" onmousedown="CRM.mail(\''+a.email+'\',\''+name+'\','+a.id+');return false;"><img class="c-ico" src="/tpls/img/mail.png" title="'+a.email+'" /></a>':'')+'</td>';
			h += '<td class="c-c3">'+a.role+'</td>';
			h += '<td class="c-c4">stats are coming soon</td>';
			h += '<td class="c-c5">'+a.dated+'</td>';
			h += '<td class="c-c6">'+a.added+'</td>';
			h += '<td class="c-c90 r">'+a.price+'</td>';
			h += '</tr>';
		}
	} else {
		h += '<tr><td class="c-nothing">Nothing found.</td></tr>';
	}
	h += '</table>';
	$('#c-list_'+name).html(h);
};
});
</script>
<style type="text/css">{literal}
/*Users*/
#c-form_users .c-c1 {width:5%;white-space:nowrap}
#c-form_users .c-c2 {width:27%}
#c-form_users .c-c3 {width:9%}
#c-form_users .c-c4 {width:40%}
#c-form_users .c-c5 {width:7%;white-space:nowrap}
#c-form_users .c-c6 {width:7%;white-space:nowrap}
{/literal}</style>
<table class="c-table">
	<thead class="c-unsel">
	<tr>
		<th class="c-c1 ui-state-highlight"><div id="c-order_{$name}-id" class="c-sort c-up">ID</div></th>
		<th class="c-c2 ui-state-highlight"><div id="c-order_{$name}-login" class="c-sort">Username / Email</div></th>
		<th class="c-c3 ui-state-highlight"><div id="c-order_{$name}-roleid" class="c-sort">Role</div></th>
		<th class="c-c4 ui-state-highlight"><div id="c-order_{$name}-stats" class="c-sort">Stats</div></th>
		<th class="c-c5 ui-state-highlight"><div id="c-order_{$name}-dated" class="c-sort">Dated</div></th>
		<th class="c-c6 ui-state-highlight"><div id="c-order_{$name}-added" class="c-sort">Joined</div></th>
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
			<input type="text" name="f[login]" class="c-input" />
		</td>
		<td>
			<select name="f[roleid]" class="c-select">
				<option value="" style="color:#ccc">Any</option>
				{'Data'|Call:'getArray':'my:roles':0}
			</select>
		</td>
		<td>&nbsp;
			
		</td>
		<td>
			<input type="text" name="f[dated_from]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[added_from]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td colspan="2">
			<button type="button" class="c-button" onclick="CRM.find();">Find</button>
		</td>
	</tr>
	<tr>
		<td>
			<input type="text" name="f[id_to]" style="width:94%" class="c-input c-number" />
		</td>
		<td>
			
		</td>
		<td>&nbsp;
			
		</td>
		<td>&nbsp;
			
		</td>
		<td>
			<input type="text" name="f[dated_to]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td>
			<input type="text" name="f[added_to]" onchange="CRM.find()" style="width:95%" class="c-date c-input" />
		</td>
		<td colspan="2">
			<button type="reset" class="c-button" onClick="CRM.reset=true;CRM.find();">Reset</button>
		</td>
	</tr>
	</tbody>
</table>
{/strip}