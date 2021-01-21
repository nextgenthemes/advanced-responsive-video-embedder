<?php
namespace Nextgenthemes\ARVE\Admin;

use \Nextgenthemes\ARVE;
use \Nextgenthemes\ARVE\Common;

function settings_sidebar() {

	if ( ! is_plugin_active( 'arve-pro/arve-pro.php' ) ) {
		print_settings_box_html( '/partials/settings-sidebar-pro.html' );
	}

	if ( ! is_plugin_active( 'arve-random-video/arve-random-video.php' ) ) {
		print_settings_box_html( '/partials/settings-sidebar-random-video.html' );
	}

	// if ( ! is_plugin_active( 'arve-amp/arve-amp.php' ) ) {
	// 	print_settings_box_html( '/partials/settings-sidebar-amp.html' );
	// }

	print_settings_box_html( '/partials/settings-sidebar-rate.html' );
}

function print_settings_box_html( $file ) {
	echo '<div class="ngt-sidebar-box">';
	readfile( __DIR__ . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
	echo '</div>';
}

function filter_save_options( $options ) {

	update_option( 'nextgenthemes_arve_oembed_recache', time() );

	$action            = json_decode( $options['action'] );
	$options['action'] = '';

	if ( $action ) {
		$product_id  = get_products()[ $action->product ]['id'];
		$product_key = $options[ $action->product ];

		$options[ $action->product . '_status' ] = api_action( $product_id, $product_key, $action->action );
	}

	return $option;
}

// unused, trigger recaching is rebuild is probably better, also there this leaves the times in the DB so will this even work?
function delete_oembed_caches() {

	global $wpdb;

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND meta_value LIKE %s",
			'%_oembed_%',
			'%' . $wpdb->esc_like( 'id="arve-' ) . '%'
		)
	);
}
