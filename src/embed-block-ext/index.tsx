import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls, store as blockEditorStore } from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	__experimentalNumberControl as NumberControl,
	ToggleControl,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import { subscribe, select, dispatch } from '@wordpress/data';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';

/**
 * Registry mapping embed URLs to their ARVE settings.
 * Kept in sync whenever the block editor data changes.
 */
const arveRegistry = {};

/** Previous ARVE registry snapshot for detecting changes that need a re-fetch. */
let prevArveRegistry = null;

let syncTimeout: ReturnType<typeof setTimeout> | undefined;
function syncArveRegistry() {
	clearTimeout(syncTimeout);
	syncTimeout = setTimeout(() => {
		try {
			const blocks = select(blockEditorStore).getBlocks();
			const embeds = blocks.filter((b) => b.name === 'core/embed' && b.attributes.url);
			const next = {};
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

apiFetch.use((options, next) => {
	if (options.path?.startsWith('/oembed/1.0/proxy')) {
		const params = new URLSearchParams(options.path.split('?')[1] || '');
		const url = params.get('url');

		if (url && arveRegistry[url]) {
			params.set('arve', JSON.stringify(arveRegistry[url]));
			options.path = options.path.split('?')[0] + '?' + params.toString();
		}
	}

	return next(options);
});

/**
 * Register the 'arve' attribute on core/embed.
 */
addFilter('blocks.registerBlockType', 'embed-block-extension/attribute', (settings, name) => {
	if (name !== 'core/embed') {
		return settings;
	}

	settings.attributes = {
		...settings.attributes,
		arve: {
			type: 'object',
			default: {},
		},
	};

	return settings;
});

/**
 * Add ARVE sidebar controls to the embed block.
 */
const withArveControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		if (props.name !== 'core/embed') {
			return <BlockEdit {...props} />;
		}

		const { attributes, setAttributes, isSelected } = props;
		const arve = attributes.arve || {};

		const updateArve = (key, value) => {
			setAttributes({ arve: { ...arve, [key]: value } });
		};

		return (
			<>
				<BlockEdit {...props} />
				{isSelected && (
					<InspectorControls>
						<PanelBody
							title={__('ARVE Settings', 'embed-block-extension')}
							initialOpen={true}
						>
							<TextControl
								label={__('Extra Parameters', 'embed-block-extension')}
								help={__(
									'Query string params to append to the embed URL (e.g. modestbranding=1&rel=0)',
									'embed-block-extension'
								)}
								value={arve.parameters || ''}
								onChange={(val) => updateArve('parameters', val)}
							/>

							<NumberControl
								label={__('Max Width (px)', 'embed-block-extension')}
								help={__(
									'Maximum container width in pixels. Leave empty for full width.',
									'embed-block-extension'
								)}
								value={arve.maxwidth ?? ''}
								onChange={(val) =>
									updateArve('maxwidth', val ? Number(val) : undefined)
								}
								min={0}
								max={1920}
								step={10}
								isShiftStepEnabled={true}
								shiftStep={100}
							/>

							<ToggleControl
								label={__('Autoplay', 'embed-block-extension')}
								help={__(
									'Automatically start playback when the embed loads.',
									'embed-block-extension'
								)}
								checked={!!arve.autoplay}
								onChange={(val) => updateArve('autoplay', val)}
							/>

							<ToggleGroupControl
								label={__('Mode', 'embed-block-extension')}
								value={arve.mode || 'normal'}
								onChange={(val) => updateArve('mode', val)}
								isBlock
								__next40pxDefaultSize
							>
								<ToggleGroupControlOption
									value="normal"
									label={__('Normal', 'embed-block-extension')}
								/>
								<ToggleGroupControlOption
									value="lazyload"
									label={__('Lazyload', 'embed-block-extension')}
								/>
								<ToggleGroupControlOption
									value="lightbox"
									label={__('Lightbox', 'embed-block-extension')}
								/>
							</ToggleGroupControl>
						</PanelBody>
					</InspectorControls>
				)}
			</>
		);
	};
}, 'withArveControls');

addFilter('editor.BlockEdit', 'embed-block-extension/controls', withArveControls);
