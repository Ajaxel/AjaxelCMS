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
* @file       tpls/admin/log_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php if (get('show')=='all'):?>

<table class="a-form a-form-all" cellspacing="0">
<?php
	if ($this->post['data']['cur']) {
		echo '<tr><th width="25%">'.lang('$Field').'</th><th width="75%">'.lang('$All data').'</th></tr>';
		foreach ($this->post['data']['cur'] as $column => $value) {
			echo '<tr valign="top"><td class="a-l">'.Action()->toColumn($column,$this->post('table', false)).'</td>';
			echo '<td class="a-r">'.Action()->toVal($column, $value, $this->post('table', false)).'</td>';
		}
	} else {
		echo '<tr><td class="a-r">Error: No data, entry doesn\'t exist</td></tr>';
	}
?>
</table>

<?php elseif (get('show')=='text_diff'):?>

<table class="a-form" cellspacing="0">
	<tr>
		<td class="a-l"><?php echo lang('$Time:')?></td><td class="a-r" colspan="2"><?php echo $this->post('time2')?> (<?php echo $this->post['time']?>) <a href="?window&<?php echo URL_KEY_ADMIN?>=log&id=<?php echo $this->id?>&show=all&popup=1" target="_blank"><?php echo lang('$View all current data')?></a>
		</td>
	</tr>
	<tr>
		<td class="a-l" width="15%"><?php echo lang('$Action / Title:')?></td><td class="a-r" colspan="2" width="85%"><span style="color:<?php echo $this->action_types($this->post('action', false),0)?>"><?php echo $this->post('action')?>. <?php echo $this->action_types($this->post('action', false),1)?></span> <?php echo $this->post('title')?> (<?php echo lang('$Template:')?> <?php echo $this->post('template')?>)</td>
	</tr>
	<tr>
		<td class="a-l" ><?php echo lang('$Table / ID:')?></td><td class="a-r" colspan="2"><?php echo $this->post('table')?>, ID: <?php echo $this->post('setid')?></td>
	</tr>
	<?php if ($this->post('action', false)==Site::ACTION_UPDATE):?>
	<tr>
		<td class="a-l"><?php echo lang('$Changes:')?></td><td class="a-r" colspan="2"><?php echo $this->post('changes')?></td>
	</tr>
	<?php endif;?>
	<tr>
		<td class="a-l"><?php echo lang('$User:')?></td><td class="a-r" colspan="2"><?php echo $this->post('userid')?>. <?php echo $this->post('user')?> [<?php echo Conf()->g2('user_groups',$this->post('groupid', false))?>]</td>
	</tr>
	<tr>
		<td colspan="3" class="a-r">
			<table cellspacing="0" cellpadding="0" width="100%">
			<?php
			
			echo '<tr><th width="10%">'.lang('$Field').'</th><th width="45%">'.lang('$Diff').'</th></tr>';
			foreach ($this->post['data']['old'] as $column => $value) {
				if (isset($this->post['data']['new'][$column]) && $this->post['data']['new'][$column]!=$value) {
					$diff = Parser::textDiff(preg_replace('/<br\s?\/?>/',"\n",$value), str_replace('<br />',"\n",$this->post['data']['new'][$column]));
					
					echo '<tr valign="top"><td class="a-l">'.Action()->toColumn($column,$this->post('table', false)).'</td>';
					echo '<td class="a-r">'.str_replace('&lt;br /&gt;',"<br />",$diff).'</td></tr>';
					
					
					
				}
			}
			?>
			</table>
		</td>
	</tr>
</table>

<?php else:

