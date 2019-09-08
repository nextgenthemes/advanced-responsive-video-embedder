<?php
namespace Nextgenthemes\ARVE\Common;

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain

function ngt_remote_get( $url, array $args = [], $json = true ) {

	$response      = wp_safe_remote_post( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	// retry with wp_remote_get
	if ( is_wp_error( $response ) || 200 !== $response_code ) {
		$response      = wp_safe_remote_get( $url, $args );
		$response_code = wp_remote_retrieve_response_code( $response );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new \WP_Error(
			$response_code,
			sprintf(
				// Translators: 1 URL 2 HTTP presponse code.
				__( 'url: %1$s Status code 200 expected but was %2$s.', 'advanced-responsive-video-embedder' ),
				$url,
				$response_code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );

	if ( '' === $body ) {
		return new \WP_Error(
			'empty-body',
			sprintf(
				// Translators: URL.
				__( 'url: %s Empty Body.', 'advanced-responsive-video-embedder' ),
				$url
			)
		);
	}

	if ( $json ) {
		$response = json_decode( $body );

		if ( null === $response ) {
			return new \WP_Error(
				'json-null',
				sprintf(
					// Translators: URL.
					__( 'url: %s json_decode returned null.', 'advanced-responsive-video-embedder' ),
					$url
				)
			);
		}
	}

	return $response;
};

function ngt_remote_get_cached( $url, array $args, $json = true ) {

	$defaults = array(
		'cache_time'       => 3600,
	);

	$args = wp_parse_args( $args, $defaults );

	$transient_name = 'nextgenthemes_remote_get_' . $url . json_encode( $args );
	$cache          = get_transient( $transient_name );

	if ( false === $cache ) {
		$cache = ngt_remote_get( $url, $args['args'] );
		set_transient( $transient_name, $cache, $args['cache_time'] );
	}

	return $cache;
}
