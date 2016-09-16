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
* @file       tpls/admin/stats_keywords_tab.php
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
	var data = <?php echo $this->json_data?>,a,html;
	if (data.length) {
		html = '<table class="a-list a-list-one a-grid" cellspacing="0">';
		html += '<tr><th width="55%"><?php echo lang('$Keyword')?></th><th><?php echo lang('$Engine')?></th><th><?php echo lang('$Last visit')?></th><th><?php echo lang('$Total')?></th><th>&nbsp;</th></tr>';
		for (i=0;i<data.length;i++){
			a=data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l"><a href="'+a[2]+'" target="_blank">'+a[0]+'</a></td>';
			html += '<td class="a-l">'+a[1]+'</td>';
			html += '<td class="a-l a-c">'+a[3]+'</td>';
			html += '<td class="a-l a-c">'+a[4]+'</td>';
			html += '<td class="a-l  a-action_buttons" width="1%"><a href="javascript:;" onclick="var obj=this;S.G.json(\'?<?php echo URL_KEY_ADMIN?>=stats&tab=keywords&a=delete\', {keyword:\''+S.A.P.js2(a[0])+'\'}, function(){$(obj).parent().parent().remove();});"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" /></a></td>';
			html += '</tr>';	
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No keywords were found')?></div>';
	}
	$('#a_stats_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
<?php echo Index::CDZ?>
</script>
<?php if (!$this->submitted):?>
<form method="post" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php $this->inc('search',array('tab'=>$this->tab))?>
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