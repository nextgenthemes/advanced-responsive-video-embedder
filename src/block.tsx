/**
 * Copyright 2019-2024 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright 2019 Gary Pendergast
 * License: GPL 2.0+
 */
import json from './block.json';
import './editor.scss';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import {
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	BaseControl,
	TextControl,
	Button,
	ToggleControl,
	SelectControl,
	PanelBody,
	ResponsiveWrapper,
	DropZone,
} from '@wordpress/components';
import { registerBlockType } from '@wordpress/blocks';
import classnames from 'classnames';
import { Fragment } from '@wordpress/element';

export {};
declare global {
	interface Window {
		ArveBlockJsBefore: inlineScriptJSON;
		Sanitizer?: any;
	}
}

interface inlineScriptJSON {
	settings: Record< string, OptionProps >;
	options: Record< string, boolean | number | string >;
}

interface sectionControls {
	main: Array< JSX.Element >;
	pro: Array< JSX.Element >;
	html5: Array< JSX.Element >;
	sticky_videos: Array< JSX.Element >;
	random_video: Array< JSX.Element >;
	privacy: Array< JSX.Element >;
}

interface SelectOption {
	label: string;
	value: string;
	disabled?: boolean;
}

interface OptionProps {
	label: string;
	tag: string;
	type: string;
	description?: string;
	descriptionlink?: string;
	descriptionlinktext?: string;
	placeholder?: string;
	options?: Record< string, string >;
	ui?: 'image_upload';
	ui_element: 'select' | 'input';
	ui_element_type: 'text' | 'number' | 'checkbox';
}

const { name } = json;
const { settings, options } = window.ArveBlockJsBefore;
delete settings.align.options.center;
const domParser = new DOMParser();

/**
 * Keypair to gutenberg component
 * @param selectOptions
 */
function PrepareSelectOptions( selectOptions: OptionProps ) {
	const gboptions = [] as Array< SelectOption >;

	Object.entries( selectOptions ).forEach( ( [ key, value ] ) => {
		const o: SelectOption = {
			label: value,
			value: key,
		};

		gboptions.push( o );
	} );

	return gboptions;
}

function changeTextControl( key: string, value: string, props ) {
	if ( 'url' === key ) {
		const iframe = domParser.parseFromString( value, 'text/html' ).querySelector( 'iframe' );
		if ( iframe && iframe.getAttribute( 'src' ) ) {
			props.setAttributes( {
				[ key ]: iframe.getAttribute( 'src' ),
			} );

			if ( iframe.width && iframe.height ) {
				const ratio = aspectRatio( iframe.width, iframe.height );

				if ( '16:9' !== ratio ) {
					props.setAttributes( {
						aspect_ratio: ratio,
					} );
				}
			}
			return;
		}
	}

	props.setAttributes( {
		[ key ]: value,
	} );
}

// function changeSelectControl( key: string, value: string, props ) {

// 	if ( ! value ) {

// 	}

// 	props.setAttributes( {
// 		[ key ]: value,
// 	} );
// }

const mediaUploadRender = ( open: VoidFunction, val, url: string ): JSX.Element => {
	return (
		<div className="editor-post-featured-image__container">
			{ /* @ts-ignore */ }
			<Button
				className={
					! val
						? 'editor-post-featured-image__toggle'
						: 'editor-post-featured-image__preview'
				}
				onClick={ open }
				aria-label={ ! val ? null : __( 'Edit or update the image' ) }
				aria-describedby={ ! val ? '' : `editor-post-featured-image-${ val }-describedby` }
			>
				{ !! val && !! url && (
					<div style={ { overflow: 'hidden' } }>
						<ResponsiveWrapper naturalWidth={ 640 } naturalHeight={ 360 } isInline>
							<img
								src={ url }
								alt="ARVE Thumbnail"
								style={ {
									width: '100%',
									height: '100%',
									objectFit: 'cover',
								} }
							/>
						</ResponsiveWrapper>
					</div>
				) }
				{ ! val && __( 'Set Thumbnail' ) }
			</Button>
			<DropZone />
		</div>
	);
};

function select( val );

