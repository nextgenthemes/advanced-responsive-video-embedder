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

/**
 * Inserts a new key/value before the key in the array.
 *
 * @param $key
 *   The key to insert before.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, FALSE otherwise.
 *
 * @see array_insert_after()
 */
function array_insert_before($key, array &$array, $new_key, $new_value) {
	if (array_key_exists($key, $array)) {
		$new = array();
		foreach ($array as $k => $value) {
			if ($k === $key) {
				$new[$new_key] = $new_value;
			}
			$new[$k] = $value;
		}
		return $new;
	}
	return false;
}

/**
 * Inserts a new key/value after the key in the array.
 *
 * @param $key
 *   The key to insert after.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, FALSE otherwise.
 *
 * @see array_insert_before()
 */
function array_insert_after($key, array &$array, $new_key, $new_value) {
	if (array_key_exists($key, $array)) {
		$new = array();
		foreach ($array as $k => $value) {
			$new[$k] = $value;
			if ($k === $key) {
				$new[$new_key] = $new_value;
			}
	  	}
	  	return $new;
	}
	return false;
}
