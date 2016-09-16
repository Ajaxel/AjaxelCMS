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
* @file       inc/Email.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

if (!defined('EMAIL_CHARSET')) define ('EMAIL_CHARSET', 'UTF-8');
if (!defined('USE_SMTP')) define ('USE_SMTP',false);
if (!defined('EMAIL_STYLE')) define ('EMAIL_STYLE', '');
if (!defined('EMAIL_PIXEL')) define ('EMAIL_PIXEL', false);


final class Email {
	
	public static $campaign = '';
	
	public static $with_plain = true;
	public static $base64 = false;
	
	public static $email_col = 'email';
	
	public static $mail_email = MAIL_EMAIL;
	public static $mail_name = MAIL_NAME;
	
	public static $use_smtp = USE_SMTP;
	
	public static $smtp_host = SMTP_HOST;
	public static $smtp_port = SMTP_PORT;
	public static $smtp_username = SMTP_USERNAME;
	public static $smtp_password = SMTP_PASSWORD;
	public static $smtp_charset = EMAIL_CHARSET;
	
	public static $email_style = EMAIL_STYLE;
	
	private static $smtp_error = '';
	private static $smtp_socket = false;
	private static $smtp_response = '';
	
	public static $args = array();
	
	public static function clear() {
		self::$mail_email = MAIL_EMAIL;
		self::$mail_name = MAIL_NAME;
		
		self::$smtp_host = SMTP_HOST;
		self::$smtp_port = SMTP_PORT;
		self::$smtp_username = SMTP_USERNAME;
		self::$smtp_password = SMTP_PASSWORD;
		self::$smtp_charset = EMAIL_CHARSET;
		
		self::$smtp_error = '';
		
		if (self::$smtp_socket && @fputs(self::$smtp_socket, "QUIT\r\n")) {
			@fclose(self::$smtp_socket);	
		}
		self::$smtp_socket = false;
	}
	
	public static function error() {
		d(self::$smtp_error);
		d(self::$smtp_response);
		return self::$smtp_error."<br>------------------------------<br>".self::$smtp_response;	
	}
	
	public static function mail($email, $subject, $body, $headers = false, $_subject = false, $_body = false, $To = false) {
		//d(array($email, $subject, $body, $headers, ((defined('EMAIL_PARAMS') && EMAIL_PARAMS) ? EMAIL_PARAMS : NULL)));
		
	//	if (is_array($email) || is_array($subject) || is_array($body)) return false;
	//	self::$args = func_get_args();
		
	//	if (defined('IS_DEV') && IS_DEV && IS_ADMIN) d(self::$args);
		
		if (!self::$use_smtp) {
			//$return_email = '-f www-data@www.'.DOMAIN;
			//$return_email = NULL;
			
			
			return mail($To ? $To : $email, $subject, $body, $headers, ((defined('EMAIL_PARAMS') && EMAIL_PARAMS) ? EMAIL_PARAMS : NULL));
		}

		if (!self::$smtp_socket) {
			if (!self::$smtp_socket = fsockopen(self::$smtp_host, self::$smtp_port, $errno, $errstr, 30)) {
				self::smtp_error($errno.'<br>'.$errstr);
				return false;
			}
			if (!self::server_parse('220', __LINE__)) return false;
			
			@fputs(self::$smtp_socket, 'HELO '.self::$smtp_host."\r\n");
			if (!self::server_parse('250', __LINE__)) {
				self::smtp_error('Couldn\'t send HELO');
				@fclose(self::$smtp_socket);
				return false;
			}
			
			@fputs(self::$smtp_socket, "AUTH LOGIN\r\n");
			if (!self::server_parse('334', __LINE__)) {
				self::smtp_error('Couldn\'t find response on authorization request');
				@fclose(self::$smtp_socket);
				return false;
			}
			
			@fputs(self::$smtp_socket, base64_encode(self::$smtp_username)."\r\n");
				if (!self::server_parse('334', __LINE__)) {
				self::smtp_error('Authorization login was not accepted by the server');
				@fclose(self::$smtp_socket);
				return false;
			}
			
			@fputs(self::$smtp_socket, base64_encode(self::$smtp_password)."\r\n");
			if (!self::server_parse('235', __LINE__)) {
				self::smtp_error('Password was not accepted as correct! Authorization error');
				@fclose(self::$smtp_socket);
				return false;
			}
			
			@fputs(self::$smtp_socket, "MAIL FROM: <".self::$smtp_username.">\r\n");
			if (!self::server_parse('250', __LINE__)) {
				self::smtp_error('Couldn\'t send command MAIL FROM:');
				@fclose(self::$smtp_socket);
				return false;
			}
			
			@fputs(self::$smtp_socket, "RCPT TO: <$email>\r\n");			
			if (!self::server_parse('250', __LINE__)) {
				self::smtp_error('Couldn\'t send command RCPT TO:');
				self::bad_email($email);
				@fclose(self::$smtp_socket);
				return false;
			}
			
			@fputs(self::$smtp_socket, "DATA\r\n");
			if (!self::server_parse('354', __LINE__)) {
				self::smtp_error('Couldn\'t send command DATA');
				@fclose(self::$smtp_socket);
				return false;
			}
			register_shutdown_function('Email::clear');
		}

		self::$mail_email = self::$smtp_username;
		
		$name = $email;
		if (is_array($email)) {
			$name = $email['name'];
			$email = $email[self::$email_col];
		}
		
		if ($To) {
			$send = "To: \"$name\" <$email>\r\n";
		} else {
			$send = "To: $To\r\n";	
		}
 		$send .= 'Subject: '.self::mime_header_encode($_subject)."\r\n";
		$send .= "Date: ".date('D, d M Y H:i:s')." UT\r\n";
		
        if ($headers) {
			$send .= $headers;
		}
        else {
			$send .= "From: \"".self::$mail_name."\" <".self::$smtp_username.">\r\n";
			$send .= "Reply-To: \"".self::$mail_name."\" <".self::$mail_email.">\r\n";
			$send .= "MIME-Version: 1.0\r\n";
			$send .= "Content-Type: text/html; charset=\"".self::$smtp_charset."\"\r\n";
			$send .= "Content-Transfer-Encoding: 8bit\r\n";
			$send .= "X-Priority: 3\r\n";
        }

        $send .= $body;
		$send .= "\r\n.\r\n";
					
		@fputs(self::$smtp_socket, $send);
			if (!self::server_parse("250", __LINE__)) {
			self::smtp_error('Couldn\'t send body of a letter. Email has not been sent');
			@fclose(self::$smtp_socket);
			return false;
		}
		
		return true;
	}
	
