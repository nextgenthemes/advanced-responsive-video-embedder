import './main.scss';

declare global {
	interface Window {
		jQuery;
	}
}

const d = document;
const qsa = d.querySelectorAll.bind( d ) as typeof d.querySelectorAll;
const jq = window.jQuery;

globalID();

domReady( () => {
	removeUnwantedStuff();
} );

// Mitigation for outdated versions of fitvids
if ( jq && typeof jq.fn.fitVids !== 'undefined' ) {
	jq( document ).ready( () => {
		setTimeout( () => {
			removeUnwantedStuff();
		}, 1 );
	} );
}

function removeUnwantedStuff(){
	qsa(
		'.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids'
	).forEach( ( el ) => {
		unwrap( el );
	} );

	// Astor theme fix
	qsa( '.ast-oembed-container' ).forEach( ( el ) => {
		if ( el.querySelector( '.arve' ) ) {
			unwrap( el );
		}
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
			el.classList.remove( 'wp-embed-aspect-16-9', 'wp-has-aspect-ratio' );

			const wrapper = el.querySelector( '.wp-block-embed__wrapper' );

			if ( wrapper ) {
				unwrap( wrapper );
			}
		}
	} );
}

export function globalID(){
	// Usually the id should be already there added with php using the language_attributes filter
	if ( 'html' === d.documentElement.id ) {
		return;
	}

	if ( ! d.documentElement.id ) {
		d.documentElement.id = 'html';
	} else if ( ! d.body.id ) {
		d.body.id = 'html';
	}
}

function unwrap( el: Element ){
	const parent = el.parentNode;
	// make eslint STFU
	if ( ! parent ) {
		return;
	}
	// move all children out of the element
	while ( parent && el.firstChild ) {
		parent.insertBefore( el.firstChild, el );
	}
	// remove the empty element
	parent.removeChild( el );
}

export function domReady( callback ){
	if ( typeof d === 'undefined' ) {
		return;
	}

	if (
		d.readyState === 'complete' || // DOMContentLoaded + Images/Styles/etc loaded, so we call directly.
		d.readyState === 'interactive' // DOMContentLoaded fires at this point, so we call directly.
	) {
		return void callback();
	}

	// DOMContentLoaded has not fired yet, delay callback until then.
	d.addEventListener( 'DOMContentLoaded', callback );
}
