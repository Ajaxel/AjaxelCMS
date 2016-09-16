{strip}
<script type="text/javascript">
CRM.tab_callback=function(){
	$('#c-content_{$name}').addClass('c-scroll');
};
CRM.tab_callback_resize=function(){
	$('#c-content_{$name}').css({
		height: CRM.height
	});
};
</script>

<table class="c-summary"><tbody>
<tr>
	<td>
		<div class="c-header">Welcome {$User.profile.firstname} {$User.profile.lastname}</div>
		<div class="c-text">
			<table class="c-box">
			<tbody>
			<tr><th>Your last login was:</th><td>{'H:i d M Y'|date:$User.LastLogged}</td></tr>
			<tr><th>Clicks made:</th><td>{$User.Clicks}</td></tr>
			<tr><th>Your IP address:</th><td>{$User.IP}</td></tr>
			<tr><th>Country detected:</th><td>{$User.Country}</td></tr>
			<tr><th>City detected:</th><td>{$User.City}</td></tr>
			<tr><th>Your browser:</th><td>{$User.Browser} {$User.BrowserVersion}</td></tr>
			<tr><th>Timezone:</th><td>{$User.Timezone/60} hours</td></tr>
			<tr><th>Screen resolution:</th><td>{$User.Width} x {$User.Height}</td></tr>
			</tbody>
			</table>
		</div>
	</td>
	<td>
		<div class="c-header">Data stats <select class="c-select"><option value="">Today</option></select> </div>
		<div class="c-text">
			<table class="c-box">
			<thead><tr><th class="c-none">&nbsp;</th><th>You today</th><th>Today</th><th>You total</th><th>Total</th></tr></thead>
			<tbody>
			<tr class="c-c"><th>Clients added:</th><td>3</td><td>3</td><td>3</td><td>3</td></td>
			<tr class="c-c"><th>Tasks created:</th><td>12</td><td>12</td><td>12</td><td>12</td></tr>
			</tbody>
			</table>
		</div>
	</td>
	<td>
		<div class="c-header">Contacts</div>
		<div class="c-text">
			Phone: +6543210<br />
			Fax: +6543210<br />
			Skype: skype_name
		</div>
	</td>
</tr>


<tr>
	<td colspan="2">
		<div class="c-header">Actions by user <select class="c-select"><option value="">Today</option></select></div>
		<div class="c-text">
			<table class="c-box">
			<thead><tr><th class="c-none">&nbsp;</th><th>Emailing</th><th>Locking</th><th>Adding</th><th>Editing</th><th>Deleting</th><th>Searching</th><th>Commenting</th><th>Clicking</th><th>Sitting</th><th>Drinking</th><th>Smoking</th></tr></thead>
			<tbody>
			<tr class="c-c"><th>{$User.Login} (you):</th><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td></tr>
			<tr class="c-c"><th>Vasja:</th><td>12</td><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td></tr>
			</tbody>
			<tfoot>
			<tr class="c-c"><th>Total:</th><td>12</td><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td><td>3</td></tr>
			</tfoot>
			</table>
		</div>
	</td>
	<td>
		<div class="c-header">Stats by checkboxes <select class="c-select"><option value="">Today</option></select> </div>
		<div class="c-text">
			<table class="c-box">
			<tr><th>Seminar:</th><td>3 (34)</td>
			<tr><th>Today tasks created:</th><td>12</td>
			</table>
		</div>
	</td>
</tr>

<tr>
	<td colspan="3">
	{*<div style="padding:50px 0;text-align:center;font:bold 104px 'Trebuchet MS';color:#5C70B1;">
		Under development
	</div>
	*}
	</td>
</tr>
</tbody></table>

{/strip}