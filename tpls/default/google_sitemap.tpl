<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{foreach from=$data item=item}
	<url>
		<loc>{$smarty.const.HTTP_BASE}{$item.url_open|trim:'/'}</loc>
		<lastmod>{if $item.edited}{'Y-m-d'|date:$item.edited}{else}{'Y-m-d'|date:$item.edited}{/if}</lastmod>
		<changefreq>weekly</changefreq>
		{*<priority>0.4</priority>*}
	</url>
{/foreach}
</urlset>