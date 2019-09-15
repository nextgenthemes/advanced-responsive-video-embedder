/**
 * Copyright (c) 2019 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright (c) 2019 Gary Pendergast
 * License: GPL 2.0+
 */

const wp = window.wp;
const el = window.wp.element.createElement;

wp.data.dispatch( 'core/edit-post' ).hideBlockTypes( [
	'core-embed/youtube',
	'core-embed/vimeo',
	'core-embed/dailymotion',
	'core-embed/collegehumor',
	'core-embed/ted',
] );

/*
 * Keypair to gutenberg component
 */
function PrepareSelectOptions( options ) {
	const gboptions = [];

	Object.keys( options ).forEach( ( key ) => {
		gboptions.push( {
			label: options[ key ],
			value: key,
		} );
	} );

	return gboptions;
}

function BuildControls( props ) {

	const controls  = [];
	const domParser = new DOMParser();

	Object.keys( window.ARVEsettings ).forEach( ( key ) => {
		const option   = window.ARVEsettings[ key ];
		const attrVal  = props.attributes[ key ];
		const ctrlArgs = {
			label: option.label,
			help: option.description,
			onChange: ( value ) => {
				if ( 'url' === key ) {
					const $iframe = domParser.parseFromString( value, 'text/html' ).querySelector( 'iframe' );
					if ( $iframe &&
						$iframe.hasAttribute( 'src' ) &&
						$iframe.getAttribute( 'src' )
					) {
						value   = $iframe.src;
						const w = $iframe.width;
						const h = $iframe.height;
						if ( w && h ) {
							props.setAttributes( { aspect_ratio: aspectRatio( w, h ) } );
						}
					}
				}
				props.setAttributes( { [ key ]: value } );
			},
		};

		if ( 'bool+default' === option.type ) {
			option.type = 'select';
		}

		switch ( option.type ) {
			case 'boolean':
				if ( typeof attrVal !== 'undefined' ) {
					ctrlArgs.checked = attrVal;
				}
				controls.push( el( wp.components.ToggleControl, ctrlArgs ) );
				break;
			case 'select':
				if ( typeof attrVal !== 'undefined' ) {
					ctrlArgs.selected = attrVal;
					ctrlArgs.value = attrVal;
				}
				ctrlArgs.options = PrepareSelectOptions( option.options );
				controls.push( el( wp.components.SelectControl, ctrlArgs ) );
				break;
			case 'string':
				if ( typeof attrVal !== 'undefined' ) {
					ctrlArgs.value = attrVal;
				}
				controls.push( el( wp.components.TextControl, ctrlArgs ) );
				break;
		}
	} );

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

	edit: ( props ) => {
		const controls = BuildControls( props );

		return [

			/*
			 * The ServerSideRender element uses the REST API to automatically call
			 * php_block_render() in your PHP code whenever it needs to get an updated
			 * view of the block.
			 */
			el( wp.components.ServerSideRender, {
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
			el( wp.blockEditor.InspectorControls, {}, ...controls ),
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: () => {
		return null;
	},
} );

function aspectRatio( w, h ) {

	const arGCD = gcd( w, h );

	return ( w / arGCD ) + ':' +  ( h / arGCD );
}

function gcd( a, b ) {

	if ( ! b ) {
		return a;
	}

	return gcd( b, a % b );
}
