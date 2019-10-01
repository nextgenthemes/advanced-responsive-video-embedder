<?php
add_action( 'admin_init', 'nextgenthemes_init_edd_updaters', 0 );
add_action( 'admin_init', 'nextgenthemes_activation_notices' );
add_action( 'admin_init', 'nextgenthemes_register_settings' );
add_action( 'admin_menu', 'nextgenthemes_menus' );

function nextgenthemes_admin_install_search_url( $search_term ) {

	$path = "plugin-install.php?s={$search_term}&tab=search&type=term";

	if ( is_multisite() ) {
		return network_admin_url( $path );
	} else {
		return admin_url( $path );
	}
}

function nextgenthemes_ads_page() { ?>
<style>
	body {
	  background: hsl(210, 13%, 16%);
	}
	#wpcontent {
		padding: 0;
	}
	#wpbody-content {
		/* padding-bottom: 2rem; */
	}
	#wpfooter {
		display: none;
	}
	#nextgenthemes-ads {
		padding: 1.7rem;
		column-width: 40rem;
		column-gap: 1.7rem;
	}
	@media only screen and (max-device-width: 400px) {

		#nextgenthemes-ads {
			padding-left: 0;
			padding-right: 0;
		}
	}
	#nextgenthemes-ads,
	#nextgenthemes-ads * {
		box-sizing: border-box;
	}
	#nextgenthemes-ads::after {
	  content: "";
	  display: table;
	  clear: both;
	}
	#nextgenthemes-ads {
		color: white;
	}
	#nextgenthemes-ads h1,
	#nextgenthemes-ads h2,
	#nextgenthemes-ads h3 {
		color: inherit;
		margin-left: 2rem;
		margin-right: 1.7rem;
	}
	#nextgenthemes-ads h1 {
		line-height: 1;
	}
	#nextgenthemes-ads img {
	  width: 100%;
		height: auto;
	}
	#nextgenthemes-ads > a {
		text-decoration: none;
		position: relative;
		display: inline-block;
		width: 100%;
	  background-color: hsl(210, 13%, 13%);
		border: 1px solid hsl(207, 48%, 30%);
		transition: box-shadow .3s, background-color .3s, border-color .3s;
		color: #eee;
		font-size: 1.05rem;
		margin-bottom: 2rem;
		line-height: 1.4;
	}
	#nextgenthemes-ads > a:hover {
		background-color: hsl(210, 13%, 10%);
		box-shadow: 0 0 10px hsla(207, 48%, 50%, 1);
		border-color: hsl(207, 48%, 40%);
	}
	#nextgenthemes-ads p {
		margin-left: 2rem;
		margin-right: 1.7rem;
		font-size: 1.2rem;
	}
	#nextgenthemes-ads ul {
		list-style: square;
		margin-left: 2.5rem;
		margin-right: .7rem;
	}
	#nextgenthemes-ads > a > span {
		position: absolute;
		padding: .6rem 1rem;
		right: 0px;
		bottom: 0px;
		font-size: 2rem;
		color: white;
		background-color: hsl(207, 48%, 30%);
		border-top-left-radius: 3px;
		//transform: rotate(3deg);
	}
	#nextgenthemes-ads figure {
		margin: 1rem;
	}
</style>

	<?php
	$img_dir = plugin_dir_url( __FILE__ ) . 'product-images/';
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

<div id="nextgenthemes-ads">

	<?php if ( ! defined( 'ARVE_PRO_VERSION' ) ) : ?>
		<a href="https://nextgenthemes.com/plugins/arve-pro/">
			<figure><img src="<?php echo $img_dir; ?>arve.svg" alt"ARVE"></figure>
			<?php nextgenthemes_feature_list_html( ARVE_PATH . 'readme/html/20-description-features-pro.html' ); ?>
			<span>Paid</span>
		</a>
	<?php endif; ?>

	<?php if ( ! defined( 'ARVE_AMP_VERSION' ) ) : ?>
		<a href="https://nextgenthemes.com/plugins/arve-accelerated-mobile-pages-addon/">
		  <figure><img src="<?php echo $img_dir; ?>arve.svg" alt"ARVE"></figure>
			<?php nextgenthemes_feature_list_html( ARVE_PATH . 'readme/html/25-description-features-amp.html' ); ?>
			<span>Paid</span>
		</a>
	<?php endif; ?>

	<?php if ( ! is_plugin_active( 'regenerate-thumbnails-reminder/regenerate-thumbnails-reminder.php' ) ) : ?>
		<a href="<?php echo nextgenthemes_admin_install_search_url( 'Regenerate+Thumbnails+Reminder' ); ?>">
		  <h1>Regenerate Thumbnails Reminder</h1>
			<p>Get a reminder when you change your thumbnail sizes to regenerate them. Note Thumbnails sizes change automatically if you swtich themes.</p>
			<span>Free</span>
		</a>
	<?php endif; ?>

