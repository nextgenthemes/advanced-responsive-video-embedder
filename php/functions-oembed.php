<?php
namespace Nextgenthemes\ARVE;

/**
 * Info: https://github.com/WordPress/WordPress/blob/master/wp-includes/class-wp-oembed.php
 * https://github.com/iamcal/oembed/tree/master/providers
 */
function add_oembed_providers() {
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

function oembed2args( $data, $url ) {

	if ( false === $data || 'video' !== $data->type || disabled_on_feeds() ) {
		return false;
	}

	$provider = strtolower( $data->provider_name );
	$provider = str_replace( 'wistia, inc.', 'wistia', $provider );

	if ( 'facebook' === $provider ) {
		preg_match( '/class="fb-video" data-href="([^"]+)"/', $data->html, $matches );
	} else {
		preg_match( '/<iframe [^>]*src="([^"]+)"/', $data->html, $matches );
	}

	if ( empty( $matches[1] ) ) {
		return false;
	}

	if ( 'facebook' === $provider ) {
		$src = 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $matches[1] );
	} else {
		$src = $matches[1];
	}

	$a = [
		'oembed_data' => $data,
		'provider'    => $provider,
		'src'         => $src,
		'url'         => $url,
	];

	return apply_filters( 'nextgenthemes/arve/oembed2args', $a );
}

// needed for private videos
function vimeo_referer( $args, $url ) {

	if ( str_contains( $url, 'vimeo' ) ) {
		$args['headers']['Referer'] = site_url();
	}

	return $args;
}

function trigger_cache_rebuild( $ttl, $url, $attr, $post_id ) {

	if ( did_action( 'nextgenthemes/arve/oembed_recache' ) ) {
		return $ttl;
	}

	// Get the time when oEmbed HTML was last cached (based on the WP_Embed class)
	$key_suffix    = md5( $url . serialize( $attr ) ); // phpcs:ignore
	$cachekey_time = '_oembed_time_' . $key_suffix;
	$cache_time    = get_post_meta( $post_id, $cachekey_time, true );

	// Get the cached HTML
	$cachekey     = '_oembed_' . $key_suffix;
	$metadata     = get_post_custom( $post_id );
	$cache_exists = isset( $metadata[ $cachekey ][0] );
	$cache_html   = $cache_exists ? strtolower( get_post_meta( $post_id, $cachekey, true ) ) : false;
	// $cache_exists2 = metadata_exists( 'post', $post_id, $cachekey ); // TODO not sure of 'post' is always right for embeds outside of

	// time after a recache should be done
	$trigger_time = get_option( 'nextgenthemes_arve_oembed_recache' );

	$not_touching = [
		'platform.twitter.com',
		'embed.redditmedia.com',
		'embedr.flickr.com',
		'open.spotify.com',
		'secure.polldaddy.com',
		'embed.tumblr.com',
		'imgur.com',
	];

	// Check if we need to regenerate the oEmbed HTML:
	if ( $cache_exists &&
		$cache_time < $trigger_time &&
		! Common\str_contains_any( $cache_html, $not_touching ) &&
		$GLOBALS['wp_embed']->usecache
	) {
		// What we need to skip the oembed cache part
		$GLOBALS['wp_embed']->usecache = false;
		$ttl                           = 0;

		do_action( 'nextgenthemes/arve/oembed_recache' );
	}

	return $ttl;
}

function reenable_oembed_cache( $discover ) {

	if ( did_action( 'nextgenthemes/arve/oembed_recache' ) ) {
		$GLOBALS['wp_embed']->usecache = true;
	}

	return $discover;
}
