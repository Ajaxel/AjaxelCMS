{strip}
	{include file='content/title.tpl' row=$row}
	{if $list==true}
		<div class="content">
		{if $row.main_photo}
			<div class="pic"><div><a href="{$row.url_open}" onclick="S.G.get('{$row.url_open}');return false;"><img src="/{$smarty.const.HTTP_DIR_FILES}content_{$row.module}/{$row.rid}/th3/{$row.main_photo}" alt="{$row.title}" border="0" /></a></div></div>
		{/if}
			{$row.descr}
		</div>
	{else}
		<div class="content open">
			{if $row.main_photo && !$row.bodylist && $row.url}
				<div class="pic"><a href="/{$smarty.const.HTTP_DIR_FILES}content_gallery/{$row.rid}/th1/{$row.main_photo}" class="colorbox" rel="gal"><img src="/{$smarty.const.HTTP_DIR_FILES}content_{$row.module}/{$row.rid}/th2/{$row.main_photo}" alt="{$row.title}" border="0" /></a></div>
			{/if}
				{$row.body}
				{if $row.body}<br />{/if}
				{if $row.url}<div class="url"><h2>{'Working website'|lang}</h2><div class="content"> <a href="{$row.url}" target="_blank" rel="nofollow">{$row.url}</a></div></div>{/if}
			<div class="gallery">
			{if !$row.bodylist}
				{if $row.url}
					<h2>{'Screenshots'|lang}</h2>
				{/if}
			{/if}
			
			
			{assign var='files' value='Data'|call:'getFiles':$row.table:$row.rid:true:false:$row}
			
			{if $row.url || $url0!='tutorials'}			
				{foreach from=$files key=i item=f}
					{if $f.media=='image' && ($i || $row.bodylist)}
						<div class="picture">
						<div class="img">
							<a href="{$f.th1}" class="colorbox" rel="gal"><img src="/{$f.th2}" width="177" alt="{$f.title|strform}" border="0" /></a>
						</div>
						{if !$row.bodylist}<div class="title">{$f.title}</div>{/if}
						</div>
					{/if}
				{/foreach}
			{else}
				{foreach from=$files key=i item=f}
					{if $f.media=='image' && ($i || $row.bodylist)}
							<img src="/{$f.th1}" alt="{$f.title|strform}" border="0" />{if !$f@last}<br /><br />{/if}
					{/if}
				{/foreach}
			{/if}
			</div>
		</div>
	{/if}
{/strip}