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
* @file       inc/EditFunctions.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

function edit_load(&$obj) {
	$_SESSION['RIGHTS']['filebrowser'] = true;
	$_SESSION['RIGHTS']['filebrowser_user_path'] = false;
	$_SESSION['RIGHTS']['template'] = TEMPLATE;
	$_SESSION['RIGHTS']['files'] = TEMPLATE;	
	Edit::$time = substr(time(),-5);
	
	$obj->Index->addJSA('jquery/'.JQUERY);
	if (defined('JQUERY_MIGRATE') && JQUERY_MIGRATE) $obj->Index->addJSA('jquery/'.JQUERY_MIGRATE);
	$obj->Index->addJSA('jquery/'.JQUERY_UI);
	$obj->Index->addJSA('swfobject.min.js');
	$obj->Index->addJSA('uploadify/'.JQUERY_UPLOADIFY);
	switch (WYSIWYG) {
		case 'ckeditor':
			$obj->Index->addJSA('ckeditor/ckeditor.js');	
		break;
		default:
			$obj->Index->addJSA(WYSIWYG.'/jquery.tinymce.js');	
	}
	
	$obj->Index->addJSA('plugins/jquery.blockUI.js');
	$obj->Index->addJSA('global.js');
	$obj->Index->addJSA('admin.js');
	$obj->Index->addCSSA('edit_'.TEMPLATE_ADMIN.'.css');
	$obj->Index->addCSSA('ui/'.($_SESSION['UI_ADMIN']?$_SESSION['UI_ADMIN']:((defined('UI_ADMIN') && UI_ADMIN)?UI_ADMIN:'selene')).'/'.JQUERY_CSS,' id="s-theme_admin"');
	if (FILE_BROWSER) {
	//	$obj->Index->addJSA('plugins/browser/browser.js');
	//	$obj->Index->addJSA('plugins/browser/plugins/fileeditor/codemirror/js/codemirror.js');
	}		
	if (!isset($_SESSION['AdminGlobal']) || !isset($_SESSION['AdminGlobal']['visual'])) {
		$_SESSION['AdminGlobal']['visual'] = false;
	}
	require_once(FTP_DIR_ROOT.'inc/Admin.php');
	$AdminEdit = new AdminEdit;
	$AdminEdit->admin_edit();
	Site::mem('Admin.php load');
	unset($AdminEdit);	
}

function edit_visual_parse($file) {
	
	Index()->Edit->file = $file;
	$ex = explode('/',$file);
	$filename = $ex[count($ex)-1];
	if (strstr($filename,'index.') || substr($filename,0,8)=='_visual_') return $file;
	array_pop($ex);
	$path = join('/',$ex);
	if ($path) $path = $path.'/';
	$file_admin = $path.'_visual_'.$filename;		
	$id = ($path ? '_':'').$path.'_visual_'.$filename;
	Index()->Edit->id_file = Parser::iDfile($id);
	/*
	if (isset($_GET['reset']) && is_file(FTP_DIR_TPL.$file_admin)) {
		unlink(FTP_DIR_TPL.$file_admin);
	}
	*/
	if (isset($_SESSION['VISUAL_FILES']) && isset($_SESSION['VISUAL_FILES'][$file_admin])) {
		unset($_SESSION['VISUAL_FILES'][$file_admin]);
	}
	if (is_file(FTP_DIR_TPL.$file_admin)) {
		if (filemtime(FTP_DIR_TPL.$file) < filemtime(FTP_DIR_TPL.$file_admin)) {
			return $file_admin;
		}
	}
	if (!is_writable(FTP_DIR_TPL.$path)) {
		Message::halt('Write permissions are restricted', 'Please chmod to 0755 the <em>'.FTP_DIR_TPL.$path.'</em> path');
	}
	if (!is_file(FTP_DIR_TPL.$file)) touch(FTP_DIR_TPL.$file);
	copy(FTP_DIR_TPL.$file,FTP_DIR_TPL.$file_admin);
	$contents = file_get_contents(FTP_DIR_TPL.$file_admin);
	$html = Index()->Edit->visual($contents);
	if ($html) {
		file_put_contents(FTP_DIR_TPL.$file_admin, $html);
		File::delFolder(FTP_DIR_TPL.'temp/', false, true, false);	
	}
	return $file_admin;	
}
function edit_unvisual($html) {
	if (strstr($html, '<visual ')) {
		$html = preg_replace('/<visual\s+(class=a-visual_nomove\s)?id=a-([^\s]+)\s*>/Ui','',$html);
		$html = str_replace('</visual>','',$html);
	}
	return $html;
}
function edit_visual($html) {
	if (edit_hasVisualTag('a')) {
		// A > IMG - skip
		$html = preg_replace('/<a\s([^>]+)>(\s*)<img\s+(.+)>(\s*)<\/a>/Ui','<a \\1>\\2<[[:IMG:]] \\3>\\4</a>',$html);
		// IMG
		$html = preg_replace('/<img (.+)\/?>/Uie','_edit_visual(\'$1\',\'\',\'img\')',$html);
		$html = str_replace('[[:IMG:]]','img',$html);
	} else {
		$html = preg_replace('/<img (.+)\/?>/Uie','_edit_visual(\'$1\',\'\',\'img\')',$html);	
	}
	// start
	$html = preg_replace('/<'.VISUAL_TAGS.'(\s|>|\{)/Uie','_edit_visual(\'$1\',\'$2\')',$html);
	// end
	$html = preg_replace('/<\/'.VISUAL_TAGS.'>/Ui','</\\1></visual>',$html);
	// body remove first
	$html = preg_replace('/<body([^>]+)>\s*<visual id=a-(.+)>/U','<body\\1>',$html);
	// img		
	$html = preg_replace('/\{foreach([^\}]+)\}([\s\S]+)\{\/foreach\}/Use','edit_noVisualMove(\'$1\',\'$2\')',$html);
	if (edit_hasVisualTag('a')) {
		$html = preg_replace('/<li([^>]*)>\s*<visual\s([^>]+)>\s*<a\s+([^>]+)>(.*)<\/a>\s*<\/visual>\s*<\/li>/Ui','<li\\1><a \\3>\\4</a></li>',$html);
	}
	return $html;	
}

