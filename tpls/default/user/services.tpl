{strip}
<h1>{'Pick %1 of your services'|lang:5}</h1>
<script>
$().ready(function(){
	S.F.checkboxes('relation_checkboxes',5)
});
</script>
<form class="form2 ajax_form" method="post" action="?{$URL}" id="form">
	<table cellspacing="0" style="width:100%" id="relation_checkboxes"><tbody>
		{include file='includes/form_errors.tpl' table=4}
		<tr>
			{$relation_categories}
		</tr>
		<tr>
			<td colspan="3" class="b">
				<button type="submit" class="reg_button">{'Save services'|lang}<span class="x">{'Save services'|lang}</span></button>
			</td>
		</tr>
	</tbody></table>
	<input type="hidden" name="submitted" value="1" />
</form>
{/strip}