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
* @file       tpls/admin/content_advert_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' advert:').' ';
$this->title = $title.$this->post('title', false);
$this->width = 800;
$tab_height = 480;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		<?php $this->inc('js_editors', array('descr'=>50,'body'=>280))?>
		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image');
		S.A.W.tabs('<?php echo $this->name_id?>');
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files);
		S.A.W.sortable_images('<?php echo $this->name_id?>','file');	
	}
	<?php $this->inc('js_setgallery')?>
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<?php
		$this->inc('tabs', array('tabs' => array(
				'main'		=> 'Tour details',
				'images'	=> 'Images',
				'addons'	=> 'Prices',
				'hotels'	=> 'Hotels',
				'days'		=> 'Days',
				'stops'		=> 'Stops',
				'notes'		=> 'Notes'
			)
		));
		?>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php $this->inc('tr_title', array('title'=>'Title', 'colspan'=>4))?>
				<tr>
					<td class="a-l" width="15%">Наличие билетов</td><td class="a-r" colspan="3" width="85%">
						<input type="text" class="a-input" style="width:29%" name="data[tickets]" value="<?php echo $this->post('tickets')?>" />
					
						&nbsp;
						<label><input type="radio" class="a-checkbox" name="data[is]" value=""<?php echo ($this->post('is', false)==''?' checked="checked"':'')?> /> <?php echo lang('$Default')?></label>&nbsp;
						<label><input type="radio" class="a-checkbox" name="data[is]" value="new"<?php echo ($this->post('is', false)=='new'?' checked="checked"':'')?> /> <?php echo lang('$New')?></label>&nbsp;
						<label><input type="radio" class="a-checkbox" name="data[is]" value="top"<?php echo ($this->post('is', false)=='top'?' checked="checked"':'')?> /> <?php echo lang('$Top')?></label>&nbsp;
						<label><input type="radio" class="a-checkbox" name="data[is]" value="hot"<?php echo ($this->post('is', false)=='hot'?' checked="checked"':'')?> /> <?php echo lang('$Hot')?></label>
						  | Страна:
						
					</td>
				</tr>
				<tr>
					<td class="a-l" width="15%">Страна</td>
					<td class="a-r" width="35%">
						 <select name="data[country]" class="a-select" style="width:150px">
						 	<option value=""></option>
						 	<?=Data::getArray('my:countries',$this->post['country']);?>
						 </select>
					</td>
					<td class="a-l" width="15%">Город</td>
					<td class="a-r" width="35%">
						<input type="text" class="a-input" style="width:89%" name="data[city]" value="<?php echo $this->post('city')?>" />
					</td>
				</tr>
			</table>
			<?php $this->inc('descr_body')?>
		</div>
		<div id="a_images_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload images:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-photo_<?php echo $this->name_id?>" style="width:80px;" size="2" />
						<?php if (!$this->id):?>
						<input type="hidden" name="data[main_photo]" id="a-main_photo_<?php echo $this->name_id?>" value="<?php echo $this->post('main_photo')?>" />
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Uploaded images:')?></td>
					<td class="a-r">
						<div style="height:<?php echo $tab_height-40;?>px;width:100%;overflow:auto;">
						<div id="a-files_div_<?php echo $this->name_id?>" style="width:99%"><div class="a-no_files"><?php echo lang('$No image files uploaded here, click BROWSE button')?></div></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div id="a_addons_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%">Валюта</td><td class="a-r" colspan="2" width="85%">
						<select name="data[currency]" class="a-select">
							<?=Data::getArray('currencies',$this->post['currency']);?>
						</select>
						
						<label><input type="checkbox" name="data[nobuy]" value="1"<?=($this->post['nobuy']?' checked="checked"':'')?> /> No Buy</label>
						<label><input type="checkbox" name="data[bank]" value="1"<?=($this->post['bank']?' checked="checked"':'')?> /> With Bank</label>
					</td>
				</tr>
				<tr>
					<th class="a-l">&nbsp;</th><th class="a-r"><?php echo lang('$Date:')?></th><th class="a-r"><?php echo lang('$Price:')?></th>
				</tr>
				<?php
				if ($this->id) {
					$opts5 = array();
					$qry = DB::qry('SELECT * FROM '.$this->prefix.'content_advert_map WHERE setid='.$this->id.' ORDER BY id',0,0);
					$i = 0;
					while ($r = DB::fetch($qry)):
						$opts5[$r['id']] = $r['date'];
					?>
					<tr>
						<td class="a-l"><?php echo ++$i?>.</td>
						<td class="a-r"><input type="text" class="a-input" style="width:350px" name="data_map[<?php echo $r['id']?>][date]" value="<?php echo $r['date']?>" /></td><td class="a-r"><input type="text" class="a-input" style="width:150px" name="data_map[<?php echo $r['id']?>][price]" value="<?php echo $r['price']?>" /> RUB</td>
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
						<td class="a-r"><input type="text" class="a-input" style="width:350px" name="data_map[-<?php echo $i?>][date]" value="" /></td><td class="a-r"><input type="text" class="a-input" style="width:150px" name="data_map[-<?php echo $i?>][price]" value="" /> RUB</td>
					</tr>
				<?php
				endfor;
				?>
			</table>
		</div>
		
		<div id="a_hotels_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r">&nbsp;</td><td class="a-r"><?php echo lang('$Title:')?></td>
					<td class="a-r">Date</td>
					<td class="a-r">2 places</td>
					<td class="a-r">1 places</td>
					<td class="a-r">3 places</td>
					<td class="a-r">2+1 child</td>
				</tr>
				<?php
				if ($this->id) {
					$qry = DB::qry('SELECT * FROM '.$this->prefix.'content_advert_map5 WHERE setid='.$this->id,0,0);
					$i = 0;
					while ($r = DB::fetch($qry)):
					?>
					<tr>
						<td class="a-r"><?php echo ++$i?>.</td>
						<td class="a-r"><input type="text" class="a-input" style="width:220px" name="data_map5[<?php echo $r['id']?>][title]" value="<?php echo $r['title']?>" /></td>
						<td class="a-r">
							<select class="a-select" name="data_map5[<?php echo $r['id']?>][dateid]" style="width:120px">
								<?=Html::buildOptions($r['dateid'], $opts5)?>
							</select>
						</td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[<?php echo $r['id']?>][price_2]" value="<?php echo $r['price_2']?>" /></td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[<?php echo $r['id']?>][price_1]" value="<?php echo $r['price_1']?>" /></td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[<?php echo $r['id']?>][price_3]" value="<?php echo $r['price_3']?>" /></td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[<?php echo $r['id']?>][price_21]" value="<?php echo $r['price_21']?>" /></td>
					</tr>
					<?php
					endwhile;
				}
				?>
				<?php
				for ($i=1;$i<=4;$i++):
				?>
					<tr>
						<td class="a-r">+<?php echo $i?>.</td>
						<td class="a-r"><input type="text" class="a-input" style="width:220px" name="data_map5[-<?php echo $i?>][title]" value="" /></td>
						<td class="a-r">
							<select class="a-select" name="data_map5[-<?php echo $i?>][dateid]" style="width:120px">
								<?=Html::buildOptions('', $opts5)?>
							</select>
						</td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[-<?php echo $i?>][price_2]" value="" /></td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[-<?php echo $i?>][price_1]" value="" /></td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[-<?php echo $i?>][price_3]" value="" /></td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map5[-<?php echo $i?>][price_21]" value="" /></td>
					</tr>
				<?php
				endfor;
				?>
			</table>
		</div>
		
		<div id="a_days_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r">&nbsp;</td><td class="a-r"><?php echo lang('$Date:')?></td><td class="a-l"><?php echo lang('$Description:')?></td>
				</tr>
				<?php
				if ($this->id) {
					$qry = DB::qry('SELECT * FROM '.$this->prefix.'content_advert_map2 WHERE setid='.$this->id,0,0);
					$i = 0;
					while ($r = DB::fetch($qry)):
					?>
					<tr>
						<td class="a-r"><?php echo ++$i?>.</td>
						<td class="a-r"><input type="text" class="a-input" style="width:70px" name="data_map2[<?php echo $r['id']?>][date]" value="<?php echo $r['date']?>" /></td><td class="a-l"><textarea type="text" class="a-textarea" style="width:98%;height:40px" name="data_map2[<?php echo $r['id']?>][descr]"><?php echo $r['descr']?></textarea></td>
					</tr>
					<?php
					endwhile;
				}
				?>
				<?php
				$max_sort = DB::one('SELECT MAX(date) FROM '.$this->prefix.'content_advert_map2 WHERE setid='.$this->id);
				for ($i=1;$i<=4;$i++):
				?>
					<tr>
						<td class="a-r"><?php echo $i?>.</td>
						<td class="a-r" style="width:15%"><input type="text" class="a-input" style="width:70px" name="data_map2[-<?php echo $i?>][date]" value="<?=$max_sort+$i?>" /></td><td class="a-l"><textarea type="text" class="a-textarea" style="width:98%;height:40px" name="data_map2[-<?php echo $i?>][descr]"></textarea></td>
					</tr>
				<?php
				endfor;
				?>
			</table>
		</div>
		<div id="a_stops_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l">No BUS</td>
					<td class="a-r"><input type="checkbox" name="data[nobus]" value="1"<?=($this->post['nobus']?' checked="checked"':'')?> /></td>
				</tr>
				<tr>
					<td class="a-r"><?php echo lang('$Sort:')?></td><td class="a-r"><?php echo lang('$Bus stop:')?></td>
				</tr>
				<?php
				if ($this->id) {
					$qry = DB::qry('SELECT * FROM '.$this->prefix.'content_advert_map4 WHERE setid='.$this->id,0,0);
					$i = 0;
					while ($r = DB::fetch($qry)):
					?>
					<tr>
						<td class="a-r"><input type="text" class="a-input" style="width:40px" name="data_map4[<?php echo $r['id']?>][sort]" value="<?php echo $r['sort']?>" /></td><td class="a-r"><input type="text" class="a-input" style="width:550px" name="data_map4[<?php echo $r['id']?>][stop]" value="<?php echo $r['stop']?>" /></td>
					</tr>
					<?php
					endwhile;
				}
				?>
				<?php
				$max_sort = DB::one('SELECT MAX(sort) FROM '.$this->prefix.'content_advert_map4 WHERE setid='.$this->id);
				for ($i=1;$i<=4;$i++):
				?>
				<tr>
					<td class="a-r"><input type="text" class="a-input" style="width:40px" name="data_map4[-<?php echo $i?>][sort]" value="<?=$max_sort+$i?>" /></td><td class="a-r"><input type="text" class="a-input" style="width:550px" name="data_map4[-<?php echo $i?>][stop]" value="" /></td>
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
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>