{strip}
<h2>{'TimeMonkey - Efficient time management web-based'|l}</h2>
{'@[timemonkey-text]This is long time ago good project, year 2009th. Giving for free, if someone needs it.'|l}

<div class="hr"></div>
{foreach from='Html'|Call:'arrRange':1:4 key=j item=i}
	<div class="image_wrapper image_p"{if $j%2==0} style="margin-right:0"{/if}><a style="background-image:url(http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/timemonkey{if $i>1}{$i}{/if}.png);" title="{'_TimeMonkey - Efficient time management web-based'|l}}" rel="cms" href="http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/timemonkey{if $i>1}{$i}{/if}.png" class="colorbox bg"></a></div>

{/foreach}
<div class="hr"></div>

<div class="back" style="float:left;position:relative;top:10px"><a href="?scripts">{'Back to other scripts'|lang}</a></div>
<div class="btn_more">
	<a href="javascript:;" onclick="S.G.download('timemonkey')">{'Download'|l}</a>
	<a href="http://time.ajaxel.com" target="_blank">{'Try now'|l}</a>
</div>
{/strip}