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
* @file       tpls/admin/orders_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'View':'Add new').' order:').' ';
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
$this->title = $title.$this->post('id', false);
$this->width = 700;
$this->height = 600;
$tab_height = 465;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_details_<?php echo $this->name_id?>"><?php echo lang('$Order details')?></a></li>
			<li><a href="#a_buyer_<?php echo $this->name_id?>"><?php echo lang('$Buyer details')?></a></li>
			<li><a href="#a_chat_<?php echo $this->name_id?>"><?php echo lang('$Chat')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_details_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Table / ID:')?></td><td class="a-r" colspan="2" width="85%"><?php echo $this->post('table')?>: <?php echo $this->post('id')?></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$User:')?></td><td class="a-r" colspan="2" width="85%"><?php echo $this->post('userid')?>. <?php echo $this->post('username')?></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Status:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[status]"><?php echo Html::buildOptions($this->post('status', false), array_label(Conf()->g('order_statuses')))?></select></td>
				</tr>
				<?php if ($this->post('table_data', false)):?>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Table data:')?></td><td class="a-r" colspan="2" width="85%">
					<table class="a-form a-form-all" cellspacing="0">
					<?php 
					ksort($this->post('table_data', false));
					$skip = array('sort','active','inserts','menuid');
					foreach ($this->post('table_data', false) as $k => $v) {
						if (in_array($k, $skip)) continue;
						if ($k=='options') {
							$v = '<div><a href="javascript:;" onclick="$(this).parent().next().slideDown()">'.lang('$Show options').'</a></div><div style="display:none">'.p(unserialize($v),0).'</div>';	
						}
						echo '<tr><td class="a-l" style="font-weight:bold;text-transform:uppercase">'.str_replace('_',' ',$k).'</td><td class="a-r" style="padding-left:20px!important">'.$v.'</td></tr>';
					}
					?></table></td>
				</tr>
				<?php endif;?>
			</table>
		</div>
		<div id="a_buyer_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<?php 
			$arr = unserialize($this->post('profile', false));
			if ($arr):?>
			<table class="a-form" cellspacing="0">
				<?php 
				foreach ($arr as $k => $v) {
					echo '<tr><td class="a-l" style="font-weight:bold" width="15%">'.$k.'</td><td class="a-r">'.$v.'</td></tr>';
				}
				?>
			</table>
			<?php else:?>
			<div class="a-no_files"><?php echo lang('$No user data')?></div>
			<?php endif;?>
		</div>
		<div id="a_chat_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						Coming soon..
					</td>
				</tr>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:453px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $url = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,'m'=>$this->module,'id'=>$this->id,'l'=>$this->lang,'o'=>$this->conid)); ?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td2">
			<?php if ($this->id):?>
			<?php $this->inc('button',array('click'=>'S.A.W.save(\''.$url.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
			<?php else:?>
			<?php $this->inc('button',array('click'=>'S.A.W.save(\''.$url.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
			<?php endif;?>
		</td>
		<?php if ($this->id):?>
		<td  class="a-td3"><?php if ($this->id):?><?php echo lang('$Added:')?> <?php echo date('H:i d M Y',$this->post('added', false))?><br /><?php echo lang('$Status changed on:')?> <?php echo date('H:i d M Y',$this->post('edited', false))?><?php endif;?>&nbsp;</td>
		<?php endif;?>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>