<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' product:').' ';
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
$this->title = $title.$this->post('title', false);
$this->width = 700;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Description:')?></td><td class="a-r" colspan="2" width="85%"><textarea type="text" class="a-textarea" name="data[descr]" style="width:99%;height:121px;font:12px monospace"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Price:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:150px" name="data[price]" value="<?php echo $this->post('price')?>" /> &euro;</td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td class="a-td1">

		</td>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?
			if ($this->id):?>
			<?php /*
			<button type="button" class="a-button a-button_s" onclick="S.A.W.save('<?php echo $this->url_save?>&a=copy', this.form, this)"><?php echo lang('$Copy')?></button> */?>
			<button type="button" class="a-button a-button_s" onclick="S.A.W.save('<?php echo $this->url_save?>&a=add', this.form, this)"><?php echo lang('$Add another')?></button>
			</td><td>
			<?php 
			$this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
			<?php else:?>
			<?php $this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
			<?php endif;?>
			</td></tr></table>
		</td>
		<?php if ($this->id):?>
		<td  class="a-td3"><?php if ($this->id):?><?php echo lang('$Last time edited:')?> <?php echo date('d M Y',$this->post('edited', false))?><br /><?php echo lang('$by author:')?> <?php echo $this->post('username')?><?php endif;?>&nbsp;</td>
		<?php endif;?>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>