$title = lang('$Log preview').' ID:';
$this->title = $title.$this->post('id', false);
$this->width = 820;
$tab_height = 465;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.W.tabs('<?php echo $this->name_id?>');
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l"><?php echo lang('$Time:')?></td><td class="a-r" colspan="2"><?php echo $this->post('time2', false)?> (<?php echo $this->post('time', false)?>)</td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Action / Title:')?></td><td class="a-r" colspan="2" width="85%"><span style="color:<?php echo $this->action_types($this->post('action', false),0)?>"><?php echo $this->post('action')?>. <?php echo $this->action_types($this->post('action', false),1)?></span> <?php echo $this->post('title')?> (<?php echo lang('$Template:')?> <?php echo $this->post('template')?>)</td>
		</tr>
		<tr>
			<td class="a-l" ><?php echo lang('$Table / ID:')?></td><td class="a-r" colspan="2"><?php echo $this->post('table')?>, ID: <?php echo $this->post('setid')?></td>
		</tr>
		<?php if ($this->post('action', false)==Site::ACTION_UPDATE):?>
		<tr>
			<td class="a-l"><?php echo lang('$Changes:')?></td><td class="a-r" colspan="2"><?php echo $this->post('changes')?></td>
		</tr>
		<?php endif;?>
		<tr>
			<td class="a-l"><?php echo lang('$User:')?></td><td class="a-r" colspan="2"><?php echo $this->post('userid')?>. <?php echo $this->post('user')?> [<?php echo Conf()->g2('user_groups',$this->post('groupid', false))?>]</td>
		</tr>
		<tr>
			<td class="a-r" colspan="3">
			<div style="overflow:auto;height:350px;">
			<table class="a-form a-form-all" cellspacing="0">
			<?
			switch ($this->post('action', false)) {
				case Site::ACTION_INSERT:
					echo '<tr><th width="34%">'.lang('$Field').'</th><th width="33%">'.lang('$After').'</th><th width="33%">'.lang('$Current').'</th></tr>';
					foreach ($this->post['data']['new'] as $column => $value) {
						echo '<tr valign="top"><td class="a-l">'.Action()->toColumn($column,$this->post('table', false)).'</td>';
						echo '<td class="a-r">'.Action()->toVal($column, $value, $this->post('table', false)).'</td>';	
						echo '<td class="a-r">'.($this->post['data']['new'][$column]==$this->post['data']['cur'][$column]?'<span class="a-info">'.lang('$the same as after').'</span>':Action()->toVal($column, $this->post['data']['cur'][$column], $this->post('table', false))).'</td></tr>';
					}
				break;
				case Site::ACTION_UPDATE:
					echo '<tr><th width="25%">'.lang('$Field').'</th><th width="25%">'.lang('$Before').'</th><th width="25%">'.lang('$After').'</th><th width="25%">'.lang('$Current').'</th></tr>';
					foreach ($this->post['data']['old'] as $column => $value) {
						if (isset($this->post['data']['new'][$column]) && $this->post['data']['new'][$column]!=$value) {
							echo '<tr valign="top"><td class="a-l">'.Action()->toColumn($column,$this->post('table', false)).'</td>';
							echo '<td class="a-r">'.Action()->toVal($column, $value, $this->post('table', false)).'</td>';
							echo '<td class="a-r">'.Action()->toVal($column, $this->post['data']['new'][$column], $this->post('table', false)).'</td>';	
							echo '<td class="a-r">'.($this->post['data']['new'][$column]==$this->post['data']['cur'][$column]?'<span class="a-info">'.lang('$the same as after').'</span>':Action()->toVal($column, $this->post['data']['cur'][$column], $this->post('table', false))).'</td></tr>';
						}
					}
				break;
				case Site::ACTION_DELETE:
					echo '<tr><th width="50%">'.lang('$Field').'</th><th width="50%">'.lang('$Before').'</th></tr>';
					if (isset($this->post['data']['old']['top']) && isset($this->post['data']['old']['sub'])) {
						foreach ($this->post['data']['old']['top'] as $column => $value) {
							echo '<tr valign="top"><td class="a-l">'.Action()->toColumn($column,$this->post('table', false)).'</td>';
							echo '<td class="a-r">'.Action()->toVal($column, $value, $this->post('table', false)).'</td></tr>';
						}
						if ($this->post['data']['old']['sub']) {
							foreach ($this->post['data']['old']['sub'] as $id_table => $d) {
								$ex = explode(':',$id_table);
								echo '<tr valign="top"><td class="a-r">'.$ex[0].': '.$ex[1].'</td><td>'.p($d,0).'</td></tr>';
							}
						}
					} else {
						foreach ($this->post['data']['old'] as $column => $value) {
							echo '<tr valign="top"><td class="a-l">'.Action()->toColumn($column,$this->post('table', false)).'</td>';
							echo '<td class="a-r">'.Action()->toVal($column, $value, $this->post('table', false)).'</td></tr>';
						}
					}
				break;
				case Site::ACTION_ERROR:
				case Site::ACTION_UNKNOWN:
					$s = unserialize($this->post['data']);
					echo '<tr><td>';
					if ($s['sql']) sql($s['sql']);
					else p($s);
					echo '</td></tr>';
				break;
			}
			?>
			</table></div>
			<?php if ($this->post('action', false)==Site::ACTION_UPDATE):?>
				<a href="javascript:;" onclick="S.A.W.popup('?window&<?php echo URL_KEY_ADMIN?>=log&id=<?php echo $this->id?>&show=all&popup=1',800,600,true)"><?php echo lang('$View all current data')?></a>
				<a href="javascript:;" onclick="S.A.W.popup('?window&<?php echo URL_KEY_ADMIN?>=log&id=<?php echo $this->id?>&show=text_diff&popup=1',800,600,true)"><?php echo lang('$Text Diff')?></a>
			<?php endif;?>
			</td>
		</tr>
	</table>
	<?php if ($this->post('action')!=Site::ACTION_UNKNOWN && $this->post('action')!=Site::ACTION_ERROR):?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr><td>
			<?php $this->inc('button',array('click'=>'if(confirm(\''.lang('$Are you sure to revert this change?').'\')){S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)}','img'=>'oxygen/16x16/actions/ark-extract.png','text'=>lang('$Revert'))); ?>
			</td></tr></table>
		</td>
		</tr></table>
	</div>
	<?php endif;?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>
<?php endif;?>