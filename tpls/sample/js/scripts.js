window.fbAsyncInit = function() {if (!Conf.fb){FB.init({appId:'504394089728019',xfbml: true,version: 'v2.4'});Conf.fb=true}};
(function(d, s, id){var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return;js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/sdk.js";fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));


$().ready(function(){
	$('#one').ContentSlider({
		width : '940px',
		height : '240px',
		speed : 400,
		easing : 'easeOutSine',
		leftBtn : '/tpls/ajaxel/images/left_nav.png',
		rightBtn : '/tpls/ajaxel/images/right_nav.png'
	});
	
	$('#menu a').click(function(){
		$('#menu li').removeClass('current');
		$(this).parent().addClass('current');
	});
	
	var f = {}, p = {};
	p.play = "true";
	p.menu = "false";
	p.scale = "showall";
	p.wmode = "transparent";
	p.allowfullscreen = "true";
	p.allowscriptaccess = "always";
	p.allownetworking = "samedomain";
	f.cssSource = "/tpls/ajaxel/images/xml/piecemaker.css";
	
	f.xmlSource = "/tpls/ajaxel/images/xml/ajaxel-cms.xml";
	swfobject.embedSWF('/tpls/ajaxel/images/piecemaker.swf', 's-ajaxel-cms', 300, 240, 10, null, f, p, null);
	setTimeout(function(){
		f.xmlSource = "/tpls/ajaxel/images/xml/ajaxel-intranet.xml";
		swfobject.embedSWF('/tpls/ajaxel/images/piecemaker.swf', 's-ajaxel-intranet', 300, 240, 10, null, f, p, null);
	},400);
	setTimeout(function(){
		f.xmlSource = "/tpls/ajaxel/images/xml/ajaxel-slots.xml";
		swfobject.embedSWF('/tpls/ajaxel/images/piecemaker.swf', 's-ajaxel-slots', 300, 240, 10, null, f, p, null);
	},800);
});

function ready() {
	Cufon.now();
}

function to_download(close,fn) {
	if (close) {
		$.unblockUI();
		$('#download').fadeOut(fn);
		return;
	}
	$.blockUI();
	$('#download').css({
		top: 120,
		left: $(window).width() /2 - 486 / 2,
		zIndex:2000
	}).fadeIn(function(){
		if (fn) fn()
	}).draggable({
		handle: 'h3'
	});
}
function to_survey(close) {
	if (close) {
		$.unblockUI();
		$('#survey').fadeOut();
		return;
	}
	$('#survey_error').hide();
	$.blockUI();
	$('#survey').css({
		top: 120,
		left: $(window).width() /2 - 486 / 2,
		zIndex:2000
	}).fadeIn().draggable({
		handle: 'h3'
	});
}
