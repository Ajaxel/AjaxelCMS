{strip}
<h2 style="margin-bottom:10px">{'Unsubscribe form'|lang}</h2>
{'Why would you go?'|l}
<br>
<form action="?{$URL}" class="form ajax_form" id="lost_pass" method="post">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=2}
		<tr>
			<th>{'Your Email to unsubscribe'|lang}: <span class="ast">*</span></th>
			<td><input type="text" class="textfield" name="{$smarty.const.URL_KEY_EMAIL}" value="{$smarty.post[$smarty.const.URL_KEY_EMAIL]}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.captcha} class="err"{/if}><a href="javascript:;" onclick="$(this).children().attr('src','/captcha.php?name=comment&n='+Math.random())"><img src="/captcha.php?name=comment&n={$time}" alt="Captcha" /></a><span class="ast">*</span>:</th>
			<td><input type="text" name="captcha" style="width:80px" class="textbox" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="b">
				<button type="submit" class="btn float_l">{'Unsubscribe'|lang}</button>
			</td>
		</tr>
	</table>
	<input type="hidden" name="unsubscribe" />
</form>
{/strip}