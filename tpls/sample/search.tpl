{strip}
{include file='content/top.tpl'}

{if !$total}
	<div class="post">
		<h2>{'Nothing relevant to your search criteria: "%1"'|lang:$smarty.get.search|strform}</h2>
		<div class="content">
			<img src="/tpls/cms/images/find.gif" alt="Not found" />
			{'Please try again'|lang}...
		</div>
	</div>
{else}
	<h1>{'Search results for "%1":'|lang:$smarty.get.search}</h1>
	<div class="subh1">{'Total found: %1'|lang:$total}</div>
	{foreach from=$list key=index item=row}
		<div class="post">
		{assign var='file' value='content/content_'|concat:$row.module|concat:'.tpl'}
		{include file=$file included=true list=true row=$row index=$index}
		</div>
	{/foreach}
	<img src="/tpls/cms/images/search.jpg" alt="Found something" />
	{include file='content/pager.tpl'}
{/if}
{include file='content/bottom.tpl'}
{/strip}