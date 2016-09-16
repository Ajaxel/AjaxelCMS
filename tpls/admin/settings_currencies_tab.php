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
* @file       tpls/admin/settings_currencies_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, i = 0;
	var html = '<table class="a-list a-list-one" cellpadding="0" cellspacing="0">';
	html += '<tr><th width="10%"><?php echo lang('$ISO code')?></th><th width="10%">BID</th><th width="10%">ASK</th><th width="51%"><?php echo lang('$Name')?></th><th width="15%"><?php echo lang('$Format')?></th><th width="4%">&nbsp;</th></tr>';
	html += '<tr class="'+(i%2?'':'a-odd')+' a-sortable">';
	html += '<td class="a-l" style="font-weight:bold;text-align:center"><input type="text" style="width:98%" name="data[new][9]" class="a-input" value="" /></td>';
	html += '<td class="a-l"><input type="text" style="width:98%" name="data[new][0]" class="a-input" value="" /></td>';
	html += '<td class="a-l"><input type="text" style="width:98%" name="data[new][4]" class="a-input" value="" /></td>';
	html += '<td class="a-l"><input type="text" style="width:98%" name="data[new][1]" class="a-input" value="" /></td>';
	html += '<td class="a-l"><input type="text" style="width:98%" name="data[new][2]" class="a-input" value="" /></td>';
	html += '<td class="a-r">&nbsp;</td>';
	html += '</tr>';
	html += '<tbody class="a-sortable_<?php echo $this->name?> a-flat_inputs">';
	for(l in data) {
		a = data[l];
		html += '<tr id="a-c-<?php echo $this->name?>-'+l+'" class="'+(i%2?'':'a-odd')+' a-sortable">';
		html += '<td class="a-l" style="font-weight:bold;text-align:center">'+l.toUpperCase()+'</td>';
		html += '<td class="a-l"><input type="text" style="width:98%" class="a-d-<?php echo $this->table?>-'+l+'" name="data['+l+'][0]" value="'+a[0]+'" /></td>';
		html += '<td class="a-l"><input type="text" style="width:98%" class="a-d-<?php echo $this->table?>-'+l+'" name="data['+l+'][4]" value="'+(a[4] ? a[4] : a[0])+'" /></td>';
		html += '<td class="a-l"><input type="text" style="width:98%" class="a-d-<?php echo $this->table?>-'+l+'" name="data['+l+'][1]" value="'+a[1]+'" /></td>';
		html += '<td class="a-l"><input type="text" style="width:98%" class="a-d-<?php echo $this->table?>-'+l+'" name="data['+l+'][2]" value="'+a[2]+'" /></td>';
		html += '<td class="a-r a-action_buttons">'+('<?php echo $this->current['DEFAULT_CURRENCY']?>'==l?'<img src="<?php echo FTP_EXT?>tpls/img/1x1.gif" width="16" />':'<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', {id:\''+l+'\', active:'+(a[3]==1)+', title: \''+a[0]+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a[3]==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a[3]==1?'red':'green')+'.png" /></a><a href="javascript:;" onclick="if(confirm(\'<?php echo lang('$Are you completely sure to remove this currency:')?> '+a[1]+'?\\n<?php echo lang('$WARNING, database data related to this currency will be updated')?>\')){$(\'#a_set_<?php echo $this->tab?>_uninstall\').val(\''+l+'\');$(\'#a_set_<?php echo $this->tab?>_a\').val(\'uninstall\');$(\'#<?php echo $this->name?>-form_<?php echo $this->tab?>\').submit()}"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>')+'</td>';
		html += '</tr>';
		i++;
	}
	html += '</tbody></table>';
	$('#<?php echo $this->name?>-content').html(html);
	S.A.L.sortable('<?php echo $this->name?>','<?php echo $this->tab?>');
	S.A.L.ready();
});
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<a href="javascript:;" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&do=update_rates',false,'<?php echo $this->tab?>');"><?php echo lang('$Update rates')?></a>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Save'))); ?>
</div>
<input type="hidden" name="data[uninstall]" id="a_set_<?php echo $this->tab?>_uninstall" value="" />
<input type="hidden" name="<?php echo self::KEY_ACTION?>" id="a_set_<?php echo $this->tab?>_a" value="" />
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>