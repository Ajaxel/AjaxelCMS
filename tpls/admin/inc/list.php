<?php if ($this->button['save']):?>
<form method="POST" id="<?php echo $this->name?>-form<?php echo ($this->tab?'_'.$this->tab:'')?>" class="ajax_form" action="?<?php echo $this->referer?>">
<?php endif;?>
<?php if ($this->category_options && $this->name!='entries'):?>
<div class="a-search">
	<div class="a-l"<?php if (isset($category_right)):?> style="width:80%"<?php endif;?>>
		<?php echo ($this->class=='AdminCategories'?lang('$Select parent:'):lang('$Category:'))?>
		<select onchange="S.A.L.get('<?php echo Url::rq(array(self::KEY_CATID,self::KEY_MODULE),$this->url_full)?>&<?php echo self::KEY_MODULE?>=<?php echo $this->module?>&<?php echo self::KEY_CATID?>='+this.value,false,'<?php echo $this->tab?>')" class="a-select"<?php if (isset($category_right)):?> style="width:90%"<?php endif;?>>
			<option value="0"><?php echo lang('$-- Top category --')?></option>
			<?php echo $this->category_options?>
		</select>
	</div>
	<?php if (isset($category_right)):?>
	<div class="a-r">
		<?php echo $category_right;?>
	</div>
	<?php endif;?>
</div>
<?php endif;?>
<div class="a-content" id="<?php echo $this->name?>-content">
	<?php $this->inc('loading')?>
</div>
<?php $this->inc('nav')?>
<?php if ($this->button['custom']):?>
<div class="a-buttons<?php echo $this->ui['buttons']?>" id="a-buttons_<?php echo $this->name?>">
	<?php echo $this->button['custom']?>
</div>	
<?php elseif ($this->button['save']):?>
<div class="a-buttons<?php echo $this->ui['buttons']?>" id="a-buttons_<?php echo $this->name?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(this.form).submit();','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/save-all.png','text'=>lang('$Save'))); ?>
</div>
<?php endif;?>
<?php if ($this->button['save']):?>
<input type="hidden" name="<?php echo self::KEY_FIND?>" value="<?php echo strform($this->find)?>" />
<input type="hidden" name="<?php echo $this->name?><?php echo ($this->tab?'_'.$this->tab:'')?>-submitted" value="1" />
</form>
<?php endif;?>
