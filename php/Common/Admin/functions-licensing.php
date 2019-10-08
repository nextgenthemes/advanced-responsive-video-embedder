<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;

function api_update_key_status( $product, $key, $action ) {

	$products   = Common\get_products();
	$key_status = api_action( $products[ $product ]['id'], $key, $action );

	Common\update_key_status( $product, $key_status );
}

function init_edd_updaters() {

	$products = Common\get_products();

	foreach ( $products as $product ) {

		if ( 'plugin' === $product['type'] && ! empty( $product['file'] ) ) {
			init_plugin_updater( $product );
		} elseif ( 'theme' === $product['type'] ) {
			init_theme_updater( $product );
		}
	}
}

function init_plugin_updater( $product ) {

	// setup the updater
	new EDD\PluginUpdater(
		apply_filters( 'nextgenthemes_api_url', 'https://nextgenthemes.com' ),
		$product['file'],
		[
			'version' => $product['version'],
			'license' => Common\get_key( $product['slug'] ),
			'item_id' => $product['id'],
			'author'  => $product['author'],
		]
	);
}

function init_theme_updater( $product ) {

	new EDD\ThemeUpdater(
		[
			'remote_api_url' => 'https://nextgenthemes.com',
			'version'        => $product['version'],
			'license'        => Common\get_key( $product['slug'] ),
			'item_id'        => $product['id'],
			'author'         => $product['author'],
			'theme_slug'     => $product['slug'],
			'download_id'    => $product['download_id'], // Optional, used for generating a license renewal link
			#'renew_url'     => $product['renew_link'], // Optional, allows for a custom license renewal link
		],
		[
			'theme-license'             => __( 'Theme License', 'advanced-responsive-video-embedder' ),
			'enter-key'                 => __( 'Enter your theme license key.', 'advanced-responsive-video-embedder' ),
			'license-key'               => __( 'License Key', 'advanced-responsive-video-embedder' ),
			'license-action'            => __( 'License Action', 'advanced-responsive-video-embedder' ),
			'deactivate-license'        => __( 'Deactivate License', 'advanced-responsive-video-embedder' ),
			'activate-license'          => __( 'Activate License', 'advanced-responsive-video-embedder' ),
			'status-unknown'            => __( 'License status is unknown.', 'advanced-responsive-video-embedder' ),
			'renew'                     => __( 'Renew?', 'advanced-responsive-video-embedder' ),
			'unlimited'                 => __( 'unlimited', 'advanced-responsive-video-embedder' ),
			'license-key-is-active'     => __( 'License key is active.', 'advanced-responsive-video-embedder' ),
			// Translators: Date
			'expires%s'                 => __( 'Expires %s.', 'advanced-responsive-video-embedder' ),
			'expires-never'             => __( 'Lifetime License.', 'advanced-responsive-video-embedder' ),
			// Translators: x of x sites activated
			'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'advanced-responsive-video-embedder' ),
			// Translators: Date
			'license-key-expired-%s'    => __( 'License key expired %s.', 'advanced-responsive-video-embedder' ),
			'license-key-expired'       => __( 'License key has expired.', 'advanced-responsive-video-embedder' ),
			'license-keys-do-not-match' => __( 'License keys do not match.', 'advanced-responsive-video-embedder' ),
			'license-is-inactive'       => __( 'License is inactive.', 'advanced-responsive-video-embedder' ),
			'license-key-is-disabled'   => __( 'License key is disabled.', 'advanced-responsive-video-embedder' ),
			'site-is-inactive'          => __( 'Site is inactive.', 'advanced-responsive-video-embedder' ),
			'license-status-unknown'    => __( 'License status is unknown.', 'advanced-responsive-video-embedder' ),
			'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", 'advanced-responsive-video-embedder' ),
			// phpcs:ignore
			'update-available'          => __( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'advanced-responsive-video-embedder' ),
		]
	);
}

function api_action( $item_id, $key, $action = 'check' ) {

	if ( ! in_array( $action, [ 'activate', 'deactivate', 'check' ], true ) ) {
		wp_die( 'invalid action' );
	}

	// Call the custom API.
	$response = Common\ngt_remote_get_json(
		'https://nextgenthemes.com',
		[
			'timeout' => 10,
			'body'    => [
				'edd_action' => $action . '_license',
				'license'    => sanitize_text_field( $key ),
				'item_id'    => $item_id,
				'url'        => home_url()
			]
		]
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
				date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
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

function dump( $var ) {
	ob_start();
	// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_dump
	var_dump( $var );
	return ob_get_clean();
}

function textarea_dump( $var ) {
	return sprintf( '<textarea style="width: 100%; height: 70vh;">%s</textarea>', esc_textarea( dump( $var ) ) );
}
