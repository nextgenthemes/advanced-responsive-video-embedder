/**
 * Checks if two objects have exactly the same set of keys
 * 
 * @template T - The type of the reference object
 * @param {object} obj - The object to check
 * @param {T} reference - The reference object with the expected keys
 * @returns {obj is T} - Type guard that indicates if obj has the same keys as reference
 */
export function hasSameKeys<T extends object>(
    obj: object,
    reference: T
): obj is T {
    const objKeys = Object.keys(obj);
    const refKeys = Object.keys(reference);
    return (
        objKeys.length === refKeys.length &&
        refKeys.every(key => objKeys.includes(key))
    );
}
