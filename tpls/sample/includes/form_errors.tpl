{strip}
{if $table && ($form_errors || $top_message)}<tr><td colspan="{if $table==1}2{else}{$table}{/if}" style="padding:0;padding-bottom:2px;">{/if}
{if $form_errors.text}
	<table class="report {if $form_errors.type=='tick' || $form_errors.type=='success'}ok{else}bad{/if}"><tr><td>
	<label>{$form_errors.text}</label>
	</td></tr></table>
{elseif $form_errors}
	<table class="report bad"><tr><td>
		{foreach from=$form_errors key=name item=msg}
			<div class="li">{$msg}</label>
		{/foreach}
	</td></tr></table>
{elseif $top_message}
	<table class="report {if $top_message.type=='success'}ok{else}bad{/if}"><tr><td>
	<div class="li">{$top_message.text}</div>
	</td></tr></table>
{/if}
{if $table && ($form_errors || $top_message)}</td></tr>{/if}	
{/strip}