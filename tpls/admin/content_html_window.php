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
* @file       tpls/admin/content_html_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' html:').' ';
$this->title = $title.$this->post('title', false);
$this->height = 550;
$this->width = 800;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0"><tr><td class="a-r">
	<textarea type="text" class="a-textarea" name="data[body]" style="width:99%;height:421px;font:12px monospace"><?php echo $this->post('body')?></textarea>
	</td></tr></table>
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'save'			=> 'content_save',
		'copy'			=> true,
		'add'			=> true
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>