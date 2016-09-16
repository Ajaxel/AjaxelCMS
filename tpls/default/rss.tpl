<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>{'#site_title'|lang}</title>
		<description>{'#site_description'|lang}</description>
		<language>en</language>
		<link>{$smarty.const.HTTP_BASE}</link>
		<image>
			<url>{$smarty.const.HTTP_BASE}{$smarty.const.HTTP_DIR_TPL}images/logo.png</url>
			<width>490</width>
			<height>59</height>
			<title>{'#site_title_short'|lang}</title>
			<link>{$smarty.const.HTTP_BASE}</link>
		</image>
		<lastBuildDate>{'Date'|Call:'pubDate':0}</lastBuildDate>
		<copyright>{'#site_title_short'|lang}</copyright>
		<ttl>25</ttl>
{foreach from=$data item=item}
		<item>
			<title>{$item.title|htmlspecialchars}</title>
			<description>{if $item.descr}{$item.descr|rssBody|htmlspecialchars}{else}{$item.body|rssBody|htmlspecialchars}{/if}</description>
			<link>{$smarty.const.HTTP_BASE}{$item.url_open|htmlspecialchars|trim:'/'}</link>
			<pubDate>{'Date'|Call:'pubDate':$item.added}</pubDate>
			<guid isPermaLink="true">{$smarty.const.HTTP_BASE}{$item.url_open|htmlspecialchars|trim:'/'}</guid>
		</item>
{/foreach}
	</channel>
</rss>