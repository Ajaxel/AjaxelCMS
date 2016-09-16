{strip}
{assign var='winner' value=0}
<div id="slots" style="width:900px;margin:0 auto;color:#000">
	<div id="slots_top">
		<a href="javascript:;" onclick="S.L.open('rules')" class="l"></a>
		<a href="javascript:;" onclick="S.L.open('paytable')" class="r"></a>
	</div>
	<div id="slots_barrels">
		<div class="slots_left slots_lines1"></div>
		<ul></ul>
		<ol></ol>
		<div class="slots_lines"></div>
		<div class="slots_right">
			<a href="javascript:;" onclick="S.L.mute(this)" class="slots_mute"></a>
			<button onclick="S.L.spin()" id="slots_spin"></button>
		</div>
	</div>
	<div id="slots_buttons" class="slots_lines1"></div>
	<div id="slots_win"></div>
	<div id="slots_double">
		<button type="button" onclick="S.L.double()"></button>
	</div>
	<div id="slots_paytable"{if $winner} class="slots_winner" style="background:url(/{$smarty.const.HTTP_DIR_TPL}images/slots/winner.jpg)"{/if}>
		<p class="p">
			{capture assign='caption'}
				Hi {if $row.firstname}{$row.firstname}{else}{$sess_email}{/if}, and welcome to our GOLD RUSH SLOT MACHINE.
			{/capture}
			{$caption}
		</p>
		<ul id="slots_top10" style="padding-left:45px">
			{$slots_top10}
		</ul>
		
		{*
		<table class="slots_links"><tr>
		<td><a href="javascript:;" onclick="S.L.top100();">TOP list 100</a></td>
		<td><a href="javascript:;" onclick="S.L.final();">Final list</a></td>
		</tr></table>
		*}
	</div>
	<div id="slots_rules">
		<p class="p">
			{$caption}
		</p>
		<div class="l" style="width:350px;padding-right:0;">
			GOLD RUSH SLOTMACHINE<br><br>
{if $sess_email}
{'@[slots_rules_free]As a crowdminer you get 1000 FREE gold nuggets  to play with.<br>
<br>
You can choose from 1 to 9 lines bet: each line costs 1 nugget so 9 lines will cost 9 nuggets. You can win Gold Coins and the total amount of your winnings will be shown in the scoreboard<br>
<br>
When win you can double up, if you decide to double up the game you will see a picture which you will find again behind one of the questions marks. If you win, you will double up your gold coins you just won, if you fail you will lose them all.<br>
<br>
If you get 5 GOLD RUSH DAYS logos you will win a FREE crowdminer access STANDART.'|lang}
{else}
{'@[slots_rules]As a crowdminer you get 1000 free gold nuggets every week to play with.<br>
<br>
You can choose from 1 to 9 lines bet: each line costs 1 nugget so 9 lines will cost 9 nuggets. You can win Gold Coins and the total amount of your winnings will be shown in the scoreboard “Your Weekly Score”.<br>
<br>
When win you can double up, if you decide to double up the game you will see a picture which you will find again behind one of the questions marks. If you win, you will double up your gold coins you just won, if you fail you will lose them all.<br>
<br>
The 10 best players will be included in the “Top 10 List” and they will get the chance to partecipate in the final round to win a 100gr gold bar.
The final round will be on the 14th week, from March 31st to April 6th 2014.<br>
<br>
In the final round all the invited players will get 5000 nuggets and the top player on this round will get a 100gr gold bar.'|lang}<br>
<br>
Good luck, {$row.firstname|first}!
{/if}
		</div>
		
	</div>
	<div id="slots_top100">
		<p class="p">
			{$caption}
		</p>
		<div>
			<table cellspacing="0" class="slots_table">
				<tbody>
				<tr>
					<td>
						<table cellspacing="0" class="slots_table2">
							<thead>
								<tr>
									<th colspan="2">player</th>
									<th>gold coins</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$top100 key=i item=d}
								{if $i<=21}
								<tr>
									<td>{$i+1}.</td>
									<td class="slots_i"><img src="/tpls/img/flags/24/{$d.country}.png"> <span>{$d.firstname}</span></td>
									<td title="Nuggets left: {$d.slots_today}">{$d.slots_credits|number_format:0:'':','}</td>
								</tr>
								{/if}
								{/foreach}
							</tbody>
						</table>
					</td>
					<td>
						<table cellspacing="0" class="slots_table2">
							<thead>
								<tr>
									<th colspan="2">player</th>
									<th>gold coins</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$top100 key=i item=d}
								{if $i>21 && $i<=43}
								<tr>
									<td>{$i+1}.</td>
									<td class="slots_i"><img src="/tpls/img/flags/24/{$d.country}.png"> <span>{$d.firstname}</span></td>
									<td title="Nuggets left: {$d.slots_today}">{$d.slots_credits|number_format:0:'':','}</td>
								</tr>
								{/if}
								{/foreach}
							</tbody>
						</table>
					</td>
					<td>
						<table cellspacing="0" class="slots_table2">
							<thead>
								<tr>
									<th colspan="2">player</th>
									<th>gold coins</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$top100 key=i item=d}
								{if $i>43}
								<tr>
									<td>{$i+1}.</td>
									<td class="slots_i"><img src="/tpls/img/flags/24/{$d.country}.png"> <span>{$d.firstname}</span></td>
									<td title="Nuggets left: {$d.slots_today}">{$d.slots_credits|number_format:0:'':','}</td>
								</tr>
								{/if}
								{/foreach}
							</tbody>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<div id="slots_final">
		<p class="p">
			{$caption}
		</p>
		<div>
			<table cellspacing="0" class="slots_table slots_span4">
				<tbody>
				{assign var='j' value=0}
				{foreach from=$final key=yw item=arr}
					{if $j%4==0}<tr>{/if}
					<td>
						<table cellspacing="0" class="slots_table2">
							<thead>
								<tr>
									<th colspan="2">{$yw|substr:0:4}, week: {$yw|substr:4}</th>
									<th>coins</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$arr key=i item=d}
								{if $i<=9}
								<tr>
									<td>{$i+1}.</td>
									<td class="slots_i"><img src="/tpls/img/flags/24/{$d.country}.png"> <span>{$d.firstname}</span></td>
									<td>{$d.credits|number_format:0:'':','}</td>
								</tr>
								{/if}
								{/foreach}
							</tbody>
						</table>
					</td>
					{if $j%4==3}</tr>{/if}
					{assign var='j' value=$j+1}
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
	
	<ul class="slots_stats">
		<li class="slots_li0">
			{if $row}
				HI {$row.firstname} {$row.lastname} YOU ARE NUMBER {$place} ON TOPLIST OF GAME #{$game_row.gameid}
			{else}
				HI {$sess_email}, WELCOME TO OUR SLOT MACHINE
			{/if}
		</li>
		<li class="slots_li1">FREE SPINS</li>
		<li class="slots_li2">ROUND NUGGETS </li>
		<li class="slots_li3">YOUR SCORE</li>
		<li class="slots_li4">TOP SCORE</li>
	</ul>
	<ul id="slots_stats">
		<li class="slots_li1" id="slots_free">0</li>
		<li class="slots_li2" id="slots_nuggets">{$credits_today|number_format:'0':'':','}</li>
		<li class="slots_li3" id="slots_credits">{$credits|number_format:'0':'':','}</li>
		<li class="slots_li4" id="slots_topscore">{$top_score|number_format:'0':'':','}</li>
		<li class="slots_li5" id="slots_clock">{$clock}</li>
	</ul>
</div>
<div id="slots_sounds"></div>


<script type="text/javascript">
$().ready(function(){
	S.G.addCSS('/{$smarty.const.HTTP_DIR_TPL}css/slots.css');
	S.G.addJS(['/tpls/js/plugins/jquery.easie.min.js','/tpls/js/slots.js'],function(){
		S.L.init({$game|json},'/{$smarty.const.HTTP_DIR_TPL}images/slots/','NUGGET','BIG PRIZE','CREDIT','S');
	});
});
</script>
{/strip}