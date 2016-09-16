<!DOCTYPE html><html><head><title><?=$this->getVar('title')?></title>
<meta name="keywords" content="<?=$this->getVar('keywords')?>" />
<meta name="description" content="<?=$this->getVar('description')?>" />
<meta name="generator" content="<?=CMS::NAME?> v<?=CMS::VERSION?>" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta property="og:url" content="http://ajaxel.com" />
<meta property="og:type" content="website" />
<meta property="og:title" content="Ajaxel - CMS and building good projects" />
<meta property="og:description" content="Very simple ajaxified CMS and framework for any project needs. Edit your website content from backend or front end. Try and see how good this stuff is!" />
<meta property="og:image" content="http://ajaxel.com/tpls/ajaxel/images/logo.png" />
<meta http-equiv="Content-Type" content="text/html; charset=<?=$this->getVar('charset')?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?=$this->getVar('favicon')?>" />
<?php echo $this->getVar('css')?>
</head>