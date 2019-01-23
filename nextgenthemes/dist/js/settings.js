/**
 * The main Vue instance for our plugin settings page
 * @link https://vuejs.org/v2/guide/instance.html
 */
/* global Vue, data */

( function() {

	var page_query_arg = GetUrlParam( 'page' );
	var data           = window[ page_query_arg ];

	function GetUrlParam( name, url ) {
		var regex, results;

		if ( ! url ) {
			url = window.location.href;
		}
		name    = name.replace( /[\[\]]/g, '\\$&' );
		regex   = new RegExp( '[?&]' + name + '(=([^&#]*)|&|#|$)' ),
		results = regex.exec( url );

		if ( ! results ) {
			return null;
		}

		if ( ! results[2]) {
			return '';
		}

		return decodeURIComponent( results[2].replace( /\+/g, ' ' ) );
	}

	new Vue({

		// DOM selector for our app's main wrapper element
		el: '#nextgenthemes-vue',

		// Data that will be proxied by Vue.js to provide reactivity to our template
		data: {
			isSaving: false,
			showPro: false,
			showMain: true,
			showDebug: false,
			message: '',
			vm: data.options,
		},

		// Methods that can be invoked from within our template
		methods: {

			// Save the options to the database
			saveOptions: function() {

				// set the state so that another save cannot happen while processing
				this.isSaving = true;

				// Make a POST request to the REST API route that we registered in our PHP file
				jQuery.ajax({
					url: data.rest_url + '/save',
					method: 'POST',
					data: this.vm,

					// set the nonce in the request header
					beforeSend: function( request ) {
						request.setRequestHeader( 'X-WP-Nonce', data.nonce );
					},

					// callback to run upon successful completion of our request
					success: () => {
						this.message = 'Options saved';
						setTimeout( () => this.message = '', 1000 );
					},

					// callback to run if our request caused an error
					error: ( data ) => this.message = data.responseText,

					// when our request is complete (successful or not), reset the state to indicate we are no longer saving
					complete: () => this.isSaving = false,
				});
			}, // end: saveOptions
			toggleMainOptions: function() {
				this.showMain  = true;
				this.showPro   = false;
				this.showDebug = false;
			},
			toggleProOptions: function() {
				this.showMain  = false;
				this.showPro   = true;
				this.showDebug = false;
			},
			toggleDebugInfo: function() {
				this.showMain  = false;
				this.showPro   = false;
				this.showDebug = true;
			}
		} // end: methods
	}); // end: Vue()

}() );
