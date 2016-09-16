{strip}
<form method="post" action="" id="c-{$win_id}_win" class="c-form" style="width:{$conf.window_width}px">
<input type="hidden" name="id" value="{$row.id}" />
<input type="hidden" name="act" id="c-{$win_id}_win-act" value="" />
<input type="hidden" name="table" value="{$smarty.post.table}" />
<input type="hidden" name="diff_id" value="{$diff_id}" />
<input type="hidden" name="diff_name" value="{$diff_name}" />
<table class="c-form ui-widget ui-corner-all ui-widget ui-resizable">
	<thead>
		<tr>
			<td colspan="4" class="c-win-top ui-widget-header ui-corner-top">
				<div class="l">
					{$title}
				</div>
				<div class="r">
					{*<a href="javascript:;" onclick="CRM.min()" class="c-win_min"></a>
					<a href="javascript:;" onclick="CRM.max()" class="c-win_max"></a>*}
					<a href="javascript:;" onclick="CRM.close()" title="[alt+q]" class="c-win_close"></a>
				</div>
			</td>
		</tr>
	</thead>
	<tbody class="ui-widget ui-widget-content">
{/strip}	