	private static function smtp_error($e) {
		d($e);
		self::$smtp_error .= $e;
		return $e;
	//	if (self::$smtp_error) die(json(array('halt'=>self::$smtp_error)));
	}

	
	private static function server_parse($response, $line = __LINE__) {
		while (substr($server_response, 3, 1)!=' ') {
			if (!($server_response = @fgets(self::$smtp_socket, 256))) {
				self::smtp_error("Problem with sending email $response");
				return false;
			}
		}
		self::$smtp_response .= $server_response.'<br>';
		if (!(substr($server_response, 0, 3)==$response)) {
			self::smtp_error("Problems with sending email! $response");
			return false;
		}
		return true;
	}
	
	
	private static function bad_email($email) {
		$group = 'non-existing';
		DB::run('DELETE FROM '.DB_PREFIX.'emails WHERE email='.e($email).' AND `group`=\''.$group.'\'');
		DB::run('UPDATE '.DB_PREFIX.'emails SET `group`=\''.$group.'\', sent='.time().', unsub=\'1\' WHERE email='.e($email));	
	}
	
	// SEND MAIL
	public static function send($to, $subject, $body, $attachments=false, $fromaddress=MAIL_EMAIL, $fromname=MAIL_NAME, $arr_headers = array()) {
		if (!$to || !$subject || !$body) return false;

		self::$mail_email = $fromaddress;
		self::$mail_name = $fromname;
		
		$from = ((defined('SMTP_USERNAME') && USE_SMTP && SMTP_USERNAME) ? SMTP_USERNAME : $fromaddress);
		
		$eol = "\r\n";
		$eot = "\r\n\t";
		$eot = ' ';
		if (!$arr_headers || !is_array($arr_headers)) $arr_headers = array();
		$mime_boundary = md5(time());
		$htmlalt_mime_boundary = $mime_boundary.'_htmlalt';
		$_fromname = self::mime_header_encode($fromname);
	//	$headers = 'From: '.$_fromname.' <'.$fromaddress.'>'.$eol;
		$return_email = 'www-data@www.'.DOMAIN;
		if (!@$arr_headers['Return-Path']) $headers .= 'Return-Path: <'.$return_email.'>'.$eol;
		if ($arr_headers && is_array($arr_headers)) {
			foreach ($arr_headers as $k => $h) {
				if ($k=='To') continue;
				$headers .= $k.': '.$h.$eol;
			}
		}
		if (!@$arr_headers['From']) $headers = 'From: '.$_fromname.' <'.$fromaddress.'>'.$eol;
		if (!@$arr_headers['Reply-To']) $headers .= 'Reply-To: '.$_fromname.' <'.$fromaddress.'>'.$eol;
		if (!@$arr_headers['List-Unsubscribe']) $headers .= 'List-Unsubscribe: <http://'.DOMAIN.'/?user&unsubscribe&email='.$to.'>'.$eol;

		$headers .= 'Message-ID: <'.time().'-'.$fromaddress.'>'.$eol;
		$headers .= 'X-Mailer: Ajaxel CMS v'.Site::VERSION.' on PHP v'.phpversion().$eol;
		if (Conf()->is('email_headers')) {
			foreach (Conf()->g('email_headers') as $h) {
				$headers .= $h.$eol;	
			}
		}
		$headers .= 'MIME-Version: 1.0'.$eol;
		$headers .= 'Content-Type: multipart/mixed;'.$eot.'boundary="'.$mime_boundary.'"'.$eol.$eol;
		
		self::add_mail_att();
		if (EMAIL_CLEAN) $body = self::cleanUpMess($body);
		if (EMAIL_URLS) $body = self::urls($body,$mime_boundary);
		
		$msg = '--'.$mime_boundary.$eol;
		
		$body = self::classes($body,$mime_boundary);
		
		if (self::$with_plain) {
			$msg .= 'Content-Type: multipart/alternative;'.$eot.'boundary="'.$htmlalt_mime_boundary.'"'.$eol.$eol;
			$msg .= '--'.$htmlalt_mime_boundary.$eol;
			$msg .= 'Content-Type: text/plain; charset="'.self::$smtp_charset.'"'.$eol;
			$msg .= 'Content-Transfer-Encoding: '.(self::$base64?'base64':'8bit').$eol.$eol;
			$msg .= (self::$base64 ? chunk_split(base64_encode(self::plain($body))) : self::plain($body)).$eol.$eol;
			$msg .= '--'.$htmlalt_mime_boundary.$eol;
		}
		$msg .= 'Content-Type: text/html; charset='.self::$smtp_charset.$eol;
		$msg .= 'Content-Transfer-Encoding: '.(self::$base64?'base64':'8bit').$eol.$eol;
		$msg .= self::$email_style.(self::$base64 ? chunk_split(base64_encode(self::body($body))) : self::body($body)).$eol.$eol;
		
		if (self::$with_plain) $msg .= '--'.$htmlalt_mime_boundary.'--'.$eol.$eol;
	
		if ($attachments && is_array($attachments)) {
			foreach ($attachments as $a) {
				if (isset($a['source'])) {
					$file = $a['file'];
					$ex = explode('/',$file);
					$file_name = end($ex);
					$f_contents = chunk_split(base64_encode($a['source']));
				} else {
					if (!($file = File::exists($a['file']))) continue;
					$file_name = substr($file, (strrpos($file, '/')+1));
					$f_contents = chunk_split(base64_encode(file_get_contents($file)));
				}
				$msg .= '--'.$mime_boundary.$eol;
				$msg .= 'Content-Type: '.(isset($a['content_type'])?$a['content_type']:@filetype($file)).'; name="'.$file_name.'"'.$eol;
				$msg .= 'Content-Transfer-Encoding: base64'.$eol;
				$msg .= 'Content-Description: '.$file_name.$eol;
				$msg .= 'Content-Disposition: attachment; filename="'.$file_name.'"'.$eol;
				$msg .= 'Content-ID: <'.$file_name.'>'.$eol.$eol;
				$msg .= $f_contents;
				$msg .= $eol.$eol;
			}
		}
		$msg .= self::$add_mail_attchments;
		self::add_mail_att();
		$msg .= '--'.$mime_boundary.'--'.$eol.$eol;
		$_subject = $subject;
		$subject = self::mime_header_encode($subject);
		ini_set('sendmail_from',$from);
		$sent = 0;
		$_emails = array();
		if (is_array($to)) {
			$to = array_unique($to);
			foreach ($to as $t) {
				$t = trim($t);
				if (!$t) continue;
				if ($t===true) $t = '';
				if (self::mail($t, $subject, $msg, $headers, $_subject, $body)) {
					$_emails[] = $t;
					$sent++;
				}
				usleep(4000);
			}
		} else {
			$to = trim($to);
			if (self::mail($to, $subject, $msg, $headers, $_subject, $body,@$arr_headers['To'])) {
				$sent = 1;
				$_emails[] = $to;
			}
		}
		ini_restore('sendmail_from');
		if ($sent && EMAIL_SAVE) {
			$params = array (
				'subject'		=> $_subject
				,'body'			=> $body
				,'attachments'	=> $attachments
				,'fromname'		=> $fromname
				,'fromemail'	=> $fromaddress
				,'type'			=> 'html'
			);
			self::saveLog($_emails, $params);
		}
		return $sent;
	}
	
