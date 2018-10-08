<?php
namespace Nextgenthemes\ARVE;

/*
add_filter( 'oembed_fetch_url',    'arve_filter_oembed_fetch_url', 999, 3 );

function filter_oembed_fetch_url( $provider, $url, $args ) {

	d($url);

	$url = remove_query_array( $url, 'arve' );
	$url = remove_query_array( $url, 'arve-ifp' );

	$provider = add_query_arg( 'url', urlencode( $url ), $provider );

	dd($url);

	return $provider;
}
*/

function filter_oembed_dataparse( $result, $data, $url ) {

	if ( $a = oembed2args( $data, $url ) ) {

		/*
		$arve_url_query  = extract_query_array( $url, 'arve' );
		$a               = array_merge( $a, $arve_url_query );
		$a['parameters'] = extract_query_array( $url, 'arve-ifp' );
		*/

		return shortcode_arve( $a );
	}

	return $result;
}

function oembed2args( $data, $url ) {

	if ( false === $data || 'video' !== $data->type ) {
		return false;
	}

	$provider = strtolower( $data->provider_name );

	if ( 'facebook' === $provider ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $data->html, $matches );
	} else {
		preg_match( '/<iframe [^>]*src="([^"]+)"/', $data->html, $matches );
	}

	if ( empty( $matches[1] ) ) {
		return false;
	}

	$a = array(
		'url'         => $url,
		'provider'    => $provider,
		'src'         => $matches[1],
		'oembed_data' => $data,
	);

	if ( 'facebook' === $provider ) {
		$a['src'] = 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
	}

	return apply_filters( 'arve_oembed2args', $a );
}

function extract_query_array( $url, $key ) {

	$parsed_url = wp_parse_url( $url );

	if ( empty( $parsed_url['query'] ) ) {
		return array();
	}

	wp_parse_str( $parsed_url['query'], $url_query );

	if ( ! empty( $url_query[ $key ] ) && is_array( $url_query[ $key ] ) ) {
		return $url_query[ $key ];
	}

	return array();
}

function get_url_( $url, $extract_array_name ) {

	$parsed_url = wp_parse_url( $url );

	if ( empty( $parsed_url['query'] ) ) {
		return array();
	}

	return parse_str( $parsed_url['query'], $url_query );
}

function remove_query_array( $url, $key ) {

	$parsed_url = wp_parse_url( $url );

	if ( empty( $parsed_url['query'] ) ) {
		return $url;
	}

	wp_parse_str( $parsed_url['query'], $query_array );
	$url = str_replace( $parsed_url['query'], '', $url );
	unset( $query_array[ $key ] );
	$url = add_query_arg( $query_array, $url );

	return $url;
}

function get_query_str_without_args( $url, $key ) {

	$url        = remove_query_array( $url, 'arve' );
	$parsed_url = wp_parse_url( $url );

	return $parsed_url['query'];
}
