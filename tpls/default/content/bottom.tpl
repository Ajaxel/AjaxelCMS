{strip}
{if $content.is_open && $content.comment=='Y'}
	{include file='content/comments.tpl' content_id=$content_id}
{/if}
{if $menu[0].name=='home' && $content.id}
	<div class="bottom">
		<div class="back"><a href="?home">{'Back to Home'|lang}</a></div>
	</div>
{elseif $page.name_back}
	<div class="bottom">		
		<div class="back"><a href="{$page.url_back}">
			{if $page.type=='sitemap'}
				{'_Back to sitemap'|lang}
			{elseif $page.type=='tag'}
				{'_Back to tag: %1'|lang:$page.name_back|strform}
			{elseif $page.type=='search'}
				{'_Back to %1'|lang:$page.name_back}
			{else}
				{'_Back to %1'|lang:$page.name_back}
			{/if}
		</a>
		</div>
		<div class="printbutton"><a href="javascript:printwin('?print&amp;{'Url'|call:'get'|trim:'?'}')">{'Print page'|lang}</a></div>
		{*<div class="addcomment"><a href="#">{'Send to friend'|lang}</a></div>*}
		{*<div class="addcomment"><a href="?print&{'Url'|call:'get'}&amp;comment=true">{'Add comment'|lang}</a></div>*}
	</div>
{/if}
{/strip}