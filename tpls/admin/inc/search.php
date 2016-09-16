<script type="text/javascript">
$().ready(function(){
	$('#<?php echo $this->name?>-search<?php echo ($this->tab?'_'.$this->tab:'')?>').submit(function(){
		S.A.L.find('<?php echo $this->name?>','<?php echo $this->name?>-find<?php echo ($this->tab?'_'.$this->tab:'')?>','<?php echo ($this->tab?$this->tab:'')?>',true);
		return false;
	})/*.find('#<?php echo $this->name?>-find<?php echo ($this->tab?'_'.$this->tab:'')?>').focus()*/;
});
</script>
<?php if (!isset($no_search)):?>
<?php echo lang('$Search:')?> <input type="text"<?php echo (isset($width)?' style="width:'.$width.'px"':'')?> placeholder="<?php echo lang('$Keyword..')?>" value="<?php echo stripslashes(strform($this->find))?>" id="<?php echo $this->name?>-find<?php echo ($this->tab?'_'.$this->tab:'')?>" onkeyup="if(S.E.keyCode==13){$('#<?php echo $this->name?>-search<?php echo ($this->tab?'_'.$this->tab:'')?>').submit();S.E.keyCode=0;}"<?php /* onkeydown="S.A.L.find('<?php echo $this->name?>',this'<?php echo (isset($tab)?',\''.$tab.'\'':'')?>);"*/?> />
<?php if (isset($andor)):?>
<select id="<?php echo $this->name?>-andor_search<?php echo ($this->tab?'_'.$this->tab:'')?>"><?php echo Html::buildOptions($this->andor,$this->array['andor_types'])?></select>
<?php endif;?>
<?php endif;?>
<?php if (isset($date)):?>
<?php echo lang('$Date:')?> 
<input type="text" class="a-datepicker<?php if (isset($date_both_required)):?> a-datepicker_from<?php endif;?>" id="<?php echo $this->name?>-<?php echo self::KEY_DATE_FROM?>_search<?php echo ($this->tab?'_'.$this->tab:'')?>" style="width:50px" value="<?php echo $this->date_from?>" /> -
 <input type="text" class="a-datepicker <?php if (isset($date_both_required)):?> a-datepicker_to<?php endif;?>" id="<?php echo $this->name?>-<?php echo self::KEY_DATE_TO?>_search<?php echo ($this->tab?'_'.$this->tab:'')?>" style="width:50px" value="<?php echo $this->date_to?>" />
<?php endif;?>
<button type="button" class="a-button" onclick="$('#<?php echo $this->name?>-search<?php echo ($this->tab?'_'.$this->tab:'')?>').submit();this.disabled=true"><?php echo lang('$'.(isset($find_button)?$find_button:'Find'))?></button>
<?php if ($this->find || $this->date_from || $this->date_to || post('userid')):?>
	<button type="button" class="a-button" onclick="S.A.L.get('?<?
	echo URL::make(array(
			URL_KEY_ADMIN	=> $this->name,
			self::KEY_MODULE=> $this->module,
			self::KEY_TAB	=> $this->tab
		)).($this->tab && isset($_GET[self::KEY_LOAD])?'&'.self::KEY_LOAD:'');
	?>'<?php echo ($this->tab?',false,\''.$this->tab.'\'':'')?>);this.disabled=true"><?php echo lang('$Reset')?></button>
<?php endif;?>