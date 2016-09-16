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
* @file       tpls/admin/pages.php
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
		var h = '<table class="a-list a-list-one" cellspacing="0">';	
		for(i=0;i<data.length;i++) {
			a = data[i];
			h += '<tr class="'+(i%2?'':'a-odd')+'">';
			h += '<td width="5%" class="a-l" nowrap style="padding-left:1px"><span class="a-date">'+a.date+'</span></td>';
			h += '<td width="85%" class="a-l" style="border-left:none">'+(a.main_photo?'<div class="a-l a-pic"><div class="a-wrap"><a href="<?php echo $this->http_dir_files?><?php echo $this->name?>/'+a.rid+'/th1/'+a.main_photo+'" target="_blank" class="a-thumb" rel="page"><img src="<?php echo $this->http_dir_files?><?php echo $this->name?>/'+a.rid+'/th3/'+a.main_photo+'" class="{w:'+a.main_photo_size[0]+',h:'+a.main_photo_size[1]+',p:\'[/th1/]\',t: \''+S.A.P.js(a.title)+'\'}" /></a></div></div>':'<div class="a-l a-pic"></div>')+'<div class="a-r a-entry" style="width:88%">';
			h += '<div class="ui-state-highlight"><img src="<?php echo FTP_EXT?>tpls/img/link.gif" /> '+a.tree+' :: <a href="'+a.url+'" target="_blank">ID '+a.rid+'</a>'+(a.cat_tree?' <span style="color:#777">('+a.cat_tree.join(' &gt; ')+')':'')+'</span></div>';
			h += '<div style="line-height:25px"><a href="javascript:;" class="a-title" onclick="S.A.M.edit(\''+a.rid+'\', this)">'+a.title+'</a>'+(a.teaser?'<div class="a-descr">'+a.teaser+'</div>':'')+'</div></div></td>';
			h += '<td class="a-r a-action_buttons" width="10%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+a.rid+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>\', {id:'+a.rid+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			h += '<a href="javascript:;" onclick="if(confirm(\'<?php echo lang('_$Are you sure to delete this entry: %1?')?>\'.replace(/%1/,\''+S.A.P.js(a.title)+'\')))S.A.L.del({id:'+a.rid+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';			
			h += '</tr>';
		}
		h += '</table>';
	} else {
		var h = '<div class="a-not_found"><?php echo lang('$No results were found','pages')?></div>';
	}	
	S.A.L.ready(h);
});
<?php echo Index::CDZ?>
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<?php $this->inc('language')?>
	<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div class="a-search">
<?php echo lang('$Tree:')?> <select onchange="S.A.L.get('<?php echo Url::rq(self::KEY_TREEID, $this->url_full)?>&<?php echo self::KEY_TREEID?>='+this.value)"><option value=""><?php echo lang('$-- all --')?><?
		echo $this->Tree->get()->selected($this->treeid)->toOptions();
	?></select>
	<?php if ($this->category_options):?>
	<?php echo lang('$Category:')?>
	<select onchange="get('<?php echo Url::rq(self::KEY_CATID,$this->url_full)?>&<?php echo self::KEY_CATID?>='+this.value)">
		<option value="0"><?php echo lang('$-- Top category --')?></option>
		<?php echo $this->category_options?>
	</select>
	<?php endif;?>
</div>
</form>
<?php $this->inc('list')?>

</div>
<?php $this->inc('bot')?>