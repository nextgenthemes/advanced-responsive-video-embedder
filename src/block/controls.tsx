/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */

import { __ } from '@wordpress/i18n';
import { BaseControl, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
// createElement import removed as we're using JSX syntax
import ImageUpload from './components/ImageUpload';
import UrlOrEmbedCode from './components/UrlOrEmbedCode';

const { settingPageUrl, options, settings } = window.ArveBlockJsBefore;
const { gutenberg_help } = options;

function createHelp(html?: string): undefined | string | JSX.Element {

    if (!gutenberg_help || !html) {
        return undefined;
    }

	// Quick check: if no <a> tags, return the html as a single string
    if (!html.match(/<a/i)) {
        return html;
    }

    const doc = new DOMParser().parseFromString(html, 'text/html');
    const result: (string | JSX.Element)[] = [];

    const walk = (node: Node) => {
        if (node.nodeType === Node.TEXT_NODE) {
            const text = node.textContent;
            if (text !== null && text !== undefined) {
                result.push(text); // Preserve all whitespace, no trim
            }
        } else if (node.nodeType === Node.ELEMENT_NODE) {
            const el = node as HTMLElement;
            if (el.tagName === 'A') {
                const a = el as HTMLAnchorElement;
                const linkText = a.textContent || '';
                result.push(
                    <a href={a.href} target="_blank">
                        {linkText}
                    </a>
                );
                return; // Don't process children since we handled the text
            }

            // Process child nodes for other elements (though assuming only text and <a>)
            Array.from(el.childNodes).forEach(walk);
        }
    };

    walk(doc.body);
    return <>{result}</>;
}

// Capitalize first letter of a string and replace underscores with spaces
function capitalizeFirstLetter(str: string): string {
	return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
}


// Convert options to SelectControl format
function prepareSelectOptions(options: Record<string, string>): GutenbergSelectOption[] {
	return Object.entries(options).map(([value, label]) => ({
		label,
		value
	}));
}

function shouldHide(settingKey: string, attributes: Record<string, unknown>): boolean {

	if ('align' === settingKey) {
		return true;
	}

	const setting = settings[settingKey];

	// If no dependencies, don't hide
	if (!setting?.depends?.length) {
		return false;
	}

	// Check if NONE of the dependency conditions are met
	const shouldHide = !setting.depends.some(condition => {
		// Each condition is an object with a single key-value pair
		const [key, value] = Object.entries(condition)[0] || [];
		// If the attribute has the key and its value matches the condition, return true
		return key !== undefined && attributes[key] === value;
	});

	return shouldHide;
}

// Main export: build controls for the block inspector
export function buildControls({ attributes, setAttributes }: BuildControlsProps): JSX.Element[] {
	const controls: JSX.Element[] = [];
	const sectionControls: Record<string, JSX.Element[]> = {};

	// Initialize section controls
	Object.values(settings).forEach((setting) => {
		sectionControls[setting.category] = [];
	});

	// Add controls to sections
	Object.entries(settings).forEach(([sKey, setting]) => {
		const val = attributes[sKey];
		const url = attributes[`${sKey}_url`] as string || '';
		const tab = setting.category || 'no-category';

		if (shouldHide(sKey, attributes)) {
			return;
		}

		const compare = {
			'': 'not-matter',
			true: 'not-matter',
			false: 'not-matter',
		}

		// if (setting.options && setting.options[0][0])

		if ( 'url' === sKey ) {
			sectionControls[tab].push(
				<UrlOrEmbedCode
					key={sKey}
					label={setting.label}
					value={val as string}
					onChange={(value: string) => setAttributes({ [sKey]: value })}
					onAspectRatioChange={(ratio: string) => setAttributes({ aspect_ratio: ratio })}
					placeholder={setting.placeholder}
					help={createHelp(setting.description)}
				/>
			);
		}else if (setting.ui === 'image_upload') {
			sectionControls[tab].push(
				<ImageUpload
					key={sKey}
					sKey={sKey}
					className={`arve-ctl-${setting.tab}`}
					val={val as number}
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
					onChange={(value: string) => setAttributes({ [sKey]: value || undefined })}
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

	if ( gutenberg_help ) {
		// Add info panel to main section
		sectionControls.main.push(
			<BaseControl
				key="info-panel"
				help={
					__(
						'Remember changing the defaults is possible on the <a href="' + settingPageUrl + '" target="_blank">Settings page</a>. You can disable the extensive help texts there.',
						'advanced-responsive-video-embedder'
					)
				}
			>
				<BaseControl.VisualLabel>
					{__('Info', 'advanced-responsive-video-embedder')}
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
	}

	// Convert section controls to panels
	Object.entries(sectionControls).forEach(([tab, tabControls]) => {
		if (tabControls.length > 0) {
			controls.push(
				<PanelBody
					key={tab}
					title={categories[tab] ?? tab}
					initialOpen={'main' === tab}
				>
					{tabControls}
				</PanelBody>
			);
		}
	});

	return controls;
}
