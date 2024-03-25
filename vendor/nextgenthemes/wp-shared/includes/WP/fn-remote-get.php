<?php declare(strict_types=1);
namespace Nextgenthemes\WP;

/**
 * Retrieves JSON data from a remote URL.
 *
 * @return mixed
 */
function remote_get_json( string $url, array $args = array(), string $json_name = '' ) {
	return remote_get_json_cached( $url, $args, $json_name, 0 );
}

/**
 * Remote get JSON from a URL and cache the response.
 *
 * @return mixed|WP_Error The decoded JSON response, or the specified JSON value if $json_name is provided.
 */
function remote_get_json_cached( string $url, array $args = array(), string $json_name = '', int $time = DAY_IN_SECONDS ) {

	if ( $time <= 0 ) {
		$response = remote_get_body( $url, $args );
	} else {
		$response = remote_get_body_cached( $url, $args, $time );
	}

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	try {
		$response = json_decode( $response, false, 128, JSON_THROW_ON_ERROR );
	} catch ( \Exception $e ) {

		return new \WP_Error(
			'json-decode-error',
			sprintf(
				// Translators: URL.
				__( 'url: %1$s json_decode error: %2$s.', 'advanced-responsive-video-embedder' ),
				esc_html( $url ),
				$e->getMessage()
			)
		);
	}

	if ( $json_name ) {
		if ( empty( $response->$json_name ) ) {
			return new \WP_Error(
				'json-value-empty',
				sprintf(
					// Translators: 1 URL 2 JSON value
					__( 'url: %1$s JSON value <code>%2$s</code> does not exist or is empty. Full Json: %3$s', 'advanced-responsive-video-embedder' ),
					esc_html( $url ),
					esc_html( $json_name ),
					esc_html( $response )
				)
			);
		} else {
			return $response->$json_name;
		}
	}

	return $response;
}

/**
 * @return mixed|WP_Error
 */
function remote_get_body( string $url, array $args = array() ) {

	$response      = wp_safe_remote_get( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new \WP_Error(
			$response_code,
			sprintf(
				// Translators: 1 URL 2 HTTP response code.
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
}

/**
 * @return mixed|WP_Error
 */
function remote_get_head( string $url, array $args = array() ) {

	$response      = wp_safe_remote_head( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new \WP_Error(
			$response_code,
			sprintf(
				// Translators: 1 URL 2 HTTP response code.
				__( 'url: %1$s Status code 200 expected but was %2$s.', 'advanced-responsive-video-embedder' ),
				$url,
				$response_code
			)
		);
	}

	return $response;
}

/**
 * Retrieves the body content from a remote URL, with caching for improved performance.
 *
 * @param string $url The URL of the remote resource.
 * @param array $args Optional. Additional arguments to include in the request.
 * @param int $time Optional. The duration in seconds to cache the response. Default is DAY_IN_SECONDS. 0 to disable caching.
 * @return mixed The response body content from the remote URL.
 */
function remote_get_body_cached( string $url, array $args = array(), int $time = DAY_IN_SECONDS ) {

	$transient_name = 'nextgenthemes_remote_get_body_' . $url . wp_json_encode( $args );
	$response       = get_transient( $transient_name );

	if ( false === $response ) {
		$response = remote_get_body( $url, $args );

		set_transient( $transient_name, $response, $time );
	}

	return $response;
}
