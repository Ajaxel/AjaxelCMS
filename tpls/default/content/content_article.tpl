{strip}
	{include file='content/title.tpl' row=$row}
	{if $list==true}
		<div class="content">
		{if $row.main_photo}
			<div class="pic"><div><a href="{$row.url_open}"><img width="180" src="/{$smarty.const.HTTP_EXT}{$smarty.const.HTTP_DIR_FILES}content_{$row.module}/{$row.rid}/th2/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div></div>
		{/if}
			{if $row.bodylist}
				{$row.body}
			{else}
				{$row.descr}
			{/if}
		</div>
	{else}
		<div class="content open">
			{if $row.main_photo}
				<div class="pic"><a href="/{$smarty.const.HTTP_EXT}{$smarty.const.HTTP_DIR_FILES}content_{$row.module}/{$row.rid}/th1/{$row.main_photo}" class="colorbox"><img src="/{$smarty.const.HTTP_DIR_FILES}content_{$row.module}/{$row.rid}/th2/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div>
			{/if}
			{$row.body}
		</div>
	{/if}
{/strip}