{strip}
<h1>{'Password reminder'|lang}</h1>
<div>
<form class="form ajax_form" method="post" action="?{$URL}" id="form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr class="bb">
			<th{if $form_errors.login} class="err"{/if}>{'Username or E-mail'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="{$smarty.const.URL_KEY_EMAIL_LOGIN}" class="textbox" value="{$smarty.post[$smarty.const.URL_KEY_EMAIL_LOGIN]|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.captcha} class="err"{/if}>{'Captcha code'|lang}<span class="ast">*</span>:</th>
			<td colspan="3"><dt><a href="javascript:;" onclick="$(this).children().attr('src','/captcha.php?name=comment&n='+Math.random())"><img src="/captcha.php?name=comment&n={$time}" alt="Captcha" /></a></dt><dd style="padding-top:4px;"><input type="text" name="captcha" style="width:80px" class="textbox" /></dd></td>
		</tr>
		<tr>
			<td colspan="4" class="b">
				<button type="submit" class="reg_button bg">{'Retrieve password'|lang}</button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="{$smarty.const.URL_KEY_LOSTPASS}" />
</form>
</div>
<ul class="ul">
	<li><a href="?user&amp;login" class="ajax_link">{'Authorization'|lang}</a></li>
	<li><a href="?user&amp;register" class="ajax_link">{'Register new user'|lang}</a></li>
</ul>
{/strip}