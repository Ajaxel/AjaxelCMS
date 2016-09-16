{strip}
{if $content}
	<div class="post">
		<h2>{'Nothing found'|lang}</h2>
		<div class="content">
			<a href="{$content.url}">{'Go back'|lang}</a>
		</div>
	</div>
{elseif $tree}
	<div class="post">
		<h2>{'This page has no content yet'|lang}</h2>
		<div class="content">
			{'Please stay patient, this page will be filled soon'|lang}...
		</div>
	</div>
{else}
	<div class="post">
		<h2>{'404 - page not found'|lang}</h2>
		<div class="content">
			The Web server is pleased to announce you that this place is not exactly what you asked for. But we don't want this to be a problem to you, so sit down, relax, have a cup of coffee and wait.<br />There's someone now who's taking care of your problems... <br /><br />Do not contact the server's administrator if this problem persists, you won't need it.
		</div>
	</div>
{/if}
{/strip}