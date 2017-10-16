/*global ajaxurl */
jQuery( function( $ ) {

	$(document).on( 'click', '[data-nj-notice-id] .notice-dismiss', function() {

		var id = $(this).closest('[data-nj-notice-id]').attr('data-nj-notice-id');

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: id
			}
		});
	});
} );
