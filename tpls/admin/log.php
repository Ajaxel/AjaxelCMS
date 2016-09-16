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
* @file       tpls/admin/log.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
<?php echo Index::CDA?>
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	var action_types = <?php echo $this->json_array['action_types']?>;
	if (data.length) {
		var h = '<table class="a-list a-list-one" cellspacing="0"><tbody>';
		h += '<tr><th>#</th><th><?php echo lang('$Time')?></th><th><?php echo lang('$Action')?></th><th width="55%"><?php echo lang('$Message')?></th><th><?php echo lang('$Table')?></th><th><?php echo lang('$ID')?></th><th title="<?php echo lang('$Changes')?>">C</th><th title="<?php echo lang('$UserID')?>">U</th><th>&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			h += '<tr class="'+(i%2?'':'a-odd')+'">';
			h += '<td class="a-l" width="1%" style="color:#ccc">'+(i+<?php echo $this->offset?>+1)+'</td>';
			h += '<td class="a-l" nowrap>'+a.time+'</td>';
			h += '<td class="a-l" style="color:'+action_types[a.action][0]+'">'+action_types[a.action][1]+'</td>';
			h += '<td class="a-l"><a href="javascript:;" style="font-size:11px" onclick="S.A.M.edit('+a.id+', this)">'+a.title+'</a></td>';
			h += '<td class="a-l">'+a.table+'</td>';
			h += '<td class="a-l">'+a.setid+'</td>';
			h += '<td class="a-l a-c">'+a.changes+'</td>';
			h += '<td class="a-l a-c"><a href="javascript:;" onclick="S.A.W.open(\'?<?php echo URL_KEY_ADMIN?>=users&id='+a.userid+'\')">'+a.userid+'</a></td>';
			h += '<td class="a-r a-action_buttons">';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/document-preview.png" /></a>';
			if (a.action!=<?php echo Site::ACTION_ERROR?> && a.action!=<?php echo Site::ACTION_UNKNOWN?>) {
				h += '<a href="javascript:;" onclick="if(confirm(\'<?php echo lang('$Are you sure to revert this backup?')?>\')) {$(this).parent().parent().fadeOut();S.A.L.json(S.A.L.url,{get:\'action\',a:\'save\',id:'+a.id+'}, function(){ S.A.L.get(S.A.L.url)})}"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/categories/preferences-other.png" title="<?php echo lang('$Revert?')?>" /></a>';
			}
			h += '<a href="javascript:;" onclick="if(confirm(\'<?php echo lang('$Are you sure to destroy this log entry?')?>\\n<?php echo lang('$Warning, this is unrecoverable operation.')?>\')){S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)}"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';
			h += '</tr>';
		}
		h += '</tbody></table>';
	} else {
		var h = '<div class="a-not_found"><?php echo lang('$No results were found','log')?></div>';
	}
	S.A.L.ready(h);
});
<?php echo Index::CDZ?>
</script>

<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<select onchange="S.A.L.get('<?php echo URL::rq(self::KEY_TYPE, $this->url_full)?>&<?php echo self::KEY_TYPE?>='+this.value);"><option value=""><?php echo lang('$all actions')?></option><?php echo Html::buildOptions($this->type, $this->array['dropdown'])?></select>
	<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<input type="hidden" name="<?php echo $this->name?>-submitted" value="1" />
</form>
<?php $this->inc('list')?>

</div>
<?php $this->inc('bot')?>