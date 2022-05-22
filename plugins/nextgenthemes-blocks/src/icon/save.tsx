import { SVG } from '@wordpress/components';
import { useBlockProps } from '@wordpress/block-editor';

const Save = ( props ) => {
	const {
		attributes: { title, href },
	} = props;

	const blockProps = useBlockProps.save();
	return (
		<SVG { ...blockProps } viewBox="0 0 16 16" width="16" height="16">
			<title>{ title }</title>
			<use href={ href } />
		</SVG>
	);
};

export default Save;
