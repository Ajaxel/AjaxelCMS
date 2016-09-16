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
* @file       tpls/admin/content_move_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$Move entry from').' ';
$this->title = $title.$this->post('title', false);
$this->width = 500;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').blur(function(){
			<?php if ($this->id):?>if ($('#a-w-name_<?php echo $this->name_id?>').val()) return false;<?php endif;?>
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=menu',{
				get:'url_name',
				title:this.value
			}, function(data) {
				$('#a-w-name_<?php echo $this->name_id?>').val(data.name);
			})
		});
		S.A.W.tabs('<?php echo $this->name_id?>');
		<?php if (request('menuid')):?>
		this.selMenu('<?php echo request('menuid')?>');
		<?php endif;?>
	},
	selMenu:function(v) {
		var s = $('#a_cb_<?php echo $this->name_id?>');
		s.hide();
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=content',{
			get:'content_blocks',
			menuid:v	
		}, function(data) {
			s.show();
			s.attr('disabled',false);
			if (!data) {
				data = {0:'<?php echo lang('_$No content blocks found')?>'}
				s.attr('disabled',true);
			}
			S.A.F.addOptions(s, data, true);
		});
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
$tab_height = 326;
$params = array(
	'select'	=> 'id, parentid, (CASE WHEN cnt>0 THEN CONCAT(title_'.$this->lang.',\' [\',cnt,\':\',cnt2,\']\') ELSE title_'.$this->lang.' END) AS title'
);
$menu = Factory::call('menu', $params)->getAll();
?>
<form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<?
		$this->inc('tabs', array('tabs' => array(
				'move'		=> 'Move to another',
				'new'		=> 'Move to new'
			)
		));
		?>
		<div id="a_move_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l"><?php echo lang('$Moving from:')?></td><td class="a-r" colspan="3">
						<?php echo Factory::call('menu')->printTree($this->post('menuid', false))?>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Menu:')?></td><td class="a-r" colspan="3"><select name="menuid" onchange="window_<?php echo $this->name_id?>.selMenu(this.value)" class="a-select" style="width:95%"><option value=""></option><?
					echo $menu->setSelected(request('menuid'))->toOptions();
				?></select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Content block:')?></td><td class="a-r" colspan="3"><div style="height:20px"><select name="setid" id="a_cb_<?php echo $this->name_id?>" disabled="disabled" class="a-select" style="width:95%"><option value=""> <?php echo lang('$-- please select the menu --')?></option><?
				?></select></div></td>
				</tr>
			</table>
		</div>
		<div id="a_new_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<?
			if ($this->action!='move') {
				$this->post = array();	
			}
			?>
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" name="data[title]" style="width:99%" value="<?php echo $this->post('title')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$URL name:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input"<?php /*if ($this->id):?> disabled="disabled"<?php endif;*/?> id="a-w-name_<?php echo $this->name_id?>" style="width:70%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
				</tr>	
				<tr>
					<td class="a-l"><?php echo lang('$Menu:')?></td><td class="a-r" colspan="3"><select name="data[menuid]" class="a-select" style="width:95%"><?
					
					echo $menu->setSelected($this->post('menuid', false) ? $this->post('menuid', false) : get('menuid'))->toOptions();
				?></select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Additional menus:')?></td><td class="a-r" colspan="3"><select name="data[menuids][]" class="a-select" style="width:95%;height:140px;" multiple="multiple" size="10"><?
					echo $menu->setSelected(explode(',',$this->post('menuids', false)))->toOptions();
				?></select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Template style:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:70%" name="data[style]" value="<?php echo $this->post('style')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Icon:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" id="a-icon_<?php echo $this->name_id?>" style="width:55%" name="data[icon]" value="<?php echo $this->post('icon')?>" /> <?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer('Icons:/','a-icon_<?php echo $this->name_id?>');"><?php echo lang('$Choose..')?></button> <button type="button" class="a-button a-button_x" onclick="$('#a-icon_<?php echo $this->name_id?>').val('');">x</button><?php endif;?></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Tags:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[keywords]" value="<?php echo $this->post('keywords')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Dated:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input a-date" style="width:120px" name="data[dated]" value="<?php echo $this->post('dated')?>" /> <input type="checkbox" name="data[comment]"<?php echo ($this->post('comment', false)=='Y'?' checked="checked"':'')?> id="a-comment_<?php echo $this->name_id?>" value="Y" /><label for="a-comment_<?php echo $this->name_id?>"> <?php echo lang('$Allow to comment')?></label></td>
				</tr>
			</table>
		</div>
	</div>
	<?php
	$url = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,'id'=>$this->id,self::KEY_MODULE=>$this->module,self::KEY_LANG=>$this->lang,self::KEY_ACTION=>'move'));
	?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td2">
			<?
			$this->inc('button',array('click'=>'S.A.W.save(\''.$url.'\', this.form, this);','img'=>'oxygen/16x16/actions/transform-move.png','text'=>lang('$Move')));
		?>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="force_no_open" value="true" />
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>