const _ = window._;
const domParser = new DOMParser();

function arveExtractURL( changed, collection, shortcode ) {

	function attrByName( name ) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get( 'attr' );
			}
		);
	}

	const val    = changed.value;
	let urlInput = null;
	let arInput  = null;
	urlInput     = attrByName( 'url' ).$el.find( 'input' );
	arInput      = attrByName( 'aspect_ratio' ).$el.find( 'input' );

	if ( typeof val === 'undefined' ) {
		return;
	}

	// <iframe src="https://example.com" width="640" height="360"></iframe>

	const $iframe = domParser
		.parseFromString( val, 'text/html' )
		.querySelector( 'iframe' );

	if ( $iframe &&
		$iframe.hasAttribute( 'src' ) &&
		$iframe.getAttribute( 'src' )
	) {
		urlInput.val( $iframe.src ).trigger( 'input' );

		const w = $iframe.width;
		const h = $iframe.height;

		if ( w && h ) {
			arInput.val( w + ':' + h ).trigger( 'input' );
		}
	}
}

window.wp.shortcake.hooks.addAction( 'arve.url', arveExtractURL );
