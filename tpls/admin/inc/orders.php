<?php if ($this->post('orders', false)):?>
<table class="a-form" cellspacing="0">
	<tr>
		<th width="1%"><?php echo lang('$ID')?></th>
		<th width="5%"><?php echo lang('$Status')?></th>
		<th width="5%"><?php echo lang('$Ordered')?></th>
		<th><?php echo lang('$Item')?></th>
		<th width="15%"><?php echo lang('$Type')?></th>
		<th width="1%"><?php echo lang('$Quantity')?></th>
		<th width="15%"><?php echo lang('$Price')?></th>
		<th width="1%" title="<?php echo lang('$Seller')?>">S</th>
	</tr>
	<?php foreach ($this->post('orders', false) as $i => $rs):?>
	<tr>
		<td class="a-r"><a href="javascript:S.A.W.open('?<?php echo URL_KEY_ADMIN?>=orders2&id=<?php echo $rs['id']?>')"><?php echo $rs['id']?></a></td>
		<td class="a-r" nowrap><?php echo Conf()->g3('order_statuses',$rs['status'],0)?></td>
		<td class="a-r"><?php echo date('d.m.Y',$rs['ordered'])?></td>
		<td class="a-r"><a href="javascript:;" onclick="S.A.L.global('link',{table:'<?php echo $rs['table']?>',itemid:'<?php echo $rs['itemid']?>'});"><?php echo $rs['title']?></a>
		<?php if ($rs['options']):?>
			<br /><?php echo $rs['options']?>
		<?php endif;?>
		</td>
		<td class="a-r"><?php echo Conf()->g3('order_types',$rs['type'],0)?></td>
		<td class="a-r"><?php echo $rs['quantity']?></td>
		<td class="a-r"><?php echo $rs['price']?> <?php echo $rs['currency']?></td>
		<td class="a-r"><a href="javascript:S.A.W.open('?<?php echo URL_KEY_ADMIN?>=users&id=<?php echo $rs['sellerid']?>')"><?php echo $rs['sellerid']?></a></td>
	</tr>
	<?php endforeach;?>
</table>
<? else:?>
	<table class="a-form" cellspacing="0">
	<tr>
		<td class="a-r">
			<div class="a-no_files"><?php echo lang('$No orders were made related to this entry')?></div>
		</td>
	</tr>
	</table>
<? endif;?>