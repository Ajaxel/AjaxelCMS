{strip}
{assign var='comments' value=$Tpl->Index->My->comments($row.content.rid,$row.content.table)}
<div class="hr"></div>
<div class="form">
<h5>{'Comment something:'|lang}</h5>
{if $User.UserID}
<form method="post" id="comment_form" class="ajax_form no_scroll" action="?{$URL}">
	<table class="details">
		{include file='includes/form_errors.tpl' table=2 form_errors=$comments.form_errors}
		<tr>
			<td colspan="2">
				<textarea name="comment[comment]" rows="5" cols="20" style="width:465px;height:77px" class="textbox">{$smarty.post.comment.comment|strform}</textarea>
			</td>
		</tr>
		<tr>
			<td class="no_e" style="line-height:32px">
				{'Did you like it?:'|lang}
				<a href="javascript:;" class="v-up" id="v-up" onclick="$('#v-down').removeClass('active');$(this).addClass('active');$('#comment-rate').val(1)"></a>
				<a href="javascript:;" class="v-down" id="v-down" onclick="$('#v-up').removeClass('active');$(this).addClass('active');$('#comment-rate').val(-1)"></a>
			</td>
			<td style="padding:5px;text-align:center">
				<button type="submit" class="btn">{'Comment!'|lang}</button>
			</td>
		</tr>
	</table>
{else}
	<div class="please_login">
		<a href="?user&login" class="ajax">{'Please login to leave your comments'|lang}</a>
	</div>
{/if}
{include file='pages/inc/comments.tpl' comments=$comments}
{if $User.UserID}
<input type="hidden" name="comment[rate]" id="comment-rate" value="" />
<input type="hidden" name="submitted" value="comment" />
</form>
{/if}
</div>
{/strip}