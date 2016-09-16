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
* @file       tpls/admin/users.php
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
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l a-c" width="1%">'+a.id+'</td>';
			if (a.main_photo) {
				
				html += '<td class="a-l a-c" width="1%"><div class="a-l a-pic"><div class="a-wrap"><a href="<?php echo FTP_EXT?>files/users/'+a.id+'/th1/'+a.main_photo+'" target="_blank" class="a-thumb" rel="users" title="'+a.login+'"><img src="<?php echo FTP_EXT?>files/users/'+a.id+'/th3/'+a.main_photo+'" class="{w:'+a.main_photo_size[0]+',h:'+a.main_photo_size[1]+',p:\'[/th1/]\'}" /></a></div></div></td>';
				/*
				html += '<td class="a-l a-c" width="1%"><a href="/files/users/'+a.id+'/th1/'+a.main_photo+'" target="_blank" class="a-thumb"><img src="/files/users/'+a.id+'/th3/'+a.main_photo+'" class="{w:'+a.main_photo_size[0]+',h:'+a.main_photo_size[1]+',p:\'[/th1/]\'}" /></a></td>';
				*/
			} else {
				html += '<td class="a-l a-c" width="1%"><div class="a-l a-pic"></div></td>';
				/*
				html += '<td class="a-l a-c" width="1%">&nbsp;</td>';
				*/
			}
			html += '<td class="a-l" width="1%"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/user-'+(a.online?'online':'offline')+'.png" width="16" title="'+(a.online?'<?php echo lang('$User is online')?>':'<?php echo lang('$User is offline')?>')+'" alt="'+(a.online?'<?php echo lang('$User is online')?>':'<?php echo lang('$User is offline')?>')+'" /><br /><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/'+a.status[1]+'" alt="'+a.status[0]+'" title="'+a.status[0]+'" width="16" /></td>';
			html += '<td class="a-l" width="30%"> <a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)" style="font-size:14px">'+a.login+(a.age?' ('+a.age+')':'')+'</a><div class="a-pd">'+a.group_name+'; '+a.class_name+'; '+a.status[0]+'</div><div class="a-pd">'+a.company+'</div></td>';
			html += '<td class="a-l" width="30%"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=mail&compose=true&to='+a.email+'\')" style="font-size:11px">'+a.email+'</a><div class="a-pd">'+a.firstname+' '+a.lastname+'</div>'+(a.country?'<div class="a-pd">'+(a.city ? a.city+', ':'')+a.country+'</div>':'')+'</td>';
			html += '<td class="a-r a-action_buttons" width="20%">';
			<?php if (0 && $this->user_statusid!=Site::ACCOUNT_DELETED):?>
			html += '<a href="?userid_admin='+a.id+'"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/switchuser.png" width="16" /></a>';
			<?php endif;?>
			<?php if (USE_IM && $this->user_statusid!=Site::ACCOUNT_DELETED):?>
			if (a.id!=<?php echo $this->Index->Session->UserID?>) {
				html += '<a href="javascript:;" onclick="S.IM.invite('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/im.png" width="16" /></a>';
			}
			<?php endif;?>
			html += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" width="16" /></a>';
			if (a.id!=<?php echo $this->Index->Session->UserID?>&&a.id!='<?php echo SUPER_ADMIN?>'){
				/*html += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>\', {id:'+a.id+', active:'+(a.active==1)+', title: \''+a.login+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';*/
				<?php if ($this->user_statusid!=Site::ACCOUNT_DELETED):?>
				html += '<a href="javascript:;" onclick="if(confirm(\'<?php echo lang('Are you sure to delete this user:')?> '+a.login+'?\')){S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+a.login+'\'}, this)}"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" width="16" /></a>';
				<?php endif;?>
			}
			html += '</td>';
			html += '</tr>';
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No users were found')?></div>';	
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
		<select class="a-select" onchange="S.A.L.get('?<?php echo URL::rq('user_groupid',$this->referer)?>&user_groupid='+this.value);"><option value="999" style="color:#666"><?php echo lang('$-- user group --')?></option><?php echo Data::getArray('user_groups', $this->user_groupid)?></select>
		&nbsp;<select class="a-select" onchange="S.A.L.get('?<?php echo URL::rq('user_classid',$this->referer)?>&user_classid='+this.value);"><option value="999" style="color:#666"><?php echo lang('$-- user class --')?></option><?php echo Data::getArray('user_classes', $this->user_classid)?></select>&nbsp;
		<?php if ($this->user_online):?>
		<a href="javascript:;" onclick="S.A.L.get('?<?php echo URL::rq('user_groupid',$this->referer)?>&user_online=0')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/user-offline.png" style="position:relative;top:3px;" /></a>
		<?php else:?>
		<a href="javascript:;" onclick="S.A.L.get('?<?php echo URL::rq('user_groupid',$this->referer)?>&user_online=1')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/status/user-online.png" style="position:relative;top:3px;" /></a>
		<?php endif;?>
		&nbsp;<select class="a-select" onchange="S.A.L.get('?<?php echo URL::rq('user_statusid',URL::get())?>&user_statusid='+this.value);"><option value="999" style="color:#666"><?php echo lang('$-- user status --')?></option><?php echo Data::getArray('user_statuses', $this->user_statusid)?></select>
		<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php $this->inc('list')?>
<?php $this->inc('bot')?>
</div>