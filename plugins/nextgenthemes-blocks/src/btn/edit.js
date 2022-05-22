/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Edit = () => {
	const blockProps = useBlockProps( {
		type: 'button',
	} );
	return (
		<div { ...blockProps }>
			<InnerBlocks />
		</div>
	);
};
export default Edit;
