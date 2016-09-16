<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' '.$this->module.':').' ';
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
if (!$this->post['sort'] && !$this->id) {
	$this->post['sort']	= DB::one('SELECT MAX(sort) FROM '.$this->prefix.$this->table.' ORDER BY sort DESC')+10;
}

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Description:')?></td><td class="a-r" colspan="2" width="85%"><textarea type="text" class="a-textarea" name="data[descr]" style="width:99%;height:121px;font:12px monospace"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Icon:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:30%" name="data[url]" value="<?php echo $this->post('url')?>" /> Sort: <input type="text" class="a-input" style="width:30%" name="data[sort]" value="<?php echo $this->post('sort')?>" /></td>
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