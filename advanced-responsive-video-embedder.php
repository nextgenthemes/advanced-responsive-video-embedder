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
 */

namespace Nextgenthemes\ARVE;

const VERSION              = '9.0.0';
const PRO_VERSION_REQUIRED = '4.0.0';
const NUM_TRACKS           = 10;

define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );
define( 'Nextgenthemes\TEXTDOMAIN', 'advanced-responsive-video-embedder' );

init();

function init() {

	add_option( __NAMESPACE__ . '\install_date', current_time( 'timestamp' ) );

	require_once __DIR__ . '/vendor/autoload.php';

	array_map( function( $file ) {
		require_once( "public/functions-{$file}.php" );
	}, [
		'enqueue',
		'html-output',
		'misc',
		'shortcode-data',
		'shortcode-filters',
		'shortcodes',
		'shared',
		'thumbnails',
		'url-handlers',
		'validation',
	] );

	require_once __DIR__ . '/admin/functions-admin.php';

	add_action( 'plugins_loaded', __NAMESPACE__ . '\load_textdomain' );

	# Public hooks
	add_action( 'plugins_loaded',      __NAMESPACE__ . '\create_shortcodes', 999 );
	add_action( 'plugins_loaded',      __NAMESPACE__ . '\create_url_handlers', 999 );
	add_action( 'wp_enqueue_scripts',  __NAMESPACE__ . '\register_assets', 0 );
	add_action( 'wp_enqueue_scripts',  __NAMESPACE__ . '\maybe_enqueue_assets' );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );

	add_filter( 'oembed_dataparse',    __NAMESPACE__ . '\filter_oembed_dataparse', 11, 3 );
	add_filter( 'embed_oembed_html',   __NAMESPACE__ . '\maybe_enqueue' );
	add_filter( 'embed_handler_html',  __NAMESPACE__ . '\maybe_enqueue' );

	add_filter( 'language_attributes', __NAMESPACE__ . '\html_id' );

	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_sanitise', -12 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_detect_provider_and_id_from_url', -10 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_detect_youtube_playlist', -8 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_get_media_gallery_video', -7 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_detect_html5', -6 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_iframe_fallback', -4 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_validate', -2 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_get_media_gallery_thumbnail', 0 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_autoplay_off_after_ran_once' );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_set_fixed_dimensions', 15 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_attr', 20 );
	add_filter( 'shortcode_atts_arve', __NAMESPACE__ . '\sc_filter_build_tracks_html', 20 );

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

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\add_action_links' );
	add_filter( 'mce_css',               __NAMESPACE__ . '\mce_css' );
}
