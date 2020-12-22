export {};
declare global {
	interface Window {
		jQuery;
		ajaxurl;
	}
}

document.body.addEventListener(
	'click',
	(ev: Event): void => {
		const target = ev.target as HTMLButtonElement | null;

		if (!target) {
			return;
		}

		const notice = target.closest(
			'[data-nextgenthemes-notice-id]'
		) as HTMLElement | null;

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
	},
	false
);
