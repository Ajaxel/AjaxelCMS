{assign var='photos' value='Data'|Call:'photo':$user.UserID}
{foreach from=$photos item=p}
{assign var='file' value='files/user/'|cat:$user.UserID|cat:'/th1/'|cat:$p}
{assign var='size' value=$file|@getimagesize}
<dl>
	<dt><div class="img"><div style="overflow:hidden;width:112px;height:83px;"><a href="javascript:;" class="show-photo" alt="{$size[0]}x{$size[1]}"><img src="/files/user/{$user.UserID}/th3/{$p}" alt=""></a></div></div></dt>
	<dd>{'File'|Call:'nameonly':$p|replace:'_':' '}</dd>
	<dd><a href="javascript:;" class="delete-photo">{'Удалить'|lang}</a></dd>
</dl>
{/foreach}
