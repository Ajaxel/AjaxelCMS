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
* @file       tpls/admin/settings_templates_install_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php

$this->title = lang('$Install %1 template',$this->post('name'));
$this->height = 320;
$this->width = 400;
?>
<script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	uploader:0
	,url: S.C.HTTP_EXT+'?json&<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>'
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.FU.init(65);
		var v='<?php echo strjs($this->post('name'))?>';
		if (!$('#a-w-<?php echo $this->name_id?>_prefix').val()) {
			$('#a-w-<?php echo $this->name_id?>_prefix').val(v+'_');
		}
		if (!$('#a-w-<?php echo $this->name_id?>_title').val()) {
			var f=v.substring(0,1).toUpperCase();
			var l=v.substr(1);
			$('#a-w-<?php echo $this->name_id?>_title').val(f+l);
		}
		this.setPrefixHint(<?php echo $this->params['has_tables'] ? 'true':'false'?>);
		$('#a-w-<?php echo $this->name_id?>_prefix').blur(function(){
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=settings&<?php echo self::KEY_TAB?>=<?php echo $this->tab?>',{
				get:'action',
				<?php echo self::KEY_ACTION?>: 'prefix_hint',
				name: '<?php echo $this->post('name')?>',
				prefix:this.value
			}, function(data) {
				window_<?php echo $this->name_id?>.setPrefixHint(data.has_tables);
				$('#a-w-<?php echo $this->name_id?>_prefix').val(data.prefix);
			})
		});
	}
	,setPrefixHint:function(has){
		var ph=$('#a-window_<?php echo $this->name_id?>_prefix_hint');
		if (has) {
			ph.html('<?php echo strjs(lang('$The tables with this prefix your database already has.'))?><br /><?php echo strjs(lang('$Installation of a tables is not needed.'))?>').show();
			$('#a-w-<?php echo $this->name_id?>_sql_file').attr('disabled','disabled');
		} else {
			ph.html('').hide();
			$('#a-w-<?php echo $this->name_id?>_sql_file').removeAttr('disabled');
		}
	}
	,tim:0,button:false
	
	,install:function(o){
		
		S.A.W.no_close='window_<?php echo $this->name_id?>';
		this.button=$(o);
		this.button.button('disable');
		var data=$('#a-form_<?php echo $this->name_id?>').serialize();
		$('input, select',$('#a-form_<?php echo $this->name_id?>')).attr('disabled','disabled');
		clearTimeout(this.tim);
		this.stop=false;
		$.ajax({
			cache: false,
			dataType: 'json',
			url: this.url+'&<?php echo self::KEY_ACTION?>=install',
			type: 'POST',
			data: data,
			success: function(data) {
				if (data.error) {
					S.G.msg(data);
					window_<?php echo $this->name_id?>.button.button('enable');
				}
				else if (data.area) {
					$('#a-window_<?php echo $this->name_id?>_table').fadeOut(function(){
						$('#a-window_<?php echo $this->name_id?>_info').fadeIn();
					});
					$('#a-window_<?php echo $this->name_id?>_process').fadeIn();
					if (data.done) {
						window_<?php echo $this->name_id?>.finish(data);
					} else {
						window_<?php echo $this->name_id?>.restore(data);
					}
				} else {
					window_<?php echo $this->name_id?>.finish(data);
				}
			}
		});	
		
		
	}
	,restore:function(data){
		
		$('#a-window_<?php echo $this->name_id?>_phrase').fadeIn(function(){
			$(this).fadeOut();	
		});
		this.button.button('disable');
		if (data.percent) {
			$('#a-window_<?php echo $this->name_id?>_process_perc').stop().animate({width:data.percent+'%'}).html(data.percent+'%'+(data.percent>100?', (GZ archive)':''));
			$('#a-window_<?php echo $this->name_id?>_run').html(data.text);
		}
		if (data.error) {
			alert(data.error);
		}
		else if (!data.done && data.percent) {
			$.ajax({
				cache: false,
				dataType: 'json',
				url: this.url+'&<?php echo self::KEY_ACTION?>=restore',
				type: 'POST',
				data: {get: 'action'},
				success: function(data) {
					window_<?php echo $this->name_id?>.restore(data);
				}
			});
		} else {
			window_<?php echo $this->name_id?>.finish(data);
		}
	}
	,finish:function(data){
		S.G.msg(data);
		S.A.W.no_close=false;
		this.button.button('enable');
		$('input, select',$('#a-form_<?php echo $this->name_id?>')).removeAttr('disabled');
		$('#a-window_<?php echo $this->name_id?>_process_perc').stop().css({width:'100%'}).html('100%');
		S.A.W.close();
		S.A.L.get('<?php echo URL::rq(self::KEY_TYPE,$this->url_full)?>',false,'<?php echo $this->tab?>');
	}
	
	
	
	
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div style="font-weight:bold;font-size:12px;height:187px;padding:5px!important;display:none" class="ui-widget-content" id="a-window_<?php echo $this->name_id?>_info">
		<div style="height:20px"><div id="a-window_<?php echo $this->name_id?>_phrase"><?php echo lang('$Installing %1 template, please wait...',$this->post('name'))?></div></div>
		<div style="height:25px;width:90%;margin:12px auto;display:none" class="ui-widget ui-state-default" id="a-window_<?php echo $this->name_id?>_process">
			<div class="ui-state-highlight" id="a-window_<?php echo $this->name_id?>_process_perc" style="line-height:28px;font-size:20px;height:25px;width:0%;overflow:hidden;text-align:right">0%</div>
		</div>
		<div id="a-window_<?php echo $this->name_id?>_run" style="padding-top:10px;font-weight:normal"><?php echo lang('$Preparing..')?></div>
	</div>
	<table class="a-form" cellspacing="0" id="a-window_<?php echo $this->name_id?>_table">
		<tr>
			<td class="a-l" width="15%" nowrap><?php echo lang('$Folder name:')?></td><td class="a-r" width="85%"><input type="hidden" name="data[name]" value="<?php echo $this->post('name')?>" /><?php echo $this->post('name')?></td>
		</tr>
		<?php if ($this->params['preview']):?>
		<tr>
			<td class="a-l" width="15%"><?php echo lang('$Preview:')?></td><td class="a-r" width="85%"><a href="/<?php echo $this->params['preview']?>" class="a-thumb"><img src="/<?php echo $this->params['preview_th']?>" alt="" /></a></td>
		</tr>
		<?php endif;?>
		<tr>
			<td class="a-l"><?php echo lang('$Engine:')?></td><td class="a-r"><select style="width:50%" class="a-select" type="text" name="data[engine]"><?php echo Html::buildOptions($this->post('engine', false), $this->options['engines'])?></select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Data prefix:')?></td><td class="a-r">
				<input class="a-input" type="text" name="data[prefix]" value="<?php echo $this->post('prefix')?>" id="a-w-<?php echo $this->name_id?>_prefix" style="width:60%" />
				<div class="a-hint" id="a-window_<?php echo $this->name_id?>_prefix_hint"></div>
			</td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$SQL File')?></td><td class="a-r"><select style="width:100%" class="a-select" type="text" name="data[sql_file]" id="a-w-<?php echo $this->name_id?>_sql_file"><?php echo Html::buildOptions($this->post('sql_file', false), $this->params['sql_files'])?></select></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Title:')?></td><td class="a-r"><input class="a-input" type="text" name="data[title]" value="<?php echo $this->post('title')?>" id="a-w-<?php echo $this->name_id?>_title" style="width:98%" /></td>
		</tr>
		<tr>
			<td class="a-l"><?php echo lang('$Description:')?></td><td class="a-r"><textarea class="a-textarea"  name="data[descr]" style="width:98%;height:65px"><?php echo $this->post('descr')?></textarea></td>
		</tr>
	</table>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<td class="a-td2">
			<?php $this->inc('button',array('click'=>'window_'.$this->name_id.'.install(this)','img'=>'oxygen/16x16/actions/document-save.png','text'=>lang('$Install'))); ?>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>