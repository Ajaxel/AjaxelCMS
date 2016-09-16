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
* @file       mod/Mainarea.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

abstract class Mainarea {
	
	protected function load() {
	
		$this->prefix['title'] = lang('_#site_title_separator').lang('_#site_title_short');
		$this->prefix['title_orig'] = $this->vars['title'];
		
		$this->conf = array (
			'LANG'			=> LANG // language
			,'TPL'			=> TEMPLATE // template
			,'DEVICE'		=> DEVICE
			,'SESSION_LIFETIME' => SESSION_LIFETIME
			,'USER_ID'		=> USER_ID // user id
			//,'IM_BOARD'		=> IM_BOARD // wether to show IM board
			,'URL_EXT'		=> URL::ext() // template, lang for URL
			,'HTTP_EXT'		=> HTTP_EXT // folder where website was copied: http://domain.com/folder for links
			,'FTP_EXT'		=> FTP_EXT // folder where website was copied: http://domain.com/folder for images, css paths
			,'REFERER'		=> strjs(URL::ht('?'.URL::get(LANG),false,false))
			,'VERSION'		=> Site::VERSION
		);
		if (IS_ADMIN) {
			$this->conf['URL_KEY_ADMIN'] = URL_KEY_ADMIN; // domain.com/admin can be also domain.com/cms
		}
		if ($_SESSION[Site::$token_key]) {
			$this->conf['_T'] = $_SESSION[Site::$token_key];
		}
	}
	
	/**
	* Some SQL query addons
	*/
	public function sql($name) {
		switch ($name) {
			case 'menu_active':	
				if ($this->Session->UserID) {
					
					$d = ' AND (m.display=1 OR m.display=0'.(IS_ADMIN?' OR m.display=3':'').' OR (m.display=4 AND ((m.options LIKE \'%,g'.$this->Session->GroupID.',%\' OR m.options NOT LIKE \'%,g%\') AND (m.options LIKE \'%,c'.$this->Session->ClassID.',%\' OR m.options NOT LIKE \'%,c%\'))))';
					
				//	$d = ' AND (m.display=1 OR m.display=0'.(IS_ADMIN?' OR m.display=3':'').' OR (m.display=4 AND (m.options LIKE \'%,g'.$this->Session->GroupID.',%\' OR m.options LIKE \'%,c'.$this->Session->ClassID.',%\')))';
				//	$d = ' AND  display NOT IN (2,3,5)';	
				} else {
					$d = ' AND m.display NOT IN (1,3,5,4)';	
				}
				$ret = ' AND m.active=1'.$d;
			break;
			default:
				$ret = 'undefined '.e($name);
			break;
		}
		return $ret;
	}
	
	
	/**
	* Mainarea of the site
	* site type is the first url key, for popup: ?popup&foo=bar (/popup/foo-bar)
	* return void()
	*/
	private function printMain($site_type = SITE_TYPE) {
		$this->mainarea = true;
		$params = array();
		if (SITE_TYPE!='ajax') echo '<div id="center-area">'; // <-- do not remove, needed for ajax container in the middle
		if ($this->show('ajax_header') && ($this->show('header') || Site::$ajax) && is_file(FTP_DIR_TPL.'ajax_header.tpl') && url(0)!='crm') $this->displayFile('ajax_header.tpl');
		$this->My->page();
		if ($this->content && $this->content['keywords']) {
			$this->setVar('keywords',html($this->vars['keywords'].', '.$this->content['keywords']));
		}		
		if (!$this->mainarea) {
			header('HTTP/1.0 404 Not Found');
			if (!$this->tree) $this->setVar('title',html(lang('_404 - page not found')));
			$this->displayFile('404.tpl');
		}
		if ($this->show('ajax_footer') && ($this->show('footer') || Site::$ajax) && is_file(FTP_DIR_TPL.'ajax_footer.tpl') && url(0)!='crm') $this->displayFile('ajax_footer.tpl');
		if (SITE_TYPE=='ajax') {
			$title = $this->getVar('title');
			echo '<script type="text/javascript">window.document.title=\''.$title.'\'; if (typeof(S)==\'undefined\') window.location.href=window.location.href.replace(\'ajax\',\'index\');else S.C.REFERER=\''.strjs($this->conf['REFERER']).'\';</script>';
		}
		if (SITE_TYPE!='ajax') echo '</div>'; // do not remove
		
	}
	


