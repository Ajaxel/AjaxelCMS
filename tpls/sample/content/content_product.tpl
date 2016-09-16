{strip}
{if $list==true}
	<table class="item" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="21%" rowspan="2" class="item_pic" align="center">
			{if $row.main_photo}
			<img src="/th.php?p=files/content_{$row.module}/{$row.rid}/th2/{$row.main_photo}&w=145" border="0" />
			{else}
			&nbsp;
			{/if}
			</td>
			<td width="79%" valign="top"><div class="item_title"><a href="{$row.url_open}">{$row.title}</a></div>
			<div class="item_text">{$row.descr}</div></td>
		</tr>
		<tr>
			<td class="item_price" valign="middle" align="right" style="color:#666666;">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="49%" align="right">{if $row.price_old>0}{'Старая цена:'|lang} <span style="text-decoration:line-through;">{$row.price_old} {$row.currency}</span>{/if}&nbsp;</td>
					<td width="7%">&nbsp;</td>
					<td width="30%">{if $row.price>0}{'Цена:'|lang}<span class="blue">{$row.price} {$row.currency}</span>{/if}</td>
					<td width="14%">{if !$in_order}<a href="{$row.url_open}">
						<div class="more">
							<div></div>
							{'Далее'|lang}</div>
						</a>{/if}
					</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>
	<div class="item_sep"></div>
{else}
	<table class="tovar" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="177" valign="top">
			<div>
				<div id="mygaltop" class="svw">
					<ul style="margin:0px; padding:0px;">
						{foreach from=$row.files item=f}
						{if $f.t=='image'}
						<li style="width:177px;height:220px;overflow:hidden"><div style="width:177px;height:140px;"><a href="javascript:;" class="show-photo"><img src="/files/content_product/{$row.rid}/th2/{$f.f}" width="177" class="{ldelim}p:'[/th1/]',w:'{$f.w}',h:'{$f.h}'{rdelim}" border="0" /></a></div></li>
						{/if}
						{/foreach}
					</ul>
				</div>
			</div>
			<div style="clear:both;margin-top:40px">
			<a href="?compare&id={$row.rid}&cid={$smarty.get.cid}">
			<div class="srav_tovar">
				<div></div>
				{'Сравнить товар'|lang}</div>
			</a> <a href="?order&id={$row.id}&cid={$smarty.get.cid}">
			<div class="make_zapros">
				<div></div>
				{'Сделать запрос'|lang}</div>
			</a>
			</div>
		</td>
		<td width="473" valign="top" rowspan="2"><div class="tovar_title_box" style="height:40px">
				<div class="tovar_title"><img src="/{$smarty.const.DIR_TPL}images/g_arrow.jpg" /> {$row.title}</div>
				{if $row.price>0}<div class="tovar_price">{str_replace('.00','',$row.price)} {$row.currency}</div>{/if}
			</div>
			<div class="item_sep"></div>
			<div class="item_text_mrgn"></div>
			<div class="item_text">
				{$row.body}
			</div></td>
	</tr>
	</table>
	<div class="tabs">
		<table class="tabNavigation" cellspacing="0"  cellpadding="0">
			<tr>
				<td valign="top"><table width="100%" cellspacing="0"  cellpadding="0">
						<tr>
							<td class="stock_zakl active" align="center" valign="middle"><a href="#1">{'Specifications'|lang}</a></td>
							<td class="zakl_otstp"></td>
							<td class="stock_zakl" align="center" valign="middle"><a href="#2">{'Accessories'|lang}</a></td>
							<td class="zakl_otstp"></td>
							<td class="stock_zakl" align="center" valign="middle"><a href="#3">{'Documents'|lang}</a></td>
						</tr>
					</table></td>
			</tr>
		</table>
		<div id="1">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				{assign var='i' value=0}
				{foreach from=$row.options.spec key=key item=item}
				{if !$i}
				<tr class="tr_grey">
					<td width="128" align="center" class="blue">Model</td>
					<td width="174" class="tr_sep"><div class="blue"> <img src="/{$smarty.const.DIR_TPL}images/t_arrow.jpg"/>{$item[0]}</div></td>
					<td width="9" class="tr_sep">&nbsp;</td>
					<td width="188" class="tr_sep"><div class="blue"> <img src="/{$smarty.const.DIR_TPL}images/t_arrow.jpg"/>{$item[1]}</div></td>
					<td width="10" class="tr_sep">&nbsp;</td>
					<td width="197" class="tr_sep"><div class="blue"> <img src="/{$smarty.const.DIR_TPL}images/t_arrow.jpg"/>{$item[2]}</div></td>
				</tr>
				{else}
				<tr class="tr_{if $i%2==0}white{else}grey{/if}">
					<td width="128" align="right">{$key}</td>
					{if $item[1]}
					<td width="174" class="tr_sep"><div class="table_item_info" style="text-align:center">{$item[0]}</div></td>
					<td width="9" class="tr_sep">&nbsp;</td>
					<td width="188" class="tr_sep"><div class="table_item_info" style="text-align:center">{$item[1]}</div></td>
					<td width="10" class="tr_sep">&nbsp;</td>
					<td width="197" class="tr_sep"><div class="table_item_info" style="text-align:center">{$item[2]}</div></td>
					{else}
					<td colspan="5" class="tr_sep"><div class="table_item_info" style="text-align:center">{$item[0]}</div></td>
					{/if}
				</tr>
				{/if}
				{assign var='i' value=$i+1}
				{/foreach}
				<tr class="tr_white">
					<td align="center" height="1" colspan="6"></td>
				</tr>
			</table>
		</div>
		<div id="2">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				{assign var='i' value=0}
				{foreach from=$row.options.access key=key item=item}
				<tr class="tr_{if $i%2==0}white{else}grey{/if}">
					<td width="128" align="center">{$key}</td>
					<td class="tr_sep"><div class="table_item_info">{$item[0]}</div></td>
				</tr>
				{assign var='i' value=$i+1}
				{/foreach}
				<tr class="tr_white">
					<td align="center" height="1" colspan="6"></td>
				</tr>
			</table>
		</div>
		<div id="3">
			<div class="offer">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					
					{foreach from=$row.files item=f}
					{if $f.t=='doc'}
					<tr>
						<td colspan="2"><a href="/files/content_product/{$row.id}/th1/{$f.f}" target="_blank" class="title">{$f.f}</a></td>
					</tr>
					{/if}
					{/foreach}
				</table>
			</div>
		</div>
	</div>
{/if}
{/strip}