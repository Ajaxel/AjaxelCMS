/*<script>*/
html += '<table cellspacing="0" class="a-list a-flat_inputs a-list-one" style="background:#dedede"><tbody>';
html += '<tr><td class="a_l a-add_answer_num">1</td><td class="a-l" width="80%"><textarea style="width:<?php echo $this->width-150?>px;height:36px;margin:2px 0;" class="a-textarea" tabindex='+(index++)+' name="data[map][0][][answer]"></textarea></td>';
html += '<td class="a-l" width="24%"><input type="text" name="data[map][0][][score]" style="width:40px" onclick="this.select()" class="a-input a-force" value="" /><br /></td>';
html += '<td class="a-r a-action_buttons" style="vertical-align:middle;text-align:center"><a href="javascript:;" class="a-add_answer" id="a-add_answer_<?php echo $this->name?>" onclick="S.A.L.clone_answer(this,S.A.O.next_answer)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" height="16" width="16" /></a><a href="javascript:;" class="a-del_answer" onclick="$(this).parent().parent().remove();" style="display:none"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" height="16" width="16" /></a></td></tr>';
if (answers) {
	for (j=0;j<answers.length;j++) {
		_a=answers[j];
		html += '<tr id="a-aia_'+_a.id+'" class="a-sortable"><td class="a_l a-add_answer_num">'+(j+2)+'</td><td class="a-l"><textarea style="width:<?php echo $this->width-150?>px;height:36px;margin:2px 0;" class="a-textarea" tabindex='+(index++)+' name="data[map]['+_a.id+'][answer]">'+_a.answer+'</textarea></td>';
		html += '<td class="a-l"><input type="text" name="data[map]['+_a.id+'][score]" class="a-input a-force" style="width:40px" onclick="this.select()" value="'+_a.score+'" /><br /></td>';
		html += '<td class="a-r a-action_buttons" style="vertical-align:middle;text-align:center"><a href="javascript:;" class="a-add_answer" onclick="S.A.L.clone_answer(this)" style="display:none"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" height="16" width="16" /></a><a href="javascript:;" onclick="$(this).parent().parent().remove()" class="a-del_answer"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" height="16" width="16" /></a> <img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/transform-move.png" height="16" width="16" /></td></tr>';
	}
}
html += '</tbody></table>';	