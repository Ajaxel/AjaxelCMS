{strip}
<h2>{'Ajaxel Slots Machine'|l}</h2>
{'@[ajaxel-slots]I have spent 2 weeks making this game for my customer. At beginning he wanted to have this 1x3, but I made 5x3 with animations, double up option, like the real slots game in casino. <a href="?slots">Check it out</a> and see how all works!'|l}
<br/>
{if $sp}<h4>{'Price: 200 eur.'|l}</h4>{/if}

<div class="hr"></div>
{foreach from='Html'|Call:'arrRange':1:3 key=j item=i}
	<div class="image_wrapper image_p"{if $j%2==0} style="margin-right:0"{/if}><a style="background-image:url(http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/ajaxel-slots{if $i>1}{$i}{/if}.png);" title="{'_Ajaxel Slots Machine'|l}}" rel="cms" href="http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/ajaxel-slots{if $i>1}{$i}{/if}.png" class="colorbox bg"></a></div>

{/foreach}
<div class="hr"></div>



<div class="bottom">
	<div class="back" style="float:left;position:relative;top:10px"><a href="?scripts">{'Back to other scripts'|lang}</a></div>
</div>
<div class="btn_more">
	<a href="?order" class="ajax">{'Order'|l}</a>
	<a href="?slots" class="ajax">{'Play now :)'|l}</a>
</div>
{/strip}