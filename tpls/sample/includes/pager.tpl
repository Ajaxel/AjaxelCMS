{strip}
<ul id="pagenavi">
	{foreach from=$pager.pages key=p item=s}
	<li>
		<a href="{$pager.fullurl}{if $p-1>0}&page={$p-1}{/if}"{if $s} style="font-weight:bold"{/if}>{$p}</a>
	</li>
	{/foreach}
</ul>
{/strip}