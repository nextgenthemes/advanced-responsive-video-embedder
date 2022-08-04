import './main.scss';

declare global {
	interface Window {
		jQuery;
	}
}

const qsa = document.querySelectorAll.bind(
	document
) as typeof document.querySelectorAll;
const jq = window.jQuery;

globalID();
removeUnwantedStuff();

document.addEventListener( 'DOMContentLoaded', (): void => {
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

function removeUnwantedStuff(): void {
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
			el.classList.remove(
				'wp-embed-aspect-16-9',
				'wp-has-aspect-ratio'
			);

			const wrapper = el.querySelector( '.wp-block-embed__wrapper' );

			if ( wrapper ) {
				unwrap( wrapper );
			}
		}
	} );
}

function globalID(): void {
	// Usually the id should be already there added with php using the language_attributes filter
	if ( 'html' === document.documentElement.id ) {
		return;
	}

	if ( ! document.documentElement.id ) {
		document.documentElement.id = 'html';
	} else if ( ! document.body.id ) {
		document.body.id = 'html';
	}
}

function unwrap( el: Element ): void {
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