	/**
	* Default site type: index
	*/
	public function printIndex($site_type = SITE_TYPE) {
		
		@header('Content-type: text/html; charset=utf-8');
		
		if ($this->show('all')) {
			ob_start(($this->My->OBparse()?array('OB','handler'):false));
			$this->ajaxel();
			/*
			switch ($this->engine) {
				case 'wordpress':
					require FTP_DIR_ROOT.'inc/lib/Wordpress.php';
					$this->wordpress();
				break;
				default:
					$this->ajaxel();
				break;
			}
			*/
			ob_end_flush();
		}
	}
	
	/**
	* Ajaxel engine
	*/
	private function ajaxel() {
		
		$this->My->head();
		$this->My->index();
		if ($this->show('head') && !Site::$ajax) {
			$this->includeFile('common/'.SITE_TYPE.'.head.php');
			echo '<script>if(typeof $==\'undefined\')var $=function(f){this.ready=function(f){document.addEventListener(\'DOMContentLoaded\',f)};if(typeof(f)==\'function\')return this.ready(f);return this},S={C:{},G:{}},jQuery=$;</script>';
			$this->addCSSA('global.css');
		}
		if ($this->show('html')) {
			if ($this->show('header')) {
			//	if (!Site::$mini) $this->My->showTopMessage(false);
				$this->displayFile('header'.$this->My->prefix().'.tpl');
			}	
			if ($this->show('main')) {
				$this->printMain();
			}
			if ($this->show('footer')) {
				$this->displayFile('footer'.$this->My->prefix().'.tpl');
			}
		}
		if ($this->show('head') && !Site::$ajax) {
			$this->includeFile('common/'.SITE_TYPE.'.foot.php');
		}
		if (!Site::$mini) { // can be removed
			$this->My->setAll();
			$this->My->showTopMessage(true);
		}
	}
	
	
	/**
	* These are just aliases, popup, print, wap and pda sites are the same as index, but with different wrapper templates
	*/
	protected function printPopup() {
		$this->printIndex();
	}
	protected function printPrint() {
		$this->printIndex();
	}
	protected function printWindow() {
		header('Content-type: text/html; charset=utf-8');
		$this->My->window();
	}
	/**
	* Output: ajax-javascript source, eval()-ed in the end by default
	*/
	protected function printAjax() {
		/*
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		*/
		header('Content-Type: text/html; charset=utf-8');
		$this->hideAll();
		$this->My->ajax();
	}
	/**
	* Output: json
	*/
	protected function printJson() {
		/*
		header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		header('Cache-Control: no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		*/
		header('Content-Type: text/json; charset=utf-8');
		$this->hideAll();
		$this->My->json();
	}
	/**
	* Output: javascript
	*/
	protected function printJs() {
		header('Content-Type: text/javascript; charset=utf-8');
		$this->My->js();
	}
	/**
	* Output: xml, database export
	*/
	protected function printXml() {
		header('Content-Type: text/xml; charset=utf-8');
		$this->My->xml();
	}
	/**
	* Output: rss, for remote programs, export
	*/
	protected function printRss() {
		header('Content-Type: application/rss+xml; charset=utf-8');
		$this->My->rss();
	}
	/**
	* Output: file download, file source
	* no headers here
	*/
	protected function printDownload() {
		$this->My->download();
	}
	protected function printPdf() {
		$this->My->pdf();
	}
	/*
	protected function printCsv() {
		$this->hideAll();
		header('Content-type: application/vnd.ms-excel; charset=utf-8');
		header('Content-disposition: csv; filename="'.$this->My->csv()->getFilename().'"; size="'.$this->My->csv()->getSize().'"');
		$this->My->csv();
	}
	protected function printExcel() {
		$this->My->excel();
	}
	protected function printImg() {
		$this->My->img();
	}
	protected function printTh() {
		$this->My->th();
	}
	*/
}