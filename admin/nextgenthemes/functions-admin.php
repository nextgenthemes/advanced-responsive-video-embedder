<?php
namespace nextgenthemes\admin;

define( 'NEXTGENTHEMES_ADMIN_VERSION', '1.0' );

if ( ! defined( 'NEXTGENTHEMES_ADMIN_URL' ) ) {
	define( 'NEXTGENTHEMES_ADMIN_URL', plugin_dir_url( __FILE__ ) );
}

add_action( 'admin_init', __NAMESPACE__ . '\\init_edd_updaters', 0 );
add_action( 'admin_init', __NAMESPACE__ . '\\activation_notices' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_settings' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_menus' );

function ads_page() {

	wp_enqueue_style(
		'nextgenthemes-product-page',
		NEXTGENTHEMES_ADMIN_URL . 'product-page.css',
		array(),
		filemtime( __DIR__ . '/product-page.css' )
	);

	echo '<div id="nextgenthemes-ads">';
	products_html();
	echo '</div>';
}

function products_html() {

	$data = get_products_data();

	if( is_wp_error( $data ) ) {

		printf(
			__( '<div class="error"><p>%s</p></div>', ARVE_SLUG ),
			$data->get_error_message()
		);
	}

	foreach( $data->products as $product ) :

		if( defined( 'ARVE_VERSION' ) && 'arve' == $product->info->slug ) {
			continue;
		}
		if( defined( 'ARVE_PRO_VERSION' ) && 'arve-pro' == $product->info->slug ) {
			continue;
		}
		if( defined( 'ARVE_AMP_VERSION' ) && 'arve-amp' == $product->info->slug ) {
			continue;
		}

		?>
		<a href="<?php product_link( $product ); ?>">
			<?php if( ! empty( $product->info->thumbnail ) ) : ?>
				<figure><img src="<?php echo $product->info->thumbnail; ?>"></figure>
			<?php endif; ?>
			<?php echo "<h2>{$product->info->title}</h2>"; ?>
			<?php echo filter_html( $product->info->content ); ?>
			<?php if( ! empty( $product->pricing->amount ) && '0.00' == $product->pricing->amount ) : ?>
				<span>Free</span>
			<?php else : ?>
				<span>More Info</span>
			<?php endif; ?>
		</a>
		<?php

	endforeach;
}

function filter_html( $content ) {

	$allowed_tags = array(
		#'a'       => array( 'href' => true, 'title' => true ),
		'code'    => array(),
		'em'      => array(),
		'h2'      => array(),
		'li'      => array(),
		'ol'      => array(),
		'p'       => array(),
		'section' => array(),
		'span'    => array(),
		'strong'  => array(),
		'ul'      => array(),
	);

	return wp_kses( $content, $allowed_tags );
}

function product_link( $product ) {
	printf( 'https://nextgenthemes.com/%s/%s/', $product->info->category[0]->slug, $product->info->slug );
}

function plugin_install_search_url( $search_term ) {

	$path = "plugin-install.php?s={$search_term}&tab=search&type=term";

	if ( is_multisite() ) {
		return network_admin_url( $path );
	} else {
		return admin_url( $path );
	}
}

function get_products_data( $url_query = array() ) {

	$transient_name = 'nextgenthemes_edd_api'; # json_encode( $url_query );

	$cache = get_transient( $transient_name );

	if ( false === $cache ) {

		$url = 'https://nextgenthemes.com/edd-api/products/';

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( '' === $body ) {
			return new WP_Error( 'edd-api', __( 'Empty body', ARVE_SLUG ) );
		}

		if ( ! empty( $cache = json_decode( $body, false ) ) ) {
			set_transient( $transient_name, $cache, 3600 );
			return $cache;
		}
	}

	return $cache;
}

function feature_list_html( $filepath ) {
	echo strip_tags( file_get_contents( $filepath ), '<ul></ul><li></li><h3></h3>' );
}

