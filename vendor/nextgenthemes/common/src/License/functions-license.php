<?php
namespace Nextgenthemes\License;

function get_key( $product, $option_only = false ) {

	$defined_key = get_defined_key( $product );

	if ( ! $option_only && $defined_key ) {
		return $defined_key;
	}

	return get_option( "nextgenthemes_{$product}_key" );
}

function get_key_status( $product ) {
	return get_option( "nextgenthemes_{$product}_key_status" );
}

function update_key_status( $product, $key ) {
	update_option( "nextgenthemes_{$product}_key_status", $key );
}

function has_valid_key( $product ) {
	return ( 'valid' === get_key_status( $product ) ) ? true : false;
}

function get_defined_key( $slug ) {

	$constant_name = str_replace( '-', '_', strtoupper( $slug . '_KEY' ) );

	if ( defined( $constant_name ) && constant( $constant_name ) ) {
		return constant( $constant_name );
	} else {
		return false;
	}
}
