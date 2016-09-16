<?php /* Smarty version 3.1.27, created on 2015-12-23 15:53:40
         compiled from "D:\home\alx\www\tpls\sample\contact.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:20815567ac384c20307_81595139%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8261a43eeed29f83f2ccaa94b1b41a08dcd6fe33' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\contact.tpl',
      1 => 1450886019,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '20815567ac384c20307_81595139',
  'variables' => 
  array (
    'URL' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ac384cfbc08_70986176',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ac384cfbc08_70986176')) {
function content_567ac384cfbc08_70986176 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '20815567ac384c20307_81595139';
?>
<h2><?php echo l('I’d Totally Love to Hear from You');?>
</h2><p><?php echo l('[contact-intro]Think I might be a good fit for your next web project?<br>
Fill out the form below, give us a call, or drop us a line and tell us about it.');?>
</p><div class="hr"></div><div class="col_w420 float_l"><div id="contact_form"><h4><?php echo l('Quick Contact Form');?>
</h4><form method="post" id="contact-form" class="form ajax" action="?<?php echo $_smarty_tpl->tpl_vars['URL']->value;?>
"><?php echo $_smarty_tpl->getSubTemplate ('includes/form_errors.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('table'=>false), 0);
?>
<label for="email"><?php echo l('Your email:');?>
</label><input type="text" name="contact[email]" value="<?php echo strform($_POST['contact']['email']);?>
" class="validate-email required input_field" /><div class="cleaner_h10"></div><label for="text"><?php echo l('Message:');?>
</label><textarea name="contact[text]" rows="0" style="height:240px;" cols="0" class="required"><?php echo strform($_POST['contact']['text']);?>
</textarea><div class="cleaner_h10"></div><center><button type="submit" style="float:none" class="btn"><?php echo l('Send');?>
</button></center></form></div></div><div class="col_w420 last_box" style="padding-left:20px;"><h4><?php echo l('Mailing Address');?>
</h4><?php echo l('[mailing-address]Nevada street<br/>
South-carolina, Francisco<br/>
Bermuda');?>
<br/><br/><iframe width="410" height="240" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=<?php echo @constant('GOOGLE_MAPS_KEY');?>
&q=<?php echo urlencode('Nevada street South-carolina, Francisco Bermuda');?>
"></iframe><br/><br/>Email: <a href="mailto:info@ajaxel.com">info@ajaxel.com</a><br/>Tel: +372-56-759-366<br/>Skype: <a href="skype:ajaxel.com?chat">ajaxel.com</a></div><?php }
}
?>