<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use Nextgenthemes\ARVE\Common\License;
use function Nextgenthemes\ARVE\Common\Utils\attr;

function get_products() {

	$products = array(
		'arve_pro'          => array(
			'namespace' => 'ARVE\Pro',
			'name'      => 'ARVE Pro',
			'id'        => 1253,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-pro/',
		),
		'arve_amp'          => array(
			'namespace' => 'ARVE\AMP',
			'name'      => 'ARVE AMP',
			'id'        => 16941,
			'type'      => 'plugin',
			'author'    => 'Nicolas Jonas',
			'url'       => 'https://nextgenthemes.com/plugins/arve-amp/',
		),
		'arve_random_video' => array(
			'namespace' => 'ARVE\RandomVideo',
			'name'      => 'ARVE Random Video',
			'id'        => 31933,
			'type'   => 'plugin',
			'author' => 'Nicolas Jonas',
			'url'    => 'https://nextgenthemes.com/plugins/arve-random-video/',
		)
	);

	$products = apply_filters( 'nextgenthemes_products', $products );
	$plugins  = get_plugins();

	foreach ( $products as $key => $value ) :

		$products[ $key ]['slug']      = $key;
		$products[ $key ]['installed'] = false;
		$products[ $key ]['active']    = false;
		$products[ $key ]['valid_key'] = License\has_valid_key( $key );

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
			$products[ $key ]['file'] = constant( $file );
		}

		if ( 'plugin' === $value['type'] ) {

			$file_slug = str_replace( '_', '-', $key );

			$products[ $key ]['installed'] = array_key_exists( "$file_slug/$file_slug.php", $plugins );
			$products[ $key ]['active']    = is_plugin_active( "$file_slug/$file_slug.php" );
		}
	endforeach;

	return $products;
}

function menus() {

	$plugin_screen_hook_suffix = add_options_page(
		__( 'ARVE Licenses', 'advanced-responsive-video-embedder' ),
		__( 'ARVE Licenses', 'advanced-responsive-video-embedder' ),
		'manage_options',
		'nextgenthemes-licenses',
		'nextgenthemes_licenses_page'
	);
}

function register_settings() {

	add_settings_section(
		'keys',                      # id,
		__( 'Licenses', 'advanced-responsive-video-embedder' ), # title,
		'__return_empty_string',     # callback,
		'nextgenthemes-licenses'     # page
	);

	foreach ( get_products() as $product_slug => $product ) :

		$option_basename = "nextgenthemes_{$product_slug}_key";
		$option_keyname  = $option_basename . '[key]';
		$key             = License\get_key( $product_slug, 'option_only' );
		$key             = is_array( $key ) ? 'error should not be array' : (string) $key;

		add_settings_field(
			$option_keyname,                 // id,
			$product['name'],                // title,
			__NAMESPACE__ . '\key_callback', // callback,
			'nextgenthemes-licenses',        // page,
			'keys',                          // section
			[                                // args
				'product'         => $product,
				'label_for'       => $option_keyname,
				'option_basename' => $option_basename,
				'attr'            => [
					'type'  => 'text',
					'id'    => $option_keyname,
					'name'  => $option_keyname,
					'class' => 'arve-license-input',
					'value' => License\get_defined_key( $product_slug ) ? __( 'is defined (wp-config.php)', 'advanced-responsive-video-embedder' ) : $key,
				]
			]
		);

		register_setting(
			'nextgenthemes',  # option_group
			$option_basename, # option_name
			'nextgenthemes_validate_license' # validation callback
		);

	endforeach;
}

