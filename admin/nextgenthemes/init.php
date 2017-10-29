<?php
namespace nextgenthemes\admin;

const VERSION = '1.0.0';

if ( ! defined( __NAMESPACE__ . '\URL' ) ) {
	define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );
}

require_once __DIR__ . '/inc/class-plugin-updater.php';
require_once __DIR__ . '/inc/class-admin-notice-factory.php';
require_once __DIR__ . '/inc/functions-deprecated.php';
require_once __DIR__ . '/inc/functions-licensing.php';
require_once __DIR__ . '/inc/functions-product-page.php';
require_once __DIR__ . '/inc/functions-misc.php';
require_once __DIR__ . '/inc/functions-notices.php';

if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	// add_action( 'admin_init', __NAMESPACE__ . '\\php_below_56_notice' );
}

add_action( 'admin_init', __NAMESPACE__ . '\\init_edd_updaters', 0 );
add_action( 'admin_init', __NAMESPACE__ . '\\activation_notices' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_settings' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_menus' );

/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function add_menus() {

 	$plugin_screen_hook_suffix = add_menu_page(
 		__( 'Nextgenthemes', TEXTDOMAIN ), # Page Title
 		__( 'Nextgenthemes', TEXTDOMAIN ), # Menu Tile
 		'manage_options',                 # capability
 		'nextgenthemes',                  # menu-slug
 		__NAMESPACE__ . '\\ads_page',     # function
		'dashicons-video-alt3',           # icon_url
		'80.892'                          # position
 	);

	/*
  add_submenu_page(
    'nextgenthemes',                      # parent_slug
    __( 'Addons and Themes', TEXTDOMAIN ), # Page Title
    __( 'Addons and Themes', TEXTDOMAIN ), # Menu Tile
    'manage_options',                     # capability
    'nextgenthemes',                      # menu-slug
    function() {
      require_once plugin_dir_path( __FILE__ ) . 'html-ad-page.php';
    }
  );
	*/

	add_submenu_page(
		'nextgenthemes',              # parent_slug
		__( 'Licenses', TEXTDOMAIN ),  # Page Title
		__( 'Licenses', TEXTDOMAIN ),  # Menu Tile
		'manage_options',             # capability
		'nextgenthemes-licenses',     # menu-slug
		__NAMESPACE__ . '\\licenses_page' # function
	);
}
