<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

function array_whitelist_keys( array $arr, array $keys ): array {

	return array_intersect_key(
		$arr,
		array_flip( $keys )
	);
}

/**
 * @return mixed
 */
function first_array_value( array $array ) {
	reset( $array );
	$key = key( $array );
	return $array[ $key ];
}

function prefix_array_keys( string $keyprefix, array $array ): array {

	foreach ( $array as $key => $value ) {
		$array[ $keyprefix . $key ] = $value;
		unset( $array[ $key ] );
	}

	return $array;
}

/**
 * @param mixed $field
 * @param mixed $value
 *
 * @return mixed
 */
function get_array_key_by_value( array $arr, $field, $value ) {

	foreach ( $arr as $key => $array_value ) {

		if ( $array_value[ $field ] === $value ) {
			return $key;
		}
	}

	return false;
}

/**
 * Inserts a new key/value before the key in the array.
 *
 * @param mixed $key        The key to insert before.
 * @param array $array      An array to insert in to.
 * @param mixed $new_key    The key to insert.
 * @param mixed $new_value  An value to insert.
 *
 * @return array|false The new array if the key exists, FALSE otherwise.
 *
 * @see array_insert_after()
 */
function array_insert_before( $key, array &$array, $new_key, $new_value ) {
	if ( array_key_exists( $key, $array ) ) {
		$new = array();
		foreach ( $array as $k => $value ) {
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
 * @param mixed $key        The key to insert after.
 * @param array $arr        An array to insert in to.
 * @param mixed $new_key    The key to insert.
 * @param mixed $new_value  An value to insert.
 *
 * @return array|false The new array if the key exists, FALSE otherwise.
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

function sort_array_by_array( array $arr, array $order_array ): array {
	$ordered = array();
	foreach ( $order_array as $key ) {
		if ( array_key_exists($key, $arr) ) {
			$ordered[ $key ] = $arr[ $key ];
			unset($arr[ $key ]);
		}
	}
	return $ordered + $arr;
}
