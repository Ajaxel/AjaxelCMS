{strip}
	{include file='content/top.tpl'}
	
	{if $row}
		{if $row.entries[0].module=='html'}
			{$row.entries[0].body}
		{else}
			<div class="post">
			{assign var='file' value='content/content_'|concat:$row.entries[0].module|concat:'.tpl'}
			{include file=$file included=true list=false row=$row.entries[0]}
			</div>
		{/if}
	{else}
		{foreach from=$list item=arr}
			{foreach from=$arr.entries item=row}
				<div class="post">
				{assign var='file' value="content/content_`$row.module`.tpl"}
				{if $row.module=='html'}
					{$row.body}
				{elseif $row.bodylist && !$row.content.another_menu && ($content.id || $row.total_entries==1)}
					{include file=$file included=true list=false row=$row}
				{else}
					{include file=$file included=true list=true row=$row}
				{/if}
					{if !$row.plain}
						{if $row.content.keywords}
						<div class="tags">
							<span class="tags">
							{'tags:'|lang}
							{assign var='ex' value=','|explode:$row.content.keywords}
							{foreach from=$ex item=e}
								{assign var='tag' value=$e|trim}
								<a href="?tag-{$tag}" rel="tag">{$tag}</a>{if !$e@last}, {/if}
							{/foreach}
							</span>
						</div>
						{/if}
					{/if}
				</div>
			{/foreach}
		{/foreach}
		{include file='content/pager.tpl' top=false}
	{/if}
	{include file='content/bottom.tpl'}
{/strip}