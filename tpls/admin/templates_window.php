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
* @file       tpls/admin/templates_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php

if (isset($_GET['theme'])) $_SESSION['AdminGlobal']['code_theme'] = get('theme');
if (isset($_GET['no_linenumbers'])) $_SESSION['AdminGlobal']['code_no_linenumbers'] = get('no_linenumbers');
$theme = (($_SESSION['AdminGlobal']['code_theme'] && array_key_exists($_SESSION['AdminGlobal']['code_theme'],$this->output['themes'])) ? $_SESSION['AdminGlobal']['code_theme'] : 'tomorrow');

asset_a(FTP_EXT.'tpls/js/ace/ace.js','',true);

switch ($this->syntax) {
	case 'js':
		$mode = 'javascript';
	break;
	case 'css':
		$mode = 'css';
	break;
	case 'php':
		$mode = 'php';
	break;
	case 'mysql':
		$mode = 'mysql';
	break;
	case 'perl':
		$mode = 'perl';
	break;
	case 'python':
		$mode = 'python';
	break;
	case 'txt':
	case 'sql':
		$mode = 'plain_text';
	break;
	default:
		if ($this->ext=='tpl') {
			$mode = 'smarty';
		} else {
			$mode = 'html';	
		}
	break;
}
?>
<style type="text/css">
.ace_editor {
	position:fixed!important;
	bottom:0px;
	left:0px;
	right:0px;
	top:30px;
	width: 100%;
}

.ace_editor.fullScreen {
	height: auto;
	width: auto;
	border: 0;
	margin: 0;
	position: fixed !important;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 10;
}
.fullScreen {
	overflow: hidden
}

.scrollmargin {
	height: 500px;
	text-align: center;
}

.large-button {
	color: lightblue;
	cursor: pointer;
	font: 30px arial;
	padding: 20px;
	text-align: center;
	border: medium solid transparent;
	display: inline-block;
}
.large-button:hover {
	border: medium solid lightgray;
	border-radius: 10px 10px 10px 10px;
	box-shadow: 0 0 12px 0 lightblue;
}
</style>
<script type="text/javascript">
<?php echo Index::CDA?>

var TemplateEditor ={
	editor:false,
	load:function(){

		ace.require("ace/ext/language_tools");
		ace.require("ace/commands/default_commands");
		this.editor = ace.edit("a-template_body");
		this.editor.session.setMode("ace/mode/<?php echo $mode?>");
		this.editor.setTheme("ace/theme/<?php echo $theme?>");
		
		this.editor.setOptions({
			enableBasicAutocompletion: true,
			enableSnippets: false,
			enableLiveAutocompletion: true,
			wrap:'free',
			wrapBehavioursEnabled: true,
			highlightActiveLine: false,
			autoScrollEditorIntoView: true
		});
		
		this.editor.commands.addCommand({
			name: 'Save',
			bindKey: {win: 'Ctrl-S',  mac: 'Command-S'},
			exec: function(editor) {
				TemplateEditor.save(true);
				return false;
			},
			readOnly: true
		});
		this.editor.commands.addCommand({
			name: 'SaveClose',
			bindKey: {win: 'Ctrl-W',  mac: 'Command-W'},
			exec: function(editor) {
				TemplateEditor.save();
				return false;
			},
			readOnly: true
		});
		this.dom = require("ace/lib/dom");
		this.editor.commands.addCommand({
			name: "Toggle Fullscreen",
			bindKey: "F11",
			exec: function(editor) {
				var fullScreen = TemplateEditor.dom.toggleCssClass(document.body, "fullScreen")
				TemplateEditor.dom.setCssClass(editor.container, "fullScreen", fullScreen)
				editor.setAutoScrollEditorIntoView(!fullScreen)
				editor.resize()
			}
		});
		
		
		$(window).keydown(function(e){
			if (e.keyCode==83 && e.ctrlKey) {
				e.preventDefault();
				return false;
			}
		});

		this.size();
	},
	size:function(){

	},
	value:function() {
		return this.editor.getValue();
	},
	save:function(a){
		$('#a-file_saved').hide();
		$('#a-file_loading').show();
		<?php if ($this->post['no_save']):?>return false;<?php endif;?>
		var v=this.value();
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=templates&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $this->folder?>', {
			get: 'action',
			a: 'save',
			file: '<?php echo $this->file?>',
			text: v
		},function(data){
			if (!a) window.close();
			else {
				$('#a-file_loading').hide();
				$('#a-file_saved').html(data.text).stop().fadeIn(function(){
					$(this).delay(1000).fadeOut();
				});
			}
		});
	}
}
$(function(){
	TemplateEditor.load();
	$('.a-button').button();
	$(window).resize(function(){
		TemplateEditor.size();
	});
});
<?php echo Index::CDZ?>
</script>
<div class="a-abs a-window-wrapper ui-dialog ui-widget ui-widget-content ui-draggable a-search" style="width:100%;line-height:100%!important;padding:2px 4px!important">
<table cellspacing="0" cellpadding="0" width="100%"><tr>
<td nowrap<?php if ($this->post['no_save']):?> disabled="disabled"<?php endif;?> style="padding-top:2px">
	<?php $this->inc('button',array('class'=>'a-button a-button_b','title'=>'CTRL+W','click'=>'TemplateEditor.save()','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save and close'))); ?>
	&nbsp;
	<?php $this->inc('button',array('class'=>'a-button a-button_b','title'=>'CTRL+S','click'=>'TemplateEditor.save(true)','img'=>'oxygen/16x16/actions/document-save-as.png','text'=>lang('$Save'))); ?>
