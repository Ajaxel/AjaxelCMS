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
* @file       tpls/admin/entries_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' entry:').' ';
$this->title = $title.$this->post('title', false).($this->id ? ' ID:'.$this->id:'');
$this->width = 800;
$tab_height = 430;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').blur(function(){
			<?php if ($this->id):?>if ($('#a-w-name_<?php echo $this->name_id?>').val()) return false;<?php endif;?>
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=menu',{
				get:'url_name',
				title:this.value
			}, function(data) {
				$('#a-w-name_<?php echo $this->name_id?>').val(data.name);
			})
		});
		<?php $this->inc('js_editors', array('descr'=>245,'body'=>$tab_height-10))?>
		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png','asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv', 'm4v','swf','flv','aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma','aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'))?>','Image, flash, video and audio files');
		S.A.W.tabs('<?php echo $this->name_id?>',1);
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files);
		S.A.W.sortable_images('<?php echo $this->name_id?>','file');	
	}
	<?php $this->inc('js_setgallery')?>
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<?
		$this->inc('tabs', array('tabs' => array(
				'main'		=> 'Entry details',
				'body'		=> 'Full description',
				'images'	=> 'Image gallery',
				'addons'	=> 'Information',
				'notes'		=> 'Admin notes'
			)
		));
		?>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php $this->inc('tr_menu', array('module'=>'entries'))?>
				<?php $this->inc('tr_category', array('module'=>'entries'))?>
				<?php $this->inc('tr_title', array('title'=>'Entry title'))?>
				<tr>
					<td class="a-l"><?php echo lang('$URL name:')?></td><td class="a-r"><input type="text" class="a-input" id="a-w-name_<?php echo $this->name_id?>" style="width:49%" name="data[name]" value="<?php echo strform($this->post('name', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Teaser:')?> /<br /><?php echo lang('$Description:')?></td><td class="a-r"><textarea type="text" class="a-textarea" id="a-w-teaser_<?php echo $this->name_id?>" name="data[teaser]" style="width:99%;height:30px"><?php echo strform($this->post('teaser', false))?></textarea></td>
				</tr>
				<tr>
					<td class="a-r" colspan="2"><textarea type="text" name="data[descr]" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:215px;visibility:hidden"><?php echo strform($this->post('descr', false))?></textarea></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Keywords:')?></td><td class="a-r"><input type="text" class="a-input" style="width:99%" name="data[keywords]" value="<?php echo strform($this->post('keywords', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Dated:')?></td><td class="a-r"><input type="text" class="a-input a-date" style="width:120px" name="data[dated]" value="<?php echo $this->post('dated')?>" /> <input type="checkbox" name="data[comment]"<?php echo ($this->post('comment', false)=='Y'?' checked="checked"':'')?> id="a-comment_<?php echo $this->name_id?>" value="Y" /><label for="a-comment_<?php echo $this->name_id?>"> <?php echo lang('$Allow to comment')?></label> <input type="checkbox" name="data[bodylist]"<?php echo ($this->post('bodylist', false)=='1'?' checked="checked"':'')?> id="a-bodylist_<?php echo $this->name_id?>" value="Y" /><label for="a-bodylist_<?php echo $this->name_id?>"> <?php echo lang('$Show full')?></label></td>
				</tr>
			</table>
		</div>
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			
				<textarea type="text" class="a-textarea" name="data[body]" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:400px;visibility:hidden"><?php echo $this->post('body')?></textarea>
		</div>
		<div id="a_images_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload images:')?></td><td class="a-r" colspan="5" width="85%">
						<div class="a-file_btn"><input type="file" class="a-file" id="a-photo_<?php echo $this->name_id?>" /></div>
						<?php if (!$this->id):?>
						<input type="hidden" name="data[main_photo]" id="a-main_photo_<?php echo $this->name_id?>" value="<?php echo $this->post('main_photo')?>" />
						<?php endif;?>
						<div class="a-r" style="line-height:40px">
							<a href="javascript:;" onclick="S.A.W.toggle_images('<?php echo $this->name_id?>')"><?php echo lang('$Toggle image fields')?></a>
						</div>
					</td>
				</tr>
				<tr>
					<td class="a-r" colspan="2">
						<div id="a-files_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No image files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="a_addons_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l"><?php echo lang('$Additional menus:')?></td><td class="a-r" colspan="3"><select name="data[menuids][]" class="a-select" style="height:190px;" multiple="multiple" size="10"><?
					$params = array(
						'select'	=> 'id, (CASE WHEN cnt>0 THEN CONCAT(title_'.$this->lang.',\' [\',cnt,\':\',cnt2,\']\') ELSE title_'.$this->lang.' END) AS title',
						'selected'	=> explode(',',$this->post('menuids', false))
					);
					echo Factory::call('menu', $params)->toOptions();
				?></select></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$URL:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-url_<?php echo $this->name_id?>" style="width:79%" name="data[options][url]" value="<?php echo strform(@$this->post['options']['url'])?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Source:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-source_<?php echo $this->name_id?>" style="width:79%" name="data[options][source]" value="<?php echo strform(@$this->post['options']['source'])?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Image copyright:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-img_copy_<?php echo $this->name_id?>" style="width:79%" name="data[options][img_copy]" value="<?php echo strform(@$this->post['options']['img_copy'])?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Type:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-type_<?php echo $this->name_id?>" style="width:39%" name="data[type]" value="<?php echo strform($this->post('type', false))?>" /></td>
				</tr>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:<?php echo ($tab_height-15)?>px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'save'			=> 'content_save',
		'copy'			=> true,
		'add'			=> true
	)); ?>
</form>
<?php $this->inc('window_bottom')?>