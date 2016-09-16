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
* @file       tpls/admin/mods.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><script type="text/javascript">
$().ready(function(){
	<?php echo $this->inc('js_load')?>
	S.A.L.tabs('a-tabs_mods');
	S.A.S.init();
	S.A.L.ready();
});
</script>
<div id="a-area">
<?php $this->inc('top')?>
<div id="a-tabs_mods" class="a-tabs2" style="display:none">
	<ul class="a-tabs_ul">
		<?php
			$tabs = array(
				'poll'		=> 'Polls & Quizzes',
				'results'	=> 'Quiz results',
				'ai'		=> 'Chat bot',
				'geo'		=> (in_array('geo_countries',DB::tables())?'GEO':''),
				'friends'	=> 'Friends'
			);
			foreach ($tabs as $key => $label) {
				if (!$label) continue;
				echo '<li><a href="?',URL_KEY_ADMIN,'=',$this->name,'&tab=',$key,'&',self::KEY_LOAD,'">',lang('$'.$label),'</a></li>';
			}
		?>
	</ul>
</div>
<?php $this->inc('bot')?>