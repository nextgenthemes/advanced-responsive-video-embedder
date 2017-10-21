<?php



add_filter( 'oembed_dataparse', 'arve_dataparse', 10, 3 );

function arve_dataparse( $result, $data, $url ) {

	if ( 'video' != $data->type ) {
		return $result;
	}

	if ( 'Facebook' == $data->provider_name ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $result, $matches );
	} elseif ( 'Vimeo' == $data->provider_name ) {
		preg_match( '/src="([^"]+)"/', $result, $matches );
	} else {
		return $result;
	}

	if ( empty( $matches[1] ) ) {
		return $result;
	}

	$parameters = $url_query = arve_extract_url_query( $url );
	unset( $parameters['arve'] );

	$a = array(
		'provider'     => strtolower( $data->provider_name ),
		'src'          => $matches[1],
		'oembed_data'  => $data,
		'aspect_ratio' => $data->width . ':' . $data->height,
		'paramaters'   => $parameters
	);

	if ( isset( $url_query['arve'] ) ) {
		$a = array_merge( $url_query['arve'], $a );
	}

	if ( 'Facebook' == $data->provider_name ) {
		$a['id'] = $a['src'];
		unset( $a['src'] );
	}

	return arve_shortcode_arve( $a );
}

#add_filter( 'oembed_result', 'arve_oembed_result', 10, 4 );

function arve_oembed_result( $result, $url, $args ) {

	return $result;
}

function arve_extract_url_query( $url ) {

	$parsed_url = parse_url( $url );

	if ( empty( $parsed_url['query'] ) ) {
		return array();
	}

	parse_str( $parsed_url['query'], $url_query );

	return $url_query;
}

function arve_fff( $provider, $matches, $attr, $url, $rawattr ) {

	//* Fix 'Markdown on save enhanced' issue
	if ( substr( $url, -4 ) === '</p>' ) {
		$url = substr( $url, 0, -4 );
	}

	$parsed_url = parse_url( $url );
	$url_query = $old_atts = $new_atts = array();

	if ( ! empty( $parsed_url['query'] ) ) {
		parse_str( $parsed_url['query'], $url_query );
	}

	foreach ( $url_query as $key => $value ) {

		if ( arve_starts_with( $key, 'arve-' ) ) {
			$key = substr( $key, 5 );
			$old_atts[ $key ] = $value;
		}
	}

	unset( $old_atts['param'] );

	if ( isset( $url_query['arve'] ) ) {
		$new_atts = $url_query['arve'];
	}

	if ( isset( $url_query['t'] ) ) {
		$url_query['start'] = arve_youtube_time_to_seconds( $url_query['t'] );
	}

	unset( $url_query['arve'] );

	if ( 'youtube' == $provider ) {
		unset( $url_query['v'] );
		unset( $url_query['t'] );
	}

	$atts               = array_merge( (array) $old_atts, (array) $new_atts );
	$atts['parameters'] = empty( $url_query ) ? null : build_query( $url_query );
	$atts['url']        = $url;

	return arve_shortcode_arve( $atts, null );
}
