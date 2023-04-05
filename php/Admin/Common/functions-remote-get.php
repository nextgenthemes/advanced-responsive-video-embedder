<?php
namespace Nextgenthemes\ARVE\Common;

// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralDomain
function remote_get_json( $url, array $args = array(), $json_name = false ) {
	return remote_get_json_cached( $url, $args, $json_name );
}

function remote_get_json_cached( $url, array $args = array(), $json_name = false ) {

	$response = remote_get_body_cached( $url, $args );

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
					// Translators: 1 URL 2 JSON value
					__( 'url: %1$s JSON value <code>%2$s</code> does not exist or is empty', 'advanced-responsive-video-embedder' ),
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

function remote_get_body( $url, array $args = array() ) {

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

function remote_get_body_cached( $url, array $args = array(), $time = DAY_IN_SECONDS ) {

	$transient_name = 'nextgenthemes_remote_get_body_' . $url . wp_json_encode( $args );
	$response       = get_transient( $transient_name );

	if ( false === $response ) {
		$response = remote_get_body( $url, $args );

		set_transient( $transient_name, $response, $time );
	}

	return $response;
}

function get_image_size( $img_url ) {
	$response = remote_get_body( $img_url, [ 'timeout' => 0.5 ] );

	if ( is_wp_error( $response ) ) {
		return false;
	}

	return getimagesizefromstring( $response );
}
