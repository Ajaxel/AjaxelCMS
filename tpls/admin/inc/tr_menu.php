<tr>
	<td class="a-l" width="15%"><?php echo lang('$Menu:')?></td><td class="a-r" colspan="2" width="85%">
		<select name="data[menuid]" class="a-select"><option value=""><?php echo lang('$-- please select --')?><?
		if (!$this->post('menuid', false)) $this->post['menuid'] = $this->menuid;
		$params = array(
			'select'	=> 'id, parentid, (CASE WHEN cnt3>0 THEN CONCAT(title_'.$this->lang.',\' (\',cnt3,\')\') ELSE title_'.$this->lang.' END) AS title, cnt',
			'selected'	=> $this->post('menuid', false)
		);
		echo Factory::call('menu', $params)->getAll()->toOptions();
	?></select>
	</td>
</tr>