<?php
namespace Nextgenthemes\ARVE\Common;

function nextgenthemes_settings_instance() {

	static $inst = null;

	if ( null === $inst ) {

		$inst = new Admin\Settings(
			[
				'namespace'           => 'nextgenthemes_licenses',
				'settings'            => nextgenthemes_settings(),
				'menu_title'          => esc_html__( 'NextGenThemes Settings', 'advanced-responsive-video-embedder' ),
				'settings_page_title' => esc_html__( 'NextGenThemes Settings', 'advanced-responsive-video-embedder' ),
			]
		);
	}

	return $inst;
}

function nextgenthemes_settings() {

	$products = get_products();

	foreach ( $products as $key => $value ) {
		$settings[ $key ] = [
			'default' => '',
			'option'  => true,
			'tag'     => 'main',
			// translators: %s is Product name
			'label'   => sprintf( esc_html__( '%s license Key', 'advanced-responsive-video-embedder' ), $value['name'] ),
			'type'    => 'string',
			'ui'      => 'licensekey',
		];
	}

	foreach ( $products as $key => $value ) {
		$settings[ $key . '_beta' ] = [
			'default' => false,
			'option'  => true,
			'tag'     => 'main',
			// translators: Product name
			'label'   => sprintf( esc_html__( '%s beta updates', 'advanced-responsive-video-embedder' ), $value['name'] ),
			'type'    => 'boolean',
		];
	}

	$settings['cdn'] = [
		'default' => false,
		'option'  => true,
		'tag'     => 'main',
		'label'   => esc_html__( 'Use jsDelivr CDN for some assets', 'advanced-responsive-video-embedder' ),
		'type'    => 'boolean',
	];

	$settings['action'] = [
		'default' => '',
		'option'  => true,
		'tag'     => 'main',
		'label'   => esc_html__( 'Action', 'advanced-responsive-video-embedder' ),
		'type'    => 'string',
		'ui'      => 'hidden',
	];

	return $settings;
}

function get_products() {

	$products = [
		'arve_pro'          => [
			'namespace' => 'ARVE\Pro',
			'name'      => 'ARVE Pro',
			'id'        => 1253,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-pro/',
		],
		'arve_amp'          => [
			'namespace' => 'ARVE\AMP',
			'name'      => 'ARVE AMP',
			'id'        => 16941,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-amp/',
		],
		'arve_random_video' => [
			'namespace' => 'ARVE\RandomVideo',
			'name'      => 'ARVE Random Video',
			'id'        => 31933,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-random-video/',
		],
	];

	$products = apply_filters( 'nextgenthemes_products', $products );

	foreach ( $products as $key => $value ) :

		$products[ $key ]['active']    = false;
		$products[ $key ]['file']      = false;
		$products[ $key ]['installed'] = false;
		$products[ $key ]['slug']      = $key;
		$products[ $key ]['valid_key'] = has_valid_key( $key );

		$version_define = strtoupper( $key ) . '_VERSION';
		$file_define    = strtoupper( $key ) . '_FILE';

		if ( defined( $version_define ) ) {
			$products[ $key ]['version'] = constant( $version_define );
		}

		if ( defined( $file_define ) ) {
			$products[ $key ]['file'] = constant( $file_define );
		}

		$version = "\\Nextgenthemes\\{$value['namespace']}\\VERSION";
		$file    = "\\Nextgenthemes\\{$value['namespace']}\\FILE";

		if ( defined( $version ) ) {
			$products[ $key ]['version'] = constant( $version );
		}

		if ( defined( $file ) ) {
			$products[ $key ]['file']   = constant( $file );
			$products[ $key ]['active'] = true;
		}
	endforeach;

	return $products;
}

function init_edd_updaters() {

	$products = get_products();

	foreach ( $products as $product ) {

		if ( 'plugin' === $product['type'] && $product['file'] ) {
			init_plugin_updater( $product );
		} elseif ( 'theme' === $product['type'] ) {
			init_theme_updater( $product );
		}
	}
}

function init_plugin_updater( $product ) {

	$ngt_options = nextgenthemes_settings_instance()->options;

	// setup the updater
	new EDD\PluginUpdater(
		apply_filters( 'nextgenthemes_api_url', 'https://nextgenthemes.com' ),
		$product['file'],
		[
			'version' => $product['version'],
			'license' => get_key( $product['slug'] ),
			'item_id' => $product['id'],
			'author'  => $product['author'],
			'beta'    => $ngt_options[ $products['slug'] . '_beta' ],
		]
	);
}

function init_theme_updater( $product ) {

	new EDD\ThemeUpdater(
		[
			'remote_api_url' => 'https://nextgenthemes.com',
			'version'        => $product['version'],
			'license'        => get_key( $product['slug'] ),
			'item_id'        => $product['id'],
			'author'         => $product['author'],
			'theme_slug'     => $product['slug'],
			'download_id'    => $product['download_id'], // Optional, used for generating a license renewal link
			#'renew_url'     => $product['renew_link'], // Optional, allows for a custom license renewal link // phpcs:ignore
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
	$response = ngt_remote_get_json(
		'https://nextgenthemes.com',
		[
			'timeout' => 10,
			'body'    => [
				'edd_action' => $action . '_license',
				'license'    => sanitize_text_field( $key ),
				'item_id'    => $item_id,
				'url'        => home_url(),
			],
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
