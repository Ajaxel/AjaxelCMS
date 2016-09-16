{strip}
	{include file='content/title.tpl' row=$row}
	{if $list==true}
		<div class="content">
		{if $row.main_photo}
			<div class="pic"><div><a href="{$row.url_open}" onclick="S.G.get('{$row.url_open}');return false;"><img src="/{$smarty.const.PREFIX}files/{$row.table}/{$row.rid}/th3/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div></div>
		{/if}
			{if $row.bodylist}
				{$row.body}
			{else}
				{$row.descr}
			{/if}
		</div>
	{else}
		<div class="content open">
			{if $row.main_photo && !$row.bodylist}
			
				<div class="pic"><a href="/{$smarty.const.HTTP_EXT}{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th1/{$row.main_photo}" class="colorbox" rel="gal"><img src="/{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th2/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div>
			{/if}
				{$row.body}
				{if $row.url}<div class="url"><h2>Working website</h2><div class="content"> <a href="{$row.url}" target="_blank">{$row.url}</a></div></div>{/if}
			{assign var='files' value='Data'|call:'getFiles':$row.table:$row.rid:true:false:$row}
			{if $files}
			<div class="gallery">
			{foreach from=$files key=i item=f}
				{if $f.media=='image' && ($i || $row.bodylist)}
					<div class="picture">
					<div class="img">
						<a href="/{$smarty.const.HTTP_EXT}{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th1/{$f.file}" class="colorbox" rel="gal"><img src="/{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th2/{$f.file}" width="177" alt="{$f.title|strform}" border="0" /></a>
					</div>
					{if !$row.bodylist}<div class="title">{$i+1}. {$f.title}</div>{/if}
					</div>
				{/if}
			{/foreach}
			</div>
			{/if}
		</div>
	{/if}
{/strip}