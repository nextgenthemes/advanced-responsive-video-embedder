<?php
namespace Nextgenthemes\ARVE;

/**
 * Info: https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp-oembed.php
 * https://github.com/iamcal/oembed/tree/master/providers
 */
function add_oembed_providers() {
	wp_oembed_add_provider( 'http://clips.twitch.tv/*', 'https://api.twitch.tv/v5/oembed' );
	wp_oembed_add_provider( 'https://clips.twitch.tv/*', 'https://api.twitch.tv/v5/oembed' );
	wp_oembed_add_provider( 'http://www.twitch.tv/*', 'https://api.twitch.tv/v5/oembed' );
	wp_oembed_add_provider( 'https://www.twitch.tv/*', 'https://api.twitch.tv/v5/oembed' );
	wp_oembed_add_provider( 'http://twitch.tv/*', 'https://api.twitch.tv/v5/oembed' );
	wp_oembed_add_provider( 'https://twitch.tv/*', 'https://api.twitch.tv/v5/oembed' );
	wp_oembed_add_provider( 'https://fast.wistia.com/embed/iframe/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://fast.wistia.com/embed/playlists/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://*.wistia.com/medias/*', 'https://fast.wistia.com/oembed.json' );
	wp_oembed_add_provider( 'https://d.tube/v/*', 'https://api.d.tube/oembed' );
}

function filter_oembed_dataparse( $result, $data, $url ) {

	$a = oembed2args( $data, $url );

	if ( $a ) {
		return build_video( $a );
	}

	return $result;
}

function oembed2args( $data ) {

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

	$a = [
		'provider'    => $provider,
		'src'         => $matches[1],
		'oembed_data' => $data,
	];

	if ( 'facebook' === $provider ) {
		$a['src'] = 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
	}

	return apply_filters( 'nextgenthemes/arve/oembed2args', $a );
}

function vimeo_referer( $args, $url ) {

	if ( contains( $url, 'vimeo' ) ) {
		$args['headers']['Referer'] = site_url();
	}

	return $args;
}
