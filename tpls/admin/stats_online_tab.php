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
* @file       tpls/admin/stats_online_tab.php
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
	if (data.length) {
		var html = '<table class="a-list a-list-one a-grid" cellspacing="0">';
		html += '<tr><th width="20%"><?php echo lang('$User / IP')?></th><th width="40%"><?php echo lang('$Visiting page')?></th><th><?php echo lang('$Last time clicked')?></th><th colspan="2"><?php echo lang('$Location')?></th><th colspan="2"><?php echo lang('$Info')?></th></tr>';
		for (i=0;i<data.length;i++){
			a=data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l" title="'+a[11]+'">'+a[11]+': '+a[0]+''+(a[10]?' (<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a[9]+'\')">'+a[10]+'</a>)':'')+'</td>';
			html += '<td class="a-l">'+a[1]+'</td>';
			html += '<td class="a-l a-c">'+a[2]+'</td>';
			html += '<td class="a-l a-c" width="1%"><img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+a[3]+'.png" width="16" /></td><td class="a-l">'+a[4]+'</td>';
			html += '<td class="a-l" width="1%">'+(a[5]?'<img src="<?php echo FTP_EXT?>tpls/img/os/'+a[5]+'.png" width="14" title="'+a[6]+'" alt="'+a[6]+'" />':'')+'</td><td width="1%" class="a-l">'+(a[7]?'<img src="<?php echo FTP_EXT?>tpls/img/browsers/'+a[7]+'.png" alt="'+a[8]+'" title="'+a[8]+'" width="14" />':'')+'</td>';
			html += '</tr>';	
		}
		html += '</table>';
	} else {
		var html = '<div class="a-not_found"><?php echo lang('$No results were found','online')?></div>';
	}	
	$('#a_stats_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div class="a-search">
	<div class="a-l">
		<?php $this->inc('template')?>
		<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>