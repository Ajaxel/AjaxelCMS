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
* @file       tpls/admin/email_compose_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$Compose message to:').' ';
$this->title = $title.$this->post('to', false);
$this->width = 800;
$tab_height = 480;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?> '+this.value);
		}).focus();
		<?php $this->inc('js_editors', array('body'=>360))?>
		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo');
		S.A.W.tabs('<?php echo $this->name_id?>');
		/*
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files);
		S.A.W.sortable_images('<?php echo $this->name_id?>','file');
		*/
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
				'main'		=> 'Compose',
				'images'	=> 'Attachments',
				//'addons'	=> 'Addons'
			)
		));
		?>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$To:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" name="data[to]" style="width:70%" id="a-w-title_<?php echo $this->name_id?>" value="<?php echo $this->post('to')?>" />
					<label for="a-w-copy_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-copy_<?php echo $this->name_id?>" name="data[copy]" value="1" checked="checked" /> <?php echo lang('$Save a copy')?></label>
					</td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Subject:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" name="data[subject]" id="a-w-subject_<?php echo $this->name_id?>" maxlength="255" style="width:99%" value="<?php echo $this->post('subject')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Template:')?></td><td class="a-r">
						<select name="data[reply]" class="a-select" style="width:50%" onchange="S.A.W.mail_template(this.value,$('#a-w-subject_<?php echo $this->name_id?>'), $('#a-w-body_<?php echo $this->name_id?>'))"><?php echo Html::buildOptions($this->post('reply', false), $this->array['replies']['F'])?></select>
					</td>
				</tr>
				
			</table>
			<table class="a-form" cellspacing="0">
			<tr><td class="a-r">
				<textarea type="text" class="a-textarea" name="data[body]" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:330px;visibility:hidden"><?php echo $this->post('body')?></textarea>
			</td></tr></table>
			<table class="a-form" cellspacing="0">
			<tr><td class="a-l" width="15%"><?php echo lang('$Save to:')?></td><td class="a-r"><input type="text" style="width:58%" name="data[save]" class="a-input" onkeyup="S.A.Conf.saveto=this.value" id="a-w-save_<?php echo $this->name_id?>" value="<?php echo strform($this->post('save', false))?>"> <label for="a-w-same_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-same_<?php echo $this->name_id?>" onclick="if(this.checked){$('#a-w-save_<?php echo $this->name_id?>').val($('#a-w-subject_<?php echo $this->name_id?>').val())}else{$('#a-w-save_<?php echo $this->name_id?>').val(S.A.Conf.saveto)}" /> <?php echo lang('$Same as subject')?></label></td></tr>
			</table>
		</div>
		<div id="a_images_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload attachments:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-photo_<?php echo $this->name_id?>" style="width:80px;" size="2" />
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Uploaded attachments:')?></td>
					<td class="a-r">
						<div style="height:<?php echo $tab_height-40;?>px;width:100%;overflow:auto;">
						<div id="a-files_div_<?php echo $this->name_id?>" style="width:99%"><div class="a-no_files"><?php echo lang('$No files were uploaded, click BROWSE button')?></div></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<?php /*
		<div id="a_addons_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$URL:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-url_<?php echo $this->name_id?>" style="width:99%" name="data[url]" value="<?php echo $this->post('url')?>" /></td>
				</tr>
			</table>
		</div>
		*/ ?>
	</div>
	<?php $this->inc('bottom', array(
		'label'	=> 'Send',
		'label_id'=> 'Send',
		'img'	=> 'oxygen/16x16/actions/mail-send.png',
		'add'	=> true
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>