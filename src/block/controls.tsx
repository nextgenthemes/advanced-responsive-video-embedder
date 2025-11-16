/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */
import { __ } from '@wordpress/i18n';
import {
	BaseControl,
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl, // eslint-disable-line @wordpress/no-unsafe-wp-apis
	__experimentalToggleGroupControlOption as ToggleGroupControlOption, // eslint-disable-line @wordpress/no-unsafe-wp-apis
} from '@wordpress/components';
// createElement import removed as we're using JSX syntax
import ImageUpload from './components/ImageUpload';
import UrlOrEmbedCode from './components/UrlOrEmbedCode';
import { hasSameKeys } from './utils';

const { settingPageUrl, options, settings, gutenbergActive } = window.ArveBlockJsBefore;
const { gutenberg_help: gutenbergHelp } = options;

function createHelp( html?: string ): undefined | string | JSX.Element {
	if ( ! gutenbergHelp || ! html ) {
		return undefined;
	}

	// Quick check: if no <a> tags, return the html as a single string
	if ( ! html.match( /<a/i ) ) {
		return html;
	}

	const doc = new DOMParser().parseFromString( html, 'text/html' );
	const result: ( string | JSX.Element )[] = [];
	let key = 1;

	const walk = ( node: Node ) => {
		if ( node.nodeType === Node.TEXT_NODE ) {
			const text = node.textContent;
			if ( text !== null && text !== undefined ) {
				result.push( text ); // Preserve all whitespace, no trim
			}
		} else if ( node.nodeType === Node.ELEMENT_NODE ) {
			const el = node as HTMLElement;
			if ( el.tagName === 'A' ) {
				const a = el as HTMLAnchorElement;
				const linkText = a.textContent || '';
				result.push(
					<a href={ a.href } target="_blank" rel="noreferrer" key={ 'link-' + key }>
						{ linkText }
					</a>
				);
				key++;
				return; // Don't process children since we handled the text
			}

			// Process child nodes for other elements (though assuming only text and <a>)
			Array.from( el.childNodes ).forEach( walk );
		}
	};

	walk( doc.body );
	return <>{ result }</>;
}

// Convert options to SelectControl format
function prepareSelectOptions( opts: Record< string, string > ): GutenbergSelectOption[] {
	return Object.entries( opts ).map( ( [ value, label ] ) => ( {
		label,
		value,
	} ) );
}

function shouldHide( settingKey: string, attributes: Record< string, unknown > ): boolean {
	if ( 'align' === settingKey ) {
		return true;
	}

	const setting = settings[ settingKey ];

	// If no dependencies, don't hide
	if ( ! setting?.depends?.length ) {
		return false;
	}

	// Check if NONE of the dependency conditions are met
	const hide = ! setting.depends.some( ( condition ) => {
		// Each condition is an object with a single key-value pair
		const [ key, value ] = Object.entries( condition )[ 0 ] || [];

		if ( ! attributes[ key ] ) {
			return true; // If the is unset (default) show all settings, as advertisement
		}

		// If the attribute has the key and its value matches the condition, return true
		return key !== undefined && attributes[ key ] === value;
	} );

	return hide;
}