</td>
<td style="padding-left:10px">
	<select onchange="location.replace('?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $this->folder?>&file='+this.value);" class="a-select a-r" style="font-size:13px;width:200px">
		<option value="" style="color:#666"><?php echo lang('-- file --')?></option>
		<?php foreach ($this->output['files'] as $file):
	?>
	<option value="<?php echo $file;?>"<?php echo ($this->file==$file ? ' selected':'')?>><?php echo $file;?></option>
	<?php endforeach;?>	
	</select>

</td>
<?php
		$ex = explode('/',$this->folder);
		array_pop($ex);
		$prev = join('/',$ex);
	?>
	<?php if ($this->folder):?>
<td style="padding-left:10px">
	<a href="?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $prev?>"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/go-up.png" /></a>
</td>
<?php endif;?>
<td style="padding-left:10px">	
	<select onchange="location.replace('?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder='+this.value);" class="a-select a-r" style="font-size:13px;width:140px">
		<option value="" style="color:#666"><?php echo lang('-- folder --')?></option>
		<option value="<?php echo $prev?>">..<?php echo $prev;?></option>
		<?php foreach ($this->output['dirs'] as $dir):
	?>
	<option value="<?php echo str_replace(' ','%20',$dir);?>"><?php echo $dir;?></option>
	<?php endforeach;?>	
	</select>
</td>
<td style="white-space:nowrap;padding-left:5px;color:green;font-weight:bold"><div id="a-file_saved" style="display:none"></div><div id="a-file_loading" style="display:none"><img src="<?php echo FTP_EXT?>tpls/img/loading/loading6.gif" /></div></td>
<td width="80%" class="a-r" style="text-align:right;padding-right:5px;">
	<select onchange="location.replace('?window&<?php echo URL_KEY_ADMIN?>=templates&popup=1&file_folder=<?php echo $this->file_folder?>&folder=<?php echo $this->folder?>&file=<?php echo $this->file?>&theme='+this.value);" class="a-select" style="font-size:13px;width:110px">
	<option value="tomorrow"><?php echo lang('$Default')?></option>
	<?php foreach ($this->output['themes'] as $t => $theme):?>
	<option value="<?php echo $t;?>"<?php echo ($_SESSION['AdminGlobal']['code_theme']==$t ? ' selected':'')?>><?php echo $theme;?></option>
	<?php endforeach;?>	
	</select>
</td>
</tr></table>
</div>
<div id="a-template_body" style="text-align:left;"><?php echo $this->post('text');?></div>
<? /*<textarea id="-a-template_body" spellcheck="false" wrap="off" autocorrect="off" autocapitalize="off" style="font-size:12px;width:100%;height:100%;" placeholder="Code goes here..." class="a-textarea a-textarea_code"><?php echo $this->post('text');?></textarea>*/?>