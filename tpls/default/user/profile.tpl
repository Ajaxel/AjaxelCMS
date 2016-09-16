{strip}
<h1>{'Your profile'|lang}</h1>
<div>
<form class="form ajax_form" method="post" action="?{$URL}" id="register_form">
	<table cellspacing="0"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr>
			<th{if $form_errors.email} class="err"{/if}>{'E-mail'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="email" class="textbox" value="{$smarty.post.email|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.state} class="err"{/if}>{'Country'|lang}<span class="ast">*</span>:</th>
			<td>
			<select class="selectbox" name="profile[country]" id="find_countries" onchange="S.G.populateGeo(this)">
				<option value="" style="color:#666"></option>
				{'Data'|Call:'getArray':'countries':$smarty.post.profile.country}
			</select></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.state} class="err"{/if}>{'State'|lang}<span class="ast">*</span>:</th>
			<td>
			<select class="selectbox" name="profile[state]" id="find_states" onchange="S.G.populateGeo(this)">
				<option value="" style="color:#666"></option>
				{'Data'|Call:'getArray':'states':$smarty.post.profile.state}
			</select></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.city} class="err"{/if}>{'City'|lang}<span class="ast">*</span>:</th>
			<td><select class="selectbox" name="profile[city]" id="find_cities" onchange="S.G.populateGeo(this)">
				<option value="" style="color:#666"></option>
				{'Data'|Call:'getArray':'cities':$smarty.post.profile.city}
			</select></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.district} class="err"{/if}>{'District'|lang}:</th>
			<td><select class="selectbox" name="profile[district]" id="find_districts">
				<option value="" style="color:#666"></option>
				{'Data'|Call:'getArray':'districts':$smarty.post.profile.district}
			</select></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.street} class="err"{/if}>{'Street'|lang}:</th>
			<td><input type="text" name="profile[street]" class="textbox" value="{$smarty.post.profile.street|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<th{if $form_errors.firstname} class="err"{/if}>{'Firstname'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="profile[firstname]" class="textbox" value="{$smarty.post.profile.firstname|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.lastname} class="err"{/if}>{'Lastname'|lang}<span class="ast">*</span>:</th>
			<td><input type="text" name="profile[lastname]" class="textbox" value="{$smarty.post.profile.lastname|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<th{if $form_errors.phone} class="err"{/if}>{'Phone number'|lang}:</th>
			<td><input type="text" name="profile[phone]" class="textbox" value="{$smarty.post.profile.phone|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<th{if $form_errors.phone} class="err"{/if}>{'Skype'|lang}:</th>
			<td><input type="text" name="profile[skype]" class="textbox" value="{$smarty.post.profile.skype|strform}" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>

		<tr>
			<th{if $form_errors.descr} class="err"{/if}>{'About you'|lang}<span class="ast">*</span>:</th>
			<td colspan="3">
				<table cellspacing="0" class="flags">
				{foreach from='Site'|Call:'getLanguages' key=l item=a}
				<tr>
					<td><img src="/tpls/img/flags/24/{$l}.png" /></td>
					<td><textarea class="textbox" name="profile[about][{$l}]" style="width:500px;height:70px;">{$smarty.post.profile.about[$l]|strform}</textarea></td>
				</tr>
				{/foreach}
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4" class="b">
				<button type="submit" class="reg_button bg">{'Update profile'|lang}</button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="{$smarty.const.URL_KEY_REGISTER}" />
</form>
</div>
{/strip}