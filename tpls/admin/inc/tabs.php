<ul class="a-tabs_ul">
	<?php foreach ($tabs as $k => $label):?>
	<li><a href="#a_<?php echo $k?>_<?php echo $this->name_id?>"><span><?php echo lang('$'.$label)?></span></a></li>
	<?php endforeach;?>
</ul>