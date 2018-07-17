function toggleClassOnScroll($element,_class,scrollLimit,cur) {
	$element.toggleClass(_class,(cur>scrollLimit));
}

$(function() {
	
	$(window).scroll(function() {
		toggleClassOnScroll($('nav'),'shrinked',150,$(window).scrollTop());
	});
})  