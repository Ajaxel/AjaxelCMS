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
* @file       tpls/admin/users_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
Data::world_find($this->post['profile']);
if ($this->id) $total_money_rows = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'users_transfers t WHERE userid='.$this->id.' AND account=\'self\'');


$title = lang('$'.($this->id ? 'Edit':'Add new').' user:').' '.($this->id?'(ID:'.$this->id.') ':'');
$this->title = $title.$this->post('login', false);
$this->width = 750;
$tab_height = 460;
?><script type="text/javascript">
<?php echo Index::CDA?>
var window_<?php echo $this->name_id?> = {
	uploader:0
	,load:function() {
		<?php echo $this->inc('js_load', array('win'=>true,'multi'=>true))?>
		$('#a-w-title_<?php echo $this->name_id?>').keyup(function(){
			$('#window_<?php echo $this->name_id?> .a-window-title').html('<?php echo $title?>'+this.value);
		});
		S.A.FU.init(300);
		S.A.W.uploadify('<?php echo $this->name_id?>','photo','<?php echo File::uploadifyExt(array('jpeg','jpg','gif','png'))?>','Image files');
		<?php /*S.A.W.uploadify('<?php echo $this->name_id?>','video','<?php echo File::uploadifyExt(array('asf','avi','divx','dv','mov','mpg','mpeg','mp4','mpv','ogm','qt','rm','vob','wmv', 'm4v','swf','flv','aac','ac3','aif','aiff','mp1','mp2','mp3','m3a','m4a','m4b','ogg','ram','wav','wma'))?>','Video / Flash files'); */?>
		S.A.W.tabs('<?php echo $this->name_id?>');
		var files = <?php echo $this->json_array['files']?>;
		this.setImages(files,'photo');
		<?php /*this.setImages(files,'video');*/?>
	}
	,setImages:function(files,name){
		var html = '<ul id="a-'+name+'s_<?php echo $this->name_id?>">';
		var j=0;
		if (files.length) {
			switch (name) {
				case 'photo':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t=='image') {
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+'" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<div class="a-p"><img src="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,'th3',true)+'" class="{w:'+a.w+',h:'+a.h+',p:\'[/th1/]\'}" /></div></li>';
							/*
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',$(this).parent().parent().find(\'a.show-photo\').children(0).attr(\'src\'), false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a><br />';
							html += '<a href="javascript:;" onclick="S.A.W.setMainPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/dialog-ok.png" /></a></div></li>';
							*/
							j++;
						}
					}
				break;
				<?php /*
				case 'video':
					for (i=0;i<files.length;i++) {
						a=files[i];
						if (a.t=='video' || a.t=='audio' || a.t=='flash') {
							html += '<li class="a-photo'+(a.m?' a-main_photo':'')+'" id="file_<?php echo $this->name_id?>_'+(i+1)+'">';
							html +='<a href="'+S.A.W.filePath('<?php echo $this->name_id?>',a.f,false,true)+'" target="_blank"><img src="<?php echo FTP_EXT?>tpls/img/media-playback-start.png" /></a><br />'+a.f;
							html += '<div class="a-photo_del"><a href="javascript:;" onclick="S.A.W.delPhoto(\'<?php echo $this->name_id?>\',\''+a.f+'\', false, this)"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/mail-delete.png" /></a></div></li>';
							j++;
						}
					}
				break;
				*/?>
			}
		}
		html += '</ul>';
		if (j) $('#a-'+name+'s_div_<?php echo $this->name_id?>').html(html);
		S.A.W.selectable_images('<?php echo $this->name_id?>',name);
	}
	,changeBalance:function(r) {
		S.A.L.json('?<?php echo URL_KEY_ADMIN?>=users',{
			get:'action',
			a:'money',
			todo: (r ? 'add' : 'remove'),
			userid: '<?php echo $this->id?>',
			sum: $('#a-sum_<?php echo $this->name_id?>').val(),
			currency: $('#a-currency_<?php echo $this->name_id?>').val(),
			title: $('#a-moneydescr_<?php echo $this->name_id?>').val()
		}, function(data) {
			$('#a-sum_<?php echo $this->name_id?>').val('');
			$('#a-moneydescr_<?php echo $this->name_id?>').val('');
			$('#a-total_<?php echo $this->name_id?>, #a-mymoney_<?php echo $this->name_id?>').html(data.total);
			$('<tr>').html(data.html).prependTo($('#a-balances_<?php echo $this->name_id?>'));
			
		});
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
			<li><a href="#a_main_<?php echo $this->name_id?>"><?php echo lang('$User details')?></a></li>
			<li><a href="#a_images_<?php echo $this->name_id?>"><?php echo lang('$Photos')?></a></li>
			<!--<li><a href="#a_videos_<?php echo $this->name_id?>"><?php echo lang('$Videos')?></a></li>-->
			<li><a href="#a_bio_<?php echo $this->name_id?>"><?php echo lang('$About')?></a></li>
			<!--<li><a href="#a_orders_<?php echo $this->name_id?>"><?php echo lang('$Orders')?> (<?php echo count($this->post('orders', false))?>)</a></li>-->
			<!--<li><a href="#a_mail_<?php echo $this->name_id?>"><?php echo lang('$Letter')?></a></li>-->
			<li><a href="#a_money_<?php echo $this->name_id?>"><?php echo lang('$Money')?> (<?php echo $total_money_rows?>)</a></li>
			<li><a href="#a_admin_<?php echo $this->name_id?>"><?php echo lang('$Admin')?></a></li>
		</ul>
		<div id="a_main_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="4"><?php echo lang('$Login details:')?></td>
				</tr>		
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Login name:')?></td><td class="a-r" width="30%"><?php if ($this->id):?><?php echo $this->post('login')?><input type="hidden" name="data[login]" value="<?php echo $this->post('login')?>" /><?php else: ?><input type="text" class="a-input" id="a-w-title_<?php echo $this->name_id?>" style="width:98%" name="data[login]" value="<?php echo $this->post('login')?>" /><?php endif;?></td>
					<td class="a-l" width="15%"><?php echo lang('$E-mail:')?></td><td class="a-r" width="30%"><input type="email" class="a-input" id="a-w-email_<?php echo $this->name_id?>" style="width:98%" name="data[email]" value="<?php echo $this->post('email')?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$New password:')?></td><td class="a-r"><input type="password" class="a-input" style="width:98%" name="data[password]" value="" /></td>
					<td class="a-l"><?php echo lang('$Re-enter password:')?></td><td class="a-r"><input type="password" class="a-input" style="width:98%" name="data[re-password]" value="" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$User group:')?></td><td class="a-r"><select class="a-select" name="data[groupid]" style="width:98%"><?php echo Data::getArray('user_groups', $this->post('groupid', false))?></select></td>
					<td class="a-l"><?php echo lang('$User class:')?></td><td class="a-r"><select class="a-select" name="data[classid]" style="width:98%"><?php echo Data::getArray('user_classes', $this->post('classid', false))?></select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$User status:')?></td><td class="a-r"><select class="a-select" name="data[active]" style="width:98%"><?php echo Data::getArray('user_statuses', $this->post('active', false))?></select></td>
					<td class="a-l"><?php echo lang('$Money:')?></td><td class="a-r" id="a-mymoney_<?php echo $this->name_id?>"><?php echo strform($this->post('profile','money'))?> <?php echo ($this->post('profile','currency') ? $this->post('profile','currency') : DEFAULT_CURRENCY)?></td>
				</tr>
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="4"><?php echo lang('$Profile details:')?></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$First name:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][firstname]" value="<?php echo strform($this->post('profile','firstname'))?>" /></td>
					<td class="a-l"><?php echo lang('$Last name:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][lastname]" value="<?php echo strform($this->post('profile','lastname'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Gender:')?></td><td class="a-r"><select class="a-select" name="data[profile][gender]" style="width:auto"><?php echo Data::getArray('gender', $this->post('profile','gender'))?></select></td>
					<td class="a-l"><?php echo lang('$Date of birth:')?></td><td class="a-r"><?php echo Data::getArray('dob_admin',$this->post('profile','dob'))?></td>
				</tr>
				<tr>
					<td class="a-m<?php echo $this->ui['a-m']?>" colspan="4"><?php echo lang('$Location / Contacts:')?></td>
				</tr>
				<?php
				if (!in_array('geo_countries',DB::tables())):
				?>
				<tr>
					<td class="a-l"><?php echo lang('$Country:')?></td><td class="a-r"><select class="a-select" name="data[profile][country]" style="width:98%"><option value=""></option><?php echo Data::getArray('countries', $this->post('profile','country'))?></select></td>
					<td class="a-l"><?php echo lang('$State:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][state]" value="<?php echo strform($this->post('profile','state'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$City:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][city]" value="<?php echo strform($this->post('profile','city'))?>" /></td>
					<td class="a-l"><?php echo lang('$District:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][district]" value="<?php echo strform($this->post('profile','district'))?>" /></td>
				</tr>
				<?php else:?>
				<tr>
					<td class="a-l"><?php echo lang('$Country:')?></td><td class="a-r"><select class="a-select" name="data[profile][country]" style="width:98%" id="find_countries<?php echo $this->name_id?>" onchange="S.G.populateGeo(this,'all','<?php echo $this->name_id?>')"><option value=""></option><?php echo Data::getArray('countries', $this->post('profile','country'))?></select></td>
					<td class="a-l"><?php echo lang('$State:')?></td><td class="a-r"><select class="a-select" name="data[profile][state]" style="width:98%" id="find_states<?php echo $this->name_id?>" onchange="S.G.populateGeo(this,'all','<?php echo $this->name_id?>')"><option value=""></option><?php echo Data::getArray('states', $this->post('profile','state'))?></select></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$City:')?></td><td class="a-r"><select class="a-select" name="data[profile][city]" style="width:98%" id="find_cities<?php echo $this->name_id?>" onchange="S.G.populateGeo(this,'all','<?php echo $this->name_id?>')"><option value=""></option><?php echo Data::getArray('cities', $this->post('profile','city'))?></select></td>
					<td class="a-l"><?php echo lang('$District:')?></td><td class="a-r"><select class="a-select" name="data[profile][district]" style="width:98%" id="find_countries<?php echo $this->name_id?>"><option value=""></option><?php echo Data::getArray('districts', $this->post('profile','district'))?></select></td>
				</tr>
				<?php endif;?>
				<tr>
					<td class="a-l"><?php echo lang('$Street:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][street]" value="<?php echo strform($this->post('profile','street'))?>" /></td>
					<td class="a-l"><?php echo lang('$Post code:')?></td><td class="a-r"><input type="text" class="a-input" style="width:59%" name="data[profile][zip]" value="<?php echo strform($this->post('profile','zip'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Company:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][company]" value="<?php echo strform($this->post('profile','company'))?>" /></td>
					<td class="a-l"><?php echo lang('$Website:')?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][www]" value="<?php echo strform($this->post('profile','www'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Reg nr.:')?></td><td class="a-r"><input type="text" class="a-input" style="width:58%" name="data[profile][reg_nr]" value="<?php echo strform($this->post('profile','reg_nr'))?>" /></td>
					<td class="a-l"><?php echo lang('$VAT nr.:')?></td><td class="a-r"><input type="text" class="a-input" style="width:58%" name="data[profile][vat_nr]" value="<?php echo strform($this->post('profile','vat_nr'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Phone:')?></td><td class="a-r"><input type="text" class="a-input" style="width:78%" name="data[profile][phone]" value="<?php echo strform($this->post('profile','phone'))?>" /></td>
					<td class="a-l"><?php echo lang('$Home phone:')?></td><td class="a-r"><input type="text" class="a-input" style="width:78%" name="data[profile][fax]" value="<?php echo strform($this->post('profile','fax'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php if ($this->post('profile','skype')) {?><a href="callto:<?php echo strform($this->post('profile','skype'))?>">Skype</a><?php } else { ?>Skype<? }?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][skype]" value="<?php echo strform($this->post('profile','skype'))?>" /></td>
					<td class="a-l"><?php if ($this->post('profile','msn')) {?><a href="msnim:chat?contact=<?php echo strform($this->post('profile','msn'))?>">MSN</a><?php } else { ?>MSN<? }?></td><td class="a-r"><input type="text" class="a-input" style="width:98%" name="data[profile][msn]" value="<?php echo strform($this->post('profile','msn'))?>" /></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Signature:')?></td>
					<td class="a-r" colspan="3"><textarea type="text" class="a-textarea" style="width:99%;height:55px" name="data[profile][signature]"><?php echo strform($this->post('profile','signature'))?></textarea></td>
				</tr>
			</table>
		</div>
		<div id="a_images_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload images:')?></td><td class="a-r" colspan="5" width="85%">
						<div class="a-file_btn"><input type="file" class="a-file" id="a-photo_<?php echo $this->name_id?>" /></div>
						<?php if (!$this->id):?>
						<input type="hidden" name="data[main_photo]" id="a-main_photo_<?php echo $this->name_id?>" value="<?php echo $this->post('main_photo')?>" />
						<?php endif;?>
					</td>
				</tr>
				<tr>
					<td class="a-r" colspan="2">
						<div id="a-photos_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No image files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
			</table>
		</div>
		<?php /*
		<div id="a_videos_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Upload video files:')?></td><td class="a-r" colspan="5" width="85%">
						<input type="file" class="a-file" id="a-video_<?php echo $this->name_id?>" style="width:80px;" size="2" />
					</td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Video files:')?></td>
					<td class="a-r">
						<div id="a-videos_div_<?php echo $this->name_id?>"><div class="a-no_files"><?php echo lang('$No video files uploaded here, click BROWSE button')?></div></div>
					</td>
				</tr>
			</table>
		</div>
		*/?>
		<div id="a_bio_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$About:')?></td>
					<td class="a-r"><textarea type="text" class="a-textarea" style="width:98%;height:55px" name="data[profile][about]"><?php echo strform($this->post('profile','about'))?></textarea></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Interests:')?></td>
					<td class="a-r"><textarea type="text" class="a-textarea" style="width:98%;height:55px" name="data[profile][interests]"><?php echo strform($this->post('profile','interests'))?></textarea></td>
				</tr>
				<tr>
					<td class="a-l"><?php echo lang('$Relation:')?></td>
					<td class="a-r"><textarea type="text" class="a-textarea" style="width:98%;height:55px" name="data[profile][relation]"><?php echo strform($this->post('profile','relation'))?></textarea></td>
				</tr>
			</table>
		</div>
		<?php /*
		<div id="a_orders_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<?php 
				if ($this->post('orders', false)):
				?>
				<tr>
					<th width="1%"><?php echo lang('$ID')?></th>
					<th width="5%"><?php echo lang('$Status')?></th>
					<th width="5%"><?php echo lang('$Ordered')?></th>
					<th><?php echo lang('$Item')?></th>
					<th width="15%"><?php echo lang('$Type')?></th>
					<th width="1%"><?php echo lang('$Quantity')?></th>
					<th width="15%"><?php echo lang('$Price')?></th>
					<th width="1%" title="<?php echo lang('$Seller')?>">S</th>
				</tr>
				<?php
				foreach ($this->post('orders', false) as $i => $rs):?>
				<tr>
					<td class="a-r"><a href="javascript:S.A.W.open('?<?php echo URL_KEY_ADMIN?>=orders2&id=<?php echo $rs['id']?>')"><?php echo $rs['id']?></a></td>
					<td class="a-r" nowrap><?php echo Conf()->g3('order_statuses',$rs['status'],0)?></td>
					<td class="a-r"><?php echo date('d.m.Y',$rs['ordered'])?></td>
					<td class="a-r"><a href="javascript:;" onclick="S.A.L.global('link',{table:'<?php echo $rs['table']?>',itemid:'<?php echo $rs['itemid']?>'});"><?php echo $rs['title']?></a>
					<?php if ($rs['options']):?>
						<br /><?php echo $rs['options']?>
					<?php endif;?>
					</td>
					<td class="a-r"><?php echo Conf()->g3('order_types',$rs['type'],0)?></td>
					<td class="a-r"><?php echo $rs['quantity']?></td>
					<td class="a-r"><?php echo $rs['price']?> <?php echo $rs['currency']?></td>
					<td class="a-r"><a href="javascript:S.A.W.open('?<?php echo URL_KEY_ADMIN?>=users&id=<?php echo $rs['sellerid']?>')"><?php echo $rs['sellerid']?></a></td>
				</tr>
				<?php endforeach;
				else:?>
				<tr><td><div class="a-not_found"><?php echo lang('$No orders this user have made')?></div></td></tr>
				<?php
				endif;?>
			</table>
		</div>
		<div id="a_mail_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l" width="15%"><?php echo lang('$Subject:')?></td><td class="a-r" width="85%"><input type="text" class="a-input" style="width:99%" name="data[profile][options][subject]" value="<?php echo strform(@$this->post['profile']['options']['subject'])?>" /></td>
				</tr>
				<tr>
					<td class="a-r" colspan="2">
						<textarea type="text" class="a-textarea" name="data[profile][options][message]" style="width:99%;height:<?php echo $tab_height-62;?>px"><?php echo strform(@$this->post['profile']['options']['message'])?></textarea>
					</td>
				</tr>
				<?php if ($this->id):?>
				<tr>
					<td class="a-l"><?php echo lang('$Re-send letter?')?></td>
					<td class="a-r"><input type="checkbox" name="data[resend]" value="Y" id="a-resend_<?php echo $this->name_id?>" /><label for="a-resend_<?php echo $this->name_id?>"> <?php echo lang('$Yes')?></label></td>
				</tr>
				<?php endif;?>
			</table>
		</div>
		*/?>
		<div id="a_money_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<?php
			if ($this->post['id']):?>
			<table cellspacing="0" cellpadding="0" class="a-form">
				<tr><td class="a-l"><?php echo lang('$Sum:')?></td><td class="a-r" colspan="<?php echo ($this->has_balance?'3':'2')?>"><input type="text" style="width:25%" id="a-sum_<?php echo $this->name_id?>" value="" class="a-input" /> <select id="a-currency_<?php echo $this->name_id?>" class="a-select"><?php echo Html::buildOptions(($this->post('profile','currency') ? $this->post('profile','currency') : DEFAULT_CURRENCY),array_label($this->currencies, 1));?></select> <?php echo ($this->post('profile','currency') ? lang('$Preffered currency:').' <b>'.$this->post('profile','currency').'</b>':'')?></td></tr>
				<tr><td class="a-l"><?php echo lang('$Description:')?></td><td class="a-r" colspan="<?php echo ($this->has_balance?'3':'2')?>"><input type="text" style="width:95%" id="a-moneydescr_<?php echo $this->name_id?>" value="" class="a-input" /></td></tr>
				<tr><td class="a-l">&nbsp;</td><td class="a-r" colspan="<?php echo ($this->has_balance?'3':'2')?>"><?php $this->inc('button',array('click'=>'window_'.$this->name_id.'.changeBalance(true);','text'=>lang('$Add'))); ?> <?php $this->inc('button',array('click'=>'window_'.$this->name_id.'.changeBalance(false);','text'=>lang('$Withdraw'))); ?></td></tr>
				<tr><td class="a-l"><?php echo lang('$Total:')?></td><td class="a-l">&nbsp;</td><td class="a-l" colspan="2" nowrap id="a-total_<?php echo $this->name_id?>"><?php echo number_format($this->count_money($this->id, 'self', ($this->post('profile','currency') ? $this->post('profile','currency') : DEFAULT_CURRENCY)),2,',',' ').' '.($this->post('profile','currency') ? $this->post('profile','currency') : DEFAULT_CURRENCY);?></td><?php echo ($this->has_balance?'<td>&nbsp;</td>':'')?></tr>
				<tbody id="a-balances_<?php echo $this->name_id?>">
				<?php
				
				$qry = DB::qry('SELECT id, price, currency, title, added'.($this->has_balance?', balance':'').' FROM '.DB_PREFIX.'users_transfers t WHERE userid='.$this->id.' ORDER BY id DESC',0,50);
				while ($rs = DB::fetch($qry)) {
					include FTP_DIR_TPLS.'admin/inc/users_window_money.php';
				}
				DB::free($qry);
				?>
				</tbody>
			</table>
			<?php endif;?>
		</div>
		<div id="a_admin_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			<table class="a-form" cellspacing="0">
				<tr>
					<td class="a-l"><?php echo lang('$Notes:')?></td>
					<td class="a-r" width="85%">
						<textarea type="text" class="a-textarea" name="data[notes]" style="width:98%;height:225px"><?php echo strform($this->post('notes', false))?></textarea>
					</td>
				</tr>
				<?php if ($this->post('id', false)):?>
				<tr>
					<td class="a-l"><?php echo lang('$Actions:')?></td>
					<td class="a-r">
						<?php if ($this->post('id', false)!=SUPER_ADMIN && $this->Index->Session->UserID!=$this->post('id', false)):?>
						<button type="button" class="a-button a-button_s" onclick="if(confirm('<?php echo lang('Are you sure to delete this user:')?> <?php echo $this->post('login')?>?')){S.A.L.del({id:<?php echo $this->post('id')?>, active:<?php echo $this->post('active')?>, title: '<?php echo $this->post('login')?>'}, this, false, function(){S.A.W.close('<?=$this->name_id?>')})}"><?php echo lang('$Delete')?></button> 
						<?php endif;?>
						<button type="button" class="a-button a-button_s" onclick="alert('? not done=(')"><?php echo lang('$Send confirmation email')?></button>
						<button type="button" class="a-button a-button_s" onclick="window.open('/?userid_admin=<?php echo $this->id?>')"><?php echo lang('$Login as %1 to public site',$this->post['login'])?></button>
					</td>
				</tr>
				<?php endif;?>
			</table>
		</div>
	</div>
	<?php $url = '?'.URL::make(array(URL_KEY_ADMIN=>$this->name,'id'=>$this->id,'l'=>$this->lang)); ?>
	<div class="a-window-bottom ui-dialog-buttonpane">
		<table width="100%"><tr>
		<?php if ($this->id):?>
		<td class="a-td1">
			<?php echo lang('$Logged:')?> <?php echo date('d M Y, H:i',$this->post('logged', false))?>; <?php echo lang('$Clicked:')?> <?php echo date('H:i',$this->post('last_click', false))?><br /><?php echo lang('$Registered:')?> <?php echo date('d M Y, H:i',$this->post('registered', false))?> <?php if ($this->post('ip', false)): echo lang('$IP:').' '.long2ip($this->post('ip', false)); endif;?>
		</td>
		<td class="a-td2">
			<?php
			if ($this->post('edited', false)) echo lang('$Edited:').' '.date('H:i / d.m.Y', $this->post('edited', false)).'<br />';
			if ($this->post('added', false)) echo lang('$Added:').' '.date('H:i / d.m.Y', $this->post('added', false));
			?>
		</td>
		<?php
		if ($this->post('username', false) && $this->post('userid', false)) {
			echo '<td class="a-td1"><img src="'.FTP_EXT.'tpls/img/oxygen/16x16/actions/user-female.png" title="',lang('$Author'),'" alt="',lang('$Author'),'" /> <a href="javascript:;" onclick="S.A.W.open(\'?',URL_KEY_ADMIN,'=users&id=',$this->post('userid', false),'\');">',strip_tags($this->post('username', false)),'</a></td>';	
		}
		?>
		<?php endif;?>
		<td class="a-td2">
			<table><tr><td>
			<?php if ($this->id):?>
				<button type="button" class="a-button a-button_s" onclick="S.A.W.save('<?php echo $url?>&a=add', this.form, this)"><?php echo lang('$Add another')?></button>
				</td><td>
				<button type="button" class="a-button a-button_b" onclick="S.A.W.save('<?php echo $url?>&a=save', this.form, this)"><table cellspacing="0" cellpadding="0"><tr><td><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/document-save.png" alt="" /></td><td><?php echo lang('$Save')?></td></tr></table></button>
			<?php else:?>
				<button type="button" class="a-button a-button_b" onclick="S.A.W.save('<?php echo $url?>&a=save', this.form, this)"><table cellspacing="0" cellpadding="0"><tr><td><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/actions/list-add.png" alt="" /></td><td><?php echo lang('$Add')?></td></tr></table></button>
			<?php endif;?>
			</td></tr></table>
		</td>
		</tr></table>
	</div>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>