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
* @file       tpls/admin/settings_global_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data;?>,a,width,height,size,v,s;
	var html = '<table class="a-list a-list-one" cellspacing="0">';
	var x=0,attr;
	
	for (k in data) {
		a=data[k];
		if (!a) continue;
		width = 98;
		height = 80;
		size = 5;
		switch (a.s) {
			case 1:
				width = 80;
			break;
			case 2:
				width = 50;
			break;
			case 3:
				width = 35;
			break;
			case 4:
				width = 20;
			break;
			case 5:
				width = 10;
			break;
			default:
				width = 98;
			break;
		}
		if (a.c) attr=a.c; else attr = '';
		if (a.sep) {
			x= 0;
			html += '<tr><td colspan="3" class="a-fm<?php echo $this->ui['a-fm']?>">'+a.sep+'</td></tr>';
			continue;
		}
		html += '<tr'+(x%2?'':' class="a-odd"')+'><td width="30%" class="a-fl" title="'+k+'">'+a.n+'</td><td class="a-fl" width="70%">';
		switch (a.t) {
			case 'input':
			case 'password':
				html += '<input id="a-settings_fld_'+k+'" type="'+(a.t=='password'?'password':'text')+'" class="a-input" name="data['+k+']" value="'+a.v+'" style="width:'+width+'%"'+attr+' />';
			break;
			case 'textarea':
				html += '<textarea id="a-settings_fld_'+k+'" name="data['+k+']" class="a-textarea" style="width:'+width+'%;height:'+height+'px"'+attr+'>'+a.v+'</textarea>';
			break;
			case 'checkbox':
				html += '<input id="a-settings_fld_'+k+'" type="checkbox"'+(a.v==1?' checked="checked"':'')+' name="data['+k+']" value="1"'+attr+' /><label for="a-settings_fld_'+k+'"> <?php echo lang('$Yes')?></label>';
			break;
			case 'select':
			case 'select-multiple':
				html += '<select id="a-settings_fld_'+k+'" '+(a.t=='select-multiple'?'name="data['+k+'][]" multiple="multiple" size="'+size+'"':'name="data['+k+']"')+' style="width:'+width+'%" class="a-select"'+attr+'>';
				for (k in a.a) {
					if (!a.a[k]) continue;
					if (typeof(a.a[k])=='object') v = a.a[k].join(', '); else v = a.a[k];
					if (a.t=='select-multiple') {
						s = $.inArray(k, a.v);
					} else {
						s = k==a.v;
					}
					html += '<option value="'+k+'"'+(s?' selected="selected"':'')+'>'+v+'</option>';
				}
				html +='</select>';
			break;
		}
		x++;
		html += '</td><td class="a-fr">';
		if (a.h) {
			html += '<img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/help-hint.png" class="a-help_hint" title="'+a.h+'" />';
		} else {
			html += '&nbsp;';	
		}
		html += '</td></tr>';
	}
	html += '</table>';
	$('#a_set_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
<?php echo Index::CDZ?>
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<?php if (count($this->templates)):?>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php echo lang('$Site selected template: %1','<span style="color:green">'.$this->Index->Session->Template.'</span>')?>. <?php echo lang('$Default template: %1','<span style="color:red">'.DEFAULT_TEMPLATE.'</span>')?>
	</div>
	<div class="a-r">
		<button type="button" class="a-button" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=cron')"><?php echo lang('$Utilities..')?></button> <button type="button" class="a-button" onclick="location.replace('?<?php echo URL_KEY_ADMIN?>=cron&do=go')"><?php echo lang('$Run cron')?></button>
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<?php endif;?>
<div id="a_set_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Save'))); ?>
</div>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>