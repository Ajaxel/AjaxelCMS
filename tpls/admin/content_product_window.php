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
* @file       tpls/admin/content_product_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = $this->post['content']['name_url'].' &gt; '.lang('$'.($this->id ? 'Edit':'Add new').' product:').' ';
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
		S.A.W.uploadify('<?php echo $this->name_id?>','doc','<?php echo File::uploadifyExt(array('doc','docx','rtf','pdf','ppt','pptx','txt','zip','rar','html','htm','xls','xlsx'))?>','Document files');
		S.A.W.uploadify('<?php echo $this->name_id?>','video','<?php echo File::uploadifyExt(array('asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv', 'm4v','swf','flv','aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'))?>','Video / Flash files');
		S.A.W.tabs('<?php echo $this->name_id?>');
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files,'photo');
		this.setImages(files,'doc');
		this.setImages(files,'video');
		var opts = <?php echo $this->json_array['options']?>;
		var group = <?php echo $this->json_array['group_spec']?>;
		this.setOptions(opts,'spec',3,'input',group);
		var group = <?php echo $this->json_array['group_access']?>;
		this.setOptions(opts,'access',2,'textarea',group);
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
	,sortable_options:function() {
		$('table.sortable_<?php echo $this->name_id?> tbody').unbind('sortable').sortable({
			axis: 'y',
			items: '>tr.a-sortable',
			update: function(e, ui) {
				var arr='';
				$(this).find('tr').each(function(){
					var o=$(this);
					var id=o.attr('id').substring(4);
					arr += id+'-'+(this.rowIndex+1)+'|';
				});	
				$(this).sortable('refresh');
			},
			tolerance: 'pointer',
			forceHelperSize: true,
			forcePlaceholderSize: true
		});	
	}
	,setOptions:function(opts,option_group,max_add,type,group,group_name) {
		if (!group_name) group_name = '';
		var html = '<table class="a-form sortable_<?php echo $this->name_id?>" id="a-options_'+option_group+'_<?php echo $this->name_id?>" cellspacing="0"><tbody>';
		html += '<tr><td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/bookmark-toolbar.png" /></td>';
		html += '<td class="a-l"><?php echo lang('$Select group:')?></td><td class="a-r" id="a-options_top_<?php echo $this->name_id?>"><select onchange="S.A.W.selectGroup(\'<?php echo $this->name_id?>\',this,{option_group:\''+option_group+'\', max_add:\''+max_add+'\', type:\''+type+'\', group:this.value})"><option value=""></option>';
		for (i=0;i<group.length;i++) {
			html += '<option value="'+group[i]+'"'+(group_name==group[i]?' selected':'')+'>'+group[i]+'</option>';	
		}
		html += '</select>&nbsp;<?php echo lang('$Save current keys to group:')?> <input value="'+group_name+'" /> <button type="button" class="a-button" onclick="S.A.W.saveOptionGroup(\'<?php echo $this->name_id?>\',$(this).parent().parent().parent(),\'.a-option_key_'+option_group+'\',this,{option_group:\''+option_group+'\', max_add:\''+max_add+'\', type:\''+type+'\', group:$(this).prev().val()})"><?php echo lang('$OK')?></button></td><td class="a-l">&nbsp;</td></tr>';
		var ti=0;
		var models = 1;
		if (opts[option_group]&&typeof(opts[option_group]['<?php echo lang('Model')?>'])!='undefined') {
			models = opts[option_group]['<?php echo lang('Model')?>'].length;
			if (!models) models = 1;
		}

		var width = parseInt(98 / models);
		html += '<tr><td class="a-l">&nbsp;</td><td class="a-l" width="25%"><input type="hidden" name="data[option_key]['+option_group+'][]" value="<?php echo lang('Model')?>" /><?php echo lang('$Model')?></td><td class="a-r" width="75%" nowrap>';
		for (s=0;s<models;s++) {
			html += '<input type="text" name="data[option_val]['+option_group+'][]" class="a-input a-option a-option_value'+(!s?' a-option_first':'')+'" value="'+((opts[option_group] && typeof(opts[option_group]['<?php echo lang('Model')?>'])!='undefined')?opts[option_group]['<?php echo lang('Model')?>'][s]:'')+'" style="width:'+width+'%" />';
		}
	
		html += '</td><td class="a-l a-action_buttons" align="right"><a href="javascript:;" onclick="S.A.W.addModelOption(\'<?php echo $this->name_id?>\',this,\''+option_group+'\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a>&nbsp;<a href="javascript:;" onclick="S.A.W.delModelOption(\'<?php echo $this->name_id?>\',this,\''+option_group+'\')"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" /></a></td></tr>';	

		for (key in opts[option_group]) {
			if (key=='<?php echo lang('$Model')?>') continue;
			val = opts[option_group][key];
			ti++;
			html += '<tr class="a-sortable"><td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/transform-move.png" /></td><td class="a-l" width="25%"><input type="text" tabindex="'+ti+'" class="a-input a-option a-option_key_'+option_group+'" style="width:99%" name="data[option_key]['+option_group+'][]" value="'+S.A.P.js(key)+'" /></td>';
			ti++;
			html += '<td class="a-r" width="75%" nowrap>';
			for (s=0;s<models;s++) {
				html += (type=='textarea'?'<textarea class="a-textarea a-option a-option_value'+(!s?' a-option_first':'')+'" tabindex="'+ti+'" style="height:50px;width:'+width+'%" name="data[option_val]['+option_group+'][]">'+S.A.P.js(val[s])+'</textarea>':'<input type="text" class="a-input a-option a-option_value'+(!s?' a-option_first':'')+'" tabindex="'+ti+'" style="width:'+width+'%" name="data[option_val]['+option_group+'][]" value="'+S.A.P.js(val[s])+'" />');
			}

			html += '</td><td class="a-l a-action_buttons" align="right"><a href="javascript:;" onclick="S.A.W.copyOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-copy.png" /></a>&nbsp;<a href="javascript:;" onclick="S.A.W.delOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" /></a></td></tr>';
		}
		for (i=1;i<max_add;i++) {
			ti++;
			html += '<tr class="a-sortable"><td class="a-l"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/transform-move.png" /></td><td class="a-l" width="25%"><input type="text" class="a-input a-option a-option_key_'+option_group+'" style="width:99%" tabindex="'+ti+'" name="data[option_key]['+option_group+'][]" /></td><td class="a-r" width="75%" nowrap>';
			for (s=0;s<models;s++) {
				html += (type=='textarea'?'<textarea class="a-textarea a-option a-option_value'+(!s?' a-option_first':'')+'" style="height:50px;width:'+width+'%" tabindex="'+ti+'" name="data[option_val]['+option_group+'][]"></textarea>':'<input type="text" class="a-input a-option a-option_value'+(!s?' a-option_first':'')+'" style="width:'+width+'%" tabindex="'+ti+'" name="data[option_val]['+option_group+'][]" />');
			}
			html += '</td><td class="a-l a-action_buttons" align="right"><a href="javascript:;" onclick="S.A.W.copyOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-copy.png" /></a>&nbsp;<a href="javascript:;" onclick="S.A.W.addOption(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a></td></tr>';
		}
		html += '</tbody></table><input type="hidden" id="a-total_models_'+option_group+'_<?php echo $this->name_id?>" value="'+models+'" />';
		$('#a_'+option_group+'_<?php echo $this->name_id?>').html(html);
		this.sortable_options();
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
			<li><a href="#a_body_<?php echo $this->name_id?>"><?php echo lang('$Body')?></a></li>
			<li><a href="#a_spec_<?php echo $this->name_id?>"><?php echo lang('$Specifications')?></a></li>
			<li><a href="#a_access_<?php echo $this->name_id?>"><?php echo lang('$Accessories')?></a></li>
			<li><a href="#a_options_<?php echo $this->name_id?>"><?php echo lang('$Options')?></a></li>
			<li><a href="#a_images_<?php echo $this->name_id?>"><?php echo lang('$Images')?></a></li>
			<li><a href="#a_docs_<?php echo $this->name_id?>"><?php echo lang('$Documents')?></a></li>
			<li><a href="#a_videos_<?php echo $this->name_id?>"><?php echo lang('$Videos')?></a></li>
			<li><a href="#a_orders_<?php echo $this->name_id?>"><?php echo lang('$Orders')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[catref]" style="width:70%;font-family:Tahoma">
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
			<table class="a-form" cellspacing="0">
				<tr><td class="a-r" width="85%">
					<textarea type="text" class="a-textarea" name="data[descr]" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:298px;visibility:hidden"><?php echo $this->post('descr')?></textarea>
				</td></tr>
			</table>
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Price:')?></td>
					<td class="a-r" width="35%"><input type="text" class="a-input" id="a-w-price_<?php echo $this->name_id?>" style="width:40%" name="data[price]" value="<?php echo ($this->post('price', false)=='0.00'?'':$this->post('price', false))?>" /> <select style="width:50%" name="data[currency]"><?php echo Html::buildOptions($this->post('currency', false),array_label($this->currencies,1))?></select></td>
					<td class="a-l" width="15%"><?php echo lang('$In stock:')?></td>
					<td class="a-r" width="10%"><input type="text" class="a-input" id="a-w-instock_<?php echo $this->name_id?>" style="width:99%" name="data[instock]" value="<?php echo $this->post('instock')?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Dated:')?></td>
					<td class="a-r" width="10%"><input type="text" class="a-input a-date" style="width:120px" name="data[dated]" value="<?php echo $this->post('dated')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Old price:')?></td>
					<td class="a-r" width="35%"><input type="text" class="a-input" id="a-w-price_<?php echo $this->name_id?>" style="width:40%" name="data[price_old]" value="<?php echo ($this->post('price_old', false)=='0.00'?'':$this->post('price_old', false))?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Product code:')?></td>
					<td class="a-r" width="10%"><input type="text" class="a-input" id="a-w-code_<?php echo $this->name_id?>" style="width:99%" name="data[code]" value="<?php echo $this->post('code')?>" /></td>

					<td class="a-l" width="15%"><?php echo lang('$Sort:')?></td>
					<td class="a-r" width="10%"><label for="a-w-active_<?php echo $this->name_id?>"><input type="text" class="a-input" id="a-w-sort_<?php echo $this->name_id?>" style="width:50%" name="data[sort]" value="<?php echo $this->post('sort')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Flags:')?></td>
					<td width="35%" colspan="5">
						<table cellspacing="0" cellpadding="0" class="a-form a-form_all" style="width:auto"><tr>
						<td class="a-r"><label for="a-w-bestseller_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-bestseller_<?php echo $this->name_id?>" name="data[bestseller]" value="1"<?php echo ($this->post('bestseller', false)?' checked="checked"':'')?> /> <?php echo lang('$Best seller')?></label></td>
						<td class="a-r"><label for="a-w-main_page_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-main_page_<?php echo $this->name_id?>" name="data[main_page]" value="1"<?php echo ($this->post('main_page', false)?' checked="checked"':'')?> /> <?php echo lang('$Main page')?></label></td>
						<td class="a-r"><label for="a-w-bodylist_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-bodylist_<?php echo $this->name_id?>" name="data[bodylist]" value="1"<?php echo ($this->post('bodylist', false)?' checked="checked"':'')?> /> <?php echo lang('$Show full')?></label></td>
					</tr></table>
				</tr>
			</table>
		</div>
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0"><tr><td class="a-r">
				<textarea type="text" class="a-textarea" name="data[body]" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:425px;visibility:hidden"><?php echo $this->post('body')?></textarea>
			</td></tr></table>
		</div>
		<div id="a_spec_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab"></div>
		<div id="a_access_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab"></div>
		<div id="a_options_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Product options:')?></td><td class="a-r" colspan="5" width="85%">
						coming soon
					</td>
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
		<div id="a_videos_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload video files:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-video_<?php echo $this->name_id?>" style="width:80px;" size="2" />
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Video files:')?></td>
					<td class="a-r">
						<div id="a-videos_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No video files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
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
		<div id="a_orders_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Orders:')?></td><td class="a-r" colspan="5" width="85%">
						coming soon
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