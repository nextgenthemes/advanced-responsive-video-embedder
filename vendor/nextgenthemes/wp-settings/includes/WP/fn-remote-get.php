<?php

declare(strict_types = 1);

namespace Nextgenthemes\WP;

use WP_Error;

/**
 * Retrieves JSON data from a remote URL.
 *
 * @return mixed|WP_Error
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

	$response = remote_get_body_cached( $url, $args, $time );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	try {
		$response = json_decode( $response, true, 128, JSON_THROW_ON_ERROR );
	} catch ( \Exception $e ) {

		return new WP_Error(
			'json-decode-error',
			sprintf(
				// Translators: %1$s URL, %2$s json_decode error
				__( 'url: %1$s json_decode error: %2$s.', 'advanced-responsive-video-embedder' ),
				esc_html( $url ),
				$e->getMessage()
			)
		);
	}

	if ( $json_name ) {
		if ( empty( $response[ $json_name ] ) ) {
			return new WP_Error(
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
			return $response[ $json_name ];
		}
	}

	return $response;
}

/**
 * Retrieves the body content from a remote URL.
 *
 * @param string $url The URL of the remote resource.
 * @param array $args Optional. Additional arguments for wp_safe_remote_get.
 * @return mixed|WP_Error The response body content from the remote URL, or a WP_Error on failure.
 */
function remote_get_body( string $url, array $args = array() ) {

	$response      = wp_safe_remote_get( $url, $args );
	$response_code = wp_remote_retrieve_response_code( $response );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	if ( 200 !== $response_code ) {

		return new WP_Error(
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
		return new WP_Error(
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

	$response = wp_safe_remote_head( $url, $args );

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$response_code = wp_remote_retrieve_response_code( $response );

	if ( 200 !== $response_code ) {

		return new WP_Error(
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
 * @param array $args Optional. Additional arguments for wp_safe_remote_get.
 * @param int $time Optional. The duration in seconds to cache the response. Default is DAY_IN_SECONDS. 0 to disable caching.
 * @return mixed|WP_Error The response body content from the remote URL, or a WP_Error on failure.
 */
function remote_get_body_cached( string $url, array $args = array(), int $time = DAY_IN_SECONDS ) {
	return _remote_get_cached( $url, $args, $time, 'body' );
}

/**
 * Retrieves the body content from a remote URL, with caching for improved performance.
 *
 * @param string $url The URL of the remote resource.
 * @param array $args Optional. Additional arguments for wp_safe_remote_get.
 * @param int $time Optional. The duration in seconds to cache the response. Default is DAY_IN_SECONDS. 0 to disable caching.
 * @return mixed|WP_Error The response body content from the remote URL, or a WP_Error on failure.
 */
function remote_get_head_cached( string $url, array $args = array(), int $time = DAY_IN_SECONDS ) {
	return _remote_get_cached( $url, $args, $time, 'head' );
}

/**
 * Retrieves the body content from a remote URL, with caching for improved performance.
 *
 * @param string $url The URL of the remote resource.
 * @param array $args Optional. Additional arguments to include in the request.
 * @param int $time Optional. The duration in seconds to cache the response. Default is DAY_IN_SECONDS. 0 to disable caching.
 * @return WP_Error|mixed The response body content from the remote URL.
 */
function _remote_get_cached( string $url, array $args, int $time, string $type ) {

	if ( ! in_array( $type, [ 'body', 'head' ], true ) ) {
		wp_trigger_error( __FUNCTION__, 'Wrong type' );
	}

	$transient_name = 'ngt_' . $url . http_build_query( $args );
	$transient_name = shorten_transient_name( $transient_name );
	$response       = $time ? get_transient( $transient_name ) : false;

	if ( false === $response ) {

		if ( 'head' === $type ) {
			$response = remote_get_head( $url, $args );
		} else {
			$response = remote_get_body( $url, $args );
		}

		if ( $time ) {
			set_transient( $transient_name, $response, $time );
		}
	}

	return $response;
}

function shorten_transient_name( string $transient_name ): string {

	$transient_name = str_replace( 'https://', '', $transient_name );

	if ( strlen( $transient_name ) > 172 ) {
		$transient_name = preg_replace( '/[^a-zA-Z0-9_]/', '', $transient_name );
	}

	if ( strlen( $transient_name ) > 172 ) {
		$transient_name = substr( $transient_name, 0, 107 ) . '_' . hash( 'sha256', $transient_name ); // 107 + 1 + 64
	}

	return $transient_name;
}
