{strip}
<h2>
	{'My adverts'|lang}
	<div class="r">
		<a href="?user&add" class="ajax_link small_button">{'Add new'|lang}<span class="x">{'_Add new'|lang}</span></a>
	</div>
</h2>
{include file='includes/form_errors.tpl' table=false}
{foreach from=$data.list item=row}
<div class="row">
	<div class="pic">
		<div class="img">
			{if $row.photo}<a href="?open={$row.id}" class="ajax_link" style="background-image:url({$row.photo})"></a>{/if}
		</div>
	</div>
	<div class="text">
		<table cellspacing="0">
		<tr>
			<td>
				<a href="?open={$row.id}" class="title ajax_link">{$row.title}</a>
				<a href="?user&edit={$row.id}" class="action ajax_link">{'edit'|lang}</a> <a href="javascript:;" onclick="if(confirm('{'_Are you sure to delete this advertisement?'|lang}')) S.G.get('?user&ads&delete={$row.id}', false, true)" class="action">{'delete'|lang}</a>
			</td>
			<td class="price">{$row.price}€/{$row.price_type}</td>
		</tr>
		<tr>
			<td colspan="2" class="text">
				{$row.descr}
			</td>
		</tr>
		<tr>
			<td colspan="2" class="foot">
				<div class="l">{'Date'|Call:'dayCountDown':$row.added}</div>
				<div class="r">{$row.location}</div>
			</td>
		</tr>
		</table>
	</div>
</div>
{/foreach}


{/strip}