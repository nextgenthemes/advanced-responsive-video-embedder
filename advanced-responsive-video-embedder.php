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
const PLUGIN_DIR            = __DIR__;
const VIDEO_FILE_EXTENSIONS = [ 'mp4', 'm4v', 'webm', 'ogv' ];
const DEFAULT_MAXWIDTH      = 900;

init();

function init() {

	$ns = __NAMESPACE__;

	add_option( 'arve_install_date', current_time( 'timestamp' ) );

	require_once PLUGIN_DIR . '/vendor/autoload.php';
	require_once PLUGIN_DIR . '/php/Common/init.php';

	array_map(
		function( $file ) {
				require_once "php/functions-{$file}.php";
		},
		[
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
		]
	);

	require_once PLUGIN_DIR . '/php/Admin/functions-admin.php';
	require_once PLUGIN_DIR . '/php/Admin/functions-settings-page.php';

	// Public hooks
	add_action( 'init',                        "{$ns}\\add_oembed_providers" );
	add_action( 'init',                        "{$ns}\\register_gb_block" );
	add_filter( 'oembed_remote_get_args',      "{$ns}\\vimeo_referer", 10, 2 );
	add_action( 'plugins_loaded',              "{$ns}\\create_shortcodes", 999 );
	add_action( 'plugins_loaded',              "{$ns}\\create_url_handlers", 999 );
	add_action( 'plugins_loaded',              "{$ns}\\load_textdomain" );
	add_action( 'wp_enqueue_scripts',          "{$ns}\\action_wp_enqueue_scripts" );
	add_action( 'wp_video_shortcode_override', "{$ns}\\wp_video_shortcode_override", 10, 4 );
	add_filter( 'language_attributes',         "{$ns}\\html_id" );
	add_filter( 'oembed_dataparse',            "{$ns}\\filter_oembed_dataparse", 11, 3 );
	add_filter( 'the_content',                 "{$ns}\\maybe_enqueue_assets", 99 );

	foreach ( [
		'missing_attribute_check'         => -100,
		'validate'                        => -99,
		'detect_html5'                    => -31,
		'detect_provider_and_id_from_url' => -30,
		'detect_youtube_playlist'         => -30,

		'aspect_ratio'                    => -10,

		'get_media_gallery_thumbnail'     => 10,
		'get_media_gallery_video'         => 10,
		'liveleak_id_fix'                 => 10,
		'maxwidth'                        => 10,
		'mode_fallback'                   => 14,
		'autoplay_off_after_ran_once'     => 15,
		'iframe_src'                      => 20,
		// Maybe validate_again ?
		'set_wrapper_id'                  => 90,
		'set_fixed_dimensions'            => 90,
	] as $filter => $priority ) {
		add_filter( 'shortcode_atts_arve', "{$ns}\\sc_filter_$filter", $priority );
	};
	unset( $filter );
	unset( $priority );

	// Admin Hooks
	add_action( 'admin_enqueue_scripts', "{$ns}\\Admin\\admin_enqueue_scripts" );
	add_action( 'admin_enqueue_scripts', "{$ns}\\Admin\\admin_enqueue_styles", 99 );
	add_action( 'admin_init',            "{$ns}\\Admin\\action_admin_init_setup_messages" );
	add_action( 'media_buttons',         "{$ns}\\Admin\\add_media_button", 11 );
	add_action( 'register_shortcode_ui', "{$ns}\\Admin\\register_shortcode_ui" );
	add_action( 'wp_dashboard_setup',    "{$ns}\\Admin\\add_dashboard_widget" );
	add_filter( 'mce_css',               "{$ns}\\Admin\\mce_css" );
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), "{$ns}\\Admin\\add_action_links" );
}//end init()
