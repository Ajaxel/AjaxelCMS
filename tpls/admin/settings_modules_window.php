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
* @file       tpls/admin/settings_modules_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' module:').' ';
$this->title = $title.$this->post('type', false).'::'.$this->post('table', false);
$this->width = 600;
$tab_height = 265;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.W.tabs('<?php echo $this->name_id?>');
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_main_<?php echo $this->name_id?>"><?php echo lang('$Module details')?></a></li>
			<li><a href="#a_settings_<?php echo $this->name_id?>"><?php echo lang('$Module settings')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Module title:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Description:')?></td><td class="a-r" colspan="2" width="85%"><textarea name="data[descr]" type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:65px"><?php echo $this->post('descr')?></textarea></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Icon:')?></td><td class="a-r" colspan="2" width="85%"><input class="a-input" type="text" name="data[icon]" style="width:<?php if (FILE_BROWSER):?>70<?php else:?>95<?php endif;?>%" id="a-icon_<?php echo $this->name_id?>" value="<?php echo $this->post('icon')?>" /><?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer('Icons:/oxygen/16x16/actions/','a-icon_<?php echo $this->name_id?>');"><?php echo lang('$Choose..')?></button> <button type="button" class="a-button a-button_x" onclick="$('#a-icon_<?php echo $this->name_id?>').val('');">x</button><?php endif;?></td>
				</tr>
			</table>
		</div>
		<div id="a_settings_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php $this->inc('form',array('post'=>$this->post('options', false),'elements'=>$this->array['options'][$this->post('type', false)],'array'=>$this->array['array']))?>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:250px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $this->inc('bottom', array(
		'save'			=> 'content_save',
		'no_add'		=> true
	)); ?>
	<?php $url = $this->url_full.'&id='.$this->id;?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>