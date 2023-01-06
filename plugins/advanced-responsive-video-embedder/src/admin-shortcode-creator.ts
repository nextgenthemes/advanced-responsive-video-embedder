import Vue from 'vue';

declare global {
	interface Window {
		wp;
		arveSCSettings;
		jQuery;
		sui;
		ajaxurl;
	}
}

const $ = window.jQuery;
const d = document;
const data = window.arveSCSettings;
const settings = data.settings as Record< string, OptionProps >;
const sections = data.sections as Record< string, string >;

const qs = d.querySelector.bind( d ) as typeof d.querySelector;
const qsa = d.querySelectorAll.bind( d ) as typeof d.querySelectorAll;
const id = d.getElementById.bind( d ) as typeof d.getElementById;

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

export function initSC(): void {
	// const arveBtn = id( 'arve-btn' );
	const arveScDialog = qs( '.arve-sc-dialog' );
	const arveScDialogSubmitBtn = qs( '.arve-sc-dialog__submit-btn' );

	if ( ! arveScDialog || ! arveScDialogSubmitBtn ) {
		return;
	}

	$( document ).on( 'click', '#arve-btn', () => {
		initVue();
		arveScDialog.showModal();
	} );

	arveScDialogSubmitBtn.addEventListener( 'click', () => {
		console.log( 'submit' );

		const text = $.trim( qs( '#arve-shortcode' ).text() );
		window.wp.media.editor.insert( text );
	} );
}

export function initVue(): void {
	const vueDiv = id( 'arve-sc-vue' );

	if ( ! vueDiv ) {
		return;
	}

	console.log( 'vue' );

	new Vue( {
		// DOM selector for our app's main wrapper element
		el: '#arve-sc-vue',

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
			showSection( section ) {
				setAllObjValues( this.sectionsDisplayed, false );
				this.sectionsDisplayed[ section ] = true;
				this.onlySectionDisplayed = section;
			},
			showAllSectionsButDebug() {
				setAllObjValues( this.sectionsDisplayed, true );
				this.sectionsDisplayed.debug = false;
				this.onlySectionDisplayed = false;
			},
			uploadImage( dataKey ) {
				const vueThis = this;
				const image = window.wp
					.media( {
						title: 'Upload Image',
						multiple: false,
					} )
					.open()
					.on( 'select', function () {
						// This will return the selected image from the Media Uploader, the result is an object
						const uploadedImage = image
							.state()
							.get( 'selection' )
							.first();
						// We convert uploadedImage to a JSON object to make accessing it easier
						const attachmentID = uploadedImage.toJSON().id;
						vueThis.vm[ dataKey ] = attachmentID;
					} );
			},
			action( action, product ) {
				this.vm.action = JSON.stringify( { action, product } );
				this.refreshAfterSave = true;
				this.saveOptions();
			},
		}, // end: methods
	} ); // end: Vue()
}

function buildSectionsDisplayed() {
	const sectionsDisplayed = {};

	Object.keys( sections ).forEach( ( key ) => {
		sectionsDisplayed[ key ] = 'debug' === key ? false : true;
	} );

	return sectionsDisplayed;
}

function setAllObjValues( obj, val ) {
	Object.keys( obj ).forEach( ( index ) => {
		obj[ index ] = val;
	} );
}
