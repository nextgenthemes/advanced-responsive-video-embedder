<?php declare(strict_types=1);
namespace Nextgenthemes\ARVE\Admin;

function plugin_ver_status( string $folder_and_filename ): string {

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

function print_active_plugins(): void {
	$all_plugins    = get_plugins();
	$active_plugins = get_option( 'active_plugins', array() );

	echo "ACTIVE PLUGINS:\n";
	foreach ( $all_plugins as $plugin_path => $plugin ) {
		// If the plugin isn't active, don't show it.
		if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
			continue;
		}
		$name = $plugin['Name'];
		$ver  = $plugin['Version'];

		echo esc_html( "$name: $ver\n" );
	}
}

function print_network_active_plugins(): void {

	if ( ! is_multisite() ) {
		return;
	}

	echo "NETWORK ACTIVE PLUGINS: \n";
	$all_plugins    = wp_get_active_network_plugins();
	$active_plugins = get_site_option( 'active_sitewide_plugins', array() );
	foreach ( $all_plugins as $plugin_path ) {
		$plugin_base = plugin_basename( $plugin_path );
		// If the plugin isn't active, don't show it.
		if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
			continue;
		}
		$plugin = get_plugin_data( $plugin_path );
		$name   = $plugin['Name'];
		$ver    = $plugin['Version'];

		echo esc_html( "$name: $ver\n" );
	}
}

function list_hooks( string $hook = '' ): array {
	global $wp_filter;

	if ( isset( $wp_filter[ $hook ]->callbacks ) ) {
		array_walk(
			$wp_filter[ $hook ]->callbacks,
			function ( $callbacks, $priority ) use ( &$hooks ): void {
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
