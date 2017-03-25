(function ($) {
	'use strict';

	function remove_unwanted_stuff() {
		$('.arve-wrapper').find('p, .fluid-width-video-wrapper, .fluid-vids').contents().unwrap();
		$('.arve-wrapper br').remove();
		$('.arve-iframe, .arve-video').removeAttr('width height style');
	};

	remove_unwanted_stuff();

	$( document ).ready( function() {

	  remove_unwanted_stuff();

		if ( $( 'html[id="arve"]' ).length <= 0 ) {

			if ( $( 'body[id]' ).length ) {
				$( 'body' ).wrapInner( '<div id="arve">' );
			} else {
				$( 'body' ).attr( 'id', 'arve' );
			}

		}

	} );

}(jQuery));