function buildControls( props ) {
	const controls = [] as Array< JSX.Element >;
	const sectionControls = {} as sectionControls;
	const mediaUploadInstructions = (
		<p>{ __( 'To edit the featured image, you need permission to upload media.' ) }</p>
	);
	let selectedMedia;

	Object.values( settings ).forEach( ( option: OptionProps ) => {
		sectionControls[ option.tag ] = [];
	} );

	Object.entries( settings ).forEach( ( [ key, option ]: [ string, OptionProps ] ) => {
		const val = props.attributes[ key ];
		const url = '';

		sectionControls[ option.tag ].push(
			<Fragment key={ key + '-fragment' }>
				{ 'select' === option.ui_element && (
					<SelectControl
						value={ val }
						label={ option.label }
						help={ createHelp( option ) }
						options={ PrepareSelectOptions( option.options ) }
						onChange={ ( value ) => {
							return props.setAttributes( {
								[ key ]: value,
							} );
						} }
					/>
				) }
				{ 'checkbox' === option.ui_element_type && (
					<ToggleControl
						key={ key }
						label={ option.label }
						help={ createHelp( option ) }
						checked={ !! val }
						onChange={ ( value ) => {
							return props.setAttributes( {
								[ key ]: value,
							} );
						} }
					/>
				) }
				{ [ 'text', 'number' ].includes( option.ui_element_type ) && (
					<TextControl
						label={ option.label }
						placeholder={ option.placeholder }
						help={ createHelp( option ) }
						value={ val }
						onChange={ ( value ) => {
							changeTextControl( key, value, props );
						} }
					/>
				) }
				{ 'image_upload' === option.ui && (
					<BaseControl
						className="editor-post-featured-image"
						help={ createHelp( option ) }
					>
						<MediaUploadCheck fallback={ mediaUploadInstructions }>
							<MediaUpload
								title={ __( 'Thumbnail' ) }
								onSelect={ ( media ) => {
									selectedMedia = media;
									return props.setAttributes( {
										[ key ]: media.id.toString(),
										[ key + '_url' ]: media.url,
									} );
								} }
								allowedTypes={ [ 'image' ] }
								modalClass="editor-post-featured-image__media-modal"
								render={ ( { open } ) => {
									return mediaUploadRender( open, val, url );
								} }
								value={ val }
							/>
						</MediaUploadCheck>
						{ !! val && !! url && (
							<MediaUploadCheck key={ key + '-MediaUploadCheck-2' }>
								<MediaUpload
									title={ __( 'Thumbnail' ) }
									onSelect={ ( media ) => {
										selectedMedia = media;
										return props.setAttributes( {
											[ key ]: media.id.toString(),
											[ key + '_url' ]: media.url,
										} );
									} }
									allowedTypes={ [ 'image' ] }
									modalClass="editor-post-featured-image__media-modal"
									render={ ( { open } ) => (
										<Button onClick={ open } variant="secondary">
											{ __( 'Replace Thumbnail' ) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
						) }
						{ !! val && (
							<MediaUploadCheck key={ key + '-MediaUploadCheck-3' }>
								<Button
									onClick={ () => {
										return props.setAttributes( {
											[ key ]: '',
											[ key + '_url' ]: '',
										} );
									} }
									isLink
									isDestructive
								>
									{ __( 'Remove Thumbnail' ) }
								</Button>
							</MediaUploadCheck>
						) }
					</BaseControl>
				) }
			</Fragment>
		);
	} );

	sectionControls.main.push(
		<BaseControl
			key={ 'info' }
			help={ __(
				'You can disable the extensive help texts on the ARVE settings page to clean up this UI',
				'advanced-responsive-video-embedder'
			) }
		>
			<BaseControl.VisualLabel>
				{ __( 'Info', 'advanced-responsive-video-embedder' ) }
			</BaseControl.VisualLabel>
		</BaseControl>
	);

	Object.keys( sectionControls ).forEach( ( key ) => {
		controls.push(
			<PanelBody key={ key } title={ capitalizeFirstLetter( key ) } initialOpen={ true }>
				{ sectionControls[ key ] }
			</PanelBody>
		);
	} );

	return controls;

	// Object.keys( sectionControls ).forEach( ( key ) => {
	// 	controls.push( sectionControls[ key ] );
	// 	open = false;
	// } );

	// return (
	// 	<PanelBody key="arve" title="ARVE" initialOpen={ true }>
	// 		{ controls }
	// 	</PanelBody>
	// );
}

function createHelp( option: OptionProps ) {
	if ( typeof option.description !== 'string' ) {
		return '';
	}

	if ( typeof option.descriptionlinktext === 'string' ) {
		const textSplit = option.description.split( option.descriptionlinktext );

		return (
			<>
				{ textSplit[ 0 ] }
				<a href={ option.descriptionlink }>{ option.descriptionlinktext }</a>
				{ textSplit[ 1 ] }
			</>
		);
	}
	return option.description;
}

function capitalizeFirstLetter( str: string ): string {
	return str.charAt( 0 ).toUpperCase() + str.slice( 1 );
}

function Edit( props: Record< string, any > ) {
	const {
		attributes: { mode, align, maxwidth },
	} = props;

	let pointerEvents = true;
	const style = {} as React.CSSProperties;
	const attrCopy = JSON.parse( JSON.stringify( props.attributes ) );
	delete attrCopy.align;
	delete attrCopy.maxwidth;

	if ( maxwidth && ( 'left' === align || 'right' === align ) ) {
		style.width = '100%';
		style.maxWidth = maxwidth;
	} else if ( 'left' === align || 'right' === align ) {
		style.width = '100%';
		style.maxWidth = options.align_maxwidth as string | number;
	}
	const blockProps = useBlockProps( { style } );

	if ( 'normal' === mode || ( ! mode && 'normal' === options.mode ) ) {
		pointerEvents = false;
	}

	return (
		<>
			<div { ...blockProps } key="block">
				<ServerSideRender
					className={ classnames( {
						'arve-ssr': true,
						'arve-ssr--pointer-events-none': ! pointerEvents,
					} ) }
					block="nextgenthemes/arve-block"
					attributes={ attrCopy }
					skipBlockSupportAttributes={ true }
				/>
			</div>
			<InspectorControls key="insp">{ buildControls( props ) }</InspectorControls>
		</>
	);
}

// @ts-ignore
registerBlockType( name, {
	edit: Edit,
} );

/**
 * Calculate aspect ratio based on width and height.
 *
 * @param {string} width  - The width value
 * @param {string} height - The height value
 * @return {string} The aspect ratio in the format 'width:height'
 */
function aspectRatio( width: string, height: string ): string {
	if ( isIntOverZero( width ) && isIntOverZero( height ) ) {
		const w = parseInt( width );
		const h = parseInt( height );
		const arGCD = gcd( w, h );

		return w / arGCD + ':' + h / arGCD;
	}

	return width + ':' + height;
}

/**
 * Check if the input string is a positive integer.
 *
 * @param {string} str - The input string to be checked.
 * @return {boolean} Whether the input string is a positive integer or not.
 */
function isIntOverZero( str: string ): boolean {
	const n = Math.floor( Number( str ) );
	return n !== Infinity && String( n ) === str && n > 0;
}

/**
 * Calculates the greatest common divisor of two numbers using the Euclidean algorithm.
 *
 * @param {number} a - the first number
 * @param {number} b - the second number
 * @return {number} the greatest common divisor of the two numbers
 */
function gcd( a: number, b: number ): number {
	if ( ! b ) {
		return a;
	}

	return gcd( b, a % b );
}

/*
TODO when the sanitizer API
function sanitizeHelpHTML( option: OptionProps ) {
	if ( typeof option.description !== 'string' ) {
		return '';
	}

	const div = document.createElement( 'div' );

	if ( 'Sanitizer' in window ) {
		const sanitizer = new window.Sanitizer( {
			allowElements: [ 'a' ],
			allowAttributes: {
				target: [ 'a' ],
				href: [ 'a' ],
			},
		} );

		// @ts-ignore
		div.setHTML( option.description, { sanitizer } );

		return <span dangerouslySetInnerHTML={ { __html: div.innerHTML } } />;
	}

	return stripHTML( option.description );
}

function stripHTML( html ) {
	const doc = new DOMParser().parseFromString( html, 'text/html' );
	return doc.body.textContent || '';
}
*/
