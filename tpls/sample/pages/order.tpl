{strip}
<h2>{'Order our services'|lang}</h2>
{'[order-service]Greetings, I hope you have enjoyed with our works and willing to get something for your own purposes also. Just let me know if so, send detailed information about what you ant and wait for my reply.'|l}
<div class="hr"></div>
<script type="text/javascript">
$(function(){
	S.G.upload({
		name	: '{$upload.name}',
		id		: '{$upload.id}',
		hash	: '{$upload.hash}',
		file	: 'main_photo',
		queueID	: 'uploadQueue',
		multi	: true,
		auto	: true,
		buttonImg: '/{$smarty.const.HTTP_DIR_TPL}images/select-a-file2.png',
		width	: 133,
		height	: 46,
		fileExt	: '{$upload.ext}',
		fileDesc: '{$upload.desc}',
		start	: function(){
			$('#uploadQueue').fadeIn();
		},
		func	: function(response){
			if (response.substring(0,2)!='1/') {
				alert(response);
			} else {
				var to=$('#photos').find('li:last');
				if (to.length) {
					$('<li>').html(response.substring(2)).hide().insertBefore($('#photos').find('li:last')).fadeIn();
				} else {
					$('<li>').html(response.substring(2)).hide().appendTo($('#photos')).fadeIn();	
				}
				S.I.init();
			}
		}
	});
	$('#order-form input.datepicker').datepicker({
		dateFormat: 'dd.mm.yy',
		defaultDate: "+1w",
		numberOfMonths: 2,
		minDate: 0
	});
	$('#order-form textarea').autosize();
});
</script>
<table style="width:100%;" cellspacing="0"><tr valign="top">
<td style="border-right:1px solid #1E1C20;padding-right:20px">
<div class="content" style="padding-top:0;width:590px">
<form class="form ajax" method="post" action="?{$URL}" id="order-form" style="margin-left:0;padding-left:0">
	{if $smarty.post.data.id}
		<h4>{'_Edit your request'|lang}<p style="float:right;color:#fff;font-weight:bold;font-size:17px;margin-top:1px;margin-bottom:-15px;">ID: {$smarty.post.data.id}</p></h4>
		<div class="content">
		{'[request_is_sent]Your request has been sent successfuly, but you may update details or <a href="?buy&reset">click here</a> to send new request'|lang}
		</div>
	{else}
		<h4>{'Request for a project form'|lang}</h4>
	{/if}
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=2}
		<tr{if $form_errors.type} class="err"{/if}>
			<th><div style="width:190px">{'Project type'|lang}<span class="ast">*</span>:</div></th>
			<td><select class="selectbox" id="fld_type" name="data[type]" style="width:160px">
				<option value="" style="color:#666"></option>
				{Data::getArray('my:type',$smarty.post.data.type)}
			</select></td>
		</tr>

		<tr{if $form_errors.descr} class="err"{/if}>
			<th>{'About project'|lang}<span class="ast">*</span>:</th>
			<td><textarea class="textbox" name="data[descr]" style="width:348px;height:200px;margin-bottom:4px;max-height:350px">{$smarty.post.data.descr|strform}</textarea></td>
		</tr>
		<tr>
			<th>{'Design files, documents, technical tasks'|lang}:</th>
			<td>
				<div id="uploadQueue" style="width:375px;clear:both;margin-left:4px"></div>
				<div style="padding-left:3px;padding-top:4px;"><input type="file" name="data[file]" id="main_photo" /></div>
				<ul class="photos" id="photos" style="padding-top:4px;">
					{foreach from=$upload.files item=f}
						<li>{include file='includes/pic.tpl' f=$f}</li>
					{/foreach}
				</ul>
			</td>
		</tr>
		
		<tr>
			<th>{'Characteristic of typical clients, visitors of web-site'|lang}:</th>
			<td><textarea class="textbox" name="data[descr2]" style="width:348px;height:60px;margin-bottom:4px;max-height:250px">{$smarty.post.data.descr2|strform}</textarea></td>
		</tr>
		<tr>
			<th>{'Similar websites to your project, URL-links'|lang}:</th>
			<td><textarea class="textbox" name="data[descr3]" style="width:348px;height:60px;margin-bottom:4px;max-height:250px">{$smarty.post.data.descr3|strform}</textarea></td>
		</tr>
		<tr>
			<th>{'Extra works'|lang}:</th>
			<td>
				<table cellspacing="0" class="checkboxes">
					{assign var='arr' value='Data'|Call:'getArray':'my:programming'}
					{'Html'|Call:'buildRadios':'checkbox':'data[programming][]':$smarty.post.data.programming:$arr:2}
				</table>
			</td>
		</tr>
		<tr{if $form_errors.url} class="err"{/if}>
			<th>{'Your website domain'|lang}:</th>
			<td><input type="text" name="data[url]" style="width:200px" id="fld_url" class="textbox" value="{if $smarty.post.data.url}{$smarty.post.data.url|strform}{else}http://{/if}" /></td>
		</tr>
		<tr>
			<th>{'FTP details, MySQL or cPanel username with password'|lang}:</th>
			<td><textarea class="textbox" name="data[descr4]" style="width:348px;height:60px;margin-bottom:1px;max-height:250px">{$smarty.post.data.descr4|strform}</textarea>
			<br><small>{'We use it to upload results directly to hosting.'|lang}</small>
			</td>
		</tr>
		<tr{if $form_errors.budget} class="err"{/if}>
			<th>{'Budget'|lang}:</th>
			<td><input type="text" name="data[budget]" id="fld_budget" class="textbox" style="width:80px" value="{$smarty.post.data.budget|strform}" /> eur
			</td>
		</tr>
		<tr>
			<th>{'Deadline'|lang}:</th>
			<td><input type="text" name="data[dated]" id="fld_dated" readonly class="textbox datepicker" value="{if $smarty.post.data.dated}{$smarty.post.data.dated|strform}{/if}" />
			</td>
		</tr>
		<tr>
			<th>{'Company'|lang}:</th>
			<td><input type="text" name="data[company]" style="width:200px" id="fld_cpmpany" class="textbox" value="{$smarty.post.data.company|strform}" /></td>
		</tr>
		<tr{if $form_errors.email} class="err"{/if}>
			<th>{'Your E-mail'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="data[email]" style="width:200px" id="fld_email" class="textbox" value="{$smarty.post.data.email|strform}" /></td>
		</tr>
		<tr>
			<th>{'Phone or Skype'|lang}:</th>
			<td><input type="text" name="data[phone]" style="width:200px" id="fld_phone" class="textbox" value="{$smarty.post.data.phone|strform}" /></td>
		</tr>
		
		<tr>
			<th>{'How have you found us?'|lang}:</th>
			<td><select class="selectbox" id="fld_type" name="data[found]" style="width:160px">
				<option value="" style="color:#666"></option>
				{Data::getArray('my:found',$smarty.post.data.found)}
			</select></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="b">
				{if $smarty.post.data.id}
					<button type="submit" class="btn float_l" style="margin-left:3px">{'Update'|lang}</button>				
				{else}
					<button type="submit" id="order-btn" class="btn float_l" style="margin-left:3px">{'Send'|lang}</button>
				{/if}
			</td>
		</tr>

	</tbody></table>
	
	<input type="hidden" name="submitted" value="buy" />
</form>
</div>
</td>
<td style="padding-left:20px;border-left:1px solid #606060;font-size:14px">
	<h4>{'Information'|lang}</h4>
	{'@how_to_buy_text'|lang}
</td>

</tr></table>
{/strip}