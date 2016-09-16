{strip}
<li class="comment {if $row.userid==1}admincomment{else}regularcomment{/if}" id="c{$row.id}" onmouseover="$('#act_{$row.id}').show()" onmouseout="$('#act_{$row.id}').hide()">
	<div class="author">
		<a name="c{$row.id}"></a>
		<div class="pic"><div class="pad">
		{if $row.author && $row.author.photo.th3}
			<a href="javascript:;"  class="show-photo"><img height="50" class="{ p:'[/th1/]',w:'{$row.author.photo.width}',h:'{$row.author.photo.height}', t:'{$row.author.login}' }" src="/{$row.author.photo.th3}" alt="Gravatar" /></a>
		{else}
			<img src="/tpls/img/no-photo.png" />
		{/if}
		</div></div>
		<div class="name">
			{if $row.url}
				<a title="{$row.author.login}" href="{$row.url}" target="_blank">{$row.name}</a>
			{else}
				{$row.name}
			{/if}
		</div>
	</div>
	<div class="info">
		<div class="date">{'d/m/y H:i'|date:$row.added} | <a title="permalink: test" href="#c{$row.id}">#</a>&nbsp;{$row.subject} {if $row.url} | <a href="{$row.url}" target="_blank">www</a>{/if}</div>
		{if '[reply='|explode:$row.original|count<5}
		{*
		<div class="act" style="display:none" id="act_{$row.id}">
			<a class="editlink" href="javascript:;" onclick="$('#cBody').focus().val('').insertAtCaret('[reply={'d/m/y H:i'|date:$row.added} {$row.name|replace:']':''}]{$row.original|strJAVA}[/reply]\n');">reply</a>
		</div>
		*}
		{/if}
		<div class="fixed"></div>
		<div class="content" style="line-height:110%;font-size;11px;margin-top:4px;">{$row.body}</div>
	</div>
	<div class="fixed"></div>
</li>
{/strip}