import { store, getContext, getConfig, getElement } from '@wordpress/interactivity';
const l = console.log; // eslint-disable-line
const domParser = new DOMParser();

const namespace = document.querySelector( '[data-wp-interactive^="nextgenthemes"]' )?.dataset
	?.wpInteractive;

if ( ! namespace ) {
	alert( 'no namespace' );
}

const { state, actions, callbacks, helpers } = store( namespace, {
	state: {
		isValidLicenseKey: () => {
			const context = getContext();
			return 'valid' === state.options[ context.key + '_status' ];
		},
		isLongEnoughLicenseKey: () => {
			const context = getContext();
			return state.options[ context.key ].length >= 32;
		},
	},
	actions: {
		changeTab: () => {
			const context = getContext();

			for ( const key in context.activeTabs ) {
				context.activeTabs[ key ] = false;
			}
			context.activeTabs[ context.section ] = true;
		},
		inputChange: ( event ) => {
			const context = getContext();
			l( 'inputChange context, event', context, event );

			if ( 'arveUrl' in event.target.dataset ) {
				helpers.extractFromEmbedCode( event.target.value );
			} else {
				state.options[ context.key ] = event.target.value;
			}
			actions.saveOptions();
		},
		checkboxChange: ( event ) => {
			const context = getContext();
			state.options[ context.key ] = event.target.checked;
			actions.saveOptions();
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

			// set the state so that another save cannot happen while processing
			state.isSaving = true;
			state.message = '...';

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( config.restUrl + '/delete-oembed-cache', {
				method: 'POST',
				body: JSON.stringify( { delete: true } ),
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': state.nonce,
				},
			} )
				.then( ( response ) => {
					if ( ! response.ok ) {
						l( response );
						helpers.debugJson( response );
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
		saveOptionsReal() {
			l( 'save options real' );

			helpers.debugJson( state.options );

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
					setTimeout( () => ( state.message = '' ), 1000 );
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

			helpers.debugJson( bodyToStringify );

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
					//window.location.reload();
				} );
		},
		eddLicenseActionGet: () => {
			const context = getContext();
			const config = getConfig();
			const url = new URL( context.eddStoreUrl ); // Site with EDD Software Licensing activated.

			const urlParams = new URLSearchParams( {
				edd_action: context.eddAction,
				license: state.options[ context.key ], // License key
				item_id: context.eddProductId, // Product ID
				url: config.homeUrl, // Domain the request is coming from.
			} );

			url.search = urlParams.toString();

			helpers.debugJson( url.toString() );

			fetch( url.toString(), {
				mode: 'no-cors',
			} )
				.then( ( response ) => {
					l( 'response', response );
					if ( response.ok ) {
						return response.json();
					}
					return Promise.reject( response );
				} )
				.then( ( data ) => {
					// Software Licensing has a valid response to parse
					console.log( 'Successful response', data );
				} )
				.catch( ( error ) => {
					// Error handling.
					console.log( 'Error', error );
				} );
		},
		eddLicenseActionPost: () => {
			const context = getContext();
			const config = getConfig();

			const formData = new FormData();
			formData.append( 'edd_action', context.eddAction ); // Valid actions are activate_license, deactivate_license, get_version, check_license
			formData.append( 'license', state.options[ context.key ] ); // License key
			formData.append( 'item_id', context.eddProductId ); // Product ID
			formData.append( 'url', config.homeUrl ); // If you disable URL checking you do not need this entry.

			// Site with Software Licensing activated.
			fetch( context.eddStoreUrl, {
				mode: 'no-cors',
				method: 'POST',
				body: formData,
			} )
				.then( ( response ) => {
					if ( response.ok ) {
						return response.json();
					}
					return Promise.reject( response );
				} )
				.then( ( data ) => {
					// Software Licensing has a valid response to parse
					console.log( 'Successful response', data );
				} )
				.catch( ( error ) => {
					// Error handling.
					console.log( 'Error', error );
				} );
		},
	},
	callbacks: {
		update() {
			callbacks.updateShortcode();
			//callbacks.updatePreview();
		},
		init() {
			const context = getContext();
			l( 'init fn log context', context );
		},
		updateShortcode() {
			l( 'update shortcode', 'options', getContext().options );

			const context = getContext();

			let out = '';

			for ( const [ key, value ] of Object.entries( context.options ) ) {
				if ( true === value ) {
					out += `${ key }="true" `;
				} else if ( value.length ) {
					out += `${ key }="${ value }" `;
				}
			}

			context.shortcode = '[arve ' + out + '/]';
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
		debounce( func, wait, immediate ) {
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
		},
		debugJson( data ) {
			state.debug = JSON.stringify( data, null, 2 );
		},
		extractFromEmbedCode( url ) {
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

actions.saveOptions = helpers.debounce( actions.saveOptionsReal, 250 );

/**
 * Calculate aspect ratio based on width and height.
 *
 * @param {string} width  - The width value
 * @param {string} height - The height value
 * @return {string} The aspect ratio in the format 'width:height'
 */
const aspectRatio = ( width, height ) => {
	if ( isIntOverZero( width ) && isIntOverZero( height ) ) {
		const w = parseInt( width );
		const h = parseInt( height );
		const arGCD = gcd( w, h );

		return w / arGCD + ':' + h / arGCD;
	}

	return width + ':' + height;
};

/**
 * Checks if the input string represents a positive integer.
 *
 * @param {string} str - the input string to be checked
 * @return {boolean} true if the input string represents a positive integer, false otherwise
 */
const isIntOverZero = ( str ) => {
	const n = Math.floor( Number( str ) );
	return n !== Infinity && String( n ) === str && n > 0;
};

/**
 * Calculate the greatest common divisor of two numbers using the Euclidean algorithm.
 *
 * @param {number} a - the first number
 * @param {number} b - the second number
 * @return {number} the greatest common divisor of the two input numbers
 */
const gcd = ( a, b ) => {
	if ( ! b ) {
		return a;
	}

	return gcd( b, a % b );
};

document.addEventListener( 'alpine:init', () => {
	Alpine.data( 'ngtsettings', () => ( {
		options: data.options,
		message: '',
		isSaving: false,
		deleteOembedCache() {
			if ( this.isSaving ) {
				this.message = 'too fast';
				return;
			}

			// set the state so that another save cannot happen while processing
			this.isSaving = true;
			this.message = '...';

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( data.restUrl + '/delete-oembed-cache', {
				method: 'POST',
				body: JSON.stringify( { delete: true } ),
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': data.nonce,
				},
			} )
				.then( ( response ) => {
					if ( ! response.ok ) {
						throw new Error( 'Network response was not ok' );
					}
					return response.json();
				} )
				.then( ( message ) => {
					this.message = message;
					setTimeout( () => ( this.message = '' ), 3000 );
				} )
				.catch( ( error ) => {
					this.message = error.message;
				} )
				.finally( () => {
					this.isSaving = false;
				} );
		},
		licenseKeyAction( action, product ) {
			this.options.action = JSON.stringify( { action, product } );
			this.saveOptions( true );
		},
	} ) );
} );
