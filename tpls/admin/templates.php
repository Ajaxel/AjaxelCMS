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
* @file       tpls/admin/templates.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
?><script type="text/javascript">
<?php echo Index::CDA?>
S.A.L.getHtml=function(){
	var a,h='',folder_url,edit_link,l=0,i=0;
	for(file in S.A.L.data) l++;
	if (l) {
		if (S.A.L.thumb_look) {
			h = '<table class="a-list_one" cellspacing="0"><tr><td class="a-l"><ul>';
			for(file in S.A.L.data) {
				a = S.A.L.data[file];
				h +=  '<li style="" class="ui-corner-all a-li_item">';
				<?php $this->inc('template_row_thumb')?>
				h +=  '</li>';
			}
			h +=  '</ul></td></tr></table>';
		} else {
			h = '<table class="a-list a-list-one" cellspacing="0">';
			h +=  '<tr><th width="1%"><input type="checkbox" onclick="if(this.checked)$(\'#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>\').attr(\'checked\',\'checked\'); else $(\'#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>\').removeAttr(\'checked\');" id="a_chk_<?php echo $this->name?>"></th><th width="1%">&nbsp;</th><th width="50%"><?php echo lang('$Folder/File')?></th><th><?php echo lang('$Size')?></th><th><?php echo lang('$Modified')?></th><th width="3%">&nbsp;</th></tr><tbody class="a-tbody">';
			for(file in S.A.L.data) {
				a = S.A.L.data[file];
				h +=  '<tr class="'+(i%2?'':'a-odd')+' a-hov">';
				<?php $this->inc('template_row')?>
				h +=  '</tr>';
				i++;
			}
			h +=  '</tbody></table>';
		}
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No files were found')?></div>';
	}
	
	return h;
}
S.A.L.thumbLook=function(){
	if (S.A.L.thumb_look) S.A.L.thumb_look=false; else S.A.L.thumb_look=true;
	S.A.L.ready(S.A.L.getHtml());
}
S.A.L.cutFiles=function(){
	var files=[];
	$('#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>:checked').each(function(){
		$(this).parent().parent().addClass('a-disabled');
		files.push(this.value);
	});
	S.C.temp.cut_from_folder='<?php echo $this->folder?>';
	S.C.temp.cut_from_file_folder='<?php echo $this->file_folder?>';
	S.C.temp.cut_files=files;
	$('#a-paste_<?php echo $this->name?>').show();
}
S.A.L.pasteFiles=function(){
	if (1 || confirm('Are you sure to move '+(S.C.temp.cut_files.length>1?''+S.C.temp.cut_files.length+' files':'`'+S.C.temp.cut_files[0])+'`?')) {
		var url='?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&bulk_paste=true';
		S.A.L.post(url,{
			from_folder: S.C.temp.cut_from_folder,
			from_file_folder: S.C.temp.cut_from_file_folder,
			files: S.C.temp.cut_files
		},false,false,function(){
			delete(S.C.temp.cut_files);
			delete(S.C.temp.cut_from_folder);
			delete(S.C.temp.cut_from_file_folder);
			$('#a-paste_<?php echo $this->name?>').hide();
			$('#a-buttons_<?php echo $this->name?>').hide();
		});	
	}
}

S.A.L.delFiles=function(){
	var files=[];
	$('#<?php echo $this->name?>-content .a_chk_<?php echo $this->name?>:checked').each(function(){
		files.push(this.value);
	});
	
	if (files.length) {
		if (confirm('Are you sure to delete '+(files.length>1?''+files.length+' files':'`'+files[0])+'` permanently?')) {
			S.A.L.post('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&bulk_delete=true',{
				files: files	
			});	
		}
	}
}



