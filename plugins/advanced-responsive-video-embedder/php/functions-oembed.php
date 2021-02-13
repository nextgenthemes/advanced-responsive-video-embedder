<?php
namespace Nextgenthemes\ARVE;

const JSON_REGEX = '#<script data-arve-oembed type="application/json">(?<json>{[^}]+})</script>#i';

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

	if ( $data && 'video' === $data->type ) {

		$data->arve_cachetime = time();

		if ( 'YouTube' === $data->provider_name ) {
			$data->arve_srcset = yt_srcset( $data->thumbnail_url );
		}
		$result .= '<script data-arve-oembed type="application/json">' . \wp_json_encode( $data, JSON_PRETTY_PRINT ) . '</script>';
	}

	return $result;
}

function filter_embed_oembed_html( $cache, $url, array $attr, $post_ID ) {

	if ( disabled_on_feeds() ) {
		return \preg_replace( JSON_REGEX, '', $cache, 1 );
	}

	\preg_match( JSON_REGEX, $cache, $matches );

	if ( ! empty( $matches['json'] ) ) {
		$attr['oembed_data'] = json_decode( $matches['json'] );
		$attr['url']         = $url;
		$attr['post_id']     = (string) $post_ID;

		$cache = build_video( $attr );
	}

	return $cache;
}

function yt_srcset( $url ) {

	$re = '@[a-z]+.jpg$@';

	$mq     = preg_replace($re, 'mqdefault.jpg', $url, 1);     // 320x180
	$sd     = preg_replace($re, 'sddefault.jpg', $url, 1);     // 640x480
	$maxres = preg_replace($re, 'maxresdefault.jpg', $url, 1); // hd, fullhd ...

	$size_sd     = Common\get_image_size( $sd );
	$size_maxres = Common\get_image_size( $maxres );

	$srcset[320] = $mq;
	$srcset[480] = $url; // 480x360

	if ( $size_sd && 640 === $size_sd[0] ) {
		$srcset[640] = $sd;
	}
	if ( $size_maxres && $size_maxres[0] >= 1280 ) {
		$srcset[ $size_maxres[0] ] = $maxres;
	}

	if ( ! empty( $srcset ) ) {

		foreach ( $srcset as $size => $url ) {
			$srcset_comb[] = "$url {$size}w";
		}

		return implode( ', ', $srcset_comb );
	}

	return false;
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

	$not_touching = array(
		'platform.twitter.com',
		'embed.redditmedia.com',
		'embedr.flickr.com',
		'open.spotify.com',
		'secure.polldaddy.com',
		'embed.tumblr.com',
		'imgur.com',
	);

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
