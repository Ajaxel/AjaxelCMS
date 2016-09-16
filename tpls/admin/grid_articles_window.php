<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' article:').' ';
$this->title = $title.$this->post('title', false);
$this->width = 850;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strJS($title)?>'+this.value);
		});
		<?php $this->inc('js_editors', array('descr'=>40,'body'=>280))?>
		S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','main_photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image files');
	}
	<?php $this->inc('js_setphoto')?>
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form ui-corner-all" cellspacing="0">
		<tr>
			<td class="a-l" width="15%">Title</td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
		<?php
		$params = array(
			'table'		=> 'category_'.$this->module,
			'lang'		=> $this->lang,
			'catalogue'	=> false,
			'retCount'	=> false,
			'getAfter'	=> false,
			'noDisable'	=> true,
			'maxLevel'	=> 0,
			'optValue'	=> 'catref',
			'getHidden'	=> true,
			'selected'	=> $this->post('catref', false)
		);
		$cats = Factory::call('category', $params)->getAll()->toOptions();
		
		if ($cats):?>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="2" width="85%">
				<select name="data[catref]" class="a-select">
					<option value="0"><?php echo lang('$-- No category --')?></option>
					<?php echo $cats?>
				</select>
			</td>
		</tr>
		<?php endif;?>
		<tr valign="top">
			<td class="a-l" width="15%"><?php echo lang('$Image:')?><br /><?php echo lang('$Teaser:')?> <br /><?php echo lang('$Text:')?></td><td class="a-r" width="15%">
				<?php $this->inc('main_photo')?>
			</td>
			<td class="a-r" width="70%"><textarea type="text" class="a-textarea" id="a-w-teaser_<?php echo $this->name_id?>" style="width:98%;height:60px" name="data[descr]"><?php echo $this->post('descr')?></textarea></td>
		</tr>
		</table>
		<table class="a-form ui-corner-all" cellspacing="0">
		<tr><td class="a-r" colspan="3">
			<textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:320px;visibility:hidden"><?php echo $this->post('body')?></textarea><textarea name="data[body]" id="a-w-body2_<?php echo $this->name_id?>" style="display:none"></textarea>
		</td></tr>
		<tr>
			<td class="a-l" width="15%">Tags</td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:79%" name="data[tags]" value="<?php echo $this->post('tags')?>" />
				<label><input type="checkbox" name="data[no_comment]" value="Y"<?php echo ($this->post('no_comment')=='Y'?' checked':'')?> /> No comment</label>
				<input type="hidden" name="data[checkboxes][]" value="no_comment" />
			</td>
		</tr>
	</table>
	<?php $this->inc('bottom', array(
	'save'	=> 'content_save'
	))?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>