function activation_notices() {

	$products = get_products();

	foreach ( $products as $key => $value ) {

		if( $value['active'] && ! $value['valid_key'] ) {

			$msg = sprintf(
				__( 'Hi there, thanks for your purchase. One last step, please activate your %s <a href="%s">here now</a>.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
				$value['name'],
				get_admin_url() . 'admin.php?page=nextgenthemes-licenses'
			);
			new Nextgenthemes_Admin_Notice_Factory( $key . '-activation-notice', "<p>$msg</p>", false );
		}
	}
}

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

/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function add_menus() {

 	$plugin_screen_hook_suffix = add_menu_page(
 		__( 'Nextgenthemes', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), # Page Title
 		__( 'Nextgenthemes', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), # Menu Tile
 		'manage_options',                 # capability
 		'nextgenthemes',                  # menu-slug
 		__NAMESPACE__ . '\\ads_page',     # function
		'dashicons-video-alt3',           # icon_url
		'80.892'                          # position
 	);

	/*
  add_submenu_page(
    'nextgenthemes',                      # parent_slug
    __( 'Addons and Themes', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), # Page Title
    __( 'Addons and Themes', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), # Menu Tile
    'manage_options',                     # capability
    'nextgenthemes',                      # menu-slug
    function() {
      require_once plugin_dir_path( __FILE__ ) . 'html-ad-page.php';
    }
  );
	*/

	add_submenu_page(
		'nextgenthemes',              # parent_slug
		__( 'Licenses', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),  # Page Title
		__( 'Licenses', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),  # Menu Tile
		'manage_options',             # capability
		'nextgenthemes-licenses',     # menu-slug
		__NAMESPACE__ . '\\licenses_page' # function
	);
}

function register_settings() {

	add_settings_section(
		'keys',                      # id,
		__( 'Licenses', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), # title,
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
					'value' => get_defined_key( $product_slug ) ? __( 'is defined (wp-config.php?)', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ) : get_key( $product_slug, 'option_only' ),
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

	printf( '<input%s>', arve_attr( array(
		'type'  => 'hidden',
		'id'    => $args['option_basename'] . '[product]',
		'name'  => $args['option_basename'] . '[product]',
		'value' => $args['product']['slug'],
	) ) );

	printf(
		'<input%s%s>',
		arve_attr( $args['attr'] ),
		get_defined_key( $args['product']['slug'] ) ? ' disabled' : ''
	);

	$defined_key = get_defined_key( $args['product']['slug'] );
	$key         = get_key(         $args['product']['slug'] );

	if( $defined_key || ! empty( $key ) ) {

		submit_button( __('Activate License',   NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), 'primary',   $args['option_basename'] . '[activate_key]',   false );
		submit_button( __('Deactivate License', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), 'secondary', $args['option_basename'] . '[deactivate_key]', false );
		submit_button( __('Check License',      NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), 'secondary', $args['option_basename'] . '[check_key]',      false );
  }
	echo '</p>';

  echo '<p>';
  echo __( 'License Status: ', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ) . get_key_status( $args['product']['slug'] );
  echo '</p>';

  if( $args['product']['installed'] && ! $args['product']['active'] ) {
		printf( '<strong>%s</strong>', __( 'Plugin is installed but not activated', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ) );
	} elseif( ! $args['product']['active'] ) {
    printf(
			'<a%s>%s</a>',
			arve_attr( array(
				'href'  => $args['product']['url'],
				'class' => 'button button-primary',
			) ),
			__( 'Not installed, check it out', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN )
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
			'theme-license'             => __( 'Theme License', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'enter-key'                 => __( 'Enter your theme license key.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-key'               => __( 'License Key', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-action'            => __( 'License Action', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'deactivate-license'        => __( 'Deactivate License', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'activate-license'          => __( 'Activate License', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'status-unknown'            => __( 'License status is unknown.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'renew'                     => __( 'Renew?', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'unlimited'                 => __( 'unlimited', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-key-is-active'     => __( 'License key is active.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'expires%s'                 => __( 'Expires %s.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'expires-never'             => __( 'Lifetime License.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-key-expired-%s'    => __( 'License key expired %s.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-key-expired'       => __( 'License key has expired.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-keys-do-not-match' => __( 'License keys do not match.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-is-inactive'       => __( 'License is inactive.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-key-is-disabled'   => __( 'License key is disabled.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'site-is-inactive'          => __( 'Site is inactive.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'license-status-unknown'    => __( 'License status is unknown.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
			'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
		)
	);
}

function remote_get( $url, $args ) {

	$response      = wp_remote_post( 'https://nextgenthemes.com', $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	# retry with wp_remote_GET
	if ( 200 !== $response_code ) {
		$response      = wp_remote_get( 'https://nextgenthemes.com', $args );
		$response_code = wp_remote_retrieve_response_code( $response );
	}

	if ( 200 !== $response_code ) {

		$response = new WP_Error(
			'response_code',
			sprintf(
				__( 'Error: Response code should be 200 but was: %s.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
				$response_code
			)
		);
	}

	return $response;
};

function api_action( $item_name, $key, $action ) {

	if ( ! in_array( $action, array( 'activate', 'deactivate', 'check' ) ) ) {
		wp_die( 'invalid action' );
	}

	$response = remote_get(
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

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) :

			switch( $license_data->error ) {

				case 'expired' :

					$message = sprintf(
						__( 'Your license key expired on %s.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :

					$message = __( 'Your license key has been disabled.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN );
					break;

				case 'missing' :

					$message = __( 'Invalid license.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN );
					break;

				case 'item_name_mismatch' :

					$message = sprintf( __( 'This appears to be an invalid license key for %s.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ), $item_name );
					break;

				case 'no_activations_left' :

					$message = __( 'Your license key has reached its activation limit.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN );
					break;

				default :

					$message = sprintf(
						__( 'Error: %s.', NEXTGENTHEMES_ADMIN_TEXT_DOMAIN ),
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
