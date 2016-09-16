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
* @file       tpls/admin/settings_langs_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, i=0;
	var html = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0">';
	html += '<tr><th width="10%" colspan="2" nowrap><?php echo lang('$ISO code')?></th><th width="27%"><?php echo lang('$Name')?></th><th width="40%"><?php echo lang('$Unicode name')?></th><th width="15%"><?php echo lang('$Short name')?></th><th width="8%">&nbsp;</th></tr>';
	html += '<tbody class="a-sortable_<?php echo $this->name?>">';
	for(l in data) {
		a = data[l];
		html += '<tr id="a-c-<?php echo $this->table?>-'+l+'" class="'+(i%2?'':'a-odd')+' a-sortable">';
		html += '<td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+l+'.png" /></td>';
		html += '<td class="a-l" style="font-weight:bold;text-align:center">'+l.toUpperCase()+'</td>';
		html += '<td class="a-l"><input type="text" style="width:98%" name="data['+l+'][0]" value="'+a[0]+'" /></td>';
		html += '<td class="a-l"><input type="text" style="width:98%" name="data['+l+'][1]" value="'+a[1]+'" /></td>';
		html += '<td class="a-l"><input type="text" style="width:98%" name="data['+l+'][2]" value="'+a[2]+'" /></td>';
		html += '<td class="a-r a-action_buttons">'+('<?php echo $this->current['DEFAULT_LANGUAGE']?>'==l?'<img src="<?php echo FTP_EXT?>tpls/img/1x1.gif" width="16" />':'<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', {id:\''+l+'\', active:'+(a[3]==1)+', title: \''+a[0]+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a[3]==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a[3]==1?'red':'green')+'.png" /></a><a href="javascript:;" onclick="if(confirm(\'<?php echo lang('$Are you completely sure to uninstall this language:')?> '+a[0]+'?\\n<?php echo lang('$WARNING, database data related to this language will be destroyed')?>\')){$(\'#a_set_<?php echo $this->tab?>_uninstall\').val(\''+l+'\');$(\'#a_set_<?php echo $this->tab?>_a\').val(\'uninstall\');$(\'#<?php echo $this->name?>-form_<?php echo $this->tab?>\').submit()}"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>')+'</td>';
		html += '</tr>';
		i++;
	}
	html += '</tbody></table>';
	S.A.L.ready(html);
	S.A.L.sortable();
});
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<?php if (count($this->templates)>1):?>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<?php endif;?>
<div class="a-search">
	<div class="a-l">
	<table cellspacing="0"><tr>
		<td>
			<a href="javascript:;" onclick="S.A.F.fillOptions('a_set_<?php echo $this->tab?>_languages','?<?php echo URL_KEY_ADMIN?>='+S.A.L.name+'&tab=langs',{get:'languages'}, true)"><?php echo lang('$Install new language')?>...</a>&nbsp;
		</td>
		<td>
			<select name="data[new]" id="a_set_<?php echo $this->tab?>_languages" style="display:none" onchange="if(this.value.length){$('#a_set_<?php echo $this->tab?>_start').show()}else{$('#a_set_<?php echo $this->tab?>_start').hide()}"></select>
		</td>
		<td id="a_set_<?php echo $this->tab?>_start" style="display:none">
			&nbsp; <?php echo lang('$Copy from:')?>
			&nbsp; <select name="data[from]"><option value=""><?php echo lang('$-- select --')?></option><?php echo Html::buildOptions($this->lang,array_label($this->langs));?></select>
			&nbsp; <button type="button" class="a-button" onclick="if(confirm('<?php echo lang('$Are you sure to install this language:')?> '+$('#a_set_<?php echo $this->tab?>_languages :selected').text()+'?')){$('#a_set_<?php echo $this->tab?>_a').val('install');$('#<?php echo $this->name?>-form_<?php echo $this->tab?>').submit()}"><?php echo lang('$install')?></button>
		</td>
	</tr></table>
	</div>
	<?php if (count($this->templates)==1):?>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
	<?php endif;?>
</div>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'_'.$this->tab.'-submitted\').val(1);$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Save'))); ?>
</div>
<?php if (USE_TRANSLATE && 0):?>
<div style="height:5px">&nbsp;</div>
<div class="a-h1<?php echo $this->ui['h1']?>"><div class="a-l" style="font-size:120%"><?php echo lang('$%1 Translator',ucfirst(USE_TRANSLATE))?></div></div>
<div class="a-search">
	<?php
	if (!post('translate_from')) {
		$_POST['translate_from'] = MY_LANGUAGE;
	}
	if (!post('translate_to')) {
		$_POST['translate_to'] = DEFAULT_LANGUAGE;
	}
	$_langs = Factory::call('translate', USE_TRANSLATE)->langs($this->lang, $this->langs);
	
	?>
	<?php echo lang('$Translate from:')?> <select name="translate_from">
	<?php echo Html::buildOptions(post('translate_from'),$_langs['from']);?>
	</select> <?php echo lang('$Translate to:')?> <select name="translate_to">
	<?php echo Html::buildOptions(post('translate_to'),$_langs['to']);?>
	</select>
</div>
<?php if ($this->array['translated_text']):?>
<div class="a-search" style="height:auto">
	<div style="padding-left:20px;background:#f1f1f1 url(<?php echo FTP_EXT?>tpls/img/flags/16/<?php echo post('translate_from')?>.png) left top no-repeat">
	<textarea class="a-textarea" name="translate_query" style="width:99%;height:130px;font:13px monospace"><?php echo strform(post('translate_query'));?></textarea>
	</div>
</div>
<div class="a-search" style="height:auto;">
	<div style="padding-left:20px;background:#f1f1f1 url(<?php echo FTP_EXT?>tpls/img/flags/16/<?php echo post('translate_to')?>.png) left top no-repeat">
	<textarea class="a-textarea" style="border:none;width:99%;height:160px;font:13px monospace;background:#f2f2f2;"><?php echo $this->array['translated_text'];?></textarea>
	</div>
</div>
<?php else:?>
<div class="a-search" style="height:auto">
	<textarea class="a-textarea" name="translate_query" style="width:99%;height:130px;font:13px monospace"><?php echo strform(post('translate_query'));?></textarea>
</div>
<?php endif;?>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#a_translate_'.$this->tab.'\').val(1);$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Translate'))); ?>
</div>
<input type="hidden" name="translate" id="a_translate_<?php echo $this->tab?>" value="" />
<?php endif;?>
<input type="hidden" name="data[uninstall]" id="a_set_<?php echo $this->tab?>_uninstall" value="" />
<input type="hidden" name="<?php echo self::KEY_ACTION?>" id="a_set_<?php echo $this->tab?>_a" value="" />
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>