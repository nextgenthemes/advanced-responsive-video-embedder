import { createElement, Fragment } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';
import {
	TextControl,
	ExternalLink,
	PanelRow,
	PanelBody,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { hasBlockSupport, getBlockSupport } from '@wordpress/blocks';
import { createHigherOrderComponent } from '@wordpress/compose';
import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { camelCase } from 'lodash';

const dataAttr = {};
const dataAttributes = [
	'data-bs-target',
	'data-bs-toggle',
	'data-bs-ride',
	'data-bs-dismiss',
	'aria-label',
	'aria-labeledby',
	'tabindex',
];

function blockAttrName( attr: string ): string {
	return 'extraAttr-' + attr;
}

/**
 * Filters registered block settings, extending attributes with anchor using ID
 * of the first node.
 *
 * @param {Object} settings Original block settings.
 *
 * @return {Object} Filtered block settings.
 */
export function addAttribute( settings ) {
	const newAttr = {};
	const extraAttr = getBlockSupport( settings.name, 'extra-attr' );

	if ( ! Array.isArray( extraAttr ) ) {
		return settings;
	}

	for ( const attrName of extraAttr ) {
		newAttr[ attrName ] = {
			type: 'string',
			source: 'attribute',
			attribute: attrName,
			selector: '*',
			default: '',
		};
	}

	// Gracefully handle if settings.attributes is undefined.
	settings.attributes = {
		...settings.attributes,
		...newAttr,
	};

	return settings;
}

/**
 * Override the default edit UI to include a new block inspector control for
 * assigning the anchor ID, if block supports anchor.
 *
 * @param {WPComponent} BlockEdit Original component.
 *
 * @return {WPComponent} Wrapped component.
 */
export const withInspectorControl = createHigherOrderComponent(
	( BlockEdit ) => {
		return ( props ) => {
			const rows = [];
			const extraAttr = getBlockSupport( props.name, 'extra-attr' );

			if ( ! Array.isArray( extraAttr ) ) {
				return <BlockEdit { ...props } />;
			}

			for ( const attrName of extraAttr ) {
				rows.push(
					<PanelRow key={ attrName }>
						<TextControl
							label={ attrName }
							value={ props.attributes[ attrName ] }
							onChange={ ( nextValue ) => {
								props.setAttributes( {
									[ attrName ]: nextValue,
								} );
							} }
						/>
					</PanelRow>
				);
			}

			return (
				<>
					<BlockEdit { ...props } />
					<InspectorControls>
						<PanelBody
							title={ __( 'Block Attributes' ) }
							initialOpen={ true }
						>
							{ rows }
						</PanelBody>
					</InspectorControls>
				</>
			);
		};
	},
	'withInspectorControl'
);

/**
 * Override props assigned to save component to inject anchor ID, if block
 * supports anchor. This is only applied if the block's save result is an
 * element and not a markup string.
 *
 * @param {Object} extraProps Additional props applied to save element.
 * @param {Object} blockType  Block type.
 * @param {Object} attributes Current block attributes.
 *
 * @return {Object} Filtered props applied to save element.
 */
export function addSaveProps( extraProps, blockType, attributes ) {
	const extraAttr = getBlockSupport( blockType, 'extra-attr' );

	if ( Array.isArray( extraAttr ) ) {
		for ( const attrName of extraAttr ) {
			const attrCamel = attrName;

			extraProps[ attrName ] =
				'' === attributes[ attrCamel ] ? null : attributes[ attrCamel ];
		}
	}

	return extraProps;
}

addFilter(
	'blocks.registerBlockType',
	'ngt/data-attrs/attribute',
	addAttribute
);
addFilter(
	'editor.BlockEdit',
	'ngt/editor/data-attrs/with-inspector-control',
	withInspectorControl
);
addFilter(
	'blocks.getSaveContent.extraProps',
	'ngt/data-attrs/save-props',
	addSaveProps
);
