{include file="`$tpl_folder`im/p_top.tpl" title='Send an abuse'}

<div class="im_text">
	<form method="post" action="">
	<table>
	<tr><th>{'To abuse:'|lang}</th><td>{$data.user.login}</td></tr>
	<tr><th>{'Abuse reason:'|lang}</th><td><select style="width:98%" name="data[reason]">{'Data'|Call:'getArray':'im_abuse':0}</select></td></tr>
	<tr><th>{'Description:'|lang}</th><td><textarea name="data[descr]" style="width:97%;height:100px"></textarea></td></tr>
	<tr><th>&nbsp;</th><td><button type="button" class="im_button" onclick="S.IM.sendAbuse(this.form)">{'Send'|lang}</button></td></tr>
	</table>
	<input type="hidden" name="{$smarty.const.URL_KEY_ACTION}" value="abuse" />
	</form>
</div>