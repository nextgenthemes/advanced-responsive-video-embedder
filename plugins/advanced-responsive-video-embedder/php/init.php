<?php
namespace Nextgenthemes\ARVE;

init_920();

function init_920() {
	init_public();
	init_admin();
}

function init_public() {

	add_option( 'arve_install_date', time() );

	if ( version_compare( get_option( 'arve_version' ), '9.5.3-alpha1', '<' ) ) {
		$GLOBALS['wpdb']->query( "DELETE FROM {$GLOBALS['wpdb']->postmeta} WHERE meta_key LIKE '%_oembed_%'" );
	}

	update_option( 'arve_version', VERSION );

	require_once PLUGIN_DIR . '/php/Common/init.php';
	require_once PLUGIN_DIR . '/php/functions-deprecated.php';
	require_once PLUGIN_DIR . '/php/functions-assets.php';
	require_once PLUGIN_DIR . '/php/functions-html-output.php';
	require_once PLUGIN_DIR . '/php/functions-misc.php';
	require_once PLUGIN_DIR . '/php/functions-oembed.php';
	require_once PLUGIN_DIR . '/php/functions-shortcode-data.php';
	require_once PLUGIN_DIR . '/php/functions-shortcode-args.php';
	require_once PLUGIN_DIR . '/php/functions-shortcodes.php';
	require_once PLUGIN_DIR . '/php/functions-url-handlers.php';
	require_once PLUGIN_DIR . '/php/functions-validation.php';
	require_once PLUGIN_DIR . '/php/functions-settings.php';

	add_action( 'init', __NAMESPACE__ . '\add_oembed_providers' );
	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_filter( 'oembed_remote_get_args', __NAMESPACE__ . '\vimeo_referer', 10, 2 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\create_shortcodes', 999 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\create_url_handlers', 999 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\load_textdomain' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\action_wp_enqueue_scripts' );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );
	add_filter( 'language_attributes', __NAMESPACE__ . '\html_id' );
	add_filter( 'oembed_dataparse', __NAMESPACE__ . '\filter_oembed_dataparse', PHP_INT_MAX, 3 );
	add_filter( 'embed_oembed_html', __NAMESPACE__ . '\filter_embed_oembed_html', OEMBED_HTML_PRIORITY, 4 );
}

function init_admin() {

	require_once PLUGIN_DIR . '/php/Admin/functions-admin.php';
	require_once PLUGIN_DIR . '/php/Admin/functions-settings-page.php';

	// Admin Hooks
	add_action( 'nextgenthemes/arve/admin/settings/sidebar', __NAMESPACE__ . '\Admin\settings_sidebar' );
	add_action( 'nextgenthemes/arve/admin/settings/content', __NAMESPACE__ . '\Admin\settings_content' );

	add_action( 'admin_bar_menu', __NAMESPACE__ . '\Admin\action_admin_bar_menu', 100 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\Admin\admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\Admin\admin_enqueue_styles', 99 );
	add_action( 'admin_init', __NAMESPACE__ . '\Admin\action_admin_init_setup_messages' );
	add_action( 'media_buttons', __NAMESPACE__ . '\Admin\add_media_button', 11 );
	add_action( 'register_shortcode_ui', __NAMESPACE__ . '\Admin\register_shortcode_ui' );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\Admin\add_dashboard_widget' );

	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), __NAMESPACE__ . '\Admin\add_action_links' );
	add_filter( 'nextgenthemes_arve_save_options', __NAMESPACE__ . '\Admin\filter_save_options' );
}
