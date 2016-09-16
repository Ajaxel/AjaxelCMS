
<?php if (isset($descr)):?>
var editor={
	element: '#a-w-descr_<?php echo $this->name_id?>',
	height: <?php echo ($descr ? $descr : 100)?>, 
	base: '<?php echo HTTP_BASE?>',
	lang: 'en',
	simple: true,
	templates: <?php echo $this->json_templates?>
}
S.A.W.editor(editor);
<?php endif;?>
<?php if (isset($body)):?>
var editor={
	element: '#a-w-body_<?php echo $this->name_id?>',
	height: <?php echo ($body ? $body : 300)?>, 
	base: '<?php echo HTTP_BASE?>',
	lang: 'en',
	templates: <?php echo $this->json_templates?>
}
S.A.W.editor(editor);
<?php endif;?>