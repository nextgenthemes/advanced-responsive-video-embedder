<?php
namespace nextgenthemes\admin;

function get_products() {

	$products = array(
		'arve_pro' => array(
			'name'    => 'Advanced Responsive Video Embedder Pro',
			'type'    => 'plugin',
			'author'  => 'Nicolas Jonas',
			'url'     => 'https://nextgenthemes.com/plugins/arve-pro/',
		),
		'arve_amp' => array(
			'name'   => 'ARVE Accelerated Mobile Pages Addon',
			'type'   => 'plugin',
			'author' => 'Nicolas Jonas',
			'url'    => 'https://nextgenthemes.com/plugins/arve-amp/',
		),
		'edd_mycelium_gear_gateway' => array(
			'name'   => 'EDD MyCelium Gear Gateway',
			'type'   => 'plugin',
			'author' => 'Nicolas Jonas',
			'url'    => 'https://nextgenthemes.com/plugins/edd-mycelium-gear-gateway/',
		),
	);

	$products = apply_filters( 'nextgenthemes_products', $products );

	foreach ( $products as $key => $value ) {

		$products[ $key ]['slug']      = $key;
		$products[ $key ]['installed'] = false;
		$products[ $key ]['active']    = false;
		$products[ $key ]['valid_key'] = has_valid_key( $key );

		$version_define = strtoupper( $key ) . '_VERSION';
		$file_define    = strtoupper( $key ) . '_FILE';

		if( defined( $version_define ) ) {
			$products[ $key ]['version'] = constant( $version_define );
		}
		if( defined( $file_define ) ) {
			$products[ $key ]['file'] = constant( $file_define );
		}

		if ( 'plugin' == $value['type'] ) {

			$file_slug = str_replace( '_', '-', $key );

			$products[ $key ]['installed'] = is_plugin_installed( "$file_slug/$file_slug.php" );

			if ( ! empty( $products[ $key ]['file'] ) ) {
				$plugin_basename = plugin_basename( $products[ $key ]['file'] );
				$products[ $key ]['active'] = is_plugin_active( $plugin_basename );
			}
		}
	}

	return $products;
}

function is_plugin_installed( $plugin_basename ) {

	$plugins = get_plugins();

	if( array_key_exists( $plugin_basename, $plugins ) ) {
		return true;
	} else {
		return false;
	}
}

function register_settings() {

	add_settings_section(
		'keys',                      # id,
		__( 'Licenses', TEXTDOMAIN ), # title,
		'__return_empty_string',     # callback,
		'nextgenthemes-licenses'     # page
	);

	foreach ( get_products() as $product_slug => $product ) :

		$option_basename = "nextgenthemes_{$product_slug}_key";
		$option_keyname  = $option_basename . '[key]';

		add_settings_field(
			$option_keyname,              # id,
			$product['name'],             # title,
			__NAMESPACE__ . '\\key_callback', # callback,
			'nextgenthemes-licenses',     # page,
			'keys',                       # section
			array(                        # args
				'product'         => $product,
				'label_for'       => $option_keyname,
				'option_basename' => $option_basename,
				'attr'            => array(
					'type'  => 'text',
					'id'    => $option_keyname,
					'name'  => $option_keyname,
					'class' => 'arve-license-input',
					'value' => get_defined_key( $product_slug ) ? __( 'is defined (wp-config.php?)', TEXTDOMAIN ) : get_key( $product_slug, 'option_only' ),
				)
			)
		);

		register_setting(
			'nextgenthemes',  # option_group
			$option_basename, # option_name
			__NAMESPACE__ . '\\validate_license' # validation callback
		);

	endforeach;
}

