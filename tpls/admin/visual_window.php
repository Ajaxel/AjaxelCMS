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
* @file       tpls/admin/visual_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
var VisualEditor_<?php echo $this->name_id?> ={
	editor:false,
	load:function(){
		ace.require("ace/commands/default_commands");		
		this.editor = ace.edit("a-visual_body_<?=$this->name_id?>");
		this.editor.session.setMode("ace/mode/smarty");
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
		this.dom = require("ace/lib/dom");
		this.editor.commands.addCommand({
			name: "Toggle Fullscreen",
			bindKey: "F11",
			exec: function(editor) {
				var fullScreen = VisualEditor_<?php echo $this->name_id?>.dom.toggleCssClass(document.body, "fullScreen")
				VisualEditor_<?php echo $this->name_id?>.dom.setCssClass(editor.container, "fullScreen", fullScreen)
				editor.setAutoScrollEditorIntoView(!fullScreen)
				editor.resize()
			}
		});
		$('#a-visual_body_<?=$this->name_id?>').focus();
	}
	,image:function(){
		S.A.W.open('?<?php echo URL_KEY_ADMIN?>=global&a=image_browser&name_id=<?php echo $this->name_id?>');
	}
	,save:function(){
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=global', {
			get: 'action',
			a: 'visual_save',
			<?php if ($this->post('visual_add', false)):?>
			visual_add: '<?php echo $this->post('visual_add')?>',
			above: <?php echo $this->post('above')?>,
			tag: $('.a-radio_<?php echo $this->name_id?>:checked').val(),
			<?php else:?>
			visual_id: '<?php echo $this->post('visual_id')?>',
			<?php endif;?>
			html: this.editor.getValue()
		}, function(data){
			if (close) S.A.W.close();
			S.G.msg(data);
			if (data.files) S.A.V.put_files(data.files);
			if (data.visual_add) {
				var span = $('<span>').html(data.html);
				if (data.above==1) {
					$('#'+data.id).before(span);
				} else {
					$('#'+data.id).after(span);
				}
			}
			else if (data.html) {
				$('#'+data.id).html(data.html);
			}
		});
	}
	,keyUp:function(e){
		if (!e||!e.keyCode) e=window.event;
		if ((e.keyCode==83&&(e.ctrlKey||e.altKey))) {
			this.save(true);
		}
	}
}

var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.G.addJS('<?php echo FTP_EXT.'tpls/js/ace/ace.js';?>',function(){
			VisualEditor_<?php echo $this->name_id?>.load();
		});
	}
	,maximize:function(win_id){
		var o=S.A.W.wins[win_id];
		if (o.maximized) {
			o.win.children(0).css({
				width:o.w2,
				height: (o.h2?o.h2:null)
			});
			o.maximized=false;
			o.win.css({
				width:o.w2,
				height: (o.h2?o.h2:null),
				top:o.t2,
				left: o.l2
			});
			o.maximized=false;
			$('#a-visual_body_<?=$this->name_id?>').css({
				height: 500
			});
			S.A.W.minimized(win_id);
			VisualEditor_<?php echo $this->name_id?>.editor.setAutoScrollEditorIntoView(true);
			VisualEditor_<?php echo $this->name_id?>.editor.resize();
		} else {
			o.h2=parseInt(o.win.children(0).height());
			var w=$(window).width()-50;
			var h=$(window).height()-40;
			o.win.children(0).css({
				width:w,
				height:h
			});
			o.maximized=true;
			o.win.css({
				width:w,
				height:h,
				top:10,
				left:25
			});
			o.maximized=true;
			$('#a-visual_body_<?=$this->name_id?>').css({
				height: h-70
			});
			S.A.W.maximized(win_id);
			VisualEditor_<?php echo $this->name_id?>.editor.setAutoScrollEditorIntoView(false);
			VisualEditor_<?php echo $this->name_id?>.editor.resize();
		}
	}
}

</script>
<style type="text/css">
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
<?php 
$this->title = $this->post('title', false);
$this->width = 900;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
<?php
if ($this->post('visual_add', false)):?>
<table cellspacing="0" cellpadding="0"><tr>
<td class="a-td2">
	<?php echo lang('Start tag').': ';
		$tags = explode('|',trim(VISUAL_TAGS,'()'));
		echo '&nbsp;&nbsp; <label for="a-tag_0" style="border-bottom:none"><input type="radio" class="a-radio_'.$this->name_id.'" id="a-tag_0" value=""> none</label>';
	foreach ($tags as $i => $t) {
		$i++;
		echo '&nbsp; <label for="a-tag_'.$i.'" style="border-bottom:none"><input type="radio" class="a-radio_'.$this->name_id.'" value="'.$t.'" id="a-tag_'.$i.'"'.($t=='div'?' checked="checked"':'').'> '.strtoupper($t).'</label>';
	}
	?>
</td>
</tr></table>
<?php
endif;
?>
<div id="a-visual_body_<?=$this->name_id?>" style="font:12px 'Lucida Console', monospace;height:500px;width:99%;" onkeydown="VisualEditor_<?php echo $this->name_id?>.keyUp(event)"><?php echo strform($this->post('html', false))?></div>
<div class="a-window-bottom ui-dialog-buttonpane">
<table cellspacing="0" cellpadding="0" style="width:100%"><tr>
<td class="a-td1" style="text-align:left">
	<?php $this->inc('button',array('class'=>'a-button a-button_s','click'=>'VisualEditor_'.$this->name_id.'.image()','text'=>lang('$Insert an image'))); ?>
</td>
<td class="a-td3">
	<?php $this->inc('button',array('class'=>'a-button a-button_b	','click'=>'VisualEditor_'.$this->name_id.'.save(true)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
</td>
</tr></table>
</div>
</form>
<?php $this->inc('window_bottom')?>