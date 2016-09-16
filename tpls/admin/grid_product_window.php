<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' product:').' ';
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strJS($title)?>'+this.value);
		});
		var editor={
			element: '#a-w-body_<?php echo $this->name_id?>',
			height: 255, 
			base: '<?php echo HTTP_BASE?>',
			full: true,
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		var editor={
			element: '#a-w-body_ee_<?php echo $this->name_id?>',
			height: 210, 
			base: '<?php echo HTTP_BASE?>',
			full: true,
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		var editor={
			element: '#a-w-body_ru_<?php echo $this->name_id?>',
			height: 210, 
			base: '<?php echo HTTP_BASE?>',
			full: true,
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image files','save_images');
		S.A.W.tabs('<?php echo $this->name_id?>');
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files,'photo');
	}
	,setImages:function(files,name){
		var html = '<ul id="a-'+name+'s_<?php echo $this->name_id?>">';
		var j=0;
		if (files.length) {
			switch (name) {
				case 'photo':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t=='image') {
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+'" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<div class="a-p"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,'th2')+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></div></li>';
							/*
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+' a-sortable" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<a href="javascript:;" class="show-photo"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,'th3')+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></a>';
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',$(this).parent().parent().find(\'a.show-photo\').children(0).attr(\'src\'), false, this)"><img src="/tpls/img/oxygen/16x16/actions/mail-delete.png" /></a><br />';
							html += '<a href="javascript:;" onclick="S.A.W.setMainPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', this)"><img src="/tpls/img/oxygen/16x16/actions/dialog-ok.png" /></a></div></li>';
							*/
							j++;
						}
					}
				break;
				case 'doc':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t!='image' && a.t!='video' && a.t!='audio' && a.t!='flash') {
							html += '<li>';
							html +='<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f)+'" target="_blank">'+a.f+'</a>';
							html += ' <span><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="/tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></span></li>';
							j++;
						}
					}
				break;
				case 'video':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t=='video' || a.t=='audio' || a.t=='flash') {
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+' a-sortable" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f)+'" target="_blank"><img src="/tpls/img/oxygen/32x32/actions/media-playback-start.png" /></a><br />'+a.f;
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="/tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></div></li>';
							j++;
						}
					}
				break;
			}

		}
		html += '</ul>';
		if (j) $('#a-'+name+'s_div_<?php echo $this->name_id?>').html(html);
		switch (name) {
			case 'photo':
				S.A.W.selectable_images('<?php echo $this->name_id?>','photo');
			break;
			case 'video':
				S.A.W.sortable_images('<?php echo $this->name_id?>','video');
			break;	
		}
	}
}
<?php $this->inc('js_pop')?>
S.A.W.addOptionGroup=function(obj){
	var tbl = $('#a-form_<?php echo $this->name_id?> table.a-product_options>tbody');
	var index = tbl.find('tr').length;
	var h = '{index}.';
	var td0=$('<td class="a-l">').html(h.replace(/{index}/g,index+1));
	h = '<ul class="a-opts"><li><input type="text" name="data[options][{index}][key]" class="a-input" value="" style="width:85%" /> <a href="javascript:;" onclick="S.A.W.addOptionGroup(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a></li></ul>';
	var td1=$('<td class="a-r">').html(h.replace(/{index}/g,index));
	h = '<ul class="a-opts"><li><input type="text" name="data[options][{index}][values][]" class="a-input" value="" style="width:70%" /> <input type="text" name="data[options][{index}][price]" class="a-input" value="" style="width:11%" /> <a href="javascript:;" onclick="S.A.W.addOptionValue({index},this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a></li></ul>';
	var td2=$('<td class="a-r">').html(h.replace(/{index}/g,index));
	h = '<select class="a-select" name="data[options][{index}][type]">';
	h += '<option value="radio">Radio</option>';
	h += '<option value="checkbox">Checkbox</option>';
	h += '<option value="dropdown">Dropdown</option>';
	h += '<option value="custom">Custom</option>';
	h += '<select>';
	var td3=$('<td class="a-r">').html(h.replace(/{index}/g,index));
	var tr=$('<tr>').append(td0).append(td1).append(td2).append(td3);
	tbl.append(tr);
	td1.find('input').focus();
	$(obj).prev().after($('<a href="javascript:;" onclick="S.A.W.remOptionGroup(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png"></a>'));
	$(obj).remove();
}
S.A.W.addOptionValue=function(index, obj) {
	var h = '<input type="text" name="data[options][{index}][values][]" class="a-input" value="" style="width:70%" /> <input type="text" name="data[options][{index}][prices][]" class="a-input" value="" style="width:11%" /> <a href="javascript:;" onclick="S.A.W.addOptionValue({index}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a>';
	
	var li=$('<li>').html(h.replace(/{index}/g,index));
	$(obj).parent().parent().append(li);
	li.find('input:first').focus();
	$(obj).prev().after($('<a href="javascript:;" onclick="S.A.W.remOptionValue(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png"></a>'));
	$(obj).remove();
}
S.A.W.remOptionGroup=function(obj) {
	$(obj).parent().parent().parent().parent().remove();	
}
S.A.W.remOptionValue=function(obj) {
	$(obj).parent().remove();
}

