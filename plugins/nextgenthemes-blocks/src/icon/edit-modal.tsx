import './editor.scss';
import { __ } from '@wordpress/i18n';
import {
	SVG,
	TextControl,
	PanelBody,
	SelectControl,
	Modal,
	Button,
} from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';

import iconsJSON from './bootstrap-icons.json';

const Edit = ( props ) => {
	const {
		attributes: { title, href },
		setAttributes,
	} = props;

	const blockProps = useBlockProps();
	const [ isOpen, setOpen ] = useState( false );
	const openModal = () => setOpen( true );
	const closeModal = () => setOpen( false );

	const onChangeIcon = ( value: string ) => {
		setAttributes( {
			icon: value,
			href: iconURL( value ),
		} );
	};

	const onChangeHref = ( value: string ) => {
		setAttributes( { href: value } );
	};

	const onChangeTitle = ( value: string ) => {
		setAttributes( { title: value } );
	};

	const icons = () => {
		const arr = [];

		for ( const key of Object.keys( iconsJSON ) ) {
			const selectIcon = () => {
				setAttributes( { href: iconURL( key ) } );
				setOpen( false );
			};

			arr.push(
				<div key={ key }>
					<Button className="ngt-icon-btn" onClick={ selectIcon }>
						<div className="ngt-icon-btn__wrap">
							<SVG
								viewBox="0 0 16 16"
								width="16"
								height="16"
								className="sicon"
							>
								<title>{ key }</title>
								<use href={ iconURL( key ) } />
							</SVG>
						</div>
						<div className="text-center">{ key }</div>
					</Button>
				</div>
			);
		}

		return <div className="ngt-icon-grid">{ arr }</div>;
	};

	return (
		<SVG
			{ ...blockProps }
			viewBox="0 0 16 16"
			width="16"
			height="16"
			className="sicon"
		>
			<InspectorControls>
				<PanelBody title={ __( 'Settings' ) } initialOpen={ true }>
					<div className="components-base-control">
						<Button variant="secondary" onClick={ openModal }>
							Select Icon
						</Button>
					</div>
					{ isOpen && (
						<Modal
							title="Select Icon"
							onRequestClose={ closeModal }
							isFullScreen={ true }
						>
							{ icons() }
							<Button variant="secondary" onClick={ closeModal }>
								Close
							</Button>
						</Modal>
					) }
					<TextControl
						label={ __( 'href' ) }
						value={ href }
						onChange={ onChangeHref }
					/>
					<TextControl
						label={ __( 'Title' ) }
						value={ title }
						onChange={ onChangeTitle }
					/>
				</PanelBody>
			</InspectorControls>

			<title>{ title }</title>
			<use href={ href } />
		</SVG>
	);
};

export default Edit;