function key_callback( $args ) {

	echo '<p>';

	printf( '<input%s>', html_attr( array(
		'type'  => 'hidden',
		'id'    => $args['option_basename'] . '[product]',
		'name'  => $args['option_basename'] . '[product]',
		'value' => $args['product']['slug'],
	) ) );

	printf(
		'<input%s%s>',
		html_attr( $args['attr'] ),
		get_defined_key( $args['product']['slug'] ) ? ' disabled' : ''
	);

	$defined_key = get_defined_key( $args['product']['slug'] );
	$key         = get_key(         $args['product']['slug'] );

	if( $defined_key || ! empty( $key ) ) {

		submit_button( __('Activate License',   TEXTDOMAIN ), 'primary',   $args['option_basename'] . '[activate_key]',   false );
		submit_button( __('Deactivate License', TEXTDOMAIN ), 'secondary', $args['option_basename'] . '[deactivate_key]', false );
		submit_button( __('Check License',      TEXTDOMAIN ), 'secondary', $args['option_basename'] . '[check_key]',      false );
  }
	echo '</p>';

  echo '<p>';
  echo __( 'License Status: ', TEXTDOMAIN ) . get_key_status( $args['product']['slug'] );
  echo '</p>';

  if( $args['product']['installed'] && ! $args['product']['active'] ) {
		printf( '<strong>%s</strong>', __( 'Plugin is installed but not activated', TEXTDOMAIN ) );
	} elseif( ! $args['product']['active'] ) {
    printf(
			'<a%s>%s</a>',
			html_attr( array(
				'href'  => $args['product']['url'],
				'class' => 'button button-primary',
			) ),
			__( 'Not installed, check it out', TEXTDOMAIN )
		);
  }
}

function validate_license( $input ) {

	if( ! is_array( $input ) ) {
		return sanitize_text_field( $input );
	}

	$product = $input['product'];

	if ( $defined_key = get_defined_key( $product ) ) {
		$option_key = $key = $defined_key;
	} else {
		$key        = sanitize_text_field( $input['key'] );
		$option_key = get_key( $product );
	}

	if( ( $key != $option_key ) || isset( $input['activate_key'] ) ) {

		api_update_key_status( $product, $key, 'activate' );

	} elseif ( isset( $input['deactivate_key'] ) ) {

		api_update_key_status( $product, $key, 'deactivate' );

	} elseif ( isset( $input['check_key'] ) ) {

		api_update_key_status( $product, $key, 'check' );
	}

	return $key;
}

