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

const d = document;
const qs = d.querySelector.bind( d ) as typeof d.querySelector;
const id = d.getElementById.bind( d ) as typeof d.getElementById;

const $ = window.jQuery;
const data = window.arveSCSettings;
const sections = data.sections as Record< string, string >;

window.addEventListener( 'DOMContentLoaded', () => {
	init();
} );

function init(): void {
	// const arveBtn = id( 'arve-btn' );
	const arveBtn = id( 'arve-btn' );
	const dialog = qs( '.arve-sc-dialog' ) as HTMLDialogElement | null;
	const submitBtn = qs( '.arve-sc-dialog__submit-btn' );
	const closeBtn = qs( '.arve-sc-dialog__close-btn' );
	const cancelBtn = qs( '.arve-sc-dialog__cancel-btn' );

	if ( ! arveBtn || ! dialog || ! submitBtn || ! closeBtn || ! cancelBtn ) {
		return;
	}

	initVue();

	arveBtn.addEventListener( 'click', () => {
		dialog.showModal();
	} );

	submitBtn.addEventListener( 'click', ( ev: Event ) => {
		ev.preventDefault();
		dialog.close();

		const text = qs( '#arve-shortcode' )?.textContent?.trim();
		window.wp.media.editor.insert( text );
	} );

	closeBtn.addEventListener( 'click', ( ev: Event ) => {
		ev.preventDefault();
		dialog.close();
	} );

	cancelBtn.addEventListener( 'click', ( ev: Event ) => {
		ev.preventDefault();
		dialog.close();
	} );
}

function initVue() {
	const vueDiv = id( 'arve-sc-vue' );

	if ( ! vueDiv ) {
		return;
	}

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
		}, // end: methods
	} ); // end: Vue()
}

function buildSectionsDisplayed() {
	const sectionsDisplayed = {} as Record< string, boolean >;

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
