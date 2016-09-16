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
* @file       tpls/admin/orders2.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
S.A.L.order_send=function(f, action, name) {
	var ids=[];
	$('#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>:checked').each(function(){
		ids.push($(this).val());
	});
	
	S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>',$.extend({
		get: 'action',
		a: (action ? action : 'send'),
		name: name,
		ids: ids,
	},f ? S.A.P.data($(f)) : false), true);
}
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var h = '<table class="a-list a-list-one" cellspacing="0">';	
		h += '<tr><th width="1%"><input type="checkbox" onclick="if(this.checked)$(\'#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>\').attr(\'checked\',\'checked\');else $(\'#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>\').removeAttr(\'checked\');" id="a_chk_<?php echo $this->name?>"></th><th width="2%"><?php echo lang('$ID')?></th><th width="14%"><?php echo lang('$Date')?></th><th width="36%"><?php echo lang('$Buyer')?></th><th width="20%"><?php echo lang('$Price/qty')?></th><th width="15%"><?php echo lang('$Status')?></th><th width="5%">&nbsp;</th></tr><tbody class="a-tbody">';
		for(i=0;i<data.length;i++) {
			a = data[i];
			h += '<tr class="'+(i%2?'':'a-odd')+'">';
			h += '<td class="a-l"><input type="checkbox" class="a_chk_<?php echo $this->name?>" value="'+a.id+'"></td>';
			<?php if ($this->order_status!=Site::STATUS_CREDIT):?>
			h += '<td class="a-l" style="text-align:right"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)" style="font-weight:bold">'+a.id+'.</a></td>';
			h += '<td class="a-l"><span class="a-date"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">'+a.ordered+'</a></span></td>';
			<?php else:?>
			h += '<td class="a-l" style="text-align:right" nowrap><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)" style="font-weight:bold">'+a.credit_id+' ('+a.id+')</a></td>';
			h += '<td class="a-l"><span class="a-date"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">'+a.credited+'</a></span></td>';
			<?php endif;?>
			h += '<td class="a-l"><a '+(a.userid?'href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\')"':'href="mailto:'+a.email+'"')+'" style="font-size:11px">'+a.login+'</a></td>';
			h += '<td class="a-l" nowrap>'+a.price_total+' '+a.currency+' <span style="color:#666">('+a.quantity_total+')</span></td>';
			h += '<td class="a-l" nowrap>'+a.s+'</td>';
			h += '<td class="a-r a-action_buttons" width="20%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \'this order: '+a.id+'\'}, this, false)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';			
			h += '</tr>';
		}
		h += '</tbody></table>';
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No orders were found')?></div>';
	}	
	S.A.L.ready(h);
	var btns=$('#a-buttons_<?php echo $this->name?>').hide();
	S.A.L.selectable(false,function(i){
		if (i>0) btns.slideDown('fast'); else {
			btns.slideUp();
			$('#a-maildiv_<?php echo $this->name?>').slideUp();
		}
	});
	S.A.L.chk(btns, false, function(){		
		$('#a-maildiv_'+this.name+'').slideUp();	
	});
	
	var editor={
		element: '#a-message_<?php echo $this->name?>',
		height: 400, 
		base: '<?php echo HTTP_BASE?>',
		lang: 'en',
		simple: false,
		templates: <?php echo $this->json_templates?>
	}
	S.A.W.editor(editor);
	$('#a-form_<?php echo $this->name_id?>2').submit(function(){
		return false;
	});
	
});
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<select onchange="S.A.L.get('?<?php echo URL::rq('user_groupid',$this->referer)?>&order_status='+this.value);">
		<option style="color:#666" value="<?php echo self::KEY_ALL?>"<?php echo ($this->order_status==self::KEY_ALL?' selected="selected"':'')?>><?php echo lang('$-- all statuses --')?></option>
		<?php echo Html::buildOptions($this->order_status, array_label(Conf()->g('order_statuses')),false,' selected="selected"',false,false)?>
	</select>
	<?php $this->inc('search')?>
	<?php $this->inc('user', array('table'=>$this->table))?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php 
$this->button['custom'] = '<button type="button" class="a-button a-button_a" onclick="S.A.L.order_send(false,\'my\',\'invoice\')">'.lang('$Print invoices').'</button>';
if ($this->order_status==Site::STATUS_CREDIT) {
	$this->button['custom'] .= ' <button type="button" class="a-button a-button_a" onclick="S.A.L.order_send(false,\'my\',\'credit\')">'.lang('$Print credit').'</button>';
}
$this->button['custom'] .= ' <button type="button" class="a-button a-button_a" onclick="$(\'#a-maildiv_'.$this->name.'\').slideDown(function(){$(\'#a-subject_'.$this->name.'\').focus()});S.G.scrollTo($(\'#a-maildiv_'.$this->name.'\'), 30);$(this).blur()">'.lang('$Email letter').'</button> <!--<button type="button" class="a-button a-button_a" onclick="">SMS</button>-->';
$this->inc('list')?>

