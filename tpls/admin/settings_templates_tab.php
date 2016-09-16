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
* @file       tpls/admin/settings_templates_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, i=0;
	var h = '<table class="a-list a-list-one" cellspacing="0"><tbody class="a-sortable_<?php echo $this->name?>">';
	h += '<tr><th width="20%"><?php echo lang('$Date')?></th><th width="20%"><?php echo lang('$Name')?></th><th width="15%"><?php echo lang('$Engine')?></th><th width="55%"><?php echo lang('$Title')?></th><th>&nbsp;</th></tr>';
	for(tpl in data) {
		a = data[tpl];
		
		if (a.to_install) {
			h += '<tr id="a-c-<?php echo $this->table?>-'+tpl+'" class="'+(i%2?'':'a-odd')+' a-hov">';
			h += '<td class="a-l"><span class="a-date">'+a.added+'</span></td>';
			h += '<td class="a-l">'+a.name+'</td>';
			h += '<td class="a-l a-i">&nbsp;</td>';
			h += '<td class="a-l">&nbsp;</td>';
			h += '<td class="a-r a-action_buttons" width="20%">';
			h += '<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&install=true\',{name:\''+a.name+'\',title:\''+a.title+'\'})"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/system-software-update.png" alt="" /></a>';
			h += '</td>';
			h += '</tr>';
		} else {
			h += '<tr id="a-c-<?php echo $this->table?>-'+tpl+'" class="'+(i%2?'':'a-odd')+' a-sortable a-hov">';
			h += '<td class="a-l"><span class="a-date">'+a.added+'</span></td>';
			h += '<td class="a-l"><a href="javascript:;" onclick="S.A.M.edit(\''+tpl+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', this)">'+a.name+'</a></td>';
			h += '<td class="a-l">'+(a.engine?a.engine:'ajaxel')+'</td>';
			h += '<td class="a-l">'+a.title+'</td><td class="a-r a-action_buttons" width="15%">';
			
			h += '<a href="javascript:;" onclick="S.A.M.edit(\''+tpl+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=templates&tpl='+tpl+'\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/file-find.png" title="<?php echo lang('$Browse files..')?>" /></a>';
			h += '<a href="?template='+tpl+'" target="_blank"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/network.png" title="<?php echo lang('$Open this site template')?>" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', {id:\''+tpl+'\', active:'+(a.active==1)+', title: \''+a.title+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=settings&id='+tpl+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&uninstall=true\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a></td>';
			h += '</tr>';
		}
		i++;
	}
	h += '</tbody></table>';
	S.A.L.ready(h);
	S.A.L.sortable();
});
</script>

<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div class="a-search">
	<div class="a-l">
		<a href="javascript:;" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&create=true')"><?php echo lang('$Create new template...')?></a>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<input type="hidden" name="<?php echo self::KEY_ACTION?>" id="a_set_<?php echo $this->tab?>_a" value="" />
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>