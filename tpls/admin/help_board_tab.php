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
* @file       tpls/admin/help_board_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,row = <?php echo $this->json_row?>,a,x=2,h='';
	h += '<table class="a-list a-list-one" cellspacing="0">';
	<?php if ($this->id):?>
	
	h += '<tr><td class="a-fl a-fbr" width="14%"><span class="a-date">'+row.date+'</span></td><td class="a-fl"><h2>'+row.title+' ('+row.c+')</h2></td><td class="a-fr">'+row.user+'</td></tr>';
	h += '<tr><td class="a-fl" colspan="4" style="padding:5px 10px">'+row.d+'</td></tr>';
	h += '<tr><td colspan="4" class="a-fsep">&nbsp;</td></tr>';
	<?php endif;?>
	
	for(i=0;i<data.length;i++) {
		a=data[i];
		h += '<tr'+(i%2?' class="a-odd"':'')+' valign="top"><td class="a-fl a-fbr" width="4%"><span class="a-date">'+a.date+'</span></td><td class="a-l"><?php if (!$this->id):?><h3><a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=help&id='+a.id+'&tab=<?=$this->tab?>\',\'\',\'<?=$this->tab?>\')">'+a.t+' ('+a.c+')</a></h3><?php else:?><h3>'+a.t+'</h3><?php endif;?><div style="padding:5px 10px">'+a.d+'</div></td><td class="a-fr">'+a.user+'</td></tr>';
	}
	h += '<tr><td colspan="4" class="a-fsep">&nbsp;</td></tr>';
	<?php if ($this->id):?>
	h += '<tr><td colspan="3" class="a-fm<?php echo $this->ui['a-fm']?>"><?php echo lang('$Post reply:')?></td></tr>';
	<?php else:?>
	h += '<tr><td colspan="3" class="a-fm<?php echo $this->ui['a-fm']?>"><?php echo lang('$New subject:')?></td></tr>';
	<?php endif;?>
	h += '<tr><td colspan="4">';
	h += '<form method="post" class="ajax_form"><table cellspacing="0" class="a-form">';
	h += '<tr valign="top"><td class="a-l" width="12%"><?php echo lang('$Subject')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[title]" onfocus="if(this.value==\'<?php echo strjava('RE: '.$this->row['title'])?>\') this.select();" value="<?php echo strjs($this->data['title'] || !$this->id ? $this->data['title']:'RE: '.$this->row['title'])?>" /></td></tr>';
	h += '<tr><td class="a-l"><?php echo lang('$Message')?></td><td class="a-r"><textarea class="a-textarea" style="width:98%;height:250px" name="data[descr]"><?php echo strjs($this->data['descr'])?></textarea></td></tr>';
	h += '</table></form>';
	h += '</td></tr>';
	h += '</table>';
	
	
	S.A.L.ready(h);
});
</script>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
		<?php $this->inc('search',array('tab'=>$this->tab))?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&id=<?php echo $this->id?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav')?>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
<?php if ($this->id):?>
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:left"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'=help&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/go-previous.png','text'=>lang('$back to '.$this->tab.''))); ?>
	
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/devices/media-floppy.png','text'=>lang('$Publish'))); ?>
	
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'S.A.W.open(\'?'.URL_KEY_ADMIN.'=help&id='.$this->id.'&tab='.$this->tab.'\',false,this)','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/edit.png','text'=>lang('$edit'))); ?>
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'if (confirm(\'Are you sure to delete this help content?\')) S.A.L.get(\'?'.URL_KEY_ADMIN.'=help&id='.$this->id.'&do=delete&tab='.$this->tab.'\',\'\',\''.$this->tab.'\')','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/eraser.png','text'=>lang('$erase'))); ?>	
<?php else:?>
<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/devices/media-floppy.png','text'=>lang('$Publish'))); ?>
<?php endif;?>
</div>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>