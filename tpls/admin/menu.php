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
* @file       tpls/admin/menu.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
$(function() {
	S.A.L.menu_counts=function(a){
		var r = '';
		if (a.c3>0) r += '<a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=entries&<?php echo self::KEY_MENUID?>='+a.id+'\')" title="<?php echo lang('$Number of entries')?>">'+a.c3+'</a>';
		if (a.c3>0&&a.c>0) r +=', ';
		if (a.c>0) r += '<a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=content&<?php echo self::KEY_MENUID?>='+a.id+'\')" title="<?php echo lang('$Number of contents')?>">'+a.c+'<sup>'+a.c2+'</sup></a>';
		if (r) r = '<span class="a-cnt">('+r+')</span>';
		return r;
	}
	S.A.L.menu_displayed=function(d,o) {
		var r = '';
		switch (parseInt(d)) {
			case 0:
				r = '';
			break;
			case 1:
				r = '<?php echo lang('$authorized')?>';
			break;
			case 2:
				r = '<?php echo lang('$not authorized')?>';
			break;
			case 3:
				r = '<?php echo lang('$administrators')?>';
			break;
			case 4:
				var g=[],c=[], ex=o.split(','),f,v;			
				for(i=0;i<ex.length;i++){
					f=ex[i].substring(0,1);
					if (!f) continue;
					v=ex[i].substr(1);
					if(f=='c')c[c.length]=v;
					else if (f=='g')g[g.length]=v;
				}
				
				if(g.length) r += '<span style="color:steelblue" title="<?php echo lang('$Group IDs')?>">'+g.join(', ')+'</span>';
				if(g.length&&c.length) r += ', ';
				if (c.length) r += '<span style="color:magenta" title="<?php echo lang('$Class IDs')?>">'+c.join(', ')+'</span>';
			break;
			case 5:
				r = '<?php echo lang('$hidden')?>';
			break;
		}
		if (r) r = '<div class="a-r">'+r+'</div>';
		return r;
	}
	S.A.L.menu_actions=function(a){
		var h = '<a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=entries&mid='+a.id+'\',false,this)" title="<?php echo lang('$Add new entry')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-add.png" /></a>';
		h += '<a href="javascript:;" onclick="S.A.M.page(\''+a.id+'&add=1\', this)" title="<?php echo lang('$Add new content with sub modules')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a>';
		//h += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)" title="<?php echo lang('$Edit this menu element')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
		h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=menu\', {id:'+a.id+', active:'+(a.ac==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)" title="<?php echo lang('$Toggle publish/unpublish')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.ac==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.ac==1?'red':'green')+'.png" /></a>';
		h += '<a href="javascript:;" onclick="S.A.M.del({id:'+a.id+'}, this)" title="<?php echo lang('$Delete this menu element')?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
		return h;
	}


	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>;
	
	var h = '', x = 0, i = 0, j = 0, p = 0, a, _a, l=0, _l=0,was_sub=false;
	for (pos in data) l++;
	if (l) {
		for (pos in data) {
			for (r in data[pos]) p++;
			if (!p) continue;
			h += '<div class="a-list-group">';
			h += '<div class="a-fm<?php echo $this->ui['a-fm']?>" onclick="$(this).next().toggle()" style="cursor:pointer">'+pos+'</div>';
			
			h += '<table class="a-list" cellspacing="0" cellpadding="0" _style="display:none"><tbody'+(data[pos].length>1?' class="a-sortable_<?php echo $this->name?>"':'')+'>';
			i = 0;
			for (r in data[pos]) {
				a = data[pos][r];
				h += '<tr id="a-c-<?php echo $this->table?>-'+a.id+'" class="a-hov a-sortable '+(!i || was_sub?'a-first':'')+'">';
				h += '<td width="4%" class="a-la"><a href="'+a.url+'" target="_blank" _onclick="S.A.L.nav(\''+a.url+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/places/folder.png" /></a></td>';
				h += '<td width="80%" class="a-l'+(a.sub?' a-nb':'')+'"><a href="javascript:;" style="font-size:13px" onclick="S.A.M.edit('+a.id+', this)">'+a.t+'</a> '+S.A.L.menu_counts(a)+' '+S.A.L.menu_displayed(a.d, a.o)+'</td>';
				h += '<td class="a-r a-b'+(a.sub?' a-nb':'')+' a-action_buttons">';
				h += S.A.L.menu_actions(a);
				h += '</td>';
				h += '</tr>';
				if (a.sub) {
					 _l = a.sub.length;
					h += '<tr id="a-cc-<?php echo $this->table?>-'+a.id+'"><td colspan="3" style="padding:0"><table class="a-sublist '+(data[pos][i+1]?'':'a-sublist-last"')+'" cellspacing="0"><tbody'+(_l>1?' class="a-sortable"':'')+'>';
					j = 0;
					for (_r in a.sub) {
						_a = a.sub[_r];
						h += '<tr id="a-c-<?php echo $this->table?>-'+_a.id+'" class="a-hov">';
						h += '<td width="5.5%" class="'+(!a.sub[j+i]?' a-last':'')+' a-left">'+(j+1)+'</td>';
						h += '<td width="1%" class="a-l"><a href="'+_a.url+'" target="_blank" _onclick="S.A.L.nav(\''+_a.url+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/places/folder-yellow.png" /></a></td>';
						h += '<td width="82%" class="a-l" style="border-left:none"><a href="javascript:;" style="font-size:13px" onclick="S.A.M.edit('+_a.id+', this)">'+_a.t+'</a> '+S.A.L.menu_counts(_a)+' '+S.A.L.menu_displayed(_a.d, _a.o)+'</td>';
						h += '<td class="a-r a-b a-action_buttons">';
						h += S.A.L.menu_actions(_a);
						h += '</td>';
						h += '</tr>';
						j++;
					}
					h += '</tbody></table></td></tr>';
					was_sub = true;
				} else {
					was_sub = false;
				}
				i++;
			}
			h += '</tbody></table>';
			h += '<div class="a-sep"></div>';
			h += '</div>';
			x++;
		}
	}
	if (!h) h = '<div class="a-not_found"><?php echo lang('$No results were found','menu')?></div>';
	S.A.L.ready(h);
	S.A.L.sortable();
	S.A.L.sortable_sub();
	
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
</form>
<?php $this->inc('list')?>

</div>
<?php $this->inc('bot')?>