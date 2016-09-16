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
* @file       tpls/admin/default/_header.php
* @category   Content management system
* @package    Ajaxel CMS
* @version    8.0, 15:25 2015-12-23
* @copyright  Copyright (c) 2007-2016, Alexander Shatalov <ajaxel.com@gmail.com>. All rights reserved.
* @license    http://ajaxel.com/license.txt
*/

?>
<?php
if (Conf()->is('cms_menus')) $menus = Conf()->g('cms_menus');
else {
	$mail_cnt = DB::one('SELECT COUNT(1) FROM '.DB_PREFIX.'mail WHERE `read`<1 AND to_id='.$this->Index->Session->UserID.' AND folder=\'INBOX\'');
	$menus = array(
		'tree'		=> array(lang('$Tree'),'places/folder'),
		'pages'		=> array(lang('$Pages'),'apps/acroread'),
	//	'sep0'		=> 1,
		'menu'		=> array(lang('$Menu'),'apps/klettres'),
		'entries'	=> array(lang('$Entries'),'actions/document-open'),
		'content'	=> array(lang('$Content'),'apps/fontforge'),
		'grid'		=> array(lang('$Grids'),'apps/kcmkwm'),
		'snippets'	=> array(lang('$Snippets'),'apps/preferences-plugin-script'),
		'sep1'		=> 1,
		'comments'	=> array(lang('$Comments'),'apps/kword'),
		'forum'		=> array(lang('$Forum'),'apps/preferences-desktop-icons'),
		'users'		=> array(lang('$Users'),'apps/system-users'),
		'sep2'		=> 1,
		'email'		=> array(lang('$Mail').($mail_cnt?' ('.$mail_cnt.')':''),'actions/'.($mail_cnt?'mail-mark-unread-new':'mail-mark-read')),
	//	'calendar'	=> array(lang('$Calendars','actions/alarmclock'),
		(ORDERS_FULL?'orders2':'orders') => array(lang('$Orders'),'actions/wallet-open'),
	//	'forms'		=> array(lang('$Forms'),'actions/insert-text'),
		'mods'		=> array(lang('$Plugins'),'apps/preferences-plugin'),
		'categories'=> array(lang('$Categories'),'places/document-multiple'),
		'lang'		=> array(lang('$Vocabulary'),'apps/KVerbos'),
	//	'browser'	=> array(FILE_BROWSER?'Browser':false,'apps/system-file-manager','S.A.W.browseServer();'),
		'sep3'		=> 1,
		'log'		=> array(lang('$Log'),'actions/ark-view'),
		'templates'	=> array(lang('$Files'),'apps/system-file-manager'),
		'settings'	=> array(lang('$Settings'),'categories/applications-system'),
		'stats'		=> array(lang('$Statistics'),'apps/kchart'),
		'sep4'		=> 1,
		'help'		=> array(lang('$Help'),'apps/help-browser'),
	);
	if (USE_TREE) unset($menus['menu'], $menus['entries'], $menus['content']);
	else unset($menus['tree'], $menus['pages']);
	if (!USE_LOG) unset($menus['log']);
	if (!$this->category_modules) unset($menus['categories']);
}
?>
<script type="text/javascript">
<?php echo Index::CDA?>
$(function(){
	var sel='<?php echo strjava(get(URL_KEY_ADMIN))?>';
	var menus=<?php echo json($menus); unset($menus);?>;
	var html='';
	for (name in menus){
		var a=menus[name];
		if(a===1) html += '<br />';
		else if (!a[0]) continue;
		else html += '<button id="a-btn_'+name+'" class="a-button_a a-button_l'+(sel==name?' ui-state-highlight':'')+'" onclick="S.A.L.get(\'?<?php echo URL_KEY_ADMIN?>='+name+'\',0,0,this,\'.a-button_l\')"><table cellspacing="0" cellpaddding="0"><tr><td><img src="<?php echo FTP_EXT?>tpls/img/oxygen/16x16/'+a[1]+'.png" width="16" alt="" /></td><td><span>'+a[0]+'</span></td></tr></table></button><div style="height:1px"></div>';
	}
	$('#a-menus').html(html);
	$('#a-wrapper').fadeIn('slow');
	$('.a-button_a').button();
});
<?php echo Index::CDZ?>
</script>
<table class="a-wrapper<?php echo $this->ui['wrapper']?>" id="a-wrapper" style="display:none"><tr valign="top">
	<td class="a-menu">
		<button class="a-button a-button_l a-logo" id="a-btn_main" onclick="S.A.L.get('?<?php echo URL_KEY_ADMIN?>',0,0,this,'.a-button_l')" id="s-title_admin"><?php echo $this->templates[$this->tpl][0]?></button>
		<div class="a-c" style="text-align:center;margin:15px 0 10px 0">
		<?php
			$url = Url::get();
			foreach ($this->langs as $l => $label) {
				echo '<a href="javascript:;" onclick="location.href=\'?'.$url.'&lang='.$l.'\'+window.location.hash" style="text-decoration:none"><img src="'.FTP_EXT.'tpls/img/flags/24/'.$l.'.png" alt="'.@$label[0].'" title="'.@$label[0].'" /></a> &nbsp;';	
			}
		?>
		</div>
		<div id="a-menus"></div>
		<div style="text-align:center;margin:30px 0" title="(<?php echo Conf()->g2('user_groups',$this->Index->Session->GroupID)?>) <?php echo lang('$LTL: %1', date('H:i d M Y',$this->Index->Session->LastLogged));?>"><?php echo lang('$Logged as')?> <a href="javascript:;" onclick="S.A.W.open('?<?php echo URL_KEY_ADMIN?>=users&id=<?php echo $this->Index->Session->UserID;?>');" style="color:inherit"><?php echo $this->Index->Session->Login?></a>
		<br /><br /><button type="button" title="<?php echo lang('$Logout')?>" class="a-button_a" style="width:44px;" onclick="$(document.body).fadeOut(function(){location.href='/?<?php echo URL_KEY_ADMIN?>&logout'})"><img src="<?php echo FTP_EXT?>tpls/img/oxygen/22x22/actions/system-shutdown.png" style="top:3px" /></button></div>
		<?php if (ADMIN_SHOW_RSS):?>
		<div class="a-ajaxel_rss ui-corner-all">
			<ul>
			<?php
			$rss = RssReader('http://ajaxel.com/rss.xml?news',false);
			
			if (isset($rss['ITEMS'])) {
				foreach ($rss['ITEMS'] as $i => $item):?><li><a href="<?php echo $item['LINK']?>" target="_blank"><span class="a-date"><?php echo date('D j M', strtotime($item['PUBDATE']))  
?></span> <?php echo $item['TITLE']?></a></li><?php
				endforeach;
			}
			unset($rss, $ex, $arrMenu);
			?>
			</ul>
		</div>
		<?php endif;?>
	</td>
	<td class="a-area">