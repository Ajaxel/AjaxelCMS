/*<script>*/
html += '<table cellspacing="0" class="a-list a-flat_inputs a-list-one" style="background:#dedede">';
html += '<tr><td class="a-l" width="70%"><textarea style="width:98%;height:36px;margin:2px 0;" class="a-textarea" tabindex='+(index++)+' name="data[<?php echo $id?>][answers][0][][answer]"></textarea></td>';
html += '<td class="a-r" width="24%"><select style="width:98%;" name="data[<?php echo $id?>][answers][0][][anc]" class="a-select a-ai_ancor">'+opts2+'</select><br /><select style="width:98%;" name="data[<?php echo $id?>][answers][0][][emotion]">'+e_opts+'</select></td><td class="a-r a-action_buttons" style="vertical-align:middle;text-align:center"><a href="javascript:;" onclick="S.A.L.clone_answer(this)" class="a-add_answer"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" height="16" width="16" /></a><a href="javascript:;" onclick="$(this).parent().parent().remove()" class="a-del_answer" style="display:none"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" height="16" width="16" /></a></td></tr>';
if (answers) {
	for (j=0;j<answers.length;j++) {
		_a=answers[j];
		html += '<tr id="a-aia_'+_a.id+'"><td class="a-l"><textarea style="width:98%;height:36px;margin:2px 0;" class="a-textarea" tabindex='+(index++)+' name="data[<?php echo $id?>][answers]['+_a.id+'][answer]">'+_a.answer+'</textarea></td>';
		html += '<td class="a-r"><select style="width:98%;" name="data[<?php echo $id?>][answers]['+_a.id+'][anc]" class="a-select a-ai_ancor">'+opts2.replace('value="'+_a.anc+'"','value="'+_a.anc+'" selected="selected"')+'</select><br /><select style="width:98%;" name="data[<?php echo $id?>][answers]['+_a.id+'][emotion]">'+e_opts.replace('value="'+_a.emotion+'"','value="'+_a.emotion+'" selected="selected"')+'</select></td><td class="a-r a-action_buttons" style="vertical-align:middle;text-align:center"><a href="javascript:;" onclick="S.A.L.del({id:'+_a.id+', title: \''+S.A.P.js2(_a.answer)+'\', answer: true}, this, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" height="16" width="16" /></a></td></tr>';
	}
}
html += '</table>';	