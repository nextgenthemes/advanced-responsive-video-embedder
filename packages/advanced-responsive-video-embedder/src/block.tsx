/**
 * Copyright (c) 2019-2021 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright (c) 2019 Gary Pendergast
 * License: GPL 2.0+
 */
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';
import { createElement, Fragment } from '@wordpress/element';
import {
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	TextControl,
	Button,
	ToggleControl,
	SelectControl,
	PanelBody,
	ResponsiveWrapper,
	DropZone,
} from '@wordpress/components';

export { };
declare global {
	interface Window {
		wp;
		ARVEsettings: Record<string, OptionProps>;
	}
}

const settings = window.ARVEsettings;
const wp = window.wp;
const domParser = new DOMParser();

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
 * Keypair to gutenberg component
 */
function PrepareSelectOptions(options: OptionProps) {
	const gboptions = [] as Array<SelectOption>;

	Object.entries(options).forEach(([key, value]) => {
		const o: SelectOption = {
			label: value,
			value: key,
		};

		gboptions.push(o);
	});

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

function maybeSetAspectRatio(key: string, value: string, props) {
	if ('url' === key) {
		const iframe = domParser
			.parseFromString(value, 'text/html')
			.querySelector('iframe');
		if (iframe && iframe.getAttribute('src')) {
			value = iframe.src;
			const w = iframe.width;
			const h = iframe.height;
			if (w && h) {
				props.setAttributes({
					aspect_ratio: aspectRatio(w, h),
				});
			}
		}
	}
}

function BuildControls(props) {
	const controls = [] as Array<JSX.Element>;
	const sectionControls = {};
	const mediaUploadInstructions = (
		<p>{__('To edit the featured image, you need permission to upload media.')}</p>
	);
	let selectedMedia = false as any;

	Object.values(settings).forEach((option: OptionProps) => {
		sectionControls[option.tag] = [];
	});

	Object.entries(settings).forEach(([key, option]: [string, OptionProps]) => {
		let val = props.attributes[key];
		let url = '';

		switch (option.type) {
			case 'boolean':
				if ('sandbox' === key && typeof val === 'undefined') {
					val = true;
				}
				sectionControls[option.tag].push(
					<ToggleControl
						key={key}
						label={option.label}
						help={createHelp(option)}
						checked={!!val}
						onChange={(value) => {
							return props.setAttributes({ [key]: value });
						}}
					/>
				);

				break;
			case 'select':
				sectionControls[option.tag].push(
					<SelectControl
						key={key}
						value={val}
						label={option.label}
						help={createHelp(option)}
						options={PrepareSelectOptions(option.options)}
						onChange={(value) => {
							return props.setAttributes({ [key]: value });
						}}
					/>
				);
				break;
			case 'string':
				sectionControls[option.tag].push(
					<TextControl
						key={key}
						label={option.label}
						placeholder={option.placeholder}
						help={createHelp(option)}
						value={val}
						onChange={(value) => {
							maybeSetAspectRatio(key, value, props);
							return props.setAttributes({ [key]: value });
						}}
					/>
				);
				break;
			case 'attachment_old':
				url = props.attributes[key + '_url'];

				sectionControls[option.tag].push(
					<div>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={(media) => {
									return props.setAttributes({
										[key]: media.id.toString(),
										[key + '_url']: media.url,
									});
								}}
								allowedTypes="image"
								render={({ open }) => (
									<Button
										className="components-button--arve-thumbnail"
										onClick={open}
										aria-label={__('Edit or update the image')}
									>
										{!!url && (
											<div>
												<img
													src={url}
													alt={__('Selected Thumbnail')}
												/>
											</div>
										)}
										{__('Edit or update the image')}
									</Button>
								)}
							/>
						</MediaUploadCheck>
						{!!val && (
							<Button
								onClick={() => {
									return props.setAttributes({
										[key]: '',
										[key + '_url']: '',
									});
								}}
							>
								{__('Remove Custom Thumbnail')}
							</Button>
						)}
						<TextControl
							label={option.label}
							placeholder={option.placeholder}
							help={createHelp(option)}
							value={val}
							onChange={(value) => {
								return props.setAttributes({ [key]: value });
							}}
						/>
					</div>
				);
				break;
			case 'attachment':
				url = props.attributes[key + '_url'];

				sectionControls[option.tag].push(
					<div className="editor-post-featured-image">
						<MediaUploadCheck fallback={mediaUploadInstructions}>
							<MediaUpload
								title={__('Thumbnail')}
								onSelect={(media) => {
									selectedMedia = media;
									return props.setAttributes({
										[key]: media.id.toString(),
										[key + '_url']: media.url,
									});
								}}
								unstableFeaturedImageFlow
								allowedTypes="Image"
								modalClass="editor-post-featured-image__media-modal"
								render={({ open }) => (
									<div className="editor-post-featured-image__container">
										<Button
											className={
												!val
													? 'editor-post-featured-image__toggle'
													: 'editor-post-featured-image__preview'
											}
											onClick={open}
											// aria-label={
											// 	!val
											// 		? null
											// 		: __('Edit or update the image')
											// }
											aria-describedby={
												!val
													? ''
													: `editor-post-featured-image-${val}-describedby`
											}
										>
											{!!val && !!url && (
												<ResponsiveWrapper
													naturalWidth={640}
													naturalHeight={380}
												>
													<img src={url} alt="" />
												</ResponsiveWrapper>
											)}
											{!val && __('Set Thumbnail')}
										</Button>
										<DropZone />
									</div>
								)}
								value={val}
							/>
						</MediaUploadCheck>
						{!!val && !!url && (
							<MediaUploadCheck>
								<MediaUpload
									title={__('Thumbnail')}
									onSelect={(media) => {
										selectedMedia = media;
										return props.setAttributes({
											[key]: media.id.toString(),
											[key + '_url']: media.url,
										});
									}}
									unstableFeaturedImageFlow
									allowedTypes="image"
									modalClass="editor-post-featured-image__media-modal"
									render={({ open }) => (
										<Button onClick={open} isSecondary>
											{__('Replace Thumbnail')}
										</Button>
									)}
								/>
							</MediaUploadCheck>
						)}
						{!!val && (
							<MediaUploadCheck>
								<Button
									onClick={() => {
										return props.setAttributes({
											[key]: '',
											[key + '_url']: '',
										});
									}}
									isLink
									isDestructive
								>
									{__('Remove Thumbnail')}
								</Button>
							</MediaUploadCheck>
						)}
						<TextControl
							label={option.label}
							placeholder={option.placeholder}
							help={createHelp(option)}
							value={val}
							onChange={(value) => {
								return props.setAttributes({ [key]: value });
							}}
						/>
					</div>
				);
				break;
		}
	});

	let open = true;

	Object.keys(sectionControls).forEach((key) => {
		controls.push(
			<PanelBody key={key} title={capitalizeFirstLetter(key)} initialOpen={open}>
				{sectionControls[key]}
			</PanelBody>
		);
		open = false;
	});

	return controls;
}

function createHelp(option: OptionProps) {
	if (typeof option.description !== 'string') {
		return '';
	}

	if (typeof option.descriptionlinktext === 'string') {
		const textSplit = option.description.split(option.descriptionlinktext);

		return (
			<span>
				<span>{textSplit[0]}</span>
				<a href={option.descriptionlink}>{option.descriptionlinktext}</a>,
				<span>{textSplit[1]}</span>
			</span>
		);
	}
	return option.description;
}

function capitalizeFirstLetter(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

/*
 * Here's where we register the block in JavaScript.
 *
 * It's not yet possible to register a block entirely without JavaScript, but
 * that is something I'd love to see happen. This is a barebones example
 * of registering the block, and giving the basic ability to edit the block
 * attributes. (In this case, there's only one attribute, 'foo'.)
 */
wp.blocks.registerBlockType('nextgenthemes/arve-block', {
	title: 'Video Embed (ARVE)',
	description:
		'You can disable help texts on the ARVE settings page to clean up the UI',
	icon: 'video-alt3',
	category: 'embed',
	supports: {
		AlignWide: true,
		align: ['left', 'right', 'center', 'wide', 'full'],
	},

	/*
	 * In most other blocks, you'd see an 'attributes' property being defined here.
	 * We've defined attributes in the PHP, that information is automatically sent
	 * to the block editor, so we don't need to redefine it here.
	 */

	edit: (props) => {
		const controls = BuildControls(props);
		/*  */
		return [
			/*
			 * The ServerSideRender element uses the REST API to automatically call
			 * php_block_render() in your PHP code whenever it needs to get an updated
			 * view of the block.
			 */
			<ServerSideRender
				key="ssr"
				block="nextgenthemes/arve-block"
				attributes={props.attributes}
			/>,
			/*
			 * InspectorControls lets you add controls to the Block sidebar. In this case,
			 * we're adding a TextControl, which lets us edit the 'foo' attribute (which
			 * we defined in the PHP). The onChange property is a little bit of magic to tell
			 * the block editor to update the value of our 'foo' property, and to re-render
			 * the block.
			 */
			<InspectorControls key="insp">{controls}</InspectorControls>,
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: () => {
		return null;
	},
});

function aspectRatio(w, h) {
	const arGCD = gcd(w, h);

	return w / arGCD + ':' + h / arGCD;
}

function gcd(a, b) {
	if (!b) {
		return a;
	}

	return gcd(b, a % b);
}
