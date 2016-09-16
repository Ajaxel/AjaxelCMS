<script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a;
	if (data.length) {
		var html = '<table class="a-list a-list-one" cellspacing="0"><tbody class="a-sortable_<?php echo $this->name?>">';
		html += '<tr><th width="55%"><?php echo lang('$Title')?></th><th width="55%"><?php echo lang('$Pavilion')?></th><th width="55%"><?php echo lang('$Deadline')?></th><th>&nbsp;</th></tr>';
		for(i=0;i<data.length;i++) {
			a = data[i];
			html += '<tr id="a-c-<?php echo $this->table?>-'+a.id+'" class="'+(i%2?'':'a-odd')+' a-sortable">';
			html += '<td class="a-l" width="70%"><a href="javascript:;" onclick="S.A.M.edit('+a.id+', this)">'+a.title+'</a></td>';
			html += '<td class="a-l">'+a.pavilion+'</td>';
			html += '<td class="a-l">'+a.deadline+'</td>';
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
});
</script>

<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template')?>
	<?php $this->inc('grid')?>
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