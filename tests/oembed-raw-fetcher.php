<?php

declare(strict_types = 1);

// phpcs:disable WordPress.WP.AlternativeFunctions

/**
 * Fetches raw oEmbed data directly from a provider's API endpoint.
 *
 * Uses zero WordPress functions — pure PHP HTTP request.
 * Intended for use inside unit tests to inspect what providers actually return.
 *
 * @param string $url The URL to fetch oEmbed data for.
 * @return object|null The decoded JSON response, or null on failure.
 */
function get_oembed_direct( string $url ): ?object {

	$providers = [
		'vimeo'   => 'https://vimeo.com/api/oembed.json?url=%s',
		'youtube' => 'https://www.youtube.com/oembed?url=%s&format=json',
	];

	$host     = (string) parse_url( $url, PHP_URL_HOST );
	$provider = null;

	foreach ( $providers as $name => $endpoint ) {
		if ( str_contains( $host, $name ) ) {
			$provider = $name;
			break;
		}
	}

	if ( ! $provider ) {
		return null;
	}

	$api_url = sprintf( $providers[ $provider ], rawurlencode( strtok( $url, '#' ) ) );

	// Try file_get_contents first.
	$ctx  = stream_context_create( [ 'http' => [ 'user_agent' => 'oembed-debug/1.0' ] ] );
	$json = @file_get_contents( $api_url, false, $ctx ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	$json = false !== $json ? $json : null;

	if ( null === $json && function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL            => $api_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT      => 'oembed-debug/1.0',
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_TIMEOUT        => 10,
			]
		);
		$json = curl_exec( $ch );
		$json = false !== $json ? $json : null;
	}

	if ( null === $json ) {
		return null;
	}

	try {
		return json_decode( $json, false, 512, JSON_THROW_ON_ERROR );
	} catch ( \JsonException $e ) {
		return null;
	}
}
