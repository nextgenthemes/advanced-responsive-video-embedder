/**
 * WordPress dependencies
 */
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const Save = () => {
	const blockProps = useBlockProps.save( {
		type: 'button',
	} );
	return (
		<button { ...blockProps }>
			<InnerBlocks.Content />
		</button>
	);
};
export default Save;
