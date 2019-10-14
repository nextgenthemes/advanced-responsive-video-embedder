<?php
namespace Nextgenthemes\ARVE\Common;

function update_key_status( $product, $key ) {
	update_option( "nextgenthemes_{$product}_key_status", $key );
}

function has_valid_key( $product ) {

	$o = get_option( 'nextgenthemes' );

	return ( ! empty( $o[ "{$product}_status" ] ) && 'valid' === $o[ "{$product}_status" ] );
}

function get_defined_key( $slug ) {

	$constant_name = str_replace( '-', '_', strtoupper( $slug . '_KEY' ) );

	if ( defined( $constant_name ) && constant( $constant_name ) ) {
		return constant( $constant_name );
	} else {
		return false;
	}
}
