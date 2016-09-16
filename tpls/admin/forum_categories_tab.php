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
* @file       tpls/admin/forum_categories_tab.php
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
	var data = <?php echo $this->json_data?>,a,x=2;
	

	if (1) {
		var h = '<table class="a-list a-list-one" cellspacing="0"><tr><th>ID</th><th width="80%"><?php echo lang('$Category')?></th><th><?php echo lang('$Threads')?></th><th><?php echo lang('$Posts')?></th><th><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" alt="" /></th></tr>';
		for (i=1;i<3;i++) {
			h += '<tr class="'+(x%2?'a-new':'a-new_odd')+'"><td class="a-fr">+'+i+'</td><td class="a-fl"><input type="text" class="a-input" maxlength="255" tabindex="'+x+'" name="data[-'+i+'][title]" style="width:98%;" value="" /><br />';
			x++;
			h += '<textarea tabindex="'+x+'" class="a-textarea" name="data[-'+i+'][descr]" style="width:98%;height:35px;margin-top:4px!important"></textarea>';
			h += '</td><td class="a-fc">-</td><td class="a-fc">-</td><td class="a-fr">&nbsp;</td></tr>';
		}
		h += '<tr><td colspan="4" class="a-fsep">&nbsp;</td></tr><tbody class="a-sortable_<?php echo $this->name?>">';
		for(i=0;i<data.length;i++) {
			a=data[i];
			h += '<tr id="a-c-<?php echo $this->table?>-'+a.id+'" class="'+(x%2?'a-new':'a-new_odd')+' a-sortable"><td class="a-fr"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=forum&tab=categories&<?php echo self::KEY_CATID?>='+a.id+'\',\'\',\'<?php echo $this->tab?>\')">'+a.id+'</a></td><td class="a-fl"><input type="text" class="a-input a-forum_s-'+a.id+'" maxlength="255" tabindex="'+x+'" name="data['+a.id+'][title]" style="width:98%;" value="'+S.A.P.js2(a.title)+'" /><br />';
			x++;
			h += '<textarea tabindex="'+x+'" class="a-textarea a-forum_s-'+a.id+'" name="data['+a.id+'][descr]" style="width:98%;height:35px;margin-top:4px!important">'+a.descr+'</textarea>';
			h += '</td><td class="a-fc"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=forum&tab=threads&<?php echo self::KEY_CATID?>='+a.id+'\',\'\',\'threads\')">'+a.threads+'</a></td><td class="a-fc"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=forum&tab=posts&<?php echo self::KEY_CATID?>='+a.id+'\',\'\',\'posts\')">'+a.posts+'</a></td><td class="a-fr"><div><input type="checkbox" class="a-checkbox" onclick="if(this.checked && !confirm(\'Are you sure to delete this forum category including all sub-contents?\')) return false;S.A.L.sdel(\'#<?php echo $this->name?>-content .a-forum_s-'+a.id+'\')" name="data_del[]" value="'+a.id+'"></div></td></tr>';
			x++;
		}
		h += '</tbody>';
	} else {
		<? /* 
		// not a very good idea
		var h = '<table class="a-list a-list-one" cellspacing="0"><tr><th>ID</th><th width="80%"><?php echo lang('$Category')?></th><th><?php echo lang('$Threads')?></th><th><?php echo lang('$Posts')?></th><th>&nbsp;</th></tr>';
		
		for(i=0;i<data.length;i++) {
			a=data[i];
			//h += '<tr><td colspan="5"><div class="a-fm<?php echo $this->ui['a-fm']?>">aaa</div></td></tr>';
			
			h += '<tr class="'+(i%2?'a-odd a-vt':' a-vt')+'"><td class="a-fr a-fbr">'+a.id+'</td><td class="a-fl a-fbr"><h3><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=forum&tab=threads&<?php echo self::KEY_CATID?>='+a.id+'\',\'\',\'threads\')">'+a.title+'</a></h3>'+a.descr+'</td><td class="a-fc a-fbr"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=forum&tab=threads&<?php echo self::KEY_CATID?>='+a.id+'\',\'\',\'threads\')">'+a.threads+'</a></td><td class="a-fc a-fbr"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=forum&tab=posts&<?php echo self::KEY_CATID?>='+a.id+'\',\'\',\'posts\')">'+a.posts+'</a></td>';
			h += '<td class="a-r a-action_buttons" style="width:6%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.del({id:\''+a.id+'\'}, this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</tr>';
		}
		*/ ?>
	}
	h += '</table>';
	S.A.L.ready(h);
	S.A.L.sortable();
});
<?php echo Index::CDZ?>
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_CATID,$this->url_full)?>&<?php echo self::KEY_CATID?>='+this.value,false,'<?php echo $this->tab?>')"><option value=""><?php echo lang('$-- top categories --')?></option><option<?php echo (get(self::KEY_CATID)===self::KEY_ALL?' selected="selected"':'')?> value="<?php echo self::KEY_ALL?>"><?php echo lang('$-- all categories --')?></option><?php echo $this->Tree->selected($this->catid)->toOptions()?></select>
	<?php $this->inc('search',array('tab'=>$this->tab))?>
	</div>
	<div class="a-r">
		
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<?php if ($this->output['tree']):?>
<div class="a-h2"><h2><a href="javascript:" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>=forum&tab=<?php echo $this->tab?>','','<?php echo $this->tab?>')"><?php echo lang('$Root')?></a> :: <?php echo $this->output['tree']?></h2></div>
<?php endif;?>
</form>
<?php endif;?>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo $this->referer?>">
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total))?>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Save'))); ?>
</div>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>