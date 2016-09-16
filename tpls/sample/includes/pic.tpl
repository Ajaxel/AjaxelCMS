{strip}
<div class="p{if $f.main} main{/if}">
	{if 'File'|Call:'isPicture':$f.file}
		<div class="pic"><a href="{$f.dir}{$f.file}?t={$time}" class="colorbox" rel="gal" style="background-image:url('{$f.dir|replace:'/th1/':$f.th}{$f.file}?t={$time}')" /></a></div>
	{else}
		<div class="pic"><a href="{$f.dir}{$f.file}?t={$time}" target="_blank" rel="gal" title="{$f.file}" style="background-image:url('/{$f.ext_file|replace:'/16/':'/130/'}')" /></a></div>
	{/if}
	<div class="c">
		<div class="name">{$f.file}</div>
		<div class="edit">
			<a href="javascript:;" onclick="S.G.delFile('{$f.id}','{$f.file}','{$f.name}',this.parentNode.parentNode.parentNode)">{'Delete'|lang}</a>
		</div>
	</div>
</div>
{/strip}