{strip}
{if $content.is_open && $content.comment=='Y'}
	{include file='pages/inc/comment_form.tpl'}
{/if}
{if $menu[0].name=='home' && $content.id}
	<div class="hr"></div>
	<div class="bottom">
		<div class="back"><a href="?home" onclick="S.G.get('?home');return false;">{'Back to Home'|lang}</a></div>
	</div>
{elseif $page.name_back}
	<div class="hr"></div>
	<div class="bottom">		
		<div class="back"><a href="{$page.url_back}" class="ajax_link">
			{if $page.type=='sitemap'}
				{'Back to sitemap'|lang}
			{elseif $page.type=='tag'}
				{'Back to tag: %1'|lang:$page.name_back|strform}
			{elseif $page.type=='search'}
				{'Back to %1'|lang:$page.name_back}
			{else}
				{'Back to %1'|lang:$page.name_back}
			{/if}
		</a>
		</div>
	</div>
{/if}
{/strip}