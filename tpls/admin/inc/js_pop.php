<?php

/**
* Ajaxel CMS v8.0
* http://ajaxel.com
* =================
* 
* Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* 
* The software, this file and its contents are subject to the Ajaxel CMS
* License. Please read the license.txt file before using, installing, copying,
* modifying or distribute this file or part of its contents. The contents of
* this file is part of the source code of Ajaxel CMS.
* 
* @file       tpls/admin/inc/js_pop.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if ($this->pop):
?>
$().ready(function(){
	S.A.W.wins['window_<?php echo $this->name_id?>']={
		win:$('<div>'),
		pop:true
	}
	S.A.W.callback('window_<?php echo $this->name_id?>');
	var w=<?php echo (!$this->width?$this->width:'parseInt($(\'#window_'.$this->name_id.'\').width())+35')?>;
	var h=<?php echo ($this->height?$this->height:'parseInt($(\'#window_'.$this->name_id.'\').height())+120')?>;
	$('#window_<?php echo $this->name_id?>').css({
		width: '99%',margin:'0 auto'
	});
	window.resizeTo(w,h);
	window.document.title='<?php echo strjs(str_replace('&gt;','>',strip_tags($this->title)))?> | '+window.document.title;
});
<?php endif;?>