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
* @file       tpls/admin/email_mail_tab.php
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
		html += '<tr><th width="10%"><?php echo ($this->folder==Mail::FOLDER_SENT?lang('$Sent'):lang('$Date'))?></th><th width="68%"><?php echo lang('$Subject')?></th><th width="15%"><?php echo ($this->folder==Mail::FOLDER_SENT?lang('$To'):lang('$From'))?></th>';
	<?php /*	html += '<th width="5%">&nbsp;</th>';*/?>
		html += '</tr>';
		for (i in data) {
			a = data[i];
			html += '<tr class="a-hov a-row" id="a-mail_'+a.rid+'">';
			<?php /*html += '<td class="a-l"><input type="checkbox" name="mail[]" class="a-checkbox" onclick="S.A.L.C.mail()" value="'+a.id+'" /></td>';*/?>
			html += '<td class="a-l" nowrap><span class="a-date">'+a.date+'</span></td>';
			html += '<td class="a-l a-icon"><a href="javascript:;" onmousedown="S.A.M.edit(\''+a.id+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo URL_KEY_FOLDER?>=<?php echo $this->folder?>\', this)">'<?php if ($this->folder==Mail::FOLDER_SENT):?>+(a.read>0?'<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-read.png" alt="" /> ':'<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-unread.png" width="16" alt="" /> ')<?php else:?>+(a.read>0?'<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail.png" width="16" alt="" /> ':'<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-mark-unread-new.png" alt="" /> ')<?php endif;?>+a.subject+'</a></td>';
			html += '<td class="a-l" nowrap><a href="javascript:;" style="color:#666!important" onmousedown="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.from_id+'\')">'+a.user+'</a></td>';
			<?php /*
			html += '<td class="a-r a-action_buttons" width="20%">';
			html += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo URL_KEY_FOLDER?>=<?php echo $this->folder?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/document-preview.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+a.title+'\', tab: \'<?php echo $this->tab?>\'}, this, false, function(){$(\'#a-mail_'+a.id+'\').remove()})"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';
			*/?>
			html += '</tr>';
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No mail found')?></div>';
	}
	$('#a_email_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
	S.A.L.C.init();
	$('#a_email_<?php echo $this->tab?>_del').button('disable');
	$('#a_email_<?php echo $this->tab?>_move').attr('disabled','true');
});
</script>
<?php if (!$this->submitted): ?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
		<button type="button" class="a-button" onclick="S.A.L.get('?<?php echo URL::rq(URL_KEY_FOLDER, $this->url)?>&<?php echo URL_KEY_FOLDER?>=<?php echo Mail::FOLDER_INBOX?>', false, '<?php echo $this->tab?>')"><?php echo lang('$Check mail')?></button>
		<select onchange="S.A.L.get('?<?php echo URL::rq(URL_KEY_FOLDER, $this->url)?>&<?php echo URL_KEY_FOLDER;?>='+encodeURI(this.value), false, '<?php echo $this->tab?>');this.disabled=true">
			<?php echo $this->folders_options;?>
		</select>
		<?php $this->inc('search',array('tab'=>$this->tab))?>
		<button type="button" class="a-button" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&compose=true')"><?php echo lang('$Compose..')?></button>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<?php $this->inc('tab_form_top')?>
<div class="a-search" id="a_email_<?php echo $this->tab?>_act">
	<div class="a-l">
	
	</div>
	<div class="a-r">
		<?php if ($this->folder!=Mail::FOLDER_SENT):?><?php echo lang('$Move to:')?> <select id="a_email_<?php echo $this->tab?>_move" onchange="S.A.L.C.move(this)"><option value=""></option><?php echo Factory::call('mail')->getFolders(true,$this->folder)->setSelected('')->toOptions();?><option value="<?php echo self::KEY_NEW?>"><?php echo lang('$New folder')?></option></select><?php endif;?>
		<button type="button" class="a-button" id="a_email_<?php echo $this->tab?>_del" onclick="S.A.L.C.del(false,<?php echo e($this->folder)?>)"><?php echo lang('$Delete..')?></button>
	</div>
</div>
<div id="a_email_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<?php $this->inc('tab_form_bottom')?>