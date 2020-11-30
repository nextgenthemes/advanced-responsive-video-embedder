<?php
/**
 * Plugin Name:       ARVE Advanced Responsive Video Embedder
 * Plugin URI:        https://nextgenthemes.com/plugins/arve-pro/
 * Description:       Easy responsive video embeds via URL (like WordPress) or Shortcodes. Supports almost anything you can imagine.
 * Version:           9.0.0-beta5
 * Author:            Nicolas Jonas
 * Author URI:        https://nextgenthemes.com
 * License:           GPL-3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       advanced-responsive-video-embedder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/nextgenthemes/advanced-responsive-video-embedder
 * GitHub Branch:     beta
 *
 * @package Nextgenthemes/ARVE
 * @author  Nicolas Jonas
 * @license GPL 3.0
 * @link    https://nextgenthemes.com
 */

namespace Nextgenthemes\ARVE;

const VERSION               = '9.0.0-beta5';
const PRO_VERSION_REQUIRED  = '5.0.0-beta5';
const NUM_TRACKS            = 3;
const PLUGIN_FILE           = __FILE__;
const PLUGIN_DIR            = __DIR__;
const VIDEO_FILE_EXTENSIONS = [ 'av1mp4', 'mp4', 'm4v', 'webm', 'ogv' ];
const DEFAULT_MAXWIDTH      = 900;

init();

function init() {

	add_option( 'arve_install_date', time() );

	if ( version_compare( get_option( 'arve_version'), VERSION, '<' ) ) {
		update_option( 'nextgenthemes_arve_oembed_recache', time() );
		update_option( 'arve_version', VERSION );
	}

	require_once PLUGIN_DIR . '/php/Common/init.php';
	require_once PLUGIN_DIR . '/php/EmbedChecker.php';
	require_once PLUGIN_DIR . '/php/functions-deprecated.php';
	require_once PLUGIN_DIR . '/php/functions-assets.php';
	require_once PLUGIN_DIR . '/php/functions-html-output.php';
	require_once PLUGIN_DIR . '/php/functions-misc.php';
	require_once PLUGIN_DIR . '/php/functions-oembed.php';
	require_once PLUGIN_DIR . '/php/functions-shortcode-data.php';
	require_once PLUGIN_DIR . '/php/functions-shortcode-filters.php';
	require_once PLUGIN_DIR . '/php/functions-shortcodes.php';
	require_once PLUGIN_DIR . '/php/functions-url-handlers.php';
	require_once PLUGIN_DIR . '/php/functions-validation.php';
	require_once PLUGIN_DIR . '/php/functions-host-properties.php';
	require_once PLUGIN_DIR . '/php/functions-settings.php';
	require_once PLUGIN_DIR . '/php/Admin/functions-admin.php';
	require_once PLUGIN_DIR . '/php/Admin/functions-settings-page.php';

	// Public hooks
	add_action( 'init',                        __NAMESPACE__ . '\add_oembed_providers' );
	add_action( 'init',                        __NAMESPACE__ . '\register_assets' );
	add_filter( 'oembed_remote_get_args',      __NAMESPACE__ . '\vimeo_referer', 10, 2 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\create_shortcodes', 999 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\create_url_handlers', 999 );
	add_action( 'plugins_loaded',              __NAMESPACE__ . '\load_textdomain' );
	add_action( 'wp_enqueue_scripts',          __NAMESPACE__ . '\action_wp_enqueue_scripts' );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );
	add_filter( 'language_attributes',         __NAMESPACE__ . '\html_id' );
	add_filter( 'oembed_dataparse',            __NAMESPACE__ . '\filter_oembed_dataparse', 11, 3 );
	add_filter( 'embed_oembed_html',           __NAMESPACE__ . '\maybe_enqueue_assets', 99 );
	add_filter( 'oembed_ttl',                  __NAMESPACE__ . '\trigger_cache_rebuild', 10, 4 );
	add_filter( 'embed_oembed_discover',       __NAMESPACE__ . '\reenable_oembed_cache' );

	foreach ( [
		'missing_attribute_check'         => -100,
		'validate'                        => -99,
		'detect_html5'                    => -35,
		'detect_provider_and_id_from_url' => -30,
		'aspect_ratio'                    => -10,
		'thumbnail'                       => 10,
		'video'                           => 10,
		'liveleak_id_fix'                 => 10,
		'maxwidth'                        => 10,
		'dimensions'                      => 12,
		'mode'                            => 14,
		'autoplay'                        => 15,
		'iframe_src'                      => 20,
		// Maybe validate_again ?
		'set_uid'                         => 90,
	] as $filter => $priority ) {
		add_filter( 'shortcode_atts_arve', __NAMESPACE__ . "\\sc_filter_{$filter}", $priority );
	};
	unset( $filter );
	unset( $priority );

	// Admin Hooks
	add_action( 'nextgenthemes/arve/admin/settings_header', __NAMESPACE__ . '\Admin\settings_header' );
	add_action( 'nextgenthemes/arve/admin/settings_sidebar', __NAMESPACE__ . '\Admin\settings_sidebar' );

	add_action( 'admin_bar_menu',        __NAMESPACE__ . '\Admin\action_admin_bar_menu', 100 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\Admin\admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\Admin\admin_enqueue_styles', 99 );
	add_action( 'admin_init',            __NAMESPACE__ . '\Admin\action_admin_init_setup_messages' );
	add_action( 'media_buttons',         __NAMESPACE__ . '\Admin\add_media_button', 11 );
	add_action( 'register_shortcode_ui', __NAMESPACE__ . '\Admin\register_shortcode_ui' );
	add_action( 'wp_dashboard_setup',    __NAMESPACE__ . '\Admin\add_dashboard_widget' );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\Admin\add_action_links' );
	add_filter( 'nextgenthemes_arve_save_options',                    __NAMESPACE__ . '\Admin\filter_save_options' );

}//end init()

register_activation_hook( __FILE__, __NAMESPACE__ . '\activation_hook' );
function activation_hook() {
	update_option( 'nextgenthemes_arve_oembed_recache', time() );
}

register_uninstall_hook( __FILE__, __NAMESPACE__ . '\uninstall_hook' );
function uninstall_hook() {

	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
			'%_oembed_%',
			'%' . $wpdb->esc_like( 'id="arve-' ) . '%'
		)
	);
}
