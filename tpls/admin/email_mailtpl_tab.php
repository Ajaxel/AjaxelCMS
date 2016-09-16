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
* @file       tpls/admin/email_mailtpl_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, j=0, html = '';
	for (i in data) j++;
	if (j) {
		html = '<table class="a-list a-list-one" cellspacing="0">';	
		html += '<tr><th width="10%"><?php echo lang('$Sent')?></th><th width="68%"><?php echo lang('$Name')?></th><th width="5%">&nbsp;</th></tr>';
		for (i in data) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'" id="a-mail_'+a.id+'">';
			html += '<td class="a-l" nowrap><span class="a-date">'+a.date+'</span></td>';
			html += '<td class="a-l"><a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/'+(a.type=='F'?'kdeprint-testprinter':'edit-select-all')+'.png" alt="" /> '+a.name+'</a></td>';
			html += '<td class="a-r a-action_buttons" width="20%">';
			html += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+a.title+'\', tab: \'<?php echo $this->tab?>\'}, this, false, function(){$(\'#a-mail_'+a.id+'\').remove()})"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';			
			html += '</tr>';
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No mail found')?></div>';
	}
	$('#a_email_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('search',array('tab'=>$this->tab))?>
	<button type="button" class="a-button" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&add=Q')"><?php echo lang('$Add Plain..')?></button>
	<button type="button" class="a-button" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&add=F')"><?php echo lang('$Add HTML..')?></button>
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