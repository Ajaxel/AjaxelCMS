{strip}
<table>
<tr class="im_title">
	<td id="im_top_{$data.to}">
		<div class="im_wrap">
			<div class="im_left">{'Chat with'|lang} {$data.user.login}{if $data.user.age} ({$data.user.age}){/if}</div>
			<div class="im_right"><a href="javascript:;" class="im_close" onclick="S.IM.close('{$data.to}')"><img src="/{$smarty.const.FTP_EXT}tpls/img/close.gif" alt="Close" /></a></div>
		</div>
	</td>
</tr>
<tr class="im_top">
	<td>
		<div class="im_wrap">
			<div class="im_pic">
				{if $data.user.pic}
					<img src="/{$smarty.const.FTP_EXT}{$data.user.pic.th3}" alt="{$data.user.login}{if $data.user.age} ({$data.user.age}){/if}" />
				{else}
					<img src="/{$smarty.const.FTP_EXT}tpls/img/no-photo.png" alt="No photo" />
				{/if}
			</div>
			<div class="im_info" style="width:auto">
				<div class="im_first">
					{if $data.user.genre}
					<img src="/{$smarty.const.FTP_EXT}tpls/img/{if $data.user.genre=='F'}female{else}male{/if}.png" alt="{if $data.user.genre=='F'}{'Female'|lang}{else}{'Male'|lang}{/if}" title="{if $data.user.genre=='F'}{'Female'|lang}{else}{'Male'|lang}{/if}" width="16" height="16" /> {/if}{if $data.user.city}{$data.user.city}, {/if}{$data.user.countryname}
				</div>
				<div id="im_status_{$data.to}" class="im_second">
					{include file="`$tpl_folder`im/win_status.tpl" online=$data.user.online here=$im_connect[1].win userleft=$data.user.user_left}
				</div>
			</div>
			{*
			<div class="im_banner">
				banner 120x60
			</div>
			*}
		</div>
	</td>
</tr>
<tr class="im_list">
	<td>
		<ul id="im_list_{$data.to}"></ul>
	</td>
</tr>
<tr class="im_middle">
	<td>
		<div class="im_wrap">
			<div class="im_bbcode"></div>
			<div class="im_typing" id="im_typing_{$data.to}"></div>
		</div>
	</td>
</tr>
<tr class="im_input">
	<td>
		<div class="im_wrap">
			<textarea id="im_text_{$data.to}" class="im_text"></textarea>
		</div>
	</td>
</tr>
<tr class="im_buttons">
	<td>
		<div class="im_wrap">
			<div class="im_send">
				<button type="button" title="(Ctrl + Enter)" onclick="S.IM.send('{$data.to}', this)">{'Send'|lang}</button>
			</div>
			<div class="im_block">
				{if $Tpl->im('html', 'block_allow', $data.user)}
					{if $im_connect[1].folder==3}
						<a href="javascript:;" onclick="S.IM.unblock('{$data.to}', this)"><img src="/{$smarty.const.FTP_EXT}tpls/img/oxygen/16x16/actions/edit-redo.png" alt="{'Unblock?'|lang}" title="{'Unblock?'|lang}" /></a>
					{else}
						<a href="javascript:;" onclick="S.IM.block('{$data.to}', this)"><img src="/{$smarty.const.FTP_EXT}tpls/img/oxygen/16x16/actions/no.png" alt="{'Block?'|lang}" title="{'Block?'|lang}" /></a>
					{/if}
					<a href="javascript:;" style="position:relative;top:1px" onclick="S.IM.abuse('{$data.to}', this)">{'Send an abuse'|lang}</a>
				{/if}
			</div>
		</div>
	</td>
</tr>
</table>
{/strip}