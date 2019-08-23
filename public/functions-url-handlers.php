<?php
namespace Nextgenthemes\ARVE;

use Nextgenthemes\Utils\starts_with;

function create_url_handlers() {

	$properties = get_host_properties();

	foreach ( $properties as $provider => $values ) {

		$function = function( $matches, $attr, $url, $rawattr ) use ( $provider ) {
			return url_detection_to_shortcode( $provider, $matches, $attr, $url, $rawattr );
		};

		if ( ! empty( $values['regex'] ) && empty( $values['oembed'] ) ) {
			wp_embed_register_handler( 'arve_' . $provider, $values['regex'], $function );
		}
	}
}

function url_detection_to_shortcode( $provider, array $matches, array $attr, $url, array $rawattr ) {

	// Fix 'Markdown on save enhanced' issue
	if ( substr( $url, -4 ) === '</p>' ) {
		$url = substr( $url, 0, -4 );
	}

	$parsed_url = wp_parse_url( $url );
	$url_query  = [];
	$old_atts   = [];
	$new_atts   = [];

	if ( ! empty( $parsed_url['query'] ) ) {
		parse_str( $parsed_url['query'], $url_query );
	}

	foreach ( $url_query as $key => $value ) {

		if ( starts_with( $key, 'arve-' ) ) {
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

	$a               = array_merge( (array) $old_atts, (array) $new_atts );
	$a['parameters'] = empty( $url_query ) ? null : build_query( $url_query );
	$a['provider']   = $provider;

	foreach ( $matches as $k => $v ) {

		if ( ! is_numeric( $k ) ) {
			$a[ $k ] = $matches[ $k ];
		}
	}

	return build_video( $a );
}
