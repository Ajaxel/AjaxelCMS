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
* @file       tpls/admin/inc/nav.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if ($this->tab) $tab = $this->tab;
if (!isset($nav)) $nav = $this->nav;
if (!isset($total)) $total = $this->total;
elseif (is_array($total)) $total = 0;
if ($nav && $total):?>
<div class="a-pager" id="a-pager">
	<div class="a-l">
		<?php	
		if (!isset($nav['pages'][1])) {
			echo '<button type="button" class="a-button" onclick="S.A.L.get(\''.$nav['url'].'&'.$nav['page_key'].'=0\''.(isset($tab)?',false,\''.$tab.'\'':'').');this.disabled=true">1</button>';
		}
		foreach ($nav['pages'] as $p => $sel) {
			if ($sel) echo '<button type="button" class="a-button ui-state-disabled" disabled="disabled">'.$p.'</button>';
			else echo '<button type="button" class="a-button" onclick="S.A.L.get(\''.$nav['url'].'&'.$nav['page_key'].'='.($p-1).'\''.(isset($tab)?',false,\''.$tab.'\'':'').');this.disabled=true">'.$p.'</button>';
		}
		if ($nav['total_pages'] && !array_key_exists(intval($nav['total_pages']),$nav['pages'])) {
			echo '<button type="button" class="a-button" onclick="S.A.L.get(\''.$nav['url'].'&'.$nav['page_key'].'='.($nav['total_pages']-1).'\''.(isset($tab)?',false,\''.$tab.'\'':'').');this.disabled=true">'.$nav['total_pages'].'</button>';
		}
		?>
	</div>
	<?php if (Pager::val('total_pages') >= Pager::val('buttons')):?>
	<div class="a-l">&nbsp;
		<select class="a-select" onchange="S.A.L.get('<?php echo $nav['url']?>&<?php echo $nav['page_key']?>='+this.value<?php echo (isset($tab)?',false,\''.$tab.'\'':'')?>);this.disabled=true">
		<?php echo Pager::page_options()?>
		</select>
	</div>
	<?php endif;?>
	<div class="a-l">&nbsp;
		<select class="a-select" onchange="S.A.L.get('<?php echo $nav['url']?>&<?php echo $nav['limit_key']?>='+this.value<?php echo (isset($tab)?',false,\''.$tab.'\'':'')?>);this.disabled=true">
		<?php echo Pager::limit_options($this->button['limits'])?>
		</select>
	</div>
	<div class="a-r">
		<?php if ($this->sql):?><button class="a-button a-button_x" onclick="S.A.L.excel('<?php echo strjava($this->sql)?>','<?php echo $this->table?>','<?php echo strjava(sql($this->sql.' LIMIT '.$this->offset.', '.$this->limit,false))?>')"><?php echo lang('$export..')?></button><?php endif;?> <?php echo lang('$Total:')?> <?php echo $total?>
	</div>
</div>
<?php endif;?>