{strip}
<h2>{'Password reminder'|lang}</h2>
<form class="form ajax_form" method="post" action="?{$URL}" id="form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr class="bb">
			<th{if $form_errors.login} class="err"{/if}><div style="width:170px">{'Username or E-mail'|lang}<span class="ast">*</span>:</div></th>
			<td><input type="text" name="{$smarty.const.URL_KEY_EMAIL_LOGIN}" class="textbox" value="{$smarty.post[$smarty.const.URL_KEY_EMAIL_LOGIN]|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.captcha} class="err"{/if}><a href="javascript:;" onclick="$(this).children().attr('src','/captcha.php?name=comment&n='+Math.random())"><img src="/captcha.php?name=comment&n={$time}" alt="Captcha" /></a><span class="ast">*</span>:</th>
			<td><input type="text" name="captcha" style="width:80px" class="textbox" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="b">
				<button type="submit" class="btn float_l">{'Send me'|lang}</button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="{$smarty.const.URL_KEY_LOSTPASS}" />
</form>
<ul class="tmo_list">
	<li><a href="?user&amp;login" class="ajax_link">{'Login?'|lang}</a></li>
	<li><a href="?user&amp;register" class="ajax_link">{'Join us'|lang}</a></li>
</ul>
{/strip}