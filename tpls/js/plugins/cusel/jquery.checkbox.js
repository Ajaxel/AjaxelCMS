jQuery(document).ready(function(){
	niceCheck();
	
	$(window.document).ajaxStop(function(e){
		niceCheck();
	});
});

function niceCheck(){ 
	jQuery(".niceCheck").unbind('mousedown').mousedown(
		function() {changeCheck(jQuery(this))}
	);
	jQuery(".niceCheck").each(
		function() {changeCheckStart(jQuery(this))}
	);
}
function changeCheck(el)
/* 
	функция смены вида и значения чекбокса
	el - span контейнер дял обычного чекбокса
	input - чекбокс
*/
{
     var el = el,
          input = el.find("input").eq(0);
   	 if(!input.attr("checked")) {
		el.css("background-position","0 -19px");	
		input.attr("checked", true)
	} else {
		el.css("background-position","0 0");	
		input.attr("checked", false)
	}
     return true;
}

function changeCheckStart(el) {
var el = el,
		input = el.find("input").eq(0);
      if(input.attr("checked")) {
		el.css("background-position","0 -19px");	
		}
     return true;
}
		