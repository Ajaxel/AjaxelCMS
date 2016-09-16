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
* @file       tpls/admin/email_templates_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' email letter:').' ';
$this->title = $title.$this->post('title', false);
$this->height = 550;
$this->width = 850;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo $title?>'+this.value);
		});
		<?php if (!@$_SESSION['AdminGlobal']['nowysiwyg']):?>
		var editor={
			element: '#a-w-body_<?php echo $this->name_id?>',
			height: 390, 
			base: '<?php echo HTTP_BASE?>',
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		<?php endif;?>
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
			<td class="a-l" width="15%"><?php echo lang('$Filename:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:70%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Subject:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0"><tr><td class="a-r">
		<textarea type="text" class="a-textarea" name="data[body]" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:400px"><?php echo $this->post('body')?></textarea>
	</td></tr></table>
	<?php 
	
	
	$this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'no_translate'	=> true,
		'save'			=> 'content_save',
		'copy'			=> false,
		'add'			=> true
	));	
	?>
	
	
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>