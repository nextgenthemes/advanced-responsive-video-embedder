<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

function check_product_keys(): void {

	$products = get_products();

	unset( $products['arve_amp'] );

	foreach ( $products as $key => $value ) :

		if ( $value['active'] && ! $value['valid_key'] ) {
			$msg = sprintf(
				// Translators: URL, Product name
				__( '<a href="%1$s">%2$s</a> license not activated or valid', 'advanced-responsive-video-embedder' ),
				esc_url( 'https://nextgenthemes.com/plugins/arve/documentation/installation/' ),
				$value['name']
			);

			throw new \Exception( esc_html( $msg ) );
		}
	endforeach;
}

function has_valid_key( string $product ): bool {
	$options = (array) get_option( 'nextgenthemes' );
	return ( ! empty( $options[ "{$product}_status" ] ) && 'valid' === $options[ "{$product}_status" ] );
}

/**
 * @return mixed
 */
function get_defined_key( string $slug ) {

	$constant_name = str_replace( '-', '_', strtoupper( $slug . '_KEY' ) );

	if ( defined( $constant_name ) && constant( $constant_name ) ) {
		return constant( $constant_name );
	} else {
		return false;
	}
}

function activate_product_key( string $product, string $key ): void {

	$product_id = get_products()[ $product ]['id'];

	$options                         = (array) get_option( 'nextgenthemes' );
	$options[ $product . '_status' ] = api_action( $product_id, $key, 'activate' );

	update_option( 'nextgenthemes', $options );
}

function activate_defined_key( string $file ): void {

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

function api_action( int $item_id, string $license, string $edd_action = 'check_license', string $edd_store_url = 'https://nextgenthemes.com' ): string {

	//return wp_json_encode( [ 'item_id' => $item_id, 'key' => $key, 'action' => $action ], JSON_PRETTY_PRINT );

	// Call the custom API.
	$response = remote_get_json(
		$edd_store_url,
		array(
			'timeout' => 10,
			'body'    => array(
				'edd_action' => $edd_action,
				'item_id'    => $item_id,
				'license'    => sanitize_text_field( $license ),
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

		if ( empty( $response['license'] ) ) {

			$message = sprintf(
				// Translators: Error message
				__( 'Error. Please report the following:%s', 'advanced-responsive-video-embedder' ),
				PHP_EOL . wp_json_encode( $response, JSON_PRETTY_PRINT )
			);
		} else {
			$message = $response['license'];
		}
	}

	return $message;
}

function get_api_error_message( array $license_data ): string {

	if ( false !== $license_data['success'] || empty( $license_data['error'] ) ) {
		return '';
	}

	switch ( $license_data['error'] ) {
		case 'expired':
			$message = sprintf(
				// Translators: Date
				__( 'Your license key expired on %s.', 'advanced-responsive-video-embedder' ),
				date_i18n( get_option( 'date_format' ), strtotime( $license_data['expires'], time() ) )
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

	return $message;
}
