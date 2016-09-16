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
* @file       mod/User.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

class User {

	private $register_params = array();	

	public function __construct() {
		$this->Index = Index::getInstance();
		$this->Session =& $this->Index->Session;
		if (!$this->Session) $this->Session = Session();
		$this->UserID =& $this->Session->UserID;
		$this->Lang =& $this->Session->Lang;
	}

	public function getContent($params = array()) {
		$ret = true;
		if (url(1)=='register' || url(1)=='profile') {
			if ($this->Session->Country && $this->Session->Country!='un' && !isset($_POST['profile']['country'])) {
				$_POST['profile']['country'] = $this->Session->Country;
			}
			if ($this->Session->City && !isset($_POST['profile']['city'])) {
				$_POST['profile']['city'] = $this->Session->City;
			}
		}
		/*
		// Call intro text or anything to $list or $row
		if (Factory::call('content')->init()->getContent(url(1),url(2), false)) {
			$ret = true;	
		}
		*/
		
		$this->Index->My->actions();
		
		switch (url(1)) {
			case 'unsubscribe':
				if (!$this->checkMy(url(1),$params)) {
					$this->set('form_errors',$this->doUnsubscribe());
				}
				$this->Index->displayFile('user/unsubscribe.tpl');
			break;
			case 'register':
				if (!$this->checkMy(url(1),$params)) {
					if ($this->UserID && !IS_ADMIN) return URL::Redirect('?user&profile');
					$this->Index->setVar('title',lang('New user registration'));
					$errors = $this->doRegister($params);
					if ($errors) $this->set('form_errors',$errors);
				}
				$this->Index->displayFile('user/register.tpl');
			break;
			case 'profile':
				if (!$this->checkMy(url(1),$params)) {
					if ($this->requireLogin()) break;
					if (!$this->UserID) return URL::Redirect('?user&register');
					//$this->Index->setVar('title',lang('Your profile'));
					$user = Data::getUser($this->UserID);
					$user['profile'] = array_map('strform',$user['profile']);
					if (!isset($_POST[URL_KEY_REGISTER])) {
						$_POST = array_merge($_POST, $user);
					} else {
						$_POST['profile']['age'] = $user['profile']['age'];
						$_POST['profile']['online'] = $user['profile']['online'];
					}
					$this->set('user',$user);
					$this->set('edit',true);
					$errors = $this->doRegister($params);
					if ($errors) $this->set('form_errors',$errors);
				}
				$this->Index->displayFile('user/profile.tpl');
			break;
			case 'login':
				if (!$this->checkMy(url(1),$params)) {
					if ($this->UserID && !IS_ADMIN && ($u = Data::link('logged_redirect'))) return URL::redirect($u);
					$this->Index->setVar('title',lang('Login to your profile'));
					$errors = $this->errors();
					$this->set('form_errors', $errors);
				}
				$this->Index->displayFile('user/login.tpl');
			break;
			case 'lostpass':
				if ($this->UserID && !IS_ADMIN) return URL::Redirect(Data::link('logged_redirect'));
				$this->Index->setVar('title',lang('Password reminder'));
				if (url(2) && url(3)=='code' && ($user = DB::row('SELECT login, firstname, lastname, main_photo, id FROM '.DB_PREFIX.'users u LEFT JOIN '.DB_PREFIX.'users_profile up ON up.setid=u.id WHERE u.id='.intval(url(2)).' AND u.code='.e(get('code',NULL,'x'))))) {
					$this->set('user',$user);
					$this->set('form_errors',$this->doLostPass2(url(2), $user['login']));
					$this->Index->displayFile('user/lostpass2.tpl');					
				} else {
					$this->set('form_errors',$this->doLostPass());
					$this->Index->displayFile('user/lostpass.tpl');					
				}
			break;
			default:
				if (url(1)) {
					if (is_file(FTP_DIR_TPL.'/user/'.($file = fixFileName(url(1)).'.tpl'))) {
						if ($this->requireLogin()) break;
						$this->checkMy(url(1),$params);
						$this->Index->displayFile('user/'.$file);
					}
					elseif (is_file(FTP_DIR_TPL.'/user/view.tpl') && ($id = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE login LIKE '.e(url(1))))) {
						$user = Data::getUser($id);
						$user['profile'] = array_map('strform',$user['profile']);
						$this->set('user',$user);
						$this->set('edit',$user['id']==$this->UserID);
						$this->set('wall',Data::DB('wall', $user['id']));
						$this->set('friends',Data::DB('friends', $user['id']));
						$this->Index->setVar('title',$user['profile']['firstname'].' ('.$user['profile']['age'].')');
						$this->Index->displayFile('user/view.tpl');	
					} else {
						$ret = false;	
					}
				} else {
					if (is_file(FTP_DIR_TPL.'/user/index.tpl')) {
						$this->Index->displayFile('user/index.tpl');
					} else {
						$ret = false;
					}
				}
			break;
		}
		if (!$ret && !Factory::call('content')->init()->getContent(url(1),url(2))) {
			$ret = false;
		}
		return $ret;
	}
	
	private function checkMy($key, $params = array()) {
		$method = 'user'.ucfirst($key);
		if (method_exists($this->Index->My,$method)) {
			if ($this->Index->My->$method($params)) {
				return true;
			}
		}
		return false;
	}
	
	/*
	private function actions($for = 0) {
		if (!$for) $for = $this->UserID;
		switch (post('act')) {
			case 'private-msg':
				if (!$this->UserID) {
					$this->set('private_not_sent', lang('You need to login first'));
					break;
				}
				if (!post('msg')) {
					$this->set('private_not_sent', lang('Please type the message'));
					break;
				}
				$data = array(
					'userid'	=> $this->UserID,
					'to_user'	=> $for,
					'from_user'	=> $this->UserID,
					'message'	=> post('msg'),
					'added'		=> time()
				);
				DB::insert('im',$data);
				$data = array(
					'userid'	=> $for,
					'to_user'	=> $for,
					'from_user'	=> $this->UserID,
					'message'	=> post('msg'),
					'added'		=> time()
				);
				DB::insert('im',$data);
				$this->set('private_sent', lang('Your message was delivered'));
			break;
			case 'wall-msg':
				if (!$this->UserID) {
					$this->set('wall_not_sent', lang('You need to login first'));
					break;
				}
				if (!post('msg')) {
					$this->set('wall_not_sent', lang('Please type the message'));
					break;
				}
				if (DB::one('SELECT 1 FROM '.DB_PREFIX.'wall WHERE from_user='.$this->UserID.' AND to_user='.$for.' AND message LIKE '.e(post('msg')))) {
					$this->set('wall_not_sent', lang('Same message was posted'));
					break;	
				}
				$data = array(
					'to_user'	=> $for,
					'from_user'	=> $this->UserID,
					'message'	=> post('msg'),
					'added'		=> time()
				);
				DB::insert('wall',$data);
				$this->set('wall_sent', lang('Your message is on the wall, thank you'));
			break;
			case 'profile-update':
				if ($this->saveProfile()) {
					return array('msg'=>lang('Your profile has been updated, %1.',Data::user($this->UserID, 'firstname')),'type'=>'success','delay'=>1500);
				} else {
					return array('msg'=>lang('Your profile has been saved with no changes made, %1.',Data::user($this->UserID, 'firstname')),'type'=>'info','delay'=>1500);	
				}
			break;
		}
	}
	*/
	
	/*
	public function json() {
		switch (url(1)) {
			case 'add2friends':
				if (!$this->UserID) return array('msg'=>lang('Please login first'),'type'=>'error');
				$id = get('add2friends');
				if (!$id) return array('msg'=>lang('User is not defined'),'type'=>'error');
				$data = array(
					'from_user'	=> $this->UserID,
					'to_user'	=> $id,
					'accepted'	=> 'N',
					'added'		=> time()
				);
				if (!DB::one('SELECT 1 FROM '.DB_PREFIX.'friends WHERE from_user='.$this->UserID.' AND to_user='.$id)) {
					DB::insert('friends',$data);
					return array('msg'=>lang('User %1 was added to your friends', Data::user($id, 'firstname')), 'type'=>'success','button_text'=>lang('Is your friend'));
				} else {
					DB::run('UPDATE '.DB_PREFIX.'friends SET added='.time().' WHERE from_user='.$this->UserID.' AND to_user='.$id);
					return array('msg'=>lang('%1 is already in your friends list',Data::user($id, 'firstname')),'type'=>'warning','button_text'=>lang('Is your friend'));	
				}
			break;
			case 'remove_friend':
				if (!$this->UserID) return array('msg'=>lang('Please login first'),'type'=>'error');
				$id = get('remove_friend');
				if (!$id) return array('msg'=>lang('User is not defined'),'type'=>'error');
				DB::run('DELETE FROM '.DB_PREFIX.'friends WHERE from_user='.$this->UserID.' AND to_user='.$id);
				return array('msg'=>lang('%1 was removed from your friends list',Data::user($id, 'firstname')),'type'=>'success','remove'=>true,'delay'=>1500);
			break;
			case 'move_files':
				if (!$this->UserID) break;
				self::moveFiles();
				$html = $this->Index->Smarty->fetch('includes/photos_edit.tpl');
				return array('html'=>$html);
			break;
			case 'delete_file':
				if (!$this->UserID) break;
				self::deleteFile($_REQUEST['file']);
			break;
			default:
				return array('msg'=>'Serious error, method: '.url(1).' does not present','type'=>'fatal','delay'=>5000);	
			break;
		}
	}
	*/
	
	private function requireLogin() {
		if (!$this->UserID) {
			$this->Index->setVar('title',lang('title_'.url(1)));
			$this->Index->displayFile('user/login.tpl');
			return true;
		}
		return false;
	}
	
	private function set($k,$v) {
		$this->Index->Smarty->assign($k,$v);	
	}
	private function error($e, $field = '') {
		$this->Session->setFormError($e,$field);
	}
	private function errors() {
		return $this->Session->getFormError();
	}
	
	public function doUnsubscribe() {
		if (get(URL_KEY_EMAIL) && !post(URL_KEY_EMAIL)) $_POST[URL_KEY_EMAIL] = $_GET[URL_KEY_EMAIL];
		
		if (!isset($_POST['unsubscribe'])) return false;
		
		if (!post(URL_KEY_EMAIL)) {
			$this->error(lang('Please enter your username or email address'), URL_KEY_EMAIL);
			return $this->errors();
		}
		if (!Parser::isEmail(post(URL_KEY_EMAIL))) {
			$this->error(lang('Incorrect e-mail address entered'), URL_KEY_EMAIL);
			return $this->errors();
		}
		if (!IS_ADMIN && $_SESSION['Captcha_sessioncode']) {
			if (!post('captcha')) {
				$this->error(lang('Please enter a verification code'), 'captcha');
				return $this->errors();
			}
			elseif (strtolower(trim(post('captcha')))!=strtolower(trim($_SESSION['Captcha_sessioncode']))) {
				$this->error(lang('Verification code is invalid'), 'captcha');
				return $this->errors();
			}
		}
		
		$data = DB::getAll('SELECT email, `group` FROM '.DB_PREFIX.'emails WHERE email LIKE '.e($_POST[URL_KEY_EMAIL]));
		if (!$data) {
			$this->error(lang('No such email found in our database'), URL_KEY_EMAIL);
			return $this->errors();
		} else {
			$aff = 0;
			foreach ($data as $row) {
				DB::run('UPDATE '.DB_PREFIX.'emails SET unsub=\'1\' WHERE email='.e($row['email']).' AND `group`='.e($row['group']));
				$aff += DB::affected();
			}
			if (get('campaign')) {
				DB::run('UPDATE '.DB_PREFIX.'emails_camp SET unsubs=unsubs+1 WHERE id='.(int)get('campaign'));	
			}
			if ($aff) {
				$this->Session->setMsg('success',lang('You have been unsubscribed'),'',2000);
			} else {
				$this->error(lang('Your E-mail is already unsubscribed'), URL_KEY_EMAIL);
				return $this->errors();
			}
		}
	}
	
	
	const
		FLOUD_FAILURE = 0,
		FLOUD_SUCCESS = 1,
		FLOUD_OTHER = 2,
		FLOUD_SLEEPING = 3,
		FLOUD_LOGOUT = 4
	;
	
	private function floudLogin($login, $password) {
		if (!FLOUD_ENABLED) return 0;
		$s_login = '';
		
		if ($login && FLOUD_LOGIN) $s_login = ' AND login LIKE '.e($login);
		else $s_login = ' AND login IS NOT NULL';
		
		$timesleep = time() - FLOUD_SLEEP_SECONDS;	
		$sql = 'SELECT COUNT(1) FROM '.DB_PREFIX.'logins WHERE ip='.(int)$this->Session->IPlong.$s_login.' AND logged>'.$timesleep.' AND success=\'0\'';
		$cnt = DB::one($sql);
		if ($cnt) {
			$total = 0;
			$logged = DB::one('SELECT logged FROM '.DB_PREFIX.'logins WHERE ip='.(int)$this->Session->IPlong.$s_login.' AND success=\'0\' AND logged>'.$timesleep.' ORDER BY logged');
			if (FLOUD_BLOCK_TIMES) {
				$total = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'logins WHERE ip='.(int)$this->Session->IPlong.$s_login.' AND success=\'0\'');
			}
			return array('times' => $cnt, 'last_time_logged' => $logged, 'total' => $total);
		}
		return false;
	}
	private function writeLogin($success, $userid, $login, $password = '') {
		if (!FLOUD_ENABLED && $success==self::FLOUD_SLEEPING) return;
		if ($success==self::FLOUD_SUCCESS) {
			DB::run('DELETE FROM '.DB_PREFIX.'logins WHERE ip='.(int)$this->Session->IPlong.' AND login IS NOT NULL');
			DB::run('DELETE FROM '.DB_PREFIX.'logins WHERE logged<'.(time()-FLOUD_SLEEP_SECONDS*10).' AND login IS NOT NULL');
		}
		else {
			DB::run('INSERT INTO '.DB_PREFIX.'logins VALUES ('.(int)$userid.','.e($login).','.(int)$this->Session->IPlong.','.time().',\''.$success.'\')');
		}
	}
	