function get_key( $product, $option_only = false ) {

	if( ! $option_only && $defined_key = get_defined_key( $product ) ) {
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
	return ( 'valid' == get_key_status( $product ) ) ? true : false;
}

function api_update_key_status( $product, $key, $action ) {

	$products   = get_products();
	$key_status = api_action( $products[ $product ]['name'], $key, $action );

	update_key_status( $product, $key_status );
}

function get_defined_key( $slug ) {

	$constant_name = str_replace( '-', '_', strtoupper( $slug . '_KEY' ) );

	if( defined( $constant_name ) && constant( $constant_name ) ) {
		return constant( $constant_name );
	} else {
		return false;
	}
}

function licenses_page() {
?>
	<div class="wrap">

		<h2><?php esc_html_e( get_admin_page_title() ); ?></h2>

		<form method="post" action="options.php">
			<?php do_settings_sections( 'nextgenthemes-licenses' ); ?>
			<?php settings_fields( 'nextgenthemes' ); ?>
			<?php submit_button( __( 'Save Changes' ), 'primary', 'submit', false ); ?>
		</form>

	</div>
<?php
}

function init_edd_updaters() {

	$products = get_products();

	foreach ( $products as $product ) {

		if ( 'plugin' == $product['type'] && ! empty( $product['file'] ) ) {
			init_plugin_updater( $product );
		} elseif ( 'theme' == $product['type'] ) {
			init_theme_updater( $product );
		}
	}
}

function init_plugin_updater( $product ) {

	// setup the updater
	new \EDD_SL_Plugin_Updater(
		'https://nextgenthemes.com',
		$product['file'],
		array(
			'version' 	=> $product['version'],
			'license' 	=> get_key( $product['slug'] ),
			'item_name' => $product['name'],
			'author' 	  => $product['author']
		)
	);
}

function init_theme_updater( $product ) {

	new \EDD_Theme_Updater(
		array(
			'remote_api_url' 	=> 'https://nextgenthemes.com',
			'version' 			  => $product['version'],
			'license' 			  => get_key( $product['slug'] ),
			'item_name' 		  => $product['name'],
			'author'			    => $product['author'],
			'theme_slug'      => $product['slug'],
			'download_id'     => $product['download_id'], // Optional, used for generating a license renewal link
			#'renew_url'       => $product['renew_link'], // Optional, allows for a custom license renewal link
		),
		array(
			'theme-license'             => __( 'Theme License', TEXTDOMAIN ),
			'enter-key'                 => __( 'Enter your theme license key.', TEXTDOMAIN ),
			'license-key'               => __( 'License Key', TEXTDOMAIN ),
			'license-action'            => __( 'License Action', TEXTDOMAIN ),
			'deactivate-license'        => __( 'Deactivate License', TEXTDOMAIN ),
			'activate-license'          => __( 'Activate License', TEXTDOMAIN ),
			'status-unknown'            => __( 'License status is unknown.', TEXTDOMAIN ),
			'renew'                     => __( 'Renew?', TEXTDOMAIN ),
			'unlimited'                 => __( 'unlimited', TEXTDOMAIN ),
			'license-key-is-active'     => __( 'License key is active.', TEXTDOMAIN ),
			'expires%s'                 => __( 'Expires %s.', TEXTDOMAIN ),
			'expires-never'             => __( 'Lifetime License.', TEXTDOMAIN ),
			'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', TEXTDOMAIN ),
			'license-key-expired-%s'    => __( 'License key expired %s.', TEXTDOMAIN ),
			'license-key-expired'       => __( 'License key has expired.', TEXTDOMAIN ),
			'license-keys-do-not-match' => __( 'License keys do not match.', TEXTDOMAIN ),
			'license-is-inactive'       => __( 'License is inactive.', TEXTDOMAIN ),
			'license-key-is-disabled'   => __( 'License key is disabled.', TEXTDOMAIN ),
			'site-is-inactive'          => __( 'Site is inactive.', TEXTDOMAIN ),
			'license-status-unknown'    => __( 'License status is unknown.', TEXTDOMAIN ),
			'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", TEXTDOMAIN ),
			'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', TEXTDOMAIN ),
		)
	);
}

function api_action( $item_name, $key, $action ) {

	if ( ! in_array( $action, array( 'activate', 'deactivate', 'check' ) ) ) {
		wp_die( 'invalid action' );
	}

	$license_data = remote_get(
		'https://nextgenthemes.com',
		array(
			'timeout'   => 15,
			'sslverify' => true,
			'body'      => array(
				'edd_action' => $action . '_license',
				'license'    => sanitize_text_field( $key ),
				'item_name'  => urlencode( $item_name ),
				'url'        => home_url(),
			)
		)
	);

	if ( is_wp_error( $response ) ) {

		$message = $response->get_error_message();

	} else {

		if ( false === $license_data->success ) :

			switch( $license_data->error ) {

				case 'expired' :
					$message = sprintf(
						__( 'Your license key expired on %s.', TEXTDOMAIN ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :
					$message = __( 'Your license key has been disabled.', TEXTDOMAIN );
					break;

				case 'missing' :
					$message = __( 'Invalid license.', TEXTDOMAIN );
					break;

				case 'invalid' :
				case 'site_inactive' :
					$message = __( 'Your license is not active for this URL.', TEXTDOMAIN );
					break;

				case 'item_name_mismatch' :
					$message = sprintf( __( 'This appears to be an invalid license key for %s.', TEXTDOMAIN ), $item_name );
					break;

				case 'no_activations_left' :
					$message = __( 'Your license key has reached its activation limit.', TEXTDOMAIN );
					break;

				default :
					$message = sprintf(
						__( 'Error: %s.', TEXTDOMAIN ),
						$license_data->error
					);
					break;
			}

		endif; // false === $license_data->success
	}

	if( empty( $message ) ) {
		$message = $license_data->license;
	}

	return $message;
}
