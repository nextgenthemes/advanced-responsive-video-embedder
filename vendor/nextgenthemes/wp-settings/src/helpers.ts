export function debounce< T extends any[] >(
	func: ( ...args: T ) => void,
	wait: number,
	immediate?: boolean
): ( ...args: T ) => void {
	let timeout: number | undefined;
	return function ( this: unknown, ...args: T ) {
		const context = this;
		const later = () => {
			timeout = undefined;
			if ( ! immediate ) {
				func.apply( context, args );
			}
		};
		const callNow = immediate && ! timeout;
		clearTimeout( timeout );
		timeout = window.setTimeout( later, wait );
		if ( callNow ) {
			func.apply( context, args );
		}
	};
}

/**
 * Calculate aspect ratio based on width and height.
 *
 * @param {string} width  - The width value
 * @param {string} height - The height value
 * @return {string} The aspect ratio in the format 'width:height'
 */
export function aspectRatio( width: string, height: string ): string {
	if ( isIntOverZero( width ) && isIntOverZero( height ) ) {
		const w = parseInt( width );
		const h = parseInt( height );
		const arGCD = gcd( w, h );

		return `${ w / arGCD }:${ h / arGCD }`;
	}

	return `${ width }:${ height }`;
}

/**
 * Checks if the input string represents a positive integer.
 *
 * @param {string} str - the input string to be checked
 * @return {boolean} true if the input string represents a positive integer, false otherwise
 */
function isIntOverZero( str: string ): boolean {
	const n = Math.floor( Number( str ) );
	return n !== Infinity && String( n ) === str && n > 0;
}

/**
 * Calculate the greatest common divisor of two numbers using the Euclidean algorithm.
 *
 * @param {number} a - the first number
 * @param {number} b - the second number
 * @return {number} the greatest common divisor of the two input numbers
 */
function gcd( a: number, b: number ): number {
	if ( ! b ) {
		return a;
	}

	return gcd( b, a % b );
}
