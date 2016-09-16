<?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' challenge:').($this->id?' (ID: '.$this->id.') ':'').' ';
$this->title = $title.$this->post('title', false);
$this->width = 850;
$this->status = '';
$tab_height = 440;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo strjs($title)?>'+this.value);
		});
		var editor={
			element: '#a-w-descr_<?php echo $this->name_id?>',
			height: 250, 
			base: '<?php echo HTTP_BASE?>',
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		var editor={
			element: '#a-w-body_<?php echo $this->name_id?>',
			height: 415, 
			base: '<?php echo HTTP_BASE?>',
			full: true,
			lang: 'en',
			templates: <?php echo $this->json_templates?>
		}
		S.A.W.editor(editor);
		S.A.W.tabs('<?php echo $this->name_id?>');
		S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','main_photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image files');
		S.A.W.uploadify_one('<?php echo $this->name_id?>','document','<?php echo File::uploadifyExt(array('doc','docx','pdf'))?>','Document files','save_document');
	}
	,setPhoto:function(file) {
		var ext=file.split('.');
		ext = ext[ext.length-1];
		if (ext=='doc'||ext=='docx'||ext=='pdf') {
			$('#a-w-document_<?php echo $this->name_id?>').html(file);
			file=file.split('/');
			file=file[file.length-1];
			$('#a-document_<?php echo $this->name_id?>_val').val(file);
		} else {
			S.A.W.addMainPhoto('<?php echo $this->name_id?>', file, 'main_photo');
		}
	}
	,fillTaxonomies:function(sel_id, val) {
		S.G.json('?action=taxonomy', {
			val: val
		}, function(data){
			var s=$('#'+sel_id).attr('disabled',false);
			s.find('option').remove().end();
			for (i in data){
				var c={};
				if (data[i].substring(0,4)=='    ') {c={'color':'#666'};data[i]=data[i].substring(4)}
				s.append($("<option></option>").css(c).attr("value",data[i]).text(data[i]));
			}
		});
	},
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_main_<?php echo $this->name_id?>"><?php echo lang('$Challenge details')?></a></li>
			<li><a href="#a_document_<?php echo $this->name_id?>"><?php echo lang('$Document')?></a></li>
			<li><a href="#a_taxonomy_<?php echo $this->name_id?>"><?php echo lang('$Taxonomy')?></a></li>
			<li><a href="#a_body_<?php echo $this->name_id?>"><?php echo lang('$Full info')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Admin')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form ui-corner-all" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Title:')?></td><td class="a-r" colspan="3" width="85%"><input type="text" class="a-input a-win_status" id="a-w-title_<?php echo $this->name_id?>" style="width:99%" name="data[title]" value="<?php echo $this->post('title')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Challenge type:')?></td><td class="a-r">
						<select name="data[challenge_type]"><?php echo Data::getArray('my:challenge_types',$this->post('challenge_type', false))?></select>
					</td>
					<td class="a-l" width="15%"><?php echo lang('$Pavilion:')?></td><td class="a-r">
						<select name="data[pavilion]"><option value=""></option><?php echo Data::getArray('my:pavilions',$this->post('pavilion', false))?></select>
					</td>
				</tr>
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Image replace:')?> <br />/ <?php echo lang('$Description:')?> <br />/ <?php echo lang('$Body:')?></td><td class="a-r" width="15%">
						<div style="width:180px;height:60px">
						<div class="a-l a-main_photo" id="a-w-main_photo_<?php echo $this->name_id?>"><?
						if ($this->post('main_photo', false)):?>
						<a href="/<?php echo $this->post('main_photo_preview')?>" target="_blank"><img src="/<?php echo $this->post('main_photo_thumb')?>?nocache=<?php echo $this->time?>" alt="<?php echo lang('$Description image')?>" /></a>
						<?php endif;?>
						</div>
						<div class="a-r" style="width:110px;height:60px"><input type="file" class="a-file" id="a-main_photo_<?php echo $this->name_id?>" style="width:80px;" size="2" /><br /><a href="javascript:;" onclick="S.A.W.delPhoto('<?php echo $this->name_id?>',0, function(response){window_<?php echo $this->name_id?>.setPhoto(response.substring(2))});">[delete]</a></div>
						</div>
					</td>
					<td class="a-r" width="70%" colspan="2"><textarea type="text" class="a-textarea" id="a-w-info_<?php echo $this->name_id?>" style="width:98%;height:60px" name="data[info]"><?php echo $this->post('info')?></textarea></td>
				</tr>
			</table>
			<table class="a-form" cellspacing="0">
				<tr><td class="a-r">
					<textarea type="text" class="a-textarea" id="a-w-descr_<?php echo $this->name_id?>" style="width:99%;height:200px;visibility:hidden"><?php echo $this->post('descr')?></textarea><textarea name="data[descr]" id="a-w-descr2_<?php echo $this->name_id?>" style="display:none"></textarea>
				</td></tr>
			</table>
			<table class="a-form ui-corner-all" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Deadline:')?></td><td class="a-r"><input type="text" class="a-input a-date" style="width:120px" name="data[deadline]" value="<?php echo $this->post('deadline')?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Award:')?></td><td class="a-r"><input type="text" class="a-input" style="width:120px" name="data[award]" value="<?php echo $this->post('award')?>" /></td>
					<td class="a-l" width="15%"><?php echo lang('$Solvers:')?></td><td class="a-r"><?php echo $this->post('solvers')?></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Tags:')?></td><td class="a-r" colspan="5">
						<input type="text" class="a-input" name="data[tags]" value="<?php echo $this->post('tags')?>" />
					</td>
				</tr>
			</table>
		</div>
		<div id="a_document_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form ui-corner-all" cellspacing="0">
			<tr>
				<td class="a-l" width="15%"><?php echo lang('$Document:')?></td>
				<td class="a-r" width="85%">
					<input type="file" class="a-file" id="a-document_<?php echo $this->name_id?>" style="width:80px;" size="2" />
					<div id="a-w-document_<?php echo $this->name_id?>"><?
					if ($this->post('document_full', false)):?>
					<a href="/<?php echo HTTP_EXT?><?php echo $this->post('document_full')?>" target="_blank"><?php echo $this->post('document_full')?></a>
					<?php endif;?>
					</div>
					<input type="hidden" id="a-document_<?php echo $this->name_id?>_val" name="data[document]" value="<?php echo $this->post('document')?>" />
					<a href="javascript:;" onclick="S.A.W.delPhoto('<?php echo $this->name_id?>','<?php echo $this->post('document')?>', function(response){$('#a-w-document_<?php echo $this->name_id?>').html('');$('#a-document_<?php echo $this->name_id?>_val').val('')},false,'document','delete_document');">[delete]</a>
				</td>
			</tr>
			</table>
		</div>
		<div id="a_taxonomy_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form ui-corner-all" cellspacing="0">
				<?php for ($i=0;$i<=3;$i++):?>
				<tr>
					<td class="a-l" style="width:20%">Select category <?php echo ($i+1)?>:</td><td class="a-r" style="padding:5px">
						<select name="data[taxonomy][<?php echo $i?>][0]" onchange="window_<?php echo $this->name_id?>.fillTaxonomies('cat_<?php echo $i?>',this.value)" style="width:400px"><option value="" style="color:#666"> -- select --</option><?php echo $this->Index->Tpl->taxonomy(-1,$this->post['taxonomy'][$i][0])?></select>
						<br />
						<select name="data[taxonomy][<?php echo $i?>][1]" id="cat_<?php echo $i?>" style="width:400px;margin-top:4px"<?php if (!$this->post['taxonomy'][$i][0]):?> disabled="disabled"<?php endif?>><?php if ($this->post['taxonomy'][$i][0]):?><?php echo $this->Index->Tpl->taxonomy(-1,$this->post['taxonomy'][$i][1])?><?php endif;?></select>
					</td>
				</tr>
				<?php endfor?>
			</table>
		</div>	
		
		<div id="a_body_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0"><tr><td class="a-r">
				<textarea type="text" class="a-textarea" id="a-w-body_<?php echo $this->name_id?>" style="width:99%;height:425px;visibility:hidden"><?php echo $this->post('body')?></textarea><textarea name="data[body]" id="a-w-body2_<?php echo $this->name_id?>" style="display:none"></textarea>
			</td></tr></table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:425px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td class="a-td1">&nbsp;
		
		</td>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?php if ($this->id):?>
			<?php /*
			<button type="button" class="a-button a-button_s" onclick="$('#a-w-body2_<?php echo $this->name_id?>').text($('#a-w-body_<?php echo $this->name_id?>').html());S.A.W.save('<?php echo $this->url_save?>&a=copy', this.form, this)"><?php echo lang('$Copy')?></button> */?>
			<button type="button" class="a-button a-button_s" onclick="$('#a-w-body2_<?php echo $this->name_id?>').text($('#a-w-body_<?php echo $this->name_id?>').html());S.A.W.save('<?php echo $this->url_save?>&a=add', this.form, this)"><?php echo lang('$Add another')?></button></td>
			<td><?php $this->inc('button',array('click'=>'$(\'#a-w-body2_'.$this->name_id.'\').text($(\'#a-w-body_'.$this->name_id.'\').html());$(\'#a-w-descr2_'.$this->name_id.'\').text($(\'#a-w-descr_'.$this->name_id.'\').html());S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Save'))); ?>
			<?php else:?>
			<?php $this->inc('button',array('click'=>'$(\'#a-w-body2_'.$this->name_id.'\').text($(\'#a-w-body_'.$this->name_id.'\').html());$(\'#a-w-descr2_'.$this->name_id.'\').text($(\'#a-w-descr_'.$this->name_id.'\').html());S.A.W.save(\''.$this->url_save.'&a=save\', this.form, this)','img'=>'oxygen/16x16/actions/list-add.png','text'=>lang('$Add'))); ?>
			<?php endif;?>
			</td></tr></table>
		</td>
		<?php if ($this->id):?>
		<td  class="a-td3"><?php if ($this->id):?><?php echo lang('$Posted:')?> <?php echo date('d M Y',$this->post('added', false))?><br /><?php echo lang('$by user:')?> <?php echo $this->post('username')?><?php endif;?>&nbsp;</td>
		<?php endif;?>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>