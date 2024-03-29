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
