( () => {
	'use strict';
	const e = {
			392: ( e, t, r ) => {
				r.d( t, { W: () => a } );
				const o = document,
					i = o.querySelectorAll.bind( o ),
					n = window.jQuery;
				function d() {
					i(
						'.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids'
					).forEach( ( e ) => {
						c( e );
					} ),
						i( '.ast-oembed-container' ).forEach( ( e ) => {
							e.querySelector( '.arve' ) && c( e );
						} ),
						i( '.arve br' ).forEach( ( e ) => {
							e.remove();
						} ),
						i( '.arve-iframe, .arve-video' ).forEach( ( e ) => {
							e.removeAttribute( 'width' ),
								e.removeAttribute( 'height' ),
								e.removeAttribute( 'style' );
						} ),
						i( '.wp-block-embed' ).forEach( ( e ) => {
							if ( e.querySelector( '.arve' ) ) {
								e.classList.remove( 'wp-embed-aspect-16-9', 'wp-has-aspect-ratio' );
								const t = e.querySelector( '.wp-block-embed__wrapper' );
								t && c( t );
							}
						} );
				}
				function a() {
					'html' !== o.documentElement.id &&
						( o.documentElement.id
							? o.body.id || ( o.body.id = 'html' )
							: ( o.documentElement.id = 'html' ) );
				}
				function c( e ) {
					const t = e.parentNode;
					if ( t ) {
						for ( ; t && e.firstChild;  ) t.insertBefore( e.firstChild, e );
						t.removeChild( e );
					}
				}
				let s;
				a(),
					( s = () => {
						d();
					} ),
					void 0 !== o &&
						( 'complete' !== o.readyState && 'interactive' !== o.readyState
							? o.addEventListener( 'DOMContentLoaded', s )
							: s() ),
					n &&
						void 0 !== n.fn.fitVids &&
						n( document ).ready( () => {
							setTimeout( () => {
								d();
							}, 1 );
						} );
			},
		},
		t = {};
	function r( o ) {
		const i = t[ o ];
		if ( void 0 !== i ) return i.exports;
		const n = ( t[ o ] = { exports: {} } );
		return e[ o ]( n, n.exports, r ), n.exports;
	}
	( r.d = ( e, t ) => {
		for ( const o in t )
			r.o( t, o ) &&
				! r.o( e, o ) &&
				Object.defineProperty( e, o, { enumerable: ! 0, get: t[ o ] } );
	} ),
		( r.o = ( e, t ) => Object.prototype.hasOwnProperty.call( e, t ) ),
		( () => {
			( 0, r( 392 ).W )();
			let e = 0;
			const t = setInterval( () => {
				e++;
				const r = document.querySelector( 'iframe[name="editor-canvas"]' ),
					o = r?.contentWindow?.document?.body;
				o && ( clearInterval( t ), o.setAttribute( 'id', 'html' ) ),
					e > 100 && clearInterval( t );
			}, 300 );
			document.addEventListener( 'click', ( e ) => {
				const t = e?.target;
				if ( t && t.matches( '.notice-dismiss' ) ) {
					e.preventDefault();
					const r = t.parentNode,
						o = r?.getAttribute( 'id' );
					if ( ! r?.matches( '.notice.is-dismissible' ) || ! o ) return;
					window.jQuery.post( window.ajaxurl, {
						action: 'dnh_dismiss_notice',
						url: window.ajaxurl,
						id: o,
					} );
				}
			} );
		} )();
} )();
