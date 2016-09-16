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
* @file       tpls/admin/email_db_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
S.A.M.importEmails=function(data){
	S.A.M.dialog({
		title: data.title,
		descr: data.descr,
		percent: data.percent
	},function(){
		S.A.M.close();
		var url = S.C.HTTP_EXT+'?json&<?php echo URL_KEY_ADMIN?>=email&tab=db';
		S.G.loader=false;
		$.ajax({
			cache: false,
			dataType: 'json',
			url: url,
			type: 'POST',
			data: {
				get: 'import',
				aa: 'dd',
				group: $('#data_group_<?php echo $this->name_id?>').val(),
				language: $('#data_lang_<?php echo $this->name_id?>').val()
			},
			success: function(data) {
				S.A.M.importEmails_msg(data);
			}
		});
	});
}
S.A.M.importEmails_next=function(data){
	if (data.title) S.A.M.email_div.find('.a-title').html(data.title);
	if (data.descr) S.A.M.email_div.find('.a-descr').html(data.descr);
	S.A.M.email_div.find('.a-progress').css({width: data.percent+'%'});
	var url = S.C.HTTP_EXT+'?json&<?php echo URL_KEY_ADMIN?>=email&tab=db';
	S.G.loader=false;
	$.ajax({
		cache: false,
		dataType: 'json',
		url: url,
		type: 'POST',
		data: {
			get: 'import'
		},
		success: function(data) {
			S.A.M.importEmails_msg(data);
		}
	})
}
S.A.M.importEmails_msg=function(data){
	if (data.error) {
		S.G.msg({text:data.error,type:'stop',delay:6000});
		if (S.A.M.email_div) {
			S.A.M.email_div.remove();
			S.A.M.email_div=false;
		}
		S.G.hideLoader();
	}
	else if (data.finished) {
		if (data.text) S.G.msg({text:data.text,type:'tick',delay:6000});
		S.A.L.tab.tabs('load',S.A.L.tab_index);
		S.A.M.close();
		if (S.A.M.email_div) {
			S.A.M.email_div.remove();
			S.A.M.email_div=false;
		}
		S.G.hideLoader();
		S.A.W.close();
	} else {
		S.A.M.importEmails_next(data);
	}	
}
S.A.M.cancelImport=function(){
	if(confirm('<?php echo strjs(lang('_$Are you sure to cancel the interruption?'))?>')) {
		S.G.json('?<?php echo URL_KEY_ADMIN?>=email&clean=email_import',{},function(){
			S.A.L.tab.tabs('load',S.A.L.tab_index);
		});
	}
}
<?php echo Index::CDZ?>
</script>
<?php 
$set = Cache::getSmall('email_import');
if ($set && $set['size']):?>
<script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	S.A.L.ready();
});
</script>
<div class="ui-content" style="text-align:center;">
	<table cellspacing="0" width="100%" class="a-list a-list-one">
	<tr><td class="a-fl a-c" style="padding:40px 0;"><button type="button" class="a-button" onclick="S.A.M.importEmails({title:'<?php echo lang('_$Preparing continuation importing emails')?>',descr:'<?php echo lang('_$Please wait..')?>',percent:0})"><?php echo lang('$Email import was interrupted at %1, click here to resume..',round($set['tell'] / $set['size'] * 100).'%')?></button></td></tr>
	<tr><td class="a-search a-c" style="padding:10px 0"><button type="button" class="a-button" onclick="S.A.M.cancelImport();"><?php echo lang('$Cancel')?></button></td></tr>
	</table>