	private static function body($h) {
		if (self::$base64) return $h;
		return wordwrap($h,76,"\r\n");
	}
	
	public static function parseVariables($s,$row,$smarty = false) {
		if ($smarty) {
			
		} else {
			foreach ($row as $k => $v) {
				$s = str_replace('{$'.$k.'}',$v,$s);	
			}
		}
		return $s;
	}
	
	public static function mailTemplate($name, $smarty = false) {
		$fs = FTP_DIR_FILES.'email/'.$name.'-[s.'.LANG.'].html';
		$fm = FTP_DIR_FILES.'email/'.$name.'-[m.'.LANG.'].html';
		if (is_file($fs) && is_file($fm)) {
			if ($smarty && Index()->Smarty) {
				$d = Index()->Smarty->template_dir;
				Index()->Smarty->template_dir = FTP_DIR_FILES.'email/';	
				$ret = array(
					Index()->Smarty->fetch($name.'-[s.'.LANG.'].html'),
					Index()->Smarty->fetch($name.'-[m.'.LANG.'].html')
				);
				Index()->Smarty->template_dir = $d;
				return $ret;	
			} else {
				return array(
					file_get_contents($fs),
					file_get_contents($fm)
				);
			}
		}
		return false;
	}
	public static function site_mail($to, $name, $vars = array()) {
		if (!$to) {
			return false;
			Message::halt('Cannot send an e-mail: '.$name.'','Email is missing');	
		}
		$dir = FTP_DIR_TPLS.'email/'.$name.'/';
		$dir2 = FTP_DIR_TPL.'email/'.$name.'/';
		$path = 'email/';
		$lang = LANG;
		
		if (!is_dir($dir) && !is_dir($dir2)) {
			//Message::halt('Cannot send an email',$to.' did not receive "'.$name.'" email letter since '.$dir.' or '.$dir2.' folders are missing', array(__CLASS__, __METHOD__, __LINE__));
			return false;
		}
		
		if (is_file($dir2.$lang.'_body.html')) {
			$path = ''.TEMPLATE.'/email/';
		}
		elseif (!is_file($dir.$lang.'_body.html')) {
			//Message::halt('Cannot send an e-mail: '.$name.'','<em>'.$dir.$lang.'_body.html</em> file does not exists!');
			return false;	
		}

		
		if (!Index()->Smarty) {
			Index()->loadSmarty();
		}
		
		Index()->Smarty->assign('vars', $vars);
		Index()->Smarty->assign('data', Data::mailData($name, $vars));
		Index()->Smarty->template_dir = FTP_DIR_TPLS;
		$body = $subject = false;
		
		
		if (is_file($dir2.'/'.lang.'_body.html')) { // inner
			$body = Index()->Smarty->fetch($path.$name.'/'.$lang.'_body.html');
		}
		elseif (is_file($dir.'/'.$lang.'_body.html')) { // outer
			$body = Index()->Smarty->fetch($path.$name.'/'.$lang.'_body.html');
		}
		elseif (is_file($dir2.'/en_body.html')) { // inner
			$body = Index()->Smarty->fetch($path.$name.'/en_body.html');
		}
		elseif (is_file($dir.'/en_body.html')) { // outer
			$body = Index()->Smarty->fetch($path.$name.'/en_body.html');
		}
		if (is_file($dir2.'/'.lang.'_subject.html')) { // inner
			$subject = Index()->Smarty->fetch($path.$name.'/'.$lang.'_subject.html');
		}
		elseif (is_file($dir.'/'.$lang.'_subject.html')) { // outer
			$subject = Index()->Smarty->fetch($path.$name.'/'.$lang.'_subject.html');
		}
		elseif (is_file($dir2.'/en_subject.html')) { // inner
			$subject = Index()->Smarty->fetch($path.$name.'/en_subject.html');
		}
		elseif (is_file($dir.'/en_subject.html')) { // outer
			$subject = Index()->Smarty->fetch($path.$name.'/en_subject.html');
		}
		if (!$body || !$subject) {
			Message::halt('Cannot send an e-mail: '.$name.'','No subject or body!');
			return false;
		}
		$att = array();
		$att_dir = $dir.'attachments/';
		if (is_dir($att_dir) && ($dh = opendir($att_dir))) {
			$i = 0;
			while ($file = readdir($dh)) {
				if ($file=='.' || $file=='..' || is_dir($dir.$file)) continue;
				$att[$i]['file'] = $dir.$file;
				$att[$i]['content-type'] = File::arrMime(ext($file));
				$i++;
			}
			closedir($dh);
		}
		Index()->Smarty->template_dir = FTP_DIR_TPL;
		return self::send($to, $subject, $body, $att);
	}


