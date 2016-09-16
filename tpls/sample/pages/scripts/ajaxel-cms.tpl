{strip}
<h2>{'Ajaxel content management system %1 and framework'|l:('CMS::VERSION'|Func)}</h2>
{'@[ajaxel-cms-i]Very simple ajaxified CMS and framework for any project needs. Edit your website content from backend or front end. Try and see how good this stuff is!'|l}
<br/><br/>
{if $sp}<h4>{'Price: free - 100 eur / license.'|l}</h4>{/if}
{'@[ajaxel-offered]Free is for non-comercial businesses.'|l}
<div class="hr"></div>
{'@ajaxel_intro_text'|l}
<div class="btn_more">
	<a href="http://ajaxel.com/order">{'Order'|l}</a>
	<a href="http://ajaxel.com">{'Download'|l}</a>
	<a href="http://ajaxel.com/readme">{'Readme'|l}</a>
	<a href="http://demo.ajaxel.com/do-login/login-demo/password-1234" target="_blank">{'Try demo!'|l}</a>
	<a href="?scripts&about">{'About AJAX'|l}</a>
</div>
<div class="hr"></div>
<center>
	<iframe width="100%" height="500" src="//www.youtube.com/embed/-5zPNx-Mh6k" frameborder="0" allowfullscreen></iframe>
</center>
{*
<div class="hr"></div>
<h3>{'Ajaxel CMS particular qualities'|lang}</h3>
<table cellspacing="0" class="features">
	{assign var='data' value='Data'|Call:'Grid':'features'}
	{foreach from=$data item=d}
	<tr>
		<td class="ico">
			{if $d.url}<img src="{if $d.url|strpos:'/'}{$d.url}{else}/tpls/img/icons/{$d.url}_48.png{/if}" width="48" />{/if}
		</td>
		<td class="text">
			<h5>{$d.title}</h5>
			{$d.descr}
		</td>
	</tr>
	{/foreach}
</table>
*}
<div class="hr"></div>
{foreach from='Html'|Call:'arrRange':1:20 key=j item=i}
	<div class="image_wrapper image_p"{if $j%2==0} style="margin-right:0"{/if}><a style="background-image:url(http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/ajaxel-cms{if $i>1}{$i}{/if}.png);" title="{'_Ajaxel content management system %1 and framework'|l:('CMS::VERSION'|Func)}" rel="cms" href="http://ajaxel.com/{$smarty.const.HTTP_DIR_TPL}images/slider/o/ajaxel-cms{if $i>1}{$i}{/if}.png" class="colorbox bg"></a></div>

{/foreach}
<div class="hr"></div>
{'@ajaxel-info'|l}

<div class="hr"></div>

<div class="back" style="float:left;position:relative;top:10px"><a href="?scripts">{'Back to other scripts'|lang}</a></div>
<div class="btn_more">
	<a href="http://ajaxel.comorder">{'Order'|l}</a>
	<a href="http://ajaxel.com">{'Download'|l}</a>
	<a href="http://ajaxel.com/readme">{'Readme'|l}</a>
	<a href="http://demo.ajaxel.com/do-login/login-demo/password-1234" target="_blank">{'Try demo!'|l}</a>
	<a href="http://ajaxel.com/scripts&about">{'About AJAX'|l}</a>
</div>
{/strip}