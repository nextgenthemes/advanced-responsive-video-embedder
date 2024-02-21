<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

/**
 * @param array <string, string> $attr
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
 * @return mixed
 */
function get_url_arg( string $url, string $arg ) {

	$parsed_url = \wp_parse_url( $url );

	if ( ! empty( $parsed_url['query'] ) ) {

		parse_str( $parsed_url['query'], $url_query );

		if ( isset( $url_query[ $arg ] ) ) {
			return $url_query[ $arg ];
		}
	}

	return false;
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
};

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

function replace_extension( string $filename, string $new_extension ): string {
	$info = pathinfo( $filename );
	$dir  = $info['dirname'] ? $info['dirname'] . DIRECTORY_SEPARATOR : '';

	return $dir . $info['filename'] . '.' . $new_extension;
}
