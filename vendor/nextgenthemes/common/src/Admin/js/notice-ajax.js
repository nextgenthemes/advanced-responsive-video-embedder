(function () {
	'use strict';

	$( document).on( 'click', '[data-nextgenthemes-notice-id] .notice-dismiss', function() {

		var id = $( this ).closest( '[data-nextgenthemes-notice-id]' ).attr( 'data-nextgenthemes-notice-id' );

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: id
			}
		});
	});

}());
