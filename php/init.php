<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

add_action( 'plugins_loaded', __NAMESPACE__ . '\init_920', 9 );

function init_920(): void {

	stop_outdated_addons_from_executing();
	init_public();

	if ( is_admin() ) {
		init_admin();
	}
}

function init_public(): void {

	add_option( 'arve_install_date', time() );

	if ( version_compare( get_option( 'arve_version', '' ), '10.3.5-alpha1', '<' ) ) {
		add_action(
			'wp_loaded',
			function (): void {
				delete_oembed_cache();
			}
		);
	}

	update_option( 'arve_version', VERSION );

	require_once PLUGIN_DIR . '/php/Base.php';
	require_once PLUGIN_DIR . '/php/Video.php';
	require_once PLUGIN_DIR . '/php/fn-deprecated.php';
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

	add_action( 'init', __NAMESPACE__ . '\add_oembed_providers' );
	add_action( 'init', __NAMESPACE__ . '\init_nextgenthemes_settings' );
	add_action( 'init', __NAMESPACE__ . '\register_assets' );
	add_action( 'init', __NAMESPACE__ . '\load_textdomain' );
	add_filter( 'oembed_remote_get_args', __NAMESPACE__ . '\vimeo_referer', 10, 2 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\create_shortcodes', 999 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\create_url_handlers', 999 );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\action_wp_enqueue_scripts' );
	add_filter( 'render_block_core/embed', __NAMESPACE__ . '\remove_embed_block_aspect_ratio' );
	add_action( 'wp_video_shortcode_override', __NAMESPACE__ . '\wp_video_shortcode_override', 10, 4 );
	add_filter( 'language_attributes', __NAMESPACE__ . '\html_id' );
	add_filter( 'oembed_dataparse', __NAMESPACE__ . '\filter_oembed_dataparse', PHP_INT_MAX, 3 );
	add_filter( 'embed_oembed_html', __NAMESPACE__ . '\filter_embed_oembed_html', OEMBED_HTML_PRIORITY, 4 );

	add_action( 'elementor/widgets/register', __NAMESPACE__ . '\register_elementor_widget' );
}

function init_admin(): void {

	require_once PLUGIN_DIR . '/php/Admin/fn-admin.php';
	require_once PLUGIN_DIR . '/php/Admin/fn-settings-page.php';
	require_once PLUGIN_DIR . '/php/Admin/fn-shortcode-creator.php';
	require_once PLUGIN_DIR . '/php/Admin/fn-debug-info.php';

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

	add_filter( 'plugin_action_links_' . plugin_basename( PLUGIN_FILE ), __NAMESPACE__ . '\Admin\add_action_links' );
	//add_filter( 'nextgenthemes_arve_save_options', __NAMESPACE__ . '\Admin\filter_save_options' ); // TODO remove?

	add_filter( 'debug_information', __NAMESPACE__ . '\Admin\add_site_health_metadata' );
}

register_uninstall_hook( PLUGIN_FILE, __NAMESPACE__ . '\uninstall' );

function uninstall(): void {

	global $wpdb;

	if ( version_compare( $wpdb->db_version(), '8.0', '>=' ) ) {
		$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = REGEXP_REPLACE( meta_value, '<template data-arve[^>]+></template>', '' )" );
	} else {
		delete_oembed_cache();
		delete_option( 'arve_version' ); // this will cause another cache clear on reinstall
	}
}

/**
 * Deletes the oEmbed cache for all posts.
 *
 * @link https://github.com/wp-cli/embed-command/blob/c868ec31c65ffa1a61868a91c198a5d815b5bafa/src/Cache_Command.php
 * @author Nicolas Lemoine <https://n5s.dev>
 * @author Nicolas Jonas <https://nextgenthemes.com>
 * @copyright Copyright (c) 2024 Nicolas Lemoine, Nicolas Jonas
 *
 * @return int|false The number of rows deleted or false on failure.
 */
function delete_oembed_cache(): string {

	global $wpdb, $wp_embed;

	$message = '';

	// Get post meta oEmbed caches
	$oembed_post_meta_post_ids = (array) $wpdb->get_col(
		$wpdb->prepare(
			"SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key LIKE %s",
			$wpdb->esc_like( '_oembed_' ) . '%'
		)
	);

	// Get posts oEmbed caches
	$oembed_post_post_ids = (array) $wpdb->get_col(
		"SELECT ID FROM $wpdb->posts
		WHERE post_type = 'oembed_cache'"
	);

	// Get transient oEmbed caches
	$oembed_transients = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
			$wpdb->esc_like( '_transient_oembed_' ) . '%'
		)
	);

	$oembed_caches = array(
		'post'        => $oembed_post_meta_post_ids,
		'oembed post' => $oembed_post_post_ids,
		'transient'   => $oembed_transients,
	);

	$total = array_sum(
		array_map(
			function ( $items ) {
				return count( $items );
			},
			$oembed_caches
		)
	);

	// Delete post meta oEmbed caches
	foreach ( $oembed_post_meta_post_ids as $post_id ) {
		$wp_embed->delete_oembed_caches( $post_id );
	}

	// Delete posts oEmbed caches
	foreach ( $oembed_post_post_ids as $post_id ) {
		wp_delete_post( $post_id, true );
	}

	// Delete transient oEmbed caches
	foreach ( $oembed_transients as $option_name ) {
		delete_transient( str_replace( '_transient_', '', $option_name ) );
	}

	if ( $total > 0 ) {
		$details = array();
		foreach ( $oembed_caches as $type => $items ) {
			$count     = count( $items );
			$details[] = sprintf(
				'%1$d %2$s %3$s',
				$count,
				$type,
				esc_html__( 'cache(s)', 'advanced-responsive-video-embedder' )
			);
		}

		$message .= sprintf(
			'Cleared %1$d oEmbed %2$s: %3$s.',
			$total,
			esc_html__( 'cache(s)', 'advanced-responsive-video-embedder' ),
			implode( ', ', $details )
		);

	} else {
		$message .= esc_html__( 'No oEmbed caches to clear!', 'advanced-responsive-video-embedder' );
	}

	if ( wp_using_ext_object_cache() ) {
		$object_cache_msg = esc_html__( 'Oembed transients are stored in an external object cache, and ARVE only deletes those stored in the database. You must flush the cache to delete all transients.', 'advanced-responsive-video-embedder' );
		update_option( 'arve_object_cache_msg', $object_cache_msg );

		$message .= ' ' . $object_cache_msg;
	}

	return $message;
}

function stop_outdated_addons_from_executing(): void {

	if ( defined( 'Nextgenthemes\ARVE\Pro\VERSION' )
		&& version_compare( \Nextgenthemes\ARVE\Pro\VERSION, PRO_VERSION_REQUIRED, '<' )
	) {
		remove_action( 'plugins_loaded', 'Nextgenthemes\ARVE\Pro\init', 15 );
	}
}
