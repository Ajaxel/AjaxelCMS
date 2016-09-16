<?php /* Smarty version 3.1.27, created on 2015-12-23 15:54:29
         compiled from "D:\home\alx\www\tpls\sample\pages\services.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:22238567ac3b518a6f6_25858235%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0e72a02085a89d4d15cafac606584dfac5bf2b94' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\services.tpl',
      1 => 1438642557,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22238567ac3b518a6f6_25858235',
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac3b5230005_19311295',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac3b5230005_19311295')) {
function content_567ac3b5230005_19311295 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '22238567ac3b518a6f6_25858235';
?>
<div class="col_w420"><h2><?php echo l('Service Overviews');?>
</h2><p><em><?php echo l('[services-intro]We are a small group of professional web designers and developers who are dedicated to creating powerful, effective and engaging websites.');?>
</em></p><div class="image_wrapper image_fl"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/image_06.jpg" /></div><p><?php echo l('@[services-text]Our bold design style and use of ultra-clean standards-based markup code combine to produce websites that boast exceptional search engine result positions, increased conversions and superior visitor loyalty.<br>
<br>
Ajaxel Web Studio creates strong internet marketing campaigns and effective web presences for small to medium size businesses.<br>
<br>
We specialize in designing powerful branding and developing W3C standards compliant websites that are compatible with the latest devices.');?>
</p><div class="hr"></div><div class="image_wrapper image_fl"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/image_07.jpg" /></div><?php echo l('[seos]Our team focuses on delivering the industry’s most innovative solutions through stunning corporate grade graphics, W3C standards based source code and white hat SEO strategies. We provide professional web development and SEO management services that can greatly increase your market reach and maximize the performance of your website.');?>
</div><div class="col_w420"><h2><?php echo l('Service List');?>
</h2><?php echo l('[services_list]<ul class="tmo_list">
	<li>PHP and JavaScript Applications</li>
	<li>Custom E-Commerce Solutions</li>
	<li>Effective Marketing Campaigns</li>
	<li>Proven SEO Results</li>
	<li>And many other Developments that are with PHP, JavaScript and domains</li>
</ul>');?>
<div class="hr"></div><div class="image_wrapper image_fl"><img src="/<?php echo @constant('HTTP_DIR_TPL');?>
images/web-design2.jpg" /></div><?php echo l('[ajaxel-studios]Ajaxel Web Studio is a family owned and operated company. We pride ourselves on providing value in our products and excellence in our service. We understand what it takes to build websites that not only perform great on Google, but also captivate visitors, help maximize business potential and accelerate profits.');?>
</div><div class="hr"></div><div class="btn_more"><a href="?order"><?php echo l('Order Now');?>
</a></div><?php }
}
?>