</script>
<?php 
$this->title = $title.$this->post('title', false);
$this->width = 800;
$tab_height = 480;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_main_<?php echo $this->name_id?>"><?php echo lang('$Details')?></a></li>
			<li><a href="#a_body_<?php echo $this->name_id?>">EST, RUS</a></li>
			<li><a href="#a_images_<?php echo $this->name_id?>"><?php echo lang('$Images')?></a></li>
			<li><a href="#a_opts_<?php echo $this->name_id?>"><?php echo lang('$Options')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="3" width="85%"><select name="data[catref]" style="width:70%;font-family:Tahoma">
				<option value="0"><?php echo lang('$-- Top category --')?></option>
				<?
				$params = array(
					'table'		=> 'category_product',
					'lang'		=> $this->lang,
					'catalogue'	=> false,
					'retCount'	=> false,
					'getAfter'	=> false,
					'noDisable'	=> true,
					'maxLevel'	=> 0,
					'optValue'	=> 'catref',
					'getHidden'	=> false,
					'selected'	=> $this->post('catref', false)
				);
				echo Factory::call('category', $params)->getAll()->toOptions();
				?>
					</select>
					| <label><input type="checkbox" name="data[featured]" value="1"<?=($this->post['featured']?' checked="checked"':'')?> /> Featured</label> <label><input type="checkbox" name="data[cat_icon]" value="1"<?=($this->post['cat_icon']?' checked="checked"':'')?> /> Category icon</label>
					<input type="hidden" name="data[checkboxes][]" value="featured" />
					<input type="hidden" name="data[checkboxes][]" value="cat_icon" />
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Title:')?> [ENG]</td><td class="a-r" colspan="3"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo strform($this->post('title', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Title:')?> [EST]</td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[title_ee]" value="<?php echo strform($this->post('title_ee', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Title:')?> [RUS]</td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[title_ru]" value="<?php echo strform($this->post('title_ru', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Model:')?></td><td class="a-r"><input type="text" class="a-input" style="width:89%" name="data[title2]" value="<?php echo strform($this->post('title2', false))?>" /></td>
				<td class="a-l"><?php echo lang('$Manufacturer:')?></td><td class="a-r"><input type="text" class="a-input" style="width:89%" name="data[title3]" value="<?php echo strform($this->post('title3', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Description:')?> [ENG]</td><td class="a-r" colspan="3"><textarea type="text" class="a-textarea" name="data[descr]"  id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:125px;visibility:hidden"><?php echo strform($this->post('descr', false))?></textarea></td>
				</tr>
				<tr>
					<td class="a-l">Youtube URL</td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:90%" name="data[youtube]" value="<?php echo strform($this->post('youtube', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Price:')?></td><td class="a-r" width="35"><input type="text" class="a-input" style="width:150px" name="data[price]" value="<?php echo $this->post('price')?>" /> &euro;</td>
					<td class="a-l" width="15%"><?php echo lang('$In stock:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" style="width:100px" name="data[instock]" value="<?php echo $this->post('instock')?>" /> <?php echo lang('$items')?></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Old price:')?></td><td class="a-r" width="35"><input type="text" class="a-input" style="width:150px" name="data[price_old]" value="<?php echo $this->post('price_old')?>" /> &euro;</td>
					<td class="a-l" width="15%"><?php echo lang('$Start-Expires:')?></td><td class="a-r" width="35%"><input type="text" class="a-input a-date" style="width:100px" name="data[dated]" value="<?php echo $this->post('dated')?>" /> - <input type="text" class="a-input a-date" style="width:100px" name="data[expires]" value="<?php echo $this->post('expires')?>" /></td>
				</tr>
			</table>
		</div>
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l"><?php echo lang('$Description:')?> [EST]</td><td class="a-r" colspan="3"><textarea type="text" class="a-textarea" name="data[descr_ee]" id="a-w-body_ee_<?php echo $this->name_id?>" style="width:99%;height:125px;visibility:hidden"><?php echo strform($this->post('descr_ee', false))?></textarea></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Description:')?> [RUS]</td><td class="a-r" colspan="3"><textarea type="text" class="a-textarea" name="data[descr_ru]" id="a-w-body_ru_<?php echo $this->name_id?>" style="width:99%;height:125px;visibility:hidden"><?php echo strform($this->post('descr_ru', false))?></textarea></td>
				</tr>
			</table>
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
						<div id="a-photos_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No image files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="a_opts_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form a-product_options" cellspacing="0">
				<thead>
					<tr>
						<th width="2%">#</th>
						<th width="45%"><?php echo lang('$Question')?></th>
						<th width="35%"><?php echo lang('$Options / Price diff')?></th>
						<th width="18%"><?php echo lang('$Field type')?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$index = 0;
					if ($this->post['options']):
						foreach ($this->post['options'] as $a):
							$cnt = 0;
							foreach ($a['values'] as $v):
								if (!$v) continue;
								$cnt++;
							endforeach;
							if (!$a['key'] || (!$cnt && $a['type']!='custom')) continue;
					?>
					
					<tr>
						<td class="a-l">
							<?php echo $index+1?>.
						</td>
						<td class="a-r">
							<ul class="a-opts">
								<li><input type="text" name="data[options][<?php echo $index?>][key]" class="a-input" value="<?php echo strform($a['key'])?>" style="width:85%" /> <a href="javascript:;" onclick="S.A.W.remOptionGroup(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png"></a></li>
							</ul>
						</td>
						<td class="a-r">
							<ul class="a-opts">
								<?php
								$i = 0;
								foreach ($a['values'] as $j => $v):
								if (!$v) continue;
								$i++;
								?>
								<li><input type="text" name="data[options][<?php echo $index?>][values][]" class="a-input" value="<?php echo strform($v)?>" style="width:70%" /> <input type="text" name="data[options][<?php echo $index?>][prices][]" class="a-input" value="<?php echo strform($a['prices'][$j])?>" style="width:11%" /> <?php if ($i==$cnt):?><a href="javascript:;" onclick="S.A.W.addOptionValue(<?php echo $index?>, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a><?php else:?><a href="javascript:;" onclick="S.A.W.remOptionValue(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png"></a><?php endif;?></li>
								<?php
								endforeach;
								if (!$i):
								?>
								<li><input type="text" name="data[options][<?php echo $index?>][values][]" class="a-input" value="<?php echo strform($v)?>" style="width:70%" /> <input type="text" name="data[options][<?php echo $index?>][prices][]" class="a-input" value="<?php echo strform($a['prices'][$j])?>" style="width:11%" /> <a href="javascript:;" onclick="S.A.W.addOptionValue(<?php echo $index?>, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a></li>
								<?php endif;?>
							</ul>
						</td>
						<td class="a-r">
							<select class="a-select" name="data[options][<?php echo $index?>][type]">
								<option value="radio"<?php echo ($a['type']=='radio' ? 'selected="selected"':'')?>>Radio</option>
								<option value="checkbox"<?php echo ($a['type']=='checkbox' ? 'selected="selected"':'')?>>Checkbox</option>
								<option value="dropdown"<?php echo ($a['type']=='dropdown' ? 'selected="selected"':'')?>>Dropdown</option>
								<option value="custom"<?php echo ($a['type']=='custom' ? 'selected="selected"':'')?>>Custom</option>
							</select>
						</td>
					</tr>
					
					<?php 
							$index++;
						endforeach;
					endif;
					
					?>
					
					<tr>
						<td class="a-l a-sortable">
							<?php echo $index+1?>.
						</td>
						<td class="a-r">
							<ul class="a-opts">
								<li><input type="text" name="data[options][<?php echo $index?>][key]" class="a-input" value="" style="width:85%" /> <a href="javascript:;" onclick="S.A.W.addOptionGroup(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a></li>
							</ul>
						</td>
						<td class="a-r">
							<ul class="a-opts">
								<li><input type="text" name="data[options][<?php echo $index?>][values][]" class="a-input" value="" style="width:70%" /> <input type="text" name="data[options][<?php echo $index?>][prices][]" class="a-input" value="" style="width:11%" /> <a href="javascript:;" onclick="S.A.W.addOptionValue(<?php echo $index?>, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png"></a></li>
							</ul>
						</td>
						<td class="a-r">
							<select class="a-select" name="data[options][<?php echo $index?>][type]">
								<option value="radio">Radio</option>
								<option value="checkbox">Checkbox</option>
								<option value="dropdown">Dropdown</option>
								<option value="custom">Custom</option>
							</select>
						</td>
						<td class="a-r">
						
						</td>
					</tr>
				</tbody>
			</table>
			
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:380px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $this->inc('buttons', array('save'=>'content_save'));?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>