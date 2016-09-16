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
* @file       tpls/admin/stats_clicks_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,a,h;
	if (data.length) {
		h = '<table class="a-list a-list-one a-grid" cellspacing="0">';
		<?php
		switch ($this->click_type) {
			case 'popular':
			case 'continuous':
			case 'visited':
				?>
				h += '<tr><th><?php echo lang('$Location')?></th><th><?php echo lang('$Min click')?></th><th><?php echo lang('$AVG click')?></th><th><?php echo lang('$Visits')?></th><th><?php echo lang('$Duration')?></th><th width="10%" nowrap><?php echo lang('$AVG Duration')?></th><th width="10%" nowrap><?php echo lang('$Last visit')?></th></tr>';
				<?php
			break;
			default:
				?>
				h += '<tr><th width="10%" nowrap><?php echo lang('$Clicked')?></th><th><?php echo lang('$Location')?></th><th><?php echo lang('$User')?></th><th><?php echo ($this->click_type=='date_user'?lang('$Clicks'):lang('$Click (step)'))?></th><th width="10%" nowrap><?php echo lang('$Duration')?></th></tr>';
				<?php
			break;
		}
		?>
		for (i=0;i<data.length;i++){
			a=data[i];
			
			<?php
			switch ($this->click_type) {
				case 'popular':
				case 'continuous':
				case 'visited':					
					?>
					h += '<tr class="'+(i%2?'':'a-odd')+'">';
					h += '<td class="a-l">'+a[0]+'</td/>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_click\',\''+a[2]+'\')">'+a[1]+'</a></td>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_click_avg\',\''+a[2]+'\')">'+a[6]+'</a></td>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_cnt\',\''+a[2]+'\')">'+a[3]+'</a></td>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_duration\',\''+a[2]+'\')">'+a[4]+'</a></td>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_duration_avg\',\''+a[2]+'\')">'+a[7]+'</a></td>';
					h += '<td class="a-l a-c"><span class="a-date">'+a[5]+'</span></td>';
					h += '</tr>';
					<?php
				break;
				default:
					?>
					h += '<tr class="'+(i%2?'':'a-odd')+'">';
					h += '<td class="a-l a-c"><span class="a-date">'+a[0]+'</span></td>';
					h += '<td class="a-l">'+a[1]+'</td>';
					h += '<td class="a-l">'+(a[2][0]>0?'<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a[2][0]+'\')" style="color:green!important">'+a[2][1]+'</a>':a[2][1])+'</td>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_click\',\''+a[5]+'\')">'+a[3]+'</a></td>';
					h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.L.chart_win(\'c_duration\',\''+a[5]+'\')">'+a[4]+'</a></td>';
					h += '</tr>';
					<?php
				break;
			}
			
			?>
		}
		h += '</table>';
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No results were found','clicks')?></div>';
	}
	$('#a_stats_<?php echo $this->tab?>_div').html(h);
	S.A.L.ready();
});
</script>
<?php if (!$this->submitted):?>
<form method="post" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
		<?php $this->inc('template', array('tab'=>$this->tab))?>
		<select onchange="S.A.L.get('?<?php echo URL::rq('click',$this->referer)?>&click='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->click_type,$this->array['click_types'])?></select>
		<select onchange="S.A.L.get('?<?php echo URL::rq('time',$this->referer)?>&time='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->time_type,$this->array['time_types'])?></select>
	</div>
</div>
<div class="a-search">
	<div class="a-l">
		<?php $this->inc('search',array('tab'=>$this->tab,'width'=>250,'andor'=>true,'date'=>true))?>
		<button class="a-button" type="button" onclick="S.A.L.chart_build('<?php echo $this->name?>-inp_search<?php echo ($this->tab?'_'.$this->tab:'')?>')"><?php echo lang('$Compare')?></button>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<?php $this->inc('tab_form_top')?>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<?php $this->inc('tab_form_bottom')?>