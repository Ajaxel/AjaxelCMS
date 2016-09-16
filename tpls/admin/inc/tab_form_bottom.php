<?php if ($this->button['save']):?>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'S.F.submit(this.form)','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/save-all.png','text'=>lang('$Save'))); ?>
</div>
<input type="hidden" name="<?php echo self::KEY_FIND?>" value="<?php echo $this->find?>" />
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>
<?php endif;?>