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
* @file       tpls/admin/inc/form.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!$elements) return;
foreach ($elements as $k => $v):
	if (substr($k,-2)=='[]') {
		$k = substr($k,0,-2);
		$a = '[]';	
	} else {
		$a = '';	
	}
?><tr>
	<td class="a-l" width="15%"><?php echo $v[1]?></td>
	<td width="35%" colspan="5" class="a-r">
		<?php switch ($v[0]):
			case 'checkbox':?>
				<label for="a-o-<?php echo $k?>_<?php echo $this->name_id?>"><input type="checkbox" class="a-checkbox" id="a-o-<?php echo $k?>_<?php echo $this->name_id?>" name="data[options][<?php echo $k?>]<?php echo $a?>" value="1"<?php echo ($post[$k]?' checked="checked"':'')?> /> <?php echo lang('$'.($v[2]?$v[1]:'Enabled'))?></label>
			<?php break;
			case 'select':?>
				<select class="a-select" id="a-o-<?php echo $k?>_<?php echo $this->name_id?>" name="data[options][<?php echo $k?>]<?php echo $a?>"<?php echo $v[3]?>>
					<?php echo Html::buildOptions(($a?explode(',',$post[$k]):$post[$k]), $array[$v[2]],$v[4])?>
				</select>
			<?php break;
			case 'input':?>
			
			
		<?php endswitch;?>
	</td>
</tr>
<?php endforeach;?>