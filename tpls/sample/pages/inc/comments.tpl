{strip}
<ul class="comments">
	{foreach from=$comments.list item=row}
	<li>
		<div class="pic">
			{if $row.user.photo}
				<a href="?user={$row.user.login}" class="ajax_link" style="background-image:url({$row.user.photo})" /></a>
			{/if}
		</div>
		<div class="text">
			<div class="l">
				<span class="date">{'Date'|Call:'dayCountDown':$row.added}</span>
				
				<span class="name linked">{$row.user.name}</span>
				{if $row.user.online}
					<span class="online"></span>			
				{else}
					<span class="offline"></span>
				{/if}
			</div>
			<div class="clean">
				{$row.body}
			</div>
		</div>
	</li>
	{/foreach}
</ul>
{include file='content/pager.tpl' pager=$comments.pager}
{/strip}