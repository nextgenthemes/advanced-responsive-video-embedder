(function ( $ ) {
	"use strict";
	$(function () {
		
		var w = 770;
		w = $(window).width();
		
		if (w < 1024) {
		
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

		var toggleFullScreen = function(e) {

			if (!document.mozFullScreen && !document.webkitFullScreen) {
				if (e.mozRequestFullScreen) {
					e.mozRequestFullScreen();
				} else {
					e.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
				}
			} else {
				if (document.mozCancelFullScreen) {
					document.mozCancelFullScreen();
				} else {
					document.webkitCancelFullScreen();
				}
			}
		}

		$('.TTTarve-thumb-wrapper').click( function() {
			toggleFullScreen( $(this).get(0) );
		});
		
		document.addEventListener("keydown", function(e) {
			if (e.keyCode == 13) {
				toggleFullScreen();
			}
		}, false);


	});
}(jQuery));