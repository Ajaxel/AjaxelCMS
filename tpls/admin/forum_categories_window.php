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
* @file       tpls/admin/forum_categories_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new')).' ';
$this->title = $title.$this->post('title', false);
$this->width = 800;
$tab_height = 444;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?> '+this.value);
		}).focus();
	}
	<?php $this->inc('js_setphoto')?>
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form ui-corner-all" cellspacing="0">
		<?php if (isset($this->post['title'])):$this->inc('tr_title', array('title'=>'Headline'));endif;?>
	<tr></table><table class="a-form ui-corner-all" cellspacing="0"><td class="a-r" colspan="2">
		<textarea type="text" class="a-textarea" name="data[descr]" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:160px;"><?php echo $this->post('descr')?></textarea>
	</td></tr></table>
		
	<?php $this->inc('bottom', array(
		'save'			=> 'content_save',
	)); ?>
</form>
<?php $this->inc('window_bottom')?>