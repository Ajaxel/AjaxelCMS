{strip}
<h2>{'Portfolio, the working websites made with Ajaxel CMS'|lang}</h2>
<div class="content">{'@portfolio text'|l}</div>
<div class="hr"></div>
{*
<ul class="portfolio">
{foreach from=$portfolio key=i item=row}
	<li{if $i%2} class="odd"{/if}>
		<div class="t">{$row.url}</div>
		<div class="p"><a href="{$row.url}" target="_blank" rel="nofollow" style="background-image:url('{$row.pic|replace:'/th/':'/mid/'}')" alt="{$row.url}"><img src="{$row.pic|replace:'/th/':'/mid/'}" style="display:none" alt="{$row.url}"></a></div>
	</li>
{/foreach}
</ul>
*}
{assign var='i' value=1}
{foreach from=$portfolio item=row}
	<div class="image_wrapper image_p"{if $i%2==0} style="margin-right:0"{/if}>
		<div class="t">{$row.url}</div>
		<a style="background-image:url('{$row.pic|replace:'/th/':'/mid/'}')" title="{$row.url}" rel="portfolio" href="{$row.url}" class="bg" target="_blank" rel="nofollow"></a>
	</div>
	{assign var='i' value=$i+1}
{/foreach}


<div class="hr"></div>
<div class="btn_more">
	<a href="?order">{'Order'|l}</a>
</div>
{strip}