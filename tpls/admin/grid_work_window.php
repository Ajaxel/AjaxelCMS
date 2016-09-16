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
* @file       tpls/admin/grid_work_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' gallery:').' ';
$this->title = $title.$this->post('title', false);
$this->width = 900;
$tab_height = 470;
?><script type="text/javascript">
<?php echo Index::CDA?>
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image files');
		S.A.W.tabs('<?php echo $this->name_id?>');
		var files = <?php echo $this->json_array['files']?>;
		<?php $this->inc('js_editors', array('body'=>400))?>
		this.setImages(files,'photo');
		S.A.W.sortable_images('<?php echo $this->name_id?>','file');	
	}
	,setImages:function(files,name){
		var html = '<ul id="a-'+name+'s_<?php echo $this->name_id?>">';
		var j=0;
		if (files.length) {
			switch (name) {
				case 'photo':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t=='image') {
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+'" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<div class="a-p"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,'th2')+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></div></li>';
							/*
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',$(this).parent().parent().find(\'a.show-photo\').children(0).attr(\'src\'), false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a><br />';
							html += '<a href="javascript:;" onclick="S.A.W.setMainPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/dialog-ok.png" /></a></div></li>';
							*/
							j++;
						}
					}
				break;
			}
		}
		html += '</ul>';
		
		if (j) $('#a-files_div_<?php echo $this->name_id?>').html(html);
		
		switch (name) {
			case 'photo':
				S.A.W.selectable_images('<?php echo $this->name_id?>','photo');
			break;
		}
	}
}
<?php $this->inc('js_pop')?>
<?php echo Index::CDZ?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<?php
		$this->inc('tabs', array('tabs' => array(
				'main'		=> 'Gallery details',
				'images'	=> 'Images',
				'notes'		=> 'Notes'
			)
		));
		?>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php $this->inc('tr_title', array('title'=>'Title', 'colspan'=>4))?>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Folio:')?></td><td class="a-r" colspan="2" width="35%"><select class="a-select"  name="data[folio]" onchange="S.A.L.json('?<?php echo URL_KEY_ADMIN?>=grid&<?php echo self::KEY_MODULE?>=<?=$this->module?>',{get:'folio',folio:this.value}, function(data){$('#a-w-body_<?php echo $this->name_id?>').html(data.body)})">
						<option value=""></option>
						<?php echo Data::getArray('my:folio',$this->post['folio']);?>
					</select>
					</td>
					<td class="a-l" width="15%"><?php echo lang('$Type:')?></td><td class="a-r" colspan="2"><select class="a-select"  name="data[type]">
						<option value=""></option>
						<?php echo Data::getArray('my:types',$this->post['type']);?>
					</select>
					</td>
				</tr>
			</table>
			<table class="a-form" cellspacing="0"><tr><td class="a-r">
				<textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" name="data[body]" style="width:99%;"><?php echo $this->post('body')?></textarea>
			</td></tr></table>
		</div>
		<div id="a_images_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload images:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-photo_<?php echo $this->name_id?>" style="width:80px;" size="2" />
						<?php if (!$this->id):?>
						<input type="hidden" name="data[main_photo]" id="a-main_photo_<?php echo $this->name_id?>" value="<?php echo $this->post('main_photo')?>" />
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Uploaded images:')?></td>
					<td class="a-r">
						<div style="height:<?php echo $tab_height-40;?>px;width:100%;overflow:auto;">
						<div id="a-files_div_<?php echo $this->name_id?>" style="width:99%"><div class="a-no_files"><?php echo lang('$No image files uploaded here, click BROWSE button')?></div></div>
						</div>
					</td>
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
		'save'			=> 'content_save',
		'copy'			=> true,
		'add'			=> true
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>