</div>

	<?php
}

function nextgenthemes_feature_list_html( $filepath ) {
	echo strip_tags( file_get_contents( $filepath ), '<ul></ul><li></li><h3></h3>' );
}

function nextgenthemes_activation_notices() {

	$products = nextgenthemes_get_products();

	foreach ( $products as $key => $value ) {

		if ( $value['active'] && ! $value['valid_key'] ) {

			$msg = sprintf(
				__( 'Hi there, thanks for your purchase. One last step, please activate your %1$s <a href="%2$s">here now</a>.', ARVE_SLUG ),
				$value['name'],
				get_admin_url() . 'admin.php?page=nextgenthemes-licenses'
			);
			new ARVE_Admin_Notice_Factory( $key . '-activation-notice', "<p>$msg</p>", false );
		}
	}
}

function nextgenthemes_get_products() {

	$products = array(
		'arve_pro'          => array(
			'name'   => 'ARVE Pro',
			'id'     => 1253,
			'type'   => 'plugin',
			'author' => 'Nicolas Jonas',
			'url'    => 'https://nextgenthemes.com/plugins/arve-pro/',
		),
		'arve_amp'          => array(
			'name'   => 'ARVE AMP',
			'id'     => 16941,
			'type'   => 'plugin',
			'author' => 'Nicolas Jonas',
			'url'    => 'https://nextgenthemes.com/plugins/arve-amp/',
		),
		'arve_random_video' => array(
			'name'   => 'ARVE Random Video',
			'id'     => 31933,
			'type'   => 'plugin',
			'author' => 'Nicolas Jonas',
			'url'    => 'https://nextgenthemes.com/plugins/arve-random-video/',
		)
	);

	$products = apply_filters( 'nextgenthemes_products', $products );

	foreach ( $products as $key => $value ) {

		$products[ $key ]['slug']      = $key;
		$products[ $key ]['installed'] = false;
		$products[ $key ]['active']    = false;
		$products[ $key ]['valid_key'] = nextgenthemes_has_valid_key( $key );

		$version_define = strtoupper( $key ) . '_VERSION';
		$file_define    = strtoupper( $key ) . '_FILE';

		if ( defined( $version_define ) ) {
			$products[ $key ]['version'] = constant( $version_define );
		}
		if ( defined( $file_define ) ) {
			$products[ $key ]['file'] = constant( $file_define );
		}

		$version_define = "\\nextgenthemes\\$key\\VERSION";
		$file_define    = "\\nextgenthemes\\$key\\FILE";

		if ( defined( $version_define ) ) {
			$products[ $key ]['version'] = constant( $version_define );
		}
		if ( defined( $file_define ) ) {
			$products[ $key ]['file'] = constant( $file_define );
		}

		if ( 'plugin' === $value['type'] ) {

			$file_slug = str_replace( '_', '-', $key );

			$products[ $key ]['installed'] = nextgenthemes_is_plugin_installed( "$file_slug/$file_slug.php" );
			$products[ $key ]['active']    = is_plugin_active( "$file_slug/$file_slug.php" );
		}
	}

	return $products;
}

function nextgenthemes_is_plugin_installed( $plugin_basename ) {

	$plugins = get_plugins();

	if ( array_key_exists( $plugin_basename, $plugins ) ) {
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
function nextgenthemes_menus() {

	$plugin_screen_hook_suffix = add_options_page(
		__( 'ARVE Licenses', ARVE_SLUG ),
		__( 'ARVE Licenses', ARVE_SLUG ),
		'manage_options',
		'nextgenthemes-licenses',
		'nextgenthemes_licenses_page'
	);
}

function nextgenthemes_register_settings() {

	add_settings_section(
		'keys',                      # id,
		__( 'Licenses', ARVE_SLUG ), # title,
		'__return_empty_string',     # callback,
		'nextgenthemes-licenses'     # page
	);

	foreach ( nextgenthemes_get_products() as $product_slug => $product ) :

		$option_basename = "nextgenthemes_{$product_slug}_key";
		$option_keyname  = $option_basename . '[key]';

		add_settings_field(
			$option_keyname,              # id,
			$product['name'],             # title,
			'nextgenthemes_key_callback', # callback,
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
					'value' => nextgenthemes_get_defined_key( $product_slug ) ? __( 'is defined (wp-config.php?)', ARVE_SLUG ) : nextgenthemes_get_key( $product_slug, 'option_only' ),
				)
			)
		);

		register_setting(
			'nextgenthemes',  # option_group
			$option_basename, # option_name
			'nextgenthemes_validate_license' # validation callback
		);

	endforeach;
}

