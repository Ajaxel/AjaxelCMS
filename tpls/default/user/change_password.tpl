{strip}
<h1>{'Change your password'|lang}</h1>

<form class="form ajax_form" method="post" action="?{$URL}" id="login_form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr>
			<th{if $form_errors.password} class="err"{/if}>{'New Password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="password" class="textbox" style="width:120px" value="{$smarty.post.password|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.re_password} class="err"{/if}>{'Reenter new password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="re_password" class="textbox" style="width:120px" value="{$smarty.post.re_password|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.cur_password} class="err"{/if}>{'Current password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="cur_password" class="textbox" style="width:100px" value="{$smarty.post.cur_password|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4" class="b">
				<button type="submit" class="reg_button">{'Save password'|lang}<span class="x">{'Save password'|lang}</span></button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="submitted" value="1" />
</form>

{/strip}