// Main export: build controls for the block inspector
export function buildControls( { attributes, setAttributes }: BuildControlsProps ): JSX.Element[] {
	const controls: JSX.Element[] = [];
	const sectionControls: Record< string, JSX.Element[] > = {};

	// Initialize section controls
	Object.values( settings ).forEach( ( setting ) => {
		sectionControls[ setting.category ] = [];
	} );

	// Add controls to sections
	Object.entries( settings ).forEach( ( [ sKey, setting ] ) => {
		const val = attributes[ sKey ];
		const url = ( attributes[ `${ sKey }_url` ] as string ) || '';
		const tab = setting.category || 'no-category';

		if ( shouldHide( sKey, attributes ) ) {
			return;
		}

		const settingOptions = setting.options || {};
		const boolWithDefaultKeys = [ '', 'true', 'false' ] as const; // Use 'as const' for literal type inference

		if ( hasSameKeys( settingOptions, boolWithDefaultKeys ) ) {
			sectionControls[ tab ].push(
				<ToggleGroupControl
					key={ sKey }
					label={ setting.label }
					value={ ( val as string ) || '' }
					isBlock
					__nextHasNoMarginBottom
					__next40pxDefaultSize
					onChange={ ( value ) => setAttributes( { [ sKey ]: value } ) }
					help={ createHelp( setting.description ) }
				>
					<ToggleGroupControlOption
						value=""
						label={ __( 'Default', 'advanced-responsive-video-embedder' ) }
					/>
					<ToggleGroupControlOption
						value="true"
						label={ __( 'True', 'advanced-responsive-video-embedder' ) }
					/>
					<ToggleGroupControlOption
						value="false"
						label={ __( 'False', 'advanced-responsive-video-embedder' ) }
					/>
				</ToggleGroupControl>
			);
		} else if ( 'url' === sKey ) {
			sectionControls[ tab ].push(
				<UrlOrEmbedCode
					key={ sKey }
					label={ setting.label }
					value={ ( val as string ) || '' }
					onChange={ ( value: string ) => setAttributes( { [ sKey ]: value } ) }
					onAspectRatioChange={ ( ratio: string ) =>
						setAttributes( { aspect_ratio: ratio } )
					}
					placeholder={ setting.placeholder }
					help={ createHelp( setting.description ) }
				/>
			);
		} else if ( setting.ui === 'image_upload' ) {
			sectionControls[ tab ].push(
				<ImageUpload
					key={ sKey }
					sKey={ sKey }
					className={ `arve-ctl-${ setting.tab }` }
					val={ ( val as number ) || undefined }
					url={ url }
					help={ createHelp( setting.description ) }
					setAttributes={ setAttributes }
				/>
			);
		} else if ( setting.ui_element === 'select' ) {
			const selectOptions = prepareSelectOptions(
				setting.options as Record< string, string >
			);
			sectionControls[ tab ].push(
				<SelectControl
					key={ sKey }
					className={ `arve-ctl-${ setting.tab }` }
					label={ setting.label }
					value={ val as string }
					options={ selectOptions }
					onChange={ ( value: string ) => setAttributes( { [ sKey ]: value } ) }
					help={ createHelp( setting.description ) }
				/>
			);
		} else if ( setting.ui_element_type === 'checkbox' ) {
			sectionControls[ tab ].push(
				<ToggleControl
					key={ sKey }
					className={ `arve-ctl-${ setting.tab }` }
					label={ setting.label }
					checked={ Boolean( val ) }
					onChange={ ( value: boolean ) => setAttributes( { [ sKey ]: value } ) }
					help={ createHelp( setting.description ) }
				/>
			);
		} else {
			sectionControls[ tab ].push(
				<TextControl
					key={ sKey }
					className={ `arve-ctl-${ setting.tab }` }
					label={ setting.label }
					type={ setting.ui_element_type }
					value={ ( val as string ) || '' }
					placeholder={ setting.placeholder }
					onChange={ ( value: string ) => setAttributes( { [ sKey ]: value } ) }
					help={ createHelp( setting.description ) }
				/>
			);
		}
	} );

	if ( gutenbergHelp || gutenbergActive ) {
		// Add info panel to main section
		sectionControls.main.push(
			<BaseControl
				key="info-panel"
				help={
					<>
						{ gutenbergHelp && (
							<>
								{ __(
									'Remember changing the defaults is possible on the',
									'advanced-responsive-video-embedder'
								) }{ ' ' }
								<a href={ settingPageUrl } target="_blank" rel="noreferrer">
									{ __( 'Settings page', 'advanced-responsive-video-embedder' ) }
								</a>
								{ '. ' }
								{ __(
									'You can also disable the extensive help texts there to clean up this UI.',
									'advanced-responsive-video-embedder'
								) }
							</>
						) }
						{ gutenbergActive && (
							<>
								{ ' ' }
								{ __(
									'Error 153 in YouTube embeds, is a known issue with the Gutenberg plugin active and effects only the editor and normal mode. Your Videos will work fine on the front-end. Lazyload is not effected.',
									'advanced-responsive-video-embedder'
								) }
							</>
						) }
					</>
				}
			>
				<BaseControl.VisualLabel>
					{ __( 'Info', 'advanced-responsive-video-embedder' ) }
				</BaseControl.VisualLabel>
			</BaseControl>
		);
	}

	const categories = {
		main: __( 'Main', 'advanced-responsive-video-embedder' ),
		lazyloadAndLightbox: __( 'Lazyload & Lightbox', 'advanced-responsive-video-embedder' ),
		lightbox: __( 'Lightbox', 'advanced-responsive-video-embedder' ),
		data: __( 'Data', 'advanced-responsive-video-embedder' ),
		stickyVideos: __( 'Sticky Videos', 'advanced-responsive-video-embedder' ),
		functional: __( 'Functional', 'advanced-responsive-video-embedder' ),
		privacy: __( 'Privacy', 'advanced-responsive-video-embedder' ),
		misc: __( 'Misc', 'advanced-responsive-video-embedder' ),
	};

	// Convert section controls to panels
	Object.entries( sectionControls ).forEach( ( [ tab, tabControls ] ) => {
		if ( tabControls.length > 0 ) {
			controls.push(
				<PanelBody
					key={ tab }
					title={ categories[ tab ] ?? tab }
					initialOpen={ 'main' === tab }
				>
					{ tabControls }
				</PanelBody>
			);
		}
	} );

	return controls;
}
