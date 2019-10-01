<?php

function arve_get_attachment_image_url_or_srcset( $url_or_srcset, $thumbnail ) {

	$found = arve_get_cached_attachment_image_url_or_srcset( $url_or_srcset, $thumbnail );

	if ( $found ) {

		return $found;

	} elseif ( 'url' === $url_or_srcset ) {

		return new WP_Error( 'wp thumbnail', __( 'No attachment with that ID', ARVE_SLUG ) );

	} else {

		return false;
	}
}

function arve_get_cached_attachment_image_url_or_srcset( $url_or_srcset, $attachment_id ) {

	$options        = arve_get_options();
	$transient_name = "arve_attachment_image_{$url_or_srcset}_{$attachment_id}";
	$transient      = get_transient( $transient_name );
	$time           = (int) $options['wp_image_cache_time'];

	if ( false === $transient || $time <= 0 ) {

		if ( 'srcset' === $url_or_srcset ) {

			$out = wp_get_attachment_image_srcset( $attachment_id, 'small' );

		} elseif ( 'url' === $url_or_srcset ) {

			$out = wp_get_attachment_image_url( $attachment_id, 'small' );
		}

		set_transient( $transient_name, (string) $out, $time );

	} else {

		$out = $transient;
	}

	return $out;
}
