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
* @file       tpls/admin/inc/template.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!isset($templates)) $templates = $this->templates;
if (count($templates)>1):
	echo lang('$Template:')?> <select onchange="S.A.L.get('?<?php echo URL::get(self::KEY_LOAD, self::KEY_TPL)?>&<?php echo self::KEY_TPL?>='+this.value<?php echo (isset($tab)?', false, \''.$tab.'\'':'')?>);S.A.W.close();S.A.L.ui(this.value, <?php echo strform($this->json_styles)?>);">
	<?php echo Html::buildOptions($this->template,array_label($templates));?></select>
<?php endif;?>