/* global ArveShortcodeDialogJsBefore */

console.log( window.ArveShortcodeDialogJsBefore );

document.addEventListener( 'alpine:init', () => {
	Alpine.data( 'arvedialog', () => ( {
		options: JSON.parse( window.ArveShortcodeDialogJsBefore.options ),
		message: '',
		shortcode: '[arve url="" /]',
		updateShortcode() {
			let out = '';

			for ( const [ key, value ] of Object.entries( this.options ) ) {
				if ( value.length ) {
					out += `${ key }="${ value }" `;
				}
			}

			this.shortcode = '[arve ' + out + '/]';
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
	} ) );
} );
