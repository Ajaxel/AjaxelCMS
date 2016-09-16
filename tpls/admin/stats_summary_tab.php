<?php

/**
* Ajaxel CMS v8.0
* http://ajaxel.com
* =================
* 
* Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* 
* The software, this file and its contents are subject to the Ajaxel CMS
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the source code of Ajaxel CMS.
* 
* @file       tpls/admin/stats_summary_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	S.A.L.ready();
});
</script>
<form method="post" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php $this->inc('search', array('tab'=>$this->tab, 'no_search'=>true, 'date'=>true, 'date_both_required'=>true, 'find_button'=>'Build graph'))?>
	<?php /*<select onchange="S.A.L.get('<?php echo URL::rq('date',$this->url_full)?>&date='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->date_type,$this->array['date_types'])?></select>
	<select onchange="S.A.L.get('<?php echo URL::rq('hit',$this->url_full)?>&hit='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->hit_type,$this->array['hit_types'])?></select>*/?>
	<button type="button" class="a-button a-button_x" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=stats&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_RESET?>',false,'<?php echo $this->tab?>');"><?php echo lang('$Refresh')?></button>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content<?php echo $this->ui['top-s']?>" style="padding:5px;background:#fff;color:#222;height:700px">
	<div class="a-c" style="text-align:center;">
		<a href="javascript:;" _onclick="S.G.download('<?php echo $this->chart['stats_chart']?>')" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=stats&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_RESET?>',false,'<?php echo $this->tab?>');"><img src="<?php echo $this->chart['stats_chart']?>?n=<?php echo $this->time?>" alt="" /></a>
		<br />
		<a href="javascript:;" _onclick="S.G.download('<?php echo $this->chart['stats_chart2']?>')" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=stats&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_RESET?>',false,'<?php echo $this->tab?>');"><img src="<?php echo $this->chart['stats_chart2']?>?n=<?php echo $this->time?>" alt="" /></a>
		<!--<div class="a-c" style="width:90%;margin:0 auto;padding-left:50px;position:relative;z-index:2;top:-40px"><div class="a-l"></div><div class="a-r"><?php echo Conf()->g2('ARR_MONTHS_M',$this->date['current_month_step_month'])?> <?php echo $this->date['current_year_step_month']?></div></div>-->
	</div>
</div>
</form>