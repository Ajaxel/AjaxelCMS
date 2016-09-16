{strip}
<script type="text/javascript">
CRM.tab_callback=function(){

	var data={$data|json};
	var h = '',d,a;
	h += '<div class="ui-state-active c-calendar_top" id="c-middle_{$name}">';
	h += '<b>'+data.options.year+' - '+data.options.month_name+'</b>';
	h += '<a href="/crm/calendar/year-'+(data.options.year-1)+'/month-'+data.options.month+'"><img src="/tpls/img/oxygen/16x16/actions/arrow-left-double.png" /></a>';
	h += '<a href="/crm/calendar/year-'+data.options.prev_year+'/month-'+data.options.prev_month+'"><img src="/tpls/img/oxygen/16x16/actions/arrow-left.png" /></a>';
	h += '<a href="/crm/calendar/year-'+data.options.next_year+'/month-'+data.options.next_month+'"><img src="/tpls/img/oxygen/16x16/actions/arrow-right.png" /></a>';
	h += '<a href="/crm/calendar/year-'+(data.options.year+1)+'/month-'+data.options.month+'"><img src="/tpls/img/oxygen/16x16/actions/arrow-right-double.png" /></a>';
	h += '<div class="r">'+data.dropdown+'</div>';
	h += '</div>';
	h += '<table id="c-{$name}_height" class="c-cal ui-state-active">';
	h += '<thead></thead><tbody>';
	
	var rows=5,i=0;
	for (day in data.days) i++;
	if (i==28) rows=4;
	CRM.height=CRM.get_height();
	var height=CRM.height/rows-5*2-17;
	
	for (day in data.days) {
		var d=data.days[day];
		if (d.tr_open) h+='<tr>';
		h += '<td class="'+d.style+' '+d.type+' day ui-corner-all  ui-state-default" id="cal_'+day+'">';
		h += '<div class="d ui-corner-top ui-dialog-titlebar'+(d.style=='today'?' ui-state-active':((d.style=='sunday' || d.style=='saturday')?' ui-state-highlight':' ui-state-default'))+'">'+d.day+' '+d.month_name+'</div>';
		h += '<div class="c-heights" style="height:'+height+'px">';
		if (d.data) {
			for (i in d.data) {
				a=d.data[i];
				if (a.table=='tasks') {
					h += '<a href="javascript:;" onclick="CRM.open('+a.id+',false,\'tasks\',false,this,\'calendar\');" class="c-self" id="c-cal_'+a.table+'_'+a.id+'"><span>'+a.time+'</span> '+a.title+'</a>';
				}
				else if (a.table=='tasks_end') {
					h += '<a href="javascript:;" onclick="CRM.open('+a.id+',false,\'tasks\',false,this,\'calendar\');" class="c-end" id="c-cal_'+a.table+'_'+a.id+'"><span>'+a.time+'</span> '+a.title+'</a>';
				}
				else {
					h += '<a href="javascript:;" onclick="CRM.open('+a.id+',false,\''+a.table+'\',false,this,\'calendar\');" class="c-other" id="c-cal_'+a.table+'_'+a.id+'"><span>'+a.time+'</span> '+a.title+'</a>';	
				}
			}
		}
		h += '</div></td>';
		if (d.tr_close) h += '</tr>';
	}
	h += '</tbody></table>';
	$('#c-content_{$name}').html(h);
};
CRM.diff_callback=function(data){

	$('#'+data.diff_id).html(data.diff_html);
};
CRM.tab_callback_resize=function(by){
	if (by!='resize') return;
	var table=$('#c-{$name}_height');
	var rows=5;
	
	if ($('#c-{$name}_height .c-heights').length==28) rows=4;
	var h=CRM.height/rows-5*2-17;
	$('div.c-heights',table).css({
		height:	h
	});
};
</script>
{/strip}