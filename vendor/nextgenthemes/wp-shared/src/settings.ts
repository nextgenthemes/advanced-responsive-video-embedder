import './settings.scss';
import { debounce, aspectRatio } from './helpers';
import { store, getContext, getConfig } from '@wordpress/interactivity';
const domParser = new DOMParser();
const d = document;
const qs = d.querySelector.bind( d ) as typeof document.querySelector;

setupInteractivityApi();
setBodyBackgroundColorAsCssVar();

function setBodyBackgroundColorAsCssVar() {
	const backgroundColor = window.getComputedStyle( d.body ).backgroundColor;
	const wrap = qs( '.wrap--nextgenthemes' );

	if ( wrap ) {
		wrap.setAttribute( 'style', `--ngt-wp-body-bg: ${ backgroundColor };` );
	}
}

function setupInteractivityApi() {
	const namespace = qs< HTMLElement >( '[data-wp-interactive^="nextgenthemes"]' )?.dataset
		?.wpInteractive;

	if ( ! namespace ) {
		// In ARVE this script will always be loaded but the config is only output when the media button is on the page
		return;
	}

	// eslint-disable-next-line @typescript-eslint/no-unused-vars
	const { state, actions, callbacks, helpers } = store< storeInterface >( namespace, {
		state: {
			isValidLicenseKey: () => {
				const context = getContext< optionContext >();
				return 'valid' === state.options[ context.option_key + '_status' ];
			},
			is32charactersLong: () => {
				const context = getContext< optionContext >();
				return state.options[ context.option_key ].length === 32;
			},
			get isActiveTab() {
				const context = getContext< optionContext >();

				if ( ! context.activeTabs ) {
					return true; // shortcode dialog has no sections
				}

				return true === context?.activeTabs[ context.tab ];
			},
		},
		actions: {
			toggleHelp: () => {
				state.help = ! state.help;
			},
			openShortcodeDialog: () => {
				state.dialog = document.querySelector(
					'dialog[data-wp-interactive="nextgenthemes_arve_dialog"]'
				);
				state.dialog.showModal();
			},
			insertShortcode: () => {
				window.wp.media.editor.insert( state.shortcode );
				state.dialog.close();
			},
			closeShortcodeDialog: () => {
				state.dialog.close();
			},
			changeTab: () => {
				const context = getContext< optionContext >();

				for ( const key in context.activeTabs ) {
					context.activeTabs[ key ] = false;
				}
				context.activeTabs[ context.tab ] = true;
			},
			inputChange: ( event: Event ) => {
				const context = getContext< optionContext >();

				const isInput = event?.target instanceof HTMLInputElement;
				const isSelect = event?.target instanceof HTMLSelectElement;

				if ( ! isInput && ! isSelect ) {
					throw new Error( 'event.target is not HTMLInputElement or HTMLSelectElement' );
				}

				if ( 'arveUrl' in event.target.dataset ) {
					helpers.extractFromEmbedCode( event.target.value );
				} else {
					state.options[ context.option_key ] = event.target.value;
				}

				if ( 'nextgenthemes_arve_dialog' !== namespace ) {
					actions.saveOptions();
				}
			},
			checkboxChange: ( event ) => {
				const context = getContext< optionContext >();
				state.options[ context.option_key ] = event.target.checked;

				if ( 'nextgenthemes_arve_dialog' !== namespace ) {
					actions.saveOptions();
				}
			},
			selectImage: () => {
				if ( state.dialog ) {
					state.dialog.close();
				}

				const context = getContext< optionContext >();
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
						state.options[ context.option_key ] = attachmentID;
						if ( state.dialog ) {
							state.dialog.showModal();
						}
					} )
					.on( 'close', function () {
						if ( state.dialog ) {
							state.dialog.showModal();
						}
					} );
			},
			deleteOembedCache: () => {
				actions.restCall( '/delete-oembed-cache', { delete: true } );
			},
			// debounced version created later
			saveOptionsReal: () => {
				actions.restCall( '/save', state.options );
			},
			restCall: (
				restRoute: string,
				body: Record< string, any >,
				refreshAfter: boolean = false
			) => {
				if ( state.isSaving ) {
					state.message = 'trying to save too fast';
					return;
				}
				const config = getConfig();

				// set the state so that another save cannot happen while processing
				state.isSaving = true;
				state.message = 'Saving...';

				// Make a POST request to the REST API route that we registered in our PHP file
				fetch( config.restUrl + restRoute, {
					method: 'POST',
					body: JSON.stringify( body ),
					headers: {
						'Content-Type': 'application/json',
						'X-WP-Nonce': config.nonce,
					},
				} )
					.then( ( response ) => {
						if ( ! response.ok ) {
							// eslint-disable-next-line no-console
							console.log( response );
							throw new Error( 'Network response was not ok' );
						}
						return response.json();
					} )
					.then( ( message ) => {
						state.message = message;
						setTimeout( () => ( state.message = '' ), 666 );
					} )
					.catch( ( error ) => {
						state.message = error.message;
					} )
					.finally( () => {
						state.isSaving = false;

						if ( refreshAfter ) {
							window.location.reload();
						}
					} );
			},
			eddLicenseAction() {
				const context = getContext< optionContext >();

				actions.restCall(
					'/edd-license-action',
					{
						option_key: context.option_key,
						edd_store_url: context.edd_store_url, // EDD Store URL
						edd_action: context.edd_action, // edd api arg has same edd_ prefix
						item_id: context.edd_item_id, // edd api arg WITHOUT edd_ prefix
						license: state.options[ context.option_key ], // edd api arg WITHOUT edd_ prefix
					},
					true
				);
			},
			resetOptionsSection() {
				const config = getConfig() as configInterface;
				const context = getContext< optionContext >();
				const sectionToReset = context.tab;

				Object.entries( config.defaultOptions ).forEach( ( [ section, options ] ) => {
					if ( 'all' === sectionToReset ) {
						// reset all
						Object.entries( options ).forEach( ( [ key, value ] ) => {
							state.options[ key ] = value;
						} );
					} else {
						Object.entries( options ).forEach( ( [ key, value ] ) => {
							if ( section === sectionToReset ) {
								state.options[ key ] = value;
							}
						} );
					}
				} );

				actions.saveOptionsReal();
			},
		},
		callbacks: {
			updateShortcode() {
				let out = '';

				for ( const [ key, value ] of Object.entries( state.options ) ) {
					if ( 'credentialless' === key ) {
						if ( false === value ) {
							out += `${ key }="false" `;
						}
					} else if ( true === value ) {
						out += `${ key }="true" `;
					} else if ( value ) {
						out += `${ key }="${ value }" `;
					}
				}

				state.shortcode = '[arve ' + out + '/]';
			},
			// updatePreview() {
			// 	const url = new URL( 'https://symbiosistheme.test/wp-json/arve/v1/shortcode' );
			// 	const params = new URLSearchParams();
			// 	const options = getContext< optionContext >().options;
			// 	const preview = document.getElementById( 'preview' );

			// 	if ( ! preview ) {
			// 		throw new Error( 'No preview element' );
			// 	}

			// 	for ( const [ key, value ] of Object.entries( options ) ) {
			// 		if ( true === value ) {
			// 			params.append( key, 'true' );
			// 		} else if ( value.length ) {
			// 			params.append( key, value );
			// 		}
			// 	}

			// 	url.search = params.toString();

			// 	fetch( url.href )
			// 		.then( ( response ) => response.json() )
			// 		.then( ( data ) => {
			// 			preview.innerHTML = data.html;
			// 		} )
			// 		.catch( () => {
			// 			//console.error( error );
			// 		} );
			// },
		},
		helpers: {
			debugJson: ( data: Record< string, unknown > ) => {
				state.debug = JSON.stringify( data, null, 2 );
			},
			extractFromEmbedCode: ( url: string ) => {
				const iframe = domParser
					.parseFromString( url, 'text/html' )
					.querySelector( 'iframe' );
				const srcAttr = iframe && iframe.getAttribute( 'src' );

				if ( srcAttr ) {
					url = srcAttr;

					if ( iframe.width && iframe.height ) {
						const ratio = aspectRatio( iframe.width, iframe.height );

						if ( '16:9' !== ratio ) {
							state.options.aspect_ratio = ratio;
						}
					}
				}
				state.options.url = url;
			},
		},
	} );

	actions.saveOptions = debounce( actions.saveOptionsReal, 1111 );
}
declare global {
	interface Window {
		wp: {
			media: wpMedia;
		};
	}
}

