{strip}
{if $online}
	<div class="im_online"><img src="/{$smarty.const.FTP_EXT}tpls/img/online.gif" alt="{'User is online'|lang}" title="{'User is online'|lang}" width="50" height="15" /></div>
	{if $here}
		<div class="im_is im_ishere">{'%1 is talking to you'|lang:$data.user.login}</div>
	{else}
		<div class="im_is im_isout">{'%1 is away'|lang:$data.user.login}</div>
	{/if}
{else}
	<img src="/{$smarty.const.FTP_EXT}tpls/img/offline.gif" alt="{'User is offline'|lang}" title="{'User is offline'|lang}" width="50" height="15" />
	{if $userleft}
		{assign var='word_left' value='Date'|Call:'dayCountDown':$userleft}
		<div class="im_offline"> {'user was %1'|lang:$word_left}</div>
	{/if}
{/if}
{/strip}