<tr>
	<td class="a-l" width="15%"><?php echo lang('$Flags:')?></td>
	<td class="a-r">
		<label for="a-w-bodylist_<?php echo $this->name_id?>" class="a-win_status" alt="<?php echo lang('$This flag allows you to replace description text to full body text on list page (where are many entries in same content)')?>"><input type="checkbox" class="a-checkbox" id="a-w-bodylist_<?php echo $this->name_id?>" name="data[bodylist]" value="1"<?php echo ($this->post('bodylist', false)?' checked="checked"':'')?> /> <?php echo lang('$Show full')?></label>
		<label><input type="checkbox" name="data[comment]"<?php echo ($this->post('comment', false)=='Y'?' checked="checked"':'')?> id="a-comment_<?php echo $this->name_id?>" value="Y" /> <?php echo lang('$Allow to comment')?></label>
		<?php /*
		&nbsp;&nbsp;&nbsp;
		<label for="a-w-top_story_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-top_story_<?php echo $this->name_id?>" name="data[top_story]" value="1"<?php echo ($this->post('top_story', false)?' checked="checked"':'')?> /> <?php echo lang('$Top story')?></label>
		<input type="hidden" name="data[checkboxes][]" value="top_story" />&nbsp;&nbsp;&nbsp;
		<label for="a-w-most_read_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-w-most_read_<?php echo $this->name_id?>" name="data[most_read]" value="1"<?php echo ($this->post('most_read', false)?' checked="checked"':'')?> /> <?php echo lang('$Most read story')?></label>
		<input type="hidden" name="data[checkboxes][]" value="most_read" />
		*/ ?>
	</td>
</tr>