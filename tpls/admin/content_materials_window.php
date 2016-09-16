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
* @file       tpls/admin/content_materials_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' material:').' ';
$this->title = $title.$this->post('title', false);
$this->height = 375;
$this->width = 700;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','main_photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png','swf'))?>','Image and Flash files');
	}
	,setPhoto:function(file){
		S.A.W.addMainPhoto('<?php echo $this->name_id?>', file, 'main_photo');
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
			<td class="a-l" width="15%"><?php echo lang('$Filter shadow:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[filter_shadow]"><option value=""></option><?php echo Data::getArray('my:filter_shadow',$this->post('filter_shadow', false))?></select></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Filter color:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[filter_color]"><option value=""></option><?php echo Data::getArray('my:filter_color',$this->post('filter_color', false))?></select></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Code:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:39%" name="data[code]" value="<?php echo $this->post('code')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Image replace:')?> <br />/ <?php echo lang('$Description:')?></td><td class="a-r">
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
		<tr>
			<td class="a-l"><?php echo lang('$Assign to:')?></td>
			<td class="a-r" colspan="2">
				<select name="data[catalogue_ids][]" multiple="muktiple" style="height:350px;width:99%">
					<?
					$arr = DB::getAll('SELECT rid, title FROM '.$this->prefix.'content_catalogue WHERE lang=\''.$this->lang.'\'','rid|title');
					$arr_selected = array();
					if ($this->id) {
						$arr_selected = DB::getAll('SELECT DISTINCT rid FROM '.$this->prefix.'content_catalogue WHERE (options LIKE \'%:"'.$this->id.'";%\' OR options LIKE \'%i:'.$this->id.';%\') AND lang=\''.$this->lang.'\'','rid');
					}
					echo Html::buildOptions($arr_selected, $arr);
					?>
				</select>
			</td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td class="a-td1">
		<?php $this->inc('lang_save',array('onchange'=>$this->options['content_lang_save']))?>
		</td>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?php if ($this->id):?>
			<button type="button" class="a-button a-button_s" onclick="$('#a-w-body2_<?php echo $this->name_id?>').text($('#a-w-body_<?php echo $this->name_id?>').html());S.A.W.save('<?php echo $this->url_save?>&a=add', this.form, this)"><?php echo lang('$Add another')?></button></td>
			<td><?php $this->inc('button',array('click'=>'$(\'#a-w-body2_'.$this->name_id.'\').text($(\'#a-w-body_'.$this->name_id.'\').html());S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
			<?php else:?>
			<?php $this->inc('button',array('click'=>'$(\'#a-w-body2_'.$this->name_id.'\').text($(\'#a-w-body_'.$this->name_id.'\').html());S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
			<?php endif;?>
			</td></tr></table>
		</td>
		<?php if ($this->id):?>
		<td  class="a-td3"><?php if ($this->id):?><?php echo lang('$Last time edited:')?> <?php echo date('d M Y',$this->post('edited', false))?><br /><?php echo lang('$by author:')?> <?php echo $this->post('username')?><?php endif;?>&nbsp;</td>
		<?php endif;?>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>