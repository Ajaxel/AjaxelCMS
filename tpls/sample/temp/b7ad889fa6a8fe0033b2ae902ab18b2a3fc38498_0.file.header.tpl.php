<?php /* Smarty version 3.1.27, created on 2015-12-24 12:52:10
         compiled from "D:\home\alx\www\tpls\sample\header.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:28915567bea7aa92e01_65655815%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b7ad889fa6a8fe0033b2ae902ab18b2a3fc38498' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\header.tpl',
      1 => 1450961275,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28915567bea7aa92e01_65655815',
  'variables' => 
  array (
    'URL' => 0,
    'langs' => 0,
    'lang' => 0,
    'l' => 0,
    'u' => 0,
    'a' => 0,
    'User' => 0,
    'arrMenu' => 0,
    'm' => 0,
    'url0' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567bea7acd6046_97018396',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567bea7acd6046_97018396')) {
function content_567bea7acd6046_97018396 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '28915567bea7aa92e01_65655815';
?>
<body><div id="fb-root"></div><div id="wrapper_outer"><div id="wrapper"><div id="header"><div id="site_title"><h1><a href="/"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/logo.png" alt="Ajaxel Logo" /><br><span style="color:#99cc34">My</span> <span style="color:#0099ff">New</span> <span style="color:#fd9902">website</span><i><?php echo l('Thank you for selecting Ajaxel CMS!');?>
</i></a></h1></div><div id="faceb"><div class="fb-like" data-href="http://ajaxel.com" data-width="220" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div></div><ul id="social_box"><li><a href="http://www.ajaxel.com"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/facebook.png" alt="facebook" /></a></li><li><a href="#"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/twitter.png" alt="twitter" /></a></li><li><a href="#"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/linkedin.png" alt="linkin" /></a></li><li><a href="#"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/technorati.png" alt="technorati" /></a></li><li><a href="#"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/myspace.png" alt="myspace" /></a></li><li>&nbsp;</li><?php $_smarty_tpl->tpl_vars['_URL'] = new Smarty_Variable(Call('URL','ht',$_smarty_tpl->tpl_vars['URL']->value), null, 0);
$_from = $_smarty_tpl->tpl_vars['langs']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['a'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['a']->_loop = false;
$_smarty_tpl->tpl_vars['l'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['l']->value => $_smarty_tpl->tpl_vars['a']->value) {
$_smarty_tpl->tpl_vars['a']->_loop = true;
$foreach_a_Sav = $_smarty_tpl->tpl_vars['a'];
?><li<?php if ($_smarty_tpl->tpl_vars['lang']->value == $_smarty_tpl->tpl_vars['l']->value) {?> class="active"<?php }?>><a href='/<?php echo $_smarty_tpl->tpl_vars['l']->value;
echo $_smarty_tpl->tpl_vars['u']->value;?>
' onclick="location.href='/<?php echo $_smarty_tpl->tpl_vars['l']->value;?>
'+S.C.REFERER;return false" title="<?php echo $_smarty_tpl->tpl_vars['a']->value[1];?>
"><img src="/tpls/img/flags/24/<?php echo $_smarty_tpl->tpl_vars['l']->value;?>
.png" /></a></li><?php
$_smarty_tpl->tpl_vars['a'] = $foreach_a_Sav;
}
if (!$_smarty_tpl->tpl_vars['User']->value['UserID']) {?><li><a href="?user&login" class="ajax"><?php echo l('Login');?>
</a></li><li>&ndash;&nbsp;</li><li><a href="?user&register" class="ajax"><?php echo l('Join');?>
</a></li><?php } else { ?><li><a href="?<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
&logout"><?php echo l('Logout');?>
</a></li><li>&ndash;&nbsp;</li><li><a href="?user&profile" class="ajax"><?php echo l('My Profile');?>
</a></li><?php }?></ul><div class="cleaner"></div></div><div id="menu"><ul><?php
$_from = $_smarty_tpl->tpl_vars['arrMenu']->value['top'];
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['m'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['m']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['m']->value) {
$_smarty_tpl->tpl_vars['m']->_loop = true;
$foreach_m_Sav = $_smarty_tpl->tpl_vars['m'];
?><li<?php if ($_smarty_tpl->tpl_vars['m']->value['name'] == $_smarty_tpl->tpl_vars['url0']->value) {?> class="current"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['m']->value['url'];?>
" class="ajax"><?php echo $_smarty_tpl->tpl_vars['m']->value['title'];?>
</a></li><?php
$_smarty_tpl->tpl_vars['m'] = $foreach_m_Sav;
}
?></ul></div><div id="slider_wrapper"><div id="slider"><div id="one" class="contentslider"><div class="cs_wrapper"><div class="cs_slider"><div class="cs_article"><div class="slider_content_wrapper"><div class="slider_image"><a href="?services" class="ajax"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/slide_1.jpg" alt="This is sample template" /></a></div><div class="slider_content"><div class="h"><h2><?php echo l('[slide1-1]This is Sample template');?>
</h2><p><?php echo l('[slide1-2]That is just has been installed with Ajaxel CMS');?>
</p><h4><?php echo l('[slide1-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know');?>
</h4></div><div class="btn_more"><a href="?services" class="ajax"><?php echo l('More...');?>
</a><a href="http://ajaxel.com/order" target="_blank"><?php echo l('Order now');?>
</a><a href="http://ajaxel.com/contact"><?php echo l('Contact');?>
</a></div></div></div></div><div class="cs_article"><div class="slider_content_wrapper"><div class="slider_image"><a href="?services" class="ajax"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/slide_2.jpg" alt="This is sample template" /></a></div><div class="slider_content"><div class="h"><h2><?php echo l('[slide2-1]This is Sample template');?>
</h2><p><?php echo l('[slide2-2]That is just has been installed with Ajaxel CMS');?>
</p><h4><?php echo l('[slide2-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know');?>
</h4></div></div></div></div><div class="cs_article"><div class="slider_content_wrapper"><div class="slider_image"><a href="?services" class="ajax"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/slide_3.jpg" alt="This is sample template" /></a></div><div class="slider_content"><div class="h"><h2><?php echo l('[slide3-1]This is Sample template');?>
</h2><p><?php echo l('[slide3-2]That is just has been installed with Ajaxel CMS');?>
</p><h4><?php echo l('[slide3-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know');?>
</h4></div></div></div></div><div class="cs_article"><div class="slider_content_wrapper"><div class="slider_image"><a href="?services" class="ajax"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/slider/slide_4.jpg" alt="This is sample template" /></a></div><div class="slider_content"><div class="h"><h2><?php echo l('[slide4-1]This is Sample template');?>
</h2><p><?php echo l('[slide4-2]That is just has been installed with Ajaxel CMS');?>
</p><h4><?php echo l('[slide4-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know');?>
</h4></div><div class="btn_more"><a href="?services" class="ajax"><?php echo l('More...');?>
</a></div></div></div></div></div></div></div><div class="cleaner"></div></div></div><div id="top"></div><div id="content_wrapper"> <?php }
}
?>