/**
 * Copyright 2019-2022 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright 2019 Gary Pendergast
 * License: GPL 2.0+
 */
import json from './block.json';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
//import { createElement, Fragment } from '@wordpress/element';
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

export {};
declare global {
	interface Window {
		ARVEsettings: Record< string, OptionProps >;
	}
}

const { name } = json;
const settings = window.ARVEsettings;
const domParser = new DOMParser();

/*
 * Keypair to gutenberg component
 */
function PrepareSelectOptions( options: OptionProps ) {
	const gboptions = [] as Array< SelectOption >;

	Object.entries( options ).forEach( ( [ key, value ] ) => {
		const o: SelectOption = {
			label: value,
			value: key,
		};

		gboptions.push( o );
	} );

	return gboptions;
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

function maybeSetAspectRatio( key: string, value: string, props ) {
	if ( 'url' === key ) {
		const iframe = domParser
			.parseFromString( value, 'text/html' )
			.querySelector( 'iframe' );
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

function BuildControls( props ) {
	const controls = [] as Array< JSX.Element >;
	const sectionControls = {};
	const mediaUploadInstructions = (
		<p>
			{ __(
				'To edit the featured image, you need permission to upload media.'
			) }
		</p>
	);
	let selectedMedia = false as any;

	Object.values( settings ).forEach( ( option: OptionProps ) => {
		sectionControls[ option.tag ] = [];
	} );

	Object.entries( settings ).forEach(
		( [ key, option ]: [ string, OptionProps ] ) => {
			let val = props.attributes[ key ];
			let url = '';

			switch ( option.type ) {
				case 'boolean':
					if ( 'sandbox' === key && typeof val === 'undefined' ) {
						val = true;
					}
					sectionControls[ option.tag ].push(
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
					);

					break;
				case 'select':
					sectionControls[ option.tag ].push(
						<SelectControl
							key={ key }
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
					);
					break;
				case 'string':
					sectionControls[ option.tag ].push(
						<TextControl
							key={ key }
							label={ option.label }
							placeholder={ option.placeholder }
							help={ createHelp( option ) }
							value={ val }
							onChange={ ( value ) => {
								maybeSetAspectRatio( key, value, props );
								return props.setAttributes( {
									[ key ]: value,
								} );
							} }
						/>
					);
					break;
				case 'attachment':
					url = props.attributes[ key + '_url' ];

					sectionControls[ option.tag ].push(
						<BaseControl
							className="editor-post-featured-image"
							help={ createHelp( option ) }
							key={ key }
						>
							<MediaUploadCheck
								fallback={ mediaUploadInstructions }
							>
								<MediaUpload
									title={ __( 'Thumbnail' ) }
									onSelect={ ( media ) => {
										selectedMedia = media;
										return props.setAttributes( {
											[ key ]: media.id.toString(),
											[ key + '_url' ]: media.url,
										} );
									} }
									unstableFeaturedImageFlow
									allowedTypes={ [ 'image' ] }
									modalClass="editor-post-featured-image__media-modal"
									render={ ( { open } ) => (
										<div className="editor-post-featured-image__container">
											<Button
												className={
													! val
														? 'editor-post-featured-image__toggle'
														: 'editor-post-featured-image__preview'
												}
												onClick={ open }
												aria-label={
													! val
														? null
														: __(
																'Edit or update the image'
														  )
												}
												aria-describedby={
													! val
														? ''
														: `editor-post-featured-image-${ val }-describedby`
												}
											>
												{ !! val && !! url && (
													<div
														style={ {
															overflow: 'hidden',
														} }
													>
														<ResponsiveWrapper
															naturalWidth={ 640 }
															naturalHeight={
																360
															}
															isInline
														>
															<img
																src={ url }
																alt="ARVE Thumbnail"
																style={ {
																	width: '100%',
																	height: '100%',
																	objectFit:
																		'cover',
																} }
															/>
														</ResponsiveWrapper>
													</div>
												) }
												{ ! val &&
													__( 'Set Thumbnail' ) }
											</Button>
											<DropZone />
										</div>
									) }
									value={ val }
								/>
							</MediaUploadCheck>
							{ !! val && !! url && (
								<MediaUploadCheck>
									<MediaUpload
										title={ __( 'Thumbnail' ) }
										onSelect={ ( media ) => {
											selectedMedia = media;
											return props.setAttributes( {
												[ key ]: media.id.toString(),
												[ key + '_url' ]: media.url,
											} );
										} }
										unstableFeaturedImageFlow
										allowedTypes={ [ 'image' ] }
										modalClass="editor-post-featured-image__media-modal"
										render={ ( { open } ) => (
											<Button
												onClick={ open }
												isSecondary
											>
												{ __( 'Replace Thumbnail' ) }
											</Button>
										) }
									/>
								</MediaUploadCheck>
							) }
							{ !! val && (
								<MediaUploadCheck>
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
					);
					break;
			}
		}
	);

	let open = true;

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
			<PanelBody
				key={ key }
				title={ capitalizeFirstLetter( key ) }
				initialOpen={ open }
			>
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
		const textSplit = option.description.split(
			option.descriptionlinktext
		);

		return (
			<span>
				<span>{ textSplit[ 0 ] }</span>
				<a href={ option.descriptionlink }>
					{ option.descriptionlinktext }
				</a>
				,<span>{ textSplit[ 1 ] }</span>
			</span>
		);
	}
	return option.description;
}

function capitalizeFirstLetter( string ) {
	return string.charAt( 0 ).toUpperCase() + string.slice( 1 );
}

function Edit( props ) {
	const {
		attributes: { align },
		setAttributes,
	} = props;

	const blockProps = useBlockProps();
	const controls = BuildControls( props );

	return [
		<div { ...blockProps } key="block">
			<div
				className="arve-select-helper"
				style={ { textAlign: 'center', padding: '.1em' } }
			>
				{ __(
					'Select ARVE block',
					'advanced-responsive-video-embedder'
				) }
			</div>
			<ServerSideRender
				block="nextgenthemes/arve-block"
				attributes={ props.attributes }
			/>
		</div>,
		<InspectorControls key="insp">{ controls }</InspectorControls>,
	];
}

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
