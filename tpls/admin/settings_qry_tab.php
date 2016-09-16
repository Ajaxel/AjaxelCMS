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
* @file       tpls/admin/settings_qry_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
S.A.L.setQrys={};
S.A.L.setQryBody=function(id) {
	$('html,body').animate({
		scrollTop: 0
	},'fast', function(){
		$('#a_set_<?php echo $this->tab?>_body').val(S.A.L.setQrys[id]).focus();	
	});
}
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	var html = '<table class="a-list a-list-one" cellspacing="0">';	
	html += '<tr><th width="5%"><?php echo lang('$Date')?></th><th><?php echo lang('$Query')?></th><th width="2%">&nbsp;</th><th width="4%">&nbsp;</th></tr>';
	for(i=0;i<data.length;i++) {
		a = data[i];
		S.A.L.setQrys[a.id]=a.sql;
		html += '<tr class="'+(i%2?'':'a-odd')+' a-hov">';
		html += '<td class="a-l"><span class="a-date">'+a.added+'</span></td>';
		html += '<td class="a-l" style="font-family:\'Courier New\';font-size:12px">'+a.title+'</td>';
		html += '<td class="a-l a-c">'+(a.userid>0?'<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\')" title="'+a.user+'">'+a.userid+'</a>':'&nbsp;')+'</td>';
		html += '<td class="a-r a-action_buttons">';
		html += '<a href="javascript:;" onclick="S.A.L.setQryBody('+a.id+');"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-paste.png" title="<?php echo lang('$Paste this SQL query to run into textarea')?>" /></a>';
		html += '</td>';
		html += '</tr>';
	}
	html += '</table>';
	if (!a) html = ' ';
	S.A.L.ready(html);
});
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div class="a-search">
	<div class="a-l">
	<?php if (count($this->params['db']['databases'])>1):?>
	<?php echo lang('$Database:');?> <select name="db_name" onchange="S.A.L.get('?<?php echo $this->url?>?>&db_name='+this.value, false, '<?php echo $this->tab?>')">
		<?php echo Html::buildOptions($this->db_name, $this->params['db']['databases'],true)?>
	</select>
	<?php endif;?>
	<?php
	if ($this->db_name!=DB_NAME) mysqli_select_db(DB::link(), $this->db_name);
	$tables = DB::tables(false, true);
	if ($this->db_name!=DB_NAME) mysqli_select_db(DB::link(), DB_NAME);
	?>
	<?php echo lang('$Tables')?>: <select name="data[table]" id="a_set_<?php echo $this->tab?>_table" class="a-select" onchange="S.A.S.columns('a_set_<?php echo $this->tab?>_cols',this.value,'<?php echo $this->tab?>')"><option value=""></option><?php echo Html::buildOptions($this->data['table'],$tables, true) ?></select>
	<?php echo lang('$Columns')?>: <select name="data[col]" class="a-select" onchange="$('#a_set_<?php echo $this->tab?>_body').insertAtCaret($('#a_set_<?php echo $this->tab?>_table').val()+'.'+this.value+', ');this.value=''" id="a_set_<?php echo $this->tab?>_cols"><option value=""></option><?php echo Html::buildOptions(false,DB::columns(str_replace($this->prefix,'',$this->data['table']?$this->data['table']:$tables[0])), true) ?></select>
	<button type="button" class="a-button" onclick="$('#a_set_<?php echo $this->tab?>_a').val('export_table');$('#<?php echo $this->name?>-form_<?php echo $this->tab?>').submit()"><?php echo lang('$print table')?></button>
	<?php if($this->data):?><button type="button" class="a-button" onclick="$('#a_set_<?php echo $this->tab?>_a').val('clear');$('#<?php echo $this->name?>-form_<?php echo $this->tab?>').submit()"><?php echo lang('$reset')?></button><?php endif;?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<?php 
if ($this->output):?>
<div class="a-search" style="height:auto;">
	<?php echo $this->output?>
</div>
<?php endif;?>
<textarea class="a-textarea a-textarea_code" spellcheck="false" autocorrect="off" autocapitalize="off" name="data[qry]" id="a_set_<?php echo $this->tab?>_body" style="width:99%;height:300px;"><?php echo strform(isset($this->data['qry']) ? $this->data['qry'] : '')?></textarea>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'if(confirm(\''.lang('$Are you sure to run this query?').'\\n\\n\'+$(\'#a_set_qry_body\').val().substr(0,300))){$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()}','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Execute'))); ?>
	<div class="a-r">
	<button type="button" class="a-button a-button_x" onclick="S.A.L.excel($('#a_set_<?php echo $this->tab?>_body').val(),'SQL','<?php echo strjava(sql($this->data['qry'],false))?>','<?php echo $this->db_name?>')"><?php echo lang('$export..')?></button>
	</div>
</div>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<input type="hidden" name="<?php echo self::KEY_ACTION?>" id="a_set_<?php echo $this->tab?>_a" value="qry">
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>