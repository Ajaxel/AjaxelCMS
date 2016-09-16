{strip}
<table cellspacing="0" cellapdding="2" width="400">
<tr>
	<td rowspan="2" width="1%">
		<div style="width:70px;overflow:hidden;text-align:center">
			{if $media=='image'}
			<a href="javascript:;" class="show-photo"><img src="/th.php?p={$dir}{$User.UserID}/{$folder}/{$f.file}&w=70" class="{ w:'{$f.width}',h:'{$f.height}',p:'{if $folder=='th3'}/{$dir}{$User.UserID}/th1/{$f.file}{else}/{$dir}{$User.UserID}/{$folder}/{$f.file}{/if}' }" alt="" /></a>
			{else}	
			<img src="/tpls/img/oxygen/32x32/mimetypes/{$f.icon}" alt="{$f.mime}">
			{/if}
		</div>
	</td>
	<td>
		<a href="/{$dir}{$User.UserID}/{if $folder=='th3'}th1{else}{$folder}{/if}/{$f.file}" target="_blank">{$f.file}</a>
	</td>
</tr>
<tr>
	<td>{'File'|Call:'display_size':$f.size} {$f.mime}
	<div style="text-align:right"><a href="javascript:;" onclick="I.delUserFile('{$f.file}','{$folder}',this);">delete</a></div>
	</td>
</tr>
</table>
{/strip}