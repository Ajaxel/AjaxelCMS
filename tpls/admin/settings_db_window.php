<?php

/**
* Ajaxel CMS v8.0
* http://ajaxel.com
* =================
* 
* Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* 
* The software, this file and its contents are subject to the Ajaxel CMS
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the source code of Ajaxel CMS.
* 
* @file       tpls/admin/settings_db_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = 'Table `'.$this->id.'` on `'.$this->db_name.'` using \''.DB_HOST.'\'';
$this->title = $title;
$this->width = 820;
$tab_height = 600;
?><script type="text/javascript">
<?php echo Index::CDA?>
var window_<?php echo $this->name_id?> = {
	opts:{Type:{VARCHAR:'red', INT:'blue',  TEXT:'#EA4DBB', TINYINT:'blue', SMALLINT:'blue', MEDIUMINT:'blue', BIGINT:'blue', FLOAT:'navy', DOUBLE:'navy', DECIMAL:'navy', DATETIME:'purple', DATE:'purple', TIMESTAMP:'purple', TIME:'purple', YEAR:'purple', ENUM:'#993300', SET:'#993300', CHAR:'red', BLOB:'green', TINYBLOB:'green', MEDIUMBLOB:'green', LONGBLOB:'green', TINYTEXT:'#EA4DBB', MEDIUMTEXT:'#EA4DBB', LONGTEXT:'#EA4DBB', BINARY:'#CC9900', VARBINARY:'#CC9900'},Attr:['','UNSIGNED','UNSIGNED ZEROFILL'],Null:['YES','NO'],Extra:['','auto_increment'], sort:['ASC','DESC']}
	,hide_length:<?php echo json($this->params['no_length_fields'])?>
	,charsets:<?php echo json($this->params['charsets'])?>
	,field:'',fields:0,prevField:'',id_col:'',index:0,index2:300
	,max:100
	,select:function(name,sel,w, attr){
		var h = '<select class="a-select"'+(w?' style="width:'+w+'px"':'')+' name="data['+this.field+']['+name+']"'+(attr?' '+attr:'')+'>';
		if ($.isArray(this.opts[name])) {
			for (i in this.opts[name]) {
				h += '<option value="'+this.opts[name][i]+'"'+(this.opts[name][i]==sel?' selected="selected"':' style="color:#666"')+'>'+this.opts[name][i]+'</option>';
			}
		} else {
			for (i in this.opts[name]) {
				h += '<option value="'+i+'"'+(i==sel?' selected="selected"':' style="color:'+this.opts.Type[i]+'"')+'>'+i+'</option>';	
			}
		}
		return h+'</select>';
	}
	,check:function(o){
		if (o.checked) {
			$('#a_structure_<?php echo $this->name_id?> .a-db_chk').attr('checked','checked');
		} else {
			$('#a_structure_<?php echo $this->name_id?> .a-db_chk:checked').removeAttr('checked');
		}
	}
	,btn:false,li:false
	,start:function(btn){
		this.btn=$(btn);
		S.G.loader=false;
		this.li=$('<li style="float:right">').appendTo($('#a-tabs_<?php echo $this->name_id?> ul.a-tabs_ul')).html('<div class="a-l"><img src="'+S.C.FTP_EXT+'tpls/img/loading/loading6.gif" /></div>');
		$('#a-form_<?php echo $this->name_id?> .a-button').button('disable');	
	}
	,end:function(){
		$('#a-form_<?php echo $this->name_id?> .a-button').button('enable');
		this.li.remove();
	}
	,save:function(btn){
		$('#a-action_<?php echo $this->name_id?>').val('save');
		this.start(btn);
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',$('#a-form_<?php echo $this->name_id?>').serialize(), function(data){
			window_<?php echo $this->name_id?>.end();
			if (data.close) {
				S.A.W.close(S.A.W.win_id);
			}
			data.close=false;
			S.A.W.dialog(data);
			if (!data.error) {
				S.A.M.edit('<?php echo $this->id?>&db_name=<?php echo $this->db_name?>');
			}
		});
	}
	,act:function(action, field, btn) {
		var cm = true, o = $('#a_structure_<?php echo $this->name_id?> .a-db_chk:checked'), l = o.length, fields=[];
		if (action=='table') {
			fields=field;	
		}
		else if (action=='drop_primary' || action=='drop_index') {
			fields=field;
			cm = confirm('Do you really want to :\nALTER TABLE `<?php echo $this->id?>` DROP'+(action=='drop_primary' ? ' PRIMARY KEY':' INDEX(`'+field+'`)'));
		}
		else if (action=='truncate') {
			cm = confirm('Do you really want to :\nTRUNCATE TABLE `<?php echo $this->id?>`');
		}
		else if (action=='rename') {
			fields=field;
			cm = confirm('Do you really want to :\nALTER TABLE `<?php echo $this->id?>` RENAME `'+fields+'`');
		}
		else if (action=='tabletype') {
			fields=field;
			cm = confirm('Do you really want to :\nALTER TABLE `<?php echo $this->id?>` TYPE '+fields+'');
		}
		else if (action=='orderby') {
			fields=field;
			var ex=field.split(':');
			cm = confirm('Do you really want to :\nALTER TABLE `<?php echo $this->id?>` ORDER BY `'+ex[0]+'` '+ex[1]);
		}
		else {
			o.each(function(){
				fields.push($(this).data('field'));
			});
			if (l>1 && action=='primary') return alert('Csnnot be 2 or more primaries');
			if (l==0) return false;
			if (action=='drop') {
				cm = confirm('Are you sure to drop `'+(l==1?o.data('field')+'` column?':l+' columns'));
			}
			if (action=='collate') {
				fields={
					columns:fields,
					charset: $('#charset_<?php echo $this->name_id?>').val()
				}
				cm = confirm('Do you really want to '+fields.charset+' '+l+' column'+(l>1?'s':'')+'?');
			}
		}
		if (cm) {
			this.start(btn);
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',{
				get: 'action',
				a:action,
				id: '<?php echo $this->id?>',
				data: fields,
				db_name: '<?php echo $this->db_name?>'
			}, function(data){
				window_<?php echo $this->name_id?>.end();
				if (data.close) {
					S.A.W.close(S.A.W.win_id);
				}
				S.A.W.dialog(data);
				if (!data.error) {
					S.A.M.edit('<?php echo $this->id?>&db_name=<?php echo $this->db_name?>');
				}
			});
		}
	}
	,run:function(btn){
		var c=$('#a-sqlcode_<?php echo $this->name_id?>').text();
		this.start(btn);
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',{
			get: 'action',
			a:'run',
			id: '<?php echo $this->id?>',
			data: c,
			db_name: '<?php echo $this->db_name?>'
		}, function(data){
			window_<?php echo $this->name_id?>.end();
			S.G.msg(data);
			if (data.error) {
				S.G.html($('#a-data_<?php echo $this->name_id?>'),'<div style="text-align:left">'+data.text+'</div>');
			} else {
				S.G.html($('#a-data_<?php echo $this->name_id?>'),window_<?php echo $this->name_id?>.put_data(data.result));
			}
		});
			
	}
	
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.W.tabs('<?php echo $this->name_id?>');
		var data=<?php echo $this->json_data?>;
		this.structure(data.structure,data.keys);
		
		if (!data.data.new) {
			this.data(data.data);
			S.A.F.options($('#a-tabletype_<?php echo $this->name_id?>'), <?php echo json($this->params['types'])?>, data.data.status.Engine);
			var fields=[];
			for (i in data.structure) fields.push(data.structure[i].Field);
			S.A.F.options($('#a-orderby_<?php echo $this->name_id?>'), fields);
			S.A.F.options($('#a-orderby_sort_<?php echo $this->name_id?>'), this.opts.sort);
		}
		
		$('#a-db_field_<?php echo $this->name_id?>').focus();

	}
	,visible_length:function(type, change) {
		if (this.hide_length.indexOf(type)>-1) {
			if (change) {
				change.hide();
			} else {
				return 'display:none;';
			}
		} else {
			if (change) {
				if ($.inArray(['255','50','8,2','15','22','8','10','2','4','\'0\',\'1\'','\'0\',\'1\',\'2\''],change.val())) {
					var v = '';
					switch (type) {
						case 'VARCHAR':
							v = '50';
						break;
						case 'FLOAT':
						case 'DOUBLE':
						case 'DECIMAL':
							v = '8,2';
						break;
						case 'MEDIUMINT':
							v = '15';
						break;
						case 'BIGINT':
							v = '22';
						break;
						case 'INT':
							v = '10';
						break;
						case 'TINYINT':
						case 'CHAR':
							v = '2';
						break;
						case 'SMALLINT':
							v = '4';
						break;
						case 'ENUM':
							v = '\'0\',\'1\'';
						break;
						case 'SET':
							v = '\'0\',\'1\',\'2\'';
						break;
					}
					if (v) change.val(v);
				}
				change.show().focus().val(change.val()).focus();
			} else {
				return '';	
			}
		}
	}
	,add:function(){
		var num = parseInt($('#a-fields_<?php echo $this->name_id?>').val());
		if (isNaN(num) || num<1 || num>100) return alert('Field`s number has to be a number');
		var to=$('#a-after_<?php echo $this->name_id?>').find('input:checked').val();
		var tb=$('#a-structure_<?php echo $this->name_id?>');
		var a={
			Type: 'VARCHAR',
			Length: '50',
			Attr: '',
			Null: 'YES',
			Default: '',
			Extra: ''
		};
		var i=0;
		if (to=='begin') {
			for (i=0;i<num;i++)	{
				a.Field=this.fields++;
				tb.prepend(this.append(a, true));
			}
		}
		else if (!to || to=='end') {
			for (i=0;i<num;i++)	{
				a.Field=this.fields++;
				tb.append(this.append(a, true));
			}
		}
		else if (to=='after') {
			var after = $('#a-db_table_<?php echo $this->name_id?>_'+$('#a-afterfield_<?php echo $this->name_id?>').val());
			for (i=0;i<num;i++)	{
				a.Field=this.fields++;
				after.after(this.append(a, true));
			}
		} else {
			alert('No such position: `'+to+'`');
		}
		$('#a-db_table_<?php echo $this->name_id?>_'+this.prevField).find('input[type="text"]:first').focus();
	}
	,append:function(a, add) {
		this.field=a.Field;
		this.index++;this.index2++;
		var o=$('<tr>').addClass('a-hov').attr('id','a-db_table_<?php echo $this->name_id?>_'+this.field), h = '';
		if (!isNaN(a.Field)) a.Field = '';
		<?php if (!$this->options['new']):?>
		h += '<td class="a-l">'+(!add?'<input type="checkbox" name="data[structure]['+this.field+']" data-field="'+this.field+'" class="a-db_chk" value="Y" />':'&nbsp;')+'</td>';
		var w1=150,w2=110,w3=140;
		<?php else:?>
		var w1=150,w2=80,w3=100;
		<?php endif;?>
		h += '<td class="a-r"'+(a.Comment?' title="'+a.Comment+'" style="background:#AFC4F3"':'')+'><input type="text" tabindex="'+this.index+'" style="width:'+w1+'px" class="a-input"'+(this.index==1?' id="a-db_field_<?php echo $this->name_id?>"':'')+' name="data['+this.field+'][Field]" value="'+a.Field+'" /></td>';
		h += '<td class="a-r"'+(a.Collation?' title="'+a.Collation+'" style="background:#AFC4F3"':'')+'>'+this.select('Type',a.Type,0,' tabindex="'+this.index2+'" onchange="window_<?php echo $this->name_id?>.visible_length(this.value, $(this).parent().next().children())"')+'</td>';
		h += '<td class="a-r"><input type="text" class="a-input" onfocus="this.select()" tabindex="'+this.index2+'" style="width:'+w2+'px;'+this.visible_length(a.Type,false)+'" name="data['+this.field+'][Length]" value="'+S.A.P.js2(a.Length)+'" /></td>';
		h += '<td class="a-r">'+this.select('Attr',a.Attr,w3,' tabindex="'+this.index2+'"')+'</td>';
		h += '<td class="a-r">'+this.select('Null',a.Null,'',' tabindex="'+this.index2+'"')+'</td>';
		h += '<td class="a-r"><input type="text" class="a-input" name="data['+this.field+'][Default]" tabindex="'+this.index2+'" onkeyup="if(this.value) $(this).parent().prev().find(\'select\').val(\'NO\')" style="width:60px" value="'+S.A.P.js2(a.Default)+'" /></td>';
		h += '<td class="a-r">'+this.select('Extra',a.Extra,40,'<?php if (!$this->options['new']):?> onchange="if(this.value==\'auto_increment\') $(\'#a-primary_field_<?php echo $this->name_id?>\').attr(\'checked\',\'checked\')"<?php endif;?>')+'</td>';
		<?php if ($this->options['new']):?>
		h += '<td class="a-l a-np"><input type="radio" name="data['+this.field+'][func]" id="a-primary_field_<?php echo $this->name_id?>" value="primary" /></td>';
		h += '<td class="a-l a-np"><input type="radio" name="data['+this.field+'][func]" value="index" /></td>';
		h += '<td class="a-l a-np"><input type="radio" name="data['+this.field+'][func]" value="unique" /></td>';
		h += '<td class="a-l a-np"><input type="radio" name="data['+this.field+'][func]" value="" checked="checked" /></td>';
		h += '<td class="a-l a-np"><input type="checkbox" name="data['+this.field+'][fulltext]" value="1" /></td>';
		<?php endif;?>
		o.html(h);
		this.prevField = this.field;
		return o;
	}
	,structure:function(data,keys){
		var h = '<table class="a-form" cellspacing="0">',a,j=1;
		h += '<tr><?php if (!$this->options['new']):?><th width="1%">&nbsp;</th><?php endif;?><th><?php echo lang('$Field')?></th><th><?php echo lang('$Type')?></th><th><?php echo lang('$Length / Values')?></th><th><?php echo lang('$Attributes')?></th><th><?php echo lang('$Null')?></th><th><?php echo lang('$Default')?></th><th><?php echo lang('$Extra')?></th>';
		<?php if ($this->options['new']):?>
		h += '<th class="a-c a-np"><img src="/tpls/img/misc/db_primary.png" alt="Primary" ttile="Primary" /></th>';
		h += '<th class="a-c a-np"><img src="/tpls/img/misc/db_index.png" alt="Index" title="Index" /></th>';
		h += '<th class="a-c a-np"><img src="/tpls/img/misc/db_unique.png" alt="Unique" title="Unique" /></th>';
		h += '<th class="a-c a-np">---</th>';
		h += '<th class="a-c a-np"><img src="/tpls/img/misc/db_ftext.png" alt="Fulltext" title="Fulltext" /></th>';
		<?php endif;?>
		h += '</tr><tbody id="a-structure_<?php echo $this->name_id?>">';
		
		h += '</tbody></table>';
		h += '<table class="a-form" cellspacing="0"><tr><td colspan="8" class="a-c"><button type="button" class="a-button" onclick="window_<?php echo $this->name_id?>.save(this)"><?php echo lang('$Save')?></button></td></tr>';
		h += '<tr><td colspan="8" class="a-m<?php echo $this->ui['a-m']?>"><?php echo lang('$Actions')?></td></tr>';
		<?php if (!$this->options['new']):?>
		h += '<tr><td colspan="8" class="a-r">';
		h += '<label><input type="checkbox" onclick="window_<?php echo $this->name_id?>.check(this)" style="position:relative;top:2px;"> <?php echo lang('$With selected')?></label>:';
		var buttons = {
			drop: ['<?php echo lang('$Drop')?>'],
			primary: ['<?php echo lang('$Primary')?>'],
			index: ['<?php echo lang('$Index')?>'],
			unique: ['<?php echo lang('$Unique')?>'],
			fulltext: ['<?php echo lang('$Fulltext')?>'],
		}
		for (i in buttons) {
			h += ' <button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act(\''+i+'\',\'\',this)">'+buttons[i][0]+'</button>';
		}
		h += ' <span style="float:right"><?php echo lang('$Charset')?>: <select class="a-select" style="width:120px" id="charset_<?php echo $this->name_id?>"></select> <button type="button" class="a-button a-button_b" onclick="if (!$(this).prev().val()) return $(this).prev().focus(); window_<?php echo $this->name_id?>.act(\'collate\',\'\',this)"><?php echo lang('$Set')?></button></span></td></tr>';
		<?php endif;?>
		var s = '<select id="a-afterfield_<?php echo $this->name_id?>" class="a-select" onclick="$(\'#after_radio_<?php echo $this->name_id?>\').attr(\'checked\',\'checked\')">';
		for (i in data) {
			s += '<option value="'+data[i].Field+'">'+data[i].Field+'</option>';	
		}
		s += '<select>';
		h += '<tr><td class="a-r" colspan="8"><?php echo lang('$Add:')?> <input type="text" style="width:40px;text-align:left;" class="a-input" onfocus="this.select()" id="a-fields_<?php echo $this->name_id?>" value="1" /> <?php echo lang('$field(s)')?> ';
		<?php if (!$this->options['new']):?>
		h += '<span id="a-after_<?php echo $this->name_id?>"><label><input type="radio" style="position:relative;top:2px;" name="after" value="end" checked="checked"> <?php echo lang('$At end of table')?></label> <label><input type="radio" style="position:relative;top:2px;" name="after" value="begin"> <?php echo lang('$At beginning of table')?></label> <label><input type="radio" style="position:relative;top:2px;" name="after" id="after_radio_<?php echo $this->name_id?>" value="after"> <?php echo lang('$After')?></label></span> '+s;
		<?php endif;?>
		h += ' <?php $this->inc('button',array('class'=>'a-button a-button_b" style="float:none;', 'click'=>'window_'.$this->name_id.'.add(this.form)','text'=>lang('$Go'))); ?></td></tr>';
		<?php if (!$this->options['new']):?>
		h += '<tr><td colspan="8" class="a-m<?php echo $this->ui['a-m']?>"><?php echo lang('$Indexes')?></td></tr>';
		h += '<tr><td colspan="8"><table class="a-form" cellspacing="0">';
		if (keys) {
			for (i in keys) {
				a=keys[i];
				if (i==0) {
					h += '<tr>';
					for (k in a) h += '<th>'+k+'</th>';
					h += '<th>&nbsp;</th>';
					h += '</tr>';
				}
				h += '<tr>';
				for (k in a) h += '<td class="a-r">'+a[k]+'</td>';
				h += '<td class="a-l a-action_buttons"><a href="javascript:;" onclick="window_<?php echo $this->name_id?>.act(\'drop_'+(a.Keyname=='PRIMARY'?'primary':'index')+'\',\''+a.Keyname+'\',this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" alt="" /></a></td>';
				h += '</tr>';
			}
		}
		h += '</table></td></tr>';
		<?php endif;?>
		h += '</table>';
		S.G.html('a_structure_<?php echo $this->name_id?>',h);
		S.A.F.options($('#charset_<?php echo $this->name_id?>'),this.charsets);
		for (i in data) $('#a-structure_<?php echo $this->name_id?>').append(this.append(data[i],false));
	}
	,idcol:function(name, a) {
		var e=name.split('-'),r=[];
		for (i in e) {
			r.push(a[e[i]]);
		}
		return r.join('||');
	}
	,put_data:function(data) {
		var h = '<table class="a-form" cellspacing="0">';
		for (i in data) {
			a=data[i];
			if (i==0) {
				h += '<tr>';
				h += '<th width="1%">&nbsp;</th>';
				for (j in a) h += '<th>'+j+'</th>';
				h += '</tr>';
			}
			h += '<tr>';
			h += '<td class="a-l" style="padding:1px!important"></td>';
			for (j in a) {
				h += '<td class="a-r">'+(isNaN(a[j])?a[j].substr(0,this.max)+(a[j].length>this.max?'...':''):a[j])+'</td>';
			}
			h += '</tr>';
		}
		h += '</table>';
		return h;	
	}
	,data:function(data){
		this.id_col=data.set.id_col;
		var h = '<textarea class="a-textarea a-textarea_code" id="a-sqlcode_<?php echo $this->name_id?>" style="width:99%;overflow:auto;height:50px">'+data.sql+'</textarea>',a;
		h += '<div style="text-align:center;padding:4px"><button type="button" onclick="window_<?php echo $this->name_id?>.run(this)" class="a-button"><?php echo lang('$Run')?></button></div>';
		h += '<div style="width:100%;overflow:auto;height:<?php echo $tab_height-90?>px" id="a-data_<?php echo $this->name_id?>">';
		h += this.put_data(data.list);
		h += '</div>';
		$('#a_data_<?php echo $this->name_id?>').html(h);
	}
};
<?php $this->inc('js_pop')?>
<?php echo Index::CDZ?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_structure_<?php echo $this->name_id?>"><?php echo lang('$Structure')?></a></li>
			<?php if (!$this->options['new']):?>
			<li><a href="#a_data_<?php echo $this->name_id?>"><?php echo lang('$Browse')?></a></li>
			<li><a href="#a_operations_<?php echo $this->name_id?>"><?php echo lang('$Operations')?></a></li>
			<?php endif;?>
		</ul>
		<div id="a_structure_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab"></div>
		<?php if (!$this->options['new']):?>
		<div id="a_data_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab"></div>
		<div id="a_operations_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="20%"><?php echo lang('$Alter table order by:')?></td><td class="a-r" width="85%"><select class="a-select" id="a-orderby_<?php echo $this->name_id?>"></select> <select class="a-select" id="a-orderby_sort_<?php echo $this->name_id?>"></select> <button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('orderby', $(this).prev().prev().val()+':'+$(this).prev().val(), this)"><?php echo lang('$Go')?></button></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Rename to:')?></td><td class="a-r" width="85%"><input type="text" class="a-input" id="a-rename_<?php echo $this->name_id?>" value="<?php echo $this->id?>" style="width:40%" /> <button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('rename', $(this).prev().val(), this)"><?php echo lang('$Go')?></button></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Table type:')?></td><td class="a-r" width="85%"><select class="a-select" id="a-tabletype_<?php echo $this->name_id?>"></select> <button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('tabletype', $(this).prev().val(), this)"><?php echo lang('$Go')?></button></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Truncate:')?></td><td class="a-r" width="85%"><button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('truncate','',this)"><?php echo lang('$Go')?></button> (will remove all data from table)</td>
				</tr>
				<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l">
						<button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('table', 'check', this)"><?php echo lang('$Check table')?></button> 
						<button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('table', 'analyze', this)"><?php echo lang('$Analyze table')?></button>
						<button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('table', 'repair', this)"><?php echo lang('$Repair table')?></button>
						<button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('table', 'optimize', this)"><?php echo lang('$Optimize table')?></button>
						<button type="button" class="a-button a-button_b" onclick="window_<?php echo $this->name_id?>.act('table', 'flush', this)"><?php echo lang('$Renew table cache (FLUSH)')?></button>
						
					</td>
				</tr>
			</table>
		</div>
		<?php endif;?>
	</div>
	<input type="hidden" name="db_name" value="<?php echo $this->db_name?>" />
	<input type="hidden" name="id" value="<?php echo $this->id?>" />
	<input type="hidden" name="<?php echo self::KEY_ACTION?>" value=""  id="a-action_<?php echo $this->name_id?>" />
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>