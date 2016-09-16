<table class="a-form sortable_<?php echo $this->name_id?>" id="a-options_<?php echo $this->name_id?>" cellspacing="0">
	<tbody>
	<tr>
		<td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/bookmark-toolbar.png" /></td>
		<td class="a-r"><?php echo lang('$Select group')?></td><td class="a-r"><select onchange="S.A.W.selectGroup(this, $('#a-options_<?php echo $this->name_id?>').parent())"><option value=""></option><?php echo Html::buildOptions(false,DB::getAll('SELECT name FROM '.DB_PREFIX.'product_options WHERE lang=\''.$this->lang.'\' AND `group`=\''.$option_group.'\' ORDER BY name','name'))?></select>
		<?php echo lang('$Save current keys to group:')?> <input name="option_group" id="option_group_<?php echo $this->name_id?>" value="" />
		<button type="button" class="a-button" onclick="S.A.W.saveOptionGroup($(this).parent().parent().parent(),'.a-option_key',this,{'option_group':'<?php echo $option_group?>', 'max_add':'<?php echo $max_add?>', 'type':'<?php echo $type?>'})"><?php echo lang('$Save')?></button>
		</td>
		<td class="a-l">&nbsp;</td>
	</tr>
	<?php 
	if ($this->post['options'][$option_group]):
		foreach ($this->post['options'][$option_group] as $key => $val):
	?><tr>
		<td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/transform-move.png" /></td>
		<td class="a-l"><input type="text" class="a-input a-option a-option_key" style="width:99%" name="data[options][<?php echo $option_group?>][]" value="<?php echo $key?>" /></td>
		<td class="a-r"><?php if ($type=='textarea'):?><textarea class="a-textarea" style="width:99%" name="data[options][<?php echo $option_group?>][]"><?php echo $val?></textarea><?php else:?><input type="text" class="a-input a-option a-option_value" style="width:99%" name="data[options][<?php echo $option_group?>][]" value="<?php echo $val?>" /><?php endif;?></td>
		<td class="a-l"><a href="javascript:;" onclick="S.A.W.copyOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-copy.png" /></a>&nbsp;<a href="javascript:;" onclick="S.A.W.delOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" /></a></td>
	</tr><?php 
		endforeach;
	endif;?>
	<?php for ($j=1;$j<=$max_add;$j++):?>
		<tr>
			<td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/transform-move.png" /></td>
			<td class="a-l" width="25%"><input type="text" class="a-input a-option a-option_key" style="width:99%" name="data[options][<?php echo $option_group?>][]" /></td>
			<td class="a-r" width="75%"><?php if ($type=='textarea'):?><textarea class="a-textarea a-option" style="width:99%" name="data[options][access][]"></textarea><?php else:?><input type="text" class="a-input a-option a-option_value" style="width:99%" name="data[<?php echo $option_group?>][<?php echo $option_group?>][]" /><?php endif;?></td>
			<td class="a-l"><a href="javascript:;" onclick="S.A.W.copyOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-copy.png" /></a>&nbsp;<a href="javascript:;" onclick="S.A.W.addOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a></td>
		</tr>
	<?php endfor;?>
	<tbody>
</table>