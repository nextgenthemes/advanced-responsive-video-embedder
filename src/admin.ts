import './admin.scss';
import './admin-shortcode-creator';
import { globalID } from './main';

export {};
declare global {
	interface Window {
		wp;
		jQuery;
		sui;
		ajaxurl;
	}
}

globalID();

window.addEventListener( 'load', () => {
	const editorIframe = document.querySelector(
		'iframe[name="editor-canvas"]'
	) as HTMLIFrameElement | null;

	if ( editorIframe ) {
		const interval = setInterval( () => {
			const iframeBody = editorIframe?.contentWindow?.document?.body;

			if ( iframeBody ) {
				iframeBody.setAttribute( 'id', 'html' );
				clearInterval( interval );
			}
		}, 200 );
	}
} );

// talken from https://github.com/WordPress/gutenberg/blob/3317ba195da0149d0bae221dc3516cd76f536c5d/packages/react-native-bridge/common/gutenberg-web-single-block/editor-behavior-overrides.js#L126
// The editor-canvas iframe relies upon `srcdoc`, which does not trigger a
// `load` event. Thus, we must poll for the iframe to be ready.
let attemptsToApplyID = 0;
const interval = setInterval( () => {
	attemptsToApplyID++;
	const canvasIframe = document.querySelector(
		'iframe[name="editor-canvas"]'
	) as HTMLIFrameElement | null;

	const canvasBody = canvasIframe?.contentWindow?.document?.body;

	if ( canvasBody ) {
		clearInterval( interval );
		canvasBody.setAttribute( 'id', 'html' );
	}

	// Safeguard against an infinite loop.
	if ( attemptsToApplyID > 100 ) {
		clearInterval( interval );
	}
}, 300 );

document.addEventListener( 'click', ( event ) => {
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
