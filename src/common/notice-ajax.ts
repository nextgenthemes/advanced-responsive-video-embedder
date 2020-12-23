export {};
declare global {
	interface Window {
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

		const httpRequest = new XMLHttpRequest();
		const postData = 'action=' + notice.dataset.nextgenthemesNoticeId;

		httpRequest.open('POST', window.ajaxurl);
		httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		httpRequest.send(postData);
	},
	false
);
