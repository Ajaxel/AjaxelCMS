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
* @file       tpls/admin/tree.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
function treeDisplayedFor(d, o) {
	var r = '';
	switch (parseInt(d)) {
		case 0:
			r = '';
		break;
		case 1:
			r = 'authorized';
		break;
		case 2:
			r = 'not authorized';
		break;
		case 3:
			r = 'administrators';
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
			if(g.length) r += 'groupIDs: '+g.join(', ');
			if(g.length&&c.length) r += '; ';
			if (c.length) r += 'classIDs: '+c.join(', ');
		break;
		case 5:
			r = 'hidden';
		break;
	}
	return r;
}

function treeFolder(l) {
	switch (l) {
		case 0:
			return 'folder';
		break;
		case 1:
			return 'folder-yellow';
		break;
		case 2:
			return 'folder-orange';
		break;
		case 3:
			return 'folder-green';
		break;
		case 4:
			return 'folder-grey';
		break;
		default:
			return 'link'
		break;
	}
}

$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, h='', i=0, a;
	
	function printTreeNode(data,level,num) {
		if (!data) return '';
		h = '<ul class="a-tree ui-widget-content" style="border:none">';
		var j = 0;
		for (i in data) {
			a=data[i];
			j++;
			<?php if ($this->find):?>
			h += '<li class="a-sortable" style="margin-left:'+(a.l*18)+'px;" id="a-c-<?php echo $this->table?>-'+a.id+'">';
			<?php else:?>
			h += '<li class="a-sortable'+(a.l?' a-level':'')+'" id="a-c-<?php echo $this->table?>-'+a.id+'">';			
			<?php endif;?>		
			h += '<div class="a-item">';
			h += '<div class="a-icon"><a href="'+a.url+'" target="_blank"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/places/'+treeFolder(level)+'.png" alt="" /></a></div>';
			h += '<div class="a-title">'+num+j+'. <a href="javascript:;" onclick="S.A.W.open(\'\?<?php echo URL_KEY_ADMIN?>=tree&id='+a.id+'\')">'+a.t+'</a> <a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=pages&<?php echo self::KEY_TREEID?>='+a.id+'\')"><span class="a-cnt">('+a.c+')</span></a> '+treeDisplayedFor(a.d, a.o)+'</div>';
			
			h += '<div class="a-action_buttons"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=pages&<?php echo self::KEY_TREEID?>='+a.id+'&add=1\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=tree\', {id:'+a.id+', active:'+(a.a==1)+', title: \''+S.A.P.js(a.t)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.a==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.M.del({id:'+a.id+'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a></div>';
			
			h += '</div>';
			h += printTreeNode(a.sub,level+1,num+j+'.');
			h += '</li>';
		}
		h += '</ul>';
		return h;
	}
	
	for (pos in data) {
		h += '<div class="a-fm<?php echo $this->ui['a-fm']?>">'+pos+'</div>';
		h += ''+printTreeNode(data[pos],0,'')+'';
	}
	if (h) h = '<div class="a-list-group" id="<?php echo $this->name?>_list_group">'+h+'</div>';
	else h = '<div class="a-not_found"><?php echo lang('$No results were found','tree')?></div>';
	S.A.L.ready(h);
	S.A.L.sortable_tree();
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
<?php $this->inc('bot')?>
</div>