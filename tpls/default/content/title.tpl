{strip}
{*
{if (!$row.bodylist && $content.renewed) || (!$content.id && !$tree[1])}
	<h2><a href="{if $row.content.name=='about-cms'}/portfolio/cmssystem{else}{$row.url_open}{/if}">{$row.title}</a></h2>
{else}
*}
{if !$row.bodylist || !$content.id || ($row.total_entries>1 && !$content.is_open)}
	<h2><a href="{$row.url_open}">{$row.title}</a></h2>
{else}
	<h1 style="margin-bottom:0px">{$row.title}</h1>
{/if}

<div class="info">
	{assign var='author' value=$Tpl->user($row.userid, 'login')}
	<span class="postinfo">{'author'|lang}:&nbsp;<span>{$author}</span> | 
		{if $row.url}
			{'started @'|lang} <a href="{$row.url_open}" title="{'Permanent link to this post'|lang}">{'l, F d, Y'|date:$row.content.dated}</a>
		{else}
			{'posted @'|lang} <a href="{$row.url_open}" title="{'Permanent link to this post'|lang}">{'l, F d, Y H:i'|date:$row.added}</a>
		{/if}
		{if $row.content.comment=='Y'}| <a href="{$row.url_open}#comments" title = "{'comments'|lang}" class="comments">{'Comments (%1)'|lang:$row.content.comments}</a>{/if}{if !$row.bodylist || $row.content.comment=='Y'} | {'Views'|lang}: {$row.content.views}{/if}
	</span>
	<div class="fixed"></div>
</div>
{/strip}