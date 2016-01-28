function arve_extract_url( changed, collection, shortcode ) {

	function attr_by_name( name ) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get( 'attr' );
			}
		);
	}

	var val     = changed.value,
		short_val = changed.value,
		input     = attr_by_name( 'url' ).$el.find( 'input' );

	if( typeof val === 'undefined' ) {
		return;
	}

	short_val =       val.replace( 'https://www.youtube.com/watch?v=', 'https://youtu.be/' );
	short_val = short_val.replace( 'http://www.dailymotion.com/video/', 'http://dai.ly/' );

	if( short_val !== val ) {
		input.val( short_val );
	}

	if( val.match(/src="([^"]+)/) ) {

		var test_url = val.match(/src="([^"]+)/),
			only_url = test_url && test_url[1];

		input.val( only_url );
	}
}
wp.shortcake.hooks.addAction( 'arve.url', arve_extract_url );

function arve_mode_select_listener( changed, collection, shortcode ) {

	function attr_by_name(name) {
		return _.find(
			collection,
			function( viewModel ) {
				return name === viewModel.model.get('attr');
			}
		);
	}

	var val = changed.value,
		autoplay = attr_by_name( 'autoplay' ),
		grow = attr_by_name( 'grow' ),
		align = attr_by_name( 'align' );

	if( typeof val === 'undefined' ) {
		return;
	}

	if ( -1 < jQuery.inArray( val, ['lazyload-lightbox', 'lazyload-fullscreen', 'lazyload-fixed'] ) ) {
		align.$el.show();
		autoplay.$el.hide();
		grow.$el.hide();
	} else if ( 'lazyload' === val ){
		align.$el.show();
		autoplay.$el.hide();
		grow.$el.show();
	} else if ( 'link-lightbox' === val ){
		align.$el.hide();
		autoplay.$el.hide();
		grow.$el.hide();
	} else { // normal
		align.$el.show();
		autoplay.$el.show();
		grow.$el.hide();
	}
}
wp.shortcake.hooks.addAction( 'arve.mode', arve_mode_select_listener );
