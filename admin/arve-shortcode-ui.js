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

	if( val.match(/src="?([^\s"]+)/) ) {

		var test_url = val.match(/src="?([^\s"]+)/),
			only_url = test_url && test_url[1];

		input.val( only_url ).trigger( 'input' );
	}
}

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
		autoplay   = attr_by_name( 'autoplay' ),
		grow       = attr_by_name( 'grow' ),
		align      = attr_by_name( 'align' ),
		hide_title = attr_by_name( 'hide_title' );

	if( typeof val === 'undefined' ) {
		return;
	}

	if ( 'lazyload-lightbox' === val ) {
		align.$el.show();
		autoplay.$el.hide();
		grow.$el.hide();
		hide_title.$el.show();
	} else if ( 'lazyload' === val ) {
		align.$el.show();
		autoplay.$el.hide();
		grow.$el.show();
		hide_title.$el.show();
	} else if ( 'link-lightbox' === val ) {
		align.$el.hide();
		autoplay.$el.hide();
		grow.$el.hide();
		hide_title.$el.hide();
	} else { // normal
		align.$el.show();
		autoplay.$el.show();
		grow.$el.hide();
		hide_title.$el.hide();
	}
}

wp.shortcake.hooks.addAction( 'arve.mode', arve_mode_select_listener );
wp.shortcake.hooks.addAction( 'arve.url', arve_extract_url );
