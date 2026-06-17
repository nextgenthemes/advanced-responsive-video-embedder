import { TextControl } from '@wordpress/components';

function calculateAspectRatio(width: string, height: string): string | undefined {
	const isPositiveIntegerString = (str: string): boolean => /^[1-9]\d*$/.test(str);

	if (!isPositiveIntegerString(width) || !isPositiveIntegerString(height)) {
		return undefined;
	}

	const w = parseInt(width, 10);
	const h = parseInt(height, 10);

	const gcd = (a: number, b: number): number => {
		return b === 0 ? a : gcd(b, a % b);
	};

	const divisor = gcd(w, h);

	return `${w / divisor}:${h / divisor}`;
}

interface UrlOrEmbedCodeProps {
	label: string;
	value: string;
	onChange: (value: string) => void;
	onAspectRatioChange: (ratio: string) => void;
	placeholder: string | undefined;
	help: string | JSX.Element | undefined;
}

export function UrlOrEmbedCode({
	label,
	value,
	onChange,
	onAspectRatioChange,
	placeholder,
	help,
}: UrlOrEmbedCodeProps) {
	const handleChange = (newValue: string) => {
		const parser = new DOMParser();
		const iframe = parser.parseFromString(newValue, 'text/html').querySelector('iframe');

		if (iframe?.src) {
			const src = iframe.getAttribute('src') || '';
			onChange(src);

			if (iframe.width && iframe.height) {
				const ratio = calculateAspectRatio(iframe.width, iframe.height);
				if (ratio && ratio !== '16:9') {
					onAspectRatioChange(ratio);
				}
			}
			return;
		}

		onChange(newValue);
	};

	return (
		<TextControl
			label={label}
			value={value}
			onChange={handleChange}
			placeholder={placeholder}
			help={help}
			type="text"
		/>
	);
}

export default UrlOrEmbedCode;