	private static function urls($body,$mime_boundary,$makefile = true) {
		return @preg_replace('/\s(href="|src="|background=")(.*)"/Uie',"Email::_urls('$1','$2','$mime_boundary',$makefile)",$body);
	}
	private static function _urls($type,$url,$mime_boundary, $makefile) {
		$type = stripslashes($type);
		if ($type=='background="' && !strpos($url,'.')) {
			return ' background="'.$url.'"';
		}
		if (($type=='src="' || $type=='background="') && $makefile) {
			if (substr($url,0,4)=='cid:') return ' '.$type.$url.'"';
			if ($ret = self::_fillAttachment($url,$mime_boundary,$type)) return $ret;
		}
		if (substr($url,0,11)=='javascript:') {
			$url = '#';
		}
		elseif (substr($url,0,7)!='http://' && 
			substr($url,0,8)!='https://' && 
			substr($url,0,6)!='ftp://' && 
			substr($url,0,7)!='call://' && 
			substr($url,0,7)!='mailto:') {
				$url = HTTP_BASE.trim($url,'/');
		}
		$url =  str_replace('&amp;','&',$url);
		$url =  str_replace('&','&amp;',$url);
		return ' '.$type.$url.'"';
	}
	
	private static function classes($body, $mime_boundary) {
		$body = preg_replace('/<(.*)\s(class|id)=([_a-z0-9\-]+)(\s(.*)>|>)/Usim','<\\1 \\2="\\3"\\5>',$body);
		$arrCss = Index()->getCSSfiles('email');
		$file = '';
		if ($arrCss) {
			foreach ($arrCss as $css) {
				if (!$css || !is_file(FTP_DIR_ROOT.$css)) continue;
				$file .= file_get_contents(FTP_DIR_ROOT.$css);
			}
		}
		if ($file) $body =  self::_InsertCSS($file,$body);
		$body = @preg_replace('/<(.*)\sstyle="([^"]+)"(.*)>/Usie',"Email::_styleUrls('$2','$1','$3','$mime_boundary')",$body);
		return $body;
	}
	
	private static function cleanUpMess($body) {
		$replace = array(
			'/\sonclick="([^"]+)"/si'
			,'/\sonfocus="([^"]+)"/si'
			,'/\sonkeyup="([^"]+)"/si'
			,'/\sonmouseover="([^"]+)"/si'
			,'/\sonmouseout="([^"]+)"/si'
			,'/\sonload="([^"]+)"/si'
			,'/<!--(.*)-->/U'
			,'/<script[^>]*>.*?<\/script>/i'
		);
		return @preg_replace($replace,'',$body);
	}
	
	private static $add_mail_att_was = array();
	private static $add_mail_attchments = '';
	private static $add_mail_att_i = 1;
	
	private static function add_mail_att() {
		self::$add_mail_att_was = array();
		self::$add_mail_attchments = '';
		self::$add_mail_att_i = 1;
	}
	
	private static function _fillAttachment($url, $mime_boundary, $type = false) {
		if (!$url) return false;
		$url = ltrim($url,'/');
		$ext = ext($url);
		if (array_key_exists($url,self::$add_mail_att_was)) {
			$cid = self::$add_mail_att_was[$url];
			return $type? ' '.$type.'cid:'.$cid.'"' : 'cid:'.$cid;
		}
		elseif (in_array($ext,array('jpg','jpeg','gif','png','bmp','jpe','jfif','tiff','tif'))) {
			if (substr($url,0,9)=='./../') $url = './'.substr($url,9);
			$f_contents = @file_get_contents($url);
			if (!$f_contents) return;
			$eol = "\r\n";
			$f_contents = chunk_split(base64_encode($f_contents));
			$file_name = fileOnly($url);
			$cid = nameOnly($file_name).'_'.self::$add_mail_att_i++;
			$msg = '--'.$mime_boundary.$eol;
			$msg .= 'Content-Type: '.File::arrMime($ext).'; name="'.$file_name.'"'.$eol;
			$msg .= 'Content-Transfer-Encoding: base64'.$eol;
			$msg .= 'Content-ID: <'.$cid.'>'.$eol.$eol;
			$msg .= $f_contents;
			$msg .= $eol.$eol;
			self::$add_mail_att_was[$url] = $cid;
			self::$add_mail_attchments .= $msg;
			return $type? ' '.$type.'cid:'.$cid.'"' : 'cid:'.$cid;
		}
		return false;
	}
	
	private static function _styleUrls($style,$start,$end,$mime_boundary) {
		preg_match_all('/url\(([^\)]+)\)/Ui',$style,$match);
		foreach ($match[1] as $url) {
			$url = trim($url,'\'"');
			$n_url = self::_fillAttachment($url, $mime_boundary, false);
			if (!$n_url && substr($url,0,4)!='cid:' && substr($url,0,7)!='http://' && !strpos($url,'://')) {
				$n_url = HTTP_BASE.trim($url,'/');
			}
			if ($n_url!=$url) {
				$style = str_replace($url,$n_url,$style);
			}
		}
		$ret = '<'.stripslashes($start).' style="'.$style.'"'.stripslashes($end).'>';
		return $ret;
	}
	
	private static function _insertCSS($css,$html) {
		$pattern = '/([\.#]*)([\w-]+)\s?\{([^}]*)/';
		preg_match_all($pattern,$css,$matches);
		$css_selectortypes	= $matches[1];
		$css_selectors    	= $matches[2];
		$css_properties   	= $matches[3];
		foreach ($css_selectors as $idx => $selector) {
			switch ($css_selectortypes[$idx]) {
				case '#':
					$find[]   	= ' id="'.$selector.'"';
					$replace[]	= ' style="'.trim(preg_replace('/\s+/',' ',$css_properties[$idx])).'"';
				break;
				case '.':
					$find[]   	= ' class="'.$selector.'"';
					$replace[]	= ' style="'.trim(preg_replace('/\s+/',' ',$css_properties[$idx])).'"';
				break;
				/*
				case '':      	
					$find[]   	= '<'.$selector;
					$replace[]	= '<'.$selector.' style="'.trim(preg_replace('/\s+/',' ',$css_properties[$idx])).'"';
				break;
				*/
			}
		}
		$html = str_replace($find,$replace,$html);
		return $html;
	}
	
	private static function linkList($url, $name = '', &$arrLinks) {
		if (strlen(trim($name))<3) return NULL;
		if ($url==$name) return $name;
		$n = count($arrLinks);
		$arrLinks[] = str_replace('&amp;','&',$url);
		return trim($name).'['.($n+1).']';
	}
	
