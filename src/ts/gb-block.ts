/**
 * Copyright (c) 2019 Nicolas Jonas
 * License: GPL 3.0
 *
 * Based on: https://gist.github.com/pento/cf38fd73ce0f13fcf0f0ae7d6c4b685d
 * Copyright (c) 2019 Gary Pendergast
 * License: GPL 2.0+
 */

export {};
declare global {
	interface Window {
		wp;
		ARVEsettings;
	}
}

const settings = window.ARVEsettings;
const wp = window.wp;
// eslint-disable-next-line
const el = window.wp.element.createElement as Function;

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
function PrepareSelectOptions(options) {
	const gboptions = [] as Array<Record<string, unknown>>;

	Object.entries(options).forEach(([key, value]) => {
		gboptions.push({
			label: value,
			value: key,
		});
	});

	return gboptions;
}

interface Option {
	label: string;
	tag: string;
	type: string;
	description: string;
	descriptionlink: string;
	descriptionlinktext: string;
	placeholder: string;
	options: Array<unknown>;
}

interface GBControlArgs {
	help: string;
	label: string;
	value: string;
	placeholder: string;
	checked: boolean;
	children;
	options;
	selected;
	onChange;
}

function BuildControls(props) {
	const controls = [] as Array<unknown>;
	const sectionControls = {};
	// eslint-disable-next-line
	const domParser = new DOMParser();

	Object.values(settings).forEach((option: Option) => {
		sectionControls[option.tag] = [];
	});

	Object.entries(settings).forEach(([key, option]: [string, Option]) => {
		const attrVal = props.attributes[key];

		const ctrlArgs = {} as GBControlArgs;

		ctrlArgs.label = option.label;
		ctrlArgs.onChange = (value) => {
			if ('url' === key) {
				const iframe = domParser
					.parseFromString(value, 'text/html')
					.querySelector('iframe');
				if (iframe && iframe.hasAttribute('src') && iframe.getAttribute('src')) {
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
			props.setAttributes({ [key]: value });
		};

		if (typeof option.description === 'string') {
			ctrlArgs.help = option.description;

			if (typeof option.descriptionlinktext === 'string') {
				const textSplit = option.description.split(option.descriptionlinktext);

				ctrlArgs.help = el(
					'span',
					null,
					el('span', {}, textSplit[0]),
					el('a', { href: option.descriptionlink }, option.descriptionlinktext),
					el('span', {}, textSplit[1])
				);
			}
		}

		let urlVal = props.attributes[key + '_url'];
		if (typeof urlVal === 'undefined') {
			urlVal = '';
		}

		switch (option.type) {
			case 'boolean':
				if ('sandbox' === key && typeof attrVal === 'undefined') {
					ctrlArgs.checked = true;
				}
				if (typeof attrVal !== 'undefined') {
					ctrlArgs.checked = attrVal;
				}
				sectionControls[option.tag].push(el(wp.components.ToggleControl, ctrlArgs));
				break;
			case 'select':
				if (typeof attrVal !== 'undefined') {
					ctrlArgs.selected = attrVal;
					ctrlArgs.value = attrVal;
				}
				ctrlArgs.options = PrepareSelectOptions(option.options);
				sectionControls[option.tag].push(el(wp.components.SelectControl, ctrlArgs));
				break;
			case 'string':
				if (typeof attrVal !== 'undefined') {
					ctrlArgs.value = attrVal;
				}
				ctrlArgs.placeholder = option.placeholder;
				sectionControls[option.tag].push(el(wp.components.TextControl, ctrlArgs));
				break;
			case 'attachment':
				ctrlArgs.children = [
					el(wp.editor.MediaUpload, {
						type: 'image',
						onSelect: (media) => {
							return props.setAttributes({
								[key]: media.id.toString(),
								[key + '_url']: media.url,
							});
						},
						render: (obj) => {
							return el(
								wp.components.Button,
								{
									className:
										'components-icon-button image-block-btn is-button is-default is-large',
									onClick: obj.open,
								},
								el(
									'svg',
									{
										className: 'dashicon dashicons-edit',
										width: '20',
										height: '20',
									},
									el('path', {
										d:
											'M2.25 1h15.5c.69 0 1.25.56 1.25 1.25v15.5c0 .69-.56 1.25-1.25 1.25H2.25C1.56 19 1 18.44 1 17.75V2.25C1 1.56 1.56 1 2.25 1zM17 17V3H3v14h14zM10 6c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm3 5s0-6 3-6v10c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V8c2 0 3 4 3 4s1-3 3-3 3 2 3 2z',
									})
								),
								el('span', {}, ' Select image')
							); // end el button
						},
					}),
					el('img', {
						src: urlVal,
						alt: 'thumbnail',
					}),
				];

				sectionControls[option.tag].push(el(wp.components.BaseControl, ctrlArgs));
				break;
		}
	});

	let open = true;

	Object.keys(sectionControls).forEach((key) => {
		controls.push(
			el(
				window.wp.components.PanelBody,
				{
					title: key,
					initialOpen: open,
				},
				...sectionControls[key]
			)
		);
		open = false;
	});

	return controls;
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
	icon: 'video-alt3',
	category: 'embed',

	/*
	 * In most other blocks, you'd see an 'attributes' property being defined here.
	 * We've defined attributes in the PHP, that information is automatically sent
	 * to the block editor, so we don't need to redefine it here.
	 */

	edit: (props) => {
		const controls = BuildControls(props);

		return [
			/*
			 * The ServerSideRender element uses the REST API to automatically call
			 * php_block_render() in your PHP code whenever it needs to get an updated
			 * view of the block.
			 */
			el(wp.components.ServerSideRender, {
				block: 'nextgenthemes/arve-block',
				attributes: props.attributes,
			}),

			/*
			 * InspectorControls lets you add controls to the Block sidebar. In this case,
			 * we're adding a TextControl, which lets us edit the 'foo' attribute (which
			 * we defined in the PHP). The onChange property is a little bit of magic to tell
			 * the block editor to update the value of our 'foo' property, and to re-render
			 * the block.
			 */
			el(wp.blockEditor.InspectorControls, {}, ...controls),
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
