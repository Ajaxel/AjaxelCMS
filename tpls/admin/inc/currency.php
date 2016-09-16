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
* @file       tpls/admin/inc/currency.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!isset($currencies)) $currencies = $this->currencies;
if (count($currencies)>1):
?><?php echo lang('$Currency:')?> <select onchange="S.A.L.get('?<?php echo URL::rq(self::KEY_CURRENCY, URL::get())?>&<?php echo self::KEY_CURRENCY?>='+this.value<?php echo (isset($tab)?', false, \''.$tab.'\'':'')?>);">
	<?php echo Html::buildOptions($this->currency,array_label($currencies, 1));?></select>
<?php endif;?>