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
* @file       tpls/admin/content_magazine_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' article:').' ';
$this->title = $title.$this->post('title', false);
$this->width = 800;
$tab_height = 444;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		<?php $this->inc('js_editors', array('descr'=>250,'body'=>425))?>
		S.A.W.tabs('<?php echo $this->name_id?>',1);
		S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','main_photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','<?php echo lang('_$Image files')?>');
	}
	<?php $this->inc('js_setphoto')?>
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<?php
		$this->inc('tabs', array('tabs' => array(
				'main'		=> 'Article details',
				'body'		=> 'Body',
				'addons'	=> 'Magazines',
				'notes'		=> 'Notes'
			)
		));
		?>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form ui-corner-all" cellspacing="0">
				<?php $this->inc('tr_title', array('title'=>'Article title','colspan'=>'2'))?>
				<tr valign="top">
					<td class="a-l" width="15%"><?php echo lang('$Image:')?>, <?php echo lang('$Teaser:')?><br /><?php echo lang('$Description:')?></td><td class="a-r" width="25%">
						<?php $this->inc('main_photo')?>
					</td>
					<td class="a-r" width="60%"><textarea type="text" class="a-textarea" id="a-w-teaser_<?php echo $this->name_id?>" style="width:98%;height:60px" name="data[teaser]"><?php echo $this->post('teaser')?></textarea></td>
				</tr>
			</table>
			<table class="a-form" cellspacing="0"><tr><td class="a-r">
				<textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" name="data[descr]" style="width:99%;height:150px;visibility:hidden"><?php echo $this->post('descr')?></textarea>
			</td></tr></table>
			<?php $this->inc('article_flags',array('onchange'=>$this->options['content_lang_save']))?>
		</div>
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0"><tr><td class="a-r">
				<textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" name="data[body]" style="width:99%;height:400px;visibility:hidden"><?php echo $this->post('body')?></textarea>
			</td></tr></table>
		</div>
		<div id="a_addons_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<th class="a-l">&nbsp;</th><th class="a-r"><?php echo lang('$Title:')?></th><th class="a-r"><?php echo lang('$Price:')?></th>
				</tr>
				<?php
				if ($this->id) {
					$opts5 = array();
					$qry = DB::qry('SELECT * FROM '.$this->prefix.'content_magazine_map WHERE setid='.$this->id.' ORDER BY id',0,0);
					$i = 0;
					while ($r = DB::fetch($qry)):
						$opts5[$r['id']] = $r['date'];
					?>
					<tr>
						<td class="a-l"><?php echo ++$i?>.</td>
						<td class="a-r"><input type="text" class="a-input" style="width:350px" name="data_map[<?php echo $r['id']?>][name]" value="<?php echo $r['name']?>" /></td><td class="a-r"><input type="text" class="a-input" style="width:150px" name="data_map[<?php echo $r['id']?>][price]" value="<?php echo $r['price']?>" /> RUB</td>
					</tr>
					<?php
					endwhile;
				}
				?>
				<?php
				for ($i=1;$i<=4;$i++):
				?>
					<tr>
						<td class="a-l">+<?php echo $i?></td>
						<td class="a-r"><input type="text" class="a-input" style="width:350px" name="data_map[-<?php echo $i?>][name]" value="" /></td><td class="a-r"><input type="text" class="a-input" style="width:150px" name="data_map[-<?php echo $i?>][price]" value="" /> RUB</td>
					</tr>
				<?php
				endfor;
				?>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:<?php echo ($tab_height-15)?>px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'save'			=> 'content_save',
		'copy'			=> true,
		'add'			=> true
	)); ?>
</form>
<?php $this->inc('window_bottom')?>