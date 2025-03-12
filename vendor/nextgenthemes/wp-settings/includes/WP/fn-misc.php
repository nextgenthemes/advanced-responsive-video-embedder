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
 * @param array<string, mixed> $org_array The original array.
 * @param array<string>        $keys      The keys to move to the start.
 *
 * @return array<string, mixed> The modified array.
 */
function move_keys_to_start( array $org_array, array $keys ): array {
	$new_array = [];

	foreach ( $keys as $key ) {
		if ( array_key_exists( $key, $org_array ) ) {
			$new_array[ $key ] = $org_array[ $key ];
			unset( $org_array[ $key ] );
		}
	}

	return $new_array + $org_array;
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
 * @param string $str The input comma-separated string
 * @param string $delimiter The delimiter to use. Space will NOT work!
 * @return array The resulting array
 */
function str_to_array( string $str, string $delimiter = ',' ): array {
	return array_filter(
		array_map(
			'trim',
			explode( $delimiter, $str )
		),
		'strlen'
	);
}

/**
 * Applies a callback function to each key of an array, returning a new array
 * with the modified keys and original values.
 *
 * @param string   $callback The callback function to apply to each key.
 * @param array    $arr      The input array.
 *
 * @return array   The resulting array with modified keys.
 */
function array_map_key( string $callback, array $arr ): array {
	return array_combine(
		array_map( $callback, array_keys( $arr ) ),
		$arr
	);
}
