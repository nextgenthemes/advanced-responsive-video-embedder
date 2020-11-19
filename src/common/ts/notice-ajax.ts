export {};
declare global {
	interface Window {
		jQuery;
		ajaxurl;
	}
}

document.addEventListener('DOMContentLoaded', function () {
	const closeBtn = document.querySelector(
		'[data-nextgenthemes-notice-id] .notice-dismiss'
	);

	if (closeBtn) {
		closeBtn.addEventListener('click', dismiss, false);
	}
});

function dismiss(ev: Event) {
	if (!ev || !ev.target) {
		return;
	}

	const target = ev.target as HTMLElement;
	const notice = target.closest('[data-nextgenthemes-notice-id]') as HTMLElement | null;

	if (!notice) {
		return;
	}

	window.jQuery.ajax({
		url: window.ajaxurl,
		data: {
			action: notice.dataset.nextgenthemesNoticeId,
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
