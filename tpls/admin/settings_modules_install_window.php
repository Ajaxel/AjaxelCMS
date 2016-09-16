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
* @file       tpls/admin/settings_modules_install_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->title = lang('$Install module to %1',$this->tpl);
$this->height = 290;
$this->width = 620;
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
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Module type:')?></td><td class="a-r" colspan="2" width="85%"><?php echo post('type')?></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Template from:')?></td><td class="a-r" colspan="2" width="85%"><?php echo post('tpl')?></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Module name:')?></td><td class="a-r" colspan="2" width="85%"><?php echo post('table')?></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Information:')?></td><td class="a-r" colspan="2" width="85%"><?php echo $this->info?></td>
		</tr>
	</table>
	<input type="hidden" name="data[tpl]" value="<?php echo post('tpl')?>" />
	<input type="hidden" name="data[type]" value="<?php echo post('type')?>" />
	<input type="hidden" name="data[table]" value="<?php echo post('table')?>" />
	<div class="a-window-bottom ui-dialog-buttonpane">
		<?php if ($this->allow_install):?>
		<table width="100%"><tr>
		<td class="a-td2">
			<?php $this->inc('button',array('click'=>'S.A.W.save(\'?'.URL_KEY_ADMIN.'=settings&'.self::KEY_TAB.'='.$this->tab.'&'.self::KEY_ACTION.'=install\', this.form, this);','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Install'))); ?>
		</td>
		</tr></table>
		<?php endif;?>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>