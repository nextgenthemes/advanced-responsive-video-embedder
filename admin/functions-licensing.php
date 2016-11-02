<?php

define( 'NEXTGENTHEMES_API_URL', 'https://nextgenthemes.com' );

add_action( 'admin_init', 'nextgenthemes_register_settings' );
add_action( 'admin_menu', 'nextgenthemes_menus' );

function nextgenthemes_get_products() {

	$products['arve_pro'] = array(
    'type'   => 'plugin',
    'name'   => 'Advanced Responsive Video Embedder Pro',
		'author' => 'Nicolas Jonas'
	);

	$products['arve_webtorrent'] = array(
		'name'   => 'ARVE Webtorrent Addon',
		'type'   => 'plugin',
		'author' => 'Nicolas Jonas'
	);

	$products = apply_filters( 'nextgenthemes_products', $products );

	foreach ( $products as $key => $value ) {
		$product[ $key ]['slug'] = $key;
	}

	return $products;
}

/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function nextgenthemes_menus() {

 	$plugin_screen_hook_suffix = add_menu_page(
 		__( 'Nextgenthemes', ARVE_SLUG ), # Page Title
 		__( 'Nextgenthemes', ARVE_SLUG ), # Menu Tile
 		'manage_options',                 # capability
 		'nextgenthemes',                  # menu-slug
 		'__return_empty_string',          # function
		'dashicons-admin-settings',       # icon_url
		null                              # position
 	);

	/*
  add_submenu_page(
    'nextgenthemes',                      # parent_slug
    __( 'Addons and Themes', ARVE_SLUG ), # Page Title
    __( 'Addons and Themes', ARVE_SLUG ), # Menu Tile
    'manage_options',                     # capability
    'nextgenthemes',                      # menu-slug
    function() {
      require_once plugin_dir_path( __FILE__ ) . 'html-ad-page.php';
    }
  );
	*/

	add_submenu_page(
		'nextgenthemes',              # parent_slug
		__( 'Licenses', ARVE_SLUG ),  # Page Title
		__( 'Licenses', ARVE_SLUG ),  # Menu Tile
		'manage_options',             # capability
		'nextgenthemes-licenses',     # menu-slug
		'nextgenthemes_licenses_page' # function
	);
}

function nextgenthemes_register_settings() {

	add_settings_section(
		'keys',                      # id,
		__( 'Licenses', ARVE_SLUG ), # title,
		'__return_empty_string',     # callback,
		'nextgenthemes-licenses'     # page
	);

	foreach ( nextgenthemes_get_products() as $product_slug => $product ) {

		$field_id = "nextgenthemes_keys[$product_slug][key]";

		add_settings_field(
			$field_id,                 # id,
			$product['name'],          # title,
			'nextgenthemes_input_key', # callback,
			'nextgenthemes-licenses',  # page,
			'keys',                    # section
			array(                     # args
				'product_slug'    => $product_slug,
				'product'         => $product,
				'label_for'       => "nextgenthemes_keys[$product_slug][key]",
				'option_basename' => "nextgenthemes_keys[$product_slug]",
				'attr'            => array(
					'type'   => 'text',
					'class'  => 'large-text',
					'id'     => $field_id,
					'name'   => $field_id,
					'value'  => nextgenthemes_has_key_defined( $product_slug ) ? __( 'defined in wp-config.php', ARVE_SLUG ) : nextgenthemes_get_key( $product_slug ),
				)
			)
		);
	}

	register_setting(
		'nextgenthemes',      # option_group
		'nextgenthemes_keys', # option_name
		'nextgenthemes_validate_licenses'  # sanitize_callback
	);
}

