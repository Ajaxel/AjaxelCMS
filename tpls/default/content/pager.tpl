{strip}
{if $pager.total_pages>1}
<div class="pager">
	<ul class="pagenavi{if $top} top{/if}">
		<li class="txt">Page: </li>
		<li class="newer">
			{if $pager.page}
				{if $json}
					<a href="javascript:;" onclick="S.G.json('{$json}{if $pager.page>1}&{$pager.page_key}={$pager.page-1}{/if}')"></a>
				{else}
					<a href="{$pager.url}{if $pager.page>1}&{$pager.page_key}={$pager.page-1}{/if}"></a>
				{/if}
			{/if}
		</li>
		{foreach from=$pager.pages key=p item=s}
		<li class="page{if $s} selected{/if}">
			{if $json}
				<a href="javascript:;" onclick="S.G.json('{$json}{if $p-1>0}&{$pager.page_key}={$p-1}{/if}');">{$p}</a>
			{else}
				<a href="{$pager.url}{if $p-1>0}&{$pager.page_key}={$p-1}{/if}">{$p}</a>
			{/if}
		</li>
		{/foreach}
		{if $pager.next_page}
			<li class="older">
				{if $json}
					<a href="javascript:;" onclick="S.G.json('{$json}&{$pager.page_key}={$pager.next_page}')"></a>
				{else}
					<a href="{$pager.url}&{$pager.page_key}={$pager.next_page}"></a>
				{/if}
			</li>
		{/if}
	</ul>
</div>
{/if}
{/strip}