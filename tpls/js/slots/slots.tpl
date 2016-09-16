<div class="post">
	{include file='includes/user_menu.tpl'}
	<h1><b>{'Goldmine slots'|lang}</b></h1>
	
	<div id="slots">
		<div id="slots_top">
			Welcome to slots
		</div>
		<div id="slots_barrels">
			<div class="slots_left"></div>
			<ul>
				
			</ul>
			<div class="slots_right"></div>
		</div>
		<div id="slots_buttons">
			<table cellspacing="0"><tr>
				<td><button type="button" class="active" onclick="S.L.lines(1,this)">1 line</button></td>
				<td><button type="button" onclick="S.L.lines(2,this)">2 lines</button></td>
				<td><button type="button" onclick="S.L.lines(3,this)">3 lines</button></td>
				<td><button type="button" onclick="S.L.lines(4,this)">4 lines</button></td>
				<td><button type="button" onclick="S.L.lines(5,this)">5 lines</button></td>
				<th><button type="button" onclick="S.L.spin()"><b>Spin</b></button></th>
			</tr></table>
		</div>
		<div id="slots_stats">
			WIN: <span id="slots_win">0.00</span><br>
			CREDITS: <span id="slots_credits">0.00</span><br>
			FREE: <span id="slots_free">0</span><br>
		</div>
	</div>
	
</div>

<script type="text/javascript">
S.G.addJS('/tpls/js/slots.js',function(){
	S.L.init({$game|json});
});
</script>