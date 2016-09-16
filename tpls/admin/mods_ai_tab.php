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
* @file       tpls/admin/mods_ai_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
S.A.O.shown_all=false;
S.A.O.shown=[];
S.A.O.toggle=function(id,obj){
	if (S.A.O.shown[id]) {
		var o=$(obj).children(0);
		if (o.attr('src')) o.attr('src',o.attr('src').replace('arrow-up','arrow-down'));
		$('#a-ait_'+id+'').stop().hide();
		$('#a-ais_'+id+'').stop().hide();
		S.A.O.shown[id]=false;
	} else {
		var o=$(obj).children(0);
		if (o.attr('src')) o.attr('src',o.attr('src').replace('arrow-down','arrow-up'));	
		$('#a-ait_'+id+'').show();
		$('#a-ais_'+id+'').show();
		S.A.O.shown[id]=true;
	}
}
S.A.O.toggle_all=function(obj){
	var ais=$('#<?php echo $this->name?>-content .a-ais_all');
	var ait=$('#<?php echo $this->name?>-content .a-ait_all');
	var aio=$('#<?php echo $this->name?>-content .a-aio_all');	
	if (S.A.O.shown_all){
		S.A.O.shown_all=false;
		ais.hide();ait.hide();
		var o=$(obj).children(0);
		if (o.attr('src')) o.attr('src',o.attr('src').replace('arrow-up','arrow-down'));
		aio.each(function(){
			S.A.O.shown[parseInt($(this).attr('rel'))]=false;
			var o=$(this).children(0);
			if (o.attr('src')) o.attr('src',o.attr('src').replace('arrow-up','arrow-down'));	
		});
	} else {
		S.A.O.shown_all=true;
		ais.show();ait.show();
		var o=$(obj).children(0);
		if (o.attr('src')) o.attr('src',o.attr('src').replace('arrow-down','arrow-up'));
		aio.each(function(){
			S.A.O.shown[parseInt($(this).attr('rel'))]=true;
			var o=$(this).children(0);
			if (o.attr('src')) o.attr('src',o.attr('src').replace('arrow-down','arrow-up'));	
		});
	}
}
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, _a, i=0, index=1,html='',answers;
	var emotions=<?php echo json