	private static function plain($text) {
		$search = array(
			"/\r/",
			'/<script[^>]*>.*?<\/script>/i',
			'/<!-- .* -->/',
			//'/<a href="([^"]+)"[^>]*>(.+?)<\/a>/ie',
			//'/<h[123][^>]*>(.+?)<\/h[123]>/ie',
			//'/<h[456][^>]*>(.+?)<\/h[456]>/ie',
			'/<p[^>]*>/i',
			'/<br[^>]*>/i',
			//'/<b[^>]*>(.+?)<\/b>/ie',
			'/<i[^>]*>(.+?)<\/i>/i',
			'/(<ul[^>]*>|<\/ul>)/i',
			'/(<ol[^>]*>|<\/ol>)/i',
			'/<li[^>]*>/i',
			'/<hr[^>]*>/i',
			'/(<table[^>]*>|<\/table>)/i',
			'/(<tr[^>]*>|<\/tr>)/i',
			'/<td[^>]*>(.+?)<\/td>/i',
			//'/<th[^>]*>(.+?)<\/th>/i',
			'/&nbsp;/i',
			'/&quot;/i',
			'/&gt;/i',
			'/&lt;/i',
			'/&amp;/i',
			'/&copy;/i',
			'/&trade;/i',
			'/&#8220;/',
			'/&#8221;/',
			'/&#8211;/',
			'/&#8217;/',
			'/&#38;/',
			'/&#169;/',
			'/&#8482;/',
			'/&#151;/',
			'/&#147;/',
			'/&#148;/',
			'/&#149;/',
			'/&reg;/i',
			'/&bull;/i',
			'/&[&;]+;/i'
		);
		$replace = array(
			'',
			'',
			'',
			//"Email::LinkList('$1', '$2', \$arrLinks)",
			//"strtoupper(\"\n\n\\1\n\n\")",
			//"ucwords(\"\n\n\\1\n\n\")",
			" \n\n\t",
			" \n",
			//'strtoupper("\\1")',
			'_\\1_',
			"\n\n",
			"\n\n",
			"\t*",
			"\n---------------------------------------------------\n",
			"\n\n",
			"\n",
			"\t\t\\1\n",
			//"strtoupper(\"\t\t\\1\n\")",
			' ',
			'"',
			'>',
			'<',
			'&',
			'(c)',
			'(tm)',
			'"',
			'"',
			'-',
			"'",
			'&',
			'(c)',
			'(tm)',
			'--',
			'"',
			'"',
			'*',
			'(R)',
			'*',
			''
		);
		
		$text = trim(stripslashes($text));
		$text = preg_replace($search, $replace, $text);
		
		$text = preg_replace("/\n\s+\n/", "\n", $text);
		$text = preg_replace("/[\n]{3,}/", "\n\n", $text);
		$text = strip_tags($text);
		/*
		if (sizeof($arrLinks)) {
			$text .= "\n\nLinks:\n------\n";
			foreach ($arrLinks as $i => $link) {
				$link =  str_replace('&amp;','&',$link);
				$text .= '['.($i+1).'] '.$link."\n";
			}
		}
		*/
		return $text;
	}
	
	
	public static function mime_header_encode($string) {
		if (strtoupper(self::$smtp_charset)!='UTF-8') return $string;
		if (!preg_match('/[^\x20-\x7E]/', $string)) return $string;
		$chunk_size = 47; // floor((75 - strlen("=?UTF-8?B??=")) * 0.75);
		$len = strlen($string);
		$output = '';
		while ($len > 0) {
			$chunk = trunc($string, $chunk_size);
			$output .= ' =?UTF-8?B?'. base64_encode($chunk) ."?=\n";
			$c = strlen($chunk);
			$string = substr($string, $c);
			$len -= $c;
		}
		return trim($output);
	}
	
	private static function convert_to_utf8($data, $encoding) {
		if (function_exists('iconv')) {
			return @iconv($encoding, 'utf-8', $data);
		}
		else if (function_exists('mb_convert_encoding')) {
			return @mb_convert_encoding($data, 'utf-8', $encoding);
		}
		else if (function_exists('recode_string')) {
			return @recode_string($encoding .'..utf-8', $data);
		}
		else {
			return FALSE;
		}
	}
	
	private static function _mime_header_decode($matches) {
		$data = ($matches[2] == 'B') ? base64_decode($matches[3]) : str_replace('_', ' ', quoted_printable_decode($matches[3]));
		if (strtolower($matches[1]) != 'utf-8') {
			$data = self::convert_to_utf8($data, $matches[1]);
		}
		return $data;
	}
	
