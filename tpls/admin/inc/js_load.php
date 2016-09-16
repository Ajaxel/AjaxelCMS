<?php if (isset($win) && $win):?>
	S.A.W.load({
		name:	'<?php echo $this->name?>',
		name_id:'<?php echo $this->name_id?>',
		id:		'<?php echo $this->id?>',
		page:	'<?php echo $this->page?>',
		url:	'<?php echo strjs($this->url_full)?>',
		userid: '<?php echo $this->Index->Session->UserID?>',
		prefix: '<?php echo trim($this->current['PREFIX'],'_')?>',
		template: '<?php echo $this->tpl?>',
		upload: '<?php echo $this->upload?>',
		module: '<?php echo $this->module?>',
		multi:	<?php echo ((isset($multi) && $multi)?'true':'false')?>
	});
	if (S.A.W.callback_func) {
		S.A.W.callback_func();
		S.A.W.callback_func = false;
	}
<?php else:?>
	S.A.L.load({
		title: 	'<?php echo $this->Index->getVar('title_js')?>',
		caption:'<?php echo strjs(first($this->title))?>',
		name:	'<?php echo $this->name?>',
		page:	'<?php echo $this->page?>',
		tab:	'<?php echo $this->tab?>',
		url:	'?<?php echo strjs($this->referer)?>',
		prefix: '<?php echo trim($this->current['PREFIX'],'_')?>',
		template: '<?php echo $this->tpl?>'
	});

<?php endif;?>