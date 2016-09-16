<div class="post">
<h2>{'Contact us'|lang}</h2>
<div class="content">
	<div class="left">
	<form action="?contact" class="ajax_form" id="register" method="post">
	<table width="100%" cellpadding="0" cellspacing="0" class="form">
		{include file='includes/form_errors.tpl' table=2}
		<tr>
			<th class="l">{'Name'|lang}: <span style="color:#090;">*</span></th>
			<td><input type="text" class="textfield" name="contact[name]" value="{$smarty.post.contact.name|strform}" /></td>
		</tr>
		<tr>
			<th class="l">{'E-mail'|lang}: <span style="color:#090;">*</span></th>
			<td><input type="text" class="textfield" name="contact[email]" value="{$smarty.post.contact.email|strform}" /></td>
		</tr>
		<tr>
			<th class="l">{'Phone'|lang}:</th>
			<td><input type="text" class="textfield" name="contact[phone]" value="{$smarty.post.contact.phone|strform}" /></td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea name="contact[text]" id="s-text" style="width:99%;height:130px;">{$smarty.post.contact.text|strform}</textarea>
			</td>
		</tr>
		<tr>
			<td class="b" colspan="2">
				<button type="submit">{'Send'|lang}</button>
			</td>
		</tr>
	</table>
	<input type="hidden" name="{$smarty.const.URL_KEY_LOSTPASS}" />
	</form>
	</div>
	<div class="right center">

	</div>
</div>
</div>