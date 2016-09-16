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
* @file       tpls/admin/settings_vars_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>;
	var html = '<table class="a-list a-list-one" cellspacing="0"><tr><th><?php echo lang('$Name')?></th><th><?php echo lang('$Value')?></th><th><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" alt="" /></th></tr>';
	var x=2;
	for (i=1;i<2;i++) {
		html += '<tr class="'+(x%2?'a-new':'a-new_odd')+'"><td class="a-fl" width="30%"><input type="text" class="a-input" maxlength="255" tabindex="'+x+'" name="data_new_key['+i+']" style="width:88%;" /></td><td class="a-fr" width="70%">';
		x++;
		html += '<div style="width:100%;height:36px"><textarea tabindex="'+x+'" class="a-textarea" name="data_new_val['+i+']" style="width:96%;height:30px;"></textarea></div>';
		html += '</td><td>&nbsp;</td></tr>';
	}
	html += '<tr><td colspan="3" class="a-fsep">&nbsp;</td></tr>';
	for(i=0;i<data.length;i++) {
		html += '<tr'+(x%2?'':' class="a-odd"')+'><td width="30%" class="a-fl" title="'+data[i].name+'">'+data[i].name.substring(0,40)+'<input type="hidden" name="data_key['+x+']" value="'+data[i].name+'" /></td><td class="a-fr" width="70%"><div style="width:100%"><textarea tabindex="'+x+'" name="data_val['+x+']" class="a-textarea" style="height:80px;width:96%" id="s-'+x+'">'+data[i].val+'</textarea></div></td><td class="a-fr"><div><input type="checkbox" class="a-checkbox" onclick="S.A.L.sdel(\'#s-'+x+'\')" name="data_del['+x+']" value="on"></div></td></tr>';
		x++;
	}
	html += '</table>';
	$('#a_set_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
</script>
<?php if (!$this->submitted):?>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php $this->inc('language', array('tab'=>$this->tab))?>
	<?php $this->inc('search',array('tab'=>$this->tab))?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div id="a_set_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total))?>
<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php $this->inc('button',array('type'=>'button','click'=>'$(\'#'.$this->name.'-form_'.$this->tab.'\').submit()','class'=>'a-button disable_button','img'=>'oxygen/16x16/places/network-server-database.png','text'=>lang('$Save'))); ?>
</div>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>