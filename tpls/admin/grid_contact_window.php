<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' contact:').' ';
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
			<td class="a-l" width="15%"><?php echo lang('$Email:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" name="data[email]" style="width:99%;" value="<?php echo $this->post('email')?>"/></td>
			<td class="a-l" width="15%"><?php echo lang('$Phone:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" name="data[phone]" style="width:99%;" value="<?php echo $this->post('phone')?>"/></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" name="data[title]" style="width:99%;" value="<?php echo $this->post('title')?>"/></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Message:')?></td><td class="a-r" colspan="3" width="85%"><?php echo $this->post['descr']?></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$IP/URL:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input" name="data[url]" style="width:70%;" value="<?php echo $this->post('url')?>" /></td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td  class="a-td1"><?php if ($this->id):?><?php echo date('H:i d M Y',$this->post('added', false))?><br /><?php if ($this->post('username', false)) {echo lang('$by author:')?> <?php echo $this->post('username')?><?php } endif;?>&nbsp;</td>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?
			if ($this->id):?>
			<?php 
			$this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
			<?php else:?>
			<?php $this->inc('button',array('click'=>'S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
			<?php endif;?>
			</td></tr></table>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>