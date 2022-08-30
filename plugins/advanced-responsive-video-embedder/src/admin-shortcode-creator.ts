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
const data = window.arveSCSettings;
const settings = data.settings as Record< string, OptionProps >;
const sections = data.sections as Record< string, string >;

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
	const vueDiv = document.getElementById( 'arve-sc-vue' );

	if ( vueDiv ) {
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

	$( '#arve-sc-dialog' ).dialog( {
		title: 'ARVE Shortcode',
		dialogClass: 'wp-dialog',
		autoOpen: false,
		draggable: true,
		width: 900,
		modal: true,
		resizable: true,
		closeOnEscape: true,
		position: {
			my: 'center',
			at: 'center',
			of: window,
		},
		open: () => {
			// close dialog by clicking the overlay behind it
			$( '.ui-widget-overlay' ).bind( 'click', function () {
				$( '#arve-sc-dialog' ).dialog( 'close' );
			} );
		},
		create: () => {
			// style fix for WordPress admin
			$( '.ui-dialog-titlebar-close' ).addClass( 'ui-button' );
			$( '.ui-dialog-buttonset button:first' ).addClass(
				'button-primary'
			);
		},
		buttons: {
			'Insert Shortcode'() {
				$( this ).dialog( 'close' );
				const text = $.trim( $( '#arve-shortcode' ).text() );
				window.wp.media.editor.insert( text );
			},
			Cancel() {
				$( this ).dialog( 'close' );
			},
		},
	} );

	$( document ).on( 'click', '#arve-btn', () => {
		$( '#arve-sc-dialog' ).dialog( 'open' );
	} );
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
