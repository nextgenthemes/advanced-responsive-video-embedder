import './admin.scss';

export {};
declare global {
	interface Window {
		wp;
		jQuery;
		sui;
		/* eslint-disable-next-line */
		tb_show;
	}
}

window.jQuery(document).on('click', '#arve-btn', function () {
	const sui = window.sui;

	if ('undefined' !== typeof sui) {
		sui.utils.shortcodeViewConstructor.parseShortcodeString('[arve]');

		window.wp
			.media({
				frame: 'post',
				state: 'shortcode-ui',
				currentShortcode: sui.utils.shortcodeViewConstructor.parseShortcodeString(
					'[arve]'
				),
			})
			.open();
	} else {
		window.tb_show('ARVE Optional Features', '#TB_inline?inlineId=arve-thickbox');
	}
});
