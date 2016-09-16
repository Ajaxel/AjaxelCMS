{strip}
<body>
<div id="fb-root"></div>
<div id="wrapper_outer">
	<div id="wrapper">
		<div id="header">
			<div id="site_title">
				<h1>
					<a href="/"><img src="/{$smarty.const.HTTP_DIR_TPL}images/logo.png" alt="Ajaxel Logo" />
					<br><span style="color:#99cc34">My</span> <span style="color:#0099ff">New</span> <span style="color:#fd9902">website</span><i>{'Thank you for selecting Ajaxel CMS!'|l}</i></a>
				</h1>
			</div>
			<div id="faceb">
				<div class="fb-like" data-href="http://ajaxel.com" data-width="220" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>
			</div>
			
			<ul id="social_box">
				<li><a href="http://www.ajaxel.com"><img src="/{$smarty.const.HTTP_DIR_TPL}images/facebook.png" alt="facebook" /></a></li>
				<li><a href="#"><img src="/{$smarty.const.HTTP_DIR_TPL}images/twitter.png" alt="twitter" /></a></li>
				<li><a href="#"><img src="/{$smarty.const.HTTP_DIR_TPL}images/linkedin.png" alt="linkin" /></a></li>
				<li><a href="#"><img src="/{$smarty.const.HTTP_DIR_TPL}images/technorati.png" alt="technorati" /></a></li>
				<li><a href="#"><img src="/{$smarty.const.HTTP_DIR_TPL}images/myspace.png" alt="myspace" /></a></li>  
				<li>&nbsp;</li>
				{assign var='_URL' value='URL'|Call:'ht':$URL}
				{foreach from=$langs key=l item=a}
					<li{if $lang==$l} class="active"{/if}><a href='/{$l}{$u}' onclick="location.href='/{$l}'+S.C.REFERER;return false" title="{$a[1]}"><img src="/tpls/img/flags/24/{$l}.png" /></a></li>
				{/foreach}
				{if !$User.UserID}
					<li><a href="?user&login" class="ajax">{'Login'|l}</a></li>
					<li>&ndash;&nbsp;</li>
					<li><a href="?user&register" class="ajax">{'Join'|l}</a></li>
				{else}
					<li><a href="?{$URL}&logout">{'Logout'|l}</a></li>
					<li>&ndash;&nbsp;</li>
					<li><a href="?user&profile" class="ajax">{'My Profile'|l}</a></li>
				{/if}
			</ul>
			
			<div class="cleaner"></div>
		</div>
		<div id="menu">
			<ul>
				{foreach from=$arrMenu.top item=m}
				<li{if $m.name==$url0} class="current"{/if}><a href="{$m.url}" class="ajax">{$m.title}</a></li>
				{/foreach}
			</ul>
		</div>
		
		<div id="slider_wrapper">
			<div id="slider">
				<div id="one" class="contentslider">
					<div class="cs_wrapper">
						<div class="cs_slider">
							
							<div class="cs_article">
								<div class="slider_content_wrapper">
									<div class="slider_image"><a href="?services" class="ajax"><img src="/{$smarty.const.HTTP_DIR_TPL}images/slider/slide_1.jpg" alt="This is sample template" /></a></div>
									<div class="slider_content">
										<div class="h">
											<h2>{'[slide1-1]This is Sample template'|l}</h2>
											<p>{'[slide1-2]That is just has been installed with Ajaxel CMS'|l}</p>
											<h4>{'[slide1-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know'|l}</h4>
										</div>
							 			<div class="btn_more">
											<a href="?services" class="ajax">{'More...'|l}</a>
											<a href="http://ajaxel.com/order" target="_blank">{'Order now'|l}</a>
											<a href="http://ajaxel.com/contact">{'Contact'|l}</a>
										</div>
									</div>
								</div>
							</div>
							
							<div class="cs_article">
								<div class="slider_content_wrapper">
									<div class="slider_image"><a href="?services" class="ajax"><img src="/{$smarty.const.HTTP_DIR_TPL}images/slider/slide_2.jpg" alt="This is sample template" /></a></div>
									<div class="slider_content">
										<div class="h">
											<h2>{'[slide2-1]This is Sample template'|l}</h2>
											<p>{'[slide2-2]That is just has been installed with Ajaxel CMS'|l}</p>
											<h4>{'[slide2-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know'|l}</h4>
										</div>
									</div>
								</div>
							</div>
							
							<div class="cs_article">
								<div class="slider_content_wrapper">
									<div class="slider_image"><a href="?services" class="ajax"><img src="/{$smarty.const.HTTP_DIR_TPL}images/slider/slide_3.jpg" alt="This is sample template" /></a></div>
									<div class="slider_content">
										<div class="h">
											<h2>{'[slide3-1]This is Sample template'|l}</h2>
											<p>{'[slide3-2]That is just has been installed with Ajaxel CMS'|l}</p>
											<h4>{'[slide3-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know'|l}</h4>
										</div>
									</div>
								</div>
							</div>
							
							<div class="cs_article">
								<div class="slider_content_wrapper">
									<div class="slider_image"><a href="?services" class="ajax"><img src="/{$smarty.const.HTTP_DIR_TPL}images/slider/slide_4.jpg" alt="This is sample template" /></a></div>
									<div class="slider_content">
										<div class="h">
											<h2>{'[slide4-1]This is Sample template'|l}</h2>
											<p>{'[slide4-2]That is just has been installed with Ajaxel CMS'|l}</p>
											<h4>{'[slide4-3]If any questions, let <a href="http://ajaxel.com/contact">me</a> know'|l}</h4>
										</div>
							 			<div class="btn_more">
											<a href="?services" class="ajax">{'More...'|l}</a>
										</div>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</div>
				<div class="cleaner"></div>
			</div>
		</div>
		<div id="top"></div>
		<div id="content_wrapper">
{/strip} 