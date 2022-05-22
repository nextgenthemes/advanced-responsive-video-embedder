import json from './block.json';
import './editor.scss';
import { __, sprintf } from '@wordpress/i18n';
import {
	SVG,
	TextControl,
	TextareaControl,
	PanelBody,
	SelectControl,
	Modal,
	Button,
} from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

const Edit = ( props ) => {
	const {
		attributes: { title, href, icon },
		setAttributes,
	} = props;

	const blockProps = useBlockProps();

	const onChangeIcon = ( value: string ) => {
		setAttributes( { icon: value } );
	};

	const onChangeTitle = ( value: string ) => {
		setAttributes( { title: value } );
	};

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) } initialOpen={ true }>
					<TextControl
						label={ __( 'Icon ID' ) }
						value={ icon }
						onChange={ onChangeIcon }
					/>
					<TextControl
						label={ __( 'Title' ) }
						value={ title }
						onChange={ onChangeTitle }
					/>
				</PanelBody>
			</InspectorControls>

			<SVG { ...blockProps } viewBox="0 0 16 16" width="16">
				<title>{ title }</title>
				<use href={ iconURL( icon ) } />
			</SVG>
		</>
	);
};

export default Edit;

function iconID( href: string ) {
	const regex = /bootstrap-icons\.svg#([^\s]+)/;
	const m = regex.exec( href );

	if ( Array.isArray( m ) && 1 in m ) {
		return m[ 1 ];
	}

	return '';
}

function iconURL( iconSlug: string ) {
	const { name } = json;
	const scriptID = name.replace( '/', '-' ) + '-editor-script-js';
	const src = document.getElementById( scriptID )?.getAttribute( 'src' );
	const url = new URL( src );
	url.search = '';
	url.pathname = url.pathname.replace( 'build-blocks/icon/index.js', '' );
	const pattern = url.toString() + 'build/svg/bootstrap-icons.svg#%s';

	// eslint-disable-next-line @wordpress/valid-sprintf
	const href = sprintf( pattern, iconSlug );

	return href;
}
