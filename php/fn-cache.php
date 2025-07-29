<?php

declare(strict_types = 1);

namespace Nextgenthemes\ARVE;

function maybe_delete_oembed_cache(): void {

	$ver = get_option( 'arve_version', '' );

	if ( version_compare( $ver, '10.3.5-alpha1', '<' ) ) {
		add_action( 'wp_loaded', __NAMESPACE__ . '\delete_oembed_cache_on_update' );
	}
}

function delete_oembed_cache_on_update(): void {
	delete_oembed_cache( '', 'arve-cachetime' );
}

/**
 * Deletes the oEmbed caches.
 *
 * @link https://github.com/wp-cli/embed-command/blob/c868ec31c65ffa1a61868a91c198a5d815b5bafa/src/Cache_Command.php
 * @author Nicolas Jonas <https://nextgenthemes.com>
 * @author Nicolas Lemoine <https://n5s.dev>
 * @copyright Copyright (c) 2025, Nicolas Jonas
 * @copyright Copyright (c) 2024, Nicolas Lemoine
 *
 * @return int|false The number of rows deleted or false on failure.
 */
function delete_oembed_cache( string $like = '', string $not_like = '' ): string {

	global $wpdb, $wp_embed;

	$message = '';

	// Get post meta oEmbed caches
	if ( $like ) {
		$oembed_post_meta_post_ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key LIKE %s AND meta_value LIKE %s",
				$wpdb->esc_like( '_oembed_' ) . '%',
				'%' . $wpdb->esc_like( $like ) . '%'
			)
		);
	} elseif ( $not_like ) {
		$oembed_post_meta_post_ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM $wpdb->postmeta
				WHERE meta_key LIKE %s
				AND meta_key NOT LIKE %s
				AND meta_value NOT LIKE %s",
				$wpdb->esc_like( '_oembed_' ) . '%',
				$wpdb->esc_like( '_oembed_time_' ) . '%',
				'%' . $wpdb->esc_like( $not_like ) . '%'
			)
		);
	} else {
		$oembed_post_meta_post_ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM $wpdb->postmeta WHERE meta_key LIKE %s",
				$wpdb->esc_like( '_oembed_' ) . '%'
			)
		);
	}

	// Get posts oEmbed caches
	if ( $like ) {
		$oembed_post_post_ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_type = 'oembed_cache' AND post_content LIKE %s",
				'%' . $wpdb->esc_like( $like ) . '%'
			)
		);
	} elseif ( $not_like ) {
		$oembed_post_post_ids = (array) $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM $wpdb->posts WHERE post_type = 'oembed_cache' AND post_content NOT LIKE %s",
				'%' . $wpdb->esc_like( $not_like ) . '%'
			)
		);
	} else {
		$oembed_post_post_ids = (array) $wpdb->get_col(
			"SELECT ID FROM $wpdb->posts WHERE post_type = 'oembed_cache'"
		);
	}

	// Get transient oEmbed caches
	if ( $like ) {
		$oembed_transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value LIKE %s",
				$wpdb->esc_like( '_transient_oembed_' ) . '%',
				'%' . $wpdb->esc_like( $like ) . '%'
			)
		);
	} elseif ( $not_like ) {
		$oembed_transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value NOT LIKE %s",
				$wpdb->esc_like( '_transient_oembed_' ) . '%',
				'%' . $wpdb->esc_like( $not_like ) . '%'
			)
		);
	} else {
		$oembed_transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_oembed_' ) . '%'
			)
		);
	}

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

	return $message;
}

/**
 * @global wpdb $wpdb
 */
function delete_transients( string $prefix, string $contains = '' ): string {

	global $wpdb;

	if ( $contains ) {
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value LIKE %s",
				$wpdb->esc_like( '_transient_' . $prefix ) . '%',
				'%' . $wpdb->esc_like( $contains ) . '%'
			)
		);
	} else {
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_' . $prefix ) . '%'
			)
		);
	}

	$count = 0;

	foreach ( $transients as $transient_name ) {
		// Strip '_transient_' to get the key for delete_transient()
		$transient_key = str_replace( '_transient_', '', $transient_name );
		if ( delete_transient( $transient_key ) ) {
			++$count;
		}
	}

	if ( $count > 0 ) {
		return sprintf(
			// translators: %d: Number of transients deleted.
			esc_html__( 'Deleted %d transients.', 'advanced-responsive-video-embedder' ),
			$count
		);
	}

	return esc_html__( 'No transients deleted.', 'advanced-responsive-video-embedder' );
}
