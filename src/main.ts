import './main.scss';

declare global {
	interface Window {
		jQuery;
	}
}

const d = document;
const qsa = d.querySelectorAll.bind( d );
const jq = window.jQuery;

globalID();

domReady( () => {
	removeUnwantedStuff();
} );

// Mitigation for outdated versions of fitvids
if ( jq && typeof jq.fn.fitVids !== 'undefined' ) {
	jq( d ).ready( () => {
		setTimeout( () => {
			removeUnwantedStuff();
		}, 1 );
	} );
}

function removeUnwantedStuff(): void {
	qsa(
		'.arve p:not(.arve-error p), .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids'
	).forEach( ( el ) => {
		unwrap( el );
	} );

	// Astor theme fix
	qsa( '.ast-oembed-container' ).forEach( ( el ) => {
		if ( el.querySelector( '.arve' ) ) {
			unwrap( el );
		}
	} );

	qsa( '.arve-iframe, .arve-video' ).forEach( ( el ) => {
		el.removeAttribute( 'width' );
		el.removeAttribute( 'height' );
		el.removeAttribute( 'style' );
	} );
}

function globalID(): void {
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

function unwrap( el: Element ): void {
	// Type guard for parentNode to ensure it exists and is a Node
	const parent = el.parentNode;
	if ( ! ( parent instanceof Node ) ) {
		throw new Error( 'Element has no parent node' );
	}

	// Move all children to parent
	while ( el.firstChild ) {
		parent.insertBefore( el.firstChild, el );
	}

	// Remove the empty element
	parent.removeChild( el );
}

export function domReady( callback ): void {
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
