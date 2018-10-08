(function () {
	'use strict';

	const qs  = document.querySelector.bind(document);
	const qsa = document.querySelectorAll.bind(document);

	function remove_unwanted_stuff() {

		qsa( '.arve-wrapper' ).forEach( el => {
			el.querySelector( 'p, .video-wrap, .fluid-width-video-wrapper, .fluid-vids' ).forEach( iel => {
				let parent = el.parentNode;
				// move all children out of the element
				while ( el.firstChild ) {
					parent.insertBefore( el.firstChild, el );
				}
				// remove the empty element
				parent.removeChild(el);
			}
		});

		qsa( '.arve-wrapper br' ).forEach( el => { el.remove() } );

		qsa( '.arve-iframe, .arve-video' ).forEach( el => {
			el.removeAttribute('width');
			el.removeAttribute('height');
			el.removeAttribute('style');
		} );
	};

	function global_id() {

		if ( qs( 'html[id="arve"]' ) ) {
			return;
		}

		if ( null === qs( 'html[id]' ) ) {

			qs( 'html' ).setAttribute( 'id', 'arve' );

		} else if ( null === qs( 'body[id]' ) ) {

			document.body.setAttribute( 'id', 'arve' );

		} else {

			let wrapper = document.createElement('div');
			wrapper.setAttribute( 'id', 'arve' );
			while ( document.body.firstChild ) {
				wrapper.append( document.body.firstChild );
			}
			document.body.append( wrapper );
		}
	}

	remove_unwanted_stuff();
	global_id();

	$( document ).ready( function() {
		remove_unwanted_stuff();
		global_id();
	} );

}());
