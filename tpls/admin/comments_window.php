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
* @file       tpls/admin/comments_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' comment:').' ';
$this->title = $title.$this->post('subject', false);
$this->width = 700;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?>'+this.value);
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
			<td class="a-l" width="15%"><?php echo lang('$Subject:')?></td><td class="a-r" width="85%" colspan="3"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[subject]" value="<?php echo $this->post('subject')?>" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Comment:')?></td><td class="a-r" colspan="3"><textarea type="text" class="a-textarea" name="data[original]" style="width:99%;height:121px;font:12px monospace" onblur="S.A.W.codebb('output_body_<?php echo $this->name_id?>', this.value)"><?php echo $this->post('original')?></textarea></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Name:')?></td><td class="a-r"><input type="text" class="a-input" style="width:99%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
			<td class="a-l" width="15%"><?php echo lang('$Email:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" style="width:97%" name="data[email]" value="<?php echo $this->post('email')?>" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$URL:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[url]" value="<?php echo $this->post('url')?>" /></td>
		</tr>
		<tr>
			<td class="a-r" colspan="4"><div style="height:200px;overflow:auto" id="output_body_<?php echo $this->name_id?>"><?php echo $this->output['body']?></div></td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td  class="a-td1"><?php if ($this->id):?><?php echo lang('$Submitted on:')?> <?php echo date('d M Y',$this->post('added', false))?><br /><?php echo lang('$by user:')?> <a href="javascript:;" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=users&id=<?php echo $this->post['userid']?>');"><?php echo $this->post('username')?></a> <?php $ip = long2ip($this->post('ip', false)); echo lang('$IP:')?> <?php echo $ip?><?php endif;?>&nbsp;</td>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?
			if ($this->id):?>
			</td><td>
			<?php 
			$this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
			<?php else:?>
			<?php $this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
			<?php endif;?>
			</td></tr></table>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>