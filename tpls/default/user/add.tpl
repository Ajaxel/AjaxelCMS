<script type="text/javascript">

$().ready(function(){
	S.G.upload({
		name	: '{$upload.name}',
		id		: '{$upload.id}',
		hash	: '{$upload.hash}',
		file	: 'main_photo',
		queueID	: 'uploadQueue',
		multi	: true,
		auto	: true,
		buttonImg: '/{$smarty.const.HTTP_DIR_TPL}images/file_{$lang}.jpg',
		width	: 177,
		height	: 27,
		fileExt	: '{$upload.ext}',
		fileDesc: '{$upload.desc}',
		start	: function(){
			$('#uploadQueue').fadeIn();
		},
		func	: function(response){
			if (response.substring(0,2)!='1/') {
				alert(response);	
			} else {
				$('<li>').html(response.substring(2)).hide().insertBefore($('#photos').find('li:last')).fadeIn();
				S.I.init();
			}
		}
	});
});
</script>
{strip}
{if $smarty.post.data.id}
	<h1>{'Edit request'|lang}</h1>
{else}
	<h1>{'Add new request'|lang}</h1>
{/if}


<form class="form ajax_form" method="post" action="?{$URL}" id="register_form">
<table cellspacing="0" style="width:100%"><tbody>
	{include file='includes/form_errors.tpl' table=4}
	<tr>
		<th{if $form_errors.title || $form_errors.title_ru} class="err"{/if}>{'Headline'|lang}<span class="ast">*</span>:</th>
		<td colspan="3">
			<table cellspacing="0" class="flags">
			<tr>
				<td><img src="/tpls/img/flags/24/ru.png" /></td>
				<td><input type="text" class="textbox" name="data[title_ru]" style="width:500px" value="{$smarty.post.data.title_ru|strform}" /></td>
			</tr>
			<tr>
				<td><img src="/tpls/img/flags/24/ee.png" /></td>
				<td><input type="text" class="textbox" name="data[title]" style="width:500px" value="{$smarty.post.data.title|strform}" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th{if $form_errors.catref} class="err"{/if}>{'Select a section'|lang}<span class="ast">*</span>:</th>
		<td colspan="3">
			<select class="selectbox" name="data[catref]" style="width:270px">
				<option value="" style="color:#666">{'Common section'|lang}</option>
				{$Category->selected($smarty.post.data.catref)->toOptions()}
			</select>
			<select class="selectbox" name="data[catref2]" style="width:270px;margin-left:7px;">
				<option value="" style="color:#666">{'Additional section'|lang}</option>
				{$Category->selected($smarty.post.data.catref2)->toOptions()}
			</select>
		</td>
	</tr>
	<tr>
		<th{if $form_errors.state} class="err"{/if}>{'State'|lang}<span class="ast">*</span>:</th>
		<td>
		<input type="hidden" name="data[country]" id="find_countries" value="63" />
		<select class="selectbox" name="data[state]" id="find_states" onchange="S.G.populateGeo(this)">
			<option value="" style="color:#666"></option>
			{'Data'|Call:'getArray':'states':$smarty.post.data.state}
		</select></td>
		<th{if $form_errors.city} class="err"{/if}>{'City'|lang}<span class="ast">*</span>:</th>
		<td colspan="3"><select class="selectbox" name="data[city]" id="find_cities" onchange="S.G.populateGeo(this)">
			<option value="" style="color:#666"></option>
			{'Data'|Call:'getArray':'cities':$smarty.post.data.city}
		</select></td>
	</tr>
	
	<tr>
		<th{if $form_errors.district} class="err"{/if}>{'District'|lang}:</th>
		<td><select class="selectbox" name="data[district]" id="find_districts">
			<option value="" style="color:#666"></option>
			{'Data'|Call:'getArray':'districts':$smarty.post.data.district}
		</select></td>
		<th{if $form_errors.street} class="err"{/if}>{'Street'|lang}:</th>
		<td><input type="text" name="data[street]" class="textbox" value="{$smarty.post.data.street|strform}" /></td>
	</tr>
	<tr>
		<th{if $form_errors.price} class="err"{/if}>{'Budget'|lang}<span class="ast">*</span>:</th>
		<td colspan="3" style="line-height:100%">
			<div><label><input type="checkbox" onclick="if(this.checked) $('#agreed').attr('disabled','disabled'); else $('#agreed').removeAttr('disabled');" name="data[agreed]"{if $smarty.post.data.agreed} checked{/if}> {'By agreement'|lang}</label></div>
			<div id="agreed" style="padding-top:5px;">
			<input type="text" name="data[price]" style="width:110px;font-size:20px;text-align:right" class="textbox" value="{$smarty.post.data.price|strform}" /> <span style="font-size:20px;color:#e90000">€</span>
			<select class="selectbox" name="data[price_type]" style="margin:-4px 0 0 10px;position:relative;top:-3px">
				<option value="" style="color:#666">{'-- please select --'|lang}</option>
				{'Data'|Call:'getArray':'my:price_type':$smarty.post.data.price_type}
			</select>
			</div>
		</td>
	</tr>
	<tr>
		<th{if $form_errors.descr} class="err"{/if}>{'Ad text'|lang}<span class="ast">*</span>:</th>
		<td colspan="3">
			<table cellspacing="0" class="flags">
			<tr>
				<td><img src="/tpls/img/flags/24/ru.png" /></td>
				<td><textarea class="textarea" name="data[descr_ru]" style="width:500px;height:120px;">{$smarty.post.data.descr_ru|strform}</textarea></td>
			</tr>
			<tr>
				<td><img src="/tpls/img/flags/24/ee.png" /></td>
				<td><textarea class="textarea" name="data[descr]" style="width:500px;height:120px;">{$smarty.post.data.descr|strform}</textarea></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>{'Add images'|lang}:</th>
		<td colspan="3" style="padding-left:25px">
			<ul class="photos" id="photos">
				{foreach from=$upload.files item=f}
					<li>{include file='includes/pic.tpl' f=$f}</li>
				{/foreach}
				<li class="button">
					<div class="up_button corner-tl corner-bl" style="border-left:1px solid #cccccc"><input type="file" id="main_photo" /></div>
				</li>
			</ul>
			<div id="uploadQueue" style="width:400px;clear:both;"></div>
		</td>
	</tr>
	<tr>
		<th>{'Youtube URL'|lang}:</th>
		<td colspan="3">		
			<input type="text" name="data[youtube]" class="textbox" value="{$smarty.post.data.youtube|strform}" /> 
		</td>
	</tr>
	<tr>
		<td colspan="5" style="text-align:center;padding:20px;">
			<a href="?user&ads" style="float:left;margin-top:10px;position:relative;left:-18px;" class="ajax_link">{'Cancel?'|lang}</a>
			<button type="submit" class="reg_button">{'Publish'|lang}<span class="x">{'Publish'|lang}</span></button>
		</td>
	</tr>
</tbody></table>
<input type="hidden" name="submitted" value="1" />
</form>

{/strip}