(function ( $ ) {
	"use strict";
	$(function () {
		
		if ( $(window).width() < 1024) {
		
			$(".iframe").colorbox({
				iframe: true,
				scrolling: false,
				width: "99%",
				height: "99%"
			});

			$(".inline").colorbox({
				inline: true,
				scrolling: false,
				width: "99%",
				height: "99%"
			});
		
		} else {
		
			$(".iframe").colorbox({
				iframe: true,
				scrolling: false,
				width: "80%",
				height: "80%"
			});
		
			$(".inline").colorbox({
				inline: true,
				scrolling: false,
				width: "80%",
				height: "80%"
			});
		}

	});
}(jQuery));