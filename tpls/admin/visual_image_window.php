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
* @file       tpls/admin/visual_image_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->title = lang('$Insert in image');
$this->width = 756;
$this->inc('window_top');
?>
<iframe src="<?php echo HTTP_EXT?>tpls/js/tinymce/plugins/filebrowser/index.php?to=a-visual_body_<?php echo $this->name_id?>&win_id=<?php echo $this->name_id?>&name_id=<?php echo html($_GET['name_id'])?>" style="border:none;width:750px;height:420px"></iframe>
<?php $this->inc('window_bottom')?>