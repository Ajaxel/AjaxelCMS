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
* @file       tpls/admin/comments.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var html = '<table class="a-list a-list-one" cellspacing="0">';	
		html += '<tr>';
		html += '<th><?php echo lang('$Date / Time')?></th>';
		html += '<th><?php echo lang('$Subject')?></th>';
		html += '<th><?php echo lang('$Name / Email')?></th>';	
		html += '<th>&nbsp;</th>';	
		html += '</tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+' -a-hov"<? /* onclick="if(!$(this).next().find(\'.a-next\').is(\':visible\')){$(\'.a-next\').hide();$(\'#a-comment_<?php echo $this->name?>_'+a.id+'\').next().show()}" _onclick="if(!$(this).next().find(\'.a-comment\').is(\':visible\')){$(\'.a-next\').slideUp(\'fast\');$(\'#a-comment_<?php echo $this->name?>_'+a.id+'\').next().find(\'.a-comment\').slideDown(\'fast\',function(){S.G.scrollTo(this,-150)});}"*/?> id="a-comment_<?php echo $this->name?>_'+a.id+'">';
			html += '<td class="a-l a-small a-nb" width="17%"><span class="a-date">'+a.added+'</span></td>';
			html += '<td class="a-l a-nb" width="53%"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">'+a.subject+'</a>'+(a.url?'<br /><a href="'+a.url+'" target="_blank" style="color:#444!important;font-size:10px"><img src="<?php echo FTP_EXT?>tpls/img/link.gif" alt="" /> ':'')+' '+a.name+(a.url?'</a>':'')+'</td>';
			html += '<td class="a-l a-nb" width="20%">'+(a.email?'<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\');" style="font-size:11px">'+a.user+'</a>':a.user)+'</td>';
			html += '<td class="a-r a-nb a-action_buttons" onclick="return false" width="10%" style="padding-top:6px">';
			html += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)" title="Edit comment"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>\', {id:'+a.id+', active:'+(a.active==1)+', title: \''+a.login+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			if (a.ip) {
				if (a.blocked) html += '<a href="javascript:;" title="<?php echo lang('$This IP is blocked, unblock?')?>" onclick="S.A.L.unblock(\''+a.ip_real+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/security-low.png" /></a>';
				else html += '<a href="javascript:;" title="<?php echo lang('$Block this ip?')?> '+a.ip_real+'" onclick="S.A.L.block(\''+a.ip_real+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/security-high.png" /></a>';
			}
			html += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+a.login+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';			
			html += '</tr>';
			html += '<tr class="'+(i%2?'':'a-odd')+' a-next"<? /* style="'+(i>0?'display:none;':'')+'"*/?>><td colspan="4" class="a-l" style="padding:0"><div class="a-comment" style="padding:4px;font-size:11px">'+a.body+'</div></td></tr>';
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No results were found','comments')?></div>';
	}	
	S.A.L.ready(html);
});
<?php echo Index::CDZ?>
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