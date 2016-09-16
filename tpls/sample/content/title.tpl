{strip}

{if $row.url_is && (!$row.bodylist || $row.content.comment=='Y')}
	<h3 style="margin-bottom:0px"><a href="{$row.url_open}" class="ajax">{$row.title}</a></h3>
{else}
	<h2 style="margin-bottom:0px" class="no_e"><span>{$menu[9].title}:</span> {$row.title}</h2>
{/if}

<div class="info">
	{assign var='author' value=$Tpl->user($row.userid, 'firstname')}
	<span class="postinfo">{'Date'|Call:'dayCountDown':$row.added} <b>{$author}</b> {if $row.content.comment=='Y'} {'%1 {number=%1,Comment,Comments}'|lang:($row.content.comments|intval)}{/if}{if !$row.bodylist || $row.content.comment=='Y'}{/if}
	</span>
</div>

{/strip}