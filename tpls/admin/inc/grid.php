<?php echo lang('$Grid:')?> <select onchange="S.A.L.get('?<?php echo URL::get(self::KEY_LOAD, self::KEY_MODULE)?>&<?php echo self::KEY_MODULE?>='+this.value);">
	<?php echo Html::buildOptions($this->module,array_label($this->grid_modules, 'title'));?>
</select>
