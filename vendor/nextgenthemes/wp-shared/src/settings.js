import { store, getContext, getConfig, getElement } from '@wordpress/interactivity';
const domParser = new DOMParser();
const _ = window._;

const namespace = document.querySelector( '[data-wp-interactive^="nextgenthemes"]' )?.dataset
	?.wpInteractive;

if ( ! namespace ) {
	throw new Error( 'no namespace' );
}

// interface optionContext {
// 	section: string;
// 	option_key: string;
// 	edd_item_id: string;
// 	edd_action: string;
// 	edd_store_url: string;
// 	activeTabs: { [ key: string ]: boolean };
// }

// interface stateInterface {
// 	options: Record< string, string | number | boolean >;
// 	help: boolean;
// 	dialog: HTMLDialogElement;
// 	isSaving: boolean;
// 	message: string;
// 	shortcode: string;
// 	debug: string;
// }

const { state, actions, callbacks, helpers } = store( namespace, {
	state: {
		isValidLicenseKey: () => {
			const context = getContext();
			return 'valid' === state.options[ context.option_key + '_status' ];
		},
		is32charactersLong: () => {
			const context = getContext();
			return state.options[ context.option_key ].length === 32;
		},
		get isActiveSection() {
			const context = getContext();

			if ( ! context.activeTabs ) {
				return true; // shortcode dialog has no sections
			}

			return true === context?.activeTabs[ context.section ];
		},
	},
	actions: {
		toggleHelp: () => {
			state.help = ! state.help;
		},
		openShortcodeDialog: () => {
			state.dialog = document.querySelector(
				'dialog[data-wp-interactive="nextgenthemes_arve_dialog"]'
			);
			state.dialog.showModal();
		},
		insertShortcode: () => {
			window.wp.media.editor.insert( state.shortcode );
			state.dialog.close();
		},
		closeShortcodeDialog: () => {
			state.dialog.close();
		},
		changeTab: () => {
			const context = getContext();

			for ( const key in context.activeTabs ) {
				context.activeTabs[ key ] = false;
			}
			context.activeTabs[ context.section ] = true;
		},
		inputChange: ( event ) => {
			const context = getContext();

			if ( 'arveUrl' in event.target.dataset ) {
				helpers.extractFromEmbedCode( event.target.value );
			} else {
				state.options[ context.option_key ] = event.target.value;
			}

			if ( 'nextgenthemes_arve_dialog' !== namespace ) {
				actions.saveOptions();
			}
		},
		checkboxChange: ( event ) => {
			const context = getContext();
			state.options[ context.option_key ] = event.target.checked;

			if ( 'nextgenthemes_arve_dialog' !== namespace ) {
				actions.saveOptions();
			}
		},
		selectImage: () => {
			if ( state.dialog ) {
				state.dialog.close();
			}

			const context = getContext();
			const image = window.wp
				.media( {
					title: 'Upload Image',
					multiple: false,
				} )
				.open()
				.on( 'select', function () {
					// This will return the selected image from the Media Uploader, the result is an object
					const uploadedImage = image.state().get( 'selection' ).first();
					// We convert uploadedImage to a JSON object to make accessing it easier
					const attachmentID = uploadedImage.toJSON().id;
					state.options[ context.option_key ] = attachmentID;
					if ( state.dialog ) {
						state.dialog.showModal();
					}
				} )
				.on( 'close', function () {
					if ( state.dialog ) {
						state.dialog.showModal();
					}
				} );
		},
		deleteOembedCache: () => {
			actions.restCall( '/delete-oembed-cache', { delete: true } );
		},
		// debounced version created later
		saveOptionsReal: () => {
			actions.restCall( '/save', state.options );
		},
		restCall: ( restRoute, body, refreshAfter = false ) => {
			if ( state.isSaving ) {
				state.message = 'trying to save too fast';
				return;
			}
			const config = getConfig();

			// set the state so that another save cannot happen while processing
			state.isSaving = true;
			state.message = 'Saving...';

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( config.restUrl + restRoute, {
				method: 'POST',
				body: JSON.stringify( body ),
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': config.nonce,
				},
			} )
				.then( ( response ) => {
					if ( ! response.ok ) {
						throw new Error( 'Network response was not ok' );
					}
					return response.json();
				} )
				.then( ( message ) => {
					state.message = message;
					setTimeout( () => ( state.message = '' ), 666 );
				} )
				.catch( ( error ) => {
					state.message = error.message;
				} )
				.finally( () => {
					state.isSaving = false;

					if ( refreshAfter ) {
						window.location.reload();
					}
				} );
		},
		eddLicenseAction() {
			const context = getContext();

			actions.restCall(
				'/edd-license-action',
				{
					option_key: context.option_key,
					edd_store_url: context.edd_store_url, // EDD Store URL
					edd_action: context.edd_action, // edd api arg has same edd_ prefix
					item_id: context.edd_item_id, // edd api arg WITHOUT edd_ prefix
					license: state.options[ context.option_key ], // edd api arg WITHOUT edd_ prefix
				},
				true
			);
		},
		resetOptionsSection() {
			const config = getConfig();
			const context = getContext();
			const sectionToReset = context.section;

			Object.entries( config.defaultOptions ).forEach( ( [ section, options ] ) => {
				if ( 'all' === sectionToReset ) {
					// reset all
					Object.entries( options ).forEach( ( [ key, value ] ) => {
						state.options[ key ] = value;
					} );
				} else {
					Object.entries( options ).forEach( ( [ key, value ] ) => {
						if ( section === sectionToReset ) {
							state.options[ key ] = value;
						}
					} );
				}
			} );

			actions.saveOptionsReal();
		},
	},
	callbacks: {
		updateShortcode() {
			let out = '';

			for ( const [ key, value ] of Object.entries( state.options ) ) {
				if ( true === value ) {
					out += `${ key }="true" `;
				} else if ( value.length ) {
					out += `${ key }="${ value }" `;
				}
			}

			state.shortcode = '[arve ' + out + '/]';
		},
		updatePreview() {
			const url = new URL( 'https://symbiosistheme.test/wp-json/arve/v1/shortcode' );
			const params = new URLSearchParams();
			const options = getContext().options;
			const preview = document.getElementById( 'preview' );

			if ( ! preview ) {
				throw new Error( 'No preview element' );
			}

			for ( const [ key, value ] of Object.entries( options ) ) {
				if ( true === value ) {
					params.append( key, 'true' );
				} else if ( value.length ) {
					params.append( key, value );
				}
			}

			url.search = params.toString();

			fetch( url.href )
				.then( ( response ) => response.json() )
				.then( ( data ) => {
					preview.innerHTML = data.html;
				} )
				.catch( ( error ) => l( error ) );
		},
	},
	helpers: {
		debugJson: ( data ) => {
			state.debug = JSON.stringify( data, null, 2 );
		},
		extractFromEmbedCode: ( url ) => {
			const iframe = domParser.parseFromString( url, 'text/html' ).querySelector( 'iframe' );
			if ( iframe && iframe.getAttribute( 'src' ) ) {
				url = iframe.getAttribute( 'src' );

				if ( iframe.width && iframe.height ) {
					const ratio = aspectRatio( iframe.width, iframe.height );

					if ( '16:9' !== ratio ) {
						state.options.aspect_ratio = ratio;
					}
				}
			}
			state.options.url = url;
		},
	},
} );

