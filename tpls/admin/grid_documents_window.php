<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' document:').' ';
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
			<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:50%" name="data[category]" value="<?php echo $this->post('category')?>" /> &lt;&lt; <select onchange="$(this).prev().val(this.value)" style="width:30%" class="a-select"><option value=""></option><?php echo Html::buildOptions($this->post('category', false), DB::getAll('SELECT DISTINCT category FROM '.$this->prefix.$this->table.' ORDER BY category','category'), true)?></select></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Description:')?></td><td class="a-r" colspan="2" width="85%"><textarea type="text" class="a-textarea" name="data[descr]" style="width:99%;height:121px;font:12px monospace"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$File:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:99%" name="data[url]" value="<?php echo $this->post('url')?>" /></td>
		</tr>
	</table>
	<?php $this->inc('bottom', array(
		'save'			=> 'content_save',
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>