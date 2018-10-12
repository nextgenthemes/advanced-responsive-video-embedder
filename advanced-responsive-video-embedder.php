<?php
/**
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/arve-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           9.0.0
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     dev
 *
 * @package Nextgenthemes/ARVE
 * @author  Nicolas Jonas
 * @license GPL 3.0
 * @link    https://nextgenthemes.com
 */

namespace Nextgenthemes\ARVE;

const VERSION              = '9.0.0';
const PRO_VERSION_REQUIRED = '5.0.0';
const NUM_TRACKS           = 10;
const PLUGIN_FILE          = __FILE__;

function url( $path ) {
	return plugins_url( $path, __FILE__ );
}

init();

function init() {

	add_option( 'arve_install_date', current_time( 'timestamp' ) );

	if ( ! defined( 'Nextgenthemes\VERSION' ) ) {
		define( 'Nextgenthemes\PLUGIN_FILE', __FILE__ );
		define( 'Nextgenthemes\TEXTDOMAIN', 'advanced-responsive-video-embedder' );
		define( 'Nextgenthemes\Admin\TEXTDOMAIN', 'advanced-responsive-video-embedder' );
		define( 'Nextgenthemes\Utils\TEXTDOMAIN', 'advanced-responsive-video-embedder' );
	}

	require_once __DIR__ . '/vendor/autoload.php';

	foreach ( [
		'assets',
		'html-output',
		'misc',
		'oembed',
		'shortcode-data',
		'shortcode-filters',
		'shortcodes',
		'shared',
		'thumbnails',
		'url-handlers',
		'validation',
		'host-properties'
	] as $file ) {
		require_once( "public/functions-{$file}.php" );
	}

	require_once __DIR__ . '/admin/functions-admin.php';

	# Public hooks
	add_action( 'init',                        __NAMESPACE__ . '\add_oembed_providers' );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\create_shortcodes', 999 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\create_url_handlers', 999 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\load_textdomain' );
	add_action( 'wp_enqueue_scripts',          __NAMESPACE__ . '\register_assets', 0 );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );
	add_filter( 'language_attributes',         __NAMESPACE__ . '\html_id' );
	add_filter( 'oembed_dataparse',            __NAMESPACE__ . '\filter_oembed_dataparse', 11, 3 );
	add_filter( 'the_content',                 __NAMESPACE__ . '\maybe_enqueue_assets', 99 );

	foreach ( [
		'sanitise'                        => -99,
		'detect_provider_and_id_from_url' => -10,
		'detect_youtube_playlist'         => -35,
		'get_media_gallery_video'         => -30,
		'detect_html5'                    => -25,
		'iframe_fallback'                 => -20,
		'validate'                        => -15,
		'get_media_gallery_thumbnail'     => -10,
		'build_iframe_src'                => -5,
		'iframe_src_query'                => 0,
		'iframe_src_autoplay_query'       => 5,
		'autoplay_off_after_ran_once'     => 10,
		'set_fixed_dimensions'            => 15,
		'attr'                            => 20,
		'build_tracks_html'               => 20,
	] as $filter => $priority ) {
		add_filter( 'shortcode_atts_arve', __NAMESPACE__ . "\sc_filter_$filter", $priority );
	};

	// Admin Hooks
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_styles', 99 );
	add_action( 'admin_init',            __NAMESPACE__ . '\action_admin_init_setup_messages' );
	add_action( 'admin_init',            __NAMESPACE__ . '\register_settings_debug', 99 );
	add_action( 'admin_init',            __NAMESPACE__ . '\register_settings' );
	add_action( 'admin_menu',            __NAMESPACE__ . '\add_plugin_admin_menu' );
	add_action( 'media_buttons',         __NAMESPACE__ . '\add_media_button', 11 );
	add_action( 'register_shortcode_ui', __NAMESPACE__ . '\register_shortcode_ui' );
	add_action( 'wp_dashboard_setup',    __NAMESPACE__ . '\add_dashboard_widget' );
	add_filter( 'mce_css',               __NAMESPACE__ . '\mce_css' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\add_action_links' );
}
