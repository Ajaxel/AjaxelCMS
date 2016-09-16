<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' news:').' ';
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?>'+this.value);
		});
		<?php $this->inc('js_editors', array('descr'=>400))?>
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->title = $title.$this->post('title', false);
$this->width = 860;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<?php $this->inc('tr_title')?>
	</table>
	<table class="a-form" cellspacing="0"><tr><td class="a-r">
		<textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:120px;visibility:hidden"><?php echo $this->post('descr')?></textarea><textarea name="data[descr]" id="a-w-descr2_<?php echo $this->name_id?>" style="display:none"></textarea>
	</td></tr>
	</table>
	
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save_descr',
		'save'			=> 'content_save',
		'copy'			=> false,
		'add'			=> false
	)); ?>
</form>
<?php $this->inc('window_bottom')?>