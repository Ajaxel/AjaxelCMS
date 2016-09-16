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
* @file       tpls/admin/forms_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'Edit':'Add new').' form:').' ';
$this->title = $title.$this->post('name', false);
$this->height = 635;
$this->width = 920;
$tab_height = 500;
?><script type="text/javascript">
<?php echo Index::CDA?>
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo $title?>'+this.value);
		});
		S.A.W.tabs('<?php echo $this->name_id?>');
		this.sortable_fields();
		/*S.A.W.maximize('window_<?php echo $this->name_id?>');*/
	}
	,sortable_fields:function(){
		$('#a-formfields_<?php echo $this->name_id?>').sortable({
			contaiment: 'parent',
			start:function(){
				
			},
			update:function(){
				var arr = '',ul = $(this),i = 1;
				ul.find('li').each(function(){
					var o=$(this);
					var j=o.attr('id').substring(5);
					arr += j+'-'+i+'|';
					i++;
				});
				ul.sortable('refresh');
				S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&id=<?php echo $this->id?>', {
					get: 'sort_element',
					sort: arr
				}, function(data) {
					
				})
			}
		})/*.find('li').resizable({
			items:'>li'	
		})*/;
		$('#a-formfields_<?php echo $this->name_id?> li').click(function(){
			$('#a-formfields_<?php echo $this->name_id?> div').removeClass('a-clicked');
			$(this).children(0).addClass('a-clicked');
			var id=parseInt(this.id.substring(5));
			window_<?php echo $this->name_id?>.element(false, id);
		})
	}
	,element:function(type, fid){
		if (!fid) $('#a-formfields_<?php echo $this->name_id?> div').removeClass('a-clicked');
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&id=<?php echo $this->id?>',{
			get:'edit_element',
			type:type,
			fid: fid
		}, function(data) {
			$('#a-fieldset_<?php echo $this->name_id?>').html(data.field_settings);
			window_<?php echo $this->name_id?>.accordion();
		})
	}
	,accordion:function(){
		$('#a-accordion_<?php echo $this->name_id?>').show();
		S.A.L.ready();
		return;
		$('#a-accordion_<?php echo $this->name_id?>').accordion({
			autoHeight: false,
			clearStyle: true,
			animates: 'swing',
			collapsible: false,
			animate: false
		}).show();
		$('.a-button').button();
	}
	,save:function(fm){
		$('#a-get_<?php echo $this->name_id?>').val('save_element');
		var post = $('#a-form_<?php echo $this->name_id?> .a-el_<?php echo $this->name_id?>').serialize();
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&id=<?php echo $this->id?>', post, function(data) {
			$('#a-fieldset_<?php echo $this->name_id?>').html('');
			$('#a-formfields_<?php echo $this->name_id?>').html(data.form_preview);
			$('#a-el_type_<?php echo $this->name_id?>').val('');
			window_<?php echo $this->name_id?>.sortable_fields();
			$('#a-formfields_<?php echo $this->name_id?> div').removeClass('a-clicked');
		})
	}
	,del:function(fid){
		if(confirm('<?php echo lang('$Are you sure to remove this form element?')?>')) {
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->name?>&id=<?php echo $this->id?>',{
				get:'delete_element',
				fid: fid
			}, function(data) {
				$('#a-fieldset_<?php echo $this->name_id?>').html('');
				window_<?php echo $this->name_id?>.accordion();
				$('#a-formfields_<?php echo $this->name_id?>').html(data.form_preview);
				$('#a-el_type_<?php echo $this->name_id?>').val('');
				window_<?php echo $this->name_id?>.sortable_fields();
				$('#a-formfields_<?php echo $this->name_id?> div').removeClass('a-clicked');
			})
		}
	}
	,copy:function(){
		
	}
}
<?php $this->inc('js_pop')?>
<?php echo Index::CDZ?>
</script>
<?php 
$this->inc('window_top');
?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_form_<?php echo $this->name_id?>"><?php echo lang('$Form elements')?></a></li>
			<li><a href="#a_display_<?php echo $this->name_id?>"><?php echo lang('$Searching')?></a></li>
			<li><a href="#a_perms_<?php echo $this->name_id?>"><?php echo lang('$Permissions')?></a></li>
			<li><a href="#a_template_<?php echo $this->name_id?>"><?php echo lang('$Template')?></a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<div id="a_form_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form a-accordion" cellspacing="0"><tr><td width="35%" valign="top">
				<table class="a-form_all" cellspacing="0">
					<tr>
						<td class="a-m<?php echo $this->ui['sub-h1']?>" colspan="2"><?php echo lang('$Form settings:')?></td>
					</tr>
				</table>
				<div class="a-div">
				<table class="a-form_all" cellspacing="0">
					<tr>
						<td class="a-l" width="35%"><?php echo lang('$Form name:')?></td><td class="a-r" width="65%"><input type="text" class="a-input a-el_<?php echo $this->name_id?>" id="a-w-title_<?php echo $this->name_id?>" style="width:98%" name="data[name]" value="<?php echo $this->post('name')?>" /></td>
					</tr>
					<tr>
						<td class="a-l"><?php echo lang('$Colspans:')?></td><td class="a-r"><select class="a-select a-el_<?php echo $this->name_id?>" name="data[fs][colspan]" onchange="window_<?php echo $this->name_id?>.save(this.form)"><?php echo Html::buildOptions($this->post['fs']['colspan'],Html::arrRange(1,9));?></select></td>
					</tr>
					<tr>
						<td class="a-l"><?php echo lang('$Add element:')?></td><td class="a-r"><select class="a-select a-el_<?php echo $this->name_id?>" id="a-el_type_<?php echo $this->name_id?>" onchange="window_<?php echo $this->name_id?>.element(this.value);"><option value=""></option><?php echo Html::buildOptions(0,array_label($this->editor->types()));?></select></td>
					</tr>
				</table>
				</div>
				<div id="a-fieldset_<?php echo $this->name_id?>"></div>
			</td>
			<td width="1%">&nbsp;</td>
			<td width="64%" class="" valign="top">
				<table class="a-form" cellspacing="0">
					<tr>
						<td class="a-m"><?php echo lang('$Form preview:')?> <span class="a-cnt"><?php echo lang('$(click on element to edit, drag to re-order)')?></span></td>
					</tr>
					<tr>
						<td class="a-l" style="text-align:left">			
							<ul id="a-formfields_<?php echo $this->name_id?>" class="a-formfields">							
								<?php $this->inc('form_preview',array('form'=>$this->form))?>							
							</ul>
						</td>
					</tr>
				</table>
			</td></tr></table>
		</div>
		<div id="a_display_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">

			</table>
		</div>
		<div id="a_perms_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Orders:')?></td><td class="a-r" colspan="5" width="85%">
						coming soon
					</td>
				</tr>
			</table>
		</div>
		<div id="a_template_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[template]" style="width:99%;height:325px"><?php echo $this->post('template')?></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:325px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $url = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,'id'=>$this->id,'l'=>$this->lang)); ?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td class="a-td1">&nbsp;
			
		</td>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?php if ($this->id):?>
			<button type="button" class="a-button" onclick="S.A.W.save('<?php echo $url?>&a=save', this.form, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/document-save.png" alt="" /> <?php echo lang('$Save')?></button>
			<?php else:?>
			<button type="button" class="a-button" onclick="S.A.W.save('<?php echo $url?>&a=save', this.form, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" alt="" /> <?php echo lang('$Add')?></button>
			<?php endif;?>
			</td></tr></table>
		</td>
		<?php if ($this->id):?>
		<td  class="a-td3"><?php if ($this->id):?><?php echo lang('$Last time edited:')?> <?php echo date('d M Y',$this->post('edited', false))?><br /><?php echo lang('$by author:')?> <?php echo $this->post('username')?><?php endif;?>&nbsp;</td>
		<?php endif;?>
		</tr></table>
	</div>
	<input type="hidden" name="get" id="a-get_<?php echo $this->name_id?>" class="a-el_<?php echo $this->name_id?>" value="action" />
</form>
<?php $this->inc('window_bottom')?>