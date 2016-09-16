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
* @file       tpls/admin/content_catalogue_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' item to catalogue:').' ';
$this->title = $title.$this->post('title', false);
$this->height = 575;
$this->width = 850;
$tab_height = 452;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		var editor={
			element: '#a-w-descr_<?php echo $this->name_id?>',
			height: 298,
			base: '<?php echo HTTP_BASE?>',
			full: false,
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		var editor={
			element: '#a-w-body_<?php echo $this->name_id?>',
			height: 425, 
			base: '<?php echo HTTP_BASE?>',
			full: true,
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);

		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image files');
		var video_ext = '<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png','asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv', 'm4v','swf','flv','aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'))?>';
		S.A.W.uploadify_one('<?php echo $this->name_id?>','videos_0',video_ext,'Video, flash or image as preview','save_videos');
		S.A.W.uploadify_one('<?php echo $this->name_id?>','videos_1',video_ext,'Video, flash or image as preview','save_videos');
		S.A.W.uploadify_one('<?php echo $this->name_id?>','videos_2',video_ext,'Video, flash or image as preview','save_videos');
		S.A.W.uploadify('<?php echo $this->name_id?>','doc','<?php echo File::uploadifyExt(array('doc','docx','rtf','pdf','ppt','pptx','txt','zip','rar','html','htm','xls','xlsx'))?>','Document files');
		S.A.W.tabs('<?php echo $this->name_id?>');
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files,'photo');
		this.setImages(files,'doc');
		this.setFiltersHtml(<?php echo $this->json_array['materials']?>);
		
	}
	,setFilters:function(content,filter_shadow,filter_color) {
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=content_catalogue&get=materials',{
			content: content,
			filter_shadow: filter_shadow,
			filter_color: filter_color,
			id: <?php echo $this->id?>
		}, function(data){
			window_<?php echo $this->name_id?>.setFiltersHtml(data);
		});
	}
	,setFilter:function(obj) {
		var opts = $('#a-i_materials_<?php echo $this->name_id?>');
		var val = opts.val();
		var v=obj.value;
		if(obj.checked){
			opts.val(val+','+v);
		} else {
			opts.val(val.replace(','+v,'').replace(','+v,'').replace(','+v,''));
		}
	}
	,setFiltersAll:function(checked) {
		if (checked) {
			$('.a_chk_materials').attr('checked','true');
		} else {
			$('.a_chk_materials').attr('checked','');
		}
		$('.a_chk_materials').each(function(){
			window_<?php echo $this->name_id?>.setFilter(this);
		});
	}
	,setFiltersHtml:function(data) {
		if (!data) return;
		var html = '<ul style="width:100%">',a;
		for (i=0;i<data.length;i++){
			a=data[i];
			html += '<li class="a-l" style="width:85px;height:100px;overflow:hidden;float:left"><div style="height:65px;overflow:hidden"><img src="/<?php echo $this->prefix?>files/content_materials/'+a.rid+'/th3/'+a.main_photo+'" /></div><div><input class="a_chk_materials" type="checkbox"'+(a.checked?' checked="checked"':'')+' onclick="window_<?php echo $this->name_id?>.setFilter(this)" value="'+a.rid+'" /> '+a.code+'</div><div>'+a.title+'</div></li>';
		}
		html += '</ul><input type="hidden" name="materials" id="a-i_materials_<?php echo $this->name_id?>" value=",<?php echo ($this->post('options', false)?join(',',$this->post('options', false)):'')?>" />';
		$('#a_materials_<?php echo $this->name_id?>_div').html(html);
	}
	,setPhoto:function(file, name){
		if (!file) {
			$('#a-w-'+name+'_<?php echo $this->name_id?>').html('');
			$('#a-i-video_1_<?php echo $this->name_id?>').val('');
		} else {
			var s = file.split('/');
			var n = s[s.length-1];
			var s = n.split('.');
			var e = s[s.length-1];
			var s = name.split('_');
			var i = s[1];
			var img = S.A.D.get('images');			
			if (S.A.P.in_array(e,img)) {
				var html = '<a href="/'+file+'" target="_blank"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',n,'th3')+'" /></a>';
				var div = 'image';
			} else {
				var html = '<a href="'+file+'" target="_blank">'+n+'</a>';
				var div = 'video';
			}
			$('#a-w-'+div+'_'+i+'_<?php echo $this->name_id?>').html(html);
		}
	}
	,setImages:function(files,name){
		var html = '<ul id="a-'+name+'s_<?php echo $this->name_id?>">';
		var j=0;
		if (files && files.length) {
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
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_main_<?php echo $this->name_id?>"><?php echo lang('$Details')?></a></li>
			<li><a href="#a_body_<?php echo $this->name_id?>"><?php echo lang('$Tech info')?></a></li>
			<li><a href="#a_images_<?php echo $this->name_id?>"><?php echo lang('$Images')?></a></li>
			<?php /*<li><a href="#a_materials_<?php echo $this->name_id?>"><?php echo lang('$Materials')?></a></li>*/?>
			<li><a href="#a_videos_<?php echo $this->name_id?>"><?php echo lang('$Videos')?></a></li>
			<li><a href="#a_docs_<?php echo $this->name_id?>"><?php echo lang('$PDF materials')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[catref]" style="width:70%;font-family:Tahoma">
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
				echo Factory::call('category', $params)->getAll()->toOptions();;
				?>
					</select></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
				</tr>
			</table>
			<table class="a-form" cellspacing="0"><tr>
				<tr><td class="a-r" width="85%">
				<textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:298px;visibility:hidden"><?php echo $this->post('descr')?></textarea><textarea name="data[descr]" id="a-w-descr2_<?php echo $this->name_id?>" style="display:none"></textarea>
			</td></tr></table>
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Price:')?></td>
					<td class="a-r" width="35%"><input type="text" class="a-input" id="a-w-price_<?php echo $this->name_id?>" style="width:40%" name="data[price]" value="<?php echo ($this->post('price', false)=='0.00'?'':$this->post('price', false))?>" /> <select style="width:50%" name="data[currency]"><?php echo Html::buildOptions($this->post('currency', false),array_label($this->currencies,1))?></select></td>
					<td class="a-l" width="15%"><?php echo lang('$In stock:')?></td>
					<td class="a-r" width="10%"><input type="text" class="a-input" id="a-w-instock_<?php echo $this->name_id?>" style="width:99%" name="data[instock]" value="<?php echo $this->post('instock')?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Active:')?></td>
					<td class="a-r" width="10%"><label for="a-w-active_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-active_<?php echo $this->name_id?>" name="data[active]" value="1"<?php echo ($this->post('active', false)?' checked="checked"':'')?> /> <?php echo lang('$Yes')?></label></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Old price:')?></td>
					<td class="a-r" width="35%"><input type="text" class="a-input" id="a-w-price_<?php echo $this->name_id?>" style="width:40%" name="data[price_old]" value="<?php echo ($this->post('price_old', false)=='0.00'?'':$this->post('price_old', false))?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Product code:')?></td>
					<td class="a-r" width="10%"><input type="text" class="a-input" id="a-w-code_<?php echo $this->name_id?>" style="width:99%" name="data[code]" value="<?php echo $this->post('code')?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Sort:')?></td>
					<td class="a-r" width="10%"><input type="text" class="a-input" id="a-w-sort_<?php echo $this->name_id?>" style="width:50%" name="data[sort]" value="<?php echo $this->post('sort')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Flags:')?></td>
					<td width="35%" colspan="5">
						<table cellspacing="0" cellpadding="0" class="a-form a-form_all" style="width:auto"><tr>
						<td class="a-r"><label for="a-w-bestseller_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-bestseller_<?php echo $this->name_id?>" name="data[bestseller]" value="1"<?php echo ($this->post('bestseller', false)?' checked="checked"':'')?> /> <?php echo lang('$Best seller')?></label></td>
						<td class="a-r"><label for="a-w-main_page_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-main_page_<?php echo $this->name_id?>" name="data[main_page]" value="1"<?php echo ($this->post('main_page', false)?' checked="checked"':'')?> /> <?php echo lang('$Main page')?></label></td>
						<td class="a-r"><label for="a-w-comments_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-comments_<?php echo $this->name_id?>" name="data[comments]" value="1"<?php echo ($this->post('comments', false)?' checked="checked"':'')?> /> <?php echo lang('$Comments allowed')?></label></td>
					</tr></table>
				</tr>
			</table>
		</div>
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0"><tr><td class="a-r">
				<textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:425px;visibility:hidden"><?php echo $this->post('body')?></textarea><textarea name="data[body]" id="a-w-body2_<?php echo $this->name_id?>" style="display:none"></textarea>
			</td></tr></table>
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
		<?php /*
		<div id="a_materials_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Content:')?></td><td class="a-r" colspan="2" width="85%"><select id="a-content_<?php echo $this->name_id?>" onchange="window_<?php echo $this->name_id?>.setFilters(this.value,0,0)"><option value=""></option><?php echo Html::buildOptions(0,$this->array['filter_content'])?></select></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Filter shadow:')?></td><td class="a-r" colspan="2" width="85%"><select id="a-filter_shadow_<?php echo $this->name_id?>" onchange="window_<?php echo $this->name_id?>.setFilters($('#a-content_<?php echo $this->name_id?>').val(),this.value,$('#a-filter_color_<?php echo $this->name_id?>').val())"><option value=""></option><?php echo Data::getArray('my:filter_shadow',$this->post('filter_shadow', false))?></select></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Filter color:')?></td><td class="a-r" colspan="2" width="85%"><select id="a-filter_color_<?php echo $this->name_id?>" onchange="window_<?php echo $this->name_id?>.setFilters($('#a-content_<?php echo $this->name_id?>').val(),$('#a-filter_shadow_<?php echo $this->name_id?>').val(),this.value)"><option value=""></option><?php echo Data::getArray('my:filter_color',$this->post('filter_color', false))?></select></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Select all:')?></td><td class="a-r" colspan="2" width="85%"><input type="checkbox" onclick="window_<?php echo $this->name_id?>.setFiltersAll(this.checked)"></td>
				</tr>
				<tr>
					<td colspan="2" class="a-r" id="a_materials_<?php echo $this->name_id?>_div">
						<div class="a-no_files"><?php echo lang('$Please select a content name')?></div>
					</td>
				</tr>
			</table>
		</div>
		*/ ?>
		<div id="a_videos_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php 
				$videos = $this->post('videos', false);
				$i = 0;
				foreach ($this->images as $name => $label):
					if (!$videos[$i]) $videos[$i] = array();
				?>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$'.$label.':')?></td><td class="a-r" colspan="5" width="85%">
						<table width="100%" cellspacing="0"><tr>
						<td width="10%">
							<input type="file" class="a-file" id="a-videos_<?php echo $i?>_<?php echo $this->name_id?>" style="width:80px;" size="2" />
							<br />
							<div id="a-w-video_<?php echo $i?>_<?php echo $this->name_id?>" style="width:200px;overflow:hidden">
								<?php if ($videos[$i]['video']):?>
									<a href="/<?php echo $videos[$i]['video']['url']?>" target="_blank"><?php echo $videos[$i]['video']['file']?></a>
								<?php endif;?>
							</div>	
						</td>
						<td width="20%"><div id="a-w-image_<?php echo $i?>_<?php echo $this->name_id?>" style="width:100px;height:90px;overflow:hidden">
							<?php if ($videos[$i]['image']):?>
								<img src="/<?php echo str_replace('/th1/','/th3/',$videos[$i]['image']['url'])?>?<?php echo $this->time?>" />
							<?php endif;?></div></td>
						<td>
							<input type="text" style="width:99%" name="data[videos][<?php echo $i?>][title]" value="<?php echo $videos[$i]['title']?>" /><br />
							<textarea style="width:99%;height:80px;margin-top:4px" name="data[videos][<?php echo $i?>][descr]"><?php echo $videos[$i]['descr']?></textarea>
						</td>
						<?php /*
						<td width="1%" nowrap>
						&nbsp;
						<a href="javascript:;" onclick="if(confirm('Are you sure?')){S.A.W.delPhoto('<?php echo $this->name_id?>','<?php echo $videos[$i]['video']['file']?>', function(){$('#a-w-video_<?php echo $i?>_<?php echo $this->name_id?>').html('');$('#a-w-image_<?php echo $i?>_<?php echo $this->name_id?>').html('')}, this, 'videos');}">[delete]</a>
						<input type="hidden" name="data[videos][<?php echo $i?>][file]" id="a-i-video_<?php echo $i?>_<?php echo $this->name_id?>" value="<?php echo $videos[$i]['file']?>" />
						</td>
						*/ ?>
						</tr></table>
					</td>
				</tr>
				<?php 
				$i++;
				endforeach;?>
			</table>
		</div>
		<div id="a_docs_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload documents:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-doc_<?php echo $this->name_id?>" style="width:80px;" size="2" />
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Document files:')?></td>
					<td class="a-r">
						<div id="a-docs_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No document files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:425px"><?php echo $this->post('notes')?></textarea>
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