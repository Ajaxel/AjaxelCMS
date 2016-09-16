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
* @file       tpls/admin/forum_threads_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo Index::CDA?>
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,row = <?php echo $this->json_row?>,a,x=2,h='';
	h += '<table class="a-list a-list-one" cellspacing="0">';
	<?php if ($this->total || $this->id):?>
		<?php if ($this->id):?>
	
		h += '<tr><td class="a-fl" width="14%"><span class="a-date">'+row.date+'</span></td><td class="a-fl"><h2>'+row.title+' ('+row.posts+')</h2></td><td class="a-fr">'+row.user+'</td></tr>';
		h += '<tr><td class="a-fl" colspan="3" style="padding:5px 10px">'+row.descr+'</td></tr>';
		h += '<tr><td colspan="3" class="a-fsep">&nbsp;</td></tr>';
		<?php endif;?>
		
		for(i=0;i<data.length;i++) {
			a=data[i];
			<?php if ($this->id):?>
			h += '<tr'+(i%2?' class="a-odd"':'')+' valign="top"><td class="a-fl a-fbr" width="4%"><span class="a-date">'+a.date+'</span><h3 style="padding-top:2px">'+a.user+'</h3></td><td class="a-fl"><table cellspacing="0"><tr><td>'+a.descr+'</td></tr></table></td>';
			
			h += '<td class="a-r a-action_buttons" width="10%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'&tab=posts\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.del({id:\''+a.id+'\', tab: \'posts\'}, this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';
			
		//	<td class="a-admin_actions"><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->admin?>&id='+a.id+'&<?php echo self::KEY_CATID?>=<?php echo $this->catid?>&tab=<?=$this->tab?>\',\'\',\'<?=$this->tab?>\')">';
			h += '</tr>';
			<?php else:?>
			h += '<tr'+(i%2?' class="a-odd"':'')+' valign="top"><td class="a-fl a-fbr" width="4%"><span class="a-date">'+a.date+'</span><h3 style="padding-top:2px">'+a.user+'</h3></td><td class="a-l"><h3><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->admin?>&id='+a.id+'&<?php echo self::KEY_CATID?>=<?php echo $this->catid?>&tab=<?=$this->tab?>\',\'\',\'<?=$this->tab?>\')">'+a.title+' ('+a.posts+')</a></h3><div style="padding:5px 10px;max-height:200px;overflow:auto;width:620px">'+a.descr+'</div></td></tr>';
			<?php endif;?>
		}
	<?php else:?>
		h += '<tr><td colspan="3"><div class="a-not_found"><?php echo lang(($this->id?'$No posts were added':'$No threads were found'))?></div></td></tr>';
	<?php endif;?>
	<?php if ($this->catid || $this->id):?>
	h += '<tr><td colspan="3" class="a-fsep">&nbsp;</td></tr>';
	<?php if ($this->id):?>
	h += '<tr><td colspan="3" class="a-fm<?php echo $this->ui['a-fm']?>"><?php echo lang('$Post reply:')?></td></tr>';
	<?php else:?>
	h += '<tr><td colspan="3" class="a-fm<?php echo $this->ui['a-fm']?>"><?php echo lang('$New thread to %1:',$this->cat['title'])?></td></tr>';
	<?php endif;?>
	h += '<tr><td colspan="3">';
	h += '<form method="post" class="ajax_form"><table cellspacing="0" class="a-form">';
	<?php if (!$this->id):?>
	h += '<tr valign="top"><td class="a-l" width="12%"><?php echo lang('$Subject')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[title]" onfocus="if(this.value==\'<?php echo strjava('RE: '.$this->row['title'])?>\') this.select();" value="<?php echo strjs($this->data['title'] || !$this->id ? $this->data['title']:'RE: '.$this->row['title'])?>" /></td></tr>';
	<?php endif;?>
	h += '<tr><?php if (!$this->id):?><td class="a-l"><?php echo lang('$Message')?></td><?php endif;?><td class="a-r"><textarea class="a-textarea" style="width:98%;height:250px" name="data[descr]"><?php echo strjs($this->data['descr'])?></textarea></td></tr>';
	h += '</table></form>';
	h += '</td></tr>';
	<?php endif;?>
	h += '</table>';
	S.A.L.ready(h);
	<?php echo Index::CDZ?>
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_CATID,$this->url_full)?>&<?php echo self::KEY_CATID?>='+this.value,false,'<?php echo $this->tab?>')"><option value=""><?php echo lang('$-- all categories --')?></option><?php echo $this->Tree->selected($this->catid)->toOptions()?></select>
	<?php $this->inc('search',array('tab'=>$this->tab))?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo $this->referer?>">
<?php if ($this->cat):?>
<div class="a-h2">
	<h2><?php echo $this->cat['title']?></h2>
</div>
<?php endif;?>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav')?>
<?php if ($this->catid || $this->id):?>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php if ($this->id):?>
		<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:left"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&'.self::KEY_CATID.'='.$this->catid.'&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/go-previous.png','text'=>'Back to '.$this->row['title'])); ?>
	<?php else:?>
		<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:left"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/go-previous.png','text'=>'Back to all threads')); ?>
	<?php endif;?>
	
	<?php if ($this->id):?>	
		<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/devices/media-floppy.png','text'=>lang('$Publish'))); ?>
		
		<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'S.A.W.open(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->id.'&tab='.$this->tab.'\',false,this)','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/edit.png','text'=>lang('$edit'))); ?>
		<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'if (confirm(\'Are you sure to delete this '.$this->admin.' content?\')) S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->admin.'&id='.$this->id.'&do=delete&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/eraser.png','text'=>lang('$erase'))); ?>	
	<?php else:?>
		<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/devices/media-floppy.png','text'=>lang('$Publish'))); ?>
	<?php endif;?>
</div>
<?php endif;?>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>