/**
 * Copyright 2019-2025 Nicolas Jonas
 * License: GPL 3.0
 */

import { __ } from '@wordpress/i18n';
import { BaseControl, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import { createElement } from '@wordpress/element';
import ImageUpload from './components/ImageUpload';
import type { BuildControlsProps, GutenbergSelectOption } from '../types';

const domParser = new DOMParser();

const { settingPageUrl } = window.ArveBlockJsBefore;
const { options } = window.ArveBlockJsBefore;
const { gutenberg_help } = options;

export function createHelp3( description: string | undefined ): string | JSX.Element {
	if ( ! description ) {
		return '';
	}

	const doc = domParser.parseFromString( description, 'text/html' );
	const link = doc.querySelector( 'a' );
	if ( link ) {
		const href = link.getAttribute( 'href' ) || '';
		const linkText = link.textContent || '';
		description = doc.body.textContent || '';
		const textSplit = description.split( linkText );

		if ( textSplit.length !== 2 ) {
			throw new Error( 'textSplit.length must be 2' );
		}

		return (    
			<>
				{ textSplit[ 0 ] }
				<a href={ href }>{ linkText }</a>
				{ textSplit[ 1 ] }
			</>
		);
	}

	return description;
}

function createHelp(html?: string): string | JSX.Element {

    // Quick check: if no <a> tags, return the html as a single string
    if (!gutenberg_help || !html || !html.match(/<a/i)) {
        return html || '';
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

	const { settings } = window.ArveBlockJsBefore;
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

	const { settings } = window.ArveBlockJsBefore;

	// Initialize section controls
	Object.values(settings).forEach((setting) => {
		sectionControls[setting.category] = [];
	});

	// Add controls to sections
	Object.entries(settings).forEach(([key, setting]) => {
		const val = attributes[key];
		const url = attributes[`${key}_url`] as string || '';
		const tab = setting.category || 'no-category';

		if (shouldHide(key, attributes)) {
			return;
		}

		if (setting.ui === 'image_upload') {
			sectionControls[tab].push(
				createElement(ImageUpload, {
					className: `arve-ctl-${setting.tab}`,
					attributeKey: key,
					val: val as string,
					url,
					help: createHelp(setting.description),
					setAttributes
				})
			);
		} else if (setting.ui_element === 'select') {
			const selectOptions = prepareSelectOptions(setting.options as Record<string, string>);
			sectionControls[tab].push(
				createElement(SelectControl, {
					className: `arve-ctl-${setting.tab}`,
					label: setting.label,
					value: val as string,
					options: selectOptions,
					onChange: (value: string) => setAttributes({ [key]: value || undefined }),
					help: createHelp(setting.description)
				})
			);
		} else if (setting.ui_element_type === 'checkbox') {
			sectionControls[tab].push(
				createElement(ToggleControl, {
					className: `arve-ctl-${setting.tab}`,
					label: setting.label,
					checked: Boolean(val),
					onChange: (value: boolean) => setAttributes({ [key]: value }),
					help: createHelp(setting.description)
				})
			);
		} else {
			sectionControls[tab].push(
				createElement(TextControl, {
					className: `arve-ctl-${setting.tab}`,
					label: setting.label,
					type: setting.ui_element_type,
					value: (val as string) || '',
					placeholder: setting.placeholder,
					onChange: (value: string) => setAttributes({ [key]: value }),
					help: createHelp(setting.description)
				})
			);
		}
	});

	// Add info panel to main section
	sectionControls.main.push(
		createElement(BaseControl, {
			help: createHelp( __(
				'Remember changing the defaults is possible on the <a href="' + settingPageUrl + '" target="_blank">Settings page</a>. You can disable the extensive help texts there.',
				'advanced-responsive-video-embedder'
			) ),
			children: createElement(BaseControl.VisualLabel, null, 
				__('Info', 'advanced-responsive-video-embedder')
			)
		})
	);

	// Convert section controls to panels
	Object.entries(sectionControls).forEach(([tab, tabControls]) => {
		if (tabControls.length > 0) {
			controls.push(
				createElement(PanelBody, {
					key: tab,
					title: capitalizeFirstLetter(tab),
					initialOpen: tab !== 'misc'
				}, tabControls)
			);
		}
	});

	return controls;
}