</div>
<?php else:?>
<script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, langs = <?php echo json(array_label($this->langs,'[[:KEY:]]')); ?>;
	var html = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0"><tr><th>&nbsp;</th><th title="Unsubscribe">us</th><th width="50%"><?php echo lang('$Email')?></th><th width="1%" title="<?php echo lang('$Number of times sent')?>">C</th><th width="8%" title="<?php echo lang('$Language')?>">L</th><th width="18%"><?php echo lang('$Name')?></th><th width="22%"><?php echo lang('$Group')?></th><th width="2%"><a href="javascript:;" onclick="if(!S.A.L.temp.ch){$(\'.a-check_email\').attr(\'checked\',\'checked\');S.A.L.temp.ch=true;$(\'.a-s-all\').attr(\'disabled\',\'disabled\');}else{$(\'.a-check_email\').removeAttr(\'checked\');S.A.L.temp.ch=false;$(\'.a-s-all\').removeAttr(\'disabled\');}"><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" alt="""></a></th></tr>';
	var x=2;
	for (i=1;i<3;i++) {
		html += '<tr class="'+(x%2?'a-new':'a-new_odd')+'">';
		html += '<td class="a-fr" colspan="2">&nbsp;</td>';
		html += '<td class="a-fl"><input type="text" tabindex="'+x+'" name="data[old_email][new]['+i+']" style="width:98%;" /></td>';
		html += '<td class="a-fr">&nbsp;</td>';
		html += '<td class="a-fl"><select name="data[lang][new]['+i+']" style="font-size:11px;width:42px;border:none;background:transparent" class="a-select">';
		for (l in langs) html += '<option value="'+l+'">'+langs[l].toLowerCase()+'</option>';
		html += '</select></td>';
		x++;
		html += '<td class="a-fr"><input type="text" tabindex="'+x+'" name="data[name][new]['+i+']" style="width:96%;" value="" /></td>';
		x++;
		if (i==1) {
			html += '<td class="a-fr" rowspan="2" colspan="2"><input type="text" tabindex="'+x+'" name="data[group][new]['+i+']" style="width:94%;" value="<?php echo (@$_POST['data']['group']['new'][1] ? @$_POST['data']['group']['new'][1] : $this->group);?>" /></td>';
		}
		html += '</tr>';
	}
	for (i in data) {
		a=data[i];
		html += '<tr'+(x%2?'':' class="a-odd"')+'><td class="a-fr"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=email&tab=mail&compose=true&to='+a.email+'\')"><img src="<?php echo FTP_EXT?>tpls/img/email.png" style="margin-top:3px;" /></a></td><td class="a-fr"><input type="checkbox" name="data[unsub]['+x+']" class="a-s-'+x+' a-s-all" value="1"'+(a.unsub==1?' checked="checked"':'')+' /></td><td class="a-fl"><input type="text" tabindex="'+x+'" name="data[email]['+x+']" style="width:98%;'+(a.unsub==1?'color:#999':'')+'" class="a-s-'+x+' a-s-all" value="'+a.email+'" /><input type="hidden" name="data[old_email]['+x+']" value="'+a.email+'" /><input type="hidden" name="data[old_group]['+x+']" value="'+a.group+'" /></td>';
		html +='<td class="a-fr" title="Sent: '+a.added+'. Last time sent: '+a.sent+'">'+(a.cnt ? a.cnt : '')+'</td>';
		html += '<td class="a-fl"><select name="data[lang]['+x+']" style="font-size:11px;width:42px;border:none;background:transparent" class="a-select">';
		for (l in langs) html += '<option value="'+l+'"'+(a.lang==l?' selected="selected"':'')+'>'+langs[l].toLowerCase()+'</option>';
		html += '</select></td>';
		html += '<td class="a-fr"><input type="text" tabindex="'+x+'" name="data[name]['+x+']" style="width:96%" class="a-s-'+x+' a-s-all" value="'+a.name+'" /></td><td class="a-fr"><input type="text" name="data[group]['+x+']" style="width:96%" class="a-s-'+x+' a-s-all" value="'+a.group+'" /></td><td class="a-fr"><div><input type="checkbox" class="a-checkbox a-check_email" onclick="S.A.L.sdel(\'.a-s-'+x+'\')" name="data[del]['+x+']" value="on"></div></td></tr>';
		x++;
	}
	html += '</table>';
	$('#a_email_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
		<?php $this->inc('search',array('tab'=>$this->tab))?>
		<?php $this->inc('language',array('tab'=>$this->tab,'key_lang'=>'language'))?>
		<label><input type="checkbox" class="a-checkbox"<?php echo (get('unsub')?' checked="checked"':'')?> onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=email&tab=db'+(this.checked?'&unsub=1':'')+'&group=<?php echo urlencode($this->group)?>',false,'<?php echo $this->tab?>');"><?php echo lang('$Unsubscribed')?></label>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div class="a-search">
	<div class="a-l">
		<select style="width:150px" onchange="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=email&tab=db&group='+this.value, false, 'db')">
		<option value="" style="color:#666"><?php echo lang('$-- All --')?></option>
		<?php echo Html::buildOptions($this->group, $this->array['groups'])?>
		</select>
		<button type="button" class="a-button" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&tab=db&import=true', false, this);"><?php echo lang('$Import')?></button>
		<?php if ($this->group):?>
		<button type="button" class="a-button" onclick="if (confirm('Are you sure to delete all emails under &quot;<?php echo $this->group?>&quot; group?')) S.A.L.get('?<?php echo URL_KEY_ADMIN?>=email&tab=db&do=delete&group=<?php echo urlencode($this->group)?>',false,'<?php echo $this->tab?>');"><?php echo lang('$Delete')?></button>
		<!--
		<button type="button" class="a-button" onclick="if (confirm('Are you sure to unsubscribe all emails under <?php echo $this->group?> group?')) S.A.L.get('?<?php echo URL_KEY_ADMIN?>=email&tab=db&do=unsub&group=<?php echo urlencode($this->group)?>',false,'<?php echo $this->tab?>');"><?php echo lang('$Unsub')?></button>
		<button type="button" class="a-button" onclick="if (confirm('Are you sure to subscribe all emails under <?php echo $this->group?> group?')) S.A.L.get('?<?php echo URL_KEY_ADMIN?>=email&tab=db&do=sub&group=<?php echo urlencode($this->group)?>',false,'<?php echo $this->tab?>');"><?php echo lang('$Sub')?></button>
		-->
		<button type="button" class="a-button" onclick="$(this).hide().next().show().children().get(0).focus()"><?php echo lang('$Rename')?></button>
		<span style="display:none">
			<input type="text" class="a-input" />
			<button type="button" class="a-button" onclick="var val=$(this).prev().val();if (val&&confirm('Are you sure to rename <?php echo $this->group?> group to '+val+'?')) S.A.L.get('?<?php echo URL_KEY_ADMIN?>=email&tab=db&do=rename&rename='+escape(val)+'&group=<?php echo urlencode($this->group)?>',false,'<?php echo $this->tab?>');"><?php echo lang('$Rename')?></button>
		</span>
		<?php endif;?>
	</div>
</div>
</form>
<?php endif;?>
<?php $this->inc('tab_form_top')?>
<div id="a_email_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<?php $this->inc('tab_form_bottom')?>
<?php endif;?>