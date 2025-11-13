import './admin.scss';
import './shortcode-dialog.scss';

export {};
declare global {
	interface Window {
		ajaxurl: string;
	}
}

const d = document;
const qs = d.querySelector.bind( d );

setEditorCanvasID();

function setEditorCanvasID() {
	// Taken from https://github.com/WordPress/gutenberg/blob/3317ba195da0149d0bae221dc3516cd76f536c5d/packages/react-native-bridge/common/gutenberg-web-single-block/editor-behavior-overrides.js#L126
	// The editor-canvas iframe relies upon `srcdoc`, which does not trigger a
	// `load` event. Thus, we must poll for the iframe to be ready.
	let attemptsToApplyID = 0;
	const interval = setInterval( () => {
		attemptsToApplyID++;
		const canvasIframe = qs< HTMLIFrameElement >( 'iframe[name="editor-canvas"]' );
		const canvasBody = canvasIframe?.contentDocument?.body;

		if ( canvasBody ) {
			clearInterval( interval );
			canvasBody.setAttribute( 'id', 'html' );
		}

		// Safeguard against an infinite loop.
		if ( attemptsToApplyID > 100 ) {
			clearInterval( interval );
		}
	}, 300 );
}

d.addEventListener( 'click', ( event ) => {
	const target = event?.target;

	if ( target instanceof HTMLElement && target.matches( '.notice-dismiss' ) ) {
		event.preventDefault();

		const parent = target.parentNode as HTMLDivElement | null;
		const noticeId = parent?.getAttribute( 'id' );

		if ( ! parent?.matches( '.notice.is-dismissible' ) || ! noticeId ) {
			return;
		}

		fetch( window.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
			},
			body: 'action=dnh_dismiss_notice&id=' + noticeId,
		} );
	}
} );
