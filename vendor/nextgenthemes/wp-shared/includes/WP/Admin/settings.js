import { store, getContext, getConfig, getElement } from '@wordpress/interactivity';
const l = console.log; // eslint-disable-line
const domParser = new DOMParser();

const namespace = document.querySelector( '[data-wp-interactive^="nextgenthemes"]' )?.dataset
	?.wpInteractive;

if ( ! namespace ) {
	console.log( 'no namespace' ); // eslint-disable-line
}

const { state, actions, callbacks, helpers } = store( namespace, {
	state: {
		isValidLicenseKey: () => {
			const context = getContext();
			return 'valid' === state.options[ context.key + '_status' ];
		},
		isLongEnoughLicenseKey: () => {
			l( state.options );
			helpers.debugJson( state.options );

			const context = getContext();
			return state.options[ context.key ].length >= 32;
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
			context.help = ! context.help;
		},
		openShortcodeDialog: () => {
			state.dialog = document.querySelector(
				'dialog[data-wp-interactive="nextgenthemes_arve_dialog"]'
			);
			l( 'dialog', state.dialog );

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
			l( 'inputChange' );

			const context = getContext();

			if ( 'arveUrl' in event.target.dataset ) {
				helpers.extractFromEmbedCode( context, event.target.value );
			} else {
				state.options[ context.key ] = event.target.value;
			}

			if ( 'nextgenthemes_arve_dialog' !== namespace ) {
				actions.saveOptions();
			}
		},
		checkboxChange: ( event ) => {
			l( 'checkboxChange' );

			const context = getContext();
			state.options[ context.key ] = event.target.checked;

			if ( 'nextgenthemes_arve_dialog' !== namespace ) {
				actions.saveOptions();
			}
		},
		selectImage: () => {
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
					state.options[ context.key ] = attachmentID;
				} );
		},
		deleteOembedCache: () => {
			if ( state.isSaving ) {
				state.message = 'too fast';
				return;
			}

			const config = getConfig();

			helpers.debugJson( config );

			// set the state so that another save cannot happen while processing
			state.isSaving = true;
			state.message = '...';

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( config.restUrl + '/delete-oembed-cache', {
				method: 'POST',
				body: JSON.stringify( { delete: true } ),
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
					setTimeout( () => ( state.message = '' ), 3000 );
				} )
				.catch( ( error ) => {
					state.message = error.message;
				} )
				.finally( () => {
					state.isSaving = false;
				} );
		},
		// debounced version created later
		saveOptionsReal: () => {
			l( 'saveOptionsReal' );

			if ( state.isSaving ) {
				state.message = 'trying to save too fast';
				return;
			}

			// set the state so that another save cannot happen while processing
			state.isSaving = true;
			state.message = 'Saving...';

			const config = getConfig();

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( config.restUrl + '/save', {
				method: 'POST',
				body: JSON.stringify( state.options ),
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
				.then( () => {
					state.message = 'Options saved';
					setTimeout( () => ( state.message = '' ), 2500 );
				} )
				.catch( ( error ) => {
					state.message = error.message;
				} )
				.finally( () => {
					state.isSaving = false;
				} );
		},
		restCall: ( restRoute, body ) => {
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
					setTimeout( () => ( state.message = '' ), 2500 );
				} )
				.catch( ( error ) => {
					state.message = error.message;
				} )
				.finally( () => {
					state.isSaving = false;
				} );
		},
		eddLicenseAction() {
			if ( state.isSaving ) {
				state.message = 'trying to save too fast';
				return;
			}

			// set the state so that another save cannot happen while processing
			state.isSaving = true;
			state.message = 'Saving...';

			const config = getConfig();
			const context = getContext();

			const bodyToStringify = {
				option_key: context.key,
				edd_store_url: context.edd_store_url, // EDD Store URL
				edd_action: context.edd_action, // edd api arg has same edd_ prefix
				item_id: context.edd_item_id, // edd api arg WITHOUT edd_ prefix
				license: state.options[ context.key ], // edd api arg WITHOUT edd_ prefix
			};

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( config.restUrl + '/edd-license-action', {
				method: 'POST',
				body: JSON.stringify( bodyToStringify ),
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
				.then( () => {
					state.message = 'Options saved';
					setTimeout( () => ( state.message = '' ), 1000 );
				} )
				.catch( ( error ) => {
					state.message = error.message;
				} )
				.finally( () => {
					state.isSaving = false;
					window.location.reload();
				} );
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
		saveOptions: () => {
			l( 'saveOptionsReal' );

			if ( state.isSaving ) {
				state.message = 'trying to save too fast';
				return;
			}

			// set the state so that another save cannot happen while processing
			state.isSaving = true;
			state.message = 'Saving...';

			const config = getConfig();
			const context = getContext();

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( config.restUrl + '/save', {
				method: 'POST',
				body: JSON.stringify( state.options ),
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
				.then( () => {
					state.message = 'Options saved';
					// setTimeout( () => ( state.message = '' ), 2500 );
					//state.isSaving = false;
				} )
				.catch( ( error ) => {
					state.message = error.message;
				} );
			// .finally( () => {
			// 	state.isSaving = false;
			// } );
		},
		updateShortcode() {
			const context = getContext();
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

			for ( const [ key, value ] of Object.entries( options ) ) {
				if ( true === value ) {
					params.append( key, 'true' );
				} else if ( value.length ) {
					params.append( key, value );
				}
			}

			url.search = params.toString();

			l( url.href );

			fetch( url.href )
				.then( ( response ) => response.json() )
				.then( ( data ) => {
					l( data );
					preview.innerHTML = data.html;
				} )
				.catch( ( error ) => l( error ) );
		},
	},
	helpers: {
		debugJson( data ) {
			state.debug = JSON.stringify( data, null, 2 );
		},
	},
} );

actions.saveOptions = debounce( actions.saveOptionsReal, 1111 );

function debounce( func, wait, immediate ) {
	l( 'debounce' );

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

function extractFromEmbedCode( context, url ) {
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
