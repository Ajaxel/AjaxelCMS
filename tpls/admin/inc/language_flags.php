<?php if (count($this->langs)>1):?>
<?php echo lang('$Selected language:')?> <img src="<?php echo FTP_EXT?>tpls/img/flags/24/<?php echo $this->lang?>.png" alt="" />
<select name="data[lang]" onchange="S.A.W.go('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&id=<?php echo $this->id?>&o=<?php echo $this->conid?>&l='+this.value, 'window_<?php echo $this->name_id?>', false, this)">
	<?php echo Html::buildOptions($this->lang,array_label($this->langs));?>
</select>
<?php else:?>
<input type="hidden" name="data[lang]" value="<?php echo $this->lang?>" />
<?php endif;?>