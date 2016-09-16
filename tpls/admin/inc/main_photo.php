<table celllspacing="0"><tr>
	<td><div class="a-l a-main_photo" id="a-w-main_photo_<?php echo $this->name_id?>"><?
	if ($this->post('main_photo_preview', false)):?>
	<a href="<?php echo $this->post('main_photo_preview')?>" target="_blank" class="a-thumb"><img src="<?php echo $this->post('main_photo_thumb')?>?nocache=<?php echo $this->time?>" alt="<?php echo lang('$Description image')?>" /></a>
	<?php endif;?>
	</div></td>
	<td>
		<div class="a-file_btn"><input type="file" class="a-file" id="a-main_photo_<?php echo $this->name_id?>" /></div>
		<div id="a-w-main_photo_del_<?php echo $this->name_id?>" <?php if (!$this->post('main_photo', false)):?>style="display:none"<?php endif;?>><a href="javascript:;" onclick="S.A.W.delPhoto('<?php echo $this->name_id?>',0,function(response){window_<?php echo $this->name_id?>.setPhoto(response.substring(2))});">[<?php echo lang('$delete')?>]</a></div>
	</td>
</tr></table>