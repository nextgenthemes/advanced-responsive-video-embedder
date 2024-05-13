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

			// Fails
			#$html .= sprintf( " %s='%s'", esc_html( $key ), json_encode( $value ) );
			// single quoted works
			#$html .= sprintf( " %s='%s'", esc_html( $key ), json_encode( $value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) );
			// for ARVE just escaping single quotes works
			$html .= sprintf( " %s='%s'", esc_html( $key ), wp_json_encode( $value, JSON_HEX_APOS ) );
			// double quoted FAILS! WHY?
			#$html .= sprintf( ' %s="%s"', esc_html( $key ), wp_json_encode( $value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) );
			// works, apperantly has issues with double quotes
			#$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( wp_json_encode( $value ) ) );
			// works, no matter the quotes
			#$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_json_encode( $value ) );
			#$html .= sprintf( " %s='%s'", esc_html( $key ), esc_json_encode( $value ) );

		} elseif ( in_array( $key, array( 'href', 'data-href', 'src', 'data-src' ), true ) ) {

			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_url( $value ) );

		} else {

			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
		}
	}

	return $html;
}

/**
 *  Escaping for HTML attributes use this instead of esc_attr( json_encode( ) )
 *
 * @link https://core.trac.wordpress.org/ticket/29910
 * 
 * @param  array or object $data array or object to be escaped 
 * @return properly escaped data
 */
function esc_json_encode( $data ) {

	$data = wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );

	return _wp_specialchars( $data, ENT_QUOTES, false, true );
}

/**
 * Retrieves the value of a specific query argument from the given URL.
 *
 * @param string $url The URL containing the query parameters.
 * @param string $arg The name of the query argument to retrieve.
 * @return string|null The value of the specified query argument, or null if it is not found.
 */
function get_url_arg( string $url, string $arg ): ?string {

	$query_string = parse_url( $url, PHP_URL_QUERY );

	if ( empty( $query_string ) || ! is_string( $query_string ) ) {
		return null;
	}

	parse_str( $query_string, $query_args );

	return $query_args[ $arg ] ?? null;
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
