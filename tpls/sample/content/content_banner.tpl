{assign var='file' value='Data'|Call:'sortFile':$row.main_photo:$row.table:$row.rid:$row}
{if $list}
<center>
{$file.html}
</center>
{else}

	<h2>{$row.title}</h2>
	<div style="padding:5px 0">
	{$file.html}
	</div>
	<div class="content">
		{$row.body}
	</div>
{/if}