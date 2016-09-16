<div class="a-content_wrap<?php echo $this->ui['top']?>">
	<div class="a-h1<?php echo $this->ui['h1']?>">
		<div class="a-l" style="font-size:120%">
			<?php echo lang('$'.$this->title)?>
		</div>
		<div class="a-r">
			<a href="javascript:;" onclick="S.A.W.close()" id="a-window_closeall" style="display:none;font-size:9px;margin:0 auto"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/22x22/actions/window-suppressed.png" alt="<?php echo lang('$Close all windows')?>" style="position:relative;top:3px" title="Close all open and docked windows" /></a>
			<?php if ($this->button['add']):?>
				<button type="button" onclick="S.A.M.add(this)" class="a-button"><table><tr><td><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" alt="" /></td><td><?php echo lang('$Add')?></td></tr></table></button>
			<?php endif;?>
			<?php echo (isset($right)?$right:'');?>
		</div>
	</div>