actions.saveOptions = debounce( actions.saveOptionsReal, 1111 );

function debounce( func, wait, immediate ) {
	let timeout;
	return function () {
		const context = this;
		const args = arguments;
		const later = function () {
			timeout = null;
			if ( ! immediate ) {
				func.apply( context, args );
			}
		};
		const callNow = immediate && ! timeout;
		clearTimeout( timeout );
		timeout = setTimeout( later, wait );
		if ( callNow ) {
			func.apply( context, args );
		}
	};
}

/**
 * Calculate aspect ratio based on width and height.
 *
 * @param {string} width  - The width value
 * @param {string} height - The height value
 * @return {string} The aspect ratio in the format 'width:height'
 */
function aspectRatio( width, height ) {
	if ( isIntOverZero( width ) && isIntOverZero( height ) ) {
		const w = parseInt( width );
		const h = parseInt( height );
		const arGCD = gcd( w, h );

		return w / arGCD + ':' + h / arGCD;
	}

	return width + ':' + height;
}

/**
 * Checks if the input string represents a positive integer.
 *
 * @param {string} str - the input string to be checked
 * @return {boolean} true if the input string represents a positive integer, false otherwise
 */
function isIntOverZero( str ) {
	const n = Math.floor( Number( str ) );
	return n !== Infinity && String( n ) === str && n > 0;
}

/**
 * Calculate the greatest common divisor of two numbers using the Euclidean algorithm.
 *
 * @param {number} a - the first number
 * @param {number} b - the second number
 * @return {number} the greatest common divisor of the two input numbers
 */
function gcd( a, b ) {
	if ( ! b ) {
		return a;
	}

	return gcd( b, a % b );
}

function shortCodeUiExtractUrl( changed, collection ) {
	function attrByName( name ) {
		return _.find( collection, function ( viewModel ) {
			return name === viewModel.model.get( 'attr' );
		} );
	}

	const val = changed.value;

	if ( typeof val === 'undefined' ) {
		return;
	}

	const urlInput = attrByName( 'url' ).$el.find( 'input' );
	const aspectRatioInput = attrByName( 'aspect_ratio' ).$el.find( 'input' );

	const iframe = domParser.parseFromString( val, 'text/html' ).querySelector( 'iframe' );
	if ( iframe && iframe.getAttribute( 'src' ) ) {
		urlInput.val( iframe.src ).trigger( 'input' );

		if ( iframe.width && iframe.height ) {
			const ratio = aspectRatio( iframe.width, iframe.height );

			if ( '16:9' !== ratio ) {
				aspectRatioInput.val( ratio ).trigger( 'input' );
			}
		}
	}
}

window?.wp?.shortcake?.hooks?.addAction( 'arve.url', shortCodeUiExtractUrl );
