/* eslint-disable @wordpress/no-unsafe-wp-apis */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls, store as blockEditorStore } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
	BaseControl,
} from '@wordpress/components';
import { subscribe, select, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

import ImageUpload from '../block/components/ImageUpload';
import UrlOrEmbedCode from './components/UrlOrEmbedCode';
import { hasSameKeys } from '../block/utils';

declare global {
	interface Window {
		ArveEmbedBlockExtData: {
			settingPageUrl: string;
			options: Record<string, string>;
			settings: Record<string, any>;
			gutenbergActive: boolean;
		};
	}
}

const { settingPageUrl, options, settings, gutenbergActive } = window.ArveEmbedBlockExtData;
const { gutenberg_help: gutenbergHelp } = options;

function createHelp(html?: string): undefined | string | JSX.Element {
	if (!gutenbergHelp || !html) {
		return undefined;
	}

	if (!html.match(/<a/i)) {
		return html;
	}

	const doc = new DOMParser().parseFromString(html, 'text/html');
	const result: (string | JSX.Element)[] = [];
	let key = 1;

	const walk = (node: Node) => {
		if (node.nodeType === Node.TEXT_NODE) {
			const text = node.textContent;
			if (text !== null && text !== undefined) {
				result.push(text);
			}
		} else if (node.nodeType === Node.ELEMENT_NODE) {
			const el = node as HTMLElement;
			if (el.tagName === 'A') {
				const a = el as HTMLAnchorElement;
				const linkText = a.textContent || '';
				result.push(
					<a href={a.href} target="_blank" rel="noreferrer" key={'link-' + key}>
						{linkText}
					</a>
				);
				key++;
				return;
			}
			Array.from(el.childNodes).forEach(walk);
		}
	};

	walk(doc.body);
	return <>{result}</>;
}

function prepareSelectOptions(opts: Record<string, string>): GutenbergSelectOption[] {
	return Object.entries(opts).map(([value, label]) => ({
		label,
		value,
	}));
}

function shouldHide(settingKey: string, attributes: Record<string, unknown>): boolean {
	if ('align' === settingKey) {
		return true;
	}

	const setting = settings[settingKey];

	if (!setting?.depends?.length) {
		return false;
	}

	const hide = !setting.depends.some((condition) => {
		const [key, value] = Object.entries(condition)[0] || [];

		if (!attributes[key]) {
			return true;
		}

		return key !== undefined && attributes[key] === value;
	});

	return hide;
}

function buildControls({ attributes, setAttributes }: BuildControlsProps): JSX.Element[] {
	const controls: JSX.Element[] = [];
	const sectionControls: Record<string, JSX.Element[]> = {};

	Object.values(settings).forEach((setting) => {
		sectionControls[setting.category] = [];
	});

	Object.entries(settings).forEach(([sKey, setting]) => {
		const val = attributes[sKey];
		const url = (attributes[`${sKey}_url`] as string) || '';
		const tab = setting.category || 'no-category';

		if (shouldHide(sKey, attributes)) {
			return;
		}

		const settingOptions = setting.options || {};
		const boolWithDefaultKeys = ['', 'true', 'false'] as const;

		if (hasSameKeys(settingOptions, boolWithDefaultKeys)) {
			sectionControls[tab].push(
				<ToggleGroupControl
					key={sKey}
					label={setting.label}
					value={(val as string) || ''}
					isBlock
					__next40pxDefaultSize
					onChange={(value) => setAttributes({ [sKey]: value })}
					help={createHelp(setting.description)}
				>
					<ToggleGroupControlOption
						value=""
						label={__('Default', 'advanced-responsive-video-embedder')}
					/>
					<ToggleGroupControlOption
						value="true"
						label={__('True', 'advanced-responsive-video-embedder')}
					/>
					<ToggleGroupControlOption
						value="false"
						label={__('False', 'advanced-responsive-video-embedder')}
					/>
				</ToggleGroupControl>
			);
		} else if ('url' === sKey) {
			sectionControls[tab].push(
				<UrlOrEmbedCode
					key={sKey}
					label={setting.label}
					value={(val as string) || ''}
					onChange={(value: string) => setAttributes({ [sKey]: value })}
					onAspectRatioChange={(ratio: string) => setAttributes({ aspect_ratio: ratio })}
					placeholder={setting.placeholder}
					help={createHelp(setting.description)}
				/>
			);
		} else if (setting.ui === 'image_upload') {
			sectionControls[tab].push(
				<ImageUpload
					key={sKey}
					sKey={sKey}
					className={`arve-ctl-${setting.tab}`}
					val={(val as number) || undefined}
					url={url}
					help={createHelp(setting.description)}
					setAttributes={setAttributes}
				/>
			);
		} else if (setting.ui_element === 'select') {
			const selectOptions = prepareSelectOptions(setting.options as Record<string, string>);
			sectionControls[tab].push(
				<SelectControl
					key={sKey}
					className={`arve-ctl-${setting.tab}`}
					label={setting.label}
					value={val as string}
					options={selectOptions}
					onChange={(value: string) => setAttributes({ [sKey]: value })}
					help={createHelp(setting.description)}
				/>
			);
		} else if (setting.ui_element_type === 'checkbox') {
			sectionControls[tab].push(
				<ToggleControl
					key={sKey}
					className={`arve-ctl-${setting.tab}`}
					label={setting.label}
					checked={Boolean(val)}
					onChange={(value: boolean) => setAttributes({ [sKey]: value })}
					help={createHelp(setting.description)}
				/>
			);
		} else {
			sectionControls[tab].push(
				<TextControl
					key={sKey}
					className={`arve-ctl-${setting.tab}`}
					label={setting.label}
					type={setting.ui_element_type}
					value={(val as string) || ''}
					placeholder={setting.placeholder}
					onChange={(value: string) => setAttributes({ [sKey]: value })}
					help={createHelp(setting.description)}
				/>
			);
		}
	});

	if (gutenbergHelp || gutenbergActive) {
		sectionControls.main.push(
			<BaseControl
				key="info-panel"
				help={
					<>
						{gutenbergHelp && (
							<>
								{__(
									'Remember changing the defaults is possible on the',
									'advanced-responsive-video-embedder'
								)}{' '}
								<a href={settingPageUrl} target="_blank" rel="noreferrer">
									{__('Settings page', 'advanced-responsive-video-embedder')}
								</a>
								{'. '}
								{__(
									'You can also disable the extensive help texts there to clean up this UI.',
									'advanced-responsive-video-embedder'
								)}
							</>
						)}
						{gutenbergActive && (
							<>
								{' '}
								{__(
									'Error 153 in YouTube embeds, is a known issue with the Gutenberg plugin active and effects only the editor and normal mode. Your Videos will work fine on the front-end. Lazyload is not effected.',
									'advanced-responsive-video-embedder'
								)}
							</>
						)}
					</>
				}
			>
				<BaseControl.VisualLabel>
					{__('Info', 'advanced-responsive-video-embedder')}
				</BaseControl.VisualLabel>
			</BaseControl>
		);
	}

	const categories = {
		main: __('Main', 'advanced-responsive-video-embedder'),
		lazyloadAndLightbox: __('Lazyload & Lightbox', 'advanced-responsive-video-embedder'),
		lightbox: __('Lightbox', 'advanced-responsive-video-embedder'),
		data: __('Data', 'advanced-responsive-video-embedder'),
		stickyVideos: __('Sticky Videos', 'advanced-responsive-video-embedder'),
		functional: __('Functional', 'advanced-responsive-video-embedder'),
		privacy: __('Privacy', 'advanced-responsive-video-embedder'),
		misc: __('Misc', 'advanced-responsive-video-embedder'),
	};

	Object.entries(sectionControls).forEach(([tab, tabControls]) => {
		if (tabControls.length > 0) {
			controls.push(
				<PanelBody key={tab} title={categories[tab] ?? tab} initialOpen={'main' === tab}>
					{tabControls}
				</PanelBody>
			);
		}
	});

	return controls;
}

/**
 * Registry mapping embed URLs to their ARVE settings.
 * Kept in sync whenever the block editor data changes.
 */
const arveRegistry: Record<string, Record<string, any>> = {};

let prevArveRegistry: Record<string, Record<string, any>> | null = null;

let syncTimeout: ReturnType<typeof setTimeout> | undefined;
function syncArveRegistry() {
	clearTimeout(syncTimeout);
	syncTimeout = setTimeout(() => {
		try {
			const blocks = select(blockEditorStore).getBlocks();
			const embeds = blocks.filter((b) => b.name === 'core/embed' && b.attributes.url);
			const next: Record<string, Record<string, any>> = {};
			for (const block of embeds) {
				if (typeof block?.attributes?.url === 'string' && block.attributes.url.trim()) {
					next[block.attributes.url] = block.attributes.arve || {};
				}
			}
			Object.assign(arveRegistry, next);
			const currentUrls = new Set(embeds.map((b) => b.attributes.url));
			for (const url of Object.keys(arveRegistry)) {
				if (!currentUrls.has(url)) {
					delete arveRegistry[url];
				}
			}

			if (prevArveRegistry !== null) {
				for (const [url, currArve] of Object.entries(next)) {
					const prevArve = prevArveRegistry[url] || {};
					if (JSON.stringify(currArve) !== JSON.stringify(prevArve)) {
						try {
							dispatch('core').invalidateResolution('getEmbedPreview', [url]);
						} catch (e) {
							// Core data store may not be available.
						}
						break;
					}
				}
			}

			prevArveRegistry = JSON.parse(JSON.stringify(next));
		} catch (e) {
			// Block editor store not yet available.
		}
	}, 100);
}

subscribe(syncArveRegistry, blockEditorStore);

apiFetch.use((proxyOptions, next) => {
	if (proxyOptions.path?.startsWith('/oembed/1.0/proxy')) {
		const params = new URLSearchParams(proxyOptions.path.split('?')[1] || '');
		const url = params.get('url');

		if (url && arveRegistry[url]) {
			params.set('arve', JSON.stringify(arveRegistry[url]));
			proxyOptions.path = proxyOptions.path.split('?')[0] + '?' + params.toString();
		}
	}

	return next(proxyOptions);
});

addFilter('blocks.registerBlockType', 'embed-block-extension/attribute', (regSettings, name) => {
	if (name !== 'core/embed') {
		return regSettings;
	}

	regSettings.attributes = {
		...regSettings.attributes,
		arve: {
			type: 'object',
			default: {},
		},
	};

	return regSettings;
});

const withArveControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		if (props.name !== 'core/embed') {
			return <BlockEdit {...props} />;
		}

		const { attributes, setAttributes } = props;
		const arve = attributes.arve || {};

		const setArveAttributes = ( attrs: Record< string, unknown > ) => {
			const topLevel: Record< string, unknown > = {};
			const arveLevel: Record< string, unknown > = {};
			for ( const [ key, value ] of Object.entries( attrs ) ) {
				if ( key === 'url' ) {
					topLevel[ key ] = value;
				} else {
					arveLevel[ key ] = value;
				}
			}
			const updates: Record< string, unknown > = {};
			if ( Object.keys( arveLevel ).length > 0 ) {
				updates.arve = { ...arve, ...arveLevel };
			}
			Object.assign( updates, topLevel );
			setAttributes( updates );
		};

		return (
			<>
				<BlockEdit {...props} />
				<InspectorControls>
					{buildControls({ attributes: { ...arve }, setAttributes: setArveAttributes })}
				</InspectorControls>
			</>
		);
	};
}, 'withArveControls');

addFilter('editor.BlockEdit', 'embed-block-extension/controls', withArveControls);