function key_callback( $args ) {

	echo '<p>';

	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	printf(
		'<input%s>',
		attr(
			[
				'type'  => 'hidden',
				'id'    => $args['option_basename'] . '[product]',
				'name'  => $args['option_basename'] . '[product]',
				'value' => $args['product']['slug']
			]
		)
	);
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

	printf(
		'<input%s%s>',
		attr( $args['attr'] ),
		License\get_defined_key( $args['product']['slug'] ) ? ' disabled' : ''
	);

	$defined_key = License\get_defined_key( $args['product']['slug'] );
	$key         = License\get_key( $args['product']['slug'] );

	if ( $defined_key || ! empty( $key ) ) {
		submit_button( __( 'Activate License', 'advanced-responsive-video-embedder' ), 'primary', $args['option_basename'] . '[activate_key]', false );
		submit_button( __( 'Deactivate License', 'advanced-responsive-video-embedder' ), 'secondary', $args['option_basename'] . '[deactivate_key]', false );
		submit_button( __( 'Check License', 'advanced-responsive-video-embedder' ), 'secondary', $args['option_basename'] . '[check_key]', false );
	}

	echo '</p>';

	echo '<p>';
	// Translators: License Status
	echo esc_html( sprintf( __( 'License Status: %s', 'advanced-responsive-video-embedder' ), License\get_key_status( $args['product']['slug'] ) ) );
	echo '</p>';

	if ( $args['product']['installed'] && ! $args['product']['active'] ) {

		printf( '<strong>%s</strong>', esc_html__( 'Plugin is installed but not activated', 'advanced-responsive-video-embedder' ) );

	} elseif ( ! $args['product']['active'] ) {
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		printf(
			'<a%s>%s</a>',
			attr(
				array(
					'href'  => $args['product']['url'],
					'class' => 'button button-primary',
				)
			),
			__( 'Not installed, check it out', 'advanced-responsive-video-embedder' )
		);
		// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

function validate_license( $input ) {

	if ( ! is_array( $input ) ) {
		return sanitize_text_field( $input );
	}

	$product     = $input['product'];
	$defined_key = License\get_defined_key( $product );

	if ( $defined_key ) {
		$option_key = $defined_key;
		$key        = $defined_key;
	} else {
		$key        = sanitize_text_field( $input['key'] );
		$option_key = License\get_key( $product );
	}

	if ( ( $key !== $option_key ) || isset( $input['activate_key'] ) ) {

		api_update_key_status( $product, $key, 'activate' );

	} elseif ( isset( $input['deactivate_key'] ) ) {

		api_update_key_status( $product, $key, 'deactivate' );

	} elseif ( isset( $input['check_key'] ) ) {

		api_update_key_status( $product, $key, 'check' );
	}

	return $key;
}

function api_update_key_status( $product, $key, $action ) {

	$products   = get_products();
	$key_status = api_action( $products[ $product ]['id'], $key, $action );

	License\update_key_status( $product, $key_status );
}

function licenses_page() {
	?>
	<div class="wrap">

		<h2><?php esc_html( get_admin_page_title() ); ?></h2>

		<form method="post" action="options.php">

			<?php do_settings_sections( 'nextgenthemes-licenses' ); ?>
			<?php settings_fields( 'nextgenthemes' ); ?>
			<?php submit_button( __( 'Save Changes', 'advanced-responsive-video-embedder' ), 'primary', 'submit', false ); ?>
		</form>

	</div>
	<?php
}

function init_edd_updaters() {

	$products = get_products();

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
		array(
			'version' => $product['version'],
			'license' => License\get_key( $product['slug'] ),
			'item_id' => $product['id'],
			'author'  => $product['author'],
		)
	);
}

function init_theme_updater( $product ) {

	new EDD\ThemeUpdater(
		array(
			'remote_api_url' => 'https://nextgenthemes.com',
			'version'        => $product['version'],
			'license'        => License\get_key( $product['slug'] ),
			'item_id'        => $product['name'],
			'author'         => $product['id'],
			'theme_slug'     => $product['slug'],
			'download_id'    => $product['download_id'], // Optional, used for generating a license renewal link
			#'renew_url'     => $product['renew_link'], // Optional, allows for a custom license renewal link
		),
		array(
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
		)
	);
}

function api_action( $item_id, $key, $action ) {

	if ( ! in_array( $action, [ 'activate', 'deactivate', 'check' ], true ) ) {
		wp_die( 'invalid action' );
	}

	// data to send in our API request
	$api_params = array(
		'edd_action' => $action . '_license',
		'license'    => sanitize_text_field( $key ),
		'item_id'    => $item_id,
		'url'        => home_url()
	);

	// Call the custom API.
	$response = wp_remote_post(
		'https://nextgenthemes.com',
		array(
			'timeout'   => 15,
			'sslverify' => true,
			'body'      => $api_params
		)
	);

	// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.', 'advanced-responsive-video-embedder' );
		}
	} else {
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		$message      = get_api_error_message( $license_data );
	}

	if ( empty( $message ) ) {

		if ( empty( $license_data->license ) ) {

			$textarea_dump = textarea_dump( $response );

			$message = sprintf(
				// Translators: Error message
				__( 'Error. Please report the following:<br> %s', 'advanced-responsive-video-embedder' ),
				$textarea_dump
			);
		} else {
			$message = $license_data->license;
		}
	}

	return $message;
}

function get_api_error_message() {

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
