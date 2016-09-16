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
* @file       tpls/admin/email_send_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->title = 'Send email letter: '.$this->post('name', false);
$this->height = 550;
$this->width = 700;
?>
<script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.F.fill($('#a-w-groups_<?php echo $this->name_id?>'),<?php echo json_encode($this->output['groups'])?>,0,false);
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">	
	<table class="a-form">
		<tr><td><div style="font:bold 14px Calibri;background:#fff;color:#000;padding:5px;background:#eaeaea"><?php echo $this->post('title')?></div></td></tr>
		<tr><td><div style="font:12px Calibri;background:#fff;color:#000;padding:5px;height:200px;overflow:auto"><?php echo $this->post['body']?><div id="a-w-message_<?php echo $this->name_id?>"></div></div></td></tr>
	</table>

	<table class="a-form" cellspacing="0">
		<?php if ($this->output['signature']):?>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Signature:')?></td><td class="a-r" colspan="3">
				<select class="a-select" name="data[signature]" onchange="S.A.W.mail_template(this.value,false, $('#a-w-message_<?php echo $this->name_id?>'))">
					<option value=""></option>
					<?php echo Html::buildOptions(0,$this->output['signature']);?>
				</select>
			</td>
		</tr>
		<?php endif;?>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Group:')?></td><td class="a-r" colspan="3">
			<ul class="a-select" rel="data[group][]" id="a-w-groups_<?php echo $this->name_id?>" style="height:120px"></ul>
			</td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Email filter:')?></td><td class="a-r"><input type="text" name="data[where]" class="a-input" value="" style="width:98%" /></td>
			<td class="a-l" width="15%"><?php echo lang('$User filter:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" name="data[where2]" value="active=1" style="width:98%" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Times sent%1no more than:','<br />')?></td><td class="a-r" width="35%"><select name="data[cnt]" class="a-select"><?php echo Html::buildOptions(0,Html::arrRange(0,10) + Html::arrRange(20,100,20))?></select> <label><input type="checkbox" name="data[with_unsub]" value="1" /> <?php echo lang('$Include unsubscribed')?></label></td>
			<td class="a-l" width="15%"><?php echo lang('$Date sent last time%1older than:','<br />')?></td><td class="a-r" width="35%"><input type="text" name="data[sent]" class="a-date a-input" value="" style="width:40%" /></td>
		</tr>
		
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Read more than:')?></td><td class="a-r" width="35%"><select name="data[read]" class="a-select"><?php echo Html::buildOptions(0,Html::arrRange(0,10) + Html::arrRange(20,100,20))?></select></td>
			<td class="a-l" width="15%"><?php echo lang('$Clicked more than:')?></td><td class="a-r" width="35%"><select name="data[clicked]" class="a-select"><?php echo Html::buildOptions(0,Html::arrRange(0,10) + Html::arrRange(20,100,20))?></select></td>
		</tr>
		
		<tr>
			<td class="a-l"><?php echo lang('$From name:')?></td><td class="a-r"><input type="text" name="data[from_name]" class="a-input" value="<?php echo strform(MAIL_NAME)?>" style="width:90%" /></td>
			<td class="a-l" width="15%"><?php echo lang('$From email:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" name="data[from_email]" value="<?php echo MAIL_EMAIL?>" style="width:90%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Portion:')?></td><td class="a-r"><select name="data[portion]" class="a-select">
			<?php echo Html::buildOptions(100,array_merge(Html::arrRange(1,50,10),Html::arrRange(100,2500,400)),true);?>
		</select> <?php echo lang('$emails per period')?></td>
			<td class="a-l"><?php echo lang('$Portion delay:')?></td><td class="a-r"><select name="data[delay]" class="a-select">
			<?php echo Html::buildOptions(200,array_merge(Html::arrRange(100,1000,100)+Html::arrRange(1000,10000,1000)+array(20000,30000)),true);?>
		</select> ms</td>
		</tr>
	</table>
		
	<?php $this->inc('bottom', array(
		'save'			=> 'content_save',
		'label_id'		=> 'Send letter',
		'action_id'		=> 'send'
	)); ?>
	
	<input type="hidden" name="id" value="<?php echo strform($this->id)?>" />
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>