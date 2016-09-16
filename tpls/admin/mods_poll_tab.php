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
* @file       tpls/admin/mods_poll_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
S.A.L.clone_answer=function(obj, fn){
	var o=$(obj).parent().parent();
	var c=o.clone().hide();
	/*
	c.find('a.a-add_answer').hide();
	c.find('a.a-del_answer').show();
	*/
	
	$('a.a-del_answer',o.parent()).show();
	$('a.a-add_answer',o.parent()).hide();
	c.last().find('a.a-add_answer').show();
	o.parent().append(c.show());
	c.find('textarea').val('').focus();
	if(fn)fn(o);
}
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, h = '', quiz = 'n/a';
	if (data.length) {
		h = '<table class="a-list a-list-one" cellspacing="0">';	
		h += '<tr><th width="8%">&nbsp;</th><th width="60%"><?php echo lang('$Name')?></th><th><?php echo lang('$Passes')?></th><th><?php echo lang('$Answers')?></th><th width="5%">&nbsp;</th></tr>';
		for(i in data) {
			a = data[i];
			if (a.quiz!=quiz) {
				h += '<tr><td colspan="5"><div class="a-fm<?php echo $this->ui['a-fm']?>">'+(a.quiz?'<?php echo lang('$Connected to Quiz:')?> '+a.quiz:'<?php echo lang('$Polls')?>')+'</div></td></tr>';
			}
			quiz=a.quiz;
			
			
			h += '<tr class="'+(i%2?'':'a-odd')+' a-hov" onclick="if(!$(this).next().find(\'.a-poll\').is(\':visible\')){$(\'.a-poll\',$(\'#<?php echo $this->name?>-content\')).slideUp(\'fast\');$(this).next().find(\'.a-poll\').slideDown(\'fast\')}">';
			h += '<td class="a-l" nowrap><span class="a-date">'+a.added+'</span></td>';
			h += '<td class="a-l"><a href="javascript:;" onclick="S.A.M.edit('+a.rid+', this)" style="font-size:14px">'+a.title+'</a></td>';
			h += '<td class="a-l a-c">'+(a.passes>0?a.passes:0)+'</td>';
			h += '<td class="a-l a-c">'+(a.answers>0?a.answers:0)+'</td>';
			h += '<td class="a-r a-action_buttons" width="10%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+a.rid+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=poll\', {id:'+a.rid+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			h += '<a href="javascript:;" onclick="if(confirm(\'<?php echo lang('$Are you sure to delete this poll with related languages and answers?')?>\'))S.A.L.del({id:'+a.rid+', active:'+(a.active==1)+', title: \''+a.title+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';			
			h += '</tr>';
			h += '<tr class="'+(i%2?'':'a-odd')+'"><td colspan="5"><div'+((('<?php echo get('open')?>' || i>0) && '<?php echo get('open')?>'!=a.id)?' style="display:none"':'')+' class="a-poll"><table style="width:93%;margin-left:7%" cellspacing="0">';
			/*
			h += '<tr><td class="a-l" style="padding:2px;font-weight:bold" colspan="4"><img src="<?php echo FTP_EXT?>tpls/img/arrow_right.gif" /> '+a.title+'</td></tr>';
			*/
			for (j in a.map) {
				var p=(a.passes>0?Math.round(a.map[j].answers/a.passes*100):0);
				h += '<tr style="font-size:10px"><td width="2%" class="a-action_buttons a-l" style="padding:0">'+(!a.quiz?'<a href="javascript:;" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=mods&<?php echo self::KEY_TAB?>=poll&vote='+a.map[j].id+'&p=<?php echo get('p')?>&open='+a.id+'\',false,\'<?php echo $this->tab?>\')"><img height="12" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/rating.png" /></a>':'&nbsp;')+'</td><td class="a-l" width="48%">'+a.map[j].answer.toString().replace(/\n/g,'<br />')+'</td><td class="a-l a-c">'+a.map[j].score+'</td><td class="a-l a-c">'+(a.map[j].answers>0?a.map[j].answers:0)+'</td><td width="40%" class="a-r" style="padding:0"><div style="height:15px;width:'+(p>0?p:'0')+'%" class="ui-state-active"><div style="padding:2px;">'+(p>0?p:'0')+'%</div></div></td></tr>';
			}
			h += '</table></div></td></tr>';
		}
		h += '</table>';
	} else {
		h = '<div class="a-not_found"><?php echo lang('$No polls were found')?></div>';
	}
	S.A.L.ready(h);
});
</script>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('language')?>
	<?php $this->inc('search')?>
	<button type="button" onclick="S.A.M.add(this)" class="a-button a-button_x"><?php echo lang('$Add')?></button>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php $this->inc('list')?>