function nextgenthemes_key_callback( $args ) {

	echo '<p>';

	printf(
		'<input%s>',
		arve_attr(
			array(
				'type'  => 'hidden',
				'id'    => $args['option_basename'] . '[product]',
				'name'  => $args['option_basename'] . '[product]',
				'value' => $args['product']['slug'],
			)
		)
	);

	printf(
		'<input%s%s>',
		arve_attr( $args['attr'] ),
		nextgenthemes_get_defined_key( $args['product']['slug'] ) ? ' disabled' : ''
	);

	$defined_key = nextgenthemes_get_defined_key( $args['product']['slug'] );
	$key         = nextgenthemes_get_key( $args['product']['slug'] );

	if ( $defined_key || ! empty( $key ) ) {

		submit_button( __( 'Activate License',   ARVE_SLUG ), 'primary',   $args['option_basename'] . '[activate_key]',   false );
		submit_button( __( 'Deactivate License', ARVE_SLUG ), 'secondary', $args['option_basename'] . '[deactivate_key]', false );
		submit_button( __( 'Check License',      ARVE_SLUG ), 'secondary', $args['option_basename'] . '[check_key]',      false );
	}
	echo '</p>';

	echo '<p>';
	echo __( 'License Status: ', ARVE_SLUG ) . nextgenthemes_get_key_status( $args['product']['slug'] );
	echo '</p>';

	if ( $args['product']['installed'] && ! $args['product']['active'] ) {
		printf( '<strong>%s</strong>', __( 'Plugin is installed but not activated', ARVE_SLUG ) );
	} elseif ( ! $args['product']['active'] ) {
		printf(
			'<a%s>%s</a>',
			arve_attr(
				array(
					'href'  => $args['product']['url'],
					'class' => 'button button-primary',
				)
			),
			__( 'Not installed, check it out', ARVE_SLUG )
		);
	}
}

function nextgenthemes_validate_license( $input ) {

	if ( ! is_array( $input ) ) {
		return sanitize_text_field( $input );
	}

	$product     = $input['product'];
	$defined_key = nextgenthemes_get_defined_key( $product );

	if ( $defined_key ) {
		$option_key = $defined_key;
		$key        = $defined_key;
	} else {
		$key        = sanitize_text_field( $input['key'] );
		$option_key = nextgenthemes_get_key( $product );
	}

	if ( ( $key !== $option_key ) || isset( $input['activate_key'] ) ) {

		nextgenthemes_api_update_key_status( $product, $key, 'activate' );

	} elseif ( isset( $input['deactivate_key'] ) ) {

		nextgenthemes_api_update_key_status( $product, $key, 'deactivate' );

	} elseif ( isset( $input['check_key'] ) ) {

		nextgenthemes_api_update_key_status( $product, $key, 'check' );
	}

	return $key;
}

function nextgenthemes_get_key( $product, $option_only = false ) {

	$defined_key = nextgenthemes_get_defined_key( $product );

	if ( ! $option_only && $defined_key ) {
		return $defined_key;
	}

	return get_option( "nextgenthemes_{$product}_key" );
}
function nextgenthemes_get_key_status( $product ) {
	return get_option( "nextgenthemes_{$product}_key_status" );
}
function nextgenthemes_update_key_status( $product, $key ) {
	update_option( "nextgenthemes_{$product}_key_status", $key );
}
function nextgenthemes_has_valid_key( $product ) {
	return ( 'valid' === nextgenthemes_get_key_status( $product ) ) ? true : false;
}

function nextgenthemes_api_update_key_status( $product, $key, $action ) {

	$products   = nextgenthemes_get_products();
	$key_status = nextgenthemes_api_action( $products[ $product ]['id'], $key, $action );

	nextgenthemes_update_key_status( $product, $key_status );
}

function nextgenthemes_get_defined_key( $slug ) {

	$constant_name = str_replace( '-', '_', strtoupper( $slug . '_KEY' ) );

	if ( defined( $constant_name ) && constant( $constant_name ) ) {
		return constant( $constant_name );
	} else {
		return false;
	}
}

function nextgenthemes_licenses_page() {
	?>
	<div class="wrap">

		<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

		<form method="post" action="options.php">

			<?php do_settings_sections( 'nextgenthemes-licenses' ); ?>
			<?php settings_fields( 'nextgenthemes' ); ?>
			<?php submit_button( __( 'Save Changes' ), 'primary', 'submit', false ); ?>
		</form>

	</div>
	<?php
}

function nextgenthemes_init_edd_updaters() {

	$products = nextgenthemes_get_products();

	foreach ( $products as $product ) {

		if ( 'plugin' === $product['type'] && ! empty( $product['file'] ) ) {
			nextgenthemes_init_plugin_updater( $product );
		} elseif ( 'theme' === $product['type'] ) {
			nextgenthemes_init_theme_updater( $product );
		}
	}
}