($this->array['emotions'])?>,anchors=<?php echo json
($this->array['anchors'])?>,opts='<option value="" style="color:#999"><?php echo lang('$-- anchor --')?></option>',opts2='<option value="" style="color:#999"><?php echo lang('_$-- new anchor --')?></option><option value="#new" style="color:green"><?php echo lang('_$-- create new --')?></option>',e_opts='',o='';
	for (i=0;i<anchors.length;i++)o+='<option value="'+anchors[i]+'">'+anchors[i]+'</option>';
	for (i=0;i<emotions.length;i++)e_opts+='<option value="'+i+'"'+(!i?' style="color:#999"':'')+'>'+emotions[i]+'</option>';
	opts+=o;opts2+=o;
	html = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0">';	
	html += '<tr>';
	html += '<th width="70%"><?php echo lang('$User question')?></th>';
	html += '<th width="24%"><?php echo lang('$Anchor / New anchor')?></th>';	
	html += '<th width="6%">&nbsp;</th>';	
	html += '</tr>';
	
	html += '<tr class="a-add">';
	html += '<td class="a-l"><input type="text" style="width:98%;font-weight:bold;font-family:Tahoma;color:#111" tabindex='+(index++)+' name="data[0][question]" value="" /></td>';
	html += '<td class="a-l"><select style="width:98%;" name="data[0][anc]">'+opts.replace('value="<?php echo $this->options['anc']?>"','value="<?php echo $this->options['anc']?>" selected="selected"')+'</select></td>';
	html += '<td class="a-r a-action_buttons"><img src="<?php echo FTP_EXT?>tpls/img/1x1.gif" height="16" width="16" /></td>';			
	html += '</tr>';
	html += '<tr><td class="a-l" colspan="2"><input type="text" title="<?php echo lang('_$Tags, categories, some info to flag the user')?>" style="width:98%;" name="data[0][tags]" value="" /></td><td class="a-r">&nbsp;</td></tr>';
	html += '<tr><td colspan="3">';
	html += '<table cellspacing="0" class="a-list a-flat_inputs a-list-one"><tr><td class="a-l" width="70%"><textarea style="width:98%;height:36px;margin:2px 0;" class="a-textarea" title="<?php echo lang('_$Chat bot answer')?>" tabindex='+(index++)+' name="data[0][answers][0][][answer]"></textarea></td>';
	html += '<td class="a-r" width="24%"><select style="width:98%;" name="data[0][answers][0][][anc]" class="a-select a-ai_ancor">'+opts2+'</select><br /><select style="width:98%;" name="data[0][answers][0][][emotion]">'+e_opts+'</select></td>';
	html += '<td class="a-r a-action_buttons" style="vertical-align:middle;text-align:center;"><a href="javascript:;" onclick="S.A.L.clone_answer(this)" class="a-add_answer"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" height="16" width="16" /></a><a href="javascript:;" onclick="$(this).parent().parent().remove()" class="a-del_answer" style="display:none"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" height="16" width="16" /></a></td></tr>';
	html += '</table></td></tr>';
	if (!data || !data.length) {
		html += '</table>';
		<?php if ($this->find && isset($this->options['foredit'])):?>
		html += '<div class="a-not_found"><?php echo lang('$No bot answers were found mathing your criteria: %1',strform($this->find))?>,<br /><?php echo lang('$But the chat bot will result the following:')?><br /><br /><?php echo $this->options['answer']?></div>';
		html += '<table class="a-list a-list-one a-flat_inputs" cellspacing="0">';
		var data = <?php echo json($this->options['foredit'])?>;
		<?php else:?>
		html += '<div class="a-not_found"><?php echo lang('$No bot answers were found')?></div>';
		<?php endif;?>
	}
	if (data&&data.length) {
		for(i=0;i<data.length;i++) {
			html += '<tr><td colspan="4" class="a-fsep">&nbsp;</td></tr>';
			a = data[i];
			html += '<tr id="a-ai_'+a.id+'">';
			html += '<td class="a-l a-action_buttons"><a href="javascript:;" onclick="S.A.O.toggle('+a.id+',this)" id="a-aio_'+a.id+'" class="a-aio_all" rel="'+a.id+'"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/arrow-down.png" style="float:left" /></a><input type="text" style="width:98%;font-weight:bold;font-family:Tahoma;color:#111;margin-top:2px" tabindex='+(index++)+' name="data['+a.id+'][question]" value="'+a.question+'" /></td>';
			html += '<td class="a-l"><select style="width:98%;" name="data['+a.id+'][anc]">'+opts.replace('value="'+a.anc+'"','value="<?php echo $this->options['anc']?>" selected="selected"')+'</select></td>';
			html += '<td class="a-r a-action_buttons">';
			<?php if (isset($this->options['foredit'])):?>
			html += '&nbsp;';
			<?php else:?>
			html += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', title: \''+S.A.P.js2(a.question)+'\'}, this, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			<?php endif;?>
			html += '</td>';			
			html += '</tr>';
			html += '<tr valign="top" id="a-ait_'+a.id+'" class="a-ait_all" style="display:none"><td class="a-l" colspan="3"><input type="text" style="width:98%;" name="data['+a.id+'][tags]" value="'+a.tags+'" /></td></tr>';
			html += '<tr id="a-ais_'+a.id+'" class="a-ais_all" style="display:none"><td colspan="3">';
			answers=a.answers;
			<?php $this->inc('js_ai_answers', array('id'=>'\'+a.id+\''))?>
			html += '</td></tr>';
		}
		html += '</table>';
	}	
	S.A.L.ready(html);
	
	$('#<?php echo $this->name?>-content .a-ai_ancor').change(function(){
		if(this.value=='#new'){
			S.A.F.addOpt(this, ['<?php echo lang('_$New anchor name')?>','<?php echo lang('_$Add')?>']);
		}
	});
	<?php if ($this->find):?>
	S.A.O.toggle_all(document.getElementById('a-ai_toggle_all'));
	<?php endif;?>
});
</script>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('search', array('width'=>240))?> <button class="a-button" type="button" onclick="S.A.L.json('?<?php echo URL_KEY_ADMIN?>=mods&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>',{get:'action', a:'ask', 'question':$('#<?php echo $this->name?>-find<?php echo ($this->tab?'_'.$this->tab:'')?>').val()},true)" id="<?php echo $this->name?>-but_asl"><?php echo lang('$Ask')?></button>
	<button type="button" onclick="S.A.M.add(this)" class="a-button a-button_x"><?php echo lang('$Add')?></button>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php $this->inc('list', array('category_right'=>'<a href="javascript:;" onclick="S.A.O.toggle_all(this)" id="a-ai_toggle_all"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/22x22/actions/arrow-down-double.png" alt="" /></a>'))?>