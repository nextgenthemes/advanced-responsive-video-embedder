import { store, getContext, getConfig, getElement } from '@wordpress/interactivity';
const l = console.log; // eslint-disable-line
const domParser = new DOMParser();

const namespace = document.querySelector( '[data-wp-interactive^="nextgenthemes"]' )?.dataset
	?.wpInteractive;

if ( ! namespace ) {
	alert( 'no namespace' );
}

function debounce( func, wait ) {
	let timeout;

	return function executedFunction( ...args ) {
		const later = () => {
			clearTimeout( timeout );
			func( ...args );
		};

		clearTimeout( timeout );
		timeout = setTimeout( later, wait );
		l( 'inside debounce' );
	};
}

const { state, actions, callbacks, helpers } = store( namespace, {
	actions: {
		changeTab: () => {
			const context = getContext();
			l( 'change tab context', context );

			for ( const key in context.activeTabs ) {
				context.activeTabs[ key ] = false;
			}

			context.activeTabs[ context.section ] = true;
		},
		doShit: () => {
			state.options.description = 'desc';
			state.options.title = 'title';
			state.options.controls = 'true';
			state.options.align = 'left';
			state.options.autoplay = 'true';
			state.options.loop = 'true';
			state.options.maxWidth = '444';
		},
		inputChange: ( event ) => {
			const context = getContext();
			if ( 'arveUrl' in event.target.dataset ) {
				helpers.extractFromEmbedCode( state, event.target.value );
			} else {
				state.options[ context.key ] = event.target.value;
			}

			debounce( helpers.saveOptions(), 5000 );
		},
		checkboxChange: ( event ) => {
			const context = getContext();
			state.options[ context.key ] = event.target.checked;
			debounce( helpers.saveOptions(), 5000 );
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

		eddLicenseAction: () => {
			const context = getContext();
			const url = new URL( 'https://nextgenthemes.com' ); // Site with Software Licensing activated.
			const urlParams = new URLSearchParams( {
				edd_action: context.eddAction,
				license: state.options[ context.key ], // License key
				item_id: context.eddProductId, // Product ID
				url: context.eddAction, // Domain the request is coming from.
			} );
			url.search = urlParams.toString();
			fetch( url.toString() )
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
					// Error handling. rrrrrrrrrrrrrrrrrrrrrrrrrrrrrr
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
		debugJson( data ) {
			state.debug = JSON.stringify( data, null, 2 );
		},
		saveOptions() {
			l( 'save options' );

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
					state.refreshAfterSave = false;
				} )
				.finally( () => {
					state.isSaving = false;

					if ( state.refreshAfterSave ) {
						state.refreshAfterSave = false;
						window.location.reload();
					}
				} );
		},
		extractFromEmbedCode( context, url ) {
			const iframe = domParser.parseFromString( url, 'text/html' ).querySelector( 'iframe' );
			if ( iframe && iframe.getAttribute( 'src' ) ) {
				url = iframe.getAttribute( 'src' );

				if ( iframe.width && iframe.height ) {
					const ratio = aspectRatio( iframe.width, iframe.height );

					if ( '16:9' !== ratio ) {
						context.options.aspect_ratio = ratio;
					}
				}
			}
			context.options.url = url;
		},
	},
} );

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
