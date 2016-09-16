{strip}
{if $row}
	<h2>{if $smarty.get.folder=='SENT'}{'Sent Message:'|lang}{else}{'Inbox Message:'|lang}{/if} {$row.subject}</h2>
	<div style="color:#444;font-size:12px">{'From: %1'|lang:$row.user.login}<br />
	{'Date: %1'|lang:('H:i d.m.Y'|date:$row.added)}</div>
	<div style="padding:10px 0 25px 0;">
	{$row.body_conv}
	</div>
	<h4>{'Send a quick reply'|lang}</h4>
	<form action="?{$URL}" class="form ajax_form" id="compose" method="post">
	<table width="100%" cellpadding="0" cellspacing="0">
		{include file='includes/form_errors.tpl' table=2}
		<tr>
			<th class="l">{'Subject'|lang}: <span style="color:#090;">*</span></th>
			<td><input type="text" class="textbox" name="subject" value="{if $smarty.post.subject}{$smarty.post.subject|strform}{else}Re: {$row.subject}{/if}" style="width:98%" /></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:0">
				<textarea name="body" id="s-text" class="textbox" style="width:99%;height:130px;">{$smarty.post.body|strform}</textarea>
			</td>
		</tr>
		<tr>
			<td class="b" colspan="2">
				<button type="submit" class="reg_button">{'Send'|lang}<span class="x">{'Send'|lang}</span></button>
			</td>
		</tr>
	</table>
	<input type="hidden" name="to" value="{$row.user.login|html}" />
	<input type="hidden" name="submitted" value="compose">
	</form>	
	<br /><br />
	{*<a href="?user&messages&compose={$row.user.login}&re={$row.rid}" class="ajax_link">{'Send a reply'|lang}</a> | *}<a href="?user&messages&folder={$smarty.get.folder|strform}" class="ajax_link">{'Back to my messages'|lang}</a>
{else}
{if $smarty.get.compose}
{if $smarty.get.re}
<h2>{'Send a reply'|lang}</h2>
{else}
<h2>{'Compose new message'|lang}</h2>
{/if}
{elseif $smarty.get.folder=='SENT'}
<h2>{'My Sent Messages'|lang}</h2>
{else}
<h2>{'My Inbox Messages'|lang}</h2>
{/if}
<div class="tabs">
	<a href="?user&messages" class="ajax_link{if $smarty.get.folder!='SENT' && !$smarty.get.compose} bg{/if}">{'Inbox'|lang}</a> <a href="?user&messages&folder=SENT" class="ajax_link{if $smarty.get.folder=='SENT' && !$smarty.get.compose} bg{/if}">{'Sent messages'|lang}</a> <a href="?user&messages&compose=1" class="ajax_link{if $smarty.get.compose} bg{/if}">{'Compose'|lang}</a>
	
</div>
{if $smarty.get.compose}

	<form action="?user&messages&compose=1" class="form ajax_form" id="compose" method="post">
	<table width="100%" cellpadding="0" cellspacing="0">
		{include file='includes/form_errors.tpl' table=2}
		<tr>
			<th class="l">{'To'|lang}: <span style="color:#090;">*</span></th>
			<td>
				{if $smarty.get.re && $smarty.get.compose}
					<div style="font-weight:bold;padding-top:7px;padding-left:10px">{$smarty.get.compose|html}</div>
					<input type="hidden" name="to" value="{$smarty.get.compose|html}" />
				{else}
				<input type="text" class="textbox" name="to" value="{if $smarty.post.to}{$smarty.post.to|strform}{elseif $smarty.get.compose!=1}{$smarty.get.compose|html}{/if}" />
				{/if}
			</td>
		</tr>
		<tr>
			<th class="l">{'Subject'|lang}: <span style="color:#090;">*</span></th>
			<td><input type="text" class="textbox" name="subject" value="{$smarty.post.subject|strform}" style="width:98%" /></td>
		</tr>
		<tr>
			<td colspan="2" style="padding-left:0">
				<textarea name="body" id="s-text" class="textbox" style="width:99%;height:130px;">{$smarty.post.body|strform}</textarea>
			</td>
		</tr>
		<tr>
			<td class="b" colspan="2">
				<button type="submit" class="reg_button">{'Send'|lang}<span class="x">{'Send'|lang}</span></button>
			</td>
		</tr>
	</table>
	<input type="hidden" name="submitted" value="compose">
	</form>

{else}
<form method="post" class="ajax_form" action="?user&messages&folder={$smarty.get.folder|strform}" id="messages">
	<table class="table" cellspacing="0">
		<thead>
		<tr>
			<th class="pl"><input type="checkbox" onclick="if(this.checked) $('.messages_chk').attr('checked','checked'); else $('.messages_chk').removeAttr('checked');" /></th>
			<th><a href="?user&messages&folder={$smarty.get.folder|strform}&sort=from" class="nohov ajax_link{if $smarty.get.sort=='from'} down{/if}">{'Sender'|lang}</a></th>
			<th>{'Subject'|lang}</th>
			<th><a href="?user&messages&folder={$smarty.get.folder|strform}&sort=date" class="nohov ajax_link{if !$smarty.get.sort || $smarty.get.sort=='date'} down{/if}">{'Date'|lang}</a></th>
		</tr>
		</thead>
		<tbody>
		{foreach from=$data.list item=row}
		{assign var='user' value='Data'|Call:'user':$row.from_id:'login,firstname,lastname'}
		<tr class="odd">
			<td class="pl"><input type="checkbox" class="messages_chk" name="delete[]" value="{$row.rid}" /></td>
			<td>{$user.login}</td>
			<td><a href="?user&messages&folder={$smarty.get.folder|strform}&id={$row.rid}" class="ajax_link">{$row.subject}</a></td>
			<td>{'d.m.Y, H:i'|date:$row.added}</td>
		</tr>
		{foreachelse}
		<tr>
			<td colspan="4">{'No messages found'|lang}</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
	<br />
	<input type="submit" value="{'_Delete'|lang}" style="padding:1px 3px;" />
	<input type="hidden" name="submitted" value="delete">
</form>
{/if}
{/if}
{/strip}


