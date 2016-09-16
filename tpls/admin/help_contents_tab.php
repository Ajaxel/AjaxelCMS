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
* @file       tpls/admin/help_contents_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
S.A.L.print_help_content_node=function(data,level,num) {
	if (!data) return '';
	var j = 0,a;
	var h = '<ul class="a-tree ui-widget-content" style="border:none">';
	for (i in data) {
		a=data[i];
		j++;
		h += '<li class="a-sortable'+('<?php echo $this->id?>'==a.id?' a-sel':'')+'" id="a-c-<?php echo $this->table?>-'+a.id+'">';
		
		h += '<div class="a-item">';
		h += '<div class="a-title" style="padding-left:4px">'+num+j+'. <a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=<?=$this->admin?>&id='+a.id+'&tab=<?=$this->tab?>\',\'\',\'<?=$this->tab?>\')">'+a.t+'</a></div>';
		h += '<div class="a-action_buttons">';
		h += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
		h += '<a href="javascript:;" onclick="S.A.L.del({id:\''+a.id+'\'}, this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
		h += '</div>';
		h += '</div>';
		h += S.A.L.print_help_content_node(a.sub,level+1,num+j+'.');
		h += '</li>';
	}
	h += '</ul>';
	return h;
}

$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,row = <?php echo $this->json_row?>,a,x=1,_x=1,h='',t=0;
	for (i in data) t++;
	if (t) {
		h += '<table class="a-list a-list-one" cellspacing="0" style="margin-bottom:0">';
		<?php if ($this->id):?>
		h += '<tr><td class="a-fl a-fbn" colspan="3"><h2>'+row.title+'</h2></td></tr>';
		h += '<tr><td class="a-fl" colspan="3"><dd>'+row.descr+'</dd></td></tr>';
		<?php else:?>
		h += '<tr><td class="a-fsep" colspan="3">&nbsp;</td></tr>';
		h += '<tr><td colspan="3">';
		h += S.A.L.print_help_content_node(data,0,'');
		if (h) h = '<div class="a-list-group" id="<?php echo $this->name?>_list_group">'+h+'</div>';
		else h = '<div class="a-not_found"><?php echo lang('$No results were found','tree')?></div>';
		<?php endif;?>
		h += '</td></tr></table>';
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No entries were found')?></div>';
	}
	S.A.L.ready(h);
	S.A.L.sortable_tree();
	
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_CATID,$this->url_full)?>&id='+this.value,false,'<?php echo $this->tab?>')"><option value=""></option><?php echo $this->Tree->selected($this->id)->toOptions()?></select>
	<?php $this->inc('search',array('tab'=>$this->tab))?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo $this->referer?>">
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php if ($this->id):?>
<div class="a-search" style="padding:5px;">
	<?php if ($this->output['prev_next'] && $this->output['prev_next']['prev']) $this->inc('button',array('type'=>'button','attr'=>'style="float:left"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->output['prev_next']['prev']['id'].'&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/go-previous.png','text'=>$this->output['prev_next']['prev']['t'])); ?>
	
	<?php if ($this->output['prev_next'] && $this->output['prev_next']['next']) $this->inc('button',array('type'=>'button','right'=>true,'attr'=>'style="float:right"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->output['prev_next']['next']['id'].'&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/go-next.png','text'=>$this->output['prev_next']['next']['t'])); ?>
</div>
<?php endif;?>	
<?php $this->inc('nav')?>

<div class="a-buttons<?php echo $this->ui['buttons']?>">
<?php if ($this->id):?>
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:left"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/go-home.png','text'=>lang('$back to '.$this->tab.''))); ?>
	
	<?php $this->inc('button',array('type'=>'button','click'=>'S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&'.self::KEY_CATID.'='.$this->id.'&tab='.$this->tab.'\',false,this)','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/edit.png','text'=>lang('$add child'))); ?>
	
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->id.'&tab='.$this->tab.'\',false,this)','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/edit.png','text'=>lang('$edit'))); ?>
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'if (confirm(\'Are you sure to delete this '.$this->admin.' content?\')) S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->id.'&do=delete&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/eraser.png','text'=>lang('$erase'))); ?>
<?php else:?>

<?php $this->inc('button',array('type'=>'button','click'=>'S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->id.'&tab='.$this->tab.'\',false,this)','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/edit.png','text'=>lang('$add'))); ?>
<?php endif;?>	
</div>

<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>