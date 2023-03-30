/**
 * Copyright 2019-2023 Nicolas Jonas
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
	'sticky-videos': Array< JSX.Element >;
	'random-video': Array< JSX.Element >;
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
	options?;
}

const { name } = json;
const { settings, options } = window.ArveBlockJsBefore;
delete settings.align.options.center;

const domParser = new DOMParser();

/*
 * Keypair to gutenberg component
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

function maybeSetAspectRatio( key: string, value: string, props ) {
	if ( 'url' === key ) {
		const iframe = domParser.parseFromString( value, 'text/html' ).querySelector( 'iframe' );
		if ( iframe && iframe.getAttribute( 'src' ) ) {
			value = iframe.src;
			const w = iframe.width;
			const h = iframe.height;
			if ( w && h ) {
				props.setAttributes( {
					aspect_ratio: aspectRatio( w, h ),
				} );
			}
		}
	}
}
const mediaUploadRender = ( open: VoidFunction, val, url ) => {
	return (
		// @ts-ignore
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
					<div
						style={ {
							overflow: 'hidden',
						} }
					>
						{ /* @ts-ignore */ }
						<ResponsiveWrapper naturalWidth={ 640 } naturalHeight={ 360 } isInline>
							{ /* @ts-ignore */ }
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
			{ /* @ts-ignore */ }
			<DropZone />
		</div>
	);
};

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
			<>
				{ 'boolean' === option.type && (
					// @ts-ignore
					<ToggleControl
						key={ key }
						label={ option.label }
						// @ts-ignore
						help={ createHelp( option ) }
						checked={ !! val }
						onChange={ ( value ) => {
							return props.setAttributes( {
								[ key ]: value,
							} );
						} }
					/>
				) }
				{ 'select' === option.type && (
					<SelectControl
						key={ key }
						value={ val }
						label={ option.label }
						// @ts-ignore
						help={ createHelp( option ) }
						options={ PrepareSelectOptions( option.options ) }
						onChange={ ( value ) => {
							return props.setAttributes( {
								[ key ]: value,
							} );
						} }
					/>
				) }
				{ 'string' === option.type && (
					// @ts-ignore
					<TextControl
						key={ key }
						label={ option.label }
						placeholder={ option.placeholder }
						// @ts-ignore
						help={ createHelp( option ) }
						value={ val }
						onChange={ ( value ) => {
							maybeSetAspectRatio( key, value, props );
							return props.setAttributes( {
								[ key ]: value,
							} );
						} }
					/>
				) }
				{ 'attachment' === option.type && (
					// @ts-ignore
					<BaseControl
						key={ key }
						className="editor-post-featured-image"
						// @ts-ignore
						help={ createHelp( option ) }
					>
						{ /* @ts-ignore */ }
						<MediaUploadCheck
							// @ts-ignore
							fallback={ mediaUploadInstructions }
						>
							{ /* @ts-ignore */ }
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
								// @ts-ignore
								render={ ( { open } ) => {
									return mediaUploadRender( open, val, url );
								} }
								value={ val }
							/>
						</MediaUploadCheck>
						{ !! val && !! url && (
							// @ts-ignore
							<MediaUploadCheck>
								{ /* @ts-ignore */ }
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
										// @ts-ignore
										<Button onClick={ open } variant="secondary">
											{ __( 'Replace Thumbnail' ) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
						) }
						{ !! val && (
							// @ts-ignore
							<MediaUploadCheck>
								{ /* @ts-ignore */ }
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
			</>
		);
	} );

	let open = true;

	sectionControls.main.push(
		<BaseControl
			key={ 'info' }
			help={ __(
				'You can disable the extensive help texts on the ARVE settings page to clean up this UI',
				'advanced-responsive-video-embedder'
			) }
		>
			{ /* @ts-ignore */ }
			<BaseControl.VisualLabel>
				{ __( 'Info', 'advanced-responsive-video-embedder' ) }
			</BaseControl.VisualLabel>
		</BaseControl>
	);

	Object.keys( sectionControls ).forEach( ( key ) => {
		controls.push(
			// @ts-ignore
			<PanelBody key={ key } title={ capitalizeFirstLetter( key ) } initialOpen={ open }>
				{ sectionControls[ key ] }
			</PanelBody>
		);
		open = false;
	} );

	return controls;
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

function capitalizeFirstLetter( string ) {
	return string.charAt( 0 ).toUpperCase() + string.slice( 1 );
}

function Edit( props ) {
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
				{ /* @ts-ignore */ }
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
			{ /* @ts-ignore */ }
			<InspectorControls key="insp">
				{ /* @ts-ignore */ }
				{ buildControls( props ) }
			</InspectorControls>
		</>
	);
}

// @ts-ignore
registerBlockType( name, {
	edit: Edit,
} );

function aspectRatio( w, h ) {
	const arGCD = gcd( w, h );

	return w / arGCD + ':' + h / arGCD;
}

function gcd( a, b ) {
	if ( ! b ) {
		return a;
	}

	return gcd( b, a % b );
}

/*
wp.data.dispatch( 'core/edit-post' ).hideBlockTypes( [
	'core-embed/youtube',
	'core-embed/vimeo',
	'core-embed/dailymotion',
	'core-embed/collegehumor',
	'core-embed/ted',
] );
*/

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
