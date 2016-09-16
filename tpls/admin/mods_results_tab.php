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
* @file       tpls/admin/mods_results_tab.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function() {
	<?php echo $this->inc('js_load')?>
	<?php echo $this->inc('js_load')?>
	var data = <?php echo $this->json_data?>, a, html = '', quiz = 'n/a';
	if (data.length) {
		html = '<table class="a-list a-list-one" cellspacing="0">';	
		html += '<tr><th width="8%">&nbsp;</th><th width="60%"><?php echo lang('$Name')?></th><th><?php echo lang('$Passes')?></th><th><?php echo lang('$Answers')?></th><th width="5%">&nbsp;</th></tr>';
	
		html += '</table>';
	} else {
		html = '<div class="a-not_found"><?php echo lang('$No quiz results were found')?></div>';
	}
	
	S.A.L.ready(html);
});
</script>
<div id="a-area">
<form method="POST" id="<?php echo $this->name?>-search_<?php echo $this->tab?>">
<div class="a-search">
	<div class="a-l">
	<?php $this->inc('language')?>
	<?php $this->inc('search')?>
	</div>
	<div class="a-r">
		<?php $this->inc('help_buttons')?>
	</div>
</div>
</form>
<?php $this->inc('list')?>

</div>