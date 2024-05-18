import { store, getContext, getConfig, getElement } from '@wordpress/interactivity';
const l = console.log; // eslint-disable-line
const domParser = new DOMParser();

const url = new URL( window.location.href );
let namespace = url.searchParams.get( 'page' );
if ( ! namespace ) {
	namespace = 'arveRestFrontend';
}

l( 'namespace', namespace );

const { state, callbacks, helpers } = store( namespace, {
	state: {
		get isActiveTab() {
			const context = getContext();
			return state.activeTab[ context.section ];
		},
		get isPremiumSection() {
			const config = getConfig();
			const el = getElement();

			const isPremium = Object.values( config.premiumSections ).includes(
				el.attributes[ 'data-section' ]
			);

			return isPremium;
		},
	},
	actions: {
		changeTab: ( event ) => {
			const section = event.target.dataset.section;

			for ( const key in state.activeTabs ) {
				state.activeTabs[ key ] = false;
			}

			state.activeTabs[ section ] = true;
		},
		doShit: () => {
			const context = getContext();
			context.options.description = 'desc';
			context.options.title = 'title';
			context.options.controls = 'true';
			context.options.align = 'left';
			context.options.autoplay = 'true';
			context.options.loop = 'true';
			context.options.maxWidth = '444';

			l( state );
		},
		inputChange: ( event ) => {
			const context = getContext();
			const optionKey = helpers.getOptionKey( event );
			if ( 'arveUrl' in event.target.dataset ) {
				helpers.extractFromEmbedCode( context, event.target.value );
			} else {
				context.options[ optionKey ] = event.target.value;
			}
		},
		checkboxChange: ( event ) => {
			const context = getContext();
			const optionKey = helpers.getOptionKey( event );
			context.options[ optionKey ] = event.target.checked;
		},
	},
	callbacks: {
		update() {
			callbacks.updateShortcode();
			//callbacks.updatePreview();
		},
		init() {
			l( 'init fn log state', state );
		},
		updateShortcode() {
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
		isPremium() {
			const config = getConfig();

			return config.premium;
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
		getOptionKey( event ) {
			return event.target.id.replace( 'ngt_opt__', '' );
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

/* global Alpine */
const url = new URL( window.location.href );
const pageQueryVal = url.searchParams.get( 'page' );
const { log } = console;
if ( ! pageQueryVal ) {
	throw 'Need page url arg';
}

const data = window[ pageQueryVal ];

document.addEventListener( 'alpine:init', () => {
	Alpine.data( 'ngtsettings', () => ( {
		options: data.options,
		message: '',
		isSaving: false,
		saveOptions( refreshAfterSave = false ) {
			if ( this.isSaving ) {
				this.message = 'trying to save too fast';
				return;
			}

			// set the state so that another save cannot happen while processing
			this.isSaving = true;
			this.message = 'Saving...';

			// Make a POST request to the REST API route that we registered in our PHP file
			fetch( data.restUrl + '/save', {
				method: 'POST',
				body: JSON.stringify( this.options ),
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
				.then( () => {
					this.message = 'Options saved';
					setTimeout( () => ( this.message = '' ), 1000 );
				} )
				.catch( ( error ) => {
					this.message = error.message;
					refreshAfterSave = false;
				} )
				.finally( () => {
					this.isSaving = false;

					if ( refreshAfterSave ) {
						refreshAfterSave = false;
						window.location.reload();
					}
				} );
		},
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
		uploadImage( optionKey ) {
			const alpineThis = this;
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
					alpineThis.options[ optionKey ] = attachmentID;
				} );
		},
		licenseKeyAction( action, product ) {
			this.options.action = JSON.stringify( { action, product } );
			this.saveOptions( true );
		},
	} ) );
} );
