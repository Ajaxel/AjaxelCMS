<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' product:').' ';
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?>'+this.value);
		});
		var editor={
			element: '#a-w-body_<?php echo $this->name_id?>',
			height: 175, 
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
							html +='<div class="a-p"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,'th3')+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></div></li>';
							/*
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+' a-sortable" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<a href="javascript:;" class="show-photo"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,'th3')+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></a>';
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',$(this).parent().parent().find(\'a.show-photo\').children(0).attr(\'src\'), false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a><br />';
							html += '<a href="javascript:;" onclick="S.A.W.setMainPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/dialog-ok.png" /></a></div></li>';
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
							html += ' <span><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></span></li>';
							j++;
						}
					}
				break;
				case 'video':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t=='video' || a.t=='audio' || a.t=='flash') {
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+' a-sortable" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f)+'" target="_blank"><img src="<?php echo FTP_EXT?>tpls/img/media-playback-start.png" /></a><br />'+a.f;
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></div></li>';
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
</script>
<?php 
$this->title = $title.$this->post('title', false);
$this->width = 800;
$tab_height = 400;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_main_<?php echo $this->name_id?>"><?php echo lang('$Details')?></a></li>
			<li><a href="#a_body_<?php echo $this->name_id?>"><?php echo lang('$Body')?></a></li>
			<li><a href="#a_images_<?php echo $this->name_id?>"><?php echo lang('$Images')?></a></li>
			<li><a href="#a_orders_<?php echo $this->name_id?>"><?php echo lang('$Orders')?></a></li>
			<li><a href="#a_changes_<?php echo $this->name_id?>"><?php echo lang('$Price changes')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="3" width="85%"><select name="data[catref]" style="width:70%;font-family:Tahoma">
				<option value="0"><?php echo lang('$-- Top category --')?></option>
				<?
				$params = array(
					'table'		=> 'category_catalogue',
					'lang'		=> $this->lang,
					'catalogue'	=> false,
					'retCount'	=> false,
					'getAfter'	=> false,
					'noDisable'	=> true,
					'maxLevel'	=> 0,
					'optValue'	=> 'catref',
					'getHidden'	=> true,
					'selected'	=> $this->post('catref', false)
				);
				echo Factory::call('category', $params)->getAll()->toOptions();
				?>
					</select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Title:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo strform($this->post('title', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Sub-title:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[title2]" value="<?php echo strform($this->post('title2', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Description:')?></td><td class="a-r" colspan="3"><textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:125px;visibility:hidden"><?php echo strform($this->post('body', false))?></textarea><textarea name="data[body]" id="a-w-body2_<?php echo $this->name_id?>" style="display:none"></textarea></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Price:')?></td><td class="a-r" width="35"><input type="text" class="a-input" style="width:150px" name="data[price]" value="<?php echo $this->post('price')?>" /> &euro;</td>
					<td class="a-l" width="15%"><?php echo lang('$In stock:')?></td><td class="a-r" width="35%"><input type="text" class="a-input" style="width:100px" name="data[instock]" value="<?php echo $this->post('instock')?>" /> <?php echo lang('$items')?></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Old price:')?></td><td class="a-r" width="35"><input type="text" class="a-input" style="width:150px" name="data[price_old]" value="<?php echo $this->post('price_old')?>" /> &euro;</td>
					<td class="a-l" width="15%"><?php echo lang('$Expires:')?></td><td class="a-r" width="35%"><input type="text" class="a-input a-date" style="width:100px" name="data[expires]" value="<?php echo $this->post('expires')?>" /></td>
				</tr>
			</table>
		</div>
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
			<?php foreach ($this->post('options', false) as $o):
			if ($o) {?>
			<tr><td class="a-r"><input type="text" name="data[options][]" class="a-input" style="width:98%" value="<?php echo strform($o)?>" /></td></tr>
			<?php 
			}
			endforeach;?>
			<tr><td class="a-r"><input type="text" name="data[options][]" class="a-input" style="width:98%" value="" /></td></tr>
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
		<div id="a_orders_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<?php $this->inc('orders')?>
		</div>
		<div id="a_changes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<?php $this->inc('changes')?>
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