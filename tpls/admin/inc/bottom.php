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
* @file       tpls/admin/inc/bottom.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php

if (!isset($save) || !$save) $save = 'content_save';
?>
<div class="a-window-bottom ui-dialog-buttonpane">
	<table style="width:100%"><tr>
	<?php if (isset($left)):?>
	<td class="a-td1"><?php echo $left?></td>
	<?php endif;?>
	<?php if ($this->id):?>
	<?php if (isset($lang) && count($this->langs)>1):?>
	<td class="a-td1">
	<?php 
		if (isset($lang)) {
			$this->inc('lang_save',array('onchange'=>$this->options[$lang], 'translate'=>(!isset($no_translate)?$this->options[$save]:false)));
		} else {
			echo '&nbsp;';	
		}
	?>
	</td>
	<td class="a-td2">
	<?php else:?>
	<td class="a-td1">
	<?php endif;?>
	<?php
	if ($this->post('added', false)) echo lang('$Added:'),' ',date('H:i / d.m.Y', $this->post('added', false)),' <a href="javascript:;" onclick="S.A.W.open(\'?',URL_KEY_ADMIN,'=users&id=',$this->post('userid', false),'\');">',($this->post('userid', false)?' <a href="javascript:;" onclick="S.A.W.open(\'?'.URL_KEY_ADMIN.'=users&id='.$this->post('userid', false).'\');">'.DB::one('SELECT login FROM '.DB_PREFIX.'users WHERE id='.$this->post('userid', false)).'</a>':''),'</a><br />';
	if ($this->post('edited', false)) echo lang('$Edited:'),' ',date('H:i / d.m.Y', $this->post('edited', false)),($this->post('editor', false)?' <a href="javascript:;" onclick="S.A.W.open(\'?'.URL_KEY_ADMIN.'=users&id='.$this->post('editor', false).'\');">'.DB::one('SELECT login FROM '.DB_PREFIX.'users WHERE id='.$this->post('editor', false)).'</a>':''),'<br />';
	$statuser = ($this->post('statuser', false) ? $this->post('statuser', false) : $this->post('editor', false));
	if ($this->post('statused', false)) echo lang('$Statused:'),' ',date('H:i / d.m.Y', $this->post('statused', false)),($statuser?' <a href="javascript:;" onclick="S.A.W.open(\'?'.URL_KEY_ADMIN.'=users&id='.$statuser.'\');">'.DB::one('SELECT login FROM '.DB_PREFIX.'users WHERE id='.$statuser).'</a>':''),'</a>';
	?>
	</td>
	<?php endif;?>
	<td class="a-td3">
		<table class="a-save_buttons"><tr>
		<?php if ($this->id):?>
			<?php if (isset($add) && $add):?>
			<td class="a-add">
			<? /*<button type="button" class="a-button a-button_s" onclick="$('#a-w-body2_<?php echo $this->name_id?>').text($('#a-w-body_<?php echo $this->name_id?>').html());S.A.W.save('<?php echo $this->url_save?>&a=add', this.form, this)"><?php echo lang('$Add another')?></button> */?>
			<?php
			$click = str_replace('&'.self::KEY_ACTION.'=save','&'.self::KEY_ACTION.'=add'.(is_string($add)?$add:''),$this->options[$save]);
			$this->inc('button',array('class'=>'a-button a-button_s', 'click' => $click.';S.A.W.close();','text'=>lang('$Save and add another'))); ?>
			</td>
			<?php endif;?>
			<td class="a-save"><?php 
			if (isset($action_id)) $click = str_replace('&'.self::KEY_ACTION.'=save','&'.self::KEY_ACTION.'='.$action_id,$this->options[$save]);
			else $click = $this->options[$save];
			$this->inc('button',array('class'=>'a-button a-button_b', 'click'=>$click,'img'=>(isset($img_id)?$img_id:'oxygen/16x16/actions/document-save.png'),'text'=>lang('$'.(isset($label_id)?$label_id:'Edit')))); ?></td>
		<?php else:?>
			<?php if (isset($add)):?>
			<td class="a-add">
			<?php
			$click = str_replace('&'.self::KEY_ACTION.'=save','&'.self::KEY_ACTION.'=add'.(is_string($add)?$add:''),$this->options[$save]);
			$this->inc('button',array('class'=>'a-button a-button_s', 'click'=>$click,'img'=>false,'text'=>lang('$Add and add another'))); ?>
			</td>
			<?php endif;?>
			<td class="a-save"><?php
			if (isset($action)) $click = str_replace('&'.self::KEY_ACTION.'=save','&'.self::KEY_ACTION.'='.$action,$this->options[$save]);
			else $click = $this->options[$save];
			$this->inc('button',array('class'=>'a-button a-button_b','click'=>$click,'img'=>(isset($img)?$img:'oxygen/16x16/actions/list-add.png'),'text'=>lang('$'.(isset($label)?$label:'Add')))); ?></td>
		<?php endif; ?>
		</tr></table>
	</td>
	</tr></table>
	<input type="hidden" name="get" value="action" />
</div>