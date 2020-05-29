<?php
namespace Nextgenthemes\ARVE\Common;

function first_array_value( array $array ) {
	reset( $array );
	$key = key( $array );
	return $array[ $key ];
}

function prefix_array_keys( $keyprefix, array $array ) {

	foreach ( $array as $key => $value ) {
		$array[ $keyprefix . $key ] = $value;
		unset( $array[ $key ] );
	}

	return $array;
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

function get_array_key_by_value( $array, $field, $value ) {

	foreach ( $array as $key => $array_value ) {

		if ( $array_value[ $field ] === $value ) {
			return $key;
		}
	}

	return false;
}
