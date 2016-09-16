{strip}
<h1>{'Password reset form'|lang}</h1>
<div>
<form class="form ajax_form" method="post" action="?{$URL}" id="form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr class="bb">
			<th{if $form_errors.login} class="err"{/if}>{'Enter your new password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="{$smarty.const.URL_KEY_PASSWORD}" class="textbox" style="width:120px" value="" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.login} class="err"{/if}>{'Enter your password again'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="re_{$smarty.const.URL_KEY_PASSWORD}" class="textbox" style="width:120px" value="" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" class="b">
				<button type="submit" class="reg_button bg">{'Save password'|lang}</button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="{$smarty.const.URL_KEY_LOSTPASS}" />
</form>
</div>
{/strip}