function nextgenthemes_init_plugin_updater( $product ) {

	// setup the updater
	new Nextgenthemes_Plugin_Updater(
		apply_filters( 'nextgenthemes_api_url', 'https://nextgenthemes.com' ),
		$product['file'],
		array(
			'version' => $product['version'],
			'license' => nextgenthemes_get_key( $product['slug'] ),
			'item_id' => $product['id'],
			'author'  => $product['author']
		)
	);
}

function nextgenthemes_init_theme_updater( $product ) {

	new EDD_Theme_Updater(
		array(
			'remote_api_url' => 'https://nextgenthemes.com',
			'version'        => $product['version'],
			'license'        => nextgenthemes_get_key( $product['slug'] ),
			'item_id'        => $product['name'],
			'author'         => $product['id'],
			'theme_slug'     => $product['slug'],
			'download_id'    => $product['download_id'], // Optional, used for generating a license renewal link
			#'renew_url'     => $product['renew_link'], // Optional, allows for a custom license renewal link
		),
		array(
			'theme-license'             => __( 'Theme License', ARVE_SLUG ),
			'enter-key'                 => __( 'Enter your theme license key.', ARVE_SLUG ),
			'license-key'               => __( 'License Key', ARVE_SLUG ),
			'license-action'            => __( 'License Action', ARVE_SLUG ),
			'deactivate-license'        => __( 'Deactivate License', ARVE_SLUG ),
			'activate-license'          => __( 'Activate License', ARVE_SLUG ),
			'status-unknown'            => __( 'License status is unknown.', ARVE_SLUG ),
			'renew'                     => __( 'Renew?', ARVE_SLUG ),
			'unlimited'                 => __( 'unlimited', ARVE_SLUG ),
			'license-key-is-active'     => __( 'License key is active.', ARVE_SLUG ),
			'expires%s'                 => __( 'Expires %s.', ARVE_SLUG ),
			'expires-never'             => __( 'Lifetime License.', ARVE_SLUG ),
			'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', ARVE_SLUG ),
			'license-key-expired-%s'    => __( 'License key expired %s.', ARVE_SLUG ),
			'license-key-expired'       => __( 'License key has expired.', ARVE_SLUG ),
			'license-keys-do-not-match' => __( 'License keys do not match.', ARVE_SLUG ),
			'license-is-inactive'       => __( 'License is inactive.', ARVE_SLUG ),
			'license-key-is-disabled'   => __( 'License key is disabled.', ARVE_SLUG ),
			'site-is-inactive'          => __( 'Site is inactive.', ARVE_SLUG ),
			'license-status-unknown'    => __( 'License status is unknown.', ARVE_SLUG ),
			'update-notice'             => __( "Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.", ARVE_SLUG ),
			'update-available'          => __( '<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4$s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', ARVE_SLUG ),
		)
	);
}

function nextgenthemes_api_action( $item_id, $key, $action ) {

	if ( ! in_array( $action, array( 'activate', 'deactivate', 'check' ), true ) ) {
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
			$message = __( 'An error occurred, please try again.', ARVE_SLUG );
		}
	} else {

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {

			switch ( $license_data->error ) {

				case 'expired':
					$message = sprintf(
						__( 'Your license key expired on %s.' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked':
					$message = __( 'Your license key has been disabled.', ARVE_SLUG );
					break;

				case 'missing':
					$message = __( 'Invalid license.', ARVE_SLUG );
					break;

				case 'invalid':
				case 'site_inactive':
					$message = __( 'Your license is not active for this URL.', ARVE_SLUG );
					break;

				case 'item_name_mismatch':
					$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), ARVE_SLUG );
					break;

				case 'no_activations_left':
					$message = __( 'Your license key has reached its activation limit.', ARVE_SLUG );
					break;

				default:
					$message = __( 'An error occurred, please try again.', ARVE_SLUG );
					break;
			}
		}
	}

	if ( empty( $message ) ) {

		if ( empty( $license_data->license ) ) {

			$textarea_dump = arve_textarea_dump( $response );

			$message = sprintf(
				__( 'Error. Please report the following:<br> %s', ARVE_SLUG ),
				$textarea_dump
			);
		} else {
			$message = $license_data->license;
		}
	}

	return $message;
}

function arve_dump( $var ) {
	ob_start();
	var_dump( $var ); // phpcs:ignore
	return ob_get_clean();
}

function arve_textarea_dump( $var ) {
	return sprintf( '<textarea style="width: 100%; height: 70vh;">%s</textarea>', esc_textarea( arve_dump( $var ) ) );
}
