import './main.scss';

const qsa = document.querySelectorAll.bind(document) as typeof document.querySelectorAll;

removeUnwantedStuff();
globalID();

document.addEventListener('DOMContentLoaded', () => {
	removeUnwantedStuff();
	globalID();
});

function removeUnwantedStuff() {
	qsa(
		'.arve p, .arve .video-wrap, .arve .fluid-width-video-wrapper, .arve .fluid-vids'
	).forEach((el) => {
		unwrap(el);
	});

	qsa('.arve br').forEach((el) => {
		el.remove();
	});

	qsa('.arve-iframe, .arve-video').forEach((el) => {
		el.removeAttribute('width');
		el.removeAttribute('height');
		el.removeAttribute('style');
	});

	qsa('.wp-block-embed').forEach((el) => {
		if (el.querySelector('.arve')) {
			el.classList.remove('wp-embed-aspect-16-9', 'wp-has-aspect-ratio');

			const wrapper = el.querySelector('.wp-block-embed__wrapper');

			if (wrapper) {
				unwrap(wrapper);
			}
		}
	});
}

function globalID() {
	// Usually the id should be already there added with php using the language_attributes filter
	if ('html' === document.documentElement.id) {
		return;
	}

	if (!document.documentElement.id) {
		document.documentElement.id = 'html';
	} else if (!document.body.id) {
		document.body.id = 'html';
	}
}

function unwrap(el) {
	// get the element's parent node
	const parent = el.parentNode;
	// move all children out of the element
	while (el.firstChild) {
		parent.insertBefore(el.firstChild, el);
	}
	// remove the empty element
	parent.removeChild(el);
}
