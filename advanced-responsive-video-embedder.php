<?php
/**
 * @link              https://nextgenthemes.com
 * @since             3.0.0
 * @package           Advanced_Responsive_Video_Embedder
 *
 * @wordpress-plugin
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/advanced-responsive-video-embedder-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           7.8.3-beta
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     beta
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ARVE_SLUG',                 'advanced-responsive-video-embedder' );
define( 'ARVE_VERSION',              '7.8.3-beta' );
define( 'ARVE_PRO_VERSION_REQUIRED', '3.0.2-beta' );

arv3_init();
#add_action( 'plugins_loaded', 'arv3_init' ); # TODO ??

function arv3_init() {

	add_option( 'arve_install_date', current_time( 'timestamp' ) );

	require_once plugin_dir_path( __FILE__ ) . 'admin/class-arve-admin-notice-factory.php';
	require_once plugin_dir_path( __FILE__ ) . 'admin/functions-admin.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/class-arve-shortcode-function-factory.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/class-arve-url-function-factory.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-enqueue.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-html-output.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-misc.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-self-hosted.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-shortcode-data.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-shortcodes.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-thumbnails.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-url-handlers.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-validation.php';
	require_once plugin_dir_path( __FILE__ ) . 'shared/functions-shared.php';

	add_action( 'plugins_loaded',     'arv3_load_plugin_textdomain' );

	// Public hooks
	add_action( 'wp_video_shortcode_override', 'arv3_wp_video_shortcode_override', 10, 4 );
	add_action( 'plugins_loaded',      'arv3_create_shortcodes', 999 );
	add_action( 'plugins_loaded',      'arv3_create_url_handlers', 999 );
	add_action( 'plugins_loaded',      'arv3_oembed_remove_providers', 998 );
	add_action( 'wp_enqueue_scripts',  'arv3_enqueue_styles' );
	add_action( 'wp_enqueue_scripts',  'arv3_register_scripts', 0 );
	add_action( 'wp_head',             'arv3_print_styles' );
	add_filter( 'shortcode_atts_arve', 'arv3_filter_atts_get_media_gallery_thumbnail', 5 );
	add_filter( 'shortcode_atts_arve', 'arv3_filter_atts_sanitise', 0 );
	add_filter( 'shortcode_atts_arve', 'arv3_filter_atts_detect_provider_and_id_from_url', 1 );
	add_filter( 'widget_text',         'do_shortcode' );

	// Admin Hooks
	add_action( 'admin_enqueue_scripts', 'arv3_admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', 'arv3_admin_enqueue_styles', 99 );
	add_action( 'admin_init',            'arv3_action_admin_init_setup_messages' );
	add_action( 'admin_init',            'arv3_register_settings_debug', 99 );
	add_action( 'admin_init',            'arv3_register_settings' );
	add_action( 'admin_menu',            'arv3_add_plugin_admin_menu' );
	add_action( 'media_buttons',         'arv3_add_media_button', 11 );
	add_action( 'register_shortcode_ui', 'arv3_register_shortcode_ui' );
	add_action( 'wp_dashboard_setup',    'arv3_add_dashboard_widget' );

	$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . ARVE_SLUG . '.php' );

	add_filter( 'plugin_action_links_' . $plugin_basename, 'arv3_add_action_links' );
	add_filter( 'mce_css',               'arv3_mce_css' );
}
