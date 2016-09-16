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
* @file       tpls/admin/categories.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function() {
	<?php $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>;
	var langs = <?php echo $this->json_langs?>;
	var max_sort = <?php echo (int)@$this->output['max_sort']?>;
	var x = 0, i = 0, j;
	if (data && (data.length || <?php echo (int)!$this->find?>)) {
		var html = '<table class="a-list a-list-one" cellspacing="0"><tr><th width="12%"><?php echo lang('$Ref ID')?></th><th width="85%"><?php echo lang('$Options')?></th><th><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-red.png" alt="" title="<?php echo lang('_$Hide?')?>" /></th><th><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" alt="" title="<?php echo lang('_$Delete?')?>" /></th></tr>';
		<?php if (!$this->find):?>
		for (i=1;i<=<?php echo INT_NEW_CATEGORIES?>;i++) {
			html += '<tr'+(i%2?' class="a-new_odd"':'')+'><td class="a-fl"><div align="center">+'+i+'</div></td><td class="a-fr">';
			for (j in langs) {
				x++;
				html += '<img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+j+'.png" alt="'+langs[j]+'" /> <input type="text" tabindex="'+x+'" name="new_cat['+i+']['+j+']" style="width:88%;margin-bottom:2px" class="a-input" /><br />';
				<?php if ($this->options['descr']):?>
				html += '<textarea name="new_descr['+i+']['+j+']" style="width:88%;margin-bottom:2px;padding-left:0"></textarea><br>';
				<?php endif;?>
			}
			html += '<div align="right" style="color:#666;font-size:10px;margin-bottom:2px;margin-top:2px;text-transform:lowercase"><?php echo lang('$name')?>: <input type="text" name="new_name['+i+']" style="width:50%" class="a-input" /></div>';
			html += '<div align="right" style="color:#666;font-size:10px"><?php echo lang('$icon')?>: <input type="text" name="new_icon['+i+']"<?php if (FILE_BROWSER):?> id="new_icon_<?php echo $this->name_id?>_'+i+'"<?php endif;?> style="width:30%" class="a-input" /><?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer(\'Icons:/\',\'new_icon_<?php echo $this->name_id?>_'+i+'\');"><?php echo lang('$Choose..')?></button><?php endif;?> <?php echo lang('$sort')?>: <input type="text" name="new_sort['+i+']" style="width:20px" value="'+(max_sort>1?(++max_sort):'')+'" class="a-input" /></div>';
			html += '</td><td colspan="2">&nbsp;</td></tr>';
		}
		<?php endif;?>
		html += '<tr><td colspan="4" class="a-fsep">&nbsp;</td></tr>';
		for(i=0;i<data.length;i++) {
			html += '<tr'+(i%2?' class="a-odd"':'')+'><td class="a-fl"><div align="center"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_MODULE?>=<?php echo $this->module?>&<?php echo self::KEY_CATID?>='+data[i].catid+'\')">'+data[i].catid+' <span'+(data[i].sublevels==0?' style="color:#888!important"':'')+'>('+parseInt(data[i].sublevels)+')</span></a></div></td><td class="a-fr"><div id="s-'+data[i].catid+'">';
			for (j in data[i].catname) {
				x++;
				html += '<img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+j+'.png" alt="'+langs[j]+'" /> <input type="text" tabindex="'+x+'" name="catname['+data[i].catid+']['+j+']" style="width:88%;margin-bottom:2px" value="'+S.A.P.js2(data[i].catname[j])+'" class="a-input" /><br />';
				<?php if ($this->options['descr']):?>
				html += '<textarea name="new_descr['+i+']['+j+']" style="width:88%;margin-bottom:2px" class="a-textarea">'+data[i].descr[j]+'</textarea><br>';
				<?php endif;?>
			}
			html += '<div align="right" style="color:#666;font-size:10px;margin-bottom:2px;margin-top:2px;text-transform:lowercase"><?php echo lang('$name')?>: <input type="text" name="name['+data[i].catid+']" value="'+data[i].name+'" style="width:50%" class="a-input" /></div>';
			html += '<div align="right" style="color:#666;font-size:10px"><?php echo lang('$icon')?>: <input type="text" name="icon['+data[i].catid+']" value="'+data[i].icon+'"<?php if (FILE_BROWSER):?> id="icon_<?php echo $this->name_id?>_'+i+'"<?php endif;?> style="width:30%" class="a-input" /><?php if (FILE_BROWSER):?> <button type="button" class="a-button a-button_x" onclick="S.A.W.browseServer(\'Icons:/\',\'icon_<?php echo $this->name_id?>_'+i+'\');"><?php echo lang('$Choose..')?></button><?php endif;?> <?php echo lang('$sort')?>: <input type="text" name="sort['+data[i].catid+']" value="'+(data[i].sort?data[i].sort:'')+'" style="width:20px" class="a-input" /></div>';
			html += '</div></td>';
			html += '<td class="a-fr"><div><input type="checkbox" class="a-checkbox" title="Hide?" name="hidden['+data[i].catid+']" value="1"'+(data[i].hidden==1?' checked="checked"':'')+'></div></td>';
			html += '<td class="a-fr"><div><input type="checkbox" class="a-checkbox" title="Delete?" onclick="S.A.L.sdel(\'#s-'+data[i].catid+'\');" name="d['+data[i].catid+']" value="on"></div></td>';
			html += '</tr>';
		}
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No results were found')?></div>';
	}
	S.A.L.ready(html);
	$('#<?php echo $this->name?>-content textarea').prettyComments({
		animate: false,
		maxHeight : 200
	});
});

</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<?php echo lang('$Module:')?> <select onchange="S.A.L.get('<?php echo Url::rq(self::KEY_MODULE,$this->url_full)?>&<?php echo self::KEY_MODULE?>='+this.value)"><?php echo Html::buildOptions($this->module, array_label($this->category_modules,'title'))?></select>
	<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<?php if (@$this->output['category_links_html']):?>
<div class="a-h2"><h2>
	<a href="javascript:;" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_MODULE?>=<?php echo $this->module?>')"><?php echo lang('$Root')?></a> :: <?php echo $this->output['category_links_html']?>
</h2></div>
<?php endif;?>
</form>
<?php $this->inc('list')?>
<?php $this->inc('bot')?>
</div>