jQuery.fn.extend({
	mousewheel: function(up, down, preventDefault) {
		return this.hover(
			function() {
				jQuery.event.mousewheel.giveFocus(this, up, down, preventDefault);
			},
			function() {
				jQuery.event.mousewheel.removeFocus(this);
			}
		);
	},
	mousewheeldown: function(fn, preventDefault) {
		return this.mousewheel(function(){}, fn, preventDefault);
	},
	mousewheelup: function(fn, preventDefault) {
		return this.mousewheel(fn, function(){}, preventDefault);
	},
	unmousewheel: function() {
		return this.each(function() {
			jQuery(this).unmouseover().unmouseout();
			jQuery.event.mousewheel.removeFocus(this);
		});
	},
	unmousewheeldown: jQuery.fn.unmousewheel,
	unmousewheelup: jQuery.fn.unmousewheel
});


jQuery.event.mousewheel = {
	giveFocus: function(el, up, down, preventDefault) {
		if (el._handleMousewheel) jQuery(el).unmousewheel();
		
		if (preventDefault == window.undefined && down && down.constructor != Function) {
			preventDefault = down;
			down = null;
		}
		
		el._handleMousewheel = function(event) {
			if (!event) event = window.event;
			if (preventDefault)
				if (event.preventDefault) event.preventDefault();
				else event.returnValue = false;
			var delta = 0;
			if (event.wheelDelta) {
				delta = event.wheelDelta/120;
				if (window.opera) delta = -delta;
			} else if (event.detail) {
				delta = -event.detail/3;
			}
			if (up && (delta > 0 || !down))
				up.apply(el, [event, delta]);
			else if (down && delta < 0)
				down.apply(el, [event, delta]);
		};
		
		if (window.addEventListener)
			window.addEventListener('DOMMouseScroll', el._handleMousewheel, false);
		window.onmousewheel = document.onmousewheel = el._handleMousewheel;
	},
	removeFocus: function(el) {
		if (!el._handleMousewheel) return;
		
		if (window.removeEventListener)
			window.removeEventListener('DOMMouseScroll', el._handleMousewheel, false);
		window.onmousewheel = document.onmousewheel = null;
		el._handleMousewheel = null;
	}
}

checkAllPrettyCheckboxes = function(caller, container){
	if($(caller).is(':checked')){
		// Find the label corresponding to each checkbox and click it
		$(container).find('input[type=checkbox]:not(:checked)').each(function(){
			$('label[for="'+$(this).attr('id')+'"]').trigger('click');
			if($.browser.msie){
				$(this).attr('checked','checked');
			}else{
				$(this).trigger('click');
			};
		});
	}else{
		$(container).find('input[type=checkbox]:checked').each(function(){
			$('label[for="'+$(this).attr('id')+'"]').trigger('click');
			if($.browser.msie){
				$(this).attr('checked','');
			}else{
				$(this).trigger('click');
			};
		});
	};
}

var init_clock=function(to) {
	var date_obj = new Date();
	var hour = date_obj.getHours();
	var minute = date_obj.getMinutes();
	var day = date_obj.getDate();
	var year = date_obj.getFullYear();
	var suffix = 'AM';
	var weekday = [
		'Sunday',
		'Monday',
		'Tuesday',
		'Wednesday',
		'Thursday',
		'Friday',
		'Saturday'
	];
	var month = [
		'January',
		'February',
		'March',
		'April',
		'May',
		'June',
		'July',
		'August',
		'September',
		'October',
		'November',
		'December'
	];
	weekday = weekday[date_obj.getDay()];
	month = month[date_obj.getMonth()];
	if (hour >= 12) suffix = 'PM';
	if (hour > 12) hour = hour - 12;
	else if (hour === 0) hour = 12;
	if (minute < 10) minute = '0' + minute;
	var clock_time = weekday + '<br />' + hour + ':' + minute + ' ' + suffix;
	var clock_date = month + ' ' + day + ', ' + year;
	$('#'+to).html(clock_time).attr('title', clock_date);
	setTimeout(S.G.init_clock, 60000);
}