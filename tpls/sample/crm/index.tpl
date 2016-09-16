{strip}
<script type="text/javascript">
$(function(){
CRM.config({$config|json},'{$name}');
{if $conf.user}
CRM.data.users={'Data'|Call:'getArray':'my:crm_users'|json};
CRM.data.activities={'Data'|Call:'getArray':'my:activities'|json};
CRM.data.interests={'Data'|Call:'getArray':'my:interests'|json};
CRM.data.interests_cnt={'Data'|Call:'getArray':'my:interests_cnt'|json};
CRM.data.status_clients={'Data'|Call:'getArray':'my:status_clients'|json};
CRM.data.status_tasks={'Data'|Call:'getArray':'my:status_tasks'|json};
CRM.data.projects={'Data'|Call:'getArray':'my:projects'|json_encode};
CRM.data.access={'Data'|Call:'getArray':'my:access'|json};
CRM.data.countries={'Data'|Call:'getArray':'my:countries'|json};
CRM.data.languages={'Data'|Call:'getArray':'my:languages'|json};
{if $name!='undefined'}
CRM.conf.statuses={$conf.statuses|json};
CRM.conf.status_classes={$conf.status_classes|json};
CRM.conf.accesses={$conf.accesses|json};
{/if}
{/if}
});
</script>
<body class="{$smarty.const.SITE_TYPE}">
<div id="loading">Loading...</div>
<div id="wrapper" class="ui-widget-content" style="display:none;">
<div id="c-tabs" class="ui-widget-content">
	<ul id="c-tabs-ul">
		<li class="c-logo" onClick="window.location.href='{$smarty.const.HTTP_EXT}?crm'+(CRM.name?'&'+CRM.name:'')">{'#site_title_short'|lang} CRM</li>
		{foreach from=$config.tabs key=i item=a}
		<li><a href="{if $name==$a[0]}#c-{$a[0]}_tab{else}?crm&{$a[0]}{/if}" class="no_ajax">{$a[2]}</a></li>
		{/foreach}
		{if $User.UserID}
		<li><button class="c-button" onClick="location.href='?crm&logout'" style="font-size:10px!important;margin-top:4px!important">LOGOUT</button></li>
		{/if}
		<li class="c-add"><a href="#c-{$name}" id="c-add"><img src="/tpls/img/oxygen/16x16/actions/list-add.png" /> ADD</a></li>
		<li class="c-search" id="c-search">
			<form method="post">
			<table><tr><td>
			<input type="text" name="f[search]" id="c-global-search" class="c-input big" value="{$smarty.get.search|strform}" />
			</td><td><button type="submit" class="c-button">FIND</button></td></tr></table>
			</form>
		</li>
	</ul>
	<div id="c-{$name}_tab">
		{include file='crm/inc/tab_top.tpl'}
		{if $conf.user}
			{include file="crm/`$tpl_file`.tpl"}
		{else}
			{include file='crm/login.tpl'}
		{/if}
		{include file='crm/inc/tab_bot.tpl'}
	</div>
</div>

<div id="c-footer" class="ui-state-default c-unsel">
	<table>
	<td class="c-1">
		{if !$User.UserID}
			Welcome guest
		{else}
			Logged as {$User.Login}
		{/if}
	</td>
	<td class="c-2">&nbsp;
		
	</td>
	<td class="c-3">
		Copyright <a href="http://ajaxel.com/" target="_blank">Ajaxel CRM</a> &copy; 2011
	</td>
	</tr></table>
</div>

{/strip}