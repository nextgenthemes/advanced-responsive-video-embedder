import './admin.scss';

export {};
declare global {
	interface Window {
		wp;
		jQuery;
		sui;
		/* eslint-disable-next-line */
		tb_show;
		ajaxurl;
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

window.jQuery(document).ready(function ($) {
	$('.notice.is-dismissible').on('click', '.notice-dismiss', function (event) {
		event.preventDefault();
		const $this = $(this);
		if ('undefined' == $this.parent().attr('id')) {
			return;
		}
		$.post(window.ajaxurl, {
			action: 'dnh_dismiss_notice',
			url: window.ajaxurl,
			id: $this.parent().attr('id'),
		});
	});
});
