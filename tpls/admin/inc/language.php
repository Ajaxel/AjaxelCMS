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
* @file       tpls/admin/inc/language.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!isset($tab)) $tab = $this->tab;
if (!isset($langs)) $langs = $this->langs;
if (count($langs)>1):
if (!isset($key_lang)) $key_lang = self::KEY_LANG;
?><?php echo lang('$Language:')?> <select onchange="S.A.L.get('?<?php echo URL::rq($key_lang, URL::get())?>&<?php echo $key_lang?>='+this.value<?php echo (isset($tab)?', false, \''.$tab.'\'':'')?>)">
	<?php
	$selected = $this->lang;
	if ($key_lang!=self::KEY_LANG):
		$selected = get($key_lang);
	?>
	<?php endif;?>
	<option value=""></option>
	<?php echo Html::buildOptions(($this->all['lang']?self::KEY_ALL:$selected),array_label($langs));?>
</select>
<?php endif;?>