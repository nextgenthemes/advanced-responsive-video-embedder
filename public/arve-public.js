(function ($) {
	'use strict';
	var remove_unwanted_stuff = function() {
		$('.arve-wrapper').find('p, .fluid-width-video-wrapper').contents().unwrap();
		$('.arve-wrapper br').remove();
		$('.arve-inner').removeAttr('width height');
	};
	remove_unwanted_stuff();
	$( document ).ready(function() {
	  remove_unwanted_stuff();
	});
}(jQuery));
