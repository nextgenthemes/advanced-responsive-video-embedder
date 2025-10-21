<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE\Admin;

use function Nextgenthemes\ARVE\is_dev_mode;

/**
 * Add ARVE‑related data to the Site Health metadata array.
 *
 * @param array   <string, array <string|array<mixed>>> $metadata Existing Site Health metadata.
 * @return array  <string, array <string|array<mixed>>>           Updated  Site Health metadata.
 */
function add_site_health_metadata( array $metadata ): array {

	$option_fields = array();
	$arve_options  = get_option( 'nextgenthemes_arve' );

	if ( is_array( $arve_options ) ) {

		foreach ( $arve_options as $key => $value ) {
			$option_fields[ 'option_' . $key ] = [
				'label'  => $key,
				'value'  => var_export( $value, true ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			];
		}
	}

	$arve_metadata['arve'] = [
		'label'  => __( 'ARVE - Advanced Responsive Video Embedder', 'advanced-responsive-video-embedder' ),
		'fields' => [
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
				'value' => var_export( is_dev_mode(), true ), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			],
			'dismissed_notices' => [
				'label' => __( 'Dismissed Notices', 'advanced-responsive-video-embedder' ),
				'value' => var_export( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
					get_user_meta(
						get_current_user_id(),
						'dnh_dismissed_notices',
						false
					),
					true
				),
			],
		],
	];

	$arve_metadata['arve']['fields'] = $option_fields + $arve_metadata['arve']['fields'];

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
