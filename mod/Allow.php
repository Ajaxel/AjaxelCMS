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
* @file       mod/Allow.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class Allow {
	private static $_instance = false;
	private $Session;
	
	public static function &getInstance() {
		if (!self::$_instance) self::$_instance = new self;
		return self::$_instance;
	}
	
	private function __construct() {
		$this->Session = Session::getInstance();
	}
	
	/**
	* return false to allow otherwise array of message, type, delay
		0	=> 'Visitor',
		1	=> '1. User',
		2	=> '2. Moderator',
		3	=> '3. Editor',
		4	=> '4. Administrator',
		5	=> '5. VIP',
		6	=> '6. Bot',
		7	=> '7. Demo'
	*/
	public function admin($area = 'login', $action = 'view', $rs = array(), $post = array(), $table = false, $id = 0) {
		$delay = 2000;
		$type = 'shield';
		$text = false;
		$redirect = '';

		if ($this->Session->GroupID <= 1) {
			$text = 'You are simple user, please write a letter to: '.MAIL_EMAIL.' if you want to be part of our website.';
		} else {
			switch ($area) {
				
				case 'menu': //
				case 'tree': //
				case 'pages': 
				case 'content': // content block
				case 'entries':
				case 'module': // save, delete, act, sort (you may separate by module table)
				case 'grid': // possible to separate by table
				case 'forum':
					$allowed = array(
						'view'		=> array(2,3,4,7),
						'save'		=> array(2,3,4,7),
						'edit'		=> array(2,3,4,7),
						'activate'	=> array(2,3,4,7),
						'sort'		=> array(2,3,4,7),
						'delete'	=> array(2,3,4),
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' this '.$area;
				break;
				case 'comments':
					$allowed = array(
						'view'		=> array(2,3,4,7),
						'save'		=> array(2,3,4),
						'edit'		=> array(2,3,4),
						'delete'	=> array(2,3,4),
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' these comments';
				break;
				case 'categories':
					$allowed = array(
						'save'		=> array(2,4,7),
						'edit'		=> array(2,4),
						'delete'	=> array(2,4)
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' categories';
				break;
				case 'email':
					$allowed = array(
						'save'		=> array(4),
						'edit'		=> array(2,7,4),
						'send'		=> array(4),
						'import'	=> array(2,7,4),
						'delete'	=> array(4)
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' emails';
				break;
				case 'settings': // edit
				case 'languages': // save, activate, add, delete
				case 'templates': // add, delete, save, activate
				case 'currencies': // save, activate
				case 'modules': // save, activate, sort, add, delete
				case 'files': // save, upload, create, rename, delete
				case 'snippets': // save, delete
				case 'geo': // save, edit, delete
				case 'help':
					if ($action=='view' || $this->Session->GroupID===ADMIN_GROUP) break;
					$text = 'Sorry, you have no rigths to '.$action.' '.$area.'';
				break;
				case 'visual': // move, add, edit, preview, delete, commit, discard
					$allowed = array(
						'preview'	=> array(2,7,4),
						'move'		=> array(4),
						'add'		=> array(4),
						'edit'		=> array(4),
						'delete'	=> array(4),
						'commit'	=> array(4),
						'discard'	=> array(4)
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' '.$area.'';
				break;
				case 'eval': // run
					if ($action=='view' || $this->Session->GroupID===ADMIN_GROUP) break;
					$text = 'Sorry, you have no rigths to evaluate custom PHP scripts';
				break;
				case 'qry': // run, export
					if ($action=='view' || $this->Session->GroupID===ADMIN_GROUP) break;
				//	if ($this->Session->UserID===SUPER_ADMIN) break;
					$text = 'Sorry, you have no rigths to commit anything in database';
				break;
				case 'db': // backup, restore, upload, edit
				case 'log': // revert, delete, view
					if ($action=='view' || $this->Session->GroupID===ADMIN_GROUP) break;
				//	if ($this->Session->UserID===SUPER_ADMIN) break;
					$text = 'Sorry, you have no rigths to '.$action.' '.$area.'';
				break;
				case 'vars':
					$allowed = array(
						'save'		=> array(3,4),
						'delete'	=> array(3,4),
					);
				//	if (in_array($this->Session->UserID, array(1,2,3,5))) break;
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' variables';
				break;
				case 'lang':
					$allowed = array(
						'save'		=> array(2,7,3,4),
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to use vocabulary';
				break;
				case 'ai':
					$allowed = array(
						'save'		=> array(2,4,7),
						'delete'	=> array(2,4)
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' emails';
				break;
				case 'users':
					$allowed = array(
						'view'		=> array(2,7,3,4),
						'save'		=> array(4),
						'edit'		=> array(4),
						'delete'	=> array(4)
					);
					if (($action=='edit' || $action=='view') && $id==$this->Session->UserID) break;
					/*
					elseif ($this->Session->UserID!=SUPER_ADMIN && $this->Session->profile['www'] && ($action=='edit' || $action=='delete' || $action=='save')) {
						
					}
					*/
					elseif (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to '.$action.' this user';
				break;
				case 'download':
					$allowed = array(
						'file'		=> array(4)
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'You cannot download this file: '.basename($rs).'';
				break;
				case 'quick_edit':
					$allowed = array(
						'save'		=> array(2,7,3,4),
					);
					if (in_array($this->Session->GroupID, $allowed['save'])) break;
					$text = 'You are cannot do that';
				break;
				case 'stats':
					$allowed = array(
						'delete'	=> array(7,4),
						'block'		=> array(7,4),
					);
					if (!$allowed[$action] || in_array($this->Session->GroupID, $allowed[$action])) break;
					$text = 'Sorry, you have no rigths to control stats';
				break;
				default:
					if ($this->Session->GroupID==3 && $this->Session->login_done) {
						if (ADMIN) {
							$redirect = '/';
						}
						
						break;
					}
					$allowed = array(
						'view'		=> array(2,4,7),
					);
					if (in_array($this->Session->GroupID, $allowed['view'])) break;
					$text = 'You cannot go to admin panel';
				break;
			}
		}
		if ($text) {
			return array(
				'text'	=> $text,
				'delay'	=> $delay,
				'type'	=> $type,
				'redirect'	=> $redirect
			);
		}
		return false;
	}
	public function user($area = 'login', $action = 'view', $rs = array(), $post = array(), $table = false, $id = 0) {
		$text = false;
		if ($this->Session->GroupID==0) {
			$text = 'Please login to system';	
		}
		switch ($area) {
				
		}
		return $text;
	}
}
