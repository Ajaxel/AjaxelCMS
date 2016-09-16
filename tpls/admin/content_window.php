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
* @file       tpls/admin/content_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' '.($this->conid?'entry to':'page').':').' ';
$this->title = $title.$this->post('title', false);
$this->width = 500;
$this->status = lang('$Content is actual page where can be placed many different modules and in any amount');
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
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
$params = array(
	'select'	=> 'id, parentid, (CASE WHEN cnt>0 THEN CONCAT(title_'.$this->lang.',\' [\',cnt,\':\',cnt2,\']\') ELSE title_'.$this->lang.' END) AS title'
);
$menu = Factory::call('menu', $params)->getAll();
?>
<form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<?php if (get(self::KEY_ADD) || get(self::KEY_NEW)):
		if (!$this->module) $this->module = 'article';
		?>
		<tr>
			<td class="a-l" width="25%"><?php echo lang('$Content type:')?></td><td class="a-r" colspan="3"><select name="module" style="width:60%;height:120px" class="a-select" size="5"<?php if ($this->conid):?> onchange="S.A.W.close('window_<?php echo $this->name_id?>');S.A.C.open(<?php echo $this->conid?>, this.value, this);"<?php endif;?>><?php echo Data::getArray('content_modules', ($this->conid?'':$this->module))?></select></td>
		</tr>
		<?php endif;?>
		<?php if ($this->conid):?>
		<tr>
			<td class="a-l"><?php echo lang('$Menu tree:')?></td><td class="a-r" colspan="3">
				<?php echo Factory::call('menu')->printTree($this->post('menuid', false))?>
			</td>
		</tr>
		<?php else:?>
		<?php
		$this->inc('tr_title');
		?>
		<tr>
			<td class="a-l"><?php echo lang('$URL name:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input"<?php /*if ($this->id):?> disabled="disabled"<?php endif;*/?> id="a-w-name_<?php echo $this->name_id?>" style="width:70%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
		</tr>	
		<tr>
			<td class="a-l"><?php echo lang('$Menu:')?></td><td class="a-r" colspan="3"><select name="data[menuid]" class="a-select" style="width:95%"><?
			echo $menu->setSelected($this->post('menuid', false))->toOptions();
		?></select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Additional menus:')?></td><td class="a-r" colspan="3"><select name="data[menuids][]" class="a-select" style="width:95%;height:140px;" multiple="multiple" size="10"><?
			echo $menu->setSelected(explode(',',$this->post('menuids', false)))->toOptions();
		?></select></td>
		</tr>
		<?php /*
		<tr>
			<td class="a-l"><?php echo lang('$Template style:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:70%" name="data[style]" value="<?php echo $this->post('style')?>" /></td>
		</tr>
		*/ ?>
		<tr>
			<td class="a-l"><?php echo lang('$Icon:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" id="a-icon_<?php echo $this->name_id?>" style="width:55%" name="data[icon]" value="<?php echo $this->post('icon')?>" /> <?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer('Icons:/','a-icon_<?php echo $this->name_id?>');"><?php echo lang('$Choose..')?></button> <button type="button" class="a-button a-button_x" onclick="$('#a-icon_<?php echo $this->name_id?>').val('');">x</button><?php endif;?></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Tags:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[keywords]" value="<?php echo $this->post('keywords')?>" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Dated:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input a-date" style="width:120px" name="data[dated]" value="<?php echo $this->post('dated')?>" /> <input type="checkbox" name="data[comment]"<?php echo ($this->post('comment', false)=='Y'?' checked="checked"':'')?> id="a-comment_<?php echo $this->name_id?>" value="Y" /><label for="a-comment_<?php echo $this->name_id?>"> <?php echo lang('$Allow to comment')?></label></td>
		</tr>
		<?php endif;?>

	</table>
	<?php if (!$this->conid):
	$url = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,'id'=>$this->id,self::KEY_LANG=>$this->lang,self::KEY_ACTION=>'save'));
	?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td class="a-td1">
		<?php 
			if ($this->langs>1) {
				$this->inc('lang_save',array('onchange'=>$this->options['content_lang_save']));
			} else {
				echo '&nbsp;';	
			}
		?>
		</td>
		<?php endif;?>
		<td class="a-td3">
			<?
			if ($this->id):
				$this->inc('button',array('click'=>'S.A.W.save(\''.$url.'&m=\'+(this.form.module?this.form.module.value:\'\'), this.form, this);','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save')));
			elseif ($this->conid):
				$this->inc('button',array('click'=>'S.A.C.open('.$this->conid.', (this.form.module?this.form.module.value:\'\'), this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add')));
			elseif ($this->menuid || get(self::KEY_NEW)):
				$this->inc('button',array('click'=>'S.A.W.save(\''.$url.'&m=\'+(this.form.module?this.form.module.value:\'\'), this.form, this);','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add')));
			else:
				$this->inc('button',array('click'=>'S.A.W.save(\''.$url.'&m=\'+(this.form.module?this.form.module.value:\'\'), this.form, this);','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save')));
			endif;
		?>
		</td>
		</tr></table>
	</div>
	<?php endif;?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>