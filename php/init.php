<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

// Stop outdated addons from executing
remove_action( 'plugins_loaded', 'Nextgenthemes\ARVE\Pro\init', 15 );
remove_action( 'plugins_loaded', 'Nextgenthemes\ARVE\RandomVideo\init', 15 );
remove_action( 'plugins_loaded', 'Nextgenthemes\ARVE\Privacy\init', 16 );

add_action( 'init', __NAMESPACE__ . '\init', 9 );
add_action( 'admin_init', __NAMESPACE__ . '\init_admin', 9 );

function init(): void {

	require_once PLUGIN_DIR . '/php/Video.php';
	require_once PLUGIN_DIR . '/php/fn-cache.php';
	require_once PLUGIN_DIR . '/php/fn-assets.php';
	require_once PLUGIN_DIR . '/php/fn-html-output.php';
	require_once PLUGIN_DIR . '/php/fn-misc.php';
	require_once PLUGIN_DIR . '/php/fn-oembed.php';
	require_once PLUGIN_DIR . '/php/fn-shortcode-data.php';
	require_once PLUGIN_DIR . '/php/fn-shortcode-args.php';
	require_once PLUGIN_DIR . '/php/fn-shortcodes.php';
	require_once PLUGIN_DIR . '/php/fn-url-handlers.php';
	require_once PLUGIN_DIR . '/php/fn-validation.php';
	require_once PLUGIN_DIR . '/php/fn-settings.php';

	add_option( 'arve_install_date', time() );
	maybe_delete_oembed_cache(); // Must be before update_option arve_version
	update_option( 'arve_version', VERSION );

	add_action( 'init', __NAMESPACE__ . '\settings_instance' );
	add_action( 'init', __NAMESPACE__ . '\init_nextgenthemes_settings' );
	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_action( 'init', __NAMESPACE__ . '\create_shortcodes' );
	add_action( 'init', __NAMESPACE__ . '\create_url_handlers' );
	add_filter( 'mce_css', __NAMESPACE__ . '\add_styles_to_mce' );
	add_filter( 'oembed_remote_get_args', __NAMESPACE__ . '\vimeo_referer', 10, 2 );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\action_wp_enqueue_scripts' );
	add_filter( 'render_block_core/embed', __NAMESPACE__ . '\remove_embed_block_aspect_ratio' );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );
	add_filter( 'language_attributes', __NAMESPACE__ . '\html_id' );
	add_filter( 'oembed_dataparse', __NAMESPACE__ . '\filter_oembed_dataparse', PHP_INT_MAX, 3 );
	add_filter( 'embed_oembed_html', __NAMESPACE__ . '\filter_embed_oembed_html', OEMBED_HTML_PRIORITY, 4 );
	add_action( 'elementor/widgets/register', __NAMESPACE__ . '\register_elementor_widget' );

	foreach ( ADDON_NAMES as $addon_name ) {
		maybe_init_addon( $addon_name );
	}
}

function maybe_init_addon( string $name ): void {

	$init_function_name = '\\' . __NAMESPACE__ . '\\' . $name . '\\init';
	$version_const_name = '\\' . __NAMESPACE__ . '\\' . $name . '\\VERSION';
	$req_ver_const_name = '\\' . __NAMESPACE__ . '\\' . strtoupper( $name ) . '_REQUIRED_VERSION';
	$version            = defined( $version_const_name ) ? constant( $version_const_name ) : '';
	$req_ver            = defined( $req_ver_const_name ) ? constant( $req_ver_const_name ) : '';

	if ( $version && version_compare( $version, $req_ver, '>=' ) && function_exists( $init_function_name ) ) {
		$init_function_name();
	}
}

function init_admin(): void {

	require_once PLUGIN_DIR . '/php/Admin/fn-admin.php';
	require_once PLUGIN_DIR . '/php/Admin/fn-settings-page.php';
	require_once PLUGIN_DIR . '/php/Admin/fn-shortcode-creator.php';
	require_once PLUGIN_DIR . '/php/Admin/fn-debug-info.php';

	add_action( 'nextgenthemes/arve/admin/settings/sidebar', __NAMESPACE__ . '\Admin\settings_sidebar' );
	add_action( 'nextgenthemes/arve/admin/settings/content', __NAMESPACE__ . '\Admin\settings_content' );

	add_action( 'admin_bar_menu', __NAMESPACE__ . '\Admin\action_admin_bar_menu', 100 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\Admin\admin_enqueue_scripts' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\Admin\admin_enqueue_styles', 99 );
	add_action( 'admin_init', __NAMESPACE__ . '\Admin\action_admin_init_setup_messages' );
	add_action( 'media_buttons', __NAMESPACE__ . '\Admin\add_media_button', 11 );

	add_action( 'register_shortcode_ui', __NAMESPACE__ . '\Admin\register_shortcode_ui' );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\Admin\add_dashboard_widget' );

	add_filter( 'plugin_action_links_' . plugin_basename( PLUGIN_FILE ), __NAMESPACE__ . '\Admin\add_action_links' );

	add_filter( 'debug_information', __NAMESPACE__ . '\Admin\add_site_health_metadata' );
}

register_uninstall_hook( PLUGIN_FILE, __NAMESPACE__ . '\uninstall' );

function uninstall(): void {

	global $wpdb;

	if ( version_compare( $wpdb->db_version(), '8.0', '>=' ) ) {
		$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = REGEXP_REPLACE( meta_value, '<template[^>]+arve_cachetime[^>]+></template>', '' )" );
	}
}
