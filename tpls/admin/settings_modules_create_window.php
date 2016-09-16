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
* @file       tpls/admin/settings_modules_create_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->title = lang('$Create new module');
$this->height = 290;
$this->width = 400;
?>
<script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.FU.init(65);
		$('#a-w-type_<?php echo $this->name_id?>').change(function(){
			var o=this;
			if (o.value) {
				var v=o.value;
				S.A.F.fillOptions('a-w-copy_from_<?php echo $this->name_id?>','?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=modules',{
					get:'modules_by_type',
					type:v
				}, true);
			} else {
				this.selectedIndex=0;	
			}
		});
		$('#a-w-name_<?php echo $this->name_id?>').blur(function(){
			this.value=this.value.toLowerCase();
			$(this).val(this.value);
			var f=this.value.substring(0,1).toUpperCase();
			var l=this.value.substr(1);			
			$('#a-w-title_<?php echo $this->name_id?>').val(f+l);
		});
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="20%" nowrap><?php echo lang('$Module type:')?></td><td class="a-r" colspan="2" width="85%"><select id="a-w-type_<?php echo $this->name_id?>" class="a-select" name="data[type]"></option><?php echo Data::getArray('module_types',$this->post('type', false))?></select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Copy from:')?></td><td class="a-r" colspan="2" width="85%"><select id="a-w-copy_from_<?php echo $this->name_id?>" class="a-select" name="data[copy_from]"><option value=""><?php echo lang('$-- install default --')?></option><?php echo Html::buildOptions('',array_label(Site::getModules(key(Data::getArray('module_types'))),'title'))?></select></td>
		</tr>
		<tr>
			<td class="a-l" nowrap><?php echo lang('$Module name:')?></td><td class="a-r" colspan="2" width="85%"><input class="a-input" type="text" name="data[name]" value="<?php echo $this->post('name')?>" id="a-w-name_<?php echo $this->name_id?>" style="width:60%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Title:')?></td><td class="a-r" colspan="2" width="85%"><input class="a-input" type="text" name="data[title]" value="<?php echo $this->post('title')?>" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Description:')?></td><td class="a-r" colspan="2" width="85%"><textarea class="a-textarea"  name="data[descr]" style="width:99%;height:65px"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Icon:')?></td><td class="a-r" colspan="2" width="85%"><input class="a-input" type="text" name="data[icon]" style="width:<?php if (FILE_BROWSER):?>55<?php else:?>95<?php endif;?>%" id="a-icon_<?php echo $this->name_id?>" value="<?php echo $this->post('icon')?>" /><?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer('Icons:/oxygen/16x16/actions/','a-icon_<?php echo $this->name_id?>');"><?php echo lang('$Choose..')?></button> <button type="button" class="a-button a-button_x" onclick="$('#a-icon_<?php echo $this->name_id?>').val('');">x</button><?php endif;?></td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td2">
			<?php $this->inc('button',array('click'=>'S.A.W.save(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_TAB.'='.$this->tab.'&'.self::KEY_ACTION.'=create\', this.form, this);','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Create'))); ?>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>