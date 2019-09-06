// License: GPLv2+

const el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
	TextControl = wp.components.TextControl,
	ToggleControl = wp.components.ToggleControl,
	InspectorControls = wp.editor.InspectorControls;

/*
 * Here's where we register the block in JavaScript.
 *
 * It's not yet possible to register a block entirely without JavaScript, but
 * that is something I'd love to see happen. This is a barebones example
 * of registering the block, and giving the basic ability to edit the block
 * attributes. (In this case, there's only one attribute, 'foo'.)
 */
registerBlockType( 'nextgenthemes/arve-block', {
	title: 'PHP Block',
	icon: 'megaphone',
	category: 'widgets',

	/*
	 * In most other blocks, you'd see an 'attributes' property being defined here.
	 * We've defined attributes in the PHP, that information is automatically sent
	 * to the block editor, so we don't need to redefine it here.
	 */

	edit: ( props ) => {
		return [
			/*
			 * The ServerSideRender element uses the REST API to automatically call
			 * php_block_render() in your PHP code whenever it needs to get an updated
			 * view of the block.
			 */
			el( ServerSideRender, {
				block: 'nextgenthemes/arve-block',
				attributes: props.attributes,
			} ),
			/*
			 * InspectorControls lets you add controls to the Block sidebar. In this case,
			 * we're adding a TextControl, which lets us edit the 'foo' attribute (which
			 * we defined in the PHP). The onChange property is a little bit of magic to tell
			 * the block editor to update the value of our 'foo' property, and to re-render
			 * the block.
			 */
			el( InspectorControls, {},
				el(
					TextControl,
					{
						label: 'Foo',
						value: props.attributes.foo,
						onChange: ( value ) => {
							props.setAttributes( { foo: value } );
						},
					}
				),
				el(
					ToggleControl,
					{
						label: 'Toogle',
						value: props.attributes.toggle,
						onChange: ( value ) => {
							props.setAttributes( { toggle: value } );
						},
					}
				),
			),
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: () => {
		return null;
	},
} );
