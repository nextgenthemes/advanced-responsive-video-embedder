import './admin.scss';
import './admin-shortcode-creator';

export {};
declare global {
	interface Window {
		wp;
		jQuery;
		sui;
		ajaxurl;
	}
}

window.jQuery( document ).ready( function ( $ ) {
	$( '.notice.is-dismissible' ).on(
		'click',
		'.notice-dismiss',
		function ( event ) {
			event.preventDefault();
			const $this = $( this );
			if ( 'undefined' == $this.parent().attr( 'id' ) ) {
				return;
			}
			$.post( window.ajaxurl, {
				action: 'dnh_dismiss_notice',
				url: window.ajaxurl,
				id: $this.parent().attr( 'id' ),
			} );
		}
	);
} );
