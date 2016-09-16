<script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var html = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0"><tbody<?php if($this->button['save']):?> class="a-sortable_<?php echo $this->name?>"<?php endif;?>>';
		html += '<tr><th width="55%"><?php echo lang('$Title')?></th><th colspan="2">&nbsp;</th><th>&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr id="a-c-<?php echo $this->table?>-'+a.id+'" class="'+(i%2?'':'a-odd')+' a-sortable">';
			<?php if($this->button['save']): ?>
			html += '<td class="a-l" width="30%"><input type="text" style="width:98%" name="data['+a.id+'][title]" value="'+a.title+'" /></td>';
			<?php else:?>
			html += '<td class="a-l" width="30%"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">'+a.title+'</a></td>';
			<?php endif;?>
			html += '<td class="a-r">'+(a.inuse>0?'used':'not used')+'</td>';
			html += '<td class="a-r">'+(a.is_year>0?'1 year':'1 month')+'</td>';
			html += '<td class="a-r a-action_buttons" width="20%">';
			html += '<a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.act(\'?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>\', {id:'+a.id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'green':'red')+'.png" alt="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/flag-'+(a.active==1?'red':'green')+'.png" /></a>';
			html += '<a href="javascript:;" onclick="S.A.L.del({id:'+a.id+', active:'+(a.active==1)+', title: \''+S.A.P.js(a.title)+'\'}, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a>';
			html += '</td>';
			html += '</tr>';
		}
		html += '</tbody></table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No grid elements were found')?></div>';
	}
	S.A.L.ready(html);
	<?php if($this->button['save']):?>S.A.L.sortable('<?php echo $this->name?>');<?php endif; ?>
});
</script>

<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<?php $this->inc('grid')?>
	<?php
	$arr = array(
		'0'	=> 'all',
		'1'	=> '1 month cards',
		'12'=> '1 year cards',	
	);
	?>
	<select onchange="S.A.L.get('?<?php echo URL::rq(self::KEY_TPL, URL::get())?>&months='+this.value)"><?php echo Html::buildOptions(get('months'),$arr)?></select>
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