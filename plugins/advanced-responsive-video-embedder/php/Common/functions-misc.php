<?php
namespace Nextgenthemes\ARVE\Common;

function attr( array $attr = array() ) {

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

function get_url_arg( $url, $arg ) {

	$parsed_url = wp_parse_url( $url );

	if ( ! empty( $parsed_url['query'] ) ) {

		parse_str( $parsed_url['query'], $url_query );

		if ( isset( $url_query[ $arg ] ) ) {
			return $url_query[ $arg ];
		}
	}

	return false;
}

function get_var_dump( $var ) {
	ob_start();
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
	var_dump( $var );
	return ob_get_clean();
};

function get_constant( $const_name ) {
	return defined( $const_name ) ? constant( $const_name ) : false;
}

function is_wp_debug() {
	return get_constant( 'WP_DEBUG' );
}
