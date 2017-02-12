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
 * Version:           8.0.5
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     master
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ARVE_SLUG',                 'advanced-responsive-video-embedder' );
define( 'ARVE_VERSION',              '8.0.5' );
define( 'ARVE_PRO_VERSION_REQUIRED', '3.6.4' );
define( 'ARVE_NUM_TRACKS', 10 );

if( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-edd-sl-plugin-updater.php';
}

arve_init();
#add_action( 'plugins_loaded', 'arve_init' ); # TODO ??

function arve_init() {

	add_option( 'arve_install_date', current_time( 'timestamp' ) );

	require_once plugin_dir_path( __FILE__ ) . 'admin/class-arve-admin-notice-factory.php';

	if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
		add_action( 'admin_init', 'arve_php_outdated_message' );
		return;
	}

	if ( ! has_action( 'admin_menu', 'nextgenthemes_menus' ) ) {
		require_once plugin_dir_path( __FILE__ ) . 'admin/functions-licensing.php';
	}

	require_once plugin_dir_path( __FILE__ ) . 'admin/functions-admin.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-enqueue.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-html-output.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-misc.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-tests.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-shortcode-data.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-shortcode-filters.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-shortcodes.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-thumbnails.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-url-handlers.php';
	require_once plugin_dir_path( __FILE__ ) . 'public/functions-validation.php';
	require_once plugin_dir_path( __FILE__ ) . 'shared/functions-shared.php';

	add_action( 'plugins_loaded',     'arve_load_plugin_textdomain' );

	# Public hooks
	add_action( 'plugins_loaded',      'arve_create_shortcodes', 999 );
	add_action( 'plugins_loaded',      'arve_create_url_handlers', 999 );
	add_action( 'plugins_loaded',      'arve_oembed_remove_providers', 998 );
	add_action( 'wp_enqueue_scripts',  'arve_enqueue_styles' );
	add_action( 'wp_enqueue_scripts',  'arve_register_scripts', 0 );
	add_action( 'wp_head',             'arve_print_styles' );
	add_action( 'wp_video_shortcode_override', 'arve_wp_video_shortcode_override', 10, 4 );
	add_filter( 'the_content',         'arve_regex_tests' );
	add_filter( 'the_content',         'arve_shortcode_tests' );
	add_filter( 'widget_text',         'do_shortcode' );

	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_sanitise', -12 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_detect_provider_and_id_from_url', -10 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_detect_youtube_playlist', -8 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_detect_html5', -6 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_iframe_fallback', -4 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_validate', -2 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_get_media_gallery_thumbnail', 0 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_build_subtitles', 20 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_build_iframe_src', 20 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_set_fixed_dimensions', 20 );
	add_filter( 'shortcode_atts_arve', 'arve_filter_atts_generate_embed_id', 20 );

	# Admin Hooks
	add_action( 'admin_enqueue_scripts', 'arve_admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', 'arve_admin_enqueue_styles', 99 );
	add_action( 'admin_init',            'arve_action_admin_init_setup_messages' );
	add_action( 'admin_init',            'arve_register_settings_debug', 99 );
	add_action( 'admin_init',            'arve_register_settings' );
	add_action( 'admin_menu',            'arve_add_plugin_admin_menu' );
	add_action( 'media_buttons',         'arve_add_media_button', 11 );
	add_action( 'register_shortcode_ui', 'arve_register_shortcode_ui' );
	add_action( 'wp_dashboard_setup',    'arve_add_dashboard_widget' );

	$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . ARVE_SLUG . '.php' );

	add_filter( 'plugin_action_links_' . $plugin_basename, 'arve_add_action_links' );
	add_filter( 'mce_css',               'arve_mce_css' );
}

function arve_php_outdated_message() {

	$msg = sprintf(
		__( 'ARVE requres at least PHP version 5.3! Your PHP version is %s and has reached End Of Life (insecure and slow). You should ask your host to update it for you not only to make ARVE work but to make your site faster and more secure. I personally recommend PHP 7.0.', ARVE_SLUG ),
		PHP_VERSION
	);

	new ARVE_Admin_Notice_Factory( 'arve-php-outdated', "<p>$msg</p>", false );
}
