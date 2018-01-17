<?php

function arve_remote_data( $args ) {

	$default_args = array(
		'method'         => 'get',
		'url'            => '',
		'json'           => true,
		'wp_remote_args' => array(
			'timeout'    => 5,
			// Lets not tell them we are WordPress.
			'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36',
		)
	);

	$args                   = wp_parse_args( $args, $default_args );
	$args['wp_remote_args'] = wp_parse_args( $args['wp_remote_args'], $default_args['wp_remote_args'] );

	// retry with wp_safe_remote_get.
	if ( 'post' === $args['method'] ) {
		$response = wp_safe_remote_post( $args['url'], $args['wp_remote_args'] );
	} else {
		$response = wp_safe_remote_get( $args['url'], $args['wp_remote_args'] );
	}

	$response_code = wp_remote_retrieve_response_code( $response );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new WP_Error(
			'remote_get',
			sprintf(
				// translators: %s is error code.
				__( 'remote_get error: Status code was expected to be 200 but was %s.', 'advanced-responsive-video-embedder' ),
				$response_code
			)
		);
	}

	$out = wp_remote_retrieve_body( $response );

	if ( '' === $out ) {
		return new WP_Error( 'remote_get', __( 'Empty body', 'advanced-responsive-video-embedder' ) );
	}

	if ( $args['json'] ) {
		$out = json_decode( $out );

		if ( null === $out ) {
			return new WP_Error( 'remote_get', __( 'json_decode returned null', 'advanced-responsive-video-embedder' ) );
		}
	}

	return $out;
};

function arve_remote_data_cached( $args ) {

	if ( empty( $args['cache_time'] ) ) {
		$args['cache_time'] = HOUR_IN_SECONDS;
	}

	$transient_name = 'arve_remote_data_' . $args['url'];
	$cache          = get_transient( $transient_name );

	if ( false === $cache || defined( 'ARVE_DEBUG' ) ) {

		$cache = arve_remote_data( $args );

		set_transient( $transient_name, $cache, $args['cache_time'] );
	}

	return $cache;
}
