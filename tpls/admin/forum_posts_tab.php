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
* @file       tpls/admin/forum_posts_tab.php
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
	<?php if ($this->total):?>
	for(i=0;i<data.length;i++) {
		a=data[i];
		h += '<tr'+(i%2?' class="a-odd"':'')+' valign="top"><td class="a-fl a-fbr" width="4%"><span class="a-date">'+a.date+'</span><h3 style="padding-top:2px">'+a.user+'</h3></td><td class="a-fl"><div style="padding:5px 10px;max-height:200px;overflow:auto;width:570px">'+a.descr+'</div></td>';
		h += '<td class="a-r a-action_buttons" width="10%">';
		h += '<a href="javascript:;" onclick="S.A.M.edit(\''+a.id+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
		h += '<a href="javascript:;" onclick="S.A.L.del({id:\''+a.id+'\'}, this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
		h += '</td>';
		h += '</tr>';
	}
	<?php else:?>
		h += '<tr><td colspan="3"><div class="a-not_found"><?php echo lang('$No posts were found')?></div></td></tr>';
	<?php endif;?>
	h += '</table>';
	S.A.L.ready(h);
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_CATID,$this->url_full)?>&<?php echo self::KEY_CATID?>='+this.value,false,'<?php echo $this->tab?>')"><option value=""></option><?php echo $this->Tree->selected($this->catid)->toOptions()?></select>
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
<?php $this->inc('nav')?>

<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>