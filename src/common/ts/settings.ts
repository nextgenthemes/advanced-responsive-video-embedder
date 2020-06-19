import Vue from 'vue';

const url = new URL(window.location.href);
const pageQueryVal = url.searchParams.get('page');

if (!pageQueryVal) {
	throw 'Need page url arg';
}

const data = window[pageQueryVal];

new Vue({
	// DOM selector for our app's main wrapper element
	el: '#nextgenthemes-vue',

	// Data that will be proxied by Vue.js to provide reactivity to our template
	data: {
		isSaving: false,
		refreshAfterSave: false,
		sectionsDisplayed: {
			main: true,
			html5: true,
			pro: true,
			videojs: true,
			randomvideo: true,
			debug: false,
			urlparams: false,
		},
		message: '',
		vm: data.options,
	},

	// Methods that can be invoked from within our template
	methods: {
		// Save the options to the database
		saveOptions() {
			// set the state so that another save cannot happen while processing
			this.isSaving = true;

			// Make a POST request to the REST API route that we registered in our PHP file
			window.jQuery.ajax({
				url: data.rest_url + '/save',
				method: 'POST',
				data: this.vm,

				// set the nonce in the request header
				beforeSend(request) {
					request.setRequestHeader('X-WP-Nonce', data.nonce);
				},

				// callback to run upon successful completion of our request
				success: () => {
					this.message = 'Options saved';
					setTimeout(() => (this.message = ''), 1000);
				},

				// callback to run if our request caused an error
				error: (errorData) => {
					this.message = errorData.responseText;
					this.refreshAfterSave = false;
				},

				// when our request is complete (successful or not), reset the state to indicate we are no longer saving
				complete: () => {
					this.isSaving = false;
					if (this.refreshAfterSave) {
						this.refreshAfterSave = false;
						window.location.reload();
					}
				},
			});
		}, // end: saveOptions
		licenseAPI(action, itemID, optKey) {
			// set the state so that another save cannot happen while processing
			this.isSaving = true;

			// Make a POST request to the REST API route that we registered in our PHP file
			window.jQuery.ajax({
				url: 'https://nextgenthemes.com',
				method: 'POST',
				crossDomain: true,
				data: {
					edd_action: action + '_license', // Valid actions are activate_license, deactivate_license, get_version, check_license
					license: this.vm[optKey],
					item_id: itemID,
					url: data.home_url,
				},

				// callback to run upon successful completion of our request
				success: (response) => {
					console.log( 'response', response ); // eslint-disable-line

					this.message = 'License API call saved';
					setTimeout(() => (this.message = ''), 1000);
				},

				// callback to run if our request caused an error
				error: (errorData) => (this.message = errorData.responseText),

				// when our request is complete (successful or not), reset the state to indicate we are no longer saving
				complete: () => (this.isSaving = false),
			});
		}, // end: saveOptions
		licenseAPI_native(action, itemID, optKey) {
			// Handling a SoftwAre licensing request without jQuery in pure JavaScript
			const xhttp = new XMLHttpRequest();

			// The url to the site running Easy Digital Downloads w/ Software Licensing
			const postUrl = 'https://nextgenthemes.com';

			xhttp.onreadystatechange = function () {
				console.log( 'xhttp.readyState', xhttp.readyState ); // eslint-disable-line
				console.log( 'xhttp.status', xhttp.status ); // eslint-disable-line

				if (xhttp.readyState === 4 && xhttp.status === 200) {
					const slData = JSON.parse(xhttp.responseText);
					console.log( 'slData', slData ); // eslint-disable-line
				}
			};

			const args = {
				edd_action: action + '_license', // Valid actions are activate_license, deactivate_license, get_version, check_license
				license: this.vm[optKey],
				item_id: itemID,
				url: data.home_url,
			};

			xhttp.open('POST', postUrl, true);
			//xhttp.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
			xhttp.setRequestHeader('Content-type', 'application/json; charset=utf-8');
			xhttp.setRequestHeader('Access-Control-Allow-Origin', data.home_url);

			let values = '';
			for (const key in args) {
				values += key + '=' + data[key] + '&';
			}
			values = values.substring(0, values.length - 1);
			xhttp.send(values);
		},
		showSection(section) {
			setAllObjValues(this.sectionsDisplayed, false);
			this.sectionsDisplayed[section] = true;
		},
		showAllSectionsButDebug() {
			setAllObjValues(this.sectionsDisplayed, true);
			this.sectionsDisplayed.debug = false;
		},
		uploadImage(dataKey) {
			const vueThis = this;
			const image = window.wp
				.media({
					title: 'Upload Image',
					multiple: false,
				})
				.open()
				.on('select', function () {
					// This will return the selected image from the Media Uploader, the result is an object
					const uploadedImage = image.state().get('selection').first();
					// We convert uploadedImage to a JSON object to make accessing it easier
					const attachmentID = uploadedImage.toJSON().id;
					vueThis.vm[dataKey] = attachmentID;
				});
		},
		action(action, product) {
			this.vm.action = JSON.stringify({ action, product });
			this.refreshAfterSave = true;
			this.saveOptions();
		},
	}, // end: methods
}); // end: Vue()

function setAllObjValues(obj, val) {
	Object.keys(obj).forEach((index) => {
		obj[index] = val;
	});
}
