const qs  = document.querySelector.bind( document );
const qsa = document.querySelectorAll.bind( document );

function unwrap( el ) {
	// get the element's parent node
	const parent = el.parentNode;
	// move all children out of the element
	while ( el.firstChild ) {
		parent.insertBefore( el.firstChild, el );
	}
	// remove the empty element
	parent.removeChild( el );
}

function removeUnwantedStuff() {
	qsa( '.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids' ).forEach( ( el ) => {
		unwrap( el );
	} );

	qsa( '.arve br' ).forEach( ( el ) => {
		el.remove();
	} );

	qsa( '.arve-iframe, .arve-video' ).forEach( ( el ) => {
		el.removeAttribute( 'width' );
		el.removeAttribute( 'height' );
		el.removeAttribute( 'style' );
	} );

	qsa( '.wp-block-embed' ).forEach( ( el ) => {
		if ( el.querySelector( '.arve' ) ) {
			const $WRAPPER = el.querySelector( '.wp-block-embed__wrapper' );
			el.classList.remove( [ 'wp-embed-aspect-16-9', 'wp-has-aspect-ratio' ] );

			if ( $WRAPPER ) {
				unwrap( $WRAPPER );
			}
		}
	} );
}

function globalID() {
	if ( qs( 'html[id="arve"]' ) ) {
		return;
	}

	if ( null === qs( 'html[id]' ) ) {
		qs( 'html' ).setAttribute( 'id', 'arve' );
	} else if ( null === qs( 'body[id]' ) ) {
		document.body.setAttribute( 'id', 'arve' );
	} else {
		const $WRAP = document.createElement( 'div' );
		$WRAP.setAttribute( 'id', 'arve' );
		while ( document.body.firstChild ) {
			$WRAP.append( document.body.firstChild );
		}
		document.body.append( $WRAP );
	}
}

removeUnwantedStuff();
globalID();

document.addEventListener( 'DOMContentLoaded', () => {
	removeUnwantedStuff();
	globalID();
} );
