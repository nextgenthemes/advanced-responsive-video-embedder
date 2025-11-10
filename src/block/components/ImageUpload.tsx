import { __ } from '@wordpress/i18n';
import { Button, BaseControl, DropZone } from '@wordpress/components';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import clsx from 'clsx';

interface MediaItem {
	id: number;
	url?: string;
	[ key: string ]: any;
}

interface ImageUploadProps {
	className?: string;
	sKey: string;
	val: number | undefined;
	url: string;
	help: undefined | string | JSX.Element;
	setAttributes: ( attributes: Record< string, any > ) => void;
}

const ImageUpload = ( { className, sKey, val, url, help, setAttributes }: ImageUploadProps ) => {
	const mediaUploadInstructions = (
		<p>{ __( 'To edit the featured image, you need permission to upload media.' ) }</p>
	);
	const containerClasses = clsx( 'editor-post-featured-image__container', className );

	const mediaUploadRender = ( open: () => void ) => {
		return (
			<div className={ containerClasses }>
				<Button
					className={
						! val
							? 'editor-post-featured-image__toggle'
							: 'editor-post-featured-image__preview'
					}
					onClick={ open }
					aria-label={ ! val ? undefined : __( 'Edit or update the image' ) }
					aria-describedby={
						! val ? '' : `editor-post-featured-image-${ val }-describedby`
					}
				>
					{ val && url ? (
						<div style={ { width: '100%', overflow: 'hidden' } }>
							<img
								src={ url }
								alt="ARVE Thumbnail"
								style={ {
									width: '100%',
									objectFit: 'cover',
									aspectRatio: '16/9',
								} }
							/>
						</div>
					) : (
						<span>{ __( 'Set Thumbnail' ) }</span>
					) }
				</Button>
				<DropZone />
			</div>
		);
	};

	const handleSelect = ( media: MediaItem ) => {
		setAttributes( {
			[ sKey ]: media.id.toString(),
			[ `${ sKey }_url` ]: media.url || '',
		} );
	};

	const handleRemove = () => {
		setAttributes( {
			[ sKey ]: '',
			[ `${ sKey }_url` ]: '',
		} );
	};

	return (
		<BaseControl className="editor-post-featured-image" help={ help }>
			<MediaUploadCheck fallback={ mediaUploadInstructions }>
				<MediaUpload
					title={ __( 'Thumbnail' ) }
					onSelect={ handleSelect }
					allowedTypes={ [ 'image' ] }
					modalClass="editor-post-featured-image__media-modal"
					render={ ( { open } ) => mediaUploadRender( open ) }
					value={ val }
				/>
			</MediaUploadCheck>
			{ !! val && !! url && (
				<MediaUploadCheck key={ `${ sKey }-MediaUploadCheck-2` }>
					<MediaUpload
						title={ __( 'Thumbnail' ) }
						onSelect={ handleSelect }
						allowedTypes={ [ 'image' ] }
						modalClass="editor-post-featured-image__media-modal"
						render={ ( { open } ) => (
							<Button onClick={ open } variant="secondary">
								{ __( 'Replace Thumbnail' ) }
							</Button>
						) }
					/>
				</MediaUploadCheck>
			) }
			{ !! val && (
				<MediaUploadCheck key={ `${ sKey }-MediaUploadCheck-3` }>
					<Button onClick={ handleRemove } isDestructive>
						{ __( 'Remove Thumbnail' ) }
					</Button>
				</MediaUploadCheck>
			) }
		</BaseControl>
	);
};

export default ImageUpload;
