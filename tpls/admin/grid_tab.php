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
* @file       tpls/admin/grid_tab.php
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
	var data = <?php echo $this->json_data?>, a, id;
	if (data.length) {
		var is_rid = 'rid' in data[0];
		var is_price = 'price' in data[0];
		var is_language = 'language' in data[0];
		var is_date = 'date' in data[0];
		var is_percent = 'percent' in data[0];
		var h = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0"><tbody<?php if($this->button['save']):?> class="a-sortable_<?php echo $this->name?>"<?php endif;?>>';
		h += '<tr><th width="2%">ID</th>'+(is_date?'<th width="10%"><?php echo lang('$Date')?></th>':'')+''+(is_language?'<th width="1%"><?php echo lang('$Lang')?></th>':'')+'<th width="55%"><?php echo lang('$Title')?></th>'+(is_percent?'<th><?php echo lang('$Percent')?></th>':'')+''+(is_price?'<th><?php echo lang('$Price')?></th>':'')+'<th>&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			id=<?php if ($this->idcol=='rid'):?>a.rid;<? else:?>a.id<? endif;?>;
			h += '<tr id="a-c-<?php echo $this->table?>-'+id+'" class="'+(i%2?'':'a-odd')+' a-hov">';
			h += '<td class="a-r a-id">'+(is_rid?a.rid:a.id)+'</td>';
			if (is_date) {
				h += '<td class="a-l"><span class="a-date">'+a.date+'</span></td>';
			}
			if (is_language) {
				h += '<td class="a-l">'+a.language+'</td>';	
			}
			<?php if($this->button['save']): ?>
			h += '<td class="a-l" width="30%"><input type="text" style="width:98%" name="data['+id+'][title]" value="'+a.title+'" /></td>';
			<?php else:?>
			h += '<td class="a-l" width="30%"><a href="javascript:;" style="font-size:13px;" onclick="S.A.M.edit('+id+', this)">'+a.title+'</a></td>';
			
			<?php endif;?>
			if (is_percent) {
				h += '<td class="a-l">'+(a.percent ? a.percent: 0)+'%</td>';	
			}
			if (is_price) {
				h += '<td class="a-r">'+a.price+(a.currency?' '+a.currency:'')+'</td>';	
			}
			h += '<td class="a-r a-action_buttons" width="20%">';
			h += '<a href="javascript:;" onclick="S.A.M.edit('+id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>\', {id:'+id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			h += '<a href="javascript:;" onclick="S.A.L.del({id:'+id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this, false, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			h += '</td>';
			h += '</tr>';
		}
		h += '</tbody></table>';
	} else {
		var h = '<div class="a-not_found"><?php echo lang('$No results were found','grids')?></div>';
	}
	
	S.A.L.ready(h);
	<?php if($this->button['save']):?>S.A.L.sortable('<?php echo $this->name?>');<?php endif; ?>
});
<?php echo Index::CDZ?>
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search<?php echo ($this->tab?'_'.$this->tab:'')?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<?php /*$this->inc('grid')*/?>
	<?php 
		if (in_array('lang',DB::columns($this->table))) {
			$this->inc('language');
		}
		elseif (in_array('language',DB::columns($this->table))) {
			$this->inc('language', array('key_lang' => 'language'));
		}
	?>
	<?php $this->inc('search', array('tab'=>(isset($_GET[self::KEY_LOAD])?$this->tab:'')))?>
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