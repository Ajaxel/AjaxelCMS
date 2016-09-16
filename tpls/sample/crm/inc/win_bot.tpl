{strip}
	</tbody>
	<tfoot>
		<tr class="c-buttons">
			<td colspan="4" class="ui-corner-bottom ui-widget-header ui-corner-bottom">
				<div class="l">
					{$bottom}
				</div>
				{if $row.edited}
					<div class="l" style="line-height:25px">
					Last time edited: {'H:i d.m.Y'|date:$row.edited}
					</div>
				{/if}
				<div class="r">
					{if $save}
					<button type="submit" class="c-button" tabindex=100>{if $row.id}{$conf.buttons[1]|lang}{else}{$conf.buttons[0]|lang}{/if}</button>
					{else}
					<button type="button" class="c-button" onclick="CRM.close()" tabindex=100>{'Close'|lang}</button>
					{/if}
				</div>
			</td>
		</tr>
	</tfoot>
</table>
</form>
{/strip}