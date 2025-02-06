export {};
declare global {
	interface Window {
		wp;
		_;
	}
}

const _ = window._;
const domParser = new DOMParser();

function arveExtractURL( changed, collection, shortcode ) {
	function attrByName( name: string ) {
		return _.find( collection, function ( viewModel ) {
			return name === viewModel.model.get( 'attr' );
		} );
	}

	if ( typeof changed.value === 'undefined' ) {
		return;
	}

	const urlInput = attrByName( 'url' ).$el.find( 'input' );
	const arInput = attrByName( 'aspect_ratio' ).$el.find( 'input' );

	// <iframe src="https://example.com" width="640" height="360"></iframe>

	const iframe = domParser
		.parseFromString( changed.value, 'text/html' )
		.querySelector( 'iframe' );

	if ( iframe && iframe.hasAttribute( 'src' ) ) {
		urlInput.val( iframe.src ).trigger( 'input' );

		const w = iframe.width;
		const h = iframe.height;

		if ( w && h ) {
			arInput.val( w + ':' + h ).trigger( 'input' );
		}
	}
}

window.wp.shortcake.hooks.addAction( 'arve.url', arveExtractURL );
