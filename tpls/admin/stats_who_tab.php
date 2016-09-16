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
* @file       tpls/admin/stats_who_tab.php
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
	var html = '<table class="a-list a-grid a-list-one" cellspacing="0">';
	html += '<tr><th width="14%"><?php echo lang('$Date/Time')?></th><th width="37%"><?php echo lang('$IP/Referer')?></th><th title="<?php echo lang('$Logged user')?>" width="2%">L</th><th title="<?php echo lang('$Refered by')?>" width="1%"><?php echo lang('$R')?></th><th><?php echo lang('$Time')?></th><th width="1%" title="<?php echo lang('$Clicks made')?>">C</th><th width="1%" title="<?php echo lang('$User agent')?>"><?php echo lang('$U')?></th><th width="1%" title="<?php echo lang('$Browser')?>">B</th><th width="1%" title="<?php echo lang('$Operating system')?>">OS</th><th width="1%" title="<?php echo lang('$Device')?>"><?php echo lang('$D')?></th><th colspan="2"><?php echo lang('$Location')?></th><th>&nbsp;</th></tr>';
	for (i=0;i<data.length;i++){
		a=data[i];
		html += '<tr class="'+(i%2?'':'a-odd')+'">';
		html += '<td class="a-l" nowrap'+(a.width>0?' title="'+a.width+'x'+a.height+'"':'')+'><span class="a-date">'+a.date+'</span></td>';
		html += '<td class="a-l">'+a.host+'</td>';		
		html += '<td class="a-l a-c">'+(a.userid>0?'<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\')" title="'+a.user+'" style="color:green!important">'+a.userid+'</a>':'&nbsp;')+'</td>';
		html += '<td class="a-l">'+(a.refered>0?' <a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.refered+'\')" title="" style="color:red!important">'+a.refered+'</a>':'&nbsp;')+'</td>';
		html += '<td class="a-l a-c">'+a.dur+'</td>';
		html += '<td class="a-l a-c">'+a.clicks+'</td>';
		html += '<td class="a-l a-c">'+(a.ua>0?'<a href="javascript:;" style="color:#888!important" onclick="S.G.alert(\'<span style=&quot;font-size:12px&quot;>'+S.A.P.js(a.ua_name)+'</span>\',\'User agent ID: '+a.ua+'\')" title="'+S.A.P.js2(a.ua_name)+'">'+a.ua+'</a>':'&nbsp;')+'</td>';
		html += '<td class="a-l a-c">'+(a.browser?'<img src="<?php echo FTP_EXT?>tpls/img/browsers/'+a.browser+'.png" alt="'+a.br+'" title="'+a.br+'" width="16" />':'&nbsp;')+'</td>';
		html += '<td class="a-l a-c">'+(a.os?'<img width="14" src="<?php echo FTP_EXT?>tpls/img/os/'+a.os+'.png" width="16" />':'&nbsp;')+'</td>';
		html += '<td class="a-l a-c"><img width="16" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/devices/'+(a.device=='mobile'?'phone':(a.device=='tablet'?'input-tablet':'computer-laptop'))+'.png" width="16" /></td>';
		html += '<td class="a-l" width="1%">'+(a.code?'<img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+(a.code=='un'?'_United Nations':a.code)+'.png" alt="'+a.country+'" title="'+a.country+'" width="16" />':'&nbsp;')+'</td>';
		html += '<td class="a-l">'+a.city+'&nbsp;</td>';
		html += '<td class="a-l  a-action_buttons" width="1%" nowrap>';
		if (a.act) {
			if (a.blocked) html += '<a href="javascript:;" title="<?php echo lang('$This IP is blocked, unblock?')?>" onclick="S.A.L.unblock(\''+a.ip+'\', this, 2)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/security-low.png" width="16" /></a>';
			else html += '<a href="javascript:;" title="<?php echo lang('$Block this ip?')?>" onclick="S.A.L.block(\''+a.ip+'\', this, 2)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/security-high.png" width="16" /></a>';
			html += '<a href="javascript:;" title="<?php echo lang('$Delete this visit')?>" onclick="S.A.L.del_stat(\''+a.id+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" width="16" /></a>';
		}
		html += '</td>';
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
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_SORT,$this->url_full)?>&<?php echo self::KEY_SORT?>='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->sort,$this->array['sort'])?></select>
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_BY,$this->url_full)?>&<?php echo self::KEY_BY?>='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->by,$this->array['by'])?></select>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_stats_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>