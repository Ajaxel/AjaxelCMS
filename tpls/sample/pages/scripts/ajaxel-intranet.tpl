{strip}
<h2>{'Ajaxel intranet system'|l}</h2>
{'@[ajaxel-intranet-system]This system is useful for receiving incoming orders from remote sites, where visitors submit a form and system processes data to intranet system.
There administrators can track all the orders, make overdues, reminders, send E-mails or SMS-es to people that have ordered. System also accepts payments from several bank exports.'|l}
<br/>
{if $sp}<h4>{'Price: 500 eur.'|l}</h4>{/if}


<div class="hr"></div>
{foreach from='Html'|Call:'arrRange':1:6 key=j item=i}
	<div class="image_wrapper image_p"{if $j%2==0} style="margin-right:0"{/if}><a style="background-image:url(http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/ajaxel-intranet{if $i>1}{$i}{/if}.png?v=2);" title="{'_Ajaxel intranet system'|l}}" rel="cms" href="http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/ajaxel-intranet{if $i>1}{$i}{/if}.png" class="colorbox bg"></a></div>

{/foreach}
<div class="hr"></div>

<div class="bottom">
	<div class="back" style="float:left;position:relative;top:10px"><a href="?scripts">{'Back to other scripts'|lang}</a></div>
</div>
<div class="btn_more">
	<a href="?order" class="ajax">{'Order'|l}</a>
	<a href="?contact" class="ajax">{'Contact'|l}</a>
</div>
{/strip}