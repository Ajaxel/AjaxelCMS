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
* @file       tpls/admin/stats_db_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	function toReason(reason) {
		if (reason=='[stats]') return 'Statistics';	
		return reason;
	}
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var html = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0"><tbody>';
	<?
	switch ($this->type){
		case 5: ?>
		html += '<tr><th width="20%"><?php echo lang('$IP address')?></th><th width="45%"><?php echo lang('$Reason')?></th><th width="30%" colspan="2"><?php echo lang('$Location')?></th><th width="5%">&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l a-nb"><input type="text" style="width:99%" name="data['+a.id+'][ip]" value="'+a.ip+'" /></td>';
			html += '<td class="a-l a-nb"><input type="text" style="width:99%" name="data['+a.id+'][reason]" value="'+S.A.P.js(a.reason)+'" /></td>';
			html += '<td width="1%">'+((a.country_code&&a.country_code!='un')?'<img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+a.country_code+'.png" />':'&nbsp;')+'</td>';
			html += '<td><a href="'+a.url+'" target="_blank">'+(a.country_name?a.country_name+(a.city?', '+a.city:''):'&nbsp;')+'</a></td>';
			html += '<td class="a-r a-action_buttons" rowspan="2" style="vertical-align:middle">';	
			html += '<a href="javascript:;" onclick="S.A.L.del_block(\''+a.id+'\', \''+a.ip+'\', this, true);"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';
			html += '</tr>';
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l" colspan="3" style="color:#ccc;font-size:11px">IP from: <input type="text" style="width:20%" name="data['+a.id+'][ip_from]" value="'+a.ip_from+'" />';
			html += '&nbsp; IP to: <input type="text" style="width:20%" name="data['+a.id+'][ip_to]" value="'+a.ip_to+'" /></td>';
			html += '<td class="a-l" title="'+a.blocked+'">'+a.blocked_ago+'</td>';
			html += '</tr>';
		}
		<?php 
		break;
		case 1:
		case 2:
		case 3:
		?>
		html += '<tr><th width="40%"><?php echo lang('$Key')?></th><th width="50%"><?php echo lang('$Value')?></th><th width="10%">&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l"><input type="text" style="width:99%" name="data['+S.A.P.js(a.k)+'][k]" value="'+S.A.P.js(a.k)+'" /></td>';
			html += '<td class="a-l"><input type="text" style="width:99%" name="data['+S.A.P.js(a.k)+'][v]" value="'+S.A.P.js(a.v)+'" /></td>';
			html += '<td class="a-r a-action_buttons">';	
			html += '<a href="javascript:;" onclick="S.A.L.del_db(\''+a.t+'\', \''+a.k+'\', this);"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';
			html += '</tr>';
		}
		<?
		break;
		case 6:
		?>
		html += '<tr><th width="10%"><?php echo lang('$ID')?></th><th width="80%"><?php echo lang('$User agent')?></th><th width="10%">&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l">'+a.k+'</td>';
			html += '<td class="a-l">'+a.v+'</td>';
			html += '<td class="a-r a-action_buttons">&nbsp;';
			html += '</td>';
			html += '</tr>';
		}
		<?
		break;
		case 4:
		?>
		html += '<tr><th width="30%"><?php echo lang('$Key')?></th><th width="30%"><?php echo lang('$Value')?></th><th width="30%"><?php echo lang('$Query key')?></th><th width="10%">&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr class="'+(i%2?'':'a-odd')+'">';
			html += '<td class="a-l"><input type="text" style="width:99%" name="data['+S.A.P.js(a.k)+'][k]" value="'+S.A.P.js(a.k)+'" /></td>';
			html += '<td class="a-l"><input type="text" style="width:99%" name="data['+S.A.P.js(a.k)+'][v]" value="'+S.A.P.js(a.v)+'" /></td>';
			html += '<td class="a-l"><input type="text" style="width:99%" name="data['+S.A.P.js(a.k)+'][s]" value="'+S.A.P.js(a.s)+'" /></td>';
			html += '<td class="a-r a-action_buttons">';	
			html += '<a href="javascript:;" onclick="S.A.L.del_db(\''+a.t+'\', \''+a.k+'\', this);"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';
			html += '</tr>';
		}
		<?
		break;
	}
	?>
		html += '</tbody></table>';
	} else {
		var html = '<div class="a-not_found"><?php echo lang('$No data found')?></div>';
	}
	
	$('#a_set_<?php echo $this->name?>_<?php echo $this->tab?>_div').html(html);
	S.A.L.ready();
});
<?php echo Index::CDZ?>
</script>
<?php if (!$this->submitted):?>
<form method="post" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
		<select name="<?php echo self::KEY_TYPE?>" onchange="S.A.L.get('<?php echo URL::rq(self::KEY_TYPE,$this->url_full)?>&<?php echo self::KEY_TYPE?>='+this.value,false,'<?php echo $this->tab?>')"><?php echo Html::buildOptions($this->type,$this->array['types'])?></select>
		<?php $this->inc('search',array('tab'=>$this->tab))?>
		<?php switch ($this->type) {
			case 1:
				?><a href="javascript:;" onclick="$('#add_new_<?php echo $this->tab?>').toggle('blind')"><?php echo lang('$Add another OS')?>..</a><?php 
			break;
			case 2:
				?><a href="javascript:;" onclick="$('#add_new_<?php echo $this->tab?>').toggle('blind')"><?php echo lang('$Add another browser')?>..</a><?php 
			break;
			case 3:
				?><a href="javascript:;" onclick="$('#add_new_<?php echo $this->tab?>').toggle('blind')"><?php echo lang('$Add another robot')?>..</a><?php 
			break;
			case 4:
				?><a href="javascript:;" onclick="$('#add_new_<?php echo $this->tab?>').toggle('blind')"><?php echo lang('$Add another search engine')?>..</a><?php 
			break;
			case 5:
				?><a href="javascript:;" onclick="$('#add_new_<?php echo $this->tab?>').toggle('blind')"><?php echo lang('$Block another ip')?>..</a><?php 
			break;
		}?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php endif;?>
<?php $this->inc('tab_form_top')?>
<div class="a-search" style="display:none;height:auto" id="add_new_<?php echo $this->tab?>">
	<?php switch ($this->type) {
		case 1:
		case 2:
		case 3;
		case 4:
			?>Key: <input type="text" name="data[new][k]" style="width:200px" value="" /> Value: <input type="text" name="data[new][v]" style="width:200px" value="" /><?php 
		break;
		case 5:
			?>
			<table cellspacing="0" cellpadding="0" class="a-form">
			<tr><td class="a-l">IP:</td><td class="a-r"><input type="text" name="data[new][ip]" style="width:200px" value="" /></td><td class="a-l">IP from:</td><td class="a-r"><input type="text" name="data[new][ip_from]" style="width:200px" value="" /></td></tr>
			<tr><td class="a-l">Reason:</td><td class="a-r"><input type="text" name="data[new][reason]" style="width:200px" value="" /></td><td class="a-l">IP to:</td><td class="a-r"><input type="text" name="data[new][ip_to	]" style="width:200px" value="" /></td></tr>
			</table>
			<?php 
		break;
	}?>
</div>
<div id="a_set_<?php echo $this->name?>_<?php echo $this->tab?>_div" class="a-content"><?php $this->inc('loading')?></div>
<?php $this->inc('nav', array('nav'=>$this->nav, 'total'=>$this->total, 'tab' => $this->tab))?>
<?php $this->inc('tab_form_bottom')?>