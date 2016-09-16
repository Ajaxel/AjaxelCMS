{strip}
{if $row}
	{if $row.entries[0].module=='html'}
		{$row.entries[0].body}
	{else}
		<div class="post open">
		{assign var='file' value='content/content_'|concat:$row.entries[0].module|concat:'.tpl'}
		{include file=$file included=true list=false row=$row.entries[0]}
		</div>
	{/if}
{else}
	<h2>{$menu[0].title}{if $menu[1].title}: {$menu[1].title}{/if}</h2>
	{foreach from=$list item=arr}
		<div class="post">
		{foreach from=$arr.entries item=row}
			{assign var='file' value="content/content_`$row.module`.tpl"}
			{if $row.module=='html'}
				{$row.descr}
			{elseif $row.bodylist && !$row.content.another_menu && ($content.id || $row.total_entries==1)}
				{include file=$file included=true list=false row=$row}
			{else}
				{include file=$file included=true list=true row=$row}
			{/if}
		{/foreach}
		</div>
		{if !$arr@last}<div class="hr"></div>{/if}
	{/foreach}
	
{/if}
{include file='content/pager.tpl' top=false}
{include file='content/bottom.tpl'}

{/strip}