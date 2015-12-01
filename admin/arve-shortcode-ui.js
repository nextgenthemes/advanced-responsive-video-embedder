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

	var updatedVal = changed.value,
		thumbnail = attributeByName( 'thumbnail' ),
		grow = attributeByName( 'grow' );

	if( typeof updatedVal === 'undefined' ) {
		return;
	}

	if ( '' === updatedVal || updatedVal.match('^lazyload') ) {
		thumbnail.$el.show();
		grow.$el.show();
	} else {
		thumbnail.$el.hide();
		grow.$el.hide();
	}
}

wp.shortcake.hooks.addAction( 'arve.mode', updateSelectFieldListener );
