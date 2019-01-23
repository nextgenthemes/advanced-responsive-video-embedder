// License: GPLv2+
//console.log( ARVEsettings );
( function() {

	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		ServerSideRender = wp.components.ServerSideRender,
		TextControl = wp.components.TextControl,
		RadioControl = wp.components.RadioControl,
		SelectControl = wp.components.SelectControl,
		InspectorControls = wp.editor.InspectorControls;

	/*
	 * Keypair to gutenberg component
	 */
	function PrepareOptions( options ) {

		var gboptions = [];

		Object.keys( options ).forEach( function( key ) {
			gboptions.push({
				label: options[key],
				value: key
			});
		});

		return gboptions;
	}

	function BuildControls( props ) {

		var controls = [];

		Object.keys( ARVEsettings ).forEach( function( key ) {

			var opt = ARVEsettings[key];

			if ( 'select' === opt.type ) {
				controls.push( el( SelectControl, {
					label: opt.label,
					selected: props.attributes[key],
					options: PrepareOptions( opt.options ),
					onChange: ( value ) => {
						props.setAttributes({ [key]: value });
					}
				}) );
			} else if ( 'string' === opt.type ) {
				controls.push( el( TextControl, {
					label: opt.label,
					value: props.attributes[key],
					onChange: ( value ) => {
						props.setAttributes({ [key]: value });
					}
				}) );
			} else if ( 'boolean' === opt.type ) {
				controls.push( el( SelectControl, {
					label: opt.label,
					selected: props.attributes[key],
					options: [
						{ label: 'Default', value: null },
						{ label: 'Yes', value: 'y' },
						{ label: 'No', value: 'n' }
					],
					onChange: ( value ) => {
						props.setAttributes({ [key]: value });
					}
				}) );
			}
		});

		return controls;
	}

	/*
	 * Here's where we register the block in JavaScript.
	 *
	 * It's not yet possible to register a block entirely without JavaScript, but
	 * that is something I'd love to see happen. This is a barebones example
	 * of registering the block, and giving the basic ability to edit the block
	 * attributes. (In this case, there's only one attribute, 'foo'.)
	 */
	registerBlockType( 'nextgenthemes/arve-block', {
		title: 'Video Embed (ARVE)',
		icon: 'video-alt3',
		category: 'embed',

		/*
		 * In most other blocks, you'd see an 'attributes' property being defined here.
		 * We've defined attributes in the PHP, that information is automatically sent
		 * to the block editor, so we don't need to redefine it here.
		 */

		edit: function( props ) {

			var controls = BuildControls( props );

			return [

				/*
				 * The ServerSideRender element uses the REST API to automatically call
				 * php_block_render() in your PHP code whenever it needs to get an updated
				 * view of the block.
				 */
				el( ServerSideRender, {
					block: 'nextgenthemes/arve-block',
					attributes: props.attributes
				}),

				/*
				 * InspectorControls lets you add controls to the Block sidebar. In this case,
				 * we're adding a TextControl, which lets us edit the 'foo' attribute (which
				 * we defined in the PHP). The onChange property is a little bit of magic to tell
				 * the block editor to update the value of our 'foo' property, and to re-render
				 * the block.
				 */
				el( InspectorControls, {}, ...controls )
			];
		},

		// We're going to be rendering in PHP, so save() can just return null.
		save: function() {
			return null;
		}
	});

}() );
