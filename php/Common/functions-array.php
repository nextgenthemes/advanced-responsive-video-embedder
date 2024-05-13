<?php
// phpcs:disable SlevomatCodingStandard.TypeHints
namespace Nextgenthemes\ARVE\Common;

function array_whitelist_keys( array $arr, array $keys ) {

	return array_intersect_key(
		$arr,
		array_flip( $keys )
	);
}

function first_array_value( array $arr ) {
	reset( $arr );
	$key = key( $arr );
	return $arr[ $key ];
}

function prefix_array_keys( $keyprefix, array $arr ) {

	foreach ( $arr as $key => $value ) {
		$arr[ $keyprefix . $key ] = $value;
		unset( $arr[ $key ] );
	}

	return $arr;
}

function get_array_key_by_value( $arr, $field, $value ) {

	foreach ( $arr as $key => $arr_value ) {

		if ( $arr_value[ $field ] === $value ) {
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
 * @param $arr
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
function array_insert_before( $key, array &$arr, $new_key, $new_value ) {
	if ( array_key_exists( $key, $arr ) ) {
		$new = array();
		foreach ( $arr as $k => $value ) {
			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}
			$new[ $k ] = $value;
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
 * @param $arr
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
function array_insert_after( $key, array &$arr, $new_key, $new_value ) {
	if ( array_key_exists( $key, $arr ) ) {
		$new = array();
		foreach ( $arr as $k => $value ) {
			$new[ $k ] = $value;
			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}
		}
		return $new;
	}
	return false;
}