function nextgenthemes_licenses_page() {
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

function nextgenthemes_validate_licenses( $input ) {

	foreach ( $input as $product_slug => $values ) :

		if ( nextgenthemes_has_key_defined( $product_slug ) ) {
			$input_key = $option_key = constant( strtoupper( $product_slug . '_KEY' ) );
		} else {
			$input_key  = sanitize_text_field( $values['key'] );
			$option_key = nextgenthemes_get_key( $product_slug );
			nextgenthemes_update_key( $product_slug, $input_key );
		}

		if( ( $input_key !== $option_key ) || isset( $input[ $product_slug ]['activate_key'] ) ) {

			nextgenthemes_api_update_key_status( $product_slug, $input_key, 'activate' );

		} elseif ( isset( $input[ $product_slug ]['deactivate_key'] ) ) {

			nextgenthemes_api_update_key_status( $product_slug, $input_key, 'deactivate' );

		} elseif ( isset( $input[ $product_slug ]['check_key'] ) ) {

			nextgenthemes_api_update_key_status( $product_slug, $input_key, 'check' );
		}

	endforeach;
}

function nextgenthemes_get_key( $product_slug ) {
	return get_option( "nextgenthemes_{$product_slug}_key" );
}
function nextgenthemes_update_key( $product_slug, $key ) {
	update_option( "nextgenthemes_{$product_slug}_key", $key );
}

function nextgenthemes_get_key_status( $product_slug ) {
	return get_option( "nextgenthemes_{$product_slug}_key_status" );
}
function nextgenthemes_update_key_status( $product_slug, $key ) {
	update_option( "nextgenthemes_{$product_slug}_key_status", $key );
}

function nextgenthemes_api_update_key_status( $slug, $key, $action ) {

	$products   = nextgenthemes_get_products();
	$key_status = nextgenthemes_api_action( $products[ $slug ]['name'], $key, $action );

	nextgenthemes_update_key_status( $slug, $key_status );
}

function nextgenthemes_has_key_defined( $slug ) {

	$constant_name = strtoupper( $slug . '_KEY' );

	return ( defined( $constant_name ) && ! empty( constant( $contant_name ) ) ) ? true : false;
}

function nextgenthemes_input_key( $args ) {

	printf(
		'<p><input%s%s>',
		arve_attr( $args['attr'] ),
		nextgenthemes_has_key_defined( $args['product_slug'] ) ? ' disabled' : ''
	);

	if( ! empty( nextgenthemes_get_key( $args['product_slug'] ) ) ) {

		submit_button( __('Activate License',   ARVE_SLUG ), 'primary',   $args['option_basename'] . '[activate_key]',   false );
		submit_button( __('Deactivate License', ARVE_SLUG ), 'secondary', $args['option_basename'] . '[deactivate_key]', false );
		submit_button( __('Check License',      ARVE_SLUG ), 'secondary', $args['option_basename'].  '[check_key]',      false );
  }

	echo '</p>';
  echo '<p>';
  echo __( 'License Status: ', ARVE_SLUG ) . nextgenthemes_get_key_status( $args['product_slug'] );
  echo '</p>';

  if ( 'plugin' == $args['product']['type'] ) {

    if ( ! empty( $args['product']['file'] ) ) {
      $plugin_file = basename( dirname( $args['product']['file'] ) ) . DIRECTORY_SEPARATOR . basename( $args['product']['file'] );
    }

    if ( ! empty( $plugin_file ) && is_plugin_active( $plugin_file ) ) {
      _e( 'Plugin is activated', ARVE_SLUG );
    } else {
      _e( 'Plugin not active', ARVE_SLUG );
    }
  }
}

function nextgenthemes_init_edd_updater( $item_slug, $file ) {

	foreach ( nextgenthemes_get_products() as $product ) {

		if ( 'plugin' == $product['type'] && ! empty( $product['file'] ) ) {
			nextgenthemes_init_plugin_updater( $product );
		}
		if ( 'theme' == $product['type'] ) {

		}
	}
}

function nextgenthemes_init_plugin_updater( $plugin ) {

	$constant_name_base = str_replace( '-', '_', strtoupper( $plugin['slug'] ) );
	$key_constant_name  = $constant_name_base . '_KEY';

	if( defined( $key_constant_name ) && ! empty( constant( $key_constant_name ) ) ) {
		$key = constant( $key_constant_name );
	} else {
		$key = get_option( 'nextgenthemes_' . $plugin['slug'] . '_key' );
	}

	// setup the updater
	$edd_updater = new EDD_SL_Plugin_Updater(
		NEXTGENTHEMES_API_URL,
		$plugin['file'],
		array(
			'version' 	=> $plugin['version'],
			'license' 	=> $key,
			'item_name' => $plugin['name'],
			'author' 	  => $plugin['author']
		)
	);
}

function nextgenthemes_api_action( $item_name, $key, $action ) {

	if ( ! in_array( $action, array( 'activate', 'deactivate', 'check' ) ) ) {
		wp_die( 'invalid action' );
	}

	// Data to send to the API
	$api_params = array(
		'edd_action' => $action . '_license',
		'license'    => sanitize_text_field( $key ),
		'item_name'  => urlencode( $item_name ),
		'url'        => home_url(),
	);

	$response = wp_remote_post( NEXTGENTHEMES_API_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	// Make sure there are no errors
	if ( is_wp_error( $response ) ) {
		return $response->get_error_message();
	}

	// Tell WordPress to look for updates
	set_site_transient( 'update_plugins', null );

	// Decode the license data
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	if( ! (bool) $license_data->success ) {
		set_transient( 'arve_license_error', $license_data, 1000 );

		if( empty( $license_data->error ) ) {
			return var_export( $license_data, true );
		} else {
			return $license_data->error;
		}
	} else {
		delete_transient( 'arve_license_error' );

		if( empty( $license_data->license ) ) {
			return 'API seems not to be accessible';
		} else {
			return $license_data->license;
		}
	}
}

function arve_pro_action_admin_notices() {

	$license_error = get_transient( 'arve_license_error' );

	if( false === $license_error ) {
		return;
	}

	if( ! empty( $license_error->error ) ) {

		switch( $license_error->error ) {

			case 'item_name_mismatch':

				$message = __( 'This license does not belong to the product you have entered it for.', 'arve-pro' );
				break;

			case 'no_activations_left':

				$message = __( 'This license does not have any activations left', 'arve-pro' );
				break;

			case 'expired':

				$message = __( 'This license key is expired. Please renew it.', 'arve-pro' );
				break;

			default:

				$message = sprintf( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'arve-pro' ), $license_error->error );
				break;
		}
	}

	if( ! empty( $message ) ) {

		echo '<div class="error">';
			echo '<p>' . $message . '</p>';
		echo '</div>';

	}

	delete_transient( 'edd_license_error' );
}
