/* global Alpine, ArveShortcodeDialogJsBefore */

document.addEventListener( 'alpine:init', () => {
	const dialog = document.querySelector( '.arve-sc-dialog' );
	const helpTexts = dialog?.querySelectorAll( '.arve-sc-dialog__description' );

	if ( ! dialog ) {
		return;
	}

	// @ts-ignore
	Alpine.data( 'arvedialog', () => ( {
		// @ts-ignore
		options: ArveShortcodeDialogJsBefore.options,
		message: '',
		shortcode: '[arve url="" /]',
		updateShortcode() {
			let out = '';

			for ( const [ key, value ] of Object.entries( this.options ) ) {
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
