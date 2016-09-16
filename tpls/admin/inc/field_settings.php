<?php if (!$settings) return;?>
<table cellspacing="0" width="100%" class="a-form_all">
	<tr>
		<td class="a-m<?php echo $this->ui['sub-h1']?>" colspan="2"><div class="a-l"><?php echo lang('$'.$type_name.' settings:')?></div><div class="a-r a-action_buttons"><?php echo ($this->el['id']?'<a href="javascript:;" onclick="window_'.$this->name_id.'.copy('.$this->el['id'].')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-copy.png" alt="'.lang('$Duplicate this element').'" title="'.lang('$Duplicate this element').'" /></a> <a href="javascript:;" onclick="window_'.$this->name_id.'.del('.$this->el['id'].')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" alt="'.lang('$Delete this element').'" title="'.lang('$Delete this element').'" /></a>':'')?></div></td>
	</tr>
	<?php if (!in_array('single',$settings)):?>
</table>
<div class="a-div">
	<table cellspacing="0" width="100%" class="a-form_all">
	<tr><td class="a-m<?php echo $this->ui['sub-m']?>" colspan="2"><?php echo lang('$Attributes:')?></td></tr>
	<?php endif;?>
		<tr>
			<td class="a-l" width="35%"><?php echo lang('$Colspan:')?></td><td class="a-r" width="65%"><select class="a-select a-el_<?php echo $this->name_id?>" name="el[settings][colspan]"><?php echo Html::buildOptions($this->el['settings']['colspan'],Html::arrRange(1,$max_colspan));?></select></td>
		</tr>
		<?php if (in_array('name',$settings)):?>
		<tr>
			<td class="a-l"><?php echo lang('$Element name:')?></td><td class="a-r"><input type="text" class="a-input a-el_<?php echo $this->name_id?>" style="width:98%" name="el[name]" value="<?php echo $this->el['name']?>" /></td>
		</tr>
		<?php endif;
		if (in_array('question',$settings)):?>
		<tr>
			<td class="a-l"><?php echo lang('$Text:')?></td><td class="a-r"><textarea class="a-textarea a-el_<?php echo $this->name_id?>" style="width:99%;height:40px" name="el[settings][val]"><?php echo $this->el['settings']['val']?></textarea></td>
		</tr>
		<?php
		elseif (in_array('sel',$settings)):?>
		<tr>
			<td class="a-l"><?php echo lang('$Default value:')?></td><td class="a-r"><input type="text" class="a-input a-el_<?php echo $this->name_id?>" style="width:99%" name="el[settings][val]" value="<?php echo $this->el['settings']['val']?>" /></td>
		</tr>
		<?php
		elseif (in_array('val',$settings)):?>
		<tr>
			<td class="a-l"><?php echo lang('$Value:')?></td><td class="a-r"><textarea class="a-textarea a-el_<?php echo $this->name_id?>" style="width:99%;height:40px" name="el[settings][val]"><?php echo $this->el['settings']['val']?></textarea><br /><input type="checkbox" name="el[settings][clear]" class="a-checkbox a-el_<?php echo $this->name_id?>" id="a-el_clean_<?php echo $this->name_id?>" value="1"<?php echo ($this->el['settings']['clear']?' checked="checked"':'')?> /><label for="a-el_clean_<?php echo $this->name_id?>"><?php echo lang('$clear on focus')?></label></td>
		</tr>
		<?php endif;
		if (in_array('ajax',$settings)):?>
		<tr>
			<td class="a-l" width="35%"><?php echo lang('$Ajax suggest:')?></td><td class="a-r" width="65%"><select class="a-select a-el_<?php echo $this->name_id?>" name="el[settings][ajax]"><option value=""></option><?php echo Html::buildOptions($this->el['settings']['ajax'], Data::ajaxSuggest())?></select></td>
		</tr>
		<?php endif;
		if (in_array('attr',$settings)):?>
		<tr>
			<td class="a-l"><?php echo lang('$Attributes:')?></td><td class="a-r"><textarea class="a-textarea a-el_<?php echo $this->name_id?>" style="width:99%;height:40px" name="el[settings][attr]"><?php echo $this->el['settings']['attr']?></textarea></td>
		</tr>
		<?php endif;
		if (in_array('help',$settings)):?>
		<tr>
			<td class="a-l"><?php echo lang('$Help:')?></td><td class="a-r"><textarea class="a-textarea a-el_<?php echo $this->name_id?>" style="width:99%;height:40px" name="el[settings][help]"><?php echo $this->el['settings']['help']?></textarea></td>
		</tr>
		<?php
		endif;
		if (in_array('html',$settings)):?>
		<tr>
			<td class="a-r" width="35%" colspan="2"><textarea class="a-textarea a-el_<?php echo $this->name_id?>" style="width:99%;height:280px" name="el[settings][val]"><?php echo $this->el['settings']['val']?></textarea></td>
		</tr>
		<?php endif;
		if (!in_array('single',$settings)):?>
	</table>
</div><?php 
if (in_array('validation',$settings)):?>
<div class="a-div">
	<table cellspacing="0" width="100%" class="a-form_all">
		<tr><td class="a-m<?php echo $this->ui['sub-m']?>" colspan="2"><?php echo lang('$Validation:')?></td></tr>
		<tr>
			<td class="a-l" width="35%"><?php echo lang('$Field is:')?></td><td class="a-r" width="65%"><select class="a-select a-el_<?php echo $this->name_id?>" name=""></select></td>
		</tr>

	</table>
</div>
<?php endif;
if (in_array('db',$settings)):?>
<div class="a-div">
	<table cellspacing="0" width="100%" class="a-form_all">
		<tr><td class="a-m<?php echo $this->ui['sub-m']?>" colspan="2"><?php echo lang('$Database:')?></td></tr>
		<tr>
			<td class="a-l" width="35%"><?php echo lang('$DB column:')?></td><td class="a-r" width="65%"><select class="a-select a-el_<?php echo $this->name_id?>" name=""></select></td>
		</tr>

	</table>
</div>
<?php endif;?>
<table cellspacing="0" width="100%" class="a-form_all">
	<?php endif;?>
	<tr>
		<td colspan="2" class="a-r" style="text-align:center;padding:15px!important">
			<button type="button" class="a-button" onclick="window_<?php echo $this->name_id?>.save(this.form)"><table cellspacing="0"><tr><td><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/document-save.png" /></td><td><?php echo lang(($this->el['id']?'$Edit':'$Add'))?></td></tr></table></button>
		</td>
	</tr>
</table>
<input type="hidden" class="a-el_<?php echo $this->name_id?>" name="el[type]" value="<?php echo $this->el['type']?>" />
<input type="hidden" class="a-el_<?php echo $this->name_id?>" name="el[id]" value="<?php echo $this->el['id']?>" />