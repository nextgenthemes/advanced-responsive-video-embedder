<?php

add_filter( 'oembed_dataparse', 'arve_facebook_dataparse', 10, 3 );

function arve_facebook_dataparse( $result, $data, $url ) {

	if ( 'video' != $data->type ) {
		return $result;
	}

	if ( 'Facebook' == $data->provider_name ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $result, $matches );

		if ( empty( $matches[1] ) ) {
			return $result;
		}

		return arve_shortcode_arve( array(
			'provider' => strtolower( $data->provider_name ),
			'id'       => $matches[1],
			'oembed'   => $data,
			'aspect_ratio' => $data->width . ':' . $data->height
		) );
	}

	return $result;
	/*

	preg_match( '/src="([^"]+)"/', $result, $matches );

	if ( empty( $matches[1] ) ) {
		return $result;
	}

	return arve_shortcode_arve( array(
		'provider' => strtolower( $data->provider_name ),
		'src'      => $matches[1],
		'oembed'   => $data,
		'aspect_ratio' => $data->width . ':' . $data->height
	) );
	*/
}

#add_filter( 'oembed_result', 'arve_oembed_result', 10, 4 );

function arve_oembed_result( $result, $url, $args ) {

	return $result;
}
