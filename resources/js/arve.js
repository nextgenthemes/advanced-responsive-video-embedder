const qsa = document.querySelectorAll.bind( document );

document.body.addEventListener( 'mouseover', iframeEnterEvent, false );
document.body.addEventListener( 'touchend', iframeEnterEvent, false );

function iframeEnterEvent( ev ) {

	const ele  = ev.target;
	const arve = ele.closest( '.arve[mode="normal"]' );

	if ( ele && ele.matches( 'iframe' ) && arve ) {

		rmClass( 'arve--clicked', 'arve--clicked' );
		arve.classList.add( 'arve--clicked' );
	}
}

document.body.addEventListener( 'play', videoPlayEvent, true );

function videoPlayEvent( ev ) {

	const ele  = ev.target;
	const arve = ele.closest( '.arve' );

	if ( ele && ele.matches( 'video' ) && arve ) {
		rmClass( 'arve--clicked', 'arve--clicked' );
		arve.classList.add( 'arve--clicked' );
	}
}

function rmClass( selector, ...classes ) {
	qsa( selector ).forEach( ( el ) => {
		el.classList.remove( ...classes );
	} );
}

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
			el.classList.remove( [ 'wp-embed-aspect-16-9', 'wp-has-aspect-ratio' ] );

			const $WRAPPER = el.querySelector( '.wp-block-embed__wrapper' );

			if ( $WRAPPER ) {
				unwrap( $WRAPPER );
			}
		}
	} );
}

function globalID() {

	// Usually the id should be already there added with php using the language_attributes filter
	if ( 'global' === document.documentElement.id ) {
		return;
	}

	if ( ! document.documentElement.id ) {
		document.documentElement.id = 'global';
	} else if ( ! document.body.id ) {
		document.body.id = 'global';
	}
}

removeUnwantedStuff();
globalID();

document.addEventListener( 'DOMContentLoaded', () => {
	removeUnwantedStuff();
	globalID();
} );
