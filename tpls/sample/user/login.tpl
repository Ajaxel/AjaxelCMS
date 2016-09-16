{strip}
<h2>{'User login'|lang}</h2>
<script>
$().ready(function(){
	if (!$('#login').val()) {
		$('#login').focus();
	} else {
		$('#password').focus();	
	}
	$('#login_form').submit(function(e) {
		e.preventDefault();
		S.G.json('?',this);
		return false;	
	});
});

</script>

<form class="form ajax" method="post" action="?{$URL}" id="login_form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr>
			<th{if $form_errors.login} class="err"{/if}><div style="width:170px">{'Username'|lang}<span class="ast">*</span>:</div></th>
			<td><input type="text" name="login" id="login" class="textbox" value="{$smarty.post.login|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.password} class="err"{/if}>{'Password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="password" id="password" class="textbox" value="{$smarty.post.password|strform}" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td class="b">
				<button type="submit" class="btn float_l">{'Login'|lang}</button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="jump" value="{if $smarty.get.jump}{$smarty.get.jump|strform}{elseif $jump}{$jump}{else}?{$URL}{/if}" />
</form>

<ul class="tmo_list">
	<li><a href="?user&amp;lostpass" class="ajax_link">{'Forgot password?'|lang}</a></li>
	<li><a href="?user&amp;register" class="ajax_link">{'Join us'|lang}</a></li>
</ul>

{/strip}