	public function doEmailConfirm($id, $code) {
		if (!$id || !$code) return;
		$this->Session->email_confirm_try = true;
		$sql = 'UPDATE '.DB_PREFIX.'users SET active='.Site::ACCOUNT_ACTIVE.', code=\'\' WHERE id='.(int)$id.' AND code='.e($code).' AND active='.Site::ACCOUNT_CONFIRM;
		DB::run($sql);
		if (DB::affected()) {
			$this->Session->email_confirm_done = true;
			$rs = DB::row('SELECT login, password FROM '.DB_PREFIX.'users WHERE id='.(int)$id);
			$this->doLoginCookie($rs['login'], $rs['password']);
		}
	}

	public function doLoginCookie($login, $password) {
		Factory::call('user')->doLogin($login, $password, 0, 0, true, true);	
		if ($this->Session->login_error) {
			$this->doLogout(true);
		}
	}
	
	public function doLogin($login, $password, $remember = false, $notmypc = false, $fromCookie = false, $skipMsg = false, $login_get = false) {
		if (is_array($login) || is_array($password)) return false;
		
		if ($this->Session->login_try) return false;
		Site::$cache_enabled = false;
		$this->Session->login_try = true;
		$this->Session->login_cookie = $fromCookie;
		$this->Session->login_get = $login_get;
		$addTo = '';
		
		$focus = '';
		$delay = 2200;
		$redirect = '';
		if ($this->Session->login_get) $redirect = '?user&login';

		if (!$fromCookie) {
			$floud = self::floudLogin($login,$password);

			if ($floud && $floud['times'] >= FLOUD_TIMES) {
				$to_wait = $floud['last_time_logged'] - time() + FLOUD_SLEEP_SECONDS;
				if ($to_wait>0) {
					if (FLOUD_BLOCK_TIMES && $floud['total']>=FLOUD_BLOCK_TIMES) {
						exit;	
					}
					$this->Session->login_error = true;
					return $this->Session->setMsg('block',lang('Floud control activated. Please wait %1 seconds',$to_wait), URL_KEY_PASSWORD, 5000, $redirect);
				}
			}
		}
		$ok_statuses = Data::getArray('user_status_login');
		if (!$login) {
			$this->Session->login_error = true;
			return $this->Session->setMsg('key',lang('Please enter your username'), URL_KEY_LOGIN, $delay, $redirect);
		}
		elseif (strlen(trim($login))<USERNAME_MIN_LENGTH) {
			$this->Session->login_error = true;
			return $this->Session->setMsg('key',lang('Username must contain more than %1 characters',USERNAME_MIN_LENGTH-1), URL_KEY_LOGIN, $delay, $redirect);
		}
		if (!$password) {
			$this->Session->login_error = true;
			return $this->Session->setMsg('key',lang('Please enter your password'), URL_KEY_PASSWORD, $delay, $redirect);
		}
		elseif (strlen(trim($password))<PASSWORD_MIN_LENGTH) {
			$this->Session->login_error = true;
			return $this->Session->setMsg('key',lang('Password must contain more than %1 characters',PASSWORD_MIN_LENGTH-1), URL_KEY_PASSWORD, $delay, $redirect);
		}
		
		if (defined('ADMIN_LOGIN') && ADMIN_LOGIN && defined('ADMIN_PASSWORD') && ADMIN_PASSWORD && ADMIN_LOGIN==$login && ADMIN_PASSWORD==$password) {
			$this->Session->UserID = 1;
			$this->Session->ClassID = 1;
			$this->Session->Login = ADMIN_LOGIN;
			$this->Session->Password = self::password(ADMIN_PASSWORD, ADMIN_LOGIN);
			$this->Session->Active = 1;
			$this->Session->GroupID = ADMIN_GROUP;
			$this->Session->LastIP = '127.0.0.1';
			$this->Session->login_done = true;
			if (!$notmypc) {
				Site::setcookie(ucfirst(URL_KEY_LOGIN), ADMIN_LOGIN, COOKIE_LIFETIME);
				if ($remember) {
					$days = time() + (int)$remember;
					Site::setcookie(ucfirst(URL_KEY_PASSWORD), ADMIN_PASSWORD, COOKIE_LIFETIME);
				}
			} else {
				Site::setcookie(ucfirst(URL_KEY_LOGIN), '', COOKIE_LIFETIME);
				Site::setcookie(ucfirst(URL_KEY_PASSWORD), '', COOKIE_LIFETIME);
			}
			return $this->Session->setMsg('success',lang('Welcome back, %1',($this->Session->profile['firstname'] ? $this->Session->profile['firstname'] : $this->Session->Login)),'',2000,Data::link('admin_logged'));
		}
		$new_code = randomString(12);
		$sql = 'SELECT id, login, password, email, groupid, classid, ip, active, last_logged, main_photo, temp_time, code FROM '.DB_PREFIX.'users WHERE (login LIKE '.e($login).' OR email LIKE '.e($login).') AND password='.e($fromCookie ? $password : self::password($password,$login)).'';
		$rs = DB::fetch(DB::qry($sql));
		if (!$rs) {
			$this->Session->login_error = true;
			Site::setcookie(ucfirst(URL_KEY_LOGIN), '', -1);
			if (!$fromCookie) self::writeLogin(self::FLOUD_FAILURE,0,$login,$password);
			else return false;
			return $this->Session->setMsg('error',lang('Wrong username or password'), URL_KEY_PASSWORD, $delay, $redirect);	
		}
		if (!$skipMsg) {
			if ($rs['active']==Site::ACCOUNT_DEACTIVATED) {
				$this->Session->login_error = true;
				return $this->Session->setMsg('user',lang('Your account was deactivated, please contact the site administration', $rs),'',4000,$redirect);
			}
			elseif ($rs['active']==Site::ACCOUNT_DELETED) {
				$this->Session->login_error = true;
				return $this->Session->setMsg('user',lang('Your account was deleted, please contact the site administration', $rs),'', 4000, $redirect);
			}
			elseif ($rs['active']==Site::ACCOUNT_CONFIRM) {
				$this->Session->login_error = true;
				//$rs['code'] = $new_code;
				Site::mail($rs['email'], 'user_confirm', $rs);
				//DB::run('UPDATE '.DB_PREFIX.'users SET code='.e($new_code).' WHERE id='.$rs['id']);
				return $this->Session->setMsg('clock',lang('Your need to confirm your email address, please check your email: {%email}', $rs),'',4000, $redirect);
			}
			elseif ($rs['active']==Site::ACCOUNT_BANNED) {
				if (time() < $rs['temp_time']) {
					$this->Session->login_error = true;
					return $this->Session->setMsg('trash',lang('Your account is banned, please contact the site administration', $rs),'',4000, $redirect);
				}
				else {
					$addTo .= ', active=1, temp_time=0';
				}
			}
		}
		
		$this->Session->login_done = true;
		if (!$skipMsg && $this->Session->UserID && $rs['id']==$this->Session->UserID) {
			 $this->Session->setMsg('success',lang('Welcome back, %1',($this->Session->profile['firstname'] ? $this->Session->profile['firstname'] : $this->Session->Login)),'',1000,Data::link($this->Session->GroupID==ADMIN_GROUP?'admin_logged':'user_logged'));
		}
		if (!$notmypc) {
			Site::setcookie(ucfirst(URL_KEY_LOGIN), $rs['login'], COOKIE_LIFETIME);
			if ($remember) {
				$days = time() + (int)$remember;
				Site::setcookie(ucfirst(URL_KEY_PASSWORD), $rs['password'], COOKIE_LIFETIME);
			}
		} else {
			Site::setcookie(ucfirst(URL_KEY_LOGIN), '', COOKIE_LIFETIME);
			Site::setcookie(ucfirst(URL_KEY_PASSWORD), '', COOKIE_LIFETIME);
		}
		
		if (!$fromCookie) self::writeLogin(self::FLOUD_SUCCESS,$rs['id'],$rs['login'],$password);
		
		$this->Session->login($rs, $new_code, $addTo);
		if ($skipMsg) return true;
		return $this->Session->setMsg('success',lang('Welcome back, %1',($this->Session->profile['firstname'] ? $this->Session->profile['firstname'] : $this->Session->Login)),'',1000,Data::link($this->Session->GroupID==ADMIN_GROUP?'admin_logged':'user_logged'));
	}
	
