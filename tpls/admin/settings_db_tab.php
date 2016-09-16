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
* @file       tpls/admin/settings_db_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$area = 'restore';
$set = Cache::getSmall('db_restore');
if (!$set || !isset($set['start']) || !$set['start']) {
	$area = 'backup';
	$set = Cache::getSmall('db_backup');
}
?>
<script type="text/javascript">
<?php echo Index::CDA?>
S.A.L.db_backup=function(obj){
	if ($('#a_file_<?php echo $this->name?>_name').length && $('#a_file_<?php echo $this->name?>_name').val().indexOf('_<?php echo date('d-m-Y')?>')===-1) {
		$('#a_file_<?php echo $this->name?>_name').val($('#a_file_<?php echo $this->name?>_name').val()+'_<?php echo date('d-m-Y')?>');
	}
	$('#<?php echo $this->name?>-form').fadeOut(function(){
		S.A.L.db_prepare('backup');
		window.scroll(0,0);
		S.A.L.db_process.fadeIn(function(){
			S.G.loader=false;
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',$('#<?php echo $this->name?>-form').serialize(), function(data){
				if (data.type) {
					data.reload = false;
					S.A.W.dialog(data, false, function(){
						S.A.L.db_process.fadeOut(function(){
							$('#<?php echo $this->name?>-form').fadeIn();
						});
					});
				} 
				else if (data.error) S.A.L.db_error(data);
				else {
					if (!data.done) {
						$('<li>').html(data.text).prependTo(S.A.L.db_process_ul);
						S.A.L.db_loop('backup');
					} else {
						S.A.L.db_done(data,'backup');
					}
				}
			});
		});
	});
}
S.A.L.db_restore=function(obj, file){
	$(obj).parent().parent().css({
		background: '#F5623D'
	});
	if (file && !confirm('<?php echo lang('$Are you sure to restore this backup:')?> '+file+'?')) {
		$(obj).parent().parent().css({
			background: ''
		});
		return false;
	}
	$('#<?php echo $this->name?>-form').fadeOut(function(){
		S.A.L.db_prepare('restore');
		window.scroll(0,0);
		S.G.loader=false;
		S.A.L.db_process.fadeIn(function(){
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',{
				get: 'action',
				db_name: '<?php echo $this->db_name?>',
				a: 'restore',
				<?php echo self::KEY_FILE?>: file
			}, function(data){
				if (!data) {
					alert('System error. Something went wrong');
					return false;
				}
				if (data.type) {
					data.reload = false;
					S.A.W.dialog(data, false, function(){
						S.A.L.db_process.fadeOut(function(){
							$('#<?php echo $this->name?>-form').fadeIn();
						});
					});
				}
				else if (data.error) S.A.L.db_error(data);
				else if (data.text) {
					if (!data.done) {
						S.G.loader=true;
						$('<li>').html(data.text).css({display:'none'}).prependTo(S.A.L.db_process_ul).slideDown('slow');
						S.A.L.db_loop('restore');
					} else {
						S.A.L.db_done(data,'restore');
					}
				} else {
					S.G.alert(data,'System error. Something went wrong');
				}
			})
		});
	});
}
S.A.L.db_error=function(data){
	$('<li>').css({color:'red','font-weight':'bold'}).html(data.error).appendTo(S.A.L.db_process_ul);
	$('<li>').html('<div style="text-align:center;padding:20px 0"><button type="button" class="a-button" style="width:90px" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&db_name=<?php echo $this->db_name?>\', false, \'<?php echo $this->tab?>\')"><?php echo lang('$OK')?></button></div>').css({display:'none'}).prependTo(S.A.L.db_process_ul).slideDown();$('.a-button').button();
}
S.A.L.db_prepare=function(action){
	S.A.L.db_process = $('#<?php echo $this->name?>-process');
	var html = '<table class="a-list a-list-one" cellspacing="0">';
	html += '<tr><td class="a-fm<?php echo $this->ui['a-fm']?>">'+(action=='restore'?'<?php echo lang('_$Database restore process, please wait..')?>':'<?php echo lang('_$Database backup process, please wait..')?>')+'</td></tr>';
	html += '<tr><td><div id="<?php echo $this->name?>-process_perc" style="height:25px;margin:5px 0;width:<?php echo (($set && $set['percent']>1 && ($set['percent']<100 && $set['ext']!='php'))?$set['percent']:'0')?>%;overflow:hidden;display:block" class="ui-state-active"><div style="padding-left:5px;font-weight:bold;padding-top:5px;"><?php echo (($set && $set['percent'])?$set['percent']:'1')?>%</div></div></td></tr>';
	html += '<tr><td><ul id="<?php echo $this->name?>-process_ul"></ul></td></tr>';
	html += '</table>';
	S.A.L.db_process.html(html);
	S.A.L.db_process_ul = $('#<?php echo $this->name?>-process_ul');
}
S.A.L.db_loop=function(action) {
	S.G.loader=false;
	S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',{
		get: 'action',
		db_name: '<?php echo $this->db_name?>',
		a: action
	}, function(data){
		if (!data) {
			alert('System error. Something went wrong');
			return false;
		}
		else if (data.error) S.A.L.db_error(data);
		else if (!data.text) return false;	
		else {
			$('#<?php echo $this->name?>-process_perc').stop().animate({width: data.percent+'%'}).children().html(data.percent+'%');
			if (!data.done) {
				if (action=='restore') {
					S.A.L.db_process_ul.html('');
					$('<li>').html(data.text).css({display:'none'}).prependTo(S.A.L.db_process_ul).show();
				} else {
					$('<li>').html(data.text).css({display:'none'}).prependTo(S.A.L.db_process_ul).slideDown('slow');
				}
				S.A.L.db_loop(action);
			} else {
				S.A.L.db_done(data,action);
			}
		}
	});
}
S.A.L.db_done=function(data, action){
	S.G.hideLoader();
	$('#<?php echo $this->name?>-process_perc').stop().css({width: '100%'}).children().html('100%');
	$('<li>').html(data.text).css({display:'none',color:'green','font-weight':'bold',padding:'4px 0'}).prependTo(S.A.L.db_process_ul).slideDown('slow', function(){$(this).show('highlight')});
	$('<li>').html('<div style="text-align:center;padding:20px 0"><button type="button" class="a-button" style="width:90px" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&db_name=<?php echo $this->db_name?>\', false, \'<?php echo $this->tab?>\')"><?php echo lang('$OK')?></button></div>').css({display:'none'}).prependTo(S.A.L.db_process_ul).slideDown();$('.a-button').button();	
}
S.A.L.db_del=function(file, obj){
	if (confirm('<?php echo lang('$Are you sure to delete this backup?')?> '+file)) {
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&tab=db',{
			get: 'action',
			a: 'delete',
			<?php echo self::KEY_FILE?>: file
		}, function(data){
			$(obj).parent().parent().fadeOut(function(){
				$(this).remove();	
			});
		})
	}
}
S.A.L.db_cancel_backup=function(){
	if(confirm('Are you sure to cancel the <?php echo ($area=='backup'?'backup':'restore')?> interruption?')) {
		S.G.json('?<?php echo URL_KEY_ADMIN?>=global&clean=db_<?php echo $area?>',{},function(){
			S.A.L.tab.tabs('load',S.A.L.tab_index);
		});
	}
}

