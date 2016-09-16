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
* @file       tpls/admin/settings_templates_uninstall_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->title = lang('$Unistall template: %1',$this->id);
$this->height = 170;
$this->width = 350;
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
			<td class="a-l" width="15%" nowrap><?php echo lang('$Delete database data:')?></td><td class="a-r"><input type="checkbox" name="data[data]" id="a-un_data" /> <label for="a-un_data"><?php echo lang('$Yes')?></label></td>
		</tr>
		<tr>
			<td class="a-l" nowrap><?php echo lang('$Delete template files:')?></td><td class="a-r"><input type="checkbox" name="data[t_files]" id="a-un_t_files" /> <label for="a-un_t_files"><?php echo lang('$Yes')?></label></td>
		</tr>
		<tr>
			<td class="a-l" nowrap><?php echo lang('$Delete uploaded files:')?></td><td class="a-r"><input type="checkbox" id="a-un_u_files" name="data[u_files]" /> <label for="a-un_u_files"><?php echo lang('$Yes')?></label></td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td2">
			<?php $this->inc('button',array('click'=>'if(confirm(\''.lang('Are you sure to delete this template: %1',$this->id).'\')){S.A.W.save(\'?'.URL_KEY_ADMIN.'=settings&id='.$this->id.'&'.self::KEY_TAB.'='.$this->tab.'&'.self::KEY_ACTION.'=uninstall\', this.form, this)}','img'=>'oxygen/16x16/actions/edit-delete.png','text'=>lang('$Unistall'))); ?>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>