function edit_noVisualMove($str,$str2) {
	$str = str_replace('\"','"',$str);
	$str2 = str_replace('\"','"',$str2);
	return '{foreach'.$str.'}'.str_replace('<visual ','<visual class=a-visual_nomove ',$str2).'{/foreach}';
}
function edit_hasVisualTag($tag) {
	return strstr(VISUAL_TAGS,'|'.$tag.'|') || strstr(VISUAL_TAGS,'('.$tag.'|') || strstr(VISUAL_TAGS,'|'.$tag.')');
}
function _edit_visual($tag, $space, $one_line_tag = false) {
	Index()->Edit->visual_index++;
	$_SESSION['VISUAL_INDEX'] = Index()->Edit->visual_index;
	if ($one_line_tag) {
		$tag = str_replace('\"','"',$tag);
		return '<visual id=a-'.Index()->Edit->visual_index.Index()->Edit->id_file.'><'.$one_line_tag.' '.$tag.'/></visual>';
	} else {
		return '<visual id=a-'.Index()->Edit->visual_index.Index()->Edit->id_file.'><'.$tag.$space;
	}
}


function edit_admin(&$obj, $force_editor) {

	$i = 0;
	
	switch ($obj->table) {
		case 'lang':
			$title = '';
			if (substr($obj->id,0,1)==='*') {
				$title = substr($obj->id,1);
				$obj->id = 0;
			}
			$obj->rs = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'" alt="'.strform($obj->value).'"'.($title?' title="'.strform($title).'"':'').' class="a-lang'.$obj->dummy.' [\'text_'.$obj->lang.'\',\'lang\','.$obj->id.','.($force_editor?'7':'6').']">'.$obj->rs.'</'.Edit::ADMIN_TAG.'>';
			return;
		break;
		case 'vars':
			$obj->rs = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'" class="a-lang'.$obj->dummy.' a-vars'.$obj->dummy.' [\'val_'.$obj->lang.'\',\''.$obj->table.'\',\''.addslashes($obj->id).'\',6]">'.$obj->rs.'</'.Edit::ADMIN_TAG.'>';
			return;
		break;
		case 'menu':
		case 'tree':
			$obj->rs['title'] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'" class="a-edit'.$obj->dummy.' [\'title_'.$obj->lang.'\',\''.$obj->table.'\','.$obj->id.',2,0,'.(int)$obj->rs['id'].','.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed('title')?',9':'').',1]">'.OB::links($obj->rs['title']).'</'.Edit::ADMIN_TAG.'>';
		break;
		case 'comments':
			$obj->rs['subject'] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'" class="a-edit'.$obj->dummy.' [\'subject\',\''.$obj->table.'\','.$obj->id.',2,0,'.(int)$obj->rs['id'].','.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed('subject')?',9':'').']">'.OB::links($obj->rs['subject']).'</'.Edit::ADMIN_TAG.'>';
			Edit::$index++;
			$obj->rs['body'] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'" class="a-edit'.$obj->dummy.' [\'original\',\''.$obj->table.'\','.$obj->id.',3,0,0,'.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed('body')?',9':'').']">'.OB::links($obj->rs['body']).'</'.Edit::ADMIN_TAG.'>';
		break;
		case 'content_html':
			$obj->rs['body'] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'" class="a-edit'.$obj->dummy.' [\'body\',\''.$obj->table.'\','.$obj->id.',5,0,0,'.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed('body')?',9':'').',1]">'.OB::links($obj->rs['body']).'</'.Edit::ADMIN_TAG.'>';
		break;
		case 'entries':
		case 'pages':
			foreach ($obj->editable_cols as $c => $type) {
				if ($obj->no_edit($c)) continue;
				if (!isset($obj->rs[$c]) || is_array($obj->rs[$c]) || !$obj->rs[$c]) continue;
				$obj->rs[$c] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'_'.$i.'" class="a-edit'.$obj->dummy.' [\''.$c.'\',\''.$obj->table.'\','.$obj->id.','.$type.',0,'.(int)$obj->Index->menu[9]['id'].','.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed($c)?',9':'').',1]">'.OB::links($obj->rs[$c]).'</'.Edit::ADMIN_TAG.'>';
				$i++;
			}
		break;
		default:
			if (substr($obj->table,0,8)=='content_') {
				foreach ($obj->editable_cols as $c => $type) {
					if ($obj->no_edit($c)) continue;
					if (!isset($obj->rs[$c]) || is_array($obj->rs[$c]) || !$obj->rs[$c]) continue;
					$obj->rs[$c] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'_'.$i.'" class="a-edit'.$obj->dummy.' [\''.$c.'\',\''.$obj->table.'\','.$obj->id.','.$type.','.(int)((isset($obj->rs['content']) && $obj->rs['content']['id'])?$obj->rs['content']['id']:$obj->rs['setid']).','.(int)$obj->Index->menu[9]['id'].','.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed($c)?',9':'').',1]">'.OB::links($obj->rs[$c]).'</'.Edit::ADMIN_TAG.'>';
					$i++;
				}
			}
			elseif (substr($obj->table,0,5)=='grid_') {
				$langed = in_array('rid',DB::columns($obj->table));
				$m = substr($obj->table,5);
				foreach ($obj->editable_cols as $c => $type) {
					if (!isset($obj->rs[$c]) || is_array($obj->rs[$c]) || !$obj->rs[$c] || is_numeric($obj->rs[$c])) continue;
					if ($obj->no_edit($c)) continue;
					$obj->rs[$c] = '<'.Edit::ADMIN_TAG.' id="'.Edit::edit_id().'_'.$i.'" class="a-edit'.$obj->dummy.' [\''.$c.'\',\'grid_'.$m.'\','.$obj->id.','.$type.',0,0,'.(isset($obj->rs['active'])?(int)$obj->rs['active']:1).($obj->isParsed($c)?',9':'').','.($langed ? '1':'0').']">'.OB::links($obj->rs[$c]).'</'.Edit::ADMIN_TAG.'>';
					$i++;
				}
			}
			
			if (substr($obj->table,-6)=='_files') {
				$obj->rs['admin'] = '<a href="javascript:;" onclick="S.A.E.del_image(\''.$obj->table.'\','.$obj->id.', \''.strjava($obj->rs['file']).'\', this)" class="a-image_del"><img src="/tpls/img/oxygen/16x16/actions/trash-empty.png"></a>';
			}
		break;
	}
	$_SESSION['EDIT_INDEX'] = Edit::$index;	
}
/*
// fuck it
function edit_parsePost($t) {
	if (strstr($t,'<p>')) {
		$c = substr_count($t,'<p>');
		if ($c==1) {
			if (substr($t,0,3)=='<p>' && substr($t,-4)=='</p>') {
				$t = substr(substr($t,0,-4),3);		
			}
		}
	}
	return $t;	
}
*/