</div>
<?php $this->inc('bot')?>
<div id="a-maildiv_<?php echo $this->name?>" style="display:none">
<br />
<?php 
$this->title = lang('$Send email letter');
$this->inc('top')?>
<form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>2">
<div class="a-search">
	<div class="a-l">
		<?php echo lang('$Template:')?> <select class="a-select" style="width:200px" name="mail[tpl]" onchange="S.A.W.mail_template(this.value,$('#a-subject_<?php echo $this->name?>'),$('#a-message_<?php echo $this->name?>'));">
			<option value=""></option>
			<?php echo Html::buildOptions(0,$this->output['signature']);?>
		</select>
	</div>
	<div class="a-r">
		<?php echo lang('$Variables:')?> <select class="a-select" onchange="if (this.value) $('#a-message_<?php echo $this->name?>').tinymce().execCommand('mceInsertContent',false,'{$'+this.value+'}');this.value=''">
			<option value=""></option>
			<?php echo Html::buildOptions(0,$this->output['columns'],true);?>
		</select>
	</div>
</div>
<div class="a-content" id="<?php echo $this->name?>-content2">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l"><?php echo lang('$From name:')?></td><td class="a-r"><input type="text" name="mail[from_name]" class="a-input" value="<?php echo strform(MAIL_NAME)?>" style="width:90%" /></td>
			<td class="a-l" width="15%"><?php echo lang('$From email:')?></td><td class="a-r" width="35%"><input type="text" name="mail[from_email]" class="a-input" value="<?php echo MAIL_EMAIL?>" style="width:90%" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Subject:')?></td><td class="a-r" width="85%" colspan="3"><input type="text" class="a-input" id="a-subject_<?php echo $this->name?>" style="width:99%" name="mail[subject]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-r" colspan="2"><textarea type="text" class="a-textarea" name="mail[message]" style="width:99%;height:121px;font:12px monospace" id="a-message_<?php echo $this->name?>"><?php echo $this->post('descr')?></textarea></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0">
		<tr>
			<tr>
			<td class="a-l" width="15%">
				<?php echo lang('$Attach:')?>
			</td>
			<td class="a-r" colspan="3">
				<label><input class="a-checkbox" type="checkbox" name="mail[att][invoice]"> <?php echo lang('$Invoice:')?></label> <label><input class="a-checkbox" type="checkbox" name="mail[att][welcome]"> <?php echo lang('$Welcome:')?></label>
			</td>
			<td class="a-l"><?php echo lang('$Portion:')?></td><td class="a-r"><select name="mail[portion]" class="a-select">
				<?php echo Html::buildOptions(2,Html::arrRange(1,200),true);?>
			</select> <?php echo lang('$emails per period')?></td>
			<td class="a-l" width="15%"><?php echo lang('$Portion delay:')?></td><td class="a-r"><select name="mail[delay]" class="a-select">
				<?php echo Html::buildOptions(200,array_merge(Html::arrRange(100,1000,100)+Html::arrRange(1000,10000,1000)+array(20000,30000)),true);?>
			</select> ms</td>
		</tr>
	</table>
</div>
<div class="a-buttons<?php echo $this->ui['buttons']?>" id="a-buttons2_<?php echo $this->name?>">
	<table cellspacing="0" style="width:100%"><tr>
		<td class="a-left"><label><input type="checkbox" name="mail[all]"> <?php echo lang('$Send to all %1 orders',$this->total)?></label></td>
		<td class="a-c"><?php echo $this->inc('button', array('click'=>'S.A.L.order_send(this.form)','img'=>'oxygen/16x16/actions/mail-send.png','text'=>'Send'))?></td>
		<td class="a-right" style="text-align:right"><button type="button" class="a-button a-button_a" onclick="S.A.L.order_send(this.form,'print')"><?php echo lang('$Print')?></button> <button type="button" class="a-button a-button_a" onclick="S.A.L.order_send(this.form,'save_tpl')"><?php echo lang('$Save')?></button> <button type="button" class="a-button a-button_a" onclick="if (confirm('Are you sure to delete this template?')) S.A.L.order_send(this.form,'del_tpl')"><?php echo lang('$Delete')?></button> <button type="button" class="a-button a-button_a" onclick="$('#a-maildiv_<?php echo $this->name?>').slideUp();S.G.scrollTo($('#a-buttons_<?php echo $this->name?>'))"><?php echo lang('$Cancel')?></button></td>
	</tr></table>
</div>	
</form>
<?php $this->inc('bot')?>
</div>