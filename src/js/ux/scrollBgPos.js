
function scrollFollowBg($element,max,init,cur) {
	
	// Cur Background Post
	var curPosY  = $element.css("background-position").split(" "), newPosY;
	curPosY      = parseInt(curPosY[1].replace("px","")); 
	newPosY = init-cur;

	if(newPosY>=max) {
		$element.css('background-position','50% ' +  newPosY  +'px');
	}
 
} 

$(function() {
	
	$(window).scroll(function() {
		scrollFollowBg($('nav'),-377,-175,$(window).scrollTop());
	});
})   