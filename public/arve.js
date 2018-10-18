(function ($) {
	'use strict';

	function remove_unwanted_stuff() {
		$('.arve-wrapper').find('p, .video-wrap, .fluid-width-video-wrapper, .fluid-vids').contents().unwrap();
		$('.arve-wrapper br').remove();
		$('.arve-iframe, .arve-video').removeAttr('width height style');

		$('.wp-block-embed').each( function( index ) {

			if ( $(this).has('.arve-wrapper') ) {

				$(this).removeClass( 'wp-embed-aspect-16-9 wp-has-aspect-ratio' );

				if ( $(this).has('.wp-block-embed__wrapper') ) {
					$(this).find('.wp-block-embed__wrapper').contents().unwrap();
				}
			}
		} );
	};

	function global_id() {

		if ( $( 'html[id="arve"]' ).length ) {
			return;
		}

		if ( $( 'html[id]' ).length <= 0 ) {

			$( 'html' ).attr( 'id', 'arve' );

		} else if ( $( 'body[id]' ).length <= 0 ) {

			$( 'body' ).attr( 'id', 'arve' );

		} else {

			$( 'body' ).wrapInner( '<div id="arve">' );
		}
	}

	remove_unwanted_stuff();
	global_id();

	$( document ).ready( function() {
		remove_unwanted_stuff();
		global_id();
	} );

}(jQuery));