	public function doLogout($fromCookie=false) {
		$this->Session->logout_try = true;
		
		if (!$this->Session->UserID) return false;
		Site::$cache_enabled = false;
		if ($fromCookie) Site::setcookie(ucfirst(URL_KEY_LOGIN), '', 0);
		Site::setcookie(ucfirst(URL_KEY_PASSWORD), '', 0);
		$this->Session->Login = $this->Session->Password = $this->Session->Active = '';
		$this->Session->userLeft($this->Session->UserID);
		$this->Session->UserID = $this->Session->SubID = $this->Session->GroupID = $this->Session->ClassID = 0;
		unset($_SESSION['facebook']);
		Site::hook('Session::logout');
	}

	
	public function doFacebook(&$facebook, $id) {

		if (!($u = @$_SESSION['facebook'])) {
			
			try {
				//$q = 'SELECT birthday, birthday_date, uid, name, timezone, verified, first_name, middle_name, last_name, sex, locale, username, likes_count ,wall_count, pic, languages, email, friend_count, friend_request_count, subscriber_count, wall_count, website, is_blocked, interests, current_location FROM user WHERE uid=\''.$id.'\'';
				// payment_instruments
				/*
				$u = $facebook->api(array(
					 'method' => 'fql.query',
					 'query' => $q,
				));
				$u = $u[0];
				*/

				$u = $facebook->api('/me');
				$u['token'] = $facebook->getAccessToken();
				if (isset($u['birthday_date'])) $u['age'] = Date::age($u['birthday_date']);
				//$u['friends'] = $facebook->api('/'.$id.'/friends');
				$u['friends'] = $facebook->api('/'.$id.'/friends');
				if (!isset($u['friend_count'])) {
					$f = $facebook->api(array(
						 'method' => 'fql.query',
						 'query' => 'SELECT friend_count FROM user WHERE uid = me()',
					));
					$u['friend_count'] = $f[0]['friend_count'];
				}
				$u['logout'] = str_replace(DOMAIN.'%2F',DOMAIN.urlencode(URL::ht('?logout').(FRIENDLY ? '/' : '&')),$facebook->getLogoutUrl());
				
				if ($u && $u['email']) {
					$_SESSION['facebook'] = $u;
				}
			} catch (Exception $e) {
				return false;
			}
		}
		
		if ($this->UserID) return;
		
		if ($u && @$u['email'] && !$this->Session->Facebook) {
			$data = DB::row('SELECT * FROM '.DB_PREFIX.'users LEFT JOIN '.DB_PREFIX.'users_profile ON setid=id WHERE (facebook='.e($id).' OR email LIKE '.e($u['email']).')');
			if (!$u['current_location']) $u['current_location'] = array();
			$profile = array(
				'firstname'	=> $u['first_name'],
				'lastname'	=> $u['last_name'],
				'gender'	=> ($u['gender']=='male' ? 'M' : 'F'),
				'dob'		=> Date::td(strtotime($u['birthday_date'])),
				'www'		=> $u['website'],
				'lat'		=> $u['current_location']['latitude'],
				'lng'		=> $u['current_location']['longitude'],
				'streeet'	=> $u['current_location']['street'],
				'street2'	=> $u['current_location']['city'].' '.$u['current_location']['state'].' '.$u['current_location']['zip'].' '.$u['current_location']['country'],
				'zip'		=> $u['current_location']['zip'],
				'options'	=> serialize(array_merge((array)unserialize($data['options']), array(
					'friend_count'	=> $u['friend_count'],
					'friend_request_count'	=> $u['friend_request_count'],
					'subscriber_count'		=> $u['subscriber_count'],
					'interests'				=> $u['interests'],
					'current_location'		=> $u['current_location'],
				))),
				'fb_access_token'	=> $u['token']
			);
			if ($u['current_location'] && $u['current_location']['country'] && ($id = DB::one('SELECT id FROM '.DB_PREFIX.'geo_countries WHERE name_en LIKE '.e($u['current_location']['country'])))) {
				$profile['country'] = $id;
				if ($u['current_location']['state'] && ($id = DB::one('SELECT number FROM '.DB_PREFIX.'geo_states WHERE country_id='.$id.' AND (name_en LIKE '.e($u['current_location']['state'].'%').' OR name_ee LIKE '.e($u['current_location']['state'].'%').')'))) {
					$profile['state'] = $id;
					if ($u['current_location']['state'] && ($id = DB::one('SELECT number FROM '.DB_PREFIX.'geo_cities WHERE country_id='.$profile['country'].' AND state_number='.$id.' AND name_en LIKE '.e($u['current_location']['city'])))) {
						$profile['city'] = $id;
					}
				}
			}
			
			if (!$data) {
				if (!@$u['username']) $u['username'] = $u['email'];
				$password = self::generatePassword(6);
				$_password = self::password($password, $u['username']);
				$login = $u['username'];
				$email = @$u['email'];
				DB::insert('users',array(
					'login'		=> $login,
					'groupid'	=> 1,
					'classid'	=> 0,
					'email'		=> $email,
					'main_photo'=> '',
					'password' 	=> $_password,
					'registered'=> time(),
					'ip'		=> $this->Session->IPlong,
					'active'	=> 1,
					'notes'		=> lang('_$Registered using Facebook API (%1) %2 [%3]',$u['name'],$u['link'],$u['locale']),
					'status'	=> 1,
					'code'		=> randomString(12),
					'facebook'	=> $id
				));
				$data['id'] = DB::id();
				//$data['password_plain'] = $password;
				$profile['setid'] = $data['id'];
				DB::insert('users_profile',$profile);
				
				$this->Session->mail = array(
					'type' 	=> 'user_registered',
					'email' => $data['email'],
					'data'	=> $data
				);
				
			} else {
				if ($u['email'] && !$data['email']) {
					DB::update('users',array(
						'email'	=> $u['email'],
					),$data['id']);
				}
				DB::update('users_profile',$profile,$data['id'],'setid');
			}
			$this->Session->login($data, randomString(12));
		}	
	}
	
