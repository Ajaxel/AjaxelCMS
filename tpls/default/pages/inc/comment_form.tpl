{strip}
{assign var='comments' value=$Tpl->Index->My->comments($row.id,$row.table)}
<div class="form">
<h3 class="x">
	{'Leave your feedback about %1'|lang:$row.alt}:<span class="x">{'Leave your feedback about %1'|lang:$row.alt}:</span>
</h3>
{if $User.UserID}<form method="post" id="comment_form" class="ajax_form no_scroll" action="?{$URL}">{/if}
{if $User.UserID}
	<table class="details">
		{include file='includes/form_errors.tpl' table=1 form_errors=$comments.form_errors}
		<tr>
			<td class="col2">
				{'Did you like it?:'|lang}&nbsp;
				<label><input type="radio" name="comment[rate]" value="+1"{if $smarty.post.comment.rate=='+1'} checked{/if} /> <img src="/{$smarty.const.HTTP_DIR_TPL}images/icon_vote_for.png" alt="" /></label>
				<label><input type="radio" name="comment[rate]" value="-1"{if $smarty.post.comment.rate=='-1'} checked{/if} /> <img src="/{$smarty.const.HTTP_DIR_TPL}images/icon_vote_against.png" alt="" /></label>
			</td>
		</tr>
		<tr>
			<td>
				<textarea name="comment[comment]" rows="5" cols="20" style="width:465px;height:77px" class="textbox">{$smarty.post.comment.comment|strform}</textarea>
			</td>
		</tr>
		<tr>
			<td style="padding:5px;text-align:center">
				<button type="submit" class="small_button x" onClick="">{'_Send feedback'|lang}<span class="x">{'Send feedback'|lang}</span></button>
			</td>
		</tr>
	</table>
{else}
	<div class="please_login">
		<a href="?user&login" class="ajax_link">{'You need to login in order to write comments'|lang}</a>
	</div>
{/if}
{include file='pages/inc/comments.tpl' comments=$comments}
{if $User.UserID}
<input type="hidden" name="submitted" value="comment" />
</form>
{/if}
</div>
{/strip}