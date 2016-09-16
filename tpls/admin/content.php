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
* @file       tpls/admin/content.php
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
	var data = <?php echo $this->json_data?>;
	var i = 0, a, j, _a, l = 0, _l = 0;
	for (j in data) l++;
	if (l) {
		var h = '<table class="a-list a-list-one" cellspacing="0"><tbody class="a-sortable_<?php echo $this->name?>">';
		for (r in data) {
			a = data[r];
			h += '<tr id="a-c-<?php echo $this->table?>-'+a.id+'" class="a-m a-sortable '+(i?'a-first':'a-firsta')+(i%2?' a-odd':'')+'">';
			h += '<td width="80%" colspan="2" class="a-l a-nb" style="padding-left:1px"><span class="a-date">'+a.date+'</span> <a href="'+a.url+'" target="_blank" class="a-big_link">'+a.title+'</a>'+(a.sub?' <span class="a-cnt">('+a.cnt+')</span>':'')+'</td>';
			h += '<td class="a-b'+(a.sub?' a-nb':'')+' a-action_buttons" width="20%">';
			h += '<a href="javascript:;" onclick="S.A.C.page(0, \''+a.id+'&add=1\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=content&<?php echo self::KEY_MODULE?>=none\', {id:'+a.id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.C.edit(0, '+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/tool2.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.C.del(0, {id:'+a.id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';
			h += '</tr>';
			if (a.sub) {
				_l = 0;
				for (_r in a.sub) _l++;
				var is_m = _l>5;
				var is_m = false;
				h += '<tr id="a-cc-<?php echo $this->table?>-'+a.id+'" class="a-cc-subs-<?php echo $this->table?>"><td colspan="3" style="padding:0!important">'+(is_m?'<div style="height:150px;overflow:auto">':'')+'<table class="a-sublist" cellspacing="0"><tbody'+(_l>1?' class="a-sortable"':'')+'>';
				j = 0;
				for (_r in a.sub) {
					_a = a.sub[_r];
					// class="{w:'+_a.main_photo_size[0]+',h:'+_a.main_photo_size[1]+',p:\'[/th1/]\',t: \''+S.A.P.js(_a.title)+'\'}"
					h += '<tr id="a-c-<?php echo $this->table?>_'+_a.module+'-'+_a.rid+'" class="a-sm a-sm-'+_a.module+(i%2?' a-odd':' a-even')+(!j?' a-first':'')+'">';
					h += '<td width="5%" class="'+(!a.sub[j+i]?' a-last':'')+' a-left'+(!j?' a-first':'')+'">'+(j+1)+'</td>';
					h += '<td width="5%" class="a-l'+(_l>1?' a-sortable':'')+' a-c"><a target="_blank" href="'+_a.url+'"><img src="'+_a.icon.replace(/16x16/,'22x22')+'" width="22" title="'+_a.module+'" alt="'+_a.module+'" /></a></td>';
					h += '<td width="75%" class="a-l" style="border-left:none">'+(_a.main_photo?'<div class="a-l a-pic"><div class="a-wrap"><a href="<?php echo $this->http_dir_files?>content_'+_a.module+'/'+_a.rid+'/th1/'+_a.main_photo+'" target="_blank" title="'+S.A.P.js(_a.title)+'" class="a-thumb" rel="content"><img src="<?php echo $this->http_dir_files?>content_'+_a.module+'/'+_a.rid+'/th3/'+_a.main_photo+'" /></a></div></div>':'<div class="a-l a-pic"></div>')+'<div class="a-r a-entry"><a href="javascript:;" class="a-title" onclick="S.A.C.edit(\''+_a.module+'\', '+_a.rid+', this)"'+(_a.hint?' onmouseover="S.A.L.hint(this,\''+S.A.P.js(_a.descr)+'\')" onmouseout="S.A.L.hint(this, false, true)"':'')+'>'+_a.title+'</a>'+(_a.descr?'<div class="a-descr">'+_a.descr+'</div>':'')+'</div></td>';
					h += '<td class="a-b a-action_buttons" width="15%">';
					h += '<a href="javascript:;" onclick="S.A.C.page(\''+_a.module+'\', \''+a.id+'&id='+_a.rid+'&menuid='+a.menuid+'&move=1\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/transform-move.png" /></a>';
					h += '<a href="javascript:;" onclick="S.A.C.edit(\''+_a.module+'\', '+_a.rid+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
					h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=content&<?php echo self::KEY_MODULE?>='+_a.module+'\', {id:'+_a.rid+', active:'+(_a.active==1)+', title: \''+S.A.P.js(_a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(_a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(_a.active==1?'red':'green')+'.png" /></a>';
					h += '<a href="javascript:;" onclick="S.A.C.del(\''+_a.module+'\', {id:'+_a.rid+', active:'+(_a.active==1)+', title: \''+S.A.P.js(_a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" /></a>';
					h += '</td>';
					h += '</tr>';
					j++;
				}
				h += '</tbody></table>'+(is_m?'</div>':'')+'</td></tr>';
			}
			i++;
		}
		h += '</tbody></table>';
	} else {
		var h = '<div class="a-not_found"><?php echo lang('$No results were found','content')?></div>';
	}

	S.A.L.ready(h);
	S.A.L.sortable_sub();
	<?php if ($this->sort=='sort'):?>
	S.A.L.sortable('<?php echo $this->name?>');
	<?php endif;?>
	<?php if ($this->hl_group && !$this->find && (!$this->module && $this->module!='all') && !$this->menuid):?>
		S.A.L.show('#a-cc-<?php echo $this->table?>-<?php echo $this->hl_group?>', false);
	<?php endif;?>
	<?php if ($this->hl_name && $this->hl_id):?>
		$('#a-c-<?php echo $this->hl_name?>-<?php echo $this->hl_id?>').show('highlight',{},3000);
	<?php endif;?>
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
		<?php $this->inc('sort')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div class="a-search">
	<div class="a-l">
		<?php echo lang('$Module:')?> <select onchange="S.A.L.get('<?php echo Url::rq(self::KEY_MODULE,$this->url_full)?>&<?php echo self::KEY_MODULE?>='+this.value)"><option value="all"><?php echo lang('$-- all types --')?><?php echo Data::getArray('content_modules',$this->module)?></select>
		<?php echo lang('$Menu:')?> <select style="width:400px" onchange="S.A.L.get('<?php echo Url::rq(self::KEY_MENUID, $this->url_full)?>&<?php echo self::KEY_MENUID?>='+this.value)"><option value=""><?php echo lang('$-- all --')?><?
		$params = array(
			'select'	=> 'id, parentid, (CASE WHEN cnt>0 THEN CONCAT(title_'.$this->lang.',\' [\',cnt,\': \',cnt2,\']\') ELSE title_'.$this->lang.' END) AS title, cnt',
			'selected'	=> $this->menuid
		);
		echo Factory::call('menu', $params)->getAll()->toOptions();
	?></select>
	</div>
	<div class="a-r">
		
	</div>
</div>
</form>
<?php $this->inc('list')?>

<?php $this->inc('bot')?>
</div>