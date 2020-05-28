/* global jQuery, ajaxurl */
(function($) {
	'use strict';

	$(document).on(
		'click',
		'[data-nextgenthemes-notice-id] .notice-dismiss',
		function() {
			const id = $(this)
				.closest('[data-nextgenthemes-notice-id]')
				.attr('data-nextgenthemes-notice-id');

			$.ajax({
				url: ajaxurl,
				data: {
					action: id,
				},
			});
		}
	);
})(jQuery);
