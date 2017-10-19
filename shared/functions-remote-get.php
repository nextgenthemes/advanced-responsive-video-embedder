<?php

function arve_remote_get( $url, $args = array(), $json = true ) {

	$response      = wp_remote_post( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	// retry with wp_remote_GET
	if ( is_wp_error( $response ) || 200 !== $response_code ) {
		$response      = wp_remote_get( $url, $args );
		$response_code = wp_remote_retrieve_response_code( $response );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new WP_Error(
			'remote_get',
			sprintf(
				__( 'remote_get error: Status code was expected to be 200 but was %s.', ARVE_SLUG ),
				$response_code
			)
		);
	}

	$body = wp_remote_retrieve_body( $response );

	if ( '' === $body ) {
		return new WP_Error( 'remote_get', __( 'Empty body', ARVE_SLUG ) );
	}

	if( $json ) {
		$response = json_decode( $body );

		if ( null == $response ) {
			return new WP_Error( 'remote_get', __( 'json_decode returned null', ARVE_SLUG ) );
		}
	}

	return $response;
};

function arve_remote_get_cached( $args ) {

	$defaults = array(
		'args' => array(),
		'json' => true,
		'cache_time' => 3600,
	);

	$args = wp_parse_args( $args, $defaults );

	$transient_name = 'arve_remote_get_' . $args['url'];
	$cache = get_transient( $transient_name );

	if ( false === $cache || defined( 'ARVE_DEBUG' ) ) {

		$cache = arve_remote_get( $args['url'], $args['args'], $args['json'] );

		if ( ! is_wp_error( $cache ) ) {
			set_transient( $transient_name, $cache, $args['cache_time'] );
		}
	}

	return $cache;
}
