{strip}
	{include file='content/title.tpl' row=$row}
	{if $list==true}
		<div class="content">
			{if $row.main_photo}
				<div class="pic"><a href="{$row.url_open}"><img src="{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th3/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div>
			{/if}
			{if $row.descr}
				{$row.descr}
			{else}
				{$row.body}
			{/if}
		</div>
	{else}
		<div class="content open">
			{if $row.main_photo && !$row.bodylist}
				<div class="pic"><a href="{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th1/{$row.main_photo}" class="colorbox" rel="gal"><img src="{$smarty.const.HTTP_DIR_FILES}{$row.table}/{$row.rid}/th2/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div>
			{/if}
				{if $row.body}
					{$row.body}
				{else}
					{$row.descr}
				{/if}
				{if $row.url}<div class="url"><h2>{'Working website'|lang}</h2><div class="content"> <a href="{$row.url}" target="_blank">{$row.url}</a></div></div>{/if}
			{assign var='files' value='Data'|call:'getFiles':$row.table:$row.rid:true:false:$row}
			{if $files}
			<div style="clear:both"></div>
			<div class="gallery" style="padding-top:15px">
			{foreach from=$files key=i item=f}
				{if $f.media=='image' && ($i || $row.bodylist)}
					<div class="pic" style="margin-bottom:15px">
						<a href="{$f.th1}" class="colorbox" rel="gal"><img src="{$f.th2}" width="177" alt="{$row.alt|strform}" border="0" /></a><div class="c"></div>
						{if !$row.bodylist && $f.title}<div class="title l">{$i+1}. {$f.title}</div>{/if}{$f.admin}
					</div>
				{/if}
			{/foreach}
			</div>
			{/if}
		</div>
	{/if}
{/strip}