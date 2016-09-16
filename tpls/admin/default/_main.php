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
* @file       tpls/admin/default/_main.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?>
<script type="text/javascript">
<?php echo Index::CDA?>
$().ready(function(){
	<?php $this->inc('js_load')?>
	var data=<?php echo json($this->data)?>;
	var html = '<table class="a-stats" cellspacing="0" cellpadding="0" width="100%"><tr><th width="20%">&nbsp;</th><th width="20%"><?php echo lang('$Today')?></th><th width="20%"><?php echo lang('$Yesterday')?></th><th width="20%"><?php echo lang('$Total')?></th><th width="20%"><?php echo lang('$Average per day')?></th></tr>';
	for (i in data.labels) {
		if (!data.names || !data.total[i]) continue;
		html += '<tr><td class="a-l">'+(data.names[i]?'<a href="javascript:;" onclick="S.A.L.chart_main(\''+data.names[i]+'\')">':'')+data.labels[i]+(data.names[i]?'</a>':'')+((data.extra&&data.extra[i])?data.extra[i]:'')+':</td><td class="a-r">'+data.today[i]+'</td><td class="a-r">'+data.yesterday[i]+'</td><td class="a-r a-total"'+(data.factors[i]?' title="<?php echo lang('$Fraction')?>: '+(data.names[i]=='duration'?(data.factors[i]/60)+' minutes':data.factors[i])+'"':'')+' style="color:#396081">'+data.total[i]+'</td><td class="a-r" style="color:#396081">'+data.avg[i]+'</td></tr>';	/*'DW bug, apos added*/
	}
	html += '<tr><td colspan="5" class="a-search a-l"><?php echo lang('$Site statistics begin:')?> <?php if ($this->data['start']):?> <?php echo date('H:i d M y', $this->data['start'])?> (<?php echo lang('$%1 days', $this->data['days'])?>) <?php echo lang('$Unique visitors: %1', $this->data['unique'])?>, <?php echo lang('$Total visits: %1', $this->data['hits'])?><? else:?> <?php echo lang('$today')?><?php endif;?><div class="a-r"><a href="http://ajaxel.com">Ajaxel CMS v<?php echo Site::VERSION?></a></td></tr>';
	html += '</table>';
	$('#a-google_find_hide').html(html);
	S.A.L.ready();
});
<?php echo Index::CDZ?>
</script>
<div id="a-area">
	<?php $this->inc('top', array('_right'=>'<a href="http://ajaxel.com" target="_blank"><img style="margin-top:5px;margin-right:2px" src="<?php echo FTP_EXT?>tpls/img/ajaxel_logo.png" alt="Ajaxel logo" /></a>'))?>
	<div class="a-content<?php echo $this->ui['top-s']?>" id="<?php echo $this->name?>-content" style="background:#fff;color:#222">
		<form method="POST" id="<?php echo $this->name?>-search">
			<div class="a-search"><div class="a-l"><?php echo $this->inc('db')?> <?php echo $this->inc('template')?> <?php echo $this->inc('language')?> <?php echo $this->inc('currency')?></div><div class="a-r"><button type="button" class="a-button" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>&<?php echo self::KEY_RESET?>');"><?php echo lang('$Refresh')?></button></div></div>
			<?php /*<div class="a-search"><div class="a-l"><?php echo $this->inc('google_search', array('google_find'=>'google_find'))?></div></div>*/?>
		</form>
		<div id="a-google_find_show" style="display:none"></div>
		<div id="a-google_find_hide"></div>
	</div>
	<div id="a-main_chart_div"></div>
</div>