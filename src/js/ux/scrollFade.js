function fadeOutOnScoll($element,scrollLimitOpacity,cur) {
   $element.removeClass('animated');
   $element.css('opacity',1-(cur/scrollLimitOpacity));
}

$(function() {
	
	$(window).scroll(function() {
		fadeOutOnScoll($('#welcome'),150,$(window).scrollTop());
	});
})  