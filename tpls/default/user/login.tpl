{strip}
<div class="post">
<h1>{'User login'|lang}</h1>

<form class="form -ajax_form" method="post" action="?{$URL}" id="login_form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr>
			<th{if $form_errors.login} class="err"{/if}>{'Username'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="login" class="textbox" value="{$smarty.post.login|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.password} class="err"{/if}>{'Password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="password" class="textbox" value="{$smarty.post.password|strform}" /></td>
		</tr>
		<tr>
			<td class="b" colspan="2">
				<button type="submit" class="reg_button bg x">{'Login'|lang}<span class="x">{'Login'|lang}</span></button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="jump" value="{if $smarty.get.jump}{$smarty.get.jump|strform}{elseif $jump}{$jump}{else}?{$URL}{/if}" />
</form>

<ul class="ul">
	<li><a href="?user&amp;lostpass" class="ajax_link">{'Password reminder'|lang}</a></li>
	<li><a href="?user&amp;register" class="ajax_link">{'Register new user'|lang}</a></li>
</ul>
</div>
{/strip}