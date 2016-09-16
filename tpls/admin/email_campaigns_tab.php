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
* @file       tpls/admin/email_campaigns_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, j=0, h = '';
	for (i in data) j++;
	if (j) {
		h = '<table class="a-list a-list-one" cellspacing="0">';	
		h += '<tr><th><?php echo lang('$Date')?></th><th width="20%"><?php echo lang('$Campaign')?></th><th width="20%"><?php echo lang('$Group')?></th><th><?php echo lang('$Sent')?></th><th><?php echo lang('$Read')?></th><th><?php echo lang('$Clicked')?></th><th width="5%">&nbsp;</th></tr>';
		for (i in data) {
			a = data[i];
			h += '<tr class="'+(i%2?'':'a-odd')+'">';
			h += '<td class="a-l"><span class="a-date">'+a.date+'</span></td>';
			h += '<td class="a-l"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">'+a.campaign+'</a></td>';
			h += '<td class="a-l">'+(a.groups.replace(/\|/g,', '))+' ('+a.total+')</td>';
			h += '<td class="a-l">'+a.cnt+'</td>';
			h += '<td class="a-l">'+a.read+'</td>';
			h += '<td class="a-l">'+a.clicked+'</td>';
			h += '<td class="a-r a-action_buttons" width="20%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+a.title+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';			
			h += '</tr>';
		}
		h += '</table>';
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No mail found')?></div>';
	}
	$('#a_email_<?php echo $this->tab?>_div').html(h);
	S.A.L.ready();
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('search',array('tab'=>$this->tab))?>
	
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<?php $this->inc('tab_form_top')?>
<div id="a_email_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<?php $this->inc('tab_form_bottom')?>