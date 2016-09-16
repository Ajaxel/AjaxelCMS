{strip}

<div class="forum">
{if $data.type=='categories'}
	<h2>{'Forum categories'|lang}</h2>
	<table cellspacing="0" cellpadding="0" class="table">
		<tbody>
			
			<tr class="head">
				<td class="a">{'Category'|lang}</td>
				<td width="5%">{'Threads'|lang}</td>
				<td width="5%" class="b">{'Posts'|lang}</td>
			</tr>
			{foreach from=$data.list key=i item=d}
			<tr class="row{if $i%2} odd{/if}">
				<td class="a">
					<a href="?forum&category={$d.id}" class="ajax_link">{$d.title}</a>
					<div class="descr">{$d.descr}</div>
				</td>
				<td{if !$d.threads} style="color:#777"{/if}>
					{$d.threads}
				</td>
				<td class="b"{if !$d.posts} style="color:#777"{/if}>
					{$d.posts}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	
	
{elseif $data.type=='threads'}

	{if $reply}
		<h2>{'Add new thread to %1'|lang:$cat.title}</h2>
		<div class="bread">
			<a href="?forum" class="ajax_link">Home</a> :: {$cat.title}
		</div>
		
		{if $User.UserID}
			<table cellspacing="0" cellpadding="0" class="form">
			<tbody>
				
				<tr><td>
					<form method="post" action="?{$URL}" class="ajax_form center-area">
					{include file='includes/form_errors.tpl'}
					<h3>{'New thread:'|lang}</h3>
					<input class="textbox" name="title" onfocus="if(this.value=='{'_Thread title'|lang}'){ this.value='';this.style.color='#000'}" onblur="if (this.value=='') { this.value='{'_Thread title'|lang}';this.style.color='#888' }" style="{if !$smarty.post.title}color:#888;{/if}width:96%;margin:10px 1% 4px 1%;padding:5px 1%;font-family:'Lucida Console'" value="{if $smarty.post.title}{$smarty.post.title|strform}{else}{'_Thread title'|lang}{/if}" />
					<textarea class="textbox" name="message" onfocus="if(this.value=='{'_Thread message'|lang}'){ this.value='';this.style.color='#000'}" onblur="if (this.value=='') { this.value='{'_Thread message'|lang}';this.style.color='#888' }" style="{if !$smarty.post.message}color:#888;{/if}width:96%;margin:4px 1% 10px 1%;height:280px;padding:5px 1%;font-family:'Lucida Console'">{if $smarty.post.message}{$smarty.post.message|strform}{else}{'_Thread message'|lang}{/if}</textarea>
					<div class="b">
					<button type="submit" class="btn">{'Submit'|lang}</button>
					</div>
					</form>
				</td></tr>
			</tbody>
		</table>
		{else}
			<div class="please_login">
			<a href="?user&login&jump=forum/category-{$cat.id}/reply" class="ajax_link">{'You need to login in order to start new thread in our forum'|lang}</a>
			</div>
		{/if}
		<div class="bottom">
		<a href="?forum&category={$cat.id}" class="ajax_link">&lt;&lt; Cancel</a>
		</div>
	{else}
		
		<h2>{$cat.title}</h2>
		<div class="bread">
			<a href="?forum" class="ajax_link">Home</a> :: {$cat.title}
			<a href="?forum&category={$cat.id}&reply" style="float:right" class="ajax_link">{'Add new thread'|lang}</a>
		</div>
		<table cellspacing="0" cellpadding="0" class="table">
			<tbody>
				
				<tr class="head">
					<td class="a">Threads</td>
					<td width="10%">Date</td>
					<td width="5%">Posts</td>
				</tr>
				{foreach from=$data.list key=i item=d}
				<tr class="row{if $i%2} odd{/if}">
					<td class="a">
						<a href="?forum&thread={$d.id}" class="ajax_link">{$d.title}</a>
						<div class="descr">{($d.descr|strip_tags)|trunc:300:1:1}</div>
						{if $smarty.const.IS_ADMIN}
							<div class="admin_action">
								<a href="javascript:;" onclick="if(confirm('Are you sure to delete this thread?')) S.G.get('?forum&category={$cat.id}&thread={$d.id}&delete',0,1);">{'Delete this thread'|lang}</a>
							</div>
						{/if}
					</td>
					<td>{'Date'|Call:'dayCountDown':$d.dated}</td>
					<td{if !$d.posts} style="color:#777"{/if}>
						{$d.posts}
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>

		<div class="bottom">
		<a href="?forum" class="ajax_link">&lt;&lt; {'Back to forum home'|lang}</a>
		<a href="?forum&category={$cat.id}&reply" style="float:right" class="ajax_link">{'Add new thread'|lang}</a>
		</div>
	{/if}
{elseif $data.type=='posts'}

	{if $reply}
		<h2>{'Reply to %1'|lang:$thread.title}</h2>
		<div class="bread">
			<a href="?forum" class="ajax_link">Home</a> :: <a href="?forum&category={$cat.id}" class="ajax_link">{$cat.title}</a> :: <a href="?forum&category={$cat.id}&thread={$thread.id}" class="ajax_link">{$thread.title}</a> :: Reply
		</div>
		
		<table cellspacing="0" cellpadding="0" class="table">
			<tbody>
				
				<tr class="thread">
					<td class="a" colspan="2">
						<div class="thread_title">{$thread.title}</div>
						<div class="thread_descr">
							<div class="thread_date">Posted {'Date'|Call:'dayCountDown':$thread.dated} by <b>{$thread.user}</b></div>
							{$thread.descr}
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		{if $User.UserID}
			<form method="post" action="?{$URL}" class="ajax form center-area">
			{include file='includes/form_errors.tpl'}
			<h3 style="text-align:left;margin-top:10px">{'Your reply:'|lang}</h3>
			<textarea class="textbox" name="message" onfocus="if(this.value=='{'_Message'|lang}'){ this.value='';this.style.color='#000'}" onblur="if (this.value=='') { this.value='{'_Message'|lang}';this.style.color='#888' }" style="{if !$smarty.post.message}color:#888;{/if}width:96%;margin:4px 1% 10px 1%;height:280px;padding:5px 1%;font-family:'Lucida Console'">{if $smarty.post.message}{$smarty.post.message|strform}{else}{'_Message'|lang}{/if}</textarea>
			<div class="b">
			<button type="submit" class="btn">{'Submit'|lang}</button>
			</div>
			</form>
		{else}
			<div class="please_login">
				<a href="?user&login&jump=forum/category-{$cat.id}/thread-{$thread.id}/reply" class="ajax_link">{'You need to login in order to write on our forum'|lang}</a>
			</div>
		{/if}

		<div class="bottom">
		<a href="?forum&category={$cat.id}&thread={$thread.id}" class="ajax_link">&lt;&lt; Cancel</a>
		</div>
	{else}
		<h2>{$thread.title}</h2>
		{if $smarty.const.IS_ADMIN}
			<div class="admin_action">
				<a href="javascript:;" onclick="if(confirm('Are you sure to delete this thread?')) S.G.get('?forum&category={$cat.id}&thread={$thread.id}&delete',0,1);">{'Delete this thread'|lang}</a>
			</div>
		{/if}
		<div class="bread">
			<a href="?forum" class="ajax_link">Home</a> :: <a href="?forum&category={$cat.id}" class="ajax_link">{$cat.title}</a> :: {$thread.title}
			<a href="?forum&category={$cat.id}&thread={$thread.id}&reply" style="float:right" class="ajax_link">Reply</a>
		</div>

		<table cellspacing="0" cellpadding="0" class="table">
			<tbody>
				<tr class="thread">
					<td class="a" colspan="2">
						<div class="thread_title">{$thread.title}</div>
						<div class="thread_descr">
							<div class="thread_date">Posted {'Date'|Call:'dayCountDown':$thread.dated} by <b>{$thread.user}</b></div>
							{$thread.descr}
						</div>
					</td>
				</tr>
				{foreach from=$data.list key=i item=d}
				
				<tr class="row{if $i%2} odd{/if}">
					<td style="text-align:left;width:15%" class="post_author">
						<b style="font-size:13px">{$d.user}</b><br />
						<span style="font-size:13px">{'Date'|Call:'dayCountDown':$d.added}</span>
						{if $smarty.const.IS_ADMIN}
							<div class="admin_action">
								<a href="javascript:;" onclick="if(confirm('Are you sure to delete this post?')) S.G.get('?forum&category={$cat.id}&thread={$thread.id}&del={$d.id}',0,1);">{'Delete this post'|lang}</a>
							</div>
						{/if}
					</td>
					<td style="text-align:left" class="post_descr">
						{$d.descr}
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
		<div class="bottom">
		<a href="?forum&category={$cat.id}" class="ajax_link">&lt;&lt; Back to {$cat.title}</a>
		<a href="?forum&category={$cat.id}&thread={$thread.id}&reply" style="float:right" class="ajax_link">Reply</a>
		</div>
	{/if}
{/if}
{include file='content/pager.tpl' pager=$data.pager}
</div>
{/strip}