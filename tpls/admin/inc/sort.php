<?php echo lang('$Sort:')?> <select onchange="S.A.L.get('<?php echo Url::rq(self::KEY_SORT,$this->url_full)?>&<?php echo self::KEY_SORT?>='+this.value,false,'<?php echo $this->tab?>')"><option value=""><?php echo Html::buildOptions($this->sort,$this->sortby)?></select>