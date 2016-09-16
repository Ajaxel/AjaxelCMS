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
* @file       tpls/admin/content_banner_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' banner:').' ';
$this->title = $title.$this->post('title', false);
$this->width = 800;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		var editor={
			element: '#a-w-body_<?php echo $this->name_id?>',
			height: 200, 
			base: '<?php echo HTTP_BASE?>',
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','main_photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png','swf'))?>','Image and Flash files');
	}
	,setPhoto:function(file){
		S.A.W.addMainPhoto('<?php echo $this->name_id?>', file, 'main_photo','th1');
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
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$URL:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[url]" value="<?php echo $this->post('url')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Image replace:')?> <br />/ <?php echo lang('$Description:')?> <br />/ <?php echo lang('$HTML:')?></td><td class="a-r" width="15%">
				<div style="width:180px;height:60px">
				<div class="a-l a-main_photo" id="a-w-main_photo_<?php echo $this->name_id?>"><?
				if ($this->post('main_photo', false)):?>
				<a href="/<?php echo $this->post('main_photo_preview')?>" target="_blank"><img src="/<?php echo $this->post('main_photo_thumb')?>?nocache=<?php echo $this->time?>" alt="<?php echo lang('$Description image')?>" /></a>
				<?php endif;?></div>
				<div class="a-r" style="width:110px;height:60px"><input type="file" class="a-file" id="a-main_photo_<?php echo $this->name_id?>" style="width:80px;" size="2" /><br /><a href="javascript:;" onclick="S.A.W.delPhoto('<?php echo $this->name_id?>',0, function(response){window_<?php echo $this->name_id?>.setPhoto(response.substring(2))});">[delete]</a></div>
				</div>
			</td>
			<td class="a-r" width="70%"><textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:98%;height:60px" name="data[descr]"><?php echo $this->post('descr')?></textarea></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0"><tr><td class="a-r">
	<textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" name="data[body]" style="width:99%;height:400px;visibility:hidden"><?php echo $this->post('body')?></textarea>
	</td></tr></table>
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'save'			=> 'content_save'
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>