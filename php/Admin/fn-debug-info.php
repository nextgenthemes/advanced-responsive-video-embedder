<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Admin;

use function Nextgenthemes\ARVE\is_dev_mode;

// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_var_export
function add_site_health_metadata( array $metadata ): array {

	$arve_metadata['arve'] = [
		'label'  => __( 'ARVE - Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		'fields' => [
			'options' => [
				'label' => __( 'ARVE Options', 'advanced-responsive-video-embedder' ),
				'value' => var_export( get_option( 'nextgenthemes_arve' ), true ),
			],
			'arve' => [
				'label' => __( 'ARVE', 'advanced-responsive-video-embedder' ),
				'value' => plugin_ver_status( 'advanced-responsive-video-embedder/advanced-responsive-video-embedder.php' ),
			],
			'arve_pro' => [
				'label' => __( 'ARVE Pro', 'advanced-responsive-video-embedder' ),
				'value' => plugin_ver_status( 'arve-pro/arve-pro.php' ),
			],
			'arve_amp' => [
				'label' => __( 'ARVE AMP', 'advanced-responsive-video-embedder' ),
				'value' => plugin_ver_status( 'arve-amp/arve-amp.php' ),
			],
			'arve_sticky_videos' => [
				'label' => __( 'ARVE Sticky Videos', 'advanced-responsive-video-embedder' ),
				'value' => plugin_ver_status( 'arve-sticky-videos/arve-sticky-videos.php' ),
			],
			'arve_random_video' => [
				'label' => __( 'ARVE Random Video', 'advanced-responsive-video-embedder' ),
				'value' => plugin_ver_status( 'arve-random-video/arve-random-video.php' ),
			],
			'wp_version' => [
				'label' => __( 'WordPress Version', 'advanced-responsive-video-embedder' ),
				'value' => $metadata['wp-core']['fields']['version']['value'],
			],
			'php_version' => [
				'label' => __( 'PHP Version', 'advanced-responsive-video-embedder' ),
				'value' => $metadata['wp-server']['fields']['php_version']['value'],
			],
			'webserver' => [
				'label' => __( 'Web Server', 'advanced-responsive-video-embedder' ),
				'value' => $metadata['wp-server']['fields']['httpd_software']['value'],
			],
			'is_dev_mode' => [
				'label' => __( 'is_dev_mode', 'advanced-responsive-video-embedder' ),
				'value' => var_export( is_dev_mode(), true ),
			],
			'dismissed_notices' => [
				'label' => __( 'Dismissed Notices', 'advanced-responsive-video-embedder' ),
				'value' => var_export( get_user_meta( get_current_user_id(), 'dnh_dismissed_notices' ), true ),
			],
		],
	];

	$metadata = array_merge( $arve_metadata, $metadata );

	return $metadata;
}

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
