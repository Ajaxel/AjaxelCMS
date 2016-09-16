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
* @file       tpls/admin/mods_ai_tab_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit AI question':'Add new AI question').':').' ';
$this->title = $title.$this->post('question', false);
$this->width = 700;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?>'+this.value);
		});
		var answers = <?php echo json
($this->post('answers', false))?>;
		var emotions=<?php echo json
($this->array['emotions'])?>,anchors=<?php echo json($this->array['anchors'])?>,opts='<option value="" style="color:#999"><?php echo lang('$-- anchor --')?></option>',opts2='<option value="" style="color:#999"><?php echo lang('_$-- new anchor --')?></option><option value="#new" style="color:green"><?php echo lang('_$-- create new --')?></option>',e_opts='',o='';
		for (i=0;i<anchors.length;i++)o+='<option value="'+anchors[i]+'">'+anchors[i]+'</option>';
		for (i=0;i<emotions.length;i++)e_opts+='<option value="'+i+'">'+emotions[i]+'</option>';
		opts2+=o;
		var html = '',index=1;
		<?php $this->inc('js_ai_answers', array('id'=>$this->post('id', false)))?>
		$('#a-ai_<?php echo $this->name_id?>').html(html);
		$('#a-form_<?php echo $this->name_id?> .a-ai_ancor').change(function(){
			if(this.value=='#new'){
				S.A.F.addOpt(this, ['<?php echo lang('_$New anchor name')?>','<?php echo lang('_$Add')?>']);
			}
		});
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<?php $this->inc('tr_category', array('module'=>$this->table,'el_name'=>'data['.$this->id.'][catref]','colspan'=>3))?>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Question:')?></td><td class="a-r" width="40%"><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[<?php echo $this->id?>][question]" value="<?php echo $this->post('question')?>" /></td>
			<td class="a-l" width="11%"><?php echo lang('$Anchor:')?></td><td class="a-r" width="29%"><input type="text" class="a-input" style="width:97%" name="data[<?php echo $this->id?>][anc]" value="<?php echo $this->post('anc')?>" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Tags:')?></td><td class="a-r" colspan="3"><input type="text" class="a-input" style="width:99%" name="data[<?php echo $this->id?>][tags]" value="<?php echo $this->post('tags')?>" /></td>
		</tr>
		<tr>
			<td colspan="4"><div style="overflow:auto;height:400px" id="a-ai_<?php echo $this->name_id?>"></div></td>
		</tr>
	</table>
	
	<?php $this->inc('bottom', array(
		'no_add'	=> true
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>