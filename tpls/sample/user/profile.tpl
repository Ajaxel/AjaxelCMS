{strip}

<form class="form ajax_form" method="post" action="?{$URL}" id="register_form">
{include file='includes/form_errors.tpl' table=false}
	<div class="col_w420">
		<h2>{'Your profile'|lang}</h2>
		<table cellspacing="0"><tbody>
			<tr>
				<th{if $form_errors.email} class="err"{/if}><div style="width:110px">{'E-mail'|lang}<span class="ast">*</span>:</div></th>
				<td>{$smarty.post.email|strform}</td>
			</tr>
			<tr>
				<th{if $form_errors.state} class="err"{/if}>{'Country'|lang}<span class="ast">*</span>:</th>
				<td>
				<select class="selectbox" name="profile[country]" id="find_countries" onchange="S.G.populateGeo(this)">
					<option value="" style="color:#666"></option>
					{'Data'|Call:'getArray':'countries':$smarty.post.profile.country}
				</select></td>
			</tr>
			<tr>
				<th{if $form_errors.state} class="err"{/if}>{'State'|lang}<span class="ast">*</span>:</th>
				<td>
				<select class="selectbox" name="profile[state]" id="find_states" onchange="S.G.populateGeo(this)">
					<option value="" style="color:#666"></option>
					{'Data'|Call:'getArray':'states':$smarty.post.profile.state}
				</select></td>
			</tr>
			<tr>
				<th{if $form_errors.city} class="err"{/if}>{'City'|lang}<span class="ast">*</span>:</th>
				<td><select class="selectbox" name="profile[city]" id="find_cities" onchange="S.G.populateGeo(this)">
					<option value="" style="color:#666"></option>
					{'Data'|Call:'getArray':'cities':$smarty.post.profile.city}
				</select></td>
			</tr>
			<tr>
				<th{if $form_errors.district} class="err"{/if}>{'District'|lang}:</th>
				<td><select class="selectbox" name="profile[district]" id="find_districts">
					<option value="" style="color:#666"></option>
					{'Data'|Call:'getArray':'districts':$smarty.post.profile.district}
				</select></td>
			</tr>
			<tr>
				<th{if $form_errors.street} class="err"{/if}>{'Street'|lang}:</th>
				<td><input type="text" name="profile[street]" class="textbox" value="{$smarty.post.profile.street|strform}" /></td>
			</tr>
	
			<tr>
				<th{if $form_errors.firstname} class="err"{/if}>{'Firstname'|lang}<span class="ast">*</span>:</th>
				<td><input type="text" name="profile[firstname]" class="textbox" value="{$smarty.post.profile.firstname|strform}" /></td>
			</tr>
			<tr>
				<th{if $form_errors.lastname} class="err"{/if}>{'Lastname'|lang}<span class="ast">*</span>:</th>
				<td><input type="text" name="profile[lastname]" class="textbox" value="{$smarty.post.profile.lastname|strform}" /></td>
			</tr>
	
			<tr>
				<th{if $form_errors.phone} class="err"{/if}>{'Phone number'|lang}:</th>
				<td><input type="text" name="profile[phone]" class="textbox" value="{$smarty.post.profile.phone|strform}" /></td>
			</tr>
			<tr>
				<th{if $form_errors.phone} class="err"{/if}>{'Skype'|lang}:</th>
				<td><input type="text" name="profile[skype]" class="textbox" value="{$smarty.post.profile.skype|strform}" /></td>
			</tr>
	
			<tr>
				<th{if $form_errors.descr} class="err"{/if}>{'About you'|lang}<span class="ast">*</span>:</th>
				<td colspan="3">
					<table cellspacing="0" class="flags">
					{foreach from='Site'|Call:'getLanguages' key=l item=a}
					<tr>
						<td><img src="/tpls/img/flags/24/{$l}.png" /></td>
						<td><textarea class="textbox" name="profile[about][{$l}]" style="width:300px;height:70px;">{$smarty.post.profile.about[$l]|strform}</textarea></td>
					</tr>
					{/foreach}
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3" class="b">
					<button type="submit" class="btn float_l">{'Update'|lang}</button>
				</td>
			</tr>
		</tbody></table>
	
	
	
	</div>
	<div class="col_w420">
		<h2>{'Change your password'|lang}</h2>
		<table cellspacing="0"><tbody>
			<tr>
				<th>
					{'New password:'|l}
				</th>
				<td>
					<input type="password" class="textbox" name="password" value="{$smarty.post.password|strform}" />
				</td>
			</tr>
			<tr>
				<th>
					{'Repeat password:'|l}
				</th>
				<td>
					<input type="password" class="textbox" name="re_password" value="" onfocus="$('#cur_password').show();" onblur="$('#cur_password').find('input').select()" />
				</td>
			</tr>
			<tr id="cur_password" style="display:none">
				<th class="text-right col-sm-4">
					{'Old password:'|l}
				</th>
				<td>
					<input type="password" class="textbox" name="cur_password" value="{$smarty.post.cur_password|strform}" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3" class="b">
					<button type="submit" class="btn float_l">{'Change'|lang}</button>
				</td>
			</tr>
		</tbody></table>
	
	</div>	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<input type="hidden" name="{$smarty.const.URL_KEY_REGISTER}" />
</form>
<div class="hr"></div>
<div class="back" style="float:left;position:relative;top:10px"><a href="?scripts">{'Back to scripts'|lang}</a></div>
{/strip}