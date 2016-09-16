{strip}
{if $display}
	<div id="path">
		<div class="breadcrump">
		{if $smarty.const.SITE_TYPE=='popup' || $smarty.const.SITE_TYPE=='print'}
			<a href="{$smarty.const.HTTP_BASE}" class="main_link">{$smarty.const.HTTP_BASE|trim:'/'}</a>
		{else}
			<a href="/" class="main_link">{'Home'|lang}</a>
		{/if}
		{foreach from=$tree key=i item=b}
			{if $b@first} :: {/if}
			<a href="{$b.url}">{$b.title}</a>
			{assign var='j' value=$i+1}
			{if $tree[$j]} &gt; {/if}
		{/foreach}
		</div>
		<div class="search">
			<table cellspacing="0" cellpadding="0"><tr>
			<td style="padding-top:2px;">{'Site search'|lang}:</td>
			<td><input type="text" id="search_value" onkeyup="if(S.E.keyCode==13){ if ($('#search_value').val().length) location.href='?search='+$('#search_value').val(); else $('#search_value').focus() }" class="textfield" /></td>
			<td><button type="button" onclick="if ($('#search_value').val().length) location.href='?search='+$('#search_value').val(); else $('#search_value').focus()"></button></td>
			</tr></table>
		</div>
	</div>
{else}
	<!--var-content:tree-->
{/if}
{/strip}