function _edit_writeLangVal($t) {
	$t = preg_replace("/&amp;#([0-9]+);/s","&#\\1;",$t);
	$t = preg_replace("/&#(\d+?)([^\d;])/i","&#\\1;\\2",$t);
	$t = preg_replace("/\\\(?!&amp;#|\?#)/","\\",$t);
	$t = str_replace(chr(0xCA),'',$t);
	$t = str_replace("\r",'',$t);
	$t = str_replace("\b",'',$t);
	$t = str_replace("\0","\\\0",$t);
	return $t;
}

function edit_writeLangVal($tpl,$id,$lang,$value) {
	$value = _edit_writeLangVal($value);
	
	$value = preg_replace('/<!--([\s\S]+)-->/U','',$value);
	
	DB::run('UPDATE `'.DB_PREFIX.'lang` SET `text_'.$lang.'`='.e($value).' WHERE `id`='.e($id));
	
	$file = FTP_DIR_ROOT.'config/lang/lang_'.$tpl.'_'.$lang.'.php';
	if (is_file($file)) {
		$name = DB::one('SELECT name FROM '.DB_PREFIX.'lang WHERE id='.(int)$id);
		if (!$name) return false;
		$c = file_get_contents($file);
		$c = preg_replace('/\$_lang\[\''.preg_quote($name,'/').'\'\]\s=\s\'([\s\S]+)\';/Us', '$_lang[\''.str_replace('\"','"',addslashes($name)).'\'] = \''.str_replace('\"','"',addslashes(trim(str_replace('\\\'','\'',$value)))).'\';',$c);
		if (Admin::filePHPok($c)) {
			file_put_contents($file,$c);
		} else {
			d('PHP error in:

'.$c.'

Unable to save to '.$file.'');
		}
	}
	
	return true;
}
function edit_quick_save() {

	if (post('a')=='edit'):
		$id = post('id');
		$table = get('table');
		$col = post('column');
		$value = post('value');
		$all = post('all');
		$tpl = get('tpl');
		if (!$id || !$table || !$tpl || !$col || !$value) return false;
		$type = post('type');
		$l = get('l');
		if (!in_array($table, DB::tables())) return false;
		if (!in_array($col, DB::columns($table))) return false;
		$id_col = 'id';
		$where = '';
		switch ($table) {
			case 'vars':
				$value = ltrim($value,'#');
				$id = ltrim($id,'#');
				if (!$id) return false;
				$where .= ' AND template='.e($tpl);
				$id_col = 'name';
				$sql = 'UPDATE `'.DB::prefix($table).'` SET `'.$col.'`='.e($value).' WHERE `'.$id_col.'`='.e($id).$where;
				DB::run($sql);
			break;
			case 'lang':
				if (!is_numeric($id)) {
					$id = DB::one('SELECT id FROM '.DB_PREFIX.'lang WHERE template='.e($tpl).' AND name='.e($id));
				}
				if (!$id) return false;
				if (!$all) {
					$ex = explode('_',$col);
					edit_writeLangVal($tpl,$id,$ex[1],$value);
				} else {
					foreach (Site::getLanguages() as $l => $a) {
						edit_writeLangVal($tpl,$id,$l,$value);
					}
				}
			break;
		}
	elseif (post('a')=='delete'):
		$id = post('id');
		$table = get('table');
		if (!$id || !$table) return false;
		if (!in_array($table, DB::tables())) return false;
		$row = DB::row('SELECT * FROM `'.DB::prefix($table).'` WHERE id='.(int)$id);
		if (!$row) return false;
		$file = $row['file'] ? $row['file'] : $row['main_photo'];
		$sql = 'DELETE FROM `'.DB::prefix($table).'` WHERE id='.(int)$id;
		DB::run($sql);
		if (DB::affected()) {
			$i = 0;
			$dir = FTP_DIR_FILES.substr($table,0,-6).'/'.$row['setid'].'/';
			if (!is_dir($dir)) return false;
			foreach (array(1,2,3,4,5) as $i) {
				if (is_dir($dir.'th'.$i.'/') && is_file($dir.'th'.$i.'/'.$file) && @unlink($dir.'th'.$i.'/'.$file)) {
					$i++;
				}
			}
			if (is_file($dir.$file) && @unlink($dir.$file)) {
				$i++;
			}
			@unlink($dir);
		}
	endif;	
}