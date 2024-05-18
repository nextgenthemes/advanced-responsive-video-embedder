<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

/**
 * This function returns the block wrapper attributes as a string, it ignores null and false values to align the functionality with nextgentheme's `attr` function. And is escapes the URL values with `esc_url`.
 *
 * @param array <string, string> $attr The array of attributes.
 * @return string The block wrapper attributes as a string.
 */
function ngt_get_block_wrapper_attributes( array $attr ): string {

	foreach ( $attr as $key => $value ) {

		if ( false === $value || null === $value ) {
			unset( $attr[ $key ] );
			continue;
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
 * @param mixed $var
 *
 * @return string|false
 */
function get_var_dump( $var ) {
	ob_start();
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
	var_dump( $var );
	return ob_get_clean();
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

function array_map_key( string $callback, array $arr ): array {

	return array_combine(
		array_map(
			function ( $key ) use ( $callback ) {
				return call_user_func($callback, $key);
			},
			array_keys($arr)
		),
		$arr
	);
}
