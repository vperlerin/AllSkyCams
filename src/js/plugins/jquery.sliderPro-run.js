jQuery( document ).ready(function( $ ) {
		$( '.slider-pro1' ).sliderPro({
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

		$( '.slider-pro2' ).sliderPro({
			width: 500,
			height: 420,
			arrows: true,
			buttons: true,
			waitForLayers: true,
			thumbnailWidth: 500, 
			thumbnailHeight: 500, 
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


 