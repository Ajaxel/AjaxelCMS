<?php

/**
* Halt error message
* Ajaxel CMS v7.2
* Author: Alexander Shatalov <admin@ajaxel.com>
* http://ajaxel.com
*/

function halt_message($title, $descr, $lines, $die, $descr_plain) {
	$copy = '';
	$descr = str_replace('<a href="','<a style="font:15px \'Georgia\';color:#000" href="',$descr);
	$closeTags = '<a name="top"></a></select></textarea>';
	/*
	</TD></TD></TD></TH></TH></TH></TR></TR></TR></TABLE></TABLE></TABLE></A></ABBREV></ACRONYM></ADDRESS></APPLET></AU></B></BANNER></BIG></BLINK></BLOCKQUOTE></BQ></CAPTION></CENTER></CITE></CODE></COMMENT></DEL></DFN></DIR></DIV></DL></EM></FIG></FN></FONT></FORM></FRAME></FRAMESET></H1></H2></H3></H4></H5></H6></HEAD></I></INS></KBD></LISTING></MAP></MARQUEE></MENU></MULTICOL></NOBR></NOFRAMES></NOSCRIPT></NOTE></OL></P></PARAM></PERSON></PLAINTEXT></PRE></Q></S></SAMP></SCRIPT></SELECT></SMALL></STRIKE></STRONG></SUB></SUP></TABLE></TD></TEXTAREA></TH></TITLE></TR></TT></U></UL></VAR></WBR></XMP>
	*/
	if ($lines) {
		if (SITE_TYPE=='index' && Site::$exit) $height = 320;
		else $height = 200;
		if (is_array($lines)) {
			$db = '<div style="padding:5px;max-height:'.$height.'px;overflow:auto;">';
			foreach ($lines as $line) {
				$db .= '<div style="color:#000;padding:2px 4px;">'.$line.'</div>';
			}
			$db .= '</div>';
		} else {
			$db = '<div style="padding:5px 5px 0 5px;"><fieldset style="padding:3px 5px 0 5px;max-height:'.$height.'px;overflow:hidden;"><legend style="font:bold 9px Verdana;color:#666">Debug Backtrace:</legend><div style="line-height:120%!important;max-height:'.$height.'px;overflow:auto;">'.$lines.'</div></fieldset></div>';
		}
	}
	else $db = '';
	if ($descr) {
		$descr = str_replace(array('<em>','</em>'),array('<em>&laquo;','&raquo;</em>'),$descr);
		$descr = str_replace('<a ','<a style="font:15px Georgia" ',$descr);
		$descr = '<div style="padding:4px 5px 4px 5px!important;font:14px \'Trebuchet MS\', Verdana;line-height:120%;color:#000;background:#f5f5f5;text-shadow: #fff 0px 1px 1px;" class="a-descr">'.$descr.'</div>';
	} else {
		$descr = '';	
	}
	if ($die && $lines!==NULL) {
	//	$descr .= '<div style="padding:5px" class="a-url"><a href="'.URL::getFull().'" style="font:11px \'Georgia\';color:#000">'.URL::getFull().'</a>'.(SITE_TYPE!='index'?' <span style="font:12px Verdana">('.SITE_TYPE.')</span>':'').'</div>';
	//	if (SITE_TYPE=='index') $descr .= '<div style="padding:5px"><a href="/" style="font:11px \'Georgia\';color:#0033FF!important">Click here to go to the main page</a></div>';
	}
	/*
	if ($_POST && $die) {
		$descr .= '<div style="padding:5px;10px">'.p($_POST,0).'</div>';
	}
	*/
	if (SITE_TYPE=='json' && $die) {
		/*
		$d = $closeTags.'<title>'.$title.'</title><style>*{padding:0;margin:0;}A{text-decoration:none}A:hover{text-decoration:underline}</style>';
		$d .= '<table style="text-align:left;margin:20px;border-left:1px solid #ccc;border-top:1px solid #ccc;border-right:2px solid #ccc;border-bottom:2px solid #ccc;font:12px Tahoma;background:#f5f5f5;width:550px"><tr><td style="font:bold 13px Verdana;padding:5px;background:#CC0000">'.$title.'</td></tr><tr><td>';
		$d .= $descr;
		$d .= $db;
		$d .= $copy;
		$d .= '</td></tr></table>';
		*/
		$arr = array('halt' => array('title' => $title,'descr' => $descr, 'db'=>$db, 'icon' => ($descr_plain?'warning':'briefcase')));
		echo json_encode($arr);
		exit;
	}
	elseif (SITE_TYPE=='window' && $die) {
		echo $descr;
		exit;	
	}
	elseif (SITE_TYPE=='js' && $die) {
		$arr = array('halt'=>array('title' => $title,'descr' => $descr, 'db'=>$db, 'icon' => ($descr_plain?'warning':'briefcase')));
		echo 'var halt = '.json_encode($arr).'; ';
		echo 'if(typeof(jQuery)!=\'undefined\') {
		jQuery(document).ready(function() {
			S.G.ready();
			S.G.halt(halt);
		});
	} else {
		alert(halt.halt.title);	
	}';
		exit;
	} else {
		if (!headers_sent()) header('Content-Type: text/html; charset=utf-8');
		$css = 'BODY{padding:0;margin:0;background:url(\''.(defined('FTP_EXT')?FTP_EXT:'/').'tpls/img/admin_bg.jpg\') center no-repeat;background-attachment:fixed}A{text-decoration:none}A:hover{text-decoration:underline}P{margin-top:0}';
		$table = '<table cellspacing="0" cellpadding="0" style="box-shadow:0 0 5px black;box-shadow:0 0 10px rgba(0,0,0,0.5);-moz-box-shadow:0 0 10px rgba(0,0,0,0.5);-webkit-box-shadow:0 0 10px rgba(0,0,0,0.5);-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;margin:20px auto;text-align:left;border-left:1px solid #ccc;border:1px solid #555;font:12px Tahoma;background:#f5f5f5;width:540px"><tr><td style="font:bold 13px Verdana;padding:5px;background:#CC0000;-moz-border-radius-topleft:4px; -webkit-border-top-left-radius:4px; -khtml-border-top-left-radius:4px; border-top-left-radius:4px;-moz-border-radius-topright:4px; -webkit-border-top-right-radius:4px; -khtml-border-top-right-radius:4px; border-top-right-radius:4px;color:#fff!important;text-shadow: #222 0px 2px 2px;">'.$title.'</td></tr><tr><td>';
		$copy = '<div style="background:#aaa;padding:2px 4px 1px 4px;text-align:right;color:#fff;font:bold 12px \'Trebuchet MS\',Arial;-moz-border-radius-bottomleft:4px; -webkit-border-bottom-left-radius:4px; -khtml-border-bottom-left-radius:4px; border-bottom-left-radius:4px;-moz-border-radius-bottomright:4px; -webkit-border-bottom-right-radius:4px; -khtml-border-bottom-right-radius:4px; border-bottom-right-radius:4px;"><a href="http://ajaxel.com/" style="color:#fff;font:bold 12px \'Trebuchet MS\',Verdana">Ajaxel CMS</a> v'.Site::VERSION.'</div></td></tr>';
		if (!$die) $copy = '';
		if ($die) {
			$d = '';
			if (SITE_TYPE=='ajax') $d .= '<div id="center-area">';
			$d .= $closeTags.'<html><head><title>'.$title.'</title><style>'.$css.'</style></head><body>';
			$d .= $table.$descr.$db.$copy;
			$d .= '</table></body></html>';
			if (SITE_TYPE=='ajax') $d .= '</div>';
			echo $d;
			Site::$halted = $d;
			exit(199);
		} else {
			$d = $closeTags.'<title>'.$title.'</title><style>*{padding:0;margin:0;}A{text-decoration:none}A:hover{text-decoration:underline}</style>';
			$d .= $table.$descr.$db.$copy;
			$d .= '</td></tr></table>';
			echo $d;
		}
	}
}