<?php if (!($set && $set['start'])):?>
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>,a;
	var html = '<table class="a-list a-list-one" cellspacing="0">';
	html += '<tr><td class="a-fm<?php echo $this->ui['a-fm']?>" colspan="8"><?php echo lang('$Database tables')?></td></tr>';
	html += '<tr><th colspan="2" width="30%"><?php echo lang('$Table name')?></th><th width="15%" colspan="2"><?php echo lang('$Size')?></th><th width="15%"><?php echo lang('$Index size')?></th><th><?php echo lang('$Total rows')?></th><th><?php echo lang('$Increment')?></th><th width="15%"><?php echo lang('$Created')?></th></tr><tbody class="a-tbody">';
	var x=0;
	for(table in data) {
		x++;
		a=data[table]
		html += '<tr class="'+(x%2?'':'a-odd')+' a-hov">';
		html += '<td width="2%" class="a-fl"><input type="checkbox" name="data[table][]" value="'+a[0]+'" id="a_chk_<?php echo $this->name?>_table_'+a[0]+'" class="a_chk_<?php echo $this->name?> a-checkbox'+(table.substring(0,<?php echo strlen($this->template)?>)=='<?php echo $this->template?>'?' a_chk_<?php echo $this->name?>_template':'')+'" value="on" /></td>';
		html += '<td class="a-fl"><a href="javascript:;" onclick="S.A.M.edit(\''+table+'&db_name=<?php echo $this->db_name?>\', this)">'+table+'</a></td>';
		html += '<td class="a-fl a-r" nowrap><label for="a_chk_<?php echo $this->name?>_table_'+a[0]+'" title="'+a[6]+'">'+a[1]+'</label></td>';
		html += '<td class="a-fl a-r"><input type="checkbox" name="data[excl][]" value="'+a[0]+'" class="a_chk_<?php echo $this->name?>_excl a-checkbox" style="width:12px;" value="on" /></td>';
		html += '<td class="a-fl a-r">'+a[2]+'</td>';
		html += '<td class="a-fl a-r">'+a[3]+'</td>';
		html += '<td class="a-fl a-r">'+(a[5]?a[5]:'')+'</td>';
		html += '<td class="a-fl a-r">'+a[4]+'</td>';
		html += '</tr>';
	}
	html += '</tbody><tr class="a-fb">';
	html += '<td class="a-fl a-fn"><input type="checkbox" id="a_chk_<?php echo $this->name?>_all_tables" onclick="$(\'#a_chk_<?php echo $this->name?>_template\').removeAttr(\'checked\');if(this.checked)$(\'.a_chk_<?php echo $this->name?>\').attr(\'checked\',\'checked\');else $(\'.a_chk_<?php echo $this->name?>\').removeAttr(\'checked\');" /></td>';
	html += '<td class="a-fl"><label for="a_chk_<?php echo $this->name?>_all_tables"><?php echo lang('$Check all')?></label> | <label><input type="checkbox" id="a_chk_<?php echo $this->name?>_template" onclick="if(this.checked)$(\'.a_chk_<?php echo $this->name?>_template\').attr(\'checked\',\'checked\');else $(\'.a_chk_<?php echo $this->name?>_template\').removeAttr(\'checked\');" /> <?php echo lang('$Template only')?></label></td>';
	html += '<td class="a-fl a-r" colspan="2" nowrap><?php echo $this->params['db']['total_size']?></td>';
	html += '<td class="a-fl a-r" nowrap><?php echo $this->params['db']['total_i_size']?></td>';
	html += '<td class="a-fl a-r"><?php echo $this->params['db']['total_rows']?></td>';
	html += '<td colspan="2" class="a-fl">&nbsp;</td>';
	html += '</tr>';
	
	html += '<tr class="a-odd"><td colspan="8"><table cellspacing="0" width="100%"><tr><td class="a-fl">';
	html += '<input type="text" class="a-input" id="a_file_<?php echo $this->name?>_name" style="width:200px" value="<?php echo $this->db_name?>-<?php echo ($this->db_name!=DB_NAME?'':$this->current['PREFIX'])?><?php echo date('d-m-Y')?><?php echo (get('all')?'_all':'')?>" name="data[name]" /> <select class="a-select" name="data[type]" onchange="if(this.value==\'php\'){$(\'.a_chk_<?php echo $this->name?>_excl\').hide();$(\'#a_chk_<?php echo $this->name?>_no_php\').attr(\'disabled\',true)}else{$(\'.a_chk_<?php echo $this->name?>_excl\').show();$(\'#a_chk_<?php echo $this->name?>_no_php\').attr(\'disabled\',false)}"><?php echo Html::buildOptions($this->data['type'], $this->params['db']['backup_types'])?></select><span id="a_chk_<?php echo $this->name?>_no_php"> <label><input type="checkbox" name="data[structure]" class="a-checkbox" checked="checked" value="on" /><?php echo lang('$Structure')?></label> <label><input type="checkbox" name="data[data]" class="a-checkbox" checked="checked" /><?php echo lang('$Data')?></label> <label><input type="checkbox" class="a-checkbox" name="data[full_insert]" /><?php echo lang('$Full insert')?></label> <label><input type="checkbox" class="a-checkbox" name="data[normal]" /><?php echo lang('$No prefix')?></label><input type="checkbox" class="a-checkbox" name="data[no_separator]" id="a_chk_<?php echo $this->name?>_no_separator" /><label for="a_chk_<?php echo $this->name?>_no_separator"><?php echo lang('$Default mode')?></label></span></td>';
	html += '<td class="a-r"><button type="button" class="a-button a-r" onclick="S.A.L.db_backup(this)"><?php echo lang('$Backup')?></button></td>';
	html += '</td></tr></table></td></tr>';
	data = <?php echo $this->output ? $this->output['restore'] : '{}'?>;
	html += '<tr><td class="a-fm<?php echo $this->ui['a-fm']?>" colspan="8"><?php echo lang('$Restore files')?></td></tr>';
	html += '<tr class="a-odd"><td colspan="8"><table cellspacing="0" cellpadding="0" width="100%">';
	i=1;
	for (t in data) {
		a=data[t];
		html += '<tr class="'+(x%2?'':'a-odd')+' a-hov">';
		html += '<td class="a-fl"><a href="javascript:S.G.download(\'<?php echo FTP_DIR_TPLS.$this->template?>/backup/'+a.file+'\', true)">'+a.file+'</a></td>';
		html += '<td class="a-l">'+a.size+'</td>';
		html += '<td class="a-l" title="'+a.time_full+'">'+a.time+'</td>';
		html += '<td class="a-r"><button type="button" class="a-button" onclick="S.A.L.db_restore(this, \''+a.file+'\')"><?php echo lang('$Restore')?></button></td>';
		html += '<td class="a-r a-action_buttons" width="1%"><a href="javascript:;" onclick="S.A.L.db_del(\''+a.file+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a></td></tr>';
		i++;
	}
	html += '</table></td></tr>';
	html += '<tr><td class="a-fm<?php echo $this->ui['a-fm']?>" colspan="8"><?php echo lang('$Upload a backup file')?></td></tr>';
	html += '<tr><td class="a-l" colspan="8">';
	html += '<div id="progress" class="progress ui-state-default" style="padding:2px 1%;display:none;width:98%"><div class="progress-title" style="font-weight:bold"></div><div class="progress-bar progress-bar-success ui-state-highlight" style="padding:0;overflow:hidden;height:20px;width:1%;line-height:20px;text-align:right;font-weight:bold"></div></div><div id="files" class="files"></div><table cellspacing="0"><tr>';
	/*html += '<td><?php echo lang('$Flash upload:')?>&nbsp;<td><div class="a-file_btn"><input type="file" class="a-file" id="a-file_<?php echo $this->name?>" /></div></td>';
	html += '<td><?php echo lang('$Huge files upload:')?>&nbsp;</td>';*/
	html += '<td><div id="jquery_upload" style="position:relative;top:1px;"><img src="<?php echo FTP_EXT?>tpls/img/upload.png" style="width:150px;height:39px;" /><input type="file" id="a-file_<?php echo $this->name?>2" name="Filedata" style="opacity:0;filter: alpha(opacity=0);width:140px;height:39px;position:relative;top:-37px;margin-bottom:-39px;z-index:1;cursor:pointer;cursor:hand;display:block;" /></div></td>';
	html += '</tr></table>';
	html += '</td></tr>';
	
	html += '<tr><td class="a-fm<?php echo $this->ui['a-fm']?>" colspan="8"><?php echo lang('$Create new table on database %1',$this->db_name)?></td></tr>';
	html += '<tr><td colspan="8"><table class="a-form"><tr>';
	html += '<td class="a-l" width="15%"><?php echo lang('$Name:')?></td><td class="a-r"><input type="text" id="a_<?php echo $this->name?>_table" class="a-input" style="width:250px" /></td><td class="a-l"><?php echo lang('$Number of fields:')?></td><td class="a-r"><input type="text" id="a_<?php echo $this->name?>_fields" onfocus="this.select()" class="a-input" style="width:70px" value="1" /></td><td class="a-l"><button type="button" onclick="if ($(\'#a_<?php echo $this->name?>_table\').val()) S.A.M.edit(\'<?php echo self::KEY_NEW?>&table_name=\'+$(\'#a_<?php echo $this->name?>_table\').val()+\'&db_name=<?php echo $this->db_name?>&fields=\'+$(\'#a_<?php echo $this->name?>_fields\').val(), this); else $(\'#a_<?php echo $this->name?>_table\').focus()" class="a-button"><?php echo lang('$Go')?></button></tr>';
	html += '</tr></table></td></tr>';
	html += '<tr><td class="a-fm<?php echo $this->ui['a-fm']?>" colspan="8"><?php echo lang('$Create new database for %1',DB_USERNAME)?></td></tr>';
	html += '<tr><td colspan="8"><table class="a-form"><tr>';
	html += '<td class="a-l" width="15%"><?php echo lang('$Name:')?></td><td class="a-r"><input type="text" id="a_<?php echo $this->name?>_database" class="a-input" style="width:250px" /><td class="a-l"><button type="button" onclick="if ($(\'#a_<?php echo $this->name?>_database\').val()) S.A.L.get(\'?<?php echo $this->url?>&reset&new_database=\'+$(\'#a_<?php echo $this->name?>_database\').val(), false, \'<?php echo $this->tab?>\', this); else $(\'#a_<?php echo $this->name?>_database\').focus()" class="a-button"><?php echo lang('$Go')?></button></tr>';
	html += '</tr></table></td></tr>';
	html += '</table>';
	S.A.L.ready(html);
	
	var old = $('#a_file_<?php echo $this->name?>_name').val();
	$('.a_chk_<?php echo $this->name?>').click(function(){
		if (this.checked) {
			S.A.L.blink($(this).parent().parent().addClass('ui-selected'),10,'ui-selected');
		}
		var l = $('.a_chk_<?php echo $this->name?>:checked').length;
		if (l>0&&l<=2) {
			var a=[];
			$('.a_chk_<?php echo $this->name?>:checked').each(function(){
				a[a.length]=$(this).val();
			});
			$('#a_file_<?php echo $this->name?>_name').val(old+'('+a.join(',')+')');
		}
		else {
			$('#a_file_<?php echo $this->name?>_name').val(old);	
		}
	});
	S.A.L.selectable();
	
	S.A.L.url = '?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&db_name=<?php echo $this->db_name?>'
	S.A.FU.upload({
		height: 65,
		name:'<?php echo $this->name?>',
		id: 0,
		limit: 1,
		sim: 1,
		upload: '<?php echo $this->upload?>',
		buttonImg: S.C.FTP_EXT+'tpls/img/upload.png',
		b_width: 150,
		b_height: 39,
		regex: (/\.(<?php echo join('|',array('zip','php','gz','sql'))?>)$/i),
		fileExt: '<?php echo File::uploadifyExt(array('zip','php','gz','sql'))?>',
		fileDesc: '<?php echo lang('_$Backup files (sql, gz, zip, php)')?>',
	});
});

