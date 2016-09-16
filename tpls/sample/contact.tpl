{strip}
<h2>{'I’d Totally Love to Hear from You'|l}</h2>
<p>
	{'[contact-intro]Think I might be a good fit for your next web project?<br>
Fill out the form below, give us a call, or drop us a line and tell us about it.'|l}
</p>
<div class="hr"></div>
<div class="col_w420 float_l">
	<div id="contact_form">
		<h4>{'Quick Contact Form'|l}</h4>
		<form method="post" id="contact-form" class="form ajax" action="?{$URL}">
			{include file='includes/form_errors.tpl' table=false}
			<label for="email">{'Your email:'|l}</label>
			<input type="text" name="contact[email]" value="{$smarty.post.contact.email|strform}" class="validate-email required input_field" />
			<div class="cleaner_h10"></div>
			<label for="text">{'Message:'|l}</label>
			<textarea name="contact[text]" rows="0" style="height:240px;" cols="0" class="required">{$smarty.post.contact.text|strform}</textarea>
			<div class="cleaner_h10"></div>
			<center>
			<button type="submit" style="float:none" class="btn">{'Send'|l}</button>
			</center>
		</form>
	</div>
</div>
<div class="col_w420 last_box" style="padding-left:20px;">
	<h4>{'Mailing Address'|l}</h4>
	{'[mailing-address]Nevada street<br/>
South-carolina, Francisco<br/>
Bermuda'|l}<br/>
	<br/>
	<iframe width="410" height="240" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key={$smarty.const.GOOGLE_MAPS_KEY}&q={'Nevada street South-carolina, Francisco Bermuda'|urlencode}"></iframe>
	<br/>
	<br/>
	Email: <a href="mailto:info@ajaxel.com">info@ajaxel.com</a><br/>
	Tel: +372-56-759-366<br/>
	Skype: <a href="skype:ajaxel.com?chat">ajaxel.com</a>
</div>
{/strip}