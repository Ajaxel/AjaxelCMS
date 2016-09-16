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
* @file       tpls/admin/mods_friends_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, html = '', quiz = 'n/a';
	if (data.length) {
		
		html = '<table class="a-list a-list-one" cellspacing="0">';	
		html += '<tr><th width="8%">&nbsp;</th><th><?php echo lang('$User')?></th><th><?php echo lang('$Connects with')?></th><th><?php echo lang('$Price')?></th><th><?php echo lang('$Status')?></th><th width="5%">&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l"><span class="a-date">'+a.date+'</span></td>';	
			html += '<td class="a-l"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\')">'+a.user+'</a></td>';
			html += '<td class="a-l"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.setid+'\')">'+a.friend+'</a></td>';
			html += '<td class="a-l" nowrap>'+a.price+' '+a.currency+(a.acc_name?' ['+a.acc_name+']':'')+'</td>';
			html += '<td class="a-l" nowrap>'+a.s+'</td>';
			html += '<td class="a-r a-action_buttons" width="20%">';
			/*
			html += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=mods&tab=friends\', {id:\''+a.setid+'|'+a.userid+'\', active:'+(a.confirmed=='Y' ? '1':'0')+'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.confirmed=='Y'?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.confirmed=='Y'?'red':'green')+'.png" /></a>';
			*/
			if (a.confirmed) {
				
				html += '<a href="javascript:;" onclick="if(confirm(\'Are you sure to cancel this friendship?\'))S.A.L.json(\'?<?php echo URL_KEY_ADMIN?>=mods&tab=friends\',{get:\'action\',a:\'confirm\',to:\'\',id:\''+a.id+'\'},true)" title="Cancel friendship?"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/system-restart.png" /></a>';
			} else {
				html += '<a href="javascript:;" onclick="if(confirm(\'Are you sure to confirm this friendship?\')) S.A.L.json(\'?<?php echo URL_KEY_ADMIN?>=mods&tab=friends\',{get:\'action\',a:\'confirm\',to:\'Y\',id:\''+a.id+'\'},true)" title="Apply friendship?"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/dialog-apply.png" /></a>';
				html += '<a href="javascript:;" onclick="S.A.L.json(\'?<?php echo URL_KEY_ADMIN?>=mods&tab=friends\',{get:\'action\',a:\'confirm\',to:\'N\',id:\''+a.id+'\'},true)" title="Deny friendship?"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/dialog-cancel.png" /></a>';
			}
			html += '<a href="javascript:;" onclick="S.A.L.del({id:\''+a.id+'\',title:\'this friend connection\'},this,true)" title="Delete?"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';			
			html += '</tr>';
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No friend connects were found')?></div>';
	}
	
	S.A.L.ready(html);
});
</script>
<div id="a-area">
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('language')?>
	<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php $this->inc('list')?>

</div>