	public static function mime_header_decode($header) {
		$header = preg_replace_callback('/=\?([^?]+)\?(Q|B)\?([^?]+|\?(?!=))\?=\s+(?==\?)/', 'Email::_mime_header_decode', $header);
		return preg_replace_callback('/=\?([^?]+)\?(Q|B)\?([^?]+|\?(?!=))\?=/', 'Email::_mime_header_decode', $header);
	}

	
	public static function sendPlain($to, $subject, $body,  $fromaddress=MAIL_EMAIL, $fromname=MAIL_NAME) {
		if (!$to) return false;
		$eol = "\r\n";
		self::$mail_email = $fromaddress;
		self::$mail_name = $fromname;
		
		$from = ((defined('SMTP_USERNAME') && USE_SMTP && SMTP_USERNAME) ? SMTP_USERNAME : $fromaddress);
		
		$mime_boundary = md5(time());
		$htmlalt_mime_boundary = $mime_boundary.'_htmlalt';
		$_fromname = self::mime_header_encode($fromname);
		$headers = 'From: '.$_fromname.' <'.$from.'>'.$eol;
		$headers .= 'Reply-To: '.$_fromname.' <'.$fromaddress.'>'.$eol;
		$headers .= 'List-Unsubscribe: <http://'.DOMAIN.'/?user&unsubscribe&email='.$to.'>'.$eol;
		$return_email = 'www-data@www.'.DOMAIN;
		$headers .= 'Return-Path: <'.$return_email.'>'.$eol;
		$headers .= 'Message-ID: <'.time().'-'.$fromaddress.'>'.$eol;
		$headers .= 'X-Mailer: Ajaxel CMS v'.Site::VERSION.' on PHP v'.phpversion().$eol;
		if (Conf()->is('email_headers')) {
			foreach (Conf()->g('email_headers') as $h) {
				$headers .= $h.$eol;	
			}
		}
		$headers .= 'MIME-Version: 1.0'.$eol;
		$headers .= 'Content-Type: multipart/related; boundary="'.$mime_boundary.'"'.$eol.$eol;
		$msg = '--'.$mime_boundary.$eol;
		$msg .= 'Content-Type: text/plain; charset='.self::$smtp_charset.$eol;
		$msg .= 'Content-Transfer-Encoding: '.(self::$base64 ? 'base64' : '8bit').$eol.$eol;
		if (EMAIL_URLS) {
			$body = self::urls($body, $mime_boundary, false);
		}
		$body = self::plain($body).$eol.$eol;
		$msg .= (self::$base64 ? chunk_split(base64_encode(self::body($body))) : self::body($body));
		$_subject = $subject;
		$subject = self::mime_header_encode($subject);
		$old_from = ini_get('sendmail_from');
		ini_set('sendmail_from',$from);
		$sent = 0;
		$to = trim($to);
		$_emails[] = array();
		if (strpos($to,',') || strpos($to,' ')) {
			$to = trim(str_replace("\r",'',$to));
			$arr = preg_split("/(,\s?|\s|\n)/",$to);
			$arr = array_unique($arr);
			foreach ($arr as $to) {
				$to = trim($to);
				if (!$to || (!$checked && !Parser::isEmail($to))) continue;
				if (self::mail($to, $subject, $msg, $headers, $_subject, $body)) {
					$_emails[] = $to;
					$sent++;
				}
			}
		} 
		elseif ($checked || Parser::isEmail($to)) {
			if ($sent = self::mail($to, $subject, $msg, $headers, $_subject, $body)) {
				$_emails[] = $to;
			}
		}
		ini_set('sendmail_from', $old_from);
		
		if ($sent && EMAIL_SAVE) {
			$params = array (
				'subject'		=> $_subject
				,'body'			=> $body
				,'fromname'		=> $fromname
				,'fromemail'	=> $fromaddress
				,'type'			=> 'plain'
			);
			self::saveLog($_emails, $params);
		}
		
		return $sent;
	}
	
	
	
