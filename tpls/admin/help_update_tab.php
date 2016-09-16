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
* @file       tpls/admin/help_update_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	
	S.A.L.ready();
});
</script>
<form method="POST" id="<?php echo $this->name?>-form_<?php echo $this->tab?>" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->admin?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>">
<div id="<?php echo $this->name?>-content" class="a-content">
<table class="a-form" cellspacing="0">
	<tr>
		<td class="a-l" width="25%"><?php echo lang('$Current version:')?></td><td class="a-r"><?php echo CMS::VERSION?></td>
	</tr>
	<tr>
		<td class="a-l"><?php echo lang('$Last time updated:')?></td><td class="a-r"><?php echo ($this->output['system_updated']?date('H:i d.m.Y',$this->output['system_updated']):lang('$Never updated'))?></td>
	</tr>
	<tr>
		<td class="a-l"><?php echo lang('$Latest version:')?></td><td class="a-r"><?php echo $this->output['latest_version']?></td>
	</tr>
	<?php if ($this->output['files']):?>
	<tr>
		<td class="a-r" colspan="2">
			<?php foreach ($this->output['files'] as $file):?>
			<?php echo $file?><br />
			<?php endforeach;?>
		</td>
	</tr>
	<?php endif;?>
	<?php if ($this->output['sqls']):?>
	<tr>
		<td class="a-r" colspan="2">
			<?php 
			foreach ($this->output['sqls'] as $file):?>
			<?php echo $file?><br />
			<?php endforeach;?>
		</td>
	</tr>
	<?php endif;?>
	
	<?php if ($this->output['update_backup_dir']):?>
	<tr>
		<td class="a-l"><?php echo lang('$Status:')?></td>
		<td class="a-r" style="line-height:22px">
			<img src="<?php echo FTP_EXT?>tpls/img/oxygen/22x22/actions/games-endturn.png" style="float:left;margin-right:5px" /> <strong><?php echo lang('$System has been updated sucessfuly')?><br /></strong>
		</td>
	</tr>
	<tr>
		<td class="a-l"><?php echo lang('$Backup directory on server:')?></td>
		<td class="a-r" colspan="2">
			<?php echo $this->output['update_backup_dir']?>
		</td>
	</tr>
	<?php else:?>
	<tr>
		<td class="a-l"><?php echo lang('$Status:')?></td>
		<td class="a-r" style="line-height:22px">
			<?php if (version_compare(CMS::VERSION,$this->output['latest_version'])<0):?>
				<img src="<?php echo FTP_EXT?>tpls/img/oxygen/22x22/actions/games-hint.png" style="float:left;margin-right:5px" /> <strong><?php echo lang('$New update is released, go ahead and click the below button to update your system.')?></strong>
				
			<?php else:?>
				<img src="<?php echo FTP_EXT?>tpls/img/oxygen/22x22/actions/games-endturn.png" style="float:left;margin-right:5px" />
				<?php echo lang('$Your system version is the latest, no new updates yet')?>
				
			<?php endif;?>
			
		</td>
	</tr>
	<?php endif;?>
	
	
</table>
</div>

<div class="a-buttons<?php echo $this->ui['buttons']?>">
	<?php if ($this->output['update']):?>
	<?php if (!$this->output['files']):?>
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'S.A.L.get(\'?'.URL_KEY_ADMIN.'=help&update=true&tab='.$this->tab.'\',\'\',\''.$this->tab.'\');this.disabled=true','class'=>'a-button disable_button','img'=>'oxygen/16x16/apps/preferences-web-browser-shortcuts.png','text'=>(version_compare(CMS::VERSION,$this->output['latest_version'])<0)?lang('$Update'):lang('$Update anyways'))); ?>
	<?php else:?>
	<?php $this->inc('button',array('type'=>'button','attr'=>'style="float:right"','click'=>'location.href=\'?'.URL_KEY_ADMIN.'=help\';this.disabled=true','class'=>'a-button disable_button','img'=>'oxygen/16x16/actions/system-suspend-hibernate.png','text'=>lang('$Restart'))); ?>
	<?php endif;?>
	<?php endif;?>
</div>
<input type="hidden" name="<?php echo $this->name?>_<?php echo $this->tab?>-submitted" value="1" />
</form>