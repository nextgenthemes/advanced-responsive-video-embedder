<?php

function arve_maybe_enqueue( $content ) {

	if ( arve_contains( $content, 'class="arve-wrapper' ) ) {
		wp_enqueue_style( ARVE_SLUG );
		wp_enqueue_script( ARVE_SLUG );
	}

	return $content;
}

function arve_filter_oembed_dataparse( $result, $data, $url ) {

	if( $a = arve_oembed2args( $data ) ) {

		$parsed_url = parse_url( $url );
		if( ! empty( $parsed_url['query'] ) ) {
			$a['parameters'] = $parsed_url['query'];
			$a['append_text'] = 'oembed_dataparse test';
		}
		$a['oembed_data'] = $data;
		return arve_shortcode_arve( $a );
	}

	return $result;
}

function arve_oembed2args( $data ) {

	if ( false === $data || 'video' != $data->type ) {
		return false;
	}

	if ( 'Facebook' == $data->provider_name ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $data->html, $matches );
	} else {
		preg_match( '/<iframe [^>]*src="([^"]+)"/', $data->html, $matches );
	}

	if ( empty( $matches[1] ) ) {
		return false;
	}

	$a = array(
		'provider'     => strtolower( $data->provider_name ),
		'src'          => $matches[1],
		'oembed_data'  => $data,
		'aspect_ratio' => ( empty( $data->width ) || empty( $data->height ) ) ? '16:9' : "{$data->width}:{$data->height}",
		'parameters'   => empty( $parsed_url['query'] ) ? '' : $parsed_url['query']
	);

	if ( 'Facebook' == $data->provider_name ) {
		$a['src'] = 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
	}

	return $a;
}

function arve_extract_url_query_array( $url, $extract_array_name ) {

	$parsed_url = parse_url( $url );

  if ( empty( $parsed_url['query'] ) ) {
		return array();
	}

  parse_str( $parsed_url['query'], $url_query );

	if ( ! empty( $url_query[ $extract_array_name ] ) && is_array( $url_query[ $extract_array_name ] ) ) {
		return $url_query[ $extract_array_name ];
	}

  return array();
}
