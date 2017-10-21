<?php

function arve_filter_oembed_dataparse( $result, $data, $url ) {

	if ( false === $result || 'video' != $data->type ) {
		return '<span data-arve-skip hidden></span>' . $result;
	}

	if ( 'Facebook' == $data->provider_name ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $result, $matches );
	} else {
		preg_match( '/src="([^"]+)"/', $result, $matches );
	}

	if ( empty( $matches[1] ) ) {
		return '<span data-arve-skip hidden></span>' . $result;
	}

	$parameters = $url_query = arve_extract_url_query( $url );
	unset( $parameters['arve'] );

	$a = array(
		'provider'     => strtolower( $data->provider_name ),
		'src'          => $matches[1],
		'oembed_data'  => $data,
		'aspect_ratio' => ( empty( $data->width ) || empty( $data->height ) ) ? '16:9' : "{$data->width}:{$data->height}",
		'paramaters'   => $parameters
	);

	if ( 'Facebook' == $data->provider_name ) {
		$a['src'] = 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
	}

	if ( isset( $url_query['arve'] ) ) {
		$a = array_merge( $url_query['arve'], $a );
	}

	return arve_shortcode_arve( $a );
}

function arve_extract_url_query( $url ) {

  $parsed_url = parse_url( $url );

  if ( empty( $parsed_url['query'] ) )
    return array();

  parse_str( $parsed_url['query'], $url_query );

  return $url_query;
}
