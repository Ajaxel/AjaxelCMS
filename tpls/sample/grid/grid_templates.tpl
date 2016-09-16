{strip}

	{if $list==true}
		
		
		<div class="post">
			<h3 class="title" style="margin-bottom:0px"><a href="?scripts&templates&id={$row.id}">{$row.title}</a></h3>
			<div class="info">
				{assign var='author' value=$Tpl->user($row.userid, 'firstname')}
				<span class="postinfo">{'Date'|Call:'dayCountDown':$row.added} <b>{$author}</b> {if $row.content.comment=='Y'} <a href="{$row.url_open}#comments" title = "{'_comments'|lang}">{'%1 {number=%1,Comment,Comments}'|lang:($row.content.comments|intval)}</a>{/if}{if !$row.bodylist || $row.content.comment=='Y'}{/if}
				</span>
			</div>
			<div class="content no_e form">
				{if $row.size}
				<table cellspacing="0" class="tpl-box">
				<tr><td colspan="2" style="padding:0px 0 8px 0;text-align:center"><a href="javascript:;" onclick="S.G.download('template:{$row.id}');"><img src="/{$smarty.const.HTTP_DIR_TPL}images/download2.png" alt="Download" /></a></td></tr>
				<tr><th>{'File size'|lang}:</th><td>{$row.size}</td></tr>
				<tr><th>{'Last time updated'|lang}:</th><td>{$row.updated}</td></tr>
				<tr><th>{'Downloads'|lang}:</th><td>{$row.downloads}</td></tr>
				</table>
				{else}
					<table cellspacing="0" style="float:right">
					<tr><td colspan="2">File is missing</td></tr>
					</table>
				{/if}
				{if $row.main_photo}
					<div class="pic"><div><a href="?scripts&templates&id={$row.id}" class="ajax"><img width="180" src="{$smarty.const.HTTP_DIR_FILES}grid_{$row.module}/{$row.rid}/th2/{$row.main_photo}" alt="{$row.alt}" border="0" /></a></div></div>
				{/if}
				{$row.descr}<br /><br />
				{$row.body}
				
			</div>
		</div>
		<div class="hr"></div>
	{else}
		
		{include file='content/title.tpl'}

		<div class="content open no_e">
			{if $row.main_photo}
				<div class="image_wrapper"><div class="picwrap" style="background-image:url('{$smarty.const.HTTP_DIR_FILES}grid_{$row.module}/{$row.rid}/th1/{$row.main_photo}')"></div></div>
			{/if}
			{if $row.size}
				<table cellspacing="0" class="tpl-box">
				<tr><td colspan="2" style="padding:0px 0 8px 0;text-align:center"><a href="javascript:;" onclick="S.G.download('template:{$row.id}');"><img src="/{$smarty.const.HTTP_DIR_TPL}images/download2.png" alt="Download" /></a></td></tr>
				<tr><th>{'File size'|lang}:</th><td>{$row.size}</td></tr>
				<tr><th>{'Last time updated'|lang}:</th><td>{$row.updated}</td></tr>
				<tr><th>{'Downloads'|lang}:</th><td>{$row.downloads}</td></tr>
				<tr><td colspan="2"><div style="padding:5px 5px;width:180px;float:right;margin-top:20px">
				{$row.descr}
			</div></td></tr>
				</table>
			{else}
				<table cellspacing="0" style="float:right">
				<tr><td colspan="2">File is missing</td></tr>
				</table>
			{/if}
			<div class="hr" style="background:none;padding-bottom:0"></div>
			{$row.body}
		</div>
		<div class="hr"></div>
		<div class="bottom">
			<div class="back"><a href="?scripts&templates">{'Back to see all templates'|lang}</a></div>
		</div>
	{/if}

{/strip}