// License: GPLv2+
//console.log( ARVEsettings );
( function() {

	var el = wp.element.createElement;

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
			var cArgs = {
				label: opt.label,

				//help: opt.description,
				onChange: ( value ) => {
					props.setAttributes({ [key]: value });
				}
			};

			if ( 'bool+default' === opt.type ) {
				console.log( opt.options );
				opt.type = 'select';
			}

			switch ( opt.type ) {

				case 'boolean':
					cArgs.onChange = ( value ) => {
						props.setAttributes({ [key]: value });
					};

					controls.push( el( wp.components.CheckboxControl, cArgs ) );
					break;

				case 'select':
					cArgs.options  = PrepareOptions( opt.options );
					cArgs.selected = props.attributes[key];

					controls.push( el( wp.components.SelectControl, cArgs ) );
					break;

				case 'string':
					cArgs.value = props.attributes[key];

					controls.push( el( wp.components.TextControl, cArgs ) );
					break;
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
	wp.blocks.registerBlockType( 'nextgenthemes/arve-block', {
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
				el( wp.components.ServerSideRender, {
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
				el( wp.editor.InspectorControls, {}, ...controls )
			];
		},

		// We're going to be rendering in PHP, so save() can just return null.
		save: function() {
			return null;
		}
	});

}() );
