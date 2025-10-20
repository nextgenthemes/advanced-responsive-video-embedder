<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

/**
 * This function returns the block wrapper attributes as a string, it ignores null and false values to align the functionality with Nextgenthemes `attr` function. And is escapes the URL values with `esc_url`.
 *
 * @param array <string, string> $attr The array of attributes.
 * @return string The block wrapper attributes as a string.
 */
function ngt_get_block_wrapper_attributes( array $attr ): string {

	foreach ( $attr as $key => $value ) {

		if ( false === $value || null === $value ) {
			unset( $attr[ $key ] );
		} elseif ( in_array( $key, array( 'href', 'data-href', 'src', 'data-src' ), true ) ) {
			$attr[ $key ] = esc_url( $value );
		}
	}

	return ' ' . \get_block_wrapper_attributes( $attr );
}

/**
 * @param array <string, mixed> $attr
 */
function attr( array $attr = array() ): string {

	$html = '';

	foreach ( $attr as $key => $value ) {

		if ( false === $value || null === $value ) {

			continue;

		} elseif ( '' === $value || true === $value ) {

			$html .= sprintf( ' %s', esc_html( $key ) );

		} elseif ( is_array( $value ) || is_object( $value ) ) {

			$html .= sprintf( " %s='%s'", esc_html( $key ), wp_json_encode( $value, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP ) );

		} elseif ( in_array( $key, array( 'href', 'data-href', 'src', 'data-src' ), true ) ) {

			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_url( $value ) );

		} else {

			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
		}
	}

	return $html;
}

/**
 * Move certain keys to the start of an associative array.
 *
 * @param array<string, mixed> $org  The original array.
 * @param array<string>        $keys The keys to move to the start.
 *
 * @return array<string, mixed> The modified array.
 */
function move_keys_to_start( array $org, array $keys ): array {

	$moved = array();

	foreach ( $keys as $key ) {
		if ( array_key_exists( $key, $org ) ) {
			$moved[ $key ] = $org[ $key ];
			unset( $org[ $key ] );
		}
	}

	return $moved + $org;
}

/**
 * Move specified keys to the end of an array
 *
 * @param array<string, mixed> $org   Array to modify
 * @param array<int, string>   $keys  Keys to move to the end
 * @return array<string, mixed> Modified array with keys moved to end
 */
function move_keys_to_end( array $org, array $keys ): array {

	$moved = array();

	foreach ( $keys as $key ) {
		if ( array_key_exists( $key, $org ) ) {
			$moved[ $key ] = $org[ $key ];
			unset( $org[ $key ] );
		}
	}

	return $org + $moved;
}

/**
 * This is to prevent constant() throwing as Error in PHP 8, E_WARNING in PHP < 8
 *
 * @return mixed
 */
function get_constant( string $const_name ) {
	return defined( $const_name ) ? constant( $const_name ) : false;
}

function is_wp_debug(): bool {
	return defined( 'WP_DEBUG' ) && WP_DEBUG;
}

/**
 * This PHP function takes a delimiter string as input and converts it into an array.
 * It removes any leading or trailing spaces from each element and filters out any empty
 * elements from the resulting array.
 *
 * @param string   $str       The input comma-separated string
 * @param string   $delimiter The delimiter to use. Space will NOT work!
 * @return array<int,string>  The resulting array
 */
function str_to_array( string $str, string $delimiter = ',' ): array {

	// Trim spaces from each element
	$arr = array_map( 'trim', explode( $delimiter, $str ) );

	// Filter out empty elements
	$arr = array_filter(
		$arr,
		fn ( string $s ): bool => (bool) strlen( $s )
	);

	// Remove duplicate elements
	$arr = array_unique( $arr );

	return $arr;
}

/**
 * Applies a callback function to each key of an array, returning a new array
 * with the modified keys and original values.
 *
 * @param callable(string):string $callback The callback function to apply to each key, must return a string.
 * @param array<mixed>            $arr      The input array with any value types.
 *
 * @return array<mixed>                     The resulting array with modified keys.
 * @throws \InvalidArgumentException        If the callback is not callable or if the callback returns non-string keys.
 */
function array_map_key( callable $callback, array $arr ): array {

	$keys     = array_keys( $arr );
	$new_keys = array_map(
		function ( $key ) use ( $callback ) {
			$result = $callback( (string) $key );
			if ( ! is_string( $result ) ) {
				throw new \InvalidArgumentException( 'Callback must return a string, got ' . esc_html( gettype( $result ) ) );
			}
			return $result;
		},
		$keys
	);

	// Ensure no duplicate keys after mapping
	if ( count( array_unique( $new_keys ) ) !== count( $new_keys ) ) {
		throw new \InvalidArgumentException( 'Callback produced duplicate keys, which is not allowed.' );
	}

	// array_combine will fail if lengths don't match, but we're safe here
	return array_combine( $new_keys, array_values( $arr ) );
}
