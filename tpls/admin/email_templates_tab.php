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
* @file       tpls/admin/email_templates_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php 
$set = Cache::getSmall('mass_send');
if ($set):?>
<script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	S.A.L.ready();
});
S.A.M.cancelSend=function(){
	if(confirm('Are you sure to cancel the interruption?')) {
		S.G.json('?<?php echo URL_KEY_ADMIN?>=email&clean=mass_send',{},function(){
			S.A.L.tab.tabs('load',S.A.L.tab_index);
		});
	}
}
</script>
<div class="ui-content" style="text-align:center;">
	<table class="a-list a-list-one" cellspacing="0">
	<tr><td class="a-fl a-c" style="padding:40px 0"><button type="button" class="a-button" onclick="S.A.M.sendEmails({})"><?php echo lang('$Mass mail process has been interrupted at %1, please click here to continue..',ceil($set['sent']/$set['total']*100).'%')?></button></td></tr>
	<tr><td class="a-search a-c" style="padding:10px 0"><button type="button" class="a-button" onclick="S.A.M.cancelSend();"><?php echo lang('$Cancel')?></button></td></tr>
	</table>
</div>
<?php else:?>
<script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, i=0, a;
	for (file in data) i++;
	if (i>0) {
		var html = '<table class="a-list a-list-one" cellspacing="0">';
		for(file in data) {
			a=data[file];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l" width="30%"><a href="javascript:;" onclick="S.A.M.edit(\''+file+'\', this)">'+file+'</a></td>';
			html += '<td class="a-l" width="30%">'+a.title+'</td>';
			html += '<td class="a-r a-action_buttons" width="20%">';
			html += '<a href="javascript:;" onclick="S.A.M.send(\''+file+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-send.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.M.edit(\''+file+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.del({id:\''+file+'\', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';
			
			html += '</tr>';
			i++;
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No email campaigns were found')?></div>';
	}
	$('#a_set_<?php echo $this->name?>_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});

</script>
<form method="post" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php $this->inc('language', array('tab'=>$this->tab))?>
	<?php $this->inc('search', array('tab'=>$this->tab))?>
	<?php /*$this->inc('search', array('tab'=>$this->tab))*/?>
	<input type="checkbox" class="a-checkbox" onclick="S.A.L.global('session',{key:'nowysiwyg',value:this.checked});"<?php echo (@$_SESSION['AdminGlobal']['nowysiwyg']?' checked="checked"':'')?> id="a-w_nowysiwyg" /><label for="a-w_nowysiwyg"> <?php echo lang('$No WYSIWYG')?></label>
	<button type="button" class="a-button" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=email&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&add=true')"><?php echo lang('$Add new campaign')?>..</button>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="a_set_<?php echo $this->name?>_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>
<?php endif;?>