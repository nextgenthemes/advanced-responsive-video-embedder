<?php
/**
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/arve-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           9.0.0-alpha1
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     master
 *
 * @package Nextgenthemes/ARVE
 * @author  Nicolas Jonas
 * @license GPL 3.0
 * @link    https://nextgenthemes.com
 */

namespace Nextgenthemes\ARVE;

const VERSION               = '9.0.0-alpha1';
const PRO_VERSION_REQUIRED  = '5.0.0-alpha1';
const NUM_TRACKS            = 3;
const PLUGIN_FILE           = __FILE__;
const VIDEO_FILE_EXTENSIONS = [ 'mp4', 'm4v', 'webm', 'ogv' ];
const DEFAULT_MAXWIDTH      = 900;

init();

function init() {

	add_option( 'arve_install_date', current_time( 'timestamp' ) );

	if ( ! defined( 'Nextgenthemes\VERSION' ) ) {
		define( 'Nextgenthemes\PLUGIN_FILE', __FILE__ );
		define( 'Nextgenthemes\TEXTDOMAIN', 'advanced-responsive-video-embedder' );
	}

	require_once __DIR__ . '/nextgenthemes/init.php';
	require_once __DIR__ . '/vendor/autoload.php';

	array_map( function( $file ) {
		require_once( "public/functions-{$file}.php" );
	}, [
		'deprecated',
		'assets',
		'html-output',
		'misc',
		'oembed',
		'shortcode-data',
		'shortcode-filters',
		'shortcodes',
		'url-handlers',
		'validation',
		'host-properties',
		'settings',
	] );

	require_once __DIR__ . '/public/Admin/functions-admin.php';

	// Public hooks
	add_action( 'init',                        __NAMESPACE__ . '\add_oembed_providers' );
	add_action( 'init',                        __NAMESPACE__ . '\register_gb_block' );
	add_filter( 'oembed_remote_get_args',      __NAMESPACE__ . '\vimeo_referer', 10, 2 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\create_shortcodes', 999 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\create_url_handlers', 999 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\load_textdomain' );
	add_action( 'wp_enqueue_scripts',          __NAMESPACE__ . '\register_assets', 0 );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );
	add_filter( 'language_attributes',         __NAMESPACE__ . '\html_id' );
	add_filter( 'oembed_dataparse',            __NAMESPACE__ . '\filter_oembed_dataparse', 11, 3 );
	add_filter( 'the_content',                 __NAMESPACE__ . '\maybe_enqueue_assets', 99 );

	foreach ( [
		'validate'                        => -99,
		'detect_html5'                    => -31,
		'detect_provider_and_id_from_url' => -30,
		'detect_youtube_playlist'         => -30,

		'aspect_ratio'                    => -10,

		'iframe_fallback'                 => 0,
		'get_media_gallery_thumbnail'     => 10,
		'get_media_gallery_video'         => 10,
		'liveleak_id_fix'                 => 10,
		'maxwidth'                        => 10,
		'mode_fallback'                   => 14,
		'autoplay_off_after_ran_once'     => 15,
		'iframe_src'                      => 20,
		// 'validate_again'                  => 80,
		'set_wrapper_id'                  => 90,
		'set_fixed_dimensions'            => 90,
	] as $filter => $priority ) {
		add_filter( 'shortcode_atts_arve', __NAMESPACE__ . "\sc_filter_$filter", $priority );
	};
	unset( $filter );
	unset( $priority );

	// Admin Hooks
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_styles', 99 );
	add_action( 'admin_init',            __NAMESPACE__ . '\action_admin_init_setup_messages' );
	add_action( 'media_buttons',         __NAMESPACE__ . '\add_media_button', 11 );
	add_action( 'register_shortcode_ui', __NAMESPACE__ . '\register_shortcode_ui' );
	add_action( 'wp_dashboard_setup',    __NAMESPACE__ . '\add_dashboard_widget' );
	add_filter( 'mce_css',               __NAMESPACE__ . '\mce_css' );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\add_action_links' );
}//end init()

function url( $path ) {
	return plugins_url( $path, __FILE__ );
}

function log( $log ) {

    if ( true === WP_DEBUG ) {

        if ( is_string( $log ) ) {
			$log .= PHP_EOL;
        } else {
			$log = print_r( $log, true );
        }

		error_log( $log, 3, __FILE__ . '.log' );
    }
}
