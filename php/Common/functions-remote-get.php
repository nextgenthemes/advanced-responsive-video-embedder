<?php
namespace Nextgenthemes\ARVE\Common;

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain

function ngt_remote_get_json( $url, array $args = [], $json_name = false ) {

	$response = ngt_remote_get_body( $url, $args );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$response = json_decode( $response );

	if ( null === $response ) {
		return new \WP_Error(
			'json-null',
			sprintf(
				// Translators: URL.
				__( 'url: %s json_decode returned null.', 'advanced-responsive-video-embedder' ),
				esc_url( $url )
			)
		);
	}

	if ( $json_name ) {

		if ( empty( $response->$json_name ) ) {
			return new \WP_Error(
				'json-value-empty',
				sprintf(
					__( "url: %s JSON value '%s' does not exist or is empty", 'advanced-responsive-video-embedder' ),
					esc_url( $url ),
					esc_html( $json_name )
				)
			);
		} else {
			return $response->$json_name;
		}
	}

	return $response;
}

function ngt_remote_get_body( $url, array $args = [] ) {

	$response      = wp_safe_remote_get( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

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

	$response = wp_remote_retrieve_body( $response );

	if ( '' === $response ) {
		return new \WP_Error(
			'empty-body',
			sprintf(
				// Translators: URL.
				__( 'url: %s Empty Body.', 'advanced-responsive-video-embedder' ),
				$url
			)
		);
	}

	return $response;
};

function ngt_remote_get_cached( $url, array $args, $json = true ) {

	$defaults = array(
		'cache_time' => 3600,
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