	public static function password($password,$login='') {
		return sha1('^gsdg'.trim($password).'CVV%$');
	}
	
	public static function generatePassword($length = 8, $possible = false) {
		$password = '';
		if (!$possible) $possible = '2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ';
		$maxlength = strlen($possible);
		if ($length > $maxlength) $length = $maxlength;
		$i = 0; 
		while ($i < $length) { 
			$char = substr($possible, mt_rand(0, $maxlength-1), 1);
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
	
	public function doLostPass() {
		if (!isset($_POST[URL_KEY_LOSTPASS])) return false;
		$this->Session->initFormError();
		
		if (!strlen(trim($_POST[URL_KEY_EMAIL_LOGIN]))) {
			$this->error(lang('Please enter your username or email address'), URL_KEY_EMAIL_LOGIN);
			return $this->errors();
		}

		if (!IS_ADMIN && $_SESSION['Captcha_sessioncode']) {
			if (!post('captcha')) {
				$this->error(lang('Please enter a verification code'), 'captcha');
				return $this->errors();
			}
			elseif (strtolower(trim(post('captcha')))!=strtolower(trim($_SESSION['Captcha_sessioncode']))) {
				$this->error(lang('Verification code is invalid'), 'captcha');
				return $this->errors();
			}
		}
		$rs = false;
		if (($l = post(URL_KEY_EMAIL_LOGIN)) && !is_array($l) && ($rs = DB::row('SELECT * FROM '.DB_PREFIX.'users LEFT JOIN '.DB_PREFIX.'users_profile ON id=setid WHERE email LIKE '.e($l).' OR login LIKE '.e($l)))) {
			unset($l);
		}
		if ($rs) {
			Site::mail($rs['email'], 'user_lostpass', $rs);
			$this->Session->setMsg('success',lang('Please check your email for furture instructions',strform($l)));
		} else {
			$this->error(lang('Cannot find such username or email'), URL_KEY_EMAIL);
			return $this->errors();
		}
	}
	
	public function doLostPass2($id, $login) {
		if (!isset($_POST[URL_KEY_LOSTPASS])) return false;
		$this->Session->initFormError();
		
		if (strlen(trim(post('password')))<3) {
			$this->error(lang('Password must contain more than %1 characters',2), 'password');	
		}
		elseif (post('password')!==post('re_password')) {
			$this->error(lang('Passwords don\'t match'), 're_password');
		}
		$errors = $this->errors();
		if ($errors) {
			return $errors;
		} else {
			DB::run('UPDATE '.DB_PREFIX.'users SET password='.e(self::password(post('password'),$login)).' WHERE id='.(int)$id);
			$this->doLogin($login, post('password'));
			$this->Index->My->userPasswordReminded($id, post('password'));
			$this->Session->setMsg('success',lang('Your password has been recovered',strform($login)),'',2000,'?');
		}
	}
	
	public static function diffLogin($login) {
		/*
		$cyr = array('А', 'а', 'В', 'Е', 'е', 'К', 'М', 'Н', 'О', 'о', 'Р', 'р', 'С', 'с', 'Т', 'Х', 'х');
		$lat = array('A', 'a', 'B', 'E', 'e', 'K', 'M', 'H', 'O', 'o', 'P', 'p', 'C', 'c', 'T', 'X', 'x');			
		$lat_login = str_replace($cyr,$lat,$login);
		$cyr_login = str_replace($lat,$cyr,$login);
		*/
		$lat_login = Parser::CyrLat($login);
		$cyr_login = Parser::CyrLat($login, false);
		
		return array('cyr'=>$cyr_login,'lat'=>$lat_login);
	}
	
	public static function isLoginExists($l, $id=0) {
		$dl = self::diffLogin($l);
		$sql = 'SELECT 1 FROM '.DB_PREFIX.'users WHERE TRUE'.($id?' AND id!='.(int)$id:'').' AND (login LIKE '.e($l).' OR login LIKE '.e($dl['lat']).' OR login LIKE '.e($dl['cyr']).')';
		return DB::one($sql);
	}
	
	
	public static function isEmailExists($email, $id=0) {
		return DB::one('SELECT 1 FROM '.DB_PREFIX.'users WHERE email LIKE '.e($email).($id?' AND id!='.(int)$id:''));
	}
	
	public function setRegisterParams($params) {
		$this->register_params = $params;	
	}
	
	public function doRegister($params = array()) {
		$params = array_merge($this->register_params, $params);
		if (!isset($params['userid'])) $params['userid'] = $this->UserID;

	//	Factory::call('uploadify',$this->Index)->name('profile')->id($params['userid'])->form('upload');
		
		if (!isset($_POST[URL_KEY_REGISTER])) return false;
		
		if (!$params['userid'] && defined('NO_USER_REGISTER') && NO_USER_REGISTER) {
			$this->error(lang('User registration is temporarily disabled'), 'login');
			return $this->errors();	
		}
		
		$this->Session->initFormError();
		
		if (post(URL_KEY_LOGIN)=='[[:EMAIL:]]') $_POST[URL_KEY_LOGIN] = $_POST[URL_KEY_EMAIL];
		
		if ($params['userid']):
			if (isset($_POST[URL_KEY_EMAIL]) && $_POST[URL_KEY_EMAIL] && $this->Session->Email!=$this->Session->Login) {
				if (!strlen(trim(post(URL_KEY_EMAIL)))) {
					$this->error(lang('Please enter your e-mail address'), URL_KEY_EMAIL);	
				}
				elseif (!Parser::isEmail(post(URL_KEY_EMAIL))) {
					$this->error(lang('Email address is invalid'), URL_KEY_EMAIL);	
				}
				elseif (!IS_ADMIN && self::isEmailExists(post(URL_KEY_EMAIL),$params['userid'])) {
					$this->error(lang('Such email address already has one of our registered users, please choose another'), URL_KEY_EMAIL);
				}
			}
			if (post(URL_KEY_PASSWORD) && strlen(trim(post(URL_KEY_PASSWORD)))<PASSWORD_MIN_LENGTH) {
				$this->error(lang('Password must contain more than %1 characters',PASSWORD_MIN_LENGTH-1), 'password');
			}
			elseif (post(URL_KEY_PASSWORD) && post(URL_KEY_PASSWORD)!=post('re_'.URL_KEY_PASSWORD)) {
				$this->error(lang('Passwords don\'t match'), 're_'.URL_KEY_PASSWORD);
			}
			elseif (post(URL_KEY_PASSWORD) && !post('cur_'.URL_KEY_PASSWORD)) {
				$this->error(lang('Enter your current password'), 'cur_'.URL_KEY_PASSWORD);
			}
			elseif (post(URL_KEY_PASSWORD) && self::password(post('cur_'.URL_KEY_PASSWORD), $this->Session->Login)!=$this->Session->Password) {
				$this->error(lang('Current password was typed wrong'), 'cur_'.URL_KEY_PASSWORD);
			}
			
			$this->Index->My->checkProfile();
			$errors = $this->errors();

			if ($errors) {
				return $errors;
			} else {
				$data = array();
				if (isset($_POST[URL_KEY_EMAIL])) {
					$data = array (
						'email'		=> trim(post(URL_KEY_EMAIL)),
						'code'		=> randomString(12)
					);
					$this->Session->set('Email',post(URL_KEY_EMAIL));
					$this->set('User',$this->Session->get());
				}
				if (post(URL_KEY_PASSWORD) && post(URL_KEY_PASSWORD)===post('re_'.URL_KEY_PASSWORD) && self::password(post('cur_'.URL_KEY_PASSWORD), $this->Session->Login)===$this->Session->Password) {
					$this->Session->Password = self::password(post(URL_KEY_PASSWORD),$this->Session->Login);
					$data['password'] = $this->Session->Password;
				}
				if ($data) DB::update('users',$data,$params['userid']);
				$data['success'] = true;
				$data['id'] = $params['userid'];
				if (post(URL_KEY_PASSWORD)) $this->doLogin(post(URL_KEY_LOGIN), post(URL_KEY_PASSWORD));
				$this->saveProfile();
			//	self::moveFiles();

				if (isset($_FILES) && $_FILES['main_photo'] && $_FILES['main_photo']['tmp_name']) {
					$main_photo = Factory::call('uploadify',$this->Index)->name('profile')->id($data['id'])->upload('main_photo');
					if ($main_photo) {
						Session()->Photo = $main_photo;
						$this->Index->Smarty->assign('User', Session()->get());
						DB::run('UPDATE '.DB_PREFIX.'users SET main_photo='.e($main_photo).' WHERE id='.$data['id']);
					}
					elseif ($e = Factory::call('uploadify')->error()) {
						// how to display this error after form submit? No idea yet.
						d($e);
					}
				}
			
				$this->Index->My->userUpdated($data['id'], $data);
				$this->set('top_message',array(
					'type'	=> 'success',
					'text'	=> lang('Your profile has been updated').(isset($data['password'])?lang('<br>and the password was changed'):'')
				));

				$this->Session->setMsg('success',lang('Your profile was updated',strform($this->Session->Login)),'',(isset($params['link_delay'])?$params['link_delay']:2000),(isset($params['link_updated']) ? $params['link_updated'] : Data::link('user_updated')));
				//Site::mail($data['email'], 'user_updated', $data);
				return array(
					'text'	=> lang('Your profile is updated'),
					'type'	=> 'tick',
					'redirect'=> (isset($params['link_updated']) ? $params['link_updated'] : Data::link('user_updated')),
					'reload'=> true,
					'delay' => (isset($params['link_delay'])?$params['link_delay']:2000)
				);
			}
			
			
		else:
		
			if (isset($params['groupid'])) $groupid = $params['groupid'];
			else $groupid = 1;
			if (isset($params['classid'])) $classid = $params['classid'];
			else $classid = 0;
			if (isset($params['active'])) $active = $params['active'];
			else $active = Site::ACCOUNT_ACTIVE;
			
			if (!isset($_POST[URL_KEY_LOGIN]) && !isset($_POST[URL_KEY_EMAIL])) {
				$this->error('Invalid register form, &quot;'.URL_KEY_LOGIN.'&quot; and &quot;'.URL_KEY_EMAIL.'&quot; fields are missing', 'login');
				return $this->errors();
			}			
			
			if (!isset($_POST[URL_KEY_PASSWORD])) {
				$_POST[URL_KEY_PASSWORD] = randomString(5);
				$_POST['re_'.URL_KEY_PASSWORD] = $_POST[URL_KEY_PASSWORD];
			}
			
			
			if (!post(URL_KEY_EMAIL) || is_array(post(URL_KEY_EMAIL))) {
				$this->error(lang('Please enter your e-mail address'), URL_KEY_EMAIL);
			}
			elseif (!Parser::isEmail(post(URL_KEY_EMAIL))) {
				$this->error(lang('Email address is invalid'), URL_KEY_EMAIL);
			}
			elseif (self::isEmailExists(post(URL_KEY_EMAIL))) {
				$this->error(lang('Such email address is already registered, please %1click here%2 if you forgot your password','<a href="'.URL::ht('?user&lostpass').'">','</a>'), URL_KEY_EMAIL);	
			}
			elseif (isset($_POST['re_'.URL_KEY_EMAIL]) && !is_array($_POST['re_'.URL_KEY_EMAIL]) && !strlen(trim($_POST['re_email']))) {
				$this->error(lang('Please confirm your email address'), 're_email');
			}
			elseif (isset($_POST['re_'.URL_KEY_EMAIL]) && !is_array($_POST['re_'.URL_KEY_EMAIL]) && post(URL_KEY_EMAIL) && $_POST[URL_KEY_EMAIL]!=$_POST['re_email']) {
				$this->error(lang('Email address is not confirmed, please enter a valid confirmation email'), 're_email');
			}
			
			elseif (!post(URL_KEY_LOGIN) || is_array(post(URL_KEY_LOGIN))) {
				$this->error(lang('Please enter your new username'), 'login');	
			}
			elseif (strlen(trim(post(URL_KEY_LOGIN)))<USERNAME_MIN_LENGTH) {
				$this->error(lang('Username must contain more than %1 characters',USERNAME_MIN_LENGTH-1), 'login');	
			}
			/*
			elseif (strstr(post('login'),'@')) {
				$this->error(lang('Username cannot contain @ sign'), 'login');	
			}
			*/
			elseif (self::isLoginExists(post(URL_KEY_LOGIN))) {
				$this->error(lang('Such username already exists, please choose another'), 'login');
			}
			
			if (!post(URL_KEY_PASSWORD) || is_array(post(URL_KEY_PASSWORD))) {
				$this->error(lang('Please enter your password'), 'password');	
			}		
			elseif (strlen(trim(post(URL_KEY_PASSWORD)))<PASSWORD_MIN_LENGTH) {
				$this->error(lang('Password must contain more than %1 characters',PASSWORD_MIN_LENGTH-1), 'password');
			}
			elseif (!isset($params['password']) && isset($_POST['re_'.URL_KEY_PASSWORD]) && !$_POST['re_'.URL_KEY_PASSWORD]) {
				$this->error(lang('Please re-enter your password again'), 're_password');	
			}
			elseif (!isset($params['password']) && isset($_POST['re_'.URL_KEY_PASSWORD]) && post(URL_KEY_PASSWORD)!==$_POST['re_'.URL_KEY_PASSWORD]) {
				$this->error(lang('Passwords don\'t match'), 're_password');	
			}
			
			if ($_SESSION['Captcha_verified']) {
				$this->Index->Smarty->assign('captcha_verified', true);
			}
			elseif ((isset($_SESSION['Captcha_sessioncode']) || isset($params['captcha'])) && $_SESSION['Captcha_sessioncode']) {
				if (!post('captcha')) {
					$this->error(lang('Please enter a verification code'), 'captcha');
				}
				elseif (strtolower(trim(post('captcha')))!==strtolower(trim($_SESSION['Captcha_sessioncode']))) {
					$this->error(lang('Verification code is invalid'), 'captcha');
				} else {
					$_SESSION['Captcha_verified'] = true;
					$this->Index->Smarty->assign('captcha_verified', true);
				}
			}
			
			
			$this->Index->My->checkProfile();
			$errors = $this->errors();
			
			
			if ($errors) {
				return $errors;
			} else {

				$data = array (
					'login'		=> post(URL_KEY_LOGIN),
					'groupid'	=> $groupid,
					'classid'	=> $classid,
					'email'		=> trim(post(URL_KEY_EMAIL)),
					'password'	=> self::password(post(URL_KEY_PASSWORD),post(URL_KEY_LOGIN)),
					'registered'=> time(),
					'logged'	=> 0,
					'ip'		=> ip2long(Session::getIP()),
					'active'	=> $active,
					'userid'	=> $_SESSION['referal'],
					'code'		=> randomString(12)
				);
				
				DB::insert('users',$data);
				$id = DB::id();
				if (isset($_FILES) && $_FILES['main_photo'] && $_FILES['main_photo']['tmp_name']) {
					$main_photo = Factory::call('uploadify',$this->Index)->name('profile')->id($id)->upload('main_photo');
					if ($main_photo) {
						DB::run('UPDATE '.DB_PREFIX.'users SET main_photo='.e($main_photo).' WHERE id='.$id);
					}
					elseif ($e = Factory::call('uploadify')->error()) {
						// how to display this error after form submit? No idea yet.
						d($e);	
					}
				}
				
				DB::run('INSERT INTO '.DB_PREFIX.'users_profile (setid) VALUES ('.$id.')');
				$this->saveProfile($id);
			//	self::moveFiles();
				$this->set('top_message',array(
					'type'	=> 'success',
					'text'	=> lang('Thank you, your profile was created')
				));
				$data['success'] = true;
				$data['id'] = $id;
				$data['password'] = post(URL_KEY_PASSWORD);
				
				$link = (isset($params['link_registered'])?$params['link_registered']:Data::link('user_registered'));
				if ($active==Site::ACCOUNT_CONFIRM) {
					$link .= '&email='.$data['email'];
				}
				$link = '?'.trim($link,'?');
					
				if (!$this->Index->My->userInserted($id, $data)) {
					$data['profile'] = post('profile');
					if ($active==Site::ACCOUNT_CONFIRM) {
						Site::mail($data['email'], 'user_confirm', $data);
					} else {
						Site::mail($data['email'], 'user_registered', $data);
					}
					$this->Session->setMsg('success',lang('Thank you, your profile was created',strform($this->Session->Login)),'',(isset($params['link_delay'])?$params['link_delay']:2000),$link);
				}
				
				if (!$params['no_login']) {
					$this->doLogin(post(URL_KEY_LOGIN), post(URL_KEY_PASSWORD), false, false, false, true);
					return array(
						'text'	=> lang('Thank you for your participation, you are now logged in'),
						'type'	=> 'tick',
						'redirect'=> $link,
						'reload'=> true,
						'delay' => (isset($params['link_delay'])?$params['link_delay']:2000)
					);
				} else {
					return array(
						'text'	=> lang('Thank you for your participation'),
						'type'	=> 'tick',
						'redirect'=> $link,
						'reload'=> true,
						'delay' => (isset($params['link_delay'])?$params['link_delay']:2000)
					);
				}
			
				//SendMail(post('email'),'Your profile was created','Thank you for registering on '.HTTP_BASE.'<br><br>Your username is: '.post('login').'<br><br>See you on <a href="'.HTTP_BASE.'">'.HTTP_BASE.'</a>');
			}
		endif;
	}
	
	

	public function saveProfile($id = 0) {
		if (!post('profile')) return false;
		if (!$id) $id = Session()->UserID;
		if (!$id) return false;
		$data = post('profile');		
		if (isset($data['dob']) && is_array($data['dob'])) {
			$data['dob'] = sprintf('%04d-%02d-%02d 00:00:00',$data['dob']['Year'],$data['dob']['Month'],$data['dob']['Day']);
		}
		elseif (strpos($data['dob'],'/')) {
			$data['dob'] = Date::td(Date::toTimestamp($data['dob']));
			$_POST['profile']['dob'] = array(
			// 0000-00-00 00:00:
				'Day'	=> substr($data['dob'],8,2),
				'Month'	=> substr($data['dob'],5,2),
				'Year'	=> substr($data['dob'],0,4),
			);
		}
		if (method_exists(Index()->My, 'saveProfile')) Index()->My->saveProfile($id, $data);
		/*
		if (post('profile','looking_for')) {
			$data['looking_for'] = join(',',array_check($data['looking_for'], array_keys(Data::getArray('looking_for'))));
		}
		if (post('profile','looking_as')) {
			$data['looking_as'] = join(',',array_check($data['looking_as'], array_keys(Data::getArray('looking_as'))));
		}
		if (isset($data['country'])) Session()->setUserData('Country',$data['country']);
		if (isset($data['city'])) Session()->setUserData('City',$data['city']);
		*/
		$_data = array();
		foreach ($data as $k => $v) $_data[$k] = strjoin($v);
		if (DB::row('SELECT setid FROM '.DB_PREFIX.'users_profile WHERE setid='.$id)) {
			unset($_data['setid']);
			DB::update('users_profile',$_data,$id,'setid');
		} else {
			$data['setid'] = $id;
			DB::insert('users_profile',$_data);
		}
		return DB::affected();
	}
	
	private static function deleteFile($file) {
		$id = Session()->UserID;
		$arrSizes = Data::getArray('photo_sizes');
		$o_file = 'files/user/'.$id.'/'.$file;
		if (!is_file($o_file)) return false;
		foreach ($arrSizes as $i => $size) {
			@unlink('files/user/'.$id.'/th'.$i.'/'.$file);
		}
		@unlink($o_file);
	}
	
	private static function moveFiles() {
		$id = Session()->UserID;
		if (!$id) return false;
		$from = 'files/temp/'.Session()->SID.'/';
		$to = 'files/user/'.$id.'/';
		if (!is_dir($from)) return false;
		if (!is_dir($to)) mkdir($to, 0777);
		$dh = opendir($from);
		while (($file = readdir($dh))!==false) {
			if ($file=='.' || $file=='..') continue;
			rename($from.$file, $to.$file);
			self::cropImages($to, $file);
		}
		@rmdir($from);
	}
	
	private static function cropImages($path, $file) {
		@ignore_user_abort(0);
		@set_time_limit(3600);
		$im = Image::Factory('GD');
		$arrSizes = Data::getArray('photo_sizes');
		$nameonly = nameOnly($file);
		$ext = ext($file);
		$new_file = File::getUnique($path.'th1/',$nameonly,$ext);
		if ($new_file!=$file) {
			rename($path.$file, $path.$new_file);
			$file = $new_file;
		}
		$im->load($path.$file);
		foreach ($arrSizes as $i => $size) {
			$dir = $path.'th'.$i.'/';
			if (!is_dir($dir)) mkdir($dir,0777);
			$im->scaleByLength(max(array($size[0], $size[1])));
			$im->save($dir.$file);
			$im->set_new_size();
			@clearstatcache();
		}
		$im->free();
		@unlink($path.$new_file);
	}
	
	/**
	* Used in admin area
	*/
	public static function doOpen($id, &$Admin = false) {
		
	}
	public static function doInsert($id, &$Admin = false) {
		if (USE_IM) Factory::call('im')->insertUser($id);
		$data = post('data','',array());
		if ($data['profile']['options']['subject'] && $data['profile']['options']['message']) {
			SendMail($data['email'], $data['profile']['options']['subject'], $data['profile']['options']['message']);
		}
	}
	public static function doUpdate($id, &$Admin = false) {
		$data = post('data','',array());
		if (isset($data['resend']) && $data['profile']['options']['subject']) {
			SendMail($data['email'], $data['profile']['options']['subject'], $data['profile']['options']['message']);
			$Admin->msg_text2 .= 'Email was sent also';
		}
	}

	public static function doDelete($id, &$Admin = false) {
		$id = (int)$id;
		$rs = DB::row('SELECT * FROM '.DB_PREFIX.'users WHERE id='.$id);
		$del_id = DB::one('SELECT id FROM '.DB_PREFIX.'users WHERE login='.e('deleted_'.$rs['login']));
		if ($del_id) {
			DB::run('DELETE FROM '.DB_PREFIX.'users WHERE id='.$del_id);
			DB::run('DELETE FROM '.DB_PREFIX.'users_profile WHERE setid='.$del_id);
		}
		$sql = 'UPDATE '.DB_PREFIX.'users SET active=2, email=REPLACE(email,\'@\',\'#\'), login=CONCAT(\'deleted_\',REPLACE(login,\'deleted_\',\'\'),\'\'), password=\'\', code=\'\', notes=CONCAT(notes,\'\nDeleted on '.date('H:i d.m.Y').' by '.Session()->Login.' (id:'.Session()->UserID.')\') WHERE id='.$id;
		DB::run($sql);
		$aff = DB::affected();
		if ($aff) {
			Factory::call('im')->deleteUser($id);
			Site::mail($rs['email'], 'user_deleted', $rs);
		}
		return $aff;
	}
	
}