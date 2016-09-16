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
* @file       tpls/admin/stats_clicks_window.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?><?php
$this->title = 'Link analyzer: '.$this->post['location'];
$this->width = 700;
$tab_height = 465;
?><script type="text/javascript">
var window_<?php echo $this->name_id?> = {
	load:function() {
		<?php echo $this->inc('js_load', array('win'=>true))?>
		S.A.W.tabs('<?php echo $this->name_id?>');
	}
}
<?php $this->inc('js_pop')?>
</script>
<?php 
$this->inc('window_top');

?><form method="post" class="window_form" id="a-form_<?php echo $this->name_id?>">
	<div id="a-tabs_<?php echo $this->name_id?>" class="a-tabs">
		<ul class="a-tabs_ul">
			<li><a href="#a_click_<?php echo $this->name_id?>"><?php echo lang('$First click')?></a></li>
			<li><a href="#a_visits_<?php echo $this->name_id?>"><?php echo lang('$Visits')?></a></li>
			<li><a href="#a_duration_<?php echo $this->name_id?>"><?php echo lang('$Duration')?></a></li>
			
		</ul>
		<div id="a_click_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			
			
		</div>
		<div id="a_visits_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			
			
		</div>
		<div id="a_duration_<?php echo $this->name_id?>" style="height:<?php echo $tab_height?>px;" class="a-tab">
			
			
		</div>
	</div>
	<?php $url = $this->url_full.'&id='.$this->id;?>
	<input type="hidden" name="get" value="action" />
</form>
<?php $this->inc('window_bottom')?>