import Vue from 'vue';

export {};
declare global {
	interface Window {
		wp;
		arveSCSettings;
		jQuery;
		sui;
		/* eslint-disable-next-line */
		tb_show;
		ajaxurl;
	}
}

const jq = window.jQuery;
const data = window.arveSCSettings;
const settings = data.settings as Record<string, OptionProps>;
const sections = data.sections as Record<string, string>;

interface OptionProps {
	label: string;
	tag: string;
	type: string;
	default: number | string | boolean;
	description?: string;
	descriptionlink?: string;
	descriptionlinktext?: string;
	placeholder?: string;
	options?;
}

jq(document).on('click', '#arve-btn', () => {
	jq('#my-dialog').dialog('open');
	//window.tb_show('ARVE Shortcode Creator', '#TB_inline?inlineId=arve-shortcode-creator');
});

jq('#my-dialog').dialog({
	title: 'My Dialog',
	dialogClass: 'wp-dialog',
	autoOpen: false,
	draggable: false,
	width: 'auto',
	modal: true,
	resizable: false,
	closeOnEscape: true,
	position: {
	my: "center",
	at: "center",
	of: window
	},
	open: function () {
		// close dialog by clicking the overlay behind it
		jq('.ui-widget-overlay').bind('click', function(){
			jq('#my-dialog').dialog('close');
		})
	},
	create: function () {
		// style fix for WordPress admin
		jq('.ui-dialog-titlebar-close').addClass('ui-button');
	},
});

function buildSectionsDisplayed() {
	const sectionsDisplayed = {};

	Object.keys(sections).forEach((key) => {
		sectionsDisplayed[key] = 'debug' === key ? false : true;
	});

	console.log('sections', sectionsDisplayed);

	return sectionsDisplayed;
}

function setAllObjValues(obj, val) {
	Object.keys(obj).forEach((index) => {
		obj[index] = val;
	});
}

new Vue({
	// DOM selector for our app's main wrapper element
	el: '#arve-shortcode-creator',

	// Data that will be proxied by Vue.js to provide reactivity to our template
	data: {
		errors: [],
		isSaving: false,
		refreshAfterSave: false,
		sectionsDisplayed: buildSectionsDisplayed(),
		onlySectionDisplayed: false,
		message: '',
		vm: data.options,
	},

	// Methods that can be invoked from within our template
	methods: {
		showSection(section) {
			setAllObjValues(this.sectionsDisplayed, false);
			this.sectionsDisplayed[section] = true;
			this.onlySectionDisplayed = section;
		},
		showAllSectionsButDebug() {
			setAllObjValues(this.sectionsDisplayed, true);
			this.sectionsDisplayed.debug = false;
			this.onlySectionDisplayed = false;
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
