{strip}
<h2>{$menu[9].title2}</h2>
{$menu[9].descr}
<div class="hr"></div>
<div class="content">
{foreach from=$menu.sub item=m}
	<div>
		<div class="pic"><a href="{$m.url}" class="ajax{if $m.name==$url1} current{/if}"><img src="http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/scripts/{$m.name}.png" width="200" alt="{$m.title}" /></a></div>
		<h4><a href="{$m.url}" class="ajax{if $m.name==$url1} current{/if}">{$m.title}</a></h4>
		{$m.descr}
	</div>
	<div class="hr"></div>
{/foreach}
</div>
<div class="btn_more">
	<a href="?order">{'Order'|l}</a>
</div>
{/strip}