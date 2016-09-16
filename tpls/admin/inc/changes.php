<table class="a-form" cellspacing="0">
	<colgroup><col width="25%"></col><col width="85%"></col></colgroup>
	<?php 
	if ($this->post['changes']):
		$keys = array();
		if ($this->change) {
			foreach ($this->change as $a):
				if ($a['array_key']):
					$keys[] = $a['array_key'];
					$arr =& $this->post['changes'][$a['array_key']];
				else: 
					$arr =& $this->post['changes'];
				endif;
					
				foreach ($arr as $name => $changes):
					foreach ($changes as $time => $val):?>
					<tr>
						<td class="a-l"><?php echo date('H:i d.m.Y',$time)?></td>
						<td class="a-r"><?php echo strform($val)?></td>
					</tr>
					<?php 
					endforeach;
				endforeach;
			endforeach;	
		}
		if ($this->columns_set_change):
			foreach ($this->post['changes'] as $name => $changes):
			if (in_array($name, $keys)) continue;
			?>
			<tr>
				<th colspan="2"><?php echo $name?></th>
			</tr>
			<?php 
			foreach ($changes as $time => $val):?>
			<tr>
				<td class="a-l"><?php echo date('H:i d.m.Y',$time)?></td>
				<td class="a-r"><?php echo strform($val)?></td>
			</tr>
			<?php endforeach;?>
			<?php 
			endforeach;
		endif;
	endif;?>
</table>