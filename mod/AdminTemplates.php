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
* @file       mod/AdminTemplates.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

final class AdminTemplates extends Admin {
	
	public $dir_links = array();
	
	private $editable_extensions = array('html', 'htm', 'js', 'jsb', 'php', 'ini', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phpt', 'shtml', 'jhtml', 'pl', 'py', 'txt', 'tpl','css','sql','xml','htaccess');

	const ROOT_FOLDER = ':root:';

	public function __construct() {
		$this->title = 'Template files';
		parent::__construct(__CLASS__);
		if (!isset($_SESSION['AdminGlobal']['editarea'])) {
			$_SESSION['AdminGlobal']['editarea'] = true;	
		}
	}
	public function init() {		
		$this->table = 'files';
		$this->array['file_folders'] = array(
			self::ROOT_FOLDER => FTP_DIR_ROOT,
			'admin' => 'Admin templates',
			'img'	=> 'Images',
			'js'	=> 'Javascript',
			'css'	=> 'CSS',
			'email'	=> 'Email templates'
		);

		$this->array['new_file_ext'] = array(
			'tpl'	=> '.tpl',
			'html'	=> '.html',
			'js'	=> '.js',
			'css'	=> '.css',
			'php'	=> '.php',
			'ini'	=> '.ini',
			'txt'	=> '.txt',
			'xml'	=> '.xml',
			'htaccess'=>'.htaccess'
		);

		if (($f = get('file_folder')) && (array_key_exists($f,$this->templates) || array_key_exists($f,$this->array['file_folders']))) {
			$this->file_folder = $f;
		} else {
			$this->file_folder = $this->template;	
		}
		$this->no_delete_folders = array('lib','inc','config','geo','mod','upload','system','lang');
		$this->params['new_file'] = false;
	}
	
	public function json() {
		switch ($this->get) {
			case 'action':
				$this->action();
			break;
		}
	}
	public function action($action = false) {
		if ($action) $this->action = $action;
		switch ($this->action) {
			case 'save':
				$this->allow($this->table,'save');
				$this->tRead();
				if (!post('file')) {
					break;
				}
				$this->file = $this->file(post('file'));
				$file = File::unchar($this->file);
				if (!is_file($this->dir.$file)) {
					$this->msg_text = lang('$File %1 does not exist!',$this->file);
					break;	
				}
				$text = post('text');
				$text = str_replace('    ',"\t",$text);
				$text = str_replace("\xEF\xBB\xBF", '', $text);
				if (file_put_contents($this->dir.$file, $text)) {
					$this->msg_text = lang('$File %1 was saved!',$this->file);
				} else {
					$this->msg_type = 'stop';
					$this->msg_delay = 5000;
					$this->msg_text = 'Cannot save to '.$this->file.'';
				}
				clearstatcache();
			break;
			case 'rename':
				$this->allow($this->table,'rename');
				if (!post('new_file') || !post('old_file')) return false;
				$f = explode('/',urldecode(request('file_path')));
				array_pop($f);
				$folder = join('/',$f);
				$this->folder = trim($folder,'/').'/';
				if ($this->file_folder==self::ROOT_FOLDER) {
					$path = FTP_DIR_ROOT.$this->folder;
				} else {
					$path = FTP_DIR_TPLS.$this->file_folder.'/'.$this->folder;
				}
				$old = File::unchar($this->file(post('old_file')));
				$new = File::unchar($this->file(post('new_file')));
				$ext = ext($old);
				$new = $new.($ext?'.'.$ext:'');
				
				if (@rename($path.$old, $path.$new)) {
					$this->msg_text = lang('$File %1 was renamed to $2',$old,$new);
					$this->msg_type = 'success';
					$this->dir = $path;
					ob_start();
					$this->inc('template_row'.((isset($_SESSION['AdminGlobal']['thumbnails']) && $_SESSION['AdminGlobal']['thumbnails'])?'_thumb':'').'.php');
					$template_bit = ob_get_contents();
					ob_end_clean();
					
					$this->json_push = array(
						'a'	=> $this->tReadFile($new),
						'row_to_eval' => $template_bit
					);
				} else {
					$this->msg_text = lang('$File %1 cannot be renamed to $2',$old,$new);
					$this->msg_type = 'error';
					$this->msg_ok = false;	
					$this->dir = $path;
					ob_start();
					$this->inc('template_row'.((isset($_SESSION['AdminGlobal']['thumbnails']) && $_SESSION['AdminGlobal']['thumbnails'])?'_thumb':'').'.php');
					$template_bit = ob_get_contents();
					ob_end_clean();
					$this->json_push = array(
						'a'	=> $this->tReadFile($new),
						'row_to_eval' => $template_bit
					);
				}
				clearstatcache();
			break;
			case 'delete':
				$this->allow($this->table,'delete');
				$this->msg_reload = false;
				$this->msg_close = false;
				$f = request('file_path');
				$file = File::unchar($f);
				if ($this->file_folder==self::ROOT_FOLDER) {
					$file = FTP_DIR_ROOT.$file;
				} else {
					$file = FTP_DIR_TPLS.$this->file_folder.'/'.$file;
				}
				if (is_dir($file)) {
					File::delFolder($file.'/',true,true);
					if (File::delFolder($file.'/',true,false)) {
						$this->msg_type = 'trash';
						$this->msg_text = lang('$Folder %1 has been removed',$f);	
					}
				} else {
					if (!is_file($file)) {
						$this->msg_type = 'error';
						$this->msg_delay = 2000;
						$this->msg_text = lang('$File %1 doesn\'t exists',$f);	
					} else {
						if (@unlink($file)) {
							$this->msg_type = 'trash';
						//	$this->msg_text = lang('$File %1 has been deleted',$f);
						} else {
							$this->msg_type = 'error';
							$this->msg_delay = 2000;
							$this->msg_text = lang('$File %1 cannot be deleted',$f);	
						}
					}
				}
				clearstatcache();
			break;
		}
	}
	public function upload() {
		if (Site::$upload[1]=='templates') {
			
			$this->allow($this->table,'upload');
			$handler = new UploadHandler;
			$folder = str_replace('$','/',Site::$upload[3]);
			$ex = explode('/',$folder);
			$root = false;
			if ($ex[0]==self::ROOT_FOLDER) {
				$root = true;
				array_shift($ex);
				$folder = join('/',$ex);
			}

			$filename = $_FILES['Filedata']['name'];
			$tmp_name = $_FILES['Filedata']['tmp_name'];
			
			if ($root) {
				$to = FTP_DIR_ROOT.($folder?$folder.'/':'').$filename;
			} else {
				$to = FTP_DIR_TPLS.$folder.'/'.$filename;
			}
			$handler->prepare($tmp_name,$filename,$to);
			$filename = $handler->file_name;
			$from = $_FILES['Filedata']['tmp_name'];
			$handler->move($from, File::unchar($to));
			echo json_encode($handler->response());
			
		//	echo '1/'.$to.'';
		}
	}
	
	private function file($file) {
		return str_replace(array('../','..\\'),'',trim($file));
	}
	
	private function folder($name = 'folder') {
		return str_replace(array('../','..\\'),'',trim(post($name,'',get($name)),'/').'/');	
	}
	
	private function tRead() {
		$this->folder = '';
		$folder = $this->folder();
		if (get('create_backup')) {
			$this->allow($this->table,'rename');
			$f = File::fixFileName(get('create_backup'));
			$ext = ext($f);
			$name = filename($f);
			if ($this->file_folder==self::ROOT_FOLDER) {
				$dir = FTP_DIR_ROOT.$folder;
			} else {
				$dir = FTP_DIR_TPLS.$this->file_folder.'/'.$folder;
			}
			$from = $dir.'/'.$f;
			if (!is_dir($from)) {
				$file = $dir.'/'.$name.'.bak.'.$ext;
				if (!is_file($file)) {
					copy($from,$file);
				}
			}
			clearstatcache();
		}
		elseif (get('restore_backup')) {
			$this->allow($this->table,'rename');
			$f = File::fixFileName(get('restore_backup'));
			$ext = ext($f);
			$name = filename($f);
			if (substr($name,-4)=='.bak') {
				if ($this->file_folder==self::ROOT_FOLDER) {
					$dir = FTP_DIR_ROOT.$folder;
				} else {
					$dir = FTP_DIR_TPLS.$this->file_folder.'/'.$folder;
				}
				$file = substr($name,0,-4).'.'.$ext;
				if (is_file($dir.'/'.$file)) {
					unlink($dir.'/'.$file);
				}
				rename($dir.'/'.$f,$dir.'/'.$file);
			}
			clearstatcache();
		}
		elseif (get('bulk_delete')) {
			$files = post('files');
			if ($files) {
				$this->allow($this->table,'delete');
				$this->folder = $this->folder();
				
				foreach ($files as $file) {
					if ($this->file_folder==self::ROOT_FOLDER) {
						$file = FTP_DIR_ROOT.($this->folder ? $this->folder.'/':'').$file;
					} else {
						$file = FTP_DIR_TPLS.$this->file_folder.'/'.($this->folder ? $this->folder.'/':'').$file;
					}
					$file = iconv('utf-8','cp1251', $file);
					if (is_dir($file)) File::rmdir($file.'/');
					elseif (is_file($file)) @unlink($file);
				}
			}
			clearstatcache();
		}
		elseif (get('bulk_paste')) {
			$files = post('files');
			if ($files && post('from_file_folder')) {
				$this->folder = $this->folder();
				$this->allow($this->table,'rename');
				foreach ($files as $file) {
					if ($this->file_folder==self::ROOT_FOLDER) {
						$to = FTP_DIR_ROOT.($this->folder ? $this->folder.'/':'').$file;
					} else {
						$to = FTP_DIR_TPLS.$this->file_folder.'/'.($this->folder ? $this->folder.'/':'').$file;
					}
					if (post('from_file_folder')==self::ROOT_FOLDER) {
						$from = FTP_DIR_ROOT.($this->folder('from_folder') ? $this->folder('from_folder').'/':'').$file;
					} else {
						$from = FTP_DIR_TPLS.$this->folder('from_file_folder').'/'.($this->folder('from_folder') ? $this->folder('from_folder').'/':'').$file;
					}
					$from = iconv('utf-8','cp1251', $from);
					$to = iconv('utf-8','cp1251', $to);
					rename ($from, $to);
					clearstatcache();
				}
			}
		}
		elseif ((get('new_ext')=='htaccess' || get('new_file')) && get('new_ext') && array_key_exists(get('new_ext'), $this->array['new_file_ext'])) {
			$this->allow($this->table,'create');
			if ($this->file_folder==self::ROOT_FOLDER) {
				$dir = FTP_DIR_ROOT.$folder;
			} else {
				$dir = FTP_DIR_TPLS.$this->file_folder.'/'.$folder;
			}
			if (get('new_ext')=='htaccess') {
				$file = '.htaccess';
			} else {
				$f = $this->file(get('new_file'));
				$file = $f.'.'.get('new_ext');
			}
			if (!is_file($dir.$file)) {
				touch($dir.$file);
			}
			$this->params['new_file'] = $file;
			unset($dir,$file);
			clearstatcache();
		}
		elseif (get('new_folder')) {
			$this->allow($this->table,'create');
			$f = File::fixFolderName(get('new_folder'));
			if ($this->file_folder==self::ROOT_FOLDER) {
				$dir = FTP_DIR_ROOT.$folder.'/'.$f;
			} else {
				$dir = FTP_DIR_TPLS.$this->file_folder.'/'.$folder.'/'.$f;
			}
			if (!is_dir($dir)) {
				if (mkdir($dir)) {
					$folder .= '/'.$f;
					$_GET['folder'] = $folder.'/';
				}
			} else {
				$folder .= '/'.$f;
				$_GET['folder'] = $folder.'/';
			}
			clearstatcache();
		}
		
		if ($folder) {
			if (($this->file_folder==self::ROOT_FOLDER && !is_dir(FTP_DIR_ROOT.$folder.'/')) || ($this->file_folder!=self::ROOT_FOLDER && !is_dir(FTP_DIR_TPLS.$this->file_folder.'/'.$folder))) {
				$folder = '';
				$_GET['folder'] = '';				
			}
		}
		$this->uploadHash();

		$ex = explode('/',trim($folder,'/'));
		$this->dir_links[] = '<a href="javascript:;" onclick="S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->name.'&file_folder='.$this->file_folder.'\')">'.$this->file_folder.'</a>';
		$_ex = $ex;
		$total = count($ex);
		foreach ($ex as $i => $e) {
			if (!$e) continue;
			$_ex = $ex;
			$file_path = join('/',array_splice($_ex,0,$i+1));
			if ($total>$i+1) {
				$this->dir_links[] = '<a href="javascript:;" onclick="S.A.L.get(\'?'.URL_KEY_ADMIN.'='.$this->name.'&folder='.$file_path.'&file_folder='.$this->file_folder.'\')">'.$e.'</a>';
			} else {
				$this->dir_links[] = ''.$e.'';	
			}
		}
		$this->folder = join('/',$ex);
		if ($this->file_folder==self::ROOT_FOLDER) {
			$this->dir = FTP_DIR_ROOT.($this->folder?$this->folder.'/':'/');
		} else {
			$this->dir = FTP_DIR_TPLS.$this->file_folder.($this->folder?'/'.$this->folder.'/':'/');
		}
		
		return $ex;
	}
	
	public function listing() {
		$this->button['save'] = false;
		$this->button['add'] = false;
		$ex = $this->tRead();
		$dirs = $files = array();
		$editables = array('');
		$this->create_ext = '';
		$i = 0;
		if (is_dir($this->dir)) {
			$dh = opendir($this->dir);
			while ($file = readdir($dh)) {
				if ($file=='.' || $file=='..') continue;
				if ($this->find && !strstr($file,$this->find)) continue;
				if (is_dir($this->dir.$file)) {
					$dirs[$file] = $this->tReadFile($file);
				} else {
					$files[$file] = $this->tReadFile($file);
					if (!$this->create_ext) $this->create_ext = ext($file);
					$i++;
					if ($i==1001) {
						break;
					}
				}
			}
			closedir($dh);
		}
		ksort($dirs);
		ksort($files);
		$_dirs = $_files = array();
		foreach ($dirs as $f) $_dirs[] = $f;
		foreach ($files as $f) $_files[] = $f;
		if ($ex[0]) {
			array_pop($ex);
			array_unshift($_dirs,array(
				'd'	=> 1,
				'p'	=> join('/',$ex),
				'm'	=> date('d.m.Y', filemtime($this->dir)),
				'f'	=> '..',
				'x'	=> 'parent'
			));
		}
		$this->data = array_merge($_dirs, $_files);
		$this->json_data = json($this->data);
	}
	
	
	private function char($s) {
		return $s;	
	}
	
	private function tReadFile($file) {
		$is_dir = is_dir($this->dir.$file);
		$f = ($this->folder?$this->folder.'/':'');
		
		
		if ($is_dir) {
			$a = !in_array($file, $this->no_delete_folders);
			$td = $tf = 0;
			$dh = opendir($this->dir.$file);
			while ($_f = readdir($dh)) {
				if ($_f=='.' || $_f=='..') continue; 
				if (is_dir($this->dir.$file.'/'.$_f)) {
					$td++;
				} else {
					$tf++;
				}
			}
			unset($_f);
			closedir($dh);
			$m = filemtime($this->dir.$file);
			$modified = '<span title="'.date('d.m.Y H:i',$m).'">'.Date::timeAgo($m).'</span>';
			return array(
				'a'	=> $a,
				'd'	=> 1,
				'p'	=> $f.self::char($file),
				'f'	=> self::char($file),
				'm'	=> $modified,
				'x'	=> md5($file),
				't'	=> lang('$%1 {number=%1,folder,folders}, %2 {number=%2,file,files}',$td,$tf)
			);
		} else {
			if (!isset($this->a)) $this->a = !preg_match('/\/('.join('|',$this->no_delete_folders).')\/$/',$this->dir);
			$ext = ext($file);
			$media = File::getMedia($file,$ext);
			$icon = 'tpls/img/ext/16/'.$ext.'.png';
			if (!is_file(FTP_DIR_ROOT.$icon)) {
				$icon = 0;	
			} else $icon = 1;
			if (!is_file($this->dir.$file)) return array();
			$size = File::display_size(filesize($this->dir.$file), true);
			$m = filemtime($this->dir.$file);
			$modified = '<span title="'.date('d.m.Y H:i',$m).'">'.Date::timeAgo($m).'</span>';
			
			$editable = $this->isEditableAsText($file, $ext) ? 1 : 0;
			$is_pic = File::isPicture($file) ? 1 : 0;
			$name = nameOnly($file);
			return array(
				'a'	=> ($this->a || $ext=='bak' || $ext=='pdf' || File::isPicture($file)),
				'b'	=> substr_count($file,'.bak.') ? str_replace('.bak.','.',$file) : 0,
				'o'	=> $ext,
			//	'a'	=> $media,
				'e'	=> $editable,
				'i'	=> $icon,
				'd'	=> 0,
			//	'p'	=> $f.$file,
				'f'	=> self::char($file),
			//	'n'	=> $name,
				's'	=> $size,
				'x'	=> md5($file),
				'm'	=> $modified,
				'u' => $is_pic
			);	
		}
	}
	
	private function isEditableAsText($file, $ext = false) {
		
		return in_array($ext, $this->editable_extensions);
	}
	
	
	public function window() {
		$this->tRead();
		//$this->output['themes'] = File::openDirectory(FTP_DIR_TPLS.'js/codemirror/theme/');
		$this->output['themes'] = array();
		$dh = opendir(FTP_DIR_TPLS.'js/ace/');
		while ($f = readdir($dh)) {
			if (substr($f,0,6)=='theme-') {
				$n = str_replace(array('theme-','.js'),'',$f);
				$this->output['themes'][$n] = ucfirst(str_replace('_',' ',$n));
			}
		}
		closedir($dh);
		$this->output['files'] = File::openDirectory($this->dir,'/\.('.join('|',$this->editable_extensions).')$/i');
		$dirs = File::openDirectory($this->dir,false,false,true,true);
		$this->output['dirs'] = array();
		if ($dirs) {
			foreach ($dirs as $dir) {
				$this->output['dirs'][] = File::char(($this->folder ? $this->folder.'/':'').$dir);
			}
		} else {
			$ex = explode('/',rtrim($this->dir,'/'));
			array_pop($ex);
			$ex2 = explode('/',$this->folder);
			array_pop($ex2);
			$folder = join('/',$ex2);
			$dirs = File::openDirectory(join('/',$ex).'/',false,false,true,true);
			foreach ($dirs as $dir) {
				$this->output['dirs'][] = File::char(($folder ? $folder.'/':'').$dir);
			}
		}

		
		$this->file = $this->file(get('file'));
		$file = File::unchar($this->file(get('file')));
		if (!$file) {
			$file = File::unchar($this->output['files'][0]);
			$this->file = $this->output['files'][0];
		}
		if (!$file) {
			$error = lang('$No files to edit in %1',$this->dir);
		}
		elseif (!is_file(realpath($this->dir.$file))) {
			$error = lang('$File %1 doesn\'t exists',$this->dir.$this->file.' ('.ext($this->file).')');
		}
		if ($error) {
			$this->post = array (
				'id'	=> '',
				'text'	=> $error,
				'title'	=> '',
				'syntax'=> 'txt',
				'no_save'=> true
			);
			$this->win($this->name);
			return;	
		}
		$hl = true;
		$this->ext = ext($file);
		switch ($this->ext) {
			case 'css': 
				$this->syntax = 'css';
			break;
			case 'js': 
				$this->syntax = 'js';
			break;
			case 'html': 
			case 'tpl':
				$this->syntax = 'html';
			break;
			case 'sql':
				$this->syntax = 'mysql';
			break;
			case 'txt':
			case 'ini':
				$this->syntax = 'txt';
				$hl = false;
			break;
			case 'pl':
				$this->syntax = 'perl';
			break;
			case 'py':
				$this->syntax = 'python';
			break;
			default:
				$this->syntax = 'php';
			break;
		}
		
		$c = $this->contents_utf8($this->dir.$file);
		$this->post = array (
			'id'	=> $this->file,
			'text'	=> $c,
			'title'	=> $this->file,
			'syntax'=> $this->syntax,
			'do_highlight'=> $hl
		);

		$this->win($this->name);	
	}
	private function contents_utf8($file) {
		$c = file_get_contents(realpath($file));
		/*
		$content = file_get_contents($fn, false, stream_context_create(array( 
			'http' => array( 
				'method'=>"GET", 
				'header'=>"Content-Type: text/html; charset=utf-8" 
			) 
		)));
		*/
		return $c;
		/*
		$c = !is_utf8(code2utf($c)) ? cp1251_to_utf8($c) : $c;
		return !is_utf8($c) ? cp1251_to_utf8($c) : $c;
		return mb_convert_encoding($c,'UTF-8');
		*/
	}
}