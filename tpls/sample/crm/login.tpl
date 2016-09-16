{strip}
<script type="text/javascript">
$(function(){
CRM.tab_callback=function(){
	CRM.form.submit(function(){
		S.G.json('?crm&login',this,false,function(data){
			if ($('#c-login_{$name}').val()) {
				CRM.focus('c-password_{$name}');
			} else {
				CRM.focus('c-login_{$name}');
			}
		});
		return false;
	});
	$(window).bind('resize', function(){
		$('table.c-login').css({
			marginTop: $(window).height()/2-140
		});
	});
	$('table.c-login').css({
		marginTop: $(window).height()/2-140
	}).draggable({
		handle: '.ui-widget-header'
	});
	if ($('#c-login_{$name}').val()) {
		CRM.focus('c-password_{$name}');
	} else {
		CRM.focus('c-login_{$name}');
	}
};
});
</script>
<input type="hidden" name="jump" value="?crm" />
<table class="c-login ui-widget ui-widget-content ui-corner-all">
	<thead>
	<tr>
		<th colspan="2" class="ui-widget-header ui-widget-titlebar ui-corner-top">Login to CRM</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<th>Username:</th>
		<td><input type="text" name="login" class="c-input" id="c-login_{$name}" value="{$smarty.post.login|strform}" /></td>
	</tr>
	<tr>
		<th>Password:</th>
		<td><input type="password" name="password" class="c-input" id="c-password_{$name}" /></td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="2">
			<button type="submit" class="c-button">LOGIN</button>
		</td>
	</tr>
	</tfoot>
</table>
{/strip}