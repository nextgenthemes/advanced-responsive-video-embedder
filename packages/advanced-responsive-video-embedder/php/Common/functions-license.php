<?php
namespace Nextgenthemes\ARVE\Common;

function check_product_keys() {

	$products = get_products();

	unset( $products['arve_amp'] );

	foreach ( $products as $key => $value ) :

		if ( $value['active'] && ! $value['valid_key'] ) {
			$msg = sprintf(
				// Translators: URL, Product name
				__( '<a href="%1$s">%2$s</a> license not activated or valid', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/installing-and-license-management/' ),
				$value['name']
			);

			throw new \Exception( $msg );
		}
	endforeach;
}

function has_valid_key( $product ) {
	$o = (array) get_option( 'nextgenthemes' );

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

function activate_product_key( $product, $key ) {

	$product_id = get_products()[ $product ]['id'];

	$options                         = (array) get_option( 'nextgenthemes' );
	$options[ $product . '_status' ] = api_action( $product_id, $key, 'activate' );

	update_option( 'nextgenthemes', $options );
}

function activate_defined_key( $file, $theme_name = '' ) {

	if ( 'functions.php' === $file ) {
		return;
	}

	$path_parts = pathinfo( $file );
	$path_parts['filename'];

	$product  = str_replace( '-', '_', $path_parts['filename'] );
	$key_name = strtoupper( $product . '_KEY' );
	$key      = defined( $key_name ) ? constant( $key_name ) : false;

	if ( $key ) {
		activate_product_key( $product, $key );
	}
}

function api_action( $item_id, $key, $action = 'check' ) {

	if ( ! in_array( $action, array( 'activate', 'deactivate', 'check' ), true ) ) {
		wp_die( 'invalid action' );
	}

	// Call the custom API.
	$response = remote_get_json(
		'https://nextgenthemes.com',
		array(
			'timeout' => 10,
			'body'    => array(
				'edd_action' => $action . '_license',
				'license'    => sanitize_text_field( $key ),
				'item_id'    => $item_id,
				'url'        => home_url(),
			),
		)
	);

	// make sure the response came back okay
	if ( is_wp_error( $response ) ) {
		$message = $response->get_error_message();
	} else {
		$message = get_api_error_message( $response );
	}

	if ( empty( $message ) ) {

		if ( empty( $response->license ) ) {

			$textarea_dump = textarea_dump( $response );

			$message = sprintf(
				// Translators: Error message
				__( 'Error. Please report the following:<br>%s', 'advanced-responsive-video-embedder' ),
				$textarea_dump
			);
		} else {
			$message = $response->license;
		}
	}

	return $message;
}

function get_api_error_message( $license_data ) {

	if ( false !== $license_data->success ) {
		return '';
	}

	switch ( $license_data->error ) {
		case 'expired':
			$message = sprintf(
				// Translators: Date
				__( 'Your license key expired on %s.', 'advanced-responsive-video-embedder' ),
				date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, time() ) )
			);
			break;

		case 'revoked':
			$message = __( 'Your license key has been disabled.', 'advanced-responsive-video-embedder' );
			break;

		case 'missing':
			$message = __( 'Invalid license.', 'advanced-responsive-video-embedder' );
			break;

		case 'invalid':
		case 'site_inactive':
			$message = __( 'Your license is not active for this URL.', 'advanced-responsive-video-embedder' );
			break;

		case 'item_name_mismatch':
			// Translators: Product Name
			$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'advanced-responsive-video-embedder' ), 'advanced-responsive-video-embedder' );
			break;

		case 'no_activations_left':
			$message = __( 'Your license key has reached its activation limit.', 'advanced-responsive-video-embedder' );
			break;

		default:
			$message = __( 'An error occurred, please try again.', 'advanced-responsive-video-embedder' );
			break;
	}//end switch
}

function textarea_dump( $var ) {
	return sprintf( '<textarea style="width: 100%; height: 70vh;">%s</textarea>', esc_textarea( get_var_dump( $var ) ) );
}
