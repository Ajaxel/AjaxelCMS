{strip}
{inc file='content/top.tpl'}
{if $row}
	{if $row.module=='html'}
		{$row.body}
	{else}
		<div class="post open">
		{assign var='file' value="grid/grid_`$row.module`.tpl"}
		{include file=$file included=true list=false row=$row}
		</div>
	{/if}
{else}
	<h2>{"h1_grid_`$data.module`"|lang}</h2>
	
	<div class="post">
		<div class="content">
			{"@`$data.module`_intro"|lang}
		</div>
		<div class="hr"></div>
		{foreach from=$data.list item=row}
			{assign var='file' value="grid/grid_`$data.module`.tpl"}
			{if $row.module=='html'}
				{$row.body}
			{else}
				{include file=$file list=true row=$row}
			{/if}
		{/foreach}
	</div>
	{inc file='content/pager.tpl' top=false}
	{inc file='content/bottom.tpl'}
	
	{if $menu && $menu[1]}
		<div class="bottom">
			<div class="back"><a href="?scripts">{'Back to %1'|lang:$menu[0].title}</a></div>
		</div>
	{/if}
{/if}
{/strip}