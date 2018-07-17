var scroll = new SmoothScroll('a[href*="#"]', {

	// Selectors
	ignore: '[data-scroll-ignore]', // Selector for links to ignore (must be a valid CSS selector)
 	topOnEmptyHash: false, // Scroll to the top of the page for links with href="#"

	// Speed & Easing
	speed: 250, // Integer. How fast to complete the scroll in milliseconds
	clip: true, // If true, adjust scroll distance to prevent abrupt stops near the bottom of the page
	offset: function (anchor, toggle) { return 100; },
	easing: 'easeInOutCubic', // Easing pattern to use
 	// History
	updateURL: false, // Update the URL on scroll
	popstate: false, // Animate scrolling with the forward/backward browser buttons (requires updateURL to be true)

	// Custom Events
	emitEvents: true // Emit custom events

});