	public static function sendMass($data, $subject, $body, $attachments=false, $fromaddress=MAIL_EMAIL, $fromname=MAIL_MAME, $S=false,$update=NULL,$plain=false) {
		if ($S) {
			Index()->loadSmarty();
			$smarty =& Index()->Smarty;
		}
		self::$mail_email = $fromaddress;
		self::$mail_name = $fromname;

		$from = ((defined('SMTP_USERNAME') && USE_SMTP && SMTP_USERNAME) ? SMTP_USERNAME : $fromaddress);
		
		$is_array = is_array($subject);
		$msg2 = $msg3 = $msg4 = '';
		$eol = "\r\n";
		$mime_boundary = md5(time());
		$htmlalt_mime_boundary = $mime_boundary.'_htmlalt';
		$old_from = ini_get('sendmail_from');
		ini_set('sendmail_from',$from);
		$_emails = array();
		$_up = 0;
		$_fromname = self::mime_header_encode($fromname);
		
		if ($plain && !$attachments) {
			$headers = 'From: "'.$_fromname.'" <'.$from.'>'.$eol;
			$headers .= 'Reply-To: "'.$_fromname.'" <'.$fromaddress.'>'.$eol;
			$headers .= 'List-Unsubscribe: <http://'.DOMAIN.'/?user&unsubscribe&email=[[:EMAIL:]]>'.$eol;
			$return_email = 'www-data@www.'.DOMAIN;
			$headers .= 'Return-Path: <'.$return_email.'>'.$eol;
			$headers .= 'Message-ID: <'.time().'-'.$fromaddress.'>'.$eol;
			$headers .= 'X-Mailer: Ajaxel CMS v'.Site::VERSION.' on PHP v'.phpversion().$eol;
			if (Conf()->is('email_headers')) {
				foreach (Conf()->g('email_headers') as $h) {
					$headers .= $h.$eol;	
				}
			}
			$headers .= 'MIME-Version: 1.0'.$eol;
			$headers .= 'Content-Type: multipart/related; boundary="'.$mime_boundary.'"'.$eol.$eol;
			
			$message = '--'.$mime_boundary.$eol;
			$message .= 'Content-Type: text/plain; charset='.self::$smtp_charset.$eol;
			$message .= 'Content-Transfer-Encoding: '.(self::$base64?'base64':'8bit').$eol.$eol;
		//	if (!$S) $message .= self::plain($body).$eol.$eol;
			$msg2 .= $eol.$eol;
			$i = 0;
			foreach ($data as $rs) {
				if (!$rs['lang']) $rs['lang'] = DEFAULT_LANGUAGE;
				if ($S) {
					if (!$S['subject'] || !$S['body']) continue;
				} else {
					if (!$body[$rs['lang']] || !$subject[$rs['lang']]) continue;		
				}
				$headers = str_replace('[[:EMAIL:]]', $rs[self::$email_col], $headers);
				if ($S) {
					$smarty->assign('name',$rs['name']);
					$smarty->assign('group',$rs['group']);
					$smarty->assign('email',$rs[self::$email_col]);
					$subject = $smarty->fetch($S['subject']);
					$html = $smarty->fetch($S['message']);
					$message = $message.(self::$base64 ? chunk_split(base64_encode($html)) : $html).$eol.$eol;
					$mail = self::mail($rs[self::$email_col], $subject, $message, $headers, $_subject, $html);
				}
				else {
					if ($is_array) {
						if (!isset($rs['lang'])) $rs['lang'] = DEFAULT_LANGUAGE;
						$message = $msg1.self::parse($body[$rs['lang']], $rs, true).$msg2;
						$mail = self::mail($rs[self::$email_col], self::parse($subject[$rs['lang']], $rs), $message, $headers, $subject[$rs['lang']], $message);
					} else {
						$message = $msg1.self::parse($body, $rs, true).$msg2;
						$mail = self::mail($rs[self::$email_col], self::parse($subject, $rs), $message, $headers, $subject, $message);
					}
				}
				if (Conf()->g('MYSQL_wait_timeout') && Conf()->g('MYSQL_second_start') && Conf()->g('MYSQL_wait_timeout') <= (time() - Conf()->g('MYSQL_second_start'))) {
					DB::close();
					sleep(1);
					Conf()->s('MYSQL_second_start', time());
					DB::reconnect();
				}
				if ($mail) {
					$_emails[] = $rs[self::$email_col];
					if ($update) {
						$ex = DB::run($s = str_replace(array('{$email}','{$type}'),array($rs[self::$email_col],$rs['group']),$update));
						if (!$ex || !DB::affected()) die(Message::sql($s));
						else $_up++;					
					}
					$i++;
				}
				usleep(4000);
			}
		} else {
			$headers = 'From: '.$_fromname.' <'.$fromaddress.'>'.$eol;
			$headers .= 'Reply-To: '.$_fromname.' <'.$fromaddress.'>'.$eol;
			$headers .= 'List-Unsubscribe: <http://'.DOMAIN.'/?user&unsubscribe&email=[[:EMAIL:]]>'.$eol;
			$return_email = 'www-data@www.'.DOMAIN;
			$headers .= 'Return-Path: <'.$return_email.'>'.$eol;
			$headers .= 'Message-ID: <'.time().'-'.$fromaddress.'>'.$eol;
			$headers .= 'X-Mailer: Ajaxel CMS v'.Site::VERSION.' on PHP v'.phpversion().$eol;
			if (Conf()->is('email_headers')) {
				foreach (Conf()->g('email_headers') as $h) {
					$headers .= $h.$eol;	
				}
			}
			$headers .= 'MIME-Version: 1.0'.$eol;
			$headers .= 'Content-Type: multipart/mixed; boundary="'.$mime_boundary.'"'.$eol.$eol;
			
			if (self::$with_plain) {
				$msg1 = '--'.$mime_boundary.$eol;
				$msg1 .= 'Content-Type: multipart/alternative; boundary="'.$htmlalt_mime_boundary.'"'.$eol.$eol;
				$msg1 .= '--'.$htmlalt_mime_boundary.$eol;
				$msg1 .= 'Content-Type: text/plain; charset='.self::$smtp_charset.$eol;
				$msg1 .= 'Content-Transfer-Encoding: '.(self::$base64?'base64':'8bit').$eol.$eol;
				$msg2 .= $eol.$eol;
				$msg2 .= '--'.$htmlalt_mime_boundary.$eol;
			} else {
				$msg2 .= $eol.$eol.'--'.$mime_boundary.$eol;
			}
			
			$msg2 .= 'Content-Type: text/html; charset='.self::$smtp_charset.$eol;
			$msg2 .= 'Content-Transfer-Encoding: '.(self::$base64?'base64':'8bit').$eol.$eol;
			if (self::$with_plain) $msg3 .= $eol.$eol.'--'.$htmlalt_mime_boundary.'--'.$eol.$eol;
			else $msg3 .= $eol.$eol;
			$att = '';
			self::add_mail_att();
			if (!$S) {
				if ($is_array) {
					$_body = array();
					foreach ($body as $l => $b) {
						if ($plain) {
							$_body[$l] = nl2br($b);
						} else {
							$_body[$l] = self::cleanUpMess($b);
							$_body[$l] = self::urls($_body[$l],$mime_boundary);
							$_body[$l] = self::classes($_body[$l],$mime_boundary);
							$_body[$l] = self::body($_body[$l]);
						}
					}
					$body = $_body;
					unset($_body);
				} else {
					if ($plain) {
						$body = nl2br($body);
					} else {
						$body = self::cleanUpMess($body);
						$body = self::urls($body,$mime_boundary);
						$body = self::classes($body,$mime_boundary);
						$body = self::body($body);	
					}
				}
			}
			if ($attachments && is_array($attachments)) {
				foreach ($attachments as $a) {
					if (!($file = File::Exists($a['file']))) continue;
					$file_name = substr($file, (strrpos($file, '/')+1));
					$att .= '--'.$mime_boundary.$eol;
					$att .= 'Content-Type: '.(isset($a['content_type']) ? $a['content_type'] : filetype($file)).'; name="'.$file_name.'"'.$eol;
					$att .= 'Content-Transfer-Encoding: base64'.$eol;
					$att .= 'Content-Description: '.$file_name.$eol;
					$att .= 'Content-Disposition: attachment; filename="'.$file_name.'"'.$eol.$eol;
					$handle = fopen($file, 'rb');
					$att .= chunk_split(base64_encode(fread($handle, filesize($file))));
					@fclose($handle);
					$att .= $eol.$eol;
				}
			}
			$att .= self::$add_mail_attchments;
			self::add_mail_att();
			$msg4 .= '--'.$mime_boundary.'--'.$eol.$eol;
			$__subject = $subject;
			if ($is_array) {
				$_subject = array();
				foreach ($subject as $l => $s) {
					$_subject[$l] = self::mime_header_encode($s);
				}
				$subject = $_subject;
				unset($_subject);
			} else {
				$subject = self::mime_header_encode($subject);
			}
			if (!$is_array) {
				$plain_body = self::plain($body);
			}
			$i = 0;
			
			foreach ($data as $rs) {
				if (!$rs['lang']) $rs['lang'] = DEFAULT_LANGUAGE;
				if ($S) {
					if (!$S['subject'] || !$S['body']) continue;
				} else {
					if (!$body[$rs['lang']] || !$subject[$rs['lang']]) continue;		
				}
				if ($rs['data']) {
					if (!is_array($rs['data'])) {
						$_data = @unserialize($rs['data']);
					} else {
						$_data = $rs['data'];	
					}
					if ($_data && is_array($_data)) $rs = array_merge($_data, $rs);
				}
				
				$att2 = '';
				if ($rs['attachments'] && is_array($rs['attachments'])) {
					foreach ($rs['attachments'] as $a) {
						if (isset($a['source'])) {
							$file_name = $a['file'];
						}
						elseif (($file = File::exists($a['file']))) {
							$file_name = substr($file, (strrpos($file, '/')+1));
						}
						else continue;
						$att2 .= '--'.$mime_boundary.$eol;
						$att2 .= 'Content-Type: '.(isset($a['content_type']) ? $a['content_type'] : filetype($file)).'; name="'.$file_name.'"'.$eol;
						$att2 .= 'Content-Transfer-Encoding: base64'.$eol;
						$att2 .= 'Content-Description: '.$file_name.$eol;
						$att2 .= 'Content-Disposition: attachment; filename="'.$file_name.'"'.$eol.$eol;
						if (isset($a['source'])) {
							$att2 .= $a['source'];
						} else {
							$handle = fopen($file, 'rb');
							$att2 .= chunk_split(base64_encode(fread($handle, filesize($file))));
							@fclose($handle);
						}
						$att2 .= $eol.$eol;
					}
				}
				
				$headers = str_replace('[[:EMAIL:]]', $rs[self::$email_col], $headers);
				
				if ($S) {
					$smarty->assign('name',$rs['name']);
					$smarty->assign('group',$rs['group']);
					$smarty->assign('email',$rs[self::$email_col]);
					$smarty->assign('row',$rs);
					$subject = self::parse($smarty->fetch($S['subject']),$rs);
					$html = self::body(self::parse($smarty->fetch($S['message']),$rs,true));
					$message = /*$msg1.self::plain($html).*/$msg2.$html.$msg3.$att2.$att.$msg4;
					$mail = self::mail($rs[self::$email_col], $subject, $message, $headers, $subject, $html);
				} else {
					if ($is_array) {
						if (!isset($rs['lang'])) $rs['lang'] = DEFAULT_LANGUAGE;
						$message = (self::$with_plain ? $msg1.self::parse(self::plain($body[$rs['lang']]), $rs, true) : '').$msg2.self::parse($body[$rs['lang']],$rs,true,true).$msg3.$att2.$att.$msg4;
						$mail = self::mail($rs[self::$email_col], self::parse($subject[$rs['lang']], $rs), $message, $headers, $subject[$rs['lang']], $message);
					} else {
						$message = (self::$with_plain ? $msg1.self::parse($plain_body, $rs, true):'').$msg2.self::parse($body, $rs, true, true).$msg3.$att2.$att.$msg4;
						$mail = self::mail($rs[self::$email_col], self::parse($subject, $rs), $message, $headers, $subject, $message);
					}
				}
				/*
				if (Conf()->g('MYSQL_wait_timeout') && Conf()->g('MYSQL_second_start') && Conf()->g('MYSQL_wait_timeout') <= (time() - Conf()->g('MYSQL_second_start'))) {
					DB::close();
					sleep(1);
					Conf()->s('MYSQL_second_start', time());
					DB::reconnect();
				}
				*/
				if ($mail) {
					if ($update) {
						$s = str_replace(array('{$email}','{$group}'),array($rs[self::$email_col],$rs['group']),$update);
						$ex = DB::run($s);
						if (!$ex || !DB::affected()) die(Message::sql($s));
						else $_up++;
					}
					$i++;
				}
			}
		}
		Conf()->s('MASS_UP', $_up);
		ini_set('sendmail_from', $old_from);
		return $i;
	}
	
	
	public static function add_catcher($domain, &$t, $rs) {
		$t = str_replace('http://'.$domain.'/?','http://'.$domain.'/?emailclick='.urlencode($rs[self::$email_col]).(self::$campaign?'!'.urlencode(self::$campaign):'').'&',$t);
		$t = str_replace('http://'.$domain.'/','http://'.$domain.'/emailclick='.urlencode($rs[self::$email_col]).(self::$campaign?'!'.urlencode(self::$campaign):'').'/',$t);
	}

