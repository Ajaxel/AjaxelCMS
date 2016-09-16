<script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, langs=<?php echo $this->json_langs?>, a, id, h = '<table class="a-list a-list-one a-flat_inputs" cellspacing="0">';
	h += '<tr>';
	h += '<th>ID</th>';
	for (k in langs) {
		h += '<th><img src="<?php echo FTP_EXT?>tpls/img/flags/16/'+langs[k]+'.png" alt="" /></th>';
	}
	h += '<th><img align="right" src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/trash-empty.png" alt="" /></th>';
	h += '</tr>';
	for (j in data) {
		a=data[j];
		h += '<tr>';
		h += '<td class="a-l" style="width:2%" nowrap>'+(a.l?'<a href="javascript:;" onclick="S.A.L.get(\''+a.l+'\',false,\'geo\')">':'')+a.h+(a.l?'</a>':'')+'</td>';
		for (i in a.n) {
			h += '<td class="a-r"><input type="text" name="data['+a.t+'][name_'+i+']" value="'+a.n[i]+'" style="width:97%" /></td>';
		}
		/*h += '<td class="a-r"><input type="text" name="" style="width:25px" value="'+Math.round(a.s)+'" /></td>';*/
		h += '<td class="a-r a-c"><input type="checkbox" name="data[del]['+a.t+']" value="Y" onclick="S.A.L.sdel(this.parentNode.parentNode,this)" title="<?php echo lang('$Delete?')?>" /></td>';
		h += '</tr>';
	}
	var next=<?php echo $this->output['next']?>;
	for (i=0;i<5;i++) {
		h += '<tr>';
		<?php if (!$this->country):?>
		h += '<td class="a-l"><input type="text" name="data[new]['+next+'][code]" style="width:25px" value="" title="<?php echo lang('$Country code')?>" /></td>';
		<?php else:?>
		h += '<td class="a-l">'+next+'</td>';
		<?php endif;?>
		for (k in langs) {
			h += '<td class="a-r"><input type="text" name="data[new]['+next+'][name_'+langs[k]+']" value="" style="width:97%" /></td>';
		}
		/*h += '<td class="a-r"><input type="text" name="" style="width:25px" value="0" /></td>';*/
		h += '<td class="a-r a-c">&nbsp;</td>';
		h += '</tr>';
		next++;
	}
	h += '</table>';
	
	S.A.L.ready(h);
});
</script>
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<?php echo $this->output['tree']?>
</div>
</form>
<?php $this->inc('list')?>