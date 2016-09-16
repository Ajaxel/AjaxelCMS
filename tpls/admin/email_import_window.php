	<?php
/**
* Admin Templates
* Ajaxel CMS v5.0
* Author: Alexander Shatalov <admin@ajaxel.com>
* http://ajaxel.com
*/
?><?php
$this->title = 'Email importer';
$this->height = 510;
$this->width = 700;
?>
<script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		/*S.A.FU.init(65);
		S.A.W.uploadify_one('<?php echo $this->name_id?>','import','<?php echo File::uploadifyExt(array('txt','csv','rar','zip','gz'))?>','<?php echo lang('_$TXT or archive files')?>','import', true);*/
		
		S.A.FU.upload({
			height: 65,
			name:'<?php echo $this->name?>',
			id: 0,
			limit: 1,
			sim: 1,
			func: function() {
				S.A.M.importEmails({
					title: 'Email import started',
					descr: 'please wait..',
					percent: 0	
				});
			},
			upload: '<?php echo $this->upload?>',
			buttonImg: S.C.FTP_EXT+'tpls/img/upload.png',
			b_width: 150,
			b_height: 39,
			regex: (/\.(<?php echo join('|',array('txt','csv'))?>)$/i),
			fileExt: '<?php echo File::uploadifyExt(array('txt','csv'))?>',
			fileDesc: '<?php echo lang('_$Text files (txt, csv)')?>',
			error: '<?php echo lang('_Please select a text file')?>'
		});
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<table class="a-form" cellspacing="0">
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Group name:')?></td><td class="a-r" colspan="2" width="85%"><input type="text" name="data[group]" id="data_group_<?php echo $this->name_id?>" value="" class="a-input" style="width:60%" /></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Language:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[lang]" class="a-select" id="data_lang_<?php echo $this->name_id?>"><option value=""></option><?php echo Html::buildOptions('',array_label($this->langs,0));?></select></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Emails:')?></td><td class="a-r" colspan="2" width="85%"><textarea class="a-textarea" name="data[emails]" style="width:99%;height:250px"></textarea></td>
		</tr>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Upload:')?></td><td class="a-r" colspan="2" width="85%">
				<div id="progress" class="progress ui-state-default" style="padding:2px 1%;display:none;width:98%"><div class="progress-title" style="font-weight:bold"></div><div class="progress-bar progress-bar-success ui-state-highlight" style="padding:0;overflow:hidden;height:20px;width:1%;line-height:20px;text-align:right;font-weight:bold"></div></div>
				<div id="jquery_upload" style="position:relative;top:1px;"><img src="<?php echo FTP_EXT?>tpls/img/upload.png" style="width:150px;height:39px;" /><input type="file" id="a-file_<?php echo $this->name?>2" name="Filedata" style="opacity:0;filter: alpha(opacity=0);width:140px;height:39px;position:relative;top:-37px;margin-bottom:-39px;z-index:1;cursor:pointer;cursor:hand;display:block;" /></div>
				<!--<input type="file" class="a-file" id="a-import_<?php echo $this->name_id?>" style="width:80px;" size="2" />-->
			</td>
		</tr>
		
		<?php /*
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Language:')?></td><td class="a-r" colspan="2" width="85%"><select name="data[lang]">
			<?php echo Html::buildOptions($this->lang,array_label($this->langs));?>
		</select></td>
		</tr>
		*/ ?>
	</table>
	<?php $this->inc('bottom', array(
		'save'	=> 'content_save',
		'label'	=> 'Import',
		'action' => 'imp',
		'img'	=> 'oxygen/16x16/actions/document-save.png',

	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>