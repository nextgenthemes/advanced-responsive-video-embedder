function arve_extract_url( changed, collection, shortcode ) {

	function attributeByName( name ) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get( 'attr' );
			}
		);
	}

	if( typeof changed.value !== 'undefined' && changed.value.match(/src="([^"]+)/) ) {
		// your code here.
		var test_url = changed.value.match(/src="([^"]+)/),
			only_url = test_url && test_url[1],
			input = attributeByName( 'url' ).$el.find( 'input' );

		input.val( only_url );
	}
}
wp.shortcake.hooks.addAction( 'arve.url', arve_extract_url );

function updateSelectFieldListener( changed, collection, shortcode ) {

	function attributeByName(name) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get('attr');
			}
		);
	}

	var val = changed.value,
		autoplay = attributeByName( 'autoplay' ),
		thumbnail = attributeByName( 'thumbnail' ),
		grow = attributeByName( 'grow' ),
		align = attributeByName( 'align' ),
		mode_setting = jQuery( '#arve-btn' ).attr( 'data-arve-mode' );

	if( typeof val === 'undefined' ) {
		return;
	}

	var lazyload_modes = ['lazyload', 'lazyload-lightbox', 'lazyload-fullscreen', 'lazyload-fixed'];

	if ( -1 === jQuery.inArray( val, lazyload_modes ) ) {
		autoplay.$el.show();
		thumbnail.$el.hide();
		grow.$el.hide(); 
	} else {
		autoplay.$el.hide();
		thumbnail.$el.show();
		grow.$el.show();
	}

	if ( 'link-lightbox' === val ) {
		autoplay.$el.hide();
		align.$el.hide();
	} else {
		autoplay.$el.show();
		align.$el.hide();
	}
}

wp.shortcake.hooks.addAction( 'arve.mode', updateSelectFieldListener );
