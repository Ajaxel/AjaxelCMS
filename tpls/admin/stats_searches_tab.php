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
* @file       tpls/admin/stats_searches_tab.php
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
		<?php if ($this->search_keys):?>
		h += '<tr><th><?php echo $this->search_long?></th><th>Total</th></tr>';
		for (i=0;i<data.length;i++){
			a=data[i];
			h += '<tr class="'+(i%2?'':'a-odd')+'">';
			h += '<td class="a-l"><a href="javascript:;" onclick="S.A.L.chart_win(\'searches\',\''+a[2]+'|'+S.A.P.js2(a[0])+'\')">'+a[0]+'</a></td>';
			h += '<td class="a-r">'+a[1]+'</td>';
			h += '</tr>';
		}
		<?php else:?>
		h += '<tr><th width="10%" nowrap><?php echo lang('$Date')?></th><th><?php echo lang('$Key')?></th><th><?php echo lang('$Value')?></th><th width="5%" nowrap><?php echo lang('$Times')?></th></tr>';
		for (i=0;i<data.length;i++){
			a=data[i];
			h += '<tr class="'+(i%2?'':'a-odd')+'">';
			h += '<td class="a-l a-c"><span class="a-date">'+a[4]+'</span></td>';
			h += '<td class="a-l" width="12%" nowrap>'+a[0]+'</td>';
			h += '<td class="a-l"><a href="javascript:;" onclick="S.A.L.chart_win(\'search\',\''+a[7]+'|'+a[2]+'\')">'+a[2]+'</a></td>';
			h += '<td class="a-l a-c">'+a[5]+'</td>';
			h += '</tr>';	
		}
		<?php endif;?>
		h += '</table>';
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No searches were found')?></div>';
	}
	$('#a_stats_<?php echo $this->tab?>_div').html(h);
	S.A.L.ready();
});
S.A.L.stats_search=function(){
	var keys = $('#a_stats_<?php echo $this->tab?>_skeys .a-checkbox').serialize();
	if (!keys) keys = 'skeys=0';
	url = '?<?php echo URL_KEY_ADMIN?>=stats&tab=<?php echo $this->tab?>&load&'+keys;
	S.A.L.get(url,false,'<?php echo $this->tab?>');
}
</script>
<?php if (!$this->submitted):?>
<form method="post" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<?php
if (Conf()->g('searches')):?>
<div class="a-search" id="a_stats_<?php echo $this->tab?>_skeys">
	<?php
	foreach (Conf()->g('searches') as $k => $a):
	if ($a[2]):
	?>
	<label><input type="checkbox" class="a-checkbox" name="skeys[]" value="<?php echo $k?>" onclick="S.A.L.stats_search()"<?php echo (in_array($k,$this->search_keys)?' checked="checked"':'')?> /> <?php echo $a[2]?></label>
	<?php
	endif;
	endforeach;
	?>
	<?php if ($this->search_keys):?>
	<button type="button" class="a-button" onclick="S.A.L.get('?<?
	echo URL::make(array(
			URL_KEY_ADMIN	=> $this->name,
			self::KEY_TAB	=> $this->tab,
			'skeys'			=> 0
		));
	?>'<?php echo ($this->tab?',false,\''.$this->tab.'\'':'')?>);this.disabled=true"><?php echo lang('$Reset')?></button>
	<?php endif;?>
</div>
<?php endif;?>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php if (!$this->search_keys):?>
	<select onchange="S.A.L.get('?<?php echo URL::rq('skey',$this->referer)?>&skey='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->search_key,$this->array['search_keys'])?></select>
	<?php endif;?>
	<?php $this->inc('search',array('tab'=>$this->tab,'date'=>true))?>
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