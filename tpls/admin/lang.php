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
* @file       tpls/admin/lang.php
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
	var data = <?php echo $this->json_data?>,x=1,tl=0,i=0;
	<?php if ($this->all['lang']):?>var langs = <?php echo $this->json_langs?>;for(l in langs){tl++}<?php endif;?>
	var html = '<table class="a-list a-list-one" cellspacing="0"><tr><th width="<?php if ($this->all['lang']):?>25<?php else:?>40<?php endif;?>%"><?php echo lang('$Name')?></th><th width="<?php if ($this->all['lang']):?>73<?php else:?>55<?php endif;?>%"><?php if ($this->all['lang']):?><?php echo lang('$Values')?><?php else:?><img src="<?php echo FTP_EXT?>tpls/img/flags/16/<?php echo $this->lang?>.png" alt="<?php echo $this->lang_name?>" /><?php endif;?></th><th><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" alt="" /></th></tr>';
	
	
	html += '<tr'+(i%2?'':' class="a-odd"')+'><td class="a-fl"><input type="text" name="data_key[0]" class="a-input" style="width:98%" /></td><td class="a-fr"><div style="width:98%;">';
	<?php if ($this->all['lang']):?>
	html += '<table cellspacing="0" width="100%" id="s-0">';
	j=0;
	for (l in langs) {
		j++;
		html += '<tr><td width="1%"'+(tl>j?' class="a-l"':' style="padding-left:5px"')+'><img src="<?php echo FTP_EXT?>tpls/img/flags/24/'+l+'.png" style="margin-right:5px;margin-top:3px;" /></td><td'+(tl>j?' class="a-l"':' style="padding:2px 4px 1px 4px"')+'><textarea tabindex="'+x+'" name="data_val[0]['+l+']" style="height:25px;width:98%" class="a-textarea"></textarea></td></tr>';
		x++;
	}
	html += '</table>';
	<?php else:?>
	html += '<textarea tabindex="'+x+'" name="data_val[0]" style="height:40px;width:98%;overflow:auto" class="a-textarea" id="s-0"></textarea>';
	x++;
	<?php endif;?>
	html += '</div></td><td class="a-fr">&nbsp;</td></tr>';
	
	
	for(i=0;i<data.length;i++) {
		var admin = data[i].name && data[i].name.toString().substring(0,1)=='$';
		html += '<tr'+(i%2?'':' class="a-odd"')+'><td class="a-fl">'+data[i].name+'</td><td class="a-fr"><div style="width:98%;">';
		<?php if ($this->all['lang']):?>
		html += '<table cellspacing="0" width="100%" id="s-'+data[i].id+'">';
		j=0;
		for (l in langs) {
			j++;
			html += '<tr><td width="1%"'+(tl>j?' class="a-l"':' style="padding-left:5px"')+'><img src="<?php echo FTP_EXT?>tpls/img/flags/24/'+l+'.png" style="margin-right:5px;margin-top:3px;" /></td><td'+(tl>j?' class="a-l"':' style="padding:2px 4px 1px 4px"')+'><textarea tabindex="'+x+'" name="data_val['+data[i].id+']['+l+']" style="height:25px;width:98%" class="a-textarea">'+data[i].text[l]+'</textarea></td></tr>';
			x++;
		}
		html += '</table>';
		<?php else:?>
		html += '<textarea tabindex="'+x+'" name="data_val['+data[i].id+']" style="height:40px;width:98%;overflow:auto"  class="a-textarea" id="s-'+data[i].id+'">'+data[i].text+'</textarea>';
		x++;
		<?php endif;?>
		html += '</div></td><td class="a-fr"><div><input type="checkbox" class="a-checkbox" onclick="S.A.L.sdel(\'<?php if ($this->all['lang']):?>#<?php else:?>#<?php endif;?>s-'+data[i].id+'\', this)" name="data_del['+data[i].id+']" value="on"></div></td></tr>';
	}
	html += '</table>';
	S.A.L.ready(html);
	$('#<?php echo $this->name?>-content .a-textarea').prettyComments({
		animate: false,
		maxHeight : 200
	});
});
<?php echo Index::CDZ?>
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php echo lang('$Template')?>: <select id="<?php echo $this->name?>-template" onchange="S.A.L.get('?<?php echo URL::rq(self::KEY_TPL, URL::get())?>&<?php echo self::KEY_TPL?>='+this.value);if (this.value!='<?php echo URL_KEY_ADMIN?>'){S.A.W.close();S.A.L.ui(this.value, <?php echo strform($this->json_styles)?>)}"><?php echo Html::buildOptions($this->template,array_label(Site()->getTemplates()))?><option value="admin"<?php echo ($this->template=='admin'?' selected="selected"':'')?>><?php echo lang('$Admin')?></option></select>
	<button type="button" class="a-button a-button_s" onclick="S.G.confirm('<?php echo lang('_$Are you sure to replace missing and empty translations in database from file?')?>', '<?php echo lang('_$Please confirm')?>', function(){S.A.L.get('?<?php echo URL::rq(self::KEY_TPL, URL::get())?>&<?php echo self::KEY_TPL?>='+$('#<?php echo $this->name?>-template').val()+'&do=replace')});"><?php echo lang('$replace')?></button>
	<?php $this->inc('language', array('langs'=>array_merge($this->langs, array('all'=>array(0=>lang('$-- all --'))))))?>
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