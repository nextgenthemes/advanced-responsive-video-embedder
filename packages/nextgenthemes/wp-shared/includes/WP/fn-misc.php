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

			$html .= sprintf( " %s='%s'", esc_html( $key ), wp_json_encode( $value ) );

		} elseif ( in_array( $key, array( 'href', 'data-href', 'src', 'data-src' ), true ) ) {

			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_url( $value ) );

		} else {

			$html .= sprintf( ' %s="%s"', esc_html( $key ), esc_attr( $value ) );
		}
	}

	return $html;
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
 * Replaces the extension of the given filename with the new extension.
 *
 * @param string $filename The original filename including the path.
 * @param string $new_extension The new extension to replace the existing one.
 * @return string The modified filename with the new extension.
 */
function replace_extension( string $filename, string $new_extension ): string {
	$info = pathinfo( $filename );
	$dir  = $info['dirname'] ? $info['dirname'] . DIRECTORY_SEPARATOR : '';

	return $dir . $info['filename'] . '.' . $new_extension;
}
