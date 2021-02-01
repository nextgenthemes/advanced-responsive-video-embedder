<?php
namespace Nextgenthemes\ARVE\Common\Admin;

use \Nextgenthemes\ARVE\Common;

function plugin_ver_status( $folder_and_filename ) {

	$file = WP_PLUGIN_DIR . '/' . $folder_and_filename;

	if ( ! is_file( $file ) ) {
		return 'NOT INSTALLED';
	}

	$data = get_plugin_data( $file );
	$out  = $data['Version'];

	if ( ! is_plugin_active( $folder_and_filename ) ) {
		$out .= ' INACTIVE';
	}

	return $out;
}

function print_active_plugins() {
	$allplugins     = get_plugins();
	$active_plugins = get_option( 'active_plugins', [] );

	echo "ACTIVE PLUGINS:\n";
	foreach ( $allplugins as $plugin_path => $plugin ) {
		// If the plugin isn't active, don't show it.
		if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
			continue;
		}
		echo esc_html( "{$plugin['Name']}: {$plugin['Version']}\n" );
	}
}

function print_network_active_plugins() {

	if ( ! is_multisite() ) {
		return;
	}

	echo "NETWORK ACTIVE PLUGINS: \n";
	$allplugins     = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', [] );
	foreach ( $allplugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );
		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}
		$plugin = get_plugin_data( $plugin_path );
		echo esc_html( "{$plugin['Name']}: {$plugin['Version']}\n" );
	}
}
