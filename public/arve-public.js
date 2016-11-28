(function ($) {
	'use strict';
	var remove_unwanted_stuff = function() {
		$('.arve-wrapper').find('p, .fluid-width-video-wrapper, .fluid-vids').contents().unwrap();
		$('.arve-wrapper br').remove();
		$('.arve-iframe, .arve-video').removeAttr('width height style');
	};
	remove_unwanted_stuff();
	$( document ).ready(function() {
	  remove_unwanted_stuff();
	});
}(jQuery));
