<?php /* Smarty version 3.1.27, created on 2015-12-23 18:04:54
         compiled from "D:\home\alx\www\tpls\sample\pages\slots.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:12961567ae2462b6f12_67031449%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '42887c8ec6a923b2c25ac4061af14e9d425f8a05' => 
    array (
      0 => 'D:\\home\\alx\\www\\tpls\\sample\\pages\\slots.tpl',
      1 => 1450893891,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12961567ae2462b6f12_67031449',
  'variables' => 
  array (
    'winner' => 0,
    'row' => 0,
    'sess_email' => 0,
    'caption' => 0,
    'slots_top10' => 0,
    'top100' => 0,
    'i' => 0,
    'd' => 0,
    'final' => 0,
    'j' => 0,
    'yw' => 0,
    'arr' => 0,
    'place' => 0,
    'game_row' => 0,
    'credits_today' => 0,
    'credits' => 0,
    'top_score' => 0,
    'clock' => 0,
    'game' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_567ae246580367_09882301',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_567ae246580367_09882301')) {
function content_567ae246580367_09882301 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '12961567ae2462b6f12_67031449';
$_smarty_tpl->tpl_vars['winner'] = new Smarty_Variable(0, null, 0);?><div id="slots" style="width:900px;margin:0 auto;color:#000"><div id="slots_top"><a href="javascript:;" onclick="S.L.open('rules')" class="l"></a><a href="javascript:;" onclick="S.L.open('paytable')" class="r"></a></div><div id="slots_barrels"><div class="slots_left slots_lines1"></div><ul></ul><ol></ol><div class="slots_lines"></div><div class="slots_right"><a href="javascript:;" onclick="S.L.mute(this)" class="slots_mute"></a><button onclick="S.L.spin()" id="slots_spin"></button></div></div><div id="slots_buttons" class="slots_lines1"></div><div id="slots_win"></div><div id="slots_double"><button type="button" onclick="S.L.double()"></button></div><div id="slots_paytable"<?php if ($_smarty_tpl->tpl_vars['winner']->value) {?> class="slots_winner" style="background:url(/<?php echo @constant('HTTP_DIR_TPL');?>
images/slots/winner.jpg)"<?php }?>><p class="p"><?php $_smarty_tpl->_capture_stack[0][] = array('default', 'caption', null); ob_start(); ?>Hi <?php if ($_smarty_tpl->tpl_vars['row']->value['firstname']) {
echo $_smarty_tpl->tpl_vars['row']->value['firstname'];
} else {
echo $_smarty_tpl->tpl_vars['sess_email']->value;
}?>, and welcome to our GOLD RUSH SLOT MACHINE.<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();
echo $_smarty_tpl->tpl_vars['caption']->value;?>
</p><ul id="slots_top10" style="padding-left:45px"><?php echo $_smarty_tpl->tpl_vars['slots_top10']->value;?>
</ul></div><div id="slots_rules"><p class="p"><?php echo $_smarty_tpl->tpl_vars['caption']->value;?>
</p><div class="l" style="width:350px;padding-right:0;">GOLD RUSH SLOTMACHINE<br><br><?php if ($_smarty_tpl->tpl_vars['sess_email']->value) {
echo lang('@[slots_rules_free]As a crowdminer you get 1000 FREE gold nuggets  to play with.<br>
<br>
You can choose from 1 to 9 lines bet: each line costs 1 nugget so 9 lines will cost 9 nuggets. You can win Gold Coins and the total amount of your winnings will be shown in the scoreboard<br>
<br>
When win you can double up, if you decide to double up the game you will see a picture which you will find again behind one of the questions marks. If you win, you will double up your gold coins you just won, if you fail you will lose them all.<br>
<br>
If you get 5 GOLD RUSH DAYS logos you will win a FREE crowdminer access STANDART.');
} else {
echo lang('@[slots_rules]As a crowdminer you get 1000 free gold nuggets every week to play with.<br>
<br>
You can choose from 1 to 9 lines bet: each line costs 1 nugget so 9 lines will cost 9 nuggets. You can win Gold Coins and the total amount of your winnings will be shown in the scoreboard “Your Weekly Score”.<br>
<br>
When win you can double up, if you decide to double up the game you will see a picture which you will find again behind one of the questions marks. If you win, you will double up your gold coins you just won, if you fail you will lose them all.<br>
<br>
The 10 best players will be included in the “Top 10 List” and they will get the chance to partecipate in the final round to win a 100gr gold bar.
The final round will be on the 14th week, from March 31st to April 6th 2014.<br>
<br>
In the final round all the invited players will get 5000 nuggets and the top player on this round will get a 100gr gold bar.');?>
<br><br>Good luck, <?php echo first($_smarty_tpl->tpl_vars['row']->value['firstname']);?>
!<?php }?></div></div><div id="slots_top100"><p class="p"><?php echo $_smarty_tpl->tpl_vars['caption']->value;?>
</p><div><table cellspacing="0" class="slots_table"><tbody><tr><td><table cellspacing="0" class="slots_table2"><thead><tr><th colspan="2">player</th><th>gold coins</th></tr></thead><tbody><?php
$_from = $_smarty_tpl->tpl_vars['top100']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['d'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['d']->_loop = false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->_loop = true;
$foreach_d_Sav = $_smarty_tpl->tpl_vars['d'];
if ($_smarty_tpl->tpl_vars['i']->value <= 21) {?><tr><td><?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
.</td><td class="slots_i"><img src="/tpls/img/flags/24/<?php echo $_smarty_tpl->tpl_vars['d']->value['country'];?>
.png"> <span><?php echo $_smarty_tpl->tpl_vars['d']->value['firstname'];?>
</span></td><td title="Nuggets left: <?php echo $_smarty_tpl->tpl_vars['d']->value['slots_today'];?>
"><?php echo number_format($_smarty_tpl->tpl_vars['d']->value['slots_credits'],0,'',',');?>
</td></tr><?php }
$_smarty_tpl->tpl_vars['d'] = $foreach_d_Sav;
}
?></tbody></table></td><td><table cellspacing="0" class="slots_table2"><thead><tr><th colspan="2">player</th><th>gold coins</th></tr></thead><tbody><?php
$_from = $_smarty_tpl->tpl_vars['top100']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['d'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['d']->_loop = false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->_loop = true;
$foreach_d_Sav = $_smarty_tpl->tpl_vars['d'];
if ($_smarty_tpl->tpl_vars['i']->value > 21 && $_smarty_tpl->tpl_vars['i']->value <= 43) {?><tr><td><?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
.</td><td class="slots_i"><img src="/tpls/img/flags/24/<?php echo $_smarty_tpl->tpl_vars['d']->value['country'];?>
.png"> <span><?php echo $_smarty_tpl->tpl_vars['d']->value['firstname'];?>
</span></td><td title="Nuggets left: <?php echo $_smarty_tpl->tpl_vars['d']->value['slots_today'];?>
"><?php echo number_format($_smarty_tpl->tpl_vars['d']->value['slots_credits'],0,'',',');?>
</td></tr><?php }
$_smarty_tpl->tpl_vars['d'] = $foreach_d_Sav;
}
?></tbody></table></td><td><table cellspacing="0" class="slots_table2"><thead><tr><th colspan="2">player</th><th>gold coins</th></tr></thead><tbody><?php
$_from = $_smarty_tpl->tpl_vars['top100']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['d'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['d']->_loop = false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->_loop = true;
$foreach_d_Sav = $_smarty_tpl->tpl_vars['d'];
if ($_smarty_tpl->tpl_vars['i']->value > 43) {?><tr><td><?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
.</td><td class="slots_i"><img src="/tpls/img/flags/24/<?php echo $_smarty_tpl->tpl_vars['d']->value['country'];?>
.png"> <span><?php echo $_smarty_tpl->tpl_vars['d']->value['firstname'];?>
</span></td><td title="Nuggets left: <?php echo $_smarty_tpl->tpl_vars['d']->value['slots_today'];?>
"><?php echo number_format($_smarty_tpl->tpl_vars['d']->value['slots_credits'],0,'',',');?>
</td></tr><?php }
$_smarty_tpl->tpl_vars['d'] = $foreach_d_Sav;
}
?></tbody></table></td></tr></tbody></table></div></div><div id="slots_final"><p class="p"><?php echo $_smarty_tpl->tpl_vars['caption']->value;?>
</p><div><table cellspacing="0" class="slots_table slots_span4"><tbody><?php $_smarty_tpl->tpl_vars['j'] = new Smarty_Variable(0, null, 0);
$_from = $_smarty_tpl->tpl_vars['final']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['arr'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['arr']->_loop = false;
$_smarty_tpl->tpl_vars['yw'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['yw']->value => $_smarty_tpl->tpl_vars['arr']->value) {
$_smarty_tpl->tpl_vars['arr']->_loop = true;
$foreach_arr_Sav = $_smarty_tpl->tpl_vars['arr'];
if ($_smarty_tpl->tpl_vars['j']->value%4 == 0) {?><tr><?php }?><td><table cellspacing="0" class="slots_table2"><thead><tr><th colspan="2"><?php echo substr($_smarty_tpl->tpl_vars['yw']->value,0,4);?>
, week: <?php echo substr($_smarty_tpl->tpl_vars['yw']->value,4);?>
</th><th>coins</th></tr></thead><tbody><?php
$_from = $_smarty_tpl->tpl_vars['arr']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$_smarty_tpl->tpl_vars['d'] = new Smarty_Variable;
$_smarty_tpl->tpl_vars['d']->_loop = false;
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable;
foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->_loop = true;
$foreach_d_Sav = $_smarty_tpl->tpl_vars['d'];
if ($_smarty_tpl->tpl_vars['i']->value <= 9) {?><tr><td><?php echo $_smarty_tpl->tpl_vars['i']->value+1;?>
.</td><td class="slots_i"><img src="/tpls/img/flags/24/<?php echo $_smarty_tpl->tpl_vars['d']->value['country'];?>
.png"> <span><?php echo $_smarty_tpl->tpl_vars['d']->value['firstname'];?>
</span></td><td><?php echo number_format($_smarty_tpl->tpl_vars['d']->value['credits'],0,'',',');?>
</td></tr><?php }
$_smarty_tpl->tpl_vars['d'] = $foreach_d_Sav;
}
?></tbody></table></td><?php if ($_smarty_tpl->tpl_vars['j']->value%4 == 3) {?></tr><?php }
$_smarty_tpl->tpl_vars['j'] = new Smarty_Variable($_smarty_tpl->tpl_vars['j']->value+1, null, 0);
$_smarty_tpl->tpl_vars['arr'] = $foreach_arr_Sav;
}
?></tbody></table></div></div><ul class="slots_stats"><li class="slots_li0"><?php if ($_smarty_tpl->tpl_vars['row']->value) {?>HI <?php echo $_smarty_tpl->tpl_vars['row']->value['firstname'];?>
 <?php echo $_smarty_tpl->tpl_vars['row']->value['lastname'];?>
 YOU ARE NUMBER <?php echo $_smarty_tpl->tpl_vars['place']->value;?>
 ON TOPLIST OF GAME #<?php echo $_smarty_tpl->tpl_vars['game_row']->value['gameid'];
} else { ?>HI <?php echo $_smarty_tpl->tpl_vars['sess_email']->value;?>
, WELCOME TO OUR SLOT MACHINE<?php }?></li><li class="slots_li1">FREE SPINS</li><li class="slots_li2">ROUND NUGGETS </li><li class="slots_li3">YOUR SCORE</li><li class="slots_li4">TOP SCORE</li></ul><ul id="slots_stats"><li class="slots_li1" id="slots_free">0</li><li class="slots_li2" id="slots_nuggets"><?php echo number_format($_smarty_tpl->tpl_vars['credits_today']->value,'0','',',');?>
</li><li class="slots_li3" id="slots_credits"><?php echo number_format($_smarty_tpl->tpl_vars['credits']->value,'0','',',');?>
</li><li class="slots_li4" id="slots_topscore"><?php echo number_format($_smarty_tpl->tpl_vars['top_score']->value,'0','',',');?>
</li><li class="slots_li5" id="slots_clock"><?php echo $_smarty_tpl->tpl_vars['clock']->value;?>
</li></ul></div><div id="slots_sounds"></div><?php echo '<script'; ?>
 type="text/javascript">$().ready(function(){S.G.addCSS('/<?php echo @constant('HTTP_DIR_TPL');?>
css/slots.css');S.G.addJS(['/tpls/js/plugins/jquery.easie.min.js','/tpls/js/slots.js'],function(){S.L.init(<?php echo json($_smarty_tpl->tpl_vars['game']->value);?>
,'/<?php echo @constant('HTTP_DIR_TPL');?>
images/slots/','NUGGET','BIG PRIZE','CREDIT','S');});});<?php echo '</script'; ?>
><?php }
}
?>