{strip}
	
	{if $list==true}
		
		
		<div class="video_item">
			<div class="p" style="">
				<a href="?videos&id={$row.id}" class="ajax_link"></a>
			</div>
			<div class="title">{$row.title}</div>
		</div>
		
	{else}
		
		<h1>{'Video:'|lang} {$row.title}</h1>
		<div class="content open">
			{$row.descr}coming soon
		</div>
		<div class="back">
	{/if}

{/strip}