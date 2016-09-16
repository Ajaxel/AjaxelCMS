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
* @file       tpls/admin/mods_poll_tab_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' poll:').' ';
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>

		$('#a-w-quiz_<?php echo $this->name_id?>').change(function(){
			if(this.value=='#new'){
				S.A.F.addOpt(this, ['<?php echo lang('_$New quiz name')?>','<?php echo lang('_$Add')?>']);
			}
			if (this.value) {
				$('#a-w-sort_<?php echo $this->name_id?>').show();
				$('#a-w-pollname_<?php echo $this->name_id?>').hide();
			}
			else {
				$('#a-w-sort_<?php echo $this->name_id?>').hide();
				$('#a-w-pollname_<?php echo $this->name_id?>').show();
			}
		});
		<?php $this->inc('js_editors', array('descr'=>200))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?>'+this.value);
		});
		var answers = <?php echo json($this->post('answers', false))?>;
		var html = '',index=1;
		<?php $this->inc('js_poll_answers', array('id'=>$this->post('id', false)))?>
		$('#a_options_<?php echo $this->name_id?>').html(html);
		$('#a_options_<?php echo $this->name_id?>').find('tbody').sortable({
			axis: 'y',
			containment: 'parent',
			cursor: 'move',
			zIndex:3000,
			items: '>tr.a-sortable'
		});
		<?php if (!$this->id):?>
		for (i=1;i<=7;i++) {
			S.A.L.clone_answer($('#a-add_answer_<?php echo $this->name?>'));
		}
		var i=0;
		$('#a_options_<?php echo $this->name_id?> .a-add_answer_num').each(function(){
			$(this).html(++i);
		});
		<?php endif;?>
		
		S.A.W.tabs('<?php echo $this->name_id?>');
	}
}
S.A.O.next_answer=function(){
	i=0;
	$('#a_options_<?php echo $this->name_id?> .a-add_answer_num').each(function(){
		$(this).html(++i);
	});
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->title = $title.$this->post('title', false);
$this->width = 700;
$tab_height = 405;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_details_<?php echo $this->name_id?>"><?php echo lang('$Poll question')?></a></li>
			<li><a href="#a_options_<?php echo $this->name_id?>"><?php echo lang('$Answers')?></a></li>
		</ul>
		<div id="a_details_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr<?php if ($this->post('quiz',false)):?> style="display:none"<?php endif;?> id="a-w-pollname_<?php echo $this->name_id?>">
					<td class="a-l"><?php echo lang('$Poll name:')?></td><td class="a-r"><input type="text" class="a-input" id="a-w-name_<?php echo $this->name_id?>" style="width:70%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Connect to Quiz:')?></td><td class="a-r" colspan="2" width="85%">
						<select name="data[quiz]" id="a-w-quiz_<?php echo $this->name_id?>" class="a-select"><option value=""><?php echo lang('$-- select --')?></option><?php echo Html::buildOptions($this->post('quiz', false), $this->options['quizzes'], true)?><option value="#new"><?php echo lang('$-- create new --')?></select>
					</td>
				</tr>
				<tr id="a-w-sort_<?php echo $this->name_id?>"<?php if (!$this->post('quiz',false)):?> style="display:none"<?php endif;?>>
					<td class="a-l"><?php echo lang('$Quiz sort:')?></td><td class="a-r"><input type="text" class="a-input" style="width:15%" name="data[sort]" value="<?php echo $this->post('sort')?>" /></td>
				</tr>
				<?php $this->inc('tr_title', array('colspan'=>'2','title'=>'Question'))?>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Description:')?></td>
					<td class="a-r" width="70%" colspan="2"><textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:70px;visibility:hidden" name="data[descr]"><?php echo $this->post('descr')?></textarea></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Type:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[type]" class="a-select"><?php echo Html::buildOptions($this->post('type', false), $this->array['types'])?></select></td>
				</tr>
			</table>
		</div>
		<div id="a_options_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab"></div>
	</div>
	<?php $url = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,'m'=>$this->module,'id'=>$this->id,'l'=>$this->lang,'o'=>$this->conid)); ?>
	<?php $this->inc('bottom', array(
		'lang'			=> 'content_lang_save',
		'save'			=> 'content_save',
		'add'			=> true
	)); ?>
	<input type="hidden" name="data[passes]" value="<?php echo $this->post('passes')?>" />
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>