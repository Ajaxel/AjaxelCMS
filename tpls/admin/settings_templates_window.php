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
* @file       tpls/admin/settings_templates_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->title = lang('$Edit %1 template',$this->post('name'));
$this->height = 320;
$this->width = 400;
?>
<script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.FU.init(65);
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%" nowrap><?php echo lang('$Folder name:')?></td><td class="a-r" width="85%"><input class="a-input" type="text" name="data[name]" value="<?php echo $this->post('name')?>" style="width:60%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Data prefix:')?></td><td class="a-r"><input class="a-input" type="text" name="data[prefix]" value="<?php echo $this->post('prefix')?>" style="width:60%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Engine:')?></td><td class="a-r"><select style="width:50%" class="a-select" type="text" name="data[engine]"><?php echo Html::buildOptions($this->post('engine', false),$this->options['engines'])?></select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Title:')?></td><td class="a-r"><input class="a-input" type="text" name="data[title]" value="<?php echo $this->post('title')?>" style="width:98%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Description:')?></td><td class="a-r"><textarea class="a-textarea"  name="data[descr]" style="width:98%;height:65px"><?php echo $this->post('descr')?></textarea></td>
		</tr>

		<?php /*
		<tr>
			<td class="a-l"><?php echo lang('$Duplicate data?')?></td><td class="a-r"><input type="checkbox" name="data[data]" id="a-chk_data"><label for="a-chk_data"><?php echo lang('$Yes')?></label></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Duplicate%1 data%1 from:','<br />')?></td><td class="a-r"><select style="width:50%" size="5" type="text" name="data[data_from]"><option value="" selected="selected"><?php echo lang('$use empty')?></option><?php echo Html::buildOptions($this->post('files_from', false), array_label($this->langs))?></select></td>
		</tr>
		*/ ?>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td2">
			<?php $this->inc('button',array('click'=>'S.A.W.save(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_TAB.'='.$this->tab.'&'.self::KEY_ACTION.'=save&id='.$this->post['name'].'\', this.form, this);','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>