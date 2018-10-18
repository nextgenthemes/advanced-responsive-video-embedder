<?php
namespace Nextgenthemes\Utils;

// phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralDomain

function remote_get( $url, array $args = [], $json = true ) {

	$response      = wp_remote_post( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	// retry with wp_remote_get
	if ( is_wp_error( $response ) || 200 !== $response_code ) {
		$response      = wp_remote_get( $url, $args );
		$response_code = wp_remote_retrieve_response_code( $response );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new \WP_Error(
			'remote_get',
			sprintf(
				// Translators: %s is HTTP presponse code.
				__( 'remote_get error: Status code was expected to be 200 but was %s.', \Nextgenthemes\TEXTDOMAIN ),
				$response_code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );

	if ( '' === $body ) {
		return new \WP_Error( 'remote_get', __( 'Empty body', \Nextgenthemes\TEXTDOMAIN ) );
	}

	if ( $json ) {
		$response = json_decode( $body );

		if ( null === $response ) {
			return new \WP_Error( 'remote_get', __( 'json_decode returned null', \Nextgenthemes\TEXTDOMAIN ) );
		}
	}

	return $response;
};

function remote_get_cached( array $args ) {

	$defaults = array(
		'args'       => array(),
		'json'       => true,
		'cache_time' => 3600,
	);

	$args = wp_parse_args( $args, $defaults );

	$transient_name = 'nextgenthemes_remote_get_' . $args['url'];
	$cache          = get_transient( $transient_name );

	if ( false === $cache || defined( 'ARVE_DEBUG' ) ) {

		$cache = remote_get( $args['url'], $args['args'], $args['json'] );

		if ( ! is_wp_error( $cache ) ) {
			set_transient( $transient_name, $cache, $args['cache_time'] );
		}
	}

	return $cache;
}