$(function() {
	<?php echo $this->inc('js_load')?>
	S.A.L.data = <?php echo $this->json_data?>;
	S.A.L.thumb_look=<?php echo ((isset($_SESSION['AdminGlobal']['thumbnails']) && $_SESSION['AdminGlobal']['thumbnails']) ? 'true':'false')?>;
	S.A.L.ready(S.A.L.getHtml());
	var btns=$('#a-buttons_<?php echo $this->name?>');
	S.A.L.selectable(false,function(i){
		if (i>0) btns.slideDown('fast'); else {
			btns.slideUp();
			$('#a-maildiv_<?php echo $this->name?>').slideUp();
		}
	});
	S.A.L.chk(btns);
	
	S.A.FU.upload({
		height: 300,
		name:'<?php echo $this->name?>',
		id: 0,
		limit: 200,
		sim: 4,
		upload: '<?php echo $this->upload?>/<?php echo str_replace('/','$',$this->file_folder.'/'.$this->folder)?>',
		buttonImg: S.C.FTP_EXT+'tpls/img/upload.gif',
		b_width: 48,
		b_height: 20,
	});
	
	$('#<?php echo $this->name?>-search').submit(function(){
		if ($('#new_folder').val()) {
			S.A.L.no_hash=true;
			S.A.L.get('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&new_folder='+escape($('#new_folder').val()))
		}
		return false;
	});
	if (S.C.temp.cut_files) {
		$('#a-paste_<?php echo $this->name?>').show();	
	} else {
		btns.hide();		
	}
	<?php if ($this->params['new_file']):?>
	setTimeout(function(){
		var l='/?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $this->folder?>&file=<?php echo $this->params['new_file']?>';
		S.A.W.popup(l,$(window).width()-$(window).width()/4.5,$(window).height()-$(window).height()/4.5);
	},300);
	<?php endif;?>
});
<?php echo Index::CDZ?>
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>" id="<?php echo $this->name?>-search">
<input type="hidden" name="folder" value="<?php echo $this->folder?>" />
<input type="hidden" name="file_folder" value="<?php echo $this->file_folder?>" />
<div class="a-search">
	<div class="a-l">
		<?php echo lang('$Folder:')?> <select onchange="S.A.L.get('?<?php echo URL::rq('file_folder', URL::get())?>&file_folder='+this.value);" style="width:270px">
		<?php echo Html::buildOptions($this->file_folder,array_label($this->templates));?>
		<optgroup label="<?php echo lang('$File folders')?>">
		<?php echo Html::buildOptions($this->file_folder,$this->array['file_folders'])?>
		</optgroup>
		</select>
		<?php $this->inc('search')?>
		<?php /*$this->inc('search')*/?>
		<?php /*
		<input type="checkbox" class="a-checkbox" onclick="S.A.L.global('session',{key:'editarea',value:this.checked});"<?php echo ($_SESSION['AdminGlobal']['editarea']?' checked="checked"':'')?> id="a-w_editerea" /><label for="a-w_editerea"><?php echo lang('$Edit Area')?></label>*/?>
		<label><input type="checkbox" class="a-checkbox" onclick="S.A.L.global('session',{key:'thumbnails',value:this.checked});S.A.L.thumbLook()"<?php echo ((isset($_SESSION['AdminGlobal']['thumbnails']) && $_SESSION['AdminGlobal']['thumbnails'])?' checked="checked"':'')?> id="a-w_thumb" /><?php echo lang('$Thumbnais')?></label>
		<label><input type="checkbox" class="a-checkbox" onclick="S.A.L.global('session',{key:'code_no_highlight',value:this.checked});"<?php echo ((isset($_SESSION['AdminGlobal']['code_no_highlight']) && $_SESSION['AdminGlobal']['code_no_highlight'])?' checked="checked"':'')?> /><?php echo lang('$No highlight')?></label>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div class="a-search" style="padding-right:1px">
	<div class="a-l">
	<table cellspacing="0" cellpadding="0"><tr>
	<td><?php echo join(' / ',$this->dir_links)?> /&nbsp;</td>
	<td>
		<a href="javascript:;" onclick="$(this).parent().hide();$('#<?php echo $this->name?>-new_folder_div').show();$('#<?php echo $this->name?>-new_folder').focus();"><?php echo lang('$Create new folder')?>..</a>
		<a href="javascript:;" onclick="$(this).parent().hide();$('#<?php echo $this->name?>-new_file_div').show();$('#<?php echo $this->name?>-new_file').focus();"><?php echo lang('$Create new file')?>..</a>
	</td>
	<td>
		<span style="display:none" id="<?php echo $this->name?>-new_folder_div"><input type="text" id="<?php echo $this->name?>-new_folder" onkeyup="if(this.value&&S.E.keyCode==13){S.E.reset();S.A.L.no_hash=true;S.A.L.get('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&new_folder='+escape($('#<?php echo $this->name?>-new_folder').val()))}" value="" /> <button type="button" class="a-button" onclick="S.A.L.no_hash=true;S.A.L.get('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&new_folder='+escape($('#<?php echo $this->name?>-new_folder').val()))"><?php echo lang('$Create folder');?></button> <button type="button" class="a-button" onclick="$(this).parent().hide();$(this).parent().parent().prev().show();"><?php echo lang('$Cancel');?></button></span>
		<span style="display:none" id="<?php echo $this->name?>-new_file_div"><input type="text" id="<?php echo $this->name?>-new_file" onkeyup="if(this.value&&S.E.keyCode==13){S.E.reset();S.A.L.no_hash=true;S.A.L.get('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&new_ext='+escape($('#<?php echo $this->name?>-new_ext').val())+'&new_file='+escape($('#<?php echo $this->name?>-new_file').val()))}" value="" /> <select id="<?php echo $this->name?>-new_ext" onchange="if(this.value=='htaccess')$(this).prev().css('visibility','hidden');else {$(this).prev().css('visibility','visible').focus()}"><?php echo Html::buildOptions($this->create_ext,$this->array['new_file_ext'])?></select> <button type="button" class="a-button" onclick="S.A.L.no_hash=true;S.A.L.get('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&folder=<?php echo $this->folder?>&file_folder=<?php echo $this->file_folder?>&new_ext='+escape($('#<?php echo $this->name?>-new_ext').val())+'&new_file='+escape($('#<?php echo $this->name?>-new_file').val()))"><?php echo lang('$Create file');?></button> <button type="button" class="a-button" onclick="$(this).parent().hide();$(this).parent().parent().prev().show();"><?php echo lang('$Cancel');?></button></span>
	</td>
	</tr></table>
	</div>
	<div class="a-r" style="line-height:100%;padding-right:0px" id="a-filediv_<?php echo $this->name?>">
		<div id="jquery_upload">
			<img src="<?php echo FTP_EXT?>tpls/img/upload.gif" style="margin:0;position:relative;z-index:0;left:48px;margin-left:-48px;" />
			<input type="file" id="a-file_<?php echo $this->name?>2" name="Filedata" multiple style="margin:0!important;padding:0!important;opacity:0;filter: alpha(opacity=0);width:48px;height:15px;position:relative;z-index:1;cursor:pointer;cursor:hand;" /></span>
		</div>
		
	</div>