	private static function parse($t, $rs, $is_message = false, $add_catcher = false) {
		$link = '?user&unsubscribe&email='.urlencode($rs[self::$email_col]).(self::$campaign?'&campaign='.urlencode(self::$campaign):'');
		$link = trim(Url::ht($link),'/');
		foreach ($rs as $k => $v) $t = str_replace('{$'.$k.'}',$v,$t);
		
		if ($add_catcher) {
			self::add_catcher(DOMAIN, $t, $rs);
			if (Conf()->is('email_domains')) {
				foreach (Conf()->g('email_domains') as $domain) {
					self::add_catcher($domain, $t, $rs);
				} 	
			}
			if (EMAIL_PIXEL) $t .= '<img src="http://'.DOMAIN.'/img.php?email='.urlencode($rs[self::$email_col]).(self::$campaign?'&campaign='.urlencode(self::$campaign):'').'" width="1" height="1" alt="" />';
		}
		
		
		$t = str_replace(array('{$unsubscribelink}','{$unsubscribe_url}','#unsubscribelink#'),array($link,$link),$t);
		if ($is_message) $t = wordwrap($t);
		
		if ($is_message && self::$base64) $t = chunk_split(base64_encode($t));
		return $t;
	}

	private static function saveLog($email, $params) {
		return false;
		/*
		if (!constant('EMAIL_SAVE') || (!$params['body'] && !$params['subject'] && !$params['attachments'])) return false;
		if (Conf()->g('NoEmailLog')) return false;
		if (Conf()->g('EmailLOGSave')) {
			if (is_array($email)) {
				foreach ($email as $e) Conf()->fill('EmailLogEmails', $e);
			} else {
				Conf()->fill('EmailLogEmails', $email);
			}
			return true;
		}
		extract($params);
		$allowed_types = array('plain','html','mass_plain','mass_html');
		if (!in_array($type,$allowed_types)) $type = 'html';
		
		if ($attachments) {
			$att = array();
			foreach ($attachments as $a) {
				if ($file = File::Exists($a['file'])) {
					$att[] = $a['file'].' - '.filesize($a['file']);
				}
			}
			$attachments = join(' |;',$att);
		} else {
			$attachments = '';
		}
		if (!is_array($email)) $email = array($email);
		$data = array (
			'authorid'		=> UserID()
			,'`emails`'		=> join(', ',$email)
			,'`emailnum`'	=> count($email)
			,'`subject`'	=> $subject
			,'`body`'		=> $body
			,'`attachments`'=> $attachments
			,'`fromname`'	=> $fromname
			,'`fromemail`'	=> $fromemail
			,'`type`'		=> $type
			,'`sent`'		=> time()
			,'`ip`'			=> Session::getIP()
		);
		DB::insert('email_log',$data);
		DB::run($sql);
		Conf()->s('EmailLOGSave', DB::id());
		Conf()->s('EmailLogEmails', array());
		DB::commit();
		*/
	}
}