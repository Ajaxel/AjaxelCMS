{strip}
<div class="post">
<h1>{'New user registration'|lang}</h1>
<form class="form ajax_form" method="post" action="?{$URL}" id="register_form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr class="bb">
			<th{if $form_errors.login} class="err"{/if}>{'Username'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="login" class="textbox" value="{$smarty.post.login|strform}" /></td>
		</tr>
		<tr>
			<th{if $form_errors.password} class="err"{/if}>{'Password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="password" class="textbox"  value="{$smarty.post.password|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.re_password} class="err"{/if}>{'Reenter password'|lang}<span class="ast">*</span>:</th>
			<td><input type="password" name="re_password" class="textbox" value="{$smarty.post.re_password|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.re_password} class="err"{/if}>{'Location'|lang}<span class="ast">*</span>:</th>
			<td>
			<select class="selectbox" name="profile[country]" id="find_countries" onchange="S.G.populateGeo(this)">
				<option value="" style="color:#666">{'_Country'|lang}</option>
				{'Data'|Call:'getArray':'countries':$smarty.post.profile.country}
			</select>
			<select class="selectbox" name="profile[state]" id="find_states" onchange="S.G.populateGeo(this)">
				<option value="" style="color:#666">{'_State'|lang}</option>
				{'Data'|Call:'getArray':'states':$smarty.post.profile.state}
			</select>
			<select class="selectbox" name="profile[city]" id="find_cities">
				<option value="" style="color:#666">{'_City'|lang}</option>
				{'Data'|Call:'getArray':'cities':$smarty.post.profile.city}
			</select>
			</td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.email} class="err"{/if}>{'E-mail'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="email" class="textbox" value="{$smarty.post.email|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.firstname} class="err"{/if}>{'Firstname'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="profile[firstname]" class="textbox" value="{$smarty.post.profile.firstname|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.lastname} class="err"{/if}>{'Lastname'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="profile[lastname]" class="textbox" value="{$smarty.post.profile.lastname|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.phone} class="err"{/if}>{'Phone number'|lang}:</th>
			<td><input type="text" name="profile[phone]" class="textbox" value="{$smarty.post.profile.phone|strform}" /></td>
		</tr>
		<tr class="bb">
			<th{if $form_errors.captcha} class="err"{/if}>{'Security code'|lang}<span class="ast">*</span>:</th>
			<td><dt><a href="javascript:;" onclick="$(this).children().attr('src','/captcha.php?name=comment&n='+Math.random())"><img src="/captcha.php?name=comment&n={$time}" alt="Captcha" /></a></dt><dd style="padding-top:8px;"><input type="text" name="captcha" style="width:80px" class="textbox" /></dd></td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td {if $form_errors.agree} class="err"{/if}>
				<label><input type="checkbox" name="agree"{if $smarty.post.agree} checked="checked"{/if} value="1" id="agree" /> {'I have read and agreed with %1site terms%2'|lang:'<a href="?terms" target="_blank">':'</a>'}</label>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="b">
				<button type="submit" class="reg_button">{'Register'|lang}</button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="{$smarty.const.URL_KEY_REGISTER}" />
</form>
<ul class="ul">
	<li><a href="?user&amp;login" class="ajax_link">{'Authorization'|lang}</a></li>
	<li><a href="?user&amp;lostpass" class="ajax_link">{'Password reminder'|lang}</a></li>
</ul>
</div>
{/strip}