</div>

<div id="progress" class="progress ui-state-default" style="padding:2px 1%;display:none;width:98%">
	<div class="progress-title" style="font-weight:bold"></div>
	<div class="progress-bar progress-bar-success ui-state-highlight" style="padding:0;overflow:hidden;height:20px;width:1%;line-height:20px;text-align:right;font-weight:bold"></div>
</div>
<div id="files" class="files"></div>


<?php $this->inc('list')?>
<div class="a-buttons<?php echo $this->ui['buttons']?>" id="a-buttons_<?php echo $this->name?>">
	<table cellspacing="0" style="width:100%"><tr>
		<td class="a-left"><button type="button" onclick="S.A.L.cutFiles(this)" class="a-button a-button_a"><?php echo lang('$Cut')?></button> <button type="button" onclick="S.A.L.pasteFiles(this)" style="display:none" id="a-paste_<?php echo $this->name?>" class="a-button a-button_a"><?php echo lang('$Paste')?></button> <button type="button" class="a-button a-button_a" onclick="S.A.L.delFiles(this)"><?php echo lang('$Delete..')?></button> <button style="display:none" type="button" class="a-button a-button_a"><?php echo lang('$Resize..')?></button></td>
		<td class="a-c">&nbsp;</td>
		<td class="a-right" style="text-align:right"></td>
	</tr></table>
</div>
</form>
<?php $this->inc('bot')?>
</div>