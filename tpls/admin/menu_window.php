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
* @file       tpls/admin/menu_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' menu:').($this->id?' (ID: '.$this->id.')':'').' ';
$this->title = $title.$this->post('title', false);
$this->width = 780;
?><script type="text/javascript">
<?php echo Index::CDA?>
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-position_<?php echo $this->name_id?>').change(function(){
			var o=this;
			if(o.value=='#new'){
				S.A.F.addOpt(this, ['<?php echo lang('_$New position name')?>','<?php echo lang('_$Add')?>'], false, function(v){
					if (!v) {
						o.selectedIndex=0;
						return false;
					}
					S.A.F.fillOptions('a-parent-menu_<?php echo $this->name_id?>','?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>',{
						get:'parent_menus',
						position:v
					}, true)
				});
			} else if (o.value) {
				var v=o.value;
				S.A.F.fillOptions('a-parent-menu_<?php echo $this->name_id?>','?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>',{
					get:'parent_menus',
					position:v
				}, true);
			} else {
				this.selectedIndex=0;	
			}
		});
		$('#a-w-target_<?php echo $this->name_id?>').change(function(){
			if(this.value=='#new'){
				S.A.F.addOpt(this, ['<?php echo lang('_$Another target name')?>','<?php echo lang('_$Add')?>']);
			}
		});
		$('#a-w-title_<?php echo $this->name_id?>').blur(function(){
			<?php if (1 || $this->id):?>if ($('#a-w-name_<?php echo $this->name_id?>').val()) return false;<?php endif;?>
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>',{
				get:'url_name',
				title:this.value
			}, function(data) {
				$('#a-w-name_<?php echo $this->name_id?>').val(data.name);
			})
		});
		setTimeout(function(){
			$('#a-w-title_<?php echo $this->name_id?>').focus();
		},500);
		S.A.F.fill($('#a-w-groupids_<?php echo $this->name_id?>'),<?php echo json(Data::getArray('user_groups'))?>,<?php echo json($this->post('groupids', false))?>,false);
		S.A.F.fill($('#a-w-classids_<?php echo $this->name_id?>'),<?php echo json(Data::getArray('user_classes'))?>,<?php echo json($this->post('classids', false))?>,false);
		
		if (S.A.W.callback_func) {
			S.A.W.callback_func();
			S.A.W.callback_func = false;
		}
		
	}
}
<?php $this->inc('js_pop')?>
<?php echo Index::CDZ?>
</script>
<?php 
$this->inc('window_top');
?>
<form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php if (count($this->langs)>1):?><img src="<?php echo FTP_EXT?>tpls/img/flags/16/<?php echo $this->lang?>.png" style="float:left;margin-right:4px" alt="" /> <?php endif;?><?php echo lang('$Headline:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$URL name:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input"<?php /* if ($this->id):?> disabled="disabled"<?php endif;*/?> id="a-w-name_<?php echo $this->name_id?>" style="width:70%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$or External link:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" style="width:95%" name="data[url]" value="<?php echo $this->post('url')?>" /></td>
			<td class="a-l2" width="15%">Target:</td><td class="a-r" width="35%"><select name="data[target]" class="a-select" id="a-w-target_<?php echo $this->name_id?>"><option value=""><?php echo Data::getArray('menu_targets',$this->post('target', false))?><option value="#new"> -- another --</select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Position:')?></td><td class="a-r"><select name="data[position]" id="a-w-position_<?php echo $this->name_id?>" style="width:95%" class="a-select"><option value=""><?php echo lang('$-- select --')?></option><?php echo Html::buildOptions($this->post('position', false), $this->Menu->getPositions())?><option value="#new"><?php echo lang('$-- create new --')?></select></td>
			<td class="a-l2"><?php echo lang('$Parent menu:')?></td><td class="a-r"><select name="data[parentid]" id="a-parent-menu_<?php echo $this->name_id?>" style="width:95%" class="a-select"><option value="0"><?php echo lang('$-- top level --')?><?php echo Html::buildOptions($this->post('parentid', false), $this->Menu->getParentMenus($this->post('position', false), $this->id))?></select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Keywords:')?></td><td class="a-r"><textarea type="text" class="a-textarea" style="width:95%;height:70px" name="data[keywords]"><?php echo $this->post('keywords')?></textarea></td>
			<td class="a-l2"><?php echo lang('$Description:')?></td><td class="a-r"><textarea type="text" class="a-textarea" style="width:95%;height:70px" name="data[descr]"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		<tr style="vertical-align:top">
			<td class="a-l"><?php echo lang('$Browser title:')?></td><td class="a-r"><input type="text" class="a-input" style="width:95%" name="data[title2]" value="<?php echo $this->post('title2')?>" /></td>
			<td class="a-l2"><?php echo lang('$Activity:')?></td><td class="a-r"><select name="data[display]" style="width:95%" class="a-select" onchange="if(this.value==4)$('#a-options_<?php echo $this->name_id?>').show(); else $('#a-options_<?php echo $this->name_id?>').hide();"><?php echo Data::getArray('menu_display',$this->post('display', false))?></select></td>
		</tr>
		<tr id="a-options_<?php echo $this->name_id?>"<?php echo ($this->post('display', false)==4?'':' style="display:none"')?>>
			<td class="a-l"><?php echo lang('$Groups:')?></td>
			<td class="a-r"><ul class="a-select" rel="data[groupids][]" id="a-w-groupids_<?php echo $this->name_id?>" style="height:90px"></ul></td>
			<td class="a-l"><?php echo lang('$Classes:')?></td>
			<td class="a-r"><ul class="a-select" rel="data[classids][]" id="a-w-classids_<?php echo $this->name_id?>" style="height:90px"></ul></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Icon:')?></td><td class="a-r"><input type="text" class="a-input" id="a-icon_<?php echo $this->name_id?>" style="width:<?php if (FILE_BROWSER):?>55<?php else:?>95<?php endif;?>%" name="data[icon]" value="<?php echo $this->post('icon')?>" /><?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer('Icons:/','a-icon_<?php echo $this->name_id?>');"><?php echo lang('$Choose..')?></button> <button type="button" class="a-button a-button_x" onclick="$('#a-icon_<?php echo $this->name_id?>').val('');">x</button><?php endif;?></td>
			<td class="a-l"><?php echo lang('$Order:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" style="width:25%" name="data[sort]" value="<?php echo $this->post('sort')?>" /></td>
		</tr>
	</table>
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'save'			=> 'content_save',
		'no_translate'	=> true,
		'copy'			=> true,
		'add'			=> '&position='.$this->post('position').''
	)); ?>
	
	
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>