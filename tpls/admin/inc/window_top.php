<?php if ($this->pop):?>
<div class="a-abs a-window-wrapper ui-dialog ui-widget ui-widget-content ui-corner-all ui-draggable" id="window_<?php echo $this->name_id?>" style="width:<?php echo $this->width?>px" width="<?php echo $this->width?>">
	<div class="a-window-top ui-dialog-titlebar ui-widget-header ui-corner-top ui-helper-clearfix" id="a-window-top_<?php echo $this->name_id?>">
		<div class="a-window-title"><span class="ui-dialog-title"><?php echo $this->title?></span></div>
		<?php if (!$this->pop):?>
		<div class="a-window-buttons">
			<? /*<a href="javascript:;" onclick="S.A.W.pop('window_<?php echo $this->name_id?>');" class="a-window_pop"></a>*/?>
			<a href="javascript:;" onclick="S.A.W.dock(this,'window_<?php echo $this->name_id?>');" class="a-window_min"></a>
			<a href="javascript:;" onclick="S.A.W.maximize('window_<?php echo $this->name_id?>')" class="a-window_resize"></a>
			<a href="javascript:;" onclick="S.A.W.close('window_<?php echo $this->name_id?>')" class="a-window_close"></a>
		</div>
		<?php endif;?>
	</div>
	<div class="a-abs a-window-contents ui-dialog-content ui-widget-content">
<?php else:?>
<div style="height:0px;overflow:hidden"><window><?php echo $this->name_id?>|<?php echo $this->width?>|<?php echo $this->title?></window></div>
<?php endif;?>