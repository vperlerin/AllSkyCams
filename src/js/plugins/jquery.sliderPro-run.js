jQuery( document ).ready(function( $ ) {
		$( '#slider' ).sliderPro({
			width: 960,
			height: 500,
			arrows: true,
			buttons: false,
			waitForLayers: true,
			thumbnailWidth: 192, 
			thumbnailHeight: 55, 
			thumbnailPointer: true,
			autoplay: true,
			autoScaleLayers: true,
			breakpoints: {
				500: {
					thumbnailWidth: 120,
					thumbnailHeight: 50
				}
			}
		});
});


 