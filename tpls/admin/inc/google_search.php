<script type="text/javascript">
$().ready(function(){
	$('#a-<?php echo $google_find?>_all').focus();
	$('#<?php echo $this->name?>-search<?php echo ($this->tab?'_'.$this->tab:'')?>').submit(function(){
		S.A.L.google_find('<?php echo $this->name?>', '<?php echo $google_find?>',S.A.L.google_type);
		return false;
	}).find('#<?php echo $this->name?>-inp_search<?php echo ($this->tab?'_'.$this->tab:'')?>').focus();
});
</script>
<?php echo lang('$Google search:')?> <input type="text"<?php echo (isset($width)?' style="width:'.$width.'px"':'')?> value="<?php echo stripslashes(strform($this->find))?>" id="<?php echo $this->name?>-find<?php echo ($this->tab?'_'.$this->tab:'')?>" style="width:300px" />

<span id="a-<?php echo $google_find?>_all_span"><input type="checkbox" id="a-<?php echo $google_find?>_all" onclick="S.A.L.prev_search=''" class="a-checkbox" checked="checked" /><label for="a-<?php echo $google_find?>_all"><?php echo lang('$Everywhere')?></label></span>
<?php /*
<select name="google_type" onchange="if(this.value=='web')$('#a-<?php echo $google_find?>_all_span').attr('disabled',false);else $('#a-<?php echo $google_find?>_all_span').attr('disabled',true);S.A.L.prev_search=''" id="a-<?php echo $google_find?>_type" class="a-select">
<?php
	$arr = array(
		'web'		=> lang('Web'),
		'images'	=> lang('Images'),
		'video'		=> lang('Videos'),
		'news'		=> lang('$News')
	);
	echo Html::buildOptions(post('google_type'),$arr);
?>
</select>
*/ ?>
<button class="a-button" type="button" onclick="$('#a-<?php echo $google_find?>_all_span').attr('disabled',false);S.A.L.google_find('<?php echo $this->name?>', '<?php echo $google_find?>', true, 'web'); return false;" id="<?php echo $this->name?>-but_search"><?php echo lang('$Web')?></button> <button class="a-button" type="button" onclick="S.A.L.google_find('<?php echo $this->name?>', '<?php echo $google_find?>', true, 'images'); return false;" id="<?php echo $this->name?>-but_search"><?php echo lang('$Images')?></button> <button class="a-button" type="button" onclick="S.A.L.google_find('<?php echo $this->name?>', '<?php echo $google_find?>', true, 'video'); return false;" id="<?php echo $this->name?>-but_search"><?php echo lang('$Video')?></button> <button class="a-button" type="button" onclick="S.A.L.google_find('<?php echo $this->name?>', '<?php echo $google_find?>', true, 'news'); return false;" id="<?php echo $this->name?>-but_search"><?php echo lang('$News')?></button>