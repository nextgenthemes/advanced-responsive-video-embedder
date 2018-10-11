<?php
namespace Nextgenthemes\ARVE;

function create_url_handlers() {

	$properties = get_host_properties();

	foreach ( $properties as $provider => $values ) {

		$function = function( $matches, $attr, $url, $rawattr ) use ( $provider ) {
			return url_detection_to_shortcode( $provider, $matches, $attr, $url, $rawattr );
		};

		if ( ! empty( $values['regex'] ) && empty( $values['use_oembed'] ) ) {
			wp_embed_register_handler( 'arve_' . $provider, $values['regex'], $function );
		}
	}
}

function url_detection_to_shortcode( $provider, array $matches, $attr, $url, $rawattr ) {

	// * Fix 'Markdown on save enhanced' issue
	if ( substr( $url, -4 ) === '</p>' ) {
		$url = substr( $url, 0, -4 );
	}

	$parsed_url = wp_parse_url( $url );
	$url_query  = array();
	$old_atts   = array();
	$new_atts   = array();

	if ( ! empty( $parsed_url['query'] ) ) {
		parse_str( $parsed_url['query'], $url_query );
	}

	foreach ( $url_query as $key => $value ) {

		if ( \Nextgenthemes\Utils\starts_with( $key, 'arve-' ) ) {
			$key              = substr( $key, 5 );
			$old_atts[ $key ] = $value;
		}
	}

	unset( $old_atts['param'] );

	if ( isset( $url_query['arve'] ) ) {
		$new_atts = $url_query['arve'];
	}

	if ( isset( $url_query['t'] ) ) {
		$url_query['start'] = youtube_time_to_seconds( $url_query['t'] );
	}

	unset( $url_query['arve'] );

	if ( 'youtube' === $provider ) {
		unset( $url_query['v'] );
		unset( $url_query['t'] );
	}

	$atts               = array_merge( (array) $old_atts, (array) $new_atts );
	$atts['parameters'] = empty( $url_query ) ? null : build_query( $url_query );
	$atts['provider']   = $provider;

	foreach ( $matches as $k => $v ) {

		if ( ! is_numeric( $k ) ) {
			$atts[ $k ] = $matches[ $k ];
		}
	}

	return build_video( $atts );
}
