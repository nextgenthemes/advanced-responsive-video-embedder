import Vue from 'vue';
/* global jQuery */

const url          = new URL( window.location.href );
const pageQueryVal = url.searchParams.get( 'page' );
const data         = window[ pageQueryVal ];

new Vue( {

	// DOM selector for our app's main wrapper element
	el: '#nextgenthemes-vue',

	// Data that will be proxied by Vue.js to provide reactivity to our template
	data: {
		isSaving: false,
		sectionsDisplayed: {
			main: true,
			html5: true,
			pro: false,
			debug: false,
			urlparams: false,
		},
		message: '',
		vm: data.options,
		tabs: {
			one: true,
			two: false,
		},
	},

	// Methods that can be invoked from within our template
	methods: {

		// Save the options to the database
		saveOptions() {
			// set the state so that another save cannot happen while processing
			this.isSaving = true;

			// Make a POST request to the REST API route that we registered in our PHP file
			jQuery.ajax( {
				url: data.rest_url + '/save',
				method: 'POST',
				data: this.vm,

				// set the nonce in the request header
				beforeSend( request ) {
					request.setRequestHeader( 'X-WP-Nonce', data.nonce );
				},

				// callback to run upon successful completion of our request
				success: () => {
					this.message = 'Options saved';
					setTimeout( () => this.message = '', 1000 );
				},

				// callback to run if our request caused an error
				error: ( errorData ) => this.message = errorData.responseText,

				// when our request is complete (successful or not), reset the state to indicate we are no longer saving
				complete: () => this.isSaving = false,
			} );
		}, // end: saveOptions
		showSection( section ) {
			setAllObjValues( this.sectionsDisplayed, false );
			this.sectionsDisplayed[ section ] = true;
		},
		showAllSectionsButDebug() {
			setAllObjValues( this.sectionsDisplayed, true );
			this.sectionsDisplayed.debug = false;
		},
	}, // end: methods
} ); // end: Vue()

function setAllObjValues( obj, val ) {

	Object.keys( obj ).forEach( ( index ) => {
		obj[ index ] = val;
	} );
}

jQuery( document ).on( 'click', '[data-attachment-upload]', function( e ) {
	const target = jQuery( this ).attr( 'data-attachment-upload' );
	const image  = wp.media( {
		title: 'Upload Image',

		// mutiple: true if you want to upload multiple files at once
		multiple: false,
	} ).open()
		.on( 'select', function() {
		// This will return the selected image from the Media Uploader, the result is an object
			const uploadedImage = image.state().get( 'selection' ).first();

			// We convert uploadedImage to a JSON object to make accessing it easier
			// Output to the console uploadedImage
			const attachmentID = uploadedImage.toJSON().id;

			// Let's assign the url value to the input field
			jQuery( target ).val( attachmentID );
		} );
	e.preventDefault();
} );
