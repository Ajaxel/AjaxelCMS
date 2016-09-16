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
* @file       tpls/admin/email_mailtpl_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit mail template':'Add new mail template').':').' ';
$this->title = $title.$this->post('name', false);
if ($this->post('type', false)=='F') {
	$this->width = 780;
} else {
	$this->width = 700;
}
$tab_height = 480;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		<?php 
			if ($this->post('type', false)=='F') {
				$this->inc('js_editors', array('body'=>420));
			}
		?>
	}
	<?php $this->inc('js_setgallery')?>
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Name:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" name="data[name]" style="width:49%" value="<?php echo $this->post('name')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Subject:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" name="data[subject]" style="width:99%" value="<?php echo $this->post('subject')?>" /></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0">
	<tr><td class="a-r">
		<textarea type="text" name="data[body]" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:400px;"><?php echo $this->post('body')?></textarea>
		
	</td></tr></table>
	<?php $this->inc('buttons', array('save'=>'content_save'));?>
	<input type="hidden" name="data[type]" value="<?php echo $this->post('type')?>" />
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>