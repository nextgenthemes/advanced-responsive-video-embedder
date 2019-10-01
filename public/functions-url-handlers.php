<?php

function arve_create_url_handlers() {

	$properties = arve_get_host_properties();

	foreach ( $properties as $provider => $values ) {

		$function = function( $matches, $attr, $url, $rawattr ) use ( $provider ) {
			return arve_url_detection_to_shortcode( $provider, $matches, $attr, $url, $rawattr );
		};

		if ( ! empty( $values['regex'] ) ) {
			wp_embed_register_handler( 'arve_' . $provider, '#' . $values['regex'] . '#i', $function );
		}
	}
}

function arve_url_detection_to_shortcode( $provider, $matches, $attr, $url, $rawattr ) {

	//* Fix 'Markdown on save enhanced' issue
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

		if ( arve_starts_with( $key, 'arve-' ) ) {
			$key              = substr( $key, 5 );
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

	if ( 'youtube' === $provider ) {
		unset( $url_query['v'] );
		unset( $url_query['t'] );
	}

	//* Pure awesomeness!
	$atts               = array_merge( (array) $old_atts, (array) $new_atts );
	$atts['parameters'] = empty( $url_query ) ? null : build_query( $url_query );
	$atts['url']        = $url;

	return arve_shortcode_arve( $atts, null );
}


/**
 * Remove the WordPress default Oembed support for video providers that ARVE Supports. Array taken from wp-includes/class-oembed.php __construct
 *
 * @since    5.9.9
 *
 */
function arve_oembed_remove_providers() {

	// phpcs:disable Squiz.PHP.CommentedOutCode.Found

	$wp_core_oembed_shits = array(
		'#http://(www\.)?youtube\.com/watch.*#i'         => array( 'http://www.youtube.com/oembed', true ),
		'#https://(www\.)?youtube\.com/watch.*#i'        => array( 'http://www.youtube.com/oembed?scheme=https', true ),
		#'#http://(www\.)?youtube\.com/playlist.*#i'           => array( 'http://www.youtube.com/oembed',                      true  ),
		#'#https://(www\.)?youtube\.com/playlist.*#i'          => array( 'http://www.youtube.com/oembed?scheme=https',         true  ),
		'#http://youtu\.be/.*#i'                         => array( 'http://www.youtube.com/oembed', true ),
		'#https://youtu\.be/.*#i'                        => array( 'http://www.youtube.com/oembed?scheme=https', true ),
		'#https?://(.+\.)?vimeo\.com/.*#i'               => array( 'http://vimeo.com/api/oembed.{format}', true ),
		'#https?://(www\.)?dailymotion\.com/.*#i'        => array( 'http://www.dailymotion.com/services/oembed', true ),
		'http://dai.ly/*'                                => array( 'http://www.dailymotion.com/services/oembed', false ),
		#'#https?://(www\.)?flickr\.com/.*#i'                  => array( 'https://www.flickr.com/services/oembed/',            true  ),
		#'#https?://flic\.kr/.*#i'                             => array( 'https://www.flickr.com/services/oembed/',            true  ),
		#'#https?://(.+\.)?smugmug\.com/.*#i'                  => array( 'http://api.smugmug.com/services/oembed/',            true  ),
		#'#https?://(www\.)?hulu\.com/watch/.*#i'              => array( 'http://www.hulu.com/api/oembed.{format}',            true  ),
		#'http://revision3.com/*'                              => array( 'http://revision3.com/api/oembed/',                   false ),
		#'http://i*.photobucket.com/albums/*'                  => array( 'http://photobucket.com/oembed',                      false ),
		#'http://gi*.photobucket.com/groups/*'                 => array( 'http://photobucket.com/oembed',                      false ),
		#'#https?://(www\.)?scribd\.com/doc/.*#i'              => array( 'http://www.scribd.com/services/oembed',              true  ),
		#'#https?://wordpress.tv/.*#i'                         => array( 'http://wordpress.tv/oembed/',                        true ),
		#'#https?://(.+\.)?polldaddy\.com/.*#i'                => array( 'https://polldaddy.com/oembed/',                      true  ),
		#'#https?://poll\.fm/.*#i'                             => array( 'https://polldaddy.com/oembed/',                      true  ),
		'#https?://(www\.)?funnyordie\.com/videos/.*#i'  => array( 'http://www.funnyordie.com/oembed', true ),
		#'#https?://(www\.)?twitter\.com/.+?/status(es)?/.*#i' => array( 'https://api.twitter.com/1/statuses/oembed.{format}', true  ),
		'#https?://vine.co/v/.*#i'                       => array( 'https://vine.co/oembed.{format}', true ),
		#'#https?://(www\.)?soundcloud\.com/.*#i'              => array( 'http://soundcloud.com/oembed',                       true  ),
		#'#https?://(.+?\.)?slideshare\.net/.*#i'              => array( 'https://www.slideshare.net/api/oembed/2',            true  ),
		#'#http://instagr(\.am|am\.com)/p/.*#i'                => array( 'http://api.instagram.com/oembed',                    true  ),
		#'#https?://(www\.)?rdio\.com/.*#i'                    => array( 'http://www.rdio.com/api/oembed/',                    true  ),
		#'#https?://rd\.io/x/.*#i'                             => array( 'http://www.rdio.com/api/oembed/',                    true  ),
		#'#https?://(open|play)\.spotify\.com/.*#i'            => array( 'https://embed.spotify.com/oembed/',                  true  ),
		#'#https?://(.+\.)?imgur\.com/.*#i'                    => array( 'http://api.imgur.com/oembed',                        true  ),
		#'#https?://(www\.)?meetu(\.ps|p\.com)/.*#i'           => array( 'http://api.meetup.com/oembed',                       true  ),
		#'#https?://(www\.)?issuu\.com/.+/docs/.+#i'           => array( 'http://issuu.com/oembed_wp',                         true  ),
		'#https?://(www\.)?collegehumor\.com/video/.*#i' => array( 'http://www.collegehumor.com/oembed.{format}', true ),
		#'#https?://(www\.)?mixcloud\.com/.*#i'                => array( 'http://www.mixcloud.com/oembed',                     true  ),
		'#https?://(www\.|embed\.)?ted\.com/talks/.*#i'  => array( 'http://www.ted.com/talks/oembed.{format}', true ),
		#'#https?://(www\.)?(animoto|video214)\.com/play/.*#i' => array( 'http://animoto.com/oembeds/create',                  true  ),
	);

	foreach ( $wp_core_oembed_shits as $shit => $fuck ) {

		wp_oembed_remove_provider( $shit );
	}

	// Jetpack shit
	remove_shortcode( 'dailymotion', 'dailymotion_shortcode' );
	remove_filter( 'pre_kses', 'jetpack_dailymotion_embed_reversal' );
	remove_filter( 'pre_kses', 'dailymotion_embed_to_shortcode' );

	remove_shortcode( 'vimeo', 'vimeo_shortcode' );
	remove_filter( 'pre_kses', 'vimeo_embed_to_shortcode' );

	wp_embed_unregister_handler( 'jetpack_vine' );
	remove_shortcode( 'vine', 'vine_shortcode' );

	remove_filter( 'pre_kses', 'youtube_embed_to_short_code' );
	remove_shortcode( 'youtube', 'youtube_shortcode' );

	remove_shortcode( 'ted', 'shortcode_ted' );
	wp_oembed_remove_provider( '!https?://(www\.)?ted.com/talks/view/id/.+!i' );
	wp_oembed_remove_provider( '!https?://(www\.)?ted.com/talks/[a-zA-Z\-\_]+\.html!i' );
}
