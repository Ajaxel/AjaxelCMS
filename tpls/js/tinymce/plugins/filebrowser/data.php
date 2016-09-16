<?php

require("config.php");
require_once "./lib/config.php";
require_once "./lib/JsHttpRequest.php";

if ($_SESSION['lang']) {
	$_SESSION['lang'] = str_replace("../", "", $_SESSION['lang']);
	if (file_exists("langs/" . $_SESSION['lang'] . ".php")) {
		require("langs/" . $_SESSION['lang'] . ".php");
	} else {
		require("langs/default.php");
	}
} else {
	require("langs/default.php");
}
$JsHttpRequest        = new JsHttpRequest("utf-8");
$_RESULT['http_link'] = $http_folder;
if ($_POST['user_action'] == "get_list") {
	$_RESULT['files']    = array();
	$_POST['dir']        = check_dir_addres($_POST['dir']);
	$work_dir            = $work_folder . check_dir_addres($_POST['dir']);
	$_RESULT['catalogs'] = get_all_dirs($work_folder);
	foreach ($_RESULT['catalogs'] as $key => $val) {
		$_RESULT['catalogs'][$key] = "/" . str_replace($work_folder, "", $_RESULT['catalogs'][$key]);
	}
	if (is_dir($work_dir)) {
		$dirs_array = array();
		$dir        = opendir($work_dir);
		$i          = 0;
		while (false !== ($file = readdir($dir))) {
			if ($file != "." && $file != ".." && is_dir($work_dir . $file)) {
				$dirs_array[$file . '-' . $i]['info']  = "folder";
				$dirs_array[$file . '-' . $i]['ext']   = "folder";
				$dirs_array[$file . '-' . $i]['image'] = "folder.gif";
				$dirs_array[$file . '-' . $i]['name']  = $file;
				$dirs_array[$file . '-' . $i]['real']  = $file;
				$dirs_array[$file . '-' . $i]['size']  = "";
				$i++;
			}
		}
		$files_array = array();
		$dir         = opendir($work_dir);
		while (false !== ($file = readdir($dir))) {
			$ext = explode(".", strtolower($file));
			$ext = array_pop($ext);
			if ($file != "." && $file != ".." && !is_dir($work_dir . $file) && in_array($ext, explode(" ", $enabled_files))) {
				if (file_exists("images/icons/" . $ext . ".gif")) {
					$image = $ext . ".gif";
				} else {
					$image = "default.icon.gif";
				}
				$file_name = trim(substr($file, 0, strlen($file) - strlen($ext) - 1));
				if (!$file_name) {
					$file_name = $file;
				}
				if (in_array($ext, array(
					'gif',
					'jpg',
					'png',
					'swf',
					'swc',
					'psd',
					'tiff',
					'bmp',
					'iff',
					'jp2',
					'jpx',
					'jb2',
					'jpc',
					'xbm'
				))) {
					$info = getimagesize($work_dir . $file);
					$info = $info[0] . "x" . $info[1];
				} else {
					if (function_exists("mime_content_type")) {
						$info = mime_content_type($work_dir . $file);
					} else {
						$info = " ";
					}
				}
				$files_array[$file . '-' . $i]['info']  = $info;
				$files_array[$file . '-' . $i]['ext']   = $ext;
				$files_array[$file . '-' . $i]['image'] = $image;
				$files_array[$file . '-' . $i]['name']  = $file_name;
				$files_array[$file . '-' . $i]['real']  = $file;
				$files_array[$file . '-' . $i]['size']  = size_selector(filesize($work_dir . $file));
				$i++;
			}
		}
		ksort($files_array);
		ksort($dirs_array);
		$data_array       = array_merge($dirs_array, $files_array);
		$_RESULT['files'] = array_values($data_array);
	}
} elseif ($_POST['user_action'] == "rename") {
	$_POST['dir'] = check_dir_addres($_POST['dir']);
	$work_dir     = $work_folder . check_dir_addres($_POST['dir']);
	if (file_exists($work_dir . $_POST['old_name']) && trim($_POST['new_name'])) {
		if (!is_dir($work_dir . $_POST['old_name'])) {
			$ext = explode(".", strtolower($_POST['old_name']));
			$ext = "." . array_pop($ext);
		} else {
			$ext = "";
		}
		$new_file_name  = file_name_check($_POST['new_name']);
		$final_new_name = $new_file_name . $ext;
		if (file_exists($work_dir . $final_new_name)) {
			for ($i = 1;; $i++) {
				$test_final_new_name = $new_file_name . "(" . $i . ")" . $ext;
				if (!file_exists($work_dir . $test_final_new_name)) {
					rename($work_dir . $_POST['old_name'], $work_dir . $test_final_new_name);
					break;
				}
			}
		} else {
			rename($work_dir . $_POST['old_name'], $work_dir . $final_new_name);
		}
	}
} elseif ($_POST['user_action'] == "delete") {
	$_POST['dir'] = check_dir_addres($_POST['dir']);
	$work_dir     = $work_folder . check_dir_addres($_POST['dir']);
	if (file_exists($work_dir . $_POST['name'])) {
		if (is_dir($work_dir . $_POST['name'])) {
			@recursiveRemoveDirectory($work_dir . $_POST['name'] . "/");
		} else {
			unlink($work_dir . $_POST['name']);
		}
	}
} elseif ($_POST['user_action'] == "new_dir") {
	$_POST['dir'] = check_dir_addres($_POST['dir']);
	$work_dir     = $work_folder . check_dir_addres($_POST['dir']);
	$new_dir_name = "New_folder";
	if (file_exists($work_dir . $new_dir_name)) {
		for ($i = 1;; $i++) {
			$test_final_new_name = $new_dir_name . "(" . $i . ")";
			if (!file_exists($work_dir . $test_final_new_name)) {
				mkdir($work_dir . $test_final_new_name);
				break;
			}
		}
	} else {
		mkdir($work_dir . $new_dir_name);
	}
} elseif ($_POST['user_action'] == "upload") {
	$_RESULT['error']    = "";
	$_RESULT['filename'] = "";
	$_POST['dir']        = check_dir_addres($_POST['dir']);
	$work_dir            = $work_folder . check_dir_addres($_POST['dir']);
	if (is_uploaded_file($_FILES['file']['tmp_name'])) {
		$ext = explode(".", strtolower($_FILES['file']['name']));
		$ext = "." . array_pop($ext);
		if (preg_match('/\.php\d?\./i', $_FILES['file']['name'])) {
			echo $getword['not_supported_file_type'];
		} elseif (in_array(substr($ext, 1, strlen($ext)), explode(" ", $enabled_files))) {
			$new_file_name = false;
			$new_file      = explode(".", $_FILES['file']['name']);
			unset($new_file[count($new_file) - 1]);
			foreach ($new_file as $val) {
				if ($new_file_name) {
					$new_file_name .= ".";
				}
				$new_file_name .= $val;
			}
			$new_file_name = file_name_check($new_file_name);
			if (file_exists($work_dir . $new_file_name . $ext)) {
				for ($i = 1;; $i++) {
					$test_final_new_name = $new_file_name . "(" . $i . ")" . $ext;
					if (!file_exists($work_dir . $test_final_new_name)) {
						if (move_uploaded_file($_FILES['file']['tmp_name'], $work_dir . $test_final_new_name)) {
							$_RESULT['filename'] = $new_file_name . "(" . $i . ")";
						}
						break;
					}
				}
			} else {
				if (move_uploaded_file($_FILES['file']['tmp_name'], $work_dir . $new_file_name . $ext)) {
					$_RESULT['filename'] = $new_file_name;
				}
			}
		} else {
			echo $getword['not_supported_file_type'];
		}
	} else {
		$_RESULT['error'] = $getword['file_not_uploaded'];
	}
} elseif ($_POST['user_action'] == "to_archive") {
	require('zip/pclzip.lib.php');
	$_POST['dir'] = check_dir_addres($_POST['dir']);
	$work_dir     = $work_folder . check_dir_addres($_POST['dir']);
	if (file_exists($work_dir . $_POST['file'])) {
		$new_file_name = file_name_check($_POST['file']);
		$ext           = ".zip";
		if (file_exists($work_dir . $new_file_name . $ext)) {
			for ($i = 1;; $i++) {
				$test_final_new_name = $new_file_name . "(" . $i . ")" . $ext;
				if (!file_exists($work_dir . $test_final_new_name)) {
					$zp      = gzopen($work_dir . $test_final_new_name, "w9");
					$archive = new PclZip($work_dir . $test_final_new_name);
					break;
				}
			}
		} else {
			$archive = new PclZip($work_dir . $_POST['file'] . $ext);
		}
		copy($work_dir . $_POST['file'], "./" . $new_file_name);
		if ($archive->create("./" . $new_file_name) == 0) {
			die('Error : ' . $archive->errorInfo(true));
		}
		unlink("./" . $new_file_name);
	}
} elseif ($_POST['user_action'] == "from_archive") {
	require('zip/pclzip.lib.php');
	$_POST['dir'] = check_dir_addres($_POST['dir']);
	$work_dir     = $work_folder . check_dir_addres($_POST['dir']);
	if (file_exists($work_dir . $_POST['file'])) {
		$archive       = new PclZip($work_dir . $_POST['file']);
		$new_file_name = false;
		$new_file      = explode(".", $_POST['file']);
		unset($new_file[count($new_file) - 1]);
		foreach ($new_file as $val) {
			if ($new_file_name) {
				$new_file_name .= ".";
			}
			$new_file_name .= $val;
		}
		$new_file_name = file_name_check($new_file_name);
		if (file_exists($work_dir . $new_file_name))
			for ($i = 1;; $i++) {
				$test_final_new_name = $new_file_name . "(" . $i . ")";
				if (!file_exists($work_dir . $test_final_new_name)) {
					if ($archive->extract(PCLZIP_OPT_PATH, $work_dir . $test_final_new_name) == 0) {
						die("Error : " . $archive->errorInfo(true));
					}
					break;
				}
			}
	} else {
		if ($archive->extract(PCLZIP_OPT_PATH, $work_dir . $new_file_name) == 0) {
			die("Error : " . $archive->errorInfo(true));
		}
		$dir = new RecursiveDirectoryIterator($work_dir . $new_file_name);
		foreach (new RecursiveIteratorIterator($dir) as $file) {
			$c         = explode("/", $file);
			$file_name = $c[count($c) - 1];
			unset($c[count($c) - 1]);
			$file_src = false;
			foreach ($c as $val) {
				if ($file_src) {
					$file_src .= "/";
				}
				$file_src .= $val;
			}
			$file_src .= "/";
			$ext = explode(".", strtolower($file_name));
			$ext = array_pop($ext);
			if (in_array($ext, explode(" ", $enabled_files))) {
				rename($file, $file_src . file_name_check($file_name));
			} else {
				unlink($file);
			}
		}
	}
} elseif ($_POST['user_action'] == "send_file") {
	$_RESULT['error'] = "";
	$_POST['dir']     = check_dir_addres($_POST['dir']);
	$work_dir         = $work_folder . check_dir_addres($_POST['dir']);
	if (!eregi("^[a-z0-9]+([-_\.]?[a-z0-9])+@[a-z0-9]+([-_\.]?[a-z0-9])+\.[a-z]{2,4}", $_POST['to'])) {
		$_POST['to']      = false;
		$_RESULT['error'] = $getword['please_set_recipient_email'];
	} elseif (!$_POST['from']) {
		$_POST['from']    = false;
		$_RESULT['error'] = $getword['please_set_your_email'];
	}
	$_POST['from'] = htmlspecialchars($_POST['from']);
	if (file_exists($work_dir . $_POST['file']) && $_POST['to'] && $_POST['from']) {
		$to_email      = $_POST['to'];
		$from          = $_POST['from'];
		$from_email    = $_POST['from'];
		$subject       = "New file attachment: " . $_POST['file'];
		$file          = $work_dir . $_POST['file'];
		$n             = "\n";
		$mime_boundary = md5(time());
		$headers       = 'To: ' . $to_email . $n;
		$headers .= 'From: ' . $from . ' <' . $from_email . '>' . $n;
		$headers .= 'Reply-To: ' . $from . ' <' . $from_email . '>' . $n;
		$headers .= 'Return-Path: ' . $from . ' <' . $from_email . '>' . $n;
		$headers .= "Message-ID: <TheSystem@" . $_SERVER['SERVER_NAME'] . ">" . $n;
		$headers .= "X-Mailer: PHP v" . phpversion() . $n;
		$headers .= 'MIME-Version: 1.0' . $n;
		$headers .= "Content-Type: multipart/related; boundary=\"" . $mime_boundary . "\"" . $n;
		$message = false;
		$message .= "--" . $mime_boundary . $n;
		$message .= "Content-Type: text/html; charset=utf-8" . $n;
		$message .= "Content-Transfer-Encoding: 8bit" . $n;
		$message .= "<font face=arial size=2><b>Date: </b>" . date("d F, Y -  h:i") . "<br><b>File sended from:</b> " . $_SERVER['REMOTE_ADDR'] . "</font>" . $n . $n;
		$fp            = fopen($file, "rb");
		$file_contents = fread($fp, filesize($file));
		$file_contents = chunk_split(base64_encode($file_contents));
		fclose($fp);
		if (function_exists('mime_content_type')) {
			$content_type = mime_content_type($file);
		} else {
			$content_type = "";
		}
		$message .= "--" . $mime_boundary . $n;
		$message .= "Content-Type: " . $content_type . "; name=\"" . basename($file) . "\"" . $n;
		$message .= "Content-Transfer-Encoding: base64" . $n;
		$message .= "Content-Disposition: attachment; filename=\"" . basename($file) . "\"" . $n . $n;
		$message .= $file_contents . $n . $n;
		$message .= "Content-Type: multipart/alternative" . $n;
		if (mail($to_email, $subject, $message, $headers)) {
			$_RESULT['error'] = $getword['file_sended_to_email'];
		} else {
			$_RESULT['error'] = $getword['file_not_sended_to_email'];
		}
	}
} elseif ($_POST['user_action'] == "copy" || $_POST['user_action'] == "move") {
	$_RESULT['error'] = "";
	$_POST['dir']     = check_dir_addres($_POST['dir']);
	$work_dir         = $work_folder . $_POST['dir'];
	if ($_POST['to'] != "/") {
		$_POST['to'] = check_dir_addres($_POST['to']);
		$copy_dir    = $work_folder . $_POST['to'];
	} else {
		$copy_dir = $work_folder;
	}
	if (is_dir($copy_dir) && is_dir($work_dir) && file_exists($work_dir . $_POST['file'])) {
		if (file_exists($copy_dir . $_POST['file'])) {
			$new_file_name = false;
			$new_file      = explode(".", $_POST['file']);
			$ext           = "." . $new_file[count($new_file) - 1];
			unset($new_file[count($new_file) - 1]);
			foreach ($new_file as $val) {
				if ($new_file_name) {
					$new_file_name .= ".";
				}
				$new_file_name .= $val;
			}
			$new_file_name = file_name_check($new_file_name);
			for ($i = 1;; $i++) {
				$test_final_new_name = $new_file_name . "(" . $i . ")" . $ext;
				if (!file_exists($copy_dir . $test_final_new_name)) {
					if (copy($work_dir . $_POST['file'], $copy_dir . $test_final_new_name)) {
						if ($_POST['user_action'] == "move") {
							unlink($work_dir . $_POST['file']);
						}
					}
					break;
				}
			}
		} else {
			if (copy($work_dir . $_POST['file'], $copy_dir . $_POST['file'])) {
				if ($_POST['user_action'] == "move") {
					unlink($work_dir . $_POST['file']);
				}
			}
		}
	} else {
		echo "Not copyng";
	}
} elseif ($_POST['user_action'] == "resize_image") {
	$_POST['dir'] = check_dir_addres($_POST['dir']);
	$work_dir     = $work_folder . $_POST['dir'];
	if (file_exists($work_dir . $_POST['file']) && $_POST['width']) {
		$image_file_link = $work_dir . $_POST['file'];
		$set_size        = $_POST['width'] . "x" . $_POST['height'];
		$real_image      = getimagesize($image_file_link);
		if (count(explode("x", $set_size)) == 2) {
			$set_size = explode("x", $set_size);
			if ($set_size[0] >= $real_image[0]) {
				$new_image['width'] = $real_image[0];
			} else {
				$new_image['width'] = $set_size[0];
			}
		} else {
			$new_image['width'] = $set_size;
		}
		$new_image['height'] = round($new_image['width'] / ($real_image[0] / $real_image[1]));
		if (count($set_size) >= 1) {
			if ($real_image['mime'] == "image/gif") {
				$img = imagecreatefromgif($image_file_link);
			} elseif ($real_image['mime'] == "image/jpeg") {
				$img = imagecreatefromjpeg($image_file_link);
			} elseif ($real_image['mime'] == "image/png") {
				$img = imagecreatefrompng($image_file_link);
			}
			if ($real_image['mime'] == "image/gif") {
				$resized = imagecreate($new_image['width'], $new_image['height']);
				$orange  = imagecolorallocate($resized, 248, 207, 81);
				imagefill($resized, 0, 0, $orange);
				imagecolortransparent($resized, $orange);
			} elseif ($real_image['mime'] == "image/png") {
				$resized = imagecreatetruecolor($new_image['width'], $new_image['height']);
				imagealphablending($resized, false);
				imagesavealpha($resized, true);
			} elseif ($real_image['mime'] == "image/jpeg") {
				$resized = imagecreatetruecolor($new_image['width'], $new_image['height']);
				$white   = imagecolorallocate($resized, 255, 255, 255);
				imagefill($resized, 0, 0, $white);
			}
			imagecopyresampled($resized, $img, 0, 0, 0, 0, $new_image['width'], $new_image['height'], $real_image[0], $real_image[1]);
			if (count($set_size) == 2) {
				$image = imagecreatetruecolor($set_size[0], $set_size[1]);
				$white = imagecolorallocate($image, 255, 255, 255);
				imagefill($image, 0, 0, $white);
				if ($new_image['width'] < $set_size[0]) {
					$padding_left = round(($set_size[0] - $new_image['width']) / 2);
				} else {
					$padding_left = 0;
				}
				$padding_top = round(($set_size[1] - $new_image['height']) / 2);
				imagecopy($image, $resized, $padding_left, $padding_top, 0, 0, $new_image['width'], $new_image['height']);
				$resized = $image;
			}
			imagedestroy($img);
			if (function_exists('imageantialias')) {
				imageantialias($resized, true);
			}
			if (unlink($image_file_link)) {
				if ($real_image['mime'] == "image/gif") {
					@imagegif($resized, $image_file_link);
				} elseif ($real_image['mime'] == "image/jpeg") {
					@imagejpeg($resized, $image_file_link);
				} elseif ($real_image['mime'] == "image/png") {
					@imagepng($resized, $image_file_link);
				}
			}
			imagedestroy($resized);
		}
	}
}