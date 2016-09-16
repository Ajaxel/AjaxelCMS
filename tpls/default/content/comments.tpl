{strip}
<a name="comments"></a>
<div id="comments">
	<div id="cmtswitcher">
		<a onclick="javascript:;" id="commenttab" class="curtab">{'Comments'|lang}</a>
		<div class="fixed"></div>
	</div>
	{assign var='comments' value='Data'|Call:'Comments':$content.id}
	<form method="post" action="{$content.url}" class="ajax_form">
	{if $comments.list}
	<div id="commentlist">
		<ol id="thecomments">		
			{foreach from=$comments.list item=row}
				{include file='content/comment.tpl' row=$row}
			{/foreach}
		</ol>
		<div id="comment_pager">
			{include file='content/pager.tpl' top=false pager=$comments.pager json="?comment_page&amp;content_id=`$content.id`"}
		</div>
	</div>
	{elseif $smarty.const.SITE_TYPE=='print'}
		<div id="commentlist" class="nothing" style="height:10px">
			{'No comments listed'|lang}...
		</div>
	{else}
		<div id="commentlist" class="nothing" style="height:10px">
			{'Be the first to submit new comment'|lang}...
		</div>
	{/if}
	{if $smarty.const.SITE_TYPE!='print'}
	<div id="respond">
		<a name="respond"></a>
		{include file='includes/form_errors.tpl'}
		<div class="row">
			<input name="comment[name]" type="text" value="{if $smarty.post.comment.name}{$smarty.post.comment.name|strform}{else}{$User.Login}{/if}" maxlength="32" id="cName" class="textfield" />
			<label for="cName" class="small">{'Name (required)'|lang}</label>
		</div>
		<div class="row">
			<input name="comment[subject]" type="text" value="{if !$smarty.post.comment.subject}Re: {$content.title}{else}{$smarty.post.comment.subject|strform}{/if}" maxlength="128" id="cSubject" class="textfield" />
			<label for="cSubject" class="small">{'Subject'|lang}</label>
		</div>
		<div class="row">
			<input name="comment[email]" type="text" value="{if $smarty.post.comment.email}{$smarty.post.comment.email|strform}{else}{$User.Email}{/if}" maxlength="128" id="cEmail" class="textfield" />
			<label for="cEmail" class="small">{'E-Mail (will not be published)'|lang}</label>
		</div>
		<div class="row">
			<input name="comment[url]" type="text" maxlength="256" value="{$smarty.post.comment.url|strform}" id="cUrl" class="textfield" />
			<label for="cUrl" class="small">{'Website'|lang}</label>
		</div>
		<div class="row">
			<input id="cRemember" type="checkbox" name="comment[remember]" checked="checked" />
			<label for="cRemember">{'Remember Me?'|lang}</label>
		</div>
		<div class="row">
			<textarea name="comment[body]" rows="5" cols="81" id="cBody">{$smarty.post.comment.body|strform}</textarea>
		</div>
		<div id="submitbox">
			<table width="100%"><tr><td width="10%"><a href="javascript:;" title="Click to see another image" onclick="$(this).children().attr('src','/captcha.php?name=comment&amp;n='+Math.random())"><img src="/captcha.php?name=comment&amp;n={$time}" alt="Captcha" /></a></td><td style="padding-left:10px"><input type="text" class="textfield" id="cCaptcha" style="width:150px" name="comment[captcha]" value="" /></td>
			<td align="right">
			<button type="{if $comments.list}button{else}submit{/if}" onclick="{if $comments.list}S.G.json('?comment', this.form){/if}" name="comment[submit]" id="btnSubmit" class="button">{'Publish Comment'|lang}</button>
			</td></tr></table>
			<div class="fixed"></div>
		</div>
	</div>
	{/if}
<script type="text/javascript">
$(document).ready(function(){
$('#cBody').prettyComments();
});
</script>
	<input type="hidden" name="comment[content_id]" value="{$content.id}" />
	</form>
</div>
{/strip}