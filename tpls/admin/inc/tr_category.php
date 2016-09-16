<?php
$params = array(
	'table'		=> 'category_'.$module,
	'lang'		=> $this->lang,
	'catalogue'	=> false,
	'retCount'	=> false,
	'getAfter'	=> false,
	'noDisable'	=> true,
	'maxLevel'	=> 0,
	'optValue'	=> 'catref',
	'getHidden'	=> true,
	'selected'	=> $this->post('catref', false)
);
$cats = Factory::call('category', $params)->getAll()->toOptions();

if ($cats):?>
<tr>
	<td class="a-l" width="15%"><?php echo lang('$Category:')?></td><td class="a-r"<?php echo (isset($colspan)?' colspan="'.$colspan.'"':'')?> width="85%">
		<select name="<?php echo (isset($el_name)?$el_name:'data[catref]')?>" class="a-select">
			<option value="0"><?php echo lang('$-- No category --')?></option>
			<?php echo $cats?>
		</select>
	</td>
</tr>
<?php endif;?>