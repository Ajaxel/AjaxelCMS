{strip}
<h2>{'My balance'|lang}</h2>
{'balance_text_top'|lang}
<br /><br />
<div class="form">
	<table cellspacing="0" style="width:100%"><tbody>
		<tr>
			<th style="border-right:1px solid #e6edf0;width:20%">
				<big style="color:#515151;font-size:18px">{'My balance:'|lang}</big><br>
				<div style="color:#f20000;font-size:24px;padding-top:5px">{$User.profile.money|number_format:2} €</div>
			</th>
			<td class="td">
				{'balance_text'|lang}<br><br>
				<button type="submit" class="reg_button" style="margin-left:110px">{'Add funds'|lang}<span class="x">{'_Add funds'|lang}</span></button>
			</td>
		</tr>
	</tbody></table>
</div>
<br /><br />
{'More info'|lang}
<ul class="ul">
	<li><a href="?user&purchase_history">{'Purchase history'|lang}</a></li>
	<li><a href="?user&payment_history">{'Payment history'|lang}</a></li>
</ul>

{/strip}