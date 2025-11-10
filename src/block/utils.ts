/**
 * Checks if an object has exactly the same set of keys as the provided array
 *
 * @template T - The type of the keys array
 * @param {Object} obj  - The object to check
 * @param {T}      keys - Array of keys to check against
 * @return {obj is Record<T[number], unknown>} - Type guard that indicates if obj has exactly these keys
 */
export function hasSameKeys< T extends ReadonlyArray< string > >(
	obj: object,
	keys: T
): obj is Record< T[ number ], unknown > {
	const objKeys = Object.keys( obj );
	return objKeys.length === keys.length && keys.every( ( key ) => objKeys.includes( key ) );
}
