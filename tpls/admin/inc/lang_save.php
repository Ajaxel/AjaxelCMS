<?php if (count($this->langs)>1):?>
<table cellspacing="0" cellpadding="2" style="width:auto"><tr>
<td><img src="<?php echo FTP_EXT?>tpls/img/flags/24/<?php echo $this->lang?>.png" alt="" /></td>
<td><select id="lang_sel_<?php echo $this->name_id?>" class="a-select a-win_status" title="<?php echo lang('_$You may quickly save this entry and then to switch to another language')?>" onchange="<?php echo $onchange?>">
	<?php echo Html::buildOptions($this->lang,array_label($this->langs));?>
</select></td>
<?php if (defined('ENABLE_LANG_SAVE') && ENABLE_LANG_SAVE):?>
<td style="padding-left:4px"><input type="checkbox" name="<?php echo self::KEY_LANG_ALL?>" onclick="if(this.checked){if(1){$('#lang_sel_<?php echo $this->name_id?>').attr('disabled','disabled')}else{this.checked=false}}else{$('#lang_sel_<?php echo $this->name_id?>').removeAttr('disabled')}" id="lang_all_<?php echo $this->name_id?>" value="on" /></td><td style="padding-left:4px"><label class="a-win_status" title="<?php echo lang('$This option will overwrite other language related entries with data of current entry')?>" for="lang_all_<?php echo $this->name_id?>"><?php echo lang('$Save all')?></label></td>
<?php endif;?>
<?php /*if (USE_TRANSLATE && isset($translate) && $translate):?>
<td><img src="<?php echo FTP_EXT?>tpls/img/translate.png" style="margin-left:5px" /></td>
<td><select id="lang_translate_<?php echo $this->name_id?>" alt="<?php echo lang('_$Google translate. Select the language from the list you want to translate this entry with. After selecting, don\'t forget to check and save it.')?>" name="data[translate]" class="a-select a-win_status" onchange="<?php echo $translate;?>">
	<option value=""></option>
	<?php 
	$langs = $this->langs;
	unset($langs[$this->lang]);
	echo Html::buildOptions(0,array_label($langs));?>
</select></td>
<?php endif;*/?>
</tr>
</table>
<?php endif;?>