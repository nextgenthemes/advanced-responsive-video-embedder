( function() {
	'use strict';

	const qs  = document.querySelector.bind( document );
	const qsa = document.querySelectorAll.bind( document );

	function removeUnwantedStuff() {

		qsa( '.arve-wrapper p, .arve-wrapper .video-wrap, .arve-wrapper .fluid-width-video-wrapper, .arve-wrapper .fluid-vids' ).forEach( el => {
			let parent = el.parentNode;

			// move all children out of the element
			while ( el.firstChild ) {
				parent.insertBefore( el.firstChild, el );
			}

			// remove the empty element
			parent.removeChild( el );
		});

		qsa( '.arve-wrapper br' ).forEach( el => {
			el.remove();
		});

		qsa( '.arve-iframe, .arve-video' ).forEach( el => {
			el.removeAttribute( 'width' );
			el.removeAttribute( 'height' );
			el.removeAttribute( 'style' );
		});

		qsa( '.wp-block-embed' ).forEach( el => {

			if ( $( this ).has( '.arve-wrapper' ) ) {

				$( this ).removeClass( 'wp-embed-aspect-16-9 wp-has-aspect-ratio' );

				if ( $( this ).has( '.wp-block-embed__wrapper' ) ) {
					$( this ).find( '.wp-block-embed__wrapper' ).contents().unwrap();
				}
			}
		});
	};

	function globalID() {

		if ( qs( 'html[id="arve"]' ) ) {
			return;
		}

		if ( null === qs( 'html[id]' ) ) {
			qs( 'html' ).setAttribute( 'id', 'arve' );
		} else if ( null === qs( 'body[id]' ) ) {
			document.body.setAttribute( 'id', 'arve' );
		} else {
			let wrapper = document.createElement( 'div' );
			wrapper.setAttribute( 'id', 'arve' );
			while ( document.body.firstChild ) {
				wrapper.append( document.body.firstChild );
			}
			document.body.append( wrapper );
		}
	}

	removeUnwantedStuff();
	globalID();

	document.addEventListener( 'DOMContentLoaded', function( event ) {
		removeUnwantedStuff();
		globalID();
	});
}() );
