/* global Alpine, ArveShortcodeDialogJsBefore */

document.addEventListener( 'alpine:init', () => {
	const dialog = document.querySelector( '.arve-sc-dialog' );

	if ( ! ( dialog instanceof HTMLDialogElement ) ) {
		return;
	}

	const helpTexts = dialog.querySelectorAll( '.arve-sc-dialog__description' );
	const domParser = new DOMParser();

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

	// @ts-ignore
	Alpine.data( 'arvedialog', () => ( {
		// @ts-ignore
		options: ArveShortcodeDialogJsBefore.options,
		message: '',
		shortcode: '[arve url="" /]',
		updateShortcode() {
			let out = '';

			for ( const [ key, value ] of Object.entries( this.options ) ) {
				if ( 'url' === key ) {
					const iframe = domParser
						.parseFromString( value, 'text/html' )
						.querySelector( 'iframe' );
					if ( iframe && iframe.getAttribute( 'src' ) ) {
						this.options.url = iframe.getAttribute( 'src' );

						if ( iframe.width && iframe.height ) {
							const ratio = aspectRatio( iframe.width, iframe.height );

							if ( '16:9' !== ratio ) {
								this.options.aspect_ratio = ratio;
							}
						}
						return;
					}
				}

				if ( true === value ) {
					out += `${ key }="true" `;
				}

				if ( value.length ) {
					out += `${ key }="${ value }" `;
				}
			}

			this.shortcode = '[arve ' + out + '/]';
		},
		uploadImage( optionKey ) {
			dialog.close();

			const alpineThis = this;
			const image = window.wp
				.media( {
					title: 'Upload Image',
					multiple: false,
				} )
				.open()
				.on( 'select', function () {
					dialog.showModal();

					// This will return the selected image from the Media Uploader, the result is an object
					const uploadedImage = image.state().get( 'selection' ).first();
					// We convert uploadedImage to a JSON object to make accessing it easier
					const attachmentID = uploadedImage.toJSON().id;
					alpineThis.options[ optionKey ] = attachmentID;
				} );
		},
		toggleHelpTexts() {
			helpTexts?.forEach( ( el ) => {
				el.toggleAttribute( 'hidden' );
			} );
		},
	} ) );
} );
