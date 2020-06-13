/* global jQuery, ajaxurl */
(function() {
	'use strict';

	const closeBtn = document.querySelector(
		'[data-nextgenthemes-notice-id] .notice-dismiss'
	);

	closeBtn.addEventListener('click', dismiss, false);

	function dismiss() {
		const id = this.closest('[data-nextgenthemes-notice-id]').getAttribute(
			'data-nextgenthemes-notice-id'
		);

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: id,
			},
		});

		/*
		xhr = new XMLHttpRequest();

		xhr.open( 'POST', ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		xhr.onload = function() {
		    if ( xhr.status === 200 && xhr.responseText !== newName ) {
		        alert('Something went wrong.  Name is now ' + xhr.responseText);
		    }
		    else if (xhr.status !== 200) {
		        alert('Request failed.  Returned status of ' + xhr.status);
		    }
		};
		xhr.send(encodeURI('name=' + newName));
		*/
	}

})();
