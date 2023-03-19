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
	$active_plugins = get_option( 'active_plugins', array() );

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
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );
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

function list_hooks( $hook = '' ) {
	global $wp_filter;

	if ( isset( $wp_filter[ $hook ]->callbacks ) ) {
		array_walk(
			$wp_filter[ $hook ]->callbacks,
			function( $callbacks, $priority ) use ( &$hooks ) {
				foreach ( $callbacks as $id => $callback ) {
					$hooks[] = array_merge(
						[
							'id'       => $id,
							'priority' => $priority,
						],
						$callback
					);
				}
			}
		);
	} else {
		return [];
	}

	foreach ( $hooks as &$item ) {
		// skip if callback does not exist
		if ( ! is_callable( $item['function'] ) ) {
			continue;
		}

		// function name as string or static class method eg. 'Foo::Bar'
		if ( is_string( $item['function'] ) ) {
			$ref = strpos( $item['function'], '::' )
				? new \ReflectionClass( strstr( $item['function'], '::', true ) )
				: new \ReflectionFunction( $item['function'] );

			$item['file'] = $ref->getFileName();
			$item['line'] = get_class( $ref ) === 'ReflectionFunction'
				? $ref->getStartLine()
				: $ref->getMethod( substr( $item['function'], strpos( $item['function'], '::' ) + 2 ) )->getStartLine();

			// array( object, method ), array( string object, method ), array( string object, string 'parent::method' )
		} elseif ( is_array( $item['function'] ) ) {

			$ref = new \ReflectionClass( $item['function'][0] );

			// $item['function'][0] is a reference to existing object
			$item['function'] = array(
				is_object( $item['function'][0] ) ? get_class( $item['function'][0] ) : $item['function'][0],
				$item['function'][1],
			);

			$item['file'] = $ref->getFileName();
			$item['line'] = strpos( $item['function'][1], '::' )
				? $ref->getParentClass()->getMethod( substr( $item['function'][1], strpos( $item['function'][1], '::' ) + 2 ) )->getStartLine()
				: $ref->getMethod( $item['function'][1] )->getStartLine();

			// closures
		} elseif ( is_callable( $item['function'] ) ) {
			$ref = new \ReflectionFunction( $item['function'] );

			$item['function'] = get_class( $item['function'] );
			$item['file']     = $ref->getFileName();
			$item['line']     = $ref->getStartLine();
		}
	}

	return $hooks;
}
