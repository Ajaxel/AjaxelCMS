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
* @file       tpls/admin/grid.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	S.A.L.tabs('a-tabs_grid');
	S.A.L.ready();
	<?php if ($this->module):?>
		S.A.L.tab.tabs('option','active',S.A.L.tabIndex('<?php echo $this->module?>'));
	<?php endif;?>
});
</script>
<div id="a-area">
<?php $this->inc('top')?>
<div id="a-tabs_grid" class="a-tabs2" style="display:none">
	<ul class="a-tabs_ul">
		<?php
			foreach ($this->grid_modules as $key => $arr) {
				if (!$arr['active']) continue;
				$url = URL::get(URL_KEY_ADMIN,self::KEY_MODULE,self::KEY_TAB,self::KEY_LOAD);
				echo '<li><a href="?',URL_KEY_ADMIN,'=',$this->name,'&',self::KEY_MODULE,'=',$key,'&'.self::KEY_TAB.'=',$key,'&',self::KEY_LOAD,'&',$url,'">',$arr['title'],'</a></li>';
			}
		?>
	</ul>
</div>
<?php $this->inc('bot')?>
</div>