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
* @file       tpls/admin/inc/buttons.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!isset($save)) $save = 'content_save';
?>
<div class="a-window-bottom ui-dialog-buttonpane">
	<table width="100%"><tr>
	<?php if ($this->id):?>
		<td class="a-td1">
		<?php if ($this->idcol=='rid'):?>
			<?php $this->inc('lang_save',array('onchange'=>$this->options['content_lang_save']))?>	
		<? endif;?>
		</td>
		<td  class="a-td3"><?php if ($this->id):?><?php if ($this->post('edited', false)) { echo lang('$Edited:')?> <?php echo date('d M Y',$this->post('edited', false))?><br /><?php if ($this->post('username', false)) { echo lang('$by author:')?> <?php echo $this->post('username'); }}?><?php endif;?>&nbsp;</td>
	<?php endif;?>
	<td class="a-td2">
		<table><tr><td>
		<?php if ($this->id):?>
			<?php if (isset($add)):?>
			<button type="button" class="a-button a-button_s" onclick="<?php echo $this->options[$save]?>"><?php echo lang('$Add another')?></button></td><td>
			<?php endif;?>
			
			<?php $this->inc('button',array('click'=>$this->options[$save],'img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
		<?php else:?>
			<?php $this->inc('button',array('click'=>$this->options[$save],'img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
		<?php endif;
		?>
		</td>
		</tr></table>
	</td>
	</tr></table>
</div>