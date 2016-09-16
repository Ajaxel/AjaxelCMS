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
* @file       tpls/admin/settings_modules_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var h = '<table class="a-list a-list-one" cellspacing="0"><tbody class="a-sortable_<?php echo $this->name?>">';
		h += '<tr><th width="1%">&nbsp;</th><th width="55%"><?php echo lang('$Title')?></th><th width="15%"><?php echo lang('$Type')?></th><th width="15%"><?php echo lang('$Table')?></th><th>&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			h += '<tr'+(a.id?' id="a-c-<?php echo $this->table?>-'+a.id+'" class="'+(i%2?'':'a-odd')+' a-sortable a-hov"':' class="a-disabled a-hov"')+'>';
			h += '<td class="a-l"><img src="'+a.icon+'" width="16" /></td>';
			if (a.id) {
				h += '<td class="a-l"><a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', this)">'+a.title+'</a></td>';
				h += '<td class="a-l">'+a.type+'</td>';
				h += '<td class="a-l">'+a.table+'</td>';
				h += '<td class="a-r a-action_buttons" width="20%">';
				h += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
				h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', {id:\''+a.id+'\', active:'+a.active+', title: \''+a.login+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
				h += '<a href="javascript:;" onclick="if(confirm(\'<?php echo lang('$Are you completely sure to uninstall this module:')?> '+a.type+'_'+a.table+'?\\n<?php echo (!NO_DELETE ? lang('$WARNING, database data related to this module will be destroyed'):lang('$Attention, data will not be destroyed because of flag `NO_DELETE`, defined in config.php file'))?>\')){S.A.L.json(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>\', {<?php echo self::KEY_GET?>:\'action\',<?php echo self::KEY_ACTION;?>:\'uninstall\',type:\''+a.type+'\',table:\''+a.table+'\'}, true)}"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
				h += '</td>';
			} else {
				h += '<td class="a-l">'+a.title+'</td>';
				h += '<td class="a-l">'+a.type+'</td>';
				h += '<td class="a-l">'+a.table+'</td>';
				h += '<td class="a-r a-action_buttons" width="20%">';
				h += '<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&install=true\',{type:\''+a.type+'\',table:\''+a.table+'\',tpl:\''+a.tpl+'\'})"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/apps/system-software-update.png" alt="" /></a>';
				h += '</td>';
			}
			h += '</tr>';
		}
		h += '</tbody></table>';
	} else {
		var h = '<div class="a-not_found"><?php echo lang('$No results were found','modules')?></div>';
	}
	S.A.L.ready(h);
	S.A.L.sortable();
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select class="a-select" onchange="S.A.L.get('<?php echo Url::rq('t',$this->url_full)?>&t='+this.value,false,'<?php echo $this->tab?>')"><option value=""><?php echo lang('$-- all types --')?></option><?php echo Html::buildOptions(get('t'), DB::getAll('SELECT DISTINCT(type) FROM '.$this->prefix.$this->table.' WHERE active!=2 ORDER BY type','type'), true)?></select>
	<?php $this->inc('search', array('tab'=>$this->tab))?>
	<a href="javascript:;" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&create=true')"><?php echo lang('$Create new module')?>...</a>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<input type="hidden" name="data[uninstall]" id="a_set_<?php echo $this->tab?>_uninstall" value="" />
<input type="hidden" name="<?php echo self::KEY_ACTION?>" id="a_set_<?php echo $this->tab?>_a" value="" />
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>