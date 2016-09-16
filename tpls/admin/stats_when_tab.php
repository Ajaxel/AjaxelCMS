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
* @file       tpls/admin/stats_when_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,a;
	var html = '<table class="a-list a-list-one a-grid" cellspacing="0">';
	html += '<tr><th width="15%"><?php echo $this->array['date_types'][$this->date_type]?></th><th><?php echo lang('$Hits')?></th><th><?php echo lang('$Unique')?></th><th><?php echo lang('$Clicks')?></th><th><?php echo lang('$Duration')?></th><th><?php echo lang('$Percent')?></th><th width="30%"><?php echo lang('$Graph')?></th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		html += '<tr class="'+(i%2?'':'a-odd')+'">'
		html += '<td class="a-l" nowrap><span class="a-date">'+a.name+'</span></td>';
		html += '<td class="a-l a-c">'+a.hits+'</td>';
		html += '<td class="a-l a-c">'+a.unique_hits+'</td>';
		html += '<td class="a-l a-c">'+a.clicks+'</td>';
		html += '<td class="a-l a-c">'+a.dur+'</td>';
		html += '<td class="a-l a-c">'+a.percent+'</td>';
		html += '<td class="a-l">'+(a.chart?'<img src="<?php echo FTP_EXT?>tpls/img/graphbar/'+a.chart[0]+'.gif" height="10" width="'+a.chart[1]+'%">':'')+'</td>';
		html += '</tr>';	
	}
	html += '</table>';
	$('#a_stats_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
</script>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select title="<?php echo lang('$Period group')?>" onchange="S.A.L.get('<?php echo URL::rq('date',$this->url_full)?>&date='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->date_type,$this->array['date_types'])?></select>
	<select title="<?php echo lang('$Type')?>" onchange="S.A.L.get('<?php echo URL::rq('hit',$this->url_full)?>&hit='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->hit_type,$this->array['hit_types'])?></select>
	<select title="<?php echo lang('$Country')?>" onchange="S.A.L.get('<?php echo URL::rq(self::KEY_COUNTRY,$this->url_full)?>&<?php echo self::KEY_COUNTRY?>='+this.value,false,'<?php echo $this->tab?>')"><option value="<?php echo self::KEY_ALL?>"><?php echo lang('$-- all countries --')?></option><?php echo Html::buildOptions($this->country,$this->array['countries'])?></select>
	<?php if ($this->country && !$this->all['country'] && $this->country!='un'):?>
	<select title="<?php echo lang('$City')?>" onchange="S.A.L.get('<?php echo URL::rq(self::KEY_COUNTRY,$this->url_full)?>&<?php echo self::KEY_COUNTRY?>='+this.value,false,'<?php echo $this->tab?>')"><option value="<?php echo self::KEY_ALL?>"><?php echo lang('$-- all cities --')?></option><?php echo Html::buildOptions($this->city,$this->array['cities'])?></select>
	<?php endif;?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>