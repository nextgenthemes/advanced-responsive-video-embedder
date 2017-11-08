<?php

function arve_create_url_handlers() {

	$properties = arve_get_host_properties();

	foreach ( $properties as $provider => $values ) {

		$function = function( $matches, $attr, $url, $rawattr ) use ( $provider ) {
			return arve_url_detection_to_shortcode( $provider, $matches, $attr, $url, $rawattr );
		};

		if ( ! empty( $values['regex'] ) && empty( $values['use_oembed'] ) ) {
			wp_embed_register_handler( 'arve_' . $provider, '#' . $values['regex'] . '#i', $function );
		}
	}
}

function arve_url_detection_to_shortcode( $provider, $matches, $attr, $url, $rawattr ) {

	// Fix 'Markdown on save enhanced' issue
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

	unset( $url_query['arve'] );

	$atts               = array_merge( (array) $old_atts, (array) $new_atts );
	$atts['parameters'] = empty( $url_query ) ? null : build_query( $url_query );
	$atts['id']         = $matches['id'];
	$atts['provider']   = $provider;

	return arve_shortcode_arve( $atts );
}

/**
 * Remove the Wordpress default Oembed support for video providers that ARVE Supports. Array taken from wp-includes/class-oembed.php __construct
 *
 * @since    5.9.9
 *
 */
function arve_remove_unwanted_shortcodes() {

	// Jetpack shit
	remove_shortcode( 'dailymotion', 'dailymotion_shortcode' );
	remove_filter( 'pre_kses', 'jetpack_dailymotion_embed_reversal' );
	remove_filter( 'pre_kses', 'dailymotion_embed_to_shortcode' );

	remove_shortcode( 'vimeo', 'vimeo_shortcode' );
	remove_filter( 'pre_kses', 'vimeo_embed_to_shortcode' );

	wp_embed_unregister_handler( 'jetpack_vine' );
	remove_shortcode( 'vine', 'vine_shortcode' );

	remove_filter('pre_kses', 'youtube_embed_to_short_code');
	remove_shortcode( 'youtube', 'youtube_shortcode' );

	remove_shortcode( 'ted', 'shortcode_ted' );
	wp_oembed_remove_provider( '!https?://(www\.)?ted.com/talks/view/id/.+!i' );
	wp_oembed_remove_provider( '!https?://(www\.)?ted.com/talks/[a-zA-Z\-\_]+\.html!i' );
}
