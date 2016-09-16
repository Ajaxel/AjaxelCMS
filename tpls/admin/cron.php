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
* @file       tpls/admin/cron.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	S.A.L.ready();
});
</script>
<div id="a-area">
<?php $this->inc('top')?>
<form method="post" id="<?php echo $this->name?>-search">
<div class="a-search">
	<div class="a-l">
		<?php $this->inc('template')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<div class="a-content" style="padding:20px" id="<?php echo $this->name?>-content">
<?php foreach ($this->array['do'] as $key => $label):?>
<?php if ($label):?>
<div style="padding:2px;"><button type="button" class="a-button" onclick="location.href='?<?php echo URL_KEY_ADMIN?>=cron&do=<?php echo $key?>'"><?php echo $label?></button></div>
<?php else:?>
<br />
<?php endif;?>
<?php endforeach;?>
</div>
<?php $this->inc('bot')?>
</div>