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
* @file       tpls/admin/orders.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var html = '<table class="a-list a-list-one" cellspacing="0">';	
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l" width="30%"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">['+a.id+'] '+a.added+'</a></td>';
			html += '<td class="a-l" width="30%"><a href="mailto:'+a.email+'" target="_blank" style="font-size:11px">'+a.email+'</a></td>';
			html += '<td class="a-l" width="30%">'+a.s+'</td>';
			html += '<td class="a-l" width="30%">'+a.price+'</td>';
			html += '<td class="a-r a-action_buttons" width="20%">';
			html += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>\', {id:'+a.id+', active:'+(a.active==1)+', title: \''+a.login+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+a.login+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';			
			html += '</tr>';
		}
		html += '</table>';
	} else {
		var html = '<div class="a-not_found"><?php echo lang('$No results were found','orders')?></div>';
	}	
	S.A.L.ready(html);
});
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php $this->inc('list')?>

</div>
<?php $this->inc('bot')?>