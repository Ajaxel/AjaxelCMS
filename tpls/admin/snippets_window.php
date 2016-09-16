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
* @file       tpls/admin/snippets_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->options['content_save'] = 'SnippetEditor_'.$this->name_id.'.save();'.$this->options['content_save'];
$title = lang('$'.($this->id ? 'Edit':'Add new').' snippet:').' ';
$this->title = $title.$this->post('title', false);
$this->height = 550;
$this->width = 800;
?><script type="text/javascript">
var SnippetEditor_<?php echo $this->name_id?> ={
	editor:false,
	load:function(){
		ace.require("ace/commands/default_commands");		
		this.editor = ace.edit("a-snippet_body_<?php echo $this->name_id?>");
		this.editor.session.setMode("ace/mode/php");
		this.editor.setTheme("ace/theme/<?php echo ($_SESSION['AdminGlobal']['code_theme'] ? $_SESSION['AdminGlobal']['code_theme'] : 'tomorrow')?>");
		this.editor.setOptions({
			enableBasicAutocompletion: true,
			enableSnippets: false,
			enableLiveAutocompletion: true,
			wrap:'free',
			wrapBehavioursEnabled: true,
			highlightActiveLine: false,
			autoScrollEditorIntoView: true
		});
		$(window).keydown(function(e){
			if (e.keyCode==83 && e.ctrlKey) {
				e.preventDefault();
				return false;
			}
		});
		$('#a-snippet_body_<?php echo $this->name_id?>').focus();
	},
	save:function(){
		$('#a-snippet_body_to_<?php echo $this->name_id?>').text(this.editor.getValue());
	}
}
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?> '+this.value);
		});
		
		S.G.addJS('<?php echo FTP_EXT.'tpls/js/ace/ace.js';?>',function(){
			SnippetEditor_<?php echo $this->name_id?>.load();
		});
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Function:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:59%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:39%" name="data[category]" id="a-w-category_<?php echo $this->name_id?>" value="<?php echo $this->post('category')?>" /> &lt;&lt; <select onchange="$(this).prev().val(this.value)" style="width:30%" class="a-select"><option value=""></option><?php echo Html::buildOptions($this->post('category', false), $this->array['categories'], true)?></select></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Description:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" class="a-input" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
		</tr>
	</table>
	<table class="a-form" cellspacing="0"><tr><td class="a-r">
	<div id="a-snippet_body_<?php echo $this->name_id?>" spellcheck="false" wrap="off" autocorrect="off" autocapitalize="off" style="font-size:12px;width:99%;height:421px" placeholder="Code goes here..." class="a-textarea a-textarea_code"><?php echo $this->post('source');?></div>
	</td></tr></table>
	<?php
	$this->inc('bottom', array(
		'copy'			=> false,
		'add'			=> false,
	)); ?>
	<textarea id="a-snippet_body_to_<?php echo $this->name_id?>" style="display:none" name="data[source]"></textarea>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>