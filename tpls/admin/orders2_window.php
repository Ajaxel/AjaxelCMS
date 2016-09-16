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
* @file       tpls/admin/orders2_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$title = lang('$'.($this->id ? 'View':'Add new').' order:').' ';
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	items: 0,
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.W.tabs('<?php echo $this->name_id?>');
		
		S.A.L.user($('#a-username_<?php echo $this->name_id?>'),false,function(value){
			S.A.L.json('?<?php echo URL_KEY_ADMIN?>=global',{
				get:'action',
				a:'user_data',
				id: value
			}, function(d){
				var arr={
					userid: d.id,
					firstname: d.profile.firstname,
					lastname: d.profile.lastname,
					company: d.profile.company,
					reg_nr: d.profile.reg_nr,
					vat_nr: d.profile.vat_nr,
					email: d.email,
					cellphone: d.profile.phone,
					homephone: d.profile.fax,
					address: d.profile.street,
					city: d.profile.city_name,
					country: d.profile.country_name,
					zip: d.profile.zip
				};
				for (k in arr) {
					$('#a-'+k+'_<?php echo $this->name_id?>').val(arr[k]);
				}
			});
		});
		
		var prod_tbl = false;
		if (!prod_tbl) prod_tbl = $('#a-product_table_<?php echo $this->name_id?>').val();
		$('#a-product_table_<?php echo $this->name_id?>').change(function(){
			if ($('#a-products_<?php echo $this->name_id?>').children().length>2 || $('#a-products_<?php echo $this->name_id?>').find('input:first').val()) {
				if (confirm('<?php echo lang('$Are you sure to change `Product table`?')?>\n\n<?php echo lang('$Added products will be dropped')?>')) {
					S.A.S.columns('a-product_column_<?php echo $this->name_id?>',this.value);
					$('#a-products_<?php echo $this->name_id?>').empty();
					window_<?php echo $this->name_id?>.arr_product_row();
				} else {
					$(this).val(prod_tbl);
					return false;	
				}
			} else {
				S.A.S.columns('a-product_column_<?php echo $this->name_id?>',this.value);
				$('#a-products_<?php echo $this->name_id?>').empty();
				window_<?php echo $this->name_id?>.arr_product_row();	
			}
		});
		this.arr_product_row();
		
		S.A.FU.init(200);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png','psd','rar','html','txt','doc','docx','zip'))?>','Images, documents','save_images');
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files,'photo');
	}
	,setImages:function(files,name){
		var html = '<ul id="a-'+name+'s_<?php echo $this->name_id?>">';
		var html2 = '<ul id="a-'+name+'s2_<?php echo $this->name_id?>">';
		var j=0,k=0;

		if (files.length) {

			for (i=0;i<files.length;i++) {
				a=files[i];
				if (a.t=='image') {
					html += '<li class="a-photo'+(a.m?' a-main_photo':'')+'" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
					html +='<div class="a-p"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f)+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></div></li>';
					j++;
				}
				else if (a.t=='video' || a.t=='audio' || a.t=='flash') {
					html2 += '<li class="a-photo'+(a.m?' a-main_photo':'')+' a-sortable" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
					html2 +='<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f)+'" target="_blank"><img src="/tpls/img/oxygen/32x32/actions/media-playback-start.png" /></a><br />'+a.f;
					html2 += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="/tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></div></li>';
					k++;
				}
				else if (a.t!='image' && a.t!='video' && a.t!='audio' && a.t!='flash') {
					html2 += '<li>';
					html2 +='<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f)+'" target="_blank">'+a.f+'</a>';
					html2 += ' <span><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="/tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></span></li>';
					k++;
				}
			}
			
		}
		html += '</ul>';
		if (j) {
			$('#a-'+name+'s_div_<?php echo $this->name_id?>').html(html);
		}
		if (k) {
			$('#a-'+name+'s2_div_<?php echo $this->name_id?>').html(html2);
		}
		/*S.I.init($('#a-'+name+'s_div_<?php echo $this->name_id?>'));*/
		switch (name) {
			case 'photo':
				S.A.W.selectable_images('<?php echo $this->name_id?>','photo');
			break;
			case 'video':
				S.A.W.sortable_images('<?php echo $this->name_id?>','video');
			break;	
		}
		$('#a-img_num_<?php echo $this->name_id?>').html(j+k);
	}

	,rem_product_row:function(o){
		$(o).parent().parent().next().remove();
		$(o).parent().parent().remove();	
	}
	,options:function(opts,index) {
		var h = '', cnt=0;
		for (i in opts) {
			cnt=0;
			for (j in opts[i].values) if (opts[i].values[i]) cnt++;
			if (!opts[i].key || (!cnt && opts[i].key!='custom')) continue;
			h += '<tr><th>'+opts[i].key+'</th><td>';
			switch (opts[i].type) {
				case 'radio':
					for (j in opts[i].values) {
						h += '<label><input type="radio" name="data[products]['+index+'][options]['+i+']" value="'+opts[i].values[j]+'" class="product_option"> '+opts[i].values[j]+'</label>';	
						
					}
				break;	
				case 'checkbox':
					for (j in opts[i].values) {
						h += '<label><input type="checkbox" name="data[products]['+index+'][options]['+i+'][]" value="'+opts[i].values[j]+'" class="product_option"> '+opts[i].values[j]+'</label>';	
					}
				break;
				case 'dropdown':
					h += '<select name="data[products]['+index+'][options]['+i+']" class="product_option">';
					for (j in opts[i].values) {
						h += '<option value="'+opts[i].values[j]+'">'+opts[i].values[j]+'</option>';	
					}
					h += '</select>';
				break;
				case 'custom':
					h += '<input type="text" name="data[products]['+index+'][options]['+i+']" class="product_option" value="">';
				break;	
			}
			h += '</td></tr>';
		}
		return h ? '<table cellspacing="0">'+h+'</table>' : '';
	}
	,arr_product_row:function() {
		var i = this.items;
		var h = '';
		h += '<td class="a-l" width="20%"><input type="text" class="a-input" id="a-product_'+i+'_<?php echo $this->name_id?>" /><input type="hidden" name="data[products]['+i+'][itemid]" id="a-product_'+i+'_itemid_<?php echo $this->name_id?>" value="" /></td>';
		h += '<td class="a-l" width="45%" style="text-align:left"><input type="text" class="a-input" name="data[products]['+i+'][title]" id="a-product_'+i+'_title_<?php echo $this->name_id?>" /></td>';
		h += '<td class="a-l" nowrap><input type="text" class="a-input" name="data[products]['+i+'][price]" onfocus="this.select()" style="width:70px" id="a-product_'+i+'_price_<?php echo $this->name_id?>" /> <?php echo DEFAULT_CURRENCY?></td>';
		h += '<td class="a-l"><input type="text" class="a-input" style="width:30px" name="data[products]['+i+'][quantity]" onfocus="this.select()" id="a-product_'+i+'_quantity_<?php echo $this->name_id?>" /></td>';
		h += '<td class="a-l" nowrap><input type="text" class="a-input" style="width:50px" name="data[products]['+i+'][discount]" id="a-product_'+i+'_discount_<?php echo $this->name_id?>" /> %</td>';
		h += '<td class="a-r a-action_buttons" id="a-product_'+i+'_actions_<?php echo $this->name_id?>" style="vertical-align:middle"><a href="javascript:;" id="a-product_'+i+'_add_<?php echo $this->name_id?>" onclick="window_<?php echo $this->name_id?>.arr_product_row()"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" /></a><a href="javascript:;" id="a-product_'+i+'_remove_<?php echo $this->name_id?>" style="display:none" onclick="window_<?php echo $this->name_id?>.rem_product_row(this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-remove.png" /></a></td>';
		var h2 = '<td colspan="5" class="a-l" style="text-align:left" id="a-product_'+i+'_options_<?php echo $this->name_id?>"><a href="javascript:;" onclick="window_<?php echo $this->name_id?>.product_options(this)">options</a></td>';
		$('#a-products_<?php echo $this->name_id?>').append($('<tr>').html(h)).append($('<tr>').hide().html(h2));
		
		var o = $('#a-product_'+i+'_<?php echo $this->name_id?>');
		
		o.autocomplete({
			source: function(request, response) {
				S.A.L.json('?'+Conf.URL_KEY_ADMIN+'=<?php echo $this->table?>', {
					get:'action',
					a:'product',
					table:$('#a-product_table_<?php echo $this->name_id?>').val(),
					column: $('#a-product_column_<?php echo $this->name_id?>').val(),
					term: request.term
				}, function(data) {
					if (!data.length) {
						o.val(request.term);
						response();
					} else {
						response(data);	
					}
				});
			}
			,position:{ 
				my: "left top",
				at: "left bottom",
				collision: "none" 
			} 
			,focus: function(event, ui) {
				o.val(ui.item.label);
				return false;			
			}
			,select: function(event, ui) {
				o.val(ui.item.label);
				$('#a-product_'+i+'_itemid_<?php echo $this->name_id?>').val(ui.item.value);
				$('#a-product_'+i+'_title_<?php echo $this->name_id?>').val(ui.item.title);
				$('#a-product_'+i+'_price_<?php echo $this->name_id?>').val(ui.item.price);
				$('#a-product_'+i+'_quantity_<?php echo $this->name_id?>').val('1');
				if (ui.item.options && $.isArray(ui.item.options)) {
					$('#a-product_'+i+'_options_<?php echo $this->name_id?>').html(window_<?php echo $this->name_id?>.options(ui.item.options,i)).parent().show();
					$('#a-product_'+i+'_actions_<?php echo $this->name_id?>').attr('rowspan','2');
				}
				return false;
			}
		});
		
		if (this.items>0) {
			for (j=0;j<this.items;j++) {
				$('#a-product_'+j+'_add_<?php echo $this->name_id?>').hide();
				$('#a-product_'+j+'_remove_<?php echo $this->name_id?>').show();
			}
		}
		this.items++;
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->title = $title.$this->post('id', false);
$this->width = 740;
$this->height = 600;
$tab_height = 465;
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<?php if (!$this->id):?><li><a href="#a_products_<?php echo $this->name_id?>"><?php echo lang('$Products')?></a></li><?php endif;?>
			<li><a href="#a_details_<?php echo $this->name_id?>"><?php echo lang('$Order details')?></a></li>
			<?php if ($this->id):?><li><a href="#a_items_<?php echo $this->name_id?>"><?php echo lang('$Product items')?></a></li><?php endif;?>
			<li><a href="#a_buyer_<?php echo $this->name_id?>"><?php echo lang('$Buyer details')?></a></li>
			<li><a href="#a_att_<?php echo $this->name_id?>"><?php echo lang('$Attachments')?> (<span id="a-img_num_<?php echo $this->name_id?>">0</span>)</a></li>
			<li><a href="#a_notes_<?php echo $this->name_id?>"><?php echo lang('$Notes')?></a></li>
		</ul>
		<?php if (!$this->id):?>
		<div id="a_products_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l"><?php echo lang('$Product table:')?></td>
					<td colspan="5" class="a-r">
						<select name="data[products][table]" id="a-product_table_<?php echo $this->name_id?>" class="a-select" style="width:20%;"><?php echo Html::buildOptions($this->params['table'],$this->params['tables'])?></select>
						<?php echo lang('$Search field:')?>
						<select id="a-product_column_<?php echo $this->name_id?>" class="a-select" style="width:20%;"><?php echo Html::buildOptions($this->params['column'],$this->params['columns'])?></select>
					</td>
				</tr>
				<tr>
					<th><?php echo lang('$Search')?></th>
					<th><?php echo lang('$Product name')?></th>
					<th><?php echo lang('$Price')?></th>
					<th><?php echo lang('$Quantity')?></th>
					<th><?php echo lang('$Discount')?></th>
					<th>&nbsp;</th>
				</tr>
				<tbody id="a-products_<?php echo $this->name_id?>">
				
				</tbody>
			</table>
		</div>
		<?php endif;?>
		<div id="a_details_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<?php if ($this->id):?>
			<?php
				$geo = Stats::geoIP(long2ip($this->post['ip']));
			?>
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="2"><?php echo lang('$Order details:')?></td>
				</tr>
				<?php if ($this->post('ordered', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date ordered:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('ordered', false))?></td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="a-l" width="20%"><?php echo lang('$Buyer:')?></td><td class="a-r" width="80%"><?php if ($this->post('userid', false)):?><?php echo $this->post('userid')?>. <?php echo $this->post('username')?> <?php endif;?>[<a target="_blank" href="<?php echo sprintf(IP_LOOKUP,long2ip($this->post('ip', false)))?>"><?php echo long2ip($this->post('ip', false))?></a> <?php echo $geo['city']?>, <?php echo $geo['country_name']?>]</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Status:')?></td><td class="a-r"><select name="data[status]" class="a-select"><?php echo Html::buildOptions($this->post('status', false), array_label(Conf()->g('order_statuses')))?></select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Price cart:')?></td><td class="a-r"><?php echo $this->post('price_basket')?> <?php echo $this->post('currency')?> (<?php echo lang('$%1 {number=%1,item,items}',intval($this->post['quantity_total']))?><?php echo ($this->post('weight', false)?', '.strform($this->post('weight', false)).' g':'')?>)</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Change discount:')?></td><td class="a-r">- <input type="text" class="a-input" name="data[discount]" value="" maxlength="2" style="width:40px" /> %</td>
				</tr>
				<?php if ($this->post('price_discount', false)>0):?>
				<tr>
					<td class="a-l"><?php echo lang('$Price discount:')?></td><td class="a-r"><?php echo $this->post('price_discount')?> <?php echo $this->post('currency')?> (-<?php echo ($this->post['price_basket']>0 ? round($this->post['price_discount']/$this->post['price_basket'] * 100) : 'n/a')?>%)</td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="a-l"><?php echo lang('$Price shipping:')?></td><td class="a-r"><input type="text" class="a-input" name="data[price_shipping]" style="width:80px" value="<?php echo strform($this->post('price_shipping', false))?>" /> <?php echo DEFAULT_CURRENCY?></td>
				</tr>
				<!--
				<?php if ($this->post('price_shipping', false)>0):?>
				<tr>
					<td class="a-l"><?php echo lang('$Price shipping:')?></td><td class="a-r"><?php echo $this->post('price_shipping')?> <?php echo DEFAULT_CURRENCY?></td>
				</tr>
				<?php endif;?>
				-->
				<?php if ($this->post('price_tax', false)>0):?>
				<tr>
					<td class="a-l"><?php echo lang('$Tax:')?></td><td class="a-r"><?php echo $this->post('price_tax')?> <?php echo $this->post('currency')?></td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="a-l"><?php echo lang('$Price total:')?></td><td class="a-r"><b><?php echo $this->post('price_total')?> <?php echo $this->post('currency')?></b></td>
				</tr>
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="2"><?php echo lang('$Shipping / Payment:')?></td>
				</tr>
				<?php if ($this->post('bank', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Payment method:')?></td><td class="a-r"><?php echo strform($this->post('bank', false))?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('shipping_method', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Shipping method:')?></td><td class="a-r"><?php echo $this->post('shipping_method')?><?php if ($this->post('use_shipping', false)):?> <i>(<?php echo lang('$Shipping required')?>)</i><?php endif;?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('address2', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Shipping details:')?></td><td class="a-r"><?php echo $this->post('address2')?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('ship_date_val', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Shipping date:')?></td><td class="a-r"><input type="text" class="a-input a-date" style="width:120px" name="data[ship_date]" value="<?php echo $this->post('ship_date_val')?>" /></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('paid', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date paid:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('paid', false))?></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Paid by:')?></td><td class="a-r"><input type="text" class="a-input" name="data[paidby]" value="<?php echo strform($this->post('paidby', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Paid to:')?></td><td class="a-r"><input type="text" class="a-input" name="data[paidto]" value="<?php echo strform($this->post('paidto', false))?>" /></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('accepted', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date accepted:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('accepted', false))?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('sent', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date sent:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('sent', false))?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('cancelled', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date cancelled:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('cancelled', false))?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('refunded', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date refunded:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('refunded', false))?></td>
				</tr>
				<?php endif;?>
				<?php if ($this->post('credited', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Date credited:')?></td><td class="a-r"><?php echo date('d M Y, H:i',$this->post('credited', false))?> ID: <?php echo $this->post('credit_id', false)?></td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="a-l"><?php echo lang('$Message:')?></td><td class="a-r"><input type="text" class="a-input" name="data[msg]" value="<?php echo $this->post('msg')?>" /></td>
				</tr>
			</table>
			<?php else:?>
			<table class="a-form" cellspacing="0">
				<?php /*
				<tr>
					<td class="a-l"><?php echo lang('$Products:')?></td><td class="a-r"><select name="data[table]" class="a-select" style="width:20%;"><?php echo Html::buildOptions('',array_keys(Site::getModules('grid')),true)?></select> <input type="text" class="a-input" name="data[item_ids]" style="width:75%" value="" /><div class="a-cnt" style="text-align:right">Use ItemID:Quantity (ex: 112:2,113:4,115,118)</div></td>
				</tr>
				*/ ?>
				<tr>
					<td class="a-l" width="20%"><?php echo lang('$Status:')?></td><td class="a-r"><select name="data[status]" class="a-select"><?php echo Html::buildOptions($this->post('status', false), array_label(Conf()->g('order_statuses')))?></select></td>
				</tr>
				
				<tr>
					<td class="a-l"><?php echo lang('$Price shipping:')?></td><td class="a-r"><input type="text" class="a-input" name="data[price_shipping]" style="width:80px" value="<?php echo strform($this->post('price_shipping', false))?>" /> <?php echo DEFAULT_CURRENCY?></td>
				</tr>
				
				<tr>
					<td class="a-l"><?php echo lang('$Payment method:')?></td><td class="a-r"><input type="text" class="a-input" name="data[bank]" style="width:160px" value="<?php echo strform($this->post('bank', false) ? $this->post('bank', false) : 'Admin')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Paid by:')?></td><td class="a-r"><input type="text" class="a-input" name="data[paidby]" value="<?php echo strform($this->post('paidby', false) ? $this->post('paidby', false) : Session()->profile['name'])?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Paid to:')?></td><td class="a-r"><input type="text" class="a-input" name="data[paidto]" value="<?php echo strform($this->post('paidto', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Shipping method:')?></td><td class="a-r"><input type="text" class="a-input" name="data[shipping_method]" style="width:160px" value="<?php echo strform($this->post('shipping_method', false))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Message:')?></td><td class="a-r"><input type="text" class="a-input" name="data[msg]" value="<?php echo $this->post('msg')?>" /></td>
				</tr>
			</table>
			
			<?php endif;?>
		</div>
		
		<?php if ($this->id):?>
		<div id="a_items_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<div class="a-m<?php echo $this->ui['a-m']?>" colspan="4"><?php echo lang('$Order items:')?></div>
			<table class="a-form" cellspacing="0">
				<tr>
					<th width="1%"><?php echo lang('$ID')?></th>
					<th><?php echo lang('$Item')?></th>
					<th width="1%" title="<?php echo lang('$Seller')?>">S</th>
					<th width="15%"><?php echo lang('$Type')?></th>
					<th width="1%"><?php echo lang('$Quantity/Price')?></th>
					<th width="15%"><?php echo lang('$Total')?></th>
					<th>&nbsp;</th>
					
				</tr>
				<?php foreach ((array)$this->post('map', false) as $i => $rs):?>
				<tr>
					<td class="a-r"><?php echo $rs['id']?></td>
					<td class="a-r"><a href="javascript:;" onclick="S.A.L.global('link',{table:'<?php echo $rs['table']?>',itemid:'<?php echo $rs['itemid']?>'});"><?php echo $rs['title']?></a>
					<?php if ($rs['options']):?>
						<br /><?php echo $rs['options']?>
					<?php endif;?>
					</td>
					<td class="a-r"><a href="javascript:S.A.W.open('?<?php echo URL_KEY_ADMIN?>=users&id=<?php echo $rs['sellerid']?>')"><?php echo $rs['sellerid']?></a></td>
					<td class="a-r"><?php echo Conf()->g3('order_types',$rs['type'],0)?></td>
					<td class="a-r"><?php echo $rs['quantity']?> x <?php echo $rs['price']?></td>
					<td class="a-r"><?php echo number_format($rs['price'] * $rs['quantity'],2,'.','')?> <?php echo $rs['currency']?></td>
					<td class="a-l"><a href="javascript:;" onclick="if (confirm('Are you sure to remove this item?')) S.A.L.json('?<?php echo URL_KEY_ADMIN?>=<?php echo $this->table?>', {get:'action',a:'del_map',id: '<?php echo $rs['id']?>', oid: '<?php echo $this->id?>'}, true)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/edit-delete.png" /></a></td>
				</tr>
				<?php endforeach;?>
			</table>
			<div class="a-m<?php echo $this->ui['a-m']?>" colspan="4"><?php echo lang('$Add more:')?></div>
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l"><?php echo lang('$Product table:')?></td>
					<td colspan="5" class="a-r">
						<select name="data[products][table]" id="a-product_table_<?php echo $this->name_id?>" class="a-select" style="width:20%;"><?php echo Html::buildOptions($this->params['table'],$this->params['tables'])?></select>
						<?php echo lang('$Search field:')?>
						<select id="a-product_column_<?php echo $this->name_id?>" class="a-select" style="width:20%;"><?php echo Html::buildOptions($this->params['column'],$this->params['columns'])?></select>
					</td>
				</tr>
				<tr>
					<th><?php echo lang('$Search')?></th>
					<th><?php echo lang('$Product name')?></th>
					<th><?php echo lang('$Price')?></th>
					<th><?php echo lang('$Quantity')?></th>
					<th><?php echo lang('$Discount')?></th>
					<th>&nbsp;</th>
				</tr>
				<tbody id="a-products_<?php echo $this->name_id?>">
				
				</tbody>
			</table>
		</div>
		<?php endif;?>
		<div id="a_buyer_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php if (!$this->id):?>
				<tr>
					<td class="a-l" width="20%"><?php echo lang('$Find:')?></td><td class="a-r" width="80%"> <input type="text" style="width:250px" class="a-input" id="a-username_<?php echo $this->name_id?>" /><input type="hidden" name="data[userid]" id="a-userid_<?php echo $this->name_id?>" value="<?php echo $this->post('userid')?>" /></td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="a-l" width="20%"><?php echo lang('$First name:')?></td><td class="a-r" width="80%"> <input type="text" style="width:180px" class="a-input" name="data[firstname]" id="a-firstname_<?php echo $this->name_id?>" value="<?php echo $this->post('firstname')?>" /></td>
				</tr>
				<tr>
					<td class="a-l" width="20%"><?php echo lang('$Last name:')?></td><td class="a-r" width="80%"><input type="text" style="width:180px" class="a-input" name="data[lastname]" id="a-lastname_<?php echo $this->name_id?>" value="<?php echo $this->post('lastname')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Company:')?></td><td class="a-r"><input type="text" style="width:180px" class="a-input" name="data[company]" id="a-company_<?php echo $this->name_id?>" value="<?php echo $this->post('company')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Reg Nr:')?></td><td class="a-r"><input type="text" style="width:110px" class="a-input" name="data[reg_nr]" id="a-reg_nr_<?php echo $this->name_id?>" value="<?php echo $this->post('reg_nr')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$VAT Nr:')?></td><td class="a-r"><input type="text" style="width:110px" class="a-input" name="data[vat_nr]" id="a-vat_nr_<?php echo $this->name_id?>" value="<?php echo $this->post('vat_nr')?>" /> <label><input type="checkbox" name="data[export]" value="1"<?php echo (@$this->post['export']=='1' ? ' checked="checked"':'')?>><?php echo lang('$Export (exclude VAT)')?></label><input type="hidden" name="data[checkboxes][]" value="export"></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Email:')?></td><td class="a-r"><input type="text" style="width:250px" class="a-input" name="data[email]" id="a-email_<?php echo $this->name_id?>" value="<?php echo $this->post('email')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Cell phone:')?></td><td class="a-r"><input type="text" style="width:120px" class="a-input" name="data[cellphone]" id="a-cellphone_<?php echo $this->name_id?>" value="<?php echo $this->post('cellphone')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Home phone:')?></td><td class="a-r"><input type="text" style="width:120px" class="a-input" name="data[homephone]" id="a-homephone_<?php echo $this->name_id?>" value="<?php echo $this->post('homephone')?>" /></td>
				</tr>
				
				
				<tr>
					<td class="a-l"><?php echo lang('$Address:')?></td><td class="a-r"><input type="text" style="width:320px" class="a-input" name="data[address]" id="a-address_<?php echo $this->name_id?>" value="<?php echo $this->post('address')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$City:')?></td><td class="a-r"><input type="text" style="width:180px" class="a-input" name="data[city]" id="a-city_<?php echo $this->name_id?>" value="<?php echo $this->post('city')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Country:')?></td><td class="a-r"><input type="text" style="width:180px" class="a-input" name="data[country]" id="a-country_<?php echo $this->name_id?>" value="<?php echo $this->post('country')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Postcode:')?></td><td class="a-r"><input type="text" style="width:80px" class="a-input" name="data[zip]" id="a-zip_<?php echo $this->name_id?>" value="<?php echo $this->post('zip')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Info:')?></td><td class="a-r"><textarea style="width:90%;height:80px" class="a-textarea" name="data[info]"><?php echo $this->post('info')?></textarea></td>
				</tr>
			
			</table>
		</div>
		<div id="a_att_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload images:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-photo_<?php echo $this->name_id?>" style="width:80px;" size="2" />
						<?php if (!$this->id):?>
						<input type="hidden" name="data[main_photo]" id="a-main_photo_<?php echo $this->name_id?>" value="<?php echo $this->post('main_photo')?>" />
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Documents:')?></td>
					<td class="a-r">
						<div id="a-photos2_div_<?php echo $this->name_id?>"><div class="a-no_files">No documents were uploaded</div></div>
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Images:')?></td>
					<td class="a-r">
						<div id="a-photos_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No image files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
			</table>
		</div>
		<div id="a_notes_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-r" colspan="5" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:99%;height:453px"><?php echo $this->post('notes')?></textarea>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php $this->inc('bottom', array(
		'no_add'	=> true
	)); ?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>