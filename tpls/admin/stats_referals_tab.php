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
* @file       tpls/admin/stats_referals_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,a,html;
	if (data.length) {
		h = '<table class="a-list a-grid a-list-one" cellspacing="0">';
	<?php 
	switch ($this->group_type):
		case 'all':
	?>
	h += '<tr><th width="15%"><?php echo $this->array['group_type'][$this->group_type]?></th><th width="15%"><?php echo lang('$Refered by')?></th><th><?php echo lang('$Domain')?></th><th><?php echo lang('$Page')?></th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		h += '<tr class="'+(i%2?'':'a-odd')+'">';
		h += '<td class="a-l" nowrap><span class="a-date">'+a[0]+'</span></td>';
		h += '<td class="a-l"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a[1]+'\')">'+a[2]+'</a></td>';
		h += '<td class="a-l">'+a[3]+'</td>';
		h += '<td class="a-l">'+a[4]+'</td>';
		h += '</tr>';	
	}
	<?
		break;
		case 'domain':
	?>
	h += '<tr><th width="25%"><?php echo $this->array['group_type'][$this->group_type]?></th><th width="2%" title="<?php echo lang('$Total incomes')?>">I</th><th><?php echo lang('$Refering period')?></th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		h += '<tr class="'+(i%2?'':'a-odd')+'">';
		h += '<td class="a-l">'+a[0]+'</td>';
		h += '<td class="a-r">'+a[4]+'</td>';
		h += '<td class="a-l"><span class="a-date">'+a[1]+' - '+a[2]+'</span> <span class="a-cnt">'+a[3]+'</span></td>';
		h += '</tr>';	
	}
	<?
		break;
		case 'month':
		case 'day':
	?>
	h += '<tr><th width="25%"><?php echo $this->array['group_type'][$this->group_type]?></th><th width="2%" title="<?php echo lang('$Total incomes')?>">I</th><th width="2%" title="<?php echo lang('$Total registrations')?>">R</th><th width="2%" title="<?php echo lang('$Total domains')?>">D</th><th width="2%" title="<?php echo lang('$Total users')?>">U</th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		h += '<tr class="'+(i%2?'':'a-odd')+'">';
		h += '<td class="a-l"><span class="a-date">'+a[0]+'</span></td>';
		h += '<td class="a-r">'+a[1]+'</td>';
		h += '<td class="a-r">'+a[4]+'</td>';
		h += '<td class="a-r">'+a[2]+'</td>';
		h += '<td class="a-r">'+a[3]+'</td>';
		h += '</tr>';	
	}
	<?
		break;
		case 'user':
		case 'user2':
	?>
	h += '<tr><th width="35%"><?php echo $this->array['group_type'][$this->group_type]?></th><th width="2%" title="<?php echo lang('$Incomes per day')?>">%</th><th width="2%" title="<?php echo lang('$Total incomes')?>">I</th><th width="2%" title="<?php echo lang('$Total registrations')?>">R</th><th width="2%" title="<?php echo lang('$Total domains')?>">D</th><th width="2%" title="<?php echo lang('$Total pages')?>">P</th><th><?php echo lang('$Refering period')?></th><th><?php echo lang('$Domain')?></th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		h += '<tr class="'+(i%2?'':'a-odd')+'">';
		h += '<td class="a-l" nowrap><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a[8]+'\')">'+a[0]+'</a></td>';
		h += '<td class="a-r">'+a[10]+'</td>';
		h += '<td class="a-r">'+a[1]+'</td>';
		h += '<td class="a-r">'+a[9]+'</td>';
		h += '<td class="a-r">'+a[2]+'</td>';
		h += '<td class="a-r">'+a[3]+'</td>';
		h += '<td class="a-l"><span class="a-date">'+a[4]+' - '+a[5]+'</span> <span class="a-cnt">'+a[6]+'</span></td>';
		h += '<td class="a-l">'+a[7]+'</td>';
		h += '</tr>';	
	}
	<?
		break;
	endswitch;
	?>
		h += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No referals were found')?></div>';
	}
	$('#a_stats_<?php echo $this->tab?>_div').html(h);
	S.A.L.ready();
});
</script>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_SORT,$this->url_full)?>&<?php echo self::KEY_SORT?>='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->group_type,$this->array['group_type'])?></select>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>