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
* @file       tpls/admin/stats_how_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,a;
	var html = '<table class="a-list a-grid a-list-one" cellspacing="0">';
	html += '<tr><th width="1%">&nbsp;</th><th width="75%"><?php echo $this->array['browser_type'][$this->browser_type]?></th><th><?php echo lang('$Hits')?></th><th><?php echo lang('$Unique')?></th><th><?php echo lang('$Percent')?></th><th width="40%"><?php echo lang('$Graph')?></th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		html += '<tr class="'+(i%2?'':'a-odd')+'">';
		<?php if ($this->browser_type=='ua'):?>
		html += '<td class="a-l" colspan="2">'+(a[1]?a[1]:'unknown')+'</td>';
		<?php else:?>
		html += '<td class="a-l">'+a[0]+'</td><td class="a-l" nowrap>'+a[1]+'</td>';
		<?php endif;?>
		html += '<td class="a-l a-r">'+a[2]+'</td>';
		html += '<td class="a-l a-r">'+a[3]+'</td>';
		html += '<td class="a-l a-r">'+a[4][1]+'%</td>';
		html += '<td class="a-l">'+(a[4]?'<img src="<?php echo FTP_EXT?>tpls/img/graphbar/'+a[4][0]+'.gif" height="10" width="'+a[4][1]+'%">':'')+'</td>';
		html += '</tr>';	
	}
	
	html += '</table>';
	$('#a_stats_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
<?php echo Index::CDZ?>
</script>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select onchange="S.A.L.get('<?php echo URL::rq('type',$this->url_full)?>&type='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->browser_type,$this->array['browser_type'])?></select>
	
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_SORT,$this->url_full)?>&<?php echo self::KEY_SORT?>='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->when_type,$this->array['when_type'])?></select>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>