<?php else:?>
$().ready(function(){
	S.A.L.ready();
});
<?php endif;?>
<?php echo Index::CDZ?>
</script>
<div id="<?php echo $this->name?>-process" style="display:none" class="a-content"></div>
<form method="POST" id="<?php echo $this->name?>-form" class="ajax_form" action="?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>&<?php echo self::KEY_LOAD?>&db_name=<?php echo $this->db_name?>">
<?php if ($set && $set['start']):?>
<div class="ui-content" style="text-align:center;">
	<table cellspacing="0" width="100%" class="a-list a-list-one">
		<tr><td class="a-fl a-c" style="padding:40px 0;"><button type="button" class="a-button" onclick="S.A.L.db_<?php echo $area?>(this)"><?php echo lang(($area=='backup'?'$Backup process has been stopped at %1%, please click here to continue..':'$Restore process has been interrupted at %1%, please click here to resume..'),$set['percent'])?></button></td></tr>
		<tr><td class="a-search a-c" style="padding:10px 0"><button type="button" class="a-button" onclick="S.A.L.db_cancel_backup();"><?php echo lang('$Cancel')?></button></td></tr>
	</table>
</div>
<?php else:?>
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('template', array('tab'=>$this->tab))?>
	<?php if (count($this->params['db']['databases'])>1):?>
	<?php echo lang('$Database:');?> <select onchange="S.A.L.get('?<?php echo $this->url?><?php echo (get('all')?'&all=true':'')?>&db_name='+this.value, true, '<?php echo $this->tab?>')">
		<?php echo Html::buildOptions($this->db_name, $this->params['db']['databases'],true)?>
	</select>
	<?php endif;?>
	<?php if ($this->db_name==DB_NAME):?>
	<input type="checkbox" class="a-checkbox" onclick="S.A.L.get('?<?php echo URL::rq('all',$this->url)?>&db_name=<?php echo $this->db_name?>'+(this.checked?'&all=true':''), false, '<?php echo $this->tab?>')"<?php echo (get('all')?' checked="checked"':'')?> id="a-w_all" /><label for="a-w_all"><?php echo lang('$Show all')?></label>
	<?php endif;?>
	</div>
	<div class="a-r" style="padding-top:2px;">
		<button type="button" class="a-button" onclick="S.A.L.get('?<?php echo $this->url?>&reset&db_name=<?php echo $this->db_name?>', false, '<?php echo $this->tab?>')"><?php echo lang('$Reload')?></button>
		<?php $this->inc('help_buttons')?>
	</div>
</div>
<div id="<?php echo $this->name?>-content" class="a-content"><?php $this->inc('loading')?></div>
<?php endif;?>
<input type="hidden" name="all" value="<?php echo get('all')?>">
<input type="hidden" name="db_name" value="<?php echo get('db_name')?>">
<input type="hidden" name="get" value="action">
<input type="hidden" name="<?php echo self::KEY_ACTION?>" value="backup">
<input type="hidden" name="<?php echo $this->name?>-submitted" value="1" />
</form>