interface wpMedia {
	( options: any ): any; // Function-like usage
	open: () => this; // Method to initialize the media dialog
	on: ( eventName: string, callback: ( data: any ) => void ) => this; // Event subscription
	editor: {
		insert: ( content: string ) => void; // Method to insert content into the editor}
	};
}

interface storeInterface {
	state: {
		options: Record< string, string | number | boolean >;
		help: boolean;
		dialog: HTMLDialogElement;
		isSaving: boolean;
		message: string;
		shortcode: string;
		debug: string;
		isValidLicenseKey: () => boolean;
		is32charactersLong: () => boolean;
		isActiveTab: boolean;
	};
	actions: {
		toggleHelp: () => void;
		openShortcodeDialog: () => void;
		insertShortcode: () => void;
		closeShortcodeDialog: () => void;
		saveOptions: () => void;
		saveOptionsReal: () => void;
		changeTab: ( tab: string ) => void;
		inputChange: ( event: Event ) => void;
		checkboxChange: ( event: Event ) => void;
		selectImage: () => void;
		deleteOembedCache: () => void;
		eddLicenseAction: () => void;
		resetOptionsSection: () => void;
		restCall: (
			restRoute: string,
			body: Record< string, any >,
			refreshAfter?: boolean
		) => void;
	};
	callbacks: {
		updateShortcode: () => void;
		updatePreview: () => void;
	};
	helpers: {
		extractFromEmbedCode: ( url: string ) => void;
		debugJson: ( data: Record< string, any > ) => void;
	};
}

interface optionContext {
	tab: string;
	option_key: string;
	edd_item_id: string;
	edd_action: string;
	edd_store_url: string;
	activeTabs: { [ key: string ]: boolean };
}

interface configInterface {
	restUrl: string;
	nonce: string;
	defaultOptions: Record< string, string | number | boolean >;
}
