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

document.body.addEventListener( 'click', ( event ) => {
	const target = event?.target as HTMLElement | null;

	if ( target && target.matches( '.notice-dismiss' ) ) {
		event.preventDefault();

		const parent = target.parentNode as HTMLDivElement | null;
		const noticeId = parent?.getAttribute( 'id' );

		if ( ! parent?.matches( '.notice.is-dismissible' ) || ! noticeId ) {
			return;
		}

		window.jQuery.post( window.ajaxurl, {
			action: 'dnh_dismiss_notice',
			url: window.ajaxurl,
			id: noticeId,
		} );

		// does not work
		// fetch( window.ajaxurl, {
		// 	method: 'POST',
		// 	headers: {
		// 		'Content-Type': 'application/json',
		// 	},
		// 	body: JSON.stringify( {
		// 		action: 'dnh_dismiss_notice',
		// 		url: window.ajaxurl,
		// 		id: noticeId,
		// 	} ),
		// } );
	}
} );
