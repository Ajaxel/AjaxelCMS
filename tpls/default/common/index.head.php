<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6"><![endif]-->
<!--[if IE 7 ]><html class="ie ie7"><![endif]-->
<!--[if IE 8 ]><html class="ie ie8"><![endif]-->
<!--[if IE 9 ]><html class="ie ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html><!--<![endif]-->
<head><title><?=$this->getVar('title')?></title>
<meta name="keywords" content="<?=$this->getVar('keywords')?>" />
<meta name="description" content="<?=$this->getVar('description')?>" />
<meta name="generator" content="<?=CMS::NAME?> v<?=CMS::VERSION?>" />
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta charset="<?=$this->getVar('charset')?>" />
<link rel="shortcut icon" type="image/x-icon" href="<?=$this->getVar('favicon')?>" />
<link rel="canonical" href="http://<?php echo DOMAIN?>/" />
<link rel='shortlink' href='http://<?php echo DOMAIN?>/' />
<?php echo $this->getVar('css')?>
</head>