<?php
namespace Nextgenthemes\ARVE\Common;

function get_url_arg( $url, $arg ) {

	$return     = false;
	$parsed_url = wp_parse_url( $url );

	if ( ! empty( $parsed_url['query'] ) ) {

		parse_str( $parsed_url['query'], $url_query );

		if ( isset( $url_query[ $arg ] ) ) {
			$return = $url_query[ $arg ];
		}
	}

	return $return;
}

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
