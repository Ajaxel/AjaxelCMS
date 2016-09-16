<!DOCTYPE html><html><head><title><?php echo $this->Index->getVar('title')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->Index->getVar('charset')?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo FTP_EXT?>tpls/img/favicon.ico" />
<?php echo $this->Index->getVar('css')?>
<?php if ($this->pop): echo $this->Index->getVar('conf').$this->Index->getVar('js');
else:?><script> if(typeof $=='undefined')var $=function(f){this.ready=function(f){document.addEventListener('DOMContentLoaded',f)};if(typeof(f)=='function')return this.ready(f);return this},S={C:{},G:{},A:{L:{}}},jQuery=$;</script><?php endif;?>
</head>
<body<?php echo $this->ui['body']?> style="background-image:url(<?php echo FTP_EXT?>tpls/img/admin_bg.jpg);background-size:cover">