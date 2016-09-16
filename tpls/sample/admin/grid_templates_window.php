<?php

$title = lang('$'.($this->id ? 'Edit':'Add new').' '.$this->module.':').' ';
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		<?php $this->inc('js_editors', array('body'=>220))?>
		S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','main_photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','<?php echo lang('_$Image files')?>');
	}
	<?php $this->inc('js_setphoto')?>
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
			<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
		<tr>
			<td class="a-l">Picture</td>
			<td class="a-r">
				<?php $this->inc('main_photo')?>
			</td>
			<td class="a-r" width="70%" colspan="2"><textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:98%;height:60px" name="data[descr]"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Dated:')?></td><td class="a-r"><input type="text" class="a-input a-date" style="width:110px" name="data[dated]" value="<?php echo $this->post('dated')?>" /></td>
			<td class="a-l"><?php echo lang('$File:')?></td><td class="a-r"><input type="text" class="a-input" style="width:90%" name="data[url]" value="<?php echo $this->post('url')?>" /></td>
		</tr>
		</table>
		<table class="a-form" cellspacing="0">
		<tr>
			<td colspan="4"><textarea type="text" class="a-textarea" name="data[body]" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:121px;font:12px monospace"><?php echo $this->post('body')?></textarea></td>
		</tr>
	</table>
	<?php $this->inc('bottom', array(
		'save'	=> 'content_save',
		'lang'	=> 'content_lang_save',
		'add'	=> true
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>