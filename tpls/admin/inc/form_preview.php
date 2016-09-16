<?php
foreach ($form as $i => $f):?>
	<li style="width:<?php echo round($f['settings']['li_width'])?>%;<?php echo ($f['settings']['li_clear']?'_clear:left;':'')?><?php echo ($f['settings']['li_auto']?'height:auto;':'')?>" id="a-el_<?php echo $f['id']?>"><div class="a-el" style=";<?php echo ($f['settings']['li_auto']?'height:auto;':'')?>">
		<?php
		if ($f['settings']['attr']) $attr = ' '.$f['settings']['attr']; else $attr = '';
		$attr = str_replace('100%','99%',$attr);
		ob_start();
		@eval('?>'.$f['settings']['val']);
		$val = ob_get_contents();
		if (!$val) $val = $f['settings']['val'];
		ob_end_clean();
		if ($f['settings']['clear']) {
			$attr .= ' onfocus="this.value=\'\'" onblur="if(this.value==\'\'){this.value=\''.strjs($val).'\'}"';	
		}
		switch ($f['type']) {
			case EL_QUESTION:
				echo '<div class="a-text"'.$attr.'>'.$val.'</div>';
			break;
			case EL_TEXT:
				echo '<input type="text" class="a-element f-el-text" name="fm['.$f['name'].']" value="'.$val.'"'.$attr.' />';
			break;
			case EL_HIDDEN:
				echo '<input type="text" class="a-element f-el-hidden" name="fm['.$f['name'].']" disabled="disabled" value="'.$val.'"'.$attr.' />';
			break;
			case EL_PASSWORD:
				echo '<input type="password" class="a-element f-el-password" name="fm['.$f['name'].']" value="'.$val.'"'.$attr.' />';
			break;
			case EL_TEXTAREA:
				echo '<textarea class="a-element f-el-textarea" name="fm['.$f['name'].']""'.$attr.'>'.$val.'</textarea>';
			break;
			case EL_SELECT:
				echo '<select class="a-element f-el-select" name="fm['.$f['name'].']"'.$attr.'>'.Html::buildOptions(false, $f['arr']).'</select>';
			break;
			case EL_RADIO:
				
			break;
			case EL_CHECKBOX:
				
			break;
			case EL_FILE:
				echo '<input type="file" class="a-element f-el-file" name="fm['.$f['name'].']" value=""'.$attr.' />';
			break;
			case EL_DATETIME:
				
			break;
			case EL_HTML:
			case EL_PHP:
			case EL_SMARTY:
				echo '<table width="100%" cellspacing="0" cellpadding="0">';
				if ($f['type']==EL_PHP) {
					echo $val;
				} else {
					echo $f['settings']['val'];
				}
				echo '</table>';
			break;
		}
		?>